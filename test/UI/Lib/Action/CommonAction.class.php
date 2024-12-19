<?php
define('UI_VERSION', 'Version 3.7.2'); //修改此参数的同时修改 system.swu_version
define('CACHE_VERSION', '2024-11-06 18:20:01'); //修改日期时间，以清除缓存
define('UI_RELEASE_DATE', substr(CACHE_VERSION, 0, 10));

class CommonAction extends Action{
    //百度地图错误信息
    protected $read_excel_failed_msg = 'Read failed: Please provide Excel 2003 file.';
    protected $pp = array();
    protected $lang = '';
    protected $oem = '';
	protected $ui_version = '';
    protected $userinfo = null;

	//去掉经纬度尾部的字母
	protected function lnglatFormat(&$lng, &$lat)
	{
		if (substr($lng, -1) === 'E')
		{
			$lng = substr($lng, 0, strlen($lng)-1);
		}
		elseif (substr($lng, -1) === 'W')
		{
			$lng = "-" . substr($lng, 0, strlen($lng)-1);
		}

		if (substr($lat, -1) === 'N')
		{
			$lat = substr($lat, 0, strlen($lat)-1);
		}
		elseif (substr($lat, -1) === 'S')
		{
			$lat = "-" . substr($lat, 0, strlen($lat)-1);
		}
	}

	//操作写入日志
	protected function wlog($username='', $cmd='', $value='', $device='', $ugid='', $value2='')
	{
		$ts = date('Y-m-d H:i:s');
		$m = M('log');
        if ($username == ''){
            $username = $_SESSION[C('SESSION_NAME')]['name'];
        }
        if ($ugid == ''){
            $ugid = $this->getUgid();
        }
        $ip = get_client_ip();
		$d = array(
			'username' => $username,
			'cmd' => $cmd,
			'value' => $value,
            'value_v2' => $value2,
			'device' => $device,
			'ugid' => $ugid,
            'ip' => $ip,
			'create_time' => $ts
		);
		if ($m->add($d)){
            import('@.ORG.Mlog');
            $log = new Mlog('./Log', 'task_log');
            $sep = "\t";
            $log->mwrite(sprintf("%s{$sep}%s[%s]{$sep}%s{$sep}value[%s]{$sep}value_v2[%s]{$sep}%s{$sep}\r\n", $ts, $username, $ip, $cmd, $value, $value2, $device));
		}
	}

	/**
	 * 获取用户组 gid
	 */
	protected function getUgid()
	{

        return $_SESSION[C('SESSION_NAME')]['gid'];
	}

	/**
	 * 获取用户组权限(tgid)
	 */
	protected function getTgids($format = 'string', $uid = 0)
	{
		$m = M('usr');
        $id = $uid == 0 ? $_SESSION[C('SESSION_NAME')]['id'] : $uid;
        $ugid = $m->where("id=$id")->getField('gid');
        if ($id == 1){
            $arr = M('term_group')->order('id ASC')->getField('id',true);
        }else{
            $arr = M('usr_group_privilege')->order('tgid ASC')->where("ugid=$ugid")->getField('tgid',true);
        }
		if (!isset($arr)){
			$arr = array();
		}
		return $format=='string' ? implode(',', $arr) : $arr;
	}

    protected function _empty()
    {
    	header("HTTP/1.0 404 Not Found");
    	$this->display("Public:404");
    	exit(0);
    }

    protected function sendmail($destEmail='', $title='', $content='', $extra='', $params=null, $attachment='')
    {
        $ssl = is_array($params) ? $params['ssl'] : C('E_SSL');
        // import('@.ORG.phpmailer');
        include_once './Lib/ORG/PHPMailer/PHPMailer.php';
        include_once './Lib/ORG/PHPMailer/SMTP.php';
        $mail = new PHPMailer();
        $mail->set('SMTPSecure', $ssl?'ssl':'');
        $mail->IsSMTP();        //使用SMTP方式发送
        $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = is_array($params) ? $params['auth'] : intval(C('E_SMTPAUTH')); //启用SMTP验证功能
        $mail->Port = is_array($params) ? $params['port'] : C('E_PORT'); //SMTP服务器端口
        $mail->SMTPDebug  = is_array($params) && $params['debug'] ? 1:0;  //启用SMTP调试功能

        $mail->Host     = is_array($params) ? $params['host'] : C('E_HOST');      //您的企业邮局域名
        $mail->Username = is_array($params) ? $params['account'] : C('E_EMAIL');     //邮局用户名(请填写完整的email地址)
        $mail->Password = is_array($params) ? $params['password'] : C('E_PASSWD');    //邮局密码
        $mail->From     = is_array($params) ? $params['account'] : C('E_EMAIL');     //邮件发送者email地址
        $mail->FromName = iconv('GBK', 'UTF-8', is_array($params) ? $params['from'] : C('E_FROM'));
        $mail->Hostname = C('E_HOSTNAME') ? C('E_HOSTNAME') : ''; //某些邮箱必须设置smtp客户端域名，否则提示错误信息：MAIL FROM command failed,Access denied - Invalid HELO name (See RFC2821 4.1.1.1)
        //收件人地址，格式是AddAddress("收件人email","收件人姓名")
        if (!is_array($destEmail)){
            $destEmail = explode(',', $destEmail);
        }
        foreach ($destEmail as $v){
            $mail->AddAddress($v, '');
        }

        // $mail->AddReplyTo("", "");
        if ($attachment) {
            // 添加附件
            $mail->AddAttachment($attachment);
        }
        $mail->IsHTML(true);        // 是否使用HTML格式

        $mail->Subject = $title;    // 邮件标题
        $mail->Body    = $content;  // 邮件内容
        $mail->AltBody = $extra;    // 附加信息，可以省略
        $send_ret = $mail->Send();
        import('@.ORG.Mlog');
        $log = new Mlog('./Log', 'email_log');
        $send_params = array(
            'ssl' => $ssl ? 1 : 0,
            'SMTPAuth' => $mail->SMTPAuth,
            'Port' => $mail->Port,
            'SMTPDebug' => $mail->SMTPDebug,
            'Host' => $mail->Host,
            'Username' => $mail->Username,
            'Password' => $mail->Password,
            'From' => $mail->From,
            'FromName' => $mail->FromName,
            'destEmail' => $destEmail,
            'Subject' => $mail->Subject,
            'Body' => $mail->Body,
            'AltBody' => $mail->AltBody
        );
        $log->mwrite(sprintf("[%s]\t send_params = %s, ret = %s\r\n", date('Y-m-d H:i:s'), json_encode($send_params), ($send_ret ? 'ok' : $mail->ErrorInfo)));
        if ($send_ret){
            return array('info'=>L('OPERATION_SUCCESS'), 'status'=>0);
        }else{
            return array('info'=>$mail->ErrorInfo, 'status'=>-1);
        }
    }

    /**
     * 使用正则验证数据
     * @access public
     * @param string $value  要验证的数据
     * @param string $rule 验证规则
     * @return boolean
     */
    protected function regex($value,$rule)
    {
        $validate = array(
            'require'   =>  '/.+/',
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency'  =>  '/^\d+(\.\d+)?$/',
            'number'    =>  '/^\d+$/',
            'zip'       =>  '/^\d{6}$/',
            'integer'   =>  '/^[-\+]?\d+$/',
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
            'english'   =>  '/^[A-Za-z]+$/',
            'tel'       =>  '/^1[34578]\d{9}$/'
        );
        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)]))
            $rule       =   $validate[strtolower($rule)];
        return preg_match($rule,$value)===1;
    }

    public function __construct()
    {
        ini_set('memory_limit', -1);
        $this->userinfo = $_SESSION[C('SESSION_NAME')];
        //Mysql配置，从session中读取
        if (C('SESSION_NAME') == 'oms_1.1_user') {
            C('DB_HOST', $_SESSION[C('SESSION_NAME')]['DB_HOST']);
            C('DB_PORT', $_SESSION[C('SESSION_NAME')]['DB_PORT']);
            C('DB_USER', $_SESSION[C('SESSION_NAME')]['DB_USER']);
            C('DB_PWD',  $_SESSION[C('SESSION_NAME')]['DB_PWD']);
            C('DB_NAME', $_SESSION[C('SESSION_NAME')]['DB_NAME'].'_'.$this->userinfo['pinfo']['name']);
        }
    	parent::__construct();

        $this->init_pp();

        //路径设置
        if (C('DATA_PATH') != ''){
            $path = C('DATA_PATH');
        }else{
            $path = str_replace("\\", '/', __FILE__);
            $path = str_replace('Lib/Action/CommonAction.class.php', '../data/', $path);
        }
    	C('FTP_WEB_PACK_PATH', $path);
        C('PROJECT_PATH', __ROOT__);

        //多语言设置
        if (C('IS_WLINK')){
            $tl = 'en-us';
        }else{
            $tl = strtolower($_COOKIE['think_language']);
            if ($tl == 'zh-hans-cn') {
                //IE - BUG
                $tl = 'zh-cn';
            }
            if (strpos(C('LANG_LIST'), $tl) === false){
                $tl = C('DEFAULT_LANG');
            }
        }
        $this->lang = $tl;
        $this->assign('think_language', $tl);
        if (!is_file('./Runtime/'.$tl.'.js')){
            $arr = L();
            $arr['curl'] = U('Index/replace');
            file_put_contents('./Runtime/'.$tl.'.js', 'var $lang='.json_encode($arr));
        }

        // 地图API设置
        C('MAP_API', $tl == 'zh-cn' ? C('GAODE_MAP_API') : C('GOOGLE_MAP_API'));
        C('MAP_STATIC_API', $tl == 'zh-cn' ? C('GAODE_MAP_STATIC_API') : C('GOOGLE_MAP_STATIC_API'));

        // server配置，由config.ini读取
        $ini_path = C('SERVER_INI_PATH');
        if (empty($ini_path)) {
            $ini_path = '../conf/';
        }
        $cfg = parse_ini_file($ini_path.'config.ini', true);
        C('SERVER_IP',      $cfg && isset($cfg['server']['ip'])          ? $cfg['server']['ip']        : '127.0.0.1');
        C('SERVER_PORT',    $cfg && isset($cfg['server']['port'])        ? $cfg['server']['port']      : 8000);
        C('GPS_PORT',       $cfg && isset($cfg['server']['gps_port'])    ? $cfg['server']['gps_port']  : 8001);
        C('GPS_DATA_FROM',  'FILE'); //默认由文件读取

        //页面打开前执行自定义sql
        $this->executeCustomSql();

        // 登录即有权限的操作
        $need_login = array(
            'Information' => array('ptgk', 'ptgkStatisticalInfo', 'loadDashboardData'),
            'Rtu' => array('dashboardData', 'qjt_rtu', 'rtuxq', 'refreshCurrentSensorValue', 'getAllSensorHistoryData', 'getSensorHistoryData', 'getModalHtml', 'loadLoraData',
                'loadCardData', 'lssj', 'getSensorData', 'dashboardManage', 'loadDashboards', 'dashboardEdit', 'dashboardView', 'dashboardOperating', 'getSensors',
                'loadDashboardData', 'rtugj', 'rtuRuleOp', 'getWarningInfo', 'delteCommonCmd', 'reReportProjectInfo', 'loadSensorTypeData', 'cdz', 'wfy', 'exportCPUMemoryStorage'
            ),
            'Syscfg' => array('alarmSendRecord', 'loadTermGroup', 'loadGroupRules', 'loadUsers', 'loadRoles', 'getModalHtml', 'loadFiles', 'addCanvasImg', 'loadCanvasImg',
                'deleteCanvasImg', 'loadSystemParams', 'loadLogData', 'loadUserLoginRecord', 'fwq', 'fwqyxjl', 'appServerEdit', 'loadUpgradeRecord'
            ),
            'Taskmgr' => array('rwlbStatisticalInfo', 'loadTaskDetail', 'loadTimedTaskDetail', 'loadTermTimedTask'),
            'Term' => array('getTermGroupTreeNodes', 'jklbStatisticalInfo', 'loadFenceRecordData', 'loadTermTasks', 'loadTermData', 'loadVpnData', 'loadOneNetData', 'termChartDataFlux', 'termChartDataSignal',
                'termChartDataOnline', 'termChartDataUsageRate', 'termChartDataNetmode', 'getModalHtml', 'getLnglatByIp', 'loadTermParams', 'loadLatestPos', 'generateLatestPos', 'loadGpsData', 'loadLoginRecordData',
                'loadNetChangeRecordData', 'checkUniqueModel', 'loadRecvData', 'geocode', 'getRtuScript', 'exportOfflineRate'
            ),
            'Xcx' => array('loadTermData', 'loadTermGroup', 'loadTermGroupTreenodes', 'loadTermDetail', 'loadTaskData', 'ptgkData', 'loadRtuScript', 'loadCommonCmd',
                'loadTermBasicInfo', 'loadRtuData', 'loadRtu', 'loadSensors', 'loadSensorInfo'
            ),
            'Mobile' => array('mterm', 'mtask', 'mreport', 'msetting', 'maction', 'mterminfo', 'mtermedit', 'mtaskinfo', 'mtaskprogress', 'mdownloadcfg', 'mupgrade',
                'mschrboot', 'mconfigset', 'msyscfg', 'mtype', 'msjcj', 'mrtuScriptGet', 'mrtuScriptSet', 'mcatchPackage', 'mclearFlash', 'mcleanRunInfo', 'editPass',
                'mrtuinfo', 'mcatchPackage', 'mrtuScriptSet'
            ),
            'Portal' => array('loadMobile', 'exportMobile', 'onlineRates', 'fluxTrend', 'loginTrend', 'editDataLimit', 'getModalHtml')
        );
        // 无需登录的操作
        $no_login = array(
            'Api' => array('m2m', 'getKey', 'GWList_RLY', 'GWList_RLY_ALL', 'DeviceSetToVR_RLY', 'SignalList', 'get_latest_gps', 'get_gps_trace'),
            'Cgi' => array('base_station_location', 'wifi_location', 'alarm', 'alarm_send', 'parse_rtu_script', 'checkOnlinePercent', 'rtu_alarm', 'uplaodPhoto', 'queryOneLinkData', 'oneNet', 'videoCallback', 'sendReport', 'taskCallback', 'dp1', 'dp2', 'dp3', 'deleteGPSData'),
            'DataUpload' => array('push', 'tcpPush'),
            'Index' => array('index', 'checkLogin', 'logout', 'changeLang'),
            'Xcx' => array('access', 'login', 'logout', 'getOpenid'),
            'Mdata' => array('loadTermData', 'loadLoraData', 'loadTaskData', 'loadTaskDetail', 'loadAdDownloadDetail', 'loadCfgData', 'loadUpgradePackageData'),
            'Mobile' => array('checkLogin', 'mlogin', 'mlogout'),
            'Task' => array('relayControl'),
            'Hemodialysis' => array('push', 'latestRecord', 'records')
        );

        //小程序-更新会话时间
        $sessid = trim($_REQUEST['sessid']);
        if ($sessid != ''){
            $sessrow = S($sessid);
            if ($sessrow){
                $_SESSION[C('SESSION_NAME')] = $sessrow;
                L(include './Lang/'.$sessrow['lang'].'/common.php');
                S($sessid, $sessrow, 3600);
            }
        }

        // gnss软件自动登录
        $token = trim($_REQUEST['token']);
        if (!empty($token)) {
            $org_cache_path = C('DATA_CACHE_PATH');
            C('DATA_CACHE_PATH', '../Runtime/Temp');
            if (S($token)) {
                $_SESSION[C('SESSION_NAME')] = $this->getUserInfo(S($token));
                $_SESSION[C('SESSION_NAME')]['pinfo'] = array('alias' => L('GNSS_SYSTEM_TITLE'));
            }
            C('DATA_CACHE_PATH', $org_cache_path);
        }

        // 3.0平台自动登录，开始------------------------------------
        if (!isset($_SESSION[C('SESSION_NAME')])){
            $cookie = cookie(C('SESSION_NAME'));
            if ($cookie){
                $arr = explode('|###|', base64_decode($cookie));
                $row = M('usr')->where("name = '%s' AND password = '%s'", $arr[0], $arr[1])->find();
                if ($row){
                    $row['member_since'] = date('Y.m.d',strtotime($row['create_time']));
                    $row['login_type'] = $arr[2];
                    $_SESSION[C('SESSION_NAME')] = $row;
                }else{
                    cookie(C('SESSION_NAME'), null);
                }
            }
        }
        if (isset($_SESSION[C('SESSION_NAME')]) && MODULE_NAME == 'Index' && ACTION_NAME == 'index'){
            if ($row['login_type'] == 'fzjh'){
                $login_to = U('Syscfg/fwq');
            } else {
                $login_to = U(C('LOGIN_JUMP_PAGE') ? C('LOGIN_JUMP_PAGE') : 'Information/ptgk');
            }
            header('Location:'.$login_to);
        }
        // 自动登录，结束------------------------------------

        // 开始检查权限
        if (isset($_SESSION[C('SESSION_NAME')]['usr_type'])) {
            $type = $_SESSION[C('SESSION_NAME')]['usr_type'];
        } else {
            $type = -1; //表示未登录用户
        }
        $auth = false;
        if (isset($no_login[MODULE_NAME]) && in_array(ACTION_NAME, $no_login[MODULE_NAME])) {
            $auth = true;
        } elseif ($type == 0) {
            $auth = true;
        } elseif ($type > 0) {
            if (isset($need_login[MODULE_NAME]) && in_array(ACTION_NAME, $need_login[MODULE_NAME])) {
                $auth = true;
            } else {
                $ps = get_usr_ps();
                if (isset($ps[MODULE_NAME]) && in_array(ACTION_NAME, $ps[MODULE_NAME])) $auth = true;
            }
        }
        if (!$auth) {
            $info = L($type == -1 ? 'SESSION_TIMEOUT' : 'PERMISSION_DENIED');
            if (ACTION_NAME == 'getModalHtml') {
                echo '<script>location.reload()</script>';
            } elseif (IS_AJAX || I('form_submit') == 'yes' || $sessid != '') {
                $this->ajaxReturn($type==-1?'timeout':'permission denied', $info, -1);
            } else if (strpos(C('SESSION_NAME'), 'oms_tr_user_') !== false) {
                echo '<script>alert("'.L('WINDOW_CLOSE_TIPS').'"); window.opener=null; window.close();</script>';
            } else {
                $this->error($info, MODULE_NAME=='Mobile' ? U('Mobile/mlogin') : U('Index/index'));
            }
            exit;
        }

        $this->loadConfigFromDb();
        $this->assign('lang', empty($tl) ? 'en-us' : $tl);
        $this->oem = strtoupper(C('OEM_VERSION'));
        $this->ui_version = strtoupper(C('UI_VERSION'));
        $this->assign('oem', $this->oem);
        $ut = L('VAR_USER_TYPE_TEXT');
        $this->assign('utt', $ut[$_SESSION[C('SESSION_NAME')]['usr_type']]);
        $page_nodes = file_get_contents('./Lib/ORG/usr_ps_def');
        $this->assign('page_nodes', !empty($page_nodes) ? $page_nodes : '[]');
        $this->assign('href_src', C('USE_QINIU') ? array('data-href-bak', 'href', 'data-src-bak', 'src') : array('href', 'data-href-bak', 'src', 'data-src-bak')); //是否使用七牛云存储加载部分css/JS文件
        $this->generateMenuString($need_login, $no_login);

        //针对用户组，自定义term_model显示
        if ($this->oem == 'ZDC' && $_SESSION[C('SESSION_NAME')]['gid'] == 0) {
            $tmp_term_model = C('TERM_MODEL');
            $tmp_term_model['D20'] = 'D29S';
            C('TERM_MODEL', $tmp_term_model);
        }
    }

    private function loadConfigFromDb() {
        $m = M('system_config');
        $rs = $m->where("name<>''")->order('name ASC')->field('name,value')->select();
        foreach ($rs as $k => $row) {
            $c[$row['name']] = $row['value'];
        }

        $configs = array(
            /*告警通用设置
            array('db_name'=>'alarm_enable_email',     'def_value'=>'0',   'cname'=>'ALARM_ENABLE_EMAIL',      'hide'=>0),
            array('db_name'=>'alarm_enable_wechat',    'def_value'=>'0',   'cname'=>'ALARM_ENABLE_WECHAT',     'hide'=>0),
            array('db_name'=>'alarm_receivers',        'def_value'=>'[]',  'cname'=>'ALARM_RECEIVERS',         'hide'=>0),
            array('db_name'=>'web_url',                'def_value'=>'http://127.0.0.1:'.$_SERVER['SERVER_PORT'].__ROOT__,  'cname'=>'WEB_URL', 'hide'=>0),*/
            //邮件配置
            array('db_name'=>'email_config_host',        'def_value'=>'',     'cname'=>'E_HOST',      'hide'=>0),
            array('db_name'=>'email_config_ssl',         'def_value'=>'0',    'cname'=>'E_SSL',       'hide'=>0),
            array('db_name'=>'email_config_port',        'def_value'=>'',     'cname'=>'E_PORT',      'hide'=>0),
            array('db_name'=>'email_config_account',     'def_value'=>'',     'cname'=>'E_EMAIL',     'hide'=>1),
            array('db_name'=>'email_config_password',    'def_value'=>'',     'cname'=>'E_PASSWD',    'hide'=>1),
            array('db_name'=>'email_config_from',        'def_value'=>'',     'cname'=>'E_FROM',      'hide'=>0),
            array('db_name'=>'email_config_smtp_auth',   'def_value'=>'1',    'cname'=>'E_SMTPAUTH',  'hide'=>0),
            //微信配置
            array('db_name'=>'weixin_config_corpid',        'def_value'=>'',    'cname'=>'WX_CORPID',      'hide'=>1),
            array('db_name'=>'weixin_config_corpsecret',    'def_value'=>'',    'cname'=>'WX_CORPSECRET',  'hide'=>1),
            array('db_name'=>'weixin_config_agentid',       'def_value'=>'',    'cname'=>'WX_AGENTID',     'hide'=>1),
            array('db_name'=>'weixin_config_txl_secret',    'def_value'=>'',    'cname'=>'WX_TXL_SECRET',  'hide'=>1),
            //串口短信
            array('db_name'=>'serial_sms_config_com_num',   'def_value'=>'1',   'cname'=>'S_SMS_COM_NUM',  'hide'=>1),
            /* 改为了每个用户单独配置了
            //设备离线告警
            array('db_name'=>'alarm_interval_offline',                'def_value'=>'60',  'cname'=>'ALARM_INTERVAL_OFFLINE',        'hide'=>0),
            array('db_name'=>'alarm_term_offline_num',                'def_value'=>'0',   'cname'=>'ALARM_TERM_OFFLINE_NUM',        'hide'=>0),
            array('db_name'=>'alarm_term_offline_num_threshold',      'def_value'=>'1',   'cname'=>'ALARM_TERM_OFFLINE_NUM_T',      'hide'=>0),
            array('db_name'=>'alarm_term_offline_percent',            'def_value'=>'0',   'cname'=>'ALARM_TERM_OFFLINE_PERCENT',    'hide'=>0),
            array('db_name'=>'alarm_term_offline_percent_threshold',  'def_value'=>'10',  'cname'=>'ALARM_TERM_OFFLINE_PERCENT_T',  'hide'=>0),
            array('db_name'=>'alarm_term_offline_time',               'def_value'=>'0',   'cname'=>'ALARM_TERM_OFFLINE_TIME',       'hide'=>0),
            array('db_name'=>'alarm_term_offline_time_threshold',     'def_value'=>'30',  'cname'=>'ALARM_TERM_OFFLINE_TIME_T',     'hide'=>0),
            //VPN离线告警
            array('db_name'=>'alarm_interval_vpn',                    'def_value'=>'60',  'cname'=>'ALARM_INTERVAL_VPN',            'hide'=>0),
            array('db_name'=>'alarm_vpn_offline_time',                'def_value'=>'0',   'cname'=>'ALARM_VPN_OFFLINE_TIME',        'hide'=>0),
            array('db_name'=>'alarm_vpn_offline_time_threshold',      'def_value'=>'5',   'cname'=>'ALARM_VPN_OFFLINE_TIME_T',      'hide'=>0),
            //信号强度告警
            array('db_name'=>'alarm_interval_signal',         'def_value'=>'60', 'cname'=>'ALARM_INTERVAL_SIGNAL',  'hide'=>0),
            array('db_name'=>'alarm_term_signal',             'def_value'=>'0',  'cname'=>'ALARM_TERM_SIGNAL',      'hide'=>0),
            array('db_name'=>'alarm_term_signal_threshold',   'def_value'=>'5',  'cname'=>'ALARM_TERM_SIGNAL_T',    'hide'=>0),
            //流量告警
            array('db_name'=>'alarm_interval_flux',                'def_value'=>'1440',  'cname'=>'ALARM_INTERVAL_FLUX',          'hide'=>0),
            array('db_name'=>'alarm_term_flux_month',              'def_value'=>'0',     'cname'=>'ALARM_TERM_FLUX_MONTH',        'hide'=>0),
            array('db_name'=>'alarm_term_flux_month_threshold',    'def_value'=>'500',   'cname'=>'ALARM_TERM_FLUX_MONTH_T',      'hide'=>0),
            array('db_name'=>'alarm_term_flux_day',                'def_value'=>'0',     'cname'=>'ALARM_TERM_FLUX_DAY',          'hide'=>0),
            array('db_name'=>'alarm_term_flux_day_threshold',      'def_value'=>'10',    'cname'=>'ALARM_TERM_FLUX_DAY_T',        'hide'=>0),
            array('db_name'=>'alarm_term_flux_pool',               'def_value'=>'0',     'cname'=>'ALARM_TERM_FLUX_POOL',         'hide'=>0),
            array('db_name'=>'alarm_term_flux_pool_threshold',     'def_value'=>'1024',  'cname'=>'ALARM_TERM_FLUX_POOL_T',       'hide'=>0),
            */
            //电子围栏
            array('db_name'=>'enable_electronic_fence',     'def_value'=>'0',  'cname'=>'ENABLE_ELECTRONIC_FENCE',  'hide'=>0),
            // array('db_name'=>'alarm_interval_fence',        'def_value'=>'60', 'cname'=>'ALARM_INTERVAL_FENCE',     'hide'=>0),
            // array('db_name'=>'alarm_fence',                 'def_value'=>'0',  'cname'=>'alarm_fence',              'hide'=>0),

            //其它
            array('db_name'=>'change_sim_pos_flux',         'def_value'=>'0',  'cname'=>'CHANGE_SIM_POS_FLUX',      'hide'=>0),
            array('db_name'=>'backup_db',                   'def_value'=>'0',  'cname'=>'BACKUP_DB',                'hide'=>0),
            array('db_name'=>'mysqldump_path',              'def_value'=>'',   'cname'=>'MYSQLDUMP_PATH',           'hide'=>0),
            array('db_name'=>'term_group_rule_mode',        'def_value'=>'0',  'cname'=>'TERM_GROUP_RULE_MODE',     'hide'=>0),
            //软件升级
            array('db_name'=>'swu_name',        'def_value'=>C('SOFT_NAME') ? C('SOFT_NAME') : 'm2m_3.0',   'cname'=>'SWU_NAME',      'hide'=>0),
            array('db_name'=>'swu_version',     'def_value'=>'3.6.23',                                      'cname'=>'SWU_VERSION',   'hide'=>0),
            array('db_name'=>'swu_server',      'def_value'=>'http://8.129.227.14/upgrade_server/',         'cname'=>'SWU_SERVER',    'hide'=>0),
            //移动设备配置
            array('db_name'=>'enable_device_day_flux_limit',    'def_value'=>'0',  'cname'=>'ENABLE_DEVICE_DAY_FLUX_LIMIT',     'hide'=>0),
            array('db_name'=>'enable_device_month_flux_limit',  'def_value'=>'0',  'cname'=>'ENABLE_DEVICE_MONTH_FLUX_LIMIT',   'hide'=>0),
            //删除GPS参数配置
            array('db_name'=>'auto_clear_gps',    'def_value'=>'0',  'cname'=>'AUTO_CLEAR_GPS',     'hide'=>1),
            array('db_name'=>'gps_reserve_days',  'def_value'=>'30',  'cname'=>'GPS_RESERVE_DAYS',   'hide'=>1),
        );

        foreach ($configs as $k => $row) {
            if (!isset($c[$row['db_name']])){
                $dall[] = array('name'=>$row['db_name'], 'value'=>$row['def_value'], 'hide'=>$row['hide']);
                C($row['cname'], $row['def_value']);
            }else{
                C($row['cname'], $c[$row['db_name']]);
            }
        }

        if (isset($dall)){
            $m->addAll($dall);
        }

        // web_url
        $real_web_url = 'http://127.0.0.1:'.$_SERVER['SERVER_PORT'].__ROOT__;
        if ($c['web_url'] != $real_web_url && IS_WIN) {
            $m->where("name = 'web_url'")->save(array('value' => $real_web_url));
            exec('sc stop m2m_task_server', $output, $ret);
            sleep(1);
            exec('sc start m2m_task_server', $output, $ret);
        }
    }

    //今日流量
    protected function get_day_flux($flux, $last_time){
        if (!$last_time || $last_time == '0000-00-00 00:00:00'){
            return '0 Bytes';
        }
        return date('Y-m-d') == substr($last_time, 0, 10) ? bitsize($flux) : '0 Bytes';
    }

    //本月流量
    protected function get_month_flux($flux, $last_time, $month_flux_onelink, $api_query_time) {
        $onelink = C('SHOW_ONELINK_MONTH_FLUX');
        $ym = date('Y-m');
        if ($onelink) {
            return !$api_query_time || $ym != substr($api_query_time, 0, 7) ? '0 Bytes' : bitsize($month_flux_onelink * 1024);
        }
        if (!$last_time || $last_time == '0000-00-00 00:00:00'){
            return '0 Bytes';
        }
        return $ym == substr($last_time, 0, 7) ? bitsize($flux) : '0 Bytes';
    }

    //获取分页参数
    private function init_pp(){
        $this->pp['page']  = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $this->pp['rp']    = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;     //每页显示多少条数据
        $this->pp['sort']  = $_REQUEST['sidx']; //排序字段
        $this->pp['order'] = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'ASC';
    }

    //搜索条件
    protected function generate_search_str(){
        if (isset($_REQUEST['searchString'])){
            $type = trim($_REQUEST['searchType']);
            $v = '%'.trim($_REQUEST['searchString']).'%';
            switch ($type) {
                case 'term':
                    $q = "(term.sn LIKE '$v' OR term.ud_sn LIKE '$v' OR term.alias LIKE '$v' OR term.iccid LIKE '$v' OR term.vsn LIKE '$v')";
                    break;
                case 'term_group':
                    $q = "(term_group.name LIKE '$v')";
                    break;
                case 'file_list':
                    $q = "(original_filename LIKE '$v')";
                    break;
                case 'rtu_data_set':
                    $q = "(rtu_data_set.name LIKE '$v')";
                    break;
                case 'rtu_warning':
                    $q = "(rtu_warning.sn LIKE '$v')";
                    break;
                case 'task':
                    $q = "(term_task.username LIKE '$v')";
                    break;
                case 'timed_term_task':
                    $q = "(timed_term_task.username LIKE '$v')";
                    break;
                case 'term_task_detail':
                    $q = "(term_task_detail.sn LIKE '$v')";
                    break;
                case 'timed_term_task_detail':
                    $q = "(timed_term_task_detail.sn LIKE '$v')";
                    break;
                case 'usr_group':
                    $q = "(usr_group.name LIKE '$v')";
                    break;
                case 'usr':
                    $q = "(usr.name LIKE '$v')";
                    break;
                case 'app_server':
                    $q = "(app_server.name LIKE '$v')";
                    break;
                case 'ad':
                    $q = "(ad.name LIKE '$v')";
                    break;
                case 'term_transfer_config':
                    $q = "(term_transfer_config.name LIKE '$v')";
                    break;
                case 'term_alarm_record':
                    $q = "(term_alarm_record.receiver_name LIKE '$v')";
                    break;
                case 'oem_charge_station':
                    $q = "(station_id LIKE '$v')";
                    break;
                case 'oem_lixun_vna':
                    $q = "(oem_lixun_vna.ip LIKE '$v' OR oem_lixun_vna.name LIKE '$v' OR oem_lixun_vna.info LIKE '$v')";
                    break;
                default:
                    $q = '1=1';
                    break;
            }
        }else{
            $q = '1=1';
        }
        return $q;
    }

    //排序字段
    protected function generate_order_str($type = ''){
        $sort = $this->pp['sort'];
        $order = strtoupper($this->pp['order']);
        if ($type == 'router_list') {
            if ($sort == 'status') {
                return $order == 'DESC' ? "is_online DESC,last_time DESC" : 'last_time ASC';
            } elseif ($sort == 'status_vc') {
                return $order == 'DESC' ? "is_online_vc DESC,last_time_vc DESC" : 'last_time_vc ASC';
            }
        } elseif ($type == 'mobile') {
            $t = I('type');
            if ($sort == 'last_time' && $t == 'history') {
                return sprintf('logout_time %s', $order);
            }
        }
        return "$sort $order";
    }

    protected function getNetmodeColor($type){
        if ($type >= 5 && $type <= 10){
            // $color = '#FFFF94';
            $color = 'yellow';
            $nm = '3G';
        }elseif($type >= 11 && $type <= 12){
            $color = '#2F7ED8';
            $nm = '4G';
        }elseif($type >= 1 && $type <= 4){
            $color = '#D6D6D6';
            $nm = '2G';
        }else{
            $color = '#D6D6D6';
            $nm = 'None';
        }
        return array('color'=>$color, 'nm'=>$nm);
    }

    //显示定时重启
    protected function displaySrestart(&$data, $value){
        $tmp = str_replace('sch_rboot=', '', $value);
        $tmp = explode(',', str_replace('"','',$tmp));
        $h = intval($tmp[1]/60);
        if ($h < 10){
            $h = '0'.$h;
        }
        $mi = intval($tmp[1]%60);
        if ($mi < 10){
            $mi = '0'.$mi;
        }
        $interval = decbin($tmp[2]);
        $interval = strrev(str_repeat('0',8-strlen($interval)) . $interval);
        $tl = strtolower($_COOKIE['think_language']);
        if ($tl == 'zh-cn' || $tl == 'zh-tw'){
            $arr = array('周天','周一','周二','周三','周四','周五','周六');
        }else{
            $arr = array(' Sun',' Mon',' Tue',' Wed',' Thu',' Fri',' Sat');
        }
        unset($tmp);
        for ($i=0; $i<8; $i++){
            if (substr($interval,$i,1) == '1'){
                $tmp[] = $arr[$i];
            }
        }
        $data['cmd'] = L('SCHEDULED_REBOOT');
        $data['value'] = sprintf("%s, %s", $h.':'.$mi, implode(',',$tmp));
        $data['value'] = sprintf("%s%s %s:%s", L('EACH'), implode(',',$tmp), $h, $mi);
    }

    //处理term表某些字段
    protected function transformTermFields(&$row){
        $netMode = C('NET_MODE');
        $cla = L('CURRENT_LINK_ARR');
        $now = date('Y-m-d H:i:s');
        //路由器流量是字节, rtu流量是字节
        $row['flux'] = bitsize($row['flux']);
        $row['day_flux_original'] = $row['day_flux'];
        $row['day_flux'] = $this->get_day_flux($row['day_flux'], $row['last_time']);
        $row['month_flux'] = $this->get_month_flux($row['month_flux'], $row['last_time'], $row['month_flux_onelink'], $row['api_query_time']);
        $row['last_7days_flux'] = bitsize($row['last_7days_flux']);
        $row['flux_sim1'] = bitsize($row['flux_sim1']);
        $row['flux_sim2'] = bitsize($row['flux_sim2']);
        //网络模式
        $row['net_mode'] = $netMode[$row['net_mode']];
        $row['net_mode_sim1'] = $netMode[$row['net_mode_sim1']];
        $row['net_mode_sim2'] = $netMode[$row['net_mode_sim2']];

        //通信协议版本
        $row['protocol'] = intval($row['protocol']/10) . '.' . $row['protocol']%10;

        $row['current_link_text'] = $cla[$row['current_link']];
        $row['current_link'] = $row['current_link_text'];

        //流量限制
        if (!isset($row['status_limit'])){
            $row['status_limit'] = $row['status'];
            //流量限制状态，当system_config.enable_electronic_fence = 1 && term_gps.fstatus = 1时，将status_limit设置为1
            if ($row['status_limit'] != 1 && M('system_config')->where("name = 'enable_electronic_fence'")->getField('value') == '1' && $row['fstatus'] == '1') {
                $row['status_limit'] = 1;
            }
        }
        $row['status_limit_text'] = L($row['status_limit'] == 1 ? 'VAR_YES':'VAR_NO');

        //终端是否在线
        $s = get_term_status_code($row['diff'], $row['is_online']);
        $row['status'] = get_term_status_str($s, $row['status_limit']);
        $row['online_duration'] = $s=='0' ? 0 : format_time($row['login_time'], $row['last_time']);
        $row['offline_duration'] = $s=='1' ? 0 : format_time($row['last_time'], $now);

        if (C('OEM_VERSION') == 'th-m2m') {
            // 上传流量
            $seconds = substr($row['last_time'], 11, 2) * 3600 + substr($row['last_time'], 14, 2) * 60 + substr($row['last_time'], 17, 2);
            $row['tx'] = ($s=='0' ? 0 : sprintf("%.2f", $row['day_flux_up']/1024/$seconds)) . ' KB/S';
            // 下载流量
            $row['rx'] = ($s=='0' ? 0 : sprintf("%.2f", ($row['day_flux_original'] - $row['day_flux_up'])/1024/$seconds)) . ' KB/S';
        }

        //sim1, sim2是否在线
        if (C('OEM_VERSION') == 'rx-m2m') {
            $s_sim1 = get_term_status_code($row['diff_sim1'], $row['is_online']);
            $row['status_sim1'] = get_term_status_str($s_sim1, $row['status_limit']);
            $s_sim2 = get_term_status_code($row['diff_sim2'], $row['is_online']);
            $row['status_sim2'] = get_term_status_str($s_sim2, $row['status_limit']);
        }

        //信号量图标
        $row['term_signal'] = get_term_signal_str($s, $row['term_signal']);
        $row['term_signal_sim1'] = get_term_signal_str($s, $row['term_signal_sim1']);
        $row['term_signal_sim2'] = get_term_signal_str($s, $row['term_signal_sim2']);

        //设备型号
        $row['term_model'] = $this->formatTermModel($row['term_model']);
        $row['term_model_text'] = $this->getTermModelText($row['term_model']);
    }

    // 将设备型号“RT52-FT2T-W”处理为“RT52”
    protected function formatTermModel($v) {
        $v = strtoupper($v);
        $i = strpos($v, '-');
        if ($i){
            $v = substr($v, 0, $i);
        }
        return $v;
    }

    // 获取设备类型显示值
    protected function getTermModelText($v, $format = 0) {
        if ($format) {
            $v = $this->formatTermModel($v);
        }
        $v = strtoupper($v);
        $tm = C('TERM_MODEL');
        return isset($tm[$v]) ? $tm[$v] : '';
    }

    //term参数赋值到页面
    protected function assignTermRow($sn, $ret = false){
        $rs = M('term')->query("SELECT a.*, b.*, c.name AS gname, d.fstatus, e.month_flux AS month_flux_onelink, e.api_query_time,
            tc.lac, tc.cellid, tc.addr, twa.ap_mac, twa.addr AS addr2, 0 AS vpn_num FROM term a
            LEFT JOIN term_cell AS tc ON tc.sn = a.sn
            LEFT JOIN term_wifi_ap AS twa ON twa.sn = a.sn
            LEFT JOIN term_run_info b ON b.sn = a.sn
            LEFT JOIN term_group c ON c.id = a.group_id
            LEFT JOIN term_gps d ON d.sn = a.sn
            LEFT JOIN oem_onelink_flux e ON e.sn = a.sn
            WHERE a.sn = '$sn'");
        $row = $rs[0];
        $ts = time();
        if ($row['lac'] && $row['cellid']) {
            $row['lac_cellid'] = $row['lac'].','.$row['cellid'];
        }
        $rs2 = M('term_virtual_channel')->where("is_online = 1 AND sn = '%s'", $row['sn'])->field('is_online, last_time')->select();
        foreach ($rs2 as $k => $ro) {
            if (get_term_status_code($ts - strtotime($ro['last_time']), 1, true) == '1') {
                $row['vpn_num'] += 1;
            }
        }
        $row['sim_pos'] = $row['sim_pos'] == 0 ? sprintf('%s 1 + %s 2', L('CARD'), L('CARD')) : sprintf('%s %d', L('CARD'), $row['sim_pos']);
        $row['diff'] = $ts - strtotime($row['last_time']);
        $row['addr'] = $row['addr'] ? $row['addr'] : $row['addr2'];
        $row['term_model_text'] = $this->getTermModelText($row['term_model']);
        $row['cpu_usage'] = $row['cpu_usage'] . '%';
        $row['mem_usage'] = $row['mem_usage'] . '%';
        $row['storage_usage'] = $row['storage_usage'] . '%';
        $this->transformTermFields($row);
        if ($ret){
            return $row;
        }
        $this->assign('row',$row);
    }

    private function executeCustomSql() {
        // 初始化page_nodes表
        $f = './Lib/ORG/usr_ps_sql';
        if (M('page_nodes')->count() == 0 && file_exists($f)) {
            $str = explode("\r\n", file_get_contents($f));
            foreach ($str as $v) {
                $v = trim($v);
                if (strpos($v, 'INSERT') === false) continue;
                M('')->execute($v);
            }
        }
        // oem客户，删除用户登录记录
        if (C('OEM_VERSION') == 'TW-CDC') {
            M('usr_login_record')->where("province != 'Taiwan'")->delete();
        }
    }

    // 生成左侧菜单
    private function generateMenuString($need_login, $no_login) {
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        if ($uid != 1) {
            $nodes = get_menu_nodes();
        }
		if ($_SESSION[C('SESSION_NAME')]['login_type'] == 'fzjh') {
			$filename = 'menu_fzjh.json';
		} elseif ($this->ui_version == 'RTU') {
			$filename = 'menu_rtu.json';
		} else {
			$filename = 'menu.json';
		}
        $json = file_get_contents('./Conf/'.$filename);
        if (empty($json)) {
            return;
        }
        $json = json_decode($json,true);
        if (!$json) {
            return;
        }
        $uid = intval($_SESSION[C('SESSION_NAME')]['id']);
        $menus = '';
        $lt = M('system_config')->where("name = 'licence_type'")->getField('value');
        if (is_null($lt)) {
            $lt = '0';
        }
        foreach ($json as $k => $row) {
            if ($k == 'Rtu' && !in_array($lt, array('0','2'), true)){
                continue;
            }
            if ($k == 'Portal' && !C('ENABLE_PORTAL')){
                continue;
            }

            // 节点权限判断 - module
            if ($uid != 1 && !in_array($k, $nodes[0], true)) {
                continue;
            }

            if (count($row['childrens']) == 0 && $row['action']){
                //没有子菜单
                $menus .= sprintf('<li class="treenode %s%s">
                            <a href="'.U($k.'/'.$row['action']).'">
                             <i class="fa '.$row['icon'].'"></i> <span>'.L($row['lang']).'</span>
                            </a>
                        </li>', $row['action'], (MODULE_NAME==$k ? ' active' : ''));
            }else{
                //包含子菜单
                $childrens = '';
                foreach ($row['childrens'] as $key => $child) {
                    // 节点权限判断 - page
                    if ($uid != 1 && !in_array($k.'_'.$child['action'], $nodes[1], true) && !in_array($child['action'], $need_login[$k], true)) {
                        continue;
                    }
                    if ($child['action'] == 'rjsj' && !IS_WIN) {
                        continue;
                    }
                    if ($child['action'] == 'grzx' && C('SESSION_NAME') == 'gnss_soft_user') {
                        continue;
                    }
                    if ($child['action'] == 'cdz' && !C('SHOW_CDZ')) {
                        continue;
                    }
                    if ($child['action'] == 'wfy' && !C('SHOW_WFY')) {
                        continue;
                    }
                    $childrens .= sprintf('<li class="%s%s"><a href="%s"><i class="fa fa-angle-right"></i>%s</a></li>',
                        $child['action'], (MODULE_NAME==$k && ACTION_NAME==$child['action'] ? ' active' : ''),
                        U($k.'/'.$child['action']), L($child['lang']));
                }
                $menus .= sprintf('<li class="treeview %s%s">
                  <a href="javascript:;">
                    <i class="fa %s"></i> <span>%s</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                  </a>
                  <ul class="treeview-menu">%s
                  </ul>
                </li>', $k, (MODULE_NAME==$k ? ' active menu-open' : ''), $row['icon'], L($row['lang']), $childrens);
                if (MODULE_NAME == $k){
                    $this->assign('web_path_0', sprintf('<li><a href="%s"><i class="fa %s"></i> %s</a></li>',
                        U($k.'/'.$row['childrens'][0]['action']), $row['icon'], L($row['lang']))
                    );
                }
            }
        }
        $this->assign('menus', $menus);
        $this->assign('uname', $_SESSION[C('SESSION_NAME')]['name']);
    }

    protected function valueType2Length($type){
        switch ($type) {
            case 1:
                return 4;
                break;
            case 2:
                return 2;
                break;
            case 3:
                return 1;
                break;
            case 4:
                return 4;
                break;
            case 5:
                return 8;
                break;
            default:
                return 0;
                break;
        }
    }

    protected function valueType2Text($type){
        switch ($type) {
            case 1:
                $t = L('FOUR_BYTES');
                break;
            case 2:
                $t = L('DOUBLE_BYTES');
                break;
            case 3:
                $t = L('SINGLE_BYTES');
                break;
            case 4:
                return L('ANALOG_QUANTITY');
                break;
            case 5:
                $t = L('EIGHT_BYTES');
                break;
            default:
                $t = '';
                break;
        }
        return $t;
    }

    //通过导出类型(term,group,all)，获取term - ids
    protected function getDestTerms($m = null){
        if (!$m) $m = M('term');
        $dest = $_REQUEST['dest'];
        if ($dest == 'term') {
            $ids = I('term_list','','string');
            $ids = explode(',',$ids);
        } elseif ($dest == 'group') {
            $gid = I('gid',0,'intval');
            if ($gid == -10) {
                $dest = 'all';
            }else{
                $ids = $m->where("group_id = %d",$gid)->getField('sn',true);
            }
        }
        if ($dest == 'all') {
            $tgids = $this->getTgids();
            $ids = $m->where("group_id IN(%s)",$tgids)->getField('sn',true);
        }
        return $ids;
    }

    /* 获取一台设备的当前位置
     * sn = 4c065828 查询一台设备的gps，返回 array(113.12, 22.345)
     * sn = '4c065828','4c065829','4c065830' 查询多台设备的gps，返回如下
     * array(
     *     array('sn'=>'4c065828', 'lng'=>113.12, 'lat'=> 22.345),
     *     array('sn'=>'4c065829', 'lng'=>113.12, 'lat'=> 22.345),
     *     array('sn'=>'4c065830', 'lng'=>113.12, 'lat'=> 22.345),
     *    )
     * ret_def_gps = true 表示：如果term_gps表没有数据就返回默认gps位置
     */
    protected function map($sn = '', $multiple = false, $ret_def_gps = true){
        $m = M('term');
        if ($ret_def_gps){
            $t = array('lng'=>C('GPS_DEFAULT_LNG'), 'lat'=>C('GPS_DEFAULT_LAT'));
        }else{
            $t = array('lng'=>0, 'lat'=>0);
        }

        if ($multiple){
            $rs = M('term_gps')->where("sn IN($sn)")->field('sn, longitude, latitude, report_time')->select();
        } else {
            $row = M('term_gps')->where("sn = '$sn'")->order('report_time DESC')->field('sn, latitude, longitude, report_time, fstatus')->limit(1)->find();
        }

        if ($rs) {
            foreach ($rs as $k => $row) {
                $this->lnglatFormat($rs[$k]['longitude'], $rs[$k]['latitude']);
            }
        }else{
            $rs = array();
        }

        import('@.ORG.Gps');
        $gps = new Gps();
        if ($row) {
            $lng = $row['longitude'];
            $lat = $row['latitude'];
            $this->lnglatFormat($lng, $lat);
            $t['sn'] = $row['sn'];
            $t['lng'] = floatval($lng);
            $t['lat'] = floatval($lat);
            $t['ts'] = $row['report_time'];
        }
        $ret_fence = I('ret_fence', 0, 'intval');
        if ($ret_fence == 1) {
            $l = $this->lang;
            $t['fstatus'] = intval($row['fstatus']);
            $fence = array();
            $rs = M('term_electronic_fence')->where("sn = '%s'", $sn)->field('ftype, fvalue')->order('ftype ASC')->select();
            foreach ($rs as $key => $row) {
                if ($row['ftype'] == 1) {
                    preg_match("/^lon1=(.*)\&lat1=(.*)\&radius=(.*)$/", $row['fvalue'], $matchs);
                    if ($l == 'zh-cn') {
                        $ret = $gps->gcj_encrypt($matchs[2], $matchs[1]);
                        $matchs[1] = $ret['lon'];
                        $matchs[2] = $ret['lat'];
                    }
                    array_push($fence, array('ftype' => 1, 'lat' => $matchs[2], 'lng' => $matchs[1], 'radius' => $matchs[3]));
                } elseif ($row['ftype'] == 2) {
                    preg_match("/^lon1=(.*)\&lat1=(.*)\&lon2=(.*)\&lat2=(.*)$/", $row['fvalue'], $matchs);
                    if ($l == 'zh-cn') {
                        $ret1 = $gps->gcj_encrypt($matchs[2], $matchs[1]);
                        $ret2 = $gps->gcj_encrypt($matchs[4], $matchs[3]);
                        $matchs[1] = $ret1['lon'];
                        $matchs[2] = $ret1['lat'];
                        $matchs[3] = $ret2['lon'];
                        $matchs[4] = $ret2['lat'];
                    }
                    array_push($fence, array('ftype' => 2, 'lng1' => $matchs[1], 'lat1' => $matchs[2], 'lng2' => $matchs[3], 'lat2' => $matchs[4]));
                } elseif ($row['ftype'] == 3) {
                    $fvalue = explode('&', $row['fvalue']);
                    $points = array();
                    for ($i=0; $i<count($fvalue); $i+=2) {
                        $lng = explode('=', $fvalue[$i]);
                        $lng = $lng[1];
                        $lat = explode('=', $fvalue[$i+1]);
                        $lat = $lat[1];
                        if ($l == 'zh-cn') {
                            $ret = $gps->gcj_encrypt($lat, $lng);
                            $lng = $ret['lon'];
                            $lat = $ret['lat'];
                        }
                        array_push($points, array($lat, $lng));
                    }
                    array_push($fence, array('ftype' => 3, 'points' => $points));
                }
            }
            $t['fence'] = $fence;
        }

        if ($this->lang == 'zh-cn'){
            if ($multiple && count($rs) > 0){
                foreach ($rs as $k => $row) {
                    $ret = $gps->gcj_encrypt($row['latitude'], $row['longitude']);
                    $rs[$k]['longitude'] = $ret['lon'];
                    $rs[$k]['latitude'] = $ret['lat'];
                }
            }
            if (!$multiple && !empty($t['lng'])) {
                $ret = $gps->gcj_encrypt($t['lat'], $t['lng']);
                $t['lng'] = $ret['lon'];
                $t['lat'] = $ret['lat'];
            }
        }
        return $multiple ? $rs : $t;
    }

    /**
     * [read_stat description]
     * @param string $sn
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     * @param string $fields 查询字段传*为全部，传空为 flux, net_mode, hb_count, sum_signal, report_day
     * @param string $q 查询条件
     * @return array
     */
    protected function read_stat($sn, $start, $end, $fields, $q){
        if ($fields == '') {
            $fields = 'flux, net_mode, hb_count, sum_signal, report_day';
        }
        if ($q == ''){
            $q = '1 = 1';
        }
        $start = date('Ymd',$start);
        $end = date('Ymd',$end);
        $from = strtoupper(C('READ_STAT_FROM'));
        if ($from == 'DB'){
            $rs = M('')->query(sprintf("SELECT %s FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d AND %s",
                $fields, $sn, $start, $end, $q));
        }else{
            // 0,0,0,0
            // flux, flux_up, sum_signal, count_signal
            chdir('../data/stat');
            exec(sprintf('./read_stat %s %d %d', $sn, $start, $end), $ret, $status);
            // $ret = array("20181019_1_12 : 0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|49,0,1320,44|89,0,1740,58|40,0,1760,59|21,0,1778,60|20,0,1750,60|37,0,797,27|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0|0,0,0,0");
            // $status = 0;
            if ($status == 0){
                foreach ($ret as $k => $v) {
                    $a = explode(' : ', $v);
                    $b = explode('_', $a[0]);
                    $rs[$k] = array(
                        'report_day' => $b[0],
                        'sim_pos' => $b[1],
                        'net_mode' => $b[2],
                        'flux' => 0,
                        'flux_up' => 0,
                        'hb_count' => 0,
                        'sum_signal' => 0
                    );
                    $c = explode('|', $a[1]);
                    foreach ($c as $kk => $vv) {
                        $d = explode(',', $vv);
                        $rs[$k]['flux'] += $d[0];
                        $rs[$k]['flux_up'] += $d[1];
                        $rs[$k]['sum_signal'] += $d[2];
                        $rs[$k]['hb_count'] += $d[3];
                        if ($fields == '*'){
                            $rs[$k]['f'.$kk] = $d[0];
                            $rs[$k]['s'.$kk] = $d[2];
                            $rs[$k]['c'.$kk] = $d[3];
                        }
                    }
                }
            }
        }
        return $rs ? $rs : array();
        /*
        $rs = $m->query(sprintf("SELECT id, flux, net_mode, report_day FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = $m->query(sprintf("SELECT * FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = $m->query(sprintf("SELECT id, flux, net_mode, SUBSTR(report_day,5,2)m FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = $m->query(sprintf("SELECT id, hb_count, sum_signal, net_mode, report_day FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d AND sim_pos = 1", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = $m->query(sprintf("SELECT * FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d AND sim_pos = 1", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = $m->query(sprintf("SELECT id, hb_count, sum_signal, net_mode, SUBSTR(report_day,5,2)m FROM term_stat_info WHERE sn = '%s' AND report_day BETWEEN %d AND %d AND sim_pos = 1", $sn, date('Ymd',$start), date('Ymd',$end)));
        $rs = M('')->query("SELECT SUM(flux)flux, report_day FROM term_stat_info WHERE sn = '$sn' AND report_day BETWEEN $begin AND $end GROUP BY report_day");
        */
    }

    //发送udp消息给后台服务
    protected function sendUdpMessage($str){
        $client = stream_socket_client('udp://'.C('SERVER_IP').':'.C('SERVER_PORT'), $errno, $errstr, 5);
        fwrite($client, $str);
        fclose($client);
    }

    //获取PHP文件上传错误信息
    //$this->getUploadErrorMsg($f['error'])
    protected function getUploadErrorMsg($code){
        if (in_array($code, array(1,2,3,4,6,7), true)){
            $arr = L('PHP_UPLOAD_ERROR_MSG');
            return $arr[$code];
        }
        return sprintf("%s, code = %d", L('UNKNOWN_MISTAKE'), $code);
    }

    //通过设备型号，获取参数文件类型
    protected function getParamsType($tm){
        $arr = C('PARAMS_TYPE');
        foreach ($arr as $k => $row){
            if (in_array($tm, $row, true)){
                return $k;
            }
        }
        return 'other';
    }

    //递归获取全部子组id
    protected function getSubTermGroupIds($id){
        static $ids = null;
        if (!$ids){
            $rs = M('term_group')->field('id,pid')->where('id NOT IN(1,2)')->select();
            foreach ($rs as $k => $row) {
                $ids[$row['pid']][] = $row['id'];
            }
        }
        $str = $id.'';
        if (!isset($ids[$id])){
            ;
        }else{
            $str = $id.'';
            foreach ($ids[$id] as $k => $v) {
                $str .= ','.$this->getSubTermGroupIds($v);
            }
        }
        return $str;
    }

    //删除(传感量，设备)时删除数据文件
    protected function delRtuDataFiles($sn = '', $addr = 0){
        $path = C('DATA_PATH') != '' ? C('DATA_PATH') : '../data/';
        $path .= 'rtu/';
        if ($sn != ''){
            $path .= substr($sn, -1, 1).'/';
            $dir = opendir($path);
            if (!$dir) return;
            while (false !== ( $file = readdir($dir)) ){
                if (strpos($file, $sn.'_') !== false){
                    unlink($path.$file);
                }
            }
            closedir($dir);
        } elseif ($addr != 0){
            $arr = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','o');
            foreach ($arr as $k => $v) {
                $subpath = $path.$v.'/';
                $dir = opendir($subpath);
                if (!$dir) continue;
                while (false !== ( $file = readdir($dir)) ){
                    if (strpos($file, '_'.$addr) !== false){
                        unlink($subpath.$file);
                    }
                }
                closedir($dir);
            }
        }
    }

    /**
     * 获取GPS文件路径
     * @param  [integer] $ts 为0表示获取current文件，非0表示获取history文件
     */
    protected function getGpsFilePath($ts = 0){
        $filename = $ts === 0 ? 'gps_current' : ('gps_data_'.$ts);
        return C('FTP_WEB_PACK_PATH').'gps/'.$filename.'.bin';
    }

    // 获取电子围栏状态数组
    // return array(系统配置参数enable_electronic_fence，array('sn => 'statuc'))
    protected function getFenceStatus($q = '1=1') {
        $enable_electronic_fence = M('system_config')->where("name = 'enable_electronic_fence'")->getField('value');
        $fstatus_arr = array();
        if ($enable_electronic_fence == '1') {
            $rs5 = M('term_gps')->where($q)->field('sn, fstatus')->select();
            foreach ($rs5 as $key => $row) {
                $fstatus_arr[$row['sn']] = $row['fstatus'];
            }
            unset($rs5);
        }
        return array($enable_electronic_fence, $fstatus_arr);
    }

    // 获取用户信息
    protected function getUserInfo($username) {
        $row = M('usr')->where("name = '%s'", $username)->find();
        if ($row) {
            $utt = L('USER_TYPE_TEXT');
            $row['user_type_text'] = $utt[$row['usr_type']];
            if ($row['never_expired'] == 1) {
                $row['expired_time'] = L('NEVER_EXPIRE');
            }
            if (empty($row['alias'])) {
                $row['alias'] = $row['name'];
            }
            $row['member_since'] = date('Y.m.d', strtotime($row['create_time']));
        }
        return $row;
    }

    protected function getLanStatus($sn) {
        $row = M('term_interface')->field('lan_status, wan_status')->where("sn = '$sn'")->find();
        $params = M('term_param')->where("sn = '%s' AND param <> 'unknown' AND param <> '' AND param IS NOT NULL", $sn)->limit(1)->getField('param');
        $d = array();
        if ($params) {
            $params = explode('&', $params);
            foreach ($params as $k => $v) {
                $i = strpos($v, '=');
                if ($i === false || strlen($v) == $i+1) continue;
                $p_name = substr($v,0,$i);
                if (strpos($p_name, '/') !== false) continue;
                $d[$p_name] = preg_replace("/^\"|\"$/", "", substr($v,$i+1));
            }
        }
        if (!$row) {
            $row = array('lan_status' => 0, 'wan_status' => 0);
        }
        $lan = format_lan_status($row['lan_status']);
        return array(
            'wan_connect_status' => $row['wan_status'],
            'lan_connect_status' => $lan,
            'wan_lan_enables' => array(
                isset($d['lan0_onoff']) ? $d['lan0_onoff'] : 0,
                isset($d['lan1_onoff']) ? $d['lan1_onoff'] : 0,
                isset($d['lan2_onoff']) ? $d['lan2_onoff'] : 0,
                isset($d['lan3_onoff']) ? $d['lan3_onoff'] : 0,
                isset($d['lan4_onoff']) ? $d['lan4_onoff'] : 0
            )
        );
    }
}