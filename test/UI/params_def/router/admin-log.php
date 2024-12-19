<?PHP include 'header.php'; ?>
<style>
		table.fields-table tr td:first-child {
			width: 30%;
			min-width: 250px;
		}
	</style><script type="text/javascript">
	//	<% nvram("log_remote,log_remoteip,log_remoteport,log_file,log_file_custom,log_file_path,log_limit,log_in,log_out,log_mark,log_events,log_wm,log_wmtype,log_wmip,log_wmdmax,log_wmsmax,log_file_size,log_file_keep"); %>
if(!nvram.log_events){
	nvram.log_events = '';
}

function verifyFields(focused, quiet)
{
	var a, b, c;

	a = E('_f_log_file').checked;
	b = E('_f_log_remote').checked;
	c = E('_f_log_file_custom').checked;

	a = !(a || b);
	E('_log_in').disabled = a;
	E('_log_out').disabled = a;
	E('_log_limit').disabled = a;
	E('_log_mark').disabled = a;
	E('_f_log_acre').disabled = a;
	E('_f_log_crond').disabled = a;
	E('_f_log_dhcpc').disabled = a;
	E('_f_log_ntp').disabled = a;
	E('_f_log_sched').disabled = a;

	elem.display(PR('_log_remoteip'), b);
	E('_log_remoteip').disabled = !b;
	E('_log_remoteport').disabled = !b;

	E('_f_log_file_custom').disabled = !E('_f_log_file').checked;
	E('_log_file_path').disabled = !c || !E('_f_log_file').checked;

	if (!a) {
		if (!v_range('_log_limit', quiet, 0, 2400)) return 0;
		if (!v_range('_log_mark', quiet, 0, 99999)) return 0;
		if (b) {
			c = E('_log_remoteip');
			if (!v_ip(c, 1) && !v_domain(c, 1)) {
				if (!quiet) ferror.show(c);
				return 0;
			}
			if (!v_port('_log_remoteport', quiet)) return 0;
		}
	}

	if (E('_f_log_file').checked) {
		E('_log_file_size').disabled = 0;
		if (!v_range('_log_file_size', quiet, 0, 99999)) return 0;
		if (parseInt(E('_log_file_size').value) > 0) {
			E('_log_file_keep').disabled = 0;
			if (!v_range('_log_file_keep', quiet, 0, 99)) return 0;
		} else {
			E('_log_file_keep').disabled = 1;
		}
	} else {
		E('_log_file_size').disabled = 1;
		E('_log_file_keep').disabled = 1;
	}

	a = E('_f_log_wm').checked;
	b = E('_log_wmtype').value != 0;
	E('_log_wmtype').disabled = !a;
	E('_f_log_wmip').disabled = !a;
	E('_log_wmdmax').disabled = !a;
	E('_log_wmsmax').disabled = !a;
	elem.display(PR('_f_log_wmip'), b);

	if (a) {
		if (b) {
			if (!_v_iptaddr('_f_log_wmip', quiet, 15, 1, 1)) return 0;
		}
		if (!v_range('_log_wmdmax', quiet, 0, 9999)) return 0;
		if (!v_range('_log_wmsmax', quiet, 0, 9999)) return 0;
	}

	return 1;
}

function save()
{
	var a, fom;

	if (!verifyFields(null, false)) return;

	fom = E('_fom');
	fom.log_remote.value = E('_f_log_remote').checked ? 1 : 0;
	fom.log_file.value = E('_f_log_file').checked ? 1 : 0;
	fom.log_file_custom.value = E('_f_log_file_custom').checked ? 1 : 0;

	a = [];
	if (E('_f_log_acre').checked) a.push('acre');
	if (E('_f_log_crond').checked) a.push('crond');
	if (E('_f_log_dhcpc').checked) a.push('dhcpc');
	if (E('_f_log_ntp').checked) a.push('ntp');
	if (E('_f_log_sched').checked) a.push('sched');
	fom.log_events.value = a.join(',');

	fom.log_wm.value = E('_f_log_wm').checked ? 1 : 0;
	fom.log_wmip.value = fom.f_log_wmip.value.split(/\s*,\s*/).join(',');

	// form.submit(fom, 1);
	return submit_form('_fom');
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
            <input type='hidden' name='_nextpage' value='/#admin-log.asp'>
            <input type='hidden' name='_service' value='logging-restart'>
            
            <input type='hidden' name='log_remote'>
            <input type='hidden' name='log_file'>
            <input type='hidden' name='log_file_custom'>
            <input type='hidden' name='log_events'>
            <input type='hidden' name='log_wm'>
            <input type='hidden' name='log_wmip'>

		<div class="box" data-box="router-log">
			<div class="heading"><script type="text/javascript">document.write($lang.SYSLOG)</script></div>
			<div class="content" id="router-log"></div>
			<script type="text/javascript">

				/* REMOVE-BEGIN
				// adjust (>=1.22)
				nvram.log_mark *= 1;
				if (nvram.log_mark >= 120) nvram.log_mark = 120;
				else if (nvram.log_mark >= 60) nvram.log_mark = 60;
				else if (nvram.log_mark > 0) nvram.log_mark = 30;
				else nvram.log_mark = 0;
				REMOVE-END */

				$('#router-log').forms([
				{ title: $lang.LOG_TO_LOCAL_SYSTEM, name: 'f_log_file', type: 'checkbox', value: nvram.log_file == 1 },
	{ title: $lang.LOG_FILE_SIZE, name: 'log_file_size', type: 'text', maxlen: 5, size: 6, value: nvram.log_file_size || 50, suffix: ' <small>KB</small>',  hidden: 1  },
	{ title: $lang.LOG_ENTRY_LIMIT, name: 'log_file_keep', type: 'text', maxlen: 2, size: 3, value: nvram.log_file_keep || 1 ,  hidden: 1 },
	{ title: $lang.CUSTOMIZE_LOG_PATH, multi: [
		{ name: 'f_log_file_custom', type: 'checkbox', value: nvram.log_file_custom == 1, suffix: '  ' },
		{ name: 'log_file_path', type: 'text', maxlen: 32, size: 20, value: nvram.log_file_path, suffix: ' <small>(确保该目录存在并可写)</small>' }
		] , hidden: 1 },
	{ title: $lang.LOG_TO_REMOTE_SYSTEM, name: 'f_log_remote', type: 'checkbox', value: nvram.log_remote == 1 },
	{ title: $lang.HOST_OR_IP_ADDRESS_PORT, indent: 2, multi: [
		{ name: 'log_remoteip', type: 'text', maxlen: 15, size: 17, value: nvram.log_remoteip, suffix: ':' },
		{ name: 'log_remoteport', type: 'text', maxlen: 5, size: 7, value: nvram.log_remoteport } ]},
	{ title: $lang.MARK_GENERATION_INTERVAL, name: 'log_mark', type: 'select', options: [[0, $lang.VAR_NONE],[30, $lang.EVERY_30_MINUTES],[60, $lang.EVERY_1_HOUR],[120, $lang.EVERY_2_HOUR],[360, $lang.EVERY_6_HOUR],[720, $lang.EVERY_12_HOUR],[1440, $lang.EVERY_OTHER_DAY],[10080, $lang.EVERY_7_DAYS]], value: nvram.log_mark },
	{ title: $lang.EVENTS_LOGGED, text: '<small>('+ $lang.RESTART_VALID_TIP +')</small>', hidden: 1  },
	{ title: $lang.ACCESS_RESTRICTION, indent: 2, name: 'f_log_acre', type: 'checkbox', value: (nvram.log_events.indexOf('acre') != -1), hidden: 1  },
	{ title: $lang.CRON, indent: 2, name: 'f_log_crond', type: 'checkbox', value: (nvram.log_events.indexOf('crond') != -1), hidden: 1  },
	{ title: $lang.DHCP_CLIENT, indent: 2, name: 'f_log_dhcpc', type: 'checkbox', value: (nvram.log_events.indexOf('dhcpc') != -1), hidden: 1  },
	{ title: $lang.NTP, indent: 2, name: 'f_log_ntp', type: 'checkbox', value: (nvram.log_events.indexOf('ntp') != -1), hidden: 1  },
	{ title: $lang.SCHEDULER, indent: 2, name: 'f_log_sched', type: 'checkbox', value: (nvram.log_events.indexOf('sched') != -1), hidden: 1  },
	{ title: $lang.CONNECTION_LOGGING, hidden: 1  },
	{ title: $lang.INBOUND, indent: 2, name: 'log_in', type: 'select', options: [[0, $lang.VAR_NONE + $lang.RECOMMENDED],[1, $lang.IF_BLOCKED_BY_FIREWALL],[2, $lang.IF_ALLOWED_BY_FIREWALL],[3, $lang.BOTH]], value: nvram.log_in, hidden: 1  },
	{ title: $lang.OUTBOUND, indent: 2, name: 'log_out', type: 'select', options: [[0,$lang.VAR_NONE + $lang.RECOMMENDED],[1, $lang.IF_BLOCKED_BY_FIREWALL],[2, $lang.IF_ALLOWED_BY_FIREWALL],[3, $lang.BOTH]], value: nvram.log_out, hidden: 1  },
	{ title: $lang.LOGGING_LIMITS, indent: 2, name: 'log_limit', type: 'text', maxlen: 4, size: 5, value: nvram.log_limit, suffix: ' <small>('+ $lang.MESSAGES_PER_MINUTE_0_MEANS_UNLIMITED +')</small>' }
				]);
			</script>
		</div>

		<div class="box" data-box="webmon-settings"  style='display:none'>
			<div class="heading"><script type="text/javascript">document.write($lang.WEB_MONITOR)</script></div>
			<div class="content" id="webmon"></div>
			<script type='text/javascript'>
				$('#webmon').forms([
					{ title: $lang.MONITOR_WEB_USAGE, name: 'f_log_wm', type: 'checkbox', value: nvram.log_wm == 1 },
{ title: $lang.MONITOR, name: 'log_wmtype', type: 'select', options: [[0, $lang.ALL_COMPUTERS_DEVICES],[1, $lang.THE_FOLLOWING +  '...'],[2, $lang.ALL_EXCEPT + '...']], value: nvram.log_wmtype },
{ title: $lang.VAR_IP + '(es)', indent: 2,  name: 'f_log_wmip', type: 'text', maxlen: 512, size: 64, value: nvram.log_wmip,
suffix: '<br><small>('+ $lang.REMOTE_ACCESS_ALLOW_IPS_TIPS +')</small>' },
{ title: $lang.NUMBER_OF_ENTRIES_TO_REMEMBER },
{ title: $lang.VAR_RULE_DOMAIN, indent: 2,  name: 'log_wmdmax', type: 'text', maxlen: 4, size: 6, value: nvram.log_wmdmax, suffix: ' <small>(0 '+ $lang.TO_DISABLE +')</small>' },
{ title: $lang.SEARCHES, indent: 2, name: 'log_wmsmax', type: 'text', maxlen: 4, size: 6, value: nvram.log_wmsmax, suffix: ' <small>(0 '+ $lang.TO_DISABLE +')</small>' }
				]);
			</script>
		</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
