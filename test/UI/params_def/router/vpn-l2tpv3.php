<?PHP include 'header.php'; ?>

<style type='text/css'>
#xtpbasic-grid .co6 ,#xtpbasic-grid .co7 {
	width: 20%;
}
#xtpbasic-grid .co1 ,#xtpbasic-grid .co8 {
	width: 10%;
}
#xtpbasic-grid .co4 ,#xtpbasic-grid .co3, #xtpbasic-grid .co5, #xtpbasic-grid .co2 {
	width: 10%;
}
</style>
<!-- <script type='text/javascript' src='debug.js'></script> -->
<script type='text/javascript'>

//	<% nvram("l2tpv3_session,l2tpv3_tunnel"); %>
if(!nvram.l2tpv3_tunnel){
	nvram.l2tpv3_tunnel = '';
}
if(!nvram.l2tpv3_session){
	nvram.l2tpv3_session = '';
}


var xtpbasic = new TomatoGrid();
var l2advanced = new TomatoGrid();

xtpbasic.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1],data[2],data[3],data[4],data[5],data[6], [$lang.ROUTER, $lang.VAR_GATEWAY, $lang.BRIDGE][data[7]]];
}

xtpbasic.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].value,f[6].value,f[7].value];
}

xtpbasic.verifyFields = function(row, quiet)
{
	var f = fields.getAll(row);
	var i,a;

	/*f[5].value = f[5].value.trim();
	ferror.clear(f[5]);
	if ((f[5].value.length) && (!v_iptaddr(f[5], quiet))) return 0;

	f[6].value = f[6].value.trim();
	ferror.clear(f[6]);
	if ((f[6].value.length) && (!v_iptaddr(f[6], quiet))) return 0;*/

	if((f[1].value.length) && !v_range(f[1], quiet, 0,65535)) return 0;
	if((f[2].value.length) && !v_range(f[2], quiet, 0,65535)) return 0;
	if((f[3].value.length) && !v_range(f[3], quiet, 0,65535)) return 0;
	if((f[4].value.length) && !v_range(f[4], quiet, 0,65535)) return 0;

	return 1;
}

xtpbasic.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].value = '';
	f[6].value = '';
	f[7].selectedIndex = 0;

	ferror.clearAll(fields.getAll(this.newEditor));
}

xtpbasic.setup = function() {
	this.init('xtpbasic-grid', 'sort', 10, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'select', options: [[0, $lang.ROUTER],[1, $lang.VAR_GATEWAY],[2, $lang.BRIDGE]]},
		]);
		this.headerSet([$lang.VAR_ENABLE, $lang.IDX, $lang.TUNNEL_ID, $lang.LOCAL_SESSION_ID, $lang.REMOTE_SESSION_ID, $lang.LOCAL_ADDRESS_AND_MASK, $lang.REMOTE_ADDRESS_AND_MASK, $lang.OPERATING_MODE]);
	var nv = nvram.l2tpv3_session.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==8)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5],t[6],t[7]]);
		}
	}
	xtpbasic.showNewEditor();
	xtpbasic.resetNewEditor();
}
///////////////L2TP ADVANCED//////////////////////

l2advanced.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1],data[2],data[3]];
}

l2advanced.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value];
}

l2advanced.verifyFields = function(row, quiet)
{
	var f = fields.getAll(row);
	var i,a;

	if((f[1].value.length) && !v_range(f[1], quiet, 0,65535)) return 0;
	if((f[2].value.length) && !v_range(f[2], quiet, 0,65535)) return 0;
	//if ((f[3].value.length)&&(!v_ip(f[3], quiet, 1))) return 0;
	return 1;
}

l2advanced.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

l2advanced.setup = function() {
	this.init('l2advanced-grid', 'sort', 5, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.LOCAL_TUNNEL_ID, $lang.REMOTE_TUNNEL_ID, $lang.PPTP_CLIENT_SRVIP]);
	var nv = nvram.l2tpv3_tunnel.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==4)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3]]);
		}
	}
	l2advanced.showNewEditor();
	l2advanced.resetNewEditor();
}

function save()
{
	if (xtpbasic.isEditing()) return;
	if (l2advanced.isEditing()) return;

	var fom = E('_fom');

	var data = xtpbasic.getAllData();	
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.l2tpv3_session.value = r.join('>');	

	data = l2advanced.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.l2tpv3_tunnel.value = r.join('>');

	// form.submit(fom);
	return submit_form('_fom');
}

function init_xtpbasic()
{
	xtpbasic.recolor();
	xtpbasic.resetNewEditor();
}
function init_l2advanced()
{
	l2advanced.recolor();
	l2advanced.resetNewEditor();
}
</script>

		<form id="_fom" method="post" action="tomato.cgi">
		<input type='hidden' name='_nextpage' value='/#vpn-l2tpv3.asp'>
		<input type='hidden' name='_service' value='*'>
		<input type='hidden' name='_reboot' value='1'>
		<input type='hidden' name='l2tpv3_tunnel'>
		<input type='hidden' name='l2tpv3_session'>
		
		<div class="box" data-box="l2tpadvanced">
			<div class='heading'><script type="text/javascript">document.write($lang.TUNNEL_SETTING)</script></div>
			<div class='section content'>
				<table class='line-table' cellspacing=1 id='l2advanced-grid'></table>
				<script type='text/javascript'>l2advanced.setup(); init_l2advanced();</script>
			</div>
		</div>

		<div class="box" data-box="basic">
		<div class='heading'><script type="text/javascript">document.write($lang.SESSION_SETTING)</script></div>
			<div class='section content'>
				<table class='line-table' cellspacing=1 id='xtpbasic-grid'></table>
				<script type='text/javascript'>xtpbasic.setup(); init_xtpbasic();</script>
			</div>
		</div>

	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
