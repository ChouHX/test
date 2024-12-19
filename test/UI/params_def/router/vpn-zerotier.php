<?PHP
include 'header.php';
if (count($_FILES) > 0 && $_FILES['file']['error'] == 0 && $_FILES['file']['type'] == 'application/octet-stream' && $_FILES['file']['size'] <= 1024) {
	$zerotier_planet = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
} else {
	$zerotier_planet = null;
}
?>
<content>
	<script type="text/javascript">
//	<% nvram("zerotier_custom_enable,zerotier_enable_fake,zerotier_id,zerotier_moonid,zerotier_nat,zerotiermoon_enable,zerotiermoon_ip,zerotiermoon_id,zerotier_route_list"); %>
if (!nvram.zerotier_route_list) {
	nvram.zerotier_route_list = '';
}
if (!nvram.uiinfo) {
	nvram.uiinfo = {};
}
function verifyFields(focused, quiet)
{
	var ok = 1, c;

	if(E('_f_zerotier_enable_fake').checked)
	{
		if(!v_length('_zerotier_id', quiet, 16, 16)) return 0;
		if((E('_zerotier_moonid').value.length > 0))
		{
			if(!v_length('_zerotier_moonid', quiet, 10, 10)) return 0;
		}
	}
	if(E('_f_zerotiermoon_enable').checked)
	{
		f = E('_zerotiermoon_ip');
		if ((f.value.length) && (!v_ip('_zerotiermoon_ip', quiet, 1))) return 0;
	}
	if(E('_f_zerotier_nat').checked)
	{
		document.getElementById('zroute-grid').style.display = '';
	}
	else
	{
		document.getElementById('zroute-grid').style.display = 'none';
	}
	return ok;
}
var zroute = new TomatoGrid();

zroute.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1], data[2]];
}

zroute.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value];
}

zroute.verifyFields = function(row, quiet)
{
	var f;

	f = fields.getAll(row);
	return 1;
}

zroute.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].value = 1;
	f[1].value = '';
	f[2].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

zroute.setup = function() {
	this.init('zroute-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 }
		]);
		this.headerSet([$lang.VAR_ENABLE, $lang.VAR_IP, $lang.VAR_GATEWAY]);
	var nv = nvram.zerotier_route_list.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==3)
		{
			this.insertData(-1, [t[0], t[1],t[2]]);
		}
	}
	zroute.showNewEditor();
}
function save()
{
	if (zroute.isEditing()) return;
	var data = zroute.getAllData();
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  fom.zerotier_route_list.value = s;
  fom.zerotier_enable_fake.value = E('_f_zerotier_enable_fake').checked ? "1" : "0";
  fom.zerotier_nat.value = E('_f_zerotier_nat').checked ? "1" : "0";
  fom.zerotiermoon_enable.value = E('_f_zerotiermoon_enable').checked ? "1" : "0";
  fom.zerotier_custom_enable.value = E('_f_zerotier_custom_enable').checked ? "1" : "0";
  // form.submit('_fom');
  return submit_form('_fom');
}
function custom_upload(which)
{
	var fom = document.form_upload;
	E('afu-upload-button').disabled = true;
	fom.submit();
}
function init()
{
	zroute.recolor();
	zroute.resetNewEditor();
}
	</script>

	<div class="box">
		<div class="heading"><script>document.write($lang.ZEROTIER)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#vpn-zerotier.asp'>
				<input type='hidden' name='_nextwait' value='15'>
				<input type='hidden' name='_reboot' value='0'>
				<input type='hidden' name='_service' value='zerotier-restart'>
				<input type='hidden' name='zerotier_enable_fake'>
				<input type='hidden' name='zerotier_nat'>
				<input type='hidden' name='zerotiermoon_enable'>
				<input type='hidden' name='zerotier_custom_enable'>
				<input type='hidden' name='zerotier_route_list'>
				<?PHP if ($zerotier_planet) {
					echo sprintf('<input type="hidden" name="zerotier_planet" value="%s">', $zerotier_planet);
				}?>
				<div id="zroute"></div>
				 <div class='section'>
                    <table class='line-table' cellspacing=1 id='zroute-grid'></table>
                    <script type='text/javascript'>zroute.setup(); init();</script>
                </div>

			</form>
			<script type='text/javascript'>
				$('#zroute').forms([
				{ title: $lang.ENABLE_ZEROTIER_CLIENT, name: 'f_zerotier_enable_fake', type: 'checkbox', value: (nvram.zerotier_enable_fake == '1') },
				{ title: $lang.ZEROTIER_WORD_NETWORK_ID, name: 'zerotier_id', type: 'text', maxlen: 16, size: 32, value: nvram.zerotier_id },
				{ title: $lang.ZEROTIER_MOON_NETWORK_ID, name: 'zerotier_moonid', type: 'text', maxlen: 10, size: 32, value: nvram.zerotier_moonid },
				{ title: $lang.ZEROTIER_ENABLE_NAT, name: 'f_zerotier_nat', type: 'checkbox', value: (nvram.zerotier_nat == '1') },
				null,
				{ title: $lang.ZEROTIER_ENABLE_MOON_SERVER, name: 'f_zerotiermoon_enable', type: 'checkbox', value: (nvram.zerotiermoon_enable == '1') },
				{ title: $lang.ZEROTIER_MOON_SERVER_IP_DOMAIN, name: 'zerotiermoon_ip', type: 'text', maxlen: 128, size: 32, value: nvram.zerotiermoon_ip },
				{ title: $lang.ZEROTIER_MOON_SERVER_ID, text: nvram.zerotiermoon_id },
				null,
				{ title: $lang.ZEROTIER_CUSTOM_PLANET, name: 'f_zerotier_custom_enable', type: 'checkbox', value: (nvram.zerotier_custom_enable == '1') }
					], { align: 'left' });
			</script>
         </div>

		 <div id='file_button'>
		 <div class='heading'><script>document.write($lang.UPLOAD_PLANET_FILE)</script></div>
		 <div class='section content'>
		 <form name='form_upload' method='post' action='' encType='multipart/form-data'>
		 <div id='box-input'>
		 <?PHP if ($zerotier_planet) echo sprintf('<div style="color:#1890ff;"><script>document.write($lang.FILE_UPLOADED)</script>：%s</div>', $_FILES['file']['name']); ?>
		 <input type='file' name='file' size='50' style="vertical-align: middle;"> <button id='afu-upload-button' class="btn btn-danger" onclick='custom_upload()'><script>document.write($lang.VAR_UPLOAD)</script></button>
		 </div>
		 </form>
		 </div>
		 </div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><script>document.write($lang.SAVE_TEMPLATE)</script><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><script>document.write($lang.VAR_BTN_CANCLE)</script><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type='text/javascript'>verifyFields(null, true);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
</content>
<?PHP include 'footer.php'; ?>