<?php
class MdataAction extends CommonAction{
    private function queryTermIds($qv){
        return M('term')->where("name LIKE '%$qv%' OR alias LIKE '%$qv%'")->getField('id',true,true);
    }

    //分页html
    private function getPagingStr($datasize, &$maxpage){
        $total = ceil($datasize / 5);
        $total = $total<1 ? 1:$total;
        $page = I('page',1,'intval');
        $page = $page<1 ? 1:$page;
        $page = $page>$total ? $total:$page;
        $maxpage = $total;

        if ($page < 1){
            $page = 1;
        }
        if ($total <= 3){
            $start = 1;
            $end = $total;
        }else{
            if ($page <= 2){
                $start = 1;
                $end = 3;
            }elseif($page >= $total-1 ){
                $start = $total - 2;
                $end = $total;
            }else{
                $start = $page - 1;
                $end = $page + 1;
            }
        }
        //&laquo;   &raquo;
        $pstr = '<li class="first-page'. ($page==1?' mui-disabled':'') .'"><a href="#">'.L('FIRST_PAGE').'</a></li>';
        for ($i=$start; $i<=$end; $i++){
            $pstr .= '<li'. ($page==$i?' class="mui-active"':'') .'><a href="#">'.$i.'</a></li>';
        }
        $pstr .= '<li class="last-page'. ($page==$total||$total==0?' mui-disabled':'') .'"><a href="#">'.L('LAST_PAGE').'</a></li>';
        $pstr .= '<li><span>'.sprintf(L('MUI_PAGING_TEXT'),$datasize,$maxpage).'</span></li>';
        return $pstr;
    }

	public function loadTermData(){
        session_write_close();
        $q = 'group_id IN('.$this->getTgids().')';
        if (isset($_REQUEST['gid'])){
            $q .= $_REQUEST['gid']==-10 ? ' AND 1=1' : ' AND group_id = '.$_REQUEST['gid'].'';
		}elseif (isset($_REQUEST['query'])){
            $qv = I('query');
            $q .= " AND (term.sn LIKE '%$qv%' OR term.alias LIKE '%$qv%' OR term.iccid LIKE '%$qv%')";
        }
		$m = M('term');
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'status';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
		$ts = mktime();
        if ($sort == 'status'){
            $orderText = "is_online $order,last_time $order";
        }elseif ($sort == 'term_signal'){
            $orderText = "term.last_time $order,term_signal $order";
        }else{
            $orderText = "$sort $order";
        }
        $rs = $m->query("SELECT term.sn, term.ud_sn, term.alias, term.vsn, term.term_model, term.term_type, term.gateway_sn, term.group_id,
            term.sw_version, term.imei, term.module_vendor, term.module_type, term.sim, term.imsi, term.iccid, term.operator,
            term.frequency, term.wifi_ssid, term.host_sn,
            B.ip, B.port, B.is_online, B.protocol, B.first_login, B.login_time, B.last_time, B.net_mode, B.term_signal, B.flux, B.month_flux,
            B.last_7days_flux, B.virtual_ip, B.virtual_is_online, B.virtual_last_time FROM term
            JOIN term_run_info AS B ON B.sn = term.sn WHERE $q ORDER BY $orderText LIMIT ".$rp*($page-1).",$rp");
		// dump($rs);
		// die($m->_sql());
		if (empty($rs[0])){
    		$total = 0;
    		$data = array();
    	}else{
			$total = $m->where($q)->count();
			foreach ($rs as $k=>$row){
                $data[$k] = $row;
				$data[$k]['diff'] =$ts - strtotime($row['last_time']);
				//终端是否在线
                $tmpStatus = get_term_status_code($data[$k]['diff'], $row['is_online']);
                $data[$k]['status'] = $tmpStatus;
                $data[$k]['status_original'] = $tmpStatus;

				//信号量图标
                $data[$k]['signal'] = $row['term_signal'];
				$data[$k]['term_signal'] = get_term_signal_str($tmpStatus, $row['term_signal']);
				$data[$k]['tmpStatus'] = $tmpStatus;
			}
		}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
	}

	public function loadLoraData(){
		session_write_close();
        $q = 'group_id IN('.$this->getTgids().')';
        if (isset($_REQUEST['gid'])){
           $q .= $_REQUEST['gid']==-10 ? ' AND 1=1' : ' AND group_id = '.$_REQUEST['gid'].'';
		}elseif (isset($_REQUEST['query'])){
            $qv = I('query');
            $q .= " AND (term.sn LIKE '%$qv%')";
        }
		$m = M('term');
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'status';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
		$ts = mktime();
        if ($sort == 'status'){
            $orderText = "is_online $order,last_time $order";
        }else{
            $orderText = "$sort $order";
        }
		$sql = "SELECT term.sn, term.gateway_sn, term_run_info.is_online, term_run_info.last_time, term_run_info.ip, term_run_info.port, term_run_info.status AS status_limit,
            rpi.prjname, rpi.name, rpi.address FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn
            WHERE $q ORDER BY $orderText LIMIT ".$rp*($page-1).",$rp";
        $rs = $m->query($sql);
		if (empty($rs[0])){
    		$total = 0;
    		$data = array();
    	}else{
			$total = $m->where($q)->count();
			foreach ($rs as $k=>$row){
				$data[$k] = $row;
				$s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
				$data[$k]['status'] = get_term_status_str($s, $row['status_limit']);
			}
		}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
	}

	public function loadTaskData(){
        session_write_close();
        $tname = "term_task";
        $tdname = $tname.'_detail';
		$m = M($tname);

    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'desc';

        $q = 'task_type=0';
    	if ($_SESSION[C('SESSION_NAME')]['id'] != 1){
    		$q .= ' AND ugid='.$this->getUgid();
    	}

        //通过出厂sn，查询一个路由器的任务
        $qv = I('query','','trim');
        if ($qv == '' || strlen($qv) < 8){
            $B = "SELECT id AS task_id FROM $tname";
        }else{
            $ids = $this->queryTermIds($qv);
            $B = "SELECT DISTINCT(task_id) FROM $tdname WHERE tid IN($ids)";
        }

        $rs = $m->query("SELECT A.* FROM $tname A INNER JOIN ($B)B ON A.id=B.task_id WHERE $q ORDER BY $sort $order LIMIT ".($page-1)*$rp.",$rp");
		if (empty($rs[0]['id'])){
    		$total = 0;
    		$data = array();
    	}else{
			$arr1 = L('VAR_TASK_TYPE_ARR');
            $rs2 = $m->query("SELECT COUNT(*)cc FROM $tname A INNER JOIN ($B)B ON A.id=B.task_id WHERE $q");
            $total = $rs2[0]['cc'];
            $cmdHasProcoess = array('download_ad', 'upgrade', 'upgrade_udp', 'upgrade_tcp', 'upgrade_camera');
			foreach ($rs as $k=>$row){
				$data[$k] = array(
                    'id'          => $row['id'],
                    'is_enable'   => $row['is_enable'],
                    'username'    => $row['username'],
                    'cmd' 		  => $arr1[$row['cmd']],
					'create_time' => $row['create_time'],
                    'progress'    => in_array($row['cmd'], $cmdHasProcoess, true)
				);
                if(strpos($row['value'], 'sch_rboot') === 0){
                    $data[$k]['cmd'] = L('SCHEDULED_REBOOT');
                    if (strpos($row['value'], 'sch_rboot="0') === 0){
                        $data[$k]['cmd'] .= ' ('.L('VAR_TERM_TERM_PARAMS_CLOSE').')';
                    }
                }
			}
		}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
	}

    public function loadTaskDetail(){
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'last_time';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'desc';

        $tname = I('get.task_type', 'term_task', 'string');
        $tdname = $tname.'_detail';
        // $fieldname = $tname=='term_task' ? 'tid' : 'rtu_id';
        $m = M($tdname);
        $q = 'task_id='.$_REQUEST['task_id'];
        $sql = "SELECT $tdname.id, task_id,  IFNULL(send_time,'')send_time, IFNULL(recv_time,'')recv_time, $tdname.last_time, send_count, IFNULL(error_info,'')error_info, status, term.sn FROM $tdname
        INNER JOIN term ON term.sn=$tdname.sn WHERE %s ORDER BY $sort $order LIMIT ".($page-1)*$rp.",$rp";

        //将实际升级成功的任务 status值修改为3
        $cmd = $_REQUEST['cmd_o'];
        if ($cmd == 'upgrade' || $cmd == 'upgrade_tcp' || $cmd == 'upgrade_udp'){
            $rs = $m->query("SELECT a.id FROM term_task_detail a INNER JOIN download_report b ON b.task_detail_id=a.id
            WHERE a.task_id = {$_REQUEST['task_id']} AND a.status IN(2,4,6) AND b.download_size=b.filesize");
            foreach ($rs as $k=>$row){
                $ids[] = $row['id'];
            }
            if (isset($ids)){
                $ret = $m->execute("UPDATE term_task_detail SET status=3 WHERE id IN(".implode(',',$ids).")");
            }
        }
    	$rs = $m->query(sprintf($sql, $q));
    	if (empty($rs[0]['id'])){
    		$total = 0;
    		$data = array();
    	}else{
    		$total = $m->where($q)->count();
    		$arr = L('VAR_TASK_STATUS_ARR');
            $green = '<font color="#0CEF0C">%s</font>';
            $red = '<font color="red">%s</font>';
    		foreach ($rs as $k=>$row){
                $data[$k] = $row;
                $data[$k]['finish_time'] = format_time($row['send_time'], $row['recv_time']);
                if ($row['status'] == 3){
                    $data[$k]['status'] = sprintf($green, $arr[$row['status']]);
                }elseif($row['status'] == 4 || $row['status'] == 6){
                    $data[$k]['status'] = sprintf($red, $arr[$row['status']]);
                }else{
                    $data[$k]['status'] = $arr[$row['status']];
                }
                $data[$k]['status_o'] = $row['status'];
                if (!empty($row['ext_info'])){
                    $data[$k]['ext_info'] = str_replace('<', '&lt;', $data[$k]['ext_info']);
                    $data[$k]['ext_info'] = str_replace('>', '&gt;', $data[$k]['ext_info']);
                }
    		}
    	}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
    }

    //文件下载进度
	public function loadAdDownloadDetail(){
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;

		$tid = $_REQUEST['tid'];
        $router_id = $_REQUEST['router_id'];
		$m = M('download_report');

        $q = "task_detail_id IN(SELECT id FROM term_task_detail WHERE task_id=$tid) AND download_type=0";

        //查询一个路由器的任务(通过出厂sn)
        if (isset($_REQUEST['qv'])){
            $ids = $this->queryTermIds($_REQUEST['qv']);
            $q .= " AND sn IN($ids)";
        }

        if (isset($_REQUEST['router_id'])){
            $q .= " AND sn=$router_id";
        }

		$rs = $m->query("SELECT a.*,term.sn,file_list.original_filename AS filename FROM download_report AS a INNER JOIN term ON term.sn=a.sn INNER JOIN file_list ON file_list.id=a.fileid WHERE $q ORDER BY fileid LIMIT ".($page-1)*$rp.",$rp");
		if (empty($rs[0]['id'])){
    		$total = 0;
    		$data = array();
    	}else{
    		$total = $m->where($q)->count();
    		$arr = L('VAR_TASK_STATUS_ARR');
    		foreach ($rs as $k=>$row){
    			$data[$k] = array(
					'name'     => $row['sn'],
					'filename' => $row['filename'],
                    'progress' => intval($row['download_size'] / $row['filesize'] * 100),
                    'filesize' => bitsize($row['filesize']),
                    'download_size' => bitsize($row['download_size'])
    			);
    		}
    	}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
	}

    //配置文件
    public function loadCfgData(){
		$m  = M('file_list');
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'original_filename';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';

    	if ($_SESSION[C('SESSION_NAME')]['id'] == 1){
    		$q = 'filetype=2 AND finish_status = 1';
    	}else{
    		$q = 'filetype=2 AND finish_status = 1 AND ugid='.$this->getUgid();
    	}

		$rs = $m->query("SELECT * FROM file_list WHERE $q ORDER BY $sort $order LIMIT ".($page-1)*$rp.",$rp");
		if (empty($rs[0]['id'])){
    		$total = 0;
    		$data = array();
    	}else{
			$total = $m->where($q)->count();
			foreach ($rs as $k=>$row){
                $data[$k] = $row;
                $data[$k]['original_filename'] = empty($row['original_filename']) ? $row['filename'] : $row['original_filename'];
                $data[$k]['filesize'] = bitsize($row['filesize']);
                $data[$k]['filesize_o'] = $row['filesize'];
                $data[$k]['md5_num'] = $row['md5_num'];
			}
		}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
    }

    //升级包
    public function loadUpgradePackageData(){
        $filetype = $_REQUEST['filetype'];
    	$m  = M('file_list');
    	$page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    	$rp    = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
    	$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'name';
    	$order = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';

    	if ($_SESSION[C('SESSION_NAME')]['id'] == 1){
    		$q = 'filetype='.$filetype;
    	}else{
    		$q = 'filetype='.$filetype.' AND ugid='.$this->getUgid();
    	}

    	$rs = $m->where($q)->order("$sort $order")->limit(($page-1)*$rp, $rp)->select();
    	if (empty($rs[0]['id'])){
    		$total = 0;
    		$data = array();
    	}else{
    		$total = $m->where($q)->count();
    		foreach ($rs as $k=>$row){
                $data[$k] = $row;
                $data[$k]['filesize'] = bitsize($row['filesize']);
                $data[$k]['filesize_o'] = $row['filesize'];
    		}
    	}
        $pstr = $this->getPagingStr($total, $maxpage);
        $this->ajaxReturn(array('rows'=>$data, 'pstr'=>$pstr, 'maxpage'=>$maxpage), '', 0);
    }
}