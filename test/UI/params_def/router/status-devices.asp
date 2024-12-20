<title><%translate("Status");%>: <%translate("Device List");%></title>
<content>
	<script type="text/javascript" src="js/wireless.jsx?_http_id=<% nv(http_id); %>"></script>
	<script type="text/javascript">
ipp = '<% lipp(); %>.';
		//<% nvram('at_update,tomatoanon_answer,lan_ifname,wl_ifname,wl_mode,wl_radio'); %>
//	<% devlist(); %>
list = [];
function find(mac, ip)
{
	var e, i;
	mac = mac.toUpperCase();
	for (i = list.length - 1; i >= 0; --i) {
		e = list[i];
		if (((e.mac == mac) && ((e.ip == ip) || (e.ip == '') || (ip == null))) ||
				((e.mac == '00:00:00:00:00:00') && (e.ip == ip))) {
			return e;
		}
	}
	return null;
}
function get(mac, ip)
{
	var e, i;
	mac = mac.toUpperCase();
	if ((e = find(mac, ip)) != null) {
		if (ip) e.ip = ip;
		return e;
	}
	e = {
mac: mac,
     ip: ip || '',
     ifname: '',
     unit: 0,
     name: '',
     rssi: '',
     txrx: '',
     lease: ''
	};
	list.push(e);
	return e;
}
var xob = null;
function _deleteLease(ip)
{
	form.submitHidden('dhcpd.cgi', { remove: ip });
}
function deleteLease(a, ip)
{
	if (xob) return;
	if ((xob = new XmlHttp()) == null) {
		_deleteLease(ip);
		return;
	}
	a = E(a);
	a.innerHTML = '<%translate("deleting");%>...';
	xob.onCompleted = function(text, xml) {
		a.innerHTML = '...';
		xob = null;
	}
	xob.onError = function() {
		_deleteLease(ip);
	}
	xob.post('dhcpd.cgi', 'remove=' + ip);
}
function addStatic(n)
{
	var e = list[n];
	cookie.set('addstatic', [e.mac, e.ip, e.name.split(',')[0]].join(','), 1);
	location.href = 'basic-static.asp';
}
function addWF(n)
{
	var e = list[n];
	cookie.set('addmac', [e.mac, e.name.split(',')[0]].join(','), 1);
	location.href = 'wlan-wfilter.asp';
}
function addbwlimit(n)
{
	var e = list[n];
	cookie.set('addbwlimit', [e.ip, e.name.split(',')[0]].join(','), 1);
	location.href = 'bwlimit.asp';
}
var ref = new TomatoRefresh('update.cgi', 'exec=devlist', 0, 'status_devices_refresh');
ref.refresh = function(text)
{
	eval(text);
	dg.removeAllData();
	dg.populate();
	dg.resort();
	for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
		if (wl_sunit(uidx)<0)
			E("noise"+uidx).innerHTML = wlnoise[uidx];
	}
}
var dg = new TomatoGrid();
dg.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var ra = a.getRowData();
	var rb = b.getRowData();
	var r;
	switch (col) {
		case 2:
			r = cmpIP(ra.ip, rb.ip);
			break;
		case 4:
			r = cmpInt(ra.rssi, rb.rssi);
			break;
		case 5:
			r = cmpInt(ra.qual, rb.qual);
			break;
		default:
			r = cmpText(a.cells[col].innerHTML, b.cells[col].innerHTML);
	}
	if (r == 0) {
		r = cmpIP(ra.ip, rb.ip);
		if (r == 0) r = cmpText(ra.ifname, rb.ifname);
	}
	return this.sortAscending ? r : -r;
}
dg.populate = function()
{
	var i, j;
	var a, b, c, e;
	list = [];
	for (i = 0; i < list.length; ++i) {
		list[i].ip = '';
		list[i].ifname = '';
		list[i].unit = 0;
		list[i].name = '';
		list[i].rssi = '';
		list[i].txrx = '';
		list[i].lease = '';
	}
	for (i = dhcpd_lease.length - 1; i >= 0; --i) {
		a = dhcpd_lease[i];
		e = get(a[2], a[1]);
		e.lease = '<small><a href="javascript:deleteLease(\'L' + i + '\',\'' + a[1] + '\')" title="<%translate("Delete Lease");%>" id="L' + i + '">' + a[3] + '</a></small>';
		e.name = a[0];
		e.ifname = '';
	}
	for (i = wldev.length - 1; i >= 0; --i) {
		a = wldev[i];
		if (a[0].indexOf('wds') == 0) {
			e = get(a[1], '-');
		}
		else {
			e = get(a[1], null);
		}
		e.ifname = a[0];
		e.unit = a[6] * 1;
		e.rssi = a[2];
		if ((a[3] > 1000) || (a[4] > 1000))
			e.txrx = ((a[3] > 1000) ? Math.round(a[3] / 1000) : '-') + ' / ' + ((a[4] > 1000) ? Math.round(a[4] / 1000) : '-'); //+ '<br><small>Mbps</small>';
	}
	for (i = arplist.length - 1; i >= 0; --i) {
		a = arplist[i];
		if ((e = get(a[1], a[0])) != null) {
			if (e.ifname == '') e.ifname = a[2];
		}
	}
	for (i = dhcpd_static.length - 1; i >= 0; --i) 
	{
		a = dhcpd_static[i].split('<');
		if (a.length < 3) continue;
		if (a[1].indexOf('.') == -1) a[1] = (ipp + a[1]);
		c = a[0].split(',');
		for (j = c.length - 1; j >= 0; --j) {
			if ((e = find(c[j], a[1])) != null) break;
		}
		if (j < 0) continue;
		if (e.ip == '') {
			e.ip = a[1];
		}
		if (e.name == '') {
			e.name = a[2];
		}
		else {
			b = e.name.toLowerCase();
			c = a[2].toLowerCase();
			if ((b.indexOf(c) == -1) && (c.indexOf(b) == -1)) {
				if (e.name != '') e.name += ', ';
				e.name += a[2];
			}
		}
	}
	for (i = list.length - 1; i >= 0; --i) {
		e = list[i];
		b = e.mac;
		var ifidx = wl_uidx(e.unit);
		if ((e.rssi !== '') && (ifidx >= 0) && (wlnoise[ifidx] < 0)) {
			e.qual = MAX(e.rssi - wlnoise[ifidx], 0);
		}
		else {
			e.qual = -1;
		}
		if(e.qual) {
			var bar = '';
			switch(MIN(MAX(Math.floor(e.qual / 10), 1), 6)) {
				case 1:
				case 2:
					bar = 'danger';
					break;
				case 3:
				case 4:
					bar = 'warning';
					break;
				case 5:
				case 6:
					bar = 'success';
					break;
			}
			this.insert(-1, e, [
			e.ifname, b, (e.ip == '-') ? '' : e.ip, e.name,
			(e.rssi != 0) ? e.rssi + ' <small>dBm</small>' : '',
					(e.qual < 0) ? '-' : '<small>' + e.qual + '%</small> <div class="progress small"><div title="' + e.qual + '%" class="bar ' + bar + '" style="width: ' + e.qual + '%;"></div></div>',
					e.txrx,	e.lease], false);
		}
	}
}
dg.setup = function()
{
	this.init('dev-grid', 'sort');
	this.headerSet(['<%translate("Interface");%>', '<%translate("MAC Address");%>', '<%translate("IP Address");%>', '<%translate("Name");%>', '<%translate("RSSI &nbsp; &nbsp;");%> ', '<%translate("Quality");%>', '<%translate("TX/RX Rate&nbsp;");%>', '<%translate("Lease &nbsp; &nbsp;");%>']);
	this.populate();
	this.sort(2);
}
function earlyInit()
{
	dg.setup();
}
function init()
{
	dg.recolor();
	ref.initPage(3000, 3);
	$('span.help-block').remove();
}
	</script>
<style type='text/css'>
#dev-grid .co1 {
	width: 8%;
}
#dev-grid .co2 {
	width: 20%;
}
#dev-grid .co3 {
	width: 13%;
}
#dev-grid .co4 {
width: 14%;
text-align: center;
}
#dev-grid .co5 {
width: 8%;
text-align: center;
}
#dev-grid .co6 {
	width: 8%;
	text-align: center;
}
#dev-grid .co7 {
width: 9%;
text-align: center;
}
#dev-grid .co8 {
width: 20%;
text-align: center;
}
#dev-grid .header {
	text-align: left;
}
</style>


	<div class="box">
		<div class="heading"><%translate("Device List");%></div>
		<div class="content">
			<table id="dev-grid" class="line-table"></table><br />
			<div class="devicedata"></div>
			<script type="text/javascript">
				f = [];
				for (var uidx = 0; uidx < wl_ifaces.length; ++uidx) {
					var u = wl_unit(uidx);
					if (nvram['wl'+u+'_radio'] == '1') {
						if (wl_sunit(uidx)<0) {
							var a = '';
							if ((nvram['wl'+u+'_mode'] == 'ap') || (nvram['wl'+u+'_mode'] == 'wds'))
								a = '&nbsp; &nbsp; <button type="button" class="btn btn-primary" value="Measure" onclick="javascript:window.location=\'wlmnoise.cgi?_http_id=' + nvram.http_id + '&_wl_unit=' + u +'\'">\
								Measure <i class="icon-signal"></i></button>';
							f.push( { title: '<b>Noise Floor (' + wl_ifaces[uidx][0] + ')</b>',
								prefix: '<span id="noise'+uidx+'">',
								custom: wlnoise[uidx],
								suffix: '</span></span> <small>dBm</small>' + a } );
						}
					}
				}
				//$('.devicedata').forms(f);
			</script>
		</div>
	</div>

	<script type="text/javascript">$('.box').after(genStdRefresh(1,1,'ref.toggle()'));</script>
	<script type="text/javascript">earlyInit(); init();</script>
</content>
