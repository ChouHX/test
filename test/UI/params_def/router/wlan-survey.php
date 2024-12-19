<?PHP include 'header.php'; ?>
<style type="text/css">
#survey-grid .brate {
	color: blue;
}
#survey-grid .grate {
	color: green;
}
#survey-grid .co4,#survey-grid .co5 #survey-grid .co6,#survey-grid .co7 {
	text-align: center;
}
#survey-msg {
}
#survey-controls {
	text-align: right;
}
#expire-time, #refresh-time {
	width: 120px;
	vertical-align: middle;
}
</style>
<script type="text/javascript">
var wlscandata = [];
var entries = [];
var dayOfWeek = ['<%translate("Sun");%>','<%translate("Mon");%>','<%translate("Tue");%>','<%translate("Wed");%>','<%translate("Thu");%>','<%translate("Fri");%>','<%translate("Sat");%>'];

Date.prototype.toWHMS = function() {
	return dayOfWeek[this.getDay()] + ' ' + this.getHours() + ':' + this.getMinutes().pad(2)+ ':' + this.getSeconds().pad(2);
}

var sg = new TomatoGrid();

sg.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {
		case 0:
			r = -cmpDate(da.lastSeen, db.lastSeen);
		break;
		case 3:
			r = cmpInt(da.channel, db.channel);
		break;
		case 4:
			r = cmpInt(da.rssi, db.rssi);
		break;
		default:
			r = cmpText(a.cells[col].innerHTML, b.cells[col].innerHTML);
	}
	if (r == 0) r = cmpText(da.bssid, db.bssid);

	return this.sortAscending ? r : -r;
}

sg.rateSorter = function(a, b)
{
	if (a < b) return -1;
	if (a > b) return 1;
	return 0;
}

sg.populate = function()
{
	var added = 0;
	var removed = 0;
	var i, j, k, t, e, s;

	if((wlscandata.length == 1) && (!wlscandata[0][0]))
	{
		setMsg("error: " + wlscandata[0][1]);
		return;
	}
	for(i=0;i<wlscandata.length;++i)
	{
		s = wlscandata[i];
		e = null;

		for(j=0;j<entries.length;++j)
		{
			if(entries[j].bssid == s[2])
			{
				e = entries[j];
				break;
			}
		}
		if(!e)
		{
			++added;
			e = {};
			e.firstSeen = new Date();
			entries.push(e);
		}
		e.lastSeen = new Date();
		e.band = s[0]
		e.bssid = s[2];
		e.ssid = s[1];
		e.channel = s[3];
		e.enc = s[4];
		e.rssi = s[5];
		e.saw = 1;
	}

	for(i=0;i<entries.length;++i)
	{
		var seen,m;

		e = entries[i];
		seen = e.lastSeen.toWHMS();
		if(useAjax())
		{
			m = Math.floor(((new Date()).getTime() - e.firstSeen.getTime()) / 60000);
			if(m <= 10)
			{
				seen += '<br> <small>NEW (' + -m + 'm)</small>';
			}
		}
		sg.insert(-1, e, ['<small>' + seen + '</small>','' + e.band,'' + e.ssid,e.bssid,'' + e.channel,e.rssi + ' <small>dBm</small>','' + e.enc], false);
	}

	s = '';
	if(useAjax())
	{
		s = added + ' <%translate("added");%>, ' + removed + ' <%translate("removed");%>, ';
	}
	s += entries.length + ' <%translate("total");%>.';
	s += '<br><small><%translate("Last updated");%>: ' + (new Date()).toWHMS() + '</small>';
	setMsg(s);
	wlscandata = [];
}

sg.setup = function() {
	this.init('survey-grid', 'sort');
	this.headerSet(['<%translate("Last Seen");%>', '<%translate("Radio Band");%>', '<%translate("SSID");%>', '<%translate("BSSID");%>', '<%translate("Channel");%>', '<%translate("RSSI");%> &nbsp; &nbsp; ', '<%translate("Encryption");%>']);
	this.populate();
	this.sort(0);
}

function setMsg(msg)
{
	E('survey-msg').innerHTML = msg;
}

var ref = new TomatoRefresh('/update.cgi', 'exec=wlscan', 0, 'tools_survey_refresh');
ref.refresh = function(text)
{
	try {
		eval(text);
	}
	catch (ex) {
		return;
	}
	sg.removeAllData();
	sg.populate();
	sg.resort();
}

function earlyInit() {
	if (!useAjax()) E('expire-time').style.visibility = 'hidden';
	sg.setup();
	sg.recolor();
	$('#survey-controls .spinner').after('&nbsp; ' + genStdTimeList('expire-time', '<%translate("Auto Expire");%>', 1) + genStdTimeList('refresh-time', '<%translate("Auto Refresh");%>', 1));
	ref.initPage();
	ref.start();
}
</script>
<div class="box">
<div class="heading"><%translate("Wireless Site Survey");%></div>
<div class="content">
<br /><table id="survey-grid" class="line-table"></table><br />
<div id="survey-msg"></div>
</div>
</div>
<div id="survey-controls">
<div class="spinner"></div>
<button type="button" value="Refresh" onclick="ref.toggle();" id="refresh-button" class="btn"><%translate("Refresh");%> <i class="icon-refresh"></i></button>
</div>
<div class="clearfix"></div><br />
<script type="text/javascript">
if ('<% wlclient(); %>' == '0') {
	$('#tabs').after('<div class="alert alert-warning icon"><h5>Warning!</h5> <%translate("wlan_survey_info1");%>. <a class="close"><i class="icon-cancel"></i></a></div>');
}

earlyInit();
</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
