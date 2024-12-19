<?php
class TaskmgrAction extends CommonAction{
    public function rwlb($tbname = 'term_task') {
        if (IS_AJAX) {
            $m = M($tbname);
            $cmd = I('cmd','all','string');
            $q = sprintf('%s AND %s', $cmd=='all'?'0=0':"cmd='$cmd'", $this->generate_search_str());
            $q_privileges = $_SESSION[C('SESSION_NAME')]['id']==1 ? '2 = 2' : 'ugid = '.$this->getUgid();
            $q = $q.' AND '.$q_privileges;
            $rs = $m->field("$tbname.*")
                ->where($q)
                ->order($this->generate_order_str())
                ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
            $arr1 = L('VAR_TASK_TYPE_ARR');
            $cmdHasProcoess = array('download_ad', 'upgrade', 'upgrade_udp', 'upgrade_tcp', 'upgrade_camera');
            $task_ids = array();
            foreach ($rs as $k=>$row){
                array_push($task_ids, $row['id']);
                $index = $row['id'];
                $data[$index] = array(
                    'id'          => $row['id'],
                    'is_enable'   => $row['is_enable'],
                    'is_enable_color' => $row['is_enable']==1 ? '#ded8d8':'#f7a7a7',
                    'username'    => $row['username'],
                    'id'          => $row['id'],
                    'create_time' => $row['create_time'],
                    'start_time'  => $row['start_time'],
                    'end_time'    => $row['is_never_expire']==1 ? L('NEVER_EXPIRE'):$row['end_time'],
                    'value'       => $row['value'],
                    'value_v2'    => $row['value_v2'],
                    'cmd'         => $arr1[$row['cmd']],
                    'cmd_o'       => $row['cmd'],
                    'period_type' => isset($row['period_type']) ? $row['period_type'] : -1,
                    'progress'    => in_array($row['cmd'], $cmdHasProcoess, true),
                    'status_all'  => 0,
                    'status_3'    => 0,
                    'status_0'    => 0,
                    'status_other'=> 0,
                );
            }
            unset($rs);
            $field_task_id = str_replace('term_', '', $tbname).'_id';
            $rs2 = M($tbname.'_detail')->field("$field_task_id, (CASE status WHEN 8 THEN 0 ELSE status END)status")->where("$field_task_id IN(%s)", implode(',', $task_ids))->select();
            foreach ($rs2 as $k => $row) {
                $index = $row[$field_task_id];
                $data[$index]['status_all'] += 1;
                if ($row['status'] == 0 || $row['status'] == 3){
                    $data[$index]['status_'.$row['status']] += 1;
                } elseif ($row['status'] == 1 || $row['status'] == 2){
                    $data[$index]['status_0'] += 1;
                }else{
                    $data[$index]['status_other'] += 1;
                }
            }
            $rs = array_values(isset($data) ? $data : array());
            $total = $m->where($q)->count();
            echo json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            ));
        } else {
            $this->assign('tsa', L('VAR_TASK_STATUS_ARR'));
            $tta = L('VAR_TASK_TYPE_ARR');
            $task_type = array(
                array('id'=>'config_get', 'name'=>$tta['config_get']),
                array('id'=>'config_set', 'name'=>$tta['config_set']),
                array('id'=>'restart', 'name'=>$tta['restart']),
                array('id'=>'upgrade', 'name'=>$tta['upgrade_tcp']),
                array('id'=>'packet_cap', 'name'=>$tta['packet_cap']),
            );
            $this->assign('tta', $task_type);
            $this->assign('web_path_1', $tbname == 'term_task' ? array(L('VAR_MENU_TASK_ONCE')) : array(L('VAR_MENU_TASK_TIMED')));
            $this->display($tbname == 'term_task' ? 'rwlb' : 'dsrwlb');
        }
    }

    // 定时任务
    public function dsrwlb() {
        $this->rwlb('timed_term_task');
    }

    //任务详情
    public function rwxq() {
        $tid = $_REQUEST['tid'];
        $this->assign('tsa', L('VAR_TASK_STATUS_ARR'));
        $params = M('term_task')->where("id=$tid")->find();
        if ($params){
            $tta = L('VAR_TASK_TYPE_ARR');
            $params['cmd_text'] = $tta[$params['cmd']];
            if ($params['cmd'] == 'download_ad') {
                preg_match('/file1=ad\/([0-9]{1,})\//', $params['value'], $match);
                if ($match) {
                    $params['cmd_text'] .= sprintf(' (%s)', M('ad')->where('id = %d', $match[1])->getField('name'));
                }
                $params['value'] = preg_replace('/(.*)password=(.*?)\&(.*)/', '${1}password=******&${3}', $params['value']); // 隐藏ftp密码
            }
            $params['is_enable_text'] = L($params['is_enable'] == 1 ? 'VAR_ENABLE' : 'VAR_DISABLE');
        }
        $this->assign('taskparams', $params);
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Taskmgr/rwlb'), L('VAR_MENU_TASK_ONCE')),
            L('VAR_TASK_DETAIL')
        ));
        $this->display('rwxq');
    }

    // 定时任务详情
    public function dsrwxq() {
        $tid = $_REQUEST['tid'];
        $this->assign('tsa', L('VAR_TASK_STATUS_ARR'));
        $params = M('timed_term_task')->where("id=$tid")->find();
        if ($params){
            $tta = L('VAR_TASK_TYPE_ARR');
            $params['cmd_text'] = $tta[$params['cmd']];
            $params['is_enable_text'] = L($params['is_enable'] == 1 ? 'VAR_ENABLE' : 'VAR_DISABLE');
            $params['period_type_text'] = get_period_type_text($params['period_type'], $params['period_value']);
        }
        $this->assign('taskparams', $params);
        $this->assign('web_path_1', array(
            sprintf('<a href="%s">%s</a>', U('Taskmgr/dsrwlb'), L('VAR_MENU_TASK_TIMED')),
            L('VAR_TASK_DETAIL')
        ));
        $this->display('dsrwxq');
    }

    // 下载文件 -- 抓包，参数文件，抓拍文件
    // id为task_detail_id
    // fileid为file_list表id
    public function cpFilesDownload() {
        $m = M('file_list');
        $fileid = trim($_REQUEST['fileid']);
        if ($fileid == '') {
            $sn = $_REQUEST['sn'];
            $id = $_REQUEST['id'];
            $tbname = I('get.tbname', 'term_task', 'string');
            $value = M($tbname)->where('id = (SELECT %s_id FROM %s_detail WHERE id = %d)', array(str_replace('term_', '', $tbname), $tbname, $id))->getField('value');
            preg_match("/fileid=(\d+)/i", $value, $match);
            $fileid = $match[1];
        }
        $row = $m->where("id = %d", $fileid)->find();
        header("Content-type:text/html;charset=utf-8");
        if ($row && $row['finish_status'] == 1){
            $filename = $row['name'];
            $path = C('FTP_WEB_PACK_PATH').$row['relative_path'].'/'.$filename;
            if (!file_exists($path)){
                die("File ($filename) not exist!");
            }
            $fp = fopen($path,"r");
            $file_size = filesize($path);
            //下载文件需要用到的头
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:".$file_size);
            Header("Content-Disposition: attachment; filename=".$filename);
            $buffer = 1024;
            $file_count = 0;
            //向浏览器返回数据
            while (!feof($fp) && $file_count < $file_size){
                $file_con = fread($fp,$buffer);
                $file_count += $buffer;
                echo $file_con;
            }
            fclose($fp);
        }else{
            die(L('VAR_DOWNLOAD_ERROR'));
        }
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于设备详情页面，删除定时任务
    public function termDetailDeleteTimedTask() {
        $this->taskDel();
    }

    // 删除任务
    // 当sn不为空时，是删除定时任务的操作：从timed_term_task表的sn_list中移除sn
    public function taskDel() {
        $tbname = I('tbname', 'term_task', 'string');
        $ids = I('ids','','string');
        $m = M($tbname);
        $sn = I('sn', '', 'trim');
        if (!empty($sn)) {
            $sn_list = explode(',', $m->where("id = $ids")->getField('sn_list'));
            foreach ($sn_list as $key => $value) {
                if ($value == $sn) continue;
                $sn_list_new[] = $value;
            }
            $ret = $m->where("id = $ids")->save(array('sn_list' => implode(',', $sn_list_new)));
            $this->wlog('', 'term_task_delete', 'ids='.$ids, "Remove $sn from sn_list");
        } else {
            $cmds = $m->where('id IN(%s)',$ids)->getField('cmd', true, true);
            $ret = $m->where('id IN(%s)', $ids)->delete();
            if ($ret) {
                M($tbname.'_detail')->where("%s_id IN(%s)", array(str_replace('term_', '', $tbname), $ids))->delete();
                $this->cleanDownloadReport();
                $this->wlog('', 'term_task_delete', 'ids='.$ids, 'cmds='.$cmds);
            }
        }
        $this->ajaxReturn($ret, L('VAR_TASK_DEL_OK'), 0);
    }

    // 1.清理表 download_report
    // 2.删除没有详情的任务
    private function cleanDownloadReport() {
        M('')->execute('DELETE FROM download_report WHERE task_detail_id NOT IN(SELECT id FROM term_task_detail UNION SELECT id FROM timed_term_task_detail)');
        M('')->execute('DELETE FROM term_task WHERE (SELECT COUNT(*) FROM term_task_detail WHERE task_id = term_task.id) = 0');
    }

    //任务 启用/停止
    public function taskEnable() {
        $tbname = I('tbname', 'term_task', 'string');
        $is_enable = I('is_enable',0,'intval');
        $ids = I('ids','','string');
        $m = M($tbname);
        $ret = $m->where('id IN(%s)',$ids)->save(array('is_enable'=>$is_enable));
        if ($ret) {
            $cmds = $m->where('id IN(%s)',$ids)->getField('cmd',true,true);
            $this->wlog('', 'term_task_'.($is_enable==1?'enable':'disable'), 'ids='.$ids, 'cmds='.$cmds);
        }
        $this->ajaxReturn($ret, L('OPERATION_SUCCESS'), 0);
    }

    //任务列表-统计信息
    public function rwlbStatisticalInfo(){
        $total = $success = $failed = 0;
        $waiting = 0;
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $ugid = $this->getUgid();
        $tb_tk = I('tbname', 'term_task', 'string');
        $tb_tkd = $tb_tk . '_detail';
        $field_task_id = str_replace('term_', '', $tb_tk).'_id';
        $rs = M('')->query("SELECT (CASE status WHEN 8 THEN 0 ELSE status END)status, COUNT(*)num FROM $tb_tkd
            INNER JOIN $tb_tk ON $tb_tk.id = $tb_tkd.$field_task_id
            WHERE ".($uid == 1 ? '1=1' : "$tb_tk.ugid = $ugid")." GROUP BY status");
        foreach ($rs as $k => $row) {
            $total += $row['num'];
            if ($row['status'] == 3){
                $success = $row['num'];
            }elseif ($row['status'] >= 4 && $row['status'] <= 7){
                $failed += $row['num'];
            }elseif ($row['status'] >= 0 && $row['status'] <= 3){
                $waiting += $row['num'];
            }
        }
        $ret = array(
            'info_box_task_total' => $total,
            'info_box_task_success' => $success,
            'info_box_task_failed' => $failed,
            'info_box_task_waiting' => $waiting
        );
        $this->ajaxReturn($ret, 'ok', 0);
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于设备详情页面，删除任务详情
    public function termDetailDeleteTask() {
        $this->taskDetailDel();
    }

    // 任务详情-删除
    public function taskDetailDel() {
        $tbname = I('tbname', 'term_task_detail', 'string');
        $ids = $_REQUEST['ids'];
        $ret = M($tbname)->where('id IN(%s)', $ids)->delete();
        if ($ret) {
            $this->cleanDownloadReport();
        }
        $this->ajaxReturn($ret, L('VAR_TASK_DEL_OK'), 0);
    }

    //任务详情-重试
    public function taskDetailRetry() {
        $type = I('type','select','string');
        $tid = $_REQUEST['tid'];
        $ids = $_REQUEST['ids'];
        $q = '1 = 2';
        if ($type == 'all'){
            $q = 'task_id = '.$tid;
        }elseif($type == 'select'){
            $q = 'id IN('.$ids.')';
        }
        $m = M('term_task_detail');
        $ret = $m->where("$q AND status IN(4,5,6,7)")->save(array('status'=>0));
        $this->ajaxReturn($ret, L($ret > 0 ? 'VAR_TASK_RETRY_OK':'VAR_TASK_RETRY_FAILED'), $ret > 0 ? 0 : -1);
    }

    //任务详情列表
    public function loadTaskDetail() {
        $tid = I('tid', 0, 'intval');
        $m = M('term_task_detail');
        $status_field = "(CASE term_task_detail.status WHEN 8 THEN 0 ELSE term_task_detail.status END)status";
		if ($tid != 0) {
            //$s表示任务状态status，逗号分隔
            $uid = $_SESSION[C('SESSION_NAME')]['id'];
            $ugid = $this->getUgid();
            $s = I('tsid', '', 'string');
            //$tid > 0表示查询某个任务的详情列表，$tid = -1表示在任务列表页面查看异常或等待执行的任务详情
            $q = sprintf('task_id %s AND %s AND %s', ($tid==-1 ? '>0' : "=$tid"), $this->generate_search_str(), ($tid==-1 ? ($uid == 1 ? '1=1' : "term_task.ugid = $ugid") : '2=2'));
            if ($s != '') {
                $q .= " AND term_task_detail.status IN($s)";
            }
            //任务详情
			$rs = $m->field("term_task.is_enable, term_task.cmd, term_task.create_time, term_task.username, term_task.value, term_task_detail.id,
                task_id, term_task_detail.sn, IFNULL(send_time,'')send_time, IFNULL(recv_time,'')recv_time, term_task_detail.last_time,
                error_info, $status_field, (SELECT SUM(download_size)/SUM(filesize) FROM download_report WHERE task_detail_id = term_task_detail.id)progress")
				->join('LEFT JOIN term_task ON term_task.id = term_task_detail.task_id')
				->where($q)
				->order($this->generate_order_str())
				->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
		} else {
            // 查询某个用户的最新一条任务(不考虑周期任务，只在term_task表查询)，方便用户在设备监控页面查看任务状态
			$task_id = M('term_task')->where("username = '%s'", $_SESSION[C('SESSION_NAME')]['name'])->order('id DESC')->limit(1)->getField('id');
			$rs = $m->field("term_task_detail.id, task_id, term_task.create_time, term_task.start_time, term_task.end_time, term_task.is_enable,
                term_task.cmd, term_task.value, term_task_detail.sn, IFNULL(send_time,'')send_time, IFNULL(recv_time,'')recv_time,
                term_task_detail.last_time, error_info, $status_field,
                (SELECT SUM(download_size)/SUM(filesize) FROM download_report WHERE task_detail_id = term_task_detail.id)progress")
				->join('INNER JOIN term_task on term_task.id = term_task_detail.task_id')
				->where("term_task_detail.task_id = $task_id")
				->order($this->generate_order_str())
				->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
		}

        $arr = L('VAR_TASK_STATUS_ARR');
        $tta = L('VAR_TASK_TYPE_ARR');
        foreach ($rs as $k => $row) {
            $rs[$k]['finish_time'] = format_time($row['send_time'], $row['recv_time']);
            $rs[$k]['status'] = $arr[$row['status']];
            $rs[$k]['status_o'] = $row['status'];
            $rs[$k]['progress'] = is_null($row['progress']) ? -1 :  round($row['progress']*100, 1);
            $rs[$k]['cmd_text'] = $tta[$row['cmd']];
            $rs[$k]['is_enable_text'] = L($row['is_enable'] == 1 ? 'VAR_ENABLE' : 'VAR_DISABLE');
            if (!empty($row['ext_info'])) {
                $rs[$k]['ext_info'] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $row['ext_info']);
            }
        }

		if ($tid != 0) {
			$total = $m->join('LEFT JOIN term_task ON term_task.id = term_task_detail.task_id')->where($q)->count();
		} else {
			$total = $m->where("term_task_detail.task_id = $task_id")->count();
		}
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );

        if (isset($_REQUEST['sessid'])){
            $this->ajaxReturn($data, '', 0);
        }
        echo json_encode($data);
    }

    // 定时任务任务详情
    public function loadTimedTaskDetail() {
        $tid = I('tid', 0, 'intval');
        $m = M('timed_term_task_detail');
        $status_field = "(CASE timed_term_task_detail.status WHEN 8 THEN 0 ELSE timed_term_task_detail.status END)status";
        //$s表示任务状态status，逗号分隔
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $ugid = $this->getUgid();
        $s = I('tsid', '', 'string');
        $sn = I('sn', '', 'string');
        // $sn不为空：表示在(设备详情-->任务列表-->周期任务)页获取数据，<未使用，使用loadTimedTaskDetail替代>
        // $tid > 0表示查询某个任务的详情列表
        // $tid = -1表示在任务列表页面查看异常或等待执行的任务详情
        if ($sn != '') {
            $q = "timed_term_task_detail.sn = '$sn'";
        } else {
            $q = sprintf('timed_task_id %s AND %s AND %s', ($tid==-1 ? '>0' : "=$tid"), $this->generate_search_str(), ($tid==-1 ? ($uid == 1 ? '1=1' : "timed_term_task.ugid = $ugid") : '2=2'));
            if ($s != '') {
                $q .= " AND timed_term_task_detail.status IN($s)";
            }
        }
        //任务详情
        $rs = $m->field("timed_term_task.is_enable, timed_term_task.cmd, timed_term_task.create_time, timed_term_task.username, timed_term_task.value, timed_term_task_detail.id, timed_term_task.period_type, timed_term_task.period_value,
            timed_task_id, timed_term_task_detail.task_time, timed_term_task_detail.sn, IFNULL(send_time,'')send_time, IFNULL(recv_time,'')recv_time, timed_term_task_detail.last_time,
            $status_field, (SELECT SUM(download_size)/SUM(filesize) FROM download_report WHERE task_detail_id = timed_term_task_detail.id)progress")
            ->join('LEFT JOIN timed_term_task ON timed_term_task.id = timed_term_task_detail.timed_task_id')
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();

        $arr = L('VAR_TASK_STATUS_ARR');
        $tta = L('VAR_TASK_TYPE_ARR');
        foreach ($rs as $k => $row) {
            $rs[$k]['finish_time'] = format_time($row['send_time'], $row['recv_time']);
            $rs[$k]['status'] = $arr[$row['status']];
            $rs[$k]['status_o'] = $row['status'];
            $rs[$k]['progress'] = is_null($row['progress']) ? -1 :  $row['progress']*100;
            $rs[$k]['cmd_text'] = $tta[$row['cmd']];
            $rs[$k]['is_enable_text'] = L($row['is_enable'] == 1 ? 'VAR_ENABLE' : 'VAR_DISABLE');
            if ($sn != '') {
                $rs[$k]['period_value_t'] = get_period_type_text($row['period_type'], $row['period_value']);
                $rs[$k]['task_time_t'] = $row['task_time'] != 0 ? date('Y-m-d H:i:00', strtotime('20'.$row['task_time'].'00')) : '0';
            }
        }
        $total = $m->join('LEFT JOIN timed_term_task ON timed_term_task.id = timed_term_task_detail.timed_task_id')->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );

        if (isset($_REQUEST['sessid'])){
            $this->ajaxReturn($data, '', 0);
        }
        echo json_encode($data);
    }

    // 获取一台设备的定时任务列表
    public function loadTermTimedTask() {
        $m = M('timed_term_task');
        $sn = I('sn', '', 'string');
        $q = "FIND_IN_SET('$sn', sn_list)";
        //任务详情
        $rs = $m->where($q)->order($this->generate_order_str())->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $tta = L('VAR_TASK_TYPE_ARR');
        foreach ($rs as $k => $row) {
            $rs[$k]['cmd_text'] = $tta[$row['cmd']];
            $rs[$k]['period_value_t'] = get_period_type_text($row['period_type'], $row['period_value']);
        }
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );

        if (isset($_REQUEST['sessid'])){
            $this->ajaxReturn($data, '', 0);
        }
        echo json_encode($data);
    }
}