<!--
--><title><% ident(); %> <%translate("WLAN");%>: <%translate("Wireless Advanced Settings");%></title>
<content>
	<script type="text/javascript" src="js/wireless.jsx?_http_id=<% nv(http_id); %>"></script>
	<script type="text/javascript">
		//	<% nvram("at_update,tomatoanon_answer,wl_security_mode,wl_afterburner,wl_antdiv,wl_ap_isolate,wl_auth,wl_bcn,wl_dtim,wl_frag,wl_frameburst,wl_gmode_protection,wl_plcphdr,wl_rate,wl_rateset,wl_rts,wl_txant,wl_wme,wl_wme_no_ack,wl_wme_apsd,wl_txpwr,wl_mrate,t_features,wl_distance,wl_maxassoc,wlx_hpamp,wlx_hperx,wl_reg_mode,wl_country_code,wl_country,wl_btc_mode,wl_mimo_preamble,wl_obss_coex,wl_mitigation,wl_interference_override,wl_wmf_bss_enable"); %>
		//	<% wlcountries(); %>

		hp = features('hpamp');
		nphy = features('11n');

		function verifyFields(focused, quiet)
		{
			for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
				//		if(wl_ifaces[uidx][0].indexOf('.') < 0) {
				if (wl_sunit(uidx)<0) {
					var u = wl_unit(uidx);

					if (!v_range('_f_wl'+u+'_distance', quiet, 0, 99999)) return 0;
					if (!v_range('_wl'+u+'_maxassoc', quiet, 0, 255)) return 0;
					if (!v_range('_wl'+u+'_bcn', quiet, 1, 65535)) return 0;
					if (!v_range('_wl'+u+'_dtim', quiet, 1, 255)) return 0;
					if (!v_range('_wl'+u+'_frag', quiet, 256, 2346)) return 0;
					if (!v_range('_wl'+u+'_rts', quiet, 0, 2347)) return 0;
					if (!v_range(E('_wl'+u+'_txpwr'), quiet, hp ? 1 : 0, hp ? 251 : 400)) return 0;

					var b = E('_wl'+u+'_wme').value == 'off';
					E('_wl'+u+'_wme_no_ack').disabled = b;
					E('_wl'+u+'_wme_apsd').disabled = b;
				}
			}

			return 1;
		}

		function save()
		{
			var fom;
			var n;

			if (!verifyFields(null, false)) return;

			fom = E('_fom');

			for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
				if (wl_sunit(uidx)<0) {
					var u = wl_unit(uidx);

					n = E('_f_wl'+u+'_distance').value * 1;
					E('_wl'+u+'_distance').value = n ? n : '';

					E('_wl'+u+'_country').value = E('_wl'+u+'_country_code').value;
					E('_wl'+u+'_nmode_protection').value = E('_wl'+u+'_gmode_protection').value;
				}
			}

			if (hp) {
				if ((E('_wlx_hpamp').value != nvram.wlx_hpamp) || (E('_wlx_hperx').value != nvram.wlx_hperx)) {
					fom._service.disabled = 1;
					fom._reboot.value = 1;
					form.submit(fom, 0);
					return;
				}
			}
			else {
				E('_wlx_hpamp').disabled = 1;
				E('_wlx_hperx').disabled = 1;
			}

			form.submit(fom, 1);
		}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
	<input type="hidden" name="_nextpage" value="/#advanced-wireless.asp">
	<input type="hidden" name="_nextwait" value="10">
	<input type="hidden" name="_service" value="*">
	<input type="hidden" name="_reboot" value="0">

	<div id="formfields"></div>
	<script type="text/javascript">
		var htmlOut = ''
		for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
			if (wl_sunit(uidx)<0) {
				var u = wl_unit(uidx);

				htmlOut += ('<input type=\'hidden\' id=\'_wl'+u+'_distance\' name=\'wl'+u+'_distance\'>');
				htmlOut += ('<input type=\'hidden\' id=\'_wl'+u+'_country\' name=\'wl'+u+'_country\'>');
				htmlOut += ('<input type=\'hidden\' id=\'_wl'+u+'_nmode_protection\' name=\'wl'+u+'_nmode_protection\'>');

				htmlOut += ('<div class="box"><div class="heading"><%translate("Wireless Advanced Settings");%>');
				//if (wl_ifaces.length > 1)
				htmlOut += ('(' + wl_display_ifname(uidx) + ') ');
				//W('');
				htmlOut += ('</div><div class="content">');

				at = ((nvram['wl'+u+'_security_mode'] != "wep") && (nvram['wl'+u+'_security_mode'] != "radius") && (nvram['wl'+u+'_security_mode'] != "disabled"));
				htmlOut += createFormFields([
				{ title: '<%translate("Afterburner");%>', name: 'wl'+u+'_afterburner', type: 'select', options: [['auto','<%translate("Auto");%>'],['on','<%translate("Enabled");%>'],['off','<%translate("Disabled");%> *']],
			value: nvram['wl'+u+'_afterburner'] },
		{ title: '<%translate("AP Isolation");%>', name: 'wl'+u+'_ap_isolate', type: 'select', options: [['0','<%translate("Disabled");%> *'],['1','<%translate("Enabled");%>']],
			value: nvram['wl'+u+'_ap_isolate'] },
		{ title: '<%translate("Authentication Type");%>', name: 'wl'+u+'_auth', type: 'select',
			options: [['0','<%translate("Auto");%> *'],['1','<%translate("Shared Key");%>']], attrib: at ? 'disabled' : '',
			value: at ? 0 : nvram['wl'+u+'_auth'] },
		{ title: '<%translate("Basic Rate");%>', name: 'wl'+u+'_rateset', type: 'select', options: [['default','<%translate("Default");%> *'],['12','1-2 Mbps'],['all','<%translate("All");%>']],
			value: nvram['wl'+u+'_rateset'] },
		{ title: '<%translate("Beacon Interval");%>', name: 'wl'+u+'_bcn', type: 'text', maxlen: 5, size: 7,
			suffix: ' <small>(<%translate("range");%>: 1 - 65535; <%translate("Default");%>: 100)</small>', value: nvram['wl'+u+'_bcn'] },
		{ title: '<%translate("CTS Protection Mode");%>', name: 'wl'+u+'_gmode_protection', type: 'select', options: [['off','<%translate("Disabled");%> *'],['auto','<%translate("Auto");%>']],
			value: nvram['wl'+u+'_gmode_protection'] },
		{ title: '<%translate("Regulatory Mode");%>', name: 'wl'+u+'_reg_mode', type: 'select',
			options: [['off', '<%translate("Disabled");%> *'],['d', '802.11d'],['h', '802.11h']],
			value: nvram['wl'+u+'_reg_mode'] },
		{ title: '<%translate("Country / Region");%>', name: 'wl'+u+'_country_code', type: 'select',
			options: wl_countries, value: nvram['wl'+u+'_country_code'] },
		{ title: '<%translate("Bluetooth Coexistence");%>', name: 'wl'+u+'_btc_mode', type: 'select',
			options: [['0', '<%translate("Disabled");%> *'],['1', '<%translate("Enabled");%>'],['2', '<%translate("Preemption");%>']],
			value: nvram['wl'+u+'_btc_mode'] },
		{ title: '<%translate("Distance / ACK Timing");%>', name: 'f_wl'+u+'_distance', type: 'text', maxlen: 5, size: 7,
			suffix: ' <small><%translate("meters");%></small>&nbsp;&nbsp;<small>(<%translate("range");%>: 0 - 99999; <%translate("Default");%>: 0)</small>',
				value: (nvram['wl'+u+'_distance'] == '') ? '0' : nvram['wl'+u+'_distance'] },
		{ title: '<%translate("DTIM Interval");%>', name: 'wl'+u+'_dtim', type: 'text', maxlen: 3, size: 5,
			suffix: ' <small>(<%translate("range");%>: 1 - 255; <%translate("Default");%>: 1)</small>', value: nvram['wl'+u+'_dtim'] },
		{ title: '<%translate("Fragmentation Threshold");%>', name: 'wl'+u+'_frag', type: 'text', maxlen: 4, size: 6,
			suffix: ' <small>(<%translate("range");%>: 256 - 2346; <%translate("Default");%>: 2346)</small>', value: nvram['wl'+u+'_frag'] },
		{ title: '<%translate("Frame Burst");%>', name: 'wl'+u+'_frameburst', type: 'select', options: [['off','<%translate("Disabled");%> *'],['on','<%translate("Enabled");%>']],
			value: nvram['wl'+u+'_frameburst'] },
		{ title: '<%translate("HP");%>', hidden: !hp || (uidx > 0) },
			{ title: '<%translate("Amplifier");%>', indent: 2, name: 'wlx_hpamp' + (uidx > 0 ? uidx + '' : ''), type: 'select', options: [['0','Disable'],['1','Enable *']],
				value: nvram.wlx_hpamp != '0', hidden: !hp || (uidx > 0) },
			{ title: '<%translate("Enhanced RX Sensitivity");%>', indent: 2, name: 'wlx_hperx' + (uidx > 0 ? uidx + '' : ''), type: 'select', options: [['0','Disable *'],['1','Enable']],
				value: nvram.wlx_hperx != '0', hidden: !hp || (uidx > 0) },
		{ title: '<%translate("Maximum Clients");%>', name: 'wl'+u+'_maxassoc', type: 'text', maxlen: 3, size: 5,
			suffix: ' <small>(<%translate("range");%>: 1 - 255; <%translate("Default");%>: 128)</small>', value: nvram['wl'+u+'_maxassoc'] },
		{ title: '<%translate("Multicast Rate");%>', name: 'wl'+u+'_mrate', type: 'select',
			options: [['0','<%translate("Auto");%> *'],['1000000','1 Mbps'],['2000000','2 Mbps'],['5500000','5.5 Mbps'],['6000000','6 Mbps'],['9000000','9 Mbps'],['11000000','11 Mbps'],['12000000','12 Mbps'],['18000000','18 Mbps'],['24000000','24 Mbps'],['36000000','36 Mbps'],['48000000','48 Mbps'],['54000000','54 Mbps']],
			value: nvram['wl'+u+'_mrate'] },
		{ title: '<%translate("Preamble");%>', name: 'wl'+u+'_plcphdr', type: 'select', options: [['long','<%translate("Long");%> *'],['short','<%translate("Short");%>']],
			value: nvram['wl'+u+'_plcphdr'] },
		{ title: '<%translate("802.11n Preamble");%>', name: 'wl'+u+'_mimo_preamble', type: 'select', options: [['auto','<%translate("Auto");%>'],['mm','Mixed Mode *'],['gf','Green Field'],['gfbcm','GF-BRCM']],
			value: nvram['wl'+u+'_mimo_preamble'], hidden: !nphy },
		{ title: '<%translate("Overlapping BSS Coexistence");%>', name: 'wl'+u+'_obss_coex', type: 'select', options: [['0','<%translate("Disabled");%> *'],['1','<%translate("Enabled");%>']],
			value: nvram['wl'+u+'_obss_coex'], hidden: !nphy },
		{ title: '<%translate("RTS Threshold");%>', name: 'wl'+u+'_rts', type: 'text', maxlen: 4, size: 6,
			suffix: ' <small>(<%translate("range");%>: 0 - 2347; <%translate("Default");%>: 2347)</small>', value: nvram['wl'+u+'_rts'] },
		{ title: '<%translate("Receive Antenna");%>', name: 'wl'+u+'_antdiv', type: 'select', options: [['3','<%translate("Auto");%> *'],['1','A'],['0','B']],
			value: nvram['wl'+u+'_antdiv'] },
		{ title: '<%translate("Transmit Antenna");%>', name: 'wl'+u+'_txant', type: 'select', options: [['3','<%translate("Auto");%> *'],['1','A'],['0','B']],
			value: nvram['wl'+u+'_txant'] },
		{ title: '<%translate("Transmit Power");%>', name: 'wl'+u+'_txpwr', type: 'text', maxlen: 3, size: 5,
			suffix: hp ?
				' <small>mW (<%translate("before amplification");%>)</small>&nbsp;&nbsp;<small>(<%translate("range");%>: 1 - 251; <%translate("Default");%>: 10)</small>' :
				' <small>mW</small>&nbsp;&nbsp;<small>(range: 0 - 400, actual max depends on Country selected; use 0 for hardware default)</small>',
				value: nvram['wl'+u+'_txpwr'] },
		{ title: '<%translate("Transmission Rate");%>', name: 'wl'+u+'_rate', type: 'select',
			options: [['0','<%translate("Auto");%> *'],['1000000','1 Mbps'],['2000000','2 Mbps'],['5500000','5.5 Mbps'],['6000000','6 Mbps'],['9000000','9 Mbps'],['11000000','11 Mbps'],['12000000','12 Mbps'],['18000000','18 Mbps'],['24000000','24 Mbps'],['36000000','36 Mbps'],['48000000','48 Mbps'],['54000000','54 Mbps']],
			value: nvram['wl'+u+'_rate'] },
	{ title: '<%translate("Interference Mitigation");%>', name: 'wl'+u+'_mitigation', type: 'select',
		options: [['0','None *'],['1','Non-WLAN'],['2','WLAN Manual'],['3','WLAN Auto'],['4','WLAN Auto with Noice Reduction']],
		value: nvram['wl'+u+'_mitigation'] },
	{ title: '<%translate("WMM");%>', name: 'wl'+u+'_wme', type: 'select', options: [['auto','<%translate("Auto");%> *'],['off','Disable'],['on','<%translate("Enabled");%>']], value: nvram['wl'+u+'_wme'] },
	{ title: '<%translate("No ACK");%>', name: 'wl'+u+'_wme_no_ack', indent: 2, type: 'select', options: [['off','<%translate("Disabled");%> *'],['on','<%translate("Enabled");%>']],
		value: nvram['wl'+u+'_wme_no_ack'] },
	{ title: '<%translate("APSD Mode");%>', name: 'wl'+u+'_wme_apsd', indent: 2, type: 'select', options: [['off','<%translate("Disabled");%>'],['on','<%translate("Enabled");%> *']],
		value: nvram['wl'+u+'_wme_apsd'] },
	{ title: '<%translate("Wireless Multicast Forwarding");%>', name: 'wl'+u+'_wmf_bss_enable', type: 'select', options: [['0','<%translate("Disabled");%> *'],['1','<%translate("Enabled");%>']],
		value: nvram['wl'+u+'_wmf_bss_enable'] }
					]);
				htmlOut += ('<small><%translate("The default settings are indicated with an asterisk");%><b style="font-size: 1.5em">*</b><%translate("symbol");%> .</small></div></div>');
			}

		}

		$('#formfields').append(htmlOut);
	</script>

	<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button>
	<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn"><%translate("Cancel");%><i class="icon-cancel"></i></button>
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
