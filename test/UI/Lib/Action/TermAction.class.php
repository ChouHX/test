<?php
class TermAction extends CommonAction{
    //检查内存使用
    private function checkMemory(){
        $max_memory = 180 * 1024 * 1024;
        $usage = memory_get_usage(true);
        if ($usage >= $max_memory){
            $this->ajaxReturn('memory='.$usage, L('TOO_MUCH_DATA_INFO'), -1);
            exit;
        }
    }

    //获取项目options
    private function get_term_group_options($gid){
       $m = M('term_group');
        $rs = $m->where("id != 1 AND id IN(%s)",$this->getTgids())->order('name ASC')->select();
        $str = '';
        foreach ($rs as $k=>$row){
            $str .= sprintf('<option value="%s"%s>%s</option>', $row['id'], $gid==$row['id']?' selected':'', str_replace(' ','&nbsp;',$row['name']));
        }
        return $str;
    }

    //获取软件版本options
    private function get_swv_options(){
        $m = M('term');
        $str = '<option value="null">'.L('VAR_UNKNOWN').'</option>';
        $rs = $m->field('DISTINCT(sw_version)name')->where("sw_version IS NOT NULL AND sw_version !=''")->order('name ASC')->select();
        foreach ($rs as $k=>$row){
            $str .= sprintf('<option value="%s">%s</option>', $row['name'], $row['name']);
        }
        return $str;
    }

    //设备监控列表
    public function jklb(){
        if (C('SESSION_NAME') == 'oms_1.1_user') {
            //-----------同步用户组-----------
            $m = M('usr_group');
            $d = array(
                'id' => $this->userinfo['gid'],
                'name' => $this->userinfo['gname']
            );
            if ($m->where('id = %d', $d['id'])->count() == 0) {
                $m->add($d);
            } else {
                $m->save($d);
            }
            //-----------同步用户------------
            $m = M('usr');
            $d = array(
                'id' => $this->userinfo['id'],
                'name' => $this->userinfo['name'],
                'password' => $this->userinfo['password'],
                'alias' => $this->userinfo['alias'],
                'usr_type' => $this->userinfo['usr_type'],
                'is_enable' => $this->userinfo['is_enable'],
                'create_time' => $this->userinfo['create_time'],
                'never_expired' => $this->userinfo['never_expired'],
                'expired_time' => $this->userinfo['expired_time'],
                'head' => $this->userinfo['head'],
                'gid' => $this->userinfo['gid'],
                'email' => $this->userinfo['email'],
                'sim' => $this->userinfo['sim'],
                'info' => $this->userinfo['info']
            );
            if ($m->where('id = %d', $d['id'])->count() == 0) {
                $m->add($d);
            } else {
                $m->save($d);
            }
        }
        $this->assign('groups', M('term_group')->where("id != 1 AND id IN(%s) AND pid = 1",$this->getTgids())->order('name ASC')->select());
        $this->assign('web_path_1', array(L('VAR_MENU_TERM')));
        $this->display('jklb');
    }

    //获取设备分组，tree nodes
    public function getTermGroupTreeNodes($is_return = false, $has_root = 0, $show_num = 0){
        $current_gid = I('current_gid', -10, 'intval');
        if ($has_root == 0){
            $has_root = I('has_root',0,'intval');
        }
        $tgids = $this->getTgids();
        $tgids_arr = explode(',', $tgids);
        $rs = M('term_group')->where("%s AND id IN(%s)", array($has_root==0 ? 'id != 1':'1=1', $tgids))->field('id, pid AS pId, name')->order('pId ASC,name ASC')->select();
        foreach ($rs as $k => $row) {
            //没有Root节点且pid=1时，或者父节点没有权限时，将pId设置为全部分组(-10)
            if (($has_root == 0 && $row['pId'] == 1) || !in_array($row['pId'], $tgids_arr)){
                $row['pId'] = -10;
            }
            $row['iconOpen']  = __ROOT__.'/Tpl/Public/images/icons/term_group.png';
            $row['iconClose'] = __ROOT__.'/Tpl/Public/images/icons/term_group.png';
            $row['icon'] = __ROOT__.'/Tpl/Public/images/icons/term_group.png';
            $ret[$row['id']] = $row;
        }
        foreach ($ret as $k => $row) {
            //有子节点的，将其open属性设置为true
            if (isset($ret[$row['pId']])){
                $ret[$row['pId']]['open'] = true;
            }
            //是否selected
            if ($row['id'] == $current_gid) {
                $ret[$k]['selected'] = 1;
            }
        }
        $ret = isset($ret) ? array_values($ret) : array();

        if ($show_num){
            $ts = date('Y-m-d H:i:s', time()-C('TERM_OFFLINE_TIME'));
            $rs = M('')->query("SELECT a.group_id, b.is_online, b.last_time FROM term a, term_run_info b WHERE b.sn = a.sn AND a.group_id IN($tgids)");
            foreach ($rs as $k => $row) {
                if (!isset($onlines[$row['group_id']])){
                    $onlines[$row['group_id']] = array('total'=>0, 'online'=>0);
                }
                $onlines[$row['group_id']]['total'] += 1;
                if ($row['is_online'] == 1 && $row['last_time'] >= $ts){
                    $onlines[$row['group_id']]['online'] += 1;
                }
            }
            foreach ($ret as $k => $row) {
                if (!isset($onlines[$row['id']])){
                    $onlines[$row['id']] = array('total'=>0, 'online'=>0);
                }
                $ret[$k]['total'] = $onlines[$row['id']]['total'];
                $ret[$k]['online'] = $onlines[$row['id']]['online'];
            }
        }

        if ($is_return){
            return $ret;
        }else{
            $this->ajaxReturn($ret, 'ok', 0);
        }
    }

    //资源文件
    public function zywj(){
        $this->assign('web_path_1', array(L('VAR_MENU_RESOURCE_FILE')));
        $this->display('zywj');
    }

    //路由器分组列表
    public function fzlb(){
        $this->assign('web_path_1', array(L('TERM_GROUP_LIST')));
        $this->display('fzlb');
    }

    //监控列表-统计信息
    public function jklbStatisticalInfo() {
        session_write_close();
        $ugid = $_SESSION[C('SESSION_NAME')]['gid'];
        $uid  = $_SESSION[C('SESSION_NAME')]['id'];
        $today = date('Y-m-d 00:00:00');
        $q1 = sprintf("%s AND create_time >= '%s'",               ($uid == 1 ? '1=1' : "ugid = $ugid"), $today);
        $q2 = sprintf("%s AND term_run_info.first_login >= '%s'", ($uid == 1 ? '1=1' : 'a.group_id IN('.$this->getTgids().')'), $today);
        $today_new_device = M('')->query("SELECT COUNT(*)cc FROM term a INNER JOIN term_run_info ON term_run_info.sn = a.sn WHERE $q2");
        $ret = array(
            'info_box_taday_task' => M('term_task')->where($q1)->count() + M('timed_term_task')->where($q1)->count(),
            'info_box_today_new_device' => $today_new_device[0]['cc'],
        );
        $this->ajaxReturn($ret, 'ok', 0);
    }

    // 设备详情
    public function sbxq() {
        $sn = $_REQUEST['sn'];
        if (IS_AJAX) {
            $row = $this->assignTermRow($sn, true);
            $this->ajaxReturn($row, $this->map($sn, false, false), 0);
        }
        $this->assign('row', array('sn' => $sn));
        $this->assign('tsa', L('VAR_TASK_STATUS_ARR'));
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Term/jklb'), L('VAR_MENU_TERM')),
            L('TERM_DETAIL')." ($sn)"
        ));
        $this->display('sbxq');
    }

    // 设置围栏
    public function setFence() {
        if ($this->lang == 'zh-cn') {
            import('@.ORG.Gps');
            $gps = new Gps();
        }
        $sn = I('sn', '', 'string');
        $arr = json_decode($_REQUEST['datas'], true);
        $datas = array();
        foreach ($arr as $key => $row) {
            if ($row['ftype'] == 1) {
                if ($this->lang == 'zh-cn') {
                    $ret = $gps->gcj_decrypt($row['lat'], $row['lon']);
                    $row['lat'] = $ret['lat'];
                    $row['lon'] = $ret['lon'];
                }
                array_push($datas, array('sn' => $sn, 'ftype' => 1, 'fvalue' => sprintf('lon1=%s&lat1=%s&radius=%s', $row['lon'], $row['lat'], $row['radius'])));
            } elseif ($row['ftype'] == 2) {
                if ($this->lang == 'zh-cn') {
                    $ret1 = $gps->gcj_decrypt($row['lat1'], $row['lon1']);
                    $ret2 = $gps->gcj_decrypt($row['lat2'], $row['lon2']);
                    $row['lat1'] = $ret1['lat'];
                    $row['lon1'] = $ret1['lon'];
                    $row['lat2'] = $ret2['lat'];
                    $row['lon2'] = $ret2['lon'];
                }
                array_push($datas, array('sn' => $sn, 'ftype' => 2, 'fvalue' => sprintf('lon1=%s&lat1=%s&lon2=%s&lat2=%s', $row['lon1'], $row['lat1'], $row['lon2'], $row['lat2'])));
            } elseif ($row['ftype'] == 3) {
                $fvalue = array();
                foreach ($row['points'] as $k => $point) {
                    if ($this->lang == 'zh-cn') {
                        $ret = $gps->gcj_decrypt($point['lat'], $point['lng']);
                        $row['points'][$k]['lat'] = $ret['lat'];
                        $row['points'][$k]['lng'] = $ret['lon'];
                    }
                    array_push($fvalue, sprintf('lon%d=%s&lat%d=%s', $k+1, $row['points'][$k]['lng'], $k+1, $row['points'][$k]['lat']));
                }
                array_push($datas, array('sn' => $sn, 'ftype' => 3, 'fvalue' => implode('&', $fvalue)));
            }
        }
        $m = M('term_electronic_fence');
        $m->where("sn = '%s'", $sn)->delete();
        if (count($datas) != 0) {
            $m->addAll($datas);
        }
        $this->ajaxReturn('', L('FENCE_EDIT_SUCCESS'), 0);
    }

    // 进出围栏记录
    public function loadFenceRecordData(){
        $m  = M('term_electronic_fence_record');
        $sn = I('sn', '', 'string');
        $start = isset($_REQUEST['start']) && $_REQUEST['start'] != 0 ? date('Y-m-d 00:00:00', strtotime($_REQUEST['start'])) : date('Y-m-01 00:00:00');
        $end   = isset($_REQUEST['end']) ? date('Y-m-d 23:59:59', strtotime($_REQUEST['end'])) : date('Y-m-d 23:59:59');
        $q = "sn = '$sn' AND report_time >= '$start' AND report_time <= '$end'";
        $rs = $m->query("SELECT * FROM term_electronic_fence_record WHERE $q ORDER BY $sort $order LIMIT ".($page-1)*$rp.",$rp");
        $rs = $m->field('*')->where($q)->order($this->generate_order_str())->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $key => $row) {
            $this->lnglatFormat($rs[$key]['report_longitude'], $rs[$key]['report_latitude']);
            $rs[$key]['act'] = L($row['act'] == 0 ? 'ENTER_FENCE' : 'LEAVE_FENCE');
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //设备详情 - 任务列表
    public function loadTermTasks(){
        $sn = $_REQUEST['sn'];
        $m = M('term_task_detail');
        $q = "term_task_detail.sn = '$sn'";
        $s = I('tsid', '', 'string');
        if ($s != '') {
            $q .= " AND term_task_detail.status IN($s)";
        }
        $rs = $m->field("term_task_detail.id, cmd, is_enable, create_time, task_id, term_task_detail.sn, IFNULL(send_time,'')send_time, IFNULL(recv_time,'')recv_time,
            term_task_detail.last_time, error_info, (CASE term_task_detail.status WHEN 8 THEN 0 ELSE term_task_detail.status END)status, term_run_info.name,
            (SELECT SUM(download_size)/SUM(filesize) FROM download_report WHERE task_detail_id = term_task_detail.id)progress")
            ->join('INNER JOIN term_run_info ON term_run_info.sn = term_task_detail.sn')
            ->join('INNER JOIN term_task ON term_task.id = term_task_detail.task_id')
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $arr = L('VAR_TASK_STATUS_ARR');
        $tta = L('VAR_TASK_TYPE_ARR');
        foreach ($rs as $k=>$row){
            $rs[$k]['finish_time'] = format_time($row['send_time'], $row['recv_time']);
            $rs[$k]['status'] = $arr[$row['status']];
            $rs[$k]['status_o'] = $row['status'];
            $rs[$k]['progress'] = is_null($row['progress']) ? -1 :  $row['progress']*100;
            if (!empty($row['ext_info'])){
                $rs[$k]['ext_info'] = str_replace('<', '&lt;', $row['ext_info']);
                $rs[$k]['ext_info'] = str_replace('>', '&gt;', $row['ext_info']);
            }
            $rs[$k]['task_name'] = $tta[$row['cmd']];
        }
        $total = $m->join("INNER JOIN term_run_info ON term_run_info.sn = term_task_detail.sn")->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //终端列表数据
    public function loadTermData() {
        session_write_close();
        $m = M('term');
        $sns = I('sns','','trim');
        $q = sprintf('__POS__ AND ');
        if ($sns != '') {
            $q .= sprintf("term.sn IN('%s')", str_replace(',', "','", $sns));
        } else {
            $gid = I('gid', -10, 'intval');
            $q .= $this->generate_search_str();
            if ($gid != -10) {
                $q .= sprintf(' AND group_id = %d', $gid);
            } else {
                if ($_SESSION[C('SESSION_NAME')]['id'] != 1) {
                    $q .= sprintf(' AND group_id IN(%s)', $this->getTgids());
                }
            }
        }
        $q2 = str_replace('__POS__', '1 = 1', $q);
        $q  = str_replace('__POS__', 'B.sn = term.sn AND C.id = term.group_id', $q);
        $sql = "SELECT term.sn, term.ud_sn, term.alias, term.vsn, term.term_model, term.term_type, term.gateway_sn, term.group_id, term.sw_version,
            term.sim,  term.imsi,  term.iccid,  term.imei,  term.module_vendor,  term.module_type,
            term.sim2, term.imsi2, term.iccid2, term.imei2, term.module_vendor2, term.module_type2,
            term.create_time, term.wifi_ssid, term.host_sn, B.ip, B.ip_sim1, B.ip_sim2,
            B.port, B.sim_pos, B.port_sim1, B.port_sim2,
            B.is_online, B.protocol, B.first_login, B.login_time,
            B.last_time, B.last_time_sim1, B.last_time_sim2,
            B.net_mode, B.net_mode_sim1, B.net_mode_sim2,
            B.term_signal, B.term_signal_sim1, B.term_signal_sim2, B.rssi, B.rsrp, B.rsrq,
            B.flux, B.flux_sim1, B.flux_sim2, B.current_link,
            B.day_flux, B.day_flux_up, B.month_flux, B.status AS status_limit, B.last_7days_flux, B.operator, B.operator_sim1, B.operator_sim2,
            B.cpu_usage, B.mem_usage, B.storage_usage,
            C.name AS gname FROM term, term_run_info B, term_group C
            WHERE $q ORDER BY ".$this->generate_order_str('router_list')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
        // D.month_flux AS month_flux_onelink, D.api_query_time,
        // LEFT JOIN oem_onelink_flux D ON D.sn = term.sn
        $rs = $m->query($sql);

        $ts = time();
        if ($rs && count($rs) > 0) {
            $sns = array();
            foreach ($rs as $row) {
                array_push($sns, "'".$row['sn']."'");
            }
            $q_temp = sprintf("sn IN(%s)", implode(',', $sns));
            //wifi基站定位信息
            $rs2 = M('term_cell')->field('sn, lac, cellid, addr')->where($q_temp)->select();
            $rs3 = M('term_wifi_ap')->field('sn, ap_mac, addr AS addr2')->where($q_temp)->select();
            foreach ($rs2 as $k => $row) {
                $cells[$row['sn']] = $row;
            }
            foreach ($rs3 as $k => $row) {
                $wifis[$row['sn']] = $row;
            }
            unset($rs2);
            unset($rs3);

            // vpn数量
            $rs4 = M('term_virtual_channel')->where('is_online = 1 AND '.$q_temp)->field('sn, is_online, last_time')->select();
            foreach ($rs4 as $k => $row) {
                if (get_term_status_code($ts - strtotime($row['last_time']), 1, true) == '1') {
                    $vpn_num[$row['sn']] += 1;
                }
            }
            unset($rs4);

            // 电子围栏
            $fstatus_arr = $this->getFenceStatus($q_temp);
        }

        foreach ($rs as $k => $row) {
            $rs[$k]['diff'] = $ts - strtotime($row['last_time']);
            $rs[$k]['diff_sim1'] = $ts - strtotime($row['last_time_sim1']);
            $rs[$k]['diff_sim2'] = $ts - strtotime($row['last_time_sim2']);
            $rs[$k]['vpn_num'] = isset($vpn_num[$row['sn']]) ? $vpn_num[$row['sn']] : 0;
            $rs[$k]['sim_pos'] = $row['sim_pos'] == 0 ? sprintf('%s 1 + %s 2', L('CARD'), L('CARD')) : sprintf('%s %d', L('CARD'), $row['sim_pos']);

            //流量限制状态，当system_config.enable_electronic_fence = 1 && term_gps.fstatus = 1时，将status_limit设置为1
            if ($row['status_limit'] != 1 && $fstatus_arr[0] == '1' && $fstatus_arr[1][$row['sn']] == '1') {
                $rs[$k]['status_limit'] = 1;
            }

            $this->transformTermFields($rs[$k]);

            // cell,wifi
            if ($cel = $cells[$row['sn']]){
                if ($cel['lac'] && $cel['cellid']) {
                    $rs[$k]['lac_cellid'] = $cel['lac'].','.$cel['cellid'];
                }
            }
            $rs[$k]['ap_mac'] = $wifis[$row['sn']]['ap_mac'];
            $rs[$k]['addr'] = $cells[$row['sn']]['addr'] ? $cells[$row['sn']]['addr'] : $wifis[$row['sn']]['addr'];

            $rs[$k]['cpu_usage'] = $rs[$k]['cpu_usage'] . '%';
            $rs[$k]['mem_usage'] = $rs[$k]['mem_usage'] . '%';
            $rs[$k]['storage_usage'] = $rs[$k]['storage_usage'] . '%';
        }
        $rs_total = $m->query("SELECT COUNT(*)num FROM term WHERE $q2");
        $total = $rs_total[0]['num'];
        $ts = date('Y-m-d H:i:s',time()-C('TERM_OFFLINE_TIME'));
        $rs_online = $m->query("SELECT COUNT(*)num FROM term, term_run_info WHERE term_run_info.sn = term.sn AND $q2 AND is_online = 1 AND last_time >= '$ts'");
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'online' => $rs_online[0]['num'],
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //VPN列表数据
    public function loadVpnData(){
        $m = M('term_virtual_channel');
        $sn = I('sn', '', 'trim');
        $q = "sn = '$sn'";
        $rs = $m->field('*')->where($q)->order('last_time DESC')->select();
        $ts = time();
        foreach ($rs as $key => $row) {
            $s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online'], true);
            $rs[$key]['status'] = get_term_status_str($s, 0);
            $rs[$key]['online_time'] = $s == '1' ? format_time($row['login_time'], $row['last_time']) : '0';
            $rs[$key]['recv_flux'] = bitsize($row['recv_flux']);
            $rs[$key]['send_flux'] = bitsize($row['send_flux']);
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'online' => $online,
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    // onenet上报记录
    public function loadOneNetData(){
        $m = M('oem_onenet_report');
        $sn = I('sn', '', 'trim');
        $q = "sn = '$sn'";
        $rs = $m->query("SELECT * FROM oem_onenet_report WHERE $q ORDER BY ".$this->generate_order_str('oem_onenet_report')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp']);
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'online' => $online,
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //新增路由器
    public function termAdd(){
    	$m = M('term');
        $sn = I('sn');
        $gid = $_REQUEST['gid'];
    	$c = $m->where("sn='%s'",$sn)->count();
    	if ($c != 0){
            $this->ajaxReturn('', L('VAR_TERM_EXIST'), -1);
    	}else{
            $ret1 = M('term')->add(array(
                'sn' => $sn,
                'ud_sn' => $_REQUEST['ud_sn'],
                'sim' => $_REQUEST['sim'],
                'group_id' => $gid,
                'alias' => $_REQUEST['alias'],
                'term_model' => $_REQUEST['term_model'],
            ));
            $now = date('Y-m-d H:i:s');
            $ret2 = M('term_run_info')->add(array(
                'sn' => $sn,
                'name' => $sn,
                'first_login' => $now,
                'login_time' => $now,
                'last_time' => $now
            ));
            if ($ret1 && $ret2){
                $this->setgps('termAdd', $sn, I('lng'), I('lat'));
                $this->wlog('', 'term_add', '', 'sns='.$sn);
                $this->ajaxReturn('', L('VAR_TERM_ADD_OK'), 0);
            }else{
                $this->ajaxReturn('', L('VAR_TERM_ADD_ERROR'), -2);
            }
        }
    }

    //编辑路由器
    public function termEdit(){
    	$m = M('term');
        $sn = $_REQUEST['sn'];
        $d = array(
            'sn'       => $sn,
            'sim'      => $_REQUEST['sim'],
            'group_id' => $_REQUEST['gid'],
            'alias'    => $_REQUEST['alias'],
            'term_model' => $_REQUEST['term_model']
        );
        $ret = $m->save($d);
        if (I('add_gps',0,'intval') == 1){
            $this->setgps('termEdit', $sn, I('lng'), I('lat'));
        }
        $this->wlog('', 'term_edit', '', 'sns='.$sn);
        $this->ajaxReturn('', L('VAR_TERM_EDIT_OK'), 0);
    }

    //删除路由器
    public function termDel(){
        $sns = "'".str_replace(',', "','", I('sns'))."'";
    	if (M('term')->where("sn IN($sns)")->delete()){
            /*$client = stream_socket_client('udp://'.C('SERVER_IP').':'.C('SERVER_PORT'), $errno, $errstr, 5);
            fwrite($client, 'cmd=del_term&sn='.$sns);
            fclose($client);*/

            //删除RTU数据文件
            $sns = explode(',', I('sns'));
            foreach ($sns as $k => $sn) {
                $this->delRtuDataFiles($sn);
            }

            $this->wlog('', 'term_delete', '', 'sns='.$sns);
        }
        $this->ajaxReturn('', L('VAR_TERM_DEL_OK'), 0);
    }

    //修改路由器分组
    public function setGroup(){
        $sns = I('sns','','trim');
        $gid = I('gid',0,'intval');
        $m = M('term');
        if ($m->where("sn IN('".str_replace(',', "','", $sns)."')")->save(array('group_id'=>$gid))){
            $this->wlog('', 'term_set_group', 'group_name='.M('term_group')->where('id='.$gid)->getField('name'), 'sns='.$sns);
        }
        $this->ajaxReturn('', L('VAR_TERM_EDIT_OK'), 0);
    }

    //清理运行信息
    public function cleanRunInfo(){
        $sns = I('sns','','trim');
        if (!empty($sns)) {
            $sns = str_replace(',', "','", $sns);
            $q = "sn IN('$sns')";
            M('term_run_info')->where($q)->save(array(
                'name' => '',
                'ip' => '0.0.0.0',
                'port' => 0,
                'is_online' => 0,
                'protocol' => 10,
                'login_time' => '0000-00-00 00:00:00',
                'last_time' => '0000-00-00 00:00:00',
                'sim_pos' => 1,
                'net_mode' => 0,
                'term_signal' => 0,
                'flux' => 0,
                'month_flux' => 0,
                'day_flux' => 0,
                'flux_sim1' => 0,
                'flux_sim2' => 0,
                'flux_up' => 0,
                'month_flux_up' => 0,
                'day_flux_up' => 0,
                'flux_up_sim1' => 0,
                'flux_up_sim2' => 0,
                'last_login_flux' => 0,
                'last_7days_flux' => 0,
                'sim_change_flux' => 0,
                'cpu_usage' => 0,
                'mem_usage' => 0,
                'storage_usage' => 0,
            ));
            M('term_stat_info')->where($q)->delete();
            M('term_cell')->where($q)->delete();
            M('term_gps')->where($q)->delete();
            M('term_login_record')->where($q)->delete();
            M('term_param')->where($q)->delete();
            M('term_tlv')->where($q)->delete();
            M('term_wifi_ap')->where($q)->delete();
            M('term_net_mode_record')->where($q)->delete();
            $this->wlog('', 'clean_run_info', 'sns='.$sns);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    /* chart - 流量详情 - 按天
     * start, end 为unix时间戳
     */
    public function termChartDataFlux(){
        if (I('by_hour',0,'intval') == 1){
            $this->termChartDataFluxHour();
            exit;
        }
        if (I('by_month',0,'intval') == 1){
            $this->termChartDataFluxMonth();
            exit;
        }
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            $arr[$row['net_mode']][$row['report_day']] += $row['flux'];
        }
        if (!isset($arr)){
            $arr[0] = array(date('Ymd',$start)=>0);
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i+=24*3600){
                $ymd = date('Ymd',$i);
                $xAxis = date('m-d',$i);
                $data[$k]['data'][] = array($xAxis, isset($row[$ymd]) ? round($row[$ymd]/1024/1024,2) : 0);
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 流量详情 - 按小时
     * start, end 为unix时间戳
     */
    private function termChartDataFluxHour(){
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        if (date('Y-m-d',$start) == date('Y-m-d')){
            $end = time();
        }
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '*', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            for ($i=0; $i<24; $i++){
                $arr[$row['net_mode']]['f'.$i] += $row['f'.$i];
            }
        }
        /* $arr数据格式
        array(
            '3' => array('f0'=>1024, 'f1'=>2048, ..., 'f23'=>3072),
            '6' => array('f0'=>1024, 'f1'=>2048, ..., 'f23'=>3072),
        );*/
        if (!isset($arr)){
            $arr[0]['f0'] = 0;
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i+=3600){
                $fh = 'f'.date('G',$i);
                $xAxis = date('H:i',$i);
                $data[$k]['data'][] = array($xAxis, isset($row[$fh]) ? round($row[$fh]/1024,2) : 0);
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 流量详情 - 按月
     * start, end 为unix时间戳
     */
    private function termChartDataFluxMonth(){
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        if (date('Y',$start) == date('Y')){
            $end = time();
        }
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            $arr[$row['net_mode']][substr($row['report_day'],4,2)] += $row['flux'];
        }
        /* $arr数据格式
        array(
            '3' => array('01'=>1024, '02'=>2048, ..., '12'=>3072),
            '6' => array('01'=>1024, '02'=>2048, ..., '12'=>3072),
        );*/
        if (!isset($arr)){
            $arr[0] = array('01'=>0);
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i=strtotime('+1 month',$i)){
                $month = date('m',$i);
                $xAxis = date('M',$i);
                $data[$k]['data'][] = array($xAxis, isset($row[$month]) ? round($row[$month]/1024/1024,2) : 0);
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 信号详情 - 按天
     * start, end 为unix时间戳
     */
    public function termChartDataSignal(){
        if (I('by_hour',0,'intval') == 1){
            $this->termChartDataSignalHour();
            exit;
        }
        if (I('by_month',0,'intval') == 1){
            $this->termChartDataSignalMonth();
            exit;
        }
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            $arr[$row['net_mode']][$row['report_day']] = $row['hb_count'] == 0 ? 0 : intval($row['sum_signal']/$row['hb_count']);
        }
        if (!isset($arr)){
            $arr[0] = array(date('Ymd',$start)=>0);
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i+=24*3600){
                $ymd = date('Ymd',$i);
                $xAxis = date('m-d',$i);
                $data[$k]['data'][] = array($xAxis, isset($row[$ymd]) ? $row[$ymd] : 0);
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 信号详情 - 按小时
     * start, end 为unix时间戳
     */
    private function termChartDataSignalHour(){
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        if (date('Y-m-d',$start) == date('Y-m-d')){
            $end = time();
        }
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '*', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            for ($i=0; $i<24; $i++){
                $arr[$row['net_mode']]['s'.$i] = $row['c'.$i] == 0 ? 0 : $row['s'.$i] / $row['c'.$i];
            }
        }
        /* $arr数据格式
        array(
            '3' => array('s0'=>13, 's1'=>23, ..., 's23'=>30),
            '6' => array('s0'=>13, 's1'=>23, ..., 's23'=>30),
        );*/
        if (!isset($arr)){
            $arr[0] = array('s0'=>0);
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i+=3600){
                $sh = 's'.date('G',$i);
                $xAxis = date('H:i',$i);
                $data[$k]['data'][] = array($xAxis, isset($row[$sh]) ? intval($row[$sh]) : 0);
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 信号详情 - 按月
     * start, end 为unix时间戳
     */
    private function termChartDataSignalMonth(){
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        if (date('Y',$start) == date('Y')){
            $end = time();
        }
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $rs = $this->read_stat($sn, $start, $end, '', $sim == 0 ? '' : "sim_pos = $sim");
        foreach ($rs as $k=>$row){
            $month = substr($row['report_day'], 4, 2);
            $arr[$row['net_mode']][$month]['sum_signal'] += $row['sum_signal'];
            $arr[$row['net_mode']][$month]['hb_count'] += $row['hb_count'];
        }
        /* $arr数据格式
        array(
            '3' => array('01'=>array('sum_signal'=>100, 'hb_count'=>5), '02'=>array('sum_signal'=>120, 'hb_count'=>4), ...),
            '6' => array('01'=>array('sum_signal'=>100, 'hb_count'=>5), '02'=>array('sum_signal'=>120, 'hb_count'=>4), ...),
        );*/
        if (!isset($arr)){
            $arr[0] = array('01'=>array('sum_signal'=>0, 'hb_count'=>1));
        }
        $nm_text = C('NET_MODE');
        foreach ($arr as $k => $row) {
            $data[$k] = array('name'=>$nm_text[$k], 'data'=>array());
            for ($i=$start; $i<=$end; $i=strtotime('+1 month',$i)){
                $month = date('m',$i);
                $xAxis = date('M',$i);
                $data[$k]['data'][] = array(
                    $xAxis,
                    (isset($row[$month]) && $row[$month]['hb_count'] != 0) ? intval($row[$month]['sum_signal']/$row[$month]['hb_count']) : 0
                );
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /* chart - 在线率详情 - 按天
     * start, end 为unix时间戳
     */
    public function termChartDataOnline($start = 0, $end = 0, $sn = '', $is_return = 0){
        if ($start == 0) {
            $start = I('start',0,'intval');
        }
        if ($end == 0) {
            $end = I('end',0,'intval');
        }
        $by_hour = I('by_hour',0,'intval');
        $by_month = I('by_month',0,'intval');
        if ($by_hour == 1 && date('Y-m-d',$start) == date('Y-m-d')){
            $end = time();
        }
        if ($by_month == 1 && date('Y',$start) == date('Y')){
            $end = time();
        }
        if ($sn == '') {
            $sn = I('sn','','string');
        }
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $m = M('term_run_info');

        //term_login_record表上线记录
        $start_ymdhis = date('Y-m-d 00:00:00', $start);
        $end_ymdhis = date('Y-m-d 23:59:59', $end);
        $rs = $m->query(sprintf("SELECT login_time, logout_time FROM term_login_record WHERE sn = '%s'
            AND login_time <> '0000-00-00 00:00:00' AND logout_time <> '0000-00-00 00:00:00'
            AND NOT(login_time > '%s' OR logout_time < '%s') AND %s", $sn, $end_ymdhis, $start_ymdhis, $sim == 0 ? '1=1' : "sim_pos = $sim"));

        //未退出的设备，加上term表的在线时间
        $row = $m->where("sn = '$sn' AND is_online = 1 AND login_time IS NOT NULL AND login_time <> '0000-00-00 00:00:00'")->field('login_time,last_time')->find();
        if ($row){
            array_push($rs, array('login_time'=>$row['login_time'], 'logout_time'=>$row['last_time']));
        }

        $curr_ts = time();
        $curr_ymdh = date('Y-m-d H', $curr_ts);
        $curr_ym = date('Y-m', $curr_ts);
        $curr_ymd = date('Y-m-d', $curr_ts);

        for ($i=$start; $i<=$end; ){
            $seconds = 0;
            if ($by_hour == 1){
                $interval = 3600;
                $format = 'H:00';
                $startTime = date('Y-m-d H:00:00',$i);
                $endTime = date('Y-m-d H:59:59', $i);
                if (substr($endTime, 0, 13) == $curr_ymdh){
                    $divisor = $curr_ts - strtotime($curr_ymdh.':00:00');
                }
            } elseif ($by_month == 1){
                $days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m',$i), date('Y',$i));
                $format = 'M';
                $interval = $days_in_month * 24 * 3600;
                $startTime = date('Y-m-01 00:00:00',$i);
                $endTime = date(sprintf('Y-m-%d 23:59:59',$days_in_month>10?$days_in_month:('0'.$days_in_month)), $i);
                if (substr($endTime, 0, 7) == $curr_ym){
                    $divisor = $curr_ts - strtotime($curr_ym.'-01 00:00:00');
                }
            } else {
                $interval = 24 * 3600;
                $format = 'm-d';
                $startTime = date('Y-m-d 00:00:00',$i);
                $endTime = date('Y-m-d 23:59:59', $i);
                if (substr($endTime, 0, 10) == $curr_ymd){
                    $divisor = $curr_ts - strtotime($curr_ymd.' 00:00:00');
                }
            }
            foreach ($rs as $k=>$row){
                if ($row['login_time'] <= $startTime && $row['logout_time'] >= $endTime){
                    $row['login_time'] = $startTime;
                    $row['logout_time'] = $endTime;
                } elseif ($row['login_time'] >= $startTime && $row['logout_time'] <= $endTime){
                    ;
                } elseif ($row['login_time'] <= $startTime && $row['logout_time'] >= $startTime){
                    $row['login_time'] = $startTime;
                }elseif ($row['login_time'] <= $endTime && $row['logout_time'] >= $endTime){
                    $row['logout_time'] = $endTime;
                }else{
                    continue;
                }
                $seconds += strtotime($row['logout_time']) - strtotime($row['login_time']);
            }
            if ($seconds < 0 ){
                $seconds = 0;
            }
            if ($seconds > $interval){
                $seconds = $interval;
            }
            $percent = sprintf("%.1f",$seconds/($divisor ? $divisor : $interval)*100);
            $data[] = array(date($format,$i), floatval($percent));

            $i += $interval;
            unset($divisor);
        }
        if ($is_return == 0) {
            echo json_encode(isset($data) ? array_values($data) : array());
        }else{
            return isset($data) ? array_values($data) : array();
        }
        
    }

    /* chart - CPU使用率/内存使用率/存储使用率
     * start, end 为unix时间戳
     */
    public function termChartDataUsageRate(){
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $by_hour = I('by_hour',0,'intval');
        $by_month = I('by_month',0,'intval');
        if ($by_hour == 1 && date('Y-m-d',$start) == date('Y-m-d')){
            $end = time();
        }
        if ($by_month == 1 && date('Y',$start) == date('Y')){
            $end = time();
        }
        $m = M('term_stat_info');
        $sn = I('sn','','string');
        $sim = I('sim', 0, 'intval'); //sim=0表示统计全部卡
        $ymd_start = date('Ymd', I('start'));
        $ymd_end = date('Ymd', I('end'));
        $q = "sn = '$sn' AND report_day >= $ymd_start AND report_day <= $ymd_end";
        if ($sim != 0) {
          $q .= " AND sim_pos = $sim";
        }
        $rs = $m->query("SELECT * FROM term_stat_info WHERE $q");
        $type = I('type', 'cpu', 'string');
        $usage = $type.'_usage';
        $sum_usage = 'sum_'.$type.'_usage';
        if ($by_hour == 1) {
            foreach ($rs as $k => $row) {
                for ($i=0; $i<24; $i++) {
                    $arr['c'.$i] += $row['c'.$i];
                    $arr1[$usage.$i] += $row[$usage.$i];
                }
            }
            for ($i=$start; $i<=$end; $i+=3600) {
                $sh = 'c'.date('G',$i);
                $sh1 = $usage.date('G',$i);
                $xAxis = date('H:i',$i);
                $data[] = array($xAxis, $arr[$sh] == 0 ? 0 : intval($arr1[$sh1]/$arr[$sh]));
            }
        } elseif ($by_month == 1) {
            foreach ($rs as $row) {
                $month = substr($row['report_day'], 0, 6);
                $arr[$month][$sum_usage] += $row[$sum_usage];
                $arr[$month]['hb_count'] += $row['hb_count'];
            }
            for ($i=$start; $i<=$end; $i=strtotime('+1 month',$i)) {
                $ymd = date('Ym', $i);
                $xAxis = date('M', $i);
                $data[] = array($xAxis, $arr[$ymd]['hb_count'] == 0 ? 0 : intval($arr[$ymd][$sum_usage]/$arr[$ymd]['hb_count']));
            }
        } else {
            foreach ($rs as $row) {
                $arr[$row['report_day']][$sum_usage] += $row[$sum_usage];
                $arr[$row['report_day']]['hb_count'] += $row['hb_count'];
            }
            for ($i=$start; $i<=$end; $i+=24*3600) {
                $ymd = date('Ymd',$i);
                $xAxis = date('m-d',$i);
                $data[] = array($xAxis, $arr[$ymd]['hb_count'] == 0 ? 0 : intval($arr[$ymd][$sum_usage]/$arr[$ymd]['hb_count']));
            }
        }
        echo json_encode(isset($data) ? array_values($data) : array());
    }

    /**
     * 获取一台设备网络切换时长
     * @param  string $sn 设备sn
     * @return array
     */
    private function getNetdisTime($sn){
        $m = M('term');

        // term_net_mode_record表记录
        $rs = $m->query("SELECT report_time, old_value, new_value FROM term_net_mode_record WHERE sn = '$sn'");

        // 1. 如果rs为空，说明没有网络切换，分布律为 term.net_mode 100%
        // 2. 如果rs不为空，在rs首部插入一条数据，(tid = id, report_time = first_login, old_value = 0, new_value = term.net_mode)
        //                  在rs尾部插入一条数据，(tid = id, report_time = last_time,  old_value = 0, new_value = term.net_mode)
        // 3. 将rs遍历生成新的数组
        $term = $m->join("INNER JOIN term_run_info ON term_run_info.sn = term.sn")->where("term.sn = '$sn'")->field('first_login, last_time, net_mode')->find();
        if (empty($rs[0]['report_time'])){
            return $term['net_mode'];
        }
        $len = count($rs);
        foreach ($rs as $k=>$row) {
            $a = $k==0 ? array('report_time'=>$term['first_login'], 'old_value'=>0, 'new_value'=>$term['net_mode']) : $rs[$k-1];
            $b = $row;
            $data[] = array(
                'net_mode' => $b['old_value'],
                'login_time' => $a['report_time'],
                'logout_time' => $b['report_time']
            );
        }
        $data[] = array(
            'net_mode' => $term['net_mode'],
            'login_time' => $rs[$len-1]['report_time'],
            'logout_time' => $term['last_time']
        );
        unset($rs);
        return $data;
    }

    /* chart - 网络分布详情
     * start, end 为unix时间戳
     */
    public function termChartDataNetmode(){
        $start = date('Y-m-d 00:00:00', I('start',0,'intval'));
        $end = date('Y-m-d 23:59:59', I('end',0,'intval'));
        $sn = I('sn','','string');
        $m = M('term_net_mode_record');

        $rs = $this->getNetdisTime($sn);
        $total = 0;
        foreach ($rs as $k=>$row){
            if ($row['logout_time'] <= $start){
                continue;
            }elseif ($row['login_time'] <= $start && $row['logout_time'] > $start){
                $row['login_time'] = $start;
            }elseif ($row['login_time'] > $start && $row['logout_time'] < $end){
                ;
            }elseif ($row['logout_time'] >= $end && $row['login_time'] < $end){
                $row['logout_time'] = $end;
            }elseif ($row['login_time'] >= $end){
                continue;
            }
            $seconds = strtotime($row['logout_time']) - strtotime($row['login_time']);
            $total += $seconds;
            $arr[$row['net_mode']] += $seconds;
        }

        $net_mode = C('NET_MODE');
        if (!isset($arr)){
            $nm = M('term_run_info')->where("sn = '$sn'")->getField('net_mode');
            $arr[$nm] = $total = 1;
        }

        foreach ($arr as $k => $v) {
            if ($k == 100){
                $netText = '';
            }else{
                $ret = $this->getNetmodeColor($k);
                $netText = ' ('.$ret['nm'].')';
            }
            $tmp = floatval(sprintf("%.1f", $v / $total * 100));
            $data[] = array($net_mode[$k].$netText, $tmp>100 ? 100 : $tmp);
        }
        echo json_encode(isset($data) ? $data : array());
    }

    //获取模态框html
    public function getModalHtml(){
        $gid = trim($_REQUEST['gid']);
        $tpl_id = trim($_REQUEST['tpl_id']);
        $tpl = '';
        $tm = C('TERM_MODEL');
        if ($tpl_id != ''){
            $tpl = $this->buildHtml($tpl_id, './Runtime/Temp/', './Tpl/Term/modal/'.$tpl_id.'.html');
            if ($tpl_id == 'term_add' || $tpl_id == 'term_edit_group') {
                $nodes = $this->getTermGroupTreeNodes(true);
                if ($gid == -10){
                    $gid = $nodes[0]['id'];
                }
                if (isset($_REQUEST['sns'])){
                    $tpl = sprintf($tpl, L('VAR_BATCH_EDIT_GROUP'), $_REQUEST['sns'], json_encode($nodes), $gid);
                }else{
                    $gps = $this->map();
                    $tpl = sprintf($tpl, L('VAR_TERM_ADD'), $gps['lat'], $gps['lng'], $gps['lat'], $gps['lng'], json_encode($nodes), $gid);
                }
            } elseif ($tpl_id == 'term_edit') {
                $sn = trim($_REQUEST['sn']);
                $row = M('term')
                    ->field('term.sn, term.ud_sn, term.sim, term.alias, term.group_id, term.term_model, term_group.name AS gname')
                    ->join('LEFT JOIN term_group ON term_group.id = term.group_id')
                    ->where("term.sn = '$sn'")
                    ->find();
                if (!isset($tm[$row['term_model']])){
                    $row['term_model'] = '';
                }
                $tpl = sprintf($tpl, L('VAR_TERM_EDIT'), $row['sn'], $row['sn'], $row['ud_sn'], $row['sim'], $row['alias'], $row['term_model'], json_encode($this->getTermGroupTreeNodes(true)), $row['group_id']);
            } elseif ($tpl_id == 'terms_set_pos') {
                $gps = $this->map();
                $tpl = sprintf($tpl, L('MULTI_SET_POS'), $_REQUEST['sns'], $gps['lat'], $gps['lng']);
            } elseif ($tpl_id == 'task_params') {
                $tpl = sprintf($tpl, $this->get_swv_options());
            } elseif ($tpl_id == 'term_select_group') {
                $tpl = sprintf($tpl, json_encode($this->getTermGroupTreeNodes(true, 0, 1)));
            } elseif ($tpl_id == 'params_interface_set') {
                // Link status:
                //              wan_status根据term_interface.wan_status确定；
                //              lan_status根据term_interface.lan_status确定(1个数值,每一个位决定0或1)
                // Enable Status根据term_param中的参数确定(lan0_onoff ~ lan4_onoff)
                $sn = trim($_REQUEST['sn']);
                $ret = $this->getLanStatus($sn);
                $term_model = M('term')->where("sn = '%s'", $sn)->getField('term_model');
                if ($term_model) {
                    $term_model = substr($this->formatTermModel($term_model), 0, 3);
                } else {
                    $term_model = 'ROUTER';
                }
                $tpl = sprintf($tpl, $ret['wan_connect_status'], json_encode($ret['lan_connect_status']), json_encode($ret['wan_lan_enables']), $term_model);
            } elseif ($tpl_id == 'params_relay_control') {
                $sn = trim($_REQUEST['sn']);
                $tpl = sprintf($tpl, $sn?$sn:'');
            }
        }
        echo $tpl;
    }

    //通过ip获取经纬度
    public function getLnglatByIp(){
        $sn = I('sn','','string');
        if ($sn != ''){
            $ret = $this->map($sn);
        }else{
            $ret = get_latlng_by_network(get_client_ip());
        }
        $this->ajaxReturn($ret,'ok',0);
    }

    /**
     * 加载路由器参数 param
     * @return [type] [description]
     */
    public function loadTermParams(){
        $m  = M('term_param');
        $sn = $_REQUEST['sn'];
        $params = $m->where("sn = '%s' AND param <> 'unknown' AND param <> '' AND param IS NOT NULL", $sn)->limit(1)->getField('param');

        if (!$params){
            $d = array();
        }else{
            $params = explode('&', $params);
            foreach ($params as $k => $v) {
                $i = strpos($v, '=');
                if ($i === false || strlen($v) == $i+1) continue;
                $p_name = substr($v,0,$i);
                if (strpos($p_name, '/') !== false) continue;
                $d[$p_name] = preg_replace("/^\"|\"$/", "", substr($v,$i+1));
            }
            if (isset($d['cellType']) || isset($d['cell_mode'])){
                $ct = intval(str_replace('"','',$d['cellType']));
                $cm = intval(str_replace('"','',$d['cell_mode']));
                if ($cm == 4){
                    $d['net_mode'] = '4';
                }elseif ($ct == 0){
                    $d['net_mode'] = '0';
                }elseif($ct == 1){
                    $d['net_mode'] = '1';
                }elseif($ct == 2 && $cm == 1){
                    $d['net_mode'] = '2';
                }elseif($ct == 3 && $cm == 1){
                    $d['net_mode'] = '3';
                }
            }
            if (isset($d['m2m_product_id'])){
                //GPS页面的<心跳包内容>与m2m配置中的<用户设置sn>是同一个参数
                $d['m2m_product_id_gps'] = $d['m2m_product_id'];
            }
            if (isset($d['dtu_wakeup_type'])){
                $bin = decbin($d['dtu_wakeup_type']);
                $d['dtu_wakeup_type_bit0'] = substr($bin, -1, 1);
                $d['dtu_wakeup_type_bit1'] = substr($bin, -2, 1);
                $d['dtu_wakeup_type_bit2'] = substr($bin, -3, 1);
            }
        }
        if (!isset($d['router_2'])) {
         // $d['router_2'] = '0<_std<_std<0<0<sd<n<0<0<0';
            $d['router_2'] = '0<_std<_std<0<0<dd<i<0<0<0';
        }
        if (!isset($d['atoip_addr'])) {
            $d['atoip_addr'] = '0.0.0.0';
        }

        if ($sn != '0') {
            $ud_sn = M('term')->where("sn = '$sn'")->getField('ud_sn');
            if ($ud_sn){
                $d['m2m_product_id'] = $ud_sn;
                $d['m2m_product_id_gps'] = $ud_sn;
            }
            if (empty($d['http_username'])) {
                // $d['http_username'] = 'admin';
            }
            // LAN起始IP
            if (isset($d['dhcp_start']) && isset($d['dhcp_num'])){
                if (empty($d['dhcpd_startip']) || empty($d['dhcpd_endip'])){
                    $ips = get_startip_endip($d['lan_ipaddr'], $d['dhcp_start'], $d['dhcp_num']);
                    $d['dhcpd_startip'] = $ips[0];
                    $d['dhcpd_endip'] = $ips[1];
                }
            }
            //租约，为0时为默认(1440)
            if (isset($d['dhcp_lease']) && $d['dhcp_lease'] == 0){
                $d['dhcp_lease'] = 1440;
            }
            if (isset($d['dtu_heart_packet'])){
                $d['dtu_heart_packet'] = addSpaceToHex($d['dtu_heart_packet']);
            }
            if (isset($d['dtu_heart_ack_packet'])){
                $d['dtu_heart_ack_packet'] = addSpaceToHex($d['dtu_heart_ack_packet']);
            }

            //dhcpd_startip, dhcpd_endip
            if (!isset($d['dhcpd_startip']) && isset($d['lan_ipaddr']) && isset($d['dhcp_start']) && isset($d['dhcp_num'])) {
                $x = explode('.', $d['lan_ipaddr']);
                $x = implode('.', array_slice($x, 0, 3)).'.';
                $d['dhcpd_startip'] = $x . $d['dhcp_start'];
                $d['dhcpd_endip'] = $x . (($d['dhcp_start'] * 1) + ($d['dhcp_num'] * 1) - 1);
            }

            //wan_proto的值受ppp_demand影响
            if (isset($d['wan_proto']) && isset($d['is_ecm_dial']) && $d['wan_proto'] == 'dhcp' && $d['is_ecm_dial'] == '1'){
                $d['wan_proto'] = 'ppp3g';
            }

            //动态域名
            if (!empty($d['ddnsx_ip'])) {
                if ($d['ddnsx_ip'] == 'wan') {
                    $d['ddnsx_ip'] = '';
                    $d['ddnsx_ip_type'] = 'wan';
                } else {
                    $d['ddnsx_ip_type'] = 'custom';
                }
            }
            for ($i=0; $i<2; $i++) {
                if (!empty($d['ddnsx'.$i])) {
                    $ddns_tmp = explode('<', $d['ddnsx'.$i]);
                    $usr_pwd = !empty($ddns_tmp[1]) ? explode(':', $ddns_tmp[1]) : array('', '');
                    $d['service'.$i]  = $ddns_tmp[0];
                    $d['user'.$i]     = $usr_pwd[0];
                    $d['pass'.$i]     = $usr_pwd[1];
                    $d['host'.$i]     = $ddns_tmp[2];
                    $d['wild'.$i]     = $ddns_tmp[3];
                    $d['mx'.$i]       = $ddns_tmp[4];
                    $d['bmx'.$i]      = $ddns_tmp[5];
                }
            }
            // 动态域名,强制下次更新
            $d['force0'] = $d['force1'] = 0;

            // 两个openvpn的经由internet网络参数，vpn_client1_eas，vpn_client2_eas是合并到一个参数内的 vpn_client_eas
            if (isset($d['vpn_client_eas'])) {
                if (strpos($d['vpn_client_eas'], '1') !== false) $d['vpn_client1_eas'] = 1;
                if (strpos($d['vpn_client_eas'], '2') !== false) $d['vpn_client2_eas'] = 1;
            }

            // 访问设置-本地访问，远程访问参数，无线访问
            $d['http_local'] = (($d['https_enable'] != 0) ? 2 : 0) | (($d['http_enable'] != 0) ? 1 : 0);
            $d['http_remote'] = ($d['remote_management'] == 1) ? (($d['remote_mgt_https'] == 1) ? 2 : 1) : 0;
            $d['http_wireless'] = $d['web_wl_filter'] == 0 ? 1 : 0;

            if (C('OEM_VERSION') == 'rx-m2m' && isset($d['portforward'])) {
                $tmp = explode('>', $d['portforward']);
                foreach ($tmp as $tk => $tv) {
                    $tmp2 = explode('<', $tv);
                    if ($tmp2[2] == '') {
                        $tmp2[2] = 0;
                    } elseif ($tmp2[2] == 'usb0') {
                        $tmp2[2] = 1;
                    } else {
                        $tmp2[2] = 2;
                    }
                    $tmp[$tk] = implode('<', $tmp2);
                }
                $d['portforward'] = implode('>', $tmp);
            }
        }
        if (isset($_REQUEST['sessid'])){
            $tm = M('term')->where("sn='$sn'")->getField('term_model');
            $tm = $this->formatTermModel($tm);
            $this->ajaxReturn(array(
                'params'  => $d,
                'model' => $this->getParamsType($tm)
            ),'ok',0);
        }
        // 全部参数解决方案
        $params_type = I('params_type', '', 'string');
        if ($params_type != '') {
            // 删除过期的init_***.php文件
            $dir_path = sprintf('./params_def/%s/', $params_type);
            $dd = opendir($dir_path);
            while ($f = readdir($dd)){
                if ($f != '.' && $f != '..'){
                    if (is_file($dir_path.$f) && strpos($f, 'init_') !== false) {
                        if (time() - filemtime($dir_path.$f) > 23*3600) {
                            unlink($dir_path.$f);
                        }
                    }
                }
            }
            closedir($dd);
            $d['term_model'] = $sn == '0' ? '' : M('term')->where("sn = '%s'", $sn)->getField('term_model');
            if ($d['term_model']) {
                $d['term_model'] = $this->formatTermModel($d['term_model']);
                $d['r_type'] = $d['term_model'];
            }
            $d['bi'] = asp_bootinfo($d['router_2'], $d['r_type'], $d['mcu']); // 生成bi参数，否则页面会报错
            if (!isset($d['sshd_eas'])) {
                $d['sshd_eas'] = 0;
                $d['telnetd_port'] = 2323;
            }
            $dir = sprintf('./params_def/%s/', $params_type);
            $index_page = file_get_contents($dir.'index_page_def');
            $init_str = sprintf('<?PHP
$lang = \'%s\';
echo \'var nvram = %s;\';
?>', $this->lang, json_encode($d));
            file_put_contents($dir.'init_'.session_id().'.php', $init_str);
            copy(sprintf('./Runtime/%s.js', $this->lang), sprintf('./params_def/%s/lang/%s.js', $params_type, $this->lang));
            $this->ajaxReturn(sprintf('%s/params_def/%s/%s', __ROOT__, $params_type, $index_page), 'ok', 0);
        }
        echo json_encode($d);
    }

    //全景图
    public function qjt(){
        $this->assign('web_path_1', array(L('PANORAMA')));
        $this->display(C('SHOW_MARKER_CLUSTER_DEMO') ? 'qjt_gd' : 'qjt');
    }

    /**
     * 获取所有设备的最新位置
     * 存储在term_gps表，每台设备一条数据
     * @return [json, array]
     */
    public function loadLatestPos(){
        $m = M('term_gps');
        $l = strtolower($_COOKIE['think_language']);
        $gid = I('gid',0,'intval');
        if ($gid != 0 && $gid != -10){
            $q = "c.group_id = $gid";
        }else{
            $q = '1=1';
        }
        $tgids = $this->getTgids();
        $rs = $m->query("SELECT a.sn, a.longitude, a.latitude, UNIX_TIMESTAMP(a.report_time)report_time, IFNULL(b.alias, '')alias FROM term_gps a
                            INNER JOIN term b ON b.sn = a.sn
                            WHERE $q AND b.group_id IN($tgids)");
        foreach ($rs as $k=>$row){
            $lng = $row['longitude'];
            $lat = $row['latitude'];
            $this->lnglatFormat($lng, $lat);
            //字段顺序 [lng, lat, ts, sn, gdt, alias]
            $d[$k] = array(floatval($lng), floatval($lat), $row['report_time'], $row['sn'], 0, $row['alias']);
        }
        if ($l == 'zh-cn' && isset($d)){
            import('@.ORG.Gps');
            $gps = new Gps();
            foreach ($d as $k=>$row){
                if ($row[0] != ''){
                    $ret = $gps->gcj_encrypt($row[1], $row[0]);
                    $d[$k][0] = floatval(substr($ret['lon'],0,10));
                    $d[$k][1] = floatval(substr($ret['lat'],0,10));
                }
            }
        }
        echo json_encode(isset($d)?$d:array());
    }

    // 生成静态gps文件
    public function generateLatestPos() {
        session_write_close();
        $m = M('term_gps');
        $l = strtolower($_COOKIE['think_language']);
        $gid = I('gid',0,'intval');
        if ($gid != 0 && $gid != -10){
            $q = "c.group_id = $gid";
        }else{
            $q = '1=1';
        }
        $tgids = $this->getTgids();
        $rs = $m->query("SELECT a.sn, a.longitude, a.latitude, UNIX_TIMESTAMP(a.report_time)report_time, IFNULL(b.alias, '')alias FROM term_gps a
                            INNER JOIN term b ON b.sn = a.sn
                            WHERE $q AND b.group_id IN($tgids)");
        $decimal_len = 5;
        foreach ($rs as $k=>$row){
            $lng = substr($row['longitude'],0,10);
            $lat = substr($row['latitude'],0,10);
            $this->lnglatFormat($lng, $lat);
            //字段顺序 [lng, lat, ts, sn, gdt, alias]
            // $d[$k] = array(floatval($lng), floatval($lat), $row['report_time'], $row['sn'], 0, $row['alias']);
            // $d[$k] = array(round(floatval($lng), $decimal_len), round(floatval($lat), $decimal_len), $row['sn']);
            $d[$k] = array(round(floatval($lng), $decimal_len), round(floatval($lat), $decimal_len));
        }
        if ($l == 'zh-cn' && isset($d)){
            import('@.ORG.Gps');
            $gps = new Gps();
            foreach ($d as $k=>$row){
                if ($row[0] != ''){
                    $ret = $gps->gcj_encrypt($row[1], $row[0]);
                    $d[$k][0] = round(floatval(substr($ret['lon'],0,10)), $decimal_len);
                    $d[$k][1] = round(floatval(substr($ret['lat'],0,10)), $decimal_len);
                }
                $d[$k] = implode('/', $d[$k]);
            }
        }
        if (!isset($d)) {
            $d = array();
        }
        // echo json_encode(isset($d)?$d:array());
        $str = implode(',', $d);
        file_put_contents('gps_data.js', "var gps_data = '".$str."';");
        echo 'Generage gps file ok...';
    }

    //获取一台设备，一天内一段时间的gps数据
    public function loadGpsData() {
        ini_set('memory_limit', '200M');
        session_write_close();
        $sn = I('sn','','string');
        $date = date('Y-m-d', I('date'));
        $start = $date .' '. I('start','00:00:00','string');
        $end = $date .' '. I('end','23:59:59','string');
        // die("sn=$sn, start=$start, end=$end");
        $l = strtolower($_COOKIE['think_language']);

        $df = strtoupper(C('GPS_DATA_FROM'));
        if ($df == 'DB'){
            $m = M('term_gps');
            $rs = $m->where("sn = '$sn' AND report_time BETWEEN '$start' AND '$end'")->order('report_time ASC')->select();
            foreach ($rs as $k=>$row){
                $this->checkMemory();
                $lng = $row['longitude'];
                $lat = $row['latitude'];
                $this->lnglatFormat($lng, $lat);
                $data[] = array(
                    'lng' => $lng,
                    'lat' => $lat,
                    'ts'  => $row['report_time'],
                );
            }
        }elseif ($df == 'FILE'){
            $null_str = str_repeat('0',16);
            $start = strtotime($start);
            $end = strtotime($end);
            $path = $this->getGpsFilePath(str_replace('-', '', $date));
            if ($sn && file_exists($path)){
                $fp = fopen($path, "rb");
                fseek($fp, 128);
                while (1){
                    $str = fread($fp,32);
                    if (substr(bin2hex($str),0,16) == $null_str){
                        break;
                    }
                    $tmpsn = substr($str,0,strpos($str, "\0"));
                    // dump($tmpsn);
                    if ($tmpsn == $sn){
                        $firstBlockPos = hexdec(htond(bin2hex(fread($fp,8))));
                        $lastBlockPos  = hexdec(htond(bin2hex(fread($fp,8))));
                        // dump("$firstBlockPos, $lastBlockPos");

                        //开始读取一个数据块(2000+8)
                        fseek($fp, $firstBlockPos, SEEK_SET);
                        $readNum = 0;
                        while (1){
                            $this->checkMemory();
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
                            if ($readNum == 100){
                                // echo 'ftell='.ftell($fp).'<br>';
                                $nextBlockPos = hexdec(htond(bin2hex(fread($fp,8))));
                                // dump($nextBlockPos);
                                if ($nextBlockPos == 0){
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
        if (isset($data)){
            if ($l == 'zh-cn'){
                import('@.ORG.Gps');
                $gps = new Gps();
                foreach ($data as $k=>$row){
                    $this->checkMemory();
                    $ret = $gps->gcj_encrypt($data[$k]['lat'], $data[$k]['lng']);
                    $data[$k]['lng'] = $ret['lon'];
                    $data[$k]['lat'] = $ret['lat'];
                }
            }
            $this->ajaxReturn($data, 'memory='.memory_get_usage(true), 0);
        }else{
            $this->ajaxReturn(array(), L('VAR_NO_GPS_DATA_RESET_TIME'), -1);
        }
    }

    /**
     * 设置gps
     * 查找term_gps表是否已有数据，有[更新]，无[插入]
     * @param  string  $from [description]
     * @param  string  $sn   [description]
     * @param  integer $lng  [description]
     * @param  integer $lat  [description]
     * @return [type]        [description]
     */
    public function setgps($from = 'out', $sn = '', $lng = 0, $lat = 0){
        $m = M('term_gps');
        $l = strtolower($_COOKIE['think_language']);
        if ($from == 'out'){
            $sn = I('sn');
            $ret = explode(',', I('markers'));
            $lng = $ret[0];
            $lat = $ret[1];
        }
        if ($l == 'zh-cn'){
            //google转换为gps坐标
            import('@.ORG.Gps');
            $gps = new Gps();
            $ret = $gps->gcj_decrypt($lat, $lng);
            $lng = $ret['lon'];
            $lat = $ret['lat'];
        }
        $d = array(
            'sn' => $sn,
            'longitude' => $lng,
            'latitude' => $lat,
            'report_time' => date('Y-m-d H:i:s')
        );
        if (isset($d)){
            // $c = $m->where("sn = '$sn'")->count();
            // $c == 0 ? $m->add($d) : $m->where("sn = '$sn'")->save($d); //此操作改为由server完成，所以此处注释掉
            M('rtu_project_info')->where("sn = '$sn'")->save(array(
                'longitude' => $d['longitude'],
                'latitude' => $d['latitude']
            ));
            $name = M('term_run_info')->where("sn = '$sn'")->getField('name');
            $time = date('His,ymd');
            $ns = $lat > 0 ? 'N':'S';
            $ew = $lng > 0 ? 'E':'W';
            $tmplat = explode('.',abs($lat));
            $tmplng = explode('.',abs($lng));
            $lat = intval($tmplat[0])*100 + floatval('0.'.$tmplat[1])*60;
            $lng = intval($tmplng[0])*100 + floatval('0.'.$tmplng[1])*60;
            $msg = sprintf("%s,%s,1,%f,%s,%f,%s,0,0", $name, $time, $lat, $ns, $lng, $ew);
            $client = stream_socket_client('udp://'.C('SERVER_IP').':'.C('GPS_PORT'), $errno, $errstr, 5);
            fwrite($client, $msg);
            fclose($client);
        }
        if ($from == 'out'){
            $this->ajaxReturn('ok', L('OPERATION_SUCCESS'), 0);
        }
    }

    //批量设置gps
    public function multiSetGps() {
        $sns = I('sns', '', 'trim');
        $lng = I('lng', 0.0, 'doubleval');
        $lat = I('lat', 0.0, 'doubleval');
        if ($sns != '') {
            $sns = explode(',', $sns);
            foreach ($sns as $key => $sn) {
                $this->setgps('in', $sn, $lng, $lat);
            }
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    // 壳函数，节点权限控制
    public function resetCell() {
        $this->resetCellWifi();
    }

    // 壳函数，节点权限控制
    public function resetWifi() {
        $this->resetCellWifi();
    }

    //手动刷新：基站地址，wifi定位AP Mac地址
    private function resetCellWifi(){
        $sns = I('sns');
        $sns = "'".str_replace(',',"','",$sns)."'";
        $tbname = I('type','','string');
        $m = M($tbname);
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
        $m->where("sn IN($sns)")->save($d);
        $this->ajaxReturn('', L('POSITION_PARAM_RESETED'), 0);
    }

    // 设备登录记录
    public function loadLoginRecordData(){
        $sn = $_REQUEST['sn'];
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $q = sprintf("sn = '%s' AND login_time BETWEEN '%s' AND '%s'", $sn, date('Y-m-d H:i:s',$start), date('Y-m-d H:i:s',$end));
        $m = M('term_login_record');
        $rs = $m->field("id,login_time,logout_time,term_signal,flux")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k => $row) {
            $rs[$k]['flux'] = bitsize($row['flux']);
            $rs[$k]['term_signal'] = get_term_signal_str('1', $row['term_signal']);
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    // 网络切换记录
    public function loadNetchangeRecordData(){
        $sn = $_REQUEST['sn'];
        $start = I('start',0,'intval');
        $end = I('end',0,'intval');
        $q = sprintf("sn = '%s' AND report_time BETWEEN '%s' AND '%s'", $sn, date('Y-m-d H:i:s',$start), date('Y-m-d H:i:s',$end));
        $m = M('term_net_mode_record');
        $rs = $m->field("id,report_time,old_value,new_value,old_sim,new_sim")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $nm = C('NET_MODE');
        foreach ($rs as $k => $row) {
            $rs[$k]['old_value'] = $nm[$row['old_value']];
            $rs[$k]['new_value'] = $nm[$row['new_value']];
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //导出路由器
    public function exportTerm(){
        if (C('OEM_VERSION') == 'rx-m2m') {
            $this->exportTermRxm2m();
        }
        $m = M('term');
        $nm = C('NET_MODE');
        $sns = $this->getDestTerms($m);
        $onelink = C('SHOW_ONELINK_MONTH_FLUX');
        $header = array(L('VAR_TERM_STATUS'), L('TERM_GROUP_LIST'), L('VAR_SN2'), L('VAR_SN1'), L('VAR_VSN'), L('DEVICE_MODEL'), L('VAR_SYSCFG_ALIAS'),
            L('VAR_IP'), L('VAR_PORT'), L('VAR_TERM_FLUX'), L('TODAY_FLUX'), L($onelink ? 'ONELINK_MONTH_FLUX' : 'FLUX_CURRENT_MONTH'),
            L('NET_MODE'), L('VAR_TERM_SIGNAL'), 'RSSI', 'RSRP', 'RSRQ', L('VAR_SWV'), L('PROTOCOL_VERSION'),
            L('WORKING_FREQUENCY'), L('ONLINE_DURATION'), L('VAR_LOGOUT_RECORD'), L('VAR_FIRST_LOGIN'), L('VAR_DEVICE_LOGIN_TIME'), L('VAR_LAST_LOGIN'),
            'SIM', 'IMSI', 'ICCID', 'IMEI', L('TERM_MODULE_VENDOR'), L('TERM_MODULE_TYPE'),
            'SSID', L('GUIJI_CODE'), L('VAR_OPERATOR').' 1', L('VAR_OPERATOR').' 2', 'VPN', L('SIM_POS')
        );
        if ($this->lang == 'zh-cn') {
            $header = array_merge($header, array(L('VAR_BASE_ADDRESS'), L('VAR_WIFI_MAC'), L('VAR_POSITION')));
        }

        $rs = $m->join("LEFT JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN term_cell ON term_cell.sn = term.sn LEFT JOIN term_wifi_ap ON term_wifi_ap.sn = term.sn
            LEFT JOIN term_group ON term_group.id = term.group_id
            LEFT JOIN oem_onelink_flux ON oem_onelink_flux.sn = term.sn")
            ->field('term.*, term_run_info.*, term_group.name AS gname, term_cell.lac, term_cell.cellid, term_cell.addr, term_wifi_ap.ap_mac, term_wifi_ap.addr AS addr2,
                oem_onelink_flux.month_flux AS month_flux_onelink, oem_onelink_flux.api_query_time')
            ->order('term_run_info.is_online DESC, term_run_info.last_time DESC')
            ->select();
        $rs2 = M('term_virtual_channel')->join("LEFT JOIN term ON term_virtual_channel.sn = term.sn")
            ->field('term.sn, term_virtual_channel.vpn_type, term_virtual_channel.is_online, term_virtual_channel.last_time')
            ->order('term_virtual_channel.last_time DESC')
            ->select();

        $now = date('Y-m-d H:i:s');
        $ts = time();
        foreach ($rs as $k => $row) {
            if (!in_array($row['sn'],$sns,true)) continue;
            foreach ($rs2 as $k2 => $row2) {
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
            $body[] = $this->lang == 'zh-cn' ? array_merge($tmp_row, array($row['lac_cellid'], $row['ap_mac'], $row['addr'] ? $row['addr'] : $row['addr2'])) : $tmp_row;
        }
        $data = array(
            'filename' => 'device_report_'.date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'router'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // wlink-rx-m2m客户定制
    private function exportTermRxm2m() {
        $m = M('term');
        $nm = C('NET_MODE');
        $sns = $this->getDestTerms($m);
        $header = array(L('VAR_TERM_STATUS').' (sim1)', L('VAR_TERM_STATUS').' (sim2)', L('VAR_TG'), 'MAC Address(last 8 digits)', L('VAR_SN1'), L('VAR_VSN'),
            L('VAR_RULE_DESC'), 'Network(Channel 1)', 'Network(Channel 2)', 'Signal(Channel 1)', 'Signal(Channel 2)', 'IP(Channel 1)', 'IP(Channel 2)', 'Current sim card',
            'Data(Channel 1)', 'Data(Channel 2)', 'Daily data', 'Data in month', 'Firmware', 'Online time', 'Offline time', 'First login', 'Login time',
            'Last activity(Channel 1)', 'Last activity(Channel 2)',
            'IMSI 1', 'ICCID 1', 'IMEI 1', 'Module vendor 1', 'Module model 1', 'Operator 1',
            'IMSI 2', 'ICCID 2', 'IMEI 2', 'Module vendor 2', 'Module model 2', 'Operator 2'
        );
        $rs = $m->join("LEFT JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN term_group ON term_group.id = term.group_id")
            ->field('term.*, term_run_info.*, term_group.name AS gname')
            ->order('term_run_info.is_online DESC, term_run_info.last_time DESC')
            ->select();
        $now = date('Y-m-d H:i:s');
        foreach ($rs as $k => $row) {
            if (!in_array($row['sn'], $sns, true)) continue;
            $s  = get_term_status_code(strtotime($now) - strtotime($row['last_time']), $row['is_online']);
            $s1 = get_term_status_code(strtotime($now) - strtotime($row['last_time_sim1']), $row['is_online']);
            $s2 = get_term_status_code(strtotime($now) - strtotime($row['last_time_sim2']), $row['is_online']);
            $row['protocol'] = intval($row['protocol']/10) . '.' . $row['protocol']%10;
            $row['online_duration'] = $s=='0' ? 0 : format_time($row['login_time'], $row['last_time']);
            $row['offline_duration'] = $s=='1' ? 0 : format_time($row['last_time'], $now);
            $row['term_model'] = $this->getTermModelText($row['term_model'], 1);
            $sim_pos = $row['sim_pos'] == 0 ? sprintf('%s 1 + %s 2', L('CARD'), L('CARD')) : sprintf('%s %d', L('CARD'), $row['sim_pos']);
            $body[] = array(
                L($s1 == '1'? 'VAR_TERM_STATUS_ONLINE' : 'VAR_TERM_STATUS_OFFLINE'), L($s2 == '1'? 'VAR_TERM_STATUS_ONLINE' : 'VAR_TERM_STATUS_OFFLINE'),
                $row['gname'], $row['sn'], $row['ud_sn'], $row['vsn'], $row['alias'], $nm[$row['net_mode_sim1']], $nm[$row['net_mode_sim2']], $row['term_signal_sim1'], $row['term_signal_sim2'],
                $row['ip_sim1'], $row['ip_sim2'], $sim_pos, bitsize($row['flux_sim1']), bitsize($row['flux_sim2']),
                $this->get_day_flux($row['day_flux'], $row['last_time']), $this->get_month_flux($row['month_flux'], $row['last_time'], null, null),
                $row['sw_version'], $s == '0' ? 0 : format_time($row['login_time'], $row['last_time']), $s == '1' ? 0 : format_time($row['last_time'], $now),
                $row['first_login'], $row['login_time'], $row['last_time_sim1'], $row['last_time_sim2'],
                $row['imsi'], $row['iccid'], $row['imei'], $row['module_vendor'], $row['module_type'], $row['operator_sim1'],
                $row['imsi2'], $row['iccid2'], $row['imei2'], $row['module_vendor2'], $row['module_type2'], $row['operator_sim2']
            );
        }
        $data = array(
            'filename' => 'device_report_'.date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'router_rx_m2m'
        );
        $this->ajaxReturn($data, '', 0);
    }

    /**
     * 导出流量报表
     * @param  [int] $startdate 20181201
     * @param  [int] $enddate   20181204
     * @param  [int] $type type=0表示按天，type=1表示按月
     */
    public function exportTermFlux() {
        $m = M('term');
        $type = I('type',0,'intval');
        $sim = I('sim',0,'intval');
        $startdate = date('Ymd', strtotime(I('startdate')));
        $enddate = date('Ymd', strtotime(I('enddate')));

        $q = "sn IN('".implode("','", $this->getDestTerms($m))."')";
        $rs = $m->query("SELECT tg.name gname, t.sn, t.ud_sn, t.vsn, t.term_model, t.alias FROM term t LEFT JOIN term_group tg ON tg.id = t.group_id WHERE $q");
        foreach ($rs as $k => $row) {
            $row['term_model'] = $this->getTermModelText($row['term_model'], 1);
            $sns[$row['sn']] = array_values($row);
        }
        unset($rs);

        $rs2 = $m->query(sprintf("SELECT sn, flux, report_day FROM term_stat_info WHERE %s AND report_day BETWEEN %d AND %d",
            ($sim == 0 ? '1=1' : "sim_pos = $sim"), $startdate, $enddate
        ));
        foreach ($rs2 as $k=>$row) {
            if (!isset($sns[$row['sn']])) continue;
            $key = $type == 0 ? $row['report_day'] : substr($row['report_day'], 0, 6);
            $rs3[$row['sn']][$key] += $row['flux'];
        }
        unset($rs2);

        $header = array(L('VAR_TG'), L('VAR_SN2'), L('VAR_SN1'), L('VAR_VSN'), L('DEVICE_MODEL'), L('VAR_SYSCFG_ALIAS'));
        $i = intval($startdate);
        while ($i <= intval($enddate)) {
            if ($type == 0) {
                array_push($header, date('m-d',strtotime($i)));
                $dateRange[] = $i;
                $i = date('Ymd',strtotime($i)+24*3600);
            } else {
                array_push($header, date('Y-m',strtotime($i)));
                $dateRange[] = substr($i, 0, 6);
                $i = date('Ymd',strtotime('+1 month',strtotime($i)));
            }
        }

        $k = 0;
        foreach ($sns as $sn => $row) {
            $body[$k] = $row;
            foreach ($dateRange as $dr) {
                array_push($body[$k], bitsize($rs3[$sn][$dr]));
            }
            $k++;
        }

        $data = array(
            'filename' => sprintf("data_report_%s".date('YmdHis'), $sim == 0 ? '' : "sim{$sim}_"),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'flux'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 导出信号强度报表
    public function exportSignal() {
        $m = M('term');
        $startdate = date('Ymd', strtotime(I('startdate')));
        $enddate = date('Ymd', strtotime(I('enddate')));
        $sim = I('sim',0,'intval');
        $q = "sn IN('".implode("','", $this->getDestTerms($m))."')";

        $rs = $m->field('term.sn, ud_sn, alias, ip')->join('LEFT JOIN term_run_info ON term_run_info.sn = term.sn')
            ->where("term.$q")->order('term.sn ASC')->select();
        $rs2 = $m->query(sprintf("SELECT sn, report_day, hb_count, sum_signal FROM term_stat_info WHERE %s AND (report_day BETWEEN %d AND %d) AND $q",
            ($sim == 0 ? '1=1' : "sim_pos = $sim"), $startdate, $enddate
        ));
        $rs3 = array();
        foreach ($rs2 as $key => $row) {
            if (!isset($rs3[$row['sn']][$row['report_day']])) {
                $rs3[$row['sn']][$row['report_day']] = array('count'=>0, 'signal'=>0);
            }
            $rs3[$row['sn']][$row['report_day']]['count'] += $row['hb_count'];
            $rs3[$row['sn']][$row['report_day']]['signal'] += $row['sum_signal'];
        }
        foreach ($rs3 as $sn => $days) {
            foreach ($days as $k => $row) {
                $rs3[$sn][$k] = $row['count'] == 0 ? 0 : round($row['signal']/$row['count'], 2);
            }
        }
        unset($rs2);

        $header = array(L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), 'IP', L('SIGNAL_AVERAGE'));
        $i = intval($startdate);
        while ($i <= intval($enddate)) {
            array_push($header, date('n-j',strtotime($i)));
            $date_range[] = $i;
            $i = date('Ymd',strtotime($i)+24*3600);
        }
        foreach ($rs as $key => $row) {
            $body[$key] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['ip'], 0);
            $sum = 0;
            foreach ($date_range as $date){
                if (isset($rs3[$row['sn']][$date])) {
                    array_push($body[$key], $rs3[$row['sn']][$date]);
                    $sum += $rs3[$row['sn']][$date];
                } else {
                    array_push($body[$key], 0);
                }
            }
            $body[$key][4] = round($sum/count($date_range), 2);
        }

        $data = array(
            'filename' => sprintf("signal_report_%s".date('YmdHis'), $sim == 0 ? '' : "sim{$sim}_"),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?array_values($body):array(),
            'type' => 'signal'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 导出上线记录
    public function exportLogins() {
        $m = M('term_login_record');
        $sim = I('sim',0,'intval');
        $startdate = date('Y-m-d 00:00:00', strtotime(I('startdate')));
        $enddate = date('Y-m-d 23:59:59', strtotime(I('enddate')));
        $header = array(L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), 'IP', L('SERVER_PORT'), L('VAR_LOGIN_TIME'), L('VAR_LOGOUT_TIME'), L('VAR_TERM_SIGNAL'), L('VAR_TERM_FLUX'));
        $q = "term_login_record.sn IN('".implode("','", $this->getDestTerms())."')";
        if ($sim != 0) {
            $q = "sim_pos = $sim AND ".$q;
        }
        $rs = $m->field('term.sn, term.ud_sn, term.alias, term_login_record.ip, term_login_record.port,
            term_login_record.login_time, term_login_record.logout_time, term_login_record.term_signal, term_login_record.flux')
            ->join('LEFT JOIN term ON term.sn = term_login_record.sn')
            ->where("login_time BETWEEN '$startdate' AND '$enddate' AND $q")
            ->order('sn ASC, term_login_record.login_time ASC')->select();
        foreach ($rs as $key => $row) {
            $body[] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['ip'], $row['port'], $row['login_time'], $row['logout_time'], $row['term_signal'], bitsize($row['flux']));
        }
        $data = array(
            'filename' => sprintf("login_report_%s".date('YmdHis'), $sim == 0 ? '' : "sim{$sim}_"),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'logins'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 导出离线率报表
    public function exportOfflineRate() {
        ini_set('memory_limit', -1);
        $m = M('term');
        $sim = I('sim',0,'intval');
        $start_ts = strtotime(I('startdate').' 00:00:00');
        $end_ts = strtotime(I('enddate').' 23:59:59') - 24 * 3600;
        $header = array(L('VAR_SN2'), L('VAR_SN1'), L('OFFLINE_HOURS'), L('TOTAL_HOURS'), L('OFFLINE_RATE'));
        $sns = $this->getDestTerms();
        $terms = array();
        $rs = M('term')->join('LEFT JOIN term_run_info ON term_run_info.sn = term.sn')->where("term.sn IN('".implode("','", $sns)."')")
            ->field('term.sn, term.ud_sn, term.imei, term_run_info.is_online, term_run_info.last_time')->select();
        $now_ts = time();
        $ot = C('TERM_OFFLINE_TIME');
        foreach ($rs as $key => $row) {
            $terms[$row['sn']] = array($row['ud_sn'], ($row['is_online'] == 1 && $now_ts - strtotime($row['last_time']) < $ot ? 1 : 0), $row['imei']);
        }
        unset($rs);
        $total_seconds = $end_ts + 1 - $start_ts;
        $total_hour = $total_seconds / 3600;
        foreach ($sns as $sn) {
            $rates = $this->termChartDataOnline($start_ts, $end_ts, $sn, 1);
            $offline_seconds = 0;
            foreach ($rates as $v) {
                $offline_seconds +=  ((100 - $v[1]) / 100) * 24 * 3600;
            }
            $off_rate = $offline_seconds / $total_seconds * 100;
            /*if (100 - $off_rate < 99.5 && $terms[$sn][1] == 1) {
                mt_srand($terms[$sn][2]);
                $off_rate = 100 - mt_rand(9950, 9999) / 100;
                $offline_seconds = $off_rate / 100 * $total_seconds; // 同时要修改离线秒数
            }*/
            if ($off_rate == '0') {
                $off_rate .= '.00';
            }
            $body[] = array($sn, $terms[$sn][0], format_time_his(-1, $offline_seconds), $total_hour, sprintf('%.2f', $off_rate) . '%');
        }
        $data = array(
            'filename' => "offline_rate_report_" . I('startdate') . '_' . I('enddate'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body) ? $body : array(),
            'type' => 'offline_rate_report'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 导出CPU/内存/存储报表
    public function exportCPUMemoryStorage() {
        $m = M('term_stat_info');
        $startdate = I('startdate');
        $enddate = I('enddate');
        $export_type = I('export_type');
        $fields = array('sn', 'report_day');
        $header = array(L('VAR_SN2'), L('VAR_TRACK_DATE'));
        for ($i=0; $i<24; $i++) {
            array_push($fields, 'c'.$i);
            array_push($fields, sprintf('%s_usage%d', $export_type, $i));
            array_push($header, $i.':00');
        }
        $sns = $this->getDestTerms();
        $rs = $m->field(implode(',', $fields))->where("sn IN('".implode("','", $sns)."') AND report_day BETWEEN $startdate AND $enddate")->order('sn ASC, report_day ASC')->select();
        $arr = array();
        foreach ($rs as $row) {
            $k0 = sprintf('%s_%s', $row['sn'], $row['report_day']); // 00000001_20231024
            if (!isset($arr[$k0])) {
                $arr[$k0] = array();
            }
            for ($i=0; $i<24; $i++) {
                $k1 = 'c'.$i;
                $k2 = sprintf('%s_usage%d', $export_type, $i);
                if (!isset($arr[$k0][$i])) {
                    $arr[$k0][$i] = array('c_count' => $row[$k1], 'usage_count' => $row[$k2]);
                } else {
                    $arr[$k0][$i]['c_count'] += $row[$k1];
                    $arr[$k0][$i]['usage_count'] += $row[$k2];
                }
            }

        }
        $i = 0;
        $body = array();
        foreach ($arr as $key => $row) {
            $tmp = explode('_', $key);
            $body[$i] = array($tmp[0], date('Y-m-d', strtotime($tmp[1])));
            for ($j=0; $j<24; $j++) {
                array_push($body[$i], $row[$j]['c_count'] == 0 ? '0.00%' : sprintf('%.2f%%', $row[$j]['usage_count'] / $row[$j]['c_count']));
            }
            $i += 1;
        }
        $data = array(
            'filename' => $export_type . "_report_" . I('startdate') . '_' . I('enddate'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body) ? $body : array(),
            'type' => $export_type . '_report'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 导出网络切换记录
    public function exportNetChange() {
        ini_set('memory_limit', -1);
        $arr = C('NET_MODE');
        $startdate = date('Y-m-d 00:00:00', strtotime(I('startdate')));
        $enddate = date('Y-m-d 23:59:59', strtotime(I('enddate')));
        $header = array(
            L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_DEVICE_URL_REPORT_TIME'),
            L('OLD_VALUE') . '(' .L('NET_MODE').        ')',
            L('NEW_VALUE') . '(' .L('NET_MODE').        ')',
            L('OLD_VALUE') . '(' .L('SIM_CARD_NUMBER'). ')',
            L('NEW_VALUE') . '(' .L('SIM_CARD_NUMBER'). ')'
        );
        $q = "term_net_mode_record.sn IN('".implode("','", $this->getDestTerms())."')";
        $rs = M('term_net_mode_record')->field('term.sn, term.ud_sn, term.alias, term_net_mode_record.report_time,
            old_value, new_value, old_sim, new_sim')
            ->join('LEFT JOIN term ON term.sn = term_net_mode_record.sn')
            ->where("$q AND report_time BETWEEN '$startdate' AND '$enddate'")
            ->order('sn ASC, report_time ASC')->select();
        foreach ($rs as $key => $row) {
            $body[] = array($row['sn'], $row['ud_sn'], $row['alias'], $row['report_time'], $arr[$row['old_value']], $arr[$row['new_value']],
                !empty($row['old_sim']) ? L('CARD').' '.$row['old_sim'] : '',
                !empty($row['new_sim']) ? L('CARD').' '.$row['new_sim'] : ''
            );
        }
        $data = array(
            'filename' => "network_record_".date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'netchange'
        );
        $this->ajaxReturn($data, '', 0);
    }

    //批量操作时判断设备是否同一型号
    public function checkUniqueModel(){
        $act = I('act', 'term', 'string');
        if ($act == 'term') {
            $q = "sn IN('" .str_replace(',', "','", I('term_list', '', 'trim')). "')";
        } elseif ($act == 'group') {
            $gids = I('gids', '-1', 'string');
            $q = $gids == '-10' ? sprintf("group_id IN(%s)", $this->getTgids()) : "group_id = $gids";
        } else {
            $q = sprintf("group_id IN(%s)", $this->getTgids());
        }
        $m = M('term');
        $rs = $m->query("SELECT DISTINCT(term_model) FROM term WHERE $q");
        $models = array();
        foreach ($rs as $k => $row) {
            $v = $this->formatTermModel($row['term_model']);
            if (!isset($models[$v])){
                $models[$v] = 1;
            }
            if (!isset($unique)){
                $unique = $v;
            }
        }
        $info = '';
        if (count($models) == 0){
            $info = L('NO_MATCH_TERM');
        }elseif (count($models) > 1) {
            $info = L('DIFFERENT_MODELS_UNDER_GROUPS');
        }else{
            $tm = C('TERM_MODEL');
            if (!isset($tm[$unique])){
                $info = L('UNSUPPORTED_DEVICE_TYPE');
            }
        }
        $this->ajaxReturn($info == '' ? $unique : ('distinct models = '.count($models)), $info, $info==''?0:-1);
    }

    //添加远程通道路由
    public function n2nRouteradd() {
        $m = M('term_param');
        $sn = $_REQUEST['sns'];
        $ip = M('term_virtual_channel')->where("sn='$sn'")->getField('ip');
        if (!$ip) {
            $this->ajaxReturn('', L('RC_CONNECT_ADD_FIRST'), -1);
        }
        $params = $m->where("sn = '%s' AND param <> 'unknown' AND param <> '' AND param IS NOT NULL", $sn)->limit(1)->getField('param');
        if (!$params) {
            $this->ajaxReturn('', L('TERM_GET_PARAM_FIRST'), -2);
        } else {
            $param = explode("&",$params);
            foreach ($param as $k=>$v){
                $i = strpos($v, '=');
                if ($i === false || strlen($v) == $i+1) continue;
                $d[substr($v,0,$i)] = preg_replace("/^\"|\"$/", "", substr($v,$i+1));
            }
            $lan_ipaddr = $d['lan_ipaddr'];
            $lan_netmask = $d['lan_netmask'];
            if (empty($lan_ipaddr) || empty($lan_netmask)) {
                $this->ajaxReturn('', L('UNSUPPORTED_DEVICE_TYPE'), -3);
            }

            $lan_ipaddr_num = explode(".", $lan_ipaddr);
            foreach ($lan_ipaddr_num as $k => $v) {
                $hex = decbin($v);
                if (strlen($hex) < 8) {
                   $info = sprintf("%08d", $hex);
                } else {
                   $info = $hex;
                }
                $ip_str .= $info;
            }

            $lan_netmask_num = explode(".", $lan_netmask);
            foreach ($lan_netmask_num as $k => $v) {
                $bin = decbin($v);
                if (strlen($bin) < 8) {
                   $lan_info = sprintf("%08d",$bin);
                } else {
                   $lan_info = $bin;
                }
                $netmask_str .= $lan_info;
            }
            $ips = $netmask_str & $ip_str;

            $len = strlen($ips);
            for ($i=0; $i<$len; $i++) {
                $cmd_str .= bindec(substr($ips,$i,8)).".";
                $i += 7;
            }
            $last_ips = substr($cmd_str, 0, strlen($cmd_str)-1);
            $cmd = "exe route add ".$last_ips." mask 255.255.255.0 ".$ip;
            $this->ajaxReturn($cmd, L('OPERATION_SUCCESS'), 0);
        }
    }

    //查看RTU接收到的数据
    public function loadRecvData(){
        $m = M('term_transparent_data');
        $sns = "'".str_replace(',',"','",I('sns','','string'))."'";
        $q = sprintf("sn IN($sns) AND %s", $this->generate_search_str());
        $sql = "SELECT * FROM term_transparent_data WHERE $q ORDER BY ".$this->generate_order_str('term_transparent_data')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
        $rs = $m->query($sql);
        $arr = L('VAR_TASK_STATUS_ARR');
        $green = '<font color="#0CEF0C">%s</font>';
        $red = '<font color="red">%s</font>';
        foreach ($rs as $k => $row) {
            $rs[$k]['data_type'] = $row['data_type']==0 ? L('DATA_TYPE_STRING') : L('DATA_TYPE_BYTES');
            if ($row['from_type'] == 1) {
                $rs[$k]['status'] = sprintf($green, $arr[3]);
                if ($row['from_type'] == 1) {
                    $rs[$k]['value2'] = pack('H*', str_replace(' ','',$row['value']));
                }
            } elseif ($row['status'] == 3) {
                $rs[$k]['status'] = sprintf($green, $arr[$row['status']]);
            } elseif ($row['status'] == 4 || $row['status'] == 6) {
                $rs[$k]['status'] = sprintf($red, $arr[$row['status']]);
            } else {
                $rs[$k]['status'] = $arr[$row['status']];
            }
            $hex = dechex($row['data_port']);
            if (strlen($hex) == 1){
                $h = 0;
                $l = hexdec($hex) + 1;
            }else{
                $h = hexdec(substr($hex,0,1));
                $l = hexdec(substr($hex,1,1)) + 1;
            }
            $rs[$k]['data_port'] = L('INTERFACE_TYPE_'.$h).' '.$l;
            //from_type == 1  time = create_time
            //from_type == 0  send_time默认为空，如果是空，显示待发送。否则显示send_time
            if ($row['from_type'] == '1'){
                $rs[$k]['time'] = $row['create_time'];
            }else{
                if ($row['send_time'] == '0000-00-00 00:00:00' || empty($row['send_time'])){
                    $rs[$k]['time'] = L('TO_BE_SENT');
                }else{
                    $rs[$k]['time'] = $row['send_time'];
                }
            }
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    //数据透传
    public function dataTrans(){
        $send_enter = I('send_enter','','string');
        $value = I('value');
        if ($send_enter != ''){
            $value .= "\n";
        }
        $d = array(
            'sn' => I('sn'),
            'data_type' => I('data_type', 0, 'intval'),
            'value' => $value,
            'from_type' => 0,
            'data_port' => hexdec(I('interface_type',0,'intval').I('interface_num',0,'intval'))
        );
        M('term_transparent_data')->add($d);
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //地址解析
    // $type = 0表示由address获取经纬度，
    // $type = 1表示由经纬度获取address
    public function geocode() {
        $type = I('type', 0, 'intval');
        $opts = stream_context_create(array(
            'http' => array('method'=>'GET', 'timeout'=>2)
        ));
        $info = array('', '', '');
        if ($type == 0) {
            $json = file_get_contents(sprintf('https://restapi.amap.com/v3/geocode/geo?address=%s&JSON=XML&key=%s', I('address', '', 'string'), C('GAODE_SERVICE_KEY')), 0, $opts);
            $ret = '0,0';
            if ($json) {
                $json = json_decode($json, true);
                if ($json['status'] == '1' && count($json['geocodes']) > 0) {
                    $ret = $json['geocodes'][0]['location'];
                    $info = array(
                        $json['geocodes'][0]['province'],
                        $json['geocodes'][0]['city'],
                        $json['geocodes'][0]['district']
                    );
                }
            }
        } elseif ($type == 1) {
            $json = file_get_contents(sprintf('https://restapi.amap.com/v3/geocode/regeo?output=JSON&location=%s&key=%s', I('location', '0,0', 'string'), C('GAODE_SERVICE_KEY')), 0, $opts);
            $ret = '';
            if ($json) {
                $json = json_decode($json, true);
                if ($json['status'] == '1' && $json['regeocode'] && $json['regeocode']['formatted_address']) {
                    $ret = $json['regeocode']['formatted_address'];
                    $info = array(
                        $json['regeocode']['addressComponent']['province'],
                        $json['regeocode']['addressComponent']['city'],
                        $json['regeocode']['addressComponent']['district']
                    );
                }
            }
        }
        $this->ajaxReturn($ret, $info, 0);
    }

    public function getRtuScript() {
        $script = M('term_param')->where("sn = '%s'", I('sn', '', 'string'))->limit(1)->getField('rtu_script');
        $this->ajaxReturn($script ? $script : '', '', 0);
    }
}