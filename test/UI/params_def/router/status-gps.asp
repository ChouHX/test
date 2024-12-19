<!--
-->
<title><%translate("Status");%>: <%translate("GPS Status");%></title>
<content>
<script type='text/javascript' src='js/status-data-gps.jsx?_http_id=<% nv(http_id); %>'></script>
	<script type="text/javascript">
var ref = new TomatoRefresh('status-data-gps.jsx', '', 0, 'status_gps_refresh');

ref.refresh = function(text)
{
	stats = {};
	try {
		eval(text);
	}
	catch (ex) {
		stats = {};
	}
	show();
}
function c(id, htm)
{
	E(id).cells[1].innerHTML = htm;
}

function show()
{
	c('gps_valid', stats.gps_valid);
	c('gps_bds', stats.gps_bds);
	c('gps_use', stats.gps_use);
	c('gps_date', stats.gps_date);
	c('gps_mesg', stats.gps_mesg);
//	c('gps_google_map', gps_google_map);
}

function earlyInit()
{
	show();
}

function init()
{
	var c;
	if (((c = cookie.get('status_gps_gps_vis')) != null) && (c != '1')) toggleVisibility("gps");

	ref.initPage(3000, 3);
}
function toggleVisibility(whichone) {
	if (E('sesdiv_' + whichone).style.display == '') {
		E('sesdiv_' + whichone).style.display = 'none';
		E('sesdiv_' + whichone + '_showhide').innerHTML = '(show)';
		cookie.set('status_gps_' + whichone + '_vis', 0);
	} else {
		E('sesdiv_' + whichone).style.display='';
		E('sesdiv_' + whichone + '_showhide').innerHTML = '(hide)';
		cookie.set('status_gps_' + whichone + '_vis', 1);
	}
}

	</script>

	<div class="box">
		<div class="heading"><%translate("GPS Status");%></div>
		<div class="content" >
			<form id="_fom">
				<div id="status_gps"></div>
			</form>
			<script type='text/javascript'>
				createFieldTable('',[
				{ title: '<%translate("Current");%>', rid: 'gps_valid', text: stats.gps_valid },
				{ title: '<%translate("System Type");%>', rid: 'gps_bds', text: stats.gps_bds},
				{ title: '<%translate("Satellites Numbers");%>', rid: 'gps_use', text: stats.gps_use},
				{ title: '<%translate("Satellites Clock");%>', rid: 'gps_date', text: stats.gps_date },
				{ title: '<%translate("Positioning");%>', rid: 'gps_mesg', text: stats.gps_mesg },
				{ title: '<%translate("Google Map");%>', rid: 'gps_google', text: '<a href="'+stats.gps_google_map+'" target="_new"><%translate("View");%></a>'}
				], '#status_gps', 'data-table dataonly' );
			</script>
        </div>
        </div>

	<script type='text/javascript'>earlyInit()</script>
</content>
