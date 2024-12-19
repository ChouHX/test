<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("dtu_mode1,local_port1,server_ip1,server_port1,socket_type1,socket_timeout1,serial_timeout1,packet_len1,m2m_product_id,heartbeat_data1,heartbeat_intval1,serial_rate1,serial_parity1,serial_databits1,serial_stopbits1,gps_data")%>

function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c;

	// --- visibility ---

	var vis = {
		_dtu_mode1: 1,
		_gps_data:1,
		_local_port1: 1,
		_server_ip1: 1,
		_server_port1: 1,
		_socket_type1: 1,
		_socket_timeout1: 1,
		_serial_timeout1: 1,
		_packet_len1: 1,
		
		_m2m_product_id: 1,
		_heartbeat_intval1: 1,
		
		_serial_rate1: 1,
		_serial_parity1: 1,
		_serial_databits1: 1,
		_serial_stopbits1: 1
	};

	c = E('_dtu_mode1').value;
	switch(c) {
		case 'disable':
			vis._gps_data = 0,
			vis._local_port1 = 0;
			vis._socket_type1 = 0;
			vis._socket_timeout1 = 0;
			vis._serial_timeout1 = 0;
			vis._packet_len1 = 0;
			vis._serial_rate1 = 0;
			vis._serial_parity1 = 0;
			vis._serial_databits1 = 0;
			vis._serial_stopbits1 = 0;
			// fall through
		case 'server':
			vis._server_ip1 = 0;
			vis._server_port1 = 0;			
			//vis._m2m_product_id = 0;
			//vis._heartbeat_intval1 = 0;
            vis._serial_rate1 = 0;
			vis._serial_parity1 = 0;
			vis._serial_databits1 = 0;
			vis._serial_stopbits1 = 0;
			break;
		case 'client':
			vis._local_port1 = 0;
			vis._socket_timeout1 = 0;
			vis._serial_timeout1 = 0;
			vis._packet_len1 = 0;
			vis._serial_rate1 = 0;
			vis._serial_parity1 = 0;
			vis._serial_databits1 = 0;
			vis._serial_stopbits1 = 0;
			break;

	}
	c = E('_gps_data').value;
	if (c == 'relay')
	{
		vis._m2m_product_id = 0;
        	vis._heartbeat_intval1 = 0;
	}

	for (a in vis) {
		b = E(a);
		c = vis[a];
		b.disabled = (c != 1);
		PR(b).style.display = c ? '' : 'none';
	}

	
		// domain name or IP address
	a = ['_server_ip1'];
	for (i = a.length - 1; i >= 0; --i)
		if (((!v_length(a[i], 1, 1)) || ((!v_ip(a[i], 1)) && (!v_domain(a[i], 1))))) {
			if (!quiet && ok) ferror.show(a[i]);
			ok = 0;
		}
		// range
	a = [['_socket_timeout1', 1, 1440], ['_serial_timeout1', 1, 1440], ['_heartbeat_intval1', 1, 1440], ['_packet_len1', 1, 1048]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}
	
	
	return ok;
}


function earlyInit()
{
	verifyFields(null, 1);
}

function save()
{
	if (!verifyFields(null, false)) return;

	var fom = E('_fom');

	// form.submit(fom, 1);
	return submit_form('_fom');

}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading">GPS</div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
<input type='hidden' name='_nextpage' value='/#forward-dtu.asp'>
<input type='hidden' name='_nextwait' value='5'>
<input type='hidden' name='_service' value='dtu-restart'>
				<div id="gpsconfig"></div>
			</form>

			<script type='text/javascript'>

				$('#gpsconfig').forms([
			  { title: 'GPS ' + $lang.NET_MODE,  name: 'dtu_mode1', type: 'select', options: [['disable', $lang.VAR_CLOSE],['server', $lang.SERVER],['client', $lang.CLIENT]],	value: nvram.dtu_mode1 },
    { title: $lang.DATA_FORMAT,  name: 'gps_data', type: 'select', options: [['relay', 'NMEA'],['m2m_fmt', 'M2M_FMT']],	value: nvram.gps_data},
	{ title: $lang.LOCAL_PORT1, name: 'local_port1', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.local_port1, 40001), hidden:1  },
	{ title: $lang.CENTRAL_HOST_IP_PORT, multi: [
		{ name: 'server_ip1', type: 'text', maxlen: 63, size: 64, value: nvram.server_ip1, suffix: ':' },
		{ name: 'server_port1', type: 'text', maxlen: 5, size: 7, value: nvram.server_port1 } ]},
	{ title: $lang.SOCKET_TYPE, name: 'socket_type1', type: 'select', options: [['tcp', 'TCP'],['udp', 'UDP']], value: nvram.socket_type1, hidden:1  },
	{ title: $lang.SOCKET_TIMEOUT1, name: 'socket_timeout1', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_MILLISECOND +')</i>', value: nvram.socket_timeout1, hidden:1  },
	{ title: $lang.SERIAL_TIMEOUT1, name: 'serial_timeout1', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_MILLISECOND +')</i>', value: nvram.serial_timeout1, hidden:1  },
	{ title: $lang.PROTOCOL_PACKAGE_SIZE, name: 'packet_len1', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_BYTE +')</i>', value: nvram.packet_len1, hidden:1  },
	null,
	{ title: $lang.LINK_HEART_PACKET_CONTENT, name: 'm2m_product_id', type: 'text', maxlen: 14, size: 15, value: nvram.m2m_product_id },
	{ title: $lang.HEART_BEAT_INTERVAL, name: 'heartbeat_intval1', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_SECOND +')</i>', value: nvram.heartbeat_intval1 },
	null,
	{ title: $lang.IBST_SERIAL_RATE, name: 'serial_rate1', type: 'select', options: [['9600', '9600'],['19200', '19200'],['38400', '38400'],['57600', '57600'],['115200', '115200']],	value: nvram.serial_rate1, hidden:1  },
	{ title: $lang.IBST_SERIAL_PARITY, name: 'serial_parity1', type: 'select', options: [['none', $lang.VAR_CLOSE],['even', $lang.VAR_EVEN],['odd', $lang.VAR_ODD]],	value: nvram.serial_parity1, hidden:1  },
	{ title: $lang.IBST_SERIAL_DATABITS, name: 'serial_databits1', type: 'select', options: [['5', '5'],['6', '6'],['7', '7'],['8', '8']],	value: nvram.serial_databits1, hidden:1  },
	{ title: $lang.IBST_SERIAL_STOPBITS, name: 'serial_stopbits1', type: 'select', options: [['1', '1'],['2', '2']],	value: nvram.serial_stopbits1, hidden:1  }
					], { align: 'left' });
			</script>
    <!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

<script type='text/javascript'>earlyInit();</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
