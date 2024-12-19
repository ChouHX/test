<?php
class TaskAction extends CommonAction{
    private $tparams = array();
    private function getq(){
        $q = '1 = 1';
        $p = $this->tparams;
        if ($p['only_online'] == 1){
            $q .= ' AND is_online = 1 AND TIMESTAMPDIFF(SECOND,last_time,now()) <= '.C('TERM_OFFLINE_TIME');
        }
        return $q;
    }

    public function _initialize(){
        //处理任务参数
        $this->tparams['term_list'] = '';
        $this->tparams['start_time'] = date('Y-m-d H:i:s', I('start_ts',time(),'intval'));
        $this->tparams['is_never_expire'] = $ine = I('is_never_expire', 0, 'intval');
        $this->tparams['end_time'] = date('Y-m-d H:i:s', I('end_ts',strtotime('+30 days'),'intval'));
        $this->tparams['act'] = $act = I('act', 'term', 'string');
        $this->tparams['only_online'] = I('only_online', 0, 'intval');
        $this->tparams['auto_retry'] = I('auto_retry', 0, 'intval');
        $max = I('max', 1000, 'intval');
        $q = $this->getq();

        $gids = I('gids', '-1', 'string');
        if ($act == 'term') {
            $this->tparams['term_list'] = I('term_list', '', 'string');
            $this->tparams['log_device'] = 'type=term&sns='.$this->tparams['term_list'];
        } elseif ($act == 'group') {
            //gids = -10表示全部分组
            $this->tparams['term_list'] = M('term')
                ->where("%s AND %s", $gids == '-10' ? ('group_id IN('.$this->getTgids().')') : ("group_id IN($gids)"), $q)
                ->limit($max)
                ->field('term.sn')
                ->getField('sn',true);
            $this->tparams['log_device'] = 'type=group&group_ids='.$gids;
        } elseif ($act == 'all') {
            $this->tparams['term_list'] = M('term')
                ->where("%s AND %s", 'group_id IN('.$this->getTgids().')', $q)
                ->limit($max)
                ->field('term.sn')
                ->getField('sn',true);
            $this->tparams['log_device'] = 'type=all';
        }

        // 检查所选设备是否支持该任务
        $tm_arr = C('TERM_MODEL');
        if (I('check_model', 0, 'intval') == 1) {
            $enable_model = C($_REQUEST['enable_model']);
            $sns = is_array($this->tparams['term_list']) ? implode(',', $this->tparams['term_list']) : $this->tparams['term_list'];
            $sns = "'".str_replace(',', "','", $sns)."'";
            $rs = M('term')->where("sn in($sns)")->field('sn, term_model')->select();
            foreach ($rs as $key => $row) {
                $tmp_model = $this->formatTermModel($row['term_model']);
                if (!in_array($tmp_model, $enable_model, true)) {
                    $this->ajaxReturn('', L('UNSUPPORTED_DEVICE_TYPE')."<br>".
                        sprintf("%s：%s<br>%s：%s", L('VAR_SN2'), $row['sn'], L('DEVICE_MODEL'), isset($tm_arr[$tmp_model]) ? $tm_arr[$tmp_model] : L('VAR_UNKNOWN')), -1
                    );
                }
            }
        }
    }

    private function dosome($cmd, $value, $term_list, $value2='', $is_return=1, $addon=array()){
        $tp = $this->tparams;
        $term_list = $tp['term_list'];
        if ($term_list == ''){
            $this->ajaxReturn('', L('NO_MATCH_TERM'), -64);
        }
        $username = $_SESSION[C('SESSION_NAME')]['name'];
        $ugid = $this->getUgid();
        if (!$username) $username = 'system';
        if (!$ugid) $ugid = 1;
        $this->wlog($username, $cmd, $value, $tp['log_device'], $ugid, $value2);

        $create_time = date('Y-m-d H:i:s');
        if ($tp['start_time'] <= $create_time) {
            $tp['start_time'] = $create_time;
        }

        // timed_type不等于-1时表示周期任务，要写入timed_term_task表
        $timed_type = I('timed_type', -1, 'intval');
        $md = M($timed_type != -1 ? 'timed_term_task_detail' : 'term_task_detail');
        $md->startTrans();
        $ret = 1;
        $ret2 = 1;

        // 针对单个设备，查询是否已经有未完成的任务，给出提示信息
        if ($timed_type == -1 && is_string($term_list) && !strpos($term_list, ',')) {
            $unfinished = $md->where("sn = '%s' AND status IN(0,1,2) AND task_id NOT IN(SELECT id FROM term_task WHERE is_enable = 0)", $term_list)->count();
        }

        if ($timed_type != -1) {
            // 定时任务只需要写入 timed_term_task表
            $md->execute(sprintf("INSERT INTO timed_term_task (cmd,username,value,ugid,create_time,start_time,end_time,is_never_expire,period_type,period_value,sn_list)VALUES('%s', '%s', '%s', %d, '%s', '%s', '%s', %d, '%s', '%s', '%s')",
                $cmd, $username, $value, $ugid, $create_time,
                $tp['start_time'], $tp['end_time'], $tp['is_never_expire'],
                $timed_type, I('timed_params', '', 'trim'),
                is_array($this->tparams['term_list']) ? implode(',', $this->tparams['term_list']) : $this->tparams['term_list']
            ));
            $lastId = $md->getLastInsID();
        } else {
            $md->execute(sprintf("INSERT INTO term_task (cmd,username,value,ugid,create_time,start_time,end_time,is_never_expire,auto_retry)VALUES('%s','%s','%s',%d, '%s', '%s','%s',%d,%d)",
                $cmd, $username, $value, $ugid, $create_time,
                $tp['start_time'], $tp['end_time'], $tp['is_never_expire'], $tp['auto_retry']
            ));

            // 写入任务详情表
            $lastId = $md->getLastInsID();
            if (!is_array($term_list)) {
                $term_list = explode(',', $term_list);
            }
            foreach ($term_list as $k => $v) {
                $d[] = array('task_id' => $lastId, 'sn' => $v, 'end_time' => $tp['end_time']);
            }
            $ret = $md->addAll($d);

            // 部分任务要写入下载进度表
            $rs = $md->where('task_id = %d', $lastId)->field('id, sn')->select();
            if ($cmd == 'upgrade' || $cmd == 'download_cfg') {
                preg_match("/fileid=(\d+)/i", $value, $match1);
                preg_match("/filesize=(\d+)/i", $value, $match2);
                // 升级和配置文件下发有进度
                foreach ($rs as $i => $r) {
                    $dataList_download_report[] = array(
                        'sn'             => $r['sn'],
                        'fileid'         => $match1[1],
                        'filesize'       => $match2[1],
                        'task_detail_id' => $r['id']
                    );
                }
                $ret2 = M('download_report')->addAll($dataList_download_report);
            }
        }

        if ($lastId && $ret && $ret2) {
            $md->commit();
            if (!$is_return) return;
            if ($unfinished) {
                $this->ajaxReturn("num = $unfinished", L('UNFINISHED_TASK_TIPS'), 1);
            } else {
                $this->ajaxReturn('', L('VAR_CMD_SEND_OK'), 0);
            }
        } else {
            $md->rollback();
            if ($is_return) {
                $this->ajaxReturn('', $md->getDbError(), -11);
            }
        }
    }

    //手动刷新：基站地址，wifi定位AP Mac地址
    public function resetMac(){
        session_write_close(true);
        if (!C('LBS')){
            $this->ajaxReturn('', L('LOCATION_DISABLED'), -1);
        }
        $type = I('type',0,'intval');
        $ids = I('ids', '', 'string');
        $m = M($type == 0 ? 'term_cell' : 'term_wifi_ap');
        $c = $m->where("tid IN($ids)")->count();
        if ($c == 0){
            $this->ajaxReturn('', L('NO_POSITION_DATA'), -1);
        }
        $d = array(
            'report_status' => 0,
            'last_api_time' => '1980-01-01 00:00:00',
            'country' => NULL,
            'province' => NULL,
            'city' => NULL,
            'street'=> NULL,
            'street_number' => NULL,
            'addr' => NULL,
        );
        $m->where("tid IN($ids)")->save($d);
        $obj = A('Cgi');
        $obj->base_station_wifi_location($type, $ids);
    }

    //查询参数
    public function configGet(){
        $cmd = 'config_get';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    //设置参数
    public function configSet(){
        $cmd = 'config_set';
        $names = explode(',', $_REQUEST['names']);
        $vals  = explode('{###}', $_REQUEST['vals']);
        foreach ($names as $k=>$v){
            if ($v == 'net_mode'){
                if ($vals[$k] == 0){
                    $params[$k] = 'cellType="0"&cell_mode="0"';
                }elseif($vals[$k] == 1){
                    $params[$k] = 'cellType="1"';
                }elseif($vals[$k] == 2){
                    $params[$k] = 'cellType="2"&cell_mode="1"';
                }elseif($vals[$k] == 3){
                    $params[$k] = 'cellType="3"&cell_mode="1"';
                }elseif($vals[$k] == 4){
                    $params[$k] = 'cell_mode="4"';
                }
            }elseif ($v == 'send_script'){
                $params[$k] = $vals[$k];
            }elseif ($v == 'dmz_ipaddr'){
                //dmz_ipaddr路由器返回的是ip最后一段，设置时也要设置最后一段
                $dmz_ipaddr = explode('.', $vals[$k]);
                $params[$k] = $v.'="'.intval($dmz_ipaddr[3]).'"';
            /*}elseif ($v == 'wan_proto'){
                //修改pppoe连接方式时，要附加额外参数
                // 1. cur_wan_proto = wan_proto
                // 2. wan_proto为ppp3g时，ppp_demand的值为0，其他情况下为4
                // 3. wan_proto为ppp3g时，cellConMode的值为1，其他情况下为0
                $wan_proto = $vals[$k];
                $ppp_demand = $wan_proto=='ppp3g' ? 0 : 4;
                $cellConMode = $wan_proto=='ppp3g' ? 1 : 0;
                $params[$k] = sprintf('wan_proto="%s"&cur_wan_proto="%s"&ppp_demand="%d"&cellConMode="%d"', $wan_proto, $wan_proto, $ppp_demand, $cellConMode);
            */
            } elseif ($v == 'portforward' && C('OEM_VERSION') == 'rx-m2m') {
                $tmp = explode('>', $vals[$k]);
                foreach ($tmp as $tk => $tv) {
                    $tmp2 = explode('<', $tv);
                    if ($tmp2[2] == 0) {
                        $tmp2[2] = '';
                    } elseif ($tmp2[2] == 1) {
                        $tmp2[2] = 'usb0';
                    } else {
                        $tmp2[2] = 'ppp1';
                    }
                    $tmp[$tk] = implode('<', $tmp2);
                }
                $params[$k] = $v.'="'.implode('>', $tmp).'"';
            } elseif ($v == 'set_password_2' || $v == 'set_upassword_2') {
                // 注意：修改路由器密码，此处加了个限制，只有admin才能下发。
                if ($vals[$k] != '**********' && $_SESSION[C('SESSION_NAME')]['name'] == 'admin') {
                    $params[$k] = sprintf('%s=%s', ($v == 'set_password_2' ? 'http_passwd' : 'http_guestpass'), $vals[$k]);
                }
            } else {
                $params[$k] = $v.'="'.$vals[$k].'"';
            }
        }
        $value = implode('&', $params);
        $this->dosome($cmd, $value, $term_list);
    }

    // 自定义参数配置，通过输入vsn选择设备，手动输入要配置的参数值
    public function configSet2() {
        $cmd = 'config_set';
        $vsns = trim(I('vsns'));
        if (!empty($vsns)) {
            $this->tparams['term_list'] = M('term')
                ->where("%s AND vsn IN('".str_replace(',', "','", $vsns)."')", 'group_id IN('.$this->getTgids().')')
                ->field('term.sn')
                ->getField('sn',true);
        }
        $this->dosome($cmd, I('values', '', 'trim'), $term_list);
    }

    // 下发配置文件
    public function downCfg($fileid = 0, $is_auto = false) {
        $cmd = 'download_cfg';
        if (!$is_auto) {
            $fileid = I('fileid',0,'intval');
            $filename = I('filename','','string');
            $filesize = I('filesize','','string');
            $md5_num = I('md5_num','','string');
            $value = 'fileid='.$fileid.'&filename='.$filename.'&filesize='.$filesize.'&md5='.$md5_num;
            $this->dosome($cmd, $value, '');
        } else {
            $row = M('file_list')->where('id = %d', $fileid)->find();
            $value = sprintf('fileid=%s&filename=%s&filesize=%s&md5=%s', $row['id'], $row['filename'], $row['filesize'], $row['md5_num']);
            $this->dosome($cmd, $value, '', '', 0);
        }
    }

    //重启终端
    public function termRestart(){
        $cmd = 'restart';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    //启用以太网接口
    public function interfaceSet(){
        $cmd = 'interface_set';
        $seq = I('seq',0,'intval');
        $value = I('value',0,'intval');
        $values = 'port=lan&seq='.$seq.'&name=status&value='.$value;
        $this->dosome($cmd, $values, $term_list);
    }

    //重启模块
    public function termModuleRestart(){
        $module = I('module','','string');
        $cmd = 'restart_module';
        $value = 'module=' . $module;
        $this->dosome($cmd, $value, $term_list);
    }

    //恢复模块
    public function restartModule(){
        $cmd = 'at_command';
        $value = 'cmd=AT+QPRTPARA=3';
        $this->dosome($cmd, $value, $term_list);
    }

    //sim切换
    public function simChange(){
        $cmd = 'change_sim';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    //定时重启
    public function termSrestart(){
        $h = I('h',0,'intval');
        $m = I('m',0,'intval');
        $time = $h*4*15 + $m;
        $interval = I('interval','','string');
        $interval = bindec($interval);
		$enable = I('enable',1,'intval');
        $this->dosome('config_set', sprintf('sch_rboot="%d,%d,%d"',$enable,$time,$interval));
    }

    //摄像头抓拍
    public function takePhoto(){
        $camera_sn = I('camera_sn', '1', 'intval');
        $shots_number = I('shots_number', '1', 'intval');
        $interval = I('interval', 0, 'intval');
        $value = sprintf("camera=%d&count=%d&interval=%d", $camera_sn, $shots_number, $interval);
        $this->dosome('take_photo', $value, $term_list);
    }

    //升级
    public function termUpgrade(){
        $md = M('term_task_detail');
        $version = $_REQUEST['version'];
        $filename = $_REQUEST['filename'];
        $filesize = $_REQUEST['filesize'];
        $fileid = $_REQUEST['fileid'];
        $md5_num = I('md5_num','','string');
        $type = $_REQUEST['type'];  //type=rtu表示附固件升级

        $cmd = 'upgrade';
        $value = 'fileid='.$fileid.'&filename='.$filename.'&filesize='.$filesize.'&md5='.$md5_num;
        if ($type == 'rtu'){
            $value .= "&type=rtu&rtu_pos=".$_REQUEST['rtu_pos'];
        }
        $this->dosome($cmd, $value, $term_list);
    }

    //网络抓包
    public function catchPackage(){
        $cmd = 'packet_cap';
        $sn = I('term_list');
        $filename = sprintf('%s_%s.pcap', $sn, date('YmdHis'));
        $insid = M('file_list')->add(array(
            'name' => $filename,
            'original_filename' => $filename,
            'filename' => $filename,
            'filetype' => 6,
            'filesize' => 0,
            'relative_path' => 'cap',
            'ugid' => $this->getUgid(),
            'info' => L('PCAP_FILE_UPLOAD_INFO'),
            'sn' => $sn,
            'creator' => $_SESSION[C('SESSION_NAME')]['name'],
        ));
        $value = sprintf("begintime=%s&endtime=%s&level=%d&fileid=%d", date('Y-m-d H:i:s',I('bt')), date('Y-m-d H:i:s',I('et')), I('level',1,'intval'), $insid);
        $this->dosome($cmd, $value, $term_list, L('VAR_CP_FILES').': '.$filename);
    }

    // 清除Flash
    public function clearFlash(){
        $cmd = 'clear_flash';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    //下发广告
    public function adSend(){
        $md = M('term_task_detail');
        $aid = I('fileid', 0, 'intval');

        //该广告的文件
        $rs = $md->query("SELECT * FROM file_list WHERE relative_path = 'ad/$aid'");
        if (!empty($rs[0]['id'])) {
            $filecount = 0;
            foreach ($rs as $j=>$row){
                $filecount++;
                $ss[$j] = "file$filecount=ad/".$aid.'/'.$row['filename']."&size$filecount=".$row['filesize'];
                $ssv2[$j] = sprintf("%s,%s,%s,%s", $row['id'], $row['filesize'], $row['filename'], $row['md5_num']);
            }
            $ss = implode('&', $ss);
            $ssv2 = implode(';', $ssv2);
        }else{
            $this->ajaxReturn('', L('VAR_AD_NO_ADFILE'), -1);
            exit;
        }

        $cmd = 'download_ad';
        $value = 'ip='.C('FTP_IP') .'&port='.C('FTP_PORT') .'&username='.C('FTP_USER') ."&password=".C('FTP_PWD') ."&filecount=$filecount&$ss";
        $value2 = sprintf("ip=%s&port=%s&username=%s&password=%s&version=%s&filecount=%d&fileinfo=%s", C('FTP_IP'), C('FTP_PORT'), C('FTP_USER'), C('FTP_PWD'), '', $filecount, $ssv2);

        $tp = $this->tparams;
        $term_list = $tp['term_list'];
        $ugid = $this->getUgid();
        $this->wlog($_SESSION[C('SESSION_NAME')]['name'], 'download_ad', $value, $tp['log_device'], $ugid, $value2);

        $md->startTrans();
        $md->execute(sprintf("INSERT INTO term_task (cmd,username,value,ugid,start_time,end_time,is_never_expire,auto_retry)VALUES('%s','%s','%s',%d,'%s','%s',%d,%d)",
            $cmd, $_SESSION[C('SESSION_NAME')]['name'], $value, $ugid, $tp['start_time'], $tp['end_time'], $tp['is_never_expire'], $tp['auto_retry']));
        $lastId = $md->getLastInsID();

        if (!is_array($term_list)){
            $term_list = explode(',', $term_list);
        }
        foreach ($term_list as $k => $v){
            $dataList[$k] = array(
                'task_id' => $lastId,
                'sn' => $v,
                'end_time' => $tp['end_time']
            );
        }
        $ret1 = $md->addAll($dataList);
        $ret2 = true;

        $mr = M('download_report');
        if ($ret1){
            $rsDetail = $mr->query("SELECT id,sn FROM term_task_detail WHERE task_id = $lastId");
            foreach ($rsDetail as $i=>$r){
                foreach ($rs as $l=>$ro){
                    $dataList_download_report[] = array(
                        'sn' => $r['sn'],
                        'fileid' => $ro['id'],
                        'filesize' => $ro['filesize'],
                        'task_detail_id' => $r['id'],
                    );
                }
            }
            $ret2 = $mr->addAll($dataList_download_report);
        }

        if ($ret1 && $ret2){
            $md->commit();
            $this->ajaxReturn('', L('VAR_CMD_SEND_OK'), 0);
        }else{
            $md->rollback();
            $this->ajaxReturn('', L('VAR_CMD_SEND_FAILED'), -2);
        }
    }

    //继电器状态(查询，设置)
    public function relayStatus(){
        $cmd = I('act2');
        $value = '';
        if ($cmd == 'relay_set'){
            $value = '1='.I('relay1','off','string').'&2='.I('relay2','off','string');
        }
        $this->dosome($cmd, $value, $term_list);
    }

    // 继电器测试接口
    public function relayControl() {
        $term_list = I('term_list', '', 'string');
        $method_type = I('method_type', '', 'string');
        if ($method_type == 'load_status') {
            sleep(1);
            $relay_status = M('term_interface')->where("sn = '%s' AND relay_status IS NOT NULL", $term_list)->getField('relay_status');
            $this->ajaxReturn($relay_status ? $relay_status : '0', L('VAR_CMD_SEND_OK'), 0);
        }
        $cmd = 'relay_set2';
        $value = sprintf('relay=%s&act=%s', I('relay', '1', 'string'), I('opt', 'on', 'string'));
        $this->dosome($cmd, $value, $term_list);
    }

    //查询路由器中的文件
    public function getFileList(){
        $cmd = 'get_file_list';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    //删除路由器中的文件
    public function deleteFile(){
        $cmd = 'delete_file';
        $value = $_REQUEST['fileList'];
        $this->dosome($cmd, $value, $term_list);
    }

    //强制下线
    public function forceOffline(){
        $macs = $_REQUEST['macs'];
        $cmd = 'device_offline';
        $value = 'cmd=device_offline&mac='.$macs;
        $this->dosome($cmd, $value, $term_list);
    }

    //连接远程通道
    public function rcConnect(){
        $this->dosome('vchannel_connect', '', $term_list);
    }

    //断开远程通道
    public function rcDissconnect(){
        $this->dosome('vchannel_disconnect', '', $term_list);
    }

    // VPN的连接与断开
    public function vpnConnect() {
        $cmd = I('cmd', 'vpn_connect', 'string');
        $this->dosome($cmd, I('value', '', 'string'), $term_list);
    }

    //Rtu查询数据
    public function rtuDataQuery(){
        $type = I('rtu_query_type',0,'intval');
        $ids = I('rtda_ids','0','string');
        if ($type == 0){
            $rs = M('rtu_type_data_set')->where("id IN($ids)")->field('slave_id,addr')->order('id ASC')->select();
            foreach ($rs as $k=>$row){
                $slave_id[] = $row['slave_id'];
                $addr[] = $row['addr'];
            }
            $value = sprintf('slave_id=%s&addr=%s', implode(',',$slave_id), implode(',',$addr));
        }elseif ($type == 1){
            $value = 'slave_id=0&addr=0';
        }
        $cmd = 'sub';
        $this->dosome($cmd, $value, $term_list);
    }

    //控制指令下发
    public function rtuDataSend(){
        $rtds_id = I('rtds_id','','string');
        $data_type = I('data_type', 'hex', 'string');
        $data_content = I('data_content', '', 'string');
        $saveas_common = I('saveas_common', '', 'string');
        $cmd_name = I('cmd_name', '', 'string');
        if ($saveas_common == 'on' && $cmd_name != ''){
            $str = file_get_contents('common_cmd');
            if ($str){
                $cc = json_decode($str, true);
                foreach ($cc as $k=>$row){
                    if ($row['name'] == $cmd_name){
                        $cc[$k]['value'] = $data_content;
                        $cc[$k]['data_type'] = $data_type;
                        $flag = true;
                        break;
                    }
                }
            }
            if (!$flag){
                $cc[] = array('name'=>$cmd_name, 'value'=>$data_content, 'data_type'=>$data_type);
            }
            function _mysort($a,$b){
                if ($a['name'] == $b['name']) return 0;
                return $a['name'] < $b['name'] ? -1 : 1;
            }
            usort($cc, '_mysort');
            file_put_contents('common_cmd', json_encode($cc));
        }
        $slave_addr_arr = explode('_', $rtds_id);
        $value = sprintf("slave_id=%d&addr=%d&value=%s&value_type=%s", $slave_addr_arr[0], $slave_addr_arr[1], $data_content, $data_type);
        $this->dosome('output', $value, $term_list);
    }

    public function disableRtuWarning(){
        $disable = I('disable',0,'intval');
        $cmd = $disable==1? 'disable_warning' : 'enable_warning';
        $rtws_id = I('rtws_id',0,'intval');
        $row = M('rtu_type_warning_set')->where('id='.$rtws_id)->field('slave_id,addr')->find();
        $addon = array(
            'slave_id' => $row['slave_id'],
            'addr' => $row['addr'],
            'act' => $disable
        );
        $this->dosome($cmd, sprintf('slave_id=%d&addr=%d', $row['slave_id'], $row['addr']), $term_list, '', 1, $addon);
    }

    //参数配置文件上报
    public function cfgFileUpload(){
        $cmd = 'upload_cfg';
        $sn = I('term_list');
        $filename = sprintf('%s_%s.cfg', $sn, date('YmdHis'));
        $insid = M('file_list')->add(array(
            'name' => $filename,
            'original_filename' => $filename,
            'filename' => $filename,
            'filetype' => 2,
            'filesize' => 0,
            'relative_path' => 'cfg',
            'ugid' => $this->getUgid(),
            'info' => L('CFG_FILE_UPLOAD_INFO'),
            'sn' => $sn,
            'creator' => $_SESSION[C('SESSION_NAME')]['name'],
        ));
        $value = 'fileid='.$insid;
        $this->dosome($cmd, $value, $term_list, L('VAR_CFG_FILENAME').': '.$filename);
    }

    // 查询RTU采集脚本
    public function rtuScriptGet(){
        $cmd = 'rtu_script_get';
        $value = '';
        $this->dosome($cmd, $value, $term_list);
    }

    // 设置RTU采集脚本
    public function rtuScriptSet(){
        $cmd = 'rtu_script_set';
        $value = addslashes($_REQUEST['rtu_script']);
        $this->dosome($cmd, $value, $term_list);
    }

    /*设置SIM卡切换的定时任务
    public function simChange() {
        $sn = I('term_list', '', 'string');
        $dates = I('dates', '', 'string');
        $time = explode(':', I('time', '', 'string'));
        $cron_file = './Lib/ORG/php_crontab/Applications/Crontab/cron_dir/'.$sn.'.crontab';
        $act = file_exists($cron_file) ? 'restart_task' : 'start_task';
        file_put_contents($cron_file, sprintf('%s %s * * %s root curl http://127.0.0.1:%d%s/index.php/Cgi/sim_change?sn=%s',
            $time[1], $time[0], $dates, $_SERVER['SERVER_PORT'], __ROOT__, $sn
        ));
        $ret = file_get_contents("http://127.0.0.1:5566/$act/".base64_encode($sn.'.crontab'));
        $this->ajaxReturn($ret, L('VAR_CMD_SEND_OK'), 0);
    }*/
}
?>