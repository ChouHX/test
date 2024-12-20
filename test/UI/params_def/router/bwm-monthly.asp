﻿<!DOCTYPE html>
<!--
--><title><%translate("Bandwidth: Monthly");%></title>
<content>
	<script type="text/javascript" src="js/bwm-hist.js"></script>
	<script type="text/javascript">
		//<% nvram("wan_ifnmaeX,wan_ifname,wan2_ifname,wan3_ifname,wan4_ifname,lan_ifname,rstats_enable"); %>

		try {
			//	<% bandwidth("monthly"); %>
		}
		catch (ex) {
			monthly_history = [];
		}
		rstats_busy = 0;
		if (typeof(monthly_history) == 'undefined') {
			monthly_history = [];
			rstats_busy = 1;
		}

		function genData()
		{
			var w, i, h;

			w = window.open('', 'tomato_data_m');
			w.document.writeln('<pre>');
			for (i = 0; i < monthly_history.length; ++i) {
				h = monthly_history[i];
				w.document.writeln([(((h[0] >> 16) & 0xFF) + 1900), (((h[0] >>> 8) & 0xFF) + 1), h[1], h[2]].join(','));
			}
			w.document.writeln('</pre>');
			w.document.close();
		}

		function save()
		{
			cookie.set('monthly', scale, 31);
		}

		function redraw()
		{
			var h;
			var grid;
			var rows;
			var yr, mo, da;

			rows = 0;
			block = '';
			gn = 0;

			grid = '<table class="line-table td-large">';
			grid += '<tr><td><b><%translate("Date");%></b></td><td><b><%translate("Download");%></b></td><td><b><%translate("Upload");%></b></th><td><b><%translate("Total");%></b></td></tr>';

			for (i = 0; i < monthly_history.length; ++i) {
				h = monthly_history[i];
				yr = (((h[0] >> 16) & 0xFF) + 1900);
				mo = ((h[0] >>> 8) & 0xFF);

				grid += makeRow(((rows & 1) ? 'odd' : 'even'), ymText(yr, mo), rescale(h[1]), rescale(h[2]), rescale(h[1] + h[2]));
				++rows;
			}

			E('bwm-monthly-grid').innerHTML = grid + '</table>';
		}

		function init()
		{
			var s;

			if (nvram.rstats_enable != '1') { $('#rstats').before('<div class="alert alert-warning"><%translate("Bandwidth monitoring disabled");%>.</b> <a href="/#admin-bwm.asp"><%translate("Enable");%> &raquo;</a></div>'); return; }

			if ((s = cookie.get('monthly')) != null) {
				if (s.match(/^([0-2])$/)) {
					E('scale').value = scale = RegExp.$1 * 1;
				}
			}

			initDate('ym');
			monthly_history.sort(cmpHist);
			redraw();
		}
	</script>

	<ul class="nav-tabs">
		<li><a class="ajaxload" href="bwm-realtime.asp"><i class="icon-hourglass"></i> <%translate("Real-Time");%></a></li>
		<li><a class="ajaxload" href="bwm-24.asp"><i class="icon-graphs"></i> <%translate("Last 24 Hours");%></a></li>
		<li><a class="ajaxload" href="bwm-daily.asp"><i class="icon-clock"></i> <%translate("Daily");%></a></li>
		<li><a class="ajaxload" href="bwm-weekly.asp"><i class="icon-week"></i> <%translate("Weekly");%></a></li>
		<li><a class="active"><i class="icon-month"></i>  <%translate("Monthly");%></a></li>
	</ul>

	<div id="rstats" class="box">
		<div class="heading"><%translate("Monthly Bandwidth");%><a class="pull-right" href="#" data-toggle="tooltip" title="<%translate("Reload Information");%>" onclick="reloadPage(); return false;"><i class="icon-refresh"></i></a></div>
		<div class="content">
			<div id="bwm-monthly-grid"></div>
		</div>
	</div>

<!--	<a href="javascript:genData()" class="btn btn-primary"><%translate("Data");%> <i class="icon-drive"></i></a>
	<a href="admin-bwm.asp" class="btn btn-danger ajaxload"><%translate("Configure");%> <i class="icon-tools"></i></a>-->
	<span class="pull-right">
		<b><%translate("Date ");%></b> <select onchange="changeDate(this, 'ym')" id="dafm"><option value="0"><%translate("yyyy-mm");%></option><option value="1"><%translate("mm-yyyy");%></option><option value="2"><%translate("mmm yyyy");%></option><option value="3"><%translate("mm.yyyy");%></option></select> &nbsp;
		<b><%translate("Scale");%></b> <select onchange="changeScale(this)" id="scale"><option value="0">KB</option><option value="1">MB</option><option value="2" selected>GB</option></select>
	</span>
	<script type="text/javascript">init();</script>
	<script type='text/javascript' src='js/uiinfo.js'></script>
</content>
