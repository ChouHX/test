<?PHP include 'header.php'; ?>
	<script type="text/javascript" src="js/interfaces.js"></script>
	<script type="text/javascript">
		//<% nvram("dmz_enable,dmz_ipaddr,dmz_sip,dmz_ifname,dmz_ra,dmz_cli,lan_ifname,lan1_ifname,lan2_ifname,lan3_ifname"); %>
		
var lipp = asp_lanip(nvram.lan_ipaddr, 1);

function asp_lanip(lan_ipaddr, mode) {
    var ret = lan_ipaddr;
    if (lan_ipaddr && lan_ipaddr.indexOf('.') != -1) {
        lan_ipaddr = lan_ipaddr.split('.');
        if (mode == 1) {
            ret = lan_ipaddr.slice(0, 3).join('.') + '.';
        } else if (mode == 2) {
            ret = lan_ipaddr.slice(-1).join('.');
        }
    }
    return ret;
}

function verifyFields(focused, quiet)
{
	var sip, dip, off;

	off = !E('_f_dmz_enable').checked;

	dip = E('_f_dmz_ipaddr')
	dip.disabled = off;

	sip = E('_f_dmz_sip');
	sip.disabled = off;

	sip = E('_f_dmz_ra');
	sip.disabled = off;

	sip = E('_f_dmz_cli');
	sip.disabled = off;

	if (off) {
		ferror.clearAll(dip, sip);
		return 1;
	}

	if (dip.value.indexOf('.') == -1) dip.value = lipp + dip.value;
	if (!v_ip(dip)) return 0;

	if ((sip.value.length) && (!v_iptaddr(sip, quiet, 15))) return 0;
	ferror.clear(sip);

	return 1;
}

function save()
{
	var fom;
	var en;
	var s;

	if (!verifyFields(null, false)) return;

	fom = E('_fom');
	en = fom.f_dmz_enable.checked;
	fom.dmz_enable.value = en ? 1 : 0;
	if (en) {
		// shorten it if possible to be more compatible with original
		s = fom.f_dmz_ipaddr.value;
		fom.dmz_ipaddr.value = (s.indexOf(lipp) == 0) ? s.replace(lipp, '') : s;
	}
	fom.dmz_sip.value = fom.f_dmz_sip.value.split(/\s*,\s*/).join(',');
	fom.dmz_ra.value = E('_f_dmz_ra').checked ? 1 : 0;
	fom.dmz_cli.value = E('_f_dmz_cli').checked ? 1 : 0;
	// form.submit(fom, 0);
	return submit_form('_fom');
}

function init() {
}

	</script>

	<form id="_fom" method="post" action="tomato.cgi">
        <input type='hidden' name='_nextpage' value='/#forward-dmz.asp'>
        <input type='hidden' name='_service' value='firewall-restart'>
        
        <input type='hidden' name='dmz_enable'>
        <input type='hidden' name='dmz_ipaddr'>
        <input type='hidden' name='dmz_sip'>
        <input type='hidden' name='dmz_ra'>
        <input type='hidden' name='dmz_cli'>

		<div class="box">
			<div class="heading">DMZ</div>
			<div class="content dmz-settings"></div>
			<script type="text/javascript">
				$('.dmz-settings').forms([
			{ title: $lang.ENABLE_DMZ, name: 'f_dmz_enable', type: 'checkbox', value: (nvram.dmz_enable == '1') },
			{ title: $lang.DMZ_IPADDR, indent: 2, name: 'f_dmz_ipaddr', type: 'text', maxlen: 15, size: 17,
			value: (nvram.dmz_ipaddr.indexOf('.') != -1) ? nvram.dmz_ipaddr : (lipp + nvram.dmz_ipaddr) },
			{ title: $lang.SOURCE_ADDRESS_RESTRICTION, indent: 2, name: 'f_dmz_sip', type: 'text', maxlen: 512, size: 64,
			value: nvram.dmz_sip, suffix: '<br><small>('+ $lang.OPTIONALS +'; '+ $lang.EX +': "1.1.1.1", "1.1.1.0/24", "1.1.1.1 - 2.2.2.2" '+ $lang.OR +' "me.example.com")</small>' },
			null,
			{ title: $lang.LEAVE_CLI_REMOTE_ACCESS, indent: 2, name: 'f_dmz_cli', type: 'checkbox', value: (nvram.dmz_cli == '1'), suffix: ' &nbsp;<small>('+ $lang.REDIRECT_REMOTE_ACCESS_PORTS_FOR_CLI_TO_ROUTER +')</small>' },
			{ title: $lang.LEAVE_WEB_REMOTE_ACCESS, indent: 2, name: 'f_dmz_ra', type: 'checkbox', value: (nvram.dmz_ra == '1'), suffix: ' &nbsp;<small>('+ $lang.REDIRECT_REMOTE_ACCESS_PORTS_FOR_HTTPS_TO_ROUTER +')</small>' }
					]);
			</script>
		</div>
		<script type="text/javascript">if (nvram.dmz_enable == '1') show_notice1('<% notice("iptables"); %>');</script>

		<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
		<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
		<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	</form>

<script type='text/javascript'>verifyFields(null, 1);</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
