<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram("at_update,tomatoanon_answer,et0macaddr,t_features,t_model_name"); %>
//	<% nvstat(); %>
function backupNameChanged() {
	var name = fixFile(E('backup-name').value);

}
function backupButton()
{
	var name = fixFile(E('backup-name').value);
	if (name.length <= 1) {
		alert('<% translate("Invalid filename"); %>');
		return;
	}
	location.href = 'cfg/' + name + '.cfg?_http_id=' + nvram.http_id;
}
function backupButton_nv()
{
	location.href = 'nvbackup.cgi?_nextpage=/%23admin-config.asp&_http_id=' + nvram.http_id;
}
function restoreButton()
{
	var name, i, f;
	name = fixFile(E('restore-name').value);
	name = name.toLowerCase();
	if ((name.indexOf('.cfg') != (name.length - 4)) && (name.indexOf('.cfg.gz') != (name.length - 7))) {
		alert('<% translate("Incorrect filename"); %>');
		return;
	}
	if (!confirm('<% translate("Are you sure"); %>?')) return;
	E('restore-button').disabled = 1;
	f = E('restore-form');
	form.addIdAction(f);
	f.submit();
}
function resetButton()
{
	var i;
	i = E('restore-mode').value;
	if (i == 0) return;
	if ((i == 2) && (features('!nve'))) {
		if (!confirm('<% translate("admin-config-warn1"); %>' + nvram.t_model_name + '<% translate("admin-config-warn2"); %>?')) return;
	}
	if (!confirm('<% translate("Are you sure"); %>?')) return;
	E('reset-button').disabled = 1;
	form.submit('aco-reset-form');
}
</script>
<div class="box">
<div class="heading"></div>
<div class="content">
<h4><% translate("Backup Configuration"); %></h4>
<div class="section" id="backup">
<div class="input-append"> .cfg &nbsp;
<button name="f_backup_button" onclick="backupButton()" value="Backup" class="btn btn-primary"><% translate("Backup"); %>
<i class="icon-download"></i></button>
</div><br /><hr>
</div>
<h4><% translate("Save As Default Configuration"); %></h4>
<div class='section'>
<form>
<input type='button' name='f_backup_button_nv' onclick='backupButton_nv()' value='<% translate("Save"); %>'><br>
</form>
</div>
<h4><% translate("Restore Configuration"); %></h4>
<div class="section">
<form id="restore-form" method="post" action="cfg/restore.cgi" encType="multipart/form-data">
<% translate("Select the configuration file to restore"); %>:<br>
<input class="uploadfile" type="file" size="40" id="restore-name" name="filename">
<button type="button" name="f_restore_button" id="restore-button" value="Restore" onclick="restoreButton()" class="btn btn-primary"><% translate("Restore"); %><i class="icon-upload"></i></button>
</form><hr>
</div>
<h4><% translate("Restore Default Configuration"); %></h4>
<div class="section">
<form id="aco-reset-form" method="post" action="cfg/defaults.cgi">
<div class="input-append"><select name="mode" id="restore-mode">
<option value=0><% translate("Select"); %>...</option>
<option value=1><% translate("Restore Custom Configuration"); %></option>
<option value=2><% translate("Restore Factory Configuration"); %></option>
</select>
<button type="button" value="OK" onclick="resetButton()" id="reset-button" class="btn btn-primary"><% translate("OK"); %></button>
</div>
</form><hr>
</div>
<div class="section" id="nvram">
<script type="text/javascript">
var a = nvstat.free / nvstat.size * 100.0;
createFieldTable('', [
{ title: '<% translate("Total / Free NVRAM"); %>:', text: scaleSize(nvstat.size) + ' / ' + scaleSize(nvstat.free) + ' <small>(' + (a).toFixed(2) + '%)</small>' }
], '#nvram', 'line-table');
if (a <= 5) {
$('#nvram').append('<div class="alert alert-warning">' +
'<% translate("The NVRAM free space is very low. It is strongly recommended to erase all data in NVRAM memory, and reconfigure the router manually in order to clean up all unused and obsolete entries"); %>.' +
'</div>');
}
$('#backup .input-append').prepend('<input type="text" size="40" maxlength="64" id="backup-name" onchange="backupNameChanged()" value="router_' + ("<% version(); %>".replace(/\./g, "")) + '_m' + nvram.et0macaddr.replace(/:/g, "").substring(6, 12) + '">'); 
</script>
</div>
</div>
</div>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>

