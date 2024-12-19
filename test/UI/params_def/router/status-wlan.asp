<!--
-->
<title><% ident(); %> <%translate("Status");%>:<%translate("LAN Status");%></title>
<content>

<script type='text/javascript'>

//	<% nvstat(); %>

wmo = {'ap':'<%translate("Access Point");%>','sta':'<%translate("Wireless Client");%>','wet':'<%translate("Wireless Ethernet Bridge");%>','wds':'WDS'};
auth = {'disabled':'<%translate("disabled");%>','wep':'WEP','wpa_personal':'WPA(PSK) <%translate("Personal");%>','wpa_enterprise':'WPA  <%translate("Enterprise");%>','wpa2_personal':'WPA2(PSK) <%translate("Personal");%>','wpa2_enterprise':'WPA2 <%translate("Enterprise");%>','wpaX_personal':'WPA / WPA2 <%translate("Personal");%>','wpaX_enterprise':'WPA / WPA2 <%translate("Enterprise");%>','radius':'Radius'};
enc = {'tkip':'TKIP','aes':'AES','tkip+aes':'TKIP / AES'};
bgmo = {'disabled':'<%translate("disabled");%>','mixed':'<%translate("Auto");%>','b-only':'<%translate("B Only");%>','g-only':'<%translate("G Only");%>','bg-mixed':'<%translate("B/G Mixed");%>','lrs':'LRS','n-only':'<%translate("N Only");%>'};

</script>

<script type='text/javascript' src='wireless.jsx?_http_id=<% nv(http_id); %>'></script>
<script type='text/javascript' src='status-data-wlan.jsx?_http_id=<% nv(http_id); %>'></script>

<script type='text/javascript'>

show_radio = [];
for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
	if (wl_sunit(uidx)<0)
		show_radio.push((nvram['wl'+wl_fface(uidx)+'_radio'] == '1'));
}

nphy = features('11n');


function serv(service, sleep)
{
	form.submitHidden('service.cgi', { _service: service, _redirect: 'status-wlan.asp', _sleep: sleep });
}


function wlenable(uidx, n)
{
	form.submitHidden('wlradio.cgi', { enable: '' + n, _nextpage: '/#status-wlan.asp', _nextwait: n ? 6 : 3, _wl_unit: wl_unit(uidx) });
}
var ref = new TomatoRefresh('status-data.jsx', '', 0, 'status_wlan_refresh');
ref.refresh = function(text)
{
	stats = {};
	try {
		eval(text);
	}
	catch (ex) {
		stats = {};
	}
	show();
}


function c(id, htm)
{
	E(id).cells[1].innerHTML = htm;
}

function show()
{
	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			c('radio'+uidx, wlstats[uidx].radio ? '<%translate("Enabled");%>' : '<b><%translate("Disabled");%></b>');
			c('rate'+uidx, wlstats[uidx].rate);
			if (show_radio[uidx]) {
				E('b_wl'+uidx+'_enable').disabled = wlstats[uidx].radio;
				E('b_wl'+uidx+'_disable').disabled = !wlstats[uidx].radio;
			}
			c('channel'+uidx, stats.channel[uidx]);
			if (nphy) {
				c('nbw'+uidx, wlstats[uidx].nbw);
			}
			c('interference'+uidx, stats.interference[uidx]);
			elem.display('interference'+uidx, stats.interference[uidx] != '');

			if (wlstats[uidx].client) {
				c('rssi'+uidx, wlstats[uidx].rssi || '');
				c('noise'+uidx, wlstats[uidx].noise || '');
				c('qual'+uidx, stats.qual[uidx] || '');
			}
		}
		c('ifstatus'+uidx, wlstats[uidx].ifstatus || '');
	}
}

function earlyInit()
{
	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
/* WIFI-BEGIN */
		u = wl_fface(uidx);
		elem.display('wl'+u+'-title', 'sesdiv_wl_'+u, show_radio[uidx]);
/* WIFI-END */
		if (wl_sunit(uidx)<0)
			elem.display('b_wl'+uidx+'_enable', 'b_wl'+uidx+'_disable', show_radio[uidx]);
	}
	show();
}

function init()
{
	var c;
	if (((c = cookie.get('status_wlan_lan_vis')) != null) && (c != '1')) toggleVisibility("lan");
	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		u = wl_unit(uidx);
		if (((c = cookie.get('status_wlan_wl_'+u+'_vis')) != null) && (c != '1')) toggleVisibility("wl_"+u);
	}
	ref.initPage(3000, 3);
}

function toggleVisibility(whichone) {
	if (E('sesdiv_' + whichone).style.display == '') {
		E('sesdiv_' + whichone).style.display = 'none';
		E('sesdiv_' + whichone + '_showhide').innerHTML = '(show)';
		cookie.set('status_wlan_' + whichone + '_vis', 0);
	} else {
		E('sesdiv_' + whichone).style.display='';
		E('sesdiv_' + whichone + '_showhide').innerHTML = '(hide)';
		cookie.set('status_wlan_' + whichone + '_vis', 1);
	}
}
	</script>
<form id="_fom" method="post" action="tomato.cgi">
<div class="box1" id="wireless">
	<div class="box" data-box="pptp-userlist">
		<div class="heading"><%translate("LAN Status");%></div>
        <div class="content" >
			<div id="lanconfig"></div>
	</div>
</div>
</form>
			<script type='text/javascript'>
function h_countbitsfromleft(num) {
	if (num == 255 ){
		return(8);
	}
	var i = 0;
	var bitpat=0xff00; 
	while (i < 8){
		if (num == (bitpat & 0xff)){
			return(i);
		}
		bitpat=bitpat >> 1;
		i++;
	}
	return(Number.NaN);
}

function numberOfBitsOnNetMask(netmask) {
	var total = 0;
	var t = netmask.split('.');
	for (var i = 0; i<= 3 ; i++) {
		total += h_countbitsfromleft(t[i]);
	}
	return total;
}

var s='';
var t='';
MAX_BRIDGE_ID = 3;
for (var i = 0 ; i <= MAX_BRIDGE_ID ; i++) {
	var j = (i == 0) ? '' : i.toString();
	if (nvram['lan' + j + '_ifname'].length > 0) {
		if (nvram['lan' + j + '_proto'] == 'dhcp') {
			if ((!fixIP(nvram.dhcpd_startip)) || (!fixIP(nvram.dhcpd_endip))) {
				var x = nvram['lan' + j + '_ipaddr'].split('.').splice(0, 3).join('.') + '.';
				nvram['dhcpd' + j + '_startip'] = x + nvram['dhcp' + j + '_start'];
				nvram['dhcpd' + j + '_endip'] = x + ((nvram['dhcp' + j + '_start'] * 1) + (nvram['dhcp' + j + '_num'] * 1) - 1);
			}
			s += ((s.length>0)&&(s.charAt(s.length-1) != ' ')) ? '<br>' : '';
			s += nvram['dhcpd' + j + '_startip'] + ' - ' + nvram['dhcpd' + j + '_endip'];
		} else {
			s += ((s.length>0)&&(s.charAt(s.length-1) != ' ')) ? '<br>' : '';
			s += 'Disabled';
		}
		t += ((t.length>0)&&(t.charAt(t.length-1) != ' ')) ? '<br>' : '';
		t += nvram['lan' + j + '_ipaddr'] + '/' + numberOfBitsOnNetMask(nvram['lan' + j + '_netmask']);
		
	}
}
			createFieldTable('', [
			{ title: '<%translate("Router MAC Address");%>', text: nvram.et0macaddr },
				{ title: '<%translate("Router IP Addresses");%>', text: t },
				{ title: '<%translate("Gateway");%>', text: nvram.lan_gateway, ignore: nvram.wan_proto != 'disabled' },
				{ title: '<%translate("DNS");%>', rid: 'dns', text: stats.dns, ignore: nvram.wan_proto != 'disabled' },
				{ title: 'DHCP', text: s }
				], '#lanconfig', 'data-table dataonly' );
			</script>
			<script type='text/javascript'>
			var f;


            for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
                u = wl_fface(uidx);
				f='<div class="box" data-box="pptp-userlist">' + ' <div class="heading" id=\'wl'+u+'-title\'>Wireless';		
               
               if (wl_ifaces.length > 0)
                   f+='(' + wl_display_ifname(uidx) + ')</div>';
				   
			f+='<div class="content" >';
            f+='<div id=\'sesdiv_wl_'+u+'\'></div>';
			f+='</div></div>';

			 $('#wireless').append(f);
                sec = auth[nvram['wl'+u+'_security_mode']] + '';
                if (sec.indexOf('WPA') != -1) sec += ' + ' + enc[nvram['wl'+u+'_crypto']];
            
                wmode = wmo[nvram['wl'+u+'_mode']] + '';
                if ((nvram['wl'+u+'_mode'] == 'ap') && (nvram['wl'+u+'_wds_enable'] * 1)) wmode += ' + WDS';
            
                createFieldTable('', [
                { title: '<%translate("MAC Address");%>', text: nvram['wl'+u+'_hwaddr'] },
		{ title: '<%translate("Wireless Mode");%>', text: wmode },
		{ title: '<%translate("Wireless Network Mode");%>', text: bgmo[nvram['wl'+u+'_net_mode']], ignore: (wl_sunit(uidx)>=0) },
		{ title: '<%translate("Interface Status");%>', rid: 'ifstatus'+uidx, text: wlstats[uidx].ifstatus },
		{ title: '<%translate("Radio");%>', rid: 'radio'+uidx, text: (wlstats[uidx].radio == 0) ? '<b><%translate("Disabled");%></b>' : '<%translate("Enabled");%>', ignore: (wl_sunit(uidx)>=0) },
		{ title: 'SSID', text: nvram['wl'+u+'_ssid'] },
		{ title: '<%translate("Broadcast");%>', text: (nvram['wl'+u+'_closed'] == 0) ? '<%translate("Enabled");%>' : '<b><%translate("Disabled");%></b>', ignore: (nvram['wl'+u+'_mode'] != 'ap') },
		{ title: '<%translate("Security");%>', text: sec },
		{ title: '<%translate("Channel");%>', rid: 'channel'+uidx, text: stats.channel[uidx], ignore: (wl_sunit(uidx)>=0) },
		{ title: '<%translate("Channel Width");%>', rid: 'nbw'+uidx, text: wlstats[uidx].nbw, ignore: ((!nphy) || (wl_sunit(uidx)>=0)) },
		{ title: '<%translate("Interference Level");%>', rid: 'interference'+uidx, text: stats.interference[uidx], hidden: ((stats.interference[uidx] == '') || (wl_sunit(uidx)>=0)) },
		{ title: '<%translate("Rate");%>', rid: 'rate'+uidx, text: wlstats[uidx].rate, ignore: (wl_sunit(uidx)>=0) },
		{ title: '<%translate("RSSI");%>', rid: 'rssi'+uidx, text: wlstats[uidx].rssi || '', ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
		{ title: '<%translate("Noise");%>', rid: 'noise'+uidx, text: wlstats[uidx].noise || '', ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
		{ title: '<%translate("Signal Quality");%>', rid: 'qual'+uidx, text: stats.qual[uidx] || '', ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) }
              ], '#sesdiv_wl_'+u, 'data-table dataonly' );
            
                $('#wireless').append('<input type=\'button\' class=\'btn btn-primary\' onclick=\'wlenable('+uidx+', 1)\' id=\'b_wl'+uidx+'_enable\' value=\'<%translate("Enabled");%>\' >');
               $('#wireless').append('<input type=\'button\' class=\'btn btn-primary\' onclick=\'wlenable('+uidx+', 0)\' id=\'b_wl'+uidx+'_disable\' value=\'<%translate("Disabled");%>\'  >');
               // W('</div>');
            }
            </script>
    <script type="text/javascript">$('.box1').after(genStdRefresh(1,0,'ref.toggle()'));</script>
    <script type='text/javascript'>earlyInit()</script>
</content>