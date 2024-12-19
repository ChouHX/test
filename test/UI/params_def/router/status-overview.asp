<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="robots" content="noindex,nofollow">
		<title><% ident(); %> <%translate("Home");%></title>
		
		<!-- Interface Design -->
		<link href="Detran.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="iconfont.css">
		<% css(); %>

		<!-- Load Favicon (icon) -->
		<link rel="shortcut icon" href="favicon.ico">

		<!-- One time load JAVASCRIPT -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/tomato.js"></script>
		<script type="text/javascript" src="js/advancedtomato.js"></script>

		<!-- Variables which we keep through whole GUI, also determine Tomato version here -->
		<script type="text/javascript">

			var wl_ifaces = {};
			var routerName = '[<% ident(); %>] ';
	//<% nvram("at_nav,at_nav_action,at_nav_state,at_update,tomatoanon_answer"); %>
	//<% anonupdate(); %>
			// AdvancedTomato related object
			var gui = {
				'ajax_state'   : false,
				'nav_delay'    : null,
				'nav_action'   : ( ( typeof(nvram.at_nav_action) != 'undefined' && nvram.at_nav_action == 'hover' ) ? 'mouseover' : 'click' ),
				'refresh_timer': null,
				'version'      : "<% version(1); %>",
			};

			// On DOM Ready, parse GUI version and create navigation
			$( document ).ready( function() {

				// Attempt match
				match_regex = gui.version.match( /^1\.28\.0000.*?([0-9]{1,3}\.[0-9]{1}\-[0-9]{3}).* ([a-z0-9\-]+)$/i );

				// Check matches
				if ( match_regex == null || match_regex[ 1 ] == null ) {

					gui.version = '<%translate("More Info");%>'

				} else {

					gui.version = 'v' + match_regex[ 1 ] + ' ' + match_regex[ 2 ];

				}

				// Write version & initiate GUI functions & binds
				$( '#gui-version' ).html( '<i class="icon-info-alt"></i> <span class="nav-collapse-hide">' + gui.version + '</span>' );
				
				AdvancedTomato();
				E('hwreset').style.display = (bi.mcu == '0') ? "" : "none";
			});

		</script>
	</head>
	<body>
		<div id="wrapper">

			<div class="top-header">

				<a href="/">
					<div class="logo" >
						<a href="#" id="icon-home" style="display:none;">
						<i class="icon-home"></i>
						</a>
					</div>
				</a>

				<div class="left-container">
					<a title="<%translate("Toggle Collapsed Navigation");%>" href="#" class="toggle-nav"><i class="icon-toggle-nav"></i></a>
				</div>

				<div class="pull-right links">
					<ul>
						<li><a title="<%translate("Tools");%>"  href="#tools-ping.asp"><%translate("Tools");%> <i class="icon-tools"></i></a></li>
						<li><a title="<%translate("Bandwidth");%>" href="#bwm-realtime.asp"><%translate("Bandwidth");%> <i class="icon-graphs"></i></a></li>
						<li><a title="<%translate("IP Traffic");%>" href="#bwm-ipt-realtime.asp"><%translate("IP Traffic");%><i class="icon-traffic"></i></a></li>
						<li><a title="<%translate("System");%> " id="system-ui" href="#system"><%translate("System");%> <i class="icon-system"></i></a></li>
					</ul>
					<div class="system-ui">

						<div class="datasystem align center"></div>
						<div class="router-control">
							<a href="#" class="btn btn-primary" onclick="reboot();"><%translate("Reboot");%> <i class="icon-reboot"></i></a>
							<a href="#" class="btn btn-primary" onclick="hwreboot();" id='hwreset'><%translate("Hardware Reboot");%> <i class="icon-power"></i></a>
							<!--<a href="#" class="btn btn-danger" onclick="shutdown();"><%translate("Shutdown");%> <i class="icon-power"></i></a>-->
							<a href="#" class="btn btn-primary" onclick="logout();" class="btn"><%translate("Logout");%> <i class="icon-logout"></i></a>
						</div>
					</div>
				</div>
			</div>

			<div class="navigation">
				<ul>
					<li class="nav-footer" id="gui-version" style="cursor: pointer;" onclick="loadPage('');"></li>
				</ul>
			</div>
			<div class="container">
				<div style="text-align:center;margin-bottom: 15px;"><% rboard(); %></div>
				<div class="ajaxwrap"></div>
				<div class="clearfix"></div>
			</div>

		</div>
	</body>
</html>
