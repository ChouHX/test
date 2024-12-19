<?PHP include 'header.php'; ?>
<style type='text/css'>

#xtpbasic-grid .co1 ,#xtpbasic-grid .co7, #xtpbasic-grid .co8 {
	width: 10%;
}
#xtpbasic-grid .co2 {
	width: 10%;
}

#xtpbasic-grid .co4 ,#xtpbasic-grid .co3, #xtpbasic-grid .co5, #xtpbasic-grid .co6, #xtpbasic-grid .co9 {
	width: 12%;
}

#l2advanced-grid .co1, #l2advanced-grid .co6 {
	width: 10%;
}
#l2advanced-grid .co2, #l2advanced-grid .co3, #l2advanced-grid .co4, #l2advanced-grid .co5, #l2advanced-grid .co7 {
	width: 13%;
}
#l2advanced-grid .co8 {
	width: 25%;
}

#ppadvanced-grid .co1, #ppadvanced-grid .co6, #ppadvanced-grid .co7 {
	width: 12%;
}
#ppadvanced-grid .co2, #ppadvanced-grid .co3, #ppadvanced-grid .co4, #ppadvanced-grid .co5 {
	width: 13%;
}
#ppadvanced-grid .co8 {
	width: 22%;
}
#xtpschedule-grid .co1 {
	width: 20%;
}
#xtpschedule-grid .co2, #xtpschedule-grid .co3, #xtpschedule-grid .co4 {
	width: 20%;
}
#xtpschedule-grid .co5 {
	width: 20%;
}
</style>
<!-- <script type='text/javascript' src='debug.js'></script> -->
<script type='text/javascript'>

//	<% nvram("xtpbasic,xtpschedule,l2advanced,ppadvanced"); %>
if(!nvram.xtpbasic){
	nvram.xtpbasic = '';
}
if(!nvram.l2advanced){
	nvram.l2advanced = '';
}
if(!nvram.ppadvanced){
	nvram.ppadvanced = '';
}
if(!nvram.xtpschedule){
	nvram.xtpschedule = '';
}

var xtpbasic = new TomatoGrid();
var xtpschedule = new TomatoGrid();
var l2advanced = new TomatoGrid();
var ppadvanced = new TomatoGrid();

xtpbasic.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 3:	// server
	case 8: // local ip
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // proto
	case 6: // firewall
	case 7: // default route
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

xtpbasic.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', ['L2TP', 'PPTP'][data[1]-1],data[2],data[3],data[4],data[5],(data[6] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',(data[7] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>' ,data[8]];
}

xtpbasic.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].value,f[6].checked ? 1 : 0,f[7].checked ? 1 : 0,f[8].value];
}

xtpbasic.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	a = [['2', $lang.VAR_PACKAGE_NAME],['3', $lang.SERVER]];
	for (i = 0;i < a.length;i++)
	{
		if (!f[a[i][0]].value.length)
		{
			ferror.set(f[a[i][0]],a[i][1] + $lang.MUST_NOT_BE_EMPTY,quiet);
			return 0;
		}
	}

	if (!v_nodelim(f[2], quiet, $lang.VAR_PACKAGE_NAME,1) ||(!v_ascii(f[2],quiet))) return 0;
	a = xtpbasic.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[2].value == a[i][2])
		{
			ferror.set(f[2], $lang.NAME_ALREADY_EXISTS,quiet);
			return 0;
		}
	}

/*
	if(!v_ip(f[3], 1) && !v_domain(f[3], 1))
	{
		ferror.set(f[3],'<% translate("Invalid server address"); %>',quiet);
		return 0;
	}
*/
	if (!v_nodelim(f[4], quiet, $lang.PPTP_CLIENT_USERNAME, 1) ||(!v_ascii(f[4],quiet))) return 0;
	if (!v_nodelim(f[5], quiet, $lang.PPTP_CLIENT_PASSWD, 1) ||(!v_ascii(f[5],quiet))) return 0;
	if ((f[8].value.length)&&(!v_ip(f[8], quiet, 1))) return 0;

	return 1;
}

xtpbasic.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].selectedIndex = 0;
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].value = '';
	f[6].checked = 0;
	f[7].checked = 0;
	f[8].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

xtpbasic.setup = function() {
	this.init('xtpbasic-grid', 'sort', 10, [
		{ type: 'checkbox' },
		{ type: 'select', options: [[1, 'L2TP'],[2, 'PPTP']] },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 64 },
		{ type: 'text', maxlen: 64 },
		{ type: 'text', maxlen: 64 },
		{ type: 'checkbox' },
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 15 }]);
		this.headerSet([$lang.VAR_RELAY_ENABLE, $lang.PROTOCOL, $lang.VAR_PACKAGE_NAME, $lang.SERVER, $lang.PPTP_CLIENT_USERNAME, $lang.PPTP_CLIENT_PASSWD, $lang.FIREWALL, $lang.DEFAULT_ROUTE, $lang.LOCAL_IP]);
	var nv = nvram.xtpbasic.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==9)
		{
			t[0] *= 1;
			t[6] *= 1;
			t[7] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5],t[6],t[7],t[8]]);
		}
	}
	xtpbasic.sort(1);
	xtpbasic.showNewEditor();
}

///////////////XTP SCHEDULE///////////////////////

xtpschedule.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 0:	// on
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

xtpschedule.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1],data[2],[$lang.AUTO_SWITCH, $lang.VAR_BACKUP][data[3]-1],data[4]];
}

xtpschedule.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value];
}

xtpschedule.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	a = [['1', $lang.VAR_NAME + '1'],['2', $lang.VAR_NAME + '2']];
	for (i = 0;i < a.length;i++)
	{
		if (!f[a[i][0]].value.length)
		{
			ferror.set(f[a[i][0]],a[i][1] + $lang.MUST_NOT_BE_EMPTY,quiet);
			return 0;
		}
	}

	if(f[1].value == f[2].value)
	{
		ferror.set(f[2], $lang.NAME_ALREADY_EXISTS,quiet);
		return 0;
	}
	if (!v_nodelim(f[1], quiet, $lang.VAR_NAME + '1',1) ||(!v_ascii(f[1],quiet))) return 0;
	if (!v_nodelim(f[2], quiet, $lang.VAR_NAME + '2',1) ||(!v_ascii(f[2],quiet))) return 0;
	a = xtpschedule.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[1].value == a[i][1] || f[1].value == a[i][2])
		{
			ferror.set(f[1], $lang.NAME_ALREADY_EXISTS,quiet);
			return 0;
		}
		if(f[2].value == a[i][1] || f[2].value == a[i][2])
		{
			ferror.set(f[2], $lang.NAME_ALREADY_EXISTS,quiet);
			return 0;
		}
	}

	if (!v_nodelim(f[4], quiet, $lang.VAR_RULE_DESC,1) ||(!v_ascii(f[4],quiet))) return 0;

	return 1;
}

xtpschedule.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].selectedIndex = 0;
	f[4].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

xtpschedule.setup = function() {
	this.init('xtpschedule-grid', 'sort', 10, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 10 },
		{ type: 'select', options: [[1, $lang.AUTO_SWITCH],[2, $lang.VAR_BACKUP]] },
		{ type: 'text', maxlen: 64 }]);
		this.headerSet([$lang.VAR_RELAY_ENABLE, $lang.VAR_NAME + '1', $lang.VAR_NAME + '2', $lang.POLICY, $lang.VAR_RULE_DESC]);
	var nv = nvram.xtpschedule.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==5) 
		{
			t[0] *= 1;
			this.insertData(-1,[t[0],t[1],t[2],t[3],t[4]]);
		}
	}
	xtpschedule.sort(1);
	xtpschedule.showNewEditor();
}

///////////////L2TP ADVANCED//////////////////////

l2advanced.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 0:	// on
	case 2:	// accept dns
	case 3:	// mtu
	case 4:	// mru
	case 5: // tunnel auth
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

l2advanced.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1],['NO', 'YES'][data[2]-1],data[3],data[4],(data[5] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[6],data[7]];
}

l2advanced.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].checked ? 1 : 0,f[6].value,f[7].value];
}

l2advanced.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	a = [['1', $lang.VAR_NAME]];
	for (i = 0;i < a.length;i++)
	{
		if (!f[a[i][0]].value.length)
		{
			ferror.set(f[a[i][0]],a[i][1] + $lang.MUST_NOT_BE_EMPTY,quiet);
			return 0;
		}
	}

	if(!v_nodelim(f[1], quiet, $lang.VAR_NAME,1) ||(!v_ascii(f[1],quiet))) return 0;
	a = l2advanced.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[1].value == a[i][1])
		{
			ferror.set(f[1], $lang.NAME_ALREADY_EXISTS,quiet);
			return 0;
		}
	}
	if((f[3].value.length) && !v_range(f[3], quiet, 128,16384)) return 0;
	if((f[4].value.length) && !v_range(f[4], quiet, 128,16384)) return 0;
	if(!v_nodelim(f[6], quiet, $lang.TUNNEL_PASSWORD,1) ||(!v_ascii(f[6],quiet))) return 0;
	if(!v_nodelim(f[7], quiet, $lang.CUSTOM_DIALING_OPTIONS,1) ||(!v_ascii(f[7],quiet))) return 0;

	return 1;
}

l2advanced.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].selectedIndex = 0;
	f[3].value = '';
	f[4].value = '';
	f[5].checked = 0;
	f[6].value = '';
	f[7].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

l2advanced.setup = function() {
	this.init('l2advanced-grid', 'sort', 5, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'select', options: [[1, 'NO'],[2, 'YES']] },
		{ type: 'text', maxlen: 5 },
		{ type: 'text', maxlen: 5 },
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 256 }]);
		this.headerSet([$lang.VAR_RELAY_ENABLE, $lang.VAR_NAME, $lang.RECEIVE_PEER_DNS, 'MTU', 'MRU', $lang.TUNNEL_AUTHENTICATION, $lang.TUNNEL_PASSWORD, $lang.CUSTOM_DIALING_OPTIONS]);
	var nv = nvram.l2advanced.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==8) 
		{
			t[0] *= 1;
			t[5] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5],t[6],t[7]]);
		}
	}
	l2advanced.sort(1);
	l2advanced.showNewEditor();
}

////////////////PPTP ADVANCED////////////////////

ppadvanced.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 0:	// on
	case 2:	// accept dns
	case 3:	// mtu
	case 4:	// mru
	case 5:	// mppe
	case 6:	// mppe stateful
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

ppadvanced.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1],['NO', 'YES'][data[2]-1],data[3],data[4],(data[5] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',(data[6] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[7]];
}

ppadvanced.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].checked ? 1 : 0,f[6].checked ? 1 : 0,f[7].value];
}

ppadvanced.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	a = [['1', $lang.VAR_NAME]];
	for (i = 0;i < a.length;i++)
	{
		if (!f[a[i][0]].value.length)
		{
			ferror.set(f[a[i][0]],a[i][1] + $lang.MUST_NOT_BE_EMPTY,quiet);
			return 0;
		}
	}

	if(!v_nodelim(f[1], quiet, $lang.VAR_NAME,1) ||(!v_ascii(f[1],quiet))) return 0;
	a = ppadvanced.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[1].value == a[i][1])
		{
			ferror.set(f[1],$lang.NAME_ALREADY_EXISTS,quiet);
			return 0;
		}
	}
	if((f[3].value.length) && !v_range(f[3], quiet, 128,16384)) return 0;
	if((f[4].value.length) && !v_range(f[4], quiet, 128,16384)) return 0;
	if(!v_nodelim(f[7], quiet, $lang.CUSTOM_DIALING_OPTIONS,1) ||(!v_ascii(f[7],quiet))) return 0;

	return 1;
}

ppadvanced.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].selectedIndex = 0;
	f[3].value = '';
	f[4].value = '';
	f[5].checked = 0;
	f[5].checked = 0;
	f[7].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

ppadvanced.setup = function() {
	this.init('ppadvanced-grid', 'sort', 5, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'select', options: [[1, 'NO'],[2, 'YES']] },
		{ type: 'text', maxlen: 5 },
		{ type: 'text', maxlen: 5 },
		{ type: 'checkbox' },
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 256 }]);
		this.headerSet([$lang.VAR_RELAY_ENABLE, $lang.VAR_NAME, $lang.RECEIVE_PEER_DNS, 'MTU', 'MRU', 'MPPE', $lang.MPPE_STATUS_CONNECTION, $lang.CUSTOM_DIALING_OPTIONS]);
	var nv = nvram.ppadvanced.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==8) 
		{
			t[0] *= 1;
			t[5] *= 1;
			t[6] *= 1;
			this.insertData(-1, [t[0],t[1],t[2],t[3],t[4],t[5],t[6],t[7]]);
		}
	}
	ppadvanced.sort(1);
	ppadvanced.showNewEditor();
}

/////////////////////////////////////

function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function save()
{
	if (xtpbasic.isEditing()) return;
	if (xtpschedule.isEditing()) return;
	if (l2advanced.isEditing()) return;
	if (ppadvanced.isEditing()) return;
	
	var fom = E('_fom');
	
	var data = xtpbasic.getAllData().sort(srcSort);
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.xtpbasic.value = s;

	data = xtpschedule.getAllData().sort(srcSort);
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.xtpschedule.value = s;
	
	data = l2advanced.getAllData().sort(srcSort);
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.l2advanced.value = s;
	
	data = ppadvanced.getAllData().sort(srcSort);
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.ppadvanced.value = s;
	
	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function init_xtpbasic()
{
	xtpbasic.recolor();
	xtpbasic.resetNewEditor();
}
function init_xtpschedule()
{
	xtpschedule.recolor();
	xtpschedule.resetNewEditor();
}
function init_l2advanced()
{
	l2advanced.recolor();
	l2advanced.resetNewEditor();
}
function init_ppadvanced()
{
	ppadvanced.recolor();
	ppadvanced.resetNewEditor();
}
</script>

	<form id="_fom" method="post" action="tomato.cgi">
		<input type='hidden' name='_nextpage' value='/#vpn-xtp.asp'>
		<input type='hidden' name='_service' value='vpnxtp-restart'>
		<input type='hidden' name='xtpbasic'>
		<input type='hidden' name='xtpschedule'>
		<input type='hidden' name='l2advanced'>
		<input type='hidden' name='ppadvanced'>

		<div class="box" data-box="basic">
		<div class='heading'>L2TP/PPTP <script type="text/javascript">document.write($lang.BASIC)</script></div>
		<div class='section content'>
			<table class='line-table' cellspacing=1 id='xtpbasic-grid'></table>
			<script type='text/javascript'>xtpbasic.setup(); init_xtpbasic();</script>
		</div>
		</div>

		<div class="box" data-box="l2tpadvanced">
		<div class='heading'>L2TP <script type="text/javascript">document.write($lang.ADVANCED)</script></div>
		<div class='section content'>
			<table class='line-table' cellspacing=1 id='l2advanced-grid'></table>
			<script type='text/javascript'>l2advanced.setup(); init_l2advanced();</script>
		</div>
		</div>

		<div class="box" data-box="pptpadvanced">
		<div class='heading'>PPTP <script type="text/javascript">document.write($lang.ADVANCED)</script></div>
		<div class='section content'>
			<table class='line-table' cellspacing=1 id='ppadvanced-grid'></table>
			<script type='text/javascript'>ppadvanced.setup(); init_ppadvanced();</script>
		</div>
		</div>

		<div class="box" data-box="schedule">
		<div class='heading'><script type="text/javascript">document.write($lang.POLICY)</script></div>
		<div class='section content'>
			<table class='line-table' cellspacing=1 id='xtpschedule-grid'></table>
			<script type='text/javascript'>xtpschedule.setup(); init_xtpschedule();</script>
		</div>
		</div>

	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
