<?PHP include 'header.php'; ?>
	<script type="text/javascript" src="js/vpn.js"></script>
	<script type="text/javascript">
		//	<% nvram("vpn_client_eas,vpn_client1_poll,vpn_client1_if,vpn_client1_bridge,vpn_client1_nat,vpn_client1_proto,vpn_client1_addr,vpn_client1_port,vpn_client1_retry,vpn_client1_firewall,vpn_client1_crypt,vpn_client1_comp,vpn_client1_cipher,vpn_client1_local,vpn_client1_remote,vpn_client1_nm,vpn_client1_reneg,vpn_client1_hmac,vpn_client1_adns,vpn_client1_rgw,vpn_client1_gw,vpn_client1_custom,vpn_client1_static,vpn_client1_ca,vpn_client1_crt,vpn_client1_key,vpn_client1_userauth,vpn_client1_username,vpn_client1_password,vpn_client1_useronly,vpn_client1_tlsremote,vpn_client1_cn,vpn_client1_br,vpn_client1_nopull,vpn_client1_route,vpn_client1_routing_val,vpn_client2_poll,vpn_client2_if,vpn_client2_bridge,vpn_client2_nat,vpn_client2_proto,vpn_client2_addr,vpn_client2_port,vpn_client2_retry,vpn_client2_firewall,vpn_client2_crypt,vpn_client2_comp,vpn_client2_cipher,vpn_client2_local,vpn_client2_remote,vpn_client2_nm,vpn_client2_reneg,vpn_client2_hmac,vpn_client2_adns,vpn_client2_rgw,vpn_client2_gw,vpn_client2_custom,vpn_client2_static,vpn_client2_ca,vpn_client2_crt,vpn_client2_key,vpn_client2_userauth,vpn_client2_username,vpn_client2_password,vpn_client2_useronly,vpn_client2_tlsremote,vpn_client2_cn,vpn_client2_br,vpn_client2_nopull,vpn_client2_route,vpn_client2_routing_val,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname"); %>
if(!nvram.vpn_client_eas){
	nvram.vpn_client_eas = '';
}

tabs = [['client1', $lang.CLIENT + '1'],['client2', $lang.CLIENT + '2']];
sections = [['basic', $lang.BASIC],['advanced', $lang.ADVANCED],['keys', $lang.KEYS]];
statusUpdaters = [];
for (i = 0; i < tabs.length; ++i) statusUpdaters.push(new StatusUpdater());
ciphers = [['default', $lang.USE_DEFAULT],['none', $lang.VAR_NONE]];
for (i = 0; i < vpnciphers.length; ++i) ciphers.push([vpnciphers[i],vpnciphers[i]]);

changed = 0;
vpn1up = parseInt('<% psup("vpnclient1"); %>');
vpn2up = parseInt('<% psup("vpnclient2"); %>');

		function updateStatus(num)
		{
			var xob = new XmlHttp();
			xob.onCompleted = function(text, xml)
			{
				statusUpdaters[num].update(text);
				xob = null;
			}
			xob.onError = function(ex)
			{
				statusUpdaters[num].errors.innerHTML += 'ERROR! '+ex+'<br>';
				xob = null;
			}

			// xob.post('/vpnstatus.cgi', 'client=' + (num+1));
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

			cookie.set('vpn_client_tab', name);
		}

		function sectSelect(tab, section)
		{
			tgHideIcons();

			for (var i = 0; i < sections.length; ++i)
			{
				if (section == sections[i][0])
				{
					elem.addClass(tabs[tab][0]+'-'+sections[i][0]+'-tab', 'active');
					elem.display(tabs[tab][0]+'-'+sections[i][0], true);
				}
				else
				{
					elem.removeClass(tabs[tab][0]+'-'+sections[i][0]+'-tab', 'active');
					elem.display(tabs[tab][0]+'-'+sections[i][0], false);
				}
			}

			cookie.set('vpn_client'+tab+'_section', section);
		}

		function toggle(service, isup)
		{
			if (changed && !confirm($lang.NO_SAVE_CFG_TIP + "?")) return;

			E('_' + service + '_button').disabled = true;
			form.submitHidden('service.cgi', {
				_redirect: '/#vpn-client.asp',
				_sleep: '3',
				_service: service + (isup ? '-stop' : '-start')
			});
		}

		function verifyFields(focused, quiet)
		{
			tgHideIcons();

			var ret = 1;

			// When settings change, make sure we restart the right client
			if (focused)
			{
				changed = 1;

				var clientindex = focused.name.indexOf("client");
				if (clientindex >= 0)
				{
					var clientnumber = focused.name.substring(clientindex+6,clientindex+7);
					var stripped = focused.name.substring(0,clientindex+6)+focused.name.substring(clientindex+7);

					if (stripped == 'vpn_client_local')
						E('_f_vpn_client'+clientnumber+'_local').value = focused.value;
					else if (stripped == 'f_vpn_client_local')
						E('_vpn_client'+clientnumber+'_local').value = focused.value;

					var fom = E('_fom');
					if (eval('vpn'+clientnumber+'up') && fom._service.value.indexOf('client'+clientnumber) < 0)
					{
						if ( fom._service.value != "" ) fom._service.value += ",";
						fom._service.value += 'vpnclient'+clientnumber+'-restart';
					}
				}
			}

			// Element varification
			for (i = 0; i < tabs.length; ++i)
			{
				t = tabs[i][0];

		if (!v_range('_vpn_'+t+'_poll', quiet, 0, 1440)) ret = 0;
		if (!v_ip('_vpn_'+t+'_addr', true) && !v_domain('_vpn_'+t+'_addr', true)) { ferror.set(E('_vpn_'+t+'_addr'), $lang.INVALID_SERVER_ADDRESS, quiet); ret = 0; }
		if (!v_port('_vpn_'+t+'_port', quiet)) ret = 0;
		if (!v_ip('_vpn_'+t+'_local', quiet, 1)) ret = 0;
		if (!v_ip('_f_vpn_'+t+'_local', true, 1)) ret = 0;
		if (!v_ip('_vpn_'+t+'_remote', quiet, 1)) ret = 0;
		if (!v_netmask('_vpn_'+t+'_nm', quiet)) ret = 0;
		if (!v_range('_vpn_'+t+'_retry', quiet, -1, 32767)) ret = 0;
		if (!v_range('_vpn_'+t+'_reneg', quiet, -1, 2147483647)) ret = 0;
		if (E('_vpn_'+t+'_gw').value.length > 0 && !v_ip('_vpn_'+t+'_gw', quiet, 1)) ret = 0;

		if(!v_ascii('_vpn_'+t+'_username',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_password',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_cn',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_custom',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_static',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_ca',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_crt',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_key',quiet)) return 0;
		if(!v_ascii('_vpn_'+t+'_username',quiet)) return 0;
	}

			// Visability changes
			for (i = 0; i < tabs.length; ++i)
			{
				t = tabs[i][0];

		fw = E('_vpn_'+t+'_firewall').value;
		auth = E('_vpn_'+t+'_crypt').value;
		iface = E('_vpn_'+t+'_if').value;
		bridge = E('_f_vpn_'+t+'_bridge').checked;
		nat = E('_f_vpn_'+t+'_nat').checked;
		hmac = E('_vpn_'+t+'_hmac').value;
		rgw = E('_f_vpn_'+t+'_rgw').checked;

				userauth =  E('_f_vpn_'+t+'_userauth').checked && auth == "tls";
				useronly = userauth && E('_f_vpn_'+t+'_useronly').checked;

		// Page Basic
		elem.display(PR('_f_vpn_'+t+'_userauth'), auth == "tls");
		elem.display(PR('_vpn_'+t+'_username'), PR('_vpn_'+t+'_password'), userauth );
		elem.display(PR('_f_vpn_'+t+'_useronly'), userauth);
		elem.display(E(t+'_ca_warn_text'), useronly);
		elem.display(PR('_vpn_'+t+'_hmac'), auth == "tls");
		elem.display(E(t+'_custom_crypto_text'), auth == "custom");
		elem.display(PR('_f_vpn_'+t+'_bridge'), iface == "tap");
		elem.display(E(t+'_bridge_warn_text'), !bridge);
		elem.display(PR('_f_vpn_'+t+'_nat'), fw != "custom" && (iface == "tun" || !bridge));
		elem.display(E(t+'_nat_warn_text'), fw != "custom" && (!nat || (auth == "secret" && iface == "tun")));
		elem.display(PR('_vpn_'+t+'_local'), iface == "tun" && auth == "secret");
		elem.display(PR('_f_vpn_'+t+'_local'), iface == "tap" && !bridge && auth == "secret");

		// Page Advanced
		elem.display(PR('_vpn_'+t+'_adns'), PR('_vpn_'+t+'_reneg'), auth == "tls");
		elem.display(E(t+'_gateway'), iface == "tap" && rgw > 0);

				// Page Key
				elem.display(PR('_vpn_'+t+'_static'), auth == "secret" || (auth == "tls" && hmac >= 0));
				elem.display(PR('_vpn_'+t+'_ca'), auth == "tls");
				elem.display(PR('_vpn_'+t+'_crt'), PR('_vpn_'+t+'_key'), auth == "tls" && !useronly);
				elem.display(PR('_f_vpn_'+t+'_tlsremote'), auth == "tls");
				elem.display(E(t+'_cn'), auth == "tls" && E('_f_vpn_'+t+'_tlsremote').checked);

				// keyHelp = E(t+'-keyhelp');
				// switch (auth)
				// {
				// 	case "tls":
				// 		keyHelp.href = helpURL['TLSKeys'];
				// 		break;
				// 	case "secret":
				// 		keyHelp.href = helpURL['staticKeys'];
				// 		break;
				// 	default:
				// 		keyHelp.href = helpURL['howto'];
				// 		break;
				// }
			}

	return ret;
}

		function save()
		{
			if (!verifyFields(null, false)) return;

			var fom = E('_fom');

			E('vpn_client_eas').value = '';

	for (i = 0; i < tabs.length; ++i)
	{
		t = tabs[i][0];

		if ( E('_f_vpn_'+t+'_eas').checked )
			E('vpn_client_eas').value += ''+(i+1)+',';

		E('vpn_'+t+'_bridge').value = E('_f_vpn_'+t+'_bridge').checked ? 1 : 0;
		E('vpn_'+t+'_nat').value = E('_f_vpn_'+t+'_nat').checked ? 1 : 0;
		E('vpn_'+t+'_rgw').value = E('_f_vpn_'+t+'_rgw').checked ? 1 : 0;
		E('vpn_'+t+'_userauth').value = E('_f_vpn_'+t+'_userauth').checked ? 1 : 0;
		E('vpn_'+t+'_useronly').value = E('_f_vpn_'+t+'_useronly').checked ? 1 : 0;
		E('vpn_'+t+'_tlsremote').value = E('_f_vpn_'+t+'_tlsremote').checked ? 1 : 0;
	}

			// form.submit(fom, 1);

			changed = 0;
			return submit_form('_fom');
		}

		function init()
		{
			tabSelect(cookie.get('vpn_client_tab') || tabs[0][0]);

			for (i = 0; i < tabs.length; ++i)
			{
				sectSelect(i, cookie.get('vpn_client'+i+'_section') || sections[i][0]);

				t = tabs[i][0];

		// statusUpdaters[i].init(null,null,t+'-status-stats-table',t+'-status-time',t+'-status-content',t+'-no-status',t+'-status-errors');
		// updateStatus(i);
	}

			verifyFields(null, true);
		}
	</script>
	<div class="box">
	<form id="_fom" method="post" action="tomato.cgi">
		<input type="hidden" name="_nextpage" value="/#vpn-client.asp">
		<input type="hidden" name="_nextwait" value="5">
		<input type="hidden" name="_service" value="">
		<input type="hidden" name="vpn_client_eas" id="vpn_client_eas" value="">
			<div class='heading'><script type="text/javascript">document.write($lang.OPEN_VPN_CLIENT)</script></div><hr>
            
		<div id="vpn-client"></div>
		<script type="text/javascript">
			var htmlOut = tabCreate.apply(this, tabs);

			for (i = 0; i < tabs.length; ++i) {

				t = tabs[i][0];
				htmlOut += '<div id=\''+t+'-tab\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_bridge\' name=\'vpn_'+t+'_bridge\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_nat\' name=\'vpn_'+t+'_nat\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_rgw\' name=\'vpn_'+t+'_rgw\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_userauth\' name=\'vpn_'+t+'_userauth\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_useronly\' name=\'vpn_'+t+'_useronly\'>';
				htmlOut += '<input type=\'hidden\' id=\'vpn_'+t+'_tlsremote\' name=\'vpn_'+t+'_tlsremote\'>';

				htmlOut += ('<br /><ul class="nav nav-tabs">');
				for (j = 0; j < sections.length; j++)
				{
					htmlOut += ('<li><a href="javascript:sectSelect('+i+', \''+sections[j][0]+'\')" id="'+t+'-'+sections[j][0]+'-tab">'+sections[j][1]+'</a></li>');
				}

				var action = (eval('vpn'+(i+1)+'up') ? 'title="' + $lang.STOP_VPN_CLIENT + (i+1) + '">' : 'title="' + $lang.START_VPN_CLIENT + (i+1) + '">');
				var status = (!eval('vpn'+(i+1)+'up') ? '' : '');

				htmlOut += '</ul>'
				+ '<div class="box"><div class="heading">'+ $lang.VPN_CLIENT +' #'+(i+1) + ' <a id="_vpn' + t + '_button" class="pull-right" href="#" data-toggle="tooltip"' +
				action + '</a></div><div class="content">';

				htmlOut += ('<div id=\''+t+'-basic\'>');
				htmlOut += createFormFields([
					{ title: $lang.VIA_INTERNET, name: 'f_vpn_'+t+'_eas', type: 'checkbox', value: nvram.vpn_client_eas.indexOf(''+(i+1)) >= 0 },
					{ title: $lang.INTERFACE_TYPE, name: 'vpn_'+t+'_if', type: 'select', options: [ ['tap','TAP'], ['tun','TUN'] ], value: eval( 'nvram.vpn_'+t+'_if' ) },
					{ title: $lang.PROTOCOL, name: 'vpn_'+t+'_proto', type: 'select', options: [ ['udp','UDP'], ['tcp-client','TCP'] ], value: eval( 'nvram.vpn_'+t+'_proto' ) },
					{ title: $lang.PPTP_CLIENT_SRVIP, multi: [
						{ name: 'vpn_'+t+'_addr', type: 'text', size: 17, value: eval( 'nvram.vpn_'+t+'_addr' ) },
						{ name: 'vpn_'+t+'_port', type: 'text', maxlen: 5, size: 7, value: eval( 'nvram.vpn_'+t+'_port' ) } ] },
					{ title: $lang.FIREWALL, name: 'vpn_'+t+'_firewall', type: 'select', options: [ ['auto', 'Automatic'], ['custom', 'Custom'] ], value: eval( 'nvram.vpn_'+t+'_firewall' ) },
					{ title: $lang.AUTHENTICATION_TYPE, name: 'vpn_'+t+'_crypt', type: 'select', options: [ ['tls', 'TLS'], ['secret', 'Static Key'], ['custom', 'Custom'] ], value: eval( 'nvram.vpn_'+t+'_crypt' ),
						suffix: '<span id=\''+t+'_custom_crypto_text\'>&nbsp;<small>('+ $lang.MUST_CONFIGURE_MANUALLY +'...)</small></span>' },
					{ title: $lang.USERNAME_PASSWORD_AUTHENTICATION, name: 'f_vpn_'+t+'_userauth', type: 'checkbox', value: eval('nvram.vpn_'+t+'_userauth') == undefined?false:(eval( 'nvram.vpn_'+t+'_userauth' ) != 0) },
					{ title: $lang.PPTP_CLIENT_USERNAME + ': ', indent: 2, name: 'vpn_'+t+'_username', type: 'text', maxlen: 50, size: 54, value: eval( 'nvram.vpn_'+t+'_username' ) },
					{ title: $lang.PPTP_CLIENT_PASSWD + ': ', indent: 2, name: 'vpn_'+t+'_password', type: 'password', maxlen: 50, size: 54, value: eval( 'nvram.vpn_'+t+'_password' ) },
					{ title: $lang.AUTHENTICATE_USERNAME_ONLY, indent: 2, name: 'f_vpn_'+t+'_useronly', type: 'checkbox', value: eval('nvram.vpn_'+t+'_useronly') == undefined?false:(eval( 'nvram.vpn_'+t+'_useronly' ) != 0),
						suffix: '<span style="width:auto" id=\''+t+'_ca_warn_text\'>&nbsp<small>'+ $lang.WARNING_MUST_DEFINE_CERTIFICATE_AUTHORITY +'.</small></span>' },
					{ title: $lang.HMAC_CERTIFICATION, name: 'vpn_'+t+'_hmac', type: 'select', options: [ [-1, 'Disabled'], [2, 'Bi-directional'], [0, 'Incoming (0)'], [1, 'Outgoing (1)'] ], value: eval( 'nvram.vpn_'+t+'_hmac' ) },
					{ title: $lang.THE_SERVER_IS_ON_THE_SAME_SUBNET, name: 'f_vpn_'+t+'_bridge', type: 'checkbox', value: eval( 'nvram.vpn_'+t+'_bridge' ) != 0,		
						suffix: '<span style="color: red;width:auto" id=\''+t+'_bridge_warn_text\'>&nbsp<small>'+ $lang.BRIDGE_TIP +'.</small></span>' },
					{ title: $lang.ALLOW_TUNNEL_NAT, name: 'f_vpn_'+t+'_nat', type: 'checkbox', value: eval( 'nvram.vpn_'+t+'_nat' ) != 0,
						suffix: '<span style="font-style: italic ;width:auto" id=\''+t+'_nat_warn_text\'>&nbsp<small>'+ $lang.ROUTING_MUST_BE_SET_MANUALLY +'.</small></span>' },
					{ title: $lang.LOCAL_REMOTE_NODE_ADDRESS, multi: [
						{ name: 'vpn_'+t+'_local', type: 'text', maxlen: 15, size: 17, value: eval( 'nvram.vpn_'+t+'_local' ) },
						{ name: 'vpn_'+t+'_remote', type: 'text', maxlen: 15, size: 17, value: eval( 'nvram.vpn_'+t+'_remote' ) } ] },
					{ title: $lang.TUNNEL_ADDRESS_MASK, multi: [
						{ name: 'f_vpn_'+t+'_local', type: 'text', maxlen: 15, size: 17, value: eval( 'nvram.vpn_'+t+'_local' ) },
						{ name: 'vpn_'+t+'_nm', type: 'text', maxlen: 15, size: 17, value: eval( 'nvram.vpn_'+t+'_nm' ) } ] }
				]);
				htmlOut += ('</div>');
				htmlOut += ('<div id=\''+t+'-advanced\'>');
				htmlOut += createFormFields([
					{ title: $lang.POLLING_INTERVAL, name: 'vpn_'+t+'_poll', type: 'text', maxlen: 4, size: 5, value: eval( 'nvram.vpn_'+t+'_poll' ), suffix: '&nbsp;<small>('+ $lang.IN_MINUTES +')</small>' },
					{ title: $lang.REDIRECT_INTERNET_TRAFFIC, multi: [
						{ name: 'f_vpn_'+t+'_rgw', type: 'checkbox', value: eval( 'nvram.vpn_'+t+'_rgw' ) != 0 },
						{ name: 'vpn_'+t+'_gw', type: 'text', maxlen: 15, size: 17, value: eval( 'nvram.vpn_'+t+'_gw' ), prefix: '<span id=\''+t+'_gateway\'> Gateway:&nbsp', suffix: '</span>'} ] },
				//	{ title: '<%translate("Ignore Redirect Gateway (route-nopull)");%>', name: 'f_vpn_'+t+'_nopull', type: 'checkbox', value: eval( 'nvram.vpn_'+t+'_nopull' ) != 0 },
					{ title: $lang.RECEIVE_PEER_DNS_CONFIGURATION, name: 'vpn_'+t+'_adns', type: 'select', options: [[0, 'Disabled'],[1, 'Relaxed'],[2, 'Strict'],[3, 'Exclusive']], value: eval( 'nvram.vpn_'+t+'_adns' ) },
					{ title: $lang.ENCRYPTION_ALGORITHM, name: 'vpn_'+t+'_cipher', type: 'select', options: ciphers, value: eval( 'nvram.vpn_'+t+'_cipher' ) },
					{ title: $lang.COMPRESSION, name: 'vpn_'+t+'_comp', type: 'select', options: [ ['-1', 'Disabled'], ['no', 'None'], ['yes', 'Enabled'], ['adaptive', 'Adaptive'] ], value: eval( 'nvram.vpn_'+t+'_comp' ) },
					{ title: $lang.TLS_RENEGOTIATION_TIME, name: 'vpn_'+t+'_reneg', type: 'text', maxlen: 10, size: 7, value: eval( 'nvram.vpn_'+t+'_reneg' ),
						suffix: '&nbsp;<small>('+ $lang.IN_SECONDS2 +')</small>' },
					{ title: $lang.NUMBER_OF_RECONNECTIONS, name: 'vpn_'+t+'_retry', type: 'text', maxlen: 5, size: 7, value: eval( 'nvram.vpn_'+t+'_retry' ),
						suffix: '&nbsp;<small>('+ $lang.IN_SECONDS1 +')</small>' },
					{ title: $lang.AUTHENTICATION_SERVER_CERTIFICATE, multi: [
						{ name: 'f_vpn_'+t+'_tlsremote', type: 'checkbox', value: eval('nvram.vpn_'+t+'_tlsremote') == undefined?false:(eval( 'nvram.vpn_'+t+'_tlsremote' ) != 0) },
						{ name: 'vpn_'+t+'_cn', type: 'text', maxlen: 64, size: 54,
							value: eval( 'nvram.vpn_'+t+'_cn' ), prefix: '<span id=\''+t+'_cn\'> Common Name:&nbsp', suffix: '</span>'} ] },
					{ title: $lang.CUSTOM_CONFIGURATION, name: 'vpn_'+t+'_custom', type: 'textarea', value: eval( 'nvram.vpn_'+t+'_custom' ), style: 'width: 100%; height: 80px;' }
				]);
				htmlOut += ('</div>');

			/*	htmlOut += '<div id=\''+t+'-policy\'>';
				htmlOut += createFormFields([
					{ title: '<%translate("Redirect through VPN");%>', name: 'f_vpn_'+t+'_route', type: 'checkbox', value: eval( 'nvram.vpn_'+t+'_route' ) != 0 },
					{ title: '', suffix: '</span><table class=\'tomato-grid line-table\' id=\'table_'+t+'_routing\'></table><span>' }
				]);
				htmlOut += '<div><ul><li><b><%translate("Type");%> -> <%translate("From Source IP");%></b> - <%translate("Ex");%>: "1.2.3.4" or "1.2.3.0/24".'
				htmlOut += '<li><b><%translate("Type");%> -> <%translate("To Destination IP");%></b> - <%translate("Ex");%>: "1.2.3.4" or "1.2.3.0/24".';
				htmlOut += '<li><b><%translate("Type");%> -> <%translate("To Domain");%></b> - <%translate("Ex");%>: "domain.com". <%translate("Please enter one domain per line");%>';
				htmlOut += '</ul></div></div>';*/

				htmlOut += ('<div id=\''+t+'-keys\' class="langrid">');
				// htmlOut += ('<p class=\'keyhelp\'>'+ $lang.FOR_HELP_GENERATING_KEYS_REFER_TO_THE_OPENVPN +' <a id=\''+t+'-keyhelp\'>HOWTO</a>.</p>');
				htmlOut += createFormFields([
					{ title: $lang.STATIC_KEY, name: 'vpn_'+t+'_static', type: 'textarea', value: eval( 'nvram.vpn_'+t+'_static' ), style: 'width: 100%; height: 80px;' },
					{ title: $lang.CERTIFICATE_AUTHORITY, name: 'vpn_'+t+'_ca', type: 'textarea', value: eval( 'nvram.vpn_'+t+'_ca' ), style: 'width: 100%; height: 80px;' },
					{ title: $lang.CLIENT_CERTIFICATE, name: 'vpn_'+t+'_crt', type: 'textarea', value: eval( 'nvram.vpn_'+t+'_crt' ), style: 'width: 100%; height: 80px;' },
					{ title: $lang.CLIENT_KEY, name: 'vpn_'+t+'_key', type: 'textarea', value: eval( 'nvram.vpn_'+t+'_key' ), style: 'width: 100%; height: 80px;' },
				]);
				htmlOut += ('</div>');
				// htmlOut += ('<div id=\''+t+'-status\'>');
				// htmlOut += ('<div id=\''+t+'-no-status\'><p>'+ $lang.CLIENT_IS_NOT_RUNNING_OR_STATUS_COULD_NOT_BE_READ +'.</p></div>');
				// htmlOut += ('<div id=\''+t+'-status-content\' style=\'display:none\' class=\'status-content\'>');
				// htmlOut += ('<div id=\''+t+'-status-header\' class=\'status-header\'><p>Data current as of <span id=\''+t+'-status-time\'></span>.</p></div>');
				// htmlOut += ('<div id=\''+t+'-status-stats\'><div class=\'section-title\'>General Statistics</div><table class=\'line-table\' id=\''+t+'-status-stats-table\'></table><br></div>');
				// htmlOut += ('<div id=\''+t+'-status-errors\' class=\'error\'></div>');
				// htmlOut += ('</div>');
				// htmlOut += ('<div style=\'text-align:right\'><a href=\'javascript:updateStatus('+i+')\'>Refresh Status</a></div>');
				// htmlOut += ('</div>');
				htmlOut += '</div></div>';
				// htmlOut += '<input type="button" value="' + (eval('vpn'+(i+1)+'up') ? $lang.STOP_NOW : $lang.START_NOW) + '" onclick="toggle(\'vpn'+t+'\', vpn'+(i+1)+'up)" id="_vpn'+t+'_button">'
				htmlOut +='</div>';
			}


			$('#vpn-client').append(htmlOut);
		</script>
		</form>
</div>
		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%> <i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%>  <i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	

	<script type="text/javascript">init();</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
