(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Syscfg','fwqyxjl'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames: ['id', $lang.VAR_NAME, $lang.CPU_USAGE, $lang.MEMORY_USAGE, $lang.VAR_DEVICE_URL_REPORT_TIME],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'cpu',          index:'cpu',          jsonmap:'cpu',         width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    return v+'%';
                }},
                {name:'mem',          index:'mem',          jsonmap:'mem',         width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    return v+'%';
                }},
                {name:'report_time',  index:'report_time',  jsonmap:'report_time', width:100,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2', //表格页脚的占位符(一般是div)的id
            rowNum: 10,//一页显示多少条
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'report_time',    //初始化的时候排序的字段
            sortorder: 'DESC',  //排序方式,可选desc,asc
            viewrecords: true, //定义是否要显示总记录数
            // caption: '表格的标题名字',
            width: $('.jqgrid_c').width()-20,
            // autowidth: true,
            // shrinkToFit: false,
            // autoheight: true,
            height: 315,
            multiselect: false,
            multiselectWidth: 30,
            page: 1, //起始页码
            pagerpos: 'center', //分页栏位置
            pgbuttons: true, //是否显示翻页按钮
            pginput: true, //是否显示翻页输入框
            postData: {
                searchType:'app_server',
                searchString:$.gf.search_val,
                start:  moment().startOf('day').unix(),
                end:  moment().endOf('day').unix()
            },
            rownumbers: true,
            rownumWidth: 60,
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

        $.gf.refresh_grid = function(){
            var dates = $('#start_dt').val().split(' - ');
            var start = new Date(dates[0]+' 00:00:00').getTime(), end = new Date(dates[1]+' 23:59:59').getTime();
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType:'app_server',
                searchString: p.searchString,
                start: start/1000,
                end: end/1000
            }}).trigger('reloadGrid');
        };

        //Search
        $('#search_fm').on('submit',function(){
            $.gf.refresh_grid();
            return false;
        });

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $.gf.refresh_grid();
        });

        //时间选择
        var month_arr = $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(',');
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
            // refresh_charts(start, end, undefined, (picker.chosenLabel == $lang.THIS_YEAR || picker.chosenLabel == $lang.LAST_YEAR ? 1 : undefined));
            $.gf.refresh_grid();
        });
        $('.btn-today').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment());
            o.setEndDate(moment());
            // refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $.gf.refresh_grid();
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).next().removeClass('btn-info').addClass('btn-default').blur();
        });
        $('.btn-this-month').click(function(){
            var o = $('#start_dt').data('daterangepicker');
            o.setStartDate(moment().startOf('month'));
            o.setEndDate(moment());
            // refresh_charts(o.startDate.startOf('day').unix(), o.endDate.endOf('day').unix());
            $.gf.refresh_grid();
            $(this).removeClass('btn-default').addClass('btn-info').blur();
            $(this).prev().removeClass('btn-info').addClass('btn-default').blur();
        });
    });
})(jQuery);