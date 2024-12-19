	</div>
</div>
<!-- <script type='text/javascript' src='js/uiinfo.js'></script> -->
<script type="text/javascript">
$(document).ready(function(){
	if(nvram.router_2){
		var arr = nvram.router_2.split('<');
		if (arr[3] == 1) {
			$("#wlsz").css('display', 'none');
		}
		if (arr[4] == 1) {
			$("#wwl").css('display', 'none');
		}
		if (arr[5] == 'dd') {
			$("#ydwl2").css('display', 'list-item');
		}
		if (arr[6] == 'n') {
			$("#gpssz").css('display', 'none');
		}
		if (nvram.term_model) {
			// nvram.term_model = 'G20';
			var type = nvram.term_model.substr(0,3);

			var arr1 = ['R10','R20','R50','R12','G20','G50','CM2','CM5'];
			var arr2 = ['R23','R21','R51','G51','G92'];
			var arr3 = ['R10','R20','R50','G20','G50','CM5'];
			var arr4 = ['R12','R23','R21','R51','G51'];
			// console.log(type);
			if ($.inArray(type, arr1) != -1) {
				$("#gjpz").css('display', 'none');
			}else if ($.inArray(type, arr2) != -1) {
				if (arr[7] == 0) {
					$("#gjpz").css('display', 'none');
				}
			}

			if (($.inArray(type, arr3) != -1 && arr[6] != 'e') || ($.inArray(type, arr4) != -1 && arr[6] == 'e')) {
				$("#ckyy").css('display', 'list-item');
			}
			if (($.inArray(type, arr4) != -1 && arr[6] != 'e') || type == 'G92' || nvram.term_model == 'ROUTER') {
				$("#ckyy2").css('display', 'list-item');
			}

			if (type == 'R10') {
				$("#wwl").css('display', 'none');
			}
		}
	}
 	// 读取配置menu_cfg.php，强制显示或隐藏某些页面
	if (menu_cfg) {
		for (var i=0; i<menu_cfg.show.length; i++) {
			$('#'+menu_cfg.show[i]).css('display', 'list-item');
		}
		for (var j=0; j<menu_cfg.hide.length; j++) {
			$('#'+menu_cfg.hide[j]).css('display', 'none');
		}
	}

	$('.menu_1').click(function() {
		if($(this).hasClass("active")){
			$(this).removeClass("active");
		}else{
			$(this).addClass("active").siblings().removeClass("active");
		}
	})
	$('.menu_1').removeClass('active');
	$('.menu_2').removeClass('active');
	var u = location.href.split('/');
	window.current = u[u.length - 1].replace('#', '');
	var a = $('a[href='+ '"./'+ window.current +'"'+']');
	if (a.parent().css('display') == 'none') {
		a.parent().next().addClass('active');
	} else {
		a.parent().addClass('active');
	}
	a.parent().parent().parent().addClass('active');
});
</script>
</body>
</html>