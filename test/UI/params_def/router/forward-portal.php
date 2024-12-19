<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("xdog_on,xdog_auth,xdog_root,xdog_iglan,xdog_whost,xdog_phost,xdog_redir,xdog_login_timeout,xdog_idle_timeout,xdog_trustmac,xdog_qos_don,xdog_qos_dt,xdog_qos_ds,xdog_qos_dsc,xdog_qos_uon,xdog_qos_ut,xdog_qos_us,xdog_qos_usc"); %>

function fixMACs(e,quiet)
{
	var mac,macs,fix_macs='';

	if(e.value=='') return 1;

	macs=e.value.split(' ');
	for(i=0;i<macs.length;i++)
	{
		if(macs[i]=='') continue;
		mac=fixMAC(macs[i]);
		if((!mac)||(isMAC0(mac)))
		{
			ferror.set(e,$lang.INVALID_MAC_ADDRESS,quiet);
			return 0;
		}
		fix_macs=fix_macs+(i>0?' ':'')+mac;
	}
	e.value=fix_macs;
	return 1;
}

function checkRateCeil(rate, ceil)
{
	var r = parseInt(rate, 10);
	var c = parseInt(ceil, 10);
	if( r > c )
	{
		return true;
	}
	return false;
}

function verifyFields(focused, quiet)
{
	var c,v,i;

	if(!fixMACs(E('_f_xdog_trustmac'),quiet))
		return 0;

	c = !E('_f_xdog_qos_don').checked;
	elem.display(PR('_xdog_qos_dt'), !c);
	elem.display(PR('_xdog_qos_ds'), !c);
	elem.display(PR('_xdog_qos_dsc'), !c);

	c = !E('_f_xdog_qos_uon').checked;
	elem.display(PR('_xdog_qos_ut'), !c);
	elem.display(PR('_xdog_qos_us'), !c);
	elem.display(PR('_xdog_qos_usc'), !c);

	c = [['_xdog_login_timeout', 0, 1440],['_xdog_idle_timeout', 0, 1440],['_xdog_qos_dt', 1, 999999],['_xdog_qos_ds', 1, 999999],['_xdog_qos_dsc', 1, 999999],['_xdog_qos_ut', 1, 999999],['_xdog_qos_us', 1, 999999],['_xdog_qos_usc', 1, 999999]];
	for(i=c.length-1;i>=0;--i)
	{
		v = c[i];
		if((!v_range(v[0], quiet, v[1], v[2]))) return 0;
	}

	if(!v_ascii('_xdog_redir',quiet)) return 0;
	if(!v_domain('_xdog_whost', quiet)) return 0;
	if(!v_domain('_xdog_phost', quiet)) return 0;

	if(checkRateCeil(E('_xdog_qos_dsc').value,E('_xdog_qos_dt').value))
	{
		ferror.set(E('_xdog_qos_dt'), $lang.XDOG_QOS_DT + $lang.MUST_BE_GREATER_THAN + $lang.XDOG_QOS_DSC, quiet);
		return 0;
	}
	if(checkRateCeil(E('_xdog_qos_ds').value,E('_xdog_qos_dsc').value))
	{
		ferror.set(E('_xdog_qos_dsc'), $lang.XDOG_QOS_DSC+ $lang.MUST_BE_GREATER_THAN + $lang.XDOG_QOS_DSC, quiet);
		return 0;
	}
	if(checkRateCeil(E('_xdog_qos_usc').value,E('_xdog_qos_ut').value))
	{
		ferror.set(E('_xdog_qos_ut'), $lang.TOTAL_UPLOAD_BANDWIDTH + $lang.MUST_BE_GREATER_THAN + $lang.XDOG_QOS_DSC, quiet);
		return 0;
	}
	if(checkRateCeil(E('_xdog_qos_us').value,E('_xdog_qos_usc').value))
	{
		ferror.set(E('_xdog_qos_usc'), $lang.XDOG_QOS_DSC + $lang.MUST_BE_GREATER_THAN + $lang.XDOG_QOS_DSC, quiet);
		return 0;
	}
	return 1;
}

function save()
{
	var fom;

	if (!verifyFields(null, false)) return;

	fom = E('_fom');
	fom.xdog_trustmac.value = fom._f_xdog_trustmac.value.split(/\s*,\s*/).join(',');
	fom.xdog_on.value = E('_f_xdog_on').checked ? 1 : 0;
	fom.xdog_iglan.value = E('_f_xdog_iglan').checked ? 1 : 0;
	fom.xdog_qos_don.value = E('_f_xdog_qos_don').checked ? 1 : 0;
	fom.xdog_qos_uon.value = E('_f_xdog_qos_uon').checked ? 1 : 0;
	if(nvram.xdog_on != fom.xdog_on.value || nvram.xdog_qos_don != fom.xdog_qos_don.value || nvram.xdog_qos_uon != fom.xdog_qos_uon.value)
	{
		fom._service.disabled = 1;
		fom._reboot.value = '1';
		// form.submit(fom);
		return submit_form('_fom');
	}
	else
	{
		// form.submit(fom, 1);
		return submit_form('_fom');
	}
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.PORTAL)</script></div>
		<div class="content">
			<form id="_fom" method="post" action="tomato.cgi">
			<input type='hidden' name='_nextpage' value='/#forward-portal.asp'>
			<input type='hidden' name='_reboot' value='0'>
            <input type='hidden' name='_service' value='xdog-restart'>
            <input type='hidden' name='xdog_on'>
<input type='hidden' name='xdog_trustmac'>
            <input type='hidden' name='xdog_iglan'>
            <input type='hidden' name='xdog_qos_don'>
            <input type='hidden' name='xdog_qos_uon'>
				<div id="cat-configure"></div><hr>
				<script type="text/javascript">
	$('#cat-configure').forms([
	{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, name: 'f_xdog_on', type: 'checkbox', value: nvram.xdog_on == '1' },
	{ title: $lang.AUTH_TYPE, name: 'xdog_auth', type: 'select', options: [['0', 'NONE']], value: nvram.xdog_auth},
	{ title: $lang.ROUTER_WEB_DIR, name: 'xdog_root', type: 'select', options: [['0', $lang.VAR_DEFAULT],['1', $lang.INTERNAL_STORAGE],['2', $lang.EXTERNAL_STORAGE]], value: nvram.xdog_root},
	{ title: $lang.XDOG_WHOST, name: 'xdog_whost', type: 'text', maxlen: 255, size: 32, value: nvram.xdog_whost},
	{ title: $lang.XDOG_PHOST, name: 'xdog_phost', type: 'text', maxlen: 255, size: 32, value: nvram.xdog_phost},
	{ title: $lang.XDOG_LOGIN_TIMEOUT, name:'xdog_login_timeout',type:'text',maxlen:6,size:8,suffix:'<small>'+ $lang.MINUTES +'</small>', value:nvram.xdog_login_timeout},
	{ title: $lang.CLIENTIDLETIMEOUT_2, name:'xdog_idle_timeout',type:'text',maxlen:6,size:8,suffix:'<small>'+ $lang.MINUTES +'</small>', value:nvram.xdog_idle_timeout},
	{ title: $lang.PORTAL_IGNORE_LAN, name: 'f_xdog_iglan', type: 'checkbox', value: nvram.xdog_iglan == '1' },
	{ title: $lang.REDIRECT, name: 'xdog_redir', type: 'text', maxlen: 255, size: 32, value: nvram.xdog_redir},
	{ title: $lang.TRUSTEDMACLIST_2, name: 'f_xdog_trustmac', type: 'text', maxlen: 255, size: 32, value: nvram.xdog_trustmac},
	{ title: $lang.XDOG_QOS_DON, name: 'f_xdog_qos_don', type: 'checkbox', value: (nvram.xdog_qos_don == 1) },
	{ title: $lang.XDOG_QOS_DT,indent:2,name:'xdog_qos_dt',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_dt},
	{ title: $lang.XDOG_QOS_DS,indent:2,name:'xdog_qos_ds',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_ds},
	{ title: $lang.XDOG_QOS_DSC,indent:2,name:'xdog_qos_dsc',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_dsc},
	{ title: $lang.F_XDOG_QOS_UON, name: 'f_xdog_qos_uon', type: 'checkbox', value: (nvram.xdog_qos_uon == 1) },
	{ title: $lang.TOTAL_UPLOAD_BANDWIDTH,indent:2,name:'xdog_qos_ut',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_ut},
	{ title: $lang.XDOG_QOS_DS,indent:2,name:'xdog_qos_us',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_us},
	{ title: $lang.XDOG_QOS_DSC,indent:2,name:'xdog_qos_usc',type:'text',maxlen:6,size:8,suffix:'<small>kbit/s</small>', value:nvram.xdog_qos_usc}
	]);
</script>
			</form>
		</div>
	</div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
