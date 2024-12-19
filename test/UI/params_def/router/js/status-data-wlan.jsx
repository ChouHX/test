//<% nvram("et0macaddr,lan_proto,lan_ipaddr,dhcp_start,dhcp_num,dhcpd_startip,dhcpd_endip,lan_netmask,wl_security_mode,wl_crypto,wl_mode,wl_wds_enable,wl_hwaddr,wl_net_mode,wl_radio,wl_channel,lan_gateway,wl_ssid,wl_closed,t_model_name,t_features,pptp_dhcp,dhcp1_start,dhcp1_num,dhcpd1_startip,dhcpd1_endip,dhcp2_start,dhcp2_num,dhcpd2_startip,dhcpd2_endip,dhcp3_start,dhcp3_num,dhcpd3_startip,dhcpd3_endip,lan1_proto,lan1_ipaddr,lan1_netmask,lan2_proto,lan2_ipaddr,lan2_netmask,lan3_proto,lan3_ipaddr,lan3_netmask,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname,lan_ifnames,lan1_ifnames,lan2_ifnames,lan3_ifnames,wan_ifnames"); %>
//<% wlstats(1); %>

stats = { };

do {
	var a, b, i;
	var xifs = ['wan', 'lan', 'lan1', 'lan2', 'lan3'];

	stats.channel = [];
	stats.interference = [];
	stats.qual = [];

	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		u = wl_unit(uidx);

		a = i = wlstats[uidx].channel * 1;
		if (i < 0) i = -i;
		stats.channel.push('<a href="wlan-survey.asp">' + ((i) ? i + '' : 'Auto') +
			((wlstats[uidx].mhz) ? ' - ' + (wlstats[uidx].mhz / 1000.0).toFixed(3) + ' <small>GHz</small>' : '') + '</a>' +
			((a < 0) ? ' <small>(scanning...)</small>' : ''));
		stats.interference.push((wlstats[uidx].intf >= 0) ? ((wlstats[uidx].intf) ? 'Severe' : 'Acceptable') : '');

		a = wlstats[uidx].nbw * 1;
		wlstats[uidx].nbw = (a > 0) ? (a + ' <small>MHz</small>') : 'Auto';

		if (wlstats[uidx].radio) {
			a = wlstats[uidx].rate * 1;
			if (a > 0)
				wlstats[uidx].rate = Math.floor(a / 2) + ((a & 1) ? '.5' : '') + ' <small>Mbps</small>';
			else
				wlstats[uidx].rate = '-';

			if (wlstats[uidx].client) {
				if (wlstats[uidx].rssi == 0) a = 0;
					else a = MAX(wlstats[uidx].rssi - wlstats[uidx].noise, 0);
				stats.qual.push(a + ' <img src="bar' + MIN(MAX(Math.floor(a / 10), 1), 6) + '.gif">');
			}
			else {
				stats.qual.push('');
			}
			wlstats[uidx].noise += ' <small>dBm</small>';
			wlstats[uidx].rssi += ' <small>dBm</small>';
		}
		else {
			wlstats[uidx].rate = '';
			wlstats[uidx].noise = '';
			wlstats[uidx].rssi = '';
			stats.qual.push('');
		}

		if (wl_ifaces[uidx][6] != 1) {
			wlstats[uidx].ifstatus = '<b>Down</b>';
		} else {
			wlstats[uidx].ifstatus = 'Up';
			for (i = 0; i < xifs.length ; ++i) {
				if ((nvram[xifs[i] + '_ifnames']).indexOf(wl_ifaces[uidx][0]) >= 0) {
					wlstats[uidx].ifstatus = wlstats[uidx].ifstatus + ' (' + xifs[i].toUpperCase() + ')';
					break;
				}
			}
		}
	}
} while (0);

