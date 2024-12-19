<?PHP include 'header.php'; ?>
<style type='text/css'>
#fpr-grid .co1 {
	width: 10%;
	text-align: center;
}
#fpr-grid .co2 {
	width: 10%;
}
#fpr-grid .co3 {
	width: 15%;
}
#fpr-grid .co4 {
	width: 15%;
}
#fpr-grid .co5 {
	width: 15%;
}
#fpr-grid .co6 {
	width: 15%;
}
#fpr-grid .co7 {
	width: 20%;
}
.editor, .header {
	text-align: center;
}
</style>
<script type="text/javascript">

//	<% nvram("portredirect"); %>
if(!nvram.portredirect){
	nvram.portredirect = '';
}


var fpr = new TomatoGrid();
fpr.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
	case 3:	// dst adr
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1:	// proto
	case 2:	// int prt
	case 4:	// ext prt
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

fpr.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',['TCP', 'UDP', 'Both'][data[1] - 1], data[2], data[3], data[4], data[5]];
}

fpr.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value, f[4].value, f[5].value];
}

fpr.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;
	
	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if(!v_port(f[2], quiet)) return 0;
	
	f[3].value = f[3].value.trim();
	ferror.clear(f[3]);
	if (f[3].value == '')
	{
		ferror.set(E(f[3]), $lang.INVALID_ADDRESS, quiet);
		return 0;
	}
	if(!v_iptip(f[3], quiet)) return 0;

	f[4].value = f[4].value.trim();
	ferror.clear(f[4]);
	if (!v_port(f[4], quiet)) return 0;
	
	f[5].value = f[5].value.replace(/>/g, '_');
	if (!v_nodelim(f[5], quiet, 'Description')) return 0;

	return 1;
}

fpr.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].selectedIndex = 0;
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

fpr.setup = function() {
	this.init('fpr-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'select', options: [[1, 'TCP'],[2, 'UDP'],[3,'TCP/UDP']] },
		{ type: 'text', maxlen: 5 },
		{ type: 'text', maxlen: 50 },
		{ type: 'text', maxlen: 5 },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_TERM_TERM_PARAMS_ACTIVATE, $lang.PROTOCOL, $lang.INT_PORT, $lang.EXT_ADDR, $lang.EXT_PORTS, $lang.VAR_RULE_DESC]);

	var nv = nvram.portredirect.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==6)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5]]);
		}
	}
	fpr.sort(6);
	fpr.showNewEditor();
}

function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function save()
{
	if (fpr.isEditing()) return;

	var data = fpr.getAllData().sort(srcSort);
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		s += data[i].join('<') + '>';
	}
	var fom = E('_fom');
	fom.portredirect.value = s;
	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function init()
{
	fpr.recolor();
	fpr.resetNewEditor();
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.PORT_REDIRECTING)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
            <input type='hidden' name='_nextpage' value='/#forward-redirection.asp'>
            <input type='hidden' name='_service' value='firewall-restart'>
            
            <input type='hidden' name='portredirect'>
      <div class='section'>
        <table class='line-table' cellspacing=1 id='fpr-grid'></table>
        <script type='text/javascript'>fpr.setup(); init();</script>
    </div>
                <br>
<script type='text/javascript'>show_notice1('iptables');</script>
			</form>
</div>
</div>
		
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>