<?PHP include 'header.php'; ?>
<style type='text/css'>
#qosg-grid {
	width: 100%;
}
#qosg-grid .co1 {
	width: 26%;
}
#qosg-grid .co2,
#qosg-grid .co3,
#qosg-grid .co4,
#qosg-grid .co5{
	width: 16%;
}
#qosg-grid .co6{
    width: 10%;
}
</style>
	<script type="text/javascript">
// <% nvram("new_qoslimit_enable,qos_ibw,qos_obw,new_qoslimit_rules,lan_ipaddr,lan_netmask,qosl_enable,qosl_dlr,qosl_dlc,qosl_ulr,qosl_ulc,qosl_udp,qosl_tcp"); %>

if(!nvram.new_qoslimit_rules) {
	nvram.new_qoslimit_rules = '';
}

var class_prio = [['0',$lang.PRIORITY_LEVELS[0]],['1',$lang.PRIORITY_LEVELS[1]],['2',$lang.PRIORITY_LEVELS[2]],['3',$lang.PRIORITY_LEVELS[3]],['4',$lang.PRIORITY_LEVELS[4]]];

var qosg = new TomatoGrid();
qosg.setup = function() {
	this.init('qosg-grid', '', 80, [
		{ type: 'text', maxlen: 31 },
		{ type: 'text', maxlen: 6 },
		{ type: 'text', maxlen: 6 },
		{ type: 'text', maxlen: 6 },
		{ type: 'text', maxlen: 6 },
		{ type: 'select', options: class_prio }
		]);
	this.headerSet([$lang.IP_SEGMENT_MAC, $lang.DOWNLOAD_RATE, $lang.MAXIMUM_DOWNLOAD_RATE, $lang.UPLOAD_RATE, $lang.MAXIMUM_UPLOAD_RATE, $lang.PRIORITY]);
	var qoslimitrules = nvram.new_qoslimit_rules.split('>');
	for (var i = 0; i < qoslimitrules.length; ++i) {
		var t = qoslimitrules[i].split('<');
		if (t.length == 8) this.insertData(-1, t);
	}
	this.showNewEditor();
	this.resetNewEditor();
}

qosg.dataToView = function(data) {
	return [data[0],data[1]+'kbps',data[2]+'kbps',data[3]+'kbps',data[4]+'kbps',class_prio[data[5]*1][1]];
}

qosg.resetNewEditor = function() {
	var f, c, n;

	var f = fields.getAll(this.newEditor);
	ferror.clearAll(f);
	if ((c = cookie.get('addbwlimit')) != null) {
		cookie.set('addbwlimit', '', 0);
		c = c.split(',');
		if (c.length == 2) {
	f[0].value = c[0];
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].selectedIndex = '2';
	return;
		}
	}

	f[0].value = '';
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].selectedIndex = '2';

	}

qosg.exist = function(f, v)
{
	var data = this.getAllData();
	for (var i = 0; i < data.length; ++i) {
		if (data[i][f] == v) return true;
	}
	return false;
}

qosg.existID = function(id)
{
	return this.exist(0, id);
}

qosg.existIP = function(ip)
{
	if (ip == "0.0.0.0") return true;
	return this.exist(0, ip);
}

qosg.checkRate = function(rate)
{
	var s = parseInt(rate, 10);
	if( isNaN(s) || s <= 0 || a >= 100000 ) return true;
	return false;
}

qosg.checkRateCeil = function(rate, ceil)
{
	var r = parseInt(rate, 10);
	var c = parseInt(ceil, 10);
	if( r > c ) return true;
	return false;
}

qosg.verifyFields = function(row, quiet)
{
	var ok = 1;
	var f = fields.getAll(row);
	var s;
	var v,a = [['_qos_ibw', 10, 999999],['_qos_obw', 10, 999999]];
	if(!E('_f_new_qoslimit_enable').checked)
	{
		return 0;
	}
	for(i=0;i<=a.length-1;i++)
	{
		v = a[i];
		if(!v_range(v[0], quiet, v[1], v[2])) return 0;
	}
	if(ok && v_macip(f[0], quiet, 0, nvram.lan_ipaddr, nvram.lan_netmask))
	{
		if(this.existIP(f[0].value))
		{
			ferror.set(f[0], $lang.DUPLICATE_IP_OR_MAC_ADDRESS, quiet);
			ok = 0;
		}
	}
	else
	{
		ok = 0;
	}
	if(ok && this.checkRate(f[1].value))
	{
		ferror.set(f[1], $lang.DOWNLOAD_RATE + $lang.MUST_BETWEEN_1_AND_99999, quiet);
		ok = 0;
	}
	if(ok && this.checkRate(f[2].value))
	{
		ferror.set(f[2], $lang.MAXIMUM_DOWNLOAD_RATE + $lang.MUST_BETWEEN_1_AND_99999, quiet);
		ok = 0;
	}
	if(ok && this.checkRateCeil(f[1].value, f[2].value))
	{
		ferror.set(f[2], $lang.MAXIMUM_DOWNLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.DOWNLOAD_RATE, quiet);
		ok = 0;
	}
	if(ok && this.checkRateCeil(f[2].value,E('_qos_ibw').value))
	{
		ferror.set(f[2], $lang.TOTAL_DOWNLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.MAXIMUM_DOWNLOAD_RATE, quiet);
		ok = 0;
	}
	if(ok && this.checkRate(f[3].value))
	{
		ferror.set(f[3], $lang.UPLOAD_RATE + $lang.MUST_BETWEEN_1_AND_99999, quiet);
		ok = 0;
	}
	if(ok && this.checkRate(f[4].value))
	{
		ferror.set(f[4], $lang.MAXIMUM_UPLOAD_RATE + $lang.MUST_BETWEEN_1_AND_99999, quiet);
		ok = 0;
	}
	if(ok && this.checkRateCeil(f[3].value, f[4].value))
	{
		ferror.set(f[4], $lang.MAXIMUM_UPLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.UPLOAD_RATE, quiet);
		ok = 0;
	}
	if(ok && this.checkRateCeil(f[4].value,E('_qos_obw').value))
	{
		ferror.set(f[4], $lang.TOTAL_UPLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.MAXIMUM_UPLOAD_RATE, quiet);
		ok = 0;
	}

	return ok;
}

function checkdefRateCeil(rate, ceil)
{
	var r = parseInt(rate, 10);
	var c = parseInt(ceil, 10);
	if( r > c ) return true;
	return false;
}
function verifyFields(focused, quiet)
{
	var c,v;
	var a = !E('_f_new_qoslimit_enable').checked;
	var b = !E('_f_qosl_enable').checked;

	E('_qos_ibw').disabled = a;
	E('_qos_obw').disabled = a;
	E('_f_qosl_enable').disabled = a;

	E('_qosl_dlr').disabled = b || a;
	E('_qosl_dlc').disabled = b || a;
	E('_qosl_ulr').disabled = b || a;
	E('_qosl_ulc').disabled = b || a;
	E('_qosl_tcp').disabled = b || a;
	E('_qosl_udp').disabled = b || a;

	elem.display(PR('_qos_ibw'), PR('_qos_obw'), !a);
	elem.display(PR('_qosl_dlr'), PR('_qosl_dlc'), PR('_qosl_ulr'), PR('_qosl_ulc'), !a && !b);

	if(E('_f_new_qoslimit_enable').checked)
	{
		c = [['_qos_ibw', 10, 999999],['_qos_obw', 10, 999999]];
		for(i=0;i<=c.length-1;i++)
		{
			v = c[i];
			if(!v_range(v[0], quiet, v[1], v[2])) return 0;
		}
		if(E('_f_qosl_enable').checked)
		{
			c = [['_qosl_dlr', 10, 999999],['_qosl_dlc', 10, 999999],['_qosl_ulr', 10, 999999],['_qosl_ulc', 10, 999999]];
			for(i=0;i<=c.length-1;i++)
			{
				v = c[i];
				if(!v_range(v[0], quiet, v[1], v[2])) return 0;
			}

			if(checkdefRateCeil(E('_qosl_dlc').value,E('_qos_ibw').value))
			{
				ferror.set(E('_qosl_dlc'), $lang.TOTAL_DOWNLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.MAXIMUM_DOWNLOAD_RATE, quiet);
				return 0;
			}
			if(checkdefRateCeil(E('_qosl_dlr').value,E('_qosl_dlc').value))
			{
				ferror.set(E('_qosl_dlr'), $lang.MAXIMUM_DOWNLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.DOWNLOAD_RATE, quiet);
				return 0;
			}
			if(checkdefRateCeil(E('_qosl_ulc').value,E('_qos_obw').value))
			{
				ferror.set(E('_qosl_ulc'), $lang.TOTAL_UPLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.MAXIMUM_UPLOAD_RATE, quiet);
				return 0;
			}
			if(checkdefRateCeil(E('_qosl_ulr').value,E('_qosl_ulc').value))
			{
				ferror.set(E('_qosl_ulr'), $lang.MAXIMUM_UPLOAD_RATE + $lang.MUST_BE_GREATER_THAN + $lang.UPLOAD_RATE, quiet);
				return 0;
			}
		}
	}
	return 1;
}

function save()
{
	var t,x;
	if (qosg.isEditing()) return;
	if (!verifyFields(null, false)) return;

	var data = qosg.getAllData();
	var qoslimitrules = '';
	var i;

	if (data.length != 0)
	{
		t = data[0].join('<');
		x = t.split('<');
		if (x.length == 6)
		{
			t += '<0<0';
		}
		qoslimitrules += t;
	}
	for (i = 1; i < data.length; ++i)
	{
		t = data[i].join('<');
		x = t.split('<');
		if (x.length == 6)
		{
			t += '<0<0';
		}
		qoslimitrules += '>' + t;
	}

	var fom = E('_fom');
	fom.new_qoslimit_enable.value = E('_f_new_qoslimit_enable').checked ? 1 : 0;
	fom.qosl_enable.value = E('_f_qosl_enable').checked ? 1 : 0;
	fom.new_qoslimit_rules.value = qoslimitrules;
	if(nvram.new_qoslimit_enable != fom.new_qoslimit_enable.value)
	{
		fom._service.disabled = 1;
		fom._reboot.value = '1';
		// form.submit(fom);
		return submit_form('_fom');
	}
	else
	{
		// form.submit(fom, 1);
		return submit_form('_fom');
	}
}

function init()
{
	qosg.recolor();
}
	</script>

<div>
    <form id="_fom" method="post" action="tomato.cgi">
        <input type='hidden' name='_nextpage' value='/#forward-bwlimit.asp'>
        <input type='hidden' name='_nextwait' value='10'>
		<input type='hidden' name='_reboot' value='0'>
        <input type='hidden' name='_service' value='qoslimit-restart'> 
        <input type='hidden' name='new_qoslimit_enable'>
        <input type='hidden' name='new_qoslimit_rules'>
        <input type='hidden' name='qosl_enable'>       
        <div class="box">
            <div class="heading"><script type="text/javascript">document.write($lang.BANDWIDTH_SPEED_LIMIT)</script></div>
            <div class="content" >
                <div id="Bandwidth"></div>
             </div>
         </div> 
            <div class="box"> 
            <div class="content" >
            	<table class='line-table' id='qosg-grid'></table>
                </div>
            </div>
       <div class="box">
            <div class="heading"><script type="text/javascript">document.write($lang.DEFAULT_GROUP)</script></div>
            <div class="content" >
                <div id="Default"></div>
             </div>
       </div>  
</div>
			</form>

			<script type='text/javascript'>

				$('#Bandwidth').forms([
			{ title: $lang.ENABLE_SPEED_LIMIT, name: 'f_new_qoslimit_enable', type: 'checkbox', value: nvram.new_qoslimit_enable != '0' },
			{ title: $lang.TOTAL_DOWNLOAD_RATE, indent: 2, name: 'qos_ibw', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qos_ibw },
			{ title: $lang.TOTAL_UPLOAD_RATE, indent: 2, name: 'qos_obw', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qos_obw }
					], { align: 'left' });
					
			$('#Default').forms([	
			{ title: $lang.ENABLE_DEFAULT_GROUP, name: 'f_qosl_enable', type: 'checkbox', value: nvram.qosl_enable == '1'},
			{ title: $lang.DOWNLOAD_RATE, indent: 2, name: 'qosl_dlr', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qosl_dlr },
			{ title: $lang.MAXIMUM_DOWNLOAD_RATE, indent: 2, name: 'qosl_dlc', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qosl_dlc },
			{ title: $lang.UPLOAD_RATE, indent: 2, name: 'qosl_ulr', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qosl_ulr },
			{ title: $lang.MAXIMUM_UPLOAD_RATE, indent: 2, name: 'qosl_ulc', type: 'text', maxlen: 6, size: 8, suffix: ' <small>kbit/s</small>', value: nvram.qosl_ulc },
			{ title: $lang.TCP_LIMIT, indent: 2, name: 'qosl_tcp', type: 'select', options:
				[['0', $lang.NO_LIMIT],
				['1', '1'],
				['2', '2'],
				['5', '5'],
				['10', '10'],
				['20', '20'],
				['50', '50'],
				['100', '100'],
				['200', '200'],
				['500', '500'],
				['1000', '1000']], value: nvram.qosl_tcp ,hidden:1},
			{ title: $lang.UDP_LIMIT, indent: 2, name: 'qosl_udp', type: 'select', options:
				[['0', $lang.NO_LIMIT],
				['1', '1/s'],
				['2', '2/s'],
				['5', '5/s'],
				['10', '10/s'],
				['20', '20/s'],
				['50', '50/s'],
				['100', '100/s']], value: nvram.qosl_udp ,hidden:1}
				
			], { align: 'left' });	
					
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

<script type='text/javascript'>qosg.setup(); verifyFields(null, 1);</script>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
