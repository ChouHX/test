<?PHP include 'header.php'; ?>
	<script type="text/javascript">

//	<% nvram("tr069_enable,tr069_periodic_enable,tr069_interval,tr069_username,tr069_password,tr069_url"); %>

function verifyFields(focused, quiet)
{
	var ok = 1, c;

	var b = E('_f_tr069_periodic_enable').checked;
	elem.display(PR('_tr069_interval'), b);
	E('_tr069_interval').disabled = !b;
	return ok;
}

function save()
{
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  fom.tr069_enable.value = E('_f_tr069_enable').checked ? "1" : "0";
  fom.tr069_periodic_enable.value = E('_f_tr069_periodic_enable').checked ? "1" : "0";
  // form.submit('_fom', 1);
  return submit_form('_fom');
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading">TR069</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#admin-tr069.asp'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='tr069-restart'>
				<input type='hidden' name='tr069_enable'>
				<input type='hidden' name='tr069_periodic_enable'>
				<div id="tr069_conf"></div>
			</form>
			<script type='text/javascript'>
				$('#tr069_conf').forms([
				{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, name: 'f_tr069_enable', type: 'checkbox', value: (nvram.tr069_enable == '1') },
				{ title: $lang.ENABLE_PERIODIC_TRANSMISSION, name: 'f_tr069_periodic_enable', type: 'checkbox', value: nvram.tr069_periodic_enable == 1 },
				{ title: $lang.SENDING_INTERVAL, name: 'tr069_interval', type: 'text', maxlen: 5, size: 7, value: nvram.tr069_interval },
				{ title: $lang.VAR_USER_NAME, name: 'tr069_username', type: 'text', maxlen: 64, size: 32, value: nvram.tr069_username },
				{ title: $lang.PPTP_CLIENT_PASSWD, name: 'tr069_password', type: 'text', maxlen: 64, size: 32, value: nvram.tr069_password },
				{ title: $lang.DOMAIN_URL, name: 'tr069_url', type: 'text', maxlen: 128, size: 64, value: nvram.tr069_url }
					], { align: 'left' });
			</script>
         </div>   
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type='text/javascript'>verifyFields(null, true);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
