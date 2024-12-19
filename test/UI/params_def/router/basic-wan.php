<?PHP include 'header.php'; ?>
<script type="text/javascript">

//	<% nvram("modem_type,wl0_radio,wl0_mode,lte,l2tp_server_ip,wan_demand,ppp_demand,ppp_mlppp,ppp_idletime,ppp_passwd,ppp_redialperiod,ppp_service,ppp_username,ppp_custom,wan_dns,dnscrypt_proxy,wan1_mtu,wan1_gateway,wan1_ipaddr,wan1_netmask,wan1_get_dns,wan_proto,wan_wins,wan_aslan"); %>

function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c, d, e;
	var u, uidx;
	var wmode, sm2;


	// --- visibility ---

	var vis = {
		_wan_proto: 1,
		_ppp_username: 1,
		_ppp_passwd: 1,
		_ppp_service: 1,
		_ppp_custom: 1,
		_wan1_ipaddr: 1,
		_wan1_netmask: 1,
		_wan1_gateway: 1,
		_f_dns_1 : 1,
		_f_dns_2 : 1,
		_wan_demand: 1,
		_ppp_idletime: 1,
		_ppp_redialperiod: 1,
		_wan1_mtu: 1,
		_f_wan_islan: 0,
		_f_wan_aslan: 0,
		_f_ppp_mlppp: 1
	};

	var wan = E('_wan_proto').value;

	switch (wan) {
	case 'disabled':
		vis._ppp_username = 0;
		vis._ppp_service = 0;
		vis._ppp_custom = 0;
		vis._wan1_ipaddr = 0;
		vis._wan1_netmask = 0;
		vis._wan1_gateway = 0;
		vis._f_dns_1 = 0;
		vis._f_dns_2 = 0;
		vis._wan_demand = 0;
		vis._wan1_mtu = 0;
		vis._f_ppp_mlppp = 0;
		break;
	case 'dhcp':
		vis._wan_demand = 0;
		vis._ppp_service = 0;
		vis._ppp_username = 0;
		vis._ppp_custom = 0;
		vis._wan1_gateway = 0;
		vis._wan1_ipaddr = 0;
		vis._wan1_netmask = 0;
		vis._f_dns_1 = 0;
		vis._f_dns_2 = 0;
		vis._f_ppp_mlppp = 0;
		break;	
		case 'ppp3g':
		vis._ppp_username = 0;
		vis._ppp_service = 0;
		vis._ppp_custom = 0;
		vis._wan1_ipaddr = 0;
		vis._wan1_netmask = 0;
		vis._wan1_gateway = 0;
		vis._f_dns_1 = 0;
		vis._f_dns_2 = 0;
		vis._wan_demand = 0;
		vis._f_ppp_mlppp = 0;
		break;
	case 'pppoe':
		vis._wan1_gateway = 0;
		vis._wan1_ipaddr = 0;
		vis._wan1_netmask = 0;
		vis._f_dns_1 = 0;
		vis._f_dns_2 = 0;
		vis._ppp_custom = 0;
		break;
	case 'static':
		vis._wan_demand = 0;
		vis._ppp_service = 0;
		vis._ppp_username = 0;
		vis._ppp_custom = 0;
		vis._f_ppp_mlppp = 0;
		break;
	}

	vis._ppp_idletime = (E('_wan_demand').value == 1) && vis._wan_demand
	vis._ppp_redialperiod = !vis._ppp_idletime && vis._wan_demand;
	vis._ppp_passwd = vis._ppp_username;

	for (a in vis) {
		b = E(a);
		c = vis[a];

			b.disabled = (c != 1);
		PR(b).style.display = c ? '' : 'none';
	}


	// --- verify ---

	ferror.clear('_wan_proto');

	// IP address
	a = ['_wan1_gateway','_wan1_ipaddr'];
	for (i = a.length - 1; i >= 0; --i)
		if ((vis[a[i]]) && (!v_ip(a[i], quiet || !ok))) ok = 0;

	// netmask
	a = ['_wan1_netmask'];
	for (i = a.length - 1; i >= 0; --i)
		if ((vis[a[i]]) && (!v_netmask(a[i], quiet || !ok))) ok = 0;

	// range
	a = [['_ppp_idletime', 3, 1440],['_ppp_redialperiod', 1, 86400],['_wan1_mtu', 0, 1500]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((vis[v[0]]) && (!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}

	if(!v_ascii('_ppp_username',quiet)) return 0;
	if(!v_ascii('_ppp_passwd',quiet)) return 0;
	if(!v_ascii('_ppp_service',quiet)) return 0;
	a = ['_f_dns_1', '_f_dns_2'];
	for (i = a.length - 1; i >= 0; --i)
		if (!v_dns(a[i], quiet || !ok)) ok = 0;

	return ok;
}

function earlyInit()
{
	verifyFields(null, 1);
}

function joinAddr(a) {
	var r, i, s;

	r = [];
	for (i = 0; i < a.length; ++i) {
		s = a[i];
		if ((s != '00:00:00:00:00:00') && (s != '0.0.0.0')) r.push(s);
	}
	return r.join(' ');
}

function save()
{
	var a, b, c;
	var i;
	var u, uidx, wmode, sm2, wradio;

	if (!verifyFields(null, false)) return;

	var fom = E('_fom');

	fom.wan_islan.value = fom.f_wan_islan.checked ? 1 : 0;
	fom.wan_aslan.value = fom.f_wan_aslan.checked ? 1 : 0;
	fom.wan1_get_dns.value = joinAddr([fom.f_dns_1.value,fom.f_dns_2.value]);
	fom.ppp_mlppp.value = fom.f_ppp_mlppp.checked ? 1 : 0;

	fom._service.value = 'modem_checkdial-restart';

	//if (((nvram.wan_aslan == 0) && fom.f_wan_aslan.checked) || ((nvram.wan_aslan == 1) && !fom.f_wan_aslan.checked))
	//{
		if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
		{
			fom._service.disabled = 1;
			fom._reboot.value = '1';
			// form.submit(fom);
			return submit_form('_fom');
		}
		else
		{
			return;
		}

	//}
	//else
	//	form.submit(fom, 0);
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading">WAN / Internet</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#basic-wan.php'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='wan-restart'>
				<input type='hidden' name='_moveip' value='0'>
				<input type='hidden' name='_reboot' value='0'>

				<input type='hidden' name='wan_islan'>
				<input type='hidden' name='wan_aslan'>
<input type='hidden' name='wan1_get_dns'>
				<input type='hidden' name='ppp_mlppp'>

				<div id="wanconfig"></div>
			</form>

			<script type='text/javascript'>
if(!nvram.wan1_get_dns){
	nvram.wan1_get_dns = '';
}
dns = nvram.wan1_get_dns.split(/\s+/);
	$('#wanconfig').forms([
	{ title: $lang.CONNECTION_TYPE, name: 'wan_proto', type: 'select', options: [['disabled', $lang.VAR_CLOSE],['dhcp', $lang.DYNAMICALLY_GET_ADDRESS],['pppoe', $lang.PPPOE_DIALING],['static', $lang.STATIC_ADDRESS]],value: nvram.wan_proto},
	{ title: $lang.VAR_USER_NAME, name: 'ppp_username', type: 'text', maxlen: 60, size: 64, value: nvram.ppp_username },
	{ title: $lang.PPTP_CLIENT_PASSWD, name: 'ppp_passwd', type: 'password', maxlen: 60, size: 64, peekaboo: 1, value: nvram.ppp_passwd },
	{ title: $lang.SERVICE_NAME, name: 'ppp_service', type: 'text', maxlen: 50, size: 64, value: nvram.ppp_service },
	{ title: $lang.VAR_IP, name: 'wan1_ipaddr', type: 'text', maxlen: 15, size: 17, value: nvram.wan1_ipaddr },
	{ title: $lang.LAN_NETMASK, name: 'wan1_netmask', type: 'text', maxlen: 15, size: 17, value: nvram.wan1_netmask },
	{ title: $lang.VAR_GATEWAY, name: 'wan1_gateway', type: 'text', maxlen: 15, size: 17, value: nvram.wan1_gateway },
	{ title: $lang.CUSTOM_DIALING_OPTIONS, name: 'ppp_custom', type: 'text', maxlen: 256, size: 64, value: nvram.ppp_custom },
	{ title: $lang.DIAL_MODE, name: 'wan_demand', type: 'select', options: [['1', $lang.DIAL_ON_DEMAND],['0', $lang.LINK_RETENTION]],
		value: nvram.wan_demand },
	{ title: $lang.MAXIMUM_IDLE_TIME, indent: 2, name: 'ppp_idletime', type: 'text', maxlen: 5, size: 7, suffix: ' <i>(' + $lang.MINUTES + ')</i>',
		value: nvram.ppp_idletime },
	{ title: $lang.CHECK_INTERVAL, indent: 2, name: 'ppp_redialperiod', type: 'text', maxlen: 5, size: 7, suffix: ' <i>(' + $lang.VAR_SECOND + ')</i>',
		value: nvram.ppp_redialperiod },
	{ title: 'MTU', name: 'wan1_mtu', type: 'text', maxlen: 4, size: 6, value: nvram.wan1_mtu, suffix: ' <small>(' + $lang.IS_THE_SYSTEM_DEFAULT + ')</small>' },
	null,
	{ title: $lang.PRIMARY_DNS_SERVER, name: 'f_dns_1', type: 'text', maxlen: 21, size: 25, value: dns[0] || '0.0.0.0' },
	{ title: $lang.ALTERNATE_DNS_SERVER, name: 'f_dns_2', type: 'text', maxlen: 21, size: 25, value: dns[1] || '0.0.0.0' },
	{ title: $lang.MULTILINK_OVERLAY, name: 'f_ppp_mlppp', type: 'checkbox', value: (nvram.ppp_mlppp == 1) },
	{ title: $lang.WAN_AS_LAN, name: 'f_wan_islan', type: 'checkbox', value: (nvram.wan_islan == 1) },
	{ title: $lang.WAN_AS_LAN, name: 'f_wan_aslan', type: 'checkbox', value: (nvram.wan_aslan == 1) }
], { align: 'left' });
			</script>
            
			</div>
			</div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
	<script type="text/javascript">verifyFields(null, 1);</script>
<?PHP include 'footer.php'; ?>