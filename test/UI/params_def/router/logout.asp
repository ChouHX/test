<html lang="en">
	<!--
	-->
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<meta name="robots" content="noindex,nofollow">
		<meta name="viewport" content="width=820">
		<style>
			body {
				background: #f0f0f0;
				color: #585858;
				font-family: Verdana;
				font-size: 13px;
			}

			#loader {
				width:100%;
				max-width: 450px;
				text-align: center;
				background: #fff;
				border: 1px solid #E1E1E1;
				margin: 15% auto;
				padding: 20px;
				border-width: 1px;
				border-style: solid;
				border-color: #EEE #EEE #D1D2D3;
				background: none repeat scroll 0% 0% #FFF;
				border-radius: 4px;
				-webkit-border-radius: 4px;
				box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.05);
				-webkit-box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.05);
			}
		</style>
	
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script>
	function mysub(){
	    var u = location.href.replace('logout.asp', '');
	    $.ajax({   
	        url: u+'logout',
	        data: {_http_id:'<% nv(http_id); %>'},  
	        username: 'xx',         
	        'password': 'xx',       
	        complete: function(msg){
	            location.href = u;
	        }
	    });  
	}        
	</script>
	</head>

	<body onload='setTimeout("mysub()", 500)'>

		<div id="loader">
			<h3 style="font-size: 16px; text-align: left; color: #353535; padding: 0; margin: 0 0 2px;"><% translate("You have Logout successfully"); %></h3>
			<div style="text-align: left;">
				<br>
				
			</div>
		</div>

		<form name="go" method="post" action="logout">
			<input type="hidden" name="_http_id" value="<% nv(http_id); %>">
		</form>
	</body>
</html>
