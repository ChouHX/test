<!--
--><title><% translate("Administration"); %>: <% translate("MCU Upgrade"); %></title>
<content>
	<style>
		#afu-progress {
			display: block;
			position: fixed;
			top: 0;
			right: 0;
			left: 0;
			bottom: 0;
			z-index: 20;
			background: #fff;
			color: #5A5A5A;
			opacity: 0;
			transition: opacity 250ms ease-out;
		}

		#afu-progress .text-container {
			position: absolute;
			display: block;
			text-align: center;
			font-size: 14px;
			width: 100%;
			height: 150px;
			top: 30%;
			margin-top: -75px;
			transform: scale(0.2);
			transition: all 350ms ease-out;
		}

		#afu-progress.active {
			opacity: 1;
		}

		#afu-progress.active .text-container {
			transform: scale(1);
			top: 40%;
		}
		
		.line-table tr { background: transparent !important; }
		.line-table tr:last-child { border: 0; }
	</style>
	<script type="text/javascript">

		function clock()
		{
			var t = ((new Date()).getTime() - startTime) / 1000;
			elem.setInnerHTML('afu-time', Math.floor(t / 60) + ':' + Number(Math.floor(t % 60)).pad(2));
		}
		function upgrade() {
			var name;
			var i;
			var fom = document.form_mcuupgrade;
			var ext;
			name = fixFile(fom.file.value);

			if (name.search(/\.(bin)$/i) == -1) {
				alert('<% translate("Expecting a"); %> ".bin" <% translate("file"); %>.');
				return false;
			}

			if (!confirm('<% translate("Are you sure you want to upgrade using"); %> ' + name + '?')) return;
			E('afu-upgrade-button').disabled = true;

			// Some cool things
			$('#wrapper > .content').css('position', 'static');
			$('#afu-progress').clone().prependTo('#wrapper').show().addClass('active');
			startTime = (new Date()).getTime();
			setInterval('clock()', 500);

			form.addIdAction(fom);
			fom.submit();
		}
	</script>
	<div id="afu-input">
		<form name="form_mcuupgrade" method="post" action="mcuupgrade.cgi" encType="multipart/form-data">

			<div class="box">
				<div class="heading"><% translate("Upgrade Firmware"); %></div>
				<div class="content">

					<div ><% translate("Select the file to use"); %>:</div>
					<div class="col-sm-9"><input class="uploadfile" type="file" name="file" size="50">
						<button type="button" value="Upgrade" id="afu-upgrade-button" onclick="upgrade();" class="btn btn-danger"><% translate("Upgrade"); %><i class="icon-upload"></i></button>
					</div>

				</div>
			</div>

			<div class="box">
				<div class="content">
					<table class="line-table" id="version-table">
						<tr><td><% translate("Current Version"); %>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <% version(4); %></td></tr>
					</table>
				</div>
			</div>

			<div id="afu-progress" style="display:none;">
				<div class="text-container">
					<div class="spinner spinner-large"></div><br /><br />
					<b id="afu-time">0:00</b><br />
					<% translate("Please wait while the firmware is uploaded &amp; flashed"); %>.<br>
					<b><% translate("Warning"); %>:</b> <% translate("Do not interrupt this browser or the router"); %>!<br>
				</div>
			</div>
		</form>
	</div>
<!--
	/* JFFS2-BEGIN */
	<div class="alert alert-error" style="display:none;" id="jwarn">
		<h5>Upgrade forbidden!</h5>
		An upgrade may overwrite the JFFS partition currently in use. Before upgrading,
		please backup the contents of the JFFS partition, disable it, then reboot the router.
		<a href="/#admin-jffs2.asp">Disable &raquo;</a>
	</div>
 -->
    <script type='text/javascript' src='js/uiinfo.js'></script>
</content>
