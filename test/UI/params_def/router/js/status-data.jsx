/*
	Tomato GUI
	Copyright (C) 2006-2010 Jonathan Zarate
	http://www.polarcloud.com/tomato/

	For use with Tomato Firmware only.
	No part of this file may be used without permission.
*/

//<% nvram("wl_sta_hwaddr,linkschedule,router_sn,router_type,lte,router_hw,dualsim,wan_iface,ppp_demand,cellManual,cellConMode,modem_imei,sim_state,sim_flag,sim_ccid,lac_ci,cell_network,cops,modem_type,modem_state,main_remain,backup_remain,csq,ppp_get_ip,pptp_server_ip,router_name,wan_domain,wan_gateway,wan_gateway_get,wan_get_domain,wan_hostname,wan_hwaddr,wan_ipaddr,wan_netmask,wan_proto,wan_run_mtu,et0macaddr,wan_ifnames,wl_mode,vpn_mode,vpn_client_up,vpn_client_rip,vpn_client_lip,ipsec1_esp,ipsec1_ike,ipsec1_mode,ipsec1_ph1,ipsec1_ph2,ipsec1_recv,ipsec1_send,ipsec2_esp,ipsec2_ike,ipsec2_mode,ipsec2_ph1,ipsec2_ph2,ipsec2_recv,ipsec2_send,lan_proto,lan_ipaddr,dhcp_start,dhcp_num,dhcpd_startip,dhcpd_endip,lan_netmask,wl_security_mode,wl_crypto,wl_mode,wl_wds_enable,wl_hwaddr,wl_net_mode,wl_radio,wl_channel,lan_gateway,wl_ssid,wl_closed,t_model_name,t_features,dhcp1_start,dhcp1_num,dhcpd1_startip,dhcpd1_endip,dhcp2_start,dhcp2_num,dhcpd2_startip,dhcpd2_endip,dhcp3_start,dhcp3_num,dhcpd3_startip,dhcpd3_endip,lan1_proto,lan1_ipaddr,lan1_netmask,lan2_proto,lan2_ipaddr,lan2_netmask,lan3_proto,lan3_ipaddr,lan3_netmask,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname,lan_ifnames,lan1_ifnames,lan2_ifnames,lan3_ifnames,wan_ifnames,tomatoanon_enable,tomatoanon_answer,lan_desc,wan_ppp_get_ip,wan_pptp_dhcp,wan_pptp_server_ip,wan_ipaddr_buf,wan_gateway,wan_gateway_get,wan_get_domain,wan_hwaddr,wan_ipaddr,wan_netmask,wan_proto,wan_run_mtu,wan_sta,wan2_ppp_get_ip,wan2_pptp_dhcp,wan2_pptp_server_ip,wan2_ipaddr_buf,wan2_gateway,wan2_gateway_get,wan2_get_domain,wan2_hwaddr,wan2_ipaddr,wan2_netmask,wan2_proto,wan2_run_mtu,wan2_sta,wan3_ppp_get_ip,wan3_pptp_dhcp,wan3_pptp_server_ip,wan3_ipaddr_buf,wan3_gateway,wan3_gateway_get,wan3_get_domain,wan3_hwaddr,wan3_ipaddr,wan3_netmask,wan3_proto,wan3_run_mtu,wan3_sta,wan4_ppp_get_ip,wan4_pptp_dhcp,wan4_pptp_server_ip,wan4_ipaddr_buf,wan4_gateway,wan4_gateway_get,wan4_get_domain,wan4_hwaddr,wan4_ipaddr,wan4_netmask,wan4_proto,wan4_run_mtu,wan4_sta,mwan_num,pptp_client_enable,pptp_client_ipaddr,pptp_client_netmask,pptp_client_gateway,pptp_client_get_dns,pptp_client_srvsub,pptp_client_srvsubmsk,router_name,ipsec1_esp,ipsec1_ike,ipsec1_mode,ipsec1_ph1,ipsec1_ph2,ipsec1_recv,ipsec1_send,ipsec2_esp,ipsec2_ike,ipsec2_mode,ipsec2_ph1,ipsec2_ph2,ipsec2_recv,ipsec2_send,ipsec1_active,ipsec2_active,enable_modem"); %>
//<% uptime(); %>
//<% sysinfo(); %>
//<% wlstats(1); %>
//<% version(0); %>
//    <% nvstat(); %>
//    <% etherstates(); %>
// <% vpnstatus(); %>
// <% activelinks(); %>
// <% ethinfo(); %>
// <% modeminfo(); %>
// <% stainfo(); %>
// <% waninfo(); %>
stats = { };
do {
	var a, b, i;
/* MULTIWAN-BEGIN */
	var xifs = ['wan', 'lan', 'lan1', 'lan2', 'lan3', 'wan2', 'wan3', 'wan4'];
/* MULTIWAN-END */

/* DUALWAN-BEGIN */
	var xifs = ['wan', 'lan', 'lan1', 'lan2', 'lan3', 'wan2'];
/* DUALWAN-END */

	stats.anon_enable = nvram.tomatoanon_enable;
	stats.anon_answer = nvram.tomatoanon_answer;
	stats.schedule_alert = 0;
	if(activelinks.length > 1)
	{
		stats.schedule_alert = 1;
		for(var j=0;(j < activelinks.length) && (stats.schedule_alert == 1);j++)
		{
			var nv = nvram.linkschedule.split('>');
			for(var k=0;k < nv.length;k++)
			{
				var t = nv[k].split('<');
				if(t.length == 6 && t[0] == 1)
				{
					if((activelinks[j].name == t[1]) || (activelinks[j].name == t[2]))
					{
						stats.schedule_alert = 0;
						break;
					}
				}
			}
		}
	}

	stats.lan_desc = nvram.lan_desc;

	if (typeof(last_wan_proto) == 'undefined') {
		last_wan_proto = nvram.wan_proto;
	}
	else if (last_wan_proto != nvram.wan_proto) {
		reloadPage();
	}
	stats.firmware = version_d.firmware;
	stats.hardware = version_d.hardware;
	stats.flashsize = sysinfo.flashsize+'MB';
	stats.cpumhz = sysinfo.cpuclk+'MHz';
	stats.cputemp = sysinfo.cputemp+'°';
	stats.systemtype = sysinfo.systemtype;
	stats.cpuload = ((sysinfo.loads[0] / 65536.0).toFixed(2) + '<small> / </small> ' +
		(sysinfo.loads[1] / 65536.0).toFixed(2) + '<small> / </small>' +
		(sysinfo.loads[2] / 65536.0).toFixed(2));
	stats.freqcpu = nvram.clkfreq;
	stats.uptime = sysinfo.uptime_s;

	a = sysinfo.totalram;
	b = sysinfo.totalfreeram;
	stats.memory = scaleSize(a - b) + ' <small>/</small> ' + scaleSize(a) + ' (' + ((a - b) / a * 100.0).toFixed(2) + '%)';
	stats.memoryperc = ((a-b) / a * 100.0).toFixed(2) + '%';

	if (sysinfo.totalswap > 0) {
		a = sysinfo.totalswap;
		b = sysinfo.freeswap;
		stats.swap = scaleSize(a - b) + ' <small>/</small> ' + scaleSize(a) + ' (' + ((a - b) / a * 100.0).toFixed(2) + '%)';
		stats.swapperc = ((a - b) / a * 100.0).toFixed(2) + '%';

	} else
		stats.swap = '';

	stats.time = '<% time(); %>';
	stats.wanup = '<% wanup(); %>' == '1';
	stats.wanuptime = '<% link_uptime(); %>';
	stats.wanlease = '<% dhcpc_time(); %>';
	stats.main_remain = nvram.main_remain + ' <% translate("Minutes"); %>';
	stats.backup_remain = nvram.backup_remain + ' <% translate("Minutes"); %>';
	//<% dns(); %>
	stats.dns = dns.join(', ');
	if(nvram.csq == '')
		stats.csq = '0';
	else
		stats.csq = nvram.csq;
	if(stats.csq > 100)
		stats.csq += ' <img src="bar' + MIN(MAX(Math.floor((nvram.csq - 100) / 16), 1), 6) + '.gif">';
	else
		stats.csq += ' <img src="bar' + MIN(MAX(Math.floor(nvram.csq / 5), 1), 6) + '.gif">';
	stats.lac_ci = nvram.lac_ci;
	stats.cell_network = nvram.cell_network;
	stats.cops = nvram.cops;
	stats.vpn_mode = nvram.vpn_mode;
	stats.vpn_client_up = nvram.vpn_client_up;
	stats.vpn_client_lip = nvram.vpn_client_lip;
	stats.vpn_client_rip = nvram.vpn_client_rip;
	if (nvram.ipsec1_mode == '1')
	{
		stats.ipsec1_mode = 'Enable';
		stats.ipsec1_ph1 = nvram.ipsec1_ph1;
		stats.ipsec1_ike = nvram.ipsec1_ike;
		stats.ipsec1_ph2 = nvram.ipsec1_ph2;
		stats.ipsec1_esp = nvram.ipsec1_esp;
		stats.ipsec1_recv = '&nbsp;&nbsp;' + nvram.ipsec1_recv + '<small>Bytes</small>';
		stats.ipsec1_send = '&nbsp;&nbsp;' + nvram.ipsec1_send + '<small>Bytes</small>';
	}
	else
	{
		stats.ipsec1_mode = 'Disable';
	}
	if (nvram.ipsec2_mode == '1')
	{
		stats.ipsec2_mode = 'Enable';
		stats.ipsec2_ph1 = nvram.ipsec2_ph1;
		stats.ipsec2_ike = nvram.ipsec2_ike;
		stats.ipsec2_ph2 = nvram.ipsec2_ph2;
		stats.ipsec2_esp = nvram.ipsec2_esp;
		stats.ipsec2_recv = '&nbsp;&nbsp;' + nvram.ipsec2_recv + '<small>Bytes</small>';
		stats.ipsec2_send = '&nbsp;&nbsp;' + nvram.ipsec2_send + '<small>Bytes</small>';
	}
	else
	{
		stats.ipsec2_mode = 'Disable';
	}
	stats.wanip = nvram.wan_ipaddr;
	stats.wannetmask = nvram.wan_netmask;
	stats.wangateway = nvram.wan_gateway_get;
	if (stats.wangateway == '0.0.0.0' || stats.wangateway == '')
		stats.wangateway = nvram.wan_gateway;
	switch (nvram.wan_proto) {
		case 'pptp':
		case 'l2tp':
			if (stats.wanup) {
				stats.wanip = nvram.ppp_get_ip;
				if (nvram.pptp_dhcp == '1') {
					if (nvram.wan_ipaddr != '' && nvram.wan_ipaddr != '0.0.0.0' && nvram.wan_ipaddr != stats.wanip)
						stats.wanip += '&nbsp;&nbsp;<small>(DHCP: ' + nvram.wan_ipaddr + ')</small>';
					if (nvram.wan_gateway != '' && nvram.wan_gateway != '0.0.0.0' && nvram.wan_gateway != stats.wangateway)
						stats.wangateway += '&nbsp;&nbsp;<small>(DHCP: ' + nvram.wan_gateway + ')</small>';
				}
				if (stats.wannetmask == '0.0.0.0')
					stats.wannetmask = '255.255.255.255';
			}
			else {
				if (nvram.wan_proto == 'pptp')
					stats.wangateway = nvram.pptp_server_ip;
			}
			break;
		default:
			if (!stats.wanup) {
				stats.wanip = '0.0.0.0';
				stats.wannetmask = '0.0.0.0';
				stats.wangateway = '0.0.0.0';
			}
	}
	stats.wanstatus = '<% wanstatus(); %>';
	//if (stats.wanstatus != 'Connected') stats.wanstatus = '<b>' + stats.wanstatus + '</b>';
	if (stats.wanstatus == 'Connected') stats.wanstatus = '<b><% translate("Connected"); %></b>';
	if (stats.wanstatus == 'Disconnected') stats.wanstatus = '<b><% translate("Disconnected"); %></b>';
	if (stats.wanstatus == 'Renewing...') stats.wanstatus = '<b><% translate("Renewing"); %>...</b>';
	if (stats.wanstatus == 'Connecting...') stats.wanstatus = '<b><% translate("Connecting"); %>...</b>';
	if (stats.vpn_client_up == "1")
		stats.vpn_client_up = 'Connected';
	else
		stats.vpn_client_up = 'Disconnected';
	stats.channel = [];
	stats.interference = [];

	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		u = wl_unit(uidx);

		a = i = wlstats[uidx].channel * 1;
		if (i < 0) i = -i;
		stats.channel.push('<a href="#tools-survey.asp">' + ((i) ? i + '' : 'Auto') +
			((wlstats[uidx].mhz) ? ' - ' + (wlstats[uidx].mhz / 1000.0).toFixed(3) + ' <small>GHz</small>' : '') + '</a>' +
			((a < 0) ? ' <small>(scanning...)</small>' : ''));
		stats.interference.push((wlstats[uidx].intf >= 0) ? ((wlstats[uidx].intf) ? '<% translate("Severe"); %>' : '<% translate("Acceptable"); %>') : '');

		a = wlstats[uidx].nbw * 1;
		wlstats[uidx].nbw = (a > 0) ? (a + ' <small>MHz</small>') : 'Auto';

		if (wlstats[uidx].radio) {
			a = wlstats[uidx].rate * 1;
			if (a > 0)
				wlstats[uidx].rate = a + ' <small>Mbps</small>';
			else
				wlstats[uidx].rate = '-';

			wlstats[uidx].rssi += ' <small>dBm</small>';
		}
		else {
			wlstats[uidx].rate = '';
			wlstats[uidx].rssi = '';
		}

		if (wl_ifaces[uidx][6] != 1) {
			wlstats[uidx].ifstatus = '<b>Down</b>';
		} else {
			wlstats[uidx].ifstatus = 'Up';
		}
	}
	
} while (0);
