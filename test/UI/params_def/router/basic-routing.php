<?PHP include 'header.php'; ?>
<style type='text/css'>
/*#ara-grid .co1, #ara-grid .co2, #ara-grid .co3 {
	width: 20%;
}*/
/*#ara-grid .co4 {
	width: 10%;
}
#ara-grid .co5 {
	width: 30%;
}*/

#ars-grid .co1, #ars-grid .co2, #ars-grid .co3  {
	width: 20%;
}
#ars-grid .co4 {
	width: 6%;
}
#ars-grid .co5 {
	width: 10%;
}
#ars-grid .co6 {
	width: 24%;
}
</style>
<content>
	<script type="text/javascript">
// <% nvram("routes_policy,bgp_on,bgp_raw_on,bgp_rawconfig,bgp_instance,bgp_network,bgp_redistribute,bgp_peers,bgp_custom,ospf_on,ospf_id,ospf_rfc1583,ospf_network,lan_ipaddr,wk_mode,dr_setting,lan_stp,routes_static,dhcp_routes,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname,wan_ifname,wan_iface,emf_enable,dr_lan_rx,dr_lan1_rx,dr_lan2_rx,dr_lan3_rx,dr_wan_rx,wan_proto"); %>
// <% activeroutes(); %>
// <% activeifs(); %>
if(!nvram.routes_static){
	nvram.routes_static = '';
}
if(!nvram.ospf_network){
	nvram.ospf_network = '';
}
if(!nvram.bgp_instance){
	nvram.bgp_instance = '';
}
if(!nvram.bgp_network){
	nvram.bgp_network = '';
}
if(!nvram.bgp_redistribute){
	nvram.bgp_redistribute = '';
}
if(!nvram.bgp_peers){
	nvram.bgp_peers = '';
}
if(!nvram.bgp_custom){
	nvram.bgp_custom = '';
}
if(!nvram.routes_policy){
	nvram.routes_policy = '';
}
var bi = JSON.parse(nvram.bi);

if(!activeroutes){
	var activeroutes = [];
}

if(!iflist){
	var iflist = [["lan","lan"],["lan1","lan1"],["lan2","lan2"],["lan3","lan3"],["wan","wan"],["modem","modem"],["modem2","modem2"],["sta","sta"],["sta2","sta2"]];
}

// var ara = new TomatoGrid();
// ara.setup = function() {
// 	var i, a;

// 	this.init('ara-grid', 'sort');
// 	this.headerSet([$lang.DEST_ADDRESS, $lang.VAR_GATEWAY + ' / ' + $lang.NEXT_HOP, $lang.LAN_NETMASK, $lang.HOPS, $lang.NETWORK_INTERFACE]);
// 	for (i = 0; i < activeroutes.length; ++i) {
// 		a = activeroutes[i];
// 		if (a[0] == nvram.lan_ifname) a[0] += ' (LAN)';
// 			else if (a[0] == nvram.wan_iface) a[0] += ' (WAN)';
// 		this.insertData(-1, [a[1],a[2],a[3],a[4],a[0]]);
// 	}
// }
var ars = new TomatoGrid();
ars.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	f[5].value = f[5].value.replace('>', '_');
	return v_ip(f[0], quiet) && v_ip(f[1], quiet) && v_netmask(f[2], quiet) && v_range(f[3], quiet, 0, 10) && v_nodelim(f[5], quiet, 'Description');
}

ars.setup = function() {
	this.init('ars-grid', '', 20, [
		{ type: 'text', maxlen: 15 },
		{ type: 'text', maxlen: 15 },
		{ type: 'text', maxlen: 15 },
		{ type: 'text', maxlen: 3 },
		{ type: 'select', options: iflist },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([ $lang.DEST_ADDRESS, $lang.VAR_GATEWAY, $lang.LAN_NETMASK, $lang.HOPS, $lang.NETWORK_INTERFACE, $lang.VAR_RULE_DESC]);
	var routes = nvram.routes_static.split('>');
	for (var i = 0; i < routes.length; ++i) {
		var t = routes[i].split('<');
		if (t.length==6)
		{
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

ars.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].value = '';
	f[1].value = '0.0.0.0';
	f[2].value = '';
	f[3].value = '0';
	f[4].selectedIndex = 0;
	f[5].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
policy_list = ['Auto','Only','Primary','Secondary'];
internet_list = ['modem','wan','sta'];
intranet_list = ['vlan1','vlan2','vlan3','vlan4','vlan5','vlan6','vlan7','vlan8','vlan9','vlan10','vlan11','vlan12','vlan13','vlan14','vlan15','vlan16','ap'];

if(bi.hw == 'dd')
{
	internet_list.push('modem2');
}
if((bi.model == 'g9') || (bi.model == 'g5'))
{
	internet_list.push('sta2');
	intranet_list.push('ap2');
}

var pg = new TomatoGrid();

pg.dataToView = function(data) {
	view_list = [];
	view_list.push(intranet_list[data[0]]);
	for(var i = 1; i <= internet_list.length; ++i)
	{
		view_list.push((data[i] == 0)?'':policy_list[data[i]]);
	}
	return view_list;
}

pg.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	data_list=[];
	for(var i = 0; i <= internet_list.length; ++i)
	{
		data_list.push(f[i].value);
	}
	return data_list;
}

pg.countElem = function(f, v)
{
	var data = this.getAllData();
	var total = 0;
	for (var i=0;i < data.length;++i)
	{
		total += (data[i][f] == v) ? 1 : 0;
	}
	return total;
}

pg.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	var t = intranet_list.length;

	f[0].selectedIndex=0;
	while((this.countElem(0,f[0].selectedIndex) > 0) && (t > 0))
	{
		f[0].selectedIndex = (f[0].selectedIndex%(intranet_list.length))+1;
		t--;
	}
	for(var i=0;i< intranet_list.length;i++)
	{
		f[0].options[i].disabled = (this.countElem(0,i) > 0);
	}

	for(var i = 1; i < internet_list.length; ++i)
	{
		f[i].selectedIndex = 0;
	}
	ferror.clearAll(fields.getAll(this.newEditor));
}

pg.verifyFields = function(row, quiet) 
{
	var f = fields.getAll(row);
	for(var i=0;i< intranet_list.length;i++)
	{
		f[0].options[i].disabled = (this.countElem(0,i) > 0);
	}
	var primary=0,secondary=0,sum=0;
	for(var i=1;i<= internet_list.length;i++)
	{
		sum += f[i].value;
		if(f[i].value == 1)
		{
			for(var j=1;j<= internet_list.length;j++)
			{
				if(i == j) continue;
				f[j].value = 0;
			}
			primary = 0;
			secondary = 0;
		}
		if(f[i].value == 2)
		{
			for(var j=1;j<= internet_list.length;j++)
			{
				if(i == j) continue;
				if(f[j].value == 2)
				{
					f[j].value = 0;
				}
			}
		}
		if(f[i].value == 3)
		{
			secondary = 1;
			for(var j=1;j<= internet_list.length;j++)
			{
				if(i == j) continue;
				if(f[j].value == 3)
				{
					f[j].value = 0;
				}
				if(f[j].value == 2) primary = 1;
			}
		}
	}
	if(secondary == 1 && primary != 1)
	{
		ferror.set(f[0],'Please select a Primary option',quiet);
		return 0;
	}
	if(sum == 0)
	{
		ferror.set(f[0],'Please select a non Auto option',quiet);
		return 0;
	}
	
	return 1;
}

pg.setup = function() {

	head_list = [];
	grid_list = [];
	intranet_option = [];
	for(var i = 0; i < intranet_list.length; ++i)
	{
		intranet_option.push([i,intranet_list[i]]);
	}	
	head_list.push('Lan');
	grid_list.push({type:'select',options: intranet_option});

	policy_option = [];
	for(var i = 0; i < policy_list.length; ++i)
	{	
		policy_option.push([i,policy_list[i]]);
	}
	for(var i = 0; i < internet_list.length; ++i)
	{	
		head_list.push(internet_list[i]);
		grid_list.push({type:'select',options: policy_option});
	}

	this.init('pg-grid', '', 10, grid_list)
	this.headerSet(head_list);
	var nv = nvram.routes_policy.split('>');
	for (var i = 0; i < nv.length; ++i) 
	{
		var t = nv[i].split('<');
		if (t.length==6)
		{
			insert_list = [t[0],t[1],t[2],t[3]];
			if(bi.hw == 'dd')
			{
				insert_list.push(t[4]);
			}
			if((bi.model == 'g9') || (bi.model == 'g5'))
			{
				insert_list.push(t[5]);
			}
			this.insertData(-1,insert_list);
		}
	}
	pg.showNewEditor();
	pg.resetNewEditor();
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var ospf = new TomatoGrid();

ospf.countElem = function(f, v)
{
	var data = this.getAllData();
	var total = 0;
	for(var i=0;i < data.length;++i)
	{
		total += (data[i][f] == v) ? 1 : 0;
	}
	return total;
}

ospf.onOK = function()
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
	this.resort();
	this.resetNewEditor();
}

ospf.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	for(var j=0;j<f[1].options.length;j++)
	{
		f[1].options[j].disabled = (this.countElem(1,f[1].options[j].value) > 0);
	}
	if(!v_ip(f[2], true) && !v_range(f[2], true,0,4294967295))
	{
		ferror.set(f[2], $lang.PLEASE_INPUT_IP_ADDRESS_OR_NUMBER + ' ('+ $lang.THE_EFFECTIVE_RANGE_IS +': 0-4294967295)',quiet);
		return 0;
	}
	return 1;
}

ospf.setup = function() {
	this.init('ospf-grid', '', 20, [
		{ type: 'checkbox' },
		{ type: 'select',  options: [['LAN', 'LAN'],['WAN','WAN']] },
		{ type: 'text', maxlen: 15 }]);

	this.headerSet([$lang.VAR_ENABLE, $lang.WEBSITE_ADDRESS, $lang.VAR_AREA]);
	var net = nvram.ospf_network.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length==3)
		{
			t[0] *= 1;
			this.insertData(-1,[t[0],t[1],t[2]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

ospf.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2]];
}

ospf.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value];
}

ospf.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	for(var j=0;j<f[1].options.length;j++)
	{
		f[1].options[j].disabled = (this.countElem(1,f[1].options[j].value) > 0);
	}
	ferror.clearAll(fields.getAll(this.newEditor));
}

var bgpinstance = new TomatoGrid();
var bgpnetwork = new TomatoGrid();
var bgpredistribute = new TomatoGrid();
var bgppeer = new TomatoGrid();
var bgpcustom = new TomatoGrid();

bgpinstance.verifyFields = function(row, quiet) {
	return 1;
}

bgpinstance.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], data[3]];
}

bgpinstance.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value];
}

bgpinstance.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

bgpinstance.setup = function() {
	this.init('bgp-instance', '', 20, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([$lang.VAR_ENABLE, 'AS', $lang.ROUTER_ID, $lang.VAR_RULE_DESC]);
	var net = nvram.bgp_instance.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length == 4)
		{
			this.insertData(-1,[t[0],t[1],t[2],t[3]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

bgpnetwork.verifyFields = function(row, quiet) {
	return 1;
}

bgpnetwork.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], data[3]];
}

bgpnetwork.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value];
}

bgpnetwork.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

bgpnetwork.setup = function() {
	this.init('bgp-network', '', 20, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([$lang.VAR_ENABLE, 'AS', $lang.VAR_TERM_PARAM_NETWORK, $lang.VAR_RULE_DESC]);
	var net = nvram.bgp_network.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length == 4)
		{
			this.insertData(-1,[t[0],t[1],t[2],t[3]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

bgpredistribute.verifyFields = function(row, quiet) {
	return 1;
}

bgpredistribute.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], data[3]];
}

bgpredistribute.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value];
}

bgpredistribute.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].selectedIndex = 0;
	f[3].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

bgpredistribute.setup = function() {
	this.init('bgp-redistribute', '', 20, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'select',  options: [['connected', 'Connected'],['kernel','Kernel'],['ospf','OSPF'],['rip','RIP'],['static','Static']] },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([$lang.VAR_ENABLE, 'AS', 'Redistribute', $lang.VAR_RULE_DESC]);
	var net = nvram.bgp_redistribute.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length == 4)
		{
			this.insertData(-1,[t[0],t[1],t[2],t[3]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

bgppeer.verifyFields = function(row, quiet) {
	return 1;
}

bgppeer.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], data[3], data[4]];
}

bgppeer.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value ,f[4].value];
}

bgppeer.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

bgppeer.setup = function() {
	this.init('bgp-peer', '', 20, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([$lang.VAR_ENABLE, 'AS', 'Peer','Remote AS',$lang.VAR_RULE_DESC]);
	var net = nvram.bgp_peers.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length == 5)
		{
			this.insertData(-1,[t[0],t[1],t[2],t[3],t[4]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

bgpcustom.verifyFields = function(row, quiet) {
	return 1;
}

bgpcustom.dataToView = function(data) {
	return [(data[0]==1) ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], data[3]];
}

bgpcustom.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value, f[3].value];
}

bgpcustom.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

bgpcustom.setup = function() {
	this.init('bgp-custom', '', 50, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 256 },
		{ type: 'text', maxlen: 32 }]);

	this.headerSet([$lang.VAR_ENABLE, 'AS', $lang.CUSTOM_CONFIGURATION, $lang.VAR_RULE_DESC]);
	var net = nvram.bgp_custom.split('>');
	for (var i = 0; i < net.length; ++i) {
		var t = net[i].split('<');
		if (t.length == 4)
		{
			this.insertData(-1,[t[0],t[1],t[2],t[3]]);
		}
	}
	this.showNewEditor();
	this.resetNewEditor();
}

function verifyFields(focused, quiet)
{
	if(E('_f_ospf_on').checked)
	{
		if(!v_ip(E('_ospf_id'), true) && !v_range(E('_ospf_id'), true,0,4294967295))
		{
			ferror.set(E('_ospf_id'), $lang.PLEASE_INPUT_IP_ADDRESS_OR_NUMBER + ' ('+ $lang.THE_EFFECTIVE_RANGE_IS +': 0-4294967295)',quiet);
			return 0;
		}
	}
	if(E('_ospf_id').value == '') E('_ospf_id').value = nvram.lan_ipaddr;

	if(E('_f_bgp_raw_on').checked)
	{
		elem.display(PR('_bgp_rawconfig'), true);
		//elem.display(PR('bgp-gird'), false);
		document.getElementById('bgpgird').style.display = 'none';
	}
	else
	{
		elem.display(PR('_bgp_rawconfig'), false);
//		elem.display(PR('bgp-gird'), true);
		document.getElementById('bgpgird').style.display = '';
	}
	return 1;
}

function save()
{
	if (ars.isEditing()) return;
	if (pg.isEditing()) return;
	if (ospf.isEditing()) return;
	if (bgpinstance.isEditing()) return;
	if (bgpnetwork.isEditing()) return;
	if (bgpredistribute.isEditing()) return;
	if (bgppeer.isEditing()) return;
	if (bgpcustom.isEditing()) return;
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');
	var data = ars.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.routes_static.value = r.join('>');

	data = pg.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i)
	{
		str = data[i][0]+'<';
		str += data[i][1]+'<';
		str += data[i][2]+'<';
		str += data[i][3]+'<';
		if(bi.hw == 'dd')
		{
			str += data[i][4]+'<';
			if((bi.model == 'g9') || (bi.model == 'g5'))
			{
				str += data[i][5];
			}
			else
			{
				str += '0';
			}
		}
		else
		{
			str += '0<';
			if((bi.model == 'g9') || (bi.model == 'g5'))
			{
				str += data[i][4];
			}
			else
			{
				str += '0';
			}
		}
		
		r.push(str);
	}
	fom.routes_policy.value = r.join('>');

	data = ospf.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.ospf_network.value = r.join('>');

	data = bgpinstance.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.bgp_instance.value = r.join('>');

	data = bgpnetwork.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.bgp_network.value = r.join('>');

	data = bgpredistribute.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.bgp_redistribute.value = r.join('>');

	data = bgppeer.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.bgp_peers.value = r.join('>');

	data = bgpcustom.getAllData();
	var r = [];
	for (var i = 0; i < data.length; ++i) r.push(data[i].join('<'));
	fom.bgp_custom.value = r.join('>');

	fom.ospf_on.value = E('_f_ospf_on').checked ? 1 : 0;
	fom.ospf_rfc1583.value = E('_f_ospf_rfc1583').checked ? 1 : 0;
	fom.bgp_on.value = E('_f_bgp_on').checked ? 1 : 0;
	fom.bgp_raw_on.value = E('_f_bgp_raw_on').checked ? 1 : 0;

/* ZEBRA-BEGIN */
	var wan = '0';
	var lan = '0';

	switch (E('_dr_setting').value) {
	case '1':
		lan = '1 2';
		break;
	case '2':
		wan = '1 2';
		break;
	case '3':
		lan = '1 2';
		wan = '1 2';
		break;
	}
	fom.dr_lan_tx.value = fom.dr_lan_rx.value = lan;
	fom.dr_wan_tx.value = fom.dr_wan_rx.value = wan;
/* ZEBRA-END */

	fom.lan_stp.value = E('_f_stp').checked ? 1 : 0;
	fom.dhcp_routes.value = E('_f_dhcp_routes').checked ? '1' : '0';
	fom._service.value = (fom.dhcp_routes.value != nvram.dhcp_routes) ? 'wan-restart' : 'routing-restart';

/* EMF-BEGIN */
	fom.emf_enable.value = E('_f_emf').checked ? 1 : 0;
	if (fom.emf_enable.value != nvram.emf_enable) fom._service.value = '*';
/* EMF-END */

	// form.submit(fom, 1);
	return submit_form('_fom');
}

function submit_complete()
{
	reloadPage();
}

function earlyInit()
{
	// ara.setup();
	ars.setup();
	ospf.setup();
	bgpinstance.setup();
	bgpnetwork.setup();
	bgpredistribute.setup();
	bgppeer.setup();
	bgpcustom.setup();
	pg.setup();	
}

function init()
{
	// ara.recolor();
	ars.recolor();
	ospf.recolor();
	bgpinstance.recolor();
	bgpnetwork.recolor();
	bgpredistribute.recolor();
	bgppeer.recolor();
	bgpcustom.recolor();
	pg.recolor();
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#basic-routing.asp'>
<input type='hidden' name='_service' value='routing-restart'>

<input type='hidden' name='bgp_on'>
<input type='hidden' name='bgp_raw_on'>
<input type='hidden' name='bgp_instance'>
<input type='hidden' name='bgp_network'>
<input type='hidden' name='bgp_redistribute'>
<input type='hidden' name='bgp_peers'>
<input type='hidden' name='bgp_custom'>
<input type='hidden' name='ospf_on'>
<input type='hidden' name='ospf_rfc1583'>
<input type='hidden' name='ospf_network'>
<input type='hidden' name='routes_static'>
<input type='hidden' name='routes_policy'>
<input type='hidden' name='lan_stp'>
<input type='hidden' name='dhcp_routes'>
<input type='hidden' name='emf_enable'>
<input type='hidden' name='dr_lan_tx'>
<input type='hidden' name='dr_lan_rx'>
<input type='hidden' name='dr_wan_tx'>
<input type='hidden' name='dr_wan_rx'>


<!-- 		<div class="box" data-box="routing-table">
			<div class="heading"><script type="text/javascript">document.write($lang.CURRENT_ROUTING_TABLE)</script></div>
			<div class="section content">
				<table class="line-table" id="ara-grid"></table>
				<br />
			</div>
		</div> -->

		<div class="box" data-box="routing-static">
			<div class="heading"><script type="text/javascript">document.write($lang.STATIC_ROUTING_TABLE)</script></div>
			<div class="section content">
				<table class="line-table" id="ars-grid"></table>
			</div>
		</div>
		
		<div class="box" data-box="routing-policy">
			<div class="heading"><script type="text/javascript">document.write($lang.POLICY_ROUTING_TABLE)</script></div>
			<div class="section content">
				<table class="line-table" id="pg-grid"></table>
			</div>
		</div>

		<div class="box" data-box="routing-ospf">
			<div class='heading'>OSPF</div>
			<div class="section content" id="ospf">
			<script type='text/javascript'>
				$( '#ospf' ).forms([
					{ title: $lang.ENABLE_OSPF, name: 'f_ospf_on', type: 'checkbox', value: nvram.ospf_on == '1' },
					{ title: 'RFC1583', name: 'f_ospf_rfc1583', type: 'checkbox', value: nvram.ospf_rfc1583 == '1' },
					{ title: $lang.ROUTER_ID, name: 'ospf_id', type: 'text', maxlen: 15, size: 17, value: nvram.ospf_id }
				]);
			</script>
			</div>
			<div class="section content">
			<table class='line-table' id='ospf-grid'></table>
			</div>
		</div>

		<div class="box" data-box="routing-bgp">
			<div class='heading'>BGP</div>
			<div class="section content" id="bgp">
			<script type='text/javascript'>
				$( '#bgp' ).forms([
					{ title: $lang.ENABLE_BGP, name: 'f_bgp_on', type: 'checkbox', value: nvram.bgp_on == '1' },
					{ title: $lang.CUSTOM_OPTIONS, name: 'f_bgp_raw_on', type: 'checkbox', value: nvram.bgp_raw_on == '1' },
					{ title: $lang.RAW_CONFIG, name: 'bgp_rawconfig', type: 'textarea', value: nvram.bgp_rawconfig, style: 'width: 100%; height: 200px;' }
				]);
			</script>
			</div>
			<div id='bgpgird'>
			<div class='heading'><script type="text/javascript">document.write($lang.BGP_INSTANCE)</script></div>
			<div class="section content">
			<table class='line-table' id='bgp-instance'></table>
			</div>
			<div class='heading'>BGP <script type="text/javascript">document.write($lang.VAR_TERM_PARAM_NETWORK)</script></div>
			<div class="section content">
			<table class='line-table' id='bgp-network'></table>
			</div>
			<div class='heading'>BGP Redistribute</div>
			<div class="section content">
			<table class='line-table' id='bgp-redistribute'></table>
			</div>
			<div class='heading'>BGP Peer</div>
			<div class="section content">
			<table class='line-table' id='bgp-peer'></table>
			</div>
			<div class='heading'>BGP <script type="text/javascript">document.write($lang.CUSTOM_OPTIONS)</script></div>
			<div class="section content">
			<table class='line-table' id='bgp-custom'></table>
			</div>
			</div>
		</div>

		<div class="box" data-box="routing-misc">
			<div class="heading"><script type="text/javascript">document.write($lang.OTHER_SETTING)</script></div>
			<div class="content misc"></div>
			<script type="text/javascript">
				$( '.content.misc' ).forms([
	{ title: $lang.VAR_NET_MODE, name: 'wk_mode', type: 'select', options: [['gateway', $lang.VAR_GATEWAY],['router', $lang.ROUTER]], value: nvram.wk_mode },
/* ZEBRA-BEGIN */
	{ title: 'RIPv1 &amp; v2', name: 'dr_setting', type: 'select',	options: [[0, $lang.VAR_CLOSE],[1,'LAN'],[2,'WAN'],[3,'Both']], value: nvram.dr_setting },
/* ZEBRA-END */
/* EMF-BEGIN */
	{ title: $lang.EFFICIENT_MULTICAST_FORWARDING, name: 'f_emf', type: 'checkbox', value: nvram.emf_enable != '0' },
/* EMF-END */
	{ title: $lang.DHCP_ROUTING, name: 'f_dhcp_routes', type: 'checkbox', value: nvram.dhcp_routes != '0' },
	{ title: $lang.SPANNING_TREE_PROTOCOL, name: 'f_stp', type: 'checkbox', value: nvram.lan_stp != '0' }
	            ]);
			</script>
		</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %> <i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	</form>

	<script type="text/javascript">earlyInit(); verifyFields( null, 1 );</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
