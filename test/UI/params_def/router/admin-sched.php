<?PHP include 'header.php'; ?>
	<style type="text/css">
		textarea {
			width: 95%;
		}
		.empty {
			height: 2em;
		}
	</style>
	<script type="text/javascript">
		//	<% nvram("at_update,tomatoanon_answer,sch_rboot,sch_rcon,sch_c1,sch_c1_cmd,sch_c2,sch_c2_cmd,sch_c3,sch_c3_cmd,sch_c4,sch_c4_cmd,sch_c5,sch_c5_cmd"); %>

		var dowNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
		var dowLow = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
		var scheds = []

		tm = [];
		tm.push([0, timeString(0)]);
		for (i = 15; i < 1440; i += 15) {
			tm.push([i, timeString(i)]);
		}

		tm.push(
			[-1, $lang.EVERY_MINUTE], [-3, $lang.EVERY_3_MINUTES], [-5, $lang.EVERY_5_MINUTES], [-15, $lang.EVERY_15_MINUTES], [-30, $lang.EVERY_30_MINUTES],
	[-60, $lang.EVERY_1_HOUR], [-(12 * 60), $lang.EVERY_12_HOUR], [-(24 * 60), $lang.EVERY_OTHER_DAY],
	['e', $lang.EACH + '...']);

		/* REMOVE-BEGIN

		sch_* = en,time,days

		REMOVE-END */

		function makeSched(key, custom)
		{
			var s, v, w, a, t, i;
			var oe;

			scheds.push(key);

			s = nvram['sch_' + key] || '';
			if ((v = s.match(/^(0|1),(-?\d+),(\d+)$/)) == null) {
				v = custom ? ['', 0, -30, 0] : ['', 0, 0, 0];
			}
			w = v[3] * 1;
			if (w <= 0) w = 0xFF;

			orgkey = key;
			key = key + '_';

			if (custom) {
				t = tm;
			}
			else {
				t = [];
				for (i = 0; i < tm.length; ++i) {
					if ((tm[i][0] >= 0) || (tm[i][0] <= -60) || (tm[i][0] == 'e')) t.push(tm[i]);
				}
			}

			oe = 1;
			for (i = 0; i < t.length; ++i) {
				if (v[2] == t[i][0]) {
					oe = 0;
					break;
				}
			}

			a = [
			{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, name: key + 'enabled', type: 'checkbox', value: v[1] == '1' },
		{ title: $lang.TIME_SETTING, multi: [
			{ name: key + 'time', type: 'select', options: t, value: oe ? 'e' : v[2] },
			{ name: key + 'every', type: 'text', maxlen: 10, size: 10, value: (v[2] < 0) ? -v[2] : 60,
				prefix: ' ', suffix: ' <small id="_' + key + 'mins"><i>'+ $lang.MINUTES +'</i></small>' } ] },
		{ title: $lang.VAR_TIME_ARR[0], multi: [
			{ name: key + 'sun', type: 'checkbox', suffix: $lang.SUN + ' &nbsp; ', value: w & 1 },
			{ name: key + 'mon', type: 'checkbox', suffix: $lang.MON + ' &nbsp; ', value: w & 2 },
			{ name: key + 'tue', type: 'checkbox', suffix: $lang.TUE + ' &nbsp; ', value: w & 4 },
			{ name: key + 'wed', type: 'checkbox', suffix: $lang.WED + ' &nbsp; ', value: w & 8 },
			{ name: key + 'thu', type: 'checkbox', suffix: $lang.THU + ' &nbsp; ', value: w & 16 },
			{ name: key + 'fri', type: 'checkbox', suffix: $lang.FRI + ' &nbsp; ', value: w & 32 },
			{ name: key + 'sat', type: 'checkbox', suffix: $lang.SAT + ' &nbsp; &nbsp;', value: w & 64 },
			{ name: key + 'everyday', type: 'checkbox', suffix: $lang.EVERY_DAY, value: (w & 0x7F) == 0x7F } ] }
			];

			if (custom) {
				a.push({ title: 'Command', name: 'sch_' + key + 'cmd', type: 'textarea', value: nvram['sch_' + key + 'cmd' ] });
			}

			$('.content.' + orgkey).forms(a);
		}

		function verifySched(focused, quiet, key)
		{
			var e, f, i, n, b;
			var eTime, eEvery, eEveryday, eCmd;

			key = '_' + key + '_';

			eTime = E(key + 'time');
			eEvery = E(key + 'every');
			eEvery.style.visibility = E(key + 'mins').style.visibility = (eTime.value == 'e') ? 'visible' : 'hidden';

			eCmd = E('_sch' + key + 'cmd');
			eEveryday = E(key + 'everyday');

			if (E(key + 'enabled').checked) {
				eEveryday.disabled = 0;
				eTime.disabled = 0;
				eEvery.disabled = 0;
				if (eCmd) eCmd.disabled = 0;

				if (focused == eEveryday) {
					for (i = 0; i < 7; ++i) {
						f = E(key + dowLow[i]);
						f.disabled = 0;
						f.checked = eEveryday.checked;
					}
				}
				else {
					n = 0;
					for (i = 0; i < 7; ++i) {
						f = E(key + dowLow[i]);
						f.disabled = 0;
						if (f.checked) ++n;
					}
					eEveryday.checked = (n == 7);
				}

				if ((eTime.value == 'e') && (!v_mins(eEvery, quiet, eCmd ? 1 : 60, 60 * 24 * 60))) return 0;

				if ((eCmd) && (!v_length(eCmd, quiet, quiet ? 0 : 1, 2048))) return 0;
			}
			else {
				eEveryday.disabled = 1;
				eTime.disabled = 1;
				eEvery.disabled = 1;
				for (i = 0; i < 7; ++i) {
					E(key + dowLow[i]).disabled = 1;
				}
				if (eCmd) eCmd.disabled = 1;
			}

			if (eCmd) {
				if ((eCmd.value.length) || (!eTime.disabled)) {
					elem.removeClass(eCmd, 'empty');
				}
				else {
					elem.addClass(eCmd, 'empty');
				}
			}

			return 1;
		}

		function verifyFields(focused, quiet)
		{
			for (var i = 0; i < scheds.length; ++i) {
				if (!verifySched(focused, quiet, scheds[i])) return 0;
			}
			return 1;
		}

		function saveSched(fom, key)
		{
			var s, i, n, k, en, e;

			k = '_' + key + '_';

			en = E(k + 'enabled').checked;
			s = en ? '1' : '0';
			s += ',';

			e = E(k + 'time').value;
			if (e == 'e') s += -(E(k + 'every').value * 1);
			else s += e;

			n = 0;
			for (i = 0; i < 7; ++i) {
				if (E(k + dowLow[i]).checked) n |= (1 << i);
			}
			if (n == 0) {
				n = 0x7F;
				e = E(k + 'everyday');
				e.checked = 1;
				verifySched(e, key);
			}

			e = fom['sch_' + key];
			e.value = s + ',' + n;
		}

		function save()
		{
			var fom, i

			if (!verifyFields(null, false)) return;

			fom = E('_fom');
			for (i = 0; i < scheds.length; ++i) {
				saveSched(fom, scheds[i]);
			}

			// form.submit(fom, 1);
			return submit_form('_fom');
		}

		function init() {
			verifyFields(null, 1);
		}
	</script>

	<form name="_fom" id="_fom" method="post" action="tomato.cgi">
		<input type="hidden" name="_nextpage" value="/#admin-sched.asp">
		<input type="hidden" name="_service" value="sched-restart">
		<input type="hidden" name="sch_rboot" value="">
		<input type="hidden" name="sch_rcon" value="">
		<input type="hidden" name="sch_c1" value="">
		<input type="hidden" name="sch_c2" value="">
		<input type="hidden" name="sch_c3" value="">
		<input type="hidden" name="sch_c4" value="">
		<input type="hidden" name="sch_c5" value="">

		<div class="box" data-box="sched-reboot">
			<div class="heading"><script type="text/javascript">document.write($lang.SCHEDULED_REBOOT)</script></div>
			<div class="content rboot"></div>
		</div>
		<script type="text/javascript">
			makeSched("rboot");
		</script>


		<div class="box" data-box="sched-reconnect" style='display:none'>
			<div class="heading">Reconnect</div>
			<div class="content rcon"></div>
			<script type="text/javascript">
				makeSched("rcon");
			</script>
		</div>

		<div class="box" data-box="sched-cust1" style='display:none'>
			<div class="heading">Custom 1</div>
			<div class="content c1"></div>
			<script type="text/javascript">
				makeSched("c1", 1);
			</script>
		</div>

		<div class="box" data-box="sched-cust2" style='display:none'>
			<div class="heading">Custom 2</div>
			<div class="content c2"></div>
			<script type="text/javascript">
				makeSched("c2", 1);
			</script>
		</div>

		<div class="box" data-box="sched-cust3" style='display:none'>
			<div class="heading">Custom 3</div>
			<div class="content c3"></div>
			<script type="text/javascript">
				makeSched("c3", 1);
			</script>
		</div>

		<div class="box" data-box="sched-cust4" style='display:none'>
			<div class="heading">Custom 4</div>
			<div class="content c4"></div>
			<script type="text/javascript">
				makeSched("c4", 1);
			</script>
		</div>

		<div class="box" data-box="sched-cust5" style='display:none'>
			<div class="heading">Custom 5</div>
			<div class="content c5"></div>
			<script type="text/javascript">
				makeSched("c5", 1);
			</script>
		</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	</form>
	<script type="text/javascript">init();</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
