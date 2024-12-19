<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram("linkschedule,linkscheck"); %>
// <% activelinks(); %>
iflist_link1 = [];
iflist_link2 = [];

var activelinks = nvram.activelinks || [];

if(!nvram.linkscheck){
	nvram.linkscheck = '';
}
if(!nvram.linkschedule){
	nvram.linkschedule = '';
}

var linkschedule = new TomatoGrid();
var linkscheck = new TomatoGrid();

linkschedule.sortCompare = function(a, b) {
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

linkschedule.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1],data[2],[$lang.AUTO_SWITCH, $lang.VAR_BACKUP][data[3]-1],data[4],data[5]];
}

linkschedule.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].value];
}

linkschedule.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	a = [['1', $lang.LINK + '1'],['2',$lang.LINK + '2']];
	for (i = 0;i < a.length;i++)
	{
		if (!f[a[i][0]].value.length)
		{
			ferror.set(f[a[i][0]],a[i][1]+ $lang.MUST_NOT_BE_EMPTY,quiet);
			return 0;
		}
	}

	if(f[1].value == f[2].value || f[1].value == f[4].value || f[2].value == f[4].value)
	{
		if(f[1].value == f[2].value)
			ferror.set(f[2], $lang.LINK_ALREADY_EXISTS,quiet);
		else
			ferror.set(f[4], $lang.LINK_ALREADY_EXISTS,quiet);
		return 0;
	}
	if (!v_nodelim(f[1], quiet, $lang.LINK + '1',1)) return 0;
	if (!v_nodelim(f[2], quiet, $lang.LINK + '2',1)) return 0;
	a = linkschedule.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[1].value == a[i][1] || f[1].value == a[i][2])
		{
			ferror.set(f[1],$lang.LINK_ALREADY_EXISTS,quiet);
			return 0;
		}
		if(f[2].value == a[i][1] || f[2].value == a[i][2])
		{
			ferror.set(f[2],$lang.LINK_ALREADY_EXISTS,quiet);
			return 0;
		}
		if((f[4].value != 'none') && (f[4].value == a[i][4]))
		{
			ferror.set(f[4],$lang.LINK_ALREADY_EXISTS,quiet);
			return 0;
		}
	}
	//check vlink define
	if((f[2].value == 'vlink1') || (f[2].value == 'vlink2'))
	{
		a = linkschedule.getAllData();
		for(i = 0; i < a.length; ++i)
		{
			if(f[2].value == a[i][4])
			{
				break;
			}
		}
		if(i >= a.length)
		{
			ferror.set(f[2],'Link not exists',quiet);
			return 0;
		}
	}
	if((f[2].value == 'vlink1' || f[2].value == 'vlink2') && (f[4].value == 'vlink1' || f[4].value == 'vlink2'))
	{
		ferror.set(f[4],'Virtual Link can only config none',quiet);
		return 0;
	}
	if (!v_nodelim(f[5], quiet, $lang.VAR_RULE_DESC,1)) return 0;
	return 1;
}

linkschedule.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].selectedIndex = 0;
	f[2].selectedIndex = 0;
	f[3].selectedIndex = 0;
	f[4].selectedIndex = 0;
	f[5].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}
function initlinks(index)
{
	if(index == 1)
	{
		for(var i=0;i<activelinks.length;i++)
		{
			iflist_link1.push([activelinks[i].name,activelinks[i].name]);
		}
	}
	else
	{
		for(var i=0;i<activelinks.length;i++)
		{
			iflist_link2.push([activelinks[i].name,activelinks[i].name]);
		}
		iflist_link2.push(['vlink1','vlink1']);
		iflist_link2.push(['vlink2','vlink2']);
	}
}
linkschedule.setup = function() {
	initlinks(1);
	initlinks(2);
	this.init('linkschedule-grid', '', 10, [
		{ type: 'checkbox' },
		{ type: 'select', options: [['modem','modem'],['modem2','modem2'],['wan','wan'],['sta','sta'],['sta2','sta2']] },
		{ type: 'select', options: [['modem','modem'],['modem2','modem2'],['wan','wan'],['sta','sta'],['sta2','sta2'],['vlink1','vlink1'],['vlink2','vlink2']] },
		{ type: 'select', options: [[1, $lang.AUTO_SWITCH],[2, $lang.VAR_BACKUP]] },
		{ type: 'select', options: [['none', 'none'], ['vlink1', 'vlink1'], ["vlink2", 'vlink2']] },
		{ type: 'text', maxlen: 64 }]);
		this.headerSet([$lang.VAR_TERM_TERM_PARAMS_ACTIVATE,$lang.LINK + '1',$lang.LINK + '2', $lang.POLICY,'Virtual Link',$lang.VAR_RULE_DESC]);
	var nv = nvram.linkschedule.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==6)
		{
			t[0] *= 1;
			this.insertData(-1,[t[0],t[1],t[2],t[3],t[4],t[5]]);
		}
	}

	linkschedule.showNewEditor();
}

linkscheck.sortCompare = function(a, b) {
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

linkscheck.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>', data[1],data[2],data[3],data[4],data[5]];
}

linkscheck.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].value];
}

linkscheck.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	var i,a;

	if(!f[1].value.length)
	{
		ferror.set(f[1],'Link' + $lang.MUST_NOT_BE_EMPTY,quiet);
		return 0;
	}
	if(!v_nodelim(f[1], quiet, $lang.LINK + '1',1)) return 0;
	a = linkscheck.getAllData().sort(srcSort);
	for(i = 0; i < a.length; ++i)
	{
		if(f[1].value == a[i][1])
		{
			ferror.set(f[1], $lang.LINK_ALREADY_EXISTS,quiet);
			return 0;
		}
	}

	if(!f[2].value.length || (!v_ip(f[2], 1) && !v_domain(f[2], 1)))
	{
		ferror.set(f[2],  $lang.INVALID_SERVER_ADDRESS,quiet);
		return 0;
	}
	if (!v_range(f[3], quiet, 1,255)) return 0;
	if (!v_range(f[4], quiet, 1,255)) return 0;
	if (!v_nodelim(f[5], quiet, $lang.VAR_RULE_DESC,1)) return 0;

	return 1;
}

linkscheck.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

linkscheck.setup = function() {
	this.init('linkscheck-grid', '', 10, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 10 },
		{ type: 'text', maxlen: 64 },
		{ type: 'text', maxlen: 8 },
		{ type: 'text', maxlen: 8 },
		{ type: 'text', maxlen: 64 }]);
		this.headerSet([$lang.VAR_RULE_ENABLE, $lang.LINK, $lang.DEST_ADDRESS, $lang.PINGINTERVAL, $lang.PINGMAX, $lang.VAR_RULE_DESC]);
	var nv = nvram.linkscheck.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==6) 
		{
			t[0] *= 1;
			this.insertData(-1,[t[0],t[1],t[2],t[3],t[4],t[5]]);
		}
	}

	linkscheck.showNewEditor();
}


function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function show_list()
{
	var i;
	var	htmlOut;
	
	
	for(i=0;i<activelinks.length;i++)
	{
		if(i%2==0)
		{
			htmlOut += "<tr class='even'>";
		}
		else
		{
			htmlOut += "<tr class='odd'>";
		}
		htmlOut += "<td width='25%'>" + activelinks[i].name +"</td>";
		switch(activelinks[i].type)
		{
			case 'WAN':
				htmlOut += "<td width='25%'>" + activelinks[i].type +'('+ activelinks[i].proto +")</td>";
				break;
			 default:
				htmlOut += "<td width='25%'>" + activelinks[i].proto +"</td>";
			break;
		}
		htmlOut += "<td width='50%'>" + activelinks[i].desc +"</td>";
		htmlOut += "</tr>\n";		
	}
	$('#file-list').append(htmlOut);
}

function save()
{
	if (linkschedule.isEditing()) return;
	if (linkscheck.isEditing()) return;
	
	var fom = E('_fom');
	
	var data;

	data = linkschedule.getAllData();
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.linkschedule.value = s;
	
	data = linkscheck.getAllData();
	s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
	fom.linkscheck.value = s;
	if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
	{
		// form.submit(fom, 0, 'tomato.cgi');
		return submit_form('_fom');
	}
	else
	{
		return;
	}
	
}

function init()
{
	linkschedule.recolor();
	linkschedule.resetNewEditor();
	linkscheck.recolor();
	linkscheck.resetNewEditor();
}

</script>

	<form id="_fom" method="post" action="tomato.cgi">
         <input type='hidden' name='_nextpage' value='/#basic-schedule.asp'>
       <input type='hidden' name='linkschedule'>
	<input type='hidden' name='linkscheck'>
	<input type='hidden' name='_reboot' value='1'>

<!-- <div class="box" data-box="list">
<div class="heading"><script type="text/javascript">document.write($lang.ENABLED_LINKS)</script></div>
<div class="section content">
    <table width='100%' border='0' cellspacing='1' cellpadding='3' class='line-table' id="file-list">
	  <tr class="header">
		<td width='25%'  id='dHost'><script type="text/javascript">document.write($lang.LINK_NAME)</script></td>
		<td width='25%'  id='dHost'><script type="text/javascript">document.write($lang.LINK_TYPE)</script></td>
		<td width='50%'   id='dMac'><script type="text/javascript">document.write($lang.VAR_RULE_DESC)</script></td>
	  </tr>
	<script language='JavaScript' type='text/javascript'>
	show_list();
	</script>
</table>
</div>
</div> -->

<div class="box" data-box="linkscheck">
<div class="heading"><script type="text/javascript">document.write($lang.ICMP_CHECK)</script></div>
<div class="section content">
    <table class='line-table' cellspacing=1 id='linkscheck-grid'></table>
	<script type='text/javascript'>linkscheck.setup();</script>
</div>
</div>

				
<div class="box" data-box="linkschedule">
<div class="heading"><script type="text/javascript">document.write($lang.POLICY)</script></div>
<div class="section content">
    <table class='line-table' cellspacing=1 id='linkschedule-grid'></table>
	<script type='text/javascript'>
	linkschedule.setup();
	</script>
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
