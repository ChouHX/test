<?PHP include 'header.php'; ?>
	<style type="text/css">
		#lan-grid .co1,
		#lan-grid .co2,
		#lan-grid .co3,
		#lan-grid .co4,
		#lan-grid .co5,
		#lan-grid .co6,
		#lan-grid .co7 {
			text-align: center;
		}

		#lan-grid .centered {
			text-align: center;
		}

		#spin {
			visibility: hidden;
			vertical-align: middle;
		}

	</style>
	<script type="text/javascript" src="js/md5.js"></script>
	<script type="text/javascript" src="js/wireless.jsx"></script>
	<script type="text/javascript" src="js/interfaces.js"></script>
	<script type='text/javascript'>
//	<% nvram("wl_sta_hwaddr,wl_country_code,wl_country,wl_sta_proto,wl_sta_mtu,wl_sta_ipaddr,wl_sta_netmask,wl_sta_gateway,wl_sta_get_dns,wl_security_mode,wl_channel,wl_closed,wl_crypto,wl_key,wl_key1,wl_key2,wl_key3,wl_key4,wl_mode,wl_net_mode,wl_passphrase,wl_radio,wl_radius_ipaddr,wl_radius_port,wl_ssid,wl_wep_bit,wl_wpa_gtk_rekey,wl_wpa_psk,wl_radius_key,wl_auth,wl_hwaddr,t_features,wl_nbw_cap,wl_nband"); %>
//  <% wlcountries(); %>

if(!nvram.wl_ifaces){
    if(nvram.router_2){
        var arr = nvram.router_2.split('<');
        if (nvram.term_model) {
            var type = nvram.term_model.substr(0,3);
            var arr = ['R10','R20','R50','R12','R23','R21','R51','CM2','CM5'];
            if ($.inArray(type, arr) != -1) {
                var wl_ifaces = [["ra0","0",0,-1,"router_wifi_2.4G","00:00:00:00:00:00",1,4,"ap","00:00:00:00:00:00"]];
            }else{
                var wl_ifaces = [["ra0","0",0,-1,"router_wifi_2.4G","00:00:00:00:00:00",1,4,"ap","00:00:00:00:00:00"],["rai0","1",1,-1,"router_wifi_5.8G","00:00:00:00:00:00",1,4,"ap","00:00:00:00:00:00"]];
            }
        }
    }
}else{
    var wl_ifaces = nvram.wl_ifaces;
}

if(!nvram.wl_bands){
    var wl_bands = [["2","1"],["1","2"]];
}else{
    var wl_bands = nvram.wl_bands;
}
// var wl_ifaces = [
//     ["ra0","0",0,-1,"router_wifi_2.4G","34:0A:4C:23:09:29",1,4,"ap","34:0A:4C:23:09:29"],
//     ["rai0","1",1,-1,"router_wifi_5.8G","34:0A:4C:23:09:68",1,4,"ap","34:0A:4C:23:09:68"]
// ];

var wl_countries = [
    [
        "AL",
        "Albania"
    ],
    [
        "DZ",
        "Algeria"
    ],
    [
        "AR",
        "Argentina"
    ],
    [
        "AM",
        "Armenia"
    ],
    [
        "AW",
        "Aruba"
    ],
    [
        "AU",
        "Australia"
    ],
    [
        "AT",
        "Austria"
    ],
    [
        "AZ",
        "Azerbaijan"
    ],
    [
        "BH",
        "Bahrain"
    ],
    [
        "BD",
        "Bangladesh"
    ],
    [
        "BB",
        "Barbados"
    ],
    [
        "BY",
        "Belarus"
    ],
    [
        "BE",
        "Belgium"
    ],
    [
        "BZ",
        "Belize"
    ],
    [
        "BO",
        "Bolivia"
    ],
    [
        "BA",
        "Bosnia"
    ],
    [
        "BR",
        "Brazil"
    ],
    [
        "BN",
        "Brunei"
    ],
    [
        "BG",
        "Bulgaria"
    ],
    [
        "KH",
        "Cambodia"
    ],
    [
        "CA",
        "Canada"
    ],
    [
        "CL",
        "Chile"
    ],
    [
        "CN",
        "China"
    ],
    [
        "CO",
        "Colombia"
    ],
    [
        "CR",
        "Costa Rica"
    ],
    [
        "HR",
        "Croatia"
    ],
    [
        "CY",
        "Cyprus"
    ],
    [
        "CZ",
        "Czech Republic"
    ],
    [
        "DK",
        "Denmark"
    ],
    [
        "DO",
        "Dominican Republic"
    ],
    [
        "EC",
        "Ecuador"
    ],
    [
        "EG",
        "Egypt"
    ],
    [
        "SV",
        "El Salvador"
    ],
    [
        "EE",
        "Estonia"
    ],
    [
        "FI",
        "Finland"
    ],
    [
        "FR",
        "France"
    ],
    [
        "GE",
        "Georgia"
    ],
    [
        "DE",
        "Germany"
    ],
    [
        "GR",
        "Greece"
    ],
    [
        "GL",
        "Greenland"
    ],
    [
        "GD",
        "Grenada"
    ],
    [
        "GU",
        "Guam"
    ],
    [
        "GT",
        "Guatemala"
    ],
    [
        "HT",
        "Haiti"
    ],
    [
        "HN",
        "Honduras"
    ],
    [
        "HK",
        "Hong Kong"
    ],
    [
        "HU",
        "Hungary"
    ],
    [
        "IS",
        "Iceland"
    ],
    [
        "IN",
        "India"
    ],
    [
        "ID",
        "Indonesia"
    ],
    [
        "IR",
        "Iran"
    ],
    [
        "IE",
        "Ireland"
    ],
    [
        "IL",
        "Israel"
    ],
    [
        "IT",
        "Italy"
    ],
    [
        "JM",
        "Jamaica"
    ],
    [
        "JP",
        "Japan"
    ],
    [
        "JO",
        "Jordan"
    ],
    [
        "KZ",
        "Kazakhstan"
    ],
    [
        "KE",
        "Kenya"
    ],
    [
        "KR",
        "Korea"
    ],
    [
        "KW",
        "Kuwait"
    ],
    [
        "LV",
        "Latvia"
    ],
    [
        "LB",
        "Lebanon"
    ],
    [
        "LI",
        "Liechtenstein"
    ],
    [
        "LT",
        "Lithuania"
    ],
    [
        "LU",
        "Luxembourg"
    ],
    [
        "MO",
        "Macao"
    ],
    [
        "MK",
        "Macedonia"
    ],
    [
        "MY",
        "Malaysia"
    ],
    [
        "MT",
        "Malta"
    ],
    [
        "MX",
        "Mexico"
    ],
    [
        "MC",
        "Monaco"
    ],
    [
        "MA",
        "Morocco"
    ],
    [
        "NP",
        "Nepal"
    ],
    [
        "NL",
        "Netherlands"
    ],
    [
        "AN",
        "Netherlands Antilles"
    ],
    [
        "NZ",
        "New Zealand"
    ],
    [
        "NO",
        "Norway"
    ],
    [
        "OM",
        "Oman"
    ],
    [
        "PK",
        "Pakistan"
    ],
    [
        "PA",
        "Panama"
    ],
    [
        "PG",
        "Papua New Guinea"
    ],
    [
        "PE",
        "Peru"
    ],
    [
        "PH",
        "Philippines"
    ],
    [
        "PL",
        "Poland"
    ],
    [
        "PT",
        "Portuga"
    ],
    [
        "PR",
        "Puerto Rico"
    ],
    [
        "QA",
        "Qatar"
    ],
    [
        "RO",
        "Romania"
    ],
    [
        "RU",
        "Russian Federation"
    ],
    [
        "BL",
        "Saint Barthelemy"
    ],
    [
        "SA",
        "Saudi Arabia"
    ],
    [
        "SG",
        "Singapore"
    ],
    [
        "SK",
        "Slovakia"
    ],
    [
        "SI",
        "Slovenia"
    ],
    [
        "ZA",
        "South Africa"
    ],
    [
        "ES",
        "Spain"
    ],
    [
        "LK",
        "Sri Lanka"
    ],
    [
        "SE",
        "Sweden"
    ],
    [
        "CH",
        "Switzerland"
    ],
    [
        "SY",
        "Syrian Arab Republic"
    ],
    [
        "TW",
        "Taiwan"
    ],
    [
        "TH",
        "Thailand"
    ],
    [
        "TT",
        "Trinidad"
    ],
    [
        "TN",
        "Tunisia"
    ],
    [
        "TR",
        "Turkey"
    ],
    [
        "UA",
        "Ukraine"
    ],
    [
        "AE",
        "United Arab Emirates"
    ],
    [
        "GB",
        "United Kingdom"
    ],
    [
        "US",
        "United States"
    ],
    [
        "UY",
        "Uruguay"
    ],
    [
        "UZ",
        "Uzbekistan"
    ],
    [
        "VE",
        "Venezuela"
    ],
    [
        "VN",
        "Viet Nam"
    ],
    [
        "YE",
        "Yemen"
    ],
    [
        "ZW",
        "Zimbabwe"
    ],
    [
        "EU",
        "Europe"
    ],
    [
        "NA",
        "North America"
    ],
    [
        "WO",
        "Others"
    ]
];

if(wl_ifaces.length == 1)//2.4G
{
	country0=nvram.wl0_country_code;
}
else//2.4G and 5G
{
	country0=nvram.wl0_country_code;
	country1=nvram.wl1_country_code;
}
var xob = null;
var refresher = [];
var nphy = features('11n');
var acphy = features('11ac');

var ghz = [];
var bands = [];
var nm_loaded = [], ch_loaded = [], max_channel = [];

var tabs = new Array();
for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
	var name = 'wl' + uidx;
	tabs[uidx] = new Array();
	tabs[uidx][0] = name;
	tabs[uidx][1] = $lang.VAR_WIFI + '(' + wl_display_ifname(uidx) + ')';
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

	cookie.set('wlan_tab', name);
}

for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
	if (wl_sunit(uidx)<0) {
		var b;
		b = [];
		for (var i = 0; i < wl_bands[uidx].length; ++i) {
			b.push([wl_bands[uidx][i] + '', (wl_bands[uidx][i] == '1') ? '5 GHz' : '2.4 GHz']);
		}
		bands.push(b);

		ghz.push(b);

		nm_loaded.push(0);
		ch_loaded.push(0);
		max_channel.push(0);
		refresher.push(null);
	}
}

function selectedBand(uidx)
{
	return E('_f_wl'+uidx+'_nband').value;
}

function refreshNetModes(uidx)
{
	var e, i, buf, val;

	if (uidx >= wl_ifaces.length) return;
	var u = wl_unit(uidx);

	var m = [['mixed', $lang.VAR_AUTO]];
	if (selectedBand(uidx) == '1') {
		m.push(['a-only', $lang.A_ONLY]);
		if (nphy) {
			m.push(['n-only', $lang.ONLY_802N]);
		}
	}
	else {
		m.push(['b-only', $lang.ONLY_802B]);
		m.push(['g-only', $lang.ONLY_802G]);
		if (nphy) {
			m.push(['bg-mixed', $lang.B_G_MIX]);
			m.push(['n-only', $lang.ONLY_802N]);
		}
	}

	e = E('_wl'+u+'_net_mode');
	buf = '';
	val = (!nm_loaded[uidx] || (e.value + '' == '')) ? eval('nvram.wl'+u+'_net_mode') : e.value;
	if (val == 'disabled') val = 'mixed';
	for (i = 0; i < m.length; ++i)
		buf += '<option value="' + m[i][0] + '"' + ((m[i][0] == val) ? ' selected' : '') + '>' + m[i][1] + '</option>';

	e = E('__wl'+u+'_net_mode');
	buf = '<select name="wl'+u+'_net_mode" onchange="verifyFields(this, 1)" id = "_wl'+u+'_net_mode">' + buf + '</select>';
	elem.setInnerHTML(e, buf);
	nm_loaded[uidx] = 1;
}

function refreshBandWidth(uidx)
{
	var e, i, buf, val;

	if (uidx >= wl_ifaces.length) return;
	var u = wl_unit(uidx);

    var m = [['0', '20 MHz'], ['1', '40 MHz'], ['3', '80 MHz']];
    /*
	var m = [['0','20 MHz']];
	if(nphy || acphy){
		m.push(['1','40 MHz']);
	}
	if(acphy && selectedBand(uidx) == '1') {
		m.push(['3','80 MHz']);
	}
    */

	e = E('_wl'+u+'_nbw_cap');
	buf = '';
	val = (!nm_loaded[uidx] || (e.value + '' == '')) ? eval('nvram.wl'+u+'_nbw_cap') : e.value;
	for (i = 0; i < m.length; ++i)
		buf += '<option value="' + m[i][0] + '"' + ((m[i][0] == val) ? ' selected' : '') + '>' + m[i][1] + '</option>';

	elem.setInnerHTML(e, buf);
	nm_loaded[uidx] = 1;
}

function refreshChannels(uidx)
{
	if (refresher[uidx] != null) return;
	if (u >= wl_ifaces.length) return;
	var u = wl_unit(uidx);

	refresher[uidx] = new XmlHttp();
	refresher[uidx].onCompleted = function(text, xml) {
		try {
			var e, i, buf, val;

			var wl_channels = [];
			eval(text);

			ghz[uidx] = [];
			max_channel[uidx] = 0;
			for (i = 0; i < wl_channels.length; ++i) {
				ghz[uidx].push([wl_channels[i][0] + '',
					(wl_channels[i][0]) ? ((wl_channels[i][1]) ? wl_channels[i][0] + ' - ' + (wl_channels[i][1] / 1000.0).toFixed(3) + ' GHz' : wl_channels[i][0] + '') : $lang.VAR_AUTO]);
				max_channel[uidx] = wl_channels[i][0] * 1;
			}

			e = E('_wl'+u+'_channel');
			buf = '';
			val = (!ch_loaded[uidx] || (e.value + '' == '')) ? eval('nvram.wl'+u+'_channel') : e.value;
			for (i = 0; i < ghz[uidx].length; ++i)
				buf += '<option value="' + ghz[uidx][i][0] + '"' + ((ghz[uidx][i][0] == val) ? ' selected' : '') + '>' + ghz[uidx][i][1] + '</option>';

			e = E('__wl'+u+'_channel');
			buf = '<select name="wl'+u+'_channel" onchange="verifyFields(this, 1)" id = "_wl'+u+'_channel">' + buf + '</select>';
			elem.setInnerHTML(e, buf);
			ch_loaded[uidx] = 1;

			refresher[uidx] = null;
			verifyFields(null, 1);
		}
		catch (x) {
		}
		refresher[uidx] = null;
	}

	refresher[uidx].onError = function(ex) { alert(ex); refresher[uidx] = null; reloadPage(); }
	// refresher[uidx].post('update.cgi', 'exec=wlchannels&arg0=' + u + '&arg1=' + E('_wl'+uidx+'_country_code').value);
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

function random_x(max)
{
	var c = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	var s = '';
	while (max-- > 0) s += c.substr(Math.floor(c.length * Math.random()), 1);
	return s;
}

function random_psk(id)
{
	var e = E(id);
	e.value = random_x(63);
	verifyFields(null, 1);
}

function random_wep(u)
{
	E('_wl'+u+'_passphrase').value = random_x(16);
	generate_wep(u);
}

function v_wep(e, quiet)
{
	var s = e.value;
	
	if (((s.length == 5) || (s.length == 13)) && (s.length == (e.maxLength >> 1))) {
		// no checking
	}
	else {
		s = s.toUpperCase().replace(/[^0-9A-F]/g, '');
		if (s.length != e.maxLength) {
			ferror.set(e, $lang.INVALID_WEP_KEY_EXPECTING + e.maxLength + $lang.HEX_OR + (e.maxLength >> 1) + $lang.ASCII_CHARACTERS, quiet);
			return 0;
		}
	}

	e.value = s;
	ferror.clear(e);
	return 1;
}

// compatible w/ Linksys' and Netgear's (key 1) method for 128-bits
function generate_wep(u)
{
	function _wepgen(pass, i)
	{
		while (pass.length < 64) pass += pass;
		return hex_md5(pass.substr(0, 64)).substr(i, (E('_wl'+u+'_wep_bit').value == 128) ? 26 : 10);
	}

	var e = E('_wl'+u+'_passphrase');
	var pass = e.value;
	if (!v_length(e, false, 3)) return;
	E('_wl'+u+'_key1').value = _wepgen(pass, 0);
	pass += '#$%';
	E('_wl'+u+'_key2').value = _wepgen(pass, 2);
	pass += '!@#';
	E('_wl'+u+'_key3').value = _wepgen(pass, 4);
	pass += '%&^';
	E('_wl'+u+'_key4').value = _wepgen(pass, 6);
	verifyFields(null, 1);
}

function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c, d, e;
	var u, uidx;
	var wmode, sm2;

	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			u = wl_unit(uidx);
			if (focused == E('_f_wl'+u+'_nband')) {
				refreshNetModes(uidx);
				refreshChannels(uidx);
				refreshBandWidth(uidx);
			}
			else if (focused == E('_wl'+u+'_nbw_cap')) {
				refreshChannels(uidx);
			}
			else if (focused == E('_wl'+u+'_country_code')) {
				refreshChannels(uidx);
			}
		}
	}


	var wl_vis = [];
	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			a = {
			_f_wl_radio: 1,
			_f_wl_mode: 1,
			_f_wl_nband: 0,//(bands[uidx].length > 1) ? 1 : 0,
			_wl_net_mode: 1,
			_wl_ssid: 1,
			_f_wl_bcast: 1,
			_wl_channel: 1,
			_wl_nbw_cap: nphy || acphy ? 1 : 0,

			_wl_security_mode: 1,
			_wl_crypto: 1,
			_wl_wpa_psk: 1,
			_f_wl_psk_random1: 1,
			_f_wl_psk_random2: 1,
			_wl_wpa_gtk_rekey: 1,
			_wl_radius_key: 1,
			_wl_radius_ipaddr: 1,
			_wl_radius_port: 1,
			_wl_wep_bit: 1,
			_wl_passphrase: 1,
			_f_wl_wep_gen: 1,
			_f_wl_wep_random: 1,
			_wl_key1: 1,
			_wl_key2: 1,
			_wl_key3: 1,
			_wl_key4: 1,

			_wl_sta_proto: 0,
			_wl_sta_mtu: 0,
			_wl_sta_ipaddr: 0,
			_wl_sta_netmask: 0,
			_wl_sta_gateway: 0,
			_wl_sta_dns_1: 0,
			_wl_sta_dns_2: 0,
			};
			wl_vis.push(a);
		}
	}


	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			u = wl_unit(uidx);
			wmode = E('_f_wl'+u+'_mode').value;

			if (!E('_f_wl'+u+'_radio').checked) {
				for (a in wl_vis[uidx]) {
					wl_vis[uidx][a] = 2;
				}
				wl_vis[uidx]._f_wl_radio = 1;
				wl_vis[uidx]._wl_nbw_cap = nphy || acphy ? 2 : 0;
				wl_vis[uidx]._f_wl_nband = (bands[uidx].length > 1) ? 2 : 0;
			}

			switch (wmode) {
			case 'sta':
				wl_vis[uidx]._wl_sta_proto = (!E('_f_wl'+u+'_radio').checked)?2:1;
				wl_vis[uidx]._wl_sta_mtu = (!E('_f_wl'+u+'_radio').checked)?2:1;
				if(E('_wl'+u+'_sta_proto').value == 'static')
				{
					wl_vis[uidx]._wl_sta_ipaddr =  (!E('_f_wl'+u+'_radio').checked)?2:1;
					wl_vis[uidx]._wl_sta_netmask =  (!E('_f_wl'+u+'_radio').checked)?2:1;
					wl_vis[uidx]._wl_sta_gateway =  (!E('_f_wl'+u+'_radio').checked)?2:1;
					wl_vis[uidx]._wl_sta_dns_1 =  (!E('_f_wl'+u+'_radio').checked)?2:1;
					wl_vis[uidx]._wl_sta_dns_2 =  (!E('_f_wl'+u+'_radio').checked)?2:1;
				}
				else
				{
					wl_vis[uidx]._wl_sta_ipaddr = 0;
					wl_vis[uidx]._wl_sta_netmask = 0;
					wl_vis[uidx]._wl_sta_gateway = 0;
					wl_vis[uidx]._wl_sta_dns_1 = 0;
					wl_vis[uidx]._wl_sta_dns_2 = 0;
				}
			case 'wet':
				wl_vis[uidx]._f_wl_bcast = 0;
				wl_vis[uidx]._wl_channel = 0;
				wl_vis[uidx]._wl_nbw_cap = 0;
				break;
			default:
				wl_vis[uidx]._wl_sta_proto = 0;
				wl_vis[uidx]._wl_sta_mtu = 0;
				wl_vis[uidx]._wl_sta_ipaddr = 0;
				wl_vis[uidx]._wl_sta_netmask = 0;
				wl_vis[uidx]._wl_sta_gateway = 0;
				wl_vis[uidx]._wl_sta_dns_1 = 0;
				wl_vis[uidx]._wl_sta_dns_2 = 0;
				break;
			}

			sm2 = E('_wl'+u+'_security_mode').value;
			switch (sm2) {
			case 'disabled':
				wl_vis[uidx]._wl_crypto = 0;
				wl_vis[uidx]._wl_wep_bit = 0;
				wl_vis[uidx]._wl_wpa_psk = 0;
				wl_vis[uidx]._wl_radius_key = 0;
				wl_vis[uidx]._wl_radius_ipaddr = 0;
				wl_vis[uidx]._wl_wpa_gtk_rekey = 0;
				break;
			case 'wep':
				wl_vis[uidx]._wl_crypto = 0;
				wl_vis[uidx]._wl_wpa_psk = 0;
				wl_vis[uidx]._wl_radius_key = 0;
				wl_vis[uidx]._wl_radius_ipaddr = 0;
				wl_vis[uidx]._wl_wpa_gtk_rekey = 0;
				break;
			case 'radius':
				wl_vis[uidx]._wl_crypto = 0;
				wl_vis[uidx]._wl_wpa_psk = 0;
				break;
			default:	// wpa*
				wl_vis[uidx]._wl_wpa_gtk_rekey = 0;
				wl_vis[uidx]._wl_wep_bit = 0;
				if (sm2.indexOf('personal') != -1) {
					wl_vis[uidx]._wl_radius_key = 0;
					wl_vis[uidx]._wl_radius_ipaddr = 0;
				}
				else {
					wl_vis[uidx]._wl_wpa_psk = 0;
				}
				break;
			}

			if (wl_vis[uidx]._wl_nbw_cap != 0) {
				switch (E('_wl'+u+'_net_mode').value) {
				case 'b-only':
				case 'g-only':
				case 'a-only':
				case 'bg-mixed':
					wl_vis[uidx]._wl_nbw_cap = 2;
					if (E('_wl'+u+'_nbw_cap').value != '0') {
						E('_wl'+u+'_nbw_cap').value = 0;
						refreshChannels(uidx);
					}
					break;
				}
				// avoid Enterprise-TKIP with 40MHz
				if ((sm2 == 'wpa_enterprise') && (E('_wl'+u+'_crypto').value == 'tkip')) {
					wl_vis[uidx]._wl_nbw_cap = 2;
					if (E('_wl'+u+'_nbw_cap').value != '0') {
						E('_wl'+u+'_nbw_cap').value = 0;
						refreshChannels(uidx);
					}
				}
			}

			wl_vis[uidx]._f_wl_psk_random1 = wl_vis[uidx]._wl_wpa_psk;
			wl_vis[uidx]._f_wl_psk_random2 = wl_vis[uidx]._wl_radius_key;
			wl_vis[uidx]._wl_radius_port = wl_vis[uidx]._wl_radius_ipaddr;
			wl_vis[uidx]._wl_key1 = wl_vis[uidx]._wl_key2 = wl_vis[uidx]._wl_key3 = wl_vis[uidx]._wl_key4 = wl_vis[uidx]._f_wl_wep_gen = wl_vis[uidx]._f_wl_wep_random = wl_vis[uidx]._wl_passphrase = wl_vis[uidx]._wl_wep_bit;
		}
	} // for each wl_iface


	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		
		if(wl_ifaces[uidx][0].indexOf('.') < 0) {	
			for (a in wl_vis[uidx]) {	
				i = 3;
				if (a.substr(0, 6) == '_f_wl_') i = 5;
				b = E(a.substr(0, i) + wl_unit(uidx) + a.substr(i, a.length));
				c = wl_vis[uidx][a];
                if (b && a != '_wl_nbw_cap') {
				    b.disabled = (c != 1);
				    PR(b).style.display = c ? '' : 'none';
                }
			}	
		}		
	}

	// --- verify ---
	var wlclnt = 0;
	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			u = wl_unit(uidx);
			wmode = E('_f_wl'+u+'_mode').value;
			sm2 = E('_wl'+u+'_security_mode').value;

/* REMOVE-BEGIN
			if ((wl_vis[uidx]._f_wl_mode == 1) && (wmode != 'ap') && (sm2.substr(0, 4) == 'wpa2')) {
				ferror.set('_wl'+u+'_security_mode', 'WPA2 is supported only in AP mode.', quiet || !ok);
				ok = 0;
			}
			else ferror.clear('_wl'+u+'_security_mode');
REMOVE-END */

			// --- N standard does not support WPA+TKIP ---
			a = E('_wl'+u+'_crypto');
			switch (E('_wl'+u+'_net_mode').value) {
			case 'mixed':
			case 'n-only':
				if ((nphy || acphy) && (a.value == 'tkip') && (sm2.indexOf('wpa') != -1)) {
					ferror.set(a, $lang.WLAN_VIF_INFO2, quiet || !ok);
					ok = 0;
				}
				else ferror.clear(a);
				break;
			}

			a = E('_wl'+u+'_net_mode');
			ferror.clear(a);
			b = E('_f_wl'+u+'_mode');
			ferror.clear(b);
			if ((wmode == 'sta') || (wmode == 'wet')) {
				++wlclnt;
				if (wlclnt > 1) {
					ferror.set(b, $lang.WLAN_VIF_INFO3, quiet || !ok);
					ok = 0;
				}
				else if (a.value == 'n-only') {
					ferror.set(a, $lang.WLAN_NETWORK_INFO1, quiet || !ok);
					ok = 0;
				}
			}

			a = E('_wl'+u+'_wpa_psk');
			ferror.clear(a);
			if (wl_vis[uidx]._wl_wpa_psk == 1) {
				if ((a.value.length < 8) || ((a.value.length == 64) && (a.value.search(/[^0-9A-Fa-f]/) != -1))) {
					ferror.set('_wl'+u+'_wpa_psk', $lang.WLAN_NETWORK_INFO2, quiet || !ok);
					ok = 0;
				}
			}
			if(!v_ascii('_wl'+u+'_wpa_psk',quiet || !ok)) ok = 0;

			ferror.clear('_wl'+u+'_channel');

		}
	}


	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			u = wl_unit(uidx);

			// IP address
			a = ['_radius_ipaddr'];
			for (i = a.length - 1; i >= 0; --i) {
				if ((wl_vis[uidx]['_wl'+a[i]]) && (!v_ip('_wl'+u+a[i], quiet || !ok))) ok = 0;
			}

			a = ['_sta_gateway','_sta_ipaddr'];
			for (i = a.length - 1; i >= 0; --i) {
				if ((wl_vis[uidx]['_wl'+a[i]]) && (!v_ip('_wl'+u+a[i], quiet || !ok))) ok = 0;
			}
			a = ['_sta_netmask'];
			for (i = a.length - 1; i >= 0; --i)
				if ((wl_vis[uidx]['_wl'+a[i]]) && (!v_netmask('_wl'+u+a[i], quiet || !ok))) ok = 0;

			a = ['_sta_dns_1', '_sta_dns_2'];
			for (i = a.length - 1; i >= 0; --i)
				if ((wl_vis[uidx]['_wl'+a[i]]) && (!v_dns('_wl'+u+a[i], quiet || !ok))) ok = 0;

			// range
			a = [['_wpa_gtk_rekey', 60, 7200], ['_radius_port', 1, 65535], ['_sta_mtu', 0, 1500]];
			for (i = a.length - 1; i >= 0; --i) {
				v = a[i];
				if ((wl_vis[uidx]['_wl'+v[0]]) && (!v_range('_wl'+u+v[0], quiet || !ok, v[1], v[2]))) ok = 0;
			}

			// length
			a = [['_ssid', 1], ['_radius_key', 1]];
			for (i = a.length - 1; i >= 0; --i) {
				v = a[i];
				if ((wl_vis[uidx]['_wl'+v[0]]) && (!v_length('_wl'+u+v[0], quiet || !ok, v[1], E('_wl'+u+v[0]).maxlength))) ok = 0;
				if ((wl_vis[uidx]['_wl'+v[0]]) && !ok) ok = 0;
			}

			if (wl_vis[uidx]._wl_key1) {
				a = (E('_wl'+u+'_wep_bit').value == 128) ? 26 : 10;
				for (i = 1; i <= 4; ++i) {
					b = E('_wl'+u+'_key' + i);
					b.maxLength = a;
					if ((b.value.length > 0) || (E('_f_wl'+u+'_wepidx_' + i).checked)) {
						if (!v_wep(b, quiet || !ok)) ok = 0;
						if (!v_ascii('_wl'+u+'_key' + i, quiet || !ok)) ok = 0;
					}
					else ferror.clear(b);
				}
			}
		}
	}

	if(wl_ifaces.length == 2)//2.4G and 5G
	{
		//2.4G config same sa 5G at countrycode
		if(country0 != E('_wl0_country_code').value)
		{
			country0 = E('_wl0_country_code').value;
			country1 = E('_wl0_country_code').value;
			E('_wl1_country_code').value = E('_wl0_country_code').value;
		}
		if(country1 != E('_wl1_country_code').value)
		{
			country1 = E('_wl1_country_code').value;
			country1 = E('_wl1_country_code').value;
			E('_wl0_country_code').value = E('_wl1_country_code').value;
		}
	}

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
	for (uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			u = wl_unit(uidx);
			wmode = E('_f_wl'+u+'_mode').value;
			if(wmode == 'sta')
			{
				fom['wl'+u+'_sta_get_dns'].value = joinAddr([E('_wl'+u+'_sta_dns_1').value,E('_wl'+u+'_sta_dns_2').value]);
			}
			sm2 = E('_wl'+u+'_security_mode').value;
			wradio = E('_f_wl'+u+'_radio').checked;

			E('_wl'+u+'_nband').value = selectedBand(uidx);

			E('_wl'+u+'_mode').value = wmode;
			E('_wl'+u+'_radio').value = wradio ? 1 : 0;
			E('_wl'+u+'_auth').value = eval('nvram.wl'+u+'_auth');

			e = E('_wl'+u+'_akm');
			switch (sm2) {
			case 'disabled':
			case 'radius':
			case 'wep':
				e.value = '';
				break;
			default:
				c = [];

				if (sm2.indexOf('personal') != -1) {
					if (sm2.indexOf('wpa2_') == -1) c.push('psk');
					if (sm2.indexOf('wpa_') == -1) c.push('psk2');
				}
				else {
					if (sm2.indexOf('wpa2_') == -1) c.push('wpa');
					if (sm2.indexOf('wpa_') == -1) c.push('wpa2');
				}
				c = c.join(' ');
				e.value = c;
				break;
			}
			E('_wl'+u+'_auth_mode').value = (sm2 == 'radius') ? 'radius' : 'none';
			E('_wl'+u+'_wep').value = ((sm2 == 'radius') || (sm2 == 'wep')) ? 'enabled': 'disabled';

			if (sm2.indexOf('wpa') != -1) E('_wl'+u+'_auth').value = 0;

			E('_wl'+u+'_nreqd').value = 0;
			E('_wl'+u+'_gmode').value = 1;
			E('_wl'+u+'_nmode').value = 0;
			E('_wl'+u+'_nmcsidx').value = -2; // Legacy Rate
			E('_wl'+u+'_nbw').value = 0;
			switch (E('_wl'+u+'_net_mode').value) {
			case 'b-only':
				E('_wl'+u+'_gmode').value = 0;
				break;
			case 'g-only':
				E('_wl'+u+'_gmode').value = 4;
				break;
			case 'bg-mixed':
				break;
			case 'a-only':
				E('_wl'+u+'_nmcsidx').value = -1; // Auto
				break;
			case 'n-only':
				if (selectedBand(uidx) == '1') { // 5 GHz
					E('_wl'+u+'_nmode').value = -1;
					E('_wl'+u+'_nmcsidx').value = -1;
				} else {
					E('_wl'+u+'_nmode').value = 1;
					E('_wl'+u+'_nmcsidx').value = 32;
				}
				E('_wl'+u+'_nreqd').value = 1;
				break;
			default: // Auto
				E('_wl'+u+'_nmode').value = -1;
				E('_wl'+u+'_nmcsidx').value = -1;
				break;
			}

			if (E('_wl'+u+'_nmode').value != 0) {
				E('_wl'+u+'_nbw').value = (E('_wl'+u+'_nbw_cap').value == 0) ? 20 : ((E('_wl'+u+'_nbw_cap').value== 3) ? 80:40);
			}

			E('_wl'+u+'_closed').value = E('_f_wl'+u+'_bcast').checked ? 0 : 1;

			a = fields.radio.selected(eval('fom.f_wl'+u+'_wepidx'));
			if (a) E('_wl'+u+'_key').value = a.value;
			E('_wl'+u+'_country').value = E('_wl'+u+'_country_code').value;
		}
	}
	if(1)//confirm("<%translate("wlan_network_info4");%>?"))
	{
		fom._service.disabled = 1;
		fom._reboot.value = '1';
		// form.submit(fom, 0);
        return submit_form('_fom');
	}
}

function init()
{
	if(wl_ifaces.length == 1)
	{
		tabSelect(tabs[0][0]);
	}
	else
	{
		tabSelect(cookie.get('wlan_tab') || tabs[0][0]);
	}
	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0) {
			refreshNetModes(uidx);
			refreshChannels(uidx);
			refreshBandWidth(uidx);
		}
	}
}
	</script>

<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#wlan-network.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_service' value='*'>
<input type='hidden' name='_moveip' value='0'>
<input type='hidden' name='_reboot' value='0'>

<div id="wifi-interfaces"></div>
<script type="text/javascript">
var htmlOut = tabCreate.apply(this, tabs);
var wl_hwaddr = [];
for(var uidx = 0; uidx < wl_ifaces.length; ++uidx)
{
	if(wl_sunit(uidx)<0)
	{
		var u = wl_unit(uidx);
		if ((nvram['wl'+u+'_mode'] == 'ap'))
		{
			wl_hwaddr[u] = nvram['wl'+u+'_hwaddr'];
		}
		else
		{
			wl_hwaddr[u] = nvram['wl'+u+'_sta_hwaddr'];
		}
        if(!nvram['wl'+u+'_sta_get_dns']){
            nvram['wl'+u+'_sta_get_dns'] = '';
        }
		dns = nvram['wl'+u+'_sta_get_dns'].split(/\s+/);
		htmlOut += '<div id=\'wl'+u+'-tab\'>';
		htmlOut += ('<input type="hidden" id="_wl'+u+'_mode" name="wl'+u+'_mode">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_nband" name="wl'+u+'_nband">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_radio" name="wl'+u+'_radio">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_closed" name="wl'+u+'_closed">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_key" name="wl'+u+'_key">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_gmode" name="wl'+u+'_gmode">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_akm" name="wl'+u+'_akm">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_auth" name="wl'+u+'_auth">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_auth_mode" name="wl'+u+'_auth_mode">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_wep" name="wl'+u+'_wep">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_nmode" name="wl'+u+'_nmode">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_nmcsidx" name="wl'+u+'_nmcsidx">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_nreqd" name="wl'+u+'_nreqd">');
		htmlOut += ('<input type="hidden" id="_wl'+u+'_nbw" name="wl'+u+'_nbw">');
					htmlOut += ('<input type=\'hidden\' id=\'_wl'+u+'_country\' name=\'wl'+u+'_country\'>');
		htmlOut += ('<input type=\'hidden\' id=\'_wl'+u+'_sta_get_dns\' name=\'wl'+u+'_sta_get_dns\'>');
		htmlOut += '<div class="box" data-box="network_wl' + u +'">';
		htmlOut += '<div class="content wifi-' + uidx + '">';
		f = [
			{ title: $lang.ENABLE_WIRELESS, name: 'f_wl'+u+'_radio', type: 'checkbox', value: (eval('nvram.wl'+u+'_radio') == '1') && (eval('nvram.wl'+u+'_net_mode') != 'disabled') },
			//{ title: '<%translate("MAC Address");%>', text: '<a href="wlan-network.asp">' + eval('nvram.wl'+u+'_hwaddr') + '</a>' },
			{ title: $lang.VAR_DEVICE_MAC, text: wl_hwaddr[u] },
			{ title: $lang.WIRELESS_MODE, name: 'f_wl'+u+'_mode', type: 'select', options: [['ap', $lang.WIRELESS_ACCESS_POINT_AP],['sta', $lang.WIRELESS_CLIENT],['wet', $lang.WIRELESS_BRIDGE]],	value: eval('nvram.wl'+u+'_mode') },
			{ title: $lang.RADIO_BAND, name: 'f_wl'+u+'_nband', type: 'select', options: bands[uidx],value: eval('nvram.wl'+u+'_nband') || '0' == '0' ? bands[uidx][0][0] : eval('nvram.wl'+u+'_nband') },
			{ title: $lang.WIRELESS_NETWORK_MODE, name: 'wl'+u+'_net_mode', type: 'select',value: (eval('nvram.wl'+u+'_net_mode') == 'disabled') ? 'mixed' : eval('nvram.wl'+u+'_net_mode'), options: [], prefix: '<span id="__wl'+u+'_net_mode">', suffix: '</span>' },
			{ title: 'SSID', name: 'wl'+u+'_ssid', type: 'text', maxlen: 32, size: 34, value: eval('nvram.wl'+u+'_ssid') },
			{ title: $lang.BROADCAST_SSID, indent: 2, name: 'f_wl'+u+'_bcast', type: 'checkbox', value: (eval('nvram.wl'+u+'_closed') == '0') },
			// { title: $lang.CHANNEL, name: 'wl'+u+'_channel', type: 'select', options: ghz[uidx], prefix: '<div class="pull-left" id="__wl'+u+'_channel">', value: eval('nvram.wl'+u+'_channel') },
			{ title: $lang.COUNTRY_REGION, name: 'wl'+u+'_country_code', type: 'select',options: wl_countries, value: nvram['wl'+u+'_country_code'] },
			{ title: $lang.BANDWIDTH, name: 'wl'+u+'_nbw_cap', type: 'select', options: [], value: eval('nvram.wl'+u+'_nbw_cap'), prefix: '<span id="__wl'+u+'_nbw_cap">', suffix: '</span>' },
			{ title: $lang.SECURITY_OPTIONS, name: 'wl'+u+'_security_mode', type: 'select', options: [['disabled', $lang.VAR_NONE],['wpa_personal','WPA Personal'],['wpa2_personal','WPA2 Personal'],['wpaX_personal','WPA / WPA2 Personal']],value: eval('nvram.wl'+u+'_security_mode') },
			{ title: $lang.ENCRYPT_TYPE, indent: 2, name: 'wl'+u+'_crypto', type: 'select',	options: [['aes','AES'],['tkip','TKIP']], value: eval('nvram.wl'+u+'_crypto') },
			{ title: $lang.SHARED_KEY, indent: 2, name: 'wl'+u+'_wpa_psk', type: 'password', maxlen: 64, size: 48, peekaboo: 1, suffix: ' <input type="button" id="_f_wl'+u+'_psk_random1" value="'+ $lang.RANDOM_GENERATE +'" onclick="random_psk(\'_wl'+u+'_wpa_psk\')">',	value: eval('nvram.wl'+u+'_wpa_psk') },
			{ title: $lang.SHARED_KEY, indent: 2, name: 'wl'+u+'_radius_key', type: 'password', maxlen: 64, size: 48, peekaboo: 1, suffix: ' <input type="button" id="_f_wl'+u+'_psk_random2" value="'+ $lang.RANDOM_GENERATE +'" onclick="random_psk(\'_wl'+u+'_radius_key\')">', value: eval('nvram.wl'+u+'_radius_key') },
			{ title: $lang.GROUP_KEY_UPDATE, indent: 2, name: 'wl'+u+'_wpa_gtk_rekey', type: 'text', maxlen: 4, size: 6, suffix: ' <i>('+ $lang.VAR_SECOND +')</i>', value: eval('nvram.wl'+u+'_wpa_gtk_rekey') },
			{ title: $lang.RADIUS_SERVER, indent: 2, multi: [
				{ name: 'wl'+u+'_radius_ipaddr', type: 'text', maxlen: 15, size: 17, value: eval('nvram.wl'+u+'_radius_ipaddr') },
				{ name: 'wl'+u+'_radius_port', type: 'text', maxlen: 5, size: 7, prefix: ' : ', value: eval('nvram.wl'+u+'_radius_port') } ] },
			{ title: $lang.ENCRYPT_TYPE, indent: 2, name: 'wl'+u+'_wep_bit', type: 'select', options: [['128','128-bits'],['64','64-bits']], value: eval('nvram.wl'+u+'_wep_bit') },
			{ title: $lang.PASSWORD_SEED, indent: 2, name: 'wl'+u+'_passphrase', type: 'text', maxlen: 15, size: 17,suffix: ' <input type="button" id="_f_wl'+u+'_wep_gen" value="'+ $lang.GENERATE +'" onclick="generate_wep('+u+')"> <input type="button" id="_f_wl'+u+'_wep_random" value="'+ $lang.RANDOM_GENERATE +'" onclick="random_wep('+u+')">',
			value: eval('nvram.wl'+u+'_passphrase') }
		];

		for (i = 1; i <= 4; ++i)	{
		f.push(
		{ title: ($lang.PPTP_CLIENT_PASSWD + i), indent: 2, name: ('wl'+u+'_key' + i), type: 'text', maxlen: 26, size: 34,
		suffix: '<input type="radio" onchange="verifyFields(this,1)" onclick="verifyFields(this,1)" name="f_wl'+u+'_wepidx" id="_f_wl'+u+'_wepidx_' + i + '" value="' + i + '"' + ((eval('nvram.wl'+u+'_key') == i) ? ' checked>' : '>'),
		value: nvram['wl'+u+'_key' + i] });
		}

		f.push(
			{ title: $lang.CONNECTION_TYPE, name: 'wl'+u+'_sta_proto', type: 'select', options: [['dhcp', $lang.DYNAMICALLY_GET_ADDRESS],['static', $lang.STATIC_ADDRESS]],value: nvram['wl'+u+'_sta_proto']},
			{ title: 'MTU', name: 'wl'+u+'_sta_mtu', type: 'text', maxlen: 15, size: 17, value: nvram['wl'+u+'_sta_mtu'], suffix: ' <small>( '+ $lang.IS_THE_SYSTEM_DEFAULT +' )</small>'},
			{ title: $lang.VAR_IP, name: 'wl'+u+'_sta_ipaddr', type: 'text', maxlen: 15, size: 17, value: nvram['wl'+u+'_sta_ipaddr']},
			{ title: $lang.LAN_NETMASK,name: 'wl'+u+'_sta_netmask',type: 'text',maxlen: 15, size: 17, value: nvram['wl'+u+'_sta_netmask']},
			{ title: $lang.VAR_GATEWAY, name: 'wl'+u+'_sta_gateway', type: 'text', maxlen: 15, size: 17, value: nvram['wl'+u+'_sta_gateway']},
			{ title: $lang.PRIMARY_DNS_SERVER, name: 'wl'+u+'_sta_dns_1', type: 'text', maxlen: 21, size: 25, value: dns[0] || '0.0.0.0' },
			{ title: $lang.ALTERNATE_DNS_SERVER, name: 'wl'+u+'_sta_dns_2', type: 'text', maxlen: 21, size: 25, value: dns[1] || '0.0.0.0' }
		);
		htmlOut += createFormFields(f);
		htmlOut += '</div></div></div>';
	}
}
// for each wlif
// Write HTML
htmlOut +='</ul><div class=\'tabs-bottom\'></div>';
$('#wifi-interfaces').append(htmlOut);
</script>
<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
</form>
<script type="text/javascript"> init();earlyInit();</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
