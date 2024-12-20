<!DOCTYPE html>
<!--
--><title><%translate("Wake On Lan");%></title>
<content>
	<script type="text/javascript">
		//	<% arplist(); %>
		//	<% nvram('at_update,tomatoanon_answer,dhcpd_static,lan_ifname'); %>

		var wg = new TomatoGrid();
		wg.setup = function() {
			this.init('wol-grid', 'sort');
			this.headerSet([' <%translate("MAC Address");%>', '<%translate("IP Address");%>', '<%translate("Status");%>', '<%translate("Name");%>']);
			this.sort(3);
		}
		wg.sortCompare = function(a, b) {
			var da = a.getRowData();
			var db = b.getRowData();
			var r = 0;
			var c = this.sortColumn;
			if (c == 1)
				r = cmpIP(da[c], db[c]);
			else
				r = cmpText(da[c], db[c]);
			return this.sortAscending ? r : -r;
		}
		wg.populate = function()
		{
			var i, j, r, s;

			this.removeAllData();

			s = [];
			var q = nvram.dhcpd_static.split('>');
			for (i = 0; i < q.length; ++i) {
				var e = q[i].split('<');
				if ((e.length == 3) || (e.length == 4)) {
					var m = e[0].split(',');
					for (j = 0; j < m.length; ++j) {
						s.push([m[j], e[1], e[2]]);
					}
				}
			}

			// show entries in static dhcp list
			for (i = 0; i < s.length; ++i) {
				var t = s[i];
				var active = '-';
				for (j = 0; j < arplist.length; ++j) {
					if ((arplist[j][2] == nvram.lan_ifname) && (t[0] == arplist[j][1])) {
						active = '<%translate("Active (In ARP)");%>';
						arplist[j][1] = '!';
						break;
					}
				}
				if (t.length == 3) {
					r = this.insertData(-1, [t[0], (t[1].indexOf('.') != -1) ? t[1] : ('<% lipp(); %>.' + t[1]), active, t[2]]);
					for (j = 0; j < 4; ++j)
						r.cells[j].title = '<%translate("Click to wake up");%>';
				}
			}

			// show anything else in ARP that is awake
			for (i = 0; i < arplist.length; ++i) {
				if ((arplist[i][2] != nvram.lan_ifname) || (arplist[i][1].length != 17)) continue;
				r = this.insertData(-1, [arplist[i][1], arplist[i][0], '<%translate("Active (In ARP)");%>', '']);
				for (j = 0; j < 4; ++j)
					r.cells[j].title = '<%translate("Click to wake up");%>';
			}

			this.resort(2);
		}
		wg.onClick = function(cell)
		{
			wake(PR(cell).getRowData()[0]);
		}

		function verifyFields(focused, quiet)
		{
			var e;

			e = E('_f_mac');
			e.value = e.value.replace(/[\t ]+/g, ' ');
			return 1;
		}

		function spin(x)
		{
			E('refreshb').disabled = x;
			E('wakeb').disabled = x;
		}

		var waker = null;

		function wake(mac)
		{
			if (!mac) {
				if (!verifyFields(null, 1)) return;
				mac = E('_f_mac').value;
				cookie.set('wakemac', mac);
			}
			E('_mac').value = mac;
			form.submit('_fom', 1);
		}



		var refresher = null;
		var timer = new TomatoTimer(refresh);
		var running = 0;

		function refresh()
		{
			if (!running) return;

			timer.stop();

			refresher = new XmlHttp();
			refresher.onCompleted = function(text, xml) {
				eval(text);
				wg.populate();
				timer.start(5000);
				refresher = null;
			}
			refresher.onError = function(ex) { alert(ex); reloadPage(); }
			refresher.post('/update.cgi', 'exec=arplist');
		}

		function refreshClick()
		{
			running ^= 1;
			E('refreshb').value = running ? '<%translate("Stop");%>' : '<%translate("Refresh");%>';
			E('spin').style.visibility = running ? 'visible' : 'hidden';
			if (running) refresh();
		}

		function init()
		{
			wg.recolor();
		}
	</script>

	<style>
		#wol-grid tr { cursor: pointer; }
		.sectionmacs tr td:first-child { width: 150px; }
	</style>

	<ul class="nav-tabs">
		<li><a class="ajaxload" href="tools-ping.asp"><i class="icon-hammer"></i> <%translate("Ping");%></a></li>
		<li><a class="ajaxload" href="tools-trace.asp"><i class="icon-gauge"></i> <%translate("Trace");%></a></li>
		<li><a class="active"><i class="icon-wake"></i> <%translate("WOL");%></a></li>
		<li><a class="ajaxload" href="tools-log.asp"><i class="icon-drive"></i> <%translate("Log");%></a></li>
		<li><a class="ajaxload" href="tools-pcap.asp"><i class="icon-lock"></i> <%translate("Capture");%></a></li>
	</ul>

	<form id="_fom" action="wakeup.cgi" method="post">

		<input type="hidden" name="_redirect" value="/#tools-wol.asp">
		<input type="hidden" name="_nextwait" value="1">
		<input type="hidden" name="mac" value="" id="_mac">

		<div class="box">
			<div class="heading"><%translate("Wake On Lan");%></div>
			<div class="content">

				<table id="wol-grid" class="line-table"></table><br />
				<div class="sectionmacs"></div>

				<div class="pull-right">
					<div id="spin" class="spinner" style="vertical-align:middle;visibility:hidden"></div> &nbsp;
					<button type="button" value="Refresh" onclick="refreshClick()" id="refreshb" class="btn"><%translate("Refresh");%> <i class="icon-refresh"></i></button>
				</div>

				<button type="button" value="Wake Up" onclick="wake(null)" id="save-button" class="btn"><%translate("Wake Up");%> <i class="icon-chevron-up"></i></button>
			</div>
		</div>

		<script type="text/javascript">
			$('.sectionmacs').forms([
				{ title: '<%translate("MAC Address List");%>', name: 'f_mac', type: 'textarea', value: cookie.get('wakemac') || '', style: 'width: 100%; height: 60px;' },
				]);
		</script>

	</form>

	<script type="text/javascript">wg.setup(); wg.populate(); init();</script>
</content>
