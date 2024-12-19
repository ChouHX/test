<?PHP session_start(); ?>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex,nofollow">
<title></title>
<link href="Detran.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="iconfont.css">
<!-- <link rel="stylesheet" type="text/css" href="usbblue.css"> -->
<link rel="shortcut icon" href="favicon.ico">
<script type="text/javascript">
	<?PHP
	include 'init_'.session_id().'.php';
	if (is_file('./menu_cfg.php')) {
		$menu_cfg = include 'menu_cfg.php';
	} else {
		$menu_cfg = array(
	        'show' => array(),
	        'hide' => array()
	    );
	}
	?>
</script>
<script type="text/javascript" src="lang/<?PHP echo $lang; ?>.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/tomato.js"></script>
<script type="text/javascript" src="js/advancedtomato.js"></script>
<script type="text/javascript">
$(function(){
	$('.jbwl').html($lang.BASIC_NETWORK);
	$('.wwl').html($lang.WAN_NETWORK);
	$('.ydwl').html($lang.VAR_TERM_PARAM_3G);
	$('.ydwl2').html($lang.VAR_TERM_PARAM_3G + ' 2');
	$('.jywl').html($lang.VAR_TERM_PARAM_LAN);
	$('.lldd').html($lang.LINK_SCHEDULING);
	$('.dtym').html($lang.DYNAMIC_DOMAIN_NAME);
	$('.lybsz').html($lang.ROUTING_TABLE_SETTINGS);
	$('.wlsz').html($lang.WLAN_SETTING);
	$('.jbcssz').html($lang.BASIC_PARAMETER_SETTING);
	$('.dssid').html($lang.MULTIPLE_SSID);
	$('.gjwl').html($lang.ADVANCED_NETWORK);
	$('.dkzf').html($lang.PORT_FORWARDING);
	$('.dkcdx').html($lang.PORT_REDIRECTING);
	$('.dmzsz').html($lang.DMZ_SETTING);
	$('.ipct').html($lang.IP_PENETRATION);
	$('.dkcf').html($lang.PORT_TRIGGER);
	$('.rdts').html($lang.PORTAL);
	$('.gpssz').html($lang.GPS_SETTING);
	$('.ckyy').html($lang.SERIAL_APPLICATION);
	$('.ckyy2').html($lang.SERIAL_APPLICATION + ' 2');
	$('.upnpsz').html($lang.UPNP_SETTING);
	$('.kdxs').html($lang.BANDWIDTH_SPEED_LIMIT);
	$('.jtdhcp').html($lang.STATIC_DHCP);
	$('.fhq').html($lang.FIREWALL);
	$('.ip_url_gl').html($lang.IP_URL_FILTER);
	$('.ymgl').html($lang.DOMAIN_FILTER);
	$('.vpnsd').html($lang.VPN_MODE);
	$('.zerotier').html($lang.ZEROTIER);
	$('.gresz').html($lang.VPN_GRE);
	$('.openvpn_khd').html($lang.OPEN_VPN_CLIENT);
	$('.openvpn_fwd').html($lang.OPEN_VPN_SERVER);
	$('.pptp_l2tp_fwq').html($lang.PPTP_L2TP_SERVER);
	$('.pptp_l2tp_khd').html($lang.VPN_PPTP_L2TP);
	$('.xtgl').html($lang.VAR_MENU_SYSCFG);
	$('.xtbssz').html($lang.SYSTEM_IDENTITY_SETTING);
	$('.sjsz').html($lang.TIME_SETTING);
	$('.fwsz').html($lang.ACCESS_SETTINGS);
	$('.dscq').html($lang.SCHEDULED_REBOOT);
	$('.ccgl').html($lang.STORAGE_MANAGE);
	$('.m2mgl').html($lang.M2M_PLATFORM_MANAGEMENT);
	$('.gjpz').html($lang.IN_OUT_PIN_CFG);
	$('.rzgl').html($lang.LOG_MANAGEMENT);
	window.menu_cfg = <?PHP echo json_encode($menu_cfg); ?>;
});
</script>
</head>
<body>
<div id="wrapper">
	<div class="navigation">
		<ul>
			<li class="menu_1"><a href="#"><i class="iconfont iconf-guanfangbanben"></i> <span class="icons-desc jbwl"><!--基本网络--></span></a>
				<ul>
					<li class="menu_2" id="wwl"><a href="./basic-wan.php" class="wwl"><!--WAN网络--></a></li>
					<li class="menu_2"><a href="./basic-cellular.php" class="ydwl"><!--移动网络--></a></li>
					<li class="menu_2" id="ydwl2" style="display: none"><a href="./basic-cellular2.php" class="ydwl2"><!--移动网络 2--></a></li>
					<li class="menu_2"><a href="./basic-lan.php" class="jywl"><!--局域网络--></a></li>
					<li class="menu_2" id="ipv6"><a href="./basic-ipv6.php">IPv6</a></li>
					<li class="menu_2"><a href="./basic-vlan.php">VLAN</a></li>
					<li class="menu_2"><a href="./basic-schedule.php" class="lldd"><!--链路调度--></a></li>
					<li class="menu_2"><a href="./basic-ddns.php" class="dtym"><!--动态域名--></a></li>
					<li class="menu_2"><a href="./basic-routing.php" class="lybsz"><!--路由表设置--></a></li>
				</ul>
			</li>
			<li class="menu_1" id="wlsz"><a href="#"><i class="iconfont iconf-wifi"></i> <span class="icons-desc wlsz"><!--WLAN设置--></span></a>
				<ul>
					<li class="menu_2"><a href="./wlan-network.php" class="jbcssz"><!--基本参数设置--></a></li>
					<li class="menu_2"><a href="./wlan-network_vif.php" class="dssid" style="display:none;"><!--多SSID--></a></li>
				</ul>
			</li>
			<li class="menu_1"><a href="#"><i class="iconfont iconf-wumoxing"></i> <span class="icons-desc gjwl"><!--高级网络--></span></a>
				<ul>
					<li class="menu_2"><a href="./forward-basic.php" class="dkzf"><!--端口转发--></a></li>
					<li class="menu_2"><a href="./forward-redirection.php" class="dkcdx"><!--端口重定向--></a></li>
					<li class="menu_2"><a href="./forward-dmz.php" class="dmzsz"><!--DMZ设置--></a></li>
					<li class="menu_2"><a href="./forward-ippass.php" class="ipct"><!--IP穿透--></a></li>
					<li class="menu_2"><a href="./forward-triggered.php" class="dkcf"><!--端口触发--></a></li>
					<li class="menu_2"><a href="./forward-portal.php" class="rdts"><!--热点推送--></a></li>
					<li class="menu_2" id="gpssz"><a href="./forward-gps.php" class="gpssz"><!--GPS设置--></a></li>
					<li class="menu_2" id="ckyy" style="display: none"><a href="./forward-dtu.php" class="ckyy"><!--串口应用--></a></li>
					<li class="menu_2" id="ckyy2" style="display: none"><a href="./forward-dtu2.php" class="ckyy2"><!--串口应用2--></a></li>
					<li class="menu_2"><a href="./forward-atoip.php">AT over IP</a></li>
					<li class="menu_2"><a href="./forward-upnp.php" class="upnpsz"><!--UPnP设置--></a></li>
					<li class="menu_2"><a href="./forward-bwlimit.php" class="kdxs"><!--带宽限速--></a></li>
					<li class="menu_2"><a href="./forward-vrrp.php">VRRP</a></li>
					<li class="menu_2"><a href="./forward-static.php" class="jtdhcp"><!--静态DHCP--></a></li>
				</ul>
			</li>
			<li class="menu_1"><a href="#"><i class="iconfont iconf-yunyingguanli"></i> <span class="icons-desc fhq"><!--防火墙--></span></a>
				<ul>
					<li class="menu_2"><a href="./firewall-port_filter.php" class="ip_url_gl"><!--IP/URL过滤--></a></li>
					<li class="menu_2"><a href="./firewall-content_filter.php" class="ymgl"><!--域名过滤--></a></li>
				</ul>
			</li>
			<li class="menu_1"><a href="#"><i class="iconfont iconf-peiwangyindao"></i> <span class="icons-desc vpnsd"><!--VPN隧道--></span></a>
				<ul>
					<li class="menu_2"><a href="./vpn-wireguard.php">Wireguard</a></li>
					<li class="menu_2"><a href="./vpn-zerotier.php" class="zerotier"><!--远程通道Zerotier设置--></a></li>
					<li class="menu_2"><a href="./vpn-gre.php" class="gresz"><!--GRE设置--></a></li>
					<li class="menu_2"><a href="./vpn-client.php" class="openvpn_khd"><!--OpenVPN客户端--></a></li>
					<li class="menu_2"><a href="./vpn-server.php" class="openvpn_fwd"><!--OpenVPN服务端--></a></li>
					<li class="menu_2"><a href="./vpn-pptp-server.php" class="pptp_l2tp_fwq"><!--PPTP/L2TP服务器--></a></li>
					<li class="menu_2"><a href="./vpn-xtp.php" class="pptp_l2tp_khd"><!--PPTP/L2TP客户端--></a></li>
					<li class="menu_2"><a href="./vpn-l2tpv3.php">L2TP V3</a></li>
					<li class="menu_2"><a href="./vpn-ipsec.php">IPSec</a></li>
					<li class="menu_2"><a href="./vpn-dmvpn.php">DMVPN</a></li>
				</ul>
			</li>
			<li class="menu_1"><a href="#"><i class="iconfont iconf-jichuguanli"></i> <span class="icons-desc xtgl"><!--系统管理--></span></a>
				<ul>
					<li class="menu_2"><a href="./admin-ident.php" class="xtbssz"><!--系统标识设置--></a></li>
					<li class="menu_2"><a href="./admin-time.php" class="sjsz"><!--时间设置--></a></li>
					<li class="menu_2"><a href="./admin-access.php" class="fwsz"><!--访问设置--></a></li>
					<li class="menu_2"><a href="./admin-sched.php" class="dscq"><!--定时重启--></a></li>
					<li class="menu_2"><a href="./admin-snmp.php">SNMP</a></li>
					<li class="menu_2"><a href="./admin-filelist.php" class="ccgl"><!--存储管理--></a></li>
					<li class="menu_2"><a href="./admin-m2m.php" class="m2mgl"><!--M2M平台管理--></a></li>
					<li class="menu_2"><a href="./admin-tr069.php">TR-069</a></li>
					<li class="menu_2" id="gjpz"><a href="./admin-gpctl.php" class="gjpz"><!--输入/输出管脚配置--></a></li>
					<li class="menu_2"><a href="./admin-log.php" class="rzgl"><!--日志管理--></a></li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="container">