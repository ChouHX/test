(function($){
$(document).ready(function(){
    Highcharts.setOptions($.gf.highChartsOptions); //highcharts通用配置
    $.gf.month_arr = $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(',');
    $('.btn-sensor-custom').click(function(){
        var name = $(this).parent().parent().attr('id');
        var params = 'sns='+$.gf.gateway_sn+'&type=rtuDataSend'+'&task_name='+$lang.DATA_SEND+'&slave_id_addr='+name;
        $('#rwcsModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Term','rwcs',params)
        });
    });

    $.gf.projectEdit = function(sn){
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=project_edit_rtu&sn='+sn)
        });
    }

    $.gf.sensor_edit = function(id){
        $('#myLgModal2').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=threshold_edit&id='+id+'&sn='+$.gf.gateway_sn)
        });
    }

    //模态框关闭时清除内容
    $("#myLgModal,#rwcsModal,#myLgModal2").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });

	//Map
    window.map_init = function(){
        $.getScript($.gf.lang == 'zh-cn' ? $.gf.gaode_ui:$.gf.empty_js, function(){
            $.gf.lssj_map = map_create('map_container', 2);
            $.gf.infoWindow = map_get_infowindow();
            for (var i=0,j=0, point=null, marker=null; i<$.gf.gps.length; i++){
                if (!$.gf.gps[i].latitude || !$.gf.gps[i].longitude){
                    continue;
                }
                marker = map_add_marker($.gf.gps[i].longitude, $.gf.gps[i].latitude);
                marker.pic = $.gf.gps[i];
                map_add_event(marker, 'click', function(e, that){
                    var c ='<table class="table-condensed infoWindowTable" style="font-size:12px">\
                        <tr><td width="70px">'+$lang.VAR_SN2+':</td><td>'+that.pic.sn+'</td></tr>\
                        <tr><td width="70px">'+$lang.VAR_DEVICE_URL_REPORT_TIME+':</td><td>'+that.pic.report_time+'</td></tr>\
                    </table>';
                    $.gf.infoWindow.setContent(c);
                    $.gf.infoWindow.setPosition(map_get_lnglat(that.pic.longitude, that.pic.latitude));
                    $.gf.infoWindow.open($.gf.map);
                });
                if (j++ == 0){
                    map_set_zoom(8);
                    map_set_center($.gf.gps[i].longitude, $.gf.gps[i].latitude);
                    $.gf.lssj_marker = marker;
                }
            }
        });
    };
    mapjs_load();

	for (var i=0; i<$.gf.sensors.length; i++){
		$('#dt_'+$.gf.sensors[i].id).daterangepicker({
			"locale": {
				"format": "YYYY-MM-DD",
				"daysOfWeek": $lang.VAR_WEEK_ARR,
				"monthNames": $.gf.month_arr
			},
			drops: 'down',
			ranges: $.gf.ranges,
			alwaysShowCalendars: true,
			showCustomRangeLabel: false,
			startDate: moment(),
			endDate: moment(),
			autoApply: true
		});
	  }
	$('.form-control').on('change',function(){
			var id = $(this).attr('id').split('_')[1];
			var slave_addr = $(this).parent().parent().attr('id').split('_');
			var slave_id = slave_addr[0],addr = slave_addr[1];
			$.gf.refresh_charts(id,slave_id,addr);
		 });

	$.gf.download_excel = function(k){
        if (typeof $.gf.excel_data[k] == 'undefined' || $.gf.excel_data[k].length == 0){
            $.notice(1, $lang.EXT_PAGING_1);
            return;
        }
        for (var i=0,len=$.gf.excel_data[k].length; i<len; i++){
            $.gf.excel_data[k][i][0] = new Date($.gf.excel_data[k][i][0]).format('yyyy-MM-dd hh:mm:ss');
            $.gf.excel_data[k][i][1] = new Date($.gf.excel_data[k][i][1]).format('yyyy-MM-dd hh:mm:ss');
        }
        var d = {
            body: $.gf.excel_data[k],
            filename: $('#name_'+k).html()+' ('+$('#dt_'+k).val()+')',
            footer: null,
            header: [$lang.VAR_DEVICE_URL_REPORT_TIME, $lang.CATCH_TIME, $lang.VAR_SYSCFG_VALUE],
            type: 'rtu_history_data'
        }
        generateExcel(d);
    };

    //is_init = true表示图表初始化时首次获取chart数据，此时不应创建grid
	$.gf.refresh_charts = function(i, slave_id, addr, is_init){
		$('#history_data_chart_'+i).highcharts().showLoading();
		var dates = $('#dt_'+i).val().split(' - '), name = $('#name_'+i).html();
		var start = new Date(dates[0].replace('-','/') +' 00:00:00').getTime(), end = new Date(dates[1].replace('-','/') +' 23:59:59').getTime();
		ajax(tpurl('Rtu','getSensorHistoryData'),{
			start:start/1000,
			end:end/1000,
			sn:$.gf.gateway_sn,
			slave_id:slave_id,
			addr:addr
		},function(msg){
			$.gf.excel_data[i] = msg;
			var data = [], jqgridData = [];
			$('#history_data_chart_'+i).highcharts().subtitle.update({text:name+'：'+dates[0]+' ~ '+dates[1]});
            $('#history_data_list_'+i).jqGrid('clearGridData');
            for (var k=0; k<msg.length; k++){
                data.push([msg[k][0], msg[k][2]]);
                jqgridData.push({
                    id:k+1,
                    report_time: new Date(msg[k][0]).toYmdhis(),
                    catch_time: new Date(msg[k][1]).toYmdhis(),
                    value: msg[k][2]
                });
            }
            $.gf.charts[i].jqgridData = jqgridData;
			$('#history_data_chart_'+i).highcharts().hideLoading();
            $('#history_data_chart_'+i).highcharts().series[0].setData(data);
            if (typeof is_init == 'undefined'){
                $.gf.refresh_grids(i);
            }
		})
	}

    $.gf.charts = [];
    $.gf.excel_data = [];
	$.gf.init_charts = function(id, name){
        var h = $('#header_'+id);
        if (h.attr('data-init') != '0') return;
        // console.log(id, name);
        h.attr('data-init', '1').hide().next().show();
        $.gf.charts[id] = {};
		$.gf.charts[id].chart = new Highcharts.StockChart({
            chart: {
				zoomType: 'x',
                renderTo: 'history_data_chart_'+id
            },
			rangeSelector: {enabled: false},
			navigator: {
				enabled: true,
				maskFill: 'rgba(180, 198, 220, 0.3)'
			},
			lang: {
				noData: $lang.EXT_PAGING_1 //真正显示的文本
			},
			loading:{
				hideDuration:10
			},
			boost: {
				useGPUTranslations: true
			},
			credits:{text:'', href:''},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				type: 'datetime'
			},
			tooltip: {
				valueDecimals: 2
			},
			yAxis: {
				title: {
					text: ''
				},
				opposite: false,
				lineWidth: 1
			},
			legend: {
				enabled: false
			},
            series : [{
				type : 'area',
                name : name,
				color: ['#00c0ef'],//00c0ef
                tooltip: {
                    valueDecimals: 2
                },
                data: [],
				fillColor: {
					linearGradient: {
						x1: 0,
						y1: 0,
						x2: 0,
						y2: 1
					},
					stops: [
						[0, Highcharts.getOptions().colors[4]],
						[1, Highcharts.Color(Highcharts.getOptions().colors[4]).setOpacity(0.25).get('rgba')]
					]
				},
				threshold: null
            }]
        });
	}

    $.gf.refresh_grids = function(k){
        if ($('#history_data_grid_'+k).attr('data-init') == '0'){
            $('#history_data_grid_'+k).attr('data-init', '1');
            $('#history_data_list_'+k).jqGrid({
                datatype: 'local',
                data: [],
                colNames: ['id', $lang.VAR_DEVICE_URL_REPORT_TIME, $lang.CATCH_TIME, $lang.VAR_SYSCFG_VALUE],
                colModel:[
                    {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                    {name:'report_time',  index:'report_time',  jsonmap:'report_time', width:150,  align:'center', hidden:false, search:false},
                    {name:'catch_time',   index:'catch_time',   jsonmap:'catch_time',  width:150,  align:'center', hidden:false, search:false},
                    {name:'value',        index:'value',        jsonmap:'value',       width:100,  align:'center', hidden:false, search:false}
                ],
                shrinkToFit: true,
                width: $('div[id^=history_data_chart_]:eq(0)').width(),
                height: 160,
                rownumbers: true,
                rownumWidth: 50,
                sortname: 'report_time',
                sortorder: 'DESC',
                pager: '#history_data_pager_'+k,
                rowNum: 5,
                rowList: [5, 10, 20, 30, 40, 50, 100],
                page: 1,
                pagerpos: 'center',
                pgbuttons: true,
                pginput: true,
                viewrecords: true
            });
        }
        $('#history_data_list_'+k).setGridParam({
            data: $.gf.charts[k].jqgridData
        }).trigger('reloadGrid');
    };

	$('.clearfix ul li a').on('click',function(){
		var key = $(this).parent().parent().parent().next().attr('id').split('_'), id = key[3];
        if ($(this).attr('data-link') == 'chart'){
            $('#history_data_chart_'+id).show().next().hide();
        } else if($(this).attr('data-link') == 'list'){
            $('#history_data_grid_'+id).show().prev().hide();
            $.gf.refresh_grids(id);
        }
        $.gf.reset_cmp_width();
    });

	for (var i=0; i<$.gf.sensors.length; i++){
        if (i >= 3) break;
        $.gf.init_charts($.gf.sensors[i].id, $.gf.sensors[i].name);
		if (typeof $.gf.sensors[i].value != 'undefined' && $.gf.sensors[i].report_time >= $.gf.today){
			$.gf.refresh_charts($.gf.sensors[i].id, $.gf.sensors[i].slave_id, $.gf.sensors[i].addr, true);
		}
	}

    var $win = $(window);
    $win.scroll(function () {
        for (var i=3, h=null; i<$.gf.sensors.length; i++){
            h = $('#header_'+$.gf.sensors[i].id);
            if (h.attr('data-init') != '0') continue;
            var itemOffsetTop = h.offset().top;
            var itemOuterHeight = h.outerHeight();
            var winHeight = $win.height();
            var winScrollTop = $win.scrollTop();
            if(!(winScrollTop > itemOffsetTop+itemOuterHeight) && !(winScrollTop < itemOffsetTop-winHeight)) {
                $.gf.init_charts($.gf.sensors[i].id, $.gf.sensors[i].name);
                if (typeof $.gf.sensors[i].value != 'undefined' && $.gf.sensors[i].report_time >= $.gf.today){
                    $.gf.refresh_charts($.gf.sensors[i].id, $.gf.sensors[i].slave_id, $.gf.sensors[i].addr, true);
                }
            }
        }
    });

    $.gf.reset_cmp_width = function(){
        for (var x in $.gf.charts){
            if (typeof $.gf.charts[x].chart != 'undefined'){
                $.gf.charts[x].chart.reflow();
            }
        }
        var w = $('div[id^=history_data_chart_]:eq(0)').width();
        for (var i=0, id=''; i<$.gf.sensors.length; i++){
            id = $.gf.sensors[i].id;
            if ($('#history_data_grid_'+id).attr('data-init') == '1'){
                $('#history_data_list_'+id).jqGrid('setGridWidth', w);
            }
        }
    };

    // 设置jqgrid的宽度
    $(window).bind('resize', function(){
        setTimeout(function(){
            $.gf.reset_cmp_width();
        }, 300);
    });
    $('a.sidebar-toggle').click(function(){
        setTimeout(function(){
            $.gf.reset_cmp_width();
        }, 300);
    });
});
})(jQuery);