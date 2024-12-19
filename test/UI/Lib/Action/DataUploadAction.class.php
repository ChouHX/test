<?php
//通过http接口，将平台数据post到客户服务器
class DataUploadAction extends CommonAction{
    private $errcode = null;
    private $opts = null;
    private $user = 'push01';
    private $pwd = '123456';
    private $log = null;
    private $tcp_report_log = null;
    private $relogin = 0;

    public function _initialize() {
        session_write_close();
        ignore_user_abort(TRUE);
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', -1);
        $this->errcode = array(
            '00000' => '成功',
            '00002' => 'token超时或不存在',
            '99998' => '数据库异常',
            '99999' => '系统异常',
            '10001' => '数据校验错误',
            '10002' => 'token超时或不存在，需要重新登录',
            '10003' => '参数不符',
            '10004' => '图形验证码不符',
            '10005' => '时间戳已使用',
            '20001' => '用户不存在',
            '20002' => '密码错误',
        );
        $this->opts = array(
            'http' => array(
                'method' => 'POST',
                'timeout' => 2
            )
        );
        import('@.ORG.Mlog');
        $this->log = new Mlog('./Log', 'push_data_log');
        $this->tcp_report_log = new Mlog('./Log','tcp_report_log');
    }

    private function login() {
        $token = S('push_token');
        if ($token) {
            echo sprintf("Token cached, token = %s<br>", $token);
            return $token;
        }
        $data = json_encode(array(
            'userCode'  => $this->user,
            'password'  => strtoupper(md5($this->pwd))
        ));
        $this->opts['http']['header'] = "Content-type: application/json\r\nContent-length:".strlen($data)."\r\n";
        $this->opts['http']['content'] = $data;
        $ret = file_get_contents('http://api.iqblife.com:8080/providers/login', false, stream_context_create($this->opts));
        if (!$ret) {
            $this->log->mwrite(sprintf("%s\t%s\r\n", date('Y-m-d H:i:s'), '[login] file_get_contents return is null'));
            exit('Error 1');
        }
        $arr = json_decode($ret, true);
        if (!$arr) {
            $this->log->mwrite(sprintf("%s\t%s, ret = %s\r\n", date('Y-m-d H:i:s'), '[login] json_decode failed', $ret));
            exit('Error 2');
        }
        if ($arr['code'] != '00000') {
            $this->log->mwrite(sprintf("%s\t%s, code = %s\r\n", date('Y-m-d H:i:s'), '[login] request failed', $arr['code']));
            exit('Error 3');
        }
        $this->log->mwrite(sprintf("%s\t%s, token = %s\r\n", date('Y-m-d H:i:s'), '[login] ok', $arr['token']));
        echo sprintf("ok, token = %s<br>", $arr['token']);
        S('push_token', $arr['token'], 6*24*3600);
        return $arr['token'];
    }

    public function push() {
        relogin:
        $token = $this->login();
        $filename = './Lib/last_report_time';
        if (!file_exists($filename)) {
            file_put_contents($filename, date('Y-m-d H:i:s', strtotime('-10 minutes')));
        }

        $fp = fopen($filename, 'r+');
        flock($fp, LOCK_EX);
        $last_time = fread($fp, 19);
        $rs = M('rtu_data')->join("INNER JOIN term ON term.sn = rtu_data.sn AND term.group_id != 3")->where("report_time > '$last_time'")->order('report_time ASC')->field('addr, rtu_data.sn, report_time, value')->select();
        $len = count($rs);
        if (APP_DEBUG) dump(array('last_time' => $last_time, 'len' => $len, 'sql' => M('rtu_data')->_sql()));

        if ($len > 0) {
            rewind($fp);
            fwrite($fp, $rs[$len-1]['report_time']);
            fclose($fp);

            foreach ($rs as $key => $row) {
                $datas[$row['sn']][$row['addr']] = $row['value'];
            }
            $data = array(
                //2001, 2004, 2002, 2000, 2003, 2007
                //流速A，瞬时流量A，温度A，浊度，超声液位A，静压液位B
                'fields' => 'deviceid,obsTime,flowrate,flow,TEMP,SS,ultrasoniclevel,staticlevel',
                'values' => array()
                // sprintf('19100006000,%s,21.1,0.1,02', date('YmdHis'))
            );
            $tm = date('YmdHis');
            foreach ($datas as $sn => $row) {
                $data['values'][] = sprintf('191000%s000,%s,%s,%s,%s,%s,%s,%s', $sn, $tm, $row['2001'], $row['2004'], $row['2002'], $row['2000'], $row['2003'], $row['2007']);
            }
            $data_num = count($data['values']);
            $data['values'] = implode(';', $data['values']);
            $data = json_encode($data);
            if (APP_DEBUG) dump($data);

            $this->opts['http']['header'] = "access_token: {$token}\r\nContent-type: application/json\r\nContent-length:".strlen($data)."\r\n";
            $this->opts['http']['content'] = $data;
            $ret = file_get_contents('http://api.iqblife.com:8080/providers/pushdata', false, stream_context_create($this->opts));
            if (!$ret) {
                $this->log->mwrite(sprintf("%s\t%s\r\n", date('Y-m-d H:i:s'), '[push] file_get_contents return is null'));
                exit('Error 4');
            }
            $arr = json_decode($ret, true);
            if (!$arr) {
                $this->log->mwrite(sprintf("%s\t%s, ret = %s\r\n", date('Y-m-d H:i:s'), '[push] json_decode failed', $ret));
                exit('Error 5');
            }
            if ($arr['code'] != '00000') {
                $this->log->mwrite(sprintf("%s\t%s, code = %s\r\n", date('Y-m-d H:i:s'), '[push] request failed', $arr['code']));
                if ($arr['code'] == '00002' || $arr['code'] == '10002') {
                    S('push_token', null);
                    if ($this->relogin++ == 0) {
                        goto relogin;
                    }
                }
                exit('Error 6');
            }
            if (APP_DEBUG) dump($arr);
            $this->log->mwrite(sprintf("%s\t%s, send data = %d, return count = %d, last_time = %s, sns = %s\r\n",
                date('Y-m-d H:i:s'), '[push] ok', $data_num, $arr['count'], $last_time, implode(',', array_keys($datas))
            ));
            echo sprintf("ok, send data = %d, return count = %d<br>", $data_num, $arr['count']);
        } else {
            fclose($fp);
        }
    }

    private function processData($sn, &$row) {
        $str = '';
        $time = date('YmdHisz', time());
        $report_time = date('YmdHis',time());
        $data_set = array('2010'=>'w01029-Rtd');
        $head = "##";       //包头 固定为##
        $str = 'QN='.$time.';ST=22;CN=2061;MN=LG20200528'.$sn.';PW=123456;Flag=5;CP=&&DataTime='.$report_time.';';
        foreach($row as $k=>$v){
            $str .= $data_set[$k].'='.$v.';';
        }
        $newstr = substr($str,0,strlen($str)-1);
        $str = $newstr.'&&';
        $len = sprintf('%04d',strlen($str));
        $crc = crc16($str);
        $result = sprintf('%s%s%s%s',$head,$len ,$str,$crc);
        return $result;
    }

    //上海水滴湖项目TCP转发数据
    public function tcpPush() {
        $filename = './Lib/tcp_report_time';
        if (!file_exists($filename)) {
            file_put_contents($filename, date('Y-m-d H:i:s', strtotime('-15 minutes')));
        }
        $fp = fopen($filename, 'r+');
        flock($fp, LOCK_EX);
        $last_time = fread($fp, 19);
        $sns = M('term')->where("group_id=3")->getField('sn', true);
        $rs = M('rtu_data')->where("sn IN('".implode("','", $sns)."') AND addr = 2010 AND report_time > '$last_time'")->order('report_time ASC')->field('addr, sn, report_time, value')->select();
        $len = count($rs);
        if ($len > 0) {
            rewind($fp);
            fwrite($fp, $rs[$len-1]['report_time']);
            fclose($fp);
            foreach ($rs as $key => $row) {
                $datas[$row['sn']][$row['addr']] = $row['value'];
            }
            foreach ($datas as $sn => $row) {
                $info = $this->processData($sn,$row);
                if ($info == '') continue;

                $client = stream_socket_client('tcp://'.C('OEM_SERVER_IP').':'.C('OEM_SERVER_PORT'), $errno, $errstr, 20);
                if (!$client) continue;

                $ret = fwrite($client, $info);
                if ($ret) {
                    $this->tcp_report_log->mwrite(sprintf("%s\t%s, send data = %s\r\n",date('Y-m-d H:i:s'), '[push] ok', $info));
                }else{
                    $this->tcp_report_log->mwrite(sprintf("%s\t%s, send data failed\r\n",date('Y-m-d H:i:s'), '[push] error'));
                }
                fclose($client);
            }
        }else{
            fclose($fp);
        }
    }
}