(function($){
var options = {
    chart: {
        type: 'spline'
    },
    credits:{text:'', href:''},
    title: {text: ''},
    xAxis: {
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
    series: [{
        name: $lang.NUMBER,
        data: []
    }]
};
$.extend(options, $.gf.highChartsOptions);
var charts = [];
charts.chart_online = Highcharts.chart('chart_online', options);

options.series[0].name = $lang.VAR_TERM_FLUX;
options.yAxis.allowDecimals = true;
charts.chart_flux = Highcharts.chart('chart_flux', options);

options.series[0].name = $lang.NUMBER;
options.yAxis.allowDecimals = false;
charts.chart_new = Highcharts.chart('chart_new', options);

options.chart.type = 'column';
/*options.series[0].dataLabels = {
    enabled: true,
    // rotation: -90,
    color: 'gray',
    align: 'center',
    y: 5
};*/
charts.chart_swv = Highcharts.chart('chart_swv', options);
charts.chart_netmode = Highcharts.chart('chart_netmode', options);
// charts.chart_task = Highcharts.chart('chart_task', options);
charts.chart_task = Highcharts.chart('chart_task', {
    global: { useUTC: false },
    lang: {
        loading: $lang.VAR_LOADING,
        noData: $lang.EXT_PAGING_1
    },
    credits:{text:'', href:''},
    legend: {enabled:false},
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45,
            beta: 0
        }
    },
    title: {text: ''},
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            depth: 35,
            dataLabels: {
                enabled: true,
                format: '{point.name}',
                formatter: function() {
                    return this.point.name + '：' + round(this.percentage, -1) + '%';
                }
            }
        }
    },
    series: [{
        type: 'pie',
        name: $lang.PROPORTION_OF,
        data: []
    }]
});

// 充电站2个图表初始化
if ($('#chart_station_model').size() != 0) {
charts.chart_station_model = Highcharts.chart('chart_station_model', {
    global: { useUTC: false },
    lang: {
        loading: $lang.VAR_LOADING,
        noData: $lang.EXT_PAGING_1
    },
    credits:{text:'', href:''},
    title: {text: ''},
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
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
        name: $lang.PROPORTION_OF,
        colorByPoint: true,
        data: [
            // {name: 'model-01', y: 3, sliced: true, selected: true}
        ]
    }]
});
charts.chart_charge_state = Highcharts.chart('chart_charge_state', options);
}

$.gf.reset_cmp_width = function(){
    for (var x in charts){
        charts[x].reflow();
    }
};

$(document).ready(function(){
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

    //Get statics info
    ajax(tpurl('Information','ptgkStatisticalInfo'), '', function(msg){
        var statics = msg.statics_info;
        for (var x in statics){
            $('#'+x).html(statics[x]);
        }
        charts.chart_swv.series[0].setData(msg.chart_swv);
        charts.chart_netmode.series[0].setData(msg.chart_netmode);
        charts.chart_task.series[0].setData(msg.chart_task);
        if ($('#chart_station_model').size() != 0) {
            charts.chart_station_model.series[0].setData(msg.chart_station_model);
            charts.chart_charge_state.series[0].setData(msg.chart_charge_state);
        }
    });

    // 图表数据
    ajax(tpurl('Information','loadDashboardData'),{chart_name:'chart_online'},function(msg){
        charts.chart_online.series[0].setData(msg.chart_online);
        charts.chart_flux.series[0].setData(msg.chart_flux);
        $('#chart_flux_unit').html('('+msg.flux_unit+')');
        charts.chart_new.series[0].setData(msg.chart_new);
    });

    // 刷新数据
    $('button[data-widget=refresh-chart]').click(function(){
        var o = $(this).parent().parent().next(), id = o.attr('id'), url = o.attr('data-url');
        charts[id].showLoading();
        ajax(tpurl('Information',url), {chart_name:id}, function(msg){
            charts[id].series[0].setData(msg[id]);
            charts[id].hideLoading();
        });
    });

    // Refresh info box
    $('.info-box-refresh').click(function(){
        var me = this;
        $(me).next().show();
        ajax(tpurl('Information','ptgkStatisticalInfo'), {}, function(msg){
            $(me).next().hide();
            var id = $(me).parent().find('h3').attr('id');
            $('#'+id).html(msg.statics_info[id]);
        });
    });
});
})(jQuery);