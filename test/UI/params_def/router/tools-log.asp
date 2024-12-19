<title><%translate("Status");%>: <%translate("Logs");%></title>
<content>
<script type="text/javascript">
	//<% nvram("log_file"); %>

	function find()
	{
		var s = E('find-text').value;
		if (s.length) document.location = 'logs/view.cgi?find=' + escapeCGI(s) + '&_http_id=' + nvram.http_id;
	}

	function init()
	{
		var e = E('find-text');
		if (e) e.onkeypress = function(ev) {
			if (checkEvent(ev).keyCode == 13) find();
			}
	}
</script>
	<ul class="nav-tabs">
	<li><a class="ajaxload" href="tools-ping.asp"><i class="icon-hammer"></i> <%translate("Ping");%></a></li>
	<li><a class="ajaxload" href="tools-trace.asp"><i class="icon-gauge"></i> <%translate("Trace");%></a></li>
	<li><a class="ajaxload" href="tools-wol.asp"><i class="icon-wake"></i> <%translate("WOL");%></a></li>
	<li><a class="active">	<i class="icon-drive"></i> <%translate("Log");%></a></li>
	<li><a class="ajaxload" href="tools-pcap.asp"><i class="icon-lock"></i> <%translate("Capture");%></a></li>
	</ul>

<div class="box">
	<div class="heading"><%translate("Logs");%>
		<!--<a class="ajaxload pull-right" data-toggle="tooltip" title="Configure Logging" href="#admin-log.asp"><i class="icon-system"></i></a>-->
	</div>
	<div class="content">

		<div id="logging">
			<div class="section">
				<a href="logs/view.cgi?which=all&_http_id=<% nv(http_id) %>"><%translate("View");%></a><br /><br />
                <a  href="logs/syslog.txt?_http_id=<% nv(http_id) %>"><%translate("Download Log File");%></a>
				<div class="input-append"><input class="span3" type="text" maxsize="32" id="find-text"> <button value="Find" onclick="find()" class="btn"> <%translate("Find");%><i class="icon-search"></i></button></div>
</i>
				<br><br /><hr>
                &raquo; <a href="#admin-log.asp"> <%translate("Logging Configuration");%></a><br><br>
				
                
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
	if (nvram.log_file != '1') {
		$('#logging').before('<div class="alert alert-info"><%translate("Internal logging disabled");%>.</b><br><br><a href="admin-log.asp"><%translate("Enabled");%> &raquo;</a></div>');
		E('logging').style.display = 'none';
	}
</script>
<script type="text/javascript">init()</script>
