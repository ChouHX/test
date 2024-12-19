<?PHP include 'header.php'; ?>
<script type='text/javascript' src='js/interfaces.js'></script>

<!-- <script type='text/javascript' src='debug.js'></script> -->
<style type='text/css'>
#ara-grid .co1, #ara-grid .co2, #ara-grid .co3 {
	width: 20%;
}
#ara-grid .co4 {
	width: 6%;
}
#ara-grid .co5 {
	width: 34%;
}

tr.odd:hover
{
	background-color:#51aded;
}
tr.even:hover
{
background-color:#51aded;
}
</style>
	<script type='text/javascript'>
// <% nvram("storage_udisk"); %>
// <% filelist("mp4"); %>
// <% statfs("/jffs", "jffs2"); %>
// <% statfs_udisk("udisk"); %>

var filelist = nvram.filelist || [];
var jffs2 = nvram.jffs2 || [];

function del_file(file)
{
	form.submitHidden('delfile.cgi', { _redirect: '/#admin-filelist.asp', _filename: file });
}

function download_file(file)
{
	form.submitHidden('downfile.cgi', { _filename: file });
}

function show_list()
{
	var i,a;
	var	htmlOut;
	for (var i = 0; i < filelist.length; ++i)
	{
		a=filelist[i];

		//htmlOut +="</tr>"
		
		if(i%2==0)
		{
			htmlOut += "<tr class='even' bgcolor='#FFFFFF'>";
		}
		else
		{
			htmlOut += "<tr class='odd'>";
		}
		htmlOut += "<td width='50%'>"+ a[0] +"</td>";
		htmlOut += "<td width='30%'>"+ a[1] +"</td>";
		htmlOut += "<td width='20%'><img src='rpx.gif' onclick="+"del_file('"+a[0]+"') title='"+ $lang.VAR_DEL +"'>&nbsp;<img src='rpd.gif' onclick="+"download_file('"+a[0]+"') title='"+ $lang.VAR_CP_DOWNLOAD +"'></td>";
		htmlOut += "</tr>";
	}
	htmlOut += "<tr><td>&nbsp;</td></tr>";
	
	$('#file-list').append(htmlOut);
}
function verifyFields(focused, quiet)
{

	var fom = E('_fom');

	if(fom.storage_udisk.value==1)
	{
		E('storage_size').innerHTML	=(((udisk.mnt) || (udisk.size > 0)) ? $lang.TOTAL + " :"+scaleSize(udisk.size) : '') + ((udisk.mnt) ? $lang.FREE + ":"+scaleSize(udisk.free) : ' ('+ $lang.NOT_MOUNTED +')');
	}
	else
	{
		E('storage_size').innerHTML	=(((jffs2.mnt) || (jffs2.size > 0)) ? $lang.TOTAL + " :"+scaleSize(jffs2.size) : '') + ((jffs2.mnt) ? $lang.FREE + ":"+scaleSize(jffs2.free) : ' ('+ $lang.NOT_MOUNTED +')');
	}
	return 1;
}

function upload()
{
	var name;
	var fom = document.form_upload;

	E('afu-upload-button').disabled = true;

	name = fixFile(fom.file.value);
	
	fom.action += '?_nextpage=/%23admin-filelist.asp&_nextwait=1&filename='+name;//URL特殊字符#有其他意义，用对应的编码替换
	form.addIdAction(fom);
	fom.submit();
}

function earlyInit()
{
}

function save()
{
	var fom = E('_fom');
	// form.submit(fom, 0);
	return submit_form('_fom');
}
	</script>
	
	<div class="box" data-box="storage">
		<div class="heading"><script type="text/javascript">document.write($lang.STORAGE_MANAGE)</script></div>
		<div class="content content" >
			<form id="_fom" method="post" action="tomato.cgi">
				<input type='hidden' name='_nextpage' value='/#admin-filelist.asp'>
				<input type='hidden' name='_nextwait' value='5'>
				<input type='hidden' name='_service' value='nodog-restart'>
				<div class='section-title' id='cell-title'></div>

			</form>	
		</div>
	</div>

<!-- 	<div class="box" data-box="upload">
			<div class='heading' ><% translate("Upload new file"); %></div>

			<div class='section content'>
			<form name='form_upload' method='post' action='upload.cgi' encType='multipart/form-data'>
			
			<div id='box-input'>
			<input type='file' name='file' size='50'> <input type='button' value='<% translate("Upload"); %>' id='afu-upload-button' onclick='upload()'>
			</div>
			</form>
			</div>
	</div> -->

<!-- 	<div class="box" data-box="filelist">
			<div class='heading'><script type="text/javascript">document.write($lang.CURRENT_FILE_LIST)</script></div>
			<div class='section content'>
			<table width="100%" border="0" cellspacing="1" cellpadding="3" class="line-table" id="file-list">
			  <tr class="header">
				<td width="30%"  id="dHost"><script type="text/javascript">document.write($lang.VAR_PACKAGE_FILE_NAME)</script></td>
				<td width="30%"  id="dHost"><script type="text/javascript">document.write($lang.VAR_FILEMGR_FILESIZE)</script></td>
				<td width="20%"  id="dMac"><script type="text/javascript">document.write($lang.FILE_OPERATION)</script></td>
			  </tr>	
				<script language="JavaScript" type="text/javascript">
					show_list();
				</script>			  
			</table>
			</div>
	</div> -->


			<script type='text/javascript'>

				$('#cell-title').forms([
	{ title: $lang.STORAGE_UDISK, name: 'storage_udisk', type: 'select', options: [['0', $lang.ROUTER],['1', $lang.MOVING_MEDIA]],suffix: "<span id='storage_size'></span>",value: nvram.storage_udisk}], { align: 'left' });
			</script>
            
	<!-- <button type="button" value="Save" id="save-button" onclick="save()" class="btn btn-primary"><% translate("Save"); %><i class="icon-check"></i></button> -->
	<!-- <button type="button" value="Cancel" id="cancel-button" onclick="javascript:reloadPage();" class="btn btn-primary"><% translate("Cancel"); %><i class="icon-cancel"></i></button> -->
	<span id="footer-msg" class="alert alert-warning" style="visibility: hidden;"></span>

	<script type="text/javascript">verifyFields(null, 1);</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
