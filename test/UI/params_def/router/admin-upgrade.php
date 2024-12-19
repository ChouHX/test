<?PHP include 'header.php'; ?>
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
		// <% nvram("jffs2_on"); %>

		function clock()
		{
			var t = ((new Date()).getTime() - startTime) / 1000;
			elem.setInnerHTML('afu-time', Math.floor(t / 60) + ':' + Number(Math.floor(t % 60)).pad(2));
		}
		function upgrade() {
			var name;
			var i;
			var fom = document.form_upgrade;
			var ext;
			name = fixFile(fom.file.value);

			if (name.search(/\.(bin|trx|chk)$/i) == -1) {
				alert('<% translate("Expecting a"); %> ".bin" <% translate("or"); %> ".trx" <% translate("file"); %>.');
				return false;
			}

			if (!confirm('<% translate("Are you sure you want to upgrade using"); %> ' + name + '?')) return;
			E('afu-upgrade-button').disabled = true;

			// Some cool things
			$('#wrapper > .content').css('position', 'static');
			$('#afu-progress').clone().prependTo('#wrapper').show().addClass('active');
			startTime = (new Date()).getTime();
			setInterval('clock()', 500);

			fom.action += '?_reset=' + (E('f_reset').checked ? "1" : "0");
			form.addIdAction(fom);
			fom.submit();
		}
	</script>
	<div id="afu-input">
		<form name="form_upgrade" method="post" action="upgrade.cgi" encType="multipart/form-data">

			<div class="box">
				<div class="heading"><% translate("Upgrade Firmware"); %></div>
				<div class="content">

						<div ><% translate("Select the file to use"); %>:</div>
						<div class="col-sm-9"><input class="uploadfile" type="file" name="file" size="50">
							<button type="button" value="Upgrade" id="afu-upgrade-button" onclick="upgrade();" class="btn btn-danger"><% translate("Upgrade"); %><i class="icon-upload"></i></button>
						</div>
						<div class="col-sm-9">
							<div id="reset-input">
								<div class="checkbox c-checkbox"><label><input class="custom" type="checkbox" checked id="f_reset">
									<span class="icon-check"></span> &nbsp; <% translate("After flashing, erase all data in NVRAM memory"); %></label>
								</div>
							</div>
						</div>
				

				</div>
			</div>

			<div class="box">
				<div class="content">
					<table class="line-table" id="version-table">
						<tr><td><% translate("Current Version"); %>:</td><td>&nbsp; <% version(1); %></td></tr>
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
	<script type="text/javascript">
/*		//	<% sysinfo(); %>
		$('#version-table').append('<tr><td><% translate("Free Memory"); %>:</td><td>&nbsp; ' + scaleSize(sysinfo.totalfreeram) + ' &nbsp; <small>(<% translate("aprox. size that can be buffered completely in RAM"); %>)</small></td></tr>');
		E('f_reset').checked = nvram.jffs2_on;
		if (nvram.jffs2_on != '0') {
			E('jwarn').style.display = '';
			E('afu-input').style.display = 'none';
		}*/
	</script>
    <!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<?PHP include 'footer.php'; ?>
