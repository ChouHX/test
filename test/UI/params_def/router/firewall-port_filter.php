<?PHP include 'header.php'; ?>
<style type='text/css'>
#fo-grid .co1, #fra-grid .co1 {
	width: 6%;
	text-align: center;
}
#fo-grid .co2, #fra-grid .co2 {
	width: 10%;
}
#fo-grid .co3, #fra-grid .co3 {
	width: 10%;
}
#fo-grid .co4, #fra-grid .co4 {
	width: 10%;
}
#fo-grid .co5, #fra-grid .co5 {
	width: 10%;
}
#fo-grid .co6, #fra-grid .co6 {
	width: 10%;
}
#fo-grid .co7, #fra-grid .co7 {
	width: 10%;
}
#fo-grid .co8, #fra-grid .co8 {
	width: 10%;
}
#fo-grid .co9, #fra-grid .co9 {
	width: 10%;
}
.editor, .header {
	text-align: center;
}
</style>
<script type="text/javascript">
//	<% nvram("weburlfilters,ipportfilterrules,keywordfilters,routeraccessrules"); %>
if(!nvram.ipportfilterrules){
	nvram.ipportfilterrules = '';
}
if(!nvram.keywordfilters){
	nvram.keywordfilters = '';
}
if(!nvram.weburlfilters){
	nvram.weburlfilters = '';
}
if(!nvram.new_qoslimit_rules){
	nvram.new_qoslimit_rules = '';
}
if(!nvram.routeraccessrules){
	nvram.routeraccessrules = '';
}
var fog = new TomatoGrid();
var fra = new TomatoGrid();
var fwkw = new TomatoGrid();
var fwurl = new TomatoGrid();

fog.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 2:	// src
	case 5:	// ia
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // proto
	case 3:	// ext prt
	case 4:	// int prt
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

fog.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', ['IPv4', 'IPv6', 'IPv4/IPv6'][data[1]], data[2]?data[2]:'-',data[3]?data[3]:'any/0',data[4]?data[4]:'any/0',['-','TCP', 'UDP', 'ICMP'][data[5]], data[6]?data[6]:'-', data[7]?data[7]:'-', [$lang.DROP, $lang.ACCEPT][data[8]],data[9]];
}

fog.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value, f[4].value, f[5].value?f[5].value:'0', f[6].value, f[7].value,f[8].value,f[9].value];
}

fog.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;
	
	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if(f[2].value.length && !v_mac(f[2], quiet)) return 0;
	
	f[3].value = f[3].value.trim();
	ferror.clear(f[3]);
	if ((f[3].value.length) && (!v_iptaddr(f[3], quiet))) return 0;

	f[4].value = f[4].value.trim();
	ferror.clear(f[4]);
	if ((f[4].value.length) && (!v_iptaddr(f[4], quiet))) return 0;

	if(f[5].value==3)
	{
		f[6].value='';
		f[7].value='';
		f[7].disabled = true;
		f[6].disabled = true;
	}
	else
	{
		f[7].disabled = false;
		f[6].disabled = false;
	}
	
	f[6].value = f[6].value.trim();
	ferror.clear(f[6]);
	f[7].value = f[7].value.trim();
	ferror.clear(f[7]);
	
	return 1;

}

fog.resetNewEditor = function() {

	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].selectedIndex = 0;
	f[6].value = '';
	f[7].value = '';
	f[8].selectedIndex = 1;
	f[9].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

fog.setup = function() {
	this.init('fo-grid', '', 50, [
		{ type: 'checkbox' },
		{ type: 'select', options: [[0,'IPv4'],[1, 'IPv6'],[2, 'IPv4/IPv6']] },
		{ type: 'text', maxlen: 17 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'select', options: [[0,'NONE'],[1, 'TCP'],[2, 'UDP'],[3,'ICMP']] },
		{ type: 'text', maxlen: 17 },
		{ type: 'text', maxlen: 17 },
		{ type: 'select', options: [[0, $lang.DROP],[1, $lang.ACCEPT]] },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, 'Network Protocol', $lang.SRC_MAC, $lang.SRC_IP, $lang.DST_IP, $lang.PROTOCOL,  $lang.SRC_PORT, $lang.DEST_PORT, $lang.POLICY, $lang.VAR_RULE_DESC]);
	var nv = nvram.ipportfilterrules.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==10)
		{
			t[0] *= 1;
			t[6] = t[6].replace(/:/g, '-');
			t[7] = t[7].replace(/:/g, '-');
			this.insertData(-1, [t[0], t[1],t[2] ,t[3],t[4],t[5],t[6],t[7],t[8],t[9]]);
		}
	}
	fog.showNewEditor();
}

fra.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 2:	// src
	case 5:	// ia
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // proto
	case 3:	// ext prt
	case 4:	// int prt
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

fra.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1]?data[1]:'-',data[2]?data[2]:'any/0',data[3]?data[3]:'any/0',['-','TCP', 'UDP', 'ICMP'][data[4]], data[5]?data[5]:'-', data[6]?data[6]:'-', [$lang.DROP, $lang.ACCEPT][data[7]],data[8]];
}

fra.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value, f[4].value?f[4].value:'0', f[5].value, f[6].value,f[7].value,f[8].value];
}

fra.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;

	f[1].value = f[1].value.trim();
	ferror.clear(f[1]);
	if(f[1].value.length && !v_mac(f[1], quiet)) return 0;

	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if ((f[2].value.length) && (!v_iptaddr(f[2], quiet))) return 0;

	f[3].value = f[3].value.trim();
	ferror.clear(f[3]);
	if ((f[3].value.length) && (!v_iptaddr(f[3], quiet))) return 0;

	if(f[4].value==3)
	{
		f[5].value='';
		f[6].value='';
		f[6].disabled = true;
		f[5].disabled = true;
	}
	else
	{
		f[6].disabled = false;
		f[5].disabled = false;
	}

	f[5].value = f[5].value.trim();
	ferror.clear(f[5]);
	if (f[5].value.length && !v_portrange(f[5], quiet)) return 0;
	f[6].value = f[6].value.trim();
	ferror.clear(f[6]);
	if (f[6].value.length && !v_portrange(f[6], quiet)) return 0;

	return 1;

}

fra.resetNewEditor = function() {

	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].selectedIndex = 0;
	f[5].value = '';
	f[6].value = '';
	f[7].selectedIndex = 1;
	f[8].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

fra.setup = function() {
	this.init('fra-grid', '', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 17 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'select', options: [[0,'NONE'],[1, 'TCP'],[2, 'UDP'],[3,'ICMP']] },
		{ type: 'text', maxlen: 17 },
		{ type: 'text', maxlen: 17 },
		{ type: 'select', options: [[0, $lang.DROP],[1, $lang.ACCEPT]] },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.SRC_MAC, $lang.SRC_IP, $lang.DST_IP, $lang.PROTOCOL, $lang.SRC_PORT, $lang.DEST_PORT, $lang.POLICY, $lang.VAR_RULE_DESC]);
	var nv = nvram.routeraccessrules.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==9)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2] ,t[3],t[4],t[5],t[6],t[7],t[8]]);
		}
	}
	fra.showNewEditor();
}

fwurl.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 2:	// src
	case 5:	// ia
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // proto
	case 3:	// ext prt
	case 4:	// int prt
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

fwurl.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1], data[2]];
}

fwurl.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value];
}

fwurl.verifyFields = function(row, quiet)
{
	var f;

	f = fields.getAll(row);
	if (!v_length(f[1], quiet,2,1024)) return 0;

	return 1;
}

fwurl.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

fwurl.setup = function() {
	this.init('fwurl-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 512 },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.URL, $lang.VAR_RULE_DESC]);
	var nv = nvram.weburlfilters.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==3)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2] ]);
		}
	}
	fwurl.sort(6);
	fwurl.showNewEditor();
}

fwkw.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 2:	// src
	case 5:	// ia
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // proto
	case 3:	// ext prt
	case 4:	// int prt
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

fwkw.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1], data[2]];
}

fwkw.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value];
}

fwkw.verifyFields = function(row, quiet)
{
	var f;

	f = fields.getAll(row);
	if (!v_length(f[1], quiet,2,1024)) return 0;

	return 1;
}

fwkw.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

fwkw.setup = function() {
	this.init('fwkw-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 128 },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.KEYWORDS, $lang.VAR_RULE_DESC]);
	var nv = nvram.keywordfilters.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==3)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2] ]);
		}
	}
	fwkw.sort(6);
	fwkw.showNewEditor();
}
function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function save()
{
	if (fog.isEditing()) return;
	if (fra.isEditing()) return;
	if (fwkw.isEditing()) return;
	if (fwurl.isEditing()) return;

	var data = fog.getAllData();
	var s = '';
	for (var i = 0; i < data.length; ++i) 
	{
		data[i][5] = data[i][5].replace(/-/g, ':');
		data[i][6] = data[i][6].replace(/-/g, ':');
		s += data[i].join('<') + '>';
	}

	var fom = E('_fom');
	fom.ipportfilterrules.value = s;

	data = fra.getAllData();
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}

	fom.routeraccessrules.value = s;

	data = fwurl.getAllData().sort(srcSort);
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}

	fom.weburlfilters.value = s;

	data = fwkw.getAllData().sort(srcSort);
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}

	fom.keywordfilters.value = s;
	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function verifyFields(focused, quiet)
{
	fog.resetNewEditor();
	fra.resetNewEditor();
}

function init()
{
	fog.recolor();
	fog.resetNewEditor();
	fra.recolor();
	fra.resetNewEditor();
	fwkw.recolor();
	fwkw.resetNewEditor();
	fwurl.recolor();
	fwurl.resetNewEditor();
}

	</script>

	<form id="_fom" method="post" action="tomato.cgi">
        <input type='hidden' name='_nextpage' value='/#firewall-port_filter.asp'>
        <input type='hidden' name='_service' value='firewall-restart'>
        
        <input type='hidden' name='ipportfilterrules'>
        <input type='hidden' name='routeraccessrules'>
        <input type='hidden' name='weburlfilters'>
        <input type='hidden' name='keywordfilters'>

<div class="box">
<div class='heading'><script type="text/javascript">document.write($lang.IP_MAC_PORT_FILTER)</script></div>
<div class='section'>
	<table class='line-table' cellspacing=1 id='fo-grid'></table>
	<script type='text/javascript'>fog.setup();</script>
</div>
</div>

<div class="box">
<div class='heading'><script type="text/javascript">document.write($lang.KEYWORDS_FILTER)</script></div>
<div class='section'>
	<table class='line-table' cellspacing=1 id='fwkw-grid'></table>
	<script type='text/javascript'>fwkw.setup();</script>
</div>
</div>


<div class="box">
<div class='heading'><script type="text/javascript">document.write($lang.URL_FILTER)</script></div>
<div class='section'>
	<table class='line-table' cellspacing=1 id='fwurl-grid'></table>
	<script type='text/javascript'>fwurl.setup();</script>
</div>
</div>

<div class="box">
<div class='heading'><script type="text/javascript">document.write($lang.ACCESS_FILTERING)</script></div>
<div class='section'>
	<table class='line-table' cellspacing=1 id='fra-grid'></table>
	<script type='text/javascript'>fra.setup();</script>
</div>
</div>
<script type='text/javascript'>show_notice1('<% notice("iptables"); %>');</script>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
	</form>

	<script type="text/javascript">init();</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
