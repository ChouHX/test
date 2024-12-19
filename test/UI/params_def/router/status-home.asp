<title><% translate("Status"); %>: <%translate("Overview");%></title>
<content>
	<script type="text/javascript" src="js/wireless_vif.jsx?_http_id=<% nv(http_id); %>"></script>
	<script type="text/javascript" src="js/interfaces.js?_http_id=<% nv(http_id); %>"></script>
	<script type="text/javascript" src="js/status-data.jsx?_http_id=<% nv(http_id); %>"></script>
	<script type="text/javascript">
		wmo = {'ap':'<%translate("Access Point");%>','sta':'<%translate("Wireless Client");%>','wet':'<%translate("Wireless Ethernet Bridge");%>','wds':'WDS'};
		auth = {'disabled':'<%translate("disabled");%>','wep':'WEP','wpa_personal':'WPA(PSK) <%translate("Personal");%>','wpa_enterprise':'WPA  <%translate("Enterprise");%>','wpa2_personal':'WPA2(PSK) <%translate("Personal");%>','wpa2_enterprise':'WPA2 <%translate("Enterprise");%>','wpaX_personal':'WPA / WPA2 <%translate("Personal");%>','wpaX_enterprise':'WPA / WPA2 <%translate("Enterprise");%>','radius':'Radius'};
		enc = {'tkip':'TKIP','aes':'AES','tkip+aes':'TKIP / AES'};
		bgmo = {'disabled':'<%translate("disabled");%>','mixed':'<%translate("Auto");%>','a-only':'<%translate("A Only");%>','b-only':'<%translate("B Only");%>','g-only':'<%translate("G Only");%>','bg-mixed':'<%translate("B/G Mixed");%>','lrs':'LRS','n-only':'<%translate("N Only");%>'};

		show_radio = [];
		for ( var uidx = 0; uidx < wl_ifaces.length; ++uidx ) {
			if ( wl_sunit( uidx ) < 0 )
				show_radio.push( (nvram[ 'wl' + wl_fface( uidx ) + '_radio' ] == '1') );
		}

		nphy = features( '11n' );

		function dhcpc( what, wan_prefix ) {
			form.submitHidden( 'dhcpc.cgi', { exec: what, prefix: wan_prefix, _redirect: '/#status-home.asp' } );
		}

		function serv( service, sleep ) {
			form.submitHidden( 'service.cgi', { _service: service, _redirect: '/#status-home.asp', _sleep: sleep } );
		}

		function wan_connect( uidx ) {
			serv( 'wan' + uidx + '-restart', 5 );
		}

		function wan_disconnect( uidx ) {
			serv( 'wan' + uidx + '-stop', 2 );
		}

		var ref = new TomatoRefresh( 'js/status-data.jsx', '', 0, 'status_overview_refresh' );

		ref.refresh = function( text ) {
			stats = {};
			try {
				eval( text );
			}
			catch ( ex ) {
				stats = {};
			}
			show();
			ethstates();
		}


		function c( id, htm ) {
			E( id ).cells[ 1 ].innerHTML = htm;
		}

		function ethstates() {

			var speed,status,pstatus,ports = [];

			if ( etherstates.port0 == "disable" || (ethinfo.length == 0 ) || typeof (etherstates.port0) == 'undefined' || typeof (etherstates.port1) == 'undefined' || typeof (etherstates.port2) == 'undefined' || typeof (etherstates.port3) == 'undefined' || typeof (etherstates.port4) == 'undefined' ) {
				$( '#ethernetPorts' ).remove();
				return false;
			}

			for(var i=0;i<ethinfo.length;i++)
			{
				pstatus = etherstates['port'+ethinfo[i].sport];
				if ( pstatus == 'DOWN' ) {
					status = 'off';
					speed  = etherstates[ 'port'+ethinfo[i].sport ].replace( "DOWN", "<% translate("Unplugged"); %>" );

				} else {
					status = 'on';
					speed  = etherstates[ 'port'+ethinfo[i].sport ].replace( 'HD', 'M Half' );
					speed  = speed.replace( "FD", "M Full" );
				}
				ports.push('<div class="eth '+status+' '+'"><div class="title">'+ethinfo[i].title+'</div><div class="speed">'+speed+'</div></div>');
			}
			$( "#ethernetPorts .content" ).html( '<div id="ethPorts">' + ports.join( '' ) + '</div>' );

		}

		function show_modem(which)
		{
			which.state = (which.state == "1")?'<b><%translate("Ready");%></b>':(which.state == "0")?'<b><%translate("Unknown");%></b>':'<b><span style="color:red"><%translate("Searching");%>...</span></b>';
			which.sim = (which.sim == "1")?'<b><%translate("Ready");%></b>':(which.sim == "0")?'<b><%translate("Unknown");%></b>':'<b><span style="color:red"><%translate("Searching");%>...</span></b>';
			which.updown = (which.updown == '1')?'<b><% translate("Connected"); %></b>':'<b><% translate("Disconnected"); %></b>';

			which.csq = (which.csq == '')?'0':which.csq;
			if(which.csq > 100)
				which.csq += ' <img src="bar' + MIN(MAX(Math.floor((which.csq - 100) / 16), 1), 6) + '.gif">';
			else
				which.csq += ' <img src="bar' + MIN(MAX(Math.floor(which.csq / 5), 1), 6) + '.gif">';

			if(which == 'modem')
			{
				c('modem_band_num'+which.suffix,which.modem_band_num);
			}
			c('imei'+which.suffix,which.imei);
			c('state'+which.suffix,which.state);
			c('cell'+which.suffix,which.cell);
			c('cops'+which.suffix,which.cops);
			c('sim'+which.suffix,which.sim);
			c('csq'+which.suffix,which.csq);
			c('mip'+which.suffix, which.ip);
			c('mmask'+which.suffix, which.mask);
			c('mgw'+which.suffix, which.gw);
			c('mdns'+which.suffix, which.dns);
			c('mupdown'+which.suffix, which.updown);
			c('muptime'+which.suffix, which.uptime);
			if(which.suffix == "")
			{
				if(bi.hw=='sd')
				{
					if(which.main_remain.indexOf('...') == -1)
					{
						which.main_remain += ' <%translate("minutes");%>';
					}
					if(which.backup_remain.indexOf('...') == -1)
					{
						which.backup_remain += ' <%translate("minutes");%>';
					}
					c('main_remain',which.main_remain);
					c('backup_remain',which.backup_remain);
					which.sim_selected = (which.sim_flag == "2")?'<b><%translate("USIM Card 2 Running...");%></b>':'<b><%translate("USIM Card 1 Running...");%></b>';
					c('sim_selected',which.sim_selected);
					elem.display('sim_selected',1);
					elem.display('main_remain',which.dualsim=='3');
					elem.display('backup_remain',which.dualsim=='3');
				}
				else
				{
					elem.display('sim_selected',0);
					elem.display('main_remain',0);
					elem.display('backup_remain',0);
				}
			}
			var mtype = nvram.modem_type + '';
			if(mtype.indexOf('RM500') != -1)
			{
				elem.display('modem_band_num',1);
			}
			else
			{
				elem.display('modem_band_num',0);
			}
		}

		function show_wan(which)
		{
			which.updown = (which.updown == '1')?'<b><% translate("Connected"); %></b>':'<b><% translate("Disconnected"); %></b>';
			if(which.proto == 'static') which.proto = 'Static';
			else if(which.proto == 'dhcp') which.proto = 'DHCP';
			else if(which.proto == 'pppoe') which.proto = 'PPPOE';
			c(which.prefix+'proto', which.proto);
			c(which.prefix+'ip', which.ip);
			c(which.prefix+'mask', which.mask);
			c(which.prefix+'gw', which.gw);
			c(which.prefix+'dns', which.dns);
			c(which.prefix+'status', which.updown);
			c(which.prefix+'uptime', which.uptime);
		}
		function show_sta(which)
		{
			which.updown = (which.updown == '1')?'<b><% translate("Connected"); %></b>':'<b><% translate("Disconnected"); %></b>';
			if(which.proto == 'static') which.proto = 'Static';
			else if(which.proto == 'dhcp') which.proto = 'DHCP';
			c(which.prefix+'proto', which.proto);
			c(which.prefix+'ip', which.ip);
			c(which.prefix+'mask', which.mask);
			c(which.prefix+'gw', which.gw);
			c(which.prefix+'dns', which.dns);
			c(which.prefix+'status', which.updown);
			c(which.prefix+'uptime', which.uptime);
		}
		function show() {

			if(modem.enable == '1')
			{
				E('cell-settings').style.display = '';
				show_modem(modem);
			}
			else
			{
				E('cell-settings').style.display = 'none';
			}
			if(bi.hw == 'dd' && modem2.enable == '1')
			{
				E('cell-settings2').style.display = '';
				show_modem(modem2);
			}
			else
			{
				E('cell-settings2').style.display = 'none';
			}
			if(wan1.proto != 'disabled')
			{
				E('wan-settings').style.display = '';
				show_wan(wan1);
			}
			else
			{
				E('wan-settings').style.display = 'none';
			}
			if(sta.enable == '1')
			{
				show_sta(sta);
			}
			if(sta2.enable == '1')
			{
				show_sta(sta2);
			}
			stats.which_link = '';
			if(stats.wanip == modem.ip)
			{
				stats.which_link = '<%translate("Cellular");%>';
			}
			else if(stats.wanip == modem2.ip)
			{
				stats.which_link = '<%translate("Cellular");%> 2';
			}
			else if(stats.wanip == wan1.ip)
			{
				stats.which_link = '<%translate("WAN");%>';
			}
			else if(stats.wanip == sta.ip)
			{
				stats.which_link = '<%translate("STA");%>';
			}
			else if(stats.wanip == sta2.ip)
			{
				stats.which_link = '<%translate("STA");%> 2';
			}
			c('which_link', stats.which_link);
			c('firmware', stats.firmware);
			c('wanip', stats.wanip);
			c('wannetmask', stats.wannetmask);
			c('wangateway', stats.wangateway);
			c('dns', stats.dns);
			c('memory', stats.memory);
			c('swap', stats.swap);
			c('wanstatus', stats.wanstatus);
			c('wanuptime', stats.wanuptime);
			if(stats.schedule_alert)
			{
				E('salert').style.display = '';
			}
	
			//c( 'cpu', stats.cpuload );
			c( 'cpupercent', stats.cpupercent );
			c( 'wlsense', stats.wlsense );
			c( 'uptime', stats.uptime );
			c( 'time', stats.time );
			c( 'memory', stats.memory + '<div class="progress small"><div class="bar" style="width: ' + stats.memoryperc + ';"></div></div>' );
			c( 'swap', stats.swap + '<div class="progress small"><div class="bar" style="width: ' + stats.swapperc + ';"></div></div>' );
			elem.display( 'swap', stats.swap != '' );
/* WIFI-BEGIN */	
			for ( uidx = 0; uidx < wl_ifaces.length; ++uidx ) {
				if ( wl_sunit( uidx ) < 0 ) {
					c( 'radio' + uidx, wlstats[ uidx ].radio ? '<% translate("Enabled"); %> <i class="icon-check"></i>' : '<% translate("Disabled"); %> <i class="icon-cancel"></i>' );
					c( 'rate' + uidx, wlstats[ uidx ].rate );

					if ( show_radio[ uidx ] ) {

						if ( wlstats[ uidx ].radio ) {

							$( '#b_wl' + uidx + '_enable' ).hide();
							$( '#b_wl' + uidx + '_disable' ).show();

						} else {

							$( '#b_wl' + uidx + '_enable' ).show();
							$( '#b_wl' + uidx + '_disable' ).hide();

						}

					} else {

						// Interface disabled, hide enable/disable
						$( '#b_wl' + uidx + '_enable' ).hide();
						$( '#b_wl' + uidx + '_disable' ).hide();

					}

					c( 'channel' + uidx, stats.channel[ uidx ] );
					if ( nphy ) {
						c( 'nbw' + uidx, wlstats[ uidx ].nbw );
					}
					c( 'interference' + uidx, stats.interference[ uidx ] );
					elem.display( 'interference' + uidx, stats.interference[ uidx ] != '' );

					if ( wlstats[ uidx ].client ) {
						c( 'rssi' + uidx, wlstats[ uidx ].rssi || '' );
					}
				}
				c( 'ifstatus' + uidx, wlstats[ uidx ].ifstatus || '' );
			}
/* WIFI-END */	
		}

		function earlyInit() {
/* WIFI-BEGIN */
			var uidx;
		/*	for ( uidx = 0; uidx < wl_ifaces.length; ++uidx ) {
				if ( wl_sunit( uidx ) < 0 )
					elem.display( 'b_wl' + uidx + '_enable', 'b_wl' + uidx + '_disable', show_radio[ uidx ] );
			}*/
/* WIFI-END */

			if(bi.wlof == 1)
			{
				for (var uidx = 0; uidx < wl_ifaces.length; ++uidx)
				{
					u = wl_fface(uidx);
					document.getElementById('wl' + u + '_settings').style.display = 'none';
				}
			}
			ethstates();
			show();
			init();
		}

		function init() {

			$( '.refresher' ).after( genStdRefresh( 1, 0, 'ref.toggle()' ) );
			ref.initPage( 3000, 3 );

		}

	</script>

	<div class="fluid-grid">

		<div class="box" data-box="home_systembox" id="system-settings">
			<div class="heading"><%translate("System");%></div>
			<div class="content" id="sesdiv_system">
				<div class="section"></div>
				<script type="text/javascript">	
					var a = (nvstat.size - nvstat.free) / nvstat.size * 100.0;
					createFieldTable('', [
						{ title: '<%translate("Router Name");%>', text: nvram.router_name },
						{ title: '<%translate("Hardware Version");%>', text: nvram.router_hw },
						{ title: '<%translate("Firmware Version");%>', rid:'firmware', text: stats.firmwave },
						{ title: '<%translate("Router Sn");%>', text: nvram.router_sn },
					//	{ title: '<%translate("Model");%>', text: nvram.t_model_name },
						{ title: '<%translate("Chipset");%>', text: stats.systemtype ,hidden:1},
						{ title: '<%translate("CPU Freq");%>', text: stats.cpumhz, hidden:1 },
						{ title: '<%translate("Flash Size");%>', text: stats.flashsize , hidden:1 },
						{ title: '<%translate("Router Time");%>', rid: 'time', text: stats.time },
						{ title: '<%translate("Uptime");%>', rid: 'uptime', text: stats.uptime },
						{ title: '<%translate("CPU Usage");%>', rid: 'cpupercent', text: stats.cpupercent , hidden:1},
						//{ title: '<%translate("CPU Load");%><small>(1 / 5 / 15 mins)</small>', rid: 'cpu', text: stats.cpuload },
						{ title: '<%translate("Memory Usage");%>', rid: 'memory', text: stats.memory + '<div class="progress small"><div class="bar" style="width: ' + stats.memoryperc + ';"></div></div>',hidden:1 },
						{ title: '<%translate("Swap Usage");%>', rid: 'swap', text: stats.swap + '<div class="progress small"><div class="bar" style="width: ' + stats.swapperc + ';"></div></div>', hidden: (stats.swap == '') },
						{ title: '<%translate("NVRAM Usage");%>', text: scaleSize(nvstat.size - nvstat.free) + ' <small>/</small> ' + scaleSize(nvstat.size) + ' (' + (a).toFixed(2) + '%) <div class="progress small"><div class="bar" style="width: ' + (a).toFixed(2) + '%;"></div></div>' ,hidden:1},
						{ title: '<%translate("CPU Temperature");%>', rid: 'temps', text: stats.cputemp + 'C', hidden:1},
						{ title: '<%translate("Wireless Temperature");%>', rid: 'wlsense', text: stats.wlsense, hidden:1 }
					], '#sesdiv_system', 'data-table dataonly');
				</script>
			</div>
		</div>
		
		

		
		<div class="box" id="ethernetPorts" data-box="home_ethports">
			<div class="heading"><%translate("Ethernet Ports Status");%>
				<!--<a class="ajaxload pull-right" data-toggle="tooltip" title="<%translate("Configure Settings");%>" href="#wlan-network.asp"><i class="icon-system"></i></a>-->
			</div>
			<div class="section content" id="sesdiv_lan-ports"></div>
		</div>

		<div class="box" data-box="home_vpnbox">
			<div class='heading' id='wan-title'><%translate("VPN Status");%>
				<a class="ajaxload pull-right" data-toggle="tooltip"  href="#vpn-xtp.asp"><i class="icon-system"></i></a>
			</div>
			<div class='section content' id='sesdiv_vpn'>
			<script type='text/javascript'>
				var vpnNum = 0;
				var nv = vpnstatus.split('>');
				for (var i = 0; i < nv.length; ++i) 
				{
					var t = nv[i].split('<');
					if (t.length==4) 
					{
						var status;
						if(t[2] != '' && t[2] != '0.0.0.0')
						{
							status = '<%translate("Connected");%>';
						}
						else
						{
							status = '<%translate("Disconnected");%>';
						}
						//document.getElementById('none_div').style.display = 'none';
						createFieldTable('', [
							{ title: '<%translate("Name");%>',text: t[0] },
							{ title: '<%translate("Protocol");%>',text: t[1] },
							{ title: '<%translate("Connection Status");%>',text: status },
							{ title: '<%translate("IP Address");%>',text: t[2] },
							{ title: '<%translate("Gateway");%>',text: t[3] }
						],'#sesdiv_vpn', 'data-table dataonly' );
						vpnNum = 1;
					}
				}

				if(nvram.ipsec1_mode == '1' && nvram.ipsec1_active== '1')
				{
					var recv,send,status;
					//document.getElementById('none_div').style.display = 'none';
					recv = (nvram.ipsec1_recv != '') ? nvram.ipsec1_recv : 0;
					send = (nvram.ipsec1_send != '') ? nvram.ipsec1_send : 0;
					status = ((nvram.ipsec1_recv != '') && (nvram.ipsec1_send != '') && (nvram.ipsec1_esp != '')) ? '<%translate("Connected");%>' : '<%translate("Disconnected");%>';
					createFieldTable('', [
						{ title: 'IPSec 1', text: status },
						{ title: 'Phase 1 Status', indent:2, text: nvram.ipsec1_ph1 },
						{ title: 'Phase 1 IKE', indent:2, text: nvram.ipsec1_ike },
						{ title: 'Phase 2 Status', indent:2, text: nvram.ipsec1_ph2 },
						{ title: 'Phase 2 ESP', indent:2, text: nvram.ipsec1_esp },
						{ title: 'IPSec Recv.',  indent:2, text: '&nbsp;&nbsp;' + recv + ' <small>Bytes</small>' },
						{ title: 'IPSec Send.', indent:2, text: '&nbsp;&nbsp;' + send + ' <small>Bytes</small>' }
					],'#sesdiv_vpn', 'data-table dataonly' );
					vpnNum = 1;
				}
				if(nvram.ipsec2_mode == '1' && nvram.ipsec2_active== '1')
				{
					var recv,send,status;
					//document.getElementById('none_div').style.display = 'none';
					recv = (nvram.ipsec2_recv != '') ? nvram.ipsec2_recv : 0;
					send = (nvram.ipsec2_send != '') ? nvram.ipsec2_send : 0;
					status = ((nvram.ipsec2_recv != '') && (nvram.ipsec2_send != '') && (nvram.ipsec2_esp != '')) ? '<%translate("Connected");%>' : '<%translate("Disconnected");%>';
					createFieldTable('', [
						{ title: 'IPSec 2', text: status},
						{ title: 'Phase 1 Status', indent:2, text: nvram.ipsec2_ph1 },
						{ title: 'Phase 1 IKE', indent:2, text: nvram.ipsec2_ike },
						{ title: 'Phase 2 Status', indent:2, text: nvram.ipsec2_ph2 },
						{ title: 'Phase 2 ESP', indent:2, text: nvram.ipsec2_esp },
						{ title: 'IPSec Recv.', indent:2, text: '&nbsp;&nbsp;' + recv + '<small>Bytes</small>' },
						{ title: 'IPSec Send.', indent:2, text: '&nbsp;&nbsp;' + send + '<small>Bytes</small>' }
					],'#sesdiv_vpn', 'data-table dataonly');
					vpnNum = 1;
				}
				if(vpnNum == 0)
				{
					createFieldTable('', [
							{title: '<% translate("No Active VPN"); %>'}
					],'#sesdiv_vpn', 'data-table dataonly' );
				}

			</script>
			</div>
		</div>
		<div class="box" data-box="home_internetbox" id="internet-settings">
			<div class="heading"><%translate("Internet");%>
				<span id="salert" style='display:none;color:red;font-size:13px;margin-top:10px'>
					&nbsp;(<%translate("schedule_notice");%><i><a href='#basic-schedule.asp'><%translate("edit");%></a></i>)</span>
			</div>
			<div class="content" id="sesdiv_internet">
				<div class="section"></div>
				<script type="text/javascript">
					createFieldTable('', [
						{ title: '<%translate("Connection Type");%>', rid: 'which_link', text: stats.which_link },
						{ title: '<%translate("IP Address");%>', rid: 'wanip', text: stats.wanip },
						{ title: '<%translate("Subnet Mask");%>', rid: 'wannetmask', text: stats.wannetmask },
						{ title: '<%translate("Gateway");%>', rid: 'wangateway', text: stats.wangateway },
						{ title: '<%translate("DNS");%>', rid: 'dns', text: stats.dns },
						{ title: '<%translate("Connection Status");%>', rid: 'wanstatus', text: stats.wanstatus },
						{ title: '<%translate("Connection Uptime");%>', rid: 'wanuptime', text: stats.wanuptime }
					], '#sesdiv_internet', 'data-table dataonly');
				</script>
			</div>
		</div>
		<div class="box" data-box="home_cellbox" id="cell-settings">
			<div class="heading"><%translate("Cellular");%>
				<span id="salert" style='display:none;color:red;font-size:13px;margin-top:10px'>
					&nbsp;(<%translate("schedule_notice");%><i><a href='#basic-schedule.asp'><%translate("edit");%></a></i>)</span>
				<a class="ajaxload pull-right" data-toggle="tooltip"  href="#basic-cellular.asp"><i class="icon-system"></i></a>
			</div>
			<div class="content" id="sesdiv_cell">
				<div class="section"></div>
				<script type="text/javascript">
					createFieldTable('', [
						{ title: '<%translate("Connection Type");%>',text: (modem.lte == 1)?"ECM/QMI":"PPP" },
						{ title: '<%translate("Modem IMEI");%>',rid: 'imei',text: modem.imei },
						{ title: '<%translate("Modem Status");%>',rid: 'state',text: modem.state },
						{ title: '<%translate("Cellular ISP");%>',rid: 'cops',text: modem.cops },
						{ title: '<%translate("Cellular Network");%>',rid: 'cell', text: modem.cell },
						{ title: '<%translate("USIM Selected");%>',rid: 'sim_selected',text: modem.sim_selected },
						{ title: '<%translate("USIM Status");%>',rid: 'sim',text: modem.sim },
						{ title: '<%translate("Band");%>', rid: 'modem_band_num', text: modem.modem_band_num },
						{ title: '<%translate("CSQ");%>',rid: 'csq',text: modem.csq },
						{ title: '<%translate("IP Address");%>',rid: 'mip',text: modem.ip },
						{ title: '<%translate("Subnet Mask");%>',rid: 'mmask',text: modem.mask },
						{ title: '<%translate("Gateway");%>',rid: 'mgw',text: modem.gw },
						{ title: '<%translate("DNS");%>',rid: 'mdns',text: modem.dns },
						{ title: '<%translate("Connection Status");%>',rid: 'mupdown', text: modem.updown },
						{ title: '<%translate("Connection Uptime");%>',rid: 'muptime', text: modem.uptime },
						{ title: '<%translate("Remaining Main Time");%>',rid: 'main_remain', text: modem.main_remain },
						{ title: '<%translate("Remaining Backup Time");%>',rid: 'backup_remain', text: modem.backup_remain }
					], '#sesdiv_cell', 'data-table dataonly');
				</script>
			</div>
		</div>
		<div class="box" data-box="home_cellbox2" id="cell-settings2">
			<div class="heading"><%translate("Cellular");%> 2
				<span id="salert" style='display:none;color:red;font-size:13px;margin-top:10px'>
					&nbsp;(<%translate("schedule_notice");%><i><a href='#basic-schedule.asp'><%translate("edit");%></a></i>)</span>
				<a class="ajaxload pull-right" data-toggle="tooltip"  href="#basic-cellular2.asp"><i class="icon-system"></i></a>
			</div>
			<div class="content" id="sesdiv_cell2">
				<div class="section"></div>
				<script type="text/javascript">
					createFieldTable('', [
						{ title: '<%translate("Connection Type");%>',text: (modem2.lte == 1)?"ECM/QMI":"PPP" },
						{ title: '<%translate("Modem IMEI");%>',rid: 'imei2',text: modem2.imei },
						{ title: '<%translate("Modem Status");%>',rid: 'state2',text: modem2.state },
						{ title: '<%translate("Cellular ISP");%>',rid: 'cops2',text: modem2.cops },
						{ title: '<%translate("Cellular Network");%>',rid: 'cell2', text: modem2.cell },
						{ title: '<%translate("USIM Status");%>',rid: 'sim2',text: modem2.sim },
						{ title: '<%translate("CSQ");%>',rid: 'csq2',text: modem2.csq },
						{ title: '<%translate("IP Address");%>',rid: 'mip2',text: modem2.ip },
						{ title: '<%translate("Subnet Mask");%>',rid: 'mmask2',text: modem2.mask },
						{ title: '<%translate("Gateway");%>',rid: 'mgw2',text: modem2.gw },
						{ title: '<%translate("DNS");%>',rid: 'mdns2',text: modem2.dns },
						{ title: '<%translate("Connection Status");%>',rid: 'mupdown2', text: modem2.updown },
						{ title: '<%translate("Connection Uptime");%>',rid: 'muptime2', text: modem2.uptime }
					], '#sesdiv_cell2', 'data-table dataonly');
				</script>
			</div>
		</div>
		<div class="box" data-box="home_wanbox" id="wan-settings">
			<div class="heading"><%translate("WAN");%>
				<span id="salert" style='display:none;color:red;font-size:13px;margin-top:10px'>
					&nbsp;(<%translate("schedule_notice");%><i><a href='#basic-schedule.asp'><%translate("edit");%></a></i>)</span>
				<a class="ajaxload pull-right" data-toggle="tooltip"  href="#basic-wan.asp"><i class="icon-system"></i></a>
			</div>
			<div class="content" id="sesdiv_wan">
				<div class="section"></div>
				<script type="text/javascript">
					createFieldTable('', [
						{ title: '<%translate("Connection Type");%>', rid: 'wan1proto', text: wan1.proto },
						{ title: '<%translate("IP Address");%>', rid: 'wan1ip', text: wan1.ip },
						{ title: '<%translate("Subnet Mask");%>', rid: 'wan1mask', text: wan1.mask },
						{ title: '<%translate("Gateway");%>', rid: 'wan1gw', text: wan1.gw },
						{ title: '<%translate("DNS");%>', rid: 'wan1dns', text: wan1.dns },
						{ title: '<%translate("Connection Status");%>', rid: 'wan1status', text: wan1.updown },
						{ title: '<%translate("Connection Uptime");%>', rid: 'wan1uptime', text: wan1.uptime }
					], '#sesdiv_wan', 'data-table dataonly');
				</script>
			</div>
		</div>
		<div class="box" id="lan-settings" data-box="home_lanbox">
			<div class="heading"><%translate("LAN");%>
				<a class="ajaxload pull-right" data-toggle="tooltip"  href="#basic-lan.asp"><i class="icon-system"></i></a>
			</div>
			<div class="content" id="sesdiv_lan">
				<script type="text/javascript">

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
								s += '<b>br' + i + '</b> (LAN' + j + ') - ' + nvram['dhcpd' + j + '_startip'] + ' - ' + nvram['dhcpd' + j + '_endip'];
							} else {
								s += ((s.length>0)&&(s.charAt(s.length-1) != ' ')) ? '<br>' : '';
								s += '<b>br' + i + '</b> (LAN' + j + ') - Disabled';
							}
							t += ((t.length>0)&&(t.charAt(t.length-1) != ' ')) ? '<br>' : '';
							t += '<b>br' + i + '</b> (LAN' + j + ') - ' + nvram['lan' + j + '_ipaddr'] + '/' + numberOfBitsOnNetMask(nvram['lan' + j + '_netmask']);

						}
					}

					createFieldTable('', [
						{ title: '<%translate("Router MAC Address");%>', text: nvram.et0macaddr },
						{ title: '<%translate("Router IP Addresses");%>', text: t },
						//{ title: '<%translate("Gateway");%>', text: nvram.lan_gateway, ignore: nvram.wan_proto != 'disabled' },
						//{ title: '<%translate("DNS");%>', rid: 'dns', text: stats.dns, ignore: nvram.wan_proto != 'disabled' },
						{ title: '<%translate("DHCP");%>', text: s }
					], '#sesdiv_lan', 'data-table dataonly' );

				</script>
			</div>
		</div>
		<script type="text/javascript">
/* WIFI-BEGIN */	
			for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {

				var data = "";
				var wl_hwaddr = [];

				if(wl_ifaces[uidx][0] == 'rai0')
				{
					suff = '2';
					stainfo = sta2;
				}
				else
				{
					suff = '';
					stainfo = sta;
				}

				/* REMOVE-BEGIN
				 //	u = wl_unit(uidx);
				 REMOVE-END */
				u = wl_fface(uidx);
				data += '<div class="box" data-box="home_wl' + u +'" id="wl' + u +'_settings"><div class="heading" id="wl'+u+'-title"><%translate("Wireless");%>';
				if (wl_ifaces.length > 0)
					data += ' (' + wl_display_ifname(uidx) + ')';
				data += '<a class="ajaxload pull-right" data-toggle="tooltip"  href="#wlan-network.asp"><i class="icon-system"></i></a>'
				data += '</div>';
				data += '<div class="content" id="sesdiv_wl_'+u+'">';
				sec = auth[nvram['wl'+u+'_security_mode']] + '';
				if (sec.indexOf('WPA') != -1) sec += ' + ' + enc[nvram['wl'+u+'_crypto']];

				wmode = wmo[nvram['wl'+u+'_mode']] + '';
				wmd = nvram['wl'+u+'_mode'] + '';
				if ((nvram['wl'+u+'_mode'] == 'ap') && (nvram['wl'+u+'_wds_enable'] * 1)) wmode += ' + WDS';
				if ((nvram['wl'+u+'_mode'] == 'ap'))
				{
					wl_hwaddr[u] = nvram['wl'+u+'_hwaddr'];
				}
				else
				{
					wl_hwaddr[u] = nvram['wl'+u+'_sta_hwaddr'];
				}


				data += createFieldTable('', [
					{ title: '<%translate("MAC Address");%>', text: wl_hwaddr[u] },
					{ title: '<%translate("Wireless Mode");%>', text: wmode },
					{ title: '<%translate("Wireless Network Mode");%>', text: bgmo[nvram['wl'+u+'_net_mode']], ignore: (wl_sunit(uidx)>=0) },
					{ title: '<%translate("Interface Status");%>', rid: 'ifstatus'+uidx, text: wlstats[uidx].ifstatus , ignore: wmd != 'ap'},
					{ title: '<%translate("Radio");%>', rid: 'radio'+uidx, text: (wlstats[uidx].radio == 0) ? '<% translate("Disabled"); %> <i class="icon-cancel"></i>' : '<% translate("Enabled"); %> <i class="icon-check"></i>', ignore: (wl_sunit(uidx)>=0) },
					{ title: '<%translate("SSID");%>', text: nvram['wl'+u+'_ssid'] },
					{ title: '<%translate("Broadcast");%>', text: (nvram['wl'+u+'_closed'] == 0) ? '<span class="text-green"><% translate("Enabled"); %> <i class="icon-check"></i></span>' : '<span class="text-red"><% translate("Disabled"); %> <i class="icon-cancel"></i></span>', ignore: (nvram['wl'+u+'_mode'] != 'ap') },
					{ title: '<%translate("Security");%>', text: sec },
					{ title: '<%translate("Channel");%>', rid: 'channel'+uidx, text: stats.channel[uidx], ignore: (wl_sunit(uidx)>=0) },
					{ title: '<%translate("Channel Width");%>', rid: 'nbw'+uidx, text: wlstats[uidx].nbw, ignore: ((!nphy) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Interference Level");%>', rid: 'interference'+uidx, text: stats.interference[uidx], hidden: ((stats.interference[uidx] == '') || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Rate");%>', rid: 'rate'+uidx, text: wlstats[uidx].rate, ignore: (wl_sunit(uidx)>=0) || (wmd != 'ap') },
					{ title: '<%translate("RSSI");%>', rid: 'rssi'+uidx, text: wlstats[uidx].rssi || '', ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					null,
					{ title: '<%translate("Connection Type");%>', rid: 'sta'+suff+'proto', text: stainfo.proto, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("IP Address");%>', rid: 'sta'+suff+'ip', text: stainfo.ip, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Subnet Mask");%>', rid: 'sta'+suff+'mask', text: stainfo.mask, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Gateway");%>', rid: 'sta'+suff+'gw', text: stainfo.gw, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("DNS");%>', rid: 'sta'+suff+'dns', text: stainfo.dns, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Connection Status");%>', rid: 'sta'+suff+'status', text: stainfo.updown, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) },
					{ title: '<%translate("Connection Uptime");%>', rid: 'sta'+suff+'uptime', text: stainfo.uptime, ignore: ((!wlstats[uidx].client) || (wl_sunit(uidx)>=0)) }

				], null, 'data-table dataonly');

				data += '</div></div>';
				$('#lan-settings').after(data);
			}
/* WIFI-END */	
		</script>		
	</div>

	
	<div class="clearfix refresher"></div>
	<script type="text/javascript">earlyInit();</script>
</content>
