<?PHP include 'header.php'; ?>
<!-- <script type='text/javascript' src='wireless.jsx?_http_id=<% nv(http_id); %>'></script> -->
<script type="text/javascript">
//	<% nvram("icmp_rtt_time2,icmp_rtt_enable2,load_balance_enable,modem1_weight,modem2_weight,cops_network2,modem_mtu2,lte_use_ppp2,enable_modem2,is_ecm_dial2,cops_oper2,tcp_server2,tcp_port2,lte2,modem_type2,cellConMode2,PingEnable2,PingInterval2,UtmsPingAddr2,UtmsPingAddr12,smspasswd2,PingMax2,icmp_action2,rx_tx_enable2,rx_tx_mode2,rx_tx_action2,rx_tx_check_int2,cellType2,CelldialPincode2,CelldialNum2,CelldialApn2,CelldialUser2,CelldialPwd2,auth_type2,local_ip2,cell_mode2,tdd_mode2,ppp_custom2,ppp_demand2,ppp_idletime2,ppp_redialperiod2,timeUpLink2,timeDownLink2,SSTimeEnable2,SSTimeUpLink2,SSTimeDownLink2,RingControl2,RingNum2,SmControl2,SmNum2")%>
// <% bootinfo(); %>
var tabs =[['sim0',$lang.BASIC_PARAMETER_SETTING],['sim1','SIM']];
if(!nvram.enable_modem2){
	nvram.enable_modem2 = 0;
}
if(!nvram.is_ecm_dial2){
	nvram.is_ecm_dial2 = 0;
}
function tabSelect(name)
{
	tgHideIcons();
	tabHigh(name);
	for (var i = 0; i < tabs.length; ++i)
	{
		var on = (name == tabs[i][0]);
		elem.display(tabs[i][0] + '-tab', on);
	}

	cookie.set('sim_tab', name);
}
function verifyFields(focused, quiet)
{
	var v;
	var a, b, c, d, e;
	var ok = 1;
	var f, g, h, k, i, j;
	if(E('_f_enable_modem2').checked)
	{
		E('sim_config').style.display = '';

	}
	else
	{
		E('sim_config').style.display = 'none';
		return 1;
	}
	f = E('_ppp_demand2').value;
	elem.display(PR(E('_ppp_redialperiod2')), 0);
	elem.display(PR(E('_ppp_idletime2')), f == 1);
	elem.display(PR(E('_timeUpLink2')), f == 2);
	elem.display(PR(E('_timeDownLink2')), f == 2);
	elem.display(PR(E('_f_SSTimeEnable2')), f == 2);
	g = !E('_f_SSTimeEnable2').checked;
	elem.display(PR(E('_SSTimeUpLink2')), !g && f == 2);
	elem.display(PR(E('_SSTimeDownLink2')), !g && f == 2);
	elem.display(PR(E('_f_RingControl2')), f == 3);
	elem.display(PR(E('_RingNum2')), f == 3);
	E('_RingNum2').disabled = !E('_f_RingControl2').checked;
	elem.display(PR(E('_f_SmControl2')), f == 3);
	elem.display(PR(E('_SmNum2')), f == 3);
	E('_SmNum2').disabled = !E('_f_SmControl2').checked;
	elem.display(PR(E('_cellType2')), (nvram.is_ecm_dial2=='1') & (nvram.modem_type2 != 'ME3760:LTE/WCDMA/TD-SCDMA'));
	elem.display(PR(E('_tdd_mode2')), (nvram.is_ecm_dial2=='1') & (nvram.modem_type2 == 'ME3760:LTE/WCDMA/TD-SCDMA'));
	elem.display(PR(E('_cell_mode2')), (nvram.is_ecm_dial2=='0'));

	f = E('_local_ip2');
	if ((f.value.length) && (!v_ip('_local_ip2', quiet, 1))) ok = 0;
	else ferror.clear(f);

	elem.display(PR(E('_f_lte_use_ppp2')), (nvram.is_ecm_dial2=='1'));
	c = !E('_f_PingEnable2').checked;
	elem.display(PR('_PingInterval2'), !c);
	elem.display(PR('_UtmsPingAddr2'), !c);
	elem.display(PR('_UtmsPingAddr12'), !c);
	elem.display(PR('_PingMax2'), !c);
	elem.display(PR('_icmp_action2'), !c);
	elem.display(PR('_f_icmp_rtt_enable2'), !c);
	elem.display(PR('_icmp_rtt_time2'), !c);
	if(E('_f_icmp_rtt_enable2').checked)
	{
		elem.display(PR(E('_icmp_rtt_time2')), 1);

	}
	else
	{
		elem.display(PR(E('_icmp_rtt_time2')), 0);
	}
	i = !E('_f_rx_tx_enable2').checked;
	elem.display(PR('_rx_tx_mode2'), !i);
	elem.display(PR('_rx_tx_action2'), !i);
	elem.display(PR('_rx_tx_check_int2'), !i);
	elem.display(PR('_CelldialApn2'), !(nvram.modem_type2=='MC509:CDMA2000')&&!(nvram.modem_type2=="MC2716:CDMA 1x/CDMA 2000")&&!(nvram.modem_type2=="EM660:CDMA 1x/CDMA 2000")&&!(nvram.modem_type2=="C5300:CDMA 1x/CDMA 2000"));
	// domain name or IP address
	a = ['_UtmsPingAddr2'];
	for (i = a.length - 1; i >= 0; --i)
		if (((!v_length(a[i], 1, 1)) || ((!v_ip(a[i], 1)) && (!v_domain(a[i], 1))))) {
			if (!quiet && ok) ferror.show(a[i]);
			ok = 0;
		}
	if ((E('_UtmsPingAddr12').value.length) && ((!v_ip('_UtmsPingAddr12', 1)) && (!v_domain('_UtmsPingAddr12', 1))))
	{
		if (!quiet && ok) ferror.show('_UtmsPingAddr12');
		ok = 0;
	}
	// range
	a = [['_icmp_rtt_time2', 50, 2000],['_PingInterval2', 1, 1440],['_PingMax2', 1, 1440],['_rx_tx_check_int2', 1, 1440],['_ppp_idletime2', 3, 1440],['_ppp_redialperiod2', 1, 86400],['_modem_mtu2', 0, 1500]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}
	k = E('_local_ip2');
	if ((k.value.length) && (!_v_iptaddr(k, quiet, 15, 1, 1))) ok = 0;
	else ferror.clear(k);
	return ok;
}
function earlyInit()
{
	var tlen;
	var cook;
	tlen = tabs.length;
	cook = cookie.get('sim_tab');
	if(cook != null)
	{
		tlen = cook.substring(3,4);
		if(tlen >= tabs.length)
		{
			tabSelect(tabs[0][0]);
		}
		else
		{
			tabSelect(cookie.get('sim_tab'));
		}
	}
	else
	{
		tabSelect(tabs[0][0]);
	}
	verifyFields(null, 1);
}
function save()
{
	var a, b, c;
	var i;
	var u, uidx, wmode, sm2, wradio;
	if (!verifyFields(null, false)) return;
	var fom = E('_fom');
	if (E('_ppp_demand2').value == 0)//auto keepalive
	{
		fom.cellConMode2.value = 1;
		fom.TimeControl2.value = 0;
		fom.RingControl2.value = 0;
		fom.SmControl2.value = 0;
	}
	if (E('_ppp_demand2').value == 1){//demand
		fom.cellConMode2.value = 1;
		fom.TimeControl2.value = 0;
		fom.RingControl2.value = 0;
		fom.SmControl2.value = 0;
	}
	if (E('_ppp_demand2').value == 2){//time control
		fom.cellConMode2.value = 0;
		fom.TimeControl2.value = 1;
		fom.RingControl2.value = 0;
		fom.SmControl2.value = 0;
	}
	if (E('_ppp_demand2').value == 3){//sms & ring
		fom.cellConMode2.value = 0;
		fom.TimeControl2.value = 0;
		fom.RingControl2.value = E('_f_RingControl2').checked ? 1 : 0;
		fom.SmControl2.value = E('_f_SmControl2').checked ? 1 : 0;
	}
	if (E('_ppp_demand2').value == 4){//manual
		fom.TimeControl2.value = 0;
		fom.cellConMode2.value = 0;
		fom.RingControl2.value = 0;
		fom.SmControl2.value = 0;
		fom._nextpage.value = "status-overview.asp";
	}

	fom.SSTimeEnable2.value = E('_f_SSTimeEnable2').checked ? 1 : 0;
	fom.PingEnable2.value = E('_f_PingEnable2').checked ? 1 : 0;
	fom.icmp_rtt_enable2.value = E('_f_icmp_rtt_enable2').checked ? 1 : 0;
	fom.rx_tx_enable2.value = E('_f_rx_tx_enable2').checked ? 1 : 0;
	fom.SmControl2.value = 1;
	fom.enable_modem2.value = E('_f_enable_modem2').checked ? 1 : 0;
	fom.load_balance_enable.value = E('_f_load_balance_enable').checked ? 1 : 0;
	fom.lte_use_ppp2.value = E('_f_lte_use_ppp2').checked ? 1 : 0;
	if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
	{
		// form.submit(fom);
		return submit_form('_fom');
	}
	else
	{
		return;
	}
}
function init()
{
}
</script>
<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#basic-cellular2.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_service' value='modem_checkdial2-restart'>
<input type='hidden' name='_reboot' value='1'>
<input type='hidden' name='cellConMode2'>
<input type='hidden' name='PingEnable2'>
<input type='hidden' name='rx_tx_enable2'>
<input type='hidden' name='SSTimeEnable2'>
<input type='hidden' name='RingControl2'>
<input type='hidden' name='SmControl2'>
<input type='hidden' name='TimeControl2'>
<input type='hidden' name='enable_modem2'>
<input type='hidden' name='icmp_rtt_enable2'>
<input type='hidden' name='lte_use_ppp2'>
<input type='hidden' name='load_balance_enable'>

<div class="box">
<div class="heading"><script type="text/javascript">document.write($lang.MOBILE_NETWORK_CONFIGURATION)</script></div>
<div class="content" id="sesdiv_tmp">
<script type='text/javascript'>
$('#sesdiv_tmp').forms([
{ title: $lang.ENABLE_MODULES, name: 'f_enable_modem2', type: 'checkbox', value: (nvram.enable_modem2 == 1) },
{ title: $lang.LOAD_BALANCING_ENABLED, name: 'f_load_balance_enable', type: 'checkbox', value: (nvram.load_balance_enable == 1) },
{ title: $lang.MODULE_1_WEIGHT, name: 'modem1_weight', type: 'text', maxlen: 2, size: 6, value: nvram.modem1_weight },
{ title: $lang.MODULE_2_WEIGHT, name: 'modem2_weight', type: 'text', maxlen: 2, size: 6, value: nvram.modem2_weight }
], { align: 'left' });
</script>

</div>
</div>

<div id = 'sim_config'></div>
<script type='text/javascript'>
			var htmlOut = tabCreate.apply(this, tabs);
			t = tabs[0][0];
		htmlOut +='<div id=\''+t+'-tab\'>';
		htmlOut += '<div class="box" data-box="sim' + '0' +'">';
		htmlOut += '<div class="content sim-' + '0' + '">';
		htmlOut += createFormFields([
			{ title: $lang.ENABLE_PPP_MODE, name: 'f_lte_use_ppp2', type: 'checkbox', value: (nvram.lte_use_ppp2 == 1) },
			{ title: $lang.PINGENABLE, name: 'f_PingEnable2', type: 'checkbox', value: (nvram.PingEnable2 == 1) },
			{ title: $lang.UTMSPINGADDR,indent: 2,name: 'UtmsPingAddr2', type: 'text', maxlen: 30, size: 20, value: nvram.UtmsPingAddr2 },
			{ title: $lang.UTMSPINGADDR + '(' + $lang.OPTIONAL + ')',indent: 2,name: 'UtmsPingAddr12', type: 'text', maxlen: 30, size: 20, value: nvram.UtmsPingAddr12 },
			{ title: $lang.PINGINTERVAL,indent: 2,name: 'PingInterval2', type: 'text', maxlen: 5, size: 5, suffix: ' <i>(' + $lang.VAR_SECOND + ')</i>',value: nvram.PingInterval2 },
			{ title: $lang.VAR_RETRY,indent: 2,name: 'PingMax2', type: 'text', maxlen: 5, size: 5, suffix: ' <i>(' + $lang.VAR_FREQUENCY + ')</i>', value: nvram.PingMax2 },
			{ title: 'ICMP RTT Enable', name: 'f_icmp_rtt_enable2', type: 'checkbox', value: (nvram.icmp_rtt_enable2 == 1) },
			{ title:'RTT Threshold',indent: 2,name: 'icmp_rtt_time2', type: 'text', maxlen: 5, size: 5, suffix: ' <i>(Range 50-2000 ms)</i>',value: nvram.icmp_rtt_time2 },
			{ title: $lang.EXCEPTION_HANDLING, indent: 2, name: 'icmp_action2', type: 'select', options: [['0', $lang.REDIAL],['1', $lang.REBOOT_SYSTEM],['2', $lang.CHANGE_SIM]],
			value: nvram.icmp_action2 },
			{ title: $lang.RX_TX_ENABLE, name: 'f_rx_tx_enable2', type: 'checkbox', value: (nvram.rx_tx_enable2 == 1) },
			{ title: $lang.RX_TX_MODE, indent: 2, name: 'rx_tx_mode2', type: 'select', options: [['0', 'Rx'],['1', 'Tx'],['2', 'Tx & Rx']],
			value: nvram.rx_tx_mode2 },
			{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'rx_tx_check_int2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.MINUTES + ')' + $lang.RANGE + ': 1 ~ 1440</i>',
			value: nvram.rx_tx_check_int2 },
			{ title: $lang.EXCEPTION_HANDLING, indent: 2, name: 'rx_tx_action2', type: 'select', options: [['0', $lang.REDIAL],['1', $lang.REBOOT_SYSTEM]],
			value: nvram.rx_tx_action2 },
			{ title: $lang.CUSTOM_DIALING_OPTIONS, name: 'ppp_custom2', type: 'text', maxlen: 256, size: 64, value: nvram.ppp_custom2, hidden: 1 },
			{ title: $lang.DIAL_MODE, name: 'ppp_demand2', type: 'select', options: [['0', $lang.AUTOMATIC_ONLINE_AND_OFFLINE],['1', $lang.DIAL_ON_DEMAND],['2', $lang.TIMED_ONLINE_AND_OFFLINE],['4', $lang.MANUAL_ONLINE_AND_OFFLINE]],
			value: nvram.ppp_demand2 , hidden: 1},
			{ title: $lang.TIMED_ONLINE_TIME, indent: 2, name: 'timeUpLink2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + ':08:30</i>',
			value: nvram.timeUpLink2,  hidden: nvram.lte2 == '1' },
			{ title: $lang.TIMED_OFFLINE_TIME, indent: 2, name: 'timeDownLink2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + ':18:00</i>',
			value: nvram.timeDownLink2,  hidden: nvram.lte2 == '1' },
			{ title: $lang.SATURDAY_SUNDAY_TIMED, indent: 2, name: 'f_SSTimeEnable2', type: 'checkbox', value: (nvram.SSTimeEnable2 == 1) },
			{ title: $lang.ONLINE_TIME_ON_SATURDAY_SUNDAY, indent: 3, name: 'SSTimeUpLink2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + '：09:00</i>',
			value: nvram.SSTimeUpLink2,  hidden: nvram.lte2 == '1' },
			{ title: $lang.OFFLINE_TIME_ON_SATURDAY_SUNDAY, indent: 3, name: 'SSTimeDownLink2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + '：17:00</i>',
			value: nvram.SSTimeDownLink2,  hidden: nvram.lte2 == '1' },
			{ title: $lang.VOICE_NUMBER, multi: [
			{ name: 'f_RingControl2', type: 'checkbox', value: nvram.RingControl2 == '1', suffix: '  ' },
			{ name: 'RingNum2', type: 'text', maxlen: 15, size: 17, value: nvram.RingNum2, suffix: ' <i>'+ $lang.EX + ':13800138000</i>' }
			] },
			{ title: $lang.SMS_PHONE, multi: [
			{ name: 'f_SmControl2', type: 'checkbox', value: nvram.SmControl2 == '1', suffix: '  ' },
			{ name: 'SmNum2', type: 'text', maxlen: 15, size: 17, value: nvram.SmNum2, suffix: ' <i>'+ $lang.EX + ':13800138000</i>' }
			] },
			{ title: $lang.MAXIMUM_IDLE_TIME, indent: 2, name: 'ppp_idletime2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.MINUTES + ')</i>',
			value: nvram.ppp_idletime2,  hidden: nvram.lte2 == '1' },
			{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'ppp_redialperiod2', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_SECOND + ')</i>',
			value: nvram.ppp_redialperiod2,  hidden: nvram.lte2 == '1' },
			{ title: 'MTU', name: 'modem_mtu2', type: 'text', maxlen: 4, size: 6, value: nvram.modem_mtu2, suffix: ' <small>('+ $lang.IS_THE_SYSTEM_DEFAULT + ')</small>'},
			{ title: 'CIMI' + $lang.VAR_ALARM_SEND_TO, multi: [
			{ name: 'tcp_server2', type: 'text', maxlen: 63, size: 20, value: nvram.tcp_server2, suffix: ':' },
			{ name: 'tcp_port2', type: 'text', maxlen: 5, size: 7, value: nvram.tcp_port2 } ]},
			{ title: $lang.SMS_VERIFICATION_CODE, name: 'smspasswd2', type: 'text', maxlen: 15, size: 17, value: nvram.smspasswd2 },
			{ title: $lang.CARRIER_LOCK, name: 'cops_oper2', type: 'text', maxlen: 6, size: 8, value: nvram.cops_oper2, suffix: ' <i>'+ $lang.EX + ':46001</i>'}
		]);
		htmlOut +='</div></div></div>';
			for (var uidx = 1; uidx < tabs.length; ++uidx) {
					t = tabs[uidx][0];
					htmlOut += '<div id=\''+ t +'-tab\'>';
					var pre = 'SIM ' + uidx + ' ';
					var suf = '';
					pre = '';
					htmlOut += '<div class="box" data-box="sim' + uidx +'">';
					htmlOut += '<div class="content sim-' + uidx + '">';
					f = [
								{ title: pre+$lang.NET_MODE, name: 'tdd_mode2'+suf, type: 'select', options: [['auto', 'Auto'],['pre-lte', $lang.LTE_PRIORITY],['pre-td', $lang.TD_SCDMA_PRIORITY],['only-lte', $lang.LTE_ONLY],['only-td', $lang.TD_SCDMA_ONLY]], value: nvram['tdd_mode2'+suf]},
								{ title: pre+$lang.NET_MODE, name: 'cellType2'+suf, type: 'select', options: [['0', 'Auto'],['1', 'LTE(FDD/TDD)'],['2', '3G(WCDMA/TD-SCDMA/HSPA)'],['3', '3G(CDMA 2000/CDMA 1x)']], value: nvram['cellType2'+suf]},
								{ title: pre+$lang.NET_MODE, name: 'cell_mode2'+suf, type: 'select', options: [['0', 'Auto'],['1', '3G'],['2', "2G"]], value: nvram['cell_mode2'+suf] },
								{ title: pre+$lang.NETWORK_OPERATOR, name: 'cops_network2'+suf, type: 'select', options: [['1', 'Others'],['3', 'Verizon']], value: nvram['cops_network2'+suf] },
								{ title: pre+$lang.PIN_CODE, name: 'CelldialPincode2'+suf, type: 'text', maxlen: 6, size: 8, value: nvram['CelldialPincode2'+suf]},
								{ title: pre+$lang.APN_ACCESS_POINT, name: 'CelldialApn2'+suf, type: 'text', maxlen: 60, size: 64, value: nvram['CelldialApn2'+suf] },
								{ title: pre+$lang.VAR_USER_NAME, name: 'CelldialUser2'+suf, type: 'text', maxlen: 60, size: 64, value: nvram['CelldialUser2'+suf]},
								{ title: pre+$lang.VAR_PASSWD, name: 'CelldialPwd2'+suf, type: 'password', maxlen: 60, size: 64, peekaboo: 1, value: nvram['CelldialPwd2'+suf] },
								{ title: pre+$lang.CELLDIALNUM, name: 'CelldialNum2'+suf, type: 'text', maxlen: 25, size: 32, value: nvram['CelldialNum2'+suf]},
								{ title: pre+$lang.AUTH_TYPE, name: 'auth_type2'+suf, type: 'select', options: [['0', 'Auto'],['1', 'PAP'],['2', 'CHAP'],['3', 'MS-CHAP'],['4', 'MS-CHAPv2']],
								value: nvram['auth_type2'+suf] },
								{ title: pre+$lang.LOCAL_IP, name: 'local_ip2'+suf, type: 'text', maxlen: 15, size: 17, value: nvram['local_ip2'+suf] }
						];
					htmlOut += createFormFields(f);
					htmlOut +='</div></div></div>';
		}
		htmlOut +='</ul><div class=\'tabs-bottom\'></div>';
		$('#sim_config').append(htmlOut);
</script>
<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
<script type="text/javascript">earlyInit();</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
</form>
<?PHP include 'footer.php'; ?>
