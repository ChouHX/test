(function($){
    $.gf.reset_cmp_width = function(){
        var index = $('#tab_info_types li.active').index()+1;
        jqgrid_set_width($('#list2,#list22,#list3,#list33,#list5,#list55'), $('.info-tab'+index+':eq(0) .box-body'), index==3?-17:0);
        if (index == 4){
            for (var x in charts){
                charts[x].reflow();
            }
        }
    };
    var month_arr = $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(',');
    var charts = [];
    var options = {
        chart: {
            type: 'spline'
        },
        credits:{text:'', href:''},
        title: {text: ''},
        xAxis: {
            crosshair: true,
            type: 'category'
        },
        yAxis: {
            min: 0,
            title: {text: ''},
            allowDecimals: true
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        legend: {enabled:false},
        colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
        series: [{
            name: $lang.NUMBER,
            data: []
        }]
    };
    var options_pie = {
        credits:{text:'', href:''},
        title: {text: ''},
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        tooltip: {
            headerFormat: '{series.name}<br>',
            pointFormat: '{point.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: $lang.PROPORTION_OF,
            data: []
        }]
    };
    $.extend(options, $.gf.highChartsOptions);
    $.extend(options_pie, $.gf.highChartsOptions);

    function reset_series(id, msg){
        var i, diff = msg.length - charts[id].series.length;
        if (diff > 0){
            for (i=0; i<diff; i++){
                charts[id].addSeries({},false);
            }
        } else if (diff < 0){
            for (i=charts[id].series.length; i>msg.length; i--){
                charts[id].series[i-1].remove(false);
            }
        } else if (msg.length == 0){
            for (i=charts[id].series.length; i>0; i--){
                charts[id].series[i-1].remove(false);
            }
        }
        if (msg.length > 1){
            charts[id].options.legend.enabled = true;
        }
        charts[id].update({series:msg},true);
    }

    function refresh_charts(start, end, id, by_month){
        $.gf.refresh_page = [start, end, id, by_month]; //保存上次参数，在点击refresh_page按钮时使用
        var params = {start:start, end:end, sn:$.gf.sn, sim:$('#slt_sims').val()};
        if (end - start <= 24*3600){
            params.by_hour = 1;
        }
        if (typeof by_month != 'undefined'){
            params.by_month = 1;
        }
        $('#chart_flux_unit').html(params.by_hour == 1 ? 'KB' : 'MB');

        if (typeof id == 'undefined' || id == 'chart_flux'){
            charts.chart_flux.showLoading();
            ajax(tpurl('Term','termChartDataFlux'),params,function(msg){
                charts.chart_flux.hideLoading();
                reset_series('chart_flux', msg);
            });
        }
        if (typeof id == 'undefined' || id == 'chart_signal'){
            charts.chart_signal.showLoading();
            setTimeout(function(){
                ajax(tpurl('Term','termChartDataSignal'),params,function(msg){
                    charts.chart_signal.hideLoading();
                    reset_series('chart_signal', msg);
                });
            }, 50);
        }
        if (typeof id == 'undefined' || id == 'chart_online'){
            charts.chart_online.showLoading();
            setTimeout(function(){
                ajax(tpurl('Term','termChartDataOnline'),params,function(msg){
                    charts.chart_online.hideLoading();
                    charts.chart_online.series[0].setData(msg);
                });
            }, 100);
        }
        if (typeof id == 'undefined' || id == 'chart_netmode'){
            charts.chart_netmode.showLoading();
            setTimeout(function(){
                ajax(tpurl('Term','termChartDataNetmode'),params,function(msg){
                    charts.chart_netmode.hideLoading();
                    charts.chart_netmode.series[0].setData(msg);
                });
            }, 150);
        }
        if (typeof id == 'undefined' || id == 'chart_CPU'){
            charts.chart_CPU.showLoading();
            setTimeout(function(){
                params.type = 'cpu';
                ajax(tpurl('Term','termChartDataUsageRate'),params,function(msg){
                    charts.chart_CPU.hideLoading();
                    charts.chart_CPU.series[0].setData(msg);
                });
            }, 200);
        }
        if (typeof id == 'undefined' || id == 'chart_MEM'){
            charts.chart_MEM.showLoading();
            setTimeout(function(){
                params.type = 'mem';
                ajax(tpurl('Term','termChartDataUsageRate'),params,function(msg){
                    charts.chart_MEM.hideLoading();
                    charts.chart_MEM.series[0].setData(msg);
                });
            }, 250);
        }
        if (typeof id == 'undefined' || id == 'chart_storage'){
            charts.chart_storage.showLoading();
            setTimeout(function(){
                params.type = 'storage';
                ajax(tpurl('Term','termChartDataUsageRate'),params,function(msg){
                    charts.chart_storage.hideLoading();
                    charts.chart_storage.series[0].setData(msg);
                });
            }, 300);
        }
    }

    $.gf.refresh_tab01 = function() {
        layer.load(0);
        ajax(tpurl('Term','sbxq'), {sn:$.gf.sn, ret_fence:1}, function(msg){
            layer.closeAll('loading');
            //更新基本信息
            for (var x in msg.data){
                if ($('#td_'+x).size() == 1){
                    $('#td_'+x).html(msg.data[x]);
                }
            }
            //更新地图位置
            if (typeof msg.info.fence == 'undefined') msg.info.fence = [];
            if (!$.gf.gm) {
                $.gf.gm = new GMap(msg.info);
                $.gf.gm.drawFence();
                $.gf.gm.initFence(msg.info.fence);
            } else {
                $.gf.gm.clearMap();
                $.gf.gm.moveTo(msg.info);
                $.gf.gm.initFence(msg.info.fence);
            }
        });
    }

    $.gf.setFence = function() {
        var datas = [];
        for (var x in $.gf.gm.circles) {
            datas.push({
                ftype: 1,
                radius: $.gf.gm.circles[x].getRadius(),
                lat: $.gf.gm.circles[x]._latlng.lat,
                lon: $.gf.gm.circles[x]._latlng.lng
            });
        }
        //矩形_latlngs从左下开始顺时针
        for (var x in $.gf.gm.rects) {
            var arr = $.gf.gm.rects[x]['_latlngs'];
            datas.push({
                ftype: 2,
                lat1: arr[0][1].lat,
                lon1: arr[0][1].lng,
                lat2: arr[0][3].lat,
                lon2: arr[0][3].lng
            });
        }
        for (var x in $.gf.gm.polygons) {
            datas.push({
                ftype:3,
                points: $.gf.gm.polygons[x]['_latlngs'][0]
            });
        }
        layer.load(0);
        ajax(tpurl('Term', 'setFence'), {sn:$.gf.sn, datas:JSON.stringify(datas)}, function(msg) {
            layer.closeAll('loading');
            $.notice(msg);
        });
    }

    $(document).ready(function(){
        // 统计报表-时间选择
        $('#start_dt').daterangepicker({
            "locale": {
                "format": "YYYY-MM-DD",
                "daysOfWeek": $lang.VAR_WEEK_ARR,
                "monthNames": month_arr
            },
            ranges: $.gf.ranges,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            startDate: moment(),
            endDate: moment(),
            autoApply: true
        }).on('apply.daterangepicker',function(ev,picker){
            var start = picker.startDate.startOf('day').unix(),
                end = picker.endDate.endOf('day').unix();
            $('.btn-time-range').removeClass('btn-info').addClass('btn-default');
            refresh_charts(start, end, undefined, (picker.chosenLabel == $lang.THIS_YEAR || picker.chosenLabel == $lang.LAST_YEAR ? 1 : undefined));
        });
        $('.btn-today').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment());
            o.setEndDate(moment());
            refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).next().removeClass('btn-info').addClass('btn-default').blur();
        });
        $('.btn-this-month').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment().startOf('month'));
            o.setEndDate(moment());
            refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).prev().removeClass('btn-info').addClass('btn-default').blur();
        });

        // 上线记录-时间选择
        $('#start_dt2').daterangepicker({
            "locale": {
                "format": "YYYY-MM-DD",
                "daysOfWeek": $lang.VAR_WEEK_ARR,
                "monthNames": month_arr
            },
            ranges: $.gf.ranges,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            startDate: moment(),
            endDate: moment(),
            autoApply: true,
            drops: 'up'
        }).on('apply.daterangepicker',function(ev,picker){
            var start = picker.startDate.startOf('day').unix(),
                end = picker.endDate.endOf('day').unix();
            $('.btn-time-range2').removeClass('btn-info').addClass('btn-default');
            $('#list2').setGridParam({page:1, postData:{sn:$.gf.sn, start:start, end:end}}).trigger('reloadGrid');
        });
        $('.btn-today2').click(function(){
            var o = $('#start_dt2').data('daterangepicker');
            o.setStartDate(moment());
            o.setEndDate(moment());
            $('#list2').setGridParam({page:1, postData:{sn:$.gf.sn, start:o.startDate.startOf('day').unix(), end:o.endDate.endOf('day').unix()}}).trigger('reloadGrid');
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).next().removeClass('btn-info').addClass('btn-default').blur();
        });
        $('.btn-this-month2').click(function(){
            var o = $('#start_dt2').data('daterangepicker');
            o.setStartDate(moment().startOf('month'));
            o.setEndDate(moment());
            $('#list2').setGridParam({page:1, postData:{sn:$.gf.sn, start:o.startDate.startOf('day').unix(), end:o.endDate.endOf('day').unix()}}).trigger('reloadGrid');
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).prev().removeClass('btn-info').addClass('btn-default').blur();
        });

        // 网络切换-时间选择
        $('#start_dt22').daterangepicker({
            "locale": {
                "format": "YYYY-MM-DD",
                "daysOfWeek": $lang.VAR_WEEK_ARR,
                "monthNames": month_arr
            },
            ranges: $.gf.ranges,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            startDate: moment(),
            endDate: moment(),
            autoApply: true,
            drops: 'up'
        }).on('apply.daterangepicker',function(ev,picker){
            var start = picker.startDate.startOf('day').unix(),
                end = picker.endDate.endOf('day').unix();
            $('.btn-time-range22').removeClass('btn-info').addClass('btn-default');
            $('#list22').setGridParam({page:1, postData:{sn:$.gf.sn, start:start, end:end}}).trigger('reloadGrid');
        });
        $('.btn-today22').click(function(){
            var o = $('#start_dt22').data('daterangepicker');
            o.setStartDate(moment());
            o.setEndDate(moment());
            $('#list22').setGridParam({page:1, postData:{sn:$.gf.sn, start:o.startDate.startOf('day').unix(), end:o.endDate.endOf('day').unix()}}).trigger('reloadGrid');
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).next().removeClass('btn-info').addClass('btn-default').blur();
        });
        $('.btn-this-month22').click(function(){
            var o = $('#start_dt22').data('daterangepicker');
            o.setStartDate(moment().startOf('month'));
            o.setEndDate(moment());
            $('#list22').setGridParam({page:1, postData:{sn:$.gf.sn, start:o.startDate.startOf('day').unix(), end:o.endDate.endOf('day').unix()}}).trigger('reloadGrid');
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).prev().removeClass('btn-info').addClass('btn-default').blur();
        });

        //图表
        charts.chart_flux = Highcharts.chart('chart_flux', options);
        charts.chart_signal = Highcharts.chart('chart_signal', options);

        options.series[0].name = $lang.VAR_ONLINE_RATES;
        options.yAxis.max = 100;
        charts.chart_online = Highcharts.chart('chart_online', options);
        charts.chart_netmode = Highcharts.chart('chart_netmode', options_pie);

        options.series[0].name = $lang.USAGE_RATE;
        charts.chart_CPU = Highcharts.chart('chart_CPU', options);
        charts.chart_MEM = Highcharts.chart('chart_MEM', options);
        charts.chart_storage = Highcharts.chart('chart_storage', options);

        //上线记录
        $('#list2').jqGrid({
            url: tpurl('Term','loadLoginRecordData'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', $lang.VAR_LOGIN_TIME, $lang.VAR_LOGOUT_TIME, $lang.VAR_TERM_SIGNAL, $lang.VAR_TERM_FLUX],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'login_time',   index:'login_time',   jsonmap:'login_time',  width:150,  align:'center', hidden:false, search:false},
                {name:'logout_time',  index:'logout_time',  jsonmap:'logout_time', width:150,  align:'center', hidden:false, search:false},
                {name:'term_signal',  index:'term_signal',  jsonmap:'term_signal', width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return v;
                }},
                {name:'flux',         index:'flux',         jsonmap:'flux',        width:100,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            postData: {
                sn: $.gf.sn,
                start:  moment().startOf('day').unix(),
                end:  moment().endOf('day').unix()
            },
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        //网络切换
        $('#list22').jqGrid({
            url: tpurl('Term','loadNetChangeRecordData'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', $lang.VAR_DEVICE_URL_REPORT_TIME, $lang.OLD_VALUE+'('+$lang.NET_MODE+')', $lang.NEW_VALUE+'('+$lang.NET_MODE+')', $lang.OLD_VALUE+'('+$lang.SIM_CARD_NUMBER+')', $lang.NEW_VALUE+'('+$lang.SIM_CARD_NUMBER+')'],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'report_time',  index:'report_time',  jsonmap:'report_time', width:150,  align:'center', hidden:false, search:false},
                {name:'old_value',    index:'old_value',    jsonmap:'old_value',   width:150,  align:'center', hidden:false, search:false},
                {name:'new_value',    index:'new_value',    jsonmap:'new_value',   width:100,  align:'center', hidden:false, search:false},
                {name:'old_sim',      index:'old_sim',      jsonmap:'old_sim',     width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    return $lang.CARD + ' ' + v;
                }},
                {name:'new_sim',      index:'new_sim',      jsonmap:'new_sim',     width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    return $lang.CARD + ' ' + v;
                }}
            ],
            pager: '#pager22',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'id',
            sortorder: 'desc',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            postData: {
                sn: $.gf.sn,
                start:  moment().startOf('day').unix(),
                end:  moment().endOf('day').unix()
            },
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        //任务列表
        $('#list3').jqGrid({
            url: tpurl('Term','loadTermTasks'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', 'task_id', $lang.VAR_CMD_NAME, $lang.IS_ENABLE, $lang.VAR_CMD_STATUS, $lang.VAR_TASK_LAST_TIME, $lang.VAR_TASK_SEND_TIME, $lang.VAR_TASK_FINISH_TIME, $lang.VAR_TASK_FINISH_TIME2, $lang.VAR_TIPS_DETAIL_INFO, $lang.VAR_OPERATION],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'task_id',      index:'task_id',      jsonmap:'task_id',     width:50,   align:'center', hidden:true,  search:false},
                {name:'task_name',    index:'cmd',          jsonmap:'task_name',   width:100,  align:'center', hidden:false, search:false},
                {name:'is_enable',    index:'is_enable',    jsonmap:'is_enable',   width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return v == '1' ? $lang.VAR_ENABLE : '<span style="color:red;">'+$lang.VAR_DISABLE+'</span>';
                }},
                {name:'status',       index:'status',       jsonmap:'status',      width:150,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return get_task_status_color(rowObject);
                }},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:150,  align:'center', hidden:false, search:false},
                {name:'send_time',    index:'send_time',    jsonmap:'send_time',   width:150,  align:'center', hidden:false, search:false},
                {name:'recv_time',    index:'recv_time',    jsonmap:'recv_time',   width:150,  align:'center', hidden:false, search:false},
                {name:'finish_time',  index:'finish_time',  jsonmap:'finish_time', width:100,  align:'center', hidden:false, search:false, sortable:false},
                {name:'progress',     index:'progress',     jsonmap:'progress',    width:100,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return v==-1 ? '':'<div class="progress" style="margin-bottom:0;position:relative;"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="'+v+'" aria-valuemin="0" aria-valuemax="100" style="width: '+v+'%">\
                    </div><span class="progress-val">'+v+'%</span></div>';
                }},
                {name:'act',          index:'act',          jsonmap:'act',         width:50,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return (rowObject.cmd == 'packet_cap' || rowObject.cmd == 'upload_cfg') ? '<a href="'+tpurl('Taskmgr','cpFilesDownload','id='+rowObject.id+'&sn='+rowObject.sn)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>' : '';
                }}
            ],
            pager: '#pager3',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'create_time',
            sortorder: 'desc',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            multiselect: true,
            multiselectWidth: 30,
            /*page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,*/
            postData: {sn: $.gf.sn},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        //周期任务
        $('#list33').jqGrid({
            url: tpurl('Taskmgr','loadTermTimedTask'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', $lang.VAR_CMD_NAME, $lang.EXEC_INTERVAL, $lang.IS_ENABLE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME, $lang.VAR_CP_START, $lang.VAR_CP_END],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',            width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'cmd',          index:'cmd',          jsonmap:'cmd_text',      width:150,  align:'center', hidden:false, search:false, classes:'td_link'},
                {name:'period_value', index:'period_value', jsonmap:'period_value_t',width:200,  align:'center', hidden:false, search:false},
                {name:'is_enable',    index:'is_enable',    jsonmap:'is_enable',     width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    var clr = v == '1' ? '#7bf57b' : 'red';
                    var txt = v == '1' ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
                    return '<span style="text-weight:700; color:'+clr+'">'+txt+'</span>';
                }},
                {name:'username',     index:'username',     jsonmap:'username',      width:100,  align:'center', hidden:false, search:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time',   width:150,  align:'center', hidden:false, search:false},
                {name:'start_time',   index:'start_time',   jsonmap:'start_time',    width:150,  align:'center', hidden:false, search:false},
                {name:'end_time',     index:'end_time',     jsonmap:'end_time',      width:150,  align:'center', hidden:false, search:false, sortable:false}
            ],
            pager: '#pager33',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'create_time',
            sortorder: 'ASC',
            viewrecords: true,
            autowidth: true,
            shrinkToFit: false,
            autoScroll: true,
            height: 'auto',
            postData: {sn: $.gf.sn},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false},
            loadComplete: function(xhr) {
                //跳转到任务详情
                $('td.td_link').on('click',function() {
                    $("#list2").jqGrid('resetSelection');
                    window.open(tpurl('Taskmgr','dsrwxq','tid='+$(this).parent().attr('id')));
                });
            }
        });

        //参数文件
        $('#list5').jqGrid({
            url: tpurl('Syscfg','loadFiles'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', $lang.VAR_PACKAGE_NAME, $lang.VAR_PACKAGE_SIZE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME, $lang.VAR_CFG_INFO],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'filesize',     index:'filesize',     jsonmap:'filesize',    width:50,   align:'center', hidden:false, search:false},
                {name:'creator',      index:'creator',      jsonmap:'creator',     width:50,   align:'center', hidden:false, search:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:100,  align:'center', hidden:false, search:false},
                {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false, sortable:false}
            ],
            pager: '#pager5',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'name',
            sortorder: 'ASC',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            postData: {searchType:'file_list', filetype:2, sn:$.gf.sn},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        //抓包文件
        $('#list55').jqGrid({
            url: tpurl('Syscfg','loadFiles'),
            datatype: 'local',
            mtype: 'post',
            colNames: ['id', $lang.VAR_PACKAGE_NAME, $lang.VAR_PACKAGE_SIZE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME, $lang.VAR_CFG_INFO],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'filesize',     index:'filesize',     jsonmap:'filesize',    width:50,   align:'center', hidden:false, search:false},
                {name:'creator',      index:'creator',      jsonmap:'creator',     width:50,   align:'center', hidden:false, search:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:100,  align:'center', hidden:false, search:false},
                {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false, sortable:false}
            ],
            pager: '#pager55',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            postData: {searchType:'file_list', filetype:6, sn:$.gf.sn},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            // setTimeout(function(){
                $.gf.reset_cmp_width();
            // }, 300);
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                $.gf.reset_cmp_width();
            }, 300);
        });

        //Change task status
        $('#tab_task_status a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.gf.tsid = $(e.target).attr('data-tsid');
            $('#list3').setGridParam({page:1, postData:{
                sn: $.gf.sn,
                tsid: $.gf.tsid,
                searchField: 'name',
                searchString: ''
            }}).trigger('reloadGrid');
        });

        //Change info type
        $('#tab_info_types a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var t = $(this).attr('data-show-div');
            $('.info-tab').hide();
            $('.info-' + t).show();
            if (t == 'tab2' && $('#list2').jqGrid('getGridParam','datatype') == 'local') {
                $("#list2").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
                $("#list22").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
            }
            if (t == 'tab3' && $('#list3').jqGrid('getGridParam','datatype') == 'local') {
                $("#list3").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
                $("#list33").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
            }
            if (t == 'tab4' && $(this).attr('data-init') == '0') {
                $('.btn-today').click();
                $(this).attr('data-init', '1');
            }
            if (t == 'tab5' && $('#list5').jqGrid('getGridParam','datatype') == 'local') {
                $("#list5").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
                $("#list55").jqGrid('setGridParam',{datatype:'json'}).trigger('reloadGrid');
            }
            $.gf.reset_cmp_width();
        });

        //Refresh grid
        $('.btn-refresh').click(function(){
            var gridid = $(this).attr('data-grid-id');
            $(gridid).trigger('reloadGrid');
        });

        //Download
        $('.btn-download').click(function(){
            var gridid = $(this).attr('data-grid-id'), act = $(this).attr('data-act');
            var id = $(gridid).jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.SELECT_FILE);
                return;
            }
            window.open(tpurl('Syscfg', act, 'id='+id));
        });

        //Delete task
        $('.btn-del-task').click(function(){
            var ids = $('#list3').jqGrid('getGridParam','selarrrow');
            if (ids.length == 0) {
                $.notice(1,$lang.SELECT_TASK);
                return;
            }
            $.confirm($lang.VAR_CONFIRM_DEL_TASK, function(){
                ajax(tpurl('Taskmgr', 'termDetailDeleteTask'), {ids:ids.join(',')}, function(msg){
                    $.notice(msg);
                    $("#list3").trigger('reloadGrid');
                });
            });
        });

        //Delete timed task
        $('.btn-del-timed-task').click(function(){
            var id = $('#list33').jqGrid('getGridParam','selrow');
            if (!id) {
                $.notice(1,$lang.SELECT_TASK);
                return;
            }
            $.confirm($lang.VAR_CONFIRM_DEL_TASK, function(){
                ajax(tpurl('Taskmgr', 'termDetailDeleteTimedTask'), {ids:id, tbname:'timed_term_task', 'sn':$.gf.sn}, function(msg){
                    $.notice(msg);
                    $("#list33").trigger('reloadGrid');
                });
            });
        });

        // 查看VPN数量
        $('#recordModal').on('shown.bs.modal', function() {
            if ($(this).attr('data-jqgrid-type') == '0'){
                $(this).attr('data-jqgrid-type','1');
                $('#list4').jqGrid({
                    url: tpurl('Term', 'loadFenceRecordData'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: [$lang.VAR_SN, $lang.TIME, $lang.VAR_GPS_LNG, $lang.VAR_GPS_LAT, $lang.ALARM_EVENT_TYPE],
                    colModel:[
                        {name:'sn',                 index:'sn',                jsonmap:'sn',               width:100,      align:'center', hidden:false, search:false, sortable:true},
                        {name:'report_time',        index:'report_time',       jsonmap:'report_time',      width:100,      align:'center', hidden:false, search:false, sortable:true},
                        {name:'report_longitude',   index:'report_longitude',  jsonmap:'report_longitude', width:100,      align:'center', hidden:false, search:false, sortable:true},
                        {name:'report_latitude',    index:'report_latitude',   jsonmap:'report_latitude',  width:100,      align:'center', hidden:false, search:false, sortable:true},
                        {name:'act',                index:'act',               jsonmap:'act',              width:100,      align:'center', hidden:false, search:false, sortable:true}
                    ],
                    pager: '#pager4',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'report_time',
                    sortorder: 'DESC',
                    viewrecords: true,
                    width: $('#recordModal div.modal-body').width(),
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {sn: $.gf.sn},
                    rownumbers: true,
                    rownumWidth: 30,
                    jsonReader: {repeatitems: false}
                });
            } else {
                $('#list4').trigger('reloadGrid');
            }
        });

        $('#enable_setgps').on('change', function(){
            if (!$.gf.gm || !$.gf.gm.map) return;
            if ($(this).is(':checked')) {
                $.gf.gm.map.on('click', function(e) {
                    var lng = e.latlng.lng, lat = e.latlng.lat;
                    $.gf.gm.marker_setgps = L.marker([lat, lng]).addTo($.gf.gm.map);
                    ajax(tpurl('Term', 'setgps'), {sn:$.gf.sn, markers:lng+','+lat}, function(msg){
                        if (msg.status == 0) {
                            layer.msg(msg.info, {offset:[0, 0], time:1000, icon:1});
                            window.setTimeout(function(){
                                $.gf.refresh_tab01();
                            }, 1000);
                        } else {
                            $.notice(msg);
                        }
                    }, '', function(){
                        $.gf.gm.map.removeLayer($.gf.gm.marker_setgps);
                        $.gf.gm.marker_setgps = null;
                    });
                });
            } else {
                $.gf.gm.map.off('click');
            }
        });

        //Refresh page
        $('#refresh_page').on('click',function(){
            var index = $('#tab_info_types li.active').index();
            if (index == 0){
                ;
            }else if (index == 1){
                $('#list2').trigger('reloadGrid');
                $('#list22').trigger('reloadGrid');
            }else if (index == 2){
                $('#list3').trigger('reloadGrid');
                $('#list33').trigger('reloadGrid');
            }else if (index == 3){
                refresh_charts($.gf.refresh_page[0], $.gf.refresh_page[1], $.gf.refresh_page[2], $.gf.refresh_page[3]);
            }else if (index == 4){
                $('#list5').trigger('reloadGrid');
                $('#list55').trigger('reloadGrid');
            }
            if (index == 0 || index == 1){
                $.gf.refresh_tab01();
            }
        });

        // Init page
        $('#refresh_page').click();
    });
})(jQuery);