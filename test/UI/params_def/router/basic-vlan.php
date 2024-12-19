<?PHP include 'header.php'; ?>
<style type='text/css'>
#vlan-grid .co1,#vlan-grid .co2,#vlan-grid .co3,#vlan-grid .co4,#vlan-grid .co5,#vlan-grid .co6,#vlan-grid .co7,#vlan-grid .co8,
#vlan-grid .co9,#vlan-grid .co10,#vlan-grid .co11,#vlan-grid .co12,#vlan-grid .co13,#vlan-grid .co14
{
	text-align: center;
}
#vlan-grid .co2
{
}
#vlan-grid .centered
{
	text-align: center;
}
</style>
<script type='text/javascript'>
// <% nvram ("radio_mode,vlan0ports,vlan1ports,vlan2ports,vlan3ports,vlan4ports,vlan5ports,vlan6ports,vlan7ports,vlan8ports,vlan9ports,vlan10ports,vlan11ports,vlan12ports,vlan13ports,vlan14ports,vlan15ports,wan_ifnameX,lan_ifname,lan_ifnames,lan1_ifname,lan1_ifnames,lan2_ifname,lan2_ifnames,lan3_ifname,lan3_ifnames,vlan0vid,vlan1vid,vlan2vid,vlan3vid,vlan4vid,vlan5vid,vlan6vid,vlan7vid,vlan8vid,vlan9vid,vlan10vid,vlan11vid,vlan12vid,vlan13vid,vlan14vid,vlan15vid");%>
// <% ethinfo(); %>
for(var i=0;i <= MAX_BRIDGE_ID;i++) {
	var j = (i == 0) ? '' : i.toString();
	if(!nvram['lan'+j+'_ifnames']) {
		nvram['lan'+j+'_ifnames'] = '';
	}
	if(!nvram['lan'+j+'_ifname']) {
		nvram['lan'+j+'_ifname'] = '';
	}
}
if(!nvram.wan_ifnameX){
	nvram.wan_ifnameX = '';
}
for(var i=0;i <= MAX_VLAN_ID;i++) {
	if(!nvram['vlan' + i + 'ports']) {
		nvram['vlan' + i + 'ports'] = '';
	}
}

var ethinfo;
if (nvram.term_model == 'ROUTER' || !nvram.term_model) {
	ethinfo=[
	    {
	        "title": "LAN0",
	        "sport": "0"
	    },
	    {
	        "title": "LAN1",
	        "sport": "1"
	    },
	    {
	        "title": "LAN2",
	        "sport": "2"
	    },
	    {
	        "title": "LAN3",
	        "sport": "3"
	    },
	    {
	        "title": "LAN4",
	        "sport": "4"
	    }
	];
}else {
	var type = nvram.term_model.substr(0,3);
	var arr1 = ['R10','R12'];
	var arr2 = ['R20','CM2'];
	var arr3 = ['R50','CM5','G50'];
	var arr4 = ['R23'];
	var arr5 = ['R21','G20'];
	var arr6 = ['G51','R51'];
	var arr7 = ['G92'];
	if ($.inArray(type, arr1) != -1) {
		ethinfo=[
		    {
		        "title": "LAN",
		        "sport": "1"
		    }
		];
	}else if ($.inArray(type, arr2) != -1) {
		ethinfo=[
		    {
		        "title": "WAN/CON",
		        "sport": "0"
		    },
		    {
		        "title": "LAN",
		        "sport": "1"
		    }
		];
	}else if ($.inArray(type, arr3) != -1) {
		ethinfo=[
		    {
		        "title": "WAN/LAN",
		        "sport": "0"
		    },
		    {
		        "title": "LAN1",
		        "sport": "1"
		    },
		    {
		        "title": "LAN2",
		        "sport": "2"
		    },
		    {
		        "title": "LAN3",
		        "sport": "3"
		    },
		    {
		        "title": "LAN4",
		        "sport": "4"
		    }
		];
	}else if ($.inArray(type, arr4) != -1) {
		ethinfo=[
		    {
		        "title": "WAN",
		        "sport": "0"
		    },
		    {
		        "title": "LAN1",
		        "sport": "1"
		    },
		    {
		        "title": "LAN2",
		        "sport": "2"
		    }
		];
	}else if ($.inArray(type, arr5) != -1) {
		ethinfo=[
		    {
		        "title": "WAN/LAN",
		        "sport": "0"
		    },
		    {
		        "title": "LAN",
		        "sport": "1"
		    }
		];
	}else if ($.inArray(type, arr6) != -1) {
		ethinfo=[
		    {
		        "title": "WAN/LAN1",
		        "sport": "0"
		    },
		    {
		        "title": "LAN2",
		        "sport": "1"
		    },
		    {
		        "title": "LAN3",
		        "sport": "3"
		    },
		    {
		        "title": "LAN4",
		        "sport": "2"
		    }
		];
	}else if ($.inArray(type, arr7) != -1) {
		ethinfo=[
		    {
		        "title": "LAN0",
		        "sport": "0"
		    },
		    {
		        "title": "LAN1",
		        "sport": "1"
		    },
		    {
		        "title": "LAN2",
		        "sport": "2"
		    },
		    {
		        "title": "LAN3",
		        "sport": "3"
		    },
		    {
		        "title": "LAN4",
		        "sport": "4"
		    }
		];
	}
}
</script>
<script type='text/javascript'>
var COL_VID = 0;
var COL_BRI=(ethinfo.length*2)+1;

function verifyFields(focused, quiet)
{
	return 1;
}

function trailingSpace(s)
{
	return ((s.length>0)&&(s.charAt(s.length-1) != ' ')) ? ' ' : '';
}

function save()
{
	if(vlg.isEditing())
	{
		return;
	}
	var fom = E('_fom');
	for(var i=0;i <= MAX_VLAN_ID;i++)
	{
		fom['vlan' + i + 'ports'].value = '';
		fom['vlan' + i + 'vid'].value = '';
	}
	fom['wan_ifnameX'].value = '';
	fom['lan_ifnames'].value = '';
	fom['lan1_ifnames'].value = '';
	fom['lan2_ifnames'].value = '';
	fom['lan3_ifnames'].value = '';

	var d = vlg.getAllData();
	for(var i=0;i < d.length;++i)
	{
		var j=1;p='';

		for(var k=0;k<ethinfo.length;k++)
		{
			p += (d[i][j++].toString() != '0') ? ethinfo[k].sport : '';
			p += (d[i][j++].toString() != '0') ? 't' : '';
			p += trailingSpace(p);
		}

		p += 5;
		p = p.split(" ");
		p = p.sort(cmpInt);
		p = p.join(" ");
		fom['vlan'+(i + 1)+'ports'].value = p;
		fom['vlan'+(i + 1)+'vid'].value = d[i][COL_VID];

	    fom['wan_ifnameX'].value += (d[i][COL_BRI] == '2') ? 'vlan'+d[i][0] : '';
		fom['lan_ifnames'].value += (d[i][COL_BRI] == '3') ? 'vlan'+d[i][0] : '';
		fom['lan1_ifnames'].value += (d[i][COL_BRI] == '4') ? 'vlan'+d[i][0] : '';
		fom['lan2_ifnames'].value += (d[i][COL_BRI] == '5') ? 'vlan'+d[i][0] : '';
		fom['lan3_ifnames'].value += (d[i][COL_BRI] == '6') ? 'vlan'+d[i][0] : '';
	}
	if(vlg.countWan() != 1)
	{
		alert($lang.ONE_VID_MUST_BE_ASSIGNED_TO + ' WAN.');
		return;
	}
	if(vlg.countLan(0) != 1)
	{
		alert($lang.ONE_VID_MUST_BE_ASSIGNED_TO + ' br0.');
		return;
	}
	fom['lan_ifnames'].value += " eth1 ra0 rai0";
	if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
	{
		// form.submit(fom);
		return submit_form('_fom');
	}
}

var vlg = new TomatoGrid();
vlg.setup = function()
{
	var grid_list=[];
	var header_list=[];

	header_list.push('VID');
	grid_list.push({ type: 'text', maxlen: 4, prefix: '<div class="centered">', suffix: '</div>' });
	for(var i=0;i<ethinfo.length;i++)
	{
		header_list.push(ethinfo[i].title);
		header_list.push($lang.TAG);
		grid_list.push({ type: 'checkbox', prefix: '<div class="centered">', suffix: '</div>' });
		grid_list.push({ type: 'checkbox', prefix: '<div class="centered">', suffix: '</div>' });
	}
	header_list.push($lang.BRIDGING);
	grid_list.push({ type: 'select', options: [[1, 'none'],[2, 'WAN'],[3, 'br0'],[4, 'br1'],[5, 'br2'],[6, 'br3']], prefix: '<div class="centered">', suffix: '</div>' });
	this.init('vlan-grid', '', (MAX_VLAN_ID + 1), grid_list);
	this.headerSet(header_list);

	vlg.populate();
	vlg.canDelete = false;
	vlg.sort(0);
	vlg.showNewEditor();
	vlg.resetNewEditor();
}

function get_index(vid)
{
	for (var i=0;i <= MAX_VLAN_ID;i++)
	{
		var nv = nvram['vlan' + i + 'vid'];
		if(nv == vid)
		{
			return i;
		}
	}
	return vid;
}
vlg.populate = function()
{
	vlg.removeAllData();
	var bridged = [];
	for(var i=0;i <= MAX_BRIDGE_ID;i++)
	{
		var j = (i == 0) ? '' : i.toString();
		var l = nvram['lan' + j + '_ifnames'].split(' ');
		for(var k=0;k < l.length;k++)
		{
			if(l[k].indexOf('vlan') != -1)
			{
				if(nvram['lan' + j + '_ifname'] != '')
				{
					bridged[get_index(parseInt(l[k].replace('vlan','')))] = (3 + parseInt(nvram['lan' + j + '_ifname'].replace('br',''))).toString();
				}
				else
				{
					bridged[get_index(parseInt(l[k].replace('vlan','')))] = '1';
				}
			}
		}
	}

    bridged[get_index(parseInt(nvram['wan_ifnameX'].replace('vlan','')))] = '2';
	for (var i=0;i <= MAX_VLAN_ID;i++)
	{
		var port = [];
		var tagged = [];
		if(nvram['vlan' + i + 'ports'].length > 0)
		{
			for(var j=0;j <= MAX_PORT_ID;j++)
			{
				port[j] = '0';
				tagged[j] = '0';
			}
			var m = nvram['vlan' + i + 'ports'].split(' ');
			for(var j=0;j < (m.length);j++)
			{
				port[parseInt(m[j].charAt(0))] = '1';
				tagged[parseInt(m[j].charAt(0))] = (m[j].indexOf('t') != -1) ? '1' : '0';
			}

			var indata=[];
			var m = nvram['vlan' + i + 'vid'];
			if(!m)
			{
				indata.push(i.toString());
			}
			else
			{
				indata.push(m);
			}
			for(var j=0;j<ethinfo.length;j++)
			{
				indata.push(port[ethinfo[j].sport]);
				indata.push(tagged[ethinfo[j].sport]);
			}
			indata.push((bridged[i] != null) ? bridged[i] : '1');
			vlg.insertData(-1, indata);
		}
	}
}

vlg.countElem = function(f, v)
{
	var data = this.getAllData();
	var total = 0;
	for (var i=0;i < data.length;++i)
	{
		total += (data[i][f] == v) ? 1 : 0;
	}
	return total;
}

vlg.countVID = function (v)
{
	return this.countElem(COL_VID,v);
}

vlg.countWan = function()
{
	return this.countElem(COL_BRI,2);
}

vlg.countLan = function(l)
{
	return this.countElem(COL_BRI,l+3);
}

vlg.verifyFields = function(row, quiet)
{
	var valid = 1;
	var f = fields.getAll(row);
	if(parseInt(f[0].value) < 1 || parseInt(f[0].value) > 4094)
	{
		alert("VLAN ID is range 1-4094");
		return 0;
	}
	for(var i=0; i <= MAX_BRIDGE_ID; i++)
	{
		var j = (i==0) ? '' : i.toString();
		f[COL_BRI].options[i+2].disabled = (nvram['lan' + j + '_ifname'].length < 1);
	}

	for(var j=1,i=0;i<ethinfo.length;i++,j+=2)
	{
		if(f[j].checked == 1)
		{
			f[j+1].disabled=0;
		}
		else
		{
			f[j+1].disabled=1;
			f[j+1].checked=0;
		}
	}

	for(var j=1,i=0;i<ethinfo.length;i++,j+=2)
	{
		if(valid && (f[j].checked == 1) && (this.countElem(j,1)>0))
		{
			if(((this.countElem(j,1) != this.countElem(j+1,1)) || (f[j+1].checked==0)))
			{
				ferror.set(f[j+1], $lang.MULTI_VLAN_TIP, quiet);
				valid=0;
			}
			else
			{
				ferror.clear(f[j+1]);
			}
		}
	}

	if((this.countWan() > 0) && (f[COL_BRI].selectedIndex == 1))
	{
		ferror.set(f[COL_BRI], $lang.ONE_AND_ONLY_ONE_VID_CAN_BE_USED_FOR + ' WAN', quiet);
		valid = 0;
	}
	else
	{
		ferror.clear(f[COL_BRI]);
	}
	for(var i=0; i<4; i++)
	{
		if((this.countLan(i) > 0) && (f[COL_BRI].selectedIndex == (i+2)))
		{
			ferror.set(f[COL_BRI], $lang.ONE_AND_ONLY_ONE_VID_CAN_BE_USED_FOR + ' br'+i, quiet);
			valid = 0;
		}
		else
		{
			ferror.clear(f[COL_BRI]);
		}
	}
	return valid;
}

vlg.dataToView = function(data)
{
	var j=0,d2v=[];
	d2v.push(data[j++]);
	for(i=0;i<ethinfo.length;i++)
	{
		d2v.push((data[j++].toString() != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>');
		d2v.push((data[j++].toString() != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>');
	}
	d2v.push(['','WAN', 'br0', 'br1', 'br2', 'br3' ][data[j] - 1]);
	return d2v;
}

vlg.dataToFieldValues = function (data)
{
	var j=0,d2fv=[];
	d2fv.push(data[j++]);
	for(i=0;i<ethinfo.length;i++)
	{
		d2fv.push((data[j++] != 0)?'checked':'');
		d2fv.push((data[j++] != 0)?'checked':'');
	}
	d2fv.push(data[j]);
	return d2fv;
}

vlg.fieldValuesToData = function(row)
{
	var j=0,fv2d=[],f = fields.getAll(row);
	fv2d.push(f[j++].value);
	for(i=0;i<ethinfo.length;i++)
	{
		fv2d.push(f[j++].checked ? 1 : 0);
		fv2d.push(f[j++].checked ? 1 : 0);
	}
	fv2d.push(f[j].value);
	return fv2d;
}

vlg.onCancel = function()
{
	this.removeEditor();
	this.showSource();
	this.disableNewEditor(false);
	this.resetNewEditor();
}

vlg.onAdd = function()
{
	var data;

	this.moving = null;
	this.rpHide();
	if(!this.verifyFields(this.newEditor, false))
	{
		return;
	}
	data = this.fieldValuesToData(this.newEditor);
	this.insertData(-1, data);
	this.disableNewEditor(false);
	this.resetNewEditor();
	this.resort();
}

vlg.onOK = function()
{
	var i, data, view;

	if(!this.verifyFields(this.editor, false))
	{
		return;
	}
	data = this.fieldValuesToData(this.editor);
	view = this.dataToView(data);
	this.source.setRowData(data);
	for(i=0;i < this.source.cells.length;++i)
	{
		this.source.cells[i].innerHTML = view[i];
	}
	this.removeEditor();
	this.showSource();
	this.disableNewEditor(false);
	this.resetNewEditor();
	this.resort();
}

vlg.onDelete = function()
{
	this.removeEditor();
	elem.remove(this.source);
	this.source = null;
	this.disableNewEditor(false);
	this.resetNewEditor();
}

vlg.sortCompare = function(a, b)
{
	var obj = TGO(a);
	var col = obj.sortColumn;
	if(this.sortColumn == 0)
	{
		var r = cmpInt(parseInt(a.cells[col].innerHTML), parseInt(b.cells[col].innerHTML));
	}
	else
	{
		var r = cmpText(a.cells[col].innerHTML, b.cells[col].innerHTML);
	}
	return obj.sortAscending ? r : -r;
}

vlg.resetNewEditor = function()
{
	var f = fields.getAll(this.newEditor);
	for(var i=0;i <= MAX_BRIDGE_ID;i++)
	{
		var j = (i==0) ? '' : i.toString();
		f[COL_BRI].options[i+2].disabled = (nvram['lan' + j + '_ifname'].length < 1);
	}
	f[COL_VID].value = '';


	var j=1;
	for(var i=0;i<ethinfo.length;i++)
	{
		f[j++].checked = 0;
		f[j].checked = 0;
		f[j++].disabled = 1;
	}
	f[j].selectedIndex = 0;
	ferror.clearAll(fields.getAll(this.newEditor));
}

function init()
{
	vlg.recolor();
	vlg.resetNewEditor();
}

function earlyInit()
{
	vlg.setup();
}
	</script>


<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#basic-vlan.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_reboot' value='1'>
<input type='hidden' name='_nvset' value='1'>
<input type='hidden' name='_commit' value='1'>
<input type='hidden' name='vlan0ports'>
<input type='hidden' name='vlan1ports'>
<input type='hidden' name='vlan2ports'>
<input type='hidden' name='vlan3ports'>
<input type='hidden' name='vlan4ports'>
<input type='hidden' name='vlan5ports'>
<input type='hidden' name='vlan6ports'>
<input type='hidden' name='vlan7ports'>
<input type='hidden' name='vlan8ports'>
<input type='hidden' name='vlan9ports'>
<input type='hidden' name='vlan10ports'>
<input type='hidden' name='vlan11ports'>
<input type='hidden' name='vlan12ports'>
<input type='hidden' name='vlan13ports'>
<input type='hidden' name='vlan14ports'>
<input type='hidden' name='vlan15ports'>
<input type='hidden' name='wan_ifnameX'>
<input type='hidden' name='lan_ifnames'>
<input type='hidden' name='lan1_ifnames'>
<input type='hidden' name='lan2_ifnames'>
<input type='hidden' name='lan3_ifnames'>
<input type='hidden' name='vlan0vid'>
<input type='hidden' name='vlan1vid'>
<input type='hidden' name='vlan2vid'>
<input type='hidden' name='vlan3vid'>
<input type='hidden' name='vlan4vid'>
<input type='hidden' name='vlan5vid'>
<input type='hidden' name='vlan6vid'>
<input type='hidden' name='vlan7vid'>
<input type='hidden' name='vlan8vid'>
<input type='hidden' name='vlan9vid'>
<input type='hidden' name='vlan10vid'>
<input type='hidden' name='vlan11vid'>
<input type='hidden' name='vlan12vid'>
<input type='hidden' name='vlan13vid'>
<input type='hidden' name='vlan14vid'>
<input type='hidden' name='vlan15vid'>

<div class="box" data-box="vlan">
<div class='heading'>VLAN</div>
<div class='section content'>
<table class='line-table' cellspacing=1 id='vlan-grid'></table>
</div>
</div>
</div>
</div>
         
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
    <!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
    <span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
</form>
<script type='text/javascript'>earlyInit(); verifyFields(null,1);</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
