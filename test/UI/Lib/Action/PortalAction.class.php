<?php
class PortalAction extends CommonAction{
    // 移动设备列表
    public function sblb() {
        if (IS_AJAX) {
            $m = M('term');
            $q = sprintf('__POS__ AND ');
            $gid = I('gid', -10, 'intval');
            $q .= $this->generate_search_str();
            if ($gid != -10) {
                $q .= sprintf(' AND group_id = %d', $gid);
            } else {
                if ($_SESSION[C('SESSION_NAME')]['id'] != 1) {
                    $q .= sprintf(' AND group_id IN(%s)', $this->getTgids());
                }
            }
            $q2 = str_replace('__POS__', '1 = 1', $q);
            $q  = str_replace('__POS__', 'B.sn = term.sn AND C.id = term.group_id', $q);
            $sql = "SELECT term.sn, term.ud_sn, term.alias, term.term_model, term.group_id, (SELECT COUNT(*) FROM device WHERE sn = term.sn)online_num,
                B.is_online, B.last_time, C.name AS gname FROM term, term_run_info B, term_group C
                WHERE $q ORDER BY ".$this->generate_order_str('router_list')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
            $rs = $m->query($sql);
            $ts = time();
            if ($rs && count($rs) > 0) {
                $sns = array();
                foreach ($rs as $row) {
                    array_push($sns, "'".$row['sn']."'");
                }
                $q_temp = sprintf("sn IN(%s)", implode(',', $sns));
                // 电子围栏
                $fstatus_arr = $this->getFenceStatus($q_temp);
            }
            foreach ($rs as $k => $row) {
                $rs[$k]['diff'] = $ts - strtotime($row['last_time']);

                //流量限制状态，当system_config.enable_electronic_fence = 1 && term_gps.fstatus = 1时，将status_limit设置为1
                if ($row['status_limit'] != 1 && $fstatus_arr[0] == '1' && $fstatus_arr[1][$row['sn']] == '1') {
                    $rs[$k]['status_limit'] = 1;
                }

                $this->transformTermFields($rs[$k]);
            }
            $rs_total = $m->query("SELECT COUNT(*)num FROM term WHERE $q2");
            $total = $rs_total[0]['num'];
            die(json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            )));
        }
        for ($i=7; $i>=1; $i--){
            $xs[] = date('M d',strtotime("-$i days"));
        }
        $this->assign('xs',json_encode($xs));
        $this->assign('web_path_1', array(L('VAR_DEVICE_LIST')));
        $this->display('sblb');
    }

    // 移动设备
    public function loadMobile() {
        $type = I('type', 'online', 'trim');
        $tbname = $type == 'online' ? 'device' : 'device_login_record';
        $m = M($tbname);
        $sn = I('sn', '', 'trim');
        $qv = I('searchString', '', 'trim');
        $q = sprintf("term.sn = '%s'", $sn);
        if ($qv != '') {
            $q .= sprintf(" AND %s.mac_addr = '%s'", $tbname, $qv);
        }
        $q .= sprintf(' AND term.group_id IN(%s)', $this->getTgids());
        $rs = $m->field("{$tbname}.*, device_conf.day_flux_limit, device_conf.month_flux_limit")
            ->join("LEFT JOIN device_conf ON device_conf.mac_addr = {$tbname}.mac_addr")
            ->join("LEFT JOIN term ON term.sn = {$tbname}.sn")
            ->where($q)
            ->order($this->generate_order_str('mobile'))
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k=>$row){
            $rs[$k]['flux'] = bitsize($row['flux']); //移动设备流量单位是BYTE
            $rs[$k]['day_flux_limit'] = $row['day_flux_limit'] ? $row['day_flux_limit'] : 0;
            $rs[$k]['day_flux_limit_format'] = $row['day_flux_limit'] ? bitsize($row['day_flux_limit'] * 1024) : 0;
            $rs[$k]['month_flux_limit'] = $row['month_flux_limit'] ? $row['month_flux_limit'] : 0;
            $rs[$k]['month_flux_limit_format'] = $row['month_flux_limit'] ? bitsize($row['month_flux_limit'] * 1024) : 0;
            $rs[$k]['duration'] = format_time($row['login_time'], $row[$type == 'history' ? 'logout_time' : 'last_time']);
            if ($type == 'history') {
                $rs[$k]['last_time'] = $row['logout_time'];
            }
            $rs[$k]['act'] = sprintf('<i class="fa fa-user-times" title="%s" style="cursor:pointer;" onclick="javascript:$.gf.force_offline(\'%s\', \'%s\');"></i>',
                L('VAR_DEVICE_FORCE_OFFLINE'), $row['sn'], $row['mac_addr']
            );
        }
        $total = $m->where($q)->join("LEFT JOIN term ON term.sn = $tbname.sn")->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    // 导出移动设备
    public function exportMobile() {
        $type = I('type', 'online', 'trim');
        $tbname = $type == 'online' ? 'device' : 'device_login_record';
        $m = M($tbname);
        $header = array(L('VAR_DEVICE_MAC'), L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_TERM_FLUX'), L('ONLINE_DURATION'), L('VAR_DEVICE_LOGIN_TIME'), L('VAR_LAST_LOGIN'));
        $q = sprintf('term.group_id IN(%s)', $this->getTgids());
        $rs = $m->field("{$tbname}.*, term.sn, term.ud_sn, term.alias")
            ->join("LEFT JOIN term ON term.sn = {$tbname}.sn")
            ->where($q)
            ->order($type == 'online' ? 'last_time DESC' : 'logout_time DESC')
            ->select();
        $last_field = $type == 'history' ? 'logout_time' : 'last_time';
        foreach ($rs as $k => $row) {
            $body[] = array($row['mac_addr'], $row['sn'], $row['ud_sn'], $row['alias'], bitsize($row['flux']),
                format_time($row['login_time'], $row[$last_field]), $row['login_time'], $row[$last_field]
            );
        }
        $data = array(
            'filename' => $type.'_report_'.date('YmdHis'),
            'header' => $header,
            'footer' => $footer,
            'body' => is_array($body)?$body:array(),
            'type' => 'mobile_report'
        );
        $this->ajaxReturn($data, '', 0);
    }

    // 移动设备在线率
    public function onlineRates(){
        $q = sprintf('group_id IN(%s)', $this->getTgids());
        $online = M('device')->join('LEFT JOIN term ON term.sn = device.sn')->where($q)->count();
        $total = M('device_flux')->join('LEFT JOIN term ON term.sn = device_flux.sn')->where($q)->count('DISTINCT(mac_addr)');
        $ret = array(
            array('name'=>L('VAR_TERM_STATUS_ONLINE'), 'y'=>intval($online)),
            array('name'=>L('VAR_TERM_STATUS_OFFLINE'), 'y'=>intval($total - $online))
        );
        echo json_encode($ret);
    }

    //流量趋势
    public function fluxTrend(){
        $q = sprintf('group_id IN(%s)', $this->getTgids());
        for ($i=7; $i>=1; $i--){
            $ts = strtotime("-$i days");
            $fields[] = 'f'.intval(date('j',$ts));
        }
        foreach ($fields as $k => $v) {
            $data[$v] = 0;
        }
        $m = M('device_flux');
        $d = intval(date('j')); //今天几号
        if ($d == 1){
            $fields1 = $fields;
            $fields2 = array();
        }elseif ($d >= 8){
            $fields1 = array();
            $fields2 = $fields;
        }else{
            $fields1 = array_slice($fields, 0, 8-$d);
            $fields2 = array_slice($fields, 1-$d);
        }
        $rs1 = $m->field(count($fields1)>0?implode(',', $fields1):'null')->join('LEFT JOIN term ON term.sn = device_flux.sn')->where("report_month = %d AND $q",date('Ym',strtotime('-1 month')))->select();
        $rs2 = $m->field(count($fields2)>0?implode(',', $fields2):'null')->join('LEFT JOIN term ON term.sn = device_flux.sn')->where("report_month = %d AND $q",date('Ym'))->select();
        foreach ($rs1 as $k => $row) {
            foreach ($fields1 as $key => $v) {
                $data[$v] += $row[$v];
            }
        }
        foreach ($rs2 as $k => $row) {
            foreach ($fields2 as $key => $v) {
                $data[$v] += $row[$v];
            }
        }
        foreach ($data as $k => $v) {
            $data[$k] = bitsizeMb($v);
        }
        echo json_encode(array_values($data));
    }

    //上线趋势
    public function loginTrend(){
        $q = sprintf('group_id IN(%s)', $this->getTgids());
        for ($i=7; $i>=1; $i--){
            $data[date('Y-m-d',strtotime("-$i days"))] = 0;
        }
        $keys = array_keys($data);
        $rs = M('')->query("SELECT COUNT(DISTINCT(mac_addr))num, LEFT(login_time,10)ymd FROM device_login_record
            LEFT JOIN term ON term.sn = device_login_record.sn
            WHERE login_time BETWEEN '{$keys[0]} 00:00:00' AND '{$keys[6]} 23:59:59' AND $q GROUP BY ymd"
        );
        foreach ($rs as $k => $row) {
            $data[$row['ymd']] = intval($row['num']);
        }
        echo json_encode(array_values($data));
    }

    // 广告管理
    public function portal(){
        if (IS_AJAX) {
            $m = M('ad');
            $uid = $_SESSION[C('SESSION_NAME')]['id'];
            $q = sprintf('%s AND %s', $this->generate_search_str(), $uid == 1 ? '2=2' : 'ugid='.$this->getUgid());
            $rs = $m->field('ad.*, (SELECT COUNT(*) FROM ad_file WHERE ad_id = ad.id)AS num')
                ->where($q)
                ->order($this->generate_order_str())
                ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
            $total = $m->where($q)->count();
            echo json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            ));
        } else {
            $this->assign('web_path_1', array(L('VAR_MENU2_AD')));
            $this->display('portal');
        }
    }

    //新增广告
    public function portalAdd() {
        $ad_id = I('ad_id', 0, 'intval');
        $name = I('name', '', 'trim');
        $user = $_SESSION[C('SESSION_NAME')];
        if ($ad_id == 0) {
            //新增广告
            if (M('ad')->where("name = '$name'")->count() != 0) {
                $this->ajaxReturn('', L('NAME_EXIST'), -1);
            }
            $log_act = 'ad_add';
            $msg_ok = L('VAR_AD_ADD_OK');
            $msg_failed = L('VAR_AD_ADD_FAILED');
            $aid = M('ad')->add(array(
                'name' => $name,
                'creator' => $user['name'],
                'ugid' => $user['gid']
            ));
        } else {
            //上传广告文件
            $log_act = 'ad_file_add';
            $msg_ok = L('VAR_AD_ADD_FILE_OK');
            $msg_failed = L('VAR_AD_ADD_FILE_FAILED');
            $aid = $ad_id;
            $exist_filenames = M('file_list')->where("id IN(SELECT file_list_id FROM ad_file WHERE ad_id = $aid)")->getField('name', true);
        }

        $dir = C('FTP_WEB_PACK_PATH').'ad/'.$aid.'/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $f = $_FILES['filedata'];
        $total = count($f['error']); //总文件数目
        $error = 0; //上传失败文件数目
        $duplicate = 0; //名称重复的文件数目

        for ($i=0; $i<$total; $i++) {
            if ($f['error'][$i] != 0) {
                $error += 1;
                continue;
            }
            if (in_array($f['name'][$i], $exist_filenames, true)) {
                $duplicate += 1;
                continue;
            }
            if (move_uploaded_file($f['tmp_name'][$i], $dir.$f['name'][$i])) {
                $ad_files[] = array(
                    'ad_id' => $aid,
                    'file_list_id' => M('file_list')->add(array(
                        'name'              => $f['name'][$i],
                        'original_filename' => $f['name'][$i],
                        'filename'          => $f['name'][$i],
                        'relative_path'     => 'ad/'.$aid,
                        'filetype'          => 3,
                        'filesize'          => $f['size'][$i],
                        'md5_num'           => strtoupper(md5_file($dir.$f['name'][$i])),
                        'ugid'              => $user['gid'],
                        'creator'           => $user['name'],
                        'finish_status'     => 1
                    ))
                );
            }
        }
        if (isset($ad_files)) {
            M('ad_file')->addAll($ad_files);
            $this->wlog($user['name'], $log_act, 'name='.$name, 'aid='.$aid, $user['gid']);
            $this->ajaxReturn('', $msg_ok, 0);
        } else {
            $this->ajaxReturn(array('total'=>$total, 'error'=>$error, 'duplicate'=>$duplicate), $msg_failed, -2);
        }
    }

    //删除广告
    public function portalDelete(){
        $ad_id = I('ad_id', 0, 'intval');
        $m = M('file_list');
        $q = "id IN(SELECT file_list_id FROM ad_file WHERE ad_id = $ad_id)";
        $files = $m->where($q)->getField('filename', true);
        $dir = C('FTP_WEB_PACK_PATH').'ad/'.$ad_id;

        //删除file_list表文件记录
        $m->where($q)->delete();

        //删除ad
        M('ad')->where("id = $ad_id")->delete();

        //删除文件
        foreach ($files as $k => $v) {
            if (!empty($v)){
                @unlink($dir.'/'.$v);
            }
        }
        //删除文件夹
        rmdir($dir);

        $this->wlog('', $log_act, 'filename='.implode(',', $files));
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    // 配置流量限制值
    public function editDataLimit(){
        $mac_addr = I('mac_addr', '', 'trim');
        $m = M('device_conf');
        $d = array(
            'day_flux_limit' => I('day_flux_limit', 0, 'intval'),
            'month_flux_limit' => I('month_flux_limit', 0, 'intval')
        );
        if ($m->where("mac_addr = '%s'", $mac_addr)->count() == 0) {
            $d['mac_addr'] = $mac_addr;
            $d['is_black'] = 0;
            $m->add($d);
        } else {
            $m->where("mac_addr = '%s'", $mac_addr)->save($d);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //获取模态框html
    public function getModalHtml(){
        $tpl_id = trim($_REQUEST['tpl_id']);
        $tpl = '';
        $tm = C('TERM_MODEL');
        if ($tpl_id != '') {
            $tpl = $this->buildHtml($tpl_id, './Runtime/Temp/', './Tpl/Portal/modal/'.$tpl_id.'.html');
            if ($tpl_id == 'edit_data_limit') {
                $mac_addr = trim($_REQUEST['mac_addr']);
                $row = M('device_conf')->where("mac_addr = '%s'", $mac_addr)->field('day_flux_limit, month_flux_limit')->find();
                $tpl = sprintf($tpl, $mac_addr, $row['day_flux_limit'] ? $row['day_flux_limit'] : 0, $row['month_flux_limit'] ? $row['month_flux_limit'] : 0);
            }
        }
        echo $tpl;
    }
}
