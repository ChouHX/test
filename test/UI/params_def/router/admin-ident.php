<?PHP include 'header.php'; ?>
	<script type="text/javascript">

//	<% nvram("router_name,wan_hostname,wan_domain"); %>


function verifyFields(focused, quiet)
{
	if (!v_hostname('_router_name', quiet)) return 0;
	if (!v_hostname('_wan_hostname', quiet)) return 0;
	if (!v_domain('_wan_domain', quiet)) return 0;
	return v_length('_router_name', quiet, 1) && v_length('_wan_hostname', quiet, 0) && v_length('_wan_domain', quiet, 0);
}

function save()
{
	if (!verifyFields(null, false)) return;
	
	var fom = E('_fom');
	if(1)//confirm("<%translate("admin-ident-warn");%>"))
	{
		fom._service.disabled = 1;
		fom._reboot.value = '1';
		// form.submit(fom);
		return submit_form('_fom');
	}
	else
	{
		return;
	}
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.ROUTER_IDENTIFICATION)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#admin-ident.asp'>
                <input type='hidden' name='_service' value='*'>
                <input type='hidden' name='_reboot' value='0'>
				<div id="m2mconfig"></div>
			</form>
			<script type='text/javascript'>
				$('#m2mconfig').forms([
				{ title: $lang.ROUTER_NAME, name: 'router_name', type: 'text', maxlen: 32, size: 34, value: nvram.router_name },
				{ title: $lang.WAN_HOSTNAME, name: 'wan_hostname', type: 'text', maxlen: 32, size: 34, value: nvram.wan_hostname },
				{ title: $lang.GPS_HOST_NAME, name: 'wan_domain', type: 'text', maxlen: 32, size: 34, value: nvram.wan_domain }
					], { align: 'left' });
			</script>
         </div>   
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type='text/javascript'>verifyFields(null, true);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
