<?PHP include 'header.php'; ?>
<style type='text/css'>
textarea {
 width: 98%;
 height: 15em;
}
</style>
<content>
	<script type="text/javascript">
//	<% nvram("gp0_enable,gp0_mode,gp0_flt,gp0_ctrig,gp0_cperiod,gp0_recov,gp0_cact,gp0_power_on_counter,gp0_trig_sms,gp0_sms_cont,gp0_sms_recv,gp0_sms_recv_bak,gp1_enable,gp1_mode,gp1_flt,gp1_ctrig,gp1_cperiod,gp1_recov,gp1_cact,gp1_power_on_counter,gp1_trig_sms,gp1_sms_cont,gp1_sms_recv,gp1_sms_recv_bak,do_enable,do_alarm_src_di,do_alarm_src_sms,do_alarm_act,do_keep_on,do_power_on_status,do_pulse_delay,do_pulse_low,do_pulse_high,do_pulse_output,do_sms_recv_cont,do_sms_send_cont,do_sms_manger_1,do_sms_manger_2,do1_enable,do1_alarm_src_di,do1_alarm_src_sms,do1_alarm_act,do1_keep_on,do1_power_on_status,do1_pulse_delay,do1_pulse_low,do1_pulse_high,do1_pulse_output,do1_sms_recv_cont,do1_sms_send_cont,do1_sms_manger_1,do1_sms_manger_2"); %>
var bi = JSON.parse(nvram.bi);

if (!nvram.term_model) {
	nvram.term_model = '';
}

function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c, d;

	// --- visibility ---

	var vis = {
		_f_gp0_enable: 1,
		_gp0_mode: 0,
		_gp0_flt: 0,
		_gp0_ctrig: 0,
		_gp0_cperiod: 0,
		_gp0_recov: 0,
		_gp0_cact: 0,
		_gp0_power_on_counter: 0,
		_f_gp0_trig_sms: 0,
		_gp0_sms_cont: 0,
		_gp0_sms_recv: 0,
		_gp0_sms_recv_bak: 0,
		
		_f_gp1_enable: 1,
		_gp1_mode: 0,
		_gp1_flt: 0,
		_gp1_ctrig: 0,
		_gp1_cperiod: 0,
		_gp1_recov: 0,
		_gp1_cact: 0,
		_gp1_power_on_counter: 0,
		_f_gp1_trig_sms: 0,
		_gp1_sms_cont: 0,
		_gp1_sms_recv: 0,
		_gp1_sms_recv_bak: 0,
		
		_f_do_enable: 1,
		_f_do_alarm_src_di: 0,
		_f_do_alarm_src_sms: 0,
		_do_alarm_act: 0,
		_do_keep_on: 0,
		_do_power_on_status: 0,
		_do_pulse_delay: 0,
		_do_pulse_low: 0,
		_do_pulse_high: 0,
		_do_pulse_output: 0,
		_do_sms_recv_cont: 0,
		_do_sms_send_cont: 0,
		_do_sms_manger_1: 0,
		_do_sms_manger_2: 0,

		_f_do1_enable: 1,
		_f_do1_alarm_src_di: 0,
		_f_do1_alarm_src_sms: 0,
		_do1_alarm_act: 0,
		_do1_keep_on: 0,
		_do1_power_on_status: 0,
		_do1_pulse_delay: 0,
		_do1_pulse_low: 0,
		_do1_pulse_high: 0,
		_do1_pulse_output: 0,
		_do1_sms_recv_cont: 0,
		_do1_sms_send_cont: 0,
		_do1_sms_manger_1: 0,
		_do1_sms_manger_2: 0
	};

	if (!v_range('_gp0_flt', quiet, 1, 100)) return 0;
	if (!v_range('_gp0_ctrig', quiet, 0, 100)) return 0;
	if (!v_range('_gp0_cperiod', quiet, 0, 30000)) return 0;
	if (!v_range('_gp0_recov', quiet, 0, 30000)) return 0;
	
	if (!v_range('_gp1_flt', quiet, 1, 100)) return 0;
	if (!v_range('_gp1_ctrig', quiet, 0, 100)) return 0;
	if (!v_range('_gp1_cperiod', quiet, 0, 30000)) return 0;
	if (!v_range('_gp1_recov', quiet, 0, 30000)) return 0;
	
	if (!v_range('_do_keep_on', quiet, 0, 2550)) return 0;
	if (!v_range('_do_pulse_delay', quiet, 0, 300)) return 0;
	if (!v_range('_do_pulse_low', quiet, 1, 300)) return 0;
	if (!v_range('_do_pulse_high', quiet, 1, 300)) return 0;
	if (!v_range('_do_pulse_output', quiet, 1, 1000)) return 0;
	
	if (!v_range('_do1_keep_on', quiet, 0, 2550)) return 0;
	if (!v_range('_do1_pulse_delay', quiet, 0, 300)) return 0;
	if (!v_range('_do1_pulse_low', quiet, 1, 300)) return 0;
	if (!v_range('_do1_pulse_high', quiet, 1, 300)) return 0;
	if (!v_range('_do1_pulse_output', quiet, 1, 1000)) return 0;

	c = E('_f_gp0_enable').checked;
	if (c == '1')
	{
		vis._gp0_mode = 1;
		vis._gp0_flt = 1;
		vis._gp0_ctrig = 1;
		vis._gp0_cperiod = 1;
		vis._gp0_recov = 1;
		vis._gp0_cact = 1;
		vis._gp0_power_on_counter = 1;
		vis._f_gp0_trig_sms = 1;
		
		c = E('_gp0_mode').value;
		switch(c) {
			case '0':
			case '1':
				vis._gp0_ctrig = 0;
				vis._gp0_cperiod = 0;
				vis._gp0_recov = 0;
				vis._gp0_cact = 0;
				vis._gp0_power_on_counter = 0;
				break;
			case '2':
				break;
		}
		
		c = E('_f_gp0_trig_sms').checked;
		if (c == '1')
		{
			vis._gp0_sms_cont = 1;
			vis._gp0_sms_recv = 1;
			vis._gp0_sms_recv_bak = 1;
		}
	}
	
	c = E('_f_gp1_enable').checked;
	if (c == '1')
	{
		vis._gp1_mode = 1;
		vis._gp1_flt = 1;
		vis._gp1_ctrig = 1;
		vis._gp1_cperiod = 1;
		vis._gp1_recov = 1;
		vis._gp1_cact = 1;
		vis._gp1_power_on_counter = 1;
		vis._f_gp1_trig_sms = 1;
		
	
		c = E('_gp1_mode').value;
		switch(c) {
			case '0':
			case '1':
				vis._gp1_ctrig = 0;
				vis._gp1_cperiod = 0;
				vis._gp1_recov = 0;
				vis._gp1_cact = 0;
				vis._gp1_power_on_counter = 0;
				break;
			case '2':
				break;
		}
		
		c = E('_f_gp1_trig_sms').checked;
		if (c == '1')
		{
			vis._gp1_sms_cont = 1;
			vis._gp1_sms_recv = 1;
			vis._gp1_sms_recv_bak = 1;
		}
	}
	

	c = E('_f_do_enable').checked;
	if (c == '1')
	{
		vis._f_do_alarm_src_di = 1;
		vis._f_do_alarm_src_sms = 1;
		vis._do_alarm_act = 1;
		vis._do_keep_on = 1;
		vis._do_power_on_status = 1;
		vis._do_pulse_delay = 1;
		vis._do_pulse_low = 1;
		vis._do_pulse_high = 1;
		vis._do_pulse_output = 1;

		c = E('_do_alarm_act').value;
		switch(c)
		{
			case '0':
			case '1':
				vis._do_pulse_delay = 0;
				vis._do_pulse_low = 0;
				vis._do_pulse_high = 0;
				vis._do_pulse_output = 0;
				break;
			case '2':
				break;
		}

		c = E('_f_do_alarm_src_sms').checked;
		if (c == '1')
		{
			vis._do_sms_recv_cont = 1;
			vis._do_sms_send_cont = 1;
			vis._do_sms_manger_1 = 1;
			vis._do_sms_manger_2 = 1;
		}
	}

	
	c = E('_f_do1_enable').checked;
	if (c == '1')
	{
		vis._f_do1_alarm_src_di = 1;
		vis._f_do1_alarm_src_sms = 1;
		vis._do1_alarm_act = 1;
		vis._do1_keep_on = 1;
		vis._do1_power_on_status = 1;
		vis._do1_pulse_delay = 1;
		vis._do1_pulse_low = 1;
		vis._do1_pulse_high = 1;
		vis._do1_pulse_output = 1;

		c = E('_do1_alarm_act').value;
		switch(c)
		{
			case '0':
			case '1':
				vis._do1_pulse_delay = 0;
				vis._do1_pulse_low = 0;
				vis._do1_pulse_high = 0;
				vis._do1_pulse_output = 0;
				break;
			case '2':
				break;
		}

		c = E('_f_do1_alarm_src_sms').checked;
		if (c == '1')
		{
			vis._do1_sms_recv_cont = 1;
			vis._do1_sms_send_cont = 1;
			vis._do1_sms_manger_1 = 1;
			vis._do1_sms_manger_2 = 1;
		}
	}
	
	for (a in vis) {
		b = E(a);
		c = vis[a];
		b.disabled = (c != 1);
		PR(b).style.display = c ? '' : 'none';
	}
	// console.log(bi.r_type);
	//G51 3pin
	if(bi.r_type.indexOf("G51") != -1)
	{
		E('_f_do1_enable').disabled = 0;
	}
	else if(bi.r_type.indexOf("G92") != -1)
	{
		E('_f_do1_enable').disabled = 1;
	}
	else
	{
		E('_f_do1_enable').disabled = 1;
	}

	if(bi.r_type.indexOf("R23") != -1)
	{
		E('_f_gp1_enable').disabled = 1;
	}

	//MT7621
	if(bi.r_type.indexOf("G") != -1)
	{
		E('_gp0_mode').disabled = 1;
		E('_gp1_mode').disabled = 1;
		E('_gp0_mode').value = 1;
		E('_gp1_mode').value = 0;
	}
	else//MT7628
	{
		E('_gp0_mode').disabled = 0;
		E('_gp1_mode').disabled = 0;
	}
	return ok;
}

function save()
{
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  
  fom.gp0_enable.value = E('_f_gp0_enable').checked ? 1 : 0;
  fom.gp1_enable.value = E('_f_gp1_enable').checked ? 1 : 0;
  fom.do_enable.value = E('_f_do_enable').checked ? 1 : 0;
  fom.do1_enable.value = E('_f_do1_enable').checked ? 1 : 0;
  fom.gp0_trig_sms.value = E('_f_gp0_trig_sms').checked ? 1 : 0;
  fom.gp1_trig_sms.value = E('_f_gp1_trig_sms').checked ? 1 : 0;
  fom.do_alarm_src_di.value = E('_f_do_alarm_src_di').checked ? 1 : 0;
  fom.do_alarm_src_sms.value = E('_f_do_alarm_src_sms').checked ? 1 : 0;
  fom.do1_alarm_src_di.value = E('_f_do1_alarm_src_di').checked ? 1 : 0;
  fom.do1_alarm_src_sms.value = E('_f_do1_alarm_src_sms').checked ? 1 : 0;
  //form.submit('_fom', 0);
  if(1)//confirm("<% translate("All the settings would take to effect when reboot the router, are you sure reboot"); %>?"))
  {
	fom._service.disabled = 1;
	fom._reboot.value = '1';
	// form.submit('_fom', 0);
	return submit_form('_fom');
  }
  else
  {
	return;
  } 
}

function init()
{
}
	</script>
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#admin-gpctl.asp'>
				<input type='hidden' name='_reboot' value='0'>
				<input type='hidden' name='_service' value='*'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_sleep' value='3'>
				<input type='hidden' name='gp0_enable'>
				<input type='hidden' name='gp1_enable'>
				<input type='hidden' name='gp0_trig_sms'>
				<input type='hidden' name='gp1_trig_sms'>

				<input type='hidden' name='do_enable'>
				<input type='hidden' name='do_alarm_src_di'>
				<input type='hidden' name='do_alarm_src_sms'>
				<input type='hidden' name='do1_enable'>
				<input type='hidden' name='do1_alarm_src_di'>
				<input type='hidden' name='do1_alarm_src_sms'>
				<div class="box" data-box="config-section">
					<div class="heading"><script type="text/javascript">document.write($lang.DI_SETTING)</script></div>
					<div class="content" >
						<div id="config-section1"></div>
					</div>
				</div>
				<div class="box" data-box="config-section">
					<div class="heading"><script type="text/javascript">document.write($lang.DO_SETTING)</script></div>
					<div class="content" >
						<div id="config-section2"></div>
					</div>
				</div>
			</form>

			<script type='text/javascript'>

$('#config-section1').forms([
	{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, multi: [
		{ name: 'f_gp0_enable', type: 'checkbox',  prefix: $lang.GPS_PORT + '1&nbsp&nbsp', value: nvram.gp0_enable == '1'},
		{ name: 'f_gp1_enable', type: 'checkbox', prefix: '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'+ $lang.SERVER_PORT +'2&nbsp&nbsp', value: nvram.gp1_enable == '1' } ] },
	null,
	{ title: $lang.GPS_PORT + '1' + $lang.NET_MODE, name: 'gp0_mode', type: 'select', options: [[0,'OFF'],[1,'ON'],[2,'EVENT_COUNTER']],
		value:  nvram.gp0_mode},
	{ title: $lang.FILTER, name: 'gp0_flt', type: 'text', maxlen: 14, size: 15, value: nvram.gp0_flt, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_TRIGGER, name: 'gp0_ctrig', type: 'text', maxlen: 14, size: 15, value: nvram.gp0_ctrig },
	{ title: $lang.COUNTER_PERIOD, name: 'gp0_cperiod', type: 'text', maxlen: 14, size: 15, value: nvram.gp0_cperiod, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_RECOVER, name: 'gp0_recov', type: 'text', maxlen: 14, size: 15, value: nvram.gp0_recov, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_ACTIVE, name: 'gp0_cact', type: 'select', options: [[0,'HI_TO_LO'],[1,'LO_TO_HI']], 
		value:  nvram.gp0_cact},
	{ title: $lang.COUNTER_START, name: 'gp0_power_on_counter', type: 'select', options: [[1,'POWER_ON']], value: nvram.gp0_power_on_counter },
	{ title: $lang.SMS_ALARM, name: 'f_gp0_trig_sms', type: 'checkbox', value: nvram.gp0_trig_sms == '1' },
	{ title: $lang.SMS_CONTENT, name: 'gp0_sms_cont', type: 'text', maxlen: 70, size: 71, value: nvram.gp0_sms_cont,suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'  },
	{ title: $lang.SMS_RECEIVER_NUM + '1', name: 'gp0_sms_recv', type: 'text', maxlen: 20, size: 21, value: nvram.gp0_sms_recv },
	{ title: $lang.SMS_RECEIVER_NUM + '2', name: 'gp0_sms_recv_bak', type: 'text', maxlen: 20, size: 21, value: nvram.gp0_sms_recv_bak, suffix: ' <small>'+ $lang.VAR_BACKUP +'</small>' },
	null,
	{ title: $lang.GPS_PORT + '2' + $lang.NET_MODE, name: 'gp1_mode', type: 'select', options: [[0,'OFF'],[1,'ON'],[2,'EVENT_COUNTER']],
		value:  nvram.gp1_mode},
	{ title: $lang.FILTER, name: 'gp1_flt', type: 'text', maxlen: 14, size: 15, value: nvram.gp1_flt, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_TRIGGER, name: 'gp1_ctrig', type: 'text', maxlen: 14, size: 15, value: nvram.gp1_ctrig },
	{ title: $lang.COUNTER_PERIOD, name: 'gp1_cperiod', type: 'text', maxlen: 14, size: 15, value: nvram.gp1_cperiod, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_RECOVER, name: 'gp1_recov', type: 'text', maxlen: 14, size: 15, value: nvram.gp1_recov, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.COUNTER_ACTIVE, name: 'gp1_cact', type: 'select', options: [[0,'HI_TO_LO'],[1,'LO_TO_HI']], 
		value:  nvram.gp1_cact},
	{ title: $lang.COUNTER_START, name: 'gp1_power_on_counter', type: 'select', options: [[1,'POWER_ON']], value: nvram.gp1_power_on_counter },
	{ title: $lang.SMS_ALARM, name: 'f_gp1_trig_sms', type: 'checkbox', value: nvram.gp1_trig_sms == '1'},
	{ title: $lang.SMS_CONTENT, name: 'gp1_sms_cont', type: 'text', maxlen: 70, size: 71, value: nvram.gp1_sms_cont, suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'},
	{ title: $lang.SMS_RECEIVER_NUM + '1', name: 'gp1_sms_recv', type: 'text', maxlen: 20, size: 21, value: nvram.gp1_sms_recv },
	{ title: $lang.SMS_RECEIVER_NUM + '2', name: 'gp1_sms_recv_bak', type: 'text', maxlen: 20, size: 21, value: nvram.gp1_sms_recv_bak, suffix: ' <small>'+ $lang.VAR_BACKUP +'</small>' }
], { align: 'left' });

$('#config-section2').forms([
	{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, multi: [
		{ name: 'f_do_enable', type: 'checkbox',  prefix: $lang.SERVER_PORT + '1&nbsp&nbsp', value: nvram.do_enable == '1'},
		{ name: 'f_do1_enable', type: 'checkbox', prefix: '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'+ $lang.SERVER_PORT +'2&nbsp&nbsp', value: nvram.do1_enable == '1' } ] },
	{ title: $lang.ALARM_SOURCE, multi: [
		{ name: 'f_do_alarm_src_di', type: 'checkbox',  prefix: $lang.DI_CONTROL + '&nbsp&nbsp', value: nvram.do_alarm_src_di == '1'},
		{ name: 'f_do_alarm_src_sms', type: 'checkbox', prefix: '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'+ $lang.SMS_CONTROL +'&nbsp&nbsp', value: nvram.do_alarm_src_sms == '1' } ] },
	{ title: $lang.ALARM_ACTION, name: 'do_alarm_act', type: 'select', options: [[0,'ON'],[1,'OFF'],[2,'Pulse']], 
		value: nvram.do_alarm_act},
	{ title: $lang.POWER_ON_STATUS, name: 'do_power_on_status', type: 'select', options: [[0,'OFF'],[1,'ON']], 
		value: nvram.do_power_on_status},
	{ title: $lang.DELAY, name: 'do_pulse_delay', type: 'text', maxlen: 14, size: 15, value: nvram.do_pulse_delay, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.LOW_LEVEL, name: 'do_pulse_low', type: 'text', maxlen: 14, size: 15, value: nvram.do_pulse_low, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.HIGH_LEVEL, name: 'do_pulse_high', type: 'text', maxlen: 14, size: 15, value: nvram.do_pulse_high, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.OUTPUT, name: 'do_pulse_output', type: 'text', maxlen: 14, size: 15, value: nvram.do_pulse_output },
	{ title: $lang.CONTINUED_TIME, name: 'do_keep_on', type: 'text', maxlen: 14, size: 15, value: nvram.do_keep_on, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.SMS_TRIGGER_CONTENT, name: 'do_sms_recv_cont', type: 'text', maxlen: 70, size: 71, value: nvram.do_sms_recv_cont, suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'},
	{ title: $lang.SMS_REPLY_CONTENT, name: 'do_sms_send_cont', type: 'text', maxlen: 70, size: 71, value: nvram.do_sms_send_cont, suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'},
	{ title: $lang.SMS_ADMIN_NUM + '1', name: 'do_sms_manger_1', type: 'text', maxlen: 20, size: 21, value: nvram.do_sms_manger_1 },
	{ title: $lang.SMS_ADMIN_NUM + '2', name: 'do_sms_manger_2', type: 'text', maxlen: 20, size: 21, value: nvram.do_sms_manger_2, suffix: ' <small>'+ $lang.VAR_BACKUP +'</small>' },
	null,
	{ title: $lang.ALARM_SOURCE, multi: [
		{ name: 'f_do1_alarm_src_di', type: 'checkbox',  prefix: $lang.DI_CONTROL + '&nbsp&nbsp', value: nvram.do1_alarm_src_di == '1'},
		{ name: 'f_do1_alarm_src_sms', type: 'checkbox', prefix: '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'+ $lang.SMS_CONTROL +'&nbsp&nbsp', value: nvram.do1_alarm_src_sms == '1' } ] },
	{ title: $lang.ALARM_ACTION, name: 'do1_alarm_act', type: 'select', options: [[0,'ON'],[1,'OFF'],[2,'Pulse']], 
		value: nvram.do1_alarm_act},
	{ title: $lang.POWER_ON_STATUS, name: 'do1_power_on_status', type: 'select', options: [[0,'OFF'],[1,'ON']], 
		value: nvram.do1_power_on_status},
	{ title: $lang.DELAY, name: 'do1_pulse_delay', type: 'text', maxlen: 14, size: 15, value: nvram.do1_pulse_delay, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.LOW_LEVEL, name: 'do1_pulse_low', type: 'text', maxlen: 14, size: 15, value: nvram.do1_pulse_low, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.HIGH_LEVEL, name: 'do1_pulse_high', type: 'text', maxlen: 14, size: 15, value: nvram.do1_pulse_high, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.OUTPUT, name: 'do1_pulse_output', type: 'text', maxlen: 14, size: 15, value: nvram.do1_pulse_output },
	{ title: $lang.CONTINUED_TIME, name: 'do1_keep_on', type: 'text', maxlen: 14, size: 15, value: nvram.do1_keep_on, suffix: ' <small>(*100ms)</small>' },
	{ title: $lang.SMS_TRIGGER_CONTENT, name: 'do1_sms_recv_cont', type: 'text', maxlen: 70, size: 71, value: nvram.do1_sms_recv_cont, suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'},
	{ title: $lang.SMS_REPLY_CONTENT, name: 'do1_sms_send_cont', type: 'text', maxlen: 70, size: 71, value: nvram.do1_sms_send_cont, suffix: ' <small>'+ $lang.ASCII_MAX_70 +'</small>'},
	{ title: $lang.SMS_ADMIN_NUM + '1', name: 'do1_sms_manger_1', type: 'text', maxlen: 20, size: 21, value: nvram.do1_sms_manger_1 },
	{ title: $lang.SMS_ADMIN_NUM + '2', name: 'do1_sms_manger_2', type: 'text', maxlen: 20, size: 21, value: nvram.do1_sms_manger_2, suffix: ' <small>'+ $lang.VAR_BACKUP +'</small>' },
], { align: 'left' });
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
