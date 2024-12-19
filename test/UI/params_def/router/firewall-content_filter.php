<?PHP include 'header.php'; ?>
	<script type="text/javascript">

//	<% nvram("portfilterenabled,defaultfirewallpolicy,webhostfilters"); %>
if(!nvram.webhostfilters){
	nvram.webhostfilters = '';
}

var fwhost = new TomatoGrid();

fwhost.sortCompare = function(a, b) {
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

fwhost.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1], data[2]];
}

fwhost.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value];
}

fwhost.verifyFields = function(row, quiet) 
{
	var f;

	f = fields.getAll(row);
	if (!v_length(f[1], quiet,2,1024)) return 0;

	return 1;
}

fwhost.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[0].value = '';
	f[2].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

fwhost.setup = function() {
	this.init('fwhost-grid', 'sort', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 128 },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.VAR_RULE_DOMAIN, $lang.VAR_RULE_DESC]);
	var nv = nvram.webhostfilters.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==3) 
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2] ]);
		}
	}
	fwhost.sort(6);
	fwhost.showNewEditor();
}

function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function save()
{
	if (fwhost.isEditing()) return;
	
	var data = fwhost.getAllData().sort(srcSort);
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	
	var fom = E('_fom');
	fom.webhostfilters.value = s;
	fom.portfilterenabled.value =E('_f_portfilterenabled').checked ? 1 : 0;
	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function init()
{
	fwhost.recolor();
	fwhost.resetNewEditor();
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.DOMAIN_FILTER)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#firewall-content_filter.asp'>
                <input type='hidden' name='_service' value='firewall-restart'>
                <input type='hidden' name='portfilterenabled'>
                <input type='hidden' name='webhostfilters'>
				<div id="Domainconfig"></div>
                <div class='section'>
                    <table class='line-table' cellspacing=1 id='fwhost-grid'></table>
                    <script type='text/javascript'>fwhost.setup(); init();</script>
                </div>

			</form>
	</div>
    </div>
<script type='text/javascript'>

				$('#Domainconfig').forms([
				{ title: $lang.VAR_ENABLE, name: 'f_portfilterenabled', type: 'checkbox', value: (nvram.portfilterenabled == 1)},
	{ title: $lang.DEFAULTFIREWALLPOLICY, name: 'defaultfirewallpolicy', type: 'select', options: [['0', $lang.VAR_TERM_PARAM_WHITELIST],['1', $lang.VAR_TERM_PARAM_BLACKLIST]],value: nvram.defaultfirewallpolicy}
					], { align: 'left' });
</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
    <script type='text/javascript'>show_notice1('<% notice("iptables"); %>');</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
