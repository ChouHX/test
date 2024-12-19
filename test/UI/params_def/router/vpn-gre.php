<?PHP include 'header.php'; ?>
<style type='text/css'>
#gre-gird .co1 {
	width: 8%;
	text-align: center;
}
#gre-grid .co2 {
	width: 12%;
}
#gre-grid .co3 {
	width: 12%;
}
#gre-grid .co4 {
	width: 12%;
}
#gre-grid .co5 {
	width: 12%;
}
#gre-grid .co6 {
	width: 8%;
}
#gre-grid .co7 {
	width: 12%;
}
#gre-grid .co8 {
	width: 12%;
}
#gre-grid .co9 {
	width: 12%;
}
.editor, .header {
	text-align: center;
}
</style>
<script type="text/javascript">

//	<% nvram("greparam,greroute"); %>
if(!nvram.greparam){
	nvram.greparam = '';
}
if(!nvram.greroute){
	nvram.greroute = '';
}

var gre = new TomatoGrid();
var grer = new TomatoGrid();
function IPv4_Address( addressDotQuad, netmaskBits ) {
	var split = addressDotQuad.split( '.', 4 );
	var byte1 = Math.max( 0, Math.min( 255, parseInt( split[0] ))); /* sanity check: valid values: = 0-255 */
	var byte2 = Math.max( 0, Math.min( 255, parseInt( split[1] )));
	var byte3 = Math.max( 0, Math.min( 255, parseInt( split[2] )));
	var byte4 = Math.max( 0, Math.min( 255, parseInt( split[3] )));
	if( isNaN( byte1 )) {	byte1 = 0;	}	/* fix NaN situations */
	if( isNaN( byte2 )) {	byte2 = 0;	}
	if( isNaN( byte3 )) {	byte3 = 0;	}
	if( isNaN( byte4 )) {	byte4 = 0;	}
	addressDotQuad = ( byte1 +'.'+ byte2 +'.'+ byte3 +'.'+ byte4 );

	this.addressDotQuad = addressDotQuad.toString();
	this.netmaskBits = Math.max( 0, Math.min( 32, parseInt( netmaskBits ))); /* sanity check: valid values: = 0-32 */
	
	this.addressInteger = IPv4_dotquadA_to_intA( this.addressDotQuad );
	this.addressBinStr  = IPv4_intA_to_binstrA( this.addressInteger );
	this.netmaskBinStr  = IPv4_bitsNM_to_binstrNM( this.netmaskBits );

	this.netaddressBinStr = IPv4_Calc_netaddrBinStr( this.addressBinStr, this.netmaskBinStr );
	this.netaddressInteger = IPv4_binstrA_to_intA( this.netaddressBinStr );
	this.netaddressDotQuad  = IPv4_intA_to_dotquadA( this.netaddressInteger );
}

function IPv4_dotquadA_to_intA( strbits ) {
	var split = strbits.split( '.', 4 );
	var myInt = (
		parseFloat( split[0] * 16777216 )	/* 2^24 */
	  + parseFloat( split[1] * 65536 )		/* 2^16 */
	  + parseFloat( split[2] * 256 )		/* 2^8  */
	  + parseFloat( split[3] )
	);
	return myInt;
}

function IPv4_intA_to_dotquadA( strnum ) {
	var byte1 = ( strnum >>> 24 );
	var byte2 = ( strnum >>> 16 ) & 255;
	var byte3 = ( strnum >>>  8 ) & 255;
	var byte4 = strnum & 255;
	return ( byte1 + '.' + byte2 + '.' + byte3 + '.' + byte4 );
}

function IPv4_intA_to_binstrA( strnum ) {
	var numStr = strnum.toString( 2 ); /* Initialize return value as string */
	var numZeros = 32 - numStr.length; /* Calculate no. of zeros */
	if (numZeros > 0) {	for (var i = 1; i <= numZeros; i++) { numStr = "0" + numStr }	} 
	return numStr;
}

function IPv4_binstrA_to_intA( binstr ) {
	return parseInt( binstr, 2 );
}

function IPv4_bitsNM_to_binstrNM( bitsNM ) {
	var bitString = '';
	var numberOfOnes = bitsNM;
	while( numberOfOnes-- ) bitString += '1'; /* fill in ones */
	numberOfZeros = 32 - bitsNM;
	while( numberOfZeros-- ) bitString += '0'; /* pad remaining with zeros */
	return bitString;
}

function IPv4_Calc_netaddrBinStr( addressBinStr, netmaskBinStr ) {
	var netaddressBinStr = '';
	var aBit = 0; var nmBit = 0;
	for( pos = 0; pos < 32; pos ++ ) {
		aBit = addressBinStr.substr( pos, 1 );
		nmBit = netmaskBinStr.substr( pos, 1 );
		if( aBit == nmBit ) {	netaddressBinStr += aBit.toString();	}
		else{	netaddressBinStr += '0';	}
	}
	return netaddressBinStr;
}

function v_cidr(e,cidr,quiet)
{
	var a;

	if(cidr.indexOf('/') == -1)
	{
		a=fixIP(cidr);
		if (!a)
		{
			ferror.set(e, $lang.INVALID_IP_ADDRESS+cidr[0], quiet);
			return 0;
		}
		return 1;
	}
	cidr=cidr.split('/');

	if(cidr.length != 2)
	{
		ferror.set(e, $lang.INVALID_NETWORK_ADDRESS + $lang.VALID_FORMAT + ': A.B.C.D/E', quiet);
		return 0;
	}
	
	a=fixIP(cidr[0]);
	if (!a)
	{
		ferror.set(e, $lang.INVALID_IP_ADDRESS + cidr[0], quiet);
		return 0;
	}
	cidr[0]=a;
	var v=cidr[1];
	if ((!v.match(/^ *[-\+]?\d+ *$/)) || (v < 0) || (v > 32)) {
		ferror.set(e, $lang.THE_EFFECTIVE_RANGE_IS + 0 + '-' + 32, quiet);
		return 0;
	}

	var cidr1=new IPv4_Address(cidr[0],cidr[1]);
	if(cidr[0] != cidr1.netaddressDotQuad)
	{
		ferror.set(e, $lang.INVALID_NETWORK_ADDRESS + $lang.YOU_MEAN + cidr1.netaddressDotQuad+'/'+cidr[1]+' ?', quiet);
		return 0;
	}
	return 1;	
}

grer.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {

	case 2:	// destination address
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // index
	case 3: 
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

grer.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1], data[2], (data[3] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[4]];
}

grer.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0, f[1].value, f[2].value,f[3].checked ? 1 : 0, f[4].value];
}

grer.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;

	if (!v_range(f[1], quiet, 1, 8)) return 0;

	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if(!v_cidr(f[2],f[2].value,quiet)) return 0;

	f[4].value = f[4].value.replace(/>/g, '_');
	if (!v_nodelim(f[4], quiet, 'Description')) return 0;

	return 1;
}

grer.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].selectedIndex = 0;
	f[2].value = '';
	f[3].checked = 0;
	f[4].value = 0;
	ferror.clearAll(fields.getAll(this.newEditor));
}

grer.setup = function() {
	this.init('grer-gird', 'sort', 20, [
		{ type: 'checkbox' },
		{ type: 'select', options: [[1, '1'],[2, '2'],[3,'3'],[4,'4'],[5,'5'],[6,'6'],[7,'7'],[8,'8']] },
		{ type: 'text', maxlen: 32 },
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 32 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.TUNNEL_NUMBER, $lang.DEST_ADDRESS, $lang.DEFAULT_ROUTE, $lang.VAR_RULE_DESC]);
	
	var nv = nvram.greroute.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==5)
		{
			t[0] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4]]);
		}
	}
	grer.sort(1);
	grer.showNewEditor();
}

gre.sortCompare = function(a, b) {
	var col = this.sortColumn;
	var da = a.getRowData();
	var db = b.getRowData();
	var r;

	switch (col) {

	case 2:	// tunnel address
	case 3:	// tunnel source
	case 4: // tunnel destination
		r = cmpIP(da[col], db[col]);
		break;
	case 0:	// on
	case 1: // index
	case 5: // keepalive
	case 6: // interval
	case 7: // retries
		r = cmpInt(da[col], db[col]);
		break;
	default:
		r = cmpText(da[col], db[col]);
		break;
	}

	return this.sortAscending ? r : -r;
}

gre.dataToView = function(data) {
	return [(data[0] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',data[1],data[2],(data[3] != '')?data[3]:$lang.VAR_AUTO,data[4],(data[5] != '0') ? '<i class="icon-check icon-green"></i>' : '<i class="icon-cancel icon-red"></i>',((data[6] != '')&&(data[6] != '0'))?data[6]:$lang.VAR_AUTO,((data[7] != '')&&(data[7] != '0'))?data[7]:$lang.VAR_AUTO,data[8]];
}

gre.fieldValuesToData = function(row) {
	var f = fields.getAll(row);
	return [f[0].checked ? 1 : 0,f[1].value,f[2].value,f[3].value,f[4].value,f[5].checked ? 1 : 0,f[6].value,f[7].value,f[8].value];
}

gre.verifyFields = function(row, quiet) {
	var f = fields.getAll(row);
	var s;

	if (!v_range(f[1], quiet, 1, 8)) return 0;

	f[2].value = f[2].value.trim();
	ferror.clear(f[2]);
	if (!v_ip(f[2], quiet)) return 0;

	f[3].value = f[3].value.trim();
	ferror.clear(f[3]);
	if((f[3].value.length) && (!v_ip(f[3], quiet))) return 0;

	f[4].value = f[4].value.trim();
	ferror.clear(f[4]);
	if (!v_ip(f[4], quiet)) return 0;

	if ((f[6].value.length)&&(!v_range(f[6], quiet, 0,255))) return 0;
	if ((f[7].value.length)&&(!v_range(f[7], quiet, 0,255))) return 0;

	f[8].value = f[8].value.replace(/>/g, '_');
	if (!v_nodelim(f[8], quiet, 'Description')) return 0;

	var data = gre.getAllData().sort(srcSort);
	for (var i = 0; i < data.length; ++i) {
		if(f[1].value == data[i][1])
		{
			ferror.set(f[1],"Don't allow same index.",quiet);
			return 0;
		}
	}

	return 1;
}

gre.resetNewEditor = function() {
	var f = fields.getAll(this.newEditor);
	f[0].checked = 1;
	f[1].value = '';
	f[2].value = '';
	f[3].value = '';
	f[4].value = '';
	f[5].checked = 0;
	f[6].value = '';
	f[7].value = '';
	f[8].value = '';
	ferror.clearAll(fields.getAll(this.newEditor));
}

gre.setup = function() {
	this.init('gre-gird', 'sort', 8, [
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 2 },
		{ type: 'text', maxlen: 16 },
		{ type: 'text', maxlen: 16 },
		{ type: 'text', maxlen: 16 },
		{ type: 'checkbox' },
		{ type: 'text', maxlen: 8 },
		{ type: 'text', maxlen: 8 },
		{ type: 'text', maxlen: 10 }]);
		this.headerSet([$lang.VAR_ENABLE, $lang.IDX, $lang.TUNNEL_ADDRESS, $lang.TUNNEL_SOURCE, $lang.TUNNEL_DEST_ADDRESS, $lang.KEEPALIVE, $lang.PINGINTERVAL, $lang.PINGMAX, $lang.VAR_RULE_DESC]);
	
	var nv = nvram.greparam.split('>');
	for (var i = 0; i < nv.length; ++i)
	{
		var t = nv[i].split('<');
		if (t.length==9)
		{
			t[0] *= 1;
			t[5] *= 1;
			this.insertData(-1, [t[0], t[1],t[2],t[3],t[4],t[5],t[6],t[7],t[8]]);
		}
	}
	gre.sort(1);
	gre.showNewEditor();
}

function srcSort(a, b)
{
	if (a[2].length) return -1;
	if (b[2].length) return 1;
	return 0;
}

function save()
{
	if (gre.isEditing()) return;

	var data = gre.getAllData().sort(srcSort);
	var s = '';
	for (var i = 0; i < data.length; ++i) {
		s += data[i].join('<') + '>';
	}

	if (grer.isEditing()) return;

	data = grer.getAllData().sort(srcSort);
	var r = '';
	for (var i = 0; i < data.length; ++i) {
		r += data[i].join('<') + '>';
	}

	var fom = E('_fom');
	fom.greparam.value = s;
	fom.greroute.value = r;

	// form.submit(fom, 0, 'tomato.cgi');
	return submit_form('_fom');
}

function init_gre()
{
	gre.recolor();
	gre.resetNewEditor();
}
function init_grer()
{
	grer.recolor();
	grer.resetNewEditor();
}

	</script>

		
			<form id="_fom" method="post" action="tomato.cgi">
                <input type='hidden' name='_nextpage' value='/#vpn-gre.asp'>
                <input type='hidden' name='_service' value='gre-restart'>
                <input type='hidden' name='greparam'>
                <input type='hidden' name='greroute'>

		<div class="box" data-box="gretunnel">
                <div class='heading'><script type="text/javascript">document.write($lang.GRE_TUNNEL_SETTINGS)</script></div>
                <div class='section content'>
                	<table class='line-table' cellspacing=1 id='gre-gird'></table>
                    	<script type='text/javascript'>gre.setup(); init_gre();</script>
                </div>
		</div>

		<div class="box" data-box="greroute">
                <div class='heading'><script type="text/javascript">document.write($lang.GRE_ROUTE_SETTINGS)</script></div>
                <div class='section content'>
                    <table class='line-table' cellspacing=1 id='grer-gird'></table>
                    <script type='text/javascript'>grer.setup(); init_grer();</script>
                </div>
		</div>
			</form>

	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><%translate("Save");%><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><%translate("Cancel");%><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span><br /><br />

<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
