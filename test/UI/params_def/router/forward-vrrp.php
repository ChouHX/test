<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("vrrp_enable,vrrp_state,vrrp_vrid,vrrp_priority,vrrp_auth,vrrp_pass,vrrp_vip,vrrp_script_type,vrrp_script_ip,vrrp_script_interval,vrrp_script_weight"); %>

function fixIPs(e,quiet)
{
	var ip,ips,fix_ips='';

	if(e.value=='')
	{
		ferror.set(e,$lang.INVALID_ADDRESS,quiet);
		return 0;
	}

	ips=e.value.split(' ');
	for(i=0;i<ips.length;i++)
	{
		if(ips[i]=='') continue;
		ip=fixIP(ips[i]);
		if((!ip)&&((ip = _v_domain(e, ips[i], quiet)) == null))
		{
			ferror.set(e,$lang.INVALID_ADDRESS,quiet);
			return 0;
		}
		fix_ips=fix_ips+(i>0?' ':'')+ip;
	}
	e.value=fix_ips;

	return 1;
}

function verifyFields(focused, quiet)
{
	var c,v,ok=1;

	c = E('_f_vrrp_auth').checked;
	elem.display(PR('_vrrp_pass'), c);

	c = E('_vrrp_script_type').value;
	elem.display(PR('_vrrp_script_ip'), c == 1);

	if((c == 1) &&(!fixIPs(E('_vrrp_script_ip'),quiet)))
		return 0;

	c = [['_vrrp_priority', 0, 255], ['_vrrp_vrid', 0, 255], ['_vrrp_script_interval', 0, 86400]];
	for (i = c.length - 1; i >= 0; --i)
	{
		v = c[i];
		if ((!v_range(v[0], quiet || !ok, v[1], v[2]))) ok=0;
	}

	return ok;
}

function save()
{
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');
	
	fom.vrrp_enable.value = E('_f_vrrp_enable').checked ? 1 : 0;
	fom.vrrp_auth.value = E('_f_vrrp_auth').checked ? 1 : 0;
	// form.submit(fom, 0);
	return submit_form('_fom');
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading">VRRP</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#forward-vrrp.asp'>
                <input type='hidden' name='_service' value='vrrp-restart'>
                <input type='hidden' name='vrrp_enable'>
                <input type='hidden' name='vrrp_auth'>
				<div id="vrrpconfig"></div>
			</form>

			<script type='text/javascript'>

				$('#vrrpconfig').forms([
				{ title: $lang.ENABLE_VRRP, name: 'f_vrrp_enable', type: 'checkbox', value: (nvram.vrrp_enable == 1) },
	{ title: $lang.NET_MODE, name: 'vrrp_state', type: 'select', options: [['1', $lang.HOST],['0',$lang.PREPARE]],value: nvram.vrrp_state },
	{ title: $lang.VIRTUAL_IP_ADDRESS, name: 'vrrp_vip', type: 'text', maxlen: 15, size: 16, value: nvram.vrrp_vip },
	{ title: $lang.VIRTUAL_ROUTER_ID, name: 'vrrp_vrid', type: 'text',maxlen: 6, size: 8 ,value: nvram.vrrp_vrid },
	{ title: $lang.PRIORITY, name: 'vrrp_priority', type: 'text',maxlen: 6, size: 8 ,value: nvram.vrrp_priority },
	{ title: $lang.CERTIFICATION, name: 'f_vrrp_auth', type: 'checkbox', value: (nvram.vrrp_auth == 1) },
	{ title: $lang.PPTP_CLIENT_PASSWD, indent: 2, name: 'vrrp_pass', type: 'text', maxlen: 15, size: 17, value: nvram.vrrp_pass },
	{ title: $lang.SCRIPT_TYPE, name: 'vrrp_script_type', type: 'select', options: [['0', 'Default'],['1', 'ICMP']],value: nvram.vrrp_script_type },
	{ title: $lang.VAR_IP, indent: 2,name: 'vrrp_script_ip', type: 'text', maxlen: 512, size: 32, value: nvram.vrrp_script_ip,
		  suffix: '<br><small>('+ $lang.EXAMPLE_A_B_C_D +')</small>' },
	{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'vrrp_script_interval', type: 'text', maxlen: 6, size: 8, value: nvram.vrrp_script_interval },
	{ title: $lang.WEIGHTS, indent: 2, name: 'vrrp_script_weight', type: 'text', maxlen: 6, size: 8, value: nvram.vrrp_script_weight }
					], { align: 'left' });
			</script>
            
			</div>
			</div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
