<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram("lan_ipaddr,lan_netmask,dhcpd_static,dhcpd_startip,dhcpd_static_only,cstats_include"); %>
if(!nvram.dhcpd_static){
	nvram.dhcpd_static = '';
}

if (nvram.lan_ipaddr.match(/^(\d+\.\d+\.\d+)\.(\d+)$/)) ipp = RegExp.$1 + '.';
	else ipp = '?.?.?.';

autonum = aton(nvram.lan_ipaddr) & aton(nvram.lan_netmask);
var sg = new TomatoGrid();
sg.exist = function(f, v) {
	var data = this.getAllData();
	for (var i = 0; i < data.length; ++i) {
		if (data[i][f] == v) return true;
	}
	return false;
}

sg.existMAC = function(mac) {
	if (isMAC0(mac)) return false;
	return this.exist(0, mac) || this.exist(1, mac);
}
sg.existName = function(name)
{
return this.exist(3, name);
}
sg.inStatic = function(n)
{
return this.exist(2, n);
}

sg.dataToView = function(data) {
var v = [];
var s = data[0];
if (!isMAC0(data[1])) s += '<br>' + data[1];
v.push(s);
for (var i = 2; i < data.length; ++i)
v.push(escapeHTML('' + data[i]));
return v;
},
sg.sortCompare = function(a, b) {
var da = a.getRowData();
var db = b.getRowData();
var r = 0;
switch (this.sortColumn) {
case 0:
r = cmpText(da[0], db[0]);
break;
case 1:
r = cmpIP(da[2], db[2]);
break;
}
if (r == 0) r = cmpText(da[3], db[3]);
return this.sortAscending ? r : -r;
}
sg.verifyFields = function(row, quiet)
{
var f, s, i;
f = fields.getAll(row);
if (!v_macz(f[0], quiet)) return 0;
if (!v_macz(f[1], quiet)) return 0;
if (isMAC0(f[0].value)) {
f[0].value = f[1].value;
f[1].value = '00:00:00:00:00:00';
}
else if (f[0].value == f[1].value) {
f[1].value = '00:00:00:00:00:00';
}
else if ((!isMAC0(f[1].value)) && (f[0].value > f[1].value)) {
s = f[1].value;
f[1].value = f[0].value;
f[0].value = s;
}
for (i = 0; i < 2; ++i) {
if (this.existMAC(f[i].value)) {
			ferror.set(f[i], $lang.DUPLICATE_MAC_ADDRESS, quiet);
return 0;
}
}	
if (f[2].value.indexOf('.') == -1) {
s = parseInt(f[2].value, 10)
if (isNaN(s) || (s <= 0) || (s >= 255)) {
ferror.set(f[2], $lang.INVALID_IP_ADDRESS, quiet);
return 0;
}
f[2].value = ipp + s;
}
if ((!isMAC0(f[0].value)) && (this.inStatic(f[2].value))) {
ferror.set(f[2], $lang.DUPLICATE_IP_ADDRESS, quiet);
return 0;
}
s = f[3].value.trim().replace(/\s+/g, ' ');
if (s.length > 0) {
if (s.search(/^[.a-zA-Z0-9_\- ]+$/) == -1) {
			ferror.set(f[3], $lang.INVALID_HOSTNAME, quiet);
return 0;
}
if (this.existName(s)) {
ferror.set(f[3], $lang.DUPLICATE_HOSTNAME, quiet);
return 0;
}
f[3].value = s;
}
if (isMAC0(f[0].value)) {
if (s == '') {
s = $lang.MAC_NAME_REQUIRED_TIP;
ferror.set(f[0], s, 1);
ferror.set(f[3], s, quiet);
return 0;
}
}
	if (!v_nodelim(f[4], quiet, $lang.VAR_RULE_DESC) ||(!v_ascii(f[4],quiet))) return 0;
return 1;
}

sg.resetNewEditor = function() {
var f, c, n;
f = fields.getAll(this.newEditor);
ferror.clearAll(f);
if ((c = cookie.get('addstatic')) != null) {
cookie.set('addstatic', '', 0);
c = c.split(',');
if (c.length == 3) {
f[0].value = c[0];
f[1].value = '00:00:00:00:00:00';
f[2].value = c[1];
f[3].value = c[2];
return;
}
}
f[0].value = '00:00:00:00:00:00';
f[1].value = '00:00:00:00:00:00';
f[3].value = '';
n = 10;
do {
if (--n < 0) {
f[2].value = '';
return;
}
autonum++;
} while (((c = fixIP(ntoa(autonum), 1)) == null) || (c == nvram.lan_ipaddr) || (this.inStatic(c)));
f[2].value = c;
}
sg.setup = function()
{
this.init('bs-grid', 'sort', 140, [
{ multi: [ { type: 'text', maxlen: 17 }, { type: 'text', maxlen: 17 } ] },
{ type: 'text', maxlen: 15 },
{ type: 'text', maxlen: 50 },
{ type: 'text', maxlen: 64 }] );
this.headerSet([$lang.VAR_DEVICE_MAC, $lang.VAR_IP, $lang.WAN_HOSTNAME, $lang.VAR_RULE_DESC]);
var s = nvram.dhcpd_static.split('>');
for (var i = 0; i < s.length; ++i) {
var t = s[i].split('<');
if (t.length >= 3) {
var d = t[0].split(',');
var desc;
if (t.length == 4) desc = t[3]; else desc = '';
			this.insertData(-1, [d[0], (d.length >= 2) ? d[1] : '00:00:00:00:00:00',(t[1].indexOf('.') == -1) ? (ipp + t[1]) : t[1], t[2], desc]);
}
}
this.sort(2);
this.showNewEditor();
this.resetNewEditor();
}
function verifyFields(focused, quiet)
{
return 1;
}
function save()
{
if (sg.isEditing()) return;
var data = sg.getAllData();
var sdhcp = '';
var i;
for (i = 0; i < data.length; ++i) {
var d = data[i];
sdhcp += d[0];
if (!isMAC0(d[1])) sdhcp += ',' + d[1];
sdhcp += '<' + d[2] + '<' + d[3] + '<' + d[4] + '>';
}
var fom = E('_fom');
fom.dhcpd_static_only.value = 0;
fom.dhcpd_static.value = sdhcp;
// form.submit(fom, 1);
return submit_form('_fom');
}

function init()
{
sg.recolor();
}


	
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#forward-static.asp'>
<input type='hidden' name='_service' value='dhcpd-restart,arpbind-restart,cstats-restart'>

<input type='hidden' name='dhcpd_static'>
<input type='hidden' name='dhcpd_static_only'>
<input type='hidden' name='cstats_include'>


		<div class="box">
			<div class="heading"><script type="text/javascript">document.write($lang.STATIC)</script> DHCP</div>
			<div class="content">
				<table class="line-table" id="bs-grid"></table><br />

			</div>
		</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>
        
	</form>

	<script type="text/javascript">sg.setup();</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
