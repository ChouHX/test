<!--
-->
<title><% ident(); %> 基本设置: 产品信息初始化</title>
<content>
	<script type="text/javascript">
//	<% nvram("modem_imei,router_actcode,router_type,router_sn,router_hw,lan_hwaddr,wan_hwaddr,wl0_hwaddr,router_2,boot_lang,boot_style,boot_logo,boot_hw,boot_gps,boot_r21,boot_wlan_off,boot_wan_off,boot_udisk"); %>

function verifyFields(focused, quiet)
{
	if (!v_mac('_init_lan_hwaddr', quiet)) return 0;
	//if (!v_mac('_init_wan_hwaddr', quiet)) return 0;
	//if (!v_mac('_init_wl0_hwaddr', quiet)) return 0;

	return (v_length('_router_type', quiet, 0, 30) && v_length('_router_sn', quiet, 0, 30) && v_length('_router_hw', quiet, 0, 30));
}

function save()
{
	if (!verifyFields(null, false)) return;

	var s="";
	var fom = E('_fom');

	s += fom.boot_lang.value+'<';
	s += fom.boot_style.value+'<';
	s += fom.boot_logo.value+'<';
	s += (E('_boot_wlan_off').checked ? '1' :'0')+'<';
	s += (E('_boot_wan_off').checked ? '1' :'0')+'<';
	s += fom.boot_hw.value+'<';
	s += fom.boot_gps.value+'<';
	s += (E('_boot_r21').checked ? '1' :'0')+'<';
	s += (E('_boot_udisk').checked ? '1' :'0')+'<';
	fom.router_2.value=s;
	form.submit('_fom', 1);
}

	</script>

	<div class="box">
		<div class="heading">路由器产品信息</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#basic-product.asp'>
				<input type='hidden' name='_service' value='initial-start'>
				<input type='hidden' name='router_2'>
				<div id="productconfig"></div>
			</form>

			<script type='text/javascript'>

				$('#productconfig').forms([
		{ title: '模块IMEI号', text: nvram.modem_imei },
		{ title: '激活码', name: 'router_actcode', type: 'text', maxlen: 64, size: 38, value: nvram.router_actcode },
		{ title: '产品型号', name: 'router_type', type: 'text', maxlen: 30, size: 38, value: nvram.router_type },
		{ title: '产品系列号', name: 'router_sn', type: 'text', maxlen: 30, size: 38, value: nvram.router_sn },
		{ title: '硬件版本', name: 'router_hw', type: 'text', maxlen: 30, size: 38, value: nvram.router_hw },
		{ title: 'LAN MAC地址', name: 'init_lan_hwaddr', type: 'text', maxlen: 17, size: 20, value: nvram.lan_hwaddr },
		//{ title: 'WAN MAC地址', name: 'init_wan_hwaddr', type: 'text', maxlen: 17, size: 20, value: nvram.wan_hwaddr },
		//{ title: 'WiFi MAC地址', name: 'init_wl0_hwaddr', type: 'text', maxlen: 17, size: 20, value: nvram.wl0_hwaddr }
		null,
		{ title: '系统语言', name: 'boot_lang', type: 'select', options: [['0','简体中文'],['1','繁体中文'],['2','英文']],value:(nvram.boot_lang == 'zh_TW')?1:(nvram.boot_lang == 'en_EN')?2:0},
		{ title: '页面风格', name: 'boot_style', type: 'select', options: [['_std','默认'],['_wl','WLINK'],['_oe','OEM']],value:nvram.boot_style},
		{ title: '页面LOGO', name: 'boot_logo', type: 'select', options: [['_std','默认'],['_wl','WLINK'],['_ht','HOMTECS'],['_oem','OEM']],value: nvram.boot_logo},
		{ title: '硬件类型', name: 'boot_hw', type: 'select', options: [['ss','单模单卡'],['sd','单模双卡'],['dd','双模双卡']],value:nvram.boot_hw},
		{ title: 'GPS功能', name: 'boot_gps', type: 'select', options: [['n','无GPS'],['i','模块GPS'],['e','扩展GPS']],value:nvram.boot_gps},
		{ title: 'R21', name: 'boot_r21', type: 'checkbox', value: (nvram.boot_r21 == '1') },
		{ title: '外置存储', name: 'boot_udisk', type: 'checkbox', value: (nvram.boot_udisk == '1') },
		{ title: '关闭WIFI', name: 'boot_wlan_off', type: 'checkbox', value: (nvram.boot_wlan_off == '1') },
		{ title: '关闭WAN口', name: 'boot_wan_off', type: 'checkbox', value: (nvram.boot_wan_off == '1') }
		], { align: 'left' });
			</script>
            
	<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button>
	<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn"><% translate("Cancel"); %><i class="icon-cancel"></i></button>
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
