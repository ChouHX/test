<!--
-->
<title><% ident(); %> <%translate("Network Tools");%>：Capture</title>
<content>
<script type='text/javascript' src='js/status-data-gps.jsx?_http_id=<% nv(http_id); %>'></script>
<script type="text/javascript">
//	<% nvram(""); %>
// <% pcapinfo(); %>

var ref = new TomatoRefresh('update.cgi', 'exec=pcapinfo');
ref.refresh = function(text)
{
	try {
		eval(text);
	}
	catch (ex) {
	}

	if(pcapinfo.status == '1')
	{
		E("do-button").value = '<% translate("Stop");%>';
		ref.refreshTime = 1000;
	}
	else
	{
		E("do-button").value = '<% translate("Start");%>';
		ref.refreshTime = 0;
	}
	E("_pcap_if").value = pcapinfo.if;
	E("_pcap_time").value = pcapinfo.time;
}

function verifyFields(focused, quiet)
{
	if(!v_range('_pcap_time', quiet, 0, 1440))
	{
		return 0;
	}
	if(pcapinfo.status == '1')
	{
		E("do-button").value = '<% translate("Stop");%>';
	}
	else
	{
		E("do-button").value = '<% translate("Start");%>';
		ref.refreshTime = 0;
	}

	return 1;
}

function pcap_do()
{
	if(verifyFields(null,0) == 0)
	{
		return;
	}

	if(pcapinfo.status == '1')
	{
		var fom = E('_fom');
		fom.pcap_action.value = "stop";
		fom._redirect.value = "/#tools-pcap.asp";
		form.submit('_fom', 0);
	}
	else
	{
		window.open('pcap.cgi?pcap_action=start&pcap_if='+ E('_pcap_if').value +'&pcap_time='+E('_pcap_time').value+'&_http_id='+nvram.http_id);
		ref.initPage(0,2000);
	}
	return;
}
</script>
<form id="_fom" method="post" action="pcap.cgi">
<input type='hidden' name='_redirect' value=''>
<input type='hidden' name='pcap_action' value='start'>
<ul class="nav-tabs">
<li><a class="ajaxload" href="tools-ping.asp"><i class="icon-hammer"></i> <%translate("Ping");%></a></li>
<li><a class="ajaxload" href="tools-trace.asp"><i class="icon-gauge"></i> <%translate("Trace");%></a></li>
<li><a class="ajaxload" href="tools-wol.asp"><i class="icon-wake"></i> <%translate("WOL");%></a></li>
<li><a class="ajaxload" href="tools-log.asp"><i class="icon-drive"></i> <%translate("Log");%></a></li>
<li><a class="active"><i class="icon-lock"></i> <%translate("Capture");%></a></li>
</ul>
<div class="box">
<div class="heading"><%translate("Capture");%></div>
<div class="section content" id = 'Capture'>
<script>
$('#Capture').forms([
	{ title: '<% translate("Network");%>', name: 'pcap_if', size: 9,type: 'select', options: [['0', 'LAN'],['1', 'WAN']], value: pcapinfo.if ,suffix:'<input type="button" value="<% translate("Start");%>" id="do-button" onclick="pcap_do();">' },
	{ title: '<% translate("Time1");%>', name: 'pcap_time', type: 'text', maxlen: 4, size: 5, value: pcapinfo.time ,suffix: '<% translate("minutes");%> (0 <% translate("for infinite");%>)'}
]);
</script>
</div>
</div>

</content>
