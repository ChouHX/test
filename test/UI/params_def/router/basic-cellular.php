<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram("ims_enable,icmp_rtt_time,icmp_rtt_enable,band_lock_num_sa,band_lock_num_nsa,band_lock_num_lte,band_lock_enable,band_lock_num_sa2,band_lock_num_nsa2,band_lock_num_lte2,band_lock_enable2,cops_network,cops_network2,modem_mtu,lte_use_ppp,enable_modem,is_ecm_dial,dualsim,cops_oper,tcp_server,tcp_port,lte,cell_mode,tdd_mode,modem_type,cellConMode,PingEnable,PingInterval,UtmsPingAddr,UtmsPingAddr1,smspasswd,PingMax,icmp_action,rx_tx_enable,rx_tx_mode,rx_tx_action,rx_tx_check_int,backup_timeout,main_timeout,cellType,NR_type,CelldialPincode,CelldialNum,CelldialApn,CelldialUser,CelldialPwd,auth_type,local_ip,CelldialPincode2,cellType2,NR_type2,CelldialNum2,cell_mode2,tdd_mode2,CelldialApn2,CelldialUser2,CelldialPwd2,auth_type2,local_ip2,ppp_custom,ppp_demand,ppp_idletime,ppp_redialperiod,timeUpLink,timeDownLink,SSTimeEnable,SSTimeUpLink,SSTimeDownLink,RingControl,RingNum,SmControl,SmNum,reset_modem")%>
// <% bootinfo(); %>
var sim_num;
var bi = JSON.parse(nvram.bi);
if(!nvram.enable_modem){
	nvram.enable_modem = 0;
}
if(!nvram.is_ecm_dial){
	nvram.is_ecm_dial = 0;
}

var icmp_action_arr = [['0', $lang.REDIAL],['1', $lang.REBOOT_SYSTEM]];
if(nvram.router_2){
	var arr = nvram.router_2.split('<');
	if (arr[5] == 'sd' || arr[5] == 'dd') {
		icmp_action_arr = [['0', $lang.REDIAL],['1', $lang.REBOOT_SYSTEM],['2', $lang.CHANGE_SIM]];
	}
}

if(bi.hw == 'sd')
{
	sim_num = 3;
}
else
{
	sim_num = 2;
}
var tabs = new Array();
for (var uidx = 0; uidx < sim_num; ++uidx) {
	var name = 'sim' + uidx;
	tabs[uidx] = new Array();
	tabs[uidx][0] = name;
	if(uidx == 0)
	{		
		tabs[uidx][1] = $lang.BASIC_PARAMETER_SETTING;	
	}
	else{
		tabs[uidx][1] = 'SIM ' + uidx;
	}

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
	if(E('_f_enable_modem').checked)
	{
		E('sim_config').style.display = '';

	}
	else
	{
		E('sim_config').style.display = 'none';
		return 1;
	}
	// E('_f_ims_enable').disabled = !E('_f_enable_modem').checked;
	f = E('_ppp_demand').value;
	elem.display(PR(E('_ppp_redialperiod')), 0);
	elem.display(PR(E('_ppp_idletime')), f == 1);
	elem.display(PR(E('_timeUpLink')), f == 2);
	elem.display(PR(E('_timeDownLink')), f == 2);
	elem.display(PR(E('_f_SSTimeEnable')), f == 2);
	g = !E('_f_SSTimeEnable').checked;
	elem.display(PR(E('_SSTimeUpLink')), !g && f == 2);
	elem.display(PR(E('_SSTimeDownLink')), !g && f == 2);
	elem.display(PR(E('_f_RingControl')), f == 3);
	elem.display(PR(E('_RingNum')), f == 3);
	E('_RingNum').disabled = !E('_f_RingControl').checked;		
	elem.display(PR(E('_f_SmControl')), f == 3);
	elem.display(PR(E('_SmNum')), f == 3);
	E('_SmNum').disabled = !E('_f_SmControl').checked;	
	if(bi.hw=='sd')
	{
		h = E('_dualsim').value;
		elem.display(PR(E('_main_timeout')), h == 3);
		elem.display(PR(E('_backup_timeout')), h == 3);
		elem.display(PR(E('_cellType')), (nvram.is_ecm_dial=='1') & (nvram.modem_type != 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_cellType2')), (nvram.is_ecm_dial=='1') & (nvram.modem_type != 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_tdd_mode')), (nvram.is_ecm_dial=='1') & (nvram.modem_type == 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_tdd_mode2')), (nvram.is_ecm_dial=='1') & (nvram.modem_type == 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_cell_mode')), (nvram.is_ecm_dial=='0'));
		elem.display(PR(E('_cell_mode2')), (nvram.is_ecm_dial=='0'));
		elem.display(PR(E('_f_band_lock_enable')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_f_band_lock_enable2')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_sa')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_nsa')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_nsa')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_sa2')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_nsa2')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_lte2')), (nvram.modem_type == 'RM500'));
		if((bi.r_type.indexOf("G") == -1) || (bi.r_type.indexOf("V2S") != -1))//MT7628 or MT7621 single LTE modem
		{
			elem.display(PR(E('_NR_type')), 0);
			elem.display(PR(E('_NR_type2')), 0);
			//7628 not surpport 5G modem
			E('_cellType').options[1].disabled=1
			E('_cellType2').options[1].disabled=1
		}
		var bk = E('_cellType2').value;
		var nr_type = E('_NR_type2').value;
		if(bk == '0')
		{
			E('_f_band_lock_enable2').disabled = 1;
			if(E('_f_band_lock_enable2').checked)
			{
				if(nr_type == '0')//sa and nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
				else if(nr_type == '1') //nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
				else if(nr_type == '2') //sa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
			}
			else
			{
				if(nr_type == '0')//sa and nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
				else if(nr_type == '1') //nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
				else if(nr_type == '2') //sa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
			}
		}
		else
		{
			E('_f_band_lock_enable2').disabled = 0;
			if(E('_f_band_lock_enable2').checked)
			{
				if(nr_type == '0')//sa and nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
				else if(nr_type == '1') //nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
				else if(nr_type == '2') //sa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
			}
			else
			{
				if(nr_type == '0')//sa and nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
				else if(nr_type == '1') //nsa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 1);
					elem.display(PR(E('_band_lock_num_nsa2')), 0);
					elem.display(PR(E('_band_lock_num_lte2')), 0);
				}
				else if(nr_type == '2') //sa
				{
					elem.display(PR(E('_band_lock_num_sa2')), 0);
					elem.display(PR(E('_band_lock_num_nsa2')), 1);
					elem.display(PR(E('_band_lock_num_lte2')), 1);
				}
			}
		}
		/*if(h==2)
		  {
		  E('sim1').style.display='none';
		  E('sim2').style.display='';
		  }
		  else if(h==1)
		  {
		  E('sim1').style.display='';
		  E('sim2').style.display='none';
		  }
		  else
		  {
		  E('sim1').style.display='';
		  E('sim2').style.display='';
		  }*/
	}
	else
	{
		elem.display(PR(E('_dualsim')), 0);
		elem.display(PR(E('_main_timeout')), 0);
		elem.display(PR(E('_backup_timeout')), 0);
		elem.display(PR(E('_cellType')), (nvram.is_ecm_dial=='1') & (nvram.modem_type != 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_tdd_mode')), (nvram.is_ecm_dial=='1') & (nvram.modem_type == 'ME3760:LTE/WCDMA/TD-SCDMA'));
		elem.display(PR(E('_cell_mode')), (nvram.is_ecm_dial=='0'));
		elem.display(PR(E('_f_band_lock_enable')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_sa')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_nsa')), (nvram.modem_type == 'RM500'));
		elem.display(PR(E('_band_lock_num_lte')), (nvram.modem_type == 'RM500'));
		if((bi.r_type.indexOf("G") == -1) || (bi.r_type.indexOf("V2S") != -1))//MT7628 or MT7621 single LTE modem 
		{
			//7628 not surpport 5G modem
			elem.display(PR(E('_NR_type')), 0);
			E('_cellType').options[1].disabled=1
		}
	}
	var bk = E('_cellType').value;
	var nr_type = E('_NR_type').value;
	if(bk == '0')
	{
		E('_f_band_lock_enable').disabled = 1;
		if(E('_f_band_lock_enable').checked)
		{
			if(nr_type == '0')//sa and nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
			else if(nr_type == '1') //nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
			else if(nr_type == '2') //sa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
		}
		else
		{
			if(nr_type == '0')//sa and nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
			else if(nr_type == '1') //nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
			else if(nr_type == '2') //sa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
		}
	}
	else
	{
		E('_f_band_lock_enable').disabled = 0;
		if(E('_f_band_lock_enable').checked)
		{
			if(nr_type == '0')//sa and nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
			else if(nr_type == '1') //nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
			else if(nr_type == '2') //sa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
		}
		else
		{
			if(nr_type == '0')//sa and nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
			else if(nr_type == '1') //nsa
			{
				elem.display(PR(E('_band_lock_num_sa')), 1);
				elem.display(PR(E('_band_lock_num_nsa')), 0);
				elem.display(PR(E('_band_lock_num_lte')), 0);
			}
			else if(nr_type == '2') //sa
			{
				elem.display(PR(E('_band_lock_num_sa')), 0);
				elem.display(PR(E('_band_lock_num_nsa')), 1);
				elem.display(PR(E('_band_lock_num_lte')), 1);
			}
		}
	}
	if(bi.hw == 'sd')
	{
		f = E('_local_ip');
		if ((f.value.length) && (!v_ip('_local_ip', quiet, 1))) ok = 0;
		else ferror.clear(f);
		g = E('_local_ip2');
		if ((g.value.length) && (!v_ip('_local_ip2', quiet, 1))) ok = 0;
		else ferror.clear(g);
	}
	else
	{
		f = E('_local_ip');
		if ((f.value.length) && (!v_ip('_local_ip', quiet, 1))) ok = 0;
		else ferror.clear(f);
	}
	elem.display(PR(E('_f_lte_use_ppp')), (nvram.is_ecm_dial=='1'));
	c = !E('_f_PingEnable').checked;
	elem.display(PR('_PingInterval'), !c);
	elem.display(PR('_UtmsPingAddr'), !c);
	elem.display(PR('_UtmsPingAddr1'), !c);
	elem.display(PR('_PingMax'), !c);
	elem.display(PR('_icmp_action'), !c);
	elem.display(PR('_f_icmp_rtt_enable'), !c);
	elem.display(PR('_icmp_rtt_time'), !c);
	if(E('_f_icmp_rtt_enable').checked)
	{
		elem.display(PR(E('_icmp_rtt_time')), 1);

	}
	else
	{
		elem.display(PR(E('_icmp_rtt_time')), 0);
	}
	i = !E('_f_rx_tx_enable').checked;
	elem.display(PR('_rx_tx_mode'), !i);
	elem.display(PR('_rx_tx_action'), !i);
	elem.display(PR('_rx_tx_check_int'), !i);
	elem.display(PR('_CelldialApn'), !(nvram.modem_type=='MC509:CDMA2000')&&!(nvram.modem_type=="MC2716:CDMA 1x/CDMA 2000")&&!(nvram.modem_type=="EM660:CDMA 1x/CDMA 2000")&&!(nvram.modem_type=="C5300:CDMA 1x/CDMA 2000"));
	// domain name or IP address
	a = ['_UtmsPingAddr'];
	for (i = a.length - 1; i >= 0; --i)
		if (((!v_length(a[i], 1, 1)) || ((!v_ip(a[i], 1)) && (!v_domain(a[i], 1))))) {
			if (!quiet && ok) ferror.show(a[i]);
			ok = 0;
		}
	if ((E('_UtmsPingAddr1').value.length) && ((!v_ip('_UtmsPingAddr1', 1)) && (!v_domain('_UtmsPingAddr1', 1))))
	{
		if (!quiet && ok) ferror.show('_UtmsPingAddr1');
		ok = 0;
	}
	// range
	a = [['_icmp_rtt_time', 50, 2000],['_PingInterval', 1, 1440],['_PingMax', 1, 1440],['_rx_tx_check_int', 1, 1440],['_ppp_idletime', 3, 1440],['_ppp_redialperiod', 1, 86400],['_modem_mtu', 0, 1500]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}
	k = E('_local_ip');
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
	if (E('_ppp_demand').value == 0)//auto keepalive
	{
		fom.cellConMode.value = 1;
		fom.TimeControl.value = 0;
		fom.RingControl.value = 0;
		fom.SmControl.value = 0;
	}
	if (E('_ppp_demand').value == 1){//demand
		fom.cellConMode.value = 1;
		fom.TimeControl.value = 0;
		fom.RingControl.value = 0;
		fom.SmControl.value = 0;
	}
	if (E('_ppp_demand').value == 2){//time control
		fom.cellConMode.value = 0;
		fom.TimeControl.value = 1;
		fom.RingControl.value = 0;
		fom.SmControl.value = 0;
	}
	if (E('_ppp_demand').value == 3){//sms & ring
		fom.cellConMode.value = 0;
		fom.TimeControl.value = 0;
		fom.RingControl.value = E('_f_RingControl').checked ? 1 : 0;	
		fom.SmControl.value = E('_f_SmControl').checked ? 1 : 0;
	}
	if (E('_ppp_demand').value == 4){//manual
		fom.TimeControl.value = 0;
		fom.cellConMode.value = 0;
		fom.RingControl.value = 0;
		fom.SmControl.value = 0;
		fom._nextpage.value = "status-overview.asp"; 
	}

	if(E('_dualsim').value == 2)
	{
		fom.sim_flag.value = 2;
	}
	else
	{
		fom.sim_flag.value = 1;
	}
	if(bi.hw == 'sd')
	{
		fom.band_lock_enable2.value = E('_f_band_lock_enable2').checked ? 1 : 0;
	}
	fom.ims_enable.value = E('_f_ims_enable').checked ? 1 : 0;
	fom.SSTimeEnable.value = E('_f_SSTimeEnable').checked ? 1 : 0;
	fom.PingEnable.value = E('_f_PingEnable').checked ? 1 : 0;
	fom.icmp_rtt_enable.value = E('_f_icmp_rtt_enable').checked ? 1 : 0;
	fom.rx_tx_enable.value = E('_f_rx_tx_enable').checked ? 1 : 0;
	fom.SmControl.value = 1;
	fom.reset_modem.value = 0;
	fom.enable_modem.value = E('_f_enable_modem').checked ? 1 : 0;
	fom.band_lock_enable.value = E('_f_band_lock_enable').checked ? 1 : 0;
	fom.lte_use_ppp.value = E('_f_lte_use_ppp').checked ? 1 : 0;
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
<input type='hidden' name='_nextpage' value='/#basic-cellular.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_service' value='modem_checkdial-restart'>
<input type='hidden' name='_reboot' value='1'>
<input type='hidden' name='cellConMode'>
<input type='hidden' name='ims_enable'>
<input type='hidden' name='PingEnable'>
<input type='hidden' name='rx_tx_enable'>
<input type='hidden' name='sim_flag'>
<input type='hidden' name='SSTimeEnable'>
<input type='hidden' name='RingControl'>
<input type='hidden' name='SmControl'>
<input type='hidden' name='TimeControl'>
<input type='hidden' name='reset_modem'>
<input type='hidden' name='enable_modem'>
<input type='hidden' name='band_lock_enable'>
<input type='hidden' name='icmp_rtt_enable'>
<input type='hidden' name='lte_use_ppp'>
<script type='text/javascript'>
var html = '';
if(bi.hw == 'sd')
{
	html = '<input type=\'hidden\' name=\'band_lock_enable2\'>';
}
$('#_fom').append(html);
</script>

<div class="box">
<div class="heading"><script type="text/javascript">document.write($lang.MOBILE_NETWORK_CONFIGURATION)</script></div>
<div class="content" id="sesdiv_tmp">
<script type='text/javascript'>
$('#sesdiv_tmp').forms([
{ title: $lang.ENABLE_MODULES, name: 'f_enable_modem', type: 'checkbox', value: (nvram.enable_modem == 1) }
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
			{ title: 'IMS', name: 'f_ims_enable', type: 'checkbox', value: (nvram.ims_enable == 1) },
			{ title: $lang.ENABLE_PPP_MODE, name: 'f_lte_use_ppp', type: 'checkbox', value: (nvram.lte_use_ppp == 1) },
			{ title: $lang.PINGENABLE, name: 'f_PingEnable', type: 'checkbox', value: (nvram.PingEnable == 1) },
			{ title: $lang.UTMSPINGADDR, indent: 2,name: 'UtmsPingAddr', type: 'text', maxlen: 30, size: 20, value: nvram.UtmsPingAddr },
			{ title: $lang.UTMSPINGADDR + '(' + $lang.OPTIONAL + ')', indent: 2,name: 'UtmsPingAddr1', type: 'text', maxlen: 30, size: 20, value: nvram.UtmsPingAddr1 },
			{ title: $lang.PINGINTERVAL, indent: 2,name: 'PingInterval', type: 'text', maxlen: 5, size: 5, suffix: ' <i>('+ $lang.VAR_SECOND + ')</i>',value: nvram.PingInterval },
			{ title: $lang.PINGMAX, indent: 2,name: 'PingMax', type: 'text', maxlen: 5, size: 5, suffix: ' <i>('+ $lang.VAR_FREQUENCY + ')</i>', value: nvram.PingMax },
			{ title: 'ICMP RTT Enable', name: 'f_icmp_rtt_enable', type: 'checkbox', value: (nvram.icmp_rtt_enable == 1) },
			{ title:'RTT Threshold',indent: 2,name: 'icmp_rtt_time', type: 'text', maxlen: 5, size: 5, suffix: ' <i>(Range 50-2000 ms)</i>',value: nvram.icmp_rtt_time },
			{ title: $lang.EXCEPTION_HANDLING, indent: 2, name: 'icmp_action', type: 'select', options: icmp_action_arr,
			value: nvram.icmp_action },
			{ title: $lang.RX_TX_ENABLE, name: 'f_rx_tx_enable', type: 'checkbox', value: (nvram.rx_tx_enable == 1) },
			{ title: $lang.RX_TX_MODE, indent: 2, name: 'rx_tx_mode', type: 'select', options: [['0', 'Rx'],['1', 'Tx'],['2', 'Tx & Rx']],
			value: nvram.rx_tx_mode },
			{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'rx_tx_check_int', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.MINUTES + ')' + $lang.RANGE + ': 1 ~ 1440</i>',
			value: nvram.rx_tx_check_int },
			{ title: $lang.EXCEPTION_HANDLING, indent: 2, name: 'rx_tx_action', type: 'select', options: [['0', $lang.REDIAL],['1', $lang.REBOOT_SYSTEM]],
			value: nvram.rx_tx_action },
			{ title: $lang.CUSTOM_DIALING_OPTIONS, name: 'ppp_custom', type: 'text', maxlen: 256, size: 64, value: nvram.ppp_custom, hidden: 1 },
			{ title: $lang.DIAL_MODE, name: 'ppp_demand', type: 'select', options: [['0', $lang.AUTOMATIC_ONLINE_AND_OFFLINE],['1', $lang.DIAL_ON_DEMAND],['2', $lang.TIMED_ONLINE_AND_OFFLINE],['4', $lang.MANUAL_ONLINE_AND_OFFLINE]],
			value: nvram.ppp_demand , hidden: 1},
			{ title: $lang.TIMED_ONLINE_TIME, indent: 2, name: 'timeUpLink', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + ':08:30</i>',
			value: nvram.timeUpLink,  hidden: nvram.lte == '1' },
			{ title: $lang.TIMED_OFFLINE_TIME, indent: 2, name: 'timeDownLink', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + ':18:00</i>',
			value: nvram.timeDownLink,  hidden: nvram.lte == '1' },
			{ title: $lang.SATURDAY_SUNDAY_TIMED, indent: 2, name: 'f_SSTimeEnable', type: 'checkbox', value: (nvram.SSTimeEnable == 1) },
			{ title: $lang.ONLINE_TIME_ON_SATURDAY_SUNDAY, indent: 3, name: 'SSTimeUpLink', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + '：09:00</i>',
			value: nvram.SSTimeUpLink,  hidden: nvram.lte == '1' },
			{ title: $lang.OFFLINE_TIME_ON_SATURDAY_SUNDAY, indent: 3, name: 'SSTimeDownLink', type: 'text', maxlen: 5, size: 7, suffix: ' <i>'+ $lang.EX + '：17:00</i>',
			value: nvram.SSTimeDownLink,  hidden: nvram.lte == '1' },
			{ title: $lang.VOICE_NUMBER, multi: [
			{ name: 'f_RingControl', type: 'checkbox', value: nvram.RingControl == '1', suffix: '  ' },
			{ name: 'RingNum', type: 'text', maxlen: 15, size: 17, value: nvram.RingNum, suffix: ' <i>'+ $lang.EX + ':13800138000</i>' }
			] },	
			{ title: $lang.SMS_PHONE, multi: [
			{ name: 'f_SmControl', type: 'checkbox', value: nvram.SmControl == '1', suffix: '  ' },
			{ name: 'SmNum', type: 'text', maxlen: 15, size: 17, value: nvram.SmNum, suffix: ' <i>'+ $lang.EX + ':13800138000</i>' }
			] },
			{ title: $lang.MAXIMUM_IDLE_TIME, indent: 2, name: 'ppp_idletime', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.MINUTES + ')</i>',
			value: nvram.ppp_idletime,  hidden: nvram.lte == '1' },
			{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'ppp_redialperiod', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_SECOND + ')</i>',
			value: nvram.ppp_redialperiod,  hidden: nvram.lte == '1' },
			{ title: 'MTU', name: 'modem_mtu', type: 'text', maxlen: 4, size: 6, value: nvram.modem_mtu, suffix: ' <small>('+ $lang.IS_THE_SYSTEM_DEFAULT + ')</small>'},
			{ title: 'CIMI' + $lang.VAR_ALARM_SEND_TO, multi: [
			{ name: 'tcp_server', type: 'text', maxlen: 63, size: 20, value: nvram.tcp_server, suffix: ':' },
			{ name: 'tcp_port', type: 'text', maxlen: 5, size: 7, value: nvram.tcp_port } ]},
			{ title: $lang.SMS_VERIFICATION_CODE, name: 'smspasswd', type: 'text', maxlen: 15, size: 17, value: nvram.smspasswd },
			{ title: $lang.CARRIER_LOCK, name: 'cops_oper', type: 'text', maxlen: 6, size: 8, value: nvram.cops_oper, suffix: ' <i>'+ $lang.EX + ':46001</i>'},
			{ title: $lang.DUALSIM, name: 'dualsim', type: 'select', options: [['0', $lang.AUTO_SWITCH],['1', $lang.CARD_1_ONLY],['2', $lang.CARD_2_ONLY],['3', $lang.VAR_BACKUP]], value: nvram.dualsim },
			{ title: $lang.SIM_1_SWITCHING_TIME, indent: 2,name: 'main_timeout', type: 'text', maxlen: 5, size: 7, suffix: ' <i>(' + $lang.MINUTES + ')' + $lang.RANGE + ': 10 ~ 1440</i>',value: nvram.main_timeout },
			{ title: $lang.SIM_2_SWITCHING_TIME, indent: 2,name: 'backup_timeout', type: 'text', maxlen: 5, size: 7, suffix: ' <i>(' + $lang.MINUTES + ')' + $lang.RANGE + ': 10 ~ 1440</i>',value: nvram.backup_timeout }
		]);
		htmlOut +='</div></div></div>';
	
			for (var uidx = 1; uidx < tabs.length; ++uidx) {
					t = tabs[uidx][0];
					htmlOut += '<div id=\''+ t +'-tab\'>';
					var pre = 'SIM ' + uidx + ' ';
					var suf = '';
					
					if(bi.hw == 'sd')
					{
						if(uidx != 1)
						{
							suf = '' + uidx;
						}
					}
					else
					{
						pre = '';
					}
					htmlOut += '<div class="box" data-box="sim' + uidx +'">';
					htmlOut += '<div class="content sim-' + uidx + '">';
					f = [
								{ title: pre+$lang.NET_MODE, name: 'tdd_mode'+suf, type: 'select', options: [['auto', 'Auto'],['pre-lte', $lang.LTE_PRIORITY],['pre-td', $lang.TD_SCDMA_PRIORITY],['only-lte', $lang.LTE_ONLY],['only-td', $lang.TD_SCDMA_ONLY]], value: nvram['tdd_mode'+suf]},
								{ title: pre+$lang.NET_MODE, name: 'cellType'+suf, type: 'select', options: [['0', 'Auto'],['5', '5G NR'],['1', 'LTE(FDD/TDD)'],['2', '3G(WCDMA/TD-SCDMA/HSPA)'],['3', '3G(CDMA 2000/CDMA 1x)']], value: nvram['cellType'+suf]},
								{ title: pre+$lang.NET_MODE, name: 'cell_mode'+suf, type: 'select', options: [['0', 'Auto'],['1', '3G'],['2', "2G"]], value: nvram['cell_mode'+suf] },
								{ title: pre+$lang.NETWORK_5G_STANDARD, name: 'NR_type'+suf, type: 'select', options: [['0', 'SA & NSA'],['1', 'NSA'],['2', "SA"]], value: nvram['NR_type'+suf] },
								{ title: pre+$lang.NETWORK_OPERATOR, name: 'cops_network'+suf, type: 'select', options: [['1', 'Others'],['3', 'Verizon']], value: nvram['cops_network'+suf] },
								{ title: pre+$lang.ENABLE_LOCK_BAND_FUNCTION, name: 'f_band_lock_enable' + suf, type: 'checkbox', value: (nvram['band_lock_enable' + suf] == 1)},
								{ title: pre+$lang.SA_LOCK_FREQUENCY_BAND, name: 'band_lock_num_sa' + suf, type: 'text', maxlen: 64, size: 32, value: nvram['band_lock_num_sa' + suf] ,suffix: ' <i>('+ $lang.EX + ': 1 or 1:3:5:7:9)</i>'},
								{ title: pre+$lang.NSA_LOCK_FREQUENCY_BAND, name: 'band_lock_num_nsa' + suf, type: 'text', maxlen: 64, size: 32, value: nvram['band_lock_num_nsa' + suf] ,suffix: ' <i>('+ $lang.EX + ': 1 or 1:3:5:7:9)</i>'},
								{ title: pre+$lang.LTE_LOCK_FREQUENCY_BAND, name: 'band_lock_num_lte' + suf, type: 'text', maxlen: 64, size: 32, value: nvram['band_lock_num_lte' + suf] ,suffix: ' <i>('+ $lang.EX + ': 1 or 1:3:5:7:9)</i>'},
								{ title: pre+$lang.PIN_CODE, name: 'CelldialPincode'+suf, type: 'text', maxlen: 6, size: 8, value: nvram['CelldialPincode'+suf]},
								{ title: pre+$lang.APN_ACCESS_POINT, name: 'CelldialApn'+suf, type: 'text', maxlen: 60, size: 64, value: nvram['CelldialApn'+suf] },
								{ title: pre+$lang.VAR_USER_NAME, name: 'CelldialUser'+suf, type: 'text', maxlen: 60, size: 64, value: nvram['CelldialUser'+suf]},
								{ title: pre+$lang.VAR_PASSWD, name: 'CelldialPwd'+suf, type: 'password', maxlen: 60, size: 64, peekaboo: 1, value: nvram['CelldialPwd'+suf] },
								{ title: pre+$lang.CELLDIALNUM, name: 'CelldialNum'+suf, type: 'text', maxlen: 25, size: 32, value: nvram['CelldialNum'+suf]},
								{ title: pre+$lang.AUTH_TYPE, name: 'auth_type'+suf, type: 'select', options: [['0', 'Auto'],['1', 'PAP'],['2', 'CHAP'],['3', 'MS-CHAP'],['4', 'MS-CHAPv2']],
								value: nvram['auth_type'+suf] },
								{ title: pre+$lang.LOCAL_IP, name: 'local_ip'+suf, type: 'text', maxlen: 15, size: 17, value: nvram['local_ip'+suf] }
						]; 
					htmlOut += createFormFields(f);	
					htmlOut +='</div></div></div>';
		}
		htmlOut +='</ul><div class=\'tabs-bottom\'></div>';	
		$('#sim_config').append(htmlOut);
</script>
<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button>
<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
<script type="text/javascript">earlyInit();</script>
</form>
<?PHP include 'footer.php'; ?>
