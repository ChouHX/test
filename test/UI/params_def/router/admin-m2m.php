<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("m2m_devlist_type,m2m_devlist_on,m2m_devlist_intval,n2n_ipaddr,n2n_bootmode,n2n_server,n2n_online,m2m_mode,m2m_product_id,m2m_server_domain,m2m_server_port,m2m_heartbeat_intval,m2m_error_action,m2m_heartbeat_retry"); %>

function verifyFields(focused, quiet)
{
	var ok = 1;

	var a = E('_f_m2m_mode').checked;

	E('_m2m_server_domain').disabled = !a;
	E('_m2m_product_id').disabled = !a;
	E('_m2m_server_port').disabled = !a;
	E('_m2m_heartbeat_intval').disabled = !a;
	E('_m2m_heartbeat_retry').disabled = !a;
	E('_m2m_error_action').disabled = !a;
	E('_f_m2m_devlist_on').disabled = !a;
	E('_m2m_devlist_intval').disabled = !a;
	E('_m2m_devlist_type').disabled = !a;
	E('_n2n_bootmode').disabled = !a;
	E('_n2n_server').disabled = !a;

	var b = E('_f_m2m_devlist_on').checked;
	PR(E('_m2m_devlist_intval')).style.display = b ? '' : 'none';
	PR(E('_m2m_devlist_type')).style.display = b ? '' : 'none';

	if (!v_port('_m2m_server_port', quiet)) return 0;
	if(!v_ip('_m2m_server_domain', true) && (!E('_m2m_server_domain').value.length))
	{
		ferror.set(E('_m2m_server_domain'), $lang.INVALID_DOMAIN_NAME_OR_IP_ADDRESS, quiet);
		return 0;
	}
	if (!v_range('_m2m_devlist_intval', quiet, 0, 3600)) return 0;
	if(!v_ascii('_m2m_product_id',quiet || !ok)) return 0;
	if (!v_range('_n2n_server', quiet, 1024, 65535)) return 0;
	if (!v_range('_m2m_heartbeat_intval', quiet, 1, 3600)) return 0;
	if (!v_range('_m2m_heartbeat_retry', quiet, 10, 1000)) return 0;
	return ok;
}

function save()
{
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  fom.m2m_mode.value = E('_f_m2m_mode').checked ? "enable" : "disable";
  fom.m2m_devlist_on.value = E('_f_m2m_devlist_on').checked ? "1" : "0";

  // form.submit('_fom', 1);
  return submit_form('_fom');
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.M2M)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#admin-m2m.asp'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='m2m-restart'>
				<input type='hidden' name='_sleep' value='3'>
				<input type='hidden' name='m2m_mode'>
				<input type='hidden' name='m2m_devlist_on'>
				<div id="m2mconfig"></div>
			</form>
		</div>
	</div>
			<script type='text/javascript'>

				$('#m2mconfig').forms([
					{ title: $lang.ENABLE_M2M_PLATFORM_MANAGEMENT, name: 'f_m2m_mode', type: 'checkbox', value: nvram.m2m_mode != 'disable' },
					{ title: $lang.EXCEPTION_HANDLING,name:'m2m_error_action',type:'select',options:[['0', $lang.RESTART_M2M],['1', $lang.RECONNECT_NETWORK],['2', $lang.REBOOT_SYSTEM]],value:nvram.m2m_error_action},
					{ title: $lang.PRODUCT_ID, name: 'm2m_product_id', type: 'text', maxlen: 14, size: 15, value: nvram.m2m_product_id },
					null,
					{ title: $lang.M2M_SERVER_DOMAIN_PORT, indent: 2, multi: [
						{ name: 'm2m_server_domain', type: 'text', maxlen: 63, size: 32, value: nvram.m2m_server_domain, suffix: ':' },
						{ name: 'm2m_server_port', type: 'text', maxlen: 5, size: 7, value: nvram.m2m_server_port } ]},
					{ title: $lang.HEARTBEAT_PACKET_REPORTING_FREQUENCY, indent: 2, name: 'm2m_heartbeat_intval', type: 'text', maxlen: 4, size: 5, value: nvram.m2m_heartbeat_intval, suffix: ' <small>('+ $lang.VAR_SECOND +')</small>' },
					{ title: $lang.M2M_HEARTBEAT_RETRY, indent: 2, name: 'm2m_heartbeat_retry', type: 'text', maxlen: 4, size: 5, value: nvram.m2m_heartbeat_retry,suffix: ' <small>('+ $lang.RANGE +':10-1000)</small>'},
						{ title: $lang.DEVICE_LIST_REPORT, name: 'f_m2m_devlist_on', type: 'checkbox', value: nvram.m2m_devlist_on == '1' },
						{ title: $lang.REPORTING_MODE,indent: 2, name:'m2m_devlist_type',type:'select',options:[['0', $lang.ADD_REPORT],['1', $lang.ALL_REPORT]],value:nvram.m2m_devlist_type},
						{ title: $lang.DEVICE_LIST_REPORT_INTERVAL, indent: 2, name: 'm2m_devlist_intval', type: 'text', maxlen: 5, size: 5, value: nvram.m2m_devlist_intval, suffix: ' <small>('+ $lang.VAR_SECOND +')</small>' },
						null,
					{ title: $lang.HOW_TO_START_NAMED_PIPES, name: 'n2n_bootmode', type: 'select', options:[['0', $lang.REMOTE_CONNECT],['1', $lang.AUTO_CONNECT]],value: nvram.n2n_bootmode != '0' },
					{ title: $lang.NAMED_PIPES_SERVICE_PORT, indent: 2, name: 'n2n_server', type: 'text',maxlen: 63, size: 32, value: nvram.n2n_server,suffix: ' <small>('+ $lang.RANGE +':1024-65535)</small>'},
					// { title: $lang.NAMED_PIPE_STATUS, indent: 2, text: { '0': $lang.VAR_TERM_STATUS_OFFLINE, '1': $lang.VAR_TERM_STATUS_ONLINE}[nvram.n2n_online] },
					null,
					// { title: $lang.NAMED_PIPE_ADDRESS, indent: 2, text: nvram.n2n_ipaddr }
					], { align: 'left' });
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
