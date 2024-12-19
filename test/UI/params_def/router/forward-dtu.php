<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("dtu_mode,local_port,server_ip,server_port,server2_ip,server2_port,socket_type,socket_timeout,serial_timeout,packet_len,m2m_product_id,heartbeat_data,heartbeat_intval,serial_rate,serial_parity,serial_databits,serial_stopbits,modbus_server_domain,modbus_mode,modbus_tcp_mode,modbus_server_port,modbus_bind_port,modbus_serial_rate,modbus_serial_parity,modbus_serial_databits,modbus_serial_stopbits,ipoc_mode,modbus_protocol,port_type,debug_enable,cache_enable,debug_num,dt_phone,max_lost_ack,dt_fail_action")%>

var bi = JSON.parse(nvram.bi);
function verifyFields(focused, quiet)
{
	var i;
	var ok = 1;
	var a, b, c;

	// --- visibility ---

	var vis = {
		_dtu_mode: 1,
		_local_port: 1,
		_server_ip: 1,
		_server_port: 1,
		_dt_phone: 1,
		_socket_type: 1,
		_socket_timeout: 1,
		_serial_timeout: 1,
		_packet_len: 1,
		
		_m2m_product_id: 1,
		_heartbeat_intval: 1,
		_max_lost_ack: 0,
		_dt_fail_action: 0,
		
		_f_debug_enable:1,
		_f_cache_enable:1,
		_debug_num:1,
		_serial_rate: 1,
		_serial_parity: 1,
		_serial_databits: 1,
		_serial_stopbits: 1,
		
		//modbus
		_ipoc_mode:1,
		_port_type:1,
		_modbus_protocol:1,
		_modbus_bind_port: 1,
		_modbus_mode: 1,
		_modbus_tcp_mode: 1,
		_modbus_server_domain: 1,
		_modbus_server_port: 1,
		_modbus_serial_rate: 1,
		_modbus_serial_parity: 1,
		_modbus_serial_databits: 1,
		_modbus_serial_stopbits: 1
	};
	d = E('_ipoc_mode').value;
	switch(d)
	{
		case 'serial':
			c = E('_dtu_mode').value;
			switch(c) {
				case 'disable':
					vis._local_port = 0;
					vis._dt_phone = 0;
					vis._socket_type = 0;
					vis._socket_timeout = 0;
					vis._serial_timeout = 0;
					vis._packet_len = 0;
					vis._f_debug_enable = 0;
					vis._f_cache_enable = 0;
					vis._debug_num = 0;
					vis._serial_rate = 0;
					vis._serial_parity = 0;
					vis._serial_databits = 0;
					vis._serial_stopbits = 0;
					vis._port_type = 0;
					
					// fall through
				case 'server':
					vis._server_ip = 0;
					vis._server_port = 0;			
					vis._heartbeat_intval = 0;
					vis._modbus_protocol = 0;
					vis._dt_phone = 0;
					vis._m2m_product_id = 0;
					vis._modbus_mode= 0;
					vis._modbus_tcp_mode= 0;
					vis._modbus_server_domain= 0;
					vis._modbus_server_port= 0;
					vis._modbus_serial_rate= 0;
					vis._modbus_serial_parity= 0;
					vis._modbus_serial_databits= 0;
					vis._modbus_serial_stopbits= 0;
					vis._modbus_bind_port= 0;
					break;
				case 'client':
					vis._local_port = 0;
					vis._modbus_protocol = 0;					
				
					vis._modbus_mode= 0;
					vis._modbus_tcp_mode= 0;
					vis._modbus_server_domain= 0;
					vis._modbus_server_port= 0;
					vis._modbus_serial_rate= 0;
					vis._modbus_serial_parity= 0;
					vis._modbus_serial_databits= 0;
					vis._modbus_serial_stopbits= 0;
					vis._modbus_bind_port= 0;
					vis._dt_phone = 0;
				
					break;
			}
			break;
		case 'modbus':
			c = E('_modbus_mode').value;
			switch(c) {
				case '0':
					vis._local_port = 0;
					vis._dt_phone = 0;
					vis._socket_type = 0;
					vis._socket_timeout = 0;
					vis._serial_timeout = 0;
					vis._packet_len = 0;
					vis._f_debug_enable = 0;
					vis._f_cache_enable = 0;
					vis._debug_num = 0;
					vis._serial_rate = 0;
					vis._serial_parity = 0;
					vis._serial_databits = 0;
					vis._serial_stopbits = 0;													
					vis._server_ip = 0;
					vis._server_port = 0;			
					vis._heartbeat_intval = 0;
					vis._modbus_protocol = 0;
			
					vis._m2m_product_id = 0;						
					vis._modbus_tcp_mode= 0;
					vis._modbus_server_domain= 0;
					vis._modbus_server_port= 0;
					vis._modbus_serial_rate= 0;
					vis._modbus_serial_parity= 0;
					vis._modbus_serial_databits= 0;
					vis._modbus_serial_stopbits= 0;
					vis._modbus_bind_port= 0;
					vis._dtu_mode = 0;
					vis._port_type = 0;
					break;
				case '1':
					c = E('_modbus_tcp_mode').value;
					vis._local_port = 0;
					vis._dt_phone = 0;
					vis._socket_type = 0;
					vis._socket_timeout = 0;
					vis._serial_timeout = 0;
					vis._packet_len = 0;
					vis._f_debug_enable = 0;
					vis._f_cache_enable = 0;
					vis._debug_num = 0;
					vis._serial_rate = 0;
					vis._serial_parity = 0;
					vis._serial_databits = 0;
					vis._serial_stopbits = 0;
												
					vis._server_ip = 0;
					vis._server_port = 0;			
					vis._heartbeat_intval = 0;			
					vis._m2m_product_id = 0;
					vis._dtu_mode = 0;
					
					switch(c){
						//client
						case '0':
							vis._modbus_bind_port= 0;
							break;
						//server
						case '1':
							vis._modbus_server_domain= 0;
							vis._modbus_server_port= 0;
							
							break;
					}
				
					break;
			}
			break;
		case 'dt':
			vis._local_port = 0;
			vis._dtu_mode= 0;
			vis._max_lost_ack = 1;
			vis._dt_fail_action = 1;
//			vis._m2m_product_id = 0;
			vis._modbus_protocol = 0;					
			vis._modbus_mode= 0;
			vis._modbus_tcp_mode= 0;
			vis._modbus_server_domain= 0;
			vis._modbus_server_port= 0;
			vis._modbus_serial_rate= 0;
			vis._modbus_serial_parity= 0;
			vis._modbus_serial_databits= 0;
			vis._modbus_serial_stopbits= 0;
			vis._modbus_bind_port= 0;
			
																
//			vis._m2m_product_id = 0;
			vis._dtu_mode = 0;
			break;
		default:
			break;
	}	

	for (a in vis) {
		b = E(a);
		c = vis[a];
		b.disabled = (c != 1);
		PR(b).style.display = c ? '' : 'none';
	}
	if(vis._f_debug_enable == 1)
	{
	
		var b = E('_f_debug_enable').checked;
		elem.display(PR('_debug_num'), b);
		E('_debug_num').disabled = !b;


	}
	if((bi.r_type.indexOf("R21") != -1) && (bi.r21 == 1) && (bi.gps == 'e'))//ttyUSB100
	{
        E('_port_type').options[1].disabled = 1;
        E('_port_type').value = 0;
	}
	else//ttyS0
	{
        E('_port_type').options[0].disabled = 1;
        E('_port_type').value = 1;
	}
	if(!v_ascii('_m2m_product_id',quiet)) return 0;

	a = ['_server_ip','_modbus_server_domain'];
	for (i = a.length - 1; i >= 0; --i)
		if (((!v_length(a[i], 1, 1)) || ((!v_ip(a[i], 1)) && (!v_domain(a[i], 1))))) {
			if (!quiet && ok) ferror.show(a[i]);
			ok = 0;
		}
		// range
	a = [['_socket_timeout', 1, 1440], ['_serial_timeout', 1, 1440], ['_heartbeat_intval', 1, 1440], ['_packet_len', 1, 1048]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}
	a = [['_debug_num', 1, 1024]];
	for (i = a.length - 1; i >= 0; --i) {
		v = a[i];
		if ((vis[v[0]]) && (!v_range(v[0], quiet || !ok, v[1], v[2]))) ok = 0;
	}
	
	a = ['_local_port','_server_port','_modbus_server_port','_modbus_bind_port'];
	for(i=a.length - 1;i >= 0;--i)
	{
		if((vis[a[i]]) && (!v_port(a[i], quiet || !ok)))
		{
			ok = 0;
		}
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

	fom.debug_enable.value = E('_f_debug_enable').checked ? 1 : 0;
	fom.cache_enable.value = E('_f_cache_enable').checked ? 1 : 0;
	// form.submit(fom, 1);
	return submit_form('_fom');
}

function init()
{
}
	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.SERIAL_APPLICATION)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' >
<input type='hidden' name='_nextwait' value='5'>
                <input type='hidden' name='_service' value='dtu-restart'>
                <input type='hidden' name='_moveip' value='1'>
                <input type='hidden' name='_reboot' value='0'>
				<input type='hidden' name='debug_enable'>
<input type='hidden' name='cache_enable'>
				<div id="dtuconfig"></div>
			</form>

			<script type='text/javascript'>

			$('#dtuconfig').forms([
	{ title: $lang.IPOC_MODE, name: 'ipoc_mode', type: 'select', options: [['serial', 'Serial'],['modbus', 'Modbus'],['dt', 'DT']],value: nvram.ipoc_mode },
	{ title: $lang.MODE_ENABLED,  name: 'modbus_mode', type: 'select', options: [ ['0', $lang.VAR_CLOSE],['1', $lang.VAR_TERM_TERM_PARAMS_ACTIVATE]],	value: nvram.modbus_mode },
	{ title: $lang.MODBUS_TCP_MODE,  name: 'modbus_tcp_mode', type: 'select', options: [ ['0', $lang.CLIENT],['1', $lang.SERVER]],	value: nvram.modbus_tcp_mode },	
	{ title: $lang.LOCAL_PORT1, name: 'modbus_bind_port', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.modbus_bind_port, 1502) },
	{ title: $lang.CENTRAL_HOST_IP_PORT, multi: [
		{ name: 'modbus_server_domain', type: 'text', maxlen: 63, size: 64, value: nvram.modbus_server_domain, suffix: ':' },
		{ name: 'modbus_server_port', type: 'text', maxlen: 5, size: 7, value: nvram.modbus_server_port } ]},							
		{ title: $lang.MODBUS_PROTOCOL, name: 'modbus_protocol', type: 'select', options: [['rtu', 'RTU']],value: nvram.modbus_protocol },
	{ title: $lang.IBST_SERIAL_RATE, name: 'modbus_serial_rate', type: 'select', options: [['300', '300'],['600', '600'],['1200', '1200'],['2400', '2400'],['4800', '4800'],['9600', '9600'],['19200', '19200'],['38400', '38400'],['57600', '57600'],['115200', '115200']],	value: nvram.modbus_serial_rate },
	{ title: $lang.IBST_SERIAL_PARITY, name: 'modbus_serial_parity', type: 'select', options: [['none', $lang.VAR_NONE],['even', $lang.VAR_EVEN],['odd', $lang.VAR_ODD]],	value: nvram.modbus_serial_parity },
	{ title: $lang.IBST_SERIAL_DATABITS, name: 'modbus_serial_databits', type: 'select', options: [['5', '5'],['6', '6'],['7', '7'],['8', '8']],	value: nvram.modbus_serial_databits },
	{ title: $lang.IBST_SERIAL_STOPBITS, name: 'modbus_serial_stopbits', type: 'select', options: [['1', '1'],['2', '2']],	value: nvram.modbus_serial_stopbits },
					
	{ title: $lang.SERIAL_APPLICATION + $lang.MODBUS_TCP_MODE,  name: 'dtu_mode', type: 'select', options: [['disable', $lang.VAR_CLOSE],['server', $lang.SERVER],['client', $lang.CLIENT]],	value: nvram.dtu_mode },
	{ title: $lang.LOCAL_PORT1, name: 'local_port', type: 'text', maxlen: 5, size: 7, value: fixPort(nvram.local_port, 40001) },
	{ title: $lang.CENTRAL_HOST_IP_PORT, multi: [
		{ name: 'server_ip', type: 'text', maxlen: 63, size: 64, value: nvram.server_ip, suffix: ':' },
		{ name: 'server_port', type: 'text', maxlen: 5, size: 7, value: nvram.server_port } ]},
/*	{ title: '<%translate("Backup Server IP/Port");%>', multi: [
		{ name: 'server2_ip', type: 'text', maxlen: 63, size: 64, value: nvram.server2_ip, suffix: ':' },
		{ name: 'server2_port', type: 'text', maxlen: 5, size: 7, value: nvram.server2_port } ]},*/
	{ title: $lang.SOCKET_TYPE, name: 'socket_type', type: 'select', options: [['tcp', 'TCP'],['udp', 'UDP']], value: nvram.socket_type },
	{ title: $lang.SOCKET_TIMEOUT1, name: 'socket_timeout', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_MILLISECOND +')</i>', value: nvram.socket_timeout },
	{ title: $lang.SERIAL_TIMEOUT1, name: 'serial_timeout', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_MILLISECOND +')</i>', value: nvram.serial_timeout },
	{ title: $lang.PROTOCOL_PACKAGE_SIZE, name: 'packet_len', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_BYTE +')</i>', value: nvram.packet_len },
	null,
	{ title: $lang.LINK_HEART_PACKET_CONTENT, name: 'm2m_product_id', type: 'text', maxlen: 14, size: 15, value: nvram.m2m_product_id },
	{ title: $lang.HEART_BEAT_INTERVAL, name: 'heartbeat_intval', type: 'text', maxlen: 5, size: 7, suffix: ' <i>('+ $lang.VAR_SECOND +')</i>', value: nvram.heartbeat_intval },
	{ title: $lang.PORT_TYPE,  name: 'port_type', type: 'select', options: [ ['0', 'Console'],['1', 'RS485/RS232']],	value: nvram.port_type },
	null,
	{ title: $lang.DT_PHONE, name: 'dt_phone', type: 'text', maxlen: 11, size: 11, value: nvram.dt_phone },
	{ title: $lang.DT_MAX_LOST_ACK, name: 'max_lost_ack', type: 'text', maxlen: 11, size: 11, value: nvram.max_lost_ack },
	{ title: $lang.EXCEPTION_HANDLING,name:'dt_fail_action',type:'select',options:[['0',$lang.RESTART_DTU],['1',$lang.RECONNECT_NETWORK],['2',$lang.REBOOT_SYSTEM]],value:nvram.dt_fail_action},
	{ title: $lang.CACHE_ENABLED, name: 'f_cache_enable', type: 'checkbox', value: nvram.cache_enable == 1 },
	{ title: $lang.DEBUG_ENABLE, name: 'f_debug_enable', type: 'checkbox', value: nvram.debug_enable == 1 },
	{ title: $lang.DEBUG_DATA_LENGTH, name: 'debug_num', type: 'text', maxlen: 5, size: 7, value: nvram.debug_num },
	{ title: $lang.IBST_SERIAL_RATE, name: 'serial_rate', type: 'select', options: [['300', '300'],['600', '600'],['1200', '1200'],['2400', '2400'],['4800', '4800'],['9600', '9600'],['19200', '19200'],['38400', '38400'],['57600', '57600'],['115200', '115200']],	value: nvram.serial_rate },
	{ title: $lang.IBST_SERIAL_PARITY, name: 'serial_parity', type: 'select', options: [['none', $lang.VAR_NONE],['even', $lang.VAR_EVEN],['odd', $lang.VAR_ODD]],	value: nvram.serial_parity },
	{ title: $lang.IBST_SERIAL_DATABITS, name: 'serial_databits', type: 'select', options: [['5', '5'],['6', '6'],['7', '7'],['8', '8']],	value: nvram.serial_databits },
	{ title: $lang.IBST_SERIAL_STOPBITS, name: 'serial_stopbits', type: 'select', options: [['1', '1'],['2', '2']],	value: nvram.serial_stopbits }
					], { align: 'left' });
			</script>
			</div>
			</div>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type="text/javascript">earlyInit();</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
