<!--
-->
<title><%translate("VPN Tunnel");%>:<%translate("SSH-VPN Client");%></title>

<content>
	<script type="text/javascript">
//	<% nvram("sshvpn_mode,tun_if_num,tun_svr_if_num,sshvpn_rsa,sshvpn_rsa_pub,ssh_srv_host,sshvpn_vt_ip_local,sshvpn_vt_ip_rmt,ssh_as_def_rt,ssh_as_firewall_rule"); %>

function verifyFields(focused, quiet)
{
    var ret = 1;

	if (!v_range('_tun_if_num', quiet, 0, 20)) ret = 0;
	if (!v_range('_tun_svr_if_num', quiet, 0, 20)) ret = 0;
	
	if (!v_ip('_ssh_srv_host', true) && !v_domain('_ssh_srv_host', true)) 
	{ 
		ferror.set(E('_ssh_srv_host'), "<% translate("Invalid server address"); %>.", quiet); ret = 0; 
	}
	
	if (!v_ip('_sshvpn_vt_ip_local', true)) 
	{ 
		ferror.set(E('_sshvpn_vt_ip_local'), "<% translate("Invalid virtual local address"); %>.", quiet); ret = 0; 
	}
	
	if (!v_ip('_sshvpn_vt_ip_rmt', true)) 
	{ 
		ferror.set(E('_sshvpn_vt_ip_rmt'), "<% translate("Invalid virtual remote address"); %>.", quiet); ret = 0; 
	}
	
	return ret;
}

function save()
{
	//if (!verifyFields(null, false)) return;

	var fom = E('_fom');
	E('sshvpn_mode').value = E('_f_sshvpn_mode').checked ? 1 : 0;
	E('ssh_as_def_rt').value = E('_f_ssh_as_def_rt').checked ? 1 : 0;
	E('ssh_as_firewall_rule').value = E('_f_ssh_as_firewall_rule').checked ? 1 : 0;
	
	form.submit(fom, 1);

	changed = 0;
}
	</script>
<style type='text/css'>
textarea {
	width: 98%;
	height: 10em;
}
</style>
	<div class="box">
		<div class="heading"><% translate("SSH VPN Client"); %></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#vpn-ssh.asp'>
				<input type='hidden' name='_service' value='sshvpnclient-restart'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' id='sshvpn_mode' name='sshvpn_mode'>
				<input type='hidden' id='ssh_as_def_rt' name='ssh_as_def_rt'>
				<input type='hidden' id='ssh_as_firewall_rule' name='ssh_as_firewall_rule'>
				<div id="vpnssh"></div>
			</form>
		</div>
	</div>
			<script type='text/javascript'>

				$('#vpnssh').forms([
	{ title: '<%translate("Enable SSHVPN");%>', name: 'f_sshvpn_mode', type: 'checkbox', value: nvram.sshvpn_mode != 0 },
    { title: '<%translate("SSH TUNNEL Interface Num");%>', name: 'tun_if_num', type: 'text', maxlen: 4, size: 4,  value: nvram.tun_if_num },
	 { title: '<%translate("SSH TUNNEL Server Interface Num");%>', name: 'tun_svr_if_num', type: 'text', maxlen: 4, size: 4,  value: nvram.tun_svr_if_num },
	{ title: '<%translate("SSH VPN Host");%>', name: 'ssh_srv_host', type: 'text', maxlen: 64, size: 64,value: nvram.ssh_srv_host },
	{ title: '<%translate("SSH VPN Virtual Local Address");%>', name: 'sshvpn_vt_ip_local', type: 'text', maxlen: 20, size: 20,value: nvram.sshvpn_vt_ip_local },
	{ title: '<%translate("SSH VPN Virtual Remote Address");%>', name: 'sshvpn_vt_ip_rmt', type: 'text', maxlen: 20, size: 20,value: nvram.sshvpn_vt_ip_rmt },
	{ title: '<%translate("As Default Route");%>', name: 'f_ssh_as_def_rt', type: 'checkbox', value: nvram.ssh_as_def_rt != 0 },
	{ title: '<%translate("As Firewall rule");%>', name: 'f_ssh_as_firewall_rule', type: 'checkbox', value: nvram.ssh_as_firewall_rule != 0 },
	{ title: '<%translate("SSH Private Key");%>: ', name: 'sshvpn_rsa', type: 'textarea', value: nvram.sshvpn_rsa },
    { title: '<%translate("SSH Public Key");%>: ', name: 'sshvpn_rsa_pub', type: 'textarea', value: nvram.sshvpn_rsa_pub }
], { align: 'left' });
			</script>
            
	<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button>
	<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button>
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
