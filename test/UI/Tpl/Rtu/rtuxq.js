(function($){
    var month_arr = $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(',');
    var charts = [], grids = [];

    function refresh_charts(start, end){
        for (var i=0; i<charts.length; i++){
            charts[i].showLoading();
            (function(j){
                var o = $('#chart_'+j).parent(), arr = o.attr('id').split('-');
                ajax(tpurl('Rtu','getSensorHistoryData'),{start:start, end:end, sn:$.gf.sn, slave_id:arr[2], addr:arr[3]},function(msg){
                    var data = [], jqgridData = [];
                    $('#list'+j).jqGrid('clearGridData');
                    for (var k=0, tmp=null; k<msg.length; k++){
                        data.push([msg[k][0], msg[k][2]]);
                        jqgridData.push({
                            id:k+1,
                            report_time: new Date(msg[k][0]).format('yyyy-MM-dd hh:mm:ss'),
                            catch_time: new Date(msg[k][1]).format('yyyy-MM-dd hh:mm:ss'),
                            value: msg[k][2]
                        });
                    }
                    charts[j].hideLoading();
                    charts[j].series[0].setData(data);
                    $('#list'+j).setGridParam({data:jqgridData}).trigger('reloadGrid');
                });
            })(i);
        }
    }

    function refreshCurrentSensorValue(){
        ajax(tpurl('Rtu','refreshCurrentSensorValue'),{sn:$.gf.sn},function(msg){
            for (var i=0,obj=null,vt=''; i<msg.length; i++){
                obj = $('#sensor-data-'+msg[i].slave_id+'-'+msg[i].addr);
                if (obj.length > 0){
                    vt = parseInt(obj.attr('data-value-type'));
                    obj.find('.current-value span').html(msg[i].value);
                    obj.find('.current-report-time').html(msg[i].report_time);
                }
            }
        });
    }

    $(document).ready(function(){
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
            if (picker.chosenLabel == $lang.VAR_TODAY){
                $('.btn-time-range:eq(0)').removeClass('btn-default').addClass('btn-info')
                $('.btn-time-range:eq(1)').removeClass('btn-info').addClass('btn-default')
            } else if (picker.chosenLabel == $lang.VAR_YESTERDAY){
                $('.btn-time-range:eq(1)').removeClass('btn-default').addClass('btn-info')
                $('.btn-time-range:eq(0)').removeClass('btn-info').addClass('btn-default')
            } else {
                $('.btn-time-range').removeClass('btn-info').addClass('btn-default');
            }
            refresh_charts(start,end);
        });
        $('.btn-today').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment());
            o.setEndDate(moment());
            refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).next().removeClass('btn-info').addClass('btn-default').blur();
        });
        $('.btn-yesterday').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment().subtract('days', 1));
            o.setEndDate(moment().subtract('days', 1));
            refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).prev().removeClass('btn-info').addClass('btn-default').blur();
        });

        //Init chart
        Highcharts.setOptions($.gf.highChartsOptions);
        for (var i=0, o=null, name='', unit=''; i<$.gf.sensors; i++){
            o = $('#chart_'+i).parent();
            name = o.find('.current-title').html();
            unit = o.find('.current-value small').html();
            charts[i] = new Highcharts.StockChart({
                chart: {
                    renderTo: 'chart_'+i
                },
                rangeSelector: {enabled: false},
                credits: {enabled: false},
                navigator: {
                    enabled: true,
                    maskFill: 'rgba(180, 198, 220, 0.3)'
                },
                scrollbar: {
                    enabled: true,
                    barBackgroundColor: '#e9e9e9',
                    barBorderWidth: 0,
                    barBorderRadius: 3,
                    buttonBackgroundColor: '#e9e9e9',
                    buttonBorderWidth: 0,
                    buttonBorderRadius: 3
                },
                colors: ['#00c0ef'],
                tooltip:{
                    split: false,
                    shared: true,
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b> ('+unit+')<br/>'
                },
                yAxis:{
                    opposite: false,
                    lineWidth: 1
                },
                series : [{
                    name: name,
                    type: 'spline'
                }]
            });
            $('#list'+i).jqGrid({
                datatype: 'local',
                /*data: [
                    {id:1, report_time:'2018-06-15 08:00:01', catch_time:'2018-06-15 08:00:00', value:1},
                    {id:2, report_time:'2018-06-15 09:00:01', catch_time:'2018-06-15 09:00:00', value:2},
                    {id:3, report_time:'2018-06-15 10:00:01', catch_time:'2018-06-15 10:00:00', value:3},
                    {id:4, report_time:'2018-06-15 11:00:01', catch_time:'2018-06-15 11:00:00', value:4},
                    {id:5, report_time:'2018-06-15 12:00:01', catch_time:'2018-06-15 12:00:00', value:5},
                    {id:6, report_time:'2018-06-15 13:00:01', catch_time:'2018-06-15 13:00:00', value:6},
                ],*/
                data: [],
                colNames: ['id', $lang.VAR_DEVICE_URL_REPORT_TIME, $lang.CATCH_TIME, $lang.VAR_SYSCFG_VALUE],
                colModel:[
                    {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                    {name:'report_time',  index:'report_time',  jsonmap:'report_time', width:150,  align:'center', hidden:false, search:false},
                    {name:'catch_time',   index:'catch_time',   jsonmap:'catch_time',  width:150,  align:'center', hidden:false, search:false},
                    {name:'value',        index:'value',        jsonmap:'value',       width:100,  align:'center', hidden:false, search:false}
                ],
                shrinkToFit: true,
                width: $('#data_list_'+i).width()-20,
                height: 85,
                rownumbers: true,
                rownumWidth: 50,
                sortname: 'report_time',
                sortorder: 'desc',
                pager: '#pager'+i,
                rowNum: 5,
                page: 1,
                pagerpos: 'center',
                pgbuttons: true,
                pginput: true
            });
        }
        $('.btn-today').click();

        //Reset chart width
        $(window).bind('resize', function(){
            for (var x in charts){
                charts[x].reflow();
            }
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                for (var x in charts){
                    charts[x].reflow();
                }
            }, 300);
        });

        //Fefresh current value
        refreshCurrentSensorValue();
        setInterval(function(){
            refreshCurrentSensorValue();
        }, 10000);

        //Change show type
        $('.data-show-type').click(function(){
            $(this).removeClass('btn-default').addClass('btn-info');
            $(this).siblings().removeClass('btn-info').addClass('btn-default');
            var index = $(this).index();
            for (var i=0; i<charts.length; i++){
                index == 0 ? $('#chart_'+i).show() : $('#chart_'+i).hide();
                index == 0 ? $('#data_list_'+i).hide() : $('#data_list_'+i).show();
            }
        });
    });
})(jQuery);