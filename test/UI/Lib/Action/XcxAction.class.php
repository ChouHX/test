<?php
class XcxAction extends CommonAction{
    private $tool = null;

    public function __construct(){
        parent::__construct();
        defined('XCX_TOKEN') or define('XCX_TOKEN', 'detranltd2013');
        vendor('Weixin/Xcx/wxBizMsgCrypt');
        session_write_close();
    }

    //接入
    public function access(){
        if (WXBizMsgCrypt::checkSignature()){
            echo $_REQUEST['echostr'];
        }else{
            die('access failed');
        }
    }

    public function login(){
        $name = I('na','','string');
        $password = I('pa','','string');
        $openid = I('openid','','string');
        $login_by_openid = I('login_by_openid',0,'intval');
        $info = '';

        try {
            $m = M('usr');
        } catch (ThinkException $e) {
            $info = $e->getMessage();
        }

        if ($login_by_openid != 0){
            $row = $m->where("id = (SELECT uid FROM usr_wx WHERE openid = '%s' AND uid != 0)", $openid)->find();
        }else{
            $row = $m->where("name='%s' AND password='%s'",array($name,md5($password)))->find();
        }

        if ($row){
            if ($row['is_enable'] != 1){
                $info = L('ACCOUNT_DISABLED');
            }
            if ($row['never_expired'] != 1 && $row['expired_time'] <= date('Y-m-d H:i:s')){
                $info = L('ACCOUNT_EXPIRED');
            }

            $row['member_since'] = date('Y.m.d',strtotime($row['create_time']));
            $row['login_type'] = $login_type;

            //更新usr_wx表
            if ($openid != ''){
                $d = array(
                    'openid' => $openid,
                    'uid' => $row['id'],
                    'login_time' => date('Y-m-d H:i:s')
                );
                M('usr_wx')->where("openid = '$openid'")->count() != 0 ? M('usr_wx')->save($d) : M('usr_wx')->add($d);
            }

            //保存会话
            $sessid = md5(session_id().'detranltd');
            $row['lang'] = I('lang', 'zh-cn', 'string');
            S($sessid, $row, 3600);
            $row['sessid'] = $sessid;

            //login record
            $ip = get_client_ip();
            $ret = get_position_by_ip($ip);
            M('usr_login_record')->add(array(
                'usr_id' => $row['id'],
                'ip' => $ip,
                'country' => $ret['country'],
                'province' => $ret['province'],
                'city' => $ret['city'],
            ));
        }else{
            $info = $login_by_openid != 0 ? sprintf("Openid (%s)从未登录过平台，请使用账号密码登录！",$openid) : L('VAR_LOGIN_ERROR');
            /*
            if ($login_by_openid != 0){
                $status = -2;
                $info = '审核测试账号';
                $row = array('name'=>'guest', 'pwd'=>'Guest1234');
            }else{
                $info = L('VAR_LOGIN_ERROR');
            }
            */
        }

        $this->ajaxReturn($row, $info, $status ? $status : ($info==''?0:-1));
    }

    //小程序注销登录
    public function logout(){
        $openid = I('openid', '', 'string');
        $sessid = I('sessid', '', 'string');
        if ($openid != ''){
            M('usr_wx')->save(array(
                'openid' => $openid,
                'uid' => 0
            ));
        }
        if ($sessid){
            S($sessid, null);
        }
        $this->ajaxReturn('', 'OK', 0);
    }

    //设备列表数据
    public function loadTermData(){
        $uid = I('uid',0,'intval');
        $m = M('term');
        $sns = I('sns','','trim');
        if ($sns != ''){
            $q = sprintf("term.sn IN('%s')", str_replace(',', "','", $sns));
        }else{
            $gid = I('gid',-10,'intval');
            $q = sprintf('%s AND group_id IN(%s) AND %s', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids('string',$uid), $this->generate_search_str());
        }
        $sql = "SELECT term.sn, term.ud_sn, term.alias, term.vsn, term.term_model, term.term_type, term.gateway_sn, term.group_id,
            term.sw_version, term.imei, term.module_vendor, term.module_type, term.sim, term.imsi, term.iccid,
            term.frequency, term.wifi_ssid, term.host_sn,
            B.ip, B.port, B.is_online, B.protocol, B.first_login, B.login_time, B.last_time, B.net_mode, B.term_signal, B.flux, B.month_flux,
            B.last_7days_flux, B.virtual_ip, B.virtual_is_online, B.virtual_last_time FROM term
            JOIN term_run_info AS B ON B.sn = term.sn WHERE $q ORDER BY ".$this->generate_order_str('router_list')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
        $rs = $m->query($sql);
        $ts = mktime();
        if (count($rs) > 0){
            //Device group name
            $rs4 = M('term_group')->field('id, name')->select();
            foreach ($rs4 as $k => $row) {
                $groups[$row['id']] = $row['name'];
            }
            unset($rs4);
        }
        $netMode = C('NET_MODE');
        foreach ($rs as $k=>$row){
            $rs[$k]['status'] = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
            // gname
            $rs[$k]['gname'] = $groups[$row['group_id']];
            //网络模式
            $rs[$k]['net_mode'] = $netMode[$row['net_mode']];
            //信号量图标
            $rs[$k]['term_signal_img_name'] = get_term_signal_str($rs[$k]['status'], $row['term_signal'], 'img_name');
            //设备型号
            $rs[$k]['term_model'] = $this->formatTermModel($row['term_model']);
            $rs[$k]['term_model_text'] = $this->getTermModelText($row['term_model']);
        }
        $ts = date('Y-m-d H:i:s', time() - C('TERM_OFFLINE_TIME'));
        $rs_online = $m->query("SELECT COUNT(*)num FROM term, term_run_info WHERE term_run_info.sn = term.sn AND $q AND is_online = 1 AND last_time >= '$ts'");
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'online' => $rs_online[0]['num'],
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );
        $this->ajaxReturn($data, '', 0);
    }

    //采集列表数据
    public function loadRtu(){
        session_write_close();
        $m = M('term');
        $gid = I('gid',-10,'intval');
        $q = sprintf('%s AND group_id IN(%s) AND %s', ($gid==-10 ? '1=1' : "group_id = $gid"), $this->getTgids(), $this->generate_search_str());
        $sql = "SELECT term.sn, term.ud_sn, term.alias, term_run_info.last_time, term_run_info.is_online, term_run_info.status AS status_limit,
            rpi.prjname, rpi.address, rpi.contact, rpi.tel FROM term
            JOIN term_run_info ON term_run_info.sn = term.sn
            LEFT JOIN rtu_project_info rpi ON rpi.sn = term.sn
            WHERE $q ORDER BY ".$this->generate_order_str('router_list')." LIMIT ".($this->pp['page']-1)*$this->pp['rp'].",".$this->pp['rp'];
        $rs = $m->query($sql);
        $ts = time();
        foreach ($rs as $key => $row) {
            $rs[$key]['status'] = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
        }
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );
        $this->ajaxReturn($data, '', 0);
    }

    //路由器分组数据
    public function loadTermGroup(){
        $m = M('term_group');
        $q = sprintf('id != 1 AND id IN(%s) AND %s', $this->getTgids(), $this->generate_search_str());
        $rs = $m->field("term_group.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->select();
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );
        $this->ajaxReturn($data, '', 0);
    }

    //路由器分组数据-treenodes
    public function loadTermGroupTreenodes() {
        // 计算在线数/总数
        $tgids = $this->getTgids();
        $rs = M('')->query("SELECT a.group_id, b.is_online, b.last_time FROM term a LEFT JOIN term_run_info b ON b.sn = a.sn WHERE a.group_id IN($tgids)");
        $all_num = 0;
        $all_online = 0;
        $ts = date('Y-m-d H:i:s', mktime()-C('TERM_OFFLINE_TIME'));
        foreach ($rs as $k => $row) {
            if (!isset($onlines[$row['group_id']])){
                $onlines[$row['group_id']] = array('total'=>0, 'online'=>0);
            }
            $onlines[$row['group_id']]['total'] += 1;
            $all_num += 1;
            if ($row['is_online'] == 1 && $row['last_time'] >= $ts){
                $onlines[$row['group_id']]['online'] += 1;
                $all_online += 1;
            }
        }
        // 查询分组
        $m = M('term_group');
        $rs = $m->query(sprintf("SELECT id, name, pid, (SELECT COUNT(*) FROM term_group a WHERE a.pid = term_group.id)sub_num FROM term_group WHERE id != 1 AND id IN(%s) AND ".$this->generate_search_str()." ORDER BY %s",
            $tgids, $this->generate_order_str()
        ));
        foreach ($rs as $k => $row) {
            if (!isset($onlines[$row['id']])){
                $onlines[$row['id']] = array('total'=>0, 'online'=>0);
            }
            $rs[$k]['total'] = $onlines[$row['id']]['total'];
            $rs[$k]['online'] = $onlines[$row['id']]['online'];
        }
        $ret = $this->d_g(1, $rs);
        $this->ajaxReturn(json_encode($ret), array($all_num, $all_online), 0);
    }

    //递归生成childMenus
    private function d_g($pid, &$rs) {
        $ret = array();
        foreach ($rs as $key => $row) {
            if ($row['pid'] == $pid) {
                $ret[] = array('text'=>$row['name'], 'id'=>$row['id'], 'total'=>$row['total'], 'online'=>$row['online'], 'childMenus'=>$row['sub_num'] == 0 ? array() : $this->d_g($row['id'], $rs));
            }
        }
        return $ret;
    }

    //设备详情
    public function loadTermDetail(){
        $sn = $_REQUEST['sn'];
        $data = $this->assignTermRow($sn, true);
        //在线状态
        $ts = mktime();
        $data['status'] = get_term_status_code($ts - strtotime($data['last_time']), $data['is_online']);
        //信号量图标
        $data['term_signal'] = strip_tags($data['term_signal']);
        $data['term_signal_img_name'] = get_term_signal_str($data['status'], $data['term_signal'], 'img_name');
        //GPS位置
        $ret = $this->map($sn, false, false);
        $data['longitude'] = $ret['lng'];
        $data['latitude'] = $ret['lat'];
        $this->ajaxReturn($data, '', 0);
    }

    //任务列表数据
    public function loadTaskData(){
        $uid = I('uid',0,'intval');
        $m = M('term_task');
        $cmd = I('cmd','all','string');
        $q = sprintf('%s AND %s', $cmd=='all'?'0=0':"cmd='$cmd'", $this->generate_search_str());
        $q_privileges = $_SESSION[C('SESSION_NAME')]['id']==1 ? '2 = 2' : 'ugid = '.$this->getUgid();
        $q = $q.' AND '.$q_privileges;
        $rs = $m->field('term_task.*')
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        $arr1 = L('VAR_TASK_TYPE_ARR');
        $cmdHasProcoess = array('download_ad', 'upgrade', 'upgrade_udp', 'upgrade_tcp', 'upgrade_camera');
        foreach ($rs as $k=>$row){
            $task_ids[] = $row['id'];
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
                'progress'    => in_array($row['cmd'], $cmdHasProcoess, true),
                'status_all'  => 0,
                'status_3'    => 0,
                'status_0'    => 0,
                'status_other'=> 0,
            );
            if (strpos($row['value'], 'sch_rboot="0') === 0){
                $data[$index]['cmd'] = L('SCHEDULED_REBOOT').' ('.L('VAR_TERM_TERM_PARAMS_CLOSE').')';
                $data[$index]['value'] = '';
            }elseif (strpos($row['value'], 'sch_rboot="1') === 0){
                $this->displaySrestart($data[$index], $row['value']);
            }
        }
        unset($rs);
        $rs2 = M('term_task_detail')->field('task_id,status')->where('task_id IN(%s)',implode(',',$task_ids))->select();
        foreach ($rs2 as $k => $row) {
            $index = $row['task_id'];
            $data[$index]['status_all'] += 1;
            if ($row['status'] == 0 || $row['status'] == 3){
                $data[$index]['status_'.$row['status']] += 1;
            } elseif ($row['status'] == 2){
                $data[$index]['status_0'] += 1;
            }else{
                $data[$index]['status_other'] += 1;
            }
        }
        $rs = array_values($data);
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );
        $this->ajaxReturn($data, '', 0);
    }

    //平台概况页面数据
    public function ptgkData(){
        session_write_close();
        ini_set('memory_limit', '-1');
        $m = M('term');
        $ms = M('stat_info');
        $data_swv = $data_netmode = array();
        $tm = date('Y-m-d H:i:s', mktime() - C('TERM_OFFLINE_TIME'));

        $ret = array(
            'chart_online' => array(),
            'chart_flux' => array(),
            'chart_new' => array(),
            'chart_swv' => array(),
            'chart_netmode' => array(),
            'chart_task' => array()
        );

        try {
            // $categories = array();
            $now = strtotime(I('start_date',date('Y-m-d'),'string'));
            $flux_x = array();

            for ($i=1; $i<=7; $i++){
                $tmpdate = $now - 24*3600*$i;
                $tmp_md = date('M d',$tmpdate);
                $tmp_ymd = date('Ymd',$tmpdate);
                array_unshift($flux_x, $tmp_ymd);
                $ret['chart_online'][$tmp_ymd] = array('type'=>$tmp_md, 'tem'=>0);
                $ret['chart_flux'][$tmp_ymd]   = array('type'=>$tmp_md, 'tem'=>0);
                $ret['chart_new'][$tmp_ymd]    = array('type'=>$tmp_md, 'tem'=>0);
            }
            $len = count($flux_x);

            //最近7天上线
            {
                $rs = $m->query("SELECT report_day, online_count FROM stat_info WHERE report_day BETWEEN {$flux_x[0]} AND {$flux_x[$len-1]}");
                foreach ($rs as $k=>$row){
                    $ret['chart_online'][$row['report_day']]['tem'] = intval($row['online_count']);
                }
                $ret['chart_online'] = array_values($ret['chart_online']);
                unset($rs);
            }

            //最近7天流量统计
            {
                $max = 0;
                $rs = $m->query("SELECT report_day, flux FROM stat_info WHERE report_day BETWEEN {$flux_x[0]} AND {$flux_x[$len-1]}");
                foreach ($rs as $k=>$row){
                    $ret['chart_flux'][$row['report_day']]['tem'] = $row['flux'];
                    if ($row['flux'] > $max){
                        $max = $row['flux'];
                    }
                }
                $divisor = 1024;
                $flux_unit = 'KB';
                if ($max >= 1024 * 1024){
                    $divisor = 1024 * 1024;
                    $flux_unit = 'MB';
                } elseif ($max >= 1024 * 1024 * 1024){
                    $divisor = 1024 * 1024 * 1024;
                    $flux_unit = 'GB';
                }
                foreach ($ret['chart_flux'] as $k => $v) {
                    $ret['chart_flux'][$k]['tem'] = floatval(round($v['tem']/$divisor,2));;
                }
                $ret['chart_flux'] = array_values($ret['chart_flux']);
                $ret['flux_unit'] = $flux_unit;
                unset($rs);
            }

            //最近7天新增
            {
                $rs = $m->query("SELECT term.sn, DATE_FORMAT(first_login,'%Y%m%d')ymd FROM term
                    JOIN term_run_info ON term_run_info.sn = term.sn
                    WHERE first_login IS NOT NULL AND first_login<>'0000-00-00 00:00:00'
                    HAVING ymd BETWEEN '{$flux_x[0]}' AND '{$flux_x[$len-1]}'");
                foreach ($rs as $k=>$row){
                    $ret['chart_new'][$row['ymd']]['tem'] += 1;
                }
                $ret['chart_new'] = array_values($ret['chart_new']);
                unset($rs);
            }
        } catch (ThinkException $e) {
            ;
        }

        //统计
        /*$device_num = $m->count();
        $online_num = M('term_run_info')->where("is_online = 1 AND last_time >= '$tm'")->count();
        $today_flux = $ms->where("report_day = %d",date('Ymd'))->sum('flux');
        $today_task = $m->query(sprintf("SELECT COUNT(*)cc FROM term_task WHERE create_time >= '%s'", date('Y-m-d 00:00:00')));
        $month_flux = $ms->where("report_day >= %d", date('Ym01'))->sum('flux');*/

        //软件版本
        $rs = $m->getField('sw_version',true);
        foreach ($rs as $k => $v) {
            $sw_key = empty($v) ? L('VAR_UNKNOWN') : $v;
            if (!isset($data_swv[$sw_key])){
                $data_swv[$sw_key] = array('type'=>$sw_key, 'tem'=>0);
            }
            $data_swv[$sw_key]['tem'] += 1;
        }
        if (isset($data_swv)){
            $ret['chart_swv'] = array_values($data_swv);
        }
        unset($rs);
        unset($data_swv);

        //网络模式
        $nm = C('NET_MODE');
        $rs = M('term_run_info')->getField('net_mode', true);
        foreach ($rs as $k => $v) {
            if (!isset($data_netmode[$v])){
                $data_netmode[$v] = array('type'=>$nm[$v], 'tem'=>0);
            }
            $data_netmode[$v]['tem'] += 1;
        }
        if (isset($data_netmode)){
            $ret['chart_netmode'] = array_values($data_netmode);
        }
        unset($rs);
        unset($data_netmode);

        //任务统计
        $rs = $m->query("SELECT COUNT(*)num, term_task_detail.status FROM term_task_detail
            INNER JOIN term_task ON term_task.id = term_task_detail.task_id GROUP BY term_task_detail.status");
        $st = L('VAR_TASK_STATUS_ARR');
        foreach ($rs as $k=>$row){
            array_push($ret['chart_task'], array('type'=>$st[$row['status']], 'tem'=>intval($row['num'])));
        }
        $this->ajaxReturn($ret,'ok',0);
    }

    //获取RTU采集脚本
    public function loadRtuScript(){
        $sns = $_REQUEST['sns'];
        $data = '';
        //Rtu脚本
        if (!empty($sns) && strpos($sns, ',') === false) {
            $data = M('term_param')->where("sn = '%s'",$sns)->getField('rtu_script');
        }
        $this->ajaxReturn($data,'ok',0);
    }

    //指令下发页面
    public function loadCommonCmd(){
        $data = array(
            'rtds'=>array(),
            'cmds'=>array(),
        );
        $rs = M('rtu_data_set')->field('slave_id, addr, name, unit')->where('set_type = 0')->order('name ASC')->select();
        foreach ($rs as $k => $row) {
            $data['rtds'][] = array('id'=>$row['slave_id'].'_'.$row['addr'], 'name'=>$row['name']);
        }
        $rs = file_get_contents('common_cmd');
        if ($rs){
            $rs = json_decode($rs, true);
            foreach ($rs as $k => $row) {
                $data['cmds'][] = array('id'=>$row['value'], 'name'=>$row['name'], 'data_type'=>$row['data_type']);
            }
        }
        $this->ajaxReturn($data,'ok',0);
    }

    //获取设备信息
    public function loadTermBasicInfo(){
        $sn = $_REQUEST['sn'];
        $m = M('term');
        $models = C('TERM_MODEL');
        array_unshift($models, '未知');
        $groups = M('term_group')->where('id != 1')->field('id,name')->order('name ASC')->select();
        $row = $m->where("sn = '$sn'")->field('term_model, group_id, sim, alias')->find();
        if ($row){
            $row['term_model'] = $this->formatTermModel($row['term_model']);
            $row['term_model_index'] = 0;
            $row['term_group_index'] = 0;

            $index = 0;
            foreach ($models as $k => $v) {
                $row['term_model_arr'][] = array('id'=>$v, 'name'=>$v);
                if ($v == $row['term_model']){
                    $row['term_model_index'] = $index;
                }
                $index++;
            }

            foreach ($groups as $k => $g) {
                $row['term_group_arr'][] = array('id'=>$g['id'], 'name'=>$g['name']);
                if ($g['id'] == $row['group_id']){
                    $row['term_group_index'] = $k;
                }
            }
            //GPS位置
            $ret = $this->map($sn);
            $row['lng'] = $ret['lng'];
            $row['lat'] = $ret['lat'];
        }
        $this->ajaxReturn($row,'ok',0);
    }

    public function loadRtuData() {
        $m = M('rtu_data');
        $sn = I('sn','','string');
        $rs = $m->query("SELECT a.slave_id, a.addr, a.report_time, value, b.name, b.unit, b.min, b.max FROM rtu_data a INNER JOIN rtu_data_set b ON b.slave_id = a.slave_id AND b.addr = a.addr WHERE a.sn = '$sn'");
        foreach ($rs as $key => $row) {
            $rs[$key]['value'] = round($row['value'], 1);
            $rs[$key]['unit'] = $row['unit'] ? $row['unit'] : '';
        }
        $this->ajaxReturn($rs,'ok',0);
    }

    //传感量数据类型
    public function loadSensors(){
        $m = M('rtu_data_set');
        $set_type = I('set_type', '', 'string');
        $q = sprintf('%s AND %s', ($set_type=='0' ? 'set_type = 0' : '1=1'), $this->generate_search_str());
        $rs = $m->field("rtu_data_set.*")
            ->where($q)
            ->order($this->generate_order_str())
            ->limit(($this->pp['page']-1)*$this->pp['rp'], $this->pp['rp'])->select();
        foreach ($rs as $k => $row) {
            $rs[$k]['value_type'] = $this->valueType2Text($row['value_type']);
        }
        $total = $m->where($q)->count();
        $data = array(
            'page' => $this->pp['page'],
            'total' => ceil($total / $this->pp['rp']),
            'records' => $total,
            'rows' => $rs,
            'userdata' => array()
        );
        $this->ajaxReturn($data, 'ok', 0);
    }

    //传感量详细信息
    public function loadSensorInfo(){
        $id = I('id', 0, 'intval');
        $row = M('rtu_data_set')->where("id = $id")->find();
        $this->ajaxReturn($row, 'ok', 0);
    }

    // 获取openid
    public function getOpenid() {
        $url = sprintf('https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code', C('WX_APPID'), C('WX_APPSECRET'), I('js_code'));
        $ret = file_get_contents($url);
        $ret = json_decode($ret, true);
        if (!$ret) {
            die(json_encode(array('status' => 104, 'info' => 'Json parsing failed', 'data' => array())));
        } elseif (isset($ret['errcode'])) {
            die(json_encode(array('status' => 108, 'info' => sprintf('errcode = %d, errmsg = %s', $ret['errcode'], $ret['errmsg']), 'data' => array())));
        } else {
            die(json_encode(array('status' => 0, 'info' => L('OPERATING_SUCCESS'), 'data' => array('session_key' => $ret ? $ret['session_key'] : '', 'openid' => $ret ? $ret['openid'] : ''))));
        }
    }
}