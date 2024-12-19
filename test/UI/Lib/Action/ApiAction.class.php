<?php
class ApiAction extends CommonAction{
    public function _initialize(){
        session_write_close();
    }

    public function m2m(){
        $str = file_get_contents("php://input");
        $p = json_decode($str, true);
        if (!$p) {
            die(json_encode(array('ret' => 4, 'msg' => 'Json parsing failed')));
        }
        // dump($p);exit;
        $cmd = $p['cmd'];
        if ($cmd == 'auth') {
            // 获取key，用于后续接口调用凭证，有效期为一个小时
            $row = M('usr')->where("name = '%s' AND password = '%s'", array($p['params']['username'],$p['params']['password']))->find();
            if (!$row){
                die(json_encode(array('ret' => 1, 'msg' => 'Authentication failed')));
            }
            $key = md5($p['params']['username'].mktime().$p['params']['password']);
            S($key, $row, 3600);
            die(json_encode(array('ret' => 0, 'msg' => 'ok', 'data' => array('key' => $key))));
        }else {
            if (!method_exists($this, $cmd)){
                die(json_encode(array('ret' => 5, 'msg' => 'Cmd does not exist')));
            }
            if ($p['key'] && S($p['key'])) {
                $p['params']['key'] = $p['key'];
                call_user_func_array(array($this,$cmd), $p['params']);
            } else {
                die(json_encode(array('ret' => 1, 'msg' => 'Authentication failed')));
            }
        }
    }

    /**
     * 指令用于查询当前系统中所有设备序列号
     * @param  [string]    $cmd    get_all_sn
     * @param  [string]    $key
     * @return [json]
     */
    private function get_all_sn($p){
        $cache = S($p);
        $uid = $cache['id'];
        $q = '1=1';
        if ($uid != 1) {
            $tgids = $this->getTgids('string', $uid);
            $q = "group_id IN($tgids)";
        }
        $rs = M('term')->where($q)->getField('sn',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 指令用于查询指定的一批设备详细运行信息
     * @param  [string]    $cmd         get_device_info
     * @param  [string]    $key
     * @param  [string]    $string     ["sn1", "sn2", "sn3"]
     * @return [json]
     */
    private function get_device_info($sn){
        $sn = implode("','", $sn);
        $rs = M('term_run_info')
            ->join('INNER JOIN term ON term.sn = term_run_info.sn')
            ->where("term.sn IN('$sn')")
            ->field('term.sn, term.ud_sn, ip, port, is_online, first_login, login_time, last_time, term_signal, net_mode, flux, month_flux, imei, imsi, iccid, sim, sw_version')
            ->order('sn ASC')
            ->select();
        $nm = C('NET_MODE');
        $now = mktime();
        foreach ($rs as $k => $row) {
            $rs[$k]['net_mode'] = $nm[$row['net_mode']];
            $rs[$k]['is_online'] = get_term_status_code($now-strtotime($row['last_time']), $row['is_online']);
        }
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 指令用于查询一台设备的详细运行信息
     * @param  [string]    $cmd         get_one_device_info
     * @param  [string]    $key
     * @param  [string]    $query_field  查询字段名称
     * @param  [string]    $query_str    查询字段值
     * @return [json]
     */
    private function get_one_device_info($query_field, $query_str){
        if ($query_field == 'iccid') {
            $q = sprintf("(iccid = '%s' OR iccid2 = '%s')", $query_str, $query_str);
        } else {
            $q = sprintf("term.%s = '%s'", $query_field, $query_str);
        }
        $row = M('term_run_info')
            ->join('INNER JOIN term ON term.sn = term_run_info.sn')
            ->where($q)
            ->field('term.sn, term.ud_sn, ip, port, is_online, first_login, login_time, last_time, term_signal, net_mode, flux, month_flux, imei, imsi, iccid, sim, sw_version')
            ->find();
        $nm = C('NET_MODE');
        if ($row) {
            $row['net_mode'] = $nm[$row['net_mode']];
            $row['is_online'] = get_term_status_code(time() - strtotime($row['last_time']), $row['is_online']);
        }
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => $row ? $row : null
        );
        echo json_encode($arr);
    }

    /**
     * 指令用于查询一台设备的lan口信息
     * @param  [string]    $cmd         get_lan_info
     * @param  [string]    $key
     * @param  [string]    $sn  设备序列号
     * @return [json]
     */
    private function get_lan_info($sn){
        $ret = $this->getLanStatus($sn);
        $link_status = array_reverse($ret['lan_connect_status']);
        $link_status = array_merge(array($ret['wan_connect_status']), $link_status);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'link_status' => $link_status,
                'enable_status' => $ret['wan_lan_enables']
            )
        );
        echo json_encode($arr);
    }

    /**
     * 查询指定设备在一段时间内，每天的流量使用详情
     * @param  [string]     $sn
     * @param  [string]     $begin    2018-01-01
     * @param  [string]     $end      2018-01-31
     * @return [json]
     */
    private function get_device_flux($sn, $begin, $end){
        $rs = $this->read_stat($sn, strtotime($begin.' 00:00:00'), strtotime($end.' 23:59:59'), '', '');
        $begin = str_replace('-', '', $begin);
        $end = str_replace('-', '', $end);
        for ($i=$begin; $i<=$end;) {
            $list[$i] = 0;
            $i = date('Ymd', strtotime('+1 day',strtotime($i)));
        }
        foreach ($rs as $k => $row) {
            $list[$row['report_day']] += $row['flux'];
        }
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => array_values($list)
            )
        );
        echo json_encode($arr);
    }

    /**
     * 重启指定设备
     * @param  [string]     $sn    ["sn1", "sn2", "sn3"]
     * @return [json]
     */
    private function reset_device($sn, $key){
        $value = '';
        $row = S($key);
        $ugid = $row['gid'];
        $cmd = 'restart';
        $create_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+1 month'));

        $md = M('term_task_detail');
        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (username,cmd,value,start_time,end_time,ugid)VALUES('%s','%s','%s','%s','%s',%d)", $row['name'], $cmd, $value, date('Y-m-d H:i:s'), $end_time, $ugid));
        $lastId = $md->getLastInsID();
        foreach ($sn as $k => $v) {
            $dataList[] = array(
                'task_id' => $lastId,
                'sn' => $v,
                'end_time' => $end_time
            );
        }

        if ($lastId && $md->addAll($dataList)){
            $md->commit();
            $this->wlog($row['name'], $cmd, $value, "type=term&sns=".implode(",", $sn), $ugid, '');
        }
        $rs = M('term_task_detail')->where("task_id = $lastId")->getField('id',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 启用/禁用Lan口，一次只能下发一台设备
     * @param  [string]     $sn
     * @param  [string]     $seq 表示lan口顺序，0=wan，1=lan1...4=lan4
     * @param  [string]     $enable 0表示禁用，1表示启用
     * @return [json]
     */
    private function enable_lan($sn, $seq, $enable, $key){
        $value = sprintf('port=lan&seq=%d&name=status&value=%d', $seq, $enable);
        $row = S($key);
        $ugid = $row['gid'];
        $cmd = 'interface_set';
        $create_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+1 month'));

        $md = M('term_task_detail');
        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (username,cmd,value,start_time,end_time,ugid)VALUES('%s','%s','%s','%s','%s',%d)", $row['name'], $cmd, $value, date('Y-m-d H:i:s'), $end_time, $ugid));
        $lastId = $md->getLastInsID();
        $dataList = array(
            'task_id' => $lastId,
            'sn' => $sn,
            'end_time' => $end_time
        );

        if ($lastId && $md->add($dataList)){
            $md->commit();
            $this->wlog($row['name'], $cmd, $value, "type=term&sns=".$sn, $ugid, '');
        }
        $rs = M('term_task_detail')->where("task_id = $lastId")->getField('id',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 查询设备参数
     * @param  [array]     $sn    ["sn1", "sn2", "sn3"]
     * @param  [string]    $names  "name, age"
     * @return [json]
     */
    private function config_get($sn, $names, $key){
        $row = S($key);
        $ugid = $row['gid'];
        $cmd = 'config_get';
        $create_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+1 month'));
        $md = M('term_task_detail');
        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (username,cmd,value,start_time,end_time,ugid)VALUES('%s','%s','%s','%s','%s',%d)", $row['name'], $cmd, '', $create_time, $end_time, $ugid));
        $lastId = $md->getLastInsID();
        foreach ($sn as $k => $v) {
            $dataList[] = array(
                'task_id' => $lastId,
                'sn' => $v,
                'end_time' => $end_time,
                'is_callback' => 1,
                'callback_params' => $names
            );
        }

        if ($lastId && $md->addAll($dataList)){
            $md->commit();
            $this->wlog($row['name'], $cmd, $value, sprintf('type=term&sns=%s&value=%s', implode(',', $sn), ''), $ugid, '');
        }
        $rs = $md->where("task_id = $lastId")->getField('id',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 修改设备参数
     * @param  [array]     $sn    ["sn1", "sn2", "sn3"]
     * @param  [array]     $vals  {"name":"laowang", "age":18}
     * @return [json]
     */
    private function config_set($sn, $vals, $key){
        $value = array();
        $row = S($key);
        $ugid = $row['gid'];
        $cmd = 'config_set';
        $create_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+1 month'));
        foreach ($vals as $k => $v) {
            array_push($value, sprintf('%s="%s"', $k, $v));
        }
        $value = implode('&', $value);

        $md = M('term_task_detail');
        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (username,cmd,value,start_time,end_time,ugid)VALUES('%s','%s','%s','%s','%s',%d)", $row['name'], $cmd, $value, $create_time, $end_time, $ugid));
        $lastId = $md->getLastInsID();
        foreach ($sn as $k => $v) {
            $dataList[] = array(
                'task_id' => $lastId,
                'sn' => $v,
                'end_time' => $end_time,
                'is_callback' => 1,
            );
        }

        if ($lastId && $md->addAll($dataList)){
            $md->commit();
            $this->wlog($row['name'], $cmd, $value, sprintf('type=term&sns=%s&value=%s', implode(',', $sn), $value), $ugid, '');
        }
        $rs = $md->where("task_id = $lastId")->getField('id',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }

    /**
     * 查询任务执行状态
     * @param  [string]     $id     ["id1", "id2", "id3"]
     * @return [json]
     */
    private function get_task_status($id){
        $id = implode(",", $id);
        $rs = M('term_task_detail')->where("id IN($id)")->field('id,status')->select();
        $tsa = L('VAR_TASK_STATUS_ARR');
        foreach ($rs as $k => $row) {
            $list[$row['id']] = $tsa[$row['status']];
        }
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => isset($list) ? $list : array()
            )
        );
        echo json_encode($arr);
    }

    //传过来的devid参数中首字母为R的，去掉R
    private function getsn(){
        $sn = $_REQUEST['devid'];
        if (strlen($sn) == 9 && substr($sn, 0, 1) == 'R'){
            $sn = substr($sn, 1);
        }
        return $sn;
    }

    /**
     * 获取key，用于后续接口调用凭证，有效期为一个小时
     * @param string name 用户名
     * @param string password 密码
     * @return json 获取成功返回{key:"xxx"}，失败返回{error:"err info"}
     */
    public function getKey(){
        $name = $_REQUEST['name'];
        $password = $_REQUEST['password'];
        $m = M('usr');
        $row = $m->where("name = '%s' AND password = '%s'", array($name,$password))->find();
        if (!$row){
            die(json_encode(array(
                'error' => '用户名或密码错误'
            )));
        }
        $key = md5($name.mktime().$password);
        S($key, $row, 3600);
        echo json_encode(array(
            'key' => $key
        ));
    }

    /**
     * 检查key
     * @return array 用户信息
     */
    private function checkKey(){
        $key = $_REQUEST['key'];
        $row = S($key);
        if (!$row){
            die(json_encode(array(
                'error' => 'Key error'
            )));
        }
        return $row;
    }

    /**
     * 网关信息上报
     * @param string key 接口调用凭证
     * @return json数组
     */
    public function GWList_RLY(){
        $row = $this->checkKey();
        $m = M('wanrui_dev_info');
		$sn = $this->getsn();
		if($sn==""){
			die(json_encode(array(
                'error' => '请传入正确的参数'
            )));
		}
		$sql ="SELECT a.appid, a.sn, a.mac, b.ip, b.port, a.prjcode, a.prjname, a.address, a.longitude, a.latitude, a.name, b.last_time, a.create_time FROM rtu_project_info a
			LEFT JOIN term_run_info b ON b.sn = a.sn
			WHERE a.sn = '$sn'";
		$rs = $m->query($sql);
		if(empty($rs)){
			die(json_encode(array(
                'error' => '指定设备不存在'
            )));
		}
		$rs = $rs[0];
		$data = array(
			"appid" => $rs['appid'],
			"devid" => $rs['sn'],
			"mac"   => $rs['mac'],
			"ip"    => $rs['ip'],
			"port"  => $rs['port'],
			"prjcode" => $rs['prjcode'],
			"prjname" => $rs['prjname'],
			"address" => $rs['address'],
			"longitude" => $rs['longitude'],
			"latitude"  => $rs['latitude'],
			"name"  => $rs['name'],
			"lastact"  => $rs['last_time'],
			"createdAt"  => $rs['create_time'],
		);
        echo json_encode($data);
    }

    /**
     * 批量网关信息上报
     * @param string key 接口调用凭证
     * @return json数组
     */
	public function GWList_RLY_ALL(){
        $row = $this->checkKey();
        $m = M('wanrui_dev_info');
		$sql ="SELECT a.appid, a.sn, a.mac, b.ip, b.port, a.prjcode, a.prjname, a.address, a.longitude, a.latitude, a.name, b.last_time, a.create_time FROM rtu_project_info a
			LEFT JOIN term_run_info b ON b.sn = a.sn";
		$rs = $m->query($sql);
		if(empty($rs)){
			die(json_encode(array(
                'error' => '没有设备'
            )));
		}
		foreach ($rs as $k=>$v){
			$data['list'][]= array(
				"appid" => $v['appid'],
				"devid" => $v['sn'],
				"mac"   => $v['mac'],
				"ip"    => $v['ip'],
				"port"  => $v['port'],
				"prjcode" => $v['prjcode'],
				"prjname" => $v['prjname'],
				"address" => $v['address'],
				"longitude" => $v['longitude'],
				"latitude"  => $v['latitude'],
				"name"  => $v['name'],
				"lastact"  => $v['last_time'],
				"createdAt"  => $v['create_time'],
			);
		}
        echo json_encode($data);
    }
	/**
     * RTU信息上报
     * @param string key 接口调用凭证
     * @return json数组
     */
	public function DeviceSetToVR_RLY(){
		$row = $this->checkKey();
		$m = M('wanrui_rtu_info');
		$sn = $this->getsn();
		if($sn==""){
			die(json_encode(array(
                'error' => '请传入正确的参数'
            )));
		}
		$sql ="SELECT a.appid, a.sn, a.mac, c.gateway_sn, a.prjcode, a.prjname, a.address, a.longitude, a.latitude, a.name, b.last_time, a.create_time, a.enable_collect, a.uprate FROM rtu_project_info a
			LEFT JOIN term_run_info b ON b.sn = a.sn
			LEFT JOIN term c ON c.sn = a.sn
			WHERE a.sn = '$sn'";
		$rs = $m->query($sql);
		if(empty($rs)){
			die(json_encode(array(
                'error' => '指定设备不存在'
            )));
		}
		$rs = $rs[0];
		$data = array(
			"appid" 	=> $rs['appid'],
			"devid" 	=> $rs['sn'],
			"mac"   	=> $rs['mac'],
			"gatewayId" => $rs['gateway_sn'],
			"prjcode" 	=> $rs['prjcode'],
			"prjname"	=> $rs['prjname'],
			"address" 	=> $rs['address'],
			"longitude" => $rs['longitude'],
			"latitude"  => $rs['latitude'],
			"name"  	=> $rs['name'],
			"lastact"  	=> $rs['last_time'],
			"createdAt" => $rs['create_time'],
			"enable" 	=> $rs['enable_collect'],
			"uprate" 	=> $rs['uprate'],
		);
		$rs1 = $m->query("select addr, name, code, max, min FROM rtu_data_set");
		foreach ($rs1 as $k=>$v){
			$data['list'][] = array(
				"signalid" 			=> $v['addr'],
				"signalname" 		=> $v['name'],
				"signalTypeCode" 	=> $v['code'],
				"valtype" 			=> '1',
				"maxval" 			=> $v['max'],
				"minval" 			=> $v['min'],
				"controlable" 		=> "false",
				"opertype" 			=> "add"
			);
		}
		echo json_encode($data);
	}

	/**
     * 信号列表上报
     * @param string key 接口调用凭证
     * @return json数组
     */
	public function SignalList(){
		$row = $this->checkKey();
		$m = M('v_rtu_info');
		$sn = $this->getsn();
		if ($sn == ""){
			die(json_encode(array(
                'error' => '请传入正确的参数'
            )));
		}
		$rs = $m->query("select addr, name, code, max, min FROM rtu_data_set");
		if (empty($rs)){
			die(json_encode(array(
                'error' => '指定设备不存在'
            )));
		}
		$data = array(
			'devid' => $sn,
		);
		foreach ($rs as $k => $v){
			$data['list'][] = array(
				"signalid" 			=> $v['addr'],
				"signalname" 		=> $v['name'],
				"signalTypeCode" 	=> $v['code'],
				"valtype" 			=> '1',
				"maxval" 			=> $v['max'],
				"minval" 			=> $v['min'],
				"controlable" 		=> "false",
				"opertype" 			=> "add"
			);
		}
		echo json_encode($data);
	}

    // 查询多个设备的最新gps位置
    public function get_latest_gps($sns){
        $sns = "'".implode("','", $sns)."'";
        $m = M('term_gps');
        $rs = $m->query("SELECT sn, longitude, latitude, UNIX_TIMESTAMP(report_time)ts FROM term_gps WHERE sn IN($sns)");
        foreach ($rs as $key => $row) {
            $this->lnglatFormat($row['longitude'], $row['latitude']);
            $d[] = array(
                'sn' => $row['sn'],
                'lng' => round($row['longitude'], 6),
                'lat' => round($row['latitude'], 6),
                'ts' => $row['ts']
            );
        }
        echo json_encode(array('ret'=>0, 'msg'=>'ok', 'data'=>array('list'=>isset($d) ? $d : array())));
    }

    // 查询一台设备一段时间内的gps轨迹
    public function get_gps_trace($sn, $date, $start, $end) {
        ini_set('memory_limit', -1);
        session_write_close();
        $start = $date .' '. $start;
        $end = $date .' '. $end;
        // die("sn=$sn, start=$start, end=$end");

        $df = strtoupper(C('GPS_DATA_FROM'));
        if ($df == 'DB') {
            ;
        } elseif ($df == 'FILE') {
            $null_str = str_repeat('0',16);
            $start = strtotime($start);
            $end = strtotime($end);
            $path = $this->getGpsFilePath(str_replace('-', '', $date));
            if ($sn && file_exists($path)) {
                $fp = fopen($path, "rb");
                fseek($fp, 128);
                while (1) {
                    $str = fread($fp,32);
                    if (substr(bin2hex($str),0,16) == $null_str){
                        break;
                    }
                    $tmpsn = substr($str,0,strpos($str, "\0"));
                    if ($tmpsn == $sn) {
                        $firstBlockPos = hexdec(htond(bin2hex(fread($fp,8))));
                        $lastBlockPos  = hexdec(htond(bin2hex(fread($fp,8))));

                        //开始读取一个数据块(2000+8)
                        fseek($fp, $firstBlockPos, SEEK_SET);
                        $readNum = 0;
                        while (1){
                            $lngh = hexdec(htons(bin2hex(fread($fp,2))));
                            if ($lngh == 0){
                                //没有更多gps数据了
                                break;
                            }
                            $lngl = hexdec(htons(bin2hex(fread($fp,2))));
                            $lath = hexdec(htons(bin2hex(fread($fp,2))));
                            $latl = hexdec(htons(bin2hex(fread($fp,2))));
                            $time = hexdec(htonl(bin2hex(fread($fp,4))));
                            fseek($fp, 8, SEEK_CUR);
                            if ($time >= $start && $time <= $end){
                                $data[] = array(
                                    'lng' => gpsTrans($lngh, $lngl),
                                    'lat' => gpsTrans($lath, $latl),
                                    'ts' => $time,
                                );
                            } elseif ($time > $end) {
                                break;
                            }
                            $readNum++;
                            if ($readNum == 100) {
                                $nextBlockPos = hexdec(htond(bin2hex(fread($fp,8))));
                                if ($nextBlockPos == 0) {
                                    //没有下个数据块
                                    break;
                                }else{
                                    $readNum = 0;
                                    fseek($fp, $nextBlockPos, SEEK_SET);
                                }
                            }
                        }
                        break;
                    }else{
                        fseek($fp, 16, SEEK_CUR);
                    }
                }
                fclose($fp);
            }
        }
        echo json_encode(array('ret'=>0, 'msg'=>'ok', 'data'=>array('list'=>isset($data) ? $data : array())));
    }

    // 查询一个设备上报的最新的业务数据
    public function get_rtu_value($sns) {
        $addr_trans = array(
            '1' => array(1200, 2400, 4800, 9600, 19200, 38400, 57600, 115200),
            '2' => array('Mpa', 'Kpa', 'Pa', 'Bar', 'Mbar', 'kg/cm2', 'psi', 'mh2o', 'mmh2o', 'mA'),
        );
        $sns = "'".implode("','", $sns)."'";
        $rs = M('rtu_data')->where("sn IN($sns)")->order('sn ASC, addr ASC')->field('sn, addr, catch_time, value')->select();
        $tmp = array();
        $d = array();
        foreach ($rs as $key => $row) {
            $v = isset($addr_trans[$row['addr']]) ? $addr_trans[$row['addr']][$row['value']] : $row['value'];
            $tmp[$row['sn']][$row['addr']] = $v;
            if ($row['addr'] == 4) {
                $tmp[$row['sn']]['catch_time'] = $row['catch_time'];
            }
        }
        foreach ($tmp as $sn => $row) {
            array_push($d, array('sn' => $sn, 'catch_time' => $row['catch_time'], 'value' => $row['4']/pow(10, $row['3']).$row['2']));
        }
        echo json_encode(array('ret'=>0, 'msg'=>'ok', 'data'=>array('list'=>$d)));
    }

    // 查询rtu最新采集数据
    public function get_rtu_real_data($sns) {
        $sns_str = "'".implode("','", $sns)."'";
        $rs = M('rtu_data')
            ->join('LEFT JOIN rtu_data_set ON rtu_data_set.addr = rtu_data.addr')
            ->where("rtu_data.sn IN($sns_str)")
            ->order('rtu_data.sn ASC, rtu_data.addr ASC')
            ->field('rtu_data.addr, rtu_data.sn, rtu_data.report_time, rtu_data.catch_time, rtu_data.value, rtu_data_set.name, rtu_data_set.unit')
            ->select();
        $tmp_datas = array();
        foreach ($rs as $sn => $row) {
            if (!isset($tmp_datas[$row['sn']])) {
                $tmp_datas[$row['sn']] = array();
            }
            array_push($tmp_datas[$row['sn']], array(
                'addr' => $row['addr'],
                'name' => $row['name'],
                'unit' => $row['unit'],
                'value' => round($row['value'], 2),
                'report_time' => $row['report_time'],
                'catch_time' => $row['catch_time']
            ));
        }
        $data_list = array();
        foreach ($sns as $sn) {
            $data_list[$sn] = isset($tmp_datas[$sn]) ? $tmp_datas[$sn] : array();
        }
        echo json_encode(array('ret'=>0, 'msg'=>'ok', 'data'=>array('list'=>$data_list)));
    }

    /**
     * 查询一台设备的继电器状态
     * @param  [string]    $cmd         get_relay_status
     * @param  [string]    $sn          序列号
     * @return [json]
     */
    private function get_relay_status($sn){
        $s = M('term_interface')->where("sn = '%s' AND relay_status IS NOT NULL", $sn)->getField('relay_status');
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => $s ? $s : null
        );
        echo json_encode($arr);
    }

    /**
     * 设置继电器状态
     * @param  [string]    $cmd         set_relay
     * @param  [string]    $sn          序列号
     * @return [json]
     */
    private function set_relay($sn, $relay, $act, $key){
        $value = sprintf('relay=%s&act=%s', $relay, $act);
        $row = S($key);
        $ugid = $row['gid'];
        $cmd = 'relay_set2';
        $create_time = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+1 month'));

        $md = M('term_task_detail');
        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (username,cmd,value,start_time,end_time,ugid)VALUES('%s','%s','%s','%s','%s',%d)", $row['name'], $cmd, $value, date('Y-m-d H:i:s'), $end_time, $ugid));
        $lastId = $md->getLastInsID();
        $dataList = array(
            'task_id' => $lastId,
            'sn' => $sn,
            'end_time' => $end_time
        );

        if ($lastId && $md->add($dataList)){
            $md->commit();
            $this->wlog($row['name'], $cmd, $value, "type=term&sns=".$sn, $ugid, '');
        }
        $rs = M('term_task_detail')->where("task_id = $lastId")->getField('id',true);
        $arr = array(
            'ret' => 0,
            'msg' => 'ok',
            'data' => array(
                'list' => $rs ? $rs : array()
            )
        );
        echo json_encode($arr);
    }
}