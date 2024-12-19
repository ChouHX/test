<?php
/**
 * 判断终端是否在线
 * is_online = 1 且 current_time - last_time < C('TERM_OFFLINE_TIME') 判断为在线。
 * @param $last_time
 * @param $is_online
 * @param $is_vc 是否为远程通道/VPN，远程通道离线时间写死为3分钟
 */
function get_term_status_code($diff, $is_online, $is_vc = false){
    // $sub = mktime() - strtotime($last_time);
    $ot = $is_vc ? 180 : C('TERM_OFFLINE_TIME');
    if (!is_null($diff) && $diff <= $ot && ($is_online==1 || $is_online == 2)){
        return '1';
    }
    return '0';
}

/**
 * 根据终端状态获取终端状态图
 * @param $status
 * @param $status_limit  是否流量限制
 */
function get_term_status_str($status, $status_limit = 0){
	$str = "";
	if($status == "1"){
        if (intval($status_limit) == 0){
            $str = "<img class='imgmiddle' src='".C('PROJECT_PATH')."/Tpl/Public/images/icons/term_on.gif' /> <font color='green' >".L('VAR_TERM_STATUS_ONLINE')."</font>";
        }else{
            $str = "<img class='imgmiddle' src='".C('PROJECT_PATH')."/Tpl/Public/images/icons/term_limit.png' title='".L('VAR_FLUX_LIMIT')."'/> <font color='green' >".L('VAR_TERM_STATUS_ONLINE')."</font>";
        }
	}else{
		$str = "<img class='imgmiddle' src='".C('PROJECT_PATH')."/Tpl/Public/images/icons/term_off.gif'/> ".L('VAR_TERM_STATUS_OFFLINE');
	}
	return $str;
}

/**
 * 根据终端状态、信号强度获取信号图
 * @param $status
 * @param $signal
 * @param $ret_type (path or img_name)
 */
function get_term_signal_str($status, $signal, $ret_type = 'path')
{
	$str = "";
	$t_signal = $signal;
	//	[86, 100]
	if($signal >= 26)
	{
		$img_str = "bar6.gif";
	}
	//	[71,86)
	else if($signal >= 21 && $signal <= 25)
	{
		$img_str = "bar5.gif";
	}
	//	[56,71)
	else if($signal >= 16 && $signal <= 20)
	{
		$img_str = "bar4.gif";
	}
	//	[41,56)
	else if($signal >= 11 && $signal <= 15)
	{
		$img_str = "bar3.gif";
	}
	//	[26,41)
	else if($signal >= 6 && $signal <= 10)
	{
		$img_str = "bar2.gif";
	}
	//	[0,26)
	else if($signal <= 5)
	{
		$img_str = "bar1.gif";
	}
	else
	{
		$img_str = "bar0.gif";
		$t_signal = 0;
	}

	if ($signal > 100){
		$t_signal = 100;
	}

	/*if($status == "0")
	{
		$img_str = "bar0.gif";
		$t_signal = 0;
	}*/
    if ($ret_type == 'img_name'){
        return $img_str;
    }
    if (C('SIGNAL_SHOW_PERCENT') && $t_signal <= 31){
        $str = "<img src='".C('PROJECT_PATH')."/Tpl/Public/images/icons/".$img_str."' title='".$t_signal."' />".intval($t_signal/31*100).'%';
    } else {
        $str = "<img src='".C('PROJECT_PATH')."/Tpl/Public/images/icons/".$img_str."' />".$t_signal;
    }
	return $str;
}

/**
 * 把字节转换为MB等
 */
function bitsize($num)
{
	if(!preg_match("/^[0-9E\+\.]+$/", $num))
		return 0;

	$type = array("Bytes", "KB", "MB", "GB", "TB");
	$i = 0;

	while( $num >= 1024 )
	{
		$num = $num / 1024;
		$i++;
		if ($i==4) break;
	}
	return round($num,2)." ".$type[$i];
}

//字节转为MB
function bitsizeMb($num){
    return ($num==0 ? 0 : round($num/(1024*1024), 3));
}

/**
 * 获取扩展名
 */
function getFileExt($file)
{
	return pathinfo($file, PATHINFO_EXTENSION);
}

/**
 * 生成唯一字符串
 * @param string $prefix
 * @return string
 */
function getGuid($prefix = '')
{
	$prefix .= mt_rand();
	$charid = strtolower(md5(uniqid($prefix, true)));
	$uuid =
	substr($charid, 0, 8).
	substr($charid, 8, 4).
	substr($charid,12, 4).
	substr($charid,16, 4).
	substr($charid,20,12);
	return $uuid;
}

/**
 * 求2个时间间隔，转化为格式：1天3小时13分33秒，传参方式有2种
 * (1) start = '2017-01-01 08:00:00'    end = '2017-07-17 17:33:00'
 * (2) start = -1, end = 整数、2个时间相差的秒数
 */
function format_time($start, $end){
    if (empty($start) || empty($end) || $start > $end){
        return '0';
    }
    if ($start == '0000-00-00 00:00:00' || $end == '0000-00-00 00:00:00'){
        return '0';
    }
    $ta = L('VAR_TIME_ARR');
    if ($start == -1 && is_int($end)){
        $ss = $end;
    }else{
        $ss = strtotime($end) - strtotime($start);
    }
    $str = '';

    $day = intval($ss/(24*3600));
    if ($day > 0){
        $str .= $day.$ta[0];
    }

    $hour = intval($ss%(24*3600)/3600);
    if ($hour > 0){
        $str .= $hour.$ta[1];
    }

    $minute = intval($ss%3600/60);
    if ($minute > 0){
        $str .= $minute.$ta[2];
    }

    $second = intval($ss%60);
    if ($second > 0){
        $str .= $second.$ta[3];
    }
    return $str==''?'0':$str;
}

// 格式化返回：0:37:10
// 参数参考上一函数 format_time
function format_time_his($start, $end) {
    if (empty($start) || empty($end) || $start > $end){
        return '00:00:00';
    }
    if ($start == '0000-00-00 00:00:00' || $end == '0000-00-00 00:00:00'){
        return '00:00:00';
    }
    if ($start == -1 && is_numeric($end)){
        $ss = $end;
    }else{
        $ss = strtotime($end) - strtotime($start);
    }
    $str = array();

    $hour = intval($ss/3600);
    if ($hour > 0){
        $str[] = ($hour < 10 ? ('0'.$hour) : $hour).'';
    } else {
        $str[] = '00';
    }

    $minute = intval($ss%3600/60);
    if ($minute > 0){
        $str[] = ($minute < 10 ? ('0'.$minute) : $minute).'';
    } else {
        $str[] = '00';
    }

    $second = intval($ss%60);
    if ($second > 0){
        $str[] = ($second < 10 ? ('0'.$second) : $second).'';
    } else {
        $str[] = '00';
    }
    return count($str) == 0 ? '00:00:00' : implode(':', $str);
}

// 秒数转时间，格式05:23:52
function seconds_to_his($seconds) {
    $seconds = intval($seconds);
    $h = '00';
    $i = '00';
    $s = '00';
    if ($seconds != 0) {
        if ($seconds >= 3600) {
            $h = intval($seconds / 3600);
            $seconds -= $h * 3600;
            if ($h < 10) {
                $h = '0'.$h;
            }
        }
        if ($seconds >= 60) {
            $i = intval($seconds / 60);
            $seconds -= $i * 60;
            if ($i < 10) {
                $i = '0'.$i;
            }
        }
        $s = $seconds;
        if ($s < 10) {
            $s = '0'.$s;
        }
    }
    return sprintf('%s:%s:%s', $h, $i, $s);
}

//2字节
function htons($str){
    return substr($str,2).substr($str,0,2);
}

//4字节
function htonl($str){
    $ret = '';
    for ($i=6; $i>=0; $i-=2){
        $ret .= substr($str, $i, 2);
    }
    return $ret;
}

//8字节
function htond($str){
    $ret = '';
    for ($i=14; $i>=0; $i-=2){
        $ret .= substr($str, $i, 2);
    }
    return $ret;
}

//16进制字符串转string
function hex2string($hex){
    $hex = str_replace(' ', '', $hex);
    $string = '';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

//16进制字符串加空格
function addSpaceToHex($str){
    for ($i=0; $i<strlen($str); $i+=2){
        $tmp[] = substr($str, $i, 2);
    }
    return isset($tmp) ? implode(' ', $tmp) : str;
}

//拆分name为sn1，sn2
function getSn12($name){
    $tmp = strripos($name, '_');
    if ($tmp === false){
        $sn1 = '';
        $sn2 = $name;
    }else{
        $sn1 = substr($name, 0, $tmp);
        $sn2 = substr($name, $tmp+1);
    }
    return array($sn1,$sn2);
}

function gpsTrans($high, $low){
    $symbol = '';
    if ($high > 32767){
        $high = 32768*2 - $high;
        $symbol = '-';
    }
    $ret = round($high/100.0 + $low/1000000.0, 6);
    return floatval($symbol.$ret);
}

// 拷贝文件夹，返回复制的文件数量 $file_count = array(成功数， 失败数)
function upgrade_copy_dir($src, $dst, $log) {
    $dir = opendir($src);
    $file_count = array(0, 0);
    $sep = "\t";
    $wrap = IS_WIN ? "\r\n" : "\n";
    while (false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $src_path = $src.DIRECTORY_SEPARATOR.$file;
        $dst_path = $dst.DIRECTORY_SEPARATOR.$file;
        if (is_dir($src_path)) {
            if (!is_dir($dst_path)) {
                mkdir($dst_path);
            }
            $ret = upgrade_copy_dir($src_path, $dst_path, $log);
            $file_count[0] += $ret[0];
            $file_count[1] += $ret[1];
            continue;
        } else {
            $cp_ret = copy($src_path, $dst_path);
            if ($cp_ret) {
                $file_count[0] += 1;
            } else {
                $file_count[1] += 1;
                $tm = date('Y-m-d H:i:s');
                $str = sprintf('Copy file(%s) failed', $src_path);
                $log->mwrite($tm.$sep.$str.$wrap);
                echo sprintf('<span class="tm">%s</span>%s<br>', $tm, $str);
            }
        }
    }
    closedir($dir);
    return $file_count;
}

//删除文件夹
function delete_dir($dir){
    if (!is_dir($dir)) return;
    $d = opendir($dir);
    while ($f = readdir($d)){
        if ($f != '.' && $f != '..'){
            if (is_dir($dir.$f)){
                delete_dir($dir.$f.'/');
            }else{
                unlink($dir.$f);
            }
        }
    }
    closedir($d);
    rmdir($dir);
}

function is_local_ip($ip){
    $ip_arr = explode('.', $ip);
    foreach ($ip_arr as $k => $v) {
        $ip_arr[$k] = intval($v);
    }
    if ($ip_arr[0] == 10 || $ip_arr[0] == 127 || ($ip_arr[0] == 172 && $ip_arr[1] >= 16 && $ip_arr[1] <= 31)
        || ($ip_arr[0] == 192 && $ip_arr[1] == 168)){
        return true;
    }
    return false;
}

// 计算startip，endip
function get_startip_endip($lan_ipaddr, $start, $num){
    $x = substr($lan_ipaddr, 0, strrpos($lan_ipaddr, '.')+1);
    return array($x.$start, $x.($start + $num -1));
}

function get_position_by_ip($ip){
    $arr = array('country'=>'', 'province'=>'', 'city'=>'');
    if ($ip == '0.0.0.0' || is_local_ip($ip)){
        $arr['city'] = 'Localhost';
        return $arr;
    }
    if (strtolower($_COOKIE['think_language']) == 'zh-cn'){
        $api = 'http://api.pi.do/api/v1/queryip?ip='.$ip;
    }else{
        $api = 'http://ipinfo.io/'.$ip.'/json';
    }
    $opts = array(
        'method' => 'get',
        'http' => array('timeout' => 2)
    );
    $context = stream_context_create($opts);
    $ret = file_get_contents($api, false, $context);
    if ($ret){
        $ret = json_decode($ret, true);
        if ($ret['statuscode'] == 0){
            $arr = array(
                'country' => $ret['data']['ipInfo']['Country'],
                'province' => $ret['data']['ipInfo']['Province'],
                'city' => $ret['data']['ipInfo']['City']
            );
        }
        if (isset($ret['country'])){
            $arr = array(
                'country' => $ret['country'],
                'province' => $ret['region'],
                'city' => $ret['city']
            );
        }
    }
    return $arr;
}

function get_latlng_by_network($ip){
    $json = array('lng'=>0, 'lat'=>0);
    if (is_local_ip($ip)){
        $api = 'http://ipinfo.io/json';
    }else{
        $api = 'http://ipinfo.io/'.$ip.'/json';
    }
    $opts = array(
        'method' => 'get',
        'http' => array('timeout' => 2)
    );
    $context = stream_context_create($opts);
    $ret = file_get_contents($api, false, $context);
    if ($ret){
        $ret = json_decode($ret, true);
        if ($ret && isset($ret['loc'])){
            $tmp = explode(',', $ret['loc']);
            $json['lat'] = floatval(sprintf("%.5f", $tmp[0]));
            $json['lng'] = floatval(sprintf("%.5f", $tmp[1]));
        }
    }
    return $json;
}

/**
 * 获取传感量类型select中的选项
 * @return [type] [description]
 */
function get_sensor_options(){
    $rs = M('rtu_data_set')->field('slave_id, addr, name, unit')->where('set_type = 0')->order('name ASC')->select();
    $sensors = '';
    $slave_id_addr = I('slave_id_addr','','string');
    foreach ($rs as $k => $row) {
        $tmp = $row['slave_id'].'_'.$row['addr'];
        $sensors .= sprintf('<option %s value="%s" data-unit="%s">%s</option>', ($tmp==$slave_id_addr?'selected':''), $tmp, $row['unit'], $row['name']);
    }
    return $sensors;
}

/**
 * 获取指令下发所保存的常用指令
 * 项目根目录common_cmd文件
 * @return [type] [description]
 */
function get_common_cmd(){
    $rs = file_get_contents('common_cmd');
    $str = '<option value="" data-type="">'.L('PLEASE_SELECT').'</option>';
    if ($rs){
        $rs = json_decode($rs, true);
        foreach ($rs as $k => $row) {
            $str .= sprintf('<option value="%s" data-type="%s">%s</option>', $row['value'], $row['data_type'], $row['name']);
        }
    }
    return $str;
}

function get_value_type($str){
    switch ($str) {
        case F:
            return array("type"=>4,"len"=>4);
            break;
        case B:
            return array("type"=>3,"len"=>1);
            break;
        case W:
            return array("type"=>2,"len"=>2);
            break;
        case L:
            return array("type"=>1,"len"=>4);
            break;
        case U:
            return array("type"=>1,"len"=>4);
            break;
        default:
            return array("type"=>4,"len"=>4);
            break;
    }
}

//处理lan_status的状态
function format_lan_status($number){
    $str = decbin($number);
    $status = sprintf("%08d", $str);
    for($i=0;$i<strlen($status);$i++){
      if($i>3){
        $arr[] = intval(substr($status,$i,1));
      }
    }
    return $arr;    
}

// 解析rtu_script为rtu_data_set表的数据
function get_rtu_data_set($info){
    if(!empty($info)){
        preg_match_all('/SET_ADDR(.*);/',$info,$slave_id);
        if(!empty($slave_id)){
            $slave_id = trim($slave_id[1][0]);
        }else{
            $slave_id = 1;
        }
        //获取传感量名称
        $pattern_name = '/ALIAS\s(.*)(\d+)/';
        preg_match_all($pattern_name,$info,$str1);
        if(!empty($str1)){
            $arr1 = $str1[0];
            foreach($arr1 as $k =>$v){
                $name_data = explode(" ",$v);
                $data1[$name_data[1]] = $name_data[2];
            }
        }else{
            $data1=array();
        }
        //获取定义数组的传感量
        $pattern_arr = '/INTFS(.*)\[\d+\];/';
        preg_match_all($pattern_arr,$info,$str);
        if(!empty($str[0])){
            $arr = $str[0];
            foreach($arr as $k =>$v){
                $data = explode(" ",$v);
                $total = substr($data[3],-3,1);
                $value =get_value_type($data[2]);
                $name = substr($data[3],0,stripos($data[3],'['));
                for($i=0;$i<$total;$i++){
                    $total_arr[]=array(
                        "slave_id" =>$slave_id,
                        "addr" =>$data[1]+$i,
                        "value_type" =>$value['type'],
                        "value_len" =>$value['len'],
                        "name" => empty($data1[$name.'['.($i+1).']'])? "":$data1[$name.'['.($i+1).']'],
                        "unit" => "",
                        "min" => 0,
                        "max" => 0
                    );
                }
            }
        }else{
            $total_arr = array();
        }
        //获取定义单个的传感量
        $pattern = '/INTF\s(.*);/';
        preg_match_all($pattern,$info,$str_sin);
        if(!empty($str_sin[0])){
            $arr_sin = $str_sin[0];
            foreach($arr_sin as $k =>$v){
                $data_sin = explode(" ",$v);
                $value_sin =get_value_type($data_sin[2]);
                $total_data[] = array(
                    "slave_id" =>$slave_id,
                    "addr" =>$data_sin[1],
                    "value_type" => $value_sin['type'],
                    "value_len" => $value_sin['len'],
                    "name" => empty($data1[$data_sin[3]])? "":$data1[$data_sin[3]],
                    "unit" => "",
                    "min" => 0,
                    "max" => 0
                );
            }
        }else{
            $total_data = array();
        }
        return array_merge($total_arr,$total_data);
    }else{
        return array();
    }
}

//将关系运算符替换为文字
function rep_op_text($str){
    return str_replace(
        array('>=', '<=', '>', '<', '='),
        array(L('VAR_OP_GE'), L('VAR_OP_LE'), L('VAR_OP_GT'), L('VAR_OP_LT'), L('VAR_OP_EQ')),
    $str);
}

function crc16($str) {
    $data = pack('H*', $str);
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]);
        for ($j=8; $j!=0; $j--) {
            if (($crc & 0x0001) != 0) {
                $crc >>= 1;
                $crc ^= 0xA001;
            }else{
                $crc >>= 1;
            }
        }
    }
    return sprintf('%04X', $crc);
}

function get_period_type_text($t, $v) {
    $arr = explode('|', $v);
    if (strpos($arr[0], ',') === false && $arr[0] < 10) $arr[0] = '0'.$arr[0];
    if (strpos($arr[1], ',') === false && $arr[1] < 10) $arr[1] = '0'.$arr[1];
    switch (intval($t)) {
        case 1:
            $str = sprintf('%s (%s) %s:%s', L('EVERY_MONTH'), $arr[2], $arr[1], $arr[0]);
            break;
        case 2:
            $WA = L('VAR_WEEK_ARR');
            $brr = explode(',', $arr[3]);
            $week_str = array();
            foreach ($brr as $week_index) {
                $week_str[] = $WA[$week_index];
            }
            $str = sprintf('%s (%s) %s:%s', L('EVERY_WEEK'), implode(', ', $week_str), $arr[1], $arr[0]);
            break;
        case 3:
            $brr = explode(',', $arr[1]);
            $day_str = array();
            foreach ($brr as $val) {
                $day_str[] = sprintf('%s%d:%s', $val<10?'0':'', $val, $arr[0]);
            }
            $str = sprintf('%s (%s)', L('EVERY_DAY'), implode(', ', $day_str), $arr[0]);
            break;
        case 4:
            $brr = explode(',', $arr[0]);
            $minute_str = array();
            foreach ($brr as $val) {
                $minute_str[] = sprintf('%s%d %s', $val<10?'0':'', $val, L('VAR_MINUTE'));
            }
            $str = sprintf('%s (%s)', L('EVERY_HOUR'), implode(', ', $minute_str));
            break;
    }
    return $str;
}

// 获取权限节点
function get_page_nodes($uid = 0) {
    $rs = M('page_nodes')->query("SELECT id, pid AS pId, alias, module_name, action_name, checkbox, (SELECT COUNT(*) FROM page_nodes WHERE pid = a.id)subs FROM page_nodes a WHERE a.id NOT IN(1,2) ORDER BY a.pId ASC, a.id ASC");
    if ($uid > 1) {
        $node_ids = M('usr_permission')->where('usr_id = %d', $uid)->getField('node_id', true);
    }
    foreach ($rs as $key => $row) {
        $disable_nodes = in_array($row['action_name'], array('yhzlb', 'yhlb'), true);
        //有子节点的，将其open属性设置为true
        if ($row['subs'] != 0 && !$disable_nodes) {
            $rs[$key]['open'] = true;
        }
        if ($uid == 0) {
            // 新增用户时
            $rs[$key]['chkDisabled'] = $disable_nodes;
        } elseif ($uid == 1) {
            // 编辑admin
            $rs[$key]['chkDisabled'] = true;
            $rs[$key]['checked'] = true;
        } elseif ($uid > 1) {
            // 编辑其他用户
            $rs[$key]['chkDisabled'] = $disable_nodes;
            if ($node_ids && in_array($row['id'], $node_ids, true)) {
                $rs[$key]['checked'] = true;
            }
        }
    }
    return is_array($rs) ? $rs : array();
}

// 获取权限节点，左侧菜单使用
function get_menu_nodes() {
    $uid = $_SESSION[C('SESSION_NAME')]['id'];
    $rs = M('page_nodes')->query("SELECT id, pid, module_name, action_name FROM page_nodes WHERE id IN(SELECT node_id FROM usr_permission WHERE usr_id = $uid)");
    $modules = array();
    $actions = array();
    foreach ($rs as $key => $row) {
        array_push($actions, $row['module_name'].'_'.$row['action_name']);
        $modules[$row['module_name']] = 1;
    }
    $modules['Information'] = 1;
    $modules = array_keys($modules);
    return array($modules, $actions);
}

// 获取权限节点，内部控制器使用
function get_usr_ps() {
    $uid = $_SESSION[C('SESSION_NAME')]['id'];
    $rs = M('page_nodes')->query("SELECT id, module_name, action_name FROM page_nodes WHERE checkbox = 1 AND id IN(SELECT node_id FROM usr_permission WHERE usr_id = $uid)");
    $ret = array();
    foreach ($rs as $key => $row) {
        if (!isset($ret[$row['module_name']])) $ret[$row['module_name']] = array();
        array_push($ret[$row['module_name']], $row['action_name']);
    }
    return $ret;
}

// 浏览器友好的变量输出，并结束程序
function dump2($var, $echo=true, $label=null, $strict=true) {
    dump($var, $echo, $label, $strict);
    exit;
}

// 通过二维数组生成ini文件，$arr是二维数组，$path是路径+文件名
function write_ini_file($arr, $path) {
    $sep = IS_WIN ? "\r\n" : "\n";
    $str = '';
    $c = count($arr);
    $i = 0;
    foreach ($arr as $section => $row) {
        $i += 1;
        $str .= sprintf("[%s]%s", $section, $sep);
        foreach ($row as $name => $value) {
            $str .= sprintf("%s = %s%s", $name, $value, $sep);
        }
        if ($i != $c) {
            $str .= $sep;
        }
    }
    file_put_contents($path, $str);
}

// 将路由器上报的router_2参数转换成bi参数
// 传过来的(router_2, r_type, mcu)的值可能是空字符串，或者null
function asp_bootinfo($router_2, $r_type, $mcu) {
    $multi = 0;
    $lang = 0;
    $style = '';
    $logo = '';
    $wifi_off = 0;
    $wan_off = 0;
    $hw = '';
    $gps = '';
    $r21 = 0;
    $model = 'g9';
    $rsdb = 0;
    $bi = array();
    $nv = $router_2 ? explode('<', $router_2) : array();
    if ($router_2 && count($nv) >= 8) {
        // 0 < _std < _std < 0 < 0 < sd < e < 1 < 0 <
        // 0 < _std < _std < 0 < 0 <dd  < i < 1 < 0 <
        // if((vstrsep(nvp,"<",&lang,&style,&logo,&wifi_off,&wan_off,&hw,&gps,&r21)) == 8)
        $multi = 1;
        $lang = $nv[0];
        $style = $nv[1];
        $logo = $nv[2];
        $wifi_off = $nv[3];
        $wan_off = $nv[4];
        $hw = $nv[5];
        $gps = $nv[6];
        $r21 = $nv[7];
    }
    return json_encode(array(
        'hw' => $hw,
        'wlof' => $wifi_off,
        'waof' => $wan_off,
        'r21' => $r21,
        'gps' => $gps,
        'model' => $model,
        'rsdb' => $rsdb,
        'r_type' => $r_type ? $r_type : '',
        'mcu' => !is_null($mcu) && $mcu != '' ? $mcu : 0
    ));
}
?>