<?php
class RtuAction extends CommonAction{
    //新增、编辑rtu_data_set表数据时，判断name、addr是否唯一
    private function checkNameAddr($m, $d, $act){
        if ($act == 'add'){
            $q = '1 = 1';
        }elseif ($act == 'edit'){
            $q = "id <> {$d['id']}";
        }
        $c = $m->where("name = '%s' AND %s", array($d['name'], $q))->count();
        if ($c != 0){
            $this->ajaxReturn('', L('NAME_EXIST'), -1);
        }
        $c = $m->where("addr = %d AND %s", array($d['addr'], $q))->count();
        if ($c != 0){
            $this->ajaxReturn('', L('ADDR_EXIST'), -2);
        }
    }

    // 采集设置页面
    public function sjlx(){
        $this->assign('web_path_1', array(L('COLLECTION_SETTINGS')));
        $this->display('sjlx');
    }

    // 传感量数据类型
    public function loadSensorTypeData() {
        $m = M('rtu_data_set');
        $sn = I('sn','','string');
        $set_type = I('set_type', '', 'string');
        $q = sprintf('%s AND %s', ($set_type=='0' ? 'set_type = 0' : '1=1'), $this->generate_search_str());
        $rs = $m->field("rtu_data_set.*, rtu_data_set_one.min AS min_custom, rtu_data_set_one.max AS max_custom")
            ->join("LEFT JOIN rtu_data_set_one ON rtu_data_set_one.rtu_data_set_id = rtu_data_set.id AND rtu_data_set_one.sn = '$sn'")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $vt = array('string', 'int/uint', 'short', 'char', 'float', 'double');
        foreach ($rs as $k => $row) {
            $rs[$k]['value_type'] = $this->valueType2Text($row['value_type']);
        }
        $total = $m->where($q)->count();
        die (json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        )));
    }

    // 新增传感量类型
    public function sensorTypeAdd() {
        $this->sensorOperation();
    }

    // 编辑传感量类型
    public function sensorTypeEdit() {
        $this->sensorOperation();
    }

    // 删除传感量类型
    public function sensorTypeDel() {
        $this->sensorOperation();
    }

    // 传感量类型阈值设置
    public function sensorTypeCustom() {
        $this->sensorOperation();
    }

    // 导入传感量类型
    public function sensorTypeImport() {
        $this->sensorOperation();
    }

    // 用来导入传感量类型的excel模板下载
    public function sensorTypeExcelDownload() {
        $this->sensorOperation();
    }

    // 传感量类型增删改, PRIVATE
    private function sensorOperation() {
        $act = $_REQUEST['act'];
        $m = M('rtu_data_set');
        $vtype = I('value_type',0,'intval');
        $d = array(
            'slave_id' => I('slave_id','1','string'),
            'addr' => I('addr','','string'),
            'set_type' => I('set_type',0,'intval'),
            'name' => I('name','','string'),
            'unit' => I('unit','','string'),
            'value_type' => $vtype,
            'value_len' => $this->valueType2Length($vtype),
            'min' => I('min'),
            'max' => I('max'),
            'uprate' => I('uprate',0,'intval'),
            'code' => I('code','','string'),
            'warn_level' => I('warn_level','','string'),
            'revised' => I('revised'),
            'info' => I('info','','string'),
            'operator' => I('operator','','string'),
            'op_value' => I('op_value',null,'float')
        );
        $id = I('id',0,'intval');
        if ($act == 'add'){
            $this->checkNameAddr($m,$d,'add');
            $m->add($d);
        } elseif ($act == 'edit'){
            $d['id'] = $id;
            $this->checkNameAddr($m,$d,'edit');
            $m->save($d);
        } elseif ($act == 'delete'){
            $ids = I('ids','','string');
            $addrs = $m->where("id IN($ids)")->getField('addr',true);
            if ($m->where("id IN($ids)")->delete()){
                //删除RTU数据文件
                foreach ($addrs as $k => $addr) {
                    $this->delRtuDataFiles('', $addr);
                }
            }
        } elseif ($act == 'custom'){
            $d = array(
                'sn' => I('sn'),
                'rtu_data_set_id' => I('id'),
                'min' => I('min'),
                'max' => I('max'),
            );
            $c = $m->where("id = %d AND min = %s AND max = %s", $d['rtu_data_set_id'],$d['min'],$d['max'])->count();
            $m = M('rtu_data_set_one');
            if ($c != 0){
                $m->where("sn = '%s' AND rtu_data_set_id = %d", array($d['sn'],$d['rtu_data_set_id']))->delete();
            }else{
                $c = $m->where("sn = '%s' AND rtu_data_set_id = %d", array($d['sn'],$d['rtu_data_set_id']))->getField('id');
                $c ? $m->where('id = %d',$c)->save($d) : $m->add($d);
            }
        } elseif ($act == 'import') {
            $f = $_FILES['filedata'];
            if ($f['error'] != 0){
                $this->ajaxReturn('', $this->getUploadErrorMsg($f['error']), -1, -1);
            }
            import('@.ORG.PHPExcel');
            $PHPExcel = new PHPExcel();
            header ( "Content-type: text/html; charset=UTF-8" );
            if (!strpos($f['name'], '.xls')){
                $this->ajaxReturn('', $this->read_excel_failed_msg, -2);
            }
            $filePath = $f['tmp_name'];
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)){
                $PHPReader = new PHPExcel_Reader_Excel2007();
                if (!$PHPReader->canRead($filePath)){
                    $this->ajaxReturn('', $this->read_excel_failed_msg, -3);
                }
            }
            $PHPExcel = $PHPReader->load($filePath);
            $currentSheet = $PHPExcel->getSheet(0);
            $allRow = $currentSheet->getHighestRow();
            $exists_names = $m->order('name ASC')->getField('name', true);
            $exists_addrs = $m->order('name ASC')->getField('addr', true);
            $cols = array('A'=>'name', 'B'=>'addr', 'C'=>'unit', 'D'=>'value_type', 'E'=>'min', 'F'=>'max', 'G'=>'code', 'H'=>'warn_level', 'I'=>'info');
            $total = $success = 0;
            for ($currentRow=2; $currentRow<=$allRow; $currentRow++){
                $total += 1;
                $row = array('slave_id'=>1);
                foreach ($cols as $k => $v) {
                    $row[$v] = trim($currentSheet->getCellByColumnAndRow(ord($k)-65,$currentRow)->getValue());
                    if ($v == 'value_type') {
                        switch ($row[$v])
                        {
                            case '2':
                                $row[$v] = 3;
                                $row['value_len'] = 1;
                                break;
                            case '3':
                                $row[$v] = 2;
                                $row['value_len'] = 2;
                                break;
                            case '4':
                                $row[$v] = 1;
                                $row['value_len'] = 4;
                                break;
                            default:
                                $row[$v] = 4;
                                $row['value_len'] = 4;
                                break;
                        }
                    }
                }
                if ($row['name'] == '' || $row['addr'] == '' || in_array($row['name'], $exists_names, true) || in_array($row['addr'], $exists_addrs, true)) {
                    continue;
                }
                if ($m->add($row)) {
                    $success += 1;
                }
            }
            $this->ajaxReturn('', sprintf(L('IMPORT_SENSOR_OK'),$total,$success), 0, 200);
        } elseif ($act == 'download_tpl'){
            $data = array(
                'filename' => 'import_tpl',
                'header' => array(L('VAR_NAME'), L('ADDR'), L('UNIT'), L('VALUE_TYPE').' (1 ~ 4)', L('MIN'), L('MAX'), L('VAR_CODE'), L('TRIGGER_ALARM_LEVEL'), L('VAR_NOTE')),
                'footer' => $footer,
                'body' => array(),
                'type' => 'import_sensor'
            );
            $this->ajaxReturn($data, '', 0);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //近30日新增设备
    public function dashboardData(){
        $retName = $_REQUEST['retName'];
        $ret = array();
        try {
            $m = M('term');
            $q = 'group_id IN('.$this->getTgids().')';
            $datay = array();

            $categories = array();
            $now = strtotime(I('start_date',date('Y-m-d'),'string'));
            $flux_x = array();
            $flux_y = array();

            for ($i=1; $i<=30; $i++){
                $tmpdate = $now - 24*3600*$i;
                array_push($datay,0);

                array_unshift($categories, date('M d',$tmpdate));
                array_unshift($flux_x, date('Ymd',$tmpdate));
            }
            $len = count($flux_x);


            //最近7天新增
            if ($retName == 'chart_new'){
                $rs = $m->query("SELECT term.sn, DATE_FORMAT(first_login,'%Y%m%d')ymd FROM term
                    LEFT JOIN term_run_info ON term_run_info.sn = term.sn
                    WHERE $q AND first_login IS NOT NULL AND first_login<>'0000-00-00 00:00:00'
                    HAVING ymd BETWEEN '{$flux_x[0]}' AND '{$flux_x[$len-1]}'");
                foreach ($rs as $k=>$row){
                    $datay[array_search($row['ymd'],$flux_x)] += 1;
                }
            }

            foreach ($categories as $k=>$v){
                $ret[] = array($v, $datay[$k]);
            }
        } catch (ThinkException $e) {
            ;
        }
        echo json_encode(array($_REQUEST['retName'] => $ret));
    }

	 //全景图
    public function qjt_rtu(){
        $this->assign('web_path_1', array(L('PANORAMA')));
        $this->display('qjt_rtu');
    }

    //Rtu详情
    public function rtuxq(){
        $sn = $_REQUEST['sn'];
        $backpage = I('get.backpage', 'sjlb', 'string');
        $this->assignTermRow($sn);
        $sensors = M('rtu_data_set')->field('slave_id, addr, name, unit, value_type')->order('name ASC')->select();
        $this->assign('sensors', $sensors);
        $links = array();
        if ($backpage == 'lora'){
            array_push($links, sprintf('<a href="%s">%s</a>', U('Rtu/'.$backpage), L('DATA_LIST')));
        }elseif ($backpage == 'wgjd') {
            array_push($links, sprintf('<a href="%s">%s</a>', U('Rtu/lora'), L('DATA_LIST')));
            array_push($links, sprintf('<a href="%s">%s</a>', U('Rtu/'.$backpage, 'data_num='.I('data_num').'&gateway_sn='.I('gateway_sn')), L('PROJECT_INFO')));
        }else{
            array_push($links, sprintf('<a href="%s">%s</a>', U('Rtu/'.$backpage), L('DATA_LIST')));
        }
        array_push($links, L('DATA_DETAIL')." ($sn)");
        $this->assign('web_path_1', $links);
        $this->display('rtuxq');
    }

    //刷新传感量的最新值
    public function refreshCurrentSensorValue(){
        $sn = $_REQUEST['sn'];
        $rs = M('rtu_data')->field('slave_id, addr, report_time, value')->where("sn = '$sn'")->select();
        foreach ($rs as $k=>$row){
            $rs[$k]['value'] = round($row['value'],2);
        }
        echo json_encode($rs ? $rs : array());
    }

	//获取所有传感量历史数据
	 public function getAllSensorHistoryData(){
        session_write_close();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '600');
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $sn = I('sn','','string');
		$sensors = M('rtu_data_set')->where('set_type=0')->field('id,slave_id, addr, name, unit, value_type')->order('name ASC')->select();
        //$sensors = M('rtu_data_set')->order('name ASC')->where('set_type = 0')->getField('id, slave_id, addr, name, unit, value_type, min, max', true);
		foreach($sensors as $k=>$row){
			$filepath = C('FTP_WEB_PACK_PATH').sprintf("rtu/%s/%s_%d_%d.bin", substr($sn,-1,1), $sn, $row['slave_id'], $row['addr']);
			if (!file_exists($filepath)){
				$data[$row['id']]['name'] = $row['name'];
				$data[$row['id']]['data']=array();
				continue;
			}
			$fp = fopen($filepath, 'rb');
			fseek($fp, 0, SEEK_END);
			$size = ftell($fp);
			rewind($fp);

			$data_len = 4; //数据长度
			$value_type = $row['value_type'];
			switch ($value_type) {
				case 4:
					$format = 'f';
					break;
				default:
					$format = 'N';
					break;
			}

			$size1000 = 1000 * (8 + $data_len);
			while (!feof($fp)) {
				$str = fread($fp, $size1000);
				$len = strlen($str);
				$ts = unpack('N', substr($str, -(8+$data_len), 4));
				$ts = $ts[1];
				if ($ts < $start){
					continue;
				}

				for ($i=0; $i<$len; $i+=(8+$data_len)){
					$ts = unpack('N', substr($str, $i, 4));
					$ts = $ts[1];
					if ($ts < $start){
						continue;
					}
					if ($ts > $end){
						break;
					}
					// if ($ts >= $start && $ts <= $end){
						$catch_ts = unpack('N', substr($str, $i+4, 4));
						$catch_ts = $catch_ts[1];
						$v = unpack($format, substr($str, $i+8, $data_len));
						$data[$row['id']]['data'][] = array($ts*1000, $catch_ts*1000, round($v[1],2));
					// }
				}
				if (ftell($fp) >= $size){
					break;
				}
			}
			fclose($fp);
			$data[$row['id']]['name'] =$row['name'];
			if(empty($data[$row['id']]['data'])){
				$data[$row['id']]['data']=array();
			}

		}
        if (isset($_REQUEST['sessid'])){
            $this->ajaxReturn($data, 'ok', 0);
        }
        echo json_encode($data);
    }

	//获取传感量历史数据
    public function getSensorHistoryData($start = 0, $end = 0, $sn = '', $addr = 0, $ret_type = 'json') {
        session_write_close();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '600');
        if ($start == 0) {
            $start = I('start',0,'intval');
        }
        if ($end == 0) {
            $end = I('end',0,'intval');
        }
        if ($sn == '') {
            $sn = I('sn','','string');
        }
        $slave_id = I('slave_id', 1, 'intval');
        if ($addr == 0) {
            $addr = I('addr',0,'intval');
        }
        $filepath = C('FTP_WEB_PACK_PATH').sprintf("rtu/%s/%s_%d_%d.bin", substr($sn,-1,1), $sn, $slave_id, $addr);
        if (!file_exists($filepath)){
            if ($ret_type == 'array') {
                return array();
            }
            $this->ajaxReturn($filepath, L('DATA_FILE_NOT_EXIST'), -1);
        }
        $fp = fopen($filepath, 'rb');
        fseek($fp, 0, SEEK_END);
        $size = ftell($fp);
        rewind($fp);

        $data_len = 4; //数据长度
        $row = M('rtu_data_set')->where("slave_id = $slave_id AND addr = $addr")->field('value_type,value_len')->find();
        $value_type = $row['value_type'];
        switch ($value_type) {
            case 4:
                $format = 'f';
                break;
            default:
                $format = 'N';
                break;
        }

        $size1000 = 1000 * (8 + $data_len);
        while (!feof($fp)) {
            $str = fread($fp, $size1000);
            $len = strlen($str);
            $ts = unpack('N', substr($str, -(8+$data_len), 4));
            $ts = $ts[1];
            if ($ts < $start){
                continue;
            }

            for ($i=0; $i<$len; $i+=(8+$data_len)){
                $ts = unpack('N', substr($str, $i, 4));
                $ts = $ts[1];
                if ($ts < $start){
                    continue;
                }
                if ($ts > $end){
                    break;
                }
                // if ($ts >= $start && $ts <= $end){
                    $catch_ts = unpack('N', substr($str, $i+4, 4));
                    $catch_ts = $catch_ts[1];
                    $v = unpack($format, substr($str, $i+8, $data_len));
                    if (is_nan($v[1])) continue;
                    $data[] = array($ts*1000, $catch_ts*1000, floatval(round($v[1],3)));
                // }
            }
            if (ftell($fp) >= $size){
                break;
            }
        }
        fclose($fp);
        if (!is_array($data)){
            $data = array();
        }
        if (isset($_REQUEST['sessid'])){
            $this->ajaxReturn($data, 'ok', 0);
        }
        if ($ret_type == 'array') {
            return $data;
        }
        echo json_encode($data);
    }

    //获取模态框html
    public function getModalHtml(){
        $tpl_id = trim($_REQUEST['tpl_id']);
        $id = $_REQUEST['id'];
        $sn = $_REQUEST['sn'];
        $tpl = '';
        if ($tpl_id != ''){
            $tpl = $this->buildHtml($tpl_id, './Runtime/Temp/', './Tpl/Rtu/modal/'.$tpl_id.'.html');
            if ($tpl_id == 'sensor_edit'){
                $row = M('rtu_data_set')->where("id = $id")->find();
                $tpl = sprintf($tpl, $row['id'], $row['name'], $row['slave_id'], $row['addr'], $row['unit'], $row['min'], $row['max'], $row['code'], $row['warn_level'], $row['revised'], $row['info'], $row['value_type'], $row['operator'], $row['op_value'], $row['set_type']);
            } elseif ($tpl_id == 'project_edit' || $tpl_id == 'project_edit_wk' || $tpl_id == 'project_edit_rtu' ) {
                $prjinfo = M('rtu_project_info')->where("sn = '$sn'")->limit(1)->find();
                $term_type = M('term')->where("sn = '$sn'")->getField('term_type');
                $rtu_name = M('term')->where("sn = '$sn'")->getField('alias');
                $gps = $this->map($sn);
                if ($tpl_id == 'project_edit_wk') {
                    $tpl = sprintf($tpl, $sn, $prjinfo['name'], $prjinfo['info1'], $prjinfo['prjcode'], $prjinfo['prjname'], $prjinfo['appid'],
                        ($prjinfo['enable_collect']=='True'?'checked':''), $prjinfo['uprate'], $prjinfo['city'], $prjinfo['area'],
                        $prjinfo['address'], $gps['lng'], $gps['lat'], $gps['lat'], $gps['lng']);
                } elseif ($tpl_id == 'project_edit_rtu'){
                     $tpl = sprintf($tpl, $sn, $prjinfo['prjname'], $prjinfo['name'], $prjinfo['address'], $prjinfo['contact'],
                        $prjinfo['tel'], ($term_type==0?'':'disabled'), $gps['lat'], $gps['lng']);
                } else {
                    $tpl = sprintf($tpl, $sn, $rtu_name, $prjinfo['address'], $prjinfo['contact'], $prjinfo['tel'], $gps['lat'], $gps['lng']);
                }
			} elseif ($tpl_id == 'history_data') {
                $tpl = sprintf($tpl, $sn, get_sensor_options());
            } elseif ($tpl_id == 'threshold_edit') {
				$data = M('rtu_data_set_one')->field('min,max')->where('sn="%s" and rtu_data_set_id="%d"',array($sn,$id))->find();
				if(!empty($data)){
					$min = $data['min'];
					$max = $data['max'];
				}else{
					$data1 = M('rtu_data_set')->field('min,max')->where("id = $id")->find();
					$min = $data1['min'];
					$max = $data1['max'];
				}
				$tpl = sprintf($tpl,$sn,$id,$min,$max);
			} elseif ($tpl_id == 'dashboard_rename'){
                $tpl = sprintf($tpl, $id, $_REQUEST['act'], $_REQUEST['name']);
            } elseif ($tpl_id == 'canvas_alarm_rule_add' || $tpl_id == 'canvas_alarm_rule_edit'){
                $tpl = sprintf($tpl, $this->getSensors('html'));
            } else if ($tpl_id == 'rtu_data_alarm_rule_add' || $tpl_id == 'rtu_data_alarm_rule_edit'){
                $rule_types = L('RTU_DATA_ALARM_TYPE');
                $options = get_sensor_options();
                $tpl = sprintf($tpl, $rule_types[0], $rule_types[1], $options, $options);
            }
        }
        echo $tpl;
    }

    //删除常用指令
    public function delteCommonCmd(){
        $name = I('name','','string');
        $str = file_get_contents('common_cmd');
        if ($str){
            $cc = json_decode($str, true);
            foreach ($cc as $k=>$row){
                if ($row['name'] == $name){
                    unset($cc[$k]);
                    break;
                }
            }
        }
        file_put_contents('common_cmd', json_encode($cc));
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //Lora设备列表
    public function lora(){
        $sensors = M('rtu_data_set')->field('slave_id, addr, name, unit, value_type')->where('set_type = 0')->order('name ASC')->select();
        $this->assign('sensors', json_encode($sensors ? $sensors : array()));
        $this->assign('groups', M('term_group')->where("id != 1 AND id IN(%s) AND pid = 1",$this->getTgids())->order('name ASC')->select());
        $this->assign('web_path_1', array(L('DATA_LIST')));
        $this->display('lora' . ($this->ui_version == 'RTU' ? '_rtu' : ''));
    }

    //Lora数据
    public function loadLoraData(){
        session_write_close();
        $m = M('term');
        $gateway_sn = I('gateway_sn','','string');
        if ($gateway_sn != ''){
            $q = sprintf("gateway_sn = '%s'", $gateway_sn);
            $limit = '100';
            $total = $m->where($q)->count();
        }else{
            $gid = I('gid',-10,'intval');
            $q = sprintf('%s AND group_id IN(%s) AND %s', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids(), $this->generate_search_str());
            $limit = ($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
            $total = $m->where($q)->count();
        }
        $sql = "SELECT term.sn, term.alias AS name, term.gateway_sn, term_run_info.is_online, term_run_info.last_time, term_run_info.ip, term_run_info.port, term_run_info.status AS status_limit,
            rpi.address, rpi.contact, rpi.tel FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn
            WHERE $q ORDER BY ".$this->generate_order_str('router_list')." LIMIT $limit";
        $rs = $m->query($sql);
        $ts = time();

        // 电子围栏
        $sns = array();
        if ($rs && count($rs) > 0) {
            foreach ($rs as $row) {
                array_push($sns, "'".$row['sn']."'");
            }
            $q_temp = sprintf("sn IN(%s)", implode(',', $sns));
            $fstatus_arr = $this->getFenceStatus($q_temp);
        }

        foreach ($rs as $k=>$row){
            $ret[$row['sn']] = $row;
            $s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);

            //流量限制状态，当system_config.enable_electronic_fence = 1 && term_gps.fstatus = 1时，将status_limit设置为1
            if ($row['status_limit'] != 1 && $fstatus_arr[0] == '1' && $fstatus_arr[1][$row['sn']] == '1') {
                $row['status_limit'] = 1;
            }

            $ret[$row['sn']]['status'] = get_term_status_str($s, $row['status_limit']);
        }

        if (isset($q_temp)) {
            $datas = M('rtu_data')->where($q_temp)->field('sn, slave_id, addr, value, report_time')->select();
            $_24hours_ago = date('Y-m-d H:i:s', strtotime('-24 hours'));
            foreach ($datas as $k => $row) {
                $ret[$row['sn']][$row['slave_id'].'_'.$row['addr']] = sprintf("<font %s>%s</font>", ($row['report_time'] < $_24hours_ago ? 'style="color:#c1bbbb"' : ''), round($row['value'],6));
            }
        }
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => array_values(isset($ret) ? $ret : array()),
            'userdata' => array()
        ));
    }

    //Lora数据-卡片显示
    public function loadCardData(){
        session_write_close();
        //删除无效的数据(由slave_id、addr修改造成)
        M('rtu_data')->where("addr NOT IN(SELECT addr FROM rtu_data_set)")->delete();

        $m = M('term');
        $gid = I('gid',-10,'intval');
        // $q = sprintf('term_type = 0 AND %s AND group_id IN(%s) AND %s', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids(), $this->generate_search_str());
        $q = sprintf('%s AND group_id IN(%s) AND %s', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids(), $this->generate_search_str());
        $sql = "SELECT term.sn, term.ud_sn, term_run_info.last_time, term_run_info.is_online, term_run_info.status AS status_limit, term.alias AS name,
            (SELECT COUNT(*) FROM rtu_data WHERE rtu_data.sn = term.sn)data_num,
            (SELECT COUNT(*) FROM rtu_warning WHERE is_recover = 0 AND rtu_warning.sn = term.sn)warning_num,
            rpi.address, term_group.name AS prjname FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn
            LEFT JOIN term_group ON term_group.id = term.group_id
            WHERE $q ORDER BY warning_num DESC, data_num DESC, last_time DESC LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
        $rs = $m->query($sql);
		$ts = time();
        import('ORG.Util.MString');
        foreach ($rs as $k => $row) {
            if ($row['address']) {
                $row['address'] = MString::msubstr($row['address'], 0, 45);
            }
            $ret[$row['sn']] = $row;
            $sns[] = "'".$row['sn']."'";
			$s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
            $ret[$row['sn']]['status'] = get_term_status_str($s, $row['status_limit']);
        }

		if ($this->ui_version == 'RTU' && isset($sns)) {
            $sns = implode(',', $sns);
            $datas = M('rtu_data')->where("sn IN($sns)")->field('sn, slave_id, addr, value, report_time')->select();
            if (!empty($datas[0]['sn'])){
                //公共阈值
                $rs = M('rtu_data_set')->field('slave_id, addr, min, max')->select();
                foreach ($rs as $k => $row) {
                    $threshold[$row['slave_id'].'_'.$row['addr']] = array(
                        'min' => $row['min'],
                        'max' => $row['max']
                    );
                }
                unset($rs);

                //自定义阈值
                $rs = M('')->query("SELECT a.sn, a.min, a.max, b.slave_id, b.addr FROM rtu_data_set_one a INNER JOIN rtu_data_set b ON b.id = a.rtu_data_set_id");
                foreach ($rs as $k => $row) {
                    $threshold_custom[$row['sn'].'_'.$row['slave_id'].'_'.$row['addr']] = array(
                        'min' => $row['min'],
                        'max' => $row['max']
                    );
                }
                unset($rs);

                $_24hours_ago = date('Y-m-d H:i:s', strtotime('-24 hours'));
                foreach ($datas as $k => $row) {
                    $t_key = $row['slave_id'].'_'.$row['addr'];

                    //传感量数据赋值到 $ret['sn']['datas']中
                    $ret[$row['sn']]['datas'][$t_key] = array(
                        'value' => floatval($row['value']),
                        'report_time' => $row['report_time'],
                        'warning' => 0,
                        'color' => 'normal' //card中某一项的数值颜色
                    );

                    //是否告警，是否已超过24小时未上报数据
                    $min = floatval($threshold[$t_key]['min']);
                    $max = floatval($threshold[$t_key]['max']);
                    if (isset($threshold_custom[$row['sn'].'_'.$t_key])){
                        $min = floatval($threshold_custom[$row['sn'].'_'.$t_key]['min']);
                        $max = floatval($threshold_custom[$row['sn'].'_'.$t_key]['max']);
                    }
    				if(floatval($row['value']) < $min || floatval($row['value']) > $max){
    					$ret[$row['sn']]['datas'][$t_key]['color'] = 'threshold';
                        //有告警产生，card的title显示为红色
                        $ret[$row['sn']]['warning'] += 1;
    				}elseif ($row['report_time'] < $_24hours_ago){
    					$ret[$row['sn']]['datas'][$t_key]['color'] = 'timeout';
    				}
    			}
            }
        }

        if ($this->ui_version != 'RTU' && isset($sns)) {
            $sns = implode(',', $sns);
            $gpsdata = $this->map($sns, true);
            foreach ($gpsdata as $k => $row) {
                $ret[$row['sn']]['lng'] = $row['longitude'];
                $ret[$row['sn']]['lat'] = $row['latitude'];
            }
        }

        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => array_values(isset($ret) ? $ret : array()),
            'userdata' => array()
        ));
    }

    //该页面显示一台路由器下的传感量列表，或一台网关下的Lora节点列表
    public function wgjd(){
        $m = M('term');
        $gateway_sn = I('get.gateway_sn');
        $data_num = M('rtu_data')->where("sn = '$gateway_sn'")->count();
        /**
         * data_num != 0时查询网关
         * data_num == 0时查询其下lora
         */
        $loras = $m->where("%s = '%s'",array($data_num != 0 ? 'term.sn':'gateway_sn', $gateway_sn))
            ->join("LEFT JOIN term_run_info ON term_run_info.sn = term.sn")
            ->join("LEFT JOIN rtu_project_info ON rtu_project_info.sn = term.sn")
            ->join("LEFT JOIN term_group ON term_group.id = term.group_id")
            ->field('term.sn, term_run_info.last_time, term.term_type, term_group.name AS loraname')
            ->order('term.sn ASC')
            ->select();
        $this->assign('loras', $loras);
        //传感量
        $sensors = M('rtu_data_set')->order('name ASC')->where('set_type = 0')->getField('id, slave_id, addr, name, unit, value_type, min, max', true);
        $this->assign('sensors', $sensors);
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Rtu/lora'), L('DATA_LIST')),
            L('PROJECT_INFO')
        ));
        //项目配置
        $this->assign('project',
            M('term')
            ->where("term.sn = '%s'", $gateway_sn)
            ->join('LEFT JOIN term_group ON term_group.id = term.group_id')
            ->join('LEFT JOIN rtu_project_info ON rtu_project_info.sn = term.sn')
            ->field('term.alias AS name, rtu_project_info.address, rtu_project_info.contact, rtu_project_info.tel, term_group.name AS prjname')
            ->find());

        //GPS位置
        $rs = M('term')->field('sn, term_type')
            ->where("term.sn = '%s' OR gateway_sn = '%s'",array($gateway_sn, $gateway_sn))
            ->order('term_type ASC')
            ->select();
        foreach ($rs as $k => $row) {
            $gps[$row['sn']] = $row;
            $sns[] = "'".$row['sn']."'";
        }
        if (isset($sns)) {
            $sns = implode(',', $sns);
            $gpsdata = $this->map($sns, true);
            foreach ($gpsdata as $k => $row) {
                $gps[$row['sn']]['longitude'] = $row['longitude'];
                $gps[$row['sn']]['latitude'] = $row['latitude'];
                $gps[$row['sn']]['report_time'] = $row['report_time'];
            }
        }
        $this->assign('gps', $gps ? array_values($gps) : array());
        $this->assign('data_num', $data_num);
        $this->assign('wgjd_show_type', isset($_COOKIE['wgjd_show_type']) ? $_COOKIE['wgjd_show_type'] : 1);
        $this->display('wgjd');
    }

	public function lssj(){
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Rtu/lora'), L('DATA_LIST')),
            L('HISTORY_DATA')
        ));

		$gateway_sn = I('get.gateway_sn');
        $data_num = M('rtu_data')->where("sn = '$gateway_sn'")->count();
        $this->assign('data_num', $data_num);
		$this->assign('project', M('rtu_project_info')->field('prjname,name,contact,tel,address,create_time')->where("sn = '$gateway_sn'")->limit(1)->find());

        //GPS位置
        $rs = M('term')->field('sn, term_type')
            ->where("term.sn = '%s' OR gateway_sn = '%s'",array($gateway_sn,$gateway_sn))
            ->order('term_type ASC')
            ->select();
        foreach ($rs as $k => $row) {
            $gps[$row['sn']] = $row;
            $sns[] = "'".$row['sn']."'";
        }
        if (isset($sns)) {
            $sns = implode(',', $sns);
            $gpsdata = $this->map($sns, true);
            foreach ($gpsdata as $k => $row) {
                $gps[$row['sn']]['longitude'] = $row['longitude'];
                $gps[$row['sn']]['latitude'] = $row['latitude'];
                $gps[$row['sn']]['report_time'] = $row['report_time'];
            }
        }
        $this->assign('gps', $gps ? array_values($gps) : array());

        //如果设置了启用的仪表盘，则显示仪表盘页面
        $arr = F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_list', '', './tmp/');
        if ($arr){
            foreach ($arr as $k => $row) {
                if ($row['use'] == 1){
                    $this->dashboard($row['id']);
                    exit;
                }
            }
        }

        //传感量(当前值，上报时间，阈值， 是否告警)
        $rs = M('')->query("SELECT a.id, a.slave_id, a.addr, a.name, a.unit, a.value_type, IFNULL(b.min,a.min)min, IFNULL(b.max,a.max)max FROM rtu_data_set a
            LEFT JOIN rtu_data_set_one b ON b.sn = '$gateway_sn' AND b.rtu_data_set_id = a.id ORDER BY a.name ASC");
        foreach ($rs as $k => $row) {
            $row['warning'] = 0;
            $row['today_rpt'] = 0;
            $sensors[$row['slave_id'].'_'.$row['addr']] = $row;
        }
        $rs = M('')->query("SELECT slave_id, addr, value, report_time FROM rtu_data WHERE sn = '$gateway_sn'");
        $today = date('Y-m-d 00:00:00');
        foreach ($rs as $k => $row) {
            $key = $row['slave_id'].'_'.$row['addr'];
            $sensors[$key]['value'] = floatval($row['value']);
            $sensors[$key]['report_time'] = $row['report_time'];
            $sensors[$key]['report_time_small'] = substr($row['report_time'], 5);
            if ($sensors[$key]['value'] < $sensors[$key]['min'] || $sensors[$key]['value'] > $sensors[$key]['max']){
                $sensors[$key]['warning'] += 1;
            }
            if ($sensors[$key]['report_time'] >= $today){
                $sensors[$key]['today_rpt'] = 1;
            }
        }
        $sensors = isset($sensors) ? array_values($sensors) : array();
        //排序 (nameASC)
        foreach ($sensors as $k => $row) {
            // $sort_warning[$k] = $row['warning'];
            $sort_name[$k] = $row['name'];
            // $sort_rpt[$k] = $row['today_rpt'];
        }
        array_multisort($sort_name, SORT_ASC, $sensors);
        $this->assign('sensors', $sensors);
		$this->display('lssj');
	}

    //如果设置了启用的仪表盘，则显示仪表盘页面
    private function dashboard($dashboard_id){
        $this->assign('dashboard_id', $dashboard_id);
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Rtu/lora'), L('DATA_LIST')),
            L('VAR_DASHBOARD')." ({$_REQUEST['gateway_sn']})"
        ));
        $this->display('dashboard');
    }

    /**
     * data_num != 0 查询网关的data
     * data_num = 0 查询该网关下的lora的data
     * @return [type] [description]
     */
    public function getSensorData(){
        $m = M('rtu_data');
        $data_num = I('data_num',0,'intval');
        $gateway_sn = I('gateway_sn');
        $rs = $m->where($data_num != 0 ? "rtu_data.sn = '$gateway_sn'" : "rtu_data.sn IN(SELECT sn FROM term WHERE gateway_sn = '$gateway_sn')")
            ->field('rtu_data.sn, rtu_data.slave_id, rtu_data.addr, rtu_data.report_time, rtu_data.value, rtu_data_set.unit, rtu_warning.value AS warning_value')
            ->join("LEFT JOIN rtu_data_set ON rtu_data_set.slave_id = rtu_data.slave_id AND rtu_data_set.addr = rtu_data.addr
                    LEFT JOIN rtu_warning ON rtu_warning.rtu_data_set_id = rtu_data_set.id AND rtu_warning.sn = rtu_data.sn AND rtu_warning.is_recover = 0")
            ->order('rtu_data.sn ASC')->select();
        $this->ajaxReturn($rs?$rs:array(), 'ok', 0);
    }

    //编辑项目配置
    public function projectEdit(){
        $m = M('rtu_project_info');
        $sn = I('sn');
        $set_gps = I('set_gps',0,'intval');
        $d = array(
            'sn' => $sn,
            'address' => I('address'),
            'contact' => I('contact'),
            'tel' => I('tel')
        );

        /*if ($this->oem == 'WANKE'){
            $enable_collect = I('enable_collect','','string');
            $d['info1'] = I('info1');
            $d['city'] = I('city');
            $d['area'] = I('area');
            $d['enable_collect'] = $enable_collect == 'on' ? 'True' : 'False';
            $d['uprate'] = I('uprate');
        }*/

        /*配置同步修改到Lora设备
        $app_to_lora = I('app_to_lora','','string');
        if ($app_to_lora == 'on'){
            $loras = M('term')->where("gateway_sn = '%s'",$sn)->getField('sn',true);
            if ($loras){
                $exists = M('rtu_project_info')->where("sn IN('".implode("','", $loras)."')")->getField('sn',true);
                foreach ($loras as $k => $v) {
                    if (in_array($v, $exists, true)){
                        $d_update[] = $v;
                    }else{
                        $d_add[] = array(
                            'sn' => $v,
                            'prjname' => $d['prjname'],
                            'name' => $v,
                            'contact' => $d['contact'],
                            'tel' => $d['tel'],
                            'address' => $d['address'],
                        );
                    }
                }
                if (isset($d_update)){
                    $m->where("sn IN('".implode("','", $d_update)."')")->save(array(
                        'prjname' => $d['prjname'],
                        'contact' => $d['contact'],
                        'tel' => $d['tel'],
                        'address' => $d['address'],
                    ));
                }
                if (isset($d_add)){
                    $m->addAll($d_add);
                }
            }
        }*/

        //配置是否已存在
        $c = $m->where("sn = '$sn'")->count();
        if ($c == 0){
            if ($set_gps != 1){
                if ($row = M('term_gps')->where("sn = '$sn'")->find()) {
                    $d['longitude'] = $row['longitude'];
                    $d['latitude'] = $row['latitude'];
                } else {
                    M('term_gps')->add(array('sn'=>$sn, 'longitude'=>C('GPS_DEFAULT_LNG'), 'latitude'=>C('GPS_DEFAULT_LAT'), 'report_time'=>date('Y-m-d H:i:s')));
                    $d['longitude'] = C('GPS_DEFAULT_LNG');
                    $d['latitude'] = C('GPS_DEFAULT_LAT');
                }
            }
            $m->add($d);
        } else {
            $d['is_report'] = 0;
            $m->save($d);
        }
        M('term')->where("sn = '%s'", $sn)->save(array('alias' => I('name', '', 'trim')));

        //是否设置GPS
        if ($set_gps == 1){
            $mgps = M('term_gps');
            $c2 = $mgps->where("sn = '$sn'")->count();
            $lng = I('lng');
            $lat = I('lat');

            $l = strtolower($_COOKIE['think_language']);
            if ($l == 'zh-cn'){
                //google转换为gps坐标
                import('@.ORG.Gps');
                $gps = new Gps();
                $ret = $gps->gcj_decrypt($lat, $lng);
                $lng = $ret['lon'];
                $lat = $ret['lat'];
            }

            $dgps = array(
                'sn' => $sn,
                'longitude' => $lng,
                'latitude' => $lat,
            );
            $m->save($dgps);
            $dgps['report_time'] = date('Y-m-d H:i:s');
            $c2 == 0 ? $mgps->add($dgps) : $mgps->where("sn = '$sn'")->save($dgps);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //告警记录
    public function gjjl(){
        if (IS_AJAX){
            $m = M('rtu_warning');
            $q = sprintf('group_id IN(%s) AND %s', $this->getTgids(), $this->generate_search_str());
            $rs = $m->field("rtu_warning.*, rtu_data_set.name AS sensor_name, rtu_data_set.unit, rtu_data_set.slave_id,
                rtu_data_set.addr, rtu_data_set.info, rtu_data_set.set_type, term.alias AS dev_name, 0 AS warning_type")
                ->join("INNER JOIN term ON term.sn = rtu_warning.sn
                        LEFT JOIN rtu_data_set ON rtu_data_set.id = rtu_warning.rtu_data_set_id
                        LEFT JOIN rtu_project_info ON rtu_project_info.sn = rtu_warning.sn")
                ->where($q)
                ->order($this->generate_order_str())
                ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
            $total = $m->join("INNER JOIN term ON term.sn = rtu_warning.sn")->where($q)->count();
            die (json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            )));
        }
        $this->assign('web_path_1', array(L('VAR_ALARM_RECORD')));
        $this->display('gjjl');
    }

    // 数据透传
    public function sjtc() {
        if (IS_AJAX) {
            $act = trim($_REQUEST['act']);
            if ($act == 'load_config_sn') {
                $m = M('term_transfer_config_sn');
                $config_id = I('config_id', 0, 'intval');

                $uid = $_SESSION[C('SESSION_NAME')]['id'];
                $q = sprintf('config_id = %d', $config_id);
                $rs = $m->field('term_transfer_config_sn.*, term.ud_sn, term.alias')
                    ->join('LEFT JOIN term ON term.sn = term_transfer_config_sn.sn')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
                foreach ($rs as $key => $row) {
                    $rs[$key]['term_type_text'] = $row['term_type'] == 0 ? L('UPPER_MACHINE') : L('LOWER_MACHINE');
                }
                $total = $m->where($q)->count();
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            } elseif ($act == 'load_config') {
                $m = M('term_transfer_config');
                $uid = $_SESSION[C('SESSION_NAME')]['id'];
                $q = sprintf('%s AND %s', $this->generate_search_str(), $uid == 1 ? '2=2' : 'ugid='.$this->getUgid());
                $rs = $m->field('term_transfer_config.*, (SELECT COUNT(*) FROM term_transfer_config_sn WHERE config_id = term_transfer_config.id AND term_type = 0)AS up_num,
                    (SELECT COUNT(*) FROM term_transfer_config_sn WHERE config_id = term_transfer_config.id AND term_type = 1)AS down_num')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
                $total = $m->where($q)->count();
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            } elseif ($act == 'load_terms') {
                $m = M('term');
                $uid = $_SESSION[C('SESSION_NAME')]['id'];
                $q = sprintf('%s AND %s AND sn NOT IN(SELECT sn FROM term_transfer_config_sn)', $this->generate_search_str(), $uid == 1 ? '2=2' : sprintf('group_id IN(%s)', $this->getTgids()));
                $rs = $m->field('sn, ud_sn, alias, iccid')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
                $total = $m->where($q)->count();
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            }
        } else {
            $this->assign('web_path_1', array(L('DATA_TRANSMISSION')));
            $this->display('sjtc');
        }
    }

    // 数据透传-操作集合
    public function sjtcOperation() {
        $act = I('act', '', 'string');
        $config_id = I('config_id', 0, 'intval');
        if ($act == 'add_cfg') {
            // 新增转发配置
            if (M('term_transfer_config')->add(array(
                'name' => I('name'),
                'is_enable' => I('is_enable', 1, 'intval'),
                'info' => I('info'),
                'creator' => $_SESSION[C('SESSION_NAME')]['name'],
                'ugid' => $_SESSION[C('SESSION_NAME')]['gid']
            ))) {
                $config_id = M('term_transfer_config')->getLastInsID();
            }
        } elseif ($act == 'edit_cfg') {
            // 编辑转发配置
            M('term_transfer_config')->where('id = %d', $config_id)->save(array(
                'name' => I('name'),
                'is_enable' => I('is_enable', 1, 'intval'),
                'info' => I('info')
            ));
        } elseif ($act == 'delete_cfg') {
            // 删除转发配置
            M('term_transfer_config')->where('id = %d', $config_id)->delete();
        }
        // 更新上位机/下位机
        if ($act == 'add_cfg' || $act == 'edit_cfg') {
            $dev0 = explode(',', I('dev0', '', 'trim'));
            $dev1 = explode(',', I('dev1', '', 'trim'));
            $datas = array();
            foreach ($dev0 as $v) {
                array_push($datas, array(
                    'config_id' => $config_id,
                    'sn' => $v,
                    'term_type' => 0
                ));
            }
            foreach ($dev1 as $v) {
                array_push($datas, array(
                    'config_id' => $config_id,
                    'sn' => $v,
                    'term_type' => 1
                ));
            }
            if ($config_id) {
                M('term_transfer_config_sn')->where('config_id = %d', $config_id)->delete();
            }
            if (count($datas) > 0) {
                M('term_transfer_config_sn')->addAll($datas);
            }
        }
        $this->ajaxReturn(null, L('OPERATION_SUCCESS'), 0);
    }

    // 充电站
    public function cdz() {
        if (IS_AJAX) {
            $act = trim($_REQUEST['act']);
            if ($act == 'load_stations') {
                $m = M('oem_charge_station');
                $q = sprintf("%s", $this->generate_search_str());
                $rs = $m->field('*')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
                $total = $m->where($q)->count();
                $cst_arr = L('CHARGING_STATION_TYPE');
                foreach ($rs as $key => $row) {
                    $rs[$key]['station_type_text'] = $cst_arr[$row['station_type']];
                }
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            } elseif ($act == 'load_ports') {
                $m = M('oem_charge_station_port');
                $station_id = I('station_id', '', 'trim');
                $q = sprintf("station_id = '%s'", $station_id);
                $rs = $m->field('*')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])
                    ->select();
                // $cs_arr = L('CHARGE_STATE');
                foreach ($rs as $key => $row) {
                    $rs[$key]['charge_time'] = seconds_to_his($row['charge_time']);
                    // $rs[$key]['charge_state_text'] = $cs_arr[$row['charge_state']];
                }
                $total = $m->where($q)->count();
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            }
        } else {
            $this->assign('web_path_1', array(L('CHARGING_STATION')));
            $this->display('cdz');
        }
    }

    // 网分仪
    public function wfy() {
        if (IS_AJAX) {
            $act = trim($_REQUEST['act']);
            if ($act == 'load_wfy') {
                $m = M('oem_lixun_vna');
                $q = sprintf("%s", $this->generate_search_str());
                $rs = $m->field('*')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
                $total = $m->where($q)->count();
                $cst_arr = L('CHARGING_STATION_TYPE');
                $ids = array();
                $onlines = array();
                foreach ($rs as $key => $row) {
                    array_push($ids, $row['id']);
                }
                if (count($ids) != 0) {
                    $rs2 = M('oem_lixun_vna_report_record')->where('vna_id IN(%s)', implode(',', $ids))->field('vna_id, status')->select();
                    foreach ($rs2 as $key => $row) {
                        if (!isset($onlines[$row['vna_id']])) {
                            $onlines[$row['vna_id']] = array(0, 0); // offline, online
                        }
                        $onlines[$row['vna_id']][intval($row['status'])] += 1;
                    }
                    if (count($onlines) != 0) {
                        foreach ($onlines as $vna_id => $row) {
                            $tmp_total = $row[0] + $row[1];
                            $onlines[$vna_id] = $tmp_total == 0 ? '0%' : round($row[1]/$tmp_total*100, 2).'%';
                        }
                    }
                }
                foreach ($rs as $key => $row) {
                    $rs[$key]['online_rate'] = isset($onlines[$row['id']]) ? $onlines[$row['id']] : '0%';
                }
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            } elseif ($act == 'load_records') {
                $m = M('oem_lixun_vna_report_record');
                $vna_id = I('vna_id', 0, 'intval');
                $q = sprintf("vna_id = %d", $vna_id);
                $rs = $m->field('*')
                    ->where($q)
                    ->order($this->generate_order_str())
                    ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])
                    ->select();
                import("ORG.Util.MString");
                $save_len = 15;
                foreach ($rs as $key => $row) {
                    $rs[$key]['d1'] = trim($row['d1']) == '' ? '' : MString::msubstr($row['d1'], 0, $save_len);
                    $rs[$key]['d2'] = trim($row['d2']) == '' ? '' : MString::msubstr($row['d2'], 0, $save_len);
                    $rs[$key]['d3'] = trim($row['d3']) == '' ? '' : MString::msubstr($row['d3'], 0, $save_len);
                }
                $total = $m->where($q)->count();
                die(json_encode(array(
                    'page' => $this->pp['page'],
                    'total' => ceil($total / $this->pp['rp']),
                    'records' => $total,
                    'rows' => $rs,
                    'userdata' => array()
                )));
            } elseif ($act == 'load_d123') {
                $this->ajaxReturn(M('oem_lixun_vna_report_record')->where('id = %d', I('id', 0, 'intval'))->getField(I('field')), L('OPERATION_SUCCESS'), 0);
            } elseif ($act == 'edit_info') {
                M('oem_lixun_vna')->where('id = %d', I('id'))->save(array(
                    'name' => I('name', '', 'trim'),
                    'info' => I('info', '', 'trim')
                ));
                $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
            }
        } else {
            $this->assign('web_path_1', array(L('NETWORK_ANALYZER')));
            $this->display('wfy');
        }
    }

    //导出告警记录
    public function exportAlarmRecord(){
        session_write_close();
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $m = M('rtu_warning');
        $q = sprintf('group_id IN(%s) AND %s', $this->getTgids(), $this->generate_search_str());
        $rs = $m->field("rtu_warning.*, 0 AS warning_type, rtu_data_set.name AS sensor_name, rtu_data_set.unit, rtu_data_set.info, rtu_data_set.set_type, rtu_project_info.prjname, rtu_project_info.name")
            ->join("INNER JOIN term ON term.sn = rtu_warning.sn
                    LEFT JOIN rtu_data_set ON rtu_data_set.id = rtu_warning.rtu_data_set_id
                    LEFT JOIN rtu_project_info ON rtu_project_info.sn = rtu_warning.sn")
            ->where($q)
            ->order('report_time DESC')
            ->select();
        $header = array(L('VAR_SN2'), L('PROJECT_NAME'), L('DEVICE_NAME'), L('RTU_WARN_TYPE'), L('ALARM_INFO'), L('ALARM_TIME'), L('IS_RECOVER'), L('RECOVER_VALUE'), L('RECOVER_TIME'));
        $arr = L('RTU_DATA_ALARM_TYPE');
        foreach ($rs as $k => $row) {
            if ($row['warning_type'] == '0'){
                $min = floatval($row['min']);
                $max = floatval($row['max']);
                $v = floatval($row['value']);
                $str = L($v > $max ? 'ALARM_STR_GT_MAX' : 'ALARM_STR_LT_MIN');
                $str = $row['set_type'] == 1 ? ($row['info'].' ('.L('CURRENT_VALUE_IS').'：'.$row['value'].')') : str_replace(array('%a','%b','%c'), array($row['sensor_name'],$v, $v>$max?$max:$min), $str);
            } else if ($row['warning_type'] == '1') {
               $str = str_replace('<br>', " ", rep_op_text($row['content']));
            } else {
                $str = rep_op_text($row['content']);
            }
            $body[] = array(
                $row['sn'],
                $row['prjname'],
                $row['name'],
                $row['warning_type'] == '0' ? L('THRESHOLD_ALARM') : $arr[$row['warning_type']-1],
                $str,
                $row['catch_time'],
                $row['warning_type'] == '0' && $row['is_recover'] == 1 ? L('RECOVERED')  : '--',
                $row['warning_type'] == '0' && $row['is_recover'] == 1 ? $row['r_value'] : '--',
                $row['warning_type'] == '0' && $row['r_catch_time'] != '0000-00-00 00:00:00' ? $row['r_catch_time'] : '--'
            );
        }
        $data = array(
            'filename' => 'alarm_record_'.date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'alarm_record'
        );
        $this->ajaxReturn($data, '', 0);
    }

     // 重新上报项目配置
    public function reReportProjectInfo(){
        $sns = I('sns','','string');
        if ($sns != ''){
            $sns = str_replace(',', "','", $sns);
            M('rtu_project_info')->where("sn IN('$sns')")->save(array('is_report' => 0));
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //仪表盘管理
    public function dashboardManage(){
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Rtu/lora'), L('DATA_LIST')),
            L('DASHBOARD_MANAGE')
        ));
        $this->display('dashboardManage');
    }

    /*从缓存文件中获取仪表盘列表数据
    public function loadDashboards(){
        $name = I('get.name', '', 'trim');
        $rs = F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_list', '', './tmp/');
        usort($rs, function($a,$b){
            if ($a['name'] === $b['name']) return 0;
            return $a['name'] < $b['name'] ? -1 : 1;
        });
        if ($name != ''){
            foreach ($rs as $k => $row) {
                if (strpos($row['name'], $name) === false){
                    unset($rs[$k]);
                }
            }
        }
        $this->ajaxReturn($rs?array_values($rs):array(), '', 0);
    }*/

    //仪表盘编辑
    public function dashboardEdit(){
        $dashboard_id = $_REQUEST['dashboard_id'];
        if (IS_AJAX){
            //保存修改
            F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_'.$dashboard_id, $_REQUEST['svg'], './tmp/');
            $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
        }
        $this->assign('dashboard_id', $dashboard_id);
        $arr = $this->getSensors('assoc_array');
        $this->assign('sensors', count($arr) > 0 ? json_encode($arr) : '{}');
        $this->display('dashboardEdit');
    }

    //仪表盘预览
    public function dashboardView(){
        $dashboard_id = $_REQUEST['dashboard_id'];
        $json = F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_'.$dashboard_id, '', './tmp/');
        if (!$json){
            $json = '[]';
        }
        if (IS_AJAX){
            $this->ajaxReturn($json, L('OPERATION_SUCCESS'), $json ? 0 : -1);
        }
        $this->assign('json', $json);
        $this->display('dashboardView');
    }

    //仪表盘操作(启用，复制，改名，删除)
    public function dashboardOperating(){
        $id = I('id');
        $name = I('name');
        $act = I('act');
        $arr = F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_list', '', './tmp/');
        if ($arr === false){
            if ($act == 'add'){
                $arr = array();
            }else{
                $this->ajaxReturn('', L('VAR_CMD_SEND_FAILED'), -1);
            }
        }
        if ($act == 'add'){
            $add_id = time();
            array_push($arr, array(
                'id' => $add_id,
                'name' => '',
                'use' => 0,
                'create_time' => date('Y-m-d H:i:s')
            ));
        } elseif ($act == 'use'){
            $type = I('type', 1, 'intval');
            foreach ($arr as $k => $row) {
                $arr[$k]['use'] = $row['id'] == $id ? $type : 0;
            }
        } elseif ($act == 'copy'){
            $json = F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_'.$id, '', './tmp/');
            if ($json){
                $new_id = time();
                array_push($arr, array(
                    'id' => $new_id,
                    'name' => '',
                    'use' => 0,
                    'create_time' => date('Y-m-d H:i:s')
                ));
                F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_'.$new_id, $json, './tmp/');
            }
        } elseif ($act == 'rename'){
            foreach ($arr as $k => $row) {
                if ($row['id'] == $id){
                    $arr[$k]['name'] = $name;
                    break;
                }
            }
        } elseif ($act == 'delete'){
            F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_'.$id, null, './tmp/');
            foreach ($arr as $k => $row) {
                if ($row['id'] == $id){
                    unset($arr[$k]);
                    break;
                }
            }
            $arr = array_values($arr);
        }

        F($_SESSION[C('SESSION_NAME')]['name'].'_dashboard_list', $arr, './tmp/');
        $this->ajaxReturn($add_id, L('OPERATION_SUCCESS'), 0);
    }

    //仪表盘编辑页面，图形绑定传感量使用
    public function getSensors($ret = ''){
        $m = M('rtu_data_set');
        $rs = $m->field('addr AS id,name,unit,min,max')->order('name ASC')->select();
        if ($ret == 'html'){
            $str = '';
            foreach ($rs as $k => $row) {
                $str .= sprintf('<option value="%s">%s</option>', $row['id'], $row['name']);
            }
            return $str;
        } elseif ($ret == 'assoc_array') {
            foreach ($rs as $k => $row) {
                $arr[$row['id']] = $row;
            }
            return isset($arr) ? $arr : array();
        }
        array_unshift($rs, array('id'=>'0', 'name'=>L('SELECT_SENSOR'), 'unit'=>'', 'min'=>'0', 'max'=>'100'));
        $this->ajaxReturn($rs, '', 0);
    }

    //RTU数据列表页面，导出报表
    public function exportRtu(){
        $m = M('term');
        $sns = I('sns','','string');
        if ($sns != ''){
            $q = "term.sn IN('".str_replace(',', "','", $sns)."')";
            unset($sns);
            $total = $m->where($q)->count();
        }else{
            $gid = I('gid',-10,'intval');
            $q = sprintf('%s AND group_id IN(%s)', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids());
            $total = $m->where($q)->count();
        }

        $sql = "SELECT term.sn, term_run_info.is_online, term_run_info.last_time, term_group.name AS prjname, term.alias AS name FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN term_group ON term_group.id = term.group_id
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn
            WHERE $q ORDER BY ".$this->generate_order_str('router_list');
        $terms = $m->query($sql);
        $ts = time();
        $sns = array();
        foreach ($terms as $k => $row){
            $s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
            $terms[$k]['status'] = L($s == '1' ? 'VAR_TERM_STATUS_ONLINE' : 'VAR_TERM_STATUS_OFFLINE');
            $sns[] = "'".$row['sn']."'";
        }

        $header = array(L('VAR_TERM_STATUS'), L('VAR_SN2'), L('VAR_LAST_LOGIN'), L('PROJECT_NAME'), L('DEVICE_NAME'));
        $rs = M('rtu_data_set')->field('addr,name')->where('set_type = 0')->order('name ASC')->select();
        foreach ($rs as $k => $row) {
            array_push($header, $row['name']);
            $addrs[] = $row['addr'];
        }
        unset($rs);

        $q_sns   = is_array($sns)   ? implode(',', $sns)   : '0';
        $q_addrs = is_array($addrs) ? implode(',', $addrs) : 0;
        $rs = M('rtu_data')->field('sn,addr,value')->where("sn IN($q_sns) AND addr IN($q_addrs)")->select();
        foreach ($rs as $k => $row) {
            $sensors[$row['sn']][$row['addr']] = $row['value'];
        }
        unset($rs);

        foreach ($terms as $k => $row) {
            $tmp = array($row['status'], $row['sn'], $row['last_time'], $row['prjname'], $row['name']);
            foreach ($addrs as $key => $addr) {
                array_push($tmp, isset($sensors[$row['sn']][$addr]) ? $sensors[$row['sn']][$addr] : '--');
            }
            $body[] = $tmp;
        }

        $data = array(
            'filename' => 'router_data_'.date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'lora'
        );
        $this->ajaxReturn($data, '', 0);
    }

    //刷新仪表盘数据
    public function loadDashboardData(){
        $sn = I('sn','','string');
        $t = I('data_type', 0, 'intval');
        $addrs = I('sids','','string');
        if ($t == 0){
            $rs = M('rtu_data')->where("sn = '$sn' AND slave_id = 1 AND addr IN($addrs)")->field('addr,value')->select();
            foreach ($rs as $k => $row) {
                $ret[intval($row['addr'])] = floatval($row['value']);
            }
        }else{
            $slave_id = 1;
            // value_type(s)
            $rs = M('rtu_data_set')->where("slave_id = $slave_id AND addr IN($addrs)")->field('value_type,addr')->select();
            foreach ($rs as $key => $row) {
                $vtypes[$row['addr']] = $row['value_type'];
            }
            unset($rs);

            $addrs = explode(',', $addrs);
            $pagesize = explode(',', I('pagesize'));
            foreach ($addrs as $key => $addr) {
                $ret[$addr] = array(
                    'x' => array(),
                    'y' => array(),
                    'info' => ''
                );
                $filepath = C('FTP_WEB_PACK_PATH').sprintf("rtu/%s/%s_%d_%d.bin", substr($sn,-1,1), $sn, $slave_id, $addr);
                if (!file_exists($filepath)){
                    $ret[$addr]['info'] = L('DATA_FILE_NOT_EXIST');
                   continue;
                }

                $fp = fopen($filepath, 'rb');
                fseek($fp, 0, SEEK_END);
                $size = ftell($fp);
                $value_type = $vtypes[$addr];
                switch ($value_type) {
                    case 4:
                        $format = 'f';
                        break;
                    default:
                        $format = 'N';
                        break;
                }
                $data_len = 4; //数据长度
                $size = $pagesize[$key]; //历史数据，读取最后n条，可在编辑仪表盘时设置
                $bytes = 10 * (8 + $data_len);
                fseek($fp, 0-$bytes, SEEK_END);
                $str = fread($fp, $bytes);
                for ($i=0,$j=0; $j<$size; $i+=(8+$data_len),$j++){
                    $ts = unpack('N', substr($str, $i, 4));
                    $ts = $ts[1];
                    $catch_ts = unpack('N', substr($str, $i+4, 4));
                    $catch_ts = $catch_ts[1];
                    $v = unpack($format, substr($str, $i+8, $data_len));
                    array_push($ret[$addr]['x'], date('m/d H:i',$ts));
                    array_push($ret[$addr]['y'], round($v[1],2));
                }
                fclose($fp);
            }
        }
        $this->ajaxReturn($ret?$ret:array(), '', 0);
    }

    //RTU数据告警
    public function rtugj(){
        //菜单： {"action": "rtugj", "icon": "fa-angle-right", "lang": "RTU_DATA_ALARM"},
        if (IS_AJAX){
            $arr = F('rtu_data_alert_rules', '', './Upload/');
            if ($arr){
                $rs = M('rtu_data_set')->field('id,name,unit,slave_id,addr')->select();
                foreach ($rs as $k => $row) {
                    $sets[$row['slave_id'].'_'.$row['addr']] = $row;
                }
                unset($rs);

                $qv = trim(I('searchString','','string'));
                foreach ($arr as $k => $row) {
                    if ($qv && strpos($row['name'], $qv) === false){
                        continue;
                    }
                    foreach ($row['rule_detail'] as $key => $value) {
                        $sid = $value['slave_id_addr'];
                        $row['rule_detail'][$key]['name'] = $sets[$sid]['name'];
                        $row['rule_detail'][$key]['unit'] = $sets[$sid]['unit'];
                    }
                    $rs[] = $row;
                }
                $total = count($rs);
                $rs = array_slice($rs, ($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp']);
            }else{
                $rs = array();
                $total = 0;
            }
            die (json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            )));
        }
        $this->assign('web_path_1', array(L('RTU_DATA_ALARM')));
        $this->display('rtugj');
    }

    //RTU数据告警-规则增删改
    public function rtuRuleOp(){
        $act = I('act', '', 'string');
        $arr = F('rtu_data_alert_rules', '', './Upload/');
        $d = array(
            'name' => trim(I('name')),
            'rule_type' => I('rule_type', 0, 'intval')
        );
        $id = trim(I('id'));

        $params = I('params', '', 'trim');
        if ($params != ''){
            $params = explode(';', $params);
            foreach ($params as $k => $row) {
                $items = explode('--', $row);
                $rule_detail[] = array(
                    'bit_op' => $items[0],
                    'slave_id_addr' => $items[1],
                    'op' => $items[2],
                    'value' => $items[3],
                    'duration' => $items[4]
                );
            }
            $d['rule_detail'] = isset($rule_detail) ? $rule_detail : array();
        }

        if ($act == 'add'){
            $d['id'] = date('YmdHis');
            $d['creator'] = $_SESSION[C('SESSION_NAME')]['name'];
            $d['create_time'] = date('Y-m-d H:i:s');
            $arr[] = $d;
        } elseif ($act == 'edit'){
            foreach ($arr as $k => $row) {
                if ($row['id'] === $id){
                    $arr[$k]['name'] = $d['name'];
                    $arr[$k]['rule_type'] = $d['rule_type'];
                    $arr[$k]['rule_detail'] = $d['rule_detail'];
                    break;
                }
            }
        } elseif ($act == 'delete'){
            foreach ($arr as $k => $row) {
                if ($row['id'] === $id){
                    unset($arr[$k]);
                    break;
                }
            }
            $arr = array_values($arr);
        }
        F('rtu_data_alert_rules', $arr, './Upload/');
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //header页面右上角告警信息，5s刷新
    public function getWarningInfo(){
        $m = M('rtu_warning');
        $tm = date('Y-m-d H:i:s', strtotime('-1 hours'));
        $q = "report_time >= '$tm'";
        $rs = $m->field("rtu_warning.*, 0 AS warning_type, rtu_data_set.name AS sensor_name, rtu_data_set.unit, rtu_data_set.slave_id,
            rtu_data_set.addr, rtu_data_set.info, rtu_data_set.set_type")
            ->join("INNER JOIN term ON term.sn = rtu_warning.sn
                    LEFT JOIN rtu_data_set ON rtu_data_set.id = rtu_warning.rtu_data_set_id")
            ->where($q)->order('report_time DESC')->limit(5)->select();
        $this->ajaxReturn(array(
            'num' => $m->where($q)->count(),
            'rows' => $rs
        ), L('OPERATION_SUCCESS'), 0);
    }

    //RTU数据告警-根据id返回一条数据
    private function getRtuRule($id){
        $arr = F('rtu_data_alert_rules', '', './Upload/');
        if (!$arr){
            return null;
        }
        foreach ($arr as $k => $row) {
            if ($row['id'] == $id){
                return $row;
            }
        }
    }

    //删除告警记录
    public function delAlarmRecords(){
        $ids = I('ids', '', 'string');
        $start = I('start', '', 'string');
        $end = I('end', '', 'string');
        if ($ids != '') {
            $q = "id IN($ids)";
        } elseif ($start != '' && $end != '') {
            $q = "report_time BETWEEN '$start' AND '$end'";
        } else {
            $q = '1=2';
        }
        M('rtu_warning')->where($q)->delete();
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }
}