<?PHP include 'header.php'; ?>
	<script type="text/javascript">
//	<% nvram("ippass_auto_mtu_enable,ippass_auto_mtu,ippass_enable,ippass_addr,ippass_gateway_static,ippass_pidx"); %>
// <% ethinfo(); %>
var ethinfo = get_ethinfo(nvram.term_model);

portlist = [];
for(var i=0;i<ethinfo.length;i++)
{
    portlist.push([ethinfo[i].sport,ethinfo[i].title]);
}

function get_ethinfo(term_model) {
    var arr = {
        G92: [{"title": "LAN0","sport": "0"},{"title": "LAN1","sport": "1"},{"title": "LAN2","sport": "2"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "4"}],
        G51: [{"title": "WAN/LAN1","sport": "0"},{"title": "LAN2","sport": "1"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "2"}],
        R51: [{"title": "WAN/LAN1","sport": "0"},{"title": "LAN2","sport": "1"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "2"}],
        R10: [{"title": "LAN","sport": "0"}],
        R12: [{"title": "LAN","sport": "0"}],
        R20: [{"title": "WAN/CON","sport": "0"},{"title": "LAN","sport": "1"}],
        G20: [{"title": "WAN/LAN","sport": "0"},{"title": "LAN","sport": "1"}],
        R21: [{"title": "WAN/LAN","sport": "0"},{"title": "LAN","sport": "1"}],
        R23: [{"title": "WAN","sport": "0"},{"title": "LAN1","sport": "1"},{"title": "LAN2","sport": "2"}],
        R50: [{"title": "WAN/LAN","sport": "0"},{"title": "LAN1","sport": "1"},{"title": "LAN2","sport": "2"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "4"}],
        G50: [{"title": "WAN/LAN","sport": "0"},{"title": "LAN1","sport": "1"},{"title": "LAN2","sport": "2"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "4"}],
        CM2: [{"title": "WAN/CON","sport": "0"},{"title": "LAN","sport": "1"}],
        CM5: [{"title": "WAN/LAN","sport": "0"},{"title": "LAN1","sport": "1"},{"title": "LAN2","sport": "2"},{"title": "LAN3","sport": "3"},{"title": "LAN4","sport": "4"}]
    };
    if (!term_model || term_model.length < 3) {
        return arr.G92;
    }
    term_model = term_model.substr(0,3);
    if (typeof arr[term_model] == 'undefined') {
        return arr.G92;
    }
    return arr[term_model];
}

function verifyFields(focused, quiet)
{
	var off;

	off = !E('_f_ippass_enable').checked;

	E('_ippass_addr').disabled = off;
	E('_ippass_gateway_static').disabled = off;

    if(E('_ippass_addr').value != '')
    {
        if (!v_mac('_ippass_addr', quiet)) return 0;
    }
    else
    {
	if (!off && !v_mac('_ippass_addr', quiet)) return 0;
       E('_ippass_addr').value = "";
    }
	if ((E('_ippass_gateway_static').value.length)&&(!v_ip('_ippass_gateway_static', quiet))) return 0;

	return 1;
}

function save()
{
	var fom;
	var en;

	if (!verifyFields(null, false)) return;

	fom = E('_fom');
	en = fom.f_ippass_enable.checked;
	fom.ippass_enable.value = en ? 1 : 0;
	fom.ippass_auto_mtu_enable.value = fom.f_ippass_auto_mtu_enable.checked ? 1 : 0;
	fom._reboot_now.value = '1';
	// form.submit(fom);
	return submit_form('_fom');
}

function init() {
}

	</script>

	<div class="box">
		<div class="heading"><script type="text/javascript">document.write($lang.IP_PENETRATION)</script></div>
		<div class="content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#forward-ippass.asp'>
				<input type='hidden' name='ippass_enable'>
				<input type='hidden' name='ippass_auto_mtu_enable'>
				<input type='hidden' name='_reboot_now' value='1'>
			<div id="ippassconfig"></div>
			</form>
		</div>
	</div>
		<script type='text/javascript'>
				$('#ippassconfig').forms([
	{ title: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE, name: 'f_ippass_enable', type: 'checkbox', value: (nvram.ippass_enable == '1') },
	{ title: $lang.VAR_DEVICE_MAC, indent: 2, name: 'ippass_addr', type: 'text', maxlen: 17, size: 17,value: nvram.ippass_addr },
	{ title: 'Auto MTU', multi: [
	{ name: 'f_ippass_auto_mtu_enable', type: 'checkbox', value: (nvram.ippass_auto_mtu_enable == '1')},
	{ name: 'ippass_auto_mtu', type: 'text', maxlen: 17, size: 17,  prefix: '&nbsp&nbsp;', value: nvram.ippass_auto_mtu } ] },
	{ title: 'Ethernet Port', indent: 2, name: 'ippass_pidx', type: 'select', options: portlist, value: nvram.ippass_pidx },
	{ title: $lang.VAR_GATEWAY, indent: 2, name: 'ippass_gateway_static', type: 'text', maxlen: 17, size: 17,value: nvram.ippass_gateway_static }
], { align: 'left' });
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
