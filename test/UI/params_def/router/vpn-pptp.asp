<!--
--><title>[<% ident(); %>] <%translate("VPN");%>:<%translate("PPTP/L2TP Client");%></title>
<content>
	<script type="text/javascript">


//	<% nvram("vpn_mode,pptp_client_enable,pptp_client_peerdns,pptp_client_mtuenable,pptp_client_mtu,pptp_client_mruenable,pptp_client_mru,pptp_client_lip,pptp_client_firewall,pptp_client_nat,pptp_client_srvip,pptp_client_srvsub,pptp_client_srvsubmsk,pptp_client_username,pptp_client_passwd,pptp_client_mppeopt,pptp_client_crypt,pptp_client_custom,pptp_client_dfltroute,pptp_client_stateless,tunnel_auth,wan_hostname,tunnel_passwd"); %>

pptpup = parseInt('<% psup("pptpclient"); %>');

var changed = 0;

function toggle(service, isup)
{
	if (changed) {
		if (!confirm("<% translate("Unsaved changes will be lost. Continue anyway"); %>?")) return;
	}
	E('_' + service + '_button').disabled = true;
	form.submitHidden('/service.cgi', {
		_redirect: 'vpn-pptp.asp',
		_service: service + (isup ? '-stop' : '-start')
	});
}

function verifyFields(focused, quiet)
{
    var ret = 1;

	elem.display(PR('_pptp_client_crypt'), PR('_f_pptp_client_stateless'), E('_vpn_mode').value == 'pptp' );
	elem.display(PR('_f_tunnel_auth'), PR('_tunnel_passwd'), E('_vpn_mode').value == 'l2tp' );
	elem.display(PR('_tunnel_passwd'), E('_f_tunnel_auth').checked);
	elem.display(PR('_pptp_client_srvsub'), PR('_pptp_client_srvsubmsk'), !E('_f_pptp_client_dfltroute').checked);

	var f = E('_pptp_client_mtuenable').value == '0';
	if (f) {
		E('_pptp_client_mtu').value = '1450';
	}
	E('_pptp_client_mtu').disabled = f;
	f = E('_pptp_client_mruenable').value == '0';
	if (f) {
		E('_pptp_client_mru').value = '1450';
	}
	E('_pptp_client_mru').disabled = f;

	if (!v_range('_pptp_client_mtu', quiet, 576, 1500)) ret = 0;
	if (!v_range('_pptp_client_mru', quiet, 576, 1500)) ret = 0;
	//if (!v_ip('_pptp_client_srvip', true) && !v_domain('_pptp_client_srvip', true)) { ferror.set(E('_pptp_client_srvip'), "Invalid server address.", quiet); ret = 0; }
	if (!E('_f_pptp_client_dfltroute').checked && !v_ip('_pptp_client_srvsub', true)) { ferror.set(E('_pptp_client_srvsub'), "<% translate("Invalid subnet address"); %>.", quiet); ret = 0; }
	if (!E('_f_pptp_client_dfltroute').checked && !v_ip('_pptp_client_srvsubmsk', true)) { ferror.set(E('_pptp_client_srvsubmsk'), "<% translate("Invalid netmask address"); %>.", quiet); ret = 0; }
	
    changed |= ret;
	return ret;
}


function save()
{
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');

    E('pptp_client_enable').value = E('_f_pptp_client_enable').checked ? 1 : 0;
    E('pptp_client_nat').value = E('_f_pptp_client_nat').checked ? 1 : 0;
    E('pptp_client_firewall').value = E('_f_pptp_client_firewall').checked ? 1 : 0;
    E('pptp_client_dfltroute').value = E('_f_pptp_client_dfltroute').checked ? 1 : 0;
    E('pptp_client_stateless').value = E('_f_pptp_client_stateless').checked ? 1 : 0;
    E('tunnel_auth').value = E('_f_tunnel_auth').checked ? 1 : 0;

	form.submit(fom, 1);

	changed = 0;
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
        <input type='hidden' name='_nextpage' value='/#vpn-pptp.asp'>
        <input type='hidden' name='_service' value='pptpclient-restart'>
        <input type='hidden' name='_nextwait' value='5'>
        
        <input type='hidden' id='pptp_client_enable' name='pptp_client_enable'>
        <input type='hidden' id='pptp_client_peerdns' name='pptp_client_peerdns'>
        <input type='hidden' id='pptp_client_firewall' name='pptp_client_firewall'>
        <input type='hidden' id='pptp_client_nat' name='pptp_client_nat'>
        <input type='hidden' id='pptp_client_dfltroute' name='pptp_client_dfltroute'>
        <input type='hidden' id='pptp_client_stateless' name='pptp_client_stateless'>
		<input type='hidden' id='tunnel_auth' name='tunnel_auth'>
		<div class="box" id="pptp-client">
			<div class="heading"><%translate("PPTP/L2TP Client");%><span class="pptp-client-status"></span></div>
			<div class="content"></div>
			<script type="text/javascript">
				$('#pptp-client .content').forms([
			{ title: '<%translate("Enable VPN");%>', name: 'f_pptp_client_enable', type: 'checkbox', value: nvram.pptp_client_enable != 0 },
	{ title: '<%translate("VPN Mode");%>', name: 'vpn_mode', type: 'select', options: [['pptp','<%translate("PPTP Client");%>'],['l2tp','<%translate("L2TP Client");%>']], value: nvram.vpn_mode },
    { title: '<%translate("Server Address");%>', name: 'pptp_client_srvip', type: 'text', size: 17, value: nvram.pptp_client_srvip },
    { title: '<%translate("Username");%>: ', name: 'pptp_client_username', type: 'text', maxlen: 50, size: 54, value: nvram.pptp_client_username },
    { title: '<%translate("Password");%>: ', name: 'pptp_client_passwd', type: 'password', maxlen: 50, size: 54, value: nvram.pptp_client_passwd },
	{ title: '<%translate("Encryption");%>', name: 'pptp_client_crypt', type: 'select', value: nvram.pptp_client_crypt,
        options: [['0', '<%translate("Auto");%>'],['1', '<%translate("None");%>'],['2','<%translate("Maximum (128 bit only)");%>'],['3','<%translate("Required (128 or 40 bit)");%>']] },
	{ title: '<%translate("Stateless MPPE connection");%>', name: 'f_pptp_client_stateless', type: 'checkbox', value: nvram.pptp_client_stateless != 0 },
	{ title: '<%translate("Accept DNS configuration");%>', name: 'pptp_client_peerdns', type: 'select', options: [[0, '<%translate("Disabled");%>'],[1, '<%translate("Enabled");%>'],[2, '<%translate("Exclusive");%>']], value: nvram.pptp_client_peerdns },
	{ title: '<%translate("Redirect Internet traffic");%>', name: 'f_pptp_client_dfltroute', type: 'checkbox', value: nvram.pptp_client_dfltroute != 0 },
    { title: '<%translate("Remote subnet / netmask");%>', multi: [
        { name: 'pptp_client_srvsub', type: 'text', maxlen: 15, size: 17, value: nvram.pptp_client_srvsub },
        { name: 'pptp_client_srvsubmsk', type: 'text', maxlen: 15, size: 17, prefix: ' /&nbsp', value: nvram.pptp_client_srvsubmsk },
        { name: 'f_pptp_client_firewall', type: 'checkbox', prefix: ' ->&nbsp <%translate("As Firewall Rule");%> &nbsp', value: nvram.pptp_client_firewall != 0 } ] },
	{ title: '<%translate("Create NAT on tunnel");%>', name: 'f_pptp_client_nat', type: 'checkbox', value: nvram.pptp_client_nat != 0 },
	{ title: 'MTU', multi: [
		{ name: 'pptp_client_mtuenable', type: 'select', options: [['0', '<%translate("Default");%>'],['1','<%translate("Manual");%>']], value: nvram.pptp_client_mtuenable },
		{ name: 'pptp_client_mtu', type: 'text', maxlen: 4, size: 6, value: nvram.pptp_client_mtu } ] },
	{ title: 'MRU', multi: [
		{ name: 'pptp_client_mruenable', type: 'select', options: [['0', '<%translate("Default");%>'],['1','<%translate("Manual");%>']], value: nvram.pptp_client_mruenable },
		{ name: 'pptp_client_mru', type: 'text', maxlen: 4, size: 6, value: nvram.pptp_client_mru } ] },
	{ title: '<%translate("Local IP Address");%>',name: 'pptp_client_lip', type: 'text', maxlen: 15, size: 16, value: nvram.pptp_client_lip  },
	null,
	{ title: '<%translate("Hostname");%>: ', name: 'wan_hostname', text: nvram.wan_hostname},
	{ title: '<%translate("Tunnel Auth.");%>', name: 'f_tunnel_auth', type: 'checkbox', value: (nvram.tunnel_auth != 0) },
    { title: '<%translate("Tunnel Password");%>: ', indent: 2, name: 'tunnel_passwd', type: 'password', maxlen: 50, size: 54, value: nvram.tunnel_passwd },
    { title: '<%translate("Custom Configuration");%>', name: 'pptp_client_custom', type: 'textarea',style: 'width: 60%; height: 80px;',value: nvram.pptp_client_custom }
				]);


			</script>
		</div>

		<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button>
		<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn"><%translate("Cancel");%><i class="icon-cancel"></i></button>
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
