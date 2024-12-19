<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("v3_auth_type,v3_auth_passwd,snmp_enable,v3_priv_type,v3_priv_passwd,snmp_port,snmp_remote,snmp_remote_sip,snmp_sysname,snmp_location,snmp_contact,snmp_ro,snmp_rw,custom_oid1,custom_oid2,custom_oid3,custom_oid4,custom_oid5"); %>

		function verifyFields(focused, quiet)
		{
			var ok = 1;

			var a = E('_f_snmp_enable').checked;

			E('_snmp_port').disabled = !a;
			E('_f_snmp_remote').disabled = !a;
			E('_snmp_remote_sip').disabled = !a;
	E('_snmp_sysname').disabled = !a;
	E('_snmp_location').disabled = !a;
	E('_snmp_contact').disabled = !a;
	E('_snmp_ro').disabled = !a;
	E('_snmp_rw').disabled = !a;
	E('_v3_auth_type').disabled = !a;
	E('_v3_auth_passwd').disabled = !a;
	E('_v3_priv_type').disabled = !a;
	E('_v3_priv_passwd').disabled = !a;
			E('_snmp_remote_sip').disabled = (!a || !E('_f_snmp_remote').checked);

	if(!v_port('_snmp_port', quiet || !ok)) ok = 0;
	if((E('_snmp_remote_sip').value.length) && (!_v_iptaddr('_snmp_remote_sip', quiet || !ok, 15, 1, 1))) ok = 0;
	var a = ['_snmp_location','_snmp_contact', '_snmp_ro'];
	for(i=a.length-1;i>=0;--i)
	{
		if(!v_ascii(a[i],quiet || !ok))
		{
			return 0;
		}
	}
	var b = E('_v3_auth_type').value;
	if(b == "NONE")
	{
		elem.display(PR('_v3_auth_passwd'), 0);
	}
	else
	{
		elem.display(PR('_v3_auth_passwd'), 1);
	}

	var b = E('_v3_priv_type').value;
	if(b == "NONE")
	{
		elem.display(PR('_v3_priv_passwd'), 0);
	}
	else
	{
		elem.display(PR('_v3_priv_passwd'), 1);
	}
			return ok;
		}

		function save()
		{
			if (verifyFields(null, 0)==0) return;
			var fom = E('_fom');
			fom.snmp_enable.value = E('_f_snmp_enable').checked ? 1 : 0;
			fom.snmp_remote.value = E('_f_snmp_remote').checked ? 1 : 0;

			if (fom.snmp_enable.value == 0) {
				fom._service.value = 'snmp-stop';
			}
			else {
				fom._service.value = 'snmp-restart,firewall-restart';
			}
			// form.submit('_fom', 1);
			return submit_form('_fom');
		}

function init()
{
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
		<input type="hidden" name="_nextpage" value="/#admin-snmp.asp">
		<input type="hidden" name="_service" value="snmp-restart,firewall-restart">
		<input type="hidden" name="snmp_enable">
		<input type="hidden" name="snmp_remote">

		<div class="box">
			<div class="heading"><script type="text/javascript">document.write($lang.SNMP_SETTINGS)</script></div>
			<div class="content" id="config-section"></div>
			<script type="text/javascript">
				$('#config-section').forms([
	{ title: $lang.ENABLE_SNMP, indent:2, name: 'f_snmp_enable', type: 'checkbox', value: nvram.snmp_enable == '1' },
	null,
	{ title: $lang.SERVER_PORT, indent:2, name: 'snmp_port', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.snmp_port, 161) },
	{ title: $lang.REMOTE_ACCESS, indent: 2, name: 'f_snmp_remote', type: 'checkbox', value: nvram.snmp_remote == '1' },
	{ title: $lang.REMOTE_ACCESS_ALLOW_IPS, indent: 2, name: 'snmp_remote_sip', type: 'text', maxlen: 512, size: 64, value: nvram.snmp_remote_sip,
			suffix: $lang.REMOTE_ACCESS_ALLOW_IPS_TIPS },
					null,
	{ title: 'System Name', indent: 2, name: 'snmp_sysname', type: 'text', maxlen: 20, size: 25, value: nvram.snmp_sysname },
	{ title: $lang.VAR_POSITION, indent: 2, name: 'snmp_location', type: 'text', maxlen: 40, size: 64, value: nvram.snmp_location },
	{ title: $lang.CONTACT, indent: 2, name: 'snmp_contact', type: 'text', maxlen: 40, size: 64, value: nvram.snmp_contact },
	{ title: $lang.RO_COMMUNITY, indent: 2, name: 'snmp_ro', type: 'text', maxlen: 40, size: 64, value: nvram.snmp_ro },
	{ title: 'RW Community', indent: 2, name: 'snmp_rw', type: 'text', maxlen: 20, size: 25, value: nvram.snmp_rw },
	{ title: 'SNMPv3 Authentication Type', name: 'v3_auth_type', type: 'select', options: [['NONE', 'NONE'],['MD5', 'MD5'],['SHA', 'SHA']],value: nvram.v3_auth_type},
	{ title: 'SNMPv3 Authentication Password', indent: 2, name: 'v3_auth_passwd', type: 'text', maxlen: 64, size: 25, value: nvram.v3_auth_passwd },
	{ title: 'SNMPv3 Privacy Type', name: 'v3_priv_type', type: 'select', options: [['NONE', 'NONE'],['DES', 'DES'],['AES', 'AES']],value: nvram.v3_priv_type},
	{ title: 'SNMPv3 Privacy Password', indent: 2, name: 'v3_priv_passwd', type: 'text', maxlen: 64, size: 25, value: nvram.v3_priv_passwd }
					]);
			</script>
		</div>
	</form>

	<!-- <button type="button" value="Save" id="save-button" onclick="save();" class="btn btn-primary"><% translate("Save"); %> <i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %> <i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
