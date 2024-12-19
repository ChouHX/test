<?php
class MobileAction extends CommonAction{
    private function getGroups(){
        $m = M('term_group');
        $q = sprintf("id IN(%s)", $this->getTgids());
        $fields = 'id,name,(SELECT COUNT(*) FROM term_run_info inner join term on term.sn=term_run_info.sn WHERE term.group_id = term_group.id AND term_run_info.is_online = 1 AND TIMESTAMPDIFF(SECOND,term_run_info.last_time,NOW()) < '.C('TERM_OFFLINE_TIME').')online,(SELECT COUNT(*) FROM term WHERE group_id = term_group.id)total';
        $rs = $m->field($fields)->where("$q")->order('id ASC')->select();
		$rs_total = $m->field('(SELECT COUNT(*) FROM term_run_info inner join term on term.sn=term_run_info.sn WHERE term_run_info.is_online = 1 AND TIMESTAMPDIFF(SECOND,term_run_info.last_time,NOW()) < '.C('TERM_OFFLINE_TIME').')online,(SELECT COUNT(*) FROM term)total')->find();
		foreach($rs as $k =>$row){
			if($row['name'] == 'Root'){
				$rs[$k]['id'] = -10;
				$rs[$k]['name'] = "全部分组";
				$rs[$k]['total'] = $rs_total['total'];
				$rs[$k]['online'] = $rs_total['online'];
			}
		}
        return $rs;
    }

    public function checkLogin(){
		$name = I('na', '', 'string');
		$password = I('pa', '', 'string');
        $auto = I('auto', 0, 'intval');
        $s = -1;
		try {
			$m = M('usr');
            $row = $m->where("name='%s' AND password='%s'",array($name,md5($password)))->find();
            if ($row){
                $_SESSION[C('SESSION_NAME')] = $row;
                if ($auto == 1){
                    cookie(C('SESSION_NAME'), json_encode($row), 30*24*3600);
                }
                $s = 0;
            }else{
                $info = L('VAR_LOGIN_ERROR');
            }
		} catch (ThinkException $e) {
            $info = $e->getMessage();
		}
        $this->ajaxReturn('', $info, $s);
    }

    public function mlogin(){
        $ck = cookie(C('SESSION_NAME'));
        if ($ck){
            $ck = json_decode($ck,true);
            $row = M('usr')->where("id=%d",$ck['id'])->find();
            if ($row && $row['password'] == $ck['password']){
                $_SESSION[C('SESSION_NAME')] = $row;
                $this->redirect('Mobile/mterm');
                exit;
            }
        }
        $this->display('mlogin');
    }

    public function mlogout(){
		session(C('SESSION_NAME'), null);
        cookie(C('SESSION_NAME'), null);
        header('Location:'.U('Mobile/mlogin'));
    }
	
	public function editPass(){
		$uid = $_SESSION[C('SESSION_NAME')]['id'];
        $m = M('usr');
        $row = $m->where("id=$uid")->find();
        $pass = I('password','','trim');
        $m->save(array('id'=>$uid, 'password'=>md5($pass)));
        $this->ajaxReturn('',L('VAR_EDIT_PASS_OK'),0);
		
	}

    public function mterm(){
        $this->assign('groups', $this->getGroups());
        $this->display('mterm');
    }

    public function mtask(){
        $this->display('mtask');
    }

    public function mreport(){
        $this->display('mreport');
    }

    public function msetting(){
        $this->display('msetting');
    }

    public function maction(){
		$sns = $_REQUEST['ids'];
		 if (!empty($sns)){
            $dest = str_replace(',', ',  ', $sns);
        }		
		$dest =explode(',',$dest);
		$dest1=array();
		foreach($dest as $k=>$row){
			$tmp = explode('@',$row);
			array_push($dest1,$tmp[0]);
		}
		$dest1 =implode(',',$dest1);
		$this->assign('dest',$dest1);
		$models = C('TERM_MODEL');
		$this->assign('models',$models);
        $this->display('maction');
    }

    public function mterminfo(){
        $id = $_REQUEST['id'];
		$this->assignTermRow($id);
        $this->display('mterminfo');
    }

    public function mtermedit(){
        $this->assign('row', M('term')->where("sn='%s'",$_REQUEST['id'])->field('sn,sim,alias,term_model,group_id')->find());		
        $this->assign('rs', M('term_group')->order('name ASC')->field('id,name')->select());
        $this->display('mtermedit');
    }

    public function mtaskinfo(){
        $row = M('term_task')->where("id=%d",$_REQUEST['id'])->field('id,is_enable,username,cmd,value,create_time,start_time,end_time')->find();
        $row['end_time'] = $row['is_never_expire']==1 ? L('NEVER_EXPIRE'):$row['end_time'];
        //处理value----------------------
        if ($row['cmd'] == 'config_set'){
            $row['value'] = str_replace('<', '&lt;', $row['value']);
            $row['value'] = str_replace('>', '&gt;', $row['value']);
            $row['value'] = str_replace('"', '&quot;', $row['value']);
        }
        if (strpos($row['value'], 'sch_rboot=&quot;0') === 0){
            $row['cmd'] = L('SCHEDULED_REBOOT');
            $row['value'] = L('VAR_TERM_TERM_PARAMS_CLOSE');
        }elseif (strpos($row['value'], 'sch_rboot') === 0){
            $this->displaySrestart($row, $row['value']);
        }else{
            $arr = L('VAR_TASK_TYPE_ARR');
            $row['cmd'] = $arr[$row['cmd']];
        }
        //-------------------------------
        $this->assign('row', $row);
        $this->display('mtaskinfo');
    }

    public function mtaskprogress(){
        $this->display('mtaskprogress');
    }

    public function mdownloadcfg(){
        $this->display('mdownloadcfg');
    }

    public function mupgrade(){
        $this->display('mupgrade');
    }

    public function mschrboot(){
        $this->display('mschrboot');
    }
	
    public function mconfigset(){
        $ids = I('get.term_list','','string');
        $sn = 0;
        if ($ids != '' && strpos($ids,',') === false){
            $sn = M('term')->where("id=$ids")->getField('sn');
        }
        $this->assign('sn', $sn);
        $this->display('mconfigset');
    }

    public function msyscfg(){
        $this->display('msyscfg');
    }
	
	public function mtype(){
		$models = C('TERM_MODEL');
		$this->assign('models',$models);
		$this->display('mtype');
	}
	
	public function msjcj(){
		$this->assign('groups', $this->getGroups());
		$this->display('msjcj');	
	}
	
	public function mrtuinfo(){
		$m = M("term");
		$sn = $_REQUEST['id'];
		$rs = $m->query("SELECT term.sn, term.gateway_sn, term_run_info.is_online, term_run_info.last_time, term_run_info.ip, term_run_info.port,
            rpi.prjname, rpi.name, rpi.address FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn where term.sn ='$sn'");
		$datas = M('rtu_data')->where("sn='$sn'")->field('sn, slave_id, addr, value, report_time')->select();
		$rets = M('rtu_data_set')->field('name,slave_id,addr,unit')->select();
		foreach($datas as $k=>$row){
			$datas[$row['slave_id'].'_'.$row['addr']] =array('value'=>$row['value'],'sn'=>$row['sn']);
		}
		foreach($rets as $k=>$row){
			$rets[$k]['sn'] =$datas[$row['slave_id'].'_'.$row['addr']]['sn']==null?"":$datas[$row['slave_id'].'_'.$row['addr']]['sn'];
			$rets[$k]['value'] =$datas[$row['slave_id'].'_'.$row['addr']]['value']==null?"":$datas[$row['slave_id'].'_'.$row['addr']]['value'];
		}
		$this->assign('rets',$rets);
		$this->assign('rs',$rs[0]);		
        $this->display('mrtuinfo');		
	}
	
	public function mcatchPackage(){
		$this->display('mcatchPackage');
	}
	
	public 	function mrtuScriptSet(){
		$this->display('mrtuScriptSet');
	}
}
