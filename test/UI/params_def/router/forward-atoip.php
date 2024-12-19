<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram("atoip_mode,atoip_proto,atoip_addr,atoip_port,atoip_idle")%>
function verifyFields(focused, quiet)
{
	var ok = 1;
	if(!v_ip('_atoip_addr')) return 0;
	if(!v_port('_atoip_port', quiet)) return 0;
	if(!v_range('_atoip_idle', quiet, 5, 3600)) return 0;
	return ok;
}

function earlyInit()
{
	verifyFields(null, 1);
}

function save()
{
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');

	if(1)
	{
		fom._service.disabled = 1;
		fom._reboot_now.value = '1';
		// form.submit(fom);
		return submit_form('_fom');
	}
}

function init()
{
}
</script>

<div class="box">
	<div class="heading">AT Over IP</div>
	<div class="content" >
		<form id="_fom" method="post" action="tomato.cgi">
			<input type='hidden' name='_nextpage' value='/#forward-atoip.asp'>
			<input type='hidden' name='_nextpage' >
			<input type='hidden' name='_nextwait' value='5'>
            <input type='hidden' name='_service' value='modem_checkdial-restart'>
            <input type='hidden' name='_reboot_now' value='0'>
			<div id="atoipconfig"></div>
		</form>
		<script type='text/javascript'>
		$('#atoipconfig').forms([
			{ title: $lang.NET_MODE,  name: 'atoip_mode', type: 'select', options: [['0', $lang.VAR_NONE],['1', $lang.VAR_TERM_TERM_PARAMS_ACTIVATE]],	value: nvram.atoip_mode },
			{ title: $lang.CONNECTION_TYPE,  name: 'atoip_proto', type: 'select', options: [['0', 'UDP'],['1', 'TCP']],	value: nvram.atoip_proto },
			{ title: $lang.LOCAL_IP, name: 'atoip_addr', type: 'text', maxlen: 20, size: 20, value: nvram.atoip_addr },
			{ title: 'Local Port', name: 'atoip_port', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.atoip_port, 5400)},
			{ title: $lang.CLIENTIDLETIMEOUT_2, name: 'atoip_idle', type: 'text', maxlen: 5, size: 7, value: nvram.atoip_idle,suffix: ' <small>('+ $lang.VAR_SECOND +')</small>'}
			], { align: 'left' });
		</script>
	</div>
	</div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br/><br/>
<script type="text/javascript">earlyInit();</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
