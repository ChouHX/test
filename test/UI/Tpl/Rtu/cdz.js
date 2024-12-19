(function($){
    $.gf.cdz_timer_seconds = 8000;
    $.gf.cdz_timer1 = null;
    $.gf.cdz_timer2 = null;
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Rtu', 'cdz'),
            datatype: 'json',
            mtype: 'post',
            colNames: [$lang.CHARGING_STATION+' ID', $lang.VAR_TYPE, $lang.MODEL, $lang.MOTHERBOARD_VERSION_NUMBER, $lang.STARTUP_TIME, $lang.CHARGING_PILES_NUM, $lang.ACTUAL_VEHICLES_NUM, 'CMM '+$lang.VAR_NAME, 'CMM '+$lang.VAR_DEVICE_MAC, 'FW '+$lang.VAR_PACKAGE_VERSION, $lang.VAR_CMD_CREATETIME, $lang.VAR_LAST_TIME],
            colModel:[
                // {name:'id',                 index:'id',                 jsonmap:'id',               width:50,   align:'center', hidden:true,  search:false},
                {name:'station_id',         index:'station_id',         jsonmap:'station_id',       width:100,  align:'center', hidden:false, search:false, key:true},
                {name:'station_type_text',  index:'station_type',       jsonmap:'station_type_text',width:100,  align:'center', hidden:false, search:false},
                {name:'station_model',      index:'station_model',      jsonmap:'station_model',    width:100,  align:'center', hidden:false, search:false},
                {name:'station_fwver',      index:'station_fwver',      jsonmap:'station_fwver',    width:100,  align:'center', hidden:false, search:false},
                {name:'station_runtime',    index:'station_runtime',    jsonmap:'station_runtime',  width:100,  align:'center', hidden:false, search:false},
                {name:'ports',              index:'ports',              jsonmap:'ports',            width:100,  align:'center', hidden:false, search:false, classes:'td_link'},
                {name:'tablet_number',      index:'tablet_number',      jsonmap:'tablet_number',    width:100,  align:'center', hidden:false, search:false},
                {name:'CMM_name',           index:'CMM_name',           jsonmap:'CMM_name',         width:100,  align:'center', hidden:false, search:false},
                {name:'CMM_mac',            index:'CMM_mac',            jsonmap:'CMM_mac',          width:100,  align:'center', hidden:false, search:false},
                {name:'CMM_fwver',          index:'CMM_fwver',          jsonmap:'CMM_fwver',        width:100,  align:'center', hidden:false, search:false},
                {name:'create_time',        index:'create_time',        jsonmap:'create_time',      width:150,  align:'center', hidden:false, search:false},
                {name:'last_time',          index:'last_time',          jsonmap:'last_time',        width:150,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2',
            rowNum: $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'station_id',
            sortorder: 'ASC',
            viewrecords: true,
            autowidth: true,
            shrinkToFit: true,
            autoScroll: true,
            height: $.gf.sm_screen ? 330 : $('.wrapper').height()-325,
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'oem_charge_station', act:'load_stations'},
            rownumbers: true,
            rownumWidth: 40,
            jsonReader: {repeatitems: false},
            loadComplete: function(xhr){
                // 打开充电桩列表页面
                $('td.td_link').on('click',function(){
                    $("#list2").jqGrid('resetSelection');
                    $('#gridFileList h4').html($lang.CHARGING_PILES_LIST + '&nbsp;(' + $(this).parent().attr('id') + ')');
                    $('#gridFileList').attr('data-station-id', $(this).parent().attr('id')).modal({
                        position: 'fit',
                        moveable: true
                    });
                });
                if (!$.gf.cdz_timer1) {
                    $.gf.cdz_timer1 = window.setInterval(function(){
                        $("#list2").trigger('reloadGrid');
                    }, $.gf.cdz_timer_seconds);
                }
            }
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

        // Search
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'oem_charge_station',
                act: 'load_stations',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        // Refresh
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });

        // 充电桩列表
        $("#gridFileList").on('shown.bs.modal', function() {
            if ($(this).attr('data-init') == '0'){
                $(this).attr('data-init','1');
                $('#list3').jqGrid({
                    url: tpurl('Rtu', 'cdz'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', $lang.VAR_CODE, $lang.VOLTAGE+' (V)', $lang.CURRENT + ' (A)', $lang.CHARGING_POWER + ' (mAh)', $lang.CHARGING_TIME, $lang.VAR_TERM_STATUS, $lang.VAR_CMD_CREATETIME, $lang.VAR_LAST_TIME],
                    colModel:[
                        {name:'id',                 index:'id',           jsonmap:'id',                 width:50,   align:'center', hidden:true,  search:false, key:true},
                        {name:'port_no',            index:'port_no',      jsonmap:'port_no',            width:100,  align:'center', hidden:false, search:false},
                        {name:'voltage',            index:'voltage',      jsonmap:'voltage',            width:100,  align:'center', hidden:false, search:false},
                        {name:'current',            index:'current',      jsonmap:'current',            width:100,  align:'center', hidden:false, search:false},
                        {name:'capacity',           index:'capacity',     jsonmap:'capacity',           width:100,  align:'center', hidden:false, search:false},
                        {name:'charge_time',        index:'charge_time',  jsonmap:'charge_time',        width:100,  align:'center', hidden:false, search:false},
                        {name:'charge_state',       index:'charge_state', jsonmap:'charge_state',       width:100,  align:'center', hidden:false, search:false},
                        {name:'create_time',        index:'create_time',  jsonmap:'create_time',        width:150,  align:'center', hidden:false, search:false},
                        {name:'last_time',          index:'last_time',    jsonmap:'last_time',          width:150,  align:'center', hidden:false, search:false}
                    ],
                    pager: '#pager3',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'port_no',
                    sortorder: 'ASC',
                    viewrecords: true,
                    width: $('#gridFileList div.modal-body').width()-20,
                    shrinkToFit: true,
                    autoScroll: true,
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {
                        searchType: 'oem_charge_station_port',
                        act: 'load_ports',
                        station_id: $('#gridFileList').attr('data-station-id')
                    },
                    rownumbers: false,
                    rownumWidth: 30,
                    multiselect: false,
                    jsonReader: {repeatitems: false},
                    loadComplete: function(xhr){
                        if (!$.gf.cdz_timer2) {
                            $.gf.cdz_timer2 = window.setInterval(function(){
                                $("#list3").trigger('reloadGrid');
                            }, $.gf.cdz_timer_seconds);
                        }
                    }
                });
            }else{
                $('#list3').setGridParam({page:1, postData:{
                    searchType: 'oem_charge_station_port',
                    act: 'load_ports',
                    station_id: $('#gridFileList').attr('data-station-id')
                }}).trigger('reloadGrid');
            }
        });

        $("#gridFileList").on('hidden.bs.modal', function() {
            if ($.gf.cdz_timer2) {
                window.clearInterval($.gf.cdz_timer2);
                $.gf.cdz_timer2 = null;
            }
        });
    });
})(jQuery);