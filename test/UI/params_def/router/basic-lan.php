<?PHP include 'header.php'; ?>
<style type='text/css'>
#lan-grid .co1,#lan-grid.co6, #lan-grid .co4,#lan-grid .co7 {
	width: 10%;
	text-align: center;
}
#lan-grid .co3,#lan-grid .co2,#lan-grid .co5 ,#lan-grid .co8{
	width: 15%;
	text-align: center;
}
#lan-grid .centered {
	text-align: center;
}
</style>
	<script type="text/javascript">
//	<% nvram("lan_dhcp_relay,lan_dhcp_server,lan1_dhcp_relay,lan1_dhcp_server,lan2_dhcp_relay,lan2_dhcp_server,lan3_dhcp_relay,lan3_dhcp_server,custom_dns,custom_dns_enable,dhcp_lease,dhcp_num,dhcp_start,dhcpd_startip,dhcpd_endip,lan_gateway,lan_ipaddr,lan_netmask,lan_proto,lan_ifname,lan_stp,lan1_ifname,lan1_ipaddr,lan1_netmask,lan1_proto,lan1_stp,dhcp1_start,dhcp1_num,dhcp1_lease,dhcpd1_startip,dhcpd1_endip,lan2_ifname,lan2_ipaddr,lan2_netmask,lan2_proto,lan2_stp,dhcp2_start,dhcp2_num,dhcp2_lease,dhcpd2_startip,dhcpd2_endip,lan3_ifname,lan3_ipaddr,lan3_netmask,lan3_proto,lan3_stp,dhcp3_start,dhcp3_num,dhcp3_lease,dhcpd3_startip,dhcpd3_endip"); %>

for(var i=0;i <= MAX_BRIDGE_ID;i++) {
	var j = (i == 0) ? '' : i.toString();
	if(!nvram['lan'+j+'_ifname']) {
		nvram['lan'+j+'_ifname'] = '';
	}
	if(!nvram['dhcpd'+j+'_startip']) {
		nvram['dhcpd'+j+'_startip'] = '';
	}
	if(!nvram['dhcpd'+j+'_endip']) {
		nvram['dhcpd'+j+'_endip'] = '';
	}
	if(!nvram['lan'+j+'_ipaddr']) {
		nvram['lan'+j+'_ipaddr'] = '';
	}
	if(!nvram['lan'+j+'_netmask']) {
		nvram['lan'+j+'_netmask'] = '';
	}
	if(!nvram['lan'+j+'_proto']) {
		nvram['lan'+j+'_proto'] = '';
	}
	if(!nvram['dhcp'+j+'_lease']) {
		nvram['dhcp'+j+'_lease'] = '';
	}
	if(nvram['lan'+j+'_dhcp_relay'] == undefined) {
		nvram['lan'+j+'_dhcp_relay'] = '0';
	}
	if(nvram['lan'+j+'_dhcp_server'] == undefined) {
		nvram['lan'+j+'_dhcp_server'] = '';
	}
}
	
var lg = new TomatoGrid();
lg.setup = function()
{
	this.init('lan-grid', '', 4, [
		{ type: 'select',options: [[0,'0'],[1,'1'],[2,'2'],[3,'3']],prefix:'<div class="centered">',suffix:'</div>'},
		{ type: 'text',maxlen: 15,size: 17},
		{ type: 'text',maxlen: 15,size: 17},
		{ type: 'checkbox',prefix:'<div class="centered">',suffix:'</div>'},
		{ multi: [{ type: 'text',maxlen: 15,size: 17},{ type: 'text',maxlen: 15,size: 17}]},
		{ type: 'text',maxlen: 6,size: 8},
		{ type: 'checkbox',prefix:'<div class="centered">',suffix:'</div>'},
		{ type: 'text',maxlen: 15,size: 17}
		]);
	this.headerSet([$lang.BRIDGING, $lang.VAR_IP, $lang.LAN_NETMASK, $lang.LAN_PROTO, $lang.VAR_IP_RANGE, $lang.DHCP_LEASE + '<i>(' + $lang.MINUTES + ')</i>', $lang.DHCP_RELAY, $lang.DHCP_SERVER_ADDRESS]);

	for(var i=0;i <= MAX_BRIDGE_ID;i++)
	{
		var j = (i == 0) ? '' : i.toString();
		if(nvram['lan'+j+'_ifname'].length > 0)
		{
			if((!fixIP(nvram['dhcpd'+j+'_startip'])) || (!fixIP(nvram['dhcpd'+j+'_endip'])))
			{
				if((fixIP(nvram['lan'+j+'_ipaddr'])) && (fixIP(nvram['lan'+j+'_netmask'])) && (nvram['dhcp'+j+'_start'] != ''))
				{
					var n = getNetworkAddress(nvram['lan'+j+'_ipaddr'],nvram['lan'+j+'_netmask']);
					nvram['dhcpd'+j+'_startip'] = getAddress(('0.0.0.' + nvram['dhcp'+j+'_start'] * 1),n);
					nvram['dhcpd'+j+'_endip'] = getAddress(('0.0.0.' + ((nvram['dhcp'+j+'_start'] * 1) + (nvram['dhcp'+j+'_num'] *1) - 1)),n);
				}
			}
			lg.insertData(-1,[i.toString(),nvram['lan'+j+'_ipaddr'],nvram['lan'+j+'_netmask'],(nvram['lan'+j+'_proto'] == 'dhcp')?'1':'0',nvram['dhcpd'+j+'_startip']
				,nvram['dhcpd'+j+'_endip'],(nvram['lan'+j+'_proto'] == 'dhcp')?(((nvram['dhcp'+j+'_lease'])*1 == 0)?'1440':(nvram['dhcp'+j+'_lease']).toString()):'',nvram['lan'+j+'_dhcp_relay'],nvram['lan'+j+'_dhcp_server']]);
		}
	}
	lg.canDelete = false;
	lg.sort(0);
	elem.removeClass(lg.header.cells[lg.sortColumn],'sortasc','sortdes');
	lg.showNewEditor();
	lg.resetNewEditor();
}

lg.dataToView = function(data)
{
	return ['br' + data[0],data[1],data[2],(data[3].toString() == '1')?'<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',
		(data[4].toString()+' - '+((numberOfBitsOnNetMask(data[2])>=24)?(data[5].split('.').splice(3,1).toString()):(data[5].toString()))),
		(((data[6] != null) && (data[6] != ''))?data[6]:''),(data[7].toString() == '1')?'<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[8]];
}

lg.dataToFieldValues = function(data)
{
	return [data[0],data[1].toString(),data[2].toString(),(data[3].toString() == '1')?'checked':'',data[4].toString(),data[5].toString(),data[6].toString(),(data[7].toString() == '1')?'checked':'',data[8].toString()];
}

lg.fieldValuesToData = function(row)
{
	var f = fields.getAll(row);
	return [f[0].value,f[1].value,f[2].value,f[3].checked?1:0,f[4].value,f[5].value,f[6].value,f[7].checked?1:0,f[8].value];
}

lg.resetNewEditor = function()
{
	var f = fields.getAll(this.newEditor);
	f[0].selectedIndex=0;
	var t = MAX_BRIDGE_ID;
	while((this.countBridge(f[0].selectedIndex) > 0) && (t > 0))
	{
		f[0].selectedIndex = (f[0].selectedIndex%(MAX_BRIDGE_ID))+1;
		t--;
	}
	for(var j=0;j<= MAX_BRIDGE_ID;j++)
	{
		f[0].options[j].disabled = (this.countBridge(j) > 0);
	}
	f[1].value = '';
	f[2].value = '';
	f[4].value = '';
	f[5].value = '';
	f[6].value = '';
	f[8].value = '';
	f[3].checked = 0;
	f[7].checked = 0;
	f[3].disabled = 1;
	f[4].disabled = 1;
	f[5].disabled = 1;
	f[6].disabled = 1;
	ferror.clearAll(fields.getAll(this.newEditor));
}

lg.onCancel = function()
{
	this.removeEditor();
	this.showSource();
	this.disableNewEditor(false);
	this.resetNewEditor();
}

lg.onAdd = function()
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

lg.onOK = function()
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

lg.onDelete = function()
{
	this.removeEditor();
	elem.remove(this.source);
	this.source = null;
	this.disableNewEditor(false);
	this.resetNewEditor();
}

lg.countElem = function(f, v)
{
	var data = this.getAllData();
	var total = 0;
	for(var i=0;i < data.length;++i)
	{
		total += (data[i][f] == v) ? 1 : 0;
	}
	return total;
}

lg.countBridge = function(v)
{
	return this.countElem(0,v);
}

lg.countOverlappingNetworks = function(ip)
{
	var data = this.getAllData();
	var total = 0;
	for(var i=0;i < data.length;++i)
	{
		var net = getNetworkAddress(data[i][1], data[i][2]);
		var brd = getBroadcastAddress(net, data[i][2]);
		total += ((aton(ip) <= aton(brd)) && (aton(ip) >= aton(net))) ? 1 : 0;
	}
	return total;
}

lg.verifyFields = function(row, quiet)
{
	var ok=1;
	var f;
	f = fields.getAll(row);
	if(f[7].checked && f[3].checked)
	{
		alert("When dhcp relay is on, please turn off dhcp server!");
		return 0;
	}
	for(var j=0;j <= MAX_BRIDGE_ID;j++)
	{
		f[0].options[j].disabled = (this.countBridge(j) > 0);
	}
	if(this.countBridge(f[0].selectedIndex) > 0)
	{
		ferror.set(f[0], $lang.CANNOT_ADD_ANOTHER_ENTRY_FOR+' br' + f[0].selectedIndex, quiet);
		ok = 0;
	}
	else
	{
		ferror.clear(f[0]);
	}
	if(!v_ip(f[1], quiet || !ok))
	{
		ok = 0;
	}
	if((f[1].value != '') && (f[1].value != '0.0.0.0'))
	{
		f[3].disabled = 0;
		if(!v_netmask(f[2], quiet || !ok))
		{
			return 0;
		}
		if(f[1].value == getNetworkAddress(f[1].value, f[2].value))
		{
			var s = $lang.INVALID_IP_ADDRESS_OR_SUBNET_MASK + ',' + $lang.THE_ADDRESS_OF_THE_NETWORK_CANNOT_BE_USED + ',';
			ferror.set(f[1], s, quiet);
			return 0;
		}
		else if(f[1].value == getBroadcastAddress(getNetworkAddress(f[1].value, f[2].value), f[2].value))
		{
			var s = $lang.INVALID_IP_ADDRESS_OR_SUBNET_MASK + ',' + $lang.THE_BROADCAST_ADDRESS_CANNOT_BE_USED + ',';
			ferror.set(f[1], s, quiet);
			return 0;
		}
		else if(this.countOverlappingNetworks(f[1].value) > 0)
		{
			var s = $lang.INVALID_IP_ADDRESS_OR_SUBNET_MASK + ',' + $lang.CONFLICTS_OVERLAPS_WITH_ANOTHER_LAN_BRIDGE + ',';
			ferror.set(f[1], s, quiet);
			return 0;
		}
		else
		{
			ferror.clear(f[1]);
			ferror.clear(f[2]);
		}
	}
	else
	{
		f[3].checked = 0;
		f[3].disabled = 1;
	}
	if((f[3].checked) && (v_ip(f[1], 1)) && (v_netmask(f[2],1)))
	{
		f[4].disabled = 0;
		f[5].disabled = 0;
		f[6].disabled = 0;
		if(f[4].value == '')
		{
			var l;
			var m = aton(f[1].value) & aton(f[2].value);
			var o = (m) ^ (~ aton(f[2].value))
			var n = o - m;
			do {
				if(--n < 0)
				{
					f[4].value = '';
					return;
				}
				m++;
			} while(((l = fixIP(ntoa(m), 1)) == null) || (l == f[1].value));
			f[4].value = l;
		}
		if(f[5].value == '')
		{
			var l;
			var m = aton(f[1].value) & aton(f[2].value);
			var o = (m) ^ (~ aton(f[2].value));
			var n = o - m;
			do {
				if(--n < 0)
				{
					f[5].value = '';
					return;
				}
				o--;
			} while(((l = fixIP(ntoa(o), 1)) == null) || (l == f[1].value));
			f[5].value = l;
		}
		if((getNetworkAddress(f[4].value, f[2].value) != getNetworkAddress(f[1].value, f[2].value)) || (f[4].value == getBroadcastAddress(getNetworkAddress(f[1].value, f[2].value), f[2].value)) ||
				(f[4].value == getNetworkAddress(f[1].value, f[2].value)) || (f[1].value == f[4].value))
		{
			ferror.set(f[4], $lang.INVALID_FIRST_IP_ADDRESS_OR_SUBNET_MASK, quiet || !ok);
			return 0;
		}
		else
		{
			ferror.clear(f[4]);
		}
		if((getNetworkAddress(f[5].value, f[2].value) != getNetworkAddress(f[1].value, f[2].value)) || (f[5].value == getBroadcastAddress(getNetworkAddress(f[1].value, f[2].value), f[2].value)) ||
				(f[5].value == getNetworkAddress(f[1].value, f[2].value)) || (f[1].value == f[5].value))
		{
			ferror.set(f[5], $lang.INVALID_LAST_IP_ADDRESS_OR_SUBNET_MASK, quiet || !ok);
			return 0;
		}
		else
		{
			ferror.clear(f[5]);
		}
		if(aton(f[5].value) < aton(f[4].value))
		{
			var t = f[4].value;
			f[4].value = f[5].value;
			f[5].value = t;
		}
		if(parseInt(f[6].value*1) == 0)
		{
			f[6].value = 1440;
		}
		if(!v_mins(f[6], quiet || !ok, 1, 10080))
		{
			ok = 0;
		}
	}
	else
	{
		f[4].disabled = 1;
		f[5].disabled = 1;
		f[6].disabled = 1;
		ferror.clear(f[4]);
		ferror.clear(f[5]);
		ferror.clear(f[6]);
	}
	return ok;
}
function joinAddr(a) {
	var r, i, s;

	r = [];
	for (i = 0; i < a.length; ++i) {
		s = a[i];
		if ((s != '00:00:00:00:00:00') && (s != '0.0.0.0')) r.push(s);
	}
	return r.join(' ');
}
function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c, d, e;


	// --- visibility ---

	var vis = {
		_f_dns_1: 0,
		_f_dns_2: 0

	};

	vis._f_dns_1 = vis._f_dns_2 = (E('_f_custom_dns_enable').checked);	
	for (a in vis) {
		b = E(a);
		c = vis[a];

		b.disabled = (c != 1);
		PR(b).style.display = c ? '' : 'none';
	}
	
	return ok;

}

function save()
{
	if(lg.isEditing())
	{
		return;
	}
	lg.resetNewEditor();

	if(!verifyFields(null, false))
	{
		return;
	}
	
	var fom = E('_fom');
	fom.custom_dns_enable.value = fom.f_custom_dns_enable.checked ? 1 : 0;
	fom.custom_dns.value = joinAddr([fom.f_dns_1.value,fom.f_dns_2.value]);
	addList();
	for(var i=0;i <= MAX_BRIDGE_ID;i++)
	{
		var j = (i == 0) ? '' : i.toString();
		fom['lan'+j+'_ifname'].value = '';
		fom['lan'+j+'_ipaddr'].value = '';
		fom['lan'+j+'_netmask'].value = '';
		fom['lan'+j+'_proto'].value = '';
		fom['lan'+j+'_dhcp_relay'].value = '0';
		fom['lan'+j+'_dhcp_server'].value = '';
		fom['dhcp'+j+'_start'].value = '';
		fom['dhcp'+j+'_num'].value = '';
		fom['dhcp'+j+'_lease'].value = '';
		fom['dhcpd'+j+'_startip'].value = '';
		fom['dhcpd'+j+'_endip'].value = '';
	}
	var d = lg.getAllData();
	for(var i=0;i < d.length;++i)
	{
		if(lg.countOverlappingNetworks(d[i][1]) > 1)
		{
			alert($lang.LAN_CONFLICT_PROMPT_TIP);
			return;
		}
		var j = (parseInt(d[i][0]) == 0) ? '' : d[i][0].toString();
		fom['lan'+j+'_ifname'].value = 'br' + d[i][0];
		fom['lan'+j+'_ipaddr'].value = d[i][1];
		fom['lan'+j+'_netmask'].value = d[i][2];
		fom['lan'+j+'_proto'].value = (d[i][3] != '0') ? 'dhcp' : 'static';
		fom['dhcp'+j+'_start'].value = (d[i][3] != '0') ? (d[i][4]).split('.').splice(3, 1) : '';
		fom['dhcp'+j+'_num'].value = (d[i][3] != '0') ? d[i][5].split('.').splice(3, 1) - (d[i][5]).split('.').splice(3, 1) + 1 : '';
		fom['dhcp'+j+'_lease'].value = (d[i][3] != '0') ? d[i][6] : '';
		fom['dhcpd'+j+'_startip'].value = (d[i][3] != '0') ? d[i][4] : '';
		fom['dhcpd'+j+'_endip'].value = (d[i][3] != '0') ? d[i][5] : '';
		fom['lan'+j+'_dhcp_relay'].value = d[i][7];
		fom['lan'+j+'_dhcp_server'].value = d[i][8];
	}
	// var t = fixIP(fom['lan_ipaddr'].value);
	// if((fom['lan_ifname'].value != 'br0') || (fom['lan_ipaddr'].value == '0.0.0.0') || (!t))
	// {
	// 	alert($lang.BRIDGE_WITH_INDEX_0_MUST_BE_ALWAYS_DEFINED);
	// 	return;
	// }

	if(1)//confirm("<%translate("All the settings would take to effect when reboot the router, are you sure reboot");%>?"))
	{
		fom._service.disabled = 1;
		fom._reboot.value = '1';
		fom._moveip.value = 0;
		// form.submit(fom);
		return submit_form('_fom');
	}
	else
	{
		return;
	}
}

function init()
{
}
function addList(){
	var htmlOut = '';
	for (var i = 0 ; i <= MAX_BRIDGE_ID ; i++) {
		var j = (i == 0) ? '' : i.toString();
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_ifname\' name=\'lan' + j + '_ifname\'>');
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_ipaddr\' name=\'lan' + j + '_ipaddr\'>');
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_netmask\' name=\'lan' + j + '_netmask\'>');
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_proto\' name=\'lan' + j + '_proto\'>');
		htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_start\' name=\'dhcp' + j + '_start\'>');
		htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_num\' name=\'dhcp' + j + '_num\'>');
		htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_lease\' name=\'dhcp' + j + '_lease\'>');
		htmlOut += ('<input type=\'hidden\' id=\'dhcpd' + j + '_startip\' name=\'dhcpd' + j + '_startip\'>');
		htmlOut += ('<input type=\'hidden\' id=\'dhcpd' + j + '_endip\' name=\'dhcpd' + j + '_endip\'>');
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_dhcp_relay\' name=\'lan' + j + '_dhcp_relay\'>');
		htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_dhcp_server\' name=\'lan' + j + '_dhcp_server\'>');
	}
	$('#lan-grid').append(htmlOut);
}
</script>

<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#basic-lan.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_service' value='*'>
<input type='hidden' name='_moveip' value='1'>
<input type='hidden' name='_reboot' value='0'>
<input type='hidden' name='custom_dns'>
<input type='hidden' name='custom_dns_enable'>


<script type='text/javascript'>
var htmlOut = '';
for (var i = 0 ; i <= MAX_BRIDGE_ID ; i++) {
	var j = (i == 0) ? '' : i.toString();
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_ifname\' name=\'lan' + j + '_ifname\'>');
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_ipaddr\' name=\'lan' + j + '_ipaddr\'>');
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_netmask\' name=\'lan' + j + '_netmask\'>');
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_proto\' name=\'lan' + j + '_proto\'>');
	htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_start\' name=\'dhcp' + j + '_start\'>');
	htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_num\' name=\'dhcp' + j + '_num\'>');
	htmlOut += ('<input type=\'hidden\' id=\'dhcp' + j + '_lease\' name=\'dhcp' + j + '_lease\'>');
	htmlOut += ('<input type=\'hidden\' id=\'dhcpd' + j + '_startip\' name=\'dhcpd' + j + '_startip\'>');
	htmlOut += ('<input type=\'hidden\' id=\'dhcpd' + j + '_endip\' name=\'dhcpd' + j + '_endip\'>');
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_dhcp_relay\' name=\'lan' + j + '_dhcp_relay\'>');
	htmlOut += ('<input type=\'hidden\' id=\'lan' + j + '_dhcp_server\' name=\'lan' + j + '_dhcp_server\'>');
}
$('#lan-grid').append(htmlOut);
</script>

<div class="box" data-box="lan">
<div class='heading'>LAN</div>
<div class='section content'>
<table class='line-table' cellspacing=1 id='lan-grid'></table>
<script type='text/javascript'>lg.setup();</script>
</div>
</div>


<div class="box" data-box="custom_dns">
<div class='heading'>DNS</div>
<div class='section content'>
<div id="custom"></div>
<script type='text/javascript'>
if(!nvram.custom_dns){
	nvram.custom_dns = '';
}
dns = nvram.custom_dns.split(/\s+/);
	$('#custom').forms([
	{ title: $lang.USE_CUSTOM_DNS, name: 'f_custom_dns_enable', type: 'checkbox', value: nvram.custom_dns_enable == 1 },
	{ title: $lang.PRIMARY_DNS_SERVER, name: 'f_dns_1', type: 'text', maxlen: 21, size: 25, value: dns[0] || '0.0.0.0' },
	{ title: $lang.ALTERNATE_DNS_SERVER, name: 'f_dns_2', type: 'text', maxlen: 21, size: 25, value: dns[1] || '0.0.0.0' }
], { align: 'left' });

</script>
</div>
</div>
   
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- // <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />
	<script type="text/javascript">verifyFields(null, 1);</script>
</form>
<?PHP include 'footer.php'; ?>
