(function($){
    $(document).ready(function(){
        $.gf.refresh_charts = function(){
            var div = layer.load(1, {
              shade: [0.1,'#fff']
            });
            var o = $('#myLgModal'),
                start = new Date(o.attr('data-start').replace(/-/g,'/')).getTime();
                end = o.attr('data-end');
            if (end == '--'){
                end = new Date().getTime();
            }else{
                end = new Date(end.replace(/-/g,'/')).getTime();
            }
            ajax(tpurl('Rtu','getSensorHistoryData'),{
                start: (start+'').substr(0,10),
                end: (end+'').substr(0,10),
                sn: o.attr('data-sn'),
                slave_id: o.attr('data-slave_id'),
                addr: o.attr('data-addr'),
            },function(msg){
                var data = [];
                for (var k=0; k<msg.length; k++){
                    data.push([msg[k][0], msg[k][2]]);
                }
                $.gf.alarm_data_chart.series[0].setData(data);
                $.gf.alarm_data_chart.subtitle.update({text:o.attr('data-sensor_name')+'：'+new Date(start).format('yyyy-MM-dd hh:mm:ss')+' ~ '+new Date(end).format('yyyy-MM-dd hh:mm:ss')});
                $.gf.alarm_data_chart.series[0].name = o.attr('data-sensor_name');
                $.gf.alarm_data_chart.series[0].unit = o.attr('data-unit');
            },'',function(){
                layer.close(div);
            });
        };

        $('#list2').jqGrid({
            url: tpurl('Rtu','gjjl'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', 'slave_id', 'addr', 'unit', 'warning_type', 'sensor_name', $lang.VAR_SN2, $lang.DEVICE_NAME, $lang.RTU_WARN_TYPE, $lang.ALARM_INFO, $lang.ALARM_TIME, $lang.IS_RECOVER, $lang.RECOVER_VALUE, $lang.RECOVER_TIME],
            colModel:[
                {name:'id',           index:'id',              jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'slave_id',     index:'slave_id',        jsonmap:'slave_id',    width:50,   align:'center', hidden:true,  search:false},
                {name:'addr',         index:'addr',            jsonmap:'addr',        width:50,   align:'center', hidden:true,  search:false},
                {name:'unit',         index:'unit',            jsonmap:'unit',        width:50,   align:'center', hidden:true,  search:false},
                {name:'warning_type', index:'warning_type',    jsonmap:'warning_type',width:50,   align:'center', hidden:true,  search:false},
                {name:'sensor_name',  index:'sensor_name',     jsonmap:'sensor_name', width:50,   align:'center', hidden:true,  search:false},
                {name:'sn',           index:'sn',              jsonmap:'sn',          width:150,  align:'center', hidden:false, search:false},
                {name:'dev_name',     index:'dev_name',        jsonmap:'dev_name',    width:150,  align:'center', hidden:false, search:false},
                {name:'xx',           index:'rtu_data_set_id', jsonmap:'xx',          width:150,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    /*if (rowObject.warning_type == '1'){
                        return '--';
                    } else {
                        return v+'('+rowObject.unit+')';
                    }*/
                    // '&nbsp;<i style="cursor:pointer" title="'+$lang.TRIGGERING_CONDITIONS+'" class="fa fa-question-circle" onclck="$.gf.view_rule_condition()"></i>'
                    var arr = $lang.RTU_DATA_ALARM_TYPE;
                    switch (rowObject.warning_type){
                        case '0':
                            return $lang.THRESHOLD_ALARM;
                        case '1':
                            return arr[0];
                        case '2':
                            return arr[1];
                        default:
                            break;
                    }
                }},
                {name:'value',        index:'value',           jsonmap:'value',       width:260,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    if (rowObject.warning_type == '0'){
                        return format_warning_info(v, rowObject);
                    } else if (rowObject.warning_type == '1') {
                        var str = rowObject.content.replace(/And/g, '<span class="bit_op">And</span>');
                        return str.replace(/Or/g, '<span class="bit_op">Or</span>');
                    } else {
                        return rowObject.content;
                    }
                }},
                {name:'report_time',  index:'report_time',     jsonmap:'report_time', width:150,   align:'center', hidden:false, search:false},
                {name:'is_recover',   index:'is_recover',      jsonmap:'is_recover',  width:100,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return rowObject.warning_type == '0' && v == '1' ? '<i class="fa fa-check" style="color:#7bf57b"></i>' : '--';
                }},
                {name:'r_value',      index:'r_value',         jsonmap:'r_value',     width:100,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return rowObject.warning_type == '0' && rowObject.is_recover == '1' ? v : '--';
                }},
                {name:'r_catch_time', index:'r_catch_time',    jsonmap:'r_catch_time',width:150,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return rowObject.warning_type == '0' && v != '0000-00-00 00:00:00' ? v : '--';
                }}
            ],
            pager: '#pager2',
            rowNum: $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'report_time',
            sortorder: 'desc',
            viewrecords: true,
            autowidth: true,
            shrinkToFit: $.gf.sm_screen ? false : true,
            autoScroll: true,
            height: $.gf.sm_screen ? 330 : $('.wrapper').height()-325,
            multiselect: true,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'rtu_warning'},
            rownumbers: true,
            rownumWidth: 50,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'),$('.jqgrid_c'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'),$('.jqgrid_c'));
            }, 300);
        });

        //Search
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'rtu_warning',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        //View history data
        $('button[data-act=view]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            var row = $('#list2').getRowData(id);
            if (row.warning_type == '1'){
                $.notice(1,$lang.MULTIPLE_SENSOR_ALERT_NOT_SUPPORT_VIEW_DATA);
                return;
            }
            var row = $('#list2').jqGrid('getRowData',id);
            $('#myLgModal').attr({
                'data-sn': row.sn,
                'data-slave_id': row.slave_id,
                'data-addr': row.addr,
                'data-start': row.report_time,
                'data-end': row.r_catch_time,
                'data-sensor_name': row.sensor_name,
                'data-unit': row.unit
            }).modal({
                position: 'fit',
                moveable: true
            });
        });
        $('#myLgModal').on('show.bs.modal', function() {
            if (typeof $.gf.alarm_data_chart == 'undefined'){
                Highcharts.setOptions($.gf.highChartsOptions);
                   $.gf.alarm_data_chart = Highcharts.chart('alarm_data_chart', {
                    chart: {
                        zoomType: 'x'
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
                    colors: ['#00c0ef'],
                    legend: {
                        enabled: false
                    },
                    series: [{
                        name: $lang.VAR_SYSCFG_VALUE,
                        unit: '',
                        type: 'spline'
                    }]
                });
            }
            $.gf.refresh_charts();
        });

        //Delete record
        $('button[data-act=del]').click(function(){
            var ids = $('#list2').jqGrid('getGridParam','selarrrow');
            if (ids.length == 0) {
                $('#myLgModal2').modal('show');
            }else{
                $.confirm($lang.ALARM_RECORD_DEL_CONFIRM, function(){
                    ajax(tpurl('Rtu','delAlarmRecords'), {ids:ids.join(',')}, function(msg){
                        $.notice(msg);
                        if (msg.status == 0){
                            $("#list2").trigger('reloadGrid');
                        }
                    });
                });
            }
        });

        //Delete record range
        $('#myLgModal2 button.alarm-record-deletes').click(function(){
            var start = $('#del_start_dt').val(), end = $('#del_end_dt').val(), info = $lang.ALARM_RECORD_RANGE_DEL_CONFIRM.replace('%a', start);
            info = info.replace('%b', end);
            $.confirm(info, function(){
                ajax(tpurl('Rtu','delAlarmRecords'), {start:start,end:end}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $('#myLgModal2').modal('hide');
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        //Refresh chart data
        $('#myLgModal button.refresh').click(function(){
            $.gf.refresh_charts();
        });

        //Export excel
        $('button[data-act=export]').click(function(){
            getExcelData(tpurl('Rtu','exportAlarmRecord'));
        });

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });

        $.gf.daterangepicker_opt = {
            "singleDatePicker": true,
            "timePicker": true,
            "timePicker24Hour": true,
            'drops': 'down',
            "locale": {
                "format": "YYYY-MM-DD HH:mm:ss",
                "daysOfWeek": $lang.VAR_WEEK_ARR,
                "monthNames": $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(','),
                "applyLabel": $lang.VAR_BTN_SURE,
                "cancelLabel": $lang.VAR_BTN_CANCLE
            }
        };
        $('#del_start_dt').daterangepicker($.gf.daterangepicker_opt);
        $('#del_end_dt').daterangepicker($.gf.daterangepicker_opt);
    });
})(jQuery);