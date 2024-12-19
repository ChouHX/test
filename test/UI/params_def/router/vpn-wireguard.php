<?PHP include 'header.php'; ?>
<script type="text/javascript">

//	<% nvram("wireguard_svr_list,wireguard_persistent_keepalive,wireguard_allowed_ips,wireguard_enable,wireguard_local_key,wireguard_local_ip,wireguard_remote_key,wireguard_remote_ip,wireguard_remote_port,wireguard_preshared_key,wireguard_mode,wireguard_bind_port,wireguard_peer_subnet"); %>
if(!nvram.wireguard_svr_list) {
	nvram.wireguard_svr_list = '';
}

function verifyFields(focused, quiet)
{
	var ok = 1, c;

	var b = E('_f_wireguard_enable').checked;
	E('_wireguard_local_key').disabled = !b;
	E('_wireguard_local_ip').disabled = !b;
	E('_wireguard_remote_key').disabled = !b;
	E('_wireguard_remote_ip').disabled = !b;
	E('_wireguard_remote_port').disabled = !b;
	E('_wireguard_mode').disabled = !b;
	E('_wireguard_preshared_key').disabled = !b;
	E('_wireguard_bind_port').disabled = !b;
	E('_wireguard_peer_subnet').disabled = !b;
	E('_wireguard_allowed_ips').disabled = !b;
	E('_wireguard_persistent_keepalive').disabled = !b;

	var c = E('_wireguard_mode').value;
	//server
	if(c == '0')
	{
		elem.display(PR('_wireguard_local_key'), 1);
		elem.display(PR('_wireguard_local_ip'), 1);
		elem.display(PR('_wireguard_remote_key'), 0);
		elem.display(PR('_wireguard_remote_ip'), 0);
		elem.display(PR('_wireguard_remote_port'), 0);
		elem.display(PR('_wireguard_preshared_key'), 0);
		elem.display(PR('_wireguard_bind_port'), 1);
		elem.display(PR('_wireguard_peer_subnet'), 1);
		elem.display(PR('_wireguard_persistent_keepalive'), 0);
		elem.display(PR('_wireguard_allowed_ips'), 0);
		document.getElementById('wgserver-grid').style.display = '';
	}
	else//client
	{
		elem.display(PR('_wireguard_local_key'), 1);
		elem.display(PR('_wireguard_local_ip'), 1);
		elem.display(PR('_wireguard_remote_key'), 1);
		elem.display(PR('_wireguard_remote_ip'), 1);
		elem.display(PR('_wireguard_remote_port'), 1);
		elem.display(PR('_wireguard_preshared_key'), 1);
		elem.display(PR('_wireguard_bind_port'), 0);
		elem.display(PR('_wireguard_peer_subnet'), 1);
		elem.display(PR('_wireguard_persistent_keepalive'), 1);
		elem.display(PR('_wireguard_allowed_ips'), 1);
		document.getElementById('wgserver-grid').style.display = 'none';
	}

	return ok;
}
var wgserver = new TomatoGrid();

wgserver.dataToView = function(data) {
	return [data[0], data[1], data[2]];
}

wgserver.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].value, f[1].value, f[2].value];
}

wgserver.verifyFields = function(row, quiet)
{
	var f;

	f = fields.getAll(row);
	if (!v_length(f[0], quiet,1,1024)) return 0;
	if (!v_range(f[1], quiet,0,65535)) return 0;
	if (!v_length(f[2], quiet,1,1024)) return 0;

	return 1;
}

wgserver.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].value = '0.0.0.0/0';
	f[1].value = '25';
	f[2].value = '';

	ferror.clearAll(fields.getAll(this.newEditor));
}

wgserver.setup = function() {
	this.init('wgserver-grid', 'sort', 50, [
		{ type: 'text', maxlen: 512 },
		{ type: 'text', maxlen: 32 },
		{ type: 'text', maxlen: 64 },
		]);
		this.headerSet(['Allowd IPS', 'Persistent Keepalive', 'Peer Key']);
	var nv = nvram.wireguard_svr_list.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==3)
		{
			this.insertData(-1, [t[0], t[1],t[2]]);
		}
	}
	wgserver.showNewEditor();
}
function save()
{
	if (wgserver.isEditing()) return;
	var data = wgserver.getAllData();
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		if(i < data.length - 1 )
			s += data[i].join('<') + '>';
		else
			s += data[i].join('<');
	}
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  fom.wireguard_svr_list.value = s;
  fom.wireguard_enable.value = E('_f_wireguard_enable').checked ? "1" : "0";
  // form.submit('_fom', 1);
  return submit_form('_fom');
}

function init()
{
	wgserver.recolor();
	wgserver.resetNewEditor();
}
	</script>

	<div class="box">
		<div class="heading">Wireguard</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#vpn-wireguard.asp'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='wireguard-restart'>
				<input type='hidden' name='wireguard_enable'>
				<input type='hidden' name='wireguard_svr_list'>
				<div id="wireguard"></div>
				 <div class='section'>
                    <table class='line-table' cellspacing=1 id='wgserver-grid'></table>
                    <script type='text/javascript'>wgserver.setup(); init();</script>
                </div>
			</form>
			<script type='text/javascript'>
				$('#wireguard').forms([
				{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, name: 'f_wireguard_enable', type: 'checkbox', value: (nvram.wireguard_enable == '1') },
				{ title: $lang.MODBUS_TCP_MODE, name: 'wireguard_mode', type: 'select', options: [['0', $lang.SERVER],['1', $lang.CLIENT]],value: nvram.wireguard_mode },
				{ title: $lang.PEER_IP_PORT, multi: [
					{ name: 'wireguard_remote_ip', type: 'text', maxlen: 64, size: 32, value: nvram.wireguard_remote_ip, suffix: ':' },
					{ name: 'wireguard_remote_port', type: 'text', maxlen: 5, size: 7, value: nvram.wireguard_remote_port } ]
				},
				{ title: $lang.LOCAL_PORT1, name: 'wireguard_bind_port', type: 'text', maxlen: 5, size: 7, value: nvram.wireguard_bind_port },
				{ title: $lang.LOCAL_KEY, name: 'wireguard_local_key', type: 'text', maxlen: 128, size: 48, value: nvram.wireguard_local_key },
				{ title: $lang.LOCAL_IP_MASK, name: 'wireguard_local_ip', type: 'text', maxlen: 32, size: 20, value: nvram.wireguard_local_ip, suffix: $lang.EX + '. 192.168.88.5/24' },
				{ title: $lang.PEER_KEY, name: 'wireguard_remote_key', type: 'text', maxlen: 128, size: 48, value: nvram.wireguard_remote_key },
				{ title: $lang.IPSEC1_PSKKEY, name: 'wireguard_preshared_key', type: 'text', maxlen: 128, size: 48, value: nvram.wireguard_preshared_key },
				{ title: $lang.PERSISTENT_KEEPALIVE, name: 'wireguard_persistent_keepalive', type: 'text', maxlen: 5, size: 7, value: nvram.wireguard_persistent_keepalive },
				{ title: $lang.ALLOWED_IPS, name: 'wireguard_allowed_ips', type: 'text', maxlen: 128, size: 32, value: nvram.wireguard_allowed_ips, suffix: $lang.EX + '. 192.168.88.0/24 or 192.168.88.0/24,192.168.99.0/24' },
				{ title: $lang.PEER_SUBNET_IP_MASK, name: 'wireguard_peer_subnet', type: 'text', maxlen: 128, size: 32, value: nvram.wireguard_peer_subnet, suffix: $lang.EX + '. 192.168.88.0/24 or 192.168.88.0/24,192.168.99.0/24' }
					], { align: 'left' });
			</script>
         </div>
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type='text/javascript'>verifyFields(null, true);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
