<?PHP include 'header.php'; ?>
<script type="text/javascript">
//<% nvram("dmvpn_on,dmvpn_tunaddr,dmvpn_tunmask,dmvpn_tunkey,dmvpn_tunmtu,dmvpn_tunsrc,dmvpn_nhs,dmvpn_nhs2,dmvpn_nhs_tunaddr,dmvpn_nhs_tunaddr2,dmvpn_nhrpkey,dmvpn_networkid,dmvpn_ike_lifetime,dmvpn_keylife,dmvpn_dpdaction,dmvpn_dpddelay,dmvpn_dpdtimeout,dmvpn_keyexchange,dmvpn_ike_enc,dmvpn_ike_auth,dmvpn_ph1_group,dmvpn_esp_enc,dmvpn_esp_auth,dmvpn_ph2_group,dmvpn_custom1,dmvpn_custom2,dmvpn_custom3,dmvpn_custom4,dmvpn_pskkey"); %>
var bi = JSON.parse(nvram.bi);

function verifyFields(focused, quiet)
{
	var on;
	on = E('_f_dmvpn_on').checked;
	E('_dmvpn_tunaddr').disabled=!on;
	E('_dmvpn_tunmask').disabled=!on;
	E('_dmvpn_tunkey').disabled=!on;
	E('_dmvpn_tunmtu').disabled=!on;
	E('_dmvpn_tunsrc').disabled=!on;
	E('_dmvpn_nhs').disabled=!on;
	E('_dmvpn_nhs2').disabled=!on;
	E('_dmvpn_nhs_tunaddr').disabled=!on;
	E('_dmvpn_nhs_tunaddr2').disabled=!on;
	E('_dmvpn_nhrpkey').disabled=!on;
	E('_dmvpn_networkid').disabled=!on;
	E('_dmvpn_ike_lifetime').disabled=!on;
	E('_dmvpn_keylife').disabled=!on;
	E('_f_dmvpn_dpdaction').disabled=!on;
	E('_dmvpn_dpddelay').disabled=!on;
	E('_dmvpn_dpdtimeout').disabled=!on;
	E('_dmvpn_keyexchange').disabled=!on;
	E('_dmvpn_ike_enc').disabled=!on;
	E('_dmvpn_ike_auth').disabled=!on;
	E('_dmvpn_ph1_group').disabled=!on;
	E('_dmvpn_esp_enc').disabled=!on;
	E('_dmvpn_esp_auth').disabled=!on;
	E('_dmvpn_ph2_group').disabled=!on;
	E('_dmvpn_custom1').disabled=!on;
	E('_dmvpn_custom2').disabled=!on;
	E('_dmvpn_custom3').disabled=!on;
	E('_dmvpn_custom4').disabled=!on;
	E('_dmvpn_pskkey').disabled=!on;

	a = E('_f_dmvpn_dpdaction').checked;
	E('_dmvpn_dpddelay').disabled = (a == 0);
	E('_dmvpn_dpdtimeout').disabled = (a == 0);
	elem.display(PR(E('_dmvpn_dpddelay')), (a != 0));
	elem.display(PR(E('_dmvpn_dpdtimeout')), (a != 0));

	if(!v_ip('_dmvpn_tunaddr', quiet)) return 0;
	if(!v_netmask('_dmvpn_tunmask', quiet)) return 0;
	if(!E('_dmvpn_nhs').value.length || (!v_ip('_dmvpn_nhs', 1) && !v_domain('_dmvpn_nhs', 1)))
	{
		ferror.set(E('_dmvpn_nhs'), $lang.INVALID_SERVER_ADDRESS,quiet);
		return 0;
	}
	if(E('_dmvpn_nhs2').value.length && (!v_ip('_dmvpn_nhs2', 1) && !v_domain('_dmvpn_nhs2', 1)))
	{
		ferror.set(E('_dmvpn_nhs2'), $lang.INVALID_SERVER_ADDRESS,quiet);
		return 0;
	}
	if(E('_dmvpn_nhs_tunaddr').value.length && !v_ip('_dmvpn_nhs_tunaddr', quiet)) return 0;
	if(E('_dmvpn_nhs_tunaddr2').value.length && !v_ip('_dmvpn_nhs_tunaddr2', quiet)) return 0;
	if(!v_range(E('_dmvpn_networkid'), quiet,1,4294967295)) return 0;
	if(!v_range(E('_dmvpn_tunmtu'), quiet,0,2000)) return 0;

	if(on != 0)
	{
		if (!v_range('_dmvpn_ike_lifetime', quiet, 1, 86400)) return 0;
		if (!v_range('_dmvpn_keylife', quiet, 1, 86400)) return 0;
	}
	else
	{
		ferror.clear(E('_dmvpn_ike_lifetime'));
		ferror.clear(E('_dmvpn_keylife'));
	}

	if(on != 0 && a != 0)
	{
		if (!v_range('_dmvpn_dpddelay', quiet, 1, 86400)) return 0;
		if (!v_range('_dmvpn_dpdtimeout', quiet, 1, 86400)) return 0;
	}
	else
	{
		ferror.clear(E('_dmvpn_dpddelay'));
		ferror.clear(E('_dmvpn_dpdtimeout'));
	}

	return 1;
}

function save()
{
	var fom;
	if(!verifyFields(null, false)) return;
	fom = E('_fom');
	fom.dmvpn_on.value = E('_f_dmvpn_on').checked ? 1 : 0;
	fom.dmvpn_dpdaction.value = E('_f_dmvpn_dpdaction').checked ? 1 : 0;
	// form.submit(fom, 0);
	return submit_form('_fom');
}

function init()
{
}
</script>

<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#vpn-dmvpn.asp'>
<input type='hidden' name='_service' value='ipsecd-restart'>
<input type='hidden' name='dmvpn_on'>
<input type='hidden' name='dmvpn_dpdaction'>

<div class="box">
<div class="heading">DMVPN</div>
<div class="content dmvpn-settings"></div>
<script type="text/javascript">
internet_list = ['modem','wan','sta'];
iflist = [];
if(bi.hw == 'dd')
{
	internet_list.push('modem2');
}
if((bi.model == 'g9') || (bi.model == 'g5'))
{
	internet_list.push('sta2');
}
for(var i=0;i<internet_list.length;i++)
{
	iflist.push([internet_list[i],internet_list[i]]);
}
$('.dmvpn-settings').forms([
	{ title: $lang.ENABLE_DMVPN, name: 'f_dmvpn_on', type: 'checkbox', value: (nvram.dmvpn_on == '1') },
	{ title: $lang.TUNNEL_ADDRESS, name: 'dmvpn_tunaddr', type: 'text', maxlen: 15, size: 32,value: nvram.dmvpn_tunaddr },
	{ title: $lang.TUNNEL_NETMASK, name: 'dmvpn_tunmask', type: 'text', maxlen: 15, size: 32,value: nvram.dmvpn_tunmask },
	{ title: $lang.TUNNEL_MTU, name: 'dmvpn_tunmtu', type: 'text', maxlen: 8, size: 16,value: nvram.dmvpn_tunmtu },
	{ title: $lang.TUNNEL_KEY, name: 'dmvpn_tunkey', type: 'text', maxlen: 8, size: 16,value: nvram.dmvpn_tunkey },
	{ title: $lang.TUNNEL_SOURCE, name: 'dmvpn_tunsrc',type: 'select', options: iflist, value: nvram.dmvpn_tunsrc },
	{ title: 'NHRP ' + $lang.PPTP_CLIENT_SRVIP, name: 'dmvpn_nhs', type: 'text', maxlen: 128, size: 32,value: nvram.dmvpn_nhs },
	{ title: 'NHRP ' + $lang.TUNNEL_ADDRESS, name: 'dmvpn_nhs_tunaddr', type: 'text', maxlen: 15, size: 32,value: nvram.dmvpn_nhs_tunaddr },
	{ title: 'NHRP ' + $lang.PPTP_CLIENT_SRVIP + ' 2', name: 'dmvpn_nhs2', type: 'text', maxlen: 128, size: 32,value: nvram.dmvpn_nhs2 },
	{ title: 'NHRP ' + $lang.TUNNEL_ADDRESS + ' 2', name: 'dmvpn_nhs_tunaddr2', type: 'text', maxlen: 15, size: 32,value: nvram.dmvpn_nhs_tunaddr2 },
	{ title: 'NHRP ' + $lang.PPTP_CLIENT_PASSWD, name: 'dmvpn_nhrpkey', type: 'text', maxlen: 8, size: 32,value: nvram.dmvpn_nhrpkey },
	{ title: 'NHRP ' + $lang.NETWORK_ID, name: 'dmvpn_networkid', type: 'text', maxlen: 6, size: 8,value: nvram.dmvpn_networkid, hidden:1 },
	{ title: $lang.IPSEC1_AUTHBY, name: 'dmvpn_keyexchange', type: 'select', options: [['ikev1', 'IKE with Preshared Key'],['ikev2','IKEv2 with Preshared Key']], value: nvram.dmvpn_keyexchange },
	{ title: $lang.IPSEC1_PH1_GROUP, name: 'dmvpn_ph1_group', type: 'select', options: [['modp768', 'Group 1 - modp768'],['modp1024', 'Group 2 - modp1024'],['modp1536', 'Group 5 - modp1536']],value: nvram.dmvpn_ph1_group },
	{ title: $lang.IPSEC1_IKE_ENC, name: 'dmvpn_ike_enc', type: 'select', options: [['3des', '3DES (168-bit)'],['aes128', 'AES-128 (128-bit)'],['aes192', 'AES-192 (192-bit)'],['aes256', 'AES-256 (256-bit)']],value: nvram.dmvpn_ike_enc },
	{ title: $lang.IPSEC1_IKE_AUTH, name: 'dmvpn_ike_auth', type: 'select', options: [['md5', 'MD5 HMAC (96-bit)'],['sha', 'SHA1 HMAC (96-bit)'],['sha256', 'SHA2_256_128 HMAC (128-bit)'],['sha384', 'SHA2_384_192 HMAC (192-bit)'],['sha512', 'SHA2_512_256 HMAC (256-bit)']], value: nvram.dmvpn_ike_auth },
	{ title: $lang.IPSEC1_IKELIFETIME, name: 'dmvpn_ike_lifetime', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: nvram.dmvpn_ike_lifetime },
	{ title: $lang.IPSEC1_PH2_GROUP, name: 'dmvpn_ph2_group', type: 'select', options: [['null', 'NONE'],['modp768', 'Group 1 - modp768'],['modp1024', 'Group 2 - modp1024'],['modp1536', 'Group 5 - modp1536']],value: nvram.dmvpn_ph2_group },
	{ title: $lang.IPSEC1_ESP_ENC, name: 'dmvpn_esp_enc', type: 'select', options: [['null', 'NONE'],['3des', '3DES (168-bit)'],['aes128', 'AES-128 (128-bit)'],['aes192', 'AES-192 (192-bit)'],['aes256', 'AES-256 (256-bit)']],value: nvram.dmvpn_esp_enc },
	{ title: $lang.IPSEC1_ESP_AUTH, name: 'dmvpn_esp_auth', type: 'select', options: [['null', 'NONE'],['md5', 'MD5 HMAC (96-bit)'],['sha', 'SHA1 HMAC (96-bit)'],['sha256', 'SHA2_256_128 HMAC (128-bit)'],['sha384', 'SHA2_384_192 HMAC (192-bit)'],['sha512', 'SHA2_512_256 HMAC (256-bit)']],value: nvram.dmvpn_esp_auth },
	{ title: $lang.IPSEC1_KEYLIFE, name: 'dmvpn_keylife', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: nvram.dmvpn_keylife },
	{ title: $lang.IPSEC1_PSKKEY, name: 'dmvpn_pskkey', type: 'password', maxlen: 64, size: 32, value: nvram.dmvpn_pskkey },
	{ title: $lang.IPSEC1_DPDACTION, name: 'f_dmvpn_dpdaction', type: 'checkbox', value: (nvram.dmvpn_dpdaction == '1') },
	{ title: $lang.DETECTION_CYCLE, indent: 2, name: 'dmvpn_dpddelay', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: nvram.dmvpn_dpddelay },
	{ title: $lang.DETECTION_TIMEOUT_INTERVAL, indent: 2, name: 'dmvpn_dpdtimeout', type: 'text', maxlen: 6, size: 8, suffix: ' <i>'+ $lang.VAR_SECOND +'</i>',value: nvram.dmvpn_dpdtimeout },
	{ title: $lang.IPSEC1_CUSTOM1, name: 'dmvpn_custom1', type: 'text', maxlen: 256, size: 64, value: nvram.dmvpn_custom1 },
	{ title: $lang.IPSEC1_CUSTOM2, name: 'dmvpn_custom2', type: 'text', maxlen: 256, size: 64, value: nvram.dmvpn_custom2 },
	{ title: $lang.IPSEC1_CUSTOM3, name: 'dmvpn_custom3', type: 'text', maxlen: 256, size: 64, value: nvram.dmvpn_custom3 },
	{ title: $lang.IPSEC1_CUSTOM4, name: 'dmvpn_custom4', type: 'text', maxlen: 256, size: 64, value: nvram.dmvpn_custom4 }
]);
</script>
</div>

<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
</form>
<script type='text/javascript'>verifyFields(null, 1);</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
