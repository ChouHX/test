<!--
--><title><% translate("IP Traffic Monitoring"); %></title>
<content>
	<script type="text/javascript">

		// <% nvram("at_update,tomatoanon_answer,cstats_enable,cstats_path,cstats_stime,cstats_offset,cstats_exclude,cstats_include,cstats_sshut,et0macaddr,cifs1,cifs2,jffs2_on,cstats_bak,cstats_all,cstats_labels"); %>

		function fix(name)
		{
			var i;
			if (((i = name.lastIndexOf('/')) > 0) || ((i = name.lastIndexOf('\\')) > 0))
				name = name.substring(i + 1, name.length);
			return name;
		}
		function backupNameChanged()
		{
			if (location.href.match(/^(http.+?\/.+\/)/)) {
				// E('backup-link').href = RegExp.$1 + 'ipt/' + fix(E('backup-name').value) + '.gz?_http_id=' + nvram.http_id; // Not Required since we replaced link with button
			}
		}
		function backupButton()
		{
			var name;
			name = fix(E('backup-name').value);
			if (name.length <= 1) {
				alert('<% translate("Invalid filename"); %>');
				return;
			}

			location.href = '/ipt/' + name + '.gz?_http_id=' + nvram.http_id;
		}
		function restoreButton() {

			var fom;
			var name;
			var i;
			name = fix(E('restore-name').value);
			name = name.toLowerCase();

			if ((name.length <= 3) || (name.substring(name.length - 3, name.length).toLowerCase() != '.gz')) {
				alert('<% translate("Incorrect filename. Expecting a"); %> ".gz".');
				return false;
			}

			if (!confirm('<% translate("Restore data from"); %> ' + name + '<% translate("?"); %>')) return;
			E('restore-button').disabled = 1;
			fields.disableAll(E('_fom'), 1);
			E('restore-form').submit();

		}

		function getPath()
		{
			var s = E('_f_loc').value;
			return (s == '*user') ? E('_f_user').value : s;
		}
		function verifyFields(focused, quiet) {

			var b, v;
			var path;
			var eLoc, eUser, eTime, eOfs;
			var bak;
			var eInc, eExc, eAll, eBak, eLab;

			eLoc = E('_f_loc');
			eUser = E('_f_user');
			eTime = E('_cstats_stime');
			eOfs = E('_cstats_offset');

			eInc = E('_cstats_include');
			eExc = E('_cstats_exclude');
			eAll = E('_f_all');
			eBak = E('_f_bak');

			eLab = E('_cstats_labels');

			b = !E('_f_cstats_enable').checked;
			eLoc.disabled = b;
			eUser.disabled = b;
			eTime.disabled = b;
			eOfs.disabled = b;
			eInc.disabled = b;
			eExc.disabled = b;
			eAll.disabled = b;
			eBak.disabled = b;
			eLab.disabled = b;
			E('_f_new').disabled = b;
			E('_f_sshut').disabled = b;
			E('backup-button').disabled = b;
			E('backup-name').disabled = b;
			E('restore-button').disabled = b;
			E('restore-name').disabled = b;
			ferror.clear(eLoc);
			ferror.clear(eUser);
			ferror.clear(eOfs);
			if (b) return 1;

			eInc.disabled = eAll.checked;

			path = getPath();
			E('newmsg').style.visibility = ((nvram.cstats_path != path) && (path != '*nvram') && (path != '')) ? 'visible' : 'hidden';

			bak = 0;
			v = eLoc.value;
			b = (v == '*user');
			elem.display(eUser, b);
			if (b) {
				if (!v_length(eUser, quiet, 2)) return 0;
				if (path.substr(0, 1) != '/') {
					ferror.set(eUser, '<% translate("Please start at the / root directory"); %>.', quiet);
					return 0;
				}
			}
			else if (v == '/jffs/') {
				if (nvram.jffs2_on != '1') {
					ferror.set(eLoc, 'JFFS2 <% translate("is not enabled"); %>.', quiet);
					return 0;
				}
			}
			else if (v.match(/^\/cifs(1|2)\/$/)) {
				if (nvram['cifs' + RegExp.$1].substr(0, 1) != '1') {
					ferror.set(eLoc, 'CIFS #' + RegExp.$1 + ' <% translate("is not enabled"); %>.', quiet);
					return 0;
				}
			}
			else {
				bak = 1;
			}

			E('_f_bak').disabled = bak;

			return v_range(eOfs, quiet, 1, 31);
		}
		function save()
		{
			var fom, path, en, e, aj;
			if (!verifyFields(null, false)) return;
			aj = 1;
			en = E('_f_cstats_enable').checked;
			fom = E('_fom');
			fom._service.value = 'cstats-restart';
			if (en) {
				path = getPath();
				if (((E('_cstats_stime').value * 1) <= 48) &&
					((path == '*nvram') || (path == '/jffs/'))) {
					if (!confirm('<% translate("Frequent saving to NVRAM or JFFS2 is not recommended. Continue anyway"); %>?')) return;
				}
				if ((nvram.cstats_path != path) && (fom.cstats_path.value != path) && (path != '') && (path != '*nvram') &&
					(path.substr(path.length - 1, 1) != '/')) {
					if (!confirm('<% translate("Note"); %>: ' + path + '<% translate("will be treated as a file. If this is a directory, please use a trailing /. Continue anyway"); %>?')) return;
				}
				fom.cstats_path.value = path;
				if (E('_f_new').checked) {
					fom._service.value = 'cstatsnew-restart';
					aj = 0;
				}
			}
			fom.cstats_path.disabled = !en;
			fom.cstats_enable.value = en ? 1 : 0;
			fom.cstats_sshut.value = E('_f_sshut').checked ? 1 : 0;
			fom.cstats_bak.value = E('_f_bak').checked ? 1 : 0;
			fom.cstats_all.value = E('_f_all').checked ? 1 : 0;
			e = E('_cstats_exclude');
			e.value = e.value.replace(/\s+/g, ',').replace(/,+/g, ',');
			e = E('_cstats_include');
			e.value = e.value.replace(/\s+/g, ',').replace(/,+/g, ',');
			fields.disableAll(E('backup-section'), 1);
			fields.disableAll(E('restore-section'), 1);
			form.submit(fom, aj);
			if (en) {
				fields.disableAll(E('backup-section'), 0);
				fields.disableAll(E('restore-section'), 0);
			}
		}
		function init() {

			$('#backup-section .input-append').prepend('<input size="40" type="text" size="40" maxlength="64" id="backup-name" name="backup_name" onchange="backupNameChanged()" value="router_cstats_' + nvram.et0macaddr.replace(/:/g, '').toLowerCase() + '">');
			backupNameChanged();

		}
	</script>

	<div class="box">
		<div class="heading"><% translate("IP Traffic Monitoring Settings"); %></div>
		<div class="content">
			<form id="_fom" method="post" action="tomato.cgi">
				<input type="hidden" name="_nextpage" value="/#admin-iptraffic.asp">
				<input type="hidden" name="_service" value="cstats-restart">
				<input type="hidden" name="cstats_enable">
				<input type="hidden" name="cstats_path">
				<input type="hidden" name="cstats_sshut">
				<input type="hidden" name="cstats_bak">
				<input type="hidden" name="cstats_all">

				<div id="iptconfig"></div>
			</form>

			<script type='text/javascript'>
				switch (nvram.cstats_path) {
					case '':
					case '*nvram':
					case '/jffs/':
					case '/cifs1/':
					case '/cifs2/':
						loc = nvram.cstats_path;
						break;
					default:
						loc = '*user';
						break;
				}
				$('#iptconfig').forms([
					{ title: '<% translate("Enable"); %>', name: 'f_cstats_enable', type: 'checkbox', value: nvram.cstats_enable == '1' },
					{ title: '<% translate("Save History Location"); %>', multi: [
						/* REMOVE-BEGIN
						//	{ name: 'f_loc', type: 'select', options: [['','RAM (Temporary)'],['*nvram','NVRAM'],['/jffs/','JFFS2'],['/cifs1/','CIFS 1'],['/cifs2/','CIFS 2'],['*user','Custom Path']], value: loc },
						REMOVE-END */
						{ name: 'f_loc', type: 'select', options: [['','RAM (<% translate("Temporary"); %>)'],['/jffs/','JFFS2'],['/cifs1/','CIFS 1'],['/cifs2/','CIFS 2'],['*user','<% translate("Custom Path"); %>']], value: loc },
						{ name: 'f_user', type: 'text', maxlen: 48, size: 30, value: nvram.cstats_path }
					] },
					{ title: '<% translate("Save Frequency"); %>', indent: 2, name: 'cstats_stime', type: 'select', value: nvram.cstats_stime, options: [
						[1,'<% translate("Every Hour"); %>'],[2,'<% translate("Every 2 Hours"); %>'],[3,'<% translate("Every 3 Hours"); %>'],[4,'<% translate("Every 4 Hours"); %>'],[5,'<% translate("Every 5 Hours"); %>'],[6,'<% translate("Every 6 Hours"); %>'],
						[9,'<% translate("Every 9 Hours"); %>'],[12,'<% translate("Every 12 Hours"); %>'],[24,'<% translate("Every 24 Hours"); %>'],[48,'<% translate("Every 2 Days"); %>'],[72,'<% translate("Every 3 Days"); %>'],[96,'<% translate("Every 4 Days"); %>'],
						[120,'<% translate("Every 5 Days"); %>'],[144,'<% translate("Every 6 Days"); %>'],[168,'<% translate("Every Week"); %>']] },
					{ title: '<% translate("Save On Shutdown"); %>', indent: 2, name: 'f_sshut', type: 'checkbox', value: nvram.cstats_sshut == '1' },
					{ title: '<% translate("Create New File"); %><br><small>(<% translate("Reset Data"); %>)</small>', indent: 2, name: 'f_new', type: 'checkbox', value: 0,
						suffix: ' &nbsp; <b id="newmsg" style="visibility:hidden"><small><% translate("Enable if this is a new file"); %></small></b>' },
					{ title: '<% translate("Create Backups"); %>', indent: 2, name: 'f_bak', type: 'checkbox', value: nvram.cstats_bak == '1' },
					{ title: '<% translate("First Day Of The Month"); %>', name: 'cstats_offset', type: 'text', value: nvram.cstats_offset, maxlen: 2, size: 4 },
					{ title: '<% translate("Excluded IPs"); %>', help: '<% translate("Comma separated list"); %>', name: 'cstats_exclude', type: 'text', value: nvram.cstats_exclude, maxlen: 512, size: 50 },
					{ title: '<% translate("Included IPs"); %>', help: '<% translate("Comma separated list"); %>', name: 'cstats_include', type: 'text', value: nvram.cstats_include, maxlen: 2048, size: 50 },
					{ title: '<% translate("Enable Auto-Discovery"); %>', name: 'f_all', type: 'checkbox', value: nvram.cstats_all == '1', suffix: '&nbsp;<small>(<% translate("automatically include new IPs in monitoring as soon as any traffic is detected"); %>)</small>' },
					{ title: '<% translate("Labels on graphics"); %>', name: 'cstats_labels', type: 'select', value: nvram.cstats_stime, options: [[0,'<% translate("Show known hostnames and IPs"); %>'],[1,'<% translate("Prefer to show only known hostnames"); %>, <% translate("otherwise show IPs"); %>'],[2,'<% translate("Show only IPs"); %>']], value: nvram.cstats_labels }
					], { align: 'left' });
			</script>
			<div class="row">
				<div class="col-sm-12">
					<h4><% translate("Backup"); %></h4>
					<div class="section" id="backup-section">
						<form>
							<div class="input-append">
								<button name="f_backup_button" id="backup-button" onclick="backupButton(); return false;" class="btn"><% translate("Backup"); %> <i class="icon-download"></i></button>
							</div>
						</form>
					</div>
				</div>

				<div class="col-sm-12">
					<h4><% translate("Restore"); %></h4>
					<div class="section" id="restore-section">
						<form id="restore-form" method="post" action="ipt/restore.cgi?_http_id=<% nv(http_id); %>" encType="multipart/form-data">
							<input class="uploadfile" type="file" size="40" id="restore-name" name="restore_name" accept="application/x-gzip">
							<button type="button" name="f_restore_button" id="restore-button" value="Restore" onclick="restoreButton(); return false;" class="btn"><% translate("Restore"); %> <i class="icon-upload"></i></button>
						</form><br>
					</div>
				</div>

				<div class="col-sm-12">
					<h4><% translate("Notes"); %></h4>
					<ul>
						<li><% translate("IP Traffic is about monitoring IPv4 network traffic flowing throughthe router"); %>.</li>
						<li><% translate("Check your"); %> <a class="ajaxload" href="basic-lan.asp">LAN <% translate("Settings"); %></a> <% translate("before enabling this feature: any/all LAN interfaces must have a netmask with at least 16 bits set"); %> (255.255.0.0).</li>
						<li><% translate("Monitoring of larger subnets is not supported"); %>.</li>
					</ul>

					<p><% translate("Other relevant notes/hints"); %>:</p>
					<ul>
						<li><% translate("Before enabling this feature, please check your"); %> <a class="ajaxload" href="basic-lan.asp">LAN <% translate("Settings"); %></a> <% translate("and make sure the netmask on any/all of your LAN bridges has been configured properly (i.e. netmask with at least 16 bits set or"); %>("255.255.0.0").</li>
						<li><% translate("Although technically supported, it's not actually recommended having IP Traffic monitoring enabled with subnets larger than/the equivalent of a class C network (i.e. netmask with at least 24 bits set or "); %>"255.255.255.0").</li>
						<li><% translate("IP Traffic monitoring keeps track of data/packets that would be either"); %> <i><% translate("coming from/leaving"); %></i> <% translate("or"); %> <i><% translate("going to/arriving"); %></i> <% translate("IPs on LAN interfaces/subnets"); %>.</li>
						
						<!-- VLAN-END -->
					</ul>
				</div>

			</div>
		</div>
	</div>

	<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %> <i class="icon-check"></i></button>
	<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn"><% translate("Cancel"); %> <i class="icon-cancel"></i></button>
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type="text/javascript">init(); verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
