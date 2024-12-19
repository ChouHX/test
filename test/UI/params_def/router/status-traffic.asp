<!--
-->
<title><%translate("Status");%>: <%translate("Traffic Stats");%></title>
<content>
<script type='text/javascript' src='js/status-data-gps.jsx?_http_id=<% nv(http_id); %>'></script>
<script type="text/javascript">
// <% traffic(); %>
function show_list()
{
	var i;
	var	htmlOut;
	var iface = '';	
	
	for(i=0;i<traffic.length;i++)
	{
		var str = traffic[i].iface;
		if(i%2==0)
		{
			htmlOut += "<tr class='even'>";
		}
		else
		{
			htmlOut += "<tr class='odd'>";
		}
		if(traffic[i].iface == 'usb0' || traffic[i].iface == 'usb1' || traffic[i].iface == 'ppp0' || traffic[i].iface == 'ppp1')
		{
			iface = 'Cellular' + '( ' + traffic[i].iface + ' )';
		}
		else if(str.indexOf("ppp10") != -1)
		{
			iface = 'VPN' + '( ' + traffic[i].iface + ' )';
		}
		else
		{
			iface = 'WAN' + '( ' + traffic[i].iface + ' )';
		}
		htmlOut += "<td width='30%'>" + iface +"</td>";
		var rtmp, ttmp;
		if(traffic[i].receive < 1024)
		{
			rtmp = traffic[i].receive;
			htmlOut += "<td width='35%'>" + rtmp + "<i> B</i>" + "</td>";
		}
		else if(traffic[i].receive >= 1024 && traffic[i].receive < 1024*1024)
		{
			rtmp = traffic[i].receive / 1024;
			htmlOut += "<td width='35%'>" + parseFloat(rtmp).toFixed(2) + "<i> KB</i>" +"</td>";
		}
		else
		{
			rtmp = traffic[i].receive / 1024 / 1024;
			htmlOut += "<td width='35%'>" + parseFloat(rtmp).toFixed(2) + "<i> MB</i>" + "</td>";
		}
		
		if(traffic[i].transmit < 1024)
		{
			ttmp = traffic[i].transmit;
			htmlOut += "<td width='35%'>" + ttmp + "<i> B</i>" + "</td>";
		}
		else if(traffic[i].transmit >= 1024 && traffic[i].transmit < 1024*1024)
		{
			ttmp = traffic[i].transmit / 1024;
			htmlOut += "<td width='35%'>" + parseFloat(ttmp).toFixed(2) + "<i> KB</i>" +"</td>";
		}
		else
		{
			ttmp = traffic[i].transmit / 1024 / 1024;
			htmlOut += "<td width='35%'>" + parseFloat(ttmp).toFixed(2) + "<i> MB</i>" + "</td>";
		}
		htmlOut += "</tr>\n";		
	}
	$('#file-list').append(htmlOut);
}

</script>

<div class="box" data-box="list">
<div class="heading"><% translate("Traffic Stats."); %></div>
<div class="section content">
    <table width='100%' border='0' cellspacing='1' cellpadding='3' class='line-table' id="file-list">
	  <tr class="header">
		<td width='30%'  id='dHost'><% translate("Interface"); %></td>
		<td width='35%'  id='dHost'><% translate("Transmit Data"); %></td>
		<td width='35%'   id='dMac'><% translate("Receive Data"); %></td>
	  </tr>
	<script language='JavaScript' type='text/javascript'>
	show_list();
</script>
</table>
</div>
</div>

</content>
