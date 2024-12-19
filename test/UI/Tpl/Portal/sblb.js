(function($){
$.gf.charts = [];
$.gf.reset_cmp_width = function(){
    jqgrid_set_width($('#list1'), $('#tab_1'));
    jqgrid_set_width($('#list2'), $('#tab_1'));
    $.gf.charts[0].reflow();
    $.gf.charts[1].reflow();
    $.gf.charts[2].reflow();
}
$.gf.force_offline = function(sn, mac) {
    $.confirm($lang.CONFIRM_FORCE_OFFLINE, function(){
        ajax(tpurl('Task','forceOffline'), {term_list:sn, macs:mac}, function(msg){
            $.notice(msg);
        });
    });
}
$.gf.edit_data_limit = function(sn, mac) {
    $('#myLgModal').modal({
        position: 'fit',
        moveable: true,
        remote: tpurl('Portal','getModalHtml','tpl_id=edit_data_limit&mac_addr='+mac)
    });
}
$.gf.export = function(type) {
    getExcelData(tpurl('Portal', 'exportMobile'), {type:type});
}
$(function () {
    $('#list1').jqGrid({
        url: tpurl('Portal', 'sblb'),
        datatype: 'json',
        mtype: 'post',
        colNames: [$lang.VAR_TG, $lang.VAR_TERM_STATUS, $lang.VAR_SN2, $lang.VAR_SN1, $lang.VAR_SYSCFG_ALIAS, $lang.VAR_ONLINE_DEVICE_NUM, $lang.VAR_ONLINE_DEVICE, $lang.VAR_HISTORY_DEVICE],
        colModel:[
            {name:'gname',             index:'group_id',          jsonmap:'gname',             width:100,   align:'center', hidden:false, search:false},
            {name:'status',            index:'status',            jsonmap:'status',            width:100,   align:'center', hidden:false, search:false},
            {name:'sn',                index:'term.sn',           jsonmap:'sn',                width:100,   align:'center', hidden:false, search:false, key:true},
            {name:'ud_sn',             index:'ud_sn',             jsonmap:'ud_sn',             width:100,   align:'center', hidden:false, search:false},
            {name:'alias',             index:'alias',             jsonmap:'alias',             width:100,   align:'center', hidden:false, search:false},
            {name:'online_num',        index:'online_num',        jsonmap:'online_num',        width:100,   align:'center', hidden:false, search:false},
            {name:'online',            index:'online',            jsonmap:'online',            width:100,   align:'center', hidden:false, search:false, classes:'td_link', formatter:function(v, options, rowObject){
                return '<span class="glyphicon glyphicon-phone"></span>';
            }},
            {name:'offline',           index:'offline',           jsonmap:'offline',           width:100,   align:'center', hidden:false, search:false, classes:'td_link-gray', formatter:function(v, options, rowObject){
                return '<span class="glyphicon glyphicon-phone"></span>';
            }}
        ],
        pager: '#pager1',
        rowNum: 10,
        rowList: [10, 20, 30, 40, 50, 100],
        sortname: 'last_time',
        sortorder: 'DESC',
        viewrecords: true,
        shrinkToFit: true,
        autoScroll: true,
        autowidth: true,
        height: 350,
        multiselect: false,
        multiselectWidth: 30,
        page: 1,
        pagerpos: 'center',
        pgbuttons: true,
        pginput: true,
        postData: {
            gid: $.gf.gid || -10,
            searchString: '',
            searchType: 'term'
        },
        rownumbers: true,
        rownumWidth: 30,
        jsonReader: {repeatitems: false},
        onCellSelect: function(rowid, iCol, cellcontent, e) {
            if (iCol == 7 || iCol == 8) {
                var row = $('#list1').jqGrid('getRowData', rowid);
                $('#myLgModal2 h4').html(iCol == 7 ? '<span style="color:#4caf50;">'+$lang.VAR_ONLINE_DEVICE+'</span>' : $lang.VAR_HISTORY_DEVICE);
                $('#myLgModal2').attr('data-sn', row.sn).attr('data-type', iCol == 7 ? 'online' : 'history');
                $('#myLgModal2').modal({
                    position: 'fit',
                    moveable: true
                });
            }
        }
    });

    $('#myLgModal2').on('shown.bs.modal', function() {
        var jq_w = $('#myLgModal2 .modal-content').width() - 35;
        if ($(this).attr('data-jqgrid-type') == '0'){
            $(this).attr('data-jqgrid-type','1');
            $('#list2').jqGrid({
                url: tpurl('Portal', 'loadMobile'),
                datatype: 'json',
                mtype: 'post',
                colNames: [$lang.VAR_DEVICE_MAC, $lang.VAR_TERM_FLUX, 'day_flux_limit', 'month_flux_limit', $lang.DAILY_FLUX_LIMIT, $lang.DEVICE_FLUX_LIMIT_VALUE_TITLE, $lang.ONLINE_DURATION, $lang.VAR_DEVICE_LOGIN_TIME, $lang.VAR_LAST_LOGIN, $lang.VAR_OPERATION],
                colModel:[
                    {name:'mac_addr',               index:'mac_addr',           jsonmap:'mac_addr',             width:100,  align:'center', hidden:false, search:false, key:true},
                    {name:'flux',                   index:'flux',               jsonmap:'flux',                 width:80,   align:'center', hidden:false, search:false},
                    {name:'day_flux_limit',         index:'day_flux_limit',     jsonmap:'day_flux_limit',       width:80,   align:'center', hidden:true,  search:false},
                    {name:'month_flux_limit',       index:'month_flux_limit',   jsonmap:'month_flux_limit',     width:80,   align:'center', hidden:true,  search:false},
                    {name:'day_flux_limit_format',  index:'day_flux_limit',     jsonmap:'day_flux_limit_format',width:80,   align:'center', hidden:true,  search:false, formatter:function(v){
                        return v == 0 ? $lang.NOT_LIMITED : v;
                    }},
                    {name:'month_flux_limit_format',index:'month_flux_limit',   jsonmap:'month_flux_limit_format',width:80,   align:'center', hidden:true,  search:false, formatter:function(v){
                        return v == 0 ? $lang.NOT_LIMITED : v;
                    }},
                    {name:'duration',               index:'duration',           jsonmap:'duration',             width:80,   align:'center', hidden:false, search:false, sortable:false},
                    {name:'login_time',             index:'login_time',         jsonmap:'login_time',           width:100,  align:'center', hidden:false, search:false},
                    {name:'last_time',              index:'last_time',          jsonmap:'last_time',            width:100,  align:'center', hidden:false, search:false},
                    {name:'act',                    index:'act',                jsonmap:'act',                  width:50,   align:'center', hidden:false, search:false, sortable:false}
                ],
                pager: '#pager2',
                rowNum: 10,
                rowList: [10, 20, 30, 40, 50, 100],
                sortname: 'last_time',
                sortorder: 'DESC',
                viewrecords: true,
                width: jq_w,
                shrinkToFit: true,
                height: 'auto',
                page: 1,
                pagerpos: 'center',
                pgbuttons: true,
                pginput: true,
                postData: {sn: $('#myLgModal2').attr('data-sn'), type: $('#myLgModal2').attr('data-type')},
                rownumbers: true,
                rownumWidth: 30,
                jsonReader: {repeatitems: false}
            });
        } else {
            $('#list2').setGridParam({page:1, postData: {sn: $('#myLgModal2').attr('data-sn'), type: $('#myLgModal2').attr('data-type')}}).trigger('reloadGrid');
        }
        $('#list2').jqGrid($('#myLgModal2').attr('data-type') == 'online' ? 'showCol' : 'hideCol', 'act');
    });

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

    // Search router
    $('#search_fm').on('submit',function(){
        var p = $.serializeObject('#search_fm'), ss = $.trim(p.searchString);
        $('#list1').setGridParam({page:1, postData:{
            gid: $.gf.gid,
            searchType: 'term',
            searchString: ss
        }}).trigger('reloadGrid');
        return false;
    });

    // Search mobile
    $('#search_fm2').on('submit',function(){
        var p = $.serializeObject('#search_fm2'), ss = $.trim(p.searchString);
        $('#list2').setGridParam({page:1, postData:{
            mac_addr: $('#myLgModal2').attr('data-sn'),
            type: $('#myLgModal2').attr('data-type'),
            searchType: 'mobile',
            searchString: ss
        }}).trigger('reloadGrid');
        return false;
    });

    var myChart1 = null, myChart2 = null, myChart3 = null;
    Highcharts.setOptions($.gf.highChartsOptions);
    $('#chart1').highcharts({
        credits:{text:'', href:''},
        title: {
            floating:true,
            text:''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
            innerSize: '80%',
            name: $lang.PROPORTION_OF,
            data: []
        }]
    }, function(c) {
        // 环形图圆心
        var centerY = c.series[0].center[1],
            titleHeight = parseInt(c.title.styles.fontSize);
        c.setTitle({y:centerY + titleHeight/2});
        myChart1 = c;
    });

    var options = {
        chart: {
            type: 'spline'
        },
        credits:{text:'', href:''},
        title: {text: ''},
        xAxis: {
            categories: $.gf.xs
        },
        yAxis: {
            min: 0,
            title: {text: ''}
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },
        legend: {enabled:false},
        series: [{
            name: $lang.VAR_DEVICE_FLUX,
            data: []
        }]
    };
    myChart2 = Highcharts.chart('chart2', options);

    options.series[0].name = $lang.NUMBER;
    myChart3 = Highcharts.chart('chart3', options);

    $.gf.charts.push(myChart1);
    $.gf.charts.push(myChart2);
    $.gf.charts.push(myChart3);

    // 图表数据
    setTimeout(function(){
        ajax(tpurl('Portal','onlineRates'),'',function(msg){
            myChart1.series[0].setData(msg);
            myChart1.title.update({ text: $lang.VAR_TOTAL+':'+(msg[0].y+msg[1].y) });
        });
    }, 50);
    setTimeout(function(){
        ajax(tpurl('Portal','fluxTrend'),'',function(msg){
            myChart2.series[0].setData(msg);
        });
    }, 100);
    setTimeout(function(){
        ajax(tpurl('Portal','loginTrend'),'',function(msg){
            myChart3.series[0].setData(msg);
        });
    }, 150);
});
})(jQuery);