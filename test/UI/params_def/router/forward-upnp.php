<?PHP include 'header.php'; ?>
	<script type="text/javascript">

		/* REMOVE-BEGIN
		!!TB - additional miniupnp settings
		REMOVE-END */
//	<% nvram("upnp_enable,upnp_mnp,upnp_clean,upnp_secure,upnp_clean_interval,upnp_clean_threshold,upnp_lan,upnp_lan1,upnp_lan2,upnp_lan3,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname,"); %>

// <% upnpinfo(); %>

nvram.upnp_enable = fixInt(nvram.upnp_enable, 0, 3, 0);


function submitDelete(proto, eport)
{
	form.submitHidden('upnp.cgi', {
		remove_proto: proto,
		remove_eport: eport,
		_redirect: 'forward-upnp.asp' });
}

function deleteData(data)
{
	if (!confirm('Delete ' + data[3] + ' ' + data[0] + ' -> ' + data[2] + ':' + data[1] + ' ?')) return;
	submitDelete(data[3], data[0]);
}
var ug = new TomatoGrid();
ug.onClick = function(cell) {
	deleteData(cell.parentNode.getRowData());
}

ug.rpDel = function(e) {
	deleteData(PR(e).getRowData());
}


ug.setup = function() {
// this.init('upnp-grid', 'sort delete');
// this.headerSet([$lang.EXT_PORTS, $lang.INT_PORT, $lang.DMZ_IPADDR, $lang.PROTOCOL, $lang.VAR_RULE_DESC]);
// ug.populate();
}

ug.populate = function() {
	var i, j, r, row, data;

	if (nvram.upnp_enable != 0) {
		var data = mupnp_data.split('\n');
		for (i = 0; i < data.length; ++i) {
			r = data[i].match(/^(UDP|TCP)\s+(\d+)\s+(.+?)\s+(\d+)\s+\[(.*)\](.*)$/);
			if (r == null) continue;
			row = this.insertData(-1, [r[2], r[4], r[3], r[1], r[5]]);

			if (!r[0]) {
				for (j = 0; j < 5; ++j) {
					elem.addClass(row.cells[j], 'disabled');
				}
			}
			for (j = 0; j < 5; ++j) {
				row.cells[j].title = $lang.CLICK_TO_DELETE;
			}
		}
		this.sort(2);
	}
	E('upnp-delete-all').disabled = (ug.getDataCount() == 0);
}

function deleteAll()
{
	if (!confirm(DELETE_ALL_ENTRIES + '?')) return;
	submitDelete('*', '0');
}

function verifyFields(focused, quiet)
{
/* REMOVE-BEGIN
	!!TB - additional miniupnp settings
REMOVE-END */
	var enable = E('_f_enable_upnp').checked || E('_f_enable_natpmp').checked;
	var bc = E('_f_upnp_clean').checked;

	E('_f_upnp_clean').disabled = (enable == 0);
	E('_f_upnp_secure').disabled = (enable == 0);
	E('_f_upnp_mnp').disabled = (E('_f_enable_upnp').checked == 0);
	E('_upnp_clean_interval').disabled = (enable == 0) || (bc == 0);
	E('_upnp_clean_threshold').disabled = (enable == 0) || (bc == 0);
	elem.display(PR(E('_upnp_clean_interval')), (enable != 0) && (bc != 0));
	elem.display(PR(E('_upnp_clean_threshold')), (enable != 0) && (bc != 0));

	if ((enable != 0) && (bc != 0)) {
		if (!v_range('_upnp_clean_interval', quiet, 60, 65535)) return 0;
		if (!v_range('_upnp_clean_threshold', quiet, 0, 9999)) return 0;
	}
	else {
		ferror.clear(E('_upnp_clean_interval'));
		ferror.clear(E('_upnp_clean_threshold'));
	}
	return 1;
}

function save()
{
/* REMOVE-BEGIN
	!!TB - miniupnp
REMOVE-END */
	if (!verifyFields(null, 0)) return;

	var fom = E('_fom');
	fom.upnp_enable.value = 0;
	if (fom.f_enable_upnp.checked) fom.upnp_enable.value = 1;
	if (fom.f_enable_natpmp.checked) fom.upnp_enable.value |= 2;

/* REMOVE-BEGIN
	!!TB - additional miniupnp settings
REMOVE-END */
	fom.upnp_mnp.value = E('_f_upnp_mnp').checked ? 1 : 0;
	fom.upnp_clean.value = E('_f_upnp_clean').checked ? 1 : 0;
	fom.upnp_secure.value = E('_f_upnp_secure').checked ? 1 : 0;
	// form.submit(fom, 0);
	return submit_form('_fom');
}

function init()
{
	ug.recolor();
}

/* REMOVE-BEGIN
	!!TB - miniupnp
REMOVE-END */
function submit_complete()
{
	reloadPage();
}
	</script>

	<form id="_fom" method="post" action="tomato.cgi">
    <input type='hidden' name='_nextpage' value='/#forward-upnp.asp'>
    <input type='hidden' name='_service' value='upnp-restart'>
    
    <input type='hidden' name='upnp_enable'>
 
    <input type='hidden' name='upnp_mnp'>
    <input type='hidden' name='upnp_clean'>
    <input type='hidden' name='upnp_secure'>
		<!-- VLAN-END -->

<!-- 		<div class="box">
			<div class="heading"><script type="text/javascript">document.write($lang.MAP_PORT)</script></div>
			<div class="content">
				<table id="upnp-grid" class="line-table"></table><br />
				<div style="width: 100%; text-align: right"><button type="button" value="Delete All" onclick="deleteAll();" id="upnp-delete-all" class="btn btn-danger"><script type="text/javascript">document.write($lang.DELETE_ALL)</script><i class="icon-cancel"></i></button>
					<button type="button" value="Refresh" onclick="javascript:reloadPage();" class="btn"><i class="icon-refresh"></i><script type="text/javascript">document.write($lang.VAR_REFRESH)</script></button></div>
			</div>
		</div> -->

		<div class="box" data-box="forward-upnp-settings">
			<div class="heading"><script type="text/javascript">document.write($lang.SETTINGS)</script></div>
			<div class="content" id="upnpsettings"></div>
			<script type="text/javascript">
				$('#upnpsettings').forms([
					{ title: $lang.ENABLE_UPNP, name: 'f_enable_upnp', type: 'checkbox', value: (nvram.upnp_enable & 1) },
{ title: $lang.ENABLE_NAT_PMP, name: 'f_enable_natpmp', type: 'checkbox', value: (nvram.upnp_enable & 2) },
/* REMOVE-BEGIN
	!!TB - additional miniupnp settings
REMOVE-END */
{ title: $lang.AUTOMATICALLY_DELETE_INVALID_RULES, name: 'f_upnp_clean', type: 'checkbox', value: (nvram.upnp_clean == '1') },
{ title: $lang.DELETE_INTERVAL, indent: 2, name: 'upnp_clean_interval', type: 'text', maxlen: 5, size: 7,
suffix: ' <small>'+ $lang.VAR_SECOND +'</small>', value: nvram.upnp_clean_interval },
{ title: $lang.DELETE_THRESHOLD, indent: 2, name: 'upnp_clean_threshold', type: 'text', maxlen: 4, size: 7,
suffix: ' <small>'+ $lang.REDIRECT + '</small>', value: nvram.upnp_clean_threshold },
{ title: $lang.SAFE_MODE, name: 'f_upnp_secure', type: 'checkbox',
suffix: ' <small>('+ $lang.UPNP_CLIENTS_CAN_ONLY_MAP_TO_THEIR_IP +')</small>',
value: (nvram.upnp_secure == '1') },
	null,
	{ title: $lang.SHOW_IN_MY_NETWORK_PLACES,  name: 'f_upnp_mnp',  type: 'checkbox',  value: (nvram.upnp_mnp == '1') }
				]);
			</script>
		</div>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	</form>
	<script type="text/javascript">ug.setup();verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
