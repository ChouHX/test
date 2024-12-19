<?PHP include 'header.php'; ?>
<script type="text/javascript">
//	<% nvram(""); %>
//<% certinfo(); %>

function certkeydown(key)
{
	location.href = 'cert/' + ((key == 1)?'key':'cert') + '.pem?_nextpage=/%23admin-cert.asp&_http_id=' + nvram.http_id;
}

function certgen()
{
	location.href = 'cert/gen.cgi?_nextpage=/%23admin-cert.asp'+'&https_crt_cn='+E('_cert_cn').value+'&_http_id=' + nvram.http_id;
}

function importButton()
{
	var name, i, f;
	name = fixFile(E('import-name').value);
	name = name.toLowerCase();
	if(name.indexOf('.pem') != (name.length - 4))
	{
		alert('<% translate("Incorrect filename pem"); %>');
		return;
	}
	E('import-button').disabled = 1;
	f = E('import-form');
	f.action += '?_nextpage=/%23admin-cert.asp';
	form.addIdAction(f);
	f.submit();
}

</script>
<div class="box">
<div class="heading"></div>
<div class="content">

<h4><% translate("Generate Certificate"); %></h4>
<div class='section'>
<form>
<fieldset>
<label class="col-sm-3 control-left-label" for="_cert_cn"><% translate("Common Name"); %> (CN)</label>
<div class="col-sm-9">
<input type="text" name="cert_cn" value="" maxlength="64" size="64" id="_cert_cn">
</div>
</fieldset>
<input type='button' name='f_cert_gen' onclick='certgen()' class="btn btn-primary" value='<% translate("Generate"); %>'><br>
</form>
</div>
<br/><hr>

<h4><% translate("Import Certificate"); %></h4>
<div class="section">
<form id="import-form" method="post" action="cert/import.cgi" encType="multipart/form-data">
<input type="hidden" name="_nextpage" value="/#admin-cert.asp">
<% translate("Certificate"); %> + <% translate("Private Key"); %> (<% translate("PEM Format"); %>):<br>
<input class="uploadfile" type="file" size="40" id="import-name" name="filename">
<button type="button" name="f_import_button" id="import-button" value="Import" onclick="importButton()" class="btn btn-primary"><% translate("Import"); %><i class="icon-upload"></i></button>
</form>
</div>
<hr>

<h4><% translate("Download Certificate"); %></h4>
<div class="section" id="down">
<!-- <button name="f_cert_button" onclick="certkeydown(0)" id='cert_button' class="btn btn-primary"><% translate("Certificate"); %><i class="icon-download"></i></button> -->
<!-- <button name="f_key_button" onclick="certkeydown(1)" id='key_button' class="btn btn-primary"><% translate("Private Key"); %><i class="icon-download"></i></button> -->
<script type='text/javascript'>
E('key_button').disabled = (certinfo.key != 1);
E('cert_button').disabled = (certinfo.cert != 1);
</script>
</div>
<br/><hr>
</div>
</div>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>

