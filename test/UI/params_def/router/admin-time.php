<?PHP include 'header.php'; ?>
	<script type="text/javascript">

//	<% nvram("tm_sel,tm_dst,tm_tz,ntp_updates,ntp_server,ntp_tdod,ntp_kiss"); %>
if(!nvram.ntp_server){
	nvram.ntp_server = '';
}

var ntpList = [
	['custom', $lang.VAR_CUSTOM + '...'],
	['', $lang.VAR_DEFAULT],
	['asia', 'Asia']
];

function ntpString(name)
{
	if (name == '') name = 'pool.ntp.org';
		else name = name + '.pool.ntp.org';
	return '0.' + name + ' 1.' + name + ' 2.' + name;
}

function b_synctime()
{
	var currentTime = new Date();

	var seconds = currentTime.getSeconds();
	var minutes = currentTime.getMinutes();
	var hours = currentTime.getHours();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	var year = currentTime.getFullYear();

	var seconds_str = " ";
	var minutes_str = " ";
	var hours_str = " ";
	var month_str = " ";
	var day_str = " ";
	var year_str = " ";

	if(seconds < 10)
		seconds_str = "0" + seconds;
	else
		seconds_str = ""+seconds;

	if(minutes < 10)
		minutes_str = "0" + minutes;
	else
		minutes_str = ""+minutes;

	if(hours < 10)
		hours_str = "0" + hours;
	else
		hours_str = ""+hours;

	if(month < 10)
		month_str = "0" + month;
	else
		month_str = ""+month;

	if(day < 10)
		day_str = "0" + day;
	else
		day_str = day;

	var tmp = year + "-" + month_str + "-" + day_str + " " + hours_str + ":" + minutes_str + ":" + seconds_str;
	
	form.submitHidden('service.cgi', { _service: 'sync_time', _redirect: '/#admin-time.php', _sleep: 5, _time: tmp });
}

function verifyFields(focused, quiet)
{
	var ok = 1;

	var s = E('_tm_sel').value;
	var f_dst = E('_f_tm_dst');
	var f_tz = E('_f_tm_tz');
	if (s == 'custom') {
		f_dst.disabled = true;
		f_tz.disabled = false;
		PR(f_dst).style.display = 'none';
		PR(f_tz).style.display = '';
	}
	else {
		f_tz.disabled = true;
		PR(f_tz).style.display = 'none';
		PR(f_dst).style.display = '';
		if (s.match(/^([A-Z]+[\d:-]+)[A-Z]+/)) {
			if (!f_dst.checked) s = RegExp.$1;
			f_dst.disabled = false;
		}
		else {
			f_dst.disabled = true;
		}
		f_tz.value = s;
	}

	var a = 1;
	var b = 1;
	switch (E('_ntp_updates').value * 1) {
	case -1:
		b = 0;
	case 0:
		a = 0;
		break;
	}
	elem.display(PR('_f_ntp_tdod'), a);

	elem.display(PR('_f_ntp_server'), b);
	a = (E('_f_ntp_server').value == 'custom');
	elem.display(PR('_f_ntp_1'), PR('_f_ntp_2'), PR('_f_ntp_3'), a && b);

	elem.display(PR('ntp-preset'), !a && b);

	if (a) {
		if ((E('_f_ntp_1').value == '') && (E('_f_ntp_2').value == '') && ((E('_f_ntp_3').value == ''))) {
			ferror.set('_f_ntp_1', $lang.AT_LEAST_ONE_NTP_SERVER_IS_REQUIRED, quiet);
			return 0;
		}
	}
	else {
		E('ntp-preset').innerHTML = ntpString(E('_f_ntp_server').value).replace(/\s+/, ', ');
	}

	ferror.clear('_f_ntp_1');
	return 1;
}

function save(clearKiss)
{
	if (!verifyFields(null, 0)) return;

	var fom, a, i;

	fom = E('_fom');
	fom.tm_dst.value = fom.f_tm_dst.checked ? 1 : 0;
	fom.tm_tz.value = fom.f_tm_tz.value;

	if (E('_f_ntp_server').value != 'custom') {
		fom.ntp_server.value = ntpString(E('_f_ntp_server').value);
	}
	else {
		a = [fom.f_ntp_1.value, fom.f_ntp_2.value, fom.f_ntp_3.value];
		for (i = 0; i < a.length; ) {
			if (a[i] == '') a.splice(i, 1);
				else ++i;
		}
		fom.ntp_server.value = a.join(' ');
	}

	fom.ntp_tdod.value = fom.f_ntp_tdod.checked ? 1 : 0;
	fom.ntp_kiss.disabled = !clearKiss;
	// form.submit(fom);
	return submit_form('_fom');
}

function earlyInit()
{
	if (nvram.ntp_kiss != '') {
		E('ntpkiss-ip').innerHTML = nvram.ntp_kiss;
		E('ntpkiss').style.display = '';
	}
	verifyFields(null, 1);
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.TIME_SETTING)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#admin-time.asp'>
                <input type='hidden' name='_nextwait' value='5'>
                <input type='hidden' name='_service' value='ntpc-restart'>
                <input type='hidden' name='_sleep' value='3'>
                <input type='hidden' name='tm_dst'>
                <input type='hidden' name='tm_tz'>
                <input type='hidden' name='ntp_server'>
                <input type='hidden' name='ntp_tdod'>
                <input type='hidden' name='ntp_kiss' value='' disabled>
				<div id="Timeconfig"></div>
			</form>
            
        </div>
        <div id='ntpkiss' style='display:none'>
<script type="text/javascript">document.write($lang.NTP_SERVER_LOCK_TIP)</script>:
<b id='ntpkiss-ip'></b>
<div>
<input type='button' value='Clear' onclick='save(1)'>
</div>
    </div>
			<script type='text/javascript'>
			ntp = nvram.ntp_server.split(/\s+/);

				ntpSel = 'custom';
				for (i = ntpList.length - 1; i > 0; --i) {
					if (ntpString(ntpList[i][0]) == nvram.ntp_server) ntpSel = ntpList[i][0];
				}

				$('#Timeconfig').forms([
				// { title: $lang.ROUTER_TIME, text: '<span id="clock">' + new Date() + '</span> <input type="button" value="'+ $lang.CLOCK_SYNC +'" class="btn btn-primary" onclick="b_synctime()" >' },
					null,
					{ title: $lang.TIMEZONE, name: 'tm_sel', type: 'select', options: $lang.TM_SEL_ARR, value: nvram.tm_sel },
					{ title: $lang.TM_DST, indent: 2, name: 'f_tm_dst', type: 'checkbox', value: nvram.tm_dst != '0' },
					{ title: $lang.TM_TZ, indent: 2, name: 'f_tm_tz', type: 'text', maxlen: 32, size: 34, value: nvram.tm_tz || '' },
					null,
					{ title: $lang.NTP_UPDATES, name: 'ntp_updates', type: 'select', options: [[-1, $lang.NEVER_SYNC],[0, $lang.UPDATE_ON_STARTUP],[1, $lang.EVERY_1_HOUR],[2, $lang.EVERY_2_HOUR],[4, $lang.EVERY_4_HOUR],[6, $lang.EVERY_6_HOUR],[8, $lang.EVERY_8_HOUR],[12, $lang.EVERY_12_HOUR],[24, $lang.EVERY_OTHER_DAY]],
					value: nvram.ntp_updates },
					{ title: $lang.NTP_TDOD, indent: 2, name: 'f_ntp_tdod', type: 'checkbox', value: nvram.ntp_tdod != '0' },
					{ title: $lang.NTP_SERVER, name: 'f_ntp_server', type: 'select', options: ntpList, value: ntpSel },
					{ title: '&nbsp;', text: '<small><span id="ntp-preset">xx</span></small>', hidden: 1 },
					{ title: '', name: 'f_ntp_1', type: 'text', maxlen: 48, size: 50, value: ntp[0] || 'time.edu.cn', hidden: 1 },
					{ title: '', name: 'f_ntp_2', type: 'text', maxlen: 48, size: 50, value: ntp[1] || '', hidden: 1 },
					{ title: '', name: 'f_ntp_3', type: 'text', maxlen: 48, size: 50, value: ntp[2] || '', hidden: 1 }
					], { align: 'left' });
			</script>
</div>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
