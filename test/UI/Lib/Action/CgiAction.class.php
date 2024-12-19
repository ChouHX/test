<?php
class CgiAction extends AlarmAction{
    protected $AUSERS = array(); //告警接收用户
    public function __construct() {
        parent::__construct();
        session_write_close();
        ignore_user_abort(TRUE);
        set_time_limit(0);
        ini_set('memory_limit', -1);
        header('content-type:text/html; charset=utf8');
    }

    //获取过期时间，单位(秒)
    private function getExpired() {
        $expired = C('LBS_EXPIRED');
        if (!$expired || !preg_match('/^([1-9][0-9]{0,})([smhd])$/',$expired,$match)){
            $expired = 30 * 24 * 3600;
        }else{
            $ch = $match[2];
            $num = intval($match[1]);
            if ($ch == 's'){
                $expired = $num;
            }elseif ($ch == 'm'){
                $expired = $num * 60;
            }elseif ($ch == 'h'){
                $expired = $num * 3600;
            }elseif ($ch == 'd'){
                $expired = $num * 24 * 3600;
            }
        }
        // dump($match); die('e='.$expired);
        return $expired;
    }

    // 基站定位
    public function base_station_location() {
        $api = C('LBS_API_CELL');
        if (empty($api)) die('LBS_API_CELL is not set, exit.');
        $expired = $this->getExpired();
        $m = M('term_cell');
        $rs = $m->field('id, sn, mnc, lac, cellid')
            ->where("lac <> '0' AND cellid <> '0' AND report_status = 0 AND (last_api_time IS NULL OR TIME_TO_SEC(TIMEDIFF(NOW(),last_api_time)) > $expired)")
            ->limit(50)->select();
        import("ORG.Net.Http");
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'cell_location');
        foreach ($rs as $k => $row) {
            $mnc = $row['mnc'];
            if (in_array($mnc, array(0,2,7))) {
                $mnc = 0;
            } elseif (in_array($mnc, array(1,6))) {
                $mnc = 1;
            }
            $this->locRetHandle(sprintf($api, $mnc, hexdec($row['lac']), hexdec($row['cellid'])), $m, $log, $row);
        }
    }

    // 处理定位接口返回数据
    private function locRetHandle($req_url, $m, $log, $row) {
        static $gps = null;
        if (!$gps) {
            $rs2 = M('term_gps')->field('id, sn')->select();
            foreach ($rs2 as $key => $ro) {
                $gps[$ro['sn']] = $ro['id'];
            }
            unset($rs2);
        }
        $ret = Http::get($req_url, $log, ACTION_NAME);
        $ymdhis = date('Y-m-d H:i:s');
        if ($ret['http_code'] == 200 && $ret['res']['errcode'] == 0) {
            $d = array(
                'id' => $row['id'],
                'report_status' => 1,
                'addr' => $ret['res']['address'],
                'last_api_time' => $ymdhis
            );
            $m->save($d);
            $gpsd = array(
                'longitude' => $ret['res']['lon'],
                'latitude'  => $ret['res']['lat'],
                'report_time' => $ymdhis,
            );
            if (isset($gps[$row['sn']])) {
                M('term_gps')->where('id = %d', $gps[$row['sn']])->save($gpsd);
            } else {
                $gpsd['sn'] = $row['sn'];
                M('term_gps')->add($gpsd);
            }
        } elseif ($ret['res']['errcode'] == 10001) {
            // 无查询结果，避免再次查询
            $m->save(array('id' => $row['id'], 'last_api_time' => $ymdhis));
        }
    }

    // wifi定位
    public function wifi_location() {
        $api = C('LBS_API_WIFI');
        if (empty($api)) die('LBS_API_WIFI is not set, exit.');
        $expired = $this->getExpired();
        $m = M('term_wifi_ap');
        $rs = $m->field('term_wifi_ap.id, term.sn, term_wifi_ap.ap_mac')
			->join("INNER JOIN term ON term.sn = term_wifi_ap.sn")
			->where("ap_mac REGEXP '([z-zA-Z0-9]{2}:){5}[z-zA-Z0-9]{2}' AND report_status = 0 AND (last_api_time IS NULL OR TIME_TO_SEC(TIMEDIFF(NOW(),last_api_time)) > $expired)")
            ->order('LENGTH(ap_mac) DESC')
            ->limit(50)->select();
        import("ORG.Net.Http");
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'wifi_location');
        foreach ($rs as $k => $row) {
            $macs = explode(',', $row['ap_mac']);
            foreach ($macs as $k => $v) {
                $macs[$k] = $v.',0';
            }
            $this->locRetHandle(sprintf($api, implode(';', $macs)), $m, $log, $row);
        }
    }

    // task_server间隔20秒调用一次，生成告警记录至term_alarm_record表
    public function alarm() {
        // 接收用户
        $tm = date('Y-m-d H:i:s');
        $users = M('usr')->where("is_enable = 1 AND (never_expired = 1 OR expired_time > '%s') AND (email IS NOT NULL OR wx IS NOT NULL) AND recv_alarm_cfg IS NOT NULL", $tm)
            ->field('id, name, email, wx, recv_alarm_cfg')->select();
        foreach ($users as $key => $row) {
            $cfg = json_decode($row['recv_alarm_cfg'], true);
            if (!$cfg || !isset($cfg['alarm_enable_email']) || ($cfg['alarm_enable_email'] == 0 && $cfg['alarm_enable_wx'] == 0) || (!$row['email'] && !$row['wx'])) {
                continue;
            }
            $row['wx'] = trim($row['wx']);
            $this->AUSERS[] = array(
                'id'    => $row['id'],
                'name'  => $row['name'],
                'email' => $cfg['alarm_enable_email'] == 1 && $this->regex($row['email'], 'email') ? $row['email'] : '',
                'wx'    => $cfg['alarm_enable_wx'] == 1 && !empty($row['wx']) ? $row['wx'] : '',
                'cfg'   => $cfg
            );
        }
        // dump2($this->AUSERS);
        if (count($this->AUSERS) == 0) {
            return;
        }
        $this->offlineAlarm();
        $this->vpnAlarm();
        $this->signalAlarm();
        $this->fluxAlarm();
        $this->fenceAlarm();
    }

    // task_server间隔10秒调用一次，从term_alarm_record表读取handle_status=0的记录，发送告警信息 (email, WeChat)
    public function alarm_send() {
        // 处理创建时间超过24小时未处理的记录，将handle_status修改为2，不再发送。
        $wx = A('Weixin');
        $m = M('term_alarm_record');
        $tm24 = date('Y-m-d H:i:s', time() - 24*3600);
        $m->where("handle_status = 0 AND create_time < '$tm24'")->save(array('handle_status' => 2));
        $rs = $m->where("handle_status = 0")->order('create_time ASC')->limit(6)->select();
        foreach ($rs as $key => $row) {
            $ids[] = $row['id'];
        }
        if (isset($ids)) {
            $m->where('id IN(%s)', implode(',', $ids))->save(array('handle_status' => 1)); //先将状态改过来，避免重复查询
            $ymdhis = date('Y-m-d H:i:s');
            foreach ($rs as $key => $row) {
                $data = array('id' => $row['id']);
                if (!empty($row['email'])) {
                    // 发邮件告警
                    $ret = $this->sendmail($row['email'], L('VAR_ALARM_TITLE').' - '.$ymdhis, $row['email_content']);
                    $data['email_send_status'] = $ret['status'] == 0 ? 1 : 2;
                    $data['email_send_info']   = $ret['status'] == 0 ? 'ok' : $ret['info'];
                    $data['email_send_ts']     = date('Y-m-d H:i:s');
                }
                if (!empty($row['wx'])) {
                    // 发送企业微信告警
                    $ret2 = $wx->wx_send_msg('textcard', array(
                        'touser' => $row['wx'],
                        'title' => L('VAR_ALARM_TITLE'),
                        'content' => $row['wx_content'],
                        'url' => 'javascript:;'
                    ));
                    $data['wx_send_status'] = $ret2['status'] == 0 ? 1 : 2;
                    $data['wx_send_info']   = $ret2['status'] == 0 ? 'ok' : $ret2['info'];
                    $data['wx_send_ts']     = date('Y-m-d H:i:s');
                }
                if (!empty($row['sms_number'])) {
                    // 发短信告警
                }
                $m->save($data);
            }
        }
    }

    public function parse_rtu_script(){
        return;
        $m = M('rtu_data_set');
        $rs = $m->field('slave_id, addr')->select();
        foreach ($rs as $k => $row) {
            $slave_id_addr_s[] = $row['slave_id'].'_'.$row['addr'];
        }
        $rs = $m->query("SELECT sn, rtu_script FROM term_param WHERE rtu_script IS NOT NULL AND rtu_script != '' AND rs_parse_status = 0");
        foreach ($rs as $k => $row) {
            $ret = get_rtu_data_set($row['rtu_script']);
            $parse_status = false;
            if (is_array($ret) && count($ret) > 0){
                $parse_status = true;
                foreach ($ret as $key => $r) {
                    $sid = $r['slave_id'] != 0 ? $r['slave_id'] : 1;
                    $slave_id_addr = $sid.'_'.$r['addr'];
                    if (!in_array($slave_id_addr, $slave_id_addr_s, true)){
                        $insert = $m->add(array(
                            'slave_id' => $sid,
                            'addr' => $r['addr'],
                            'name' => empty($r['name']) ? ($sid.'-'.$r['addr']) : $r['name'],
                            'unit' => empty($r['unit']) ? L('NOT_SET') : $r['unit'],
                            'value_type' => $r['value_type'],
                            'value_len' => $r['value_len'],
                            'min' => $r['min'],
                            'max' => $r['max'],
                        ));
                        if ($insert){
                            $slave_id_addr_s[] = $slave_id_addr;
                        }
                    }
                }
            }
            $m->execute(sprintf("UPDATE term_param SET rs_parse_status = %d WHERE sn = '%s'", $parse_status?1:2, $row['sn']));
        }
    }

    public function checkOnlinePercent(){
        $m = M('term');
        $total = $m->count('sn');
        $online = M('term_run_info')->where("is_online = 1 AND last_time >= '%s'", date('Y-m-d H:i:s',mktime()-C('TERM_OFFLINE_TIME')))->count('sn');
        echo sprintf('{"online":%d, "total":%d}', $online, $total);
    }

    //RTU传感量告警
    public function rtu_alarm(){
        // 日志文件
        G('begin');
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'rtu_alarm');
        header('Content-type:text/html; charset=utf-8');

        /* 1.读取文件(./Upload/rtu_data_alert_rules.php)，获取告警规则列表
         * 两种告警类型分开处理(单传感量在一个时间段内超阈值告警，多传感量联合告警)
         * 2.循环“单传感量在一个时间段内超阈值告警”规则，从历史数据文件中读取某一段时间的数据，查看是否满足规则，如满足则写入rtu_warning表
         * 3.从rtu_data读取最近的数据(这里要增加一个时间配置表示多长时间的数据为有效(例如1个小时))，循环查看是否满足告警规则，如满足则写入rtu_warning表
         */

        $rules = F('rtu_data_alert_rules', '', './Upload/');
        if (!$rules){
            $log->mwrite(date('Y-m-d H:i:s')."\tNo rules\r\n");
            die('没有告警规则，程序结束<br>');
        }

        //path = RTU历史数据文件路径，now = 当前时间戳
        $path = (C('DATA_PATH') != '' ? C('DATA_PATH') : '../data/').'rtu';
        // 1573295940 = 2019-11-09 18:39:00
        $now = 1573295940;
        $ymdhis = date('Y-m-d H:i:s', $now);

        //针对同一条告警规则，同一sn产生告警信息的频率，在config.php中有一个配置项：RTU_DATA_ALARM_INTERVAL = 5分钟(默认)
        $rtu_data_alarm_interval = C('RTU_DATA_ALARM_INTERVAL');
        if (!$rtu_data_alarm_interval) $rtu_data_alarm_interval = 5;
        $rtu_data_alarm_interval *= 60;

        //rtu_data表的数据，最近多长时间的可以用来判断是否产生告警，这个在config.php中有一个配置项：RTU_DATA_VALID_TIME = 60分钟(默认)
        $rtu_data_valid_time = C('RTU_DATA_VALID_TIME');
        if (!$rtu_data_valid_time) $rtu_data_valid_time = 60;
        $rtu_data_valid_time *= 60;

        //求传感量配置，key = slave_id_addr，value = (id, name, unit, value_type)
        $rs = M('rtu_data_set')->field('id, slave_id, addr, value_type, name, unit')->select();
        foreach ($rs as $k => $row) {
            $sets[$row['slave_id'].'_'.$row['addr']] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'unit' => $row['unit'],
                'value_type' => $row['value_type']
            );
        }
        unset($rs);

        //求rtu_data表最新数据
        $rs = M('rtu_data')->where("report_time >= '%s'", date('Y-m-d H:i:s', $now-$rtu_data_valid_time))->field('sn, addr, value')->select();
        foreach ($rs as $row) {
            $datas[$row['sn']][$row['addr']] = $row['value'];
        }
        unset($rs);

        foreach ($rules as $key => $row) {
            if (APP_DEBUG) {
                dump($row);
            }

            //统计报警数量
            $exists = 0;
            $trigger = 0;
            unset($records);

            G('rule_start');
            if ($row['rule_type'] == 0) {
                //多传感量联合告警
                foreach ($datas as $sn => $arr) {
                    if ($this->checkExistAlarm($sn, $row['id'], $now, $rtu_data_alarm_interval)) {
                        $exists += 1;
                        continue;
                    }
                    $quit = false;
                    $expressions = array();
                    $content = array();
                    foreach ($row['rule_detail'] as $rule) {
                        $slave_id_addr = $rule['slave_id_addr'];
                        $slave_id_addr_arr = explode('_', $slave_id_addr);
                        if (isset($arr[$slave_id_addr_arr[1]])) {
                            $tmpa = $arr[$slave_id_addr_arr[1]];
                            $tmpb = $rule['op'] == '=' ? '==' : $rule['op'];
                            $tmpc = $rule['value'];
                            eval('$bool = '."$tmpa $tmpb $tmpc ? true : false;");
                        } else {
                            //如果触发条件中的其中一个传感量没有数据，直接跳出，不产生告警
                            $quit = true;
                            break;
                        }
                        array_push($expressions, array($rule['bit_op'], $bool));
                        array_push($content, sprintf("%s %s %s %s %s (%s)", ($rule['bit_op']=='&'?'And':($rule['bit_op']=='|'?'Or':'')), $sets[$slave_id_addr]['name'], $tmpa, $rule['op'], $tmpc, $sets[$slave_id_addr]['unit']));
                    }

                    if ($quit){
                        continue;
                    }
                    if (count($expressions) == 1){
                        $ret = $expressions[0]['bool'];
                    }else{
                        $str = '$ret = ';
                        foreach ($expressions as $exp_index => $exp) {
                            $str .= sprintf(" %s %d", ($exp[0]=='&'?'&&':($exp[0]=='|'?'||':'')), intval($exp[1]));
                        }
                        eval($str.';');
                    }

                    if ($ret){
                        $trigger += 1;
                        $d = array(
                            'sn' => $sn,
                            'rtu_data_set_id' => $sets[$row[rule_detail][0]['slave_id_addr']]['id'], //有多个传感量时，插入第一个传感量的id
                            'warning_type' => 1,
                            'catch_time' => $ymdhis,
                            'value' => $row['id'], //此时value字段保存告警规则ID
                            'content' => implode('<br>', $content)
                        );
                        if (M('rtu_warning')->add($d)){
                            $this->checkExistAlarm($sn, $row['id'], $now, -1);
                        }
                    }
                }
            } elseif ($row['rule_type'] == 1) {
                //单传感量在一个时间段内超阈值告警
                $duration = $row['rule_detail'][0]['duration'] * 60; //持续时间，分转秒
                $slave_id_addr = $row['rule_detail'][0]['slave_id_addr'];
                $slave_id_addr_arr = explode('_', $slave_id_addr);

                //查找出在持续时间段内上报过数据的设备(rtu_data表report_time >= now - duration)
                $rs = M('rtu_data')->where("slave_id = %d AND addr = %d AND report_time >= '%s' AND value %s %s",
                    array($slave_id_addr_arr[0], $slave_id_addr_arr[1], date('Y-m-d H:i:s',$now-$duration), $row['rule_detail'][0]['op'], $row['rule_detail'][0]['value'])
                )->getField('sn', true);
                if (APP_DEBUG) {
                    echo M('rtu_data')->_sql().'<br>';
                    echo 'Rs = '.count($rs).', '.date('Y-m-d H:i:s',$now-$duration).' ~ '.date('Y-m-d H:i:s',$now).'<br>';
                }

                $read_file_times = 0;
                $read_file_n = 0;

                foreach ($rs as $sn) {
                    if ($this->checkExistAlarm($sn, $row['id'], $now, $rtu_data_alarm_interval)) {
                        $exists += 1;
                        continue;
                    }
                    $filename = sprintf("%s/%s/%s_%s.bin", $path, substr($sn, -1, 1), $sn, $slave_id_addr);
                    // G('check_start');
                    $check = $this->checkRtuDataInRange($now-$duration, $now, $filename, $sets[$slave_id_addr]['value_type'], $row['rule_detail'][0]['op'], $row['rule_detail'][0]['value']);
                    /*G('check_end');
                    if (APP_DEBUG){
                        $read_file_times += floatval(G('check_start','check_end'));
                        $read_file_n += 1;
                    }*/

                    if ($check) {
                        $trigger += 1;
                        $d = array(
                            'sn' => $sn,
                            'rtu_data_set_id' => $sets[$slave_id_addr]['id'],
                            'warning_type' => 2,
                            'catch_time' => $ymdhis,
                            'content' => sprintf("%s%s %s %s (%s)", str_replace('{_minutes_}', $duration/60, L('CONTINUED_SOME_MINUTES')), $sets[$slave_id_addr]['name'], $row['rule_detail'][0]['op'], $row['rule_detail'][0]['value'], $sets[$slave_id_addr]['unit'])
                        );
                        $this->checkExistAlarm($sn, $row['id'], $now, -1);
                        $records[] = $d;
                    }
                }
                if (isset($records)) {
                    M('rtu_warning')->addAll($records);
                }
            }
            G('rule_end');
            if (APP_DEBUG) {
                echo sprintf("<span style='color:red'>%d分钟内产生过告警的有：%d，告警生成有：%d，读取文件的平均时间为：%s，总时间为：%s，rule_time = %s</span><hr>", $rtu_data_alarm_interval/60, $exists, $trigger, $read_file_times/($read_file_n==0?1:$read_file_n), $read_file_times, G('rule_start','rule_end').'s');
            }
        }

        $this->checkExistAlarm(0, 0, 0, -2);
        //记录脚本执行时间
        G('end');
        $log->mwrite(date('Y-m-d H:i:s')."\t".G('begin','end')."s\r\n");
    }

    // 针对同一条告警规则，同一sn生成过告警信息的，直接跳过
    // 在Runtime下面创建文件(rtu_data_alarm_record)，文件存储一个数组，保存上次生成告警的时间戳(key = sn_告警ID，value = 时间戳)
    // $rtu_data_alarm_interval 大于等于0时表示查询，等于-1表示save，等于-2表示重新写入数组到文件
    private function checkExistAlarm($sn, $rule_id, $now, $rtu_data_alarm_interval){
        static $records = null;
        if (!$records) {
            $records = F('rtu_data_alarm_record', '', './Runtime/');
            if (!$records) {
                $records = array();
            }
        }
        $key = $sn.'_'.$rule_id;
        if ($rtu_data_alarm_interval >= 0 && $records[$key] && $now - $records[$key] <= $rtu_data_alarm_interval) {
            return true;
        } elseif ($rtu_data_alarm_interval == -1) {
            $records[$key] = $now;
        } elseif ($rtu_data_alarm_interval == -2) {
            F('rtu_data_alarm_record', $records, './Runtime/');
        }
        return false;
    }

    /* 查询一台设备，一段时间内的传感量是否都(>=, <=, >, <, =)某一数值
     * 从文件尾部开始，一次读取3600个数据块
     * 逆序查询每个数据是否满足条件
     */
    private function checkRtuDataInRange($start = 0, $end = 0, $filepath = '', $value_type = 4, $op = '', $threshold = 0){
        if (!file_exists($filepath)) {
            return false;
        }
        $fp = fopen($filepath, 'rb');
        fseek($fp, 0, SEEK_END);
        $size = ftell($fp);

        $data_len = 4; //数据长度
        switch ($value_type) {
            case 4:
                $format = 'f';
                break;
            default:
                $format = 'N';
                break;
        }
        $has_value = false;
        $unit = 8 + $data_len;
        $block = 3600 * $unit;
        $positions = $this->getReadPos($size, $block);

        foreach ($positions as $key => $pos) {
            fseek($fp, $pos, SEEK_SET);
            $str = fread($fp, $block);
            $len = strlen($str);
            $ts = unpack('N', substr($str, -$unit, 4));
            $ts = $ts[1];
            if ($ts < $start){
                break;
            }
            $quit = false;

            for ($i=$len-$unit; $i>=0; $i-=$unit) {
                $ts = unpack('N', substr($str, $i, 4));
                $ts = $ts[1];
                if ($ts < $start) {
                    $quit = true;
                    break;
                }
                $has_value = true;
                $v = unpack($format, substr($str, $i+8, $data_len));
                switch ($op) {
                    case '>=':
                        if ($v[1] < $threshold) {fclose($fp); return false;}
                        break;
                    case '<=':
                        if ($v[1] > $threshold) {fclose($fp); return false;}
                        break;
                    case '>':
                        if ($v[1] <= $threshold) {fclose($fp); return false;}
                        break;
                    case '<':
                        if ($v[1] >= $threshold) {fclose($fp); return false;}
                        break;
                    case '=':
                        if ($v[1] != $threshold) {fclose($fp); return false;}
                        break;
                    default:
                        fclose($fp);
                        return false;
                }
            }
            if ($quit) break;
        }

        fclose($fp);
        return $has_value ? true : false;
    }

    // 获取文件读取位置
    private function getReadPos($size, $block) {
        if ($size <= $block) {
            return array(0);
        }
        for ($i=1; $i<=intval($size/$block); $i++) {
            $positions[] = $size - $block * $i;
        }
        if ($size%$block != 0) {
            $positions[] = 0;
        }
        return $positions;
    }

    /* 提供给路由上传抓拍的图片
     *  0  OK
     * -1 sn不存在
     * -2 文件已存在
     * -3 未读取到文件内容
     */
    public function uplaodPhoto() {
        $sn = $_REQUEST['sn'];
        if (M('term')->where("sn='%s'",$sn)->count() == 0) die('-1');
        $filename = $sn.'_'.date('YmdHis').'.jpg';
        $relative_path = 'photo/'.date('Ym');
        $dir = C('FTP_WEB_PACK_PATH').$relative_path.'/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir.$filename;
        if (file_exists($path)) die('-2');
        $str = file_get_contents("php://input");
        if (!$str) die('-3');
        if (file_put_contents($path, $str)) {
            $tm = date('Y-m-d H:i:s');
            M('file_list')->add(array(
                'name' => $filename,
                'original_filename' => $filename,
                'filename' => $filename,
                'relative_path' => $relative_path,
                'filetype' => 7,
                'filesize' => filesize($path),
                'create_time' => $tm,
                'md5_num' => md5_file($path),
                'creator' => 'system',
                'sn' => $sn,
                'finish_status' => 1,
                'finish_time' => $tm
            ));
        }
        echo '0';
    }

    // onelink流水号
    private function getOneLinkSn() {
        $path = './Lib/one_link_cmd_sn';
        if (!file_exists($path)) {
            $cmd_sn = '80000001';
        } else {
            $cmd_sn = intval(file_get_contents($path)) + 1;
            if ($cmd_sn >= 99999999) $cmd_sn = '80000001';
        }
        file_put_contents($path, $cmd_sn);
        return $cmd_sn.'';
    }

    // onelink token，token有效时间是60分钟
    private function getOneLinkToken($log = null) {
        $token = S('one_link_token');
        if ($token) return $token;
        $url = C('ONE_LINK_URL').'ec/get/token?appid='.C('ONE_LINK_APPID').'&password='.C('ONE_LINK_PASSWORD').'&transid='.$this->getOneLinkTransid();
        $ret = Http::get($url, $log, ACTION_NAME);
        if ($ret && $ret['http_code'] == 200 && $ret['res']['status'] == '0') {
            S('one_link_token', $ret['res']['result'][0]['token'], 3600);
            return $ret['res']['result'][0]['token'];
        } else {
            return null;
        }
    }

    // onelink transid
    private function getOneLinkTransid() {
        // APPID+YYYYMMDDHHMISS+8位数字序列
        return C('ONE_LINK_APPID').date('YmdHis').$this->getOneLinkSn();
    }

    // 移动onelink接口查询流量，每天执行一次查询本月流量(不包括今天)
    public function queryOneLinkData() {
        $appid = C('ONE_LINK_APPID');
        $app_pwd = C('ONE_LINK_PASSWORD');
        if (!$appid || !$app_pwd) return;
        import("ORG.Net.Http");
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'one_link');
        $token = $this->getOneLinkToken($log);
        if (!$token) {
            die('Get token failed, more info in ./Log/one_link.txt');
        } else {
            $rs = M('term')->where("iccid IS NOT NULL")->field('sn, iccid')->select();
            $iccids = array();
            foreach ($rs as $key => $row) {
                $iccid_sn[$row['iccid']] = $row['sn'];
                array_push($iccids, $row['iccid']);
            }
            if (count($iccids) > 0) {
                // 该批量查询接口，每次最多支持100个iccid
                $iccids = array_chunk($iccids, 100);
                $url = C('ONE_LINK_URL').'ec/query/sim-data-usage-monthly/batch';
                $month = date('Ym'); //查询当月流量使用量
                $tm = date('Y-m-d H:i:s');
                foreach ($iccids as $iccid) {
                    $iccid = implode('_', $iccid);
                    $param = array(
                        'transid' => $this->getOneLinkTransid(),
                        'token' => $token,
                        'iccids' => $iccid,
                        'queryDate' => $month
                    );
                    $ret = Http::post($url, $param, false, $log, ACTION_NAME, 'application/json');
                    if (!$ret || $ret['http_code'] != 200 || $ret['res']['status'] != 0) continue;
                    foreach ($ret['res']['result'][0]['dataAmountList'] as $row) {
                        $insert_datas[] = array('sn' => $iccid_sn[$row['iccid']], 'iccid' => $row['iccid'], 'month_flux' => $row['dataAmount'], 'api_query_time' => $tm);
                        $delete_datas[] = "'".$iccid_sn[$row['iccid']]."'";
                    }
                }
                if (isset($delete_datas)) {
                    M('oem_onelink_flux')->where("sn IN(".implode(',', $delete_datas).")")->delete();
                    M('oem_onelink_flux')->addAll($insert_datas);
                }
            }
        }
    }

	// oneNet上报数据接口
	public function oneNet() {
        session_write_close();
		$msg = $_REQUEST['msg'];
		// die($msg); //验证
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'onenet_report_data');
		$str = file_get_contents("php://input");
		$log->mwrite(sprintf("%s [%s] data = %s\r\n", date('Y-m-d H:i:s'), ACTION_NAME, $str));
        if ($str && $str = json_decode($str, true)) {
            if ($str['msg']['type'] == 1) {
                $m = M('oem_onenet_report');
                $ts = date('Y-m-d H:i:s', $str['msg']['at']/1000);
                $id =$m->where("dev_id = '%s' AND report_time = '%s'", array($str['msg']['dev_id'], $ts))->getField('id');
                $arr = array($str['msg']['ds_id'] => $str['msg']['value'], 'report_time' => $ts);
                if (!$id) {
                    $arr['dev_id'] = $str['msg']['dev_id'];
                    $m->add($arr);
                } else {
                    $m->where('id = %d', $id)->save($arr);
                }
            }
        }
		header("HTTP/1.0 200 OK");
		die('OK');
	}

    // 添加测试数据
    public function testdata() {
        /*插入模拟设备*/
        /*$now = date('Y-m-d H:i:s');
        for ($i=1; $i<=5000; $i++){
            $sn = str_repeat('0', 8-strlen($i)).$i;
            M('term')->add(array(
                'sn' => $sn,
                'alias' => 'test alarm',
                'vsn' => 'vsn0919',
                'sw_version' => '1.0.0.0',
                'imei' => 'imei-test',
                'sim' => 'sim-test',
                'imsi' => 'imsi-test',
                'iccid' => 'iccid-test',
                'term_model' => 'R50',
                'ud_sn' => 'test0919'
            ));
            M('term_run_info')->add(array(
                'sn' => $sn,
                'name' => 'test0919_R'.$sn,
                'ip' => '192.168.10.198',
                'port' => 1000,
                'is_online' => 1,
                'first_login' => $now,
                'login_time' => $now,
                'last_time' => $now,
                'net_mode' => 10,
                'term_signal' => 13,
                'operator' => 'china unicom',
                'frequency' => '1800MHZ'
            ));
        }*/

        /*写入模拟数据
        $sets = M('rtu_data_set')->getField('addr', true);
        foreach ($sets as $k => $addr) {
            $fps[$addr] = fopen(C('DATA_PATH').'rtu/1/00000001_1_'.$addr.'.bin', 'wb+');
            if (!$fps[$addr]){
                die('Open file failed...'.$addr);
            }
        }
        $ts = strtotime('2019-09-01 08:00:00');
        for ($i=0; $i<100000; $i++){
            foreach ($sets as $k => $addr) {
                $v = $addr == '300' ? 0 : mt_rand(0,100);
                $start = $ts + $i*60;
                $tmp = pack("N*", $start);
                fwrite($fps[$addr], $tmp);
                fwrite($fps[$addr], $tmp);
                fwrite($fps[$addr], pack("f*",$v));
            }
        }
        foreach ($fps as $k => $fp) {
            if ($fp) {
                fclose($fp);
            }
        }
        */

        /*复制数据文件
        $sns = M('term')->where("sn != '00000001'")->limit(499)->getField('sn', true);
        $addrs = M('rtu_data_set')->getField('addr', true);
        $path = C('DATA_PATH').'rtu/';
        foreach ($sns as $k => $sn) {
            $last_char = substr($sn, -1, 1);
            foreach ($addrs as $key => $addr) {
                copy($path.'1/00000001_1_'.$addr.'.bin', $path.$last_char.'/'.$sn.'_1_'.$addr.'.bin');
            }
        }*/

        /*插入rtu_data数据
        $sns = M('term')->where("sn != '00000001'")->limit(499)->getField('sn', true);
        $addrs = M('rtu_data_set')->getField('addr', true);
        foreach ($sns as $k => $sn) {
            $now = date('Y-m-d H:i:s');
            foreach ($addrs as $key => $addr) {
                M('rtu_data')->add(array(
                    'slave_id' => 1,
                    'addr' => $addr,
                    'sn' => $sn,
                    'report_time' => $now,
                    'catch_time' => $now,
                    'value' => $addr == 300 ? 0 : rand(0,100)
                ));
            }
        }*/
    }

    // 客户定制功能(5个报表通过邮件定时发送)
    public function sendReport() {
        $recv = C('REPORT_RECV_EMAILS');
        if (!$recv) {
            die('REPORT_RECV_EMAILS is not set, exit.');
        }
        import('@.ORG.PHPExcel');
        $PHPExcel = new PHPExcel();

        $m = M('term');
        $arr = C('NET_MODE');
        $tgids = $this->getTgids();
        $sns = $m->where("group_id IN(%s)",$tgids)->getField('sn',true);
        $q = "sn IN('".implode("','", $sns)."')";
        $nowdate = date('Ymd');
        $startdate = date('Y-m-d 00:00:00');
        $enddate = date('Y-m-d 23:59:59');

        // 添加第一个工作表(设备报表)
        $PHPExcel->setActiveSheetIndex(0); // 设置活跃的工作表索引到第一个工作表
        $sheet1 = $PHPExcel->getActiveSheet(); // 获取活跃的工作表
        $sheet1->getDefaultColumnDimension()->setWidth(25); // 设置所有列的默认宽度
        $sheet1->setTitle(L('DEVICE_REPORT')); // 设置工作表的标题

        $onelink = C('SHOW_ONELINK_MONTH_FLUX');
        $header_sb = array(L('VAR_TERM_STATUS'), L('TERM_GROUP_LIST'), L('VAR_SN2'), L('VAR_SN1'), L('VAR_VSN'), L('DEVICE_MODEL'), L('VAR_SYSCFG_ALIAS'),
            L('VAR_IP'), L('VAR_PORT'), L('VAR_TERM_FLUX'), L('TODAY_FLUX'), L($onelink ? 'ONELINK_MONTH_FLUX' : 'FLUX_CURRENT_MONTH'),
            L('NET_MODE'), L('VAR_TERM_SIGNAL'), 'RSSI', 'RSRP', 'RSRQ', L('VAR_SWV'), L('PROTOCOL_VERSION'),
            L('WORKING_FREQUENCY'), L('ONLINE_DURATION'), L('VAR_LOGOUT_RECORD'), L('VAR_FIRST_LOGIN'), L('VAR_DEVICE_LOGIN_TIME'), L('VAR_LAST_LOGIN'),
            'SIM', 'IMSI', 'ICCID', 'IMEI', L('TERM_MODULE_VENDOR'), L('TERM_MODULE_TYPE'),
            'SSID', L('GUIJI_CODE'), L('VAR_OPERATOR').' 1', L('VAR_OPERATOR').' 2', 'VPN', L('SIM_POS')
        );
        if ($this->lang == 'zh-cn') {
            $header_sb = array_merge($header_sb, array(L('VAR_BASE_ADDRESS'), L('VAR_WIFI_MAC'), L('VAR_POSITION')));
        }
        $rs_sb = $m->join("LEFT JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN term_cell ON term_cell.sn = term.sn LEFT JOIN term_wifi_ap ON term_wifi_ap.sn = term.sn
            LEFT JOIN term_group ON term_group.id = term.group_id
            LEFT JOIN oem_onelink_flux ON oem_onelink_flux.sn = term.sn")
            ->field('term.*, term_run_info.*, term_group.name AS gname, term_cell.lac, term_cell.cellid, term_cell.addr, term_wifi_ap.ap_mac, term_wifi_ap.addr AS addr2,
                oem_onelink_flux.month_flux AS month_flux_onelink, oem_onelink_flux.api_query_time')
            ->order('term_run_info.is_online DESC, term_run_info.last_time DESC')
            ->select();
        $rs_sb2 = M('term_virtual_channel')->join("LEFT JOIN term ON term_virtual_channel.sn = term.sn")
            ->field('term.sn, term_virtual_channel.vpn_type, term_virtual_channel.is_online, term_virtual_channel.last_time')
            ->order('term_virtual_channel.last_time DESC')
            ->select();
        $now = date('Y-m-d H:i:s');
        $ts = time();
        foreach ($rs_sb as $k => $row) {
            if (!in_array($row['sn'],$sns,true)) continue;
            foreach ($rs_sb2 as $k2 => $row2) {
                if ($row['sn'] == $row2['sn']) {
                    $s = get_term_status_code($ts - strtotime($row2['last_time']), $row2['is_online'], true);
                    $row['vpn'] .= ($row['vpn']?',':'') . ($row2['vpn_type']) . ($row2['vpn_type']?('(' . L($s == '1'?'VAR_TERM_STATUS_ONLINE':'VAR_TERM_STATUS_OFFLINE') . ')'):'');
                }
            }
            $s = get_term_status_code(strtotime($now)-strtotime($row['last_time']), $row['is_online']);
            $row['flux']       = bitsize($row['flux']);
            $row['day_flux']   = $this->get_day_flux($row['day_flux'], $row['last_time']);
            $row['month_flux'] = $this->get_month_flux($row['month_flux'], $row['last_time'], $row['month_flux_onelink'], $row['api_query_time']);
            $row['net_mode'] = $nm[$row['net_mode']];
            $row['protocol'] = intval($row['protocol']/10) . '.' . $row['protocol']%10;
            $row['online_duration'] = $s=='0' ? 0 : format_time($row['login_time'], $row['last_time']);
            $row['offline_duration'] = $s=='1' ? 0 : format_time($row['last_time'], $now);
            if ($row['lac'] && $row['cellid']) {
                $row['lac_cellid'] = $row['lac'].','.$row['cellid'];
            }
            $row['term_model'] = $this->getTermModelText($row['term_model'], 1);
            $sim_pos = $row['sim_pos'] == 0 ? sprintf('%s 1 + %s 2', L('CARD'), L('CARD')) : sprintf('%s %d', L('CARD'), $row['sim_pos']);

            $tmp_row = array(L($s=='1'?'VAR_TERM_STATUS_ONLINE':'VAR_TERM_STATUS_OFFLINE'), $row['gname'], $row['sn'], $row['ud_sn'], $row['vsn'], $row['term_model'], $row['alias'],
                $row['ip'], $row['port'], $row['flux'], $row['day_flux'], $row['month_flux'],
                $row['net_mode'], $row['term_signal'], $row['rssi'], $row['rsrp'], $row['rsrq'], $row['sw_version'], $row['protocol'],
                $row['frequency'], $row['online_duration'], $row['offline_duration'], $row['first_login'], $row['login_time'], $row['last_time'],
                $row['sim'], $row['imsi'], $row['iccid'], $row['imei'], $row['module_vendor'], $row['module_type'],
                $row['wifi_ssid'], $row['host_sn'], $row['operator_sim1'], $row['operator_sim2'], $row['vpn'], $sim_pos
            );
            $data_sb[] = $this->lang == 'zh-cn' ? array_merge($tmp_row, array($row['lac_cellid'], $row['ap_mac'], $row['addr'] ? $row['addr'] : $row['addr2'])) : $tmp_row;
        }
        array_unshift($data_sb, $header_sb);

        $row_sb = 1;
        foreach ($data_sb as $rowData) {
            $column = 0; // 从A列开始
            foreach ($rowData as $cellData) {
                $sheet1->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($column) . $row_sb, $cellData, PHPExcel_Cell_DataType::TYPE_STRING);
                $column++;
            }
            $row_sb++;
        }
         
        // 添加第二个工作表(流量报表)
        $sheet2 = $PHPExcel->createSheet(); // 创建新的工作表
        $sheet2->getDefaultColumnDimension()->setWidth(25); // 设置所有列的默认宽度
        $sheet2->setTitle(L('FLUX_REPORT')); // 设置工作表的标题
        $header_ll = array(L('VAR_TG'), L('VAR_SN2'), L('VAR_SN1'), L('VAR_VSN'), L('DEVICE_MODEL'), L('VAR_SYSCFG_ALIAS'));

        $rs_ll = $m->query("SELECT tg.name gname, t.sn, t.ud_sn, t.vsn, t.term_model, t.alias FROM term t LEFT JOIN term_group tg ON tg.id = t.group_id WHERE $q");
        foreach ($rs_ll as $k => $row) {
            $row['term_model'] = $this->getTermModelText($row['term_model'], 1);
            $sns_ll[$row['sn']] = array_values($row);
        }
        unset($rs_ll);

        $rs_ll2 = $m->query("SELECT sn, flux, report_day FROM term_stat_info WHERE report_day = $nowdate");

        foreach ($rs_ll2 as $k=>$row) {
            if (!isset($sns_ll[$row['sn']])) continue;
            $key = $type == 0 ? $row['report_day'] : substr($row['report_day'], 0, 6);
            $rs_ll3[$row['sn']][$key] += $row['flux'];
        }
        unset($rs_ll2);

        array_push($header_ll, date('m-d'));

        $k = 0;
        foreach ($sns_ll as $sn => $row) {
            $data_ll[$k] = $row;
            array_push($data_ll[$k], bitsize($rs_ll3[$sn][$nowdate]));
            $k++;
        }

        array_unshift($data_ll, $header_ll);
        $row_ll = 1;
        foreach ($data_ll as $rowData) {
            $column = 0; // 从A列开始
            foreach ($rowData as $cellData) {
                $sheet2->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($column) . $row_ll, $cellData, PHPExcel_Cell_DataType::TYPE_STRING);
                $column++;
            }
            $row_ll++;
        }

        // 添加第三个工作表(信号报表)
        $sheet3 = $PHPExcel->createSheet(); // 创建新的工作表
        $sheet3->getDefaultColumnDimension()->setWidth(25); // 设置所有列的默认宽度
        $sheet3->setTitle(L('SIGNAL_REPORT')); // 设置工作表的标题
        $header_xh = array(L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), 'IP', L('SIGNAL_AVERAGE'));

        $rs_xh = $m->field('term.sn, ud_sn, alias, ip')->join('LEFT JOIN term_run_info ON term_run_info.sn = term.sn')
            ->where("term.$q")->order('term.sn ASC')->select();
        $rs_xh2 = $m->query("SELECT sn, report_day, hb_count, sum_signal FROM term_stat_info WHERE report_day = $nowdate AND $q");
        $rs_xh3 = array();

        foreach ($rs_xh2 as $key => $row) {
            if (!isset($rs_xh3[$row['sn']][$row['report_day']])) {
                $rs3[$row['sn']][$row['report_day']] = array('count'=>0, 'signal'=>0);
            }
            $rs_xh3[$row['sn']][$row['report_day']]['count'] += $row['hb_count'];
            $rs_xh3[$row['sn']][$row['report_day']]['signal'] += $row['sum_signal'];
        }
        foreach ($rs_xh3 as $sn => $days) {
            foreach ($days as $k => $row) {
                $rs_xh3[$sn][$k] = $row['count'] == 0 ? 0 : round($row['signal']/$row['count'], 2);
            }
        }
        unset($rs_xh2);

        array_push($header_xh, date('n-j'));

        foreach ($rs_xh as $key => $row) {
            $data_xh[$key] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['ip'], 0);
            $sum = 0;
            if (isset($rs_xh3[$row['sn']][$nowdate])) {
                array_push($data_xh[$key], $rs_xh3[$row['sn']][$nowdate]);
                $sum = $rs_xh3[$row['sn']][$nowdate];
            } else {
                array_push($data_xh[$key], 0);
            }
            $data_xh[$key][4] = round($sum/1, 2);
        }

        array_unshift($data_xh, $header_xh);
        $row_xh = 1;
        foreach ($data_xh as $rowData) {
            $column = 0; // 从A列开始
            foreach ($rowData as $cellData) {
                $sheet3->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($column) . $row_xh, $cellData, PHPExcel_Cell_DataType::TYPE_STRING);
                $column++;
            }
            $row_xh++;
        }


        // 添加第四个工作表(上线记录)
        $sheet4 = $PHPExcel->createSheet(); // 创建新的工作表
        $sheet4->getDefaultColumnDimension()->setWidth(25); // 设置所有列的默认宽度
        $sheet4->setTitle(L('VAR_DEVICE_HISTORY')); // 设置工作表的标题
        $header_sx = array(L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), 'IP', L('SERVER_PORT'), L('VAR_LOGIN_TIME'), L('VAR_LOGOUT_TIME'), L('VAR_TERM_SIGNAL'), L('VAR_TERM_FLUX'));

        $q_sx = "term_login_record.sn IN('".implode("','", $sns)."')";
        $rs_sx = M('term_login_record')->field('term.sn, term.ud_sn, term.alias, term_login_record.ip, term_login_record.port,
            term_login_record.login_time, term_login_record.logout_time, term_login_record.term_signal, term_login_record.flux')
            ->join('LEFT JOIN term ON term.sn = term_login_record.sn')
            ->where("login_time BETWEEN '$startdate' AND '$enddate' AND $q_sx")
            ->order('sn ASC, term_login_record.login_time ASC')->select();
        foreach ($rs_sx as $key => $row) {
            $data_sx[] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['ip'], $row['port'], $row['login_time'], $row['logout_time'], $row['term_signal'], bitsize($row['flux']));
        }

        array_unshift($data_sx, $header_sx);
        $row_sx = 1;
        foreach ($data_sx as $rowData) {
            $column = 0; // 从A列开始
            foreach ($rowData as $cellData) {
                $sheet4->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($column) . $row_sx, $cellData, PHPExcel_Cell_DataType::TYPE_STRING);
                $column++;
            }
            $row_sx++;
        }

        // 添加第五个工作表(网络切换记录)
        $sheet5 = $PHPExcel->createSheet(); // 创建新的工作表
        $sheet5->getDefaultColumnDimension()->setWidth(25); // 设置所有列的默认宽度
        $sheet5->setTitle(L('NET_CHANGE_RECORD')); // 设置工作表的标题
        $header_wl = array(
            L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_DEVICE_URL_REPORT_TIME'),
            L('OLD_VALUE') . '(' .L('NET_MODE').        ')',
            L('NEW_VALUE') . '(' .L('NET_MODE').        ')',
            L('OLD_VALUE') . '(' .L('SIM_CARD_NUMBER'). ')',
            L('NEW_VALUE') . '(' .L('SIM_CARD_NUMBER'). ')'
        );
        $q_wl = "term_net_mode_record.sn IN('".implode("','", $sns)."')";
        $rs_wl = M('term_net_mode_record')->field('term.sn, term.ud_sn, term.alias, term_net_mode_record.report_time,
            old_value, new_value, old_sim, new_sim')
            ->join('LEFT JOIN term ON term.sn = term_net_mode_record.sn')
            ->where("$q_wl AND report_time BETWEEN '$startdate' AND '$enddate'")
            ->order('sn ASC, report_time ASC')->select();
        foreach ($rs_wl as $key => $row) {
            $data_wl[] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['report_time'], $arr[$row['old_value']], $arr[$row['new_value']],
                !empty($row['old_sim']) ? L('CARD').' '.$row['old_sim'] : '',
                !empty($row['new_sim']) ? L('CARD').' '.$row['new_sim'] : ''
            );
        }

        array_unshift($data_wl, $header_wl);
        $row_wl = 1;
        foreach ($data_wl as $rowData) {
            $column = 0; // 从A列开始
            foreach ($rowData as $cellData) {
                $sheet5->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($column) . $row_wl, $cellData, PHPExcel_Cell_DataType::TYPE_STRING);
                $column++;
            }
            $row_wl++;
        }
        $fpath = './Runtime/report_'.date('YmdHis').'.xlsx';
        $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save($fpath);
        $this->sendmail($recvs, 'M2M report', 'Please refer to the attachment.', '', null, $fpath);
        unlink($fpath);
        die('ok');
    }

    public function taskCallback() {
        import("ORG.Net.Http");
        import('@.ORG.Mlog');
        $url = C('TASK_RETURN_URL'); // 任务结果回调地址
        if (!$url) {
            die('TASK_RETURN_URL not set！');
        }
        $rs = M('term_task_detail')
            ->field('term_task_detail.*, term_task.cmd')
            ->join('LEFT JOIN term_task ON term_task.id = term_task_detail.task_id')
            ->where("is_callback = 1 AND callback_time = '0000-00-00 00:00:00' AND status >= 3")
            ->select();
        if (!$rs) {
            die('No data！');
        }
        $sns = array();
        foreach ($rs as $key => $row) {
            if (!in_array($row['sn'], $sns)) {
                array_push($sns, $row['sn']);
            }
        }
        $params_arr = M('term_param')->where(sprintf("sn in ('%s')", implode("','", $sns)))->getField('sn, param', true);
        $now = date('Y-m-d H:i:s');
        foreach ($rs as $key => $row) {
            $arr = array();
            if ($row['cmd'] == 'config_get' && $row['status'] == 3) {
                $req_names = explode(',', $row['callback_params']); // 请求返回的参数
                $params_val = array();  // 表里面存储的参数
                if (isset($params_arr[$row['sn']])) {
                    $tmp = explode('&', $params_arr[$row['sn']]);
                    foreach ($tmp as $k => $v) {
                        $i = strpos($v, '=');
                        if ($i === false || strlen($v) == $i+1) continue;
                        $params_val[substr($v,0,$i)] = preg_replace("/^\"|\"$/", "", substr($v,$i+1));
                    }
                    foreach ($req_names as $name) {
                        if (isset($params_val[$name])) {
                            $arr[$name] = $params_val[$name];
                        }
                    }
                }
            }
            $json = array(
                'task_id' => $row['id'],
                'status' => $row['status']
            );
            if ($row['cmd'] == 'config_get') {
                $json['params'] = $arr;
            }
            $ret = Http::post($url, $json, false, false, ACTION_NAME, 'application/json');
            if (!$ret || $ret['http_code'] != 200 || $ret['res']['status'] != 0) continue;
            M('term_task_detail')->where(sprintf('id = %d', $row['id']))->save(array('callback_time' => $now));
        }
    }

    public function deleteGPSData() {
        $m = M('system_config');
        $row = $m->where("name = 'auto_clear_gps'")->find();
        if (!$row['value']) {
            return;
        }
        $row2 = $m->where("name = 'gps_reserve_days'")->find();
        if (!is_numeric($row2['value']) || $row2['value'] <= 0){
            return;
        }
        $date_before = date('Ymd', strtotime('-'. $row2['value'] .' days'));

        $path = C('FTP_WEB_PACK_PATH') . 'gps/';
        $filenames = scandir($path);

        foreach($filenames as $f){
            if ($f == 'gps_current.bin') continue;
            if (strpos($f, 'gps_data_') !== 0) continue;
            $tmp = explode('_', $f);
            $file_date = explode('.', $tmp[2]);
            if ($file_date[0] <= $date_before) {
                $file = $path . $f;
                unlink($file);
            }
        }
    }
}