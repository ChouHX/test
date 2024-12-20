<?PHP include 'header.php'; ?>
<style type='text/css'>
#fo-grid .co1 {
	width: 10%;
	text-align: center;
}
#fo-grid .co2 {
	width: 10%;
}
#fo-grid .co3 {
	width: 15%;
}
#fo-grid .co4 {
	width: 15%;
}
#fo-grid .co5 {
	width: 15%;
}
#fo-grid .co6 {
	width: 15%;
}
#fo-grid .co7 {
	width: 20%;
}
</style>



<script type='text/javascript'>

//	<% nvram("portforward"); %>
var lipp = '<% lipp(); %>.';
var fog = new TomatoGrid();
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
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', ['TCP', 'UDP', 'Both'][data[1] - 1], (data[2].match(/(.+)-(.+)/)) ? (RegExp.$1 + ' -<br>' + RegExp.$2) : data[2], data[3], data[4], data[5], data[6]];
}

fog.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value, f[4].value, f[5].value, f[6].value];
}

fog.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;

	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if ((f[2].value.length) && (!v_iptaddr(f[2], quiet))) return 0;

	if (!v_iptport(f[3], quiet)) return 0;
	ferror.clear(f[4]);
	if (f[3].value.search(/[-:,]/) != -1) {
		f[4].value = '';
		f[4].disabled = true;
	}
	else {
		f[4].disabled = false;
		f[4].value = f[4].value.trim();
		if (f[4].value != '') {
			if (!v_port(f[4], quiet)) return 0;
		}
	}
	ferror.clear(f[4]);

	s = f[5].value.trim();
	if (s.match(/^ *\d+ *$/)) f[5].value = lipp + s;
	if (!v_ip(f[5], quiet, 1)) return 0;

	f[6].value = f[6].value.replace(/>/g, '_');
	if (!v_nodelim(f[6], quiet, 'Description')) return 0;
	return 1;
}

fog.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].selectedIndex = 0;
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].value = '';
	f[6].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

fog.setup = function() {
	this.init('fo-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'select', options: [[1, 'TCP'],[2, 'UDP'],[3,'Both']] },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 16 },
		{ type: 'text', maxlen: 5 },
		{ type: 'text', maxlen: 15 },
		{ type: 'text', maxlen: 32 }]);
	this.headerSet([$lang.VAR_TERM_TERM_PARAMS_ACTIVATE, $lang.PROTOCOL, $lang.SRC_ADDR, $lang.EXT_PORTS, $lang.INT_PORT, $lang.INT_ADDR, $lang.VAR_RULE_DESC]);
	var nv = nvram.portforward.split('>');
	for (var i = 0; i < nv.length; ++i) {
		var r;

		// temp: <=1.06
		if (r = nv[i].match(/^(\d)<(\d)<([\d\-\:,]+)<(\d*)<(.*)<(.*)$/)) {
			r[1] *= 1;
			r[2] *= 1;
			r[3] = r[3].replace(/:/g, '-');
			if (r[5].match(/^ *\d+ *$/)) r[5] = lipp + r[5];
			fog.insertData(-1, [r[1], r[2], '', r[3], r[4], r[5], r[6]]);
		}
		// >=1.07
		else if (r = nv[i].match(/^(\d)<(\d)<(.*)<(.+?)<(\d*)<(.*)<(.*)$/)) {
			r[1] *= 1;
			r[2] *= 1;
			r[4] = r[4].replace(/:/g, '-');
			if (r[6].match(/^ *\d+ *$/)) r[6] = lipp + r[6];
			fog.insertData(-1, r.slice(1, 8));
		}
	}
	fog.sort(6);
	fog.showNewEditor();
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

	var data = fog.getAllData().sort(srcSort);
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		data[i][3] = data[i][3].replace(/-/g, ':');
		s += data[i].join('<') + '>';
	}
	var fom = E('_fom');
	fom.portforward.value = s;
	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function init()
{
	fog.recolor();
	fog.resetNewEditor();
}
	</script>
<form id="_fom" method="post" action="tomato.cgi">
	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.PORT_FORWARDING)</script></div>
		<div class="content" >
			
                <input type='hidden' name='_nextpage' value='/#forward-basic.asp'>
                <input type='hidden' name='_service' value='firewall-restart'>
                <input type='hidden' name='portforward'>
                <div class='section'>
                    <table class='line-table' cellspacing=1 id='fo-grid'></table>
                    <script type='text/javascript'>fog.setup(); init();</script>
                </div>
                <div><script type="text/javascript">document.write($lang.PORT_FORWARDING_TIPS)</script></div>
		</div>
   </div>
   <br>
<script type='text/javascript'>show_notice1('iptables');</script>
			</form>

			<script type='text/javascript'>

				
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />


<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
