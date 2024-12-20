<?PHP include 'header.php'; ?>
	<script type="text/javascript" src="js/interfaces.js"></script>
	<script type="text/javascript">

//	<% nvram("block_wan,http_enable,web_lang,https_enable,http_lanport,https_lanport,remote_management,remote_mgt_https,web_wl_filter,icmp_keepalive,web_css,ttb_css,sshd_eas,sshd_pass,sshd_remote,telnetd_eas,telnetd_remote,http_wanport,sshd_authkeys,sshd_port,sshd_rport,sshd_forwarding,telnetd_port,rmgt_sip,https_crt_cn,https_crt_save,lan_ipaddr,ne_shlimit,http_web_dir"); %>
if(!nvram.ne_shlimit){
	nvram.ne_shlimit = '';
}

changed = 0;
tdup = parseInt('<% psup("telnetd"); %>');
sdup = parseInt('<% psup("dropbear"); %>');

shlimit = nvram.ne_shlimit.split(',');
if (shlimit.length != 3) shlimit = [0,3,60];
var xmenus = [[$lang.DEVICE_STATUS, 'status'], [$lang.BASIC_NETWORK, 'basic'],
/* WIFI-BEGIN */
[$lang.WLAN_SETTING, 'wlan'],
/* WIFI-END */
[$lang.FIREWALL, 'firewall'],[$lang.VPN_MODE, 'vpn'],[$lang.ADVANCED_NETWORK, 'forward'], [$lang.VAR_MENU_SYSCFG, 'admin'],
[$lang.DEBUGGING, 'tools']];

function toggle(service, isup)
{
	if (changed) {
		if (!confirm($lang.NO_SAVE_CFG_TIP + "?")) return;
	}
	E('_' + service + '_button').disabled = true;
	form.submitHidden('service.cgi', {
		_redirect: '/#admin-access.asp',
		_sleep: ((service == 'sshd') && (!isup)) ? '7' : '3',
		_service: service + (isup ? '-stop' : '-start')
	});
}

function verifyFields(focused, quiet)
{
	var ok = 1;
	var a, b, c;
	var i;

	a = E('_f_http_local');
	b = E('_f_http_remote').value;
	if ((a.value != 3) && (b != 0) && (a.value != b)) {
		ferror.set(a, $lang.WARNING_ADMIN_ACCESS1, quiet || !ok);
		ok = 0;
	}
	else {
		ferror.clear(a);
	}

	elem.display(PR('_http_lanport'), (a.value == 1) || (a.value == 3));

	c = (a.value == 2) || (a.value == 3);
	elem.display(PR('_https_lanport'), c);

	if ((!v_port('_http_lanport', quiet || !ok)) || (!v_port('_https_lanport', quiet || !ok))) ok = 0;

	b = b != 0;
	a = E('_f_rmgt_sip');
	elem.display(PR(a), b);
	a = E('_http_wanport');
	elem.display(PR(a), b);
	if ((b) && (!v_port(a, quiet || !ok))) ok = 0;

	if (!v_port('_telnetd_port', quiet || !ok)) ok = 0;

	a = E('_f_sshd_remote').checked;
	b = E('_sshd_rport');
	elem.display(PR(b), a);
	if ((a) && (!v_port(b, quiet || !ok))) ok = 0;

	a = E('_sshd_authkeys');
	if (!v_length(a, quiet || !ok, 0, 4096)) {
		ok = 0;
	}
	else if (a.value != '') {
        if (a.value.search(/^\s*ssh-(dss|rsa)/) == -1) {
			ferror.set(a, $lang.INVALID_SSH_KEY, quiet || !ok);
			ok = 0;
		}
	}
//	E('_web_lang').disabled = 1;
	a = E('_f_rmgt_sip');
	if ((a.value.length) && (!_v_iptaddr(a, quiet || !ok, 15, 1, 1))) return 0;
	ferror.clear(a);

	if (!v_range('_f_limit_hit', quiet || !ok, 1, 100)) return 0;
	if (!v_range('_f_limit_sec', quiet || !ok, 3, 3600)) return 0;

	if(!v_ascii('_set_password_1',quiet || !ok)) return 0;
	if(!v_ascii('_set_password_2',quiet || !ok)) return 0;
	if(!v_ascii('_set_upassword_1',quiet || !ok)) return 0;
	if(!v_ascii('_set_upassword_2',quiet || !ok)) return 0;

	a = E('_set_password_1');
	b = E('_set_password_2');
	a.value = a.value.trim();
	b.value = b.value.trim();
	if (a.value != b.value) {
		ferror.set(b, $lang.BOTH_PASSWORDS_MUST_MATCH, quiet || !ok);
		ok = 0;
	}
	else if (a.value == '') {
		ferror.set(a, $lang.PASSWORD_MUST_NOT_BE_EMPTY, quiet || !ok);
		ok = 0;
	}
	else {
		ferror.clear(a);
		ferror.clear(b);
	}

	a = E('_set_upassword_1');
	b = E('_set_upassword_2');
	a.value = a.value.trim();
	b.value = b.value.trim();
	if (a.value != b.value) {
		ferror.set(b, $lang.BOTH_PASSWORDS_MUST_MATCH, quiet || !ok);
		ok = 0;
	}
	else if (a.value == '') {
		ferror.set(a, $lang.PASSWORD_MUST_NOT_BE_EMPTY, quiet || !ok);
		ok = 0;
	}
	else {
		ferror.clear(a);
		ferror.clear(b);
	}
	changed |= ok;
	return ok;
}

function save()
{
	var a, b, fom;

	if (!verifyFields(null, false)) return;

	fom = E('_fom');
	a = E('_f_http_local').value * 1;
	if (a == 0) {
		if (!confirm($lang.WARNING_ADMIN_ACCESS)) return;
		fom._nextpage.value = 'about:blank';
	}
	fom.http_enable.value = (a & 1) ? 1 : 0;
	fom.https_enable.value = (a & 2) ? 1 : 0;
	
	nvram.lan_ipaddr = location.hostname;
	if ((a != 0) && (location.hostname == nvram.lan_ipaddr)) {
		if (location.protocol == 'https:') {
			b = 's';
			if ((a & 2) == 0) b = '';
		}
		else {
			b = '';
			if ((a & 1) == 0) b = 's';
		}

		a = 'http' + b + '://' + location.hostname;
		if (b == 's') {
			if (fom.https_lanport.value != 443) a += ':' + fom.https_lanport.value;
		}
		else {
			if (fom.http_lanport.value != 80) a += ':' + fom.http_lanport.value;
		}
		fom._nextpage.value = a + '/#admin-access.asp';
	}

	a = E('_f_http_remote').value;
	fom.remote_management.value = (a != 0) ? 1 : 0;
	fom.remote_mgt_https.value = (a == 2) ? 1 : 0;
/*
	if ((a != 0) && (location.hostname != nvram.lan_ipaddr)) {
		if (location.protocol == 'https:') {
			if (a != 2) fom._nextpage.value = 'http://' + location.hostname + ':' + fom.http_wanport.value + '/admin-access.asp';
		}
		else {
			if (a == 2) fom._nextpage.value = 'https://' + location.hostname + ':' + fom.http_wanport.value + '/admin-access.asp';
		}
	}
*/

	fom.web_wl_filter.value = E('_f_http_wireless').checked ? 0 : 1;

	fom.telnetd_eas.value = E('_f_telnetd_eas').checked ? 1 : 0;

	fom.block_wan.value = E('_f_block_wan').checked ? 1 : 0;
	fom.sshd_eas.value = E('_f_sshd_eas').checked ? 1 : 0;
	fom.telnetd_remote.value = E('_f_telnetd_remote').checked ? 1 : 0;
	fom.sshd_pass.value = E('_f_sshd_pass').checked ? 1 : 0;
	fom.sshd_remote.value = E('_f_sshd_remote').checked ? 1 : 0;

	fom.sshd_forwarding.value = E('_f_sshd_forwarding').checked ? 1 : 0;

	fom.rmgt_sip.value = fom.f_rmgt_sip.value.split(/\s*,\s*/).join(',');
	
	fom.ne_shlimit.value = ((E('_f_limit_ssh').checked ? 1 : 0) | (E('_f_limit_telnet').checked ? 2 : 0)) +
		',' + E('_f_limit_hit').value + ',' + E('_f_limit_sec').value;

	/*a = [];
	for (var i = 0; i < xmenus.length; ++i) {
		b = xmenus[i][1];
		if (E('_f_mx_' + b).checked) a.push(b);
	}
	fom.web_mx.value = a.join(',');*/
	if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
	{
		// form.submit(fom, 0);
		return submit_form('_fom');
	}
	else
	{
		return;
	}

}

function init()
{
	changed = 0;
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">

		
        <input type='hidden' name='_nextpage' value='/#admin-access.asp'>
        <input type='hidden' name='_nextwait' value='5'>
        <input type='hidden' name='_service' value='admin-restart'>
<input type='hidden' name='_service' value='zebra-restart'>
<input type='hidden' name='_reboot' value='1'>
        
        <input type='hidden' name='http_enable'>
        <input type='hidden' name='https_enable'>
        <input type='hidden' name='remote_management'>
        <input type='hidden' name='remote_mgt_https'>
        <input type='hidden' name='web_wl_filter'>
        <input type='hidden' name='telnetd_eas'>
        <input type='hidden' name='telnetd_remote'>
<input type='hidden' name='block_wan'>
        <input type='hidden' name='sshd_eas'>
        <input type='hidden' name='sshd_pass'>
        <input type='hidden' name='sshd_remote'>
        <input type='hidden' name='ne_shlimit'>
        <input type='hidden' name='rmgt_sip'>
        <input type='hidden' name='sshd_forwarding'>
<input type='hidden' name='web_mx'>

		<div class="box" data-box="admin-access">
			<div class="heading">Web <script type="text/javascript">document.write($lang.ACCESS_SETTINGS)</script></div>
			<div class="content" id="section-gui">

				<script type="text/javascript">
				var m = [
					{ title: $lang.LOCAL_ACCESS, name: 'f_http_local', type: 'select', options: [[0, $lang.VAR_CLOSE],[1,'HTTP'],[2,'HTTPS'],[3,'HTTP &amp; HTTPS']],
					value: ((nvram.https_enable != 0) ? 2 : 0) | ((nvram.http_enable != 0) ? 1 : 0) },
					{ title: 'HTTP ' + $lang.ACCESS_PORT, indent: 2, name: 'http_lanport', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.http_lanport, 80) },
					{ title: 'HTTPS ' + $lang.ACCESS_PORT, indent: 2, name: 'https_lanport', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.https_lanport, 443) },
					{ title: $lang.REMOTE_ACCESS, name: 'f_http_remote', type: 'select', options: [[0, $lang.VAR_CLOSE],[1,'HTTP'],[2,'HTTPS']],
					value:  (nvram.remote_management == 1) ? ((nvram.remote_mgt_https == 1) ? 2 : 1) : 0 },
					{ title: $lang.ACCESS_PORT, indent: 2, name: 'http_wanport', type: 'text', maxlen: 5, size: 7, value:  fixPort(nvram.http_wanport, 8080) },
					{ title: $lang.REMOTE_ACCESS_ALLOW_IPS, name: 'f_rmgt_sip', type: 'text', maxlen: 512, size: 64, value: nvram.rmgt_sip,
					suffix: $lang.REMOTE_ACCESS_ALLOW_IPS_TIPS },
					{ title: $lang.ALLOW_WIRELESS_ACCESS, name: 'f_http_wireless', type: 'checkbox', value:  nvram.web_wl_filter == 0 },
					{ title: $lang.WAN_PORT_BAN_PING, name: 'f_block_wan', type: 'checkbox', value: nvram.block_wan == 1 },
					{ title: $lang.SSH_BOOT_UP, name: 'f_sshd_eas', type: 'checkbox', value: nvram.sshd_eas == 1 },
					{ title: $lang.ENABLE_TELNET_REMOTE_ACCESS, name: 'f_telnetd_remote', type: 'checkbox', value:  nvram.telnetd_remote == 1 },
					null,
					//	{ title: '<% translate("Open Menus"); %>' }
					];
					
					/*var webmx = get_config('web_mx', '').toLowerCase();
					for (var i = 0; i < xmenus.length; ++i) {
						m.push({ title: xmenus[i][0], indent: 2, name: 'f_mx_' + xmenus[i][1],
							type: 'checkbox', value: (webmx.indexOf(xmenus[i][1]) != -1) });
					}*/

					$('#section-gui').forms(m);
				</script>
			</div>
		</div>
        
		<div class="box" id="section-ssh" data-box="access-ssh"  style='display:none'>
			<div class="heading">SSH<script type="text/javascript">document.write($lang.ACCESS_SETTINGS)</script><span class="ssh-status"></span></div>
			<div class="content">
				<script type="text/javascript">
					$('#section-ssh .content').forms([
					{ title: $lang.ACCESS_PORT, name: 'f_sshd_remote', type: 'checkbox', value: nvram.sshd_remote == 1 },
					{ title: $lang.REMOTE_PORT, indent: 2, name: 'sshd_rport', type: 'text', maxlen: 5, size: 7, value: nvram.sshd_rport },
					{ title: $lang.REMOTE_FORWARDING, name: 'f_sshd_forwarding', type: 'checkbox', value: nvram.sshd_forwarding == 1 },
					{ title: $lang.ACCESS_PORT, name: 'sshd_port', type: 'text', maxlen: 5, size: 7, value: nvram.sshd_port },
					{ title: $lang.ALLOW_PASSWORD_LOGIN, name: 'f_sshd_pass', type: 'checkbox', value: nvram.sshd_pass == 1 },
					{ title: $lang.AUTHORIZED_KEYS, name: 'sshd_authkeys', type: 'textarea', value: nvram.sshd_authkeys }
					]);
					$('#section-ssh .heading').append('<a href="#" data-toggle="tooltip" class="pull-right" title="' + (sdup ? 'Stop' : 'Start') + ' SSH Daemon" onclick="toggle(\'sshd\', sdup)" id="_sshd_button">'
						+ (sdup ? '<i class="icon-stop"></i>' : '<i class="icon-play"></i>') + '</a>');
					$('.ssh-status').html((sdup ? '<small style="color: green;">(Running)</small>' : '<small style="color: red;">(Stopped)</small>'));
				</script>
			</div>
		</div>

		<div class="box" id="section-telnet" data-box="access-telnet"  style='display:none'>
			<div class="heading">Telnet<script type="text/javascript">document.write($lang.ACCESS_SETTINGS)</script><span class="telnet-status"></span></div>
			<div class="content">
				<script type="text/javascript">
					$('#section-telnet .content').forms([
					{ title: $lang.ENABLE_AT_STARTUP, name: 'f_telnetd_eas', type: 'checkbox', value: nvram.telnetd_eas == 1 },
					{ title: $lang.ACCESS_PORT, name: 'telnetd_port', type: 'text', maxlen: 5, size: 7, value: nvram.telnetd_port }
					]);
					$('#section-telnet .heading').append('<a href="#" data-toggle="tooltip" class="pull-right" title="' + (tdup ? 'Stop' : 'Start') + ' Telnet Daemon" onclick="toggle(\'telnetd\', tdup)" id="_telnetd_button">'
						+ (tdup ? '<i class="icon-stop"></i>' : '<i class="icon-play"></i>') + '</a>');
					$('.telnet-status').html((tdup ? '<small style="color: green;">(Running)</small>' : '<small style="color: red;">(Stopped)</small>'));
				</script>
			</div>
		</div>

		<div class="box" id="section-restrict" data-box="access-restrict"  style='display:none'>
			<div class="heading"><script type="text/javascript">document.write($lang.ADMIN_RESTRICTIONS)</script></div>
			<div class="content">
				<script type="text/javascript">
					$('#section-restrict .content').forms([
						{ title: $lang.LIMIT_CONNECTION_ATTEMPTS, multi: [
							{ suffix: '&nbsp; SSH &nbsp; / &nbsp;', name: 'f_limit_ssh', type: 'checkbox', value: (shlimit[0] & 1) != 0 },
							{ suffix: '&nbsp; Telnet &nbsp;', name: 'f_limit_telnet', type: 'checkbox', value: (shlimit[0] & 2) != 0 }
							] },
							{ title: '', indent: 2, multi: [
							{ name: 'f_limit_hit', type: 'text', maxlen: 4, size: 6, suffix: '&nbsp; '+ $lang.EACH +'&nbsp;', value: shlimit[1] },
							{ name: 'f_limit_sec', type: 'text', maxlen: 4, size: 6, suffix: '&nbsp; '+ $lang.VAR_SECOND, value: shlimit[2] }
						] }
					]);
				</script>
			</div>
		</div>
		<div class="box" data-box="admin-weblogin">
			<div class="heading"><script type="text/javascript">document.write($lang.PASSWORD_SETTING)</script></div>
			<div class="content" id="section-weblogin">
				<script type="text/javascript">
					$('#section-weblogin').forms([
						{ title: $lang.PLEASE_ENTER_YOUR_PASSWORD_ADMIN, name: 'set_password_1', type: 'password', value: '**********' },
						{ title: $lang.ENTER_PASSWORD_AGAIN, indent: 2, name: 'set_password_2', type: 'password', value: '**********' },
						{ title: $lang.PLEASE_ENTER_YOUR_PASSWORD_USER, name: 'set_upassword_1', type: 'password', value: '**********' },
						{ title: $lang.ENTER_PASSWORD_AGAIN, indent: 2, name: 'set_upassword_2', type: 'password', value: '**********' }
					]);
				</script>
			</div>
		</div>
        
        
        	<!--	<div class="box" data-box="admin-weblogin">
			<div class="heading"><% translate("Language");%></div>
			<div class="content" id="section-language">
				<script type="text/javascript">
					$('#section-language').forms([
						{title: '<% translate("System Language"); %>', name: 'web_lang', type: 'select', options: [['zh_CN','简体中文'],['zh_TW','繁體中文'],['en_EN','English']],value: nvram.web_lang}
					]);
				</script>
			</div>
		</div>
        -->
        
        
        
   
		<!-- <button type="button" value="Save" id="save-button" onclick="save();" class="btn btn-primary"><% translate("Save"); %> <i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %> <i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

	<script type="text/javascript">init(); verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
