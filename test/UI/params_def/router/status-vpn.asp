<!--
-->
<title><% ident(); %> <%translate("Status");%>: <%translate("VPN");%></title>
<content>
<script type='text/javascript' src='status-data-gps.jsx?_http_id=<% nv(http_id); %>'></script>
	<script type="text/javascript">
// <% nvram("router_name"); %>
// <% vpnstatus(); %>

	</script>

		<div class="heading"><%translate("VPN Status");%></div>
		<div class="content" >
			<script type='text/javascript'>
				var nv = vpnstatus.split('>');
				for (var i = 0; i < nv.length; ++i) 
				{
					var t = nv[i].split('<');
					if (t.length==4) 
					{
						createFieldTable('', [
							{ title: 'VPN Name',text: t[0] },
							{ title: 'VPN Protocol',text: t[1] },
							{ title: 'Local IP',text: t[2] },
							{ title: 'Peer IP',text: t[3] }
						]);
					}
				}
			</script>
        </div>

</content>