<!--
-->
<title>[<% ident(); %>] <% translate("Admin"); %>: <% translate("modbus"); %></title>
<style type='text/css'>
textarea {
 width: 98%;
 height: 15em;
}
</style>
<content>
	<script type="text/javascript">
//	<% nvram("modbus_mode,modbus_tcp_mode,modbus_server_domain,modbus_server_port,modbus_bind_port,modbus_serial_rate,modbus_serial_parity,modbus_serial_databits,modbus_serial_stopbits"); %>
function verifyFields(focused, quiet)
{
	var ok = 1;
	var vis = {
		_f_modbus_mode: 1,
		_modbus_tcp_mode: 1,
		_modbus_bind_port: 1,
		_modbus_server_domain: 1,
		_modbus_server_port: 1,
		_modbus_serial_rate: 1,
		_modbus_serial_parity: 1,
		_modbus_serial_databits: 1,
		_modbus_serial_stopbits: 1,
	};
	
	var a = E('_f_modbus_mode').checked;
	
	E('_modbus_tcp_mode').disabled = !a;
	E('_modbus_server_domain').disabled = !a;
	E('_modbus_server_port').disabled = !a;
	E('_modbus_bind_port').disabled = !a;
	E('_modbus_serial_rate').disabled = !a;
	E('_modbus_serial_parity').disabled = !a;
	E('_modbus_serial_databits').disabled = !a;
	E('_modbus_serial_stopbits').disabled = !a;
	
	var type = E('_modbus_tcp_mode').value;
	switch (type) {
		case '1':
			vis._modbus_server_domain = 0;
			vis._modbus_server_port = 0;
			break;
		case '0':
			vis._modbus_bind_port = 0;
			break;
	}
	
	for (a in vis) {
		b = E(a);
		c = vis[a];

		PR(b).style.display = c ? '' : 'none';
	}
	return ok;
}

function save()
{
  if (verifyFields(null, 0)==0) return;
  var fom = E('_fom');
  fom.modbus_mode.value = E('_f_modbus_mode').checked ? "1" : "0";

  form.submit('_fom', 1);
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading"><% translate("MODBUS"); %></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#admin-modbus.asps'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='modbus-restart'>
				<input type='hidden' name='_sleep' value='3'>
				<input type='hidden' name='modbus_mode'>
				<div id="modbusconfig"></div>
			</form>
		</div>
	</div>
			<script type='text/javascript'>

				$('#modbusconfig').forms([
	{ title: '<% translate("Enabled"); %>', name: 'f_modbus_mode', type: 'checkbox', value: nvram.modbus_mode != '0' },
	{ title: 'Modbus <% translate("TCP Mode"); %>', name: 'modbus_tcp_mode', type: 'select', options: [['0', '<% translate("TCP Client"); %>'],['1', '<% translate("TCP Server"); %>']],	value: nvram.modbus_tcp_mode },
	{ title: '<% translate("Modbus Server/Port"); %>', multi: [
		{ name: 'modbus_server_domain', type: 'text', maxlen: 63, size: 32, value: nvram.modbus_server_domain, suffix: ':' },
		{ name: 'modbus_server_port', type: 'text', maxlen: 5, size: 7, value: nvram.modbus_server_port } ]},
	{ title: '<% translate("Bind Port"); %>', name: 'modbus_bind_port', type: 'text', maxlen: 5, size: 7, value: nvram.modbus_bind_port },
	{ title: '<%translate("Baud Rate");%>', name: 'modbus_serial_rate', type: 'select', options: [['300', '300'],['600', '600'],['1200', '1200'],['2400', '2400'],['4800', '4800'],['9600', '9600'],['19200', '19200'],['38400', '38400'],['57600', '57600'],['115200', '115200']],	value: nvram.modbus_serial_rate },
	{ title: '<%translate("Parity Bit");%>', name: 'modbus_serial_parity', type: 'select', options: [['none', '<%translate("none");%>'],['even', '<%translate("even");%>'],['odd', '<%translate("odd");%>']],	value: nvram.modbus_serial_parity },
	{ title: '<%translate("Data Bit");%>', name: 'modbus_serial_databits', type: 'select', options: [['5', '5'],['6', '6'],['7', '7'],['8', '8']],	value: nvram.modbus_serial_databits },
	{ title: '<%translate("Stop Bit");%>', name: 'modbus_serial_stopbits', type: 'select', options: [['1', '1'],['2', '2']],	value: nvram.modbus_serial_stopbits }
], { align: 'left' });
			</script>
            
	<button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button>
	<button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn"><% translate("Cancel"); %><i class="icon-cancel"></i></button>
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>