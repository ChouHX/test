<?php
class SyscfgAction extends CommonAction{
    // 升级包解压后的文件夹
    private $version_dir_path = null;
    private $record_d = null;

    // 输出升级日志(1.输出到屏幕，2.输出到文件)
    private function write_upgrade_log($log, $str, $is_failed = false, $is_finish = false) {
        $sep = "\t";
        $wrap = IS_WIN ? "\r\n" : "\n";
        $tm = date('Y-m-d H:i:s');
        $log->mwrite($tm.$sep.iconv('utf-8', 'GBK', $str).$wrap);
        if (!IS_WIN) {
            echo str_pad(' ', 4096);
        }
        echo sprintf('<span class="tm">%s</span>%s<br>', $tm, $str);
        if ($is_failed || $is_finish) {
            // 删除版本升级包解压后的文件夹
            if (is_dir($this->version_dir_path)) {
                exec("RD /S /Q {$this->version_dir_path}", $output, $ret_rd);
                if ($ret_rd != 0) {
                    $str2 = sprintf(L('REMOVE_DIR_FAILED'), $this->version_dir_path);
                    $log->mwrite($tm.$sep.iconv('utf-8', 'GBK', $str2).$wrap);
                    echo sprintf('<span class="tm">%s</span>%s<br>', $tm, $str2);
                }
            }
            // 成功或失败，都写入升级记录
            M('system_upgrade_record')->add($this->record_d);

            if ($is_finish) {
                // 成功，更新版本号
                M('system_config')->where("name = 'swu_version'")->save(array('value' => $this->record_d['new_version']));
            } else {
                // 失败，退出程序
                $str3 = L('UPGRADE_FAILED_TIPS');
                $log->mwrite($tm.$sep.iconv('utf-8', 'GBK', $str3).$wrap);
                die(sprintf('<span class="tm">%s</span>%s</body></html>', $tm, $str3));
            }
        }
        ob_flush();
        flush();
    }

    //用户列表
    public function yhlb(){
        $this->assign('web_path_1', array(L('VAR_MENU_USER_LIST')));
        $this->display('yhlb');
    }

    //用户组列表
    public function yhzlb(){
        $this->assign('web_path_1', array(L('VAR_USER_GROUP_LIST')));
        $this->display('yhzlb');
    }

    //路由器分组数据
    public function loadTermGroup(){
        $m = M('term_group');
        $q = sprintf('id != 1 AND %s AND id IN(%s)', $this->generate_search_str(), $this->getTgids());
        $rs = $m->field("term_group.*, '--' as operation")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k => $row) {
            $rs[$k]['day_flux_limit'] = $row['day_flux_limit']/1024;
            $rs[$k]['month_flux_limit'] = $row['month_flux_limit']/1024;
            $rs[$k]['device_month_flux_limit'] = $row['device_month_flux_limit']/1024;
            $rs[$k]['device_day_flux_limit'] = $row['device_day_flux_limit']/1024;
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

    // 分组规则
    public function loadGroupRules() {
        $m = M('term_group_rule');
        $q = $this->generate_search_str();
        $rs = $m->field("term_group_rule.*, term_group.name AS gname")
            ->join("LEFT JOIN term_group ON term_group.id = term_group_rule.group_id")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $arr = array(
            'ud_sn' =>  L('VAR_SN1'),
            'vsn'   =>  L('VAR_VSN'),
            'sw_version' =>  L('VAR_SWV'),
            'imei'   =>  'IMEI',
            'imsi'   =>  'IMSI',
            'iccid'  =>  'ICCID'
        );
        foreach ($rs as $key => $row) {
            $rs[$key]['rule_type_text'] = $arr[$row['rule_type']];
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

    // 此处仅为一个壳函数，为了节点权限控制。用于分组规则页面，添加规则
    public function groupRulesAdd() {
        $this->groupRulesOperationg();
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于分组规则页面，修改规则
    public function groupRulesEdit() {
        $this->groupRulesOperationg();
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于分组规则页面，删除规则
    public function groupRulesDel() {
        $this->groupRulesOperationg();
    }

    // 运行分组规则
    // rule_id为规则id，如果不传则查询全部规则
    // is_ret表示是否需要AJAX返回，在新增/编辑规则时调用此函数，可设置为0
    // 三处调用：1.手动点击运行规则；2.新增/编辑规则；3.cgi调用(暂未实现)
    // from：=1表示类中调用，=2表示页面调用，=3表示cgi调用
	// ------特别注意：只有在Default分组的设备才会被分组 ------
    public function execGroupRule($rule_id = 0, $is_ret = 1, $from = 2) {
        if ($from == 2) {
            $tgids = $this->getTgids('arr');
            if (!in_array(2, $tgids)) {
                $this->ajaxReturn(0, L('RULE_EXEC_TIPS'), 0);
            }
        }
        if ($rule_id == 0) {
            $rule_id = I('rule_id', 0, 'intval');
        }
        $rules = M('term_group_rule')->where($rule_id == 0 ? '1 = 1' : "id = $rule_id")->field('rule_type, rule_key, group_id')->order('id ASC')->select();
        if (!$rules) {
            $this->ajaxReturn('', L('NO_RULE_TIPS'), -1);
        }
        $fields = array('ud_sn', 'vsn', 'sw_version', 'imei', 'imsi', 'iccid');
        $rs = M('term')->field('sn,'.implode(',', $fields))->where('group_id = 2')->select();
        $num = 0;
        foreach ($rs as $key => $row) {
            foreach ($rules as $rule) {
                $field = $rule['rule_type'];
                $keyword = $rule['rule_key'];
                if (!empty($row[$field]) && strpos($row[$field], $keyword) !== false) {
                    $moves[$rule['group_id']][] = $row['sn'];
                    $num += 1;
                    break;
                }
            }
        }
        if (isset($moves)) {
            foreach ($moves as $gid => $sns) {
                $q = "sn IN('".implode("','", $sns)."')";
                M('term')->where($q)->save(array('group_id' => $gid));
            }
        }
        if ($is_ret) {
            $this->ajaxReturn($num, L('RULE_EXEC_TIPS'), 0);
        }
    }

    // 分组规则增删改
    private function groupRulesOperationg() {
        $act = I('act');
        if ($act != 'delete') {
            $tgids = $this->getTgids('arr');
            if (!in_array(2, $tgids)) {
                $this->ajaxReturn('', L('GROUP_RULE_ADD_EDIT_TIPS'), -1);
            }
        }
        $id = I('id', 0, 'intval');
        $d = array(
            'rule_type' => I('rule_type', 'ud_sn', 'string'),
            'rule_key' => I('rule_key', '', 'string'),
            'group_id' => I('group_id', 2, 'intval'),
            'info' => I('info', '', 'string'),
            'is_enable' => I('is_enable', 1, 'intval')
        );
        $m = M('term_group_rule');
        if ($act == 'add') {
            $m->add($d);
            // call_user_func_array(array(A('Cgi'), 'execGroupRule'), array('rule_id' => $m->getLastInsID(), 'is_ret' => 0));
            $this->execGroupRule($m->getLastInsID(), 0, 1);
        } elseif ($act == 'edit') {
            $m->where("id = $id")->save($d);
            // call_user_func_array(array(A('Cgi'), 'execGroupRule'), array('rule_id' => $id, 'is_ret' => 0));
            $this->execGroupRule($id, 0, 1);
        } elseif ($act == 'delete') {
            $m->where("id = $id")->delete();
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //用户数据
    public function loadUsers(){
        $m = M('usr');
        $q = sprintf('%s', $this->generate_search_str());
        $rs = $m->field("usr.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $rs2 = M('usr_group')->field('id,name')->select();
        foreach ($rs2 as $k => $row) {
            $gnames[$row['id']] = $row['name'];
        }
        $utt = L('VAR_USER_TYPE_TEXT');
        foreach ($rs as $k => $row) {
            $rs[$k]['gname'] = $gnames[$row['gid']];
            $rs[$k]['usr_type'] = $utt[$row['usr_type']];
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

    //角色(用户组)数据
    public function loadRoles(){
        $m = M('usr_group');
        $q = sprintf('1 = 1 AND %s', $this->generate_search_str());
        $rs = $m->field("usr_group.*, '--' as operation")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $rs2 = M('usr')->field('id,name,gid')->order('gid asc')->select();
        foreach ($rs2 as $k => $row) {
            $users[$row['gid']][] = $row['name'];
        }
        unset($rs2);
        $rs3 = M('usr_group_privilege')->field('ugid,tgid,name')->join('INNER JOIN term_group ON term_group.id = usr_group_privilege.tgid')->order('ugid asc')->select();
        foreach ($rs3 as $k => $row) {
            $privileges[$row['ugid']][] = $row['name'];
        }
        unset($rs3);
        foreach ($rs as $k => $row) {
            $rs[$k]['user_list'] = isset($users[$row['id']]) ? implode(', ', $users[$row['id']]) : '--';
            $rs[$k]['privileges'] = isset($privileges[$row['id']]) ? implode(', ', $privileges[$row['id']]) : '--';
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

    //获取模态框html
    public function getModalHtml(){
        $tpl_id = trim($_REQUEST['tpl_id']);
        $id = $_REQUEST['id'];
        $pid = $_REQUEST['pid'];
        $name = $_REQUEST['name'];
        $tpl = '';
        if ($tpl_id != ''){
            $tpl = $this->buildHtml($tpl_id, './Runtime/Temp/', './Tpl/Syscfg/modal/'.$tpl_id.'.html');
            if ($tpl_id == 'term_group_add'){
                $nodes = A('Term')->getTermGroupTreeNodes(true, 1);
                $tpl = sprintf($tpl, json_encode($nodes), $nodes[0]['id']);
            }elseif ($tpl_id == 'term_group_edit'){
                $nodes = A('Term')->getTermGroupTreeNodes(true, 1);
                //不能将一个组的父节点设置为自己下级
                $exclude = explode(',', $this->getSubTermGroupIds($id));
                foreach ($nodes as $k => $row) {
                    if (in_array($row['id'], $exclude, true)){
                        unset($nodes[$k]);
                    }
                }
                $nodes = array_values($nodes);
                $tpl = sprintf($tpl, $id, I('get.month_flux_limit',0,'intval'), I('get.day_flux_limit',0,'intval'), I('get.device_month_flux_limit',0,'intval'), I('get.device_day_flux_limit',0,'intval'), $name, json_encode($nodes), $pid);
            }elseif ($tpl_id == 'usr_add'){
                $tpl = sprintf($tpl, $this->getUsrGroupStr(isset($_REQUEST['gid']) ? $_REQUEST['gid'] : 0), $this->getUsrTypeStr(), json_encode(get_page_nodes()));
            }elseif ($tpl_id == 'usr_edit'){
                $row = M('usr')->where("id = $id")->find();
                $tpl = sprintf($tpl, $row['id'], $row['name'], $row['id']==1?'disabled':'', $this->getUsrGroupStr($row['gid']), $row['id']==1?'disabled':'',
                    $this->getUsrTypeStr($row['usr_type']), $row['email'], $row['sim'], $row['info'], json_encode(get_page_nodes($id))
                );
            }elseif ($tpl_id == 'usr_group_edit'){
                $privileges = M('usr_group_privilege')->where("ugid = $id")->getField('tgid',true);
                $tpl = sprintf($tpl, $id, $_REQUEST['name'], json_encode($privileges ? $privileges : array()));
            }elseif ($tpl_id == 'usr_group_add_user'){
                $tpl = sprintf($tpl, $id, $_REQUEST['name'], $this->getUserStr($id));
            }elseif ($tpl_id == 'app_server_edit'){
                $tpl = sprintf($tpl, $id, M('app_server')->where("id=$id")->getField('info'));
            } elseif ($tpl_id == 'group_rule_add') {
                $nodes = A('Term')->getTermGroupTreeNodes(true);
                $tpl = sprintf($tpl, json_encode($nodes), $nodes[0]['id']);
            } elseif ($tpl_id == 'group_rule_edit') {
                $nodes = A('Term')->getTermGroupTreeNodes(true);
                $row = M('term_group_rule')->where("id = $id")->find();
                $tpl = sprintf($tpl, $row['id'], $row['rule_key'], $row['info'], $row['rule_type'], $row['is_enable'], json_encode($nodes), $row['group_id']);
            }
        }
        echo $tpl;
    }

    //角色权限，checkbox格式
    private function getPrivilegesStr($ugid, $tpl_id){
        $rs = M('term_group')->where('id<>1')->field('id,name')->order('id ASC')->select();
        function _mysort($a,$b){
            if ($a['id'] == 2) return -1;
            if ($a['name'] == $b['name']) return 0;
            return $a['name'] < $b['name'] ? -1 : 1;
        }
        usort($rs, '_mysort');
        $privileges = M('usr_group_privilege')->where("ugid = $ugid")->getField('tgid',true);
        $privileges_str = '';
        foreach ($rs as $k=>$row) {
            $checked = $tpl_id=='usr_group_edit' && in_array($row['id'], $privileges, true) ? 'checked' : '';
            $privileges_str .= '<div class="checkbox-inline"><label><input type="checkbox" '.$checked.' name="privileges[]" value="'.$row['id'].'">'.$row['name'].'</label></div>';
        }
        return $privileges_str;
    }

    //用户列表，checkbox格式
    private function getUserStr($ugid){
        $rs = M('usr')->field('id,name')->where("gid != $ugid")->select();
        function _mysort($a,$b){
            if ($a['id'] == 1) return -1;
            if ($a['name'] == $b['name']) return 0;
            return $a['name'] < $b['name'] ? -1 : 1;
        }
        usort($rs, '_mysort');
        $str = '';
        foreach ($rs as $k=>$row) {
            $str .= '<div class="checkbox-inline"><label><input type="checkbox" name="users[]" value="'.$row['id'].'">'.$row['name'].'</label></div>';
        }
        return $str;
    }

    //用户组，select格式
    private function getUsrGroupStr($gid = 0){
        $rs = M('usr_group')->field('id,name')->select();
        function _mysort($a,$b){
            if ($a['id'] == 1) return -1;
            if ($a['name'] == $b['name']) return 0;
            return $a['name'] < $b['name'] ? -1 : 1;
        }
        usort($rs, '_mysort');
        $str = '';
        foreach ($rs as $k => $row) {
            $selected = $row['id']==$gid ? 'selected' : '';
            $str .= '<option '.$selected.' value="'.$row['id'].'">'.$row['name'].'</option>';
        }
        return $str;
    }

    //用户类型，select格式
    private function getUsrTypeStr($usr_type = -1){
        $usr_type = intval($usr_type);
        $rs = L('VAR_USER_TYPE_TEXT');
        if ($usr_type != 0){
            $rs = array_slice($rs, 1, 2, true);
        }
        foreach ($rs as $k => $v) {
            $selected = $k == $usr_type ? 'selected' : '';
            $str .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
        }
        return $str;
    }

    //添加路由器分组
    public function termGroupAdd(){
        $m = M('term_group');
        $name = $_REQUEST['name'];
        $c = $m->where("name='%s'",$name)->count();
        if ($c != 0){
            $ret = array('info'=>L('NAME_EXIST'), 'status'=>-1, 'success'=>true);
        }else{
            $ret = $m->add(array(
                'name' => $name,
                'pid' => I('pid', 0, 'intval'),
                'month_flux_limit' => I('month_flux_limit',0,'intval') * 1024,
                'day_flux_limit' => I('day_flux_limit',0,'intval') * 1024,
                'device_month_flux_limit' => I('device_month_flux_limit',0,'intval') * 1024,
                'device_day_flux_limit' => I('device_day_flux_limit',0,'intval') * 1024,
                'creator' => $_SESSION[C('SESSION_NAME')]['name']
            ));
            if ($ret){
                $this->enableFluxLimit();
                $this->wlog('', 'term_group_add', '', 'ids='.$ret);
                //用户新建一个终端组后，(该用户所在的用户组)拥有该终端组的权限
                $ugid = $this->getUgid();
                $m->execute("INSERT INTO usr_group_privilege (ugid,tgid)VALUES($ugid,$ret)");
                $ret = array('data'=>'', 'info'=>L('VAR_MSG_TG_ADD_OK'), 'status'=>0, 'success'=>true);
            }else{
                $ret = array('data'=>'', 'info'=>L('VAR_MSG_TG_ADD_ERROR'), 'status'=>-2, 'success'=>true);
            }
        }
        die(json_encode($ret));
    }

    //查看是否还有分组设置了流量(月/日)限制，如果没有就在system_config表中关闭对应的功能
    private function enableFluxLimit(){
        $m = M('term_group');
        $c = $m->where('month_flux_limit != 0')->count();
        $ret = $m->execute(sprintf("UPDATE system_config SET value = '%d' WHERE name = 'enable_month_flux_limit'", $c>0?1:0));
        $c2 = $m->where('day_flux_limit != 0')->count();
        $ret2 = $m->execute(sprintf("UPDATE system_config SET value = '%d' WHERE name = 'enable_day_flux_limit'", $c2>0?1:0));
        if ($ret || $ret2){
            $this->sendUdpMessage('cmd=sync_setting');
        }
    }

    //编辑路由器分组
    public function termGroupEdit(){
        $m = M('term_group');
        $id = I('id');
        $name = I('name');
        $c = $m->where("id<>%d AND name='%s'",array($id,$name))->count();
        if ($c != 0){
            $ret = array('data'=>'', 'info'=>L('NAME_EXIST'), 'status'=>-1, 'success'=>true);
        }else{
            $pid = I('pid', 0, 'intval');
            $d = array(
                'id' => $id,
                'name' => $name,
                'month_flux_limit' => I('month_flux_limit',0,'intval')*1024,
                'day_flux_limit' => I('day_flux_limit',0,'intval')*1024,
                'device_month_flux_limit' => I('device_month_flux_limit',0,'intval') * 1024,
                'device_day_flux_limit' => I('device_day_flux_limit',0,'intval') * 1024
            );
            if ($pid != 0){
                $d['pid'] = $pid;
            }
            if ($m->save($d)){
                $this->enableFluxLimit();
                $this->wlog('', 'term_group_edit', '', 'ids='.$id);
            }
            $ret = array('data'=>$name, 'info'=>L('VAR_MSG_TG_EDIT_OK'), 'status'=>0, 'success'=>true);
        }
        die(json_encode($ret));
    }

    //删除路由器分组，只有admin有权限
    public function termGroupDel(){
        $m  = M('term_group');
        $id = $_REQUEST['id'];
        if ($id == 1 || $id == 2){
            $this->ajaxReturn('', L('VAR_CAN_NOT_DEL'), -1);
        }
        //将分组下的设备转移到Default组
        M('term')->where("group_id IN(%s)", $this->getSubTermGroupIds($id))->save(array('group_id' => 2));
        if ($m->where("id=$id")->delete()){
            $this->enableFluxLimit();
            $this->wlog('', 'term_group_delete', '', 'ids='.$id);
        }
        $this->ajaxReturn('', L('VAR_MSG_TG_DEL_OK'), 0);
    }

    //新增角色
    public function userGroupAdd(){
        $name = I('name');
        $tgids = explode(',', $_REQUEST['privileges']);
        $m = M('usr_group_privilege');
        if (M('usr_group')->where("name='$name'")->count() != 0){
            $this->ajaxReturn('', L('NAME_EXIST'), -1);
        }
        $m->execute("INSERT INTO usr_group (name)VALUES('$name')");
        $insertId = $m->getLastInsID();
        foreach ($tgids as $v){
            $dall[] = array(
                'ugid' => $insertId,
                'tgid' => $v
            );
        }
        if (isset($dall)){
            $m->addAll($dall);
        }
        $this->wlog('', 'user_group_add', 'name='.$name, 'ids='.$insertId);
        $this->ajaxReturn($d, L('VAR_USER_GROUP_ADD_OK'), 0);
    }

    //编辑角色
    public function userGroupEdit(){
        $id = I('id');
        $name = I('name');
        $tgids = explode(',', $_REQUEST['privileges']);
        $m = M('usr_group_privilege');
        if (M('usr_group')->where("id<>$id AND name='$name'")->count() != 0){
            $this->ajaxReturn('', L('NAME_EXIST'), -1);
        }
        foreach ($tgids as $v){
            $dall[] = array(
                'ugid' => $id,
                'tgid' => $v
            );
        }
        if ($m->execute("UPDATE usr_group SET name='$name' WHERE id=$id")){
            $this->wlog('', 'user_group_edit', 'name='.$name, 'ids='.$id);
        }
        $m->where("ugid=$id")->delete();
        if (isset($dall)){
            $m->addAll($dall);
        }
        $this->ajaxReturn(array('id'=>$id,'name'=>$name), L('VAR_USER_GROUP_EDIT_OK'), 0);
    }

    //删除角色
    public function userGroupDelete(){
        $name = I('name');
        $id = I('id',0,'intval');
        $m = M('usr_group');
        if ($m->where("id != 1 AND id = $id")->delete()){
            $this->wlog('', 'user_group_delete', 'name='.$name, 'ids='.$id);
        }
        $this->ajaxReturn('', L('VAR_USER_GROUP_DEL_OK'), 0);
    }

    //给角色添加用户
    public function userGroupAddUser(){
        $id = I('id');
        $name = I('name');
        $users = implode(',',$_REQUEST['users']);
        if (M('usr')->where("id IN(%s)",$users)->save(array('gid'=>$id))){
            $this->wlog('', 'user_group_add_user', 'role name='.$name, 'ids='.$users);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //新增用户
    public function userAdd(){
        $m = M('usr');
        $name = I('name','','trim');
        if ($m->where("name='%s'",$name)->count() != 0){
            $this->ajaxReturn('',L('NAME_EXIST'),-1);
        }
        $d = array(
            'name'     => $name,
            'gid'      => I('gid',1,'intval'),
            'usr_type' => I('usr_type', 2, 'intval'),
            'password' => md5(I('pwd')),
            'email'    => I('email', '', 'trim'),
            'sim'      => I('sim', '', 'trim'),
            'info'     => I('info','', 'trim'),
            'head'     => '01.png',
        );
        if ($m->add($d)) {
            $nodes = I('nodes', '', 'trim');
            if ($nodes != '') {
                $node_ids = explode(',', $nodes);
                $usr_id = $m->getLastInsID();
                foreach ($node_ids as $node_id) {
                    $datas[] = array('usr_id' => $usr_id, 'node_id' => $node_id);
                }
                M('usr_permission')->addAll($datas);
            }
            $this->wlog('', 'user_add', 'name='.$name, 'ids='.$usr_id.'&nodes='.$nodes);
        }
        $this->ajaxReturn('', L('VAR_MSG_USER_ADD_OK'), 0);
    }

    //编辑用户
    public function userEdit(){
        $m = M('usr');
        $id = I('id', 0, 'intval');
        $d = array(
            'id'       => $id,
            'gid'      => $id==1 ? 1 : I('gid',1,'intval'),
            'usr_type' => $id==1 ? 0 : I('usr_type', 2, 'intval'),
            'email'    => I('email', '', 'trim'),
            'sim'      => I('sim', '', 'trim'),
            'info'     => I('info','', 'trim'),
        );
        $p = I('pwd','','trim');
        if (!empty($p)){
            $d['password'] = md5($p);
        }
        $ret1 = $m->save($d);
        $nodes = I('nodes', '', 'trim');
        if ($id != 1) {
            $node_ids = explode(',', $nodes);
            foreach ($node_ids as $node_id) {
                $datas[] = array('usr_id' => $id, 'node_id' => $node_id);
            }
            M('usr_permission')->where('usr_id = %d', $id)->delete();
            if (isset($datas)) {
                $ret2 = M('usr_permission')->addAll($datas);
            }
        }
        if ($ret1 || $ret2) {
            $this->wlog('', 'user_edit', '', 'ids='.$id.'&nodes='.$nodes);
        }
        $this->ajaxReturn('', L('VAR_MSG_USER_EDIT_OK'), 0);
    }

    //删除用户
    public function userDel(){
        $m = M('usr');
        $ids = $_REQUEST['ids'];
        $q = "id IN($ids) AND id <> 1";
        $names = $m->where($q)->getField('name',true);
        if ($m->where($q)->delete()){
            $this->wlog('', 'user_delete', 'names='.implode(',',$names), 'ids='.$ids);
        }
        $this->ajaxReturn('', L('VAR_MSG_USER_DEL_OK'), 0);
    }

    //资源文件列表
    public function loadFiles(){
        $m = M('file_list');
        $filetype = I('filetype',0,'intval');
        $ad_id = I('ad_id',0,'intval');
        $sn = I('sn','','string');
        $ugid = $this->getUgid();
        $q = sprintf('filetype = %d AND %s AND %s AND finish_status = 1', $filetype, $this->generate_search_str(), $_SESSION[C('SESSION_NAME')]['id'] == 1 ? '2=2' : "ugid=$ugid");
        if ($sn != ''){
            $q .= " AND filename LIKE '{$sn}_%'";
        }
        if ($ad_id != 0) {
            $q .= " AND id IN(SELECT file_list_id FROM ad_file WHERE ad_id = $ad_id)";
        }
        $rs = $m->field("file_list.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k => $row) {
            $rs[$k]['filesize_o'] = $row['filesize'];
            $rs[$k]['filesize'] = bitsize($row['filesize']);
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
            $this->ajaxReturn($data,'ok',0);
        }
        echo json_encode($data);
    }

    //添加文件到file_list
    public function addResFile(){
        $filetype = I('filetype',0,'intval'); //文件类型
        $info = I('info');
        $f = $_FILES['filedata'];
        if ($f['error'] != 0){
            $this->ajaxReturn('', $this->getUploadErrorMsg($f['error']), -1, -1);
        }

        if (!is_dir($dest_dir)){
            @mkdir($dest_dir, 0777);
        }
        $ext = getFileExt($f['name']);
        $prefix = 'unknown'; //重定义文件名前缀
        $success_str = 'OK';
        $relative_path = '';

        if ($filetype == 1){
            $log_act = 'package_add';
            $success_str = L('VAR_MSG_PACKAGE_ADD_OK');
            $prefix = 'package';
            $dest_dir = C('FTP_WEB_PACK_PATH').'upgrade';
            $relative_path = 'upgrade';
        }elseif ($filetype == 2){
            $log_act = 'cfg_add';
            $success_str = L('VAR_CFG_ADD_OK');
            $prefix = 'cfg';
            $dest_dir = C('FTP_WEB_PACK_PATH').'cfg';
            $relative_path = 'cfg';
        }elseif ($filetype == 5){
            $log_act = 'package_add';
            $success_str = L('VAR_MSG_PACKAGE_ADD_OK');
            $prefix = 'package_addon';
            $dest_dir = C('FTP_WEB_PACK_PATH').'upgrade';
            $relative_path = 'upgrade';
        }
        $new_name = $prefix.'_'.date('YmdHis').'.'.$ext;
        $dest_file_name = $dest_dir.'/'.$new_name;
        if (!move_uploaded_file($f['tmp_name'], $dest_file_name)){
            $this->ajaxReturn('', L('COPY_FILE_FAILED'), -2, -2);
        }
        $d = array(
            'name' => $f['name'],
            'original_filename' => $f['name'],
            'filename' => $new_name,
            'filetype' => $filetype,
            'filesize' => $f['size'],
            'md5_num' => strtoupper(md5_file($dest_file_name)),
            'ugid' => $this->getUgid(),
            'info' => $info,
            'creator' => $_SESSION[C('SESSION_NAME')]['name'],
            'relative_path' => $relative_path,
            'finish_status' => 1,
        );
        $m = M('file_list');
        if ($ret = $m->add($d)) {
            $auto_down = I('auto_down', '', 'trim');
            if ($filetype == 2 && $auto_down != '') {
                // 新增配置文件后，自动下发功能
                $a = A('Task');
                $a->downCfg($ret, true);
            }
            $this->wlog('', $log_act, sprintf("original_filename=%s,filename=%s",$f['name'],$new_name), 'ids='.$ret);
            $this->ajaxReturn('', $success_str, 0, 200);
        }else{
            @unlink($dest_file_name);
            $this->ajaxReturn('', L('VAR_CMD_SEND_FAILED').'<br>'.$m->getDbError(), -3, 200);
        }
    }

    //数据仪表盘-资源文件添加(图片，背景)
    public function addCanvasImg(){
        $filetype = I('filetype','bg','string'); //文件类型
        $f = $_FILES['filedata'];
        if ($f['error'] != 0){
            $this->ajaxReturn('', $this->getUploadErrorMsg($f['error']), -1, -1);
        }
        $ext = getFileExt($f['name']);
        $dest_dir = sprintf('./tmp/canvas_%s/%s/', $filetype, $_SESSION[C('SESSION_NAME')]['name']);
        if (!is_dir($dest_dir)){
            @mkdir($dest_dir, 0777, true);
        }
        $new_name = $filetype.'_'.date('YmdHis').'.'.$ext;
        $dest_file_name = $dest_dir.'/'.$new_name;
        if (!move_uploaded_file($f['tmp_name'], $dest_file_name)){
            $this->ajaxReturn(array('source'=>$f['tmp_name'],'dest'=>$dest_file_name), L('COPY_FILE_FAILED'), -2, -2);
        }
        $this->ajaxReturn($new_name, L('VAR_SWF_UPLOAD_OK'), 0, 200);
    }

    //数据仪表盘-获取图片列表
    public function loadCanvasImg(){
        $filetype = I('filetype','bg','string'); //文件类型
        $dir = sprintf('./tmp/canvas_%s/%s/', $filetype, $_SESSION[C('SESSION_NAME')]['name']);
        $files = array();
        if (is_dir($dir)){
            $d = opendir($dir);
            while ($f = readdir($d)){
                if ($f != '.' && $f != '..' && is_file($dir.$f)){
                    $files[filectime($dir.$f)] = array('filename'=>$f, 'path'=>__ROOT__.substr($dir,1).$f);
                }
            }
            closedir($d);
        }
        krsort($files);
        $this->ajaxReturn(array_values($files), '', 0);
    }

    //数据仪表盘-删除图片
    public function deleteCanvasImg(){
        $filetype = I('filetype','bg','string'); //文件类型
        $filename = I('filename','','string');
        if ($filename != ''){
            $path = sprintf('./tmp/canvas_%s/%s/%s', $filetype, $_SESSION[C('SESSION_NAME')]['name'], $filename);
            if (file_exists($path)){
                @unlink($path);
            }
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    //删除 file_list中的文件
    public function deleteResFile(){
        $ids = I('ids', '', 'string');
        $filetype = I('filetype', 0, 'string');
        $m = M('file_list');
        if ($filetype == 1 || $filetype == 5){
            $log_act = 'package_delete';
            // $relative_path = 'upgrade';
        }elseif ($filetype == 2) {
            $log_act = 'cfg_delete';
            // $relative_path = 'cfg';
        }elseif ($filetype == 3){
            $log_act = 'ad_file_delete';
            // $relative_path = 'ad/'.I('ad_id',0,'intval');
        }elseif ($filetype == 6) {
            $log_act = 'cap_delete';
            // $relative_path = 'cap';
        }elseif ($filetype == 7) {
            $log_act = 'take_photo_delete';
        }
        $dir = C('FTP_WEB_PACK_PATH');
        $rows = $m->field('filename, relative_path')->where("id IN($ids)")->select();
        if ($m->where("id IN($ids)")->delete()){
            foreach ($rows as $k => $row) {
                $path = $dir.$row['relative_path'].'/'.$row['filename'];
                if (!empty($row['filename'])) {
                    $files[] = $row['filename'];
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }
            $this->wlog('', $log_act, 'filename='.implode(',', $files));
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }

    // 软件升级
    public function rjsj() {
        $this->assign('web_path_1', array(L('SOFTWARE_UPGRADE')));
        $this->display('rjsj');
    }

    // 开始升级，目前仅支持windows系统
    public function kssj() {
        $cfg = M('system_config')->where("name LIKE 'swu_%'")->getField('name, value', true);
        if (IS_AJAX) {
            if (empty($cfg['swu_name'])) {
                $this->ajaxReturn('', L('SOFTWARE_NAME_SET_TIPS'), -1);
            }
            if (empty($cfg['swu_server'])) {
                $this->ajaxReturn('', L('UPGRADE_SERVER_ADDR_SET_TIPS'), -1);
            }
            $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
        }
        $wrap = IS_WIN ? "\r\n" : "\n";
        session_write_close();
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', '2G');
        import('@.ORG.Mlog');
        import("ORG.Net.Http");
        $log_filename = 'm2m_upgrade_'.date('Ymd_His');
        $this->record_d = array(
            'old_version' => $cfg['swu_version'],
            'new_version' => $cfg['swu_version'],
            'log_filename' => $log_filename
        );
        if (!is_dir('./Log/upgrade/')) {
            mkdir('./Log/upgrade/');
        }
        $log = new Mlog('./Log/upgrade/', $log_filename);
        echo '<html><head><meta charset="utf-8"><style>.tm{margin-right:30px;font-weight:400;color:gray;}</style></head><body>';
        $this->write_upgrade_log($log, L('START_UPGRADE'));
        if (!IS_WIN) {
            $this->write_upgrade_log($log, L('UPGRADE_ONLY_WIN_TIPS'), true);
        }

        // 查询版本
        $this->write_upgrade_log($log, sprintf(L('CHECK_VERSION_TIPS'), $cfg['swu_name']));
        $ret = Http::curlRequest($cfg['swu_server'].'index.php/Cgi/get_version', 'raw', array('program_name' => $cfg['swu_name'], 'current_version' => $cfg['swu_version']));
        if ($ret['status'] != 0) {
            $this->write_upgrade_log($log, sprintf(L('GET_VERSION_FAILED_TIPS'), isset($ret['errmsg']) ? $ret['errmsg'] : $ret['info']), true);
        }
        $this->write_upgrade_log($log, sprintf(L('CURRENT_VERSION_TIPS'), $ret['data']['new_version'], $cfg['swu_version'] ? $cfg['swu_version'] : 'null'));
        if ($cfg['swu_version'] && version_compare($cfg['swu_version'], $ret['data']['new_version'], '>=')) {
            $this->write_upgrade_log($log, L('VERSION_UP_TO_DATE_TIPS'), true);
        }

        // 下载文件
        $this->write_upgrade_log($log, sprintf(L('START_DOWNLOAD_TIPS'), $ret['data']['filename'], $ret['data']['filesize']));
        $start_time = date('Y-m-d H:i:s');  //开始下载
        $f = Http::downFile($cfg['swu_server'].'index.php/Cgi/download_file', './tmp/', array('filename' => $ret['data']['filename']));
        $end_time = date('Y-m-d H:i:s');    //结束下载
        $this->write_upgrade_log($log, sprintf(L('DOWNLOAD_RESULT_TIPS'), $f['status'] == 0 ? L('VAR_SUCCESS') : L('VAR_FAILED')), $f['status'] != 0);
        // 上报文件下载结果
        if ($f['status'] == 0) {
            Http::curlRequest($cfg['swu_server'].'index.php/Cgi/download_file_ack', 'raw', array(
                'filename' => $ret['data']['filename'],
                'start_time' => $start_time,
                'end_time' => $end_time,
                'status' => $f['status'],
                'info' => $f['status'] == 0 ? 'ok' : $f['info']
            ));
        }

        // 检查文件md5值
        if (md5_file('./tmp/'.$ret['data']['filename']) != $ret['data']['md5']) {
            $this->write_upgrade_log($log, L('MD5_FAILED_TIPS'), true);
        }

        // 解压zip文件
        $this->write_upgrade_log($log, sprintf(L('START_UNZIP_TIPS'), $ret['data']['filename']));
        $ui_dir = basename(getcwd());
        chdir('..');
        $zip_file = sprintf('./%s/tmp/%s', $ui_dir, $ret['data']['filename']);
        exec(sprintf('7za.exe x -o./%s/tmp/ %s', $ui_dir, $zip_file), $output, $ret_unzip);
        if (is_file($zip_file)) {
            // 删除压缩包
            unlink($zip_file);
        }
        $path = sprintf('%s/%s/tmp/%s/', getcwd(), $ui_dir, $ret['data']['new_version']);
        $path = IS_WIN ? str_replace('/', '\\', $path) : str_replace('\\', '/', $path);
        $this->version_dir_path = $path;
        $this->write_upgrade_log($log, L($ret_unzip == 0 ? 'UNZIP_OK_TIPS' : 'UNZIP_FAILED_TIPS'), $ret_unzip != 0);

        // 读取patch.json文件
        if (!file_exists($path.'patch.json')) {
            $this->write_upgrade_log($log, L('MISSING_PATCH_FILE_TIPS'), true);
        }
        $cfg = json_decode(file_get_contents($path.'patch.json'), true);
        unlink($path.'patch.json'); // 删除patch文件

        // 文件处理
        // 首先处理db文件，其次处理配置文件，因为server和UI可能依赖里面的某个字段、某个配置项才能启动；
        // db_file，config_file_list，service_list 这三个字段可以为空，但必须存在；
        if (!isset($cfg['db_file']) || !isset($cfg['config_file_list']) || !isset($cfg['service_list'])) {
            $this->write_upgrade_log($log, L('PATCH_FILE_ERROR'), true);
        }

        // 1.SQL文件
        // 注意db_file格式为json，里面是一个数组，包含多条sql语句
        if (!empty($cfg['db_file'])) {
            if (!file_exists($path.$cfg['db_file'])) {
                $this->write_upgrade_log($log, sprintf(L('MISSING_DB_FILE_TIPS'), $cfg['db_file']), true);
            }
            $this->write_upgrade_log($log, L('START_UPDATE_DB_TIPS'));
            $sqls = json_decode(file_get_contents($path.$cfg['db_file']), true);
            foreach ($sqls as $sql) {
                $ret_sql = M('')->execute($sql);
                $this->write_upgrade_log($log, sprintf(L('EXEC_SQL_RESULT_TIPS'), $sql, $ret_sql === false ? L('VAR_FAILED') : L('VAR_SUCCESS')));
            }
            unlink($path.$cfg['db_file']); // 删除sql文件
        }

        // 2.配置文件
        $this->write_upgrade_log($log, L('START_UPDATE_CFG_TIPS'));
        foreach ($cfg['config_file_list'] as $cfg_file) {
            $old_path = $cfg_file['path'].DIRECTORY_SEPARATOR.$cfg_file['name'];
            $new_path = $path.$old_path;
            if (strpos($old_path, 'UI') === false) {
                $old_ini = parse_ini_file($old_path, true);
                $new_ini = parse_ini_file($new_path, true);
                foreach ($new_ini as $section => $row) {
                    if (!isset($old_ini[$section])) {
                        $old_ini[$section] = $row;
                        continue;
                    }
                    foreach ($row as $name => $value) {
                        if (!isset($old_ini[$section][$name])) {
                            $old_ini[$section][$name] = $value;
                        }
                    }
                }
                write_ini_file($old_ini, $old_path);
            } else {
                $old_file = file_get_contents($old_path);
                $new_file = fopen($new_path, 'r');
                $new_file_str = '';
                while (!feof($new_file) && ($line = fgets($new_file))) {
                    $line = trim($line);
                    if (empty($line)) {
                        continue;
                    }
                    $line_arr = explode('=>', $line);
                    if (strpos($old_file, trim($line_arr[0])) === false) {
                        $new_file_str .= "\r\n\t".$line;
                    }
                }
                if ($new_file_str != '') {
                    file_put_contents($old_path, str_replace("\r\n);", $new_file_str."\r\n);", $old_file));
                }
                fclose($new_file);
            }
            $this->write_upgrade_log($log, sprintf(L('UPDATE_CFG_OK_TIPS'), $old_path));
            unlink($new_path); // 删除配置文件
        }

        // 3.后台服务
        $this->write_upgrade_log($log, L('START_UPDATE_SERVICE_TIPS'));
        foreach ($cfg['service_list'] as $service) {
            exec(sprintf('sc stop %s', $service['service_name']), $sc_output, $sc_ret);
            $this->write_upgrade_log($log, sprintf(L('STOP_SERVICE_TIPS'), $service['service_name'], $sc_ret == 0 ? L('VAR_SUCCESS') : L('VAR_FAILED')));

            $cp_cmd = sprintf('copy /Y %s%s %s', $path, $service['exe_name'], $service['exe_name']);
            exec($cp_cmd, $cp_output, $cp_ret);
            $this->write_upgrade_log($log, sprintf(L('REPLACE_SERVICE_FILE_TIPS'), $service['exe_name'], $cp_ret == 0 ? L('VAR_SUCCESS') : L('VAR_FAILED')));

            exec(sprintf('sc start %s', $service['service_name']), $sc2_output, $sc2_ret);
            $this->write_upgrade_log($log, sprintf(L('START_SERVICE_TIPS'), $service['service_name'], $sc2_ret == 0 ? L('VAR_SUCCESS') : L('VAR_FAILED')));

            unlink($path.$service['exe_name']); // 删除exe文件
        }

        // 4.替换文件
        $this->write_upgrade_log($log, L('START_REPLACE_FILES_TIPS'));
        $rep_ret = upgrade_copy_dir(substr($path, 0, strlen($path)-1), getcwd(), $log);
        $this->write_upgrade_log($log, sprintf(L('REPLACE_FILES_RESULT_TIPS'), $rep_ret[0], $rep_ret[1]));

        // 5.删除缓存
        exec(sprintf('RD /S /Q %s%sRuntime', $ui_dir, DIRECTORY_SEPARATOR), $dr_output, $dr_ret);
        $this->write_upgrade_log($log, sprintf(L('DEL_RUNTIME_TIPS'), $ui_dir, DIRECTORY_SEPARATOR, $dr_ret == 0 ? L('VAR_SUCCESS') : L('DEL_RUNTIME_FAILED_TIPS')));

        // 6.工作目录回到UI下，否则可能生成多余的Runtime文件夹
        chdir($ui_dir);

        // 升级成功处理
        $this->record_d['new_version'] = $ret['data']['new_version'];
        $this->write_upgrade_log($log, sprintf(L('FINISH_UPGRADE_TIPS'), $ret['data']['new_version']), false, true);
        die('</body></html>');
    }

    // 升级记录
    public function loadUpgradeRecord() {
        $m  = M('system_upgrade_record');
        $rs = $m->field('*')
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $total = $m->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        ));
    }

    // 系统设置
    public function xtsz() {
        $this->assign('web_path_1', array(L('SYSTEM_SETTING')));
        $this->display('xtsz');
    }

    /**
     * 加载系统参数 (system_config)
     * @param  $name 不为空时查询 name LIKE '%$name'的参数值
     * @return json
     */
    public function loadSystemParams() {
        $m = M('system_config');
        $name = I('name', '', 'trim');
        $rs = $m->field('name,value,hide')->where($name == '' ? '1=1' : "name LIKE '{$name}%'")->select();
        $arr = L('LICENCE_TYPE_TEXT');
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        foreach ($rs as $k=>$row){
            $v = $row['value'];
            if ($row['hide'] == 1 && $uid != 1 && !empty($row['value'])){
                $v = '******';
            }
            $d[$row['name']] = $v;
        }
        if (isset($_REQUEST['sessid'])){
            $d['version'] = UI_VERSION;
            $d['release'] = UI_RELEASE_DATE;
            $this->ajaxReturn($d,'ok',0);
        }
        if ($d['licence_type'] == 1) {
            $d['licence_end_time_tips'] = L('LICENCE_END_TIME_TIPS');
        } else {
            $d['licence_end_time'] = L('NOT_LIMITED');
            $d['licence_end_time_tips'] = '';
        }
        $d['licence_type'] = $arr[$d['licence_type']];
        echo json_encode($d);
    }

    // 修改个人告警配置
    public function editAlarmParams() {
        $d = array(
            'email' => $_POST['alarm_email'],
            'wx' => $_POST['alarm_wx']
        );
        unset($_POST['alarm_email']);
        unset($_POST['alarm_wx']);
        $d['recv_alarm_cfg'] = json_encode($_POST);
        M('usr')->where('id = %d', $_SESSION[C('SESSION_NAME')]['id'])->save($d);
        $this->ajaxReturn('',L('VAR_MENU_SYSCFG_EDIT_OK'),0);
    }

    /**
     * 修改系统参数
     * @param $name 不为空时，只修改某一个参数：update system_config set value = '$params' where name = '$name'
     */
    public function editSystemParams() {
        $m = M('system_config');
        $name = I('name', '', 'trim');
        $params = I('params', '', 'string');
        if ($name != '') {
            $sql = "UPDATE system_config SET value = '".urldecode($params)."' WHERE name = '$name'";
        } else {
            $params = explode(',', $params);
            $sql = 'UPDATE system_config SET value = CASE name';
            foreach ($params as $v){
                if (empty($v)){
                    continue;
                }
                $tmp = explode('=', $v);
                $names[] = "'".$tmp[0]."'";
                $sql .= sprintf(" WHEN '%s' THEN '%s' ", $tmp[0], $tmp[1]=='null'?'0':$tmp[1]);
            }
            $sql .= sprintf("END WHERE name IN(%s)", implode(',',$names));
        }
        $ret = $m->execute($sql);
        if ($ret){
            $this->wlog('', 'system_config_edit', I('params'));
            $client = stream_socket_client('udp://'.C('SERVER_IP').':'.C('SERVER_PORT'), $errno, $errstr, 5);
            fwrite($client, 'cmd=sync_setting');
            fclose($client);
            $this->ajaxReturn('',L('VAR_MENU_SYSCFG_EDIT_OK'),0);
        }else{
            $this->ajaxReturn('',L('VAR_NO_CHANGE'),-1);
        }
    }

    // 测试邮箱配置
    public function emailConfigTest(){
        $dest = I('email_config_test');
        $title = 'Test email config ...';
        $content = 'datetime = '.date('Y-m-d H:i:s');
        $params = array(
            'host' => I('email_config_host'),
            'ssl'  => I('email_config_ssl',0,'intval'),
            'port' => I('email_config_port'),
            'account' => I('email_config_account'),
            'password' => I('email_config_password'),
            'from' => I('email_config_from'),
            'auth' => I('email_config_smtp_auth',0,'intval'),
        );
        $ret = $this->sendmail($dest, $title, $content, '', $params);
        $this->ajaxReturn('', $ret['info'], $ret['status']);
    }

    // 设置企业号二维码，仅管理员能操作
    public function setQrcode(){
        $filename = explode('Lib', __FILE__);
        $filename = $filename[0] . 'Tpl/Public/images/wxqyh.jpg';
        $f = $_FILES['filedata'];
        if ($f['error'] != 0){
            $this->ajaxReturn('', $this->getUploadErrorMsg($f['error']), -1, 'HTML');
        }
        import("ORG.Util.Image");
        $ret = Image::thumb($f['tmp_name'], $filename, '', 80, 80);
        $this->ajaxReturn($ret, L('OPERATION_SUCCESS'), 0, 'HTML');
    }

    /**
     * 加载系统操作日志，非admin用户只获取自己的记录
     */
    public function loadLogData(){
        $m  = M('log');
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $uname = $_SESSION[C('SESSION_NAME')]['name'];
        $q = sprintf('%s AND %s', $this->generate_search_str(), $uid == 1 ? '1=1':"username='$uname'");
        $rs = $m->field("log.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();

        $arr = array_merge(L('VAR_TASK_TYPE_ARR'), L('VAR_OPERATION_TYPE'));
        foreach ($rs as $k=>$row){
            $data[$k] = array(
                'id' => $row['id'],
                'username' => $row['username'],
                'cmd' => $arr[$row['cmd']],
                'ip' => $row['ip'],
                'create_time' => $row['create_time'],
            );
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $data,
            'userdata' => array()
        ));
    }

    //个人中心
    public function grzx(){
        $row = M('usr')->field('name, usr_type, create_time, never_expired, expired_time, head, gid, email, sim, recv_alarm, info')->where("id = %d", $_SESSION[C('SESSION_NAME')]['id'])->find();
        $utt = L('VAR_USER_TYPE_TEXT');
        $row['usr_type_text'] = $utt[$row['usr_type']];
        if ($row['never_expired'] == 1){
            $row['expired_time'] = L('NEVER_EXPIRE');
        }
        $this->assign('row', $row);
        $this->assign('web_path_1', array(L('PERSONAL_CENTER')));
        $this->display();
    }

    //用户登录记录
    public function loadUserLoginRecord(){
        $m  = M('usr_login_record');
        $invoke = I('invoke','','string');
        $q = sprintf('%s AND (%s)', $this->generate_search_str(), $invoke=='yhgl' ? '1=1' : 'usr_id='.$_SESSION[C('SESSION_NAME')]['id']);
        $rs = $m->field("usr_login_record.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k=>$row){
            $rs[$k]['position'] = sprintf("%s %s %s", $row['city'], $row['province'], $row['country']);
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

    //修改个人信息
    public function editMyInfo(){
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $m = M('usr');
        $m->save(array(
            'id' => $uid,
            'email' => I('email','','string'),
            'sim' => I('sim','','string'),
            'info' => I('info','','string')
        ));
        $this->ajaxReturn('',L('VAR_EDIT_MYSELF_OK'),0);
    }

    //修改密码
    public function editPass(){
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $m = M('usr');
        $row = $m->where("id=$uid")->find();
        $old_pass = I('old_pass','','trim');
        $new_pass = I('new_pass','','trim');
        if ($row['password'] != md5($old_pass)){
            $this->ajaxReturn('',L('OLD_PWD_ERROR'),-1);
        }
        $m->save(array('id'=>$uid, 'password'=>md5($new_pass)));
        $this->ajaxReturn('',L('VAR_EDIT_PASS_OK'),0);
    }

    //设置头像
    public function editHeadImg(){
        $f = $_FILES['filedata'];
        if ($f['error'] != 0){
            $this->ajaxReturn('', $this->getUploadErrorMsg($f['error']), -1, -1);
        }
        $dest_dir = './Upload/headimg';
        if (!is_dir($dest_dir)){
            @mkdir($dest_dir, 0777);
        }
        $ext = getFileExt($f['name']);
        $log_act = 'head_img_edit';
        $success_str = L('OPERATION_SUCCESS');
        $new_name = date('YmdHis').'.'.$ext;
        $dest_file_name = $dest_dir.'/'.$new_name;
        $m = M('usr');
        $row = $_SESSION[C('SESSION_NAME')];

        if (!move_uploaded_file($f['tmp_name'], $dest_file_name)){
            $this->ajaxReturn('', L('COPY_FILE_FAILED'), -2, -2);
        }
        if ($m->save(array('id'=>$row['id'], 'head'=>$new_name))){
            $this->wlog('', $log_act, sprintf("original_filename=%s,filename=%s",$row['head'],$new_name), 'username='.$row['name']);
            if ($row['head'] && strlen($row['head']) > 6){
                @unlink($dest_dir.'/'.$row['head']);
            }
            $_SESSION[C('SESSION_NAME')]['head'] = $new_name;
            $this->ajaxReturn($new_name, $success_str, 0, 200);
        }else{
            @unlink($dest_file_name);
            $this->ajaxReturn('', L('VAR_CMD_SEND_FAILED').'<br>'.$m->getDbError(), -3, 200);
        }
    }

    // 服务均衡，服务器列表
    public function fwq(){
        if (IS_AJAX) {
            $m = M('app_server');
            $q = sprintf('%s', $this->generate_search_str());
            $rs = $m->field("app_server.*")
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
            return;
        }
        $this->assign('web_path_1', array(L('APP_SERVER')));
        $this->display('fwq');
    }

    // 服务均衡，运行记录
    public function fwqyxjl(){
        if (IS_AJAX) {
            $m = M('app_server_run_record');
            $q = sprintf("%s AND report_time BETWEEN '%s' AND '%s'", $this->generate_search_str(), date('Y-m-d H:i:s',I('start')), date('Y-m-d H:i:s',I('end')));
            $rs = $m->field("app_server_run_record.*, app_server.name, app_server.info")
                ->join("LEFT JOIN app_server ON app_server.id = app_server_run_record.app_server_id")
                ->where($q)
                ->order($this->generate_order_str())
                ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
            $total = $m->join("LEFT JOIN app_server ON app_server.id = app_server_run_record.app_server_id")->where($q)->count();
            echo json_encode(array(
                'page' => $this->pp['page'],
                'total' => ceil($total / $this->pp['rp']),
                'records' => $total,
                'rows' => $rs,
                'userdata' => array()
            ));
            return;
        }
        $this->assign('web_path_1', array(L('APP_SERVER_RUN_RECORD')));
        $this->display('fwqyxjl');
    }

    // 负载均衡，编辑服务器info信息
    public function appServerEdit(){
        $m = M('app_server');
        $id = I('id');
        $info = I('info');
        if ($m->save(array('id'=>$id,'info'=>$info))){
            // $this->wlog('', 'app_server_edit', '', 'ids='.$id);
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), -0);
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于设备详情页面，下载配置文件
    public function termDetailDownCfg() {
        $this->resDownload();
    }

    // 此处仅为一个壳函数，为了节点权限控制。用于设备详情页面，下载抓包文件
    public function termDetailDownCap() {
        $this->resDownload();
    }

    // 资源文件下载
    // id==view_log_txt时查看平台日志log.txt
    public function resDownload() {
        $id = $_REQUEST['id'];
        header("Content-type:text/html;charset=utf-8");
        if ($id == 'view_log_txt') {
            $path = '../log/log.txt';
            if (!file_exists($path)) {
                die('File not exists: '.$path);
            }
            die(file_get_contents($path));
        }
        $m = M('file_list');
        $row = $m->where("id = $id")->field('name,filename,relative_path')->find();
        if ($row){
            $path = sprintf('%s%s/%s', C('FTP_WEB_PACK_PATH'), $row['relative_path'], $row['filename']);
            if (!file_exists($path)){
                die('File ('.$row['name'].') not exist!');
            }
            $fp = fopen($path,"r");
            $file_size = filesize($path);
            //下载文件需要用到的头
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:".$file_size);
            Header("Content-Disposition: attachment; filename=".$row['name']);
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

    // 告警策略
    public function gjcl(){
        if (IS_AJAX) {
            $cfgs = M('usr')->where('id = %d', $_SESSION[C('SESSION_NAME')]['id'])->field('email AS alarm_email, wx AS alarm_wx, recv_alarm_cfg')->find();
            if ($cfgs['recv_alarm_cfg']) {
                $tmp = json_decode($cfgs['recv_alarm_cfg'], true);
                unset($cfgs['recv_alarm_cfg']);
                $cfgs = array_merge($cfgs, $tmp);
            }
            die(json_encode($cfgs));
        }
        $this->assign('web_path_1', array(L('ALARM_STRATEGY')));
        $this->display('gjcl');
    }

    // 告警发送记录
    public function alarmSendRecord() {
        $m = M('term_alarm_record');
        $q = sprintf('%s', $this->generate_search_str());
        $at = I('alarm_type', -1, 'intval');
        $hs = I('handle_status', -1, 'intval');
        if ($at != -1) $q .= " AND alarm_type = $at ";
        if ($hs != -1) $q .= " AND handle_status = $hs ";
        if ($_SESSION[C('SESSION_NAME')]['id'] != 1) {
            $q .= sprintf(" AND receiver_name = '%s'", $_SESSION[C('SESSION_NAME')]['name']);
        }
        $rs = $m->field('term_alarm_record.*')
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $at = L('ALARM_TYPE_DEFINE');
        import("ORG.Util.MString");
        foreach ($rs as $key => $row) {
            $rs[$key]['email_send_ts'] = $row['email_send_ts'] == 0 ? '--' : $row['email_send_ts'];
            $rs[$key]['wx_send_ts'] = $row['wx_send_ts'] == 0 ? '--' : $row['wx_send_ts'];
            $rs[$key]['alarm_type'] = $at[$row['alarm_type']];
            $rs[$key]['email'] = empty($row['email']) ? '--' : $row['email'];
            $rs[$key]['wx'] = empty($row['wx']) ? '--' : $row['wx'];
            // $rs[$key]['email_content'] = MString::msubstr(htmlspecialchars($row['email_content']), 0, 100);
        }
        $total = $m->where($q)->count();
        echo json_encode(array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs ? $rs : array(),
            'userdata' => array()
        ));
    }

    // 删除告警发送记录，操作员以上有权限
    public function alarmSendRecordDelete() {
        $ids = I('ids', '0', 'string');
        if (M('term_alarm_record')->where('id IN(%s)', $ids)->delete()) {
            $this->wlog('', "alarm_record_delete", sprintf("ids=%s", $ids));
        }
        $this->ajaxReturn('', L('OPERATION_SUCCESS'), 0);
    }
}