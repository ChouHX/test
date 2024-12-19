//<% nvram("lte,router_hw,dualsim,wan_iface,ppp_demand,cellManual,cellConMode,modem_imei,sim_state,sim_flag,sim_ccid,lac_ci,cell_network,cops,modem_type,modem_state,main_remain,backup_remain,csq,ppp_get_ip,pptp_server_ip,router_name,wan_domain,wan_gateway,wan_gateway_get,wan_get_domain,wan_hostname,wan_hwaddr,wan_ipaddr,wan_netmask,wan_proto,wan_run_mtu,et0macaddr,wan_ifnames,wl_mode,vpn_mode,vpn_client_up,vpn_client_rip,vpn_client_lip,ipsec1_esp,ipsec1_ike,ipsec1_mode,ipsec1_ph1,ipsec1_ph2,ipsec1_recv,ipsec1_send,ipsec2_esp,ipsec2_ike,ipsec2_mode,ipsec2_ph1,ipsec2_ph2,ipsec2_recv,ipsec2_send"); %>
//<% version(0); %>
//<% uptime(); %>
//<% sysinfo(); %>
stats = { };
do {
var a, b, i;
var xifs = ['wan', 'lan', 'lan1', 'lan2', 'lan3'];
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
stats.systemtype = sysinfo.systemtype;
stats.cpuload = ((sysinfo.loads[0] / 65536.0).toFixed(2) + '<small> / </small> ' +
(sysinfo.loads[1] / 65536.0).toFixed(2) + '<small> / </small>' +
(sysinfo.loads[2] / 65536.0).toFixed(2));
stats.uptime = sysinfo.uptime_s;
a = sysinfo.totalram;
b = sysinfo.totalfreeram;
stats.memory = scaleSize(a) + ' / ' + scaleSize(b) + ' <small>(' + (b / a * 100.0).toFixed(2) + '%)</small>';
if (sysinfo.totalswap > 0) {
a = sysinfo.totalswap;
b = sysinfo.freeswap;
stats.swap = scaleSize(a) + ' / ' + scaleSize(b) + ' <small>(' + (b / a * 100.0).toFixed(2) + '%)</small>';
} else
stats.swap = '';
stats.time = '<% time(); %>';
stats.wanup = '<% wanup(); %>' == '1';
stats.wanuptime = '<% link_uptime(); %>';
stats.wanlease = '<% dhcpc_time(); %>';
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
if (stats.wanstatus != 'Connected') stats.wanstatus = '<b>' + stats.wanstatus + '</b>';
if (stats.vpn_client_up == "1")
stats.vpn_client_up = 'Connected';
else
stats.vpn_client_up = 'Disconnected';
stats.channel = [];
stats.interference = [];
stats.qual = [];
} while (0);
