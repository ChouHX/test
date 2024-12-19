<?PHP include 'header.php'; ?>
<script type='text/javascript'>

//	<% nvram("ipsec1_ca,ipsec2_ca,ipsec1_certificate,ipsec1_private_key,ipsec2_certificate,ipsec2_private_key,ipsec1_authby,ipsec2_authby,ipsec_schedule,ipsec1_mode,ipsec2_mode,ipsec1_ext,ipsec2_ext,ipsec1_left,ipsec2_left,ipsec1_leftsubnet,ipsec2_leftsubnet,ipsec1_leftfirewall,ipsec2_leftfirewall,ipsec1_right,ipsec2_right,ipsec1_rightsubnet,ipsec2_rightsubnet,ipsec1_rightfirewall,ipsec2_rightfirewall,ipsec1_keyexchange,ipsec2_keyexchange,ipsec1_ph1_group,ipsec2_ph1_group,ipsec1_ike_enc,ipsec2_ike_enc,ipsec1_ike_auth,ipsec2_ike_auth,ipsec1_ike_lifetime,ipsec2_ike_lifetime,ipsec1_ph2_group,ipsec2_ph2_group,ipsec1_esp_enc,ipsec2_esp_enc,ipsec1_esp_auth,ipsec2_esp_auth,ipsec1_keylife,ipsec2_keylife,ipsec1_pskkey,ipsec2_pskkey,ipsec1_aggressive,ipsec2_aggressive,ipsec1_compress,ipsec2_compress,ipsec1_dpdaction,ipsec2_dpdaction,ipsec1_dpddelay,ipsec2_dpddelay,ipsec1_dpdtimeout,ipsec2_dpdtimeout,ipsec1_icmp_check,ipsec2_icmp_check,ipsec1_icmp_intval,ipsec2_icmp_intval,ipsec1_icmp_count,ipsec2_icmp_count,ipsec1_icmp_addr,ipsec2_icmp_addr,ipsec1_custom1,ipsec2_custom1,ipsec1_custom2,ipsec2_custom2,ipsec1_custom3,ipsec2_custom3,ipsec1_custom4,ipsec2_custom4"); %>

tabs = [['ipsec1', 'IPSec 1'],['ipsec2', 'IPSec 2'],['schedule', $lang.POLICY]];
sections = [['group', $lang.VAR_TERM_PARAM_IPSEC_GROUP],['basic', $lang.VAR_TERM_PARAM_IPSEC_BASIC],['advanced', $lang.VAR_TERM_PARAM_IPSEC_BASIC_ADVANCED]];
changed = 0;

function tabSelect(name)
{
	tgHideIcons();
	tabHigh(name);
	for (var i = 0; i < tabs.length; ++i)
	{
		var on = (name == tabs[i][0]);
		elem.display(tabs[i][0] + '-tab', on);
	}

	cookie.set('vpn_ipsec_tab', name);
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

	cookie.set('ipsec'+tab+'_section', section);
}

function verifyFields(focused, quiet)
{
	tgHideIcons();

	var enable,a,b,c;

	for (i = 0; i < tabs.length-1; ++i)
	{
		t = tabs[i][0];

		enable = E('_f_'+t+'_mode').checked;

		E('_'+t+'_ext').disabled=!enable;
		E('_f_'+t+'_aggressive').disabled=!enable;
		E('_'+t+'_ike_lifetime').disabled=!enable;
		E('_'+t+'_keylife').disabled=!enable;
		E('_f_'+t+'_dpdaction').disabled=!enable;
		E('_'+t+'_dpddelay').disabled=!enable;
		E('_'+t+'_dpdtimeout').disabled=!enable;
		E('_'+t+'_keyexchange').disabled=!enable;
		E('_'+t+'_ike_enc').disabled=!enable;
		E('_'+t+'_ike_auth').disabled=!enable;
		E('_'+t+'_ph1_group').disabled=!enable;
		E('_'+t+'_esp_enc').disabled=!enable;
		E('_'+t+'_esp_auth').disabled=!enable;
		E('_'+t+'_ph2_group').disabled=!enable;
		E('_f_'+t+'_leftfirewall').disabled=!enable;
		E('_f_'+t+'_rightfirewall').disabled=!enable;
		E('_f_'+t+'_compress').disabled=!enable;
		E('_'+t+'_left').disabled=!enable;
		E('_'+t+'_leftsubnet').disabled=!enable;
		E('_'+t+'_right').disabled=!enable;
		E('_'+t+'_rightsubnet').disabled=!enable;
		E('_'+t+'_custom1').disabled=!enable;
		E('_'+t+'_custom2').disabled=!enable;
		E('_'+t+'_custom3').disabled=!enable;
		E('_'+t+'_custom4').disabled=!enable;
		E('_'+t+'_pskkey').disabled=!enable;
		E('_f_'+t+'_icmp_check').disabled=!enable;
		E('_'+t+'_icmp_intval').disabled=!enable;
		E('_'+t+'_icmp_count').disabled=!enable;
		E('_'+t+'_icmp_addr').disabled=!enable;

		c = E('_'+t+'_authby').value;
		elem.display(PR(E('_'+t+'_pskkey')), c == 0);
		elem.display(PR(E('_'+t+'_certificate')), c != 0);
		elem.display(PR(E('_'+t+'_private_key')), c != 0);
		elem.display(PR(E('_'+t+'_ca')), c != 0);

		c = E('_'+t+'_ext').value;
		elem.display(PR(E('_'+t+'_leftsubnet')), c == 0);
		elem.display(PR(E('_'+t+'_rightsubnet')), c == 0);

		a = E('_f_'+t+'_dpdaction').checked;
		E('_'+t+'_dpddelay').disabled = (a == 0);
		E('_'+t+'_dpdtimeout').disabled = (a == 0);
		elem.display(PR(E('_'+t+'_dpddelay')), (a != 0));
		elem.display(PR(E('_'+t+'_dpdtimeout')), (a != 0));

		b = E('_f_'+t+'_icmp_check').checked;
		E('_'+t+'_icmp_intval').disabled = (b == 0);
		E('_'+t+'_icmp_count').disabled = (b == 0);
		E('_'+t+'_icmp_addr').disabled = (b == 0);
		elem.display(PR(E('_'+t+'_icmp_intval')), (b != 0));
		elem.display(PR(E('_'+t+'_icmp_count')), (b != 0));
		elem.display(PR(E('_'+t+'_icmp_addr')), (b != 0));

		if (enable != 0)
		{
			if (!v_range('_'+t+'_ike_lifetime', quiet, 1, 86400)) return 0;
			if (!v_range('_'+t+'_keylife', quiet, 1, 86400)) return 0;
		}
		else
		{
			ferror.clear(E('_'+t+'_ike_lifetime'));
			ferror.clear(E('_'+t+'_keylife'));
		}

		if (enable != 0 && a != 0)
		{
			if (!v_range('_'+t+'_dpddelay', quiet, 1, 86400)) return 0;
			if (!v_range('_'+t+'_dpdtimeout', quiet, 1, 86400)) return 0;
		}
		else
		{
			ferror.clear(E('_'+t+'_dpddelay'));
			ferror.clear(E('_'+t+'_dpdtimeout'));
		}

		if (!_v_iptaddr('_'+t+'_rightsubnet', quiet, 15, 1, 1, 0)) return 0;
		if (!_v_iptaddr('_'+t+'_leftsubnet', quiet, 15, 1, 1)) return 0;

		if (enable != 0 && b != 0)
		{
			if (!v_range('_'+t+'_icmp_intval', quiet, 1, 86400)) return 0;
			if (!v_range('_'+t+'_icmp_count', quiet, 1, 86400)) return 0;
			if (!v_ip('_'+t+'_icmp_addr', true))
			{
				ferror.set(E('_'+t+'_icmp_addr'), $lang.INVALID_SERVER_ADDRESS, quiet);
				return 0;
			}
		}
		else
		{
			ferror.clear(E('_'+t+'_icmp_intval'));
			ferror.clear(E('_'+t+'_icmp_count'));
		}
	}

	return 1;
}

function save()
{
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');

	for (i = 0; i < tabs.length-1; ++i)
	{
		t = tabs[i][0];

		E(t+'_mode').value = E('_f_'+t+'_mode').checked ? 1 : 0;
		E(t+'_aggressive').value = E('_f_'+t+'_aggressive').checked ? 1 : 0;
		E(t+'_leftfirewall').value = E('_f_'+t+'_leftfirewall').checked ? 1 : 0;
		E(t+'_rightfirewall').value = E('_f_'+t+'_rightfirewall').checked ? 1 : 0;
		E(t+'_compress').value = E('_f_'+t+'_compress').checked ? 1 : 0;
		E(t+'_dpdaction').value = E('_f_'+t+'_dpdaction').checked ? 1 : 0;
		E(t+'_icmp_check').value = E('_f_'+t+'_icmp_check').checked ? 1 : 0;
	}

	// form.submit(fom, 1);

	changed = 0;
	return submit_form('_fom');
}

function init()
{
	tabSelect(cookie.get('vpn_ipsec_tab') || tabs[0][0]);

	for (i = 0; i < tabs.length-1; ++i)
	{
		sectSelect(i, cookie.get('ipsec'+i+'_section') || sections[i][0]);
	}

	verifyFields(null, true);
}

</script>

<div class="box">
<form id="_fom" method="post" action="tomato.cgi">
	<input type="hidden" name="_nextpage" value="/#vpn-ipsec.asp">
	<input type='hidden' name='_service' value='ipsecd-restart'>
	<div class='heading'>IPSec</div><hr>
	<div id="vpn-client"></div>
<script type='text/javascript'>

var htmlOut = tabCreate.apply(this, tabs);
for (i = 0; i < tabs.length-1; ++i)
{
	t = tabs[i][0];
	htmlOut += '<div id=\''+t+'-tab\'>';

	htmlOut += '<input type=\'hidden\' id=\''+t+'_mode\' name=\''+t+'_mode\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_aggressive\' name=\''+t+'_aggressive\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_leftfirewall\' name=\''+t+'_leftfirewall\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_rightfirewall\' name=\''+t+'_rightfirewall\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_compress\' name=\''+t+'_compress\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_dpdaction\' name=\''+t+'_dpdaction\'>';
	htmlOut += '<input type=\'hidden\' id=\''+t+'_icmp_check\' name=\''+t+'_icmp_check\'>';

	htmlOut += '<br /><ul class="nav nav-tabs">';
	for (j = 0; j < sections.length; j++)
	{
		htmlOut += '<li><a href="javascript:sectSelect('+i+', \''+sections[j][0]+'\')" id="'+t+'-'+sections[j][0]+'-tab">'+sections[j][1]+'</a></li>';
	}
	htmlOut +='</ul>';

	htmlOut +='<div id=\''+t+'-group\'>';
	htmlOut +=createFormFields([
	{ title: $lang.IPSEC1_MODE, name: 'f_'+t+'_mode', type: 'checkbox', value: (eval( 'nvram.'+t+'_mode' ) == '1') },
	{ title: $lang.IPSEC1_EXT, name: t+'_ext', type: 'select', options: [['0', 'Normal'],['1', 'GRE over IPSec'],['2', 'L2TP over IPSec']],	value: eval('nvram.'+t+'_ext') },
	{ title: $lang.IPSEC1_LEFT,  name: t+'_left', type: 'select', options: [['3g', 'Cellular']],	value: eval('nvram.'+t+'_left') },
	{ title: $lang.IPSEC1_LEFTSUBNET, name: t+'_leftsubnet', type: 'text', maxlen: 32, size: 33, value: eval('nvram.'+t+'_leftsubnet'), suffix: $lang.EX + '. 192.168.1.0/24' },
	{ title: $lang.IPSEC1_LEFTFIREWALL, name: 'f_'+t+'_leftfirewall', type: 'checkbox', value: (eval('nvram.'+t+'_leftfirewall') == '1') },
	{ title: $lang.IPSEC1_RIGHT, name: t+'_right', type: 'text', maxlen: 64, size: 67, value: eval('nvram.'+t+'_right') },
	{ title: $lang.IPSEC1_RIGHTSUBNET, name: t+'_rightsubnet', type: 'text', maxlen: 32, size: 33, value: eval('nvram.'+t+'_rightsubnet'), suffix: $lang.EX + '. 192.168.88.0/24' },
	{ title: $lang.IPSEC1_RIGHTFIREWALL, name: 'f_'+t+'_rightfirewall', type: 'checkbox', value: (eval('nvram.'+t+'_rightfirewall') == '1')}
	]);
	htmlOut +='</div>';
	htmlOut +='<div id=\''+t+'-basic\'>';
	htmlOut +=createFormFields([
	{ title: $lang.IPSEC1_AUTHBY,  name: t+'_keyexchange', type: 'select', options: [['ikev1', 'IKE'],['ikev2','IKEv2']],	value: eval('nvram.'+t+'_keyexchange') },
	{ title: $lang.AUTH_MODE,  name: t+'_authby', type: 'select', options: [['0', 'Preshared Key'],['1','X509']],	value: eval('nvram.'+t+'_authby') },
	{ title: $lang.IPSEC1_PH1_GROUP, name: t+'_ph1_group', type: 'select', options: [['modp768', 'Group 1 - modp768'],['modp1024', 'Group 2 - modp1024'],['modp1536', 'Group 5 - modp1536'],['modp2048', 'Group 14 - modp2048']],value: eval('nvram.'+t+'_ph1_group') },
	{ title: $lang.IPSEC1_IKE_ENC, name: t+'_ike_enc', type: 'select', options: [['des', 'DES (56-bit)'],['3des', '3DES (168-bit)'],['aes128', 'AES-128 (128-bit)'],['aes192', 'AES-192 (192-bit)'],['aes256', 'AES-256 (256-bit)']],value: eval('nvram.'+t+'_ike_enc') },
	{ title: $lang.IPSEC1_IKE_AUTH, name: t+'_ike_auth', type: 'select', options: [['md5', 'MD5 HMAC (96-bit)'],['sha', 'SHA1 HMAC (96-bit)'],['sha256', 'SHA2_256_128 HMAC (128-bit)'],['sha384', 'SHA2_384_192 HMAC (192-bit)'],['sha512', 'SHA2_512_256 HMAC (256-bit)']],	value: eval('nvram.'+t+'_ike_auth') },
	{ title: $lang.IPSEC1_IKELIFETIME, name: t+'_ike_lifetime', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: (eval('nvram.'+t+'_ike_lifetime') > 0) ? eval('nvram.'+t+'_ike_lifetime') : 28800 },
	null,
	{ title: $lang.IPSEC1_PH2_GROUP, name: t+'_ph2_group', type: 'select', options: [['null', 'NONE'],['modp768', 'Group 1 - modp768'],['modp1024', 'Group 2 - modp1024'],['modp1536', 'Group 5 - modp1536'],['modp2048', 'Group 14 - modp2048']],value: eval('nvram.'+t+'_ph2_group') },
	{ title: $lang.IPSEC1_ESP_ENC, name: t+'_esp_enc', type: 'select', options: [['null', 'NONE'],['des', 'DES (56-bit)'],['3des', '3DES (168-bit)'],['aes128', 'AES-128 (128-bit)'],['aes192', 'AES-192 (192-bit)'],['aes256', 'AES-256 (256-bit)']],value: eval('nvram.'+t+'_esp_enc') },
	{ title: $lang.IPSEC1_ESP_AUTH, name: t+'_esp_auth', type: 'select', options: [['null', 'NONE'],['md5', 'MD5 HMAC (96-bit)'],['sha', 'SHA1 HMAC (96-bit)'],['sha256', 'SHA2_256_128 HMAC (128-bit)'],['sha384', 'SHA2_384_192 HMAC (192-bit)'],['sha512', 'SHA2_512_256 HMAC (256-bit)']],value: eval('nvram.'+t+'_esp_auth') },
	{ title: $lang.IPSEC1_KEYLIFE, name: t+'_keylife', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: (eval('nvram.'+t+'_keylife') > 0) ? eval('nvram.'+t+'_keylife') : 3600 },
	{ title: $lang.IPSEC1_PSKKEY, name: t+'_pskkey', type: 'password', maxlen: 64, size: 68, value: eval('nvram.'+t+'_pskkey') },
	{ title: $lang.CA, name: t+'_ca', type: 'textarea', value: eval('nvram.'+t+'_ca' ), style: 'width: 100%; height: 80px;' },
	{ title: $lang.CERTIFICATE, name: t+'_certificate', type: 'textarea', value: eval('nvram.'+t+'_certificate' ), style: 'width: 100%; height: 80px;' },
	{ title: $lang.PRIVATE_KEY, name: t+'_private_key', type: 'textarea', value: eval('nvram.'+t+'_private_key'), style: 'width: 100%; height: 80px;' }
	]);
	htmlOut +='</div>';
	htmlOut +='<div id=\''+t+'-advanced\'>';
	htmlOut += createFormFields([
	{ title: $lang.IPSEC1_AGGRESSIVE, name: 'f_'+t+'_aggressive', type: 'checkbox', value: (eval('nvram.'+t+'_aggressive') == '1') },
	{ title: $lang.IPSEC1_COMPRESS, name: 'f_'+t+'_compress', type: 'checkbox', value: (eval('nvram.'+t+'_compress')== '1') },
	{ title: $lang.IPSEC1_DPDACTION, name: 'f_'+t+'_dpdaction', type: 'checkbox', value: (eval('nvram.'+t+'_dpdaction')== '1') },
		{ title: $lang.DETECTION_CYCLE, indent: 2, name: t+'_dpddelay', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: (eval('nvram.'+t+'_dpddelay') > 0) ? eval('nvram.'+t+'_dpddelay') : 30 },
		{ title: $lang.DETECTION_TIMEOUT_INTERVAL, indent: 2, name: t+'_dpdtimeout', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: (eval('nvram.'+t+'_dpdtimeout') > 0) ? eval('nvram.'+t+'_dpdtimeout') : 150 },	
		{ title: $lang.ICMP_CHECK, name: 'f_'+t+'_icmp_check', type: 'checkbox', value: (eval('nvram.'+t+'_icmp_check') == '1') },
		{ title: $lang.DETECTION_CYCLE, indent: 2, name: t+'_icmp_intval', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: (eval('nvram.'+t+'_icmp_intval') > 0) ? eval('nvram.'+t+'_icmp_intval') : 30 },
		{ title: $lang.DETECTION_TIMEOUT_TIMES, indent: 2, name: t+'_icmp_count', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.TIMES +'</i>',value: (eval('nvram.'+t+'_icmp_count') > 0) ? eval('nvram.'+t+'_icmp_count') : 3 },
		{ title: $lang.UTMSPINGADDR, indent: 2, name: t+'_icmp_addr', type: 'text', maxlen: 15, size: 17, value: eval('nvram.'+t+'_icmp_addr') },
	{ title: $lang.IPSEC1_CUSTOM1, name: t+'_custom1', type: 'text', maxlen: 256, size: 64, value: eval('nvram.'+t+'_custom1') },
	{ title: $lang.IPSEC1_CUSTOM2, name: t+'_custom2', type: 'text', maxlen: 256, size: 64, value: eval('nvram.'+t+'_custom2') },
	{ title: $lang.IPSEC1_CUSTOM3, name: t+'_custom3', type: 'text', maxlen: 256, size: 64, value: eval('nvram.'+t+'_custom3') },
	{ title: $lang.IPSEC1_CUSTOM4, name: t+'_custom4', type: 'text', maxlen: 256, size: 64, value: eval('nvram.'+t+'_custom4') }
	]);
	htmlOut +='</div>';
	htmlOut +='</div>';
}

	t = tabs[tabs.length-1][0];
	htmlOut +='<div id=\''+t+'-tab\'>';
	htmlOut +='<div id=\''+t+'-config\'>';
	htmlOut += createFormFields([
		{ title: $lang.POLICY, name: 'ipsec_schedule', type: 'select', options: [['0', $lang.VAR_NONE],['1', $lang.AUTO_SWITCH],['2', $lang.VAR_BACKUP]],value: nvram.ipsec_schedule }
	]);
	htmlOut +='</div>';
	htmlOut +='</ul><div class=\'tabs-bottom\'></div>';
	htmlOut +='</div>';
	$('#vpn-client').append(htmlOut);
</script>
</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%> <i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%>  <i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
		</form>

<script type="text/javascript">init();</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
