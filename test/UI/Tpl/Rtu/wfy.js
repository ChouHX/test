(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Rtu', 'wfy'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', $lang.ROUTER_SN, $lang.NETWORK_ANALYZER+'(IP)', $lang.NETWORK_ANALYZER+'('+$lang.VAR_NAME+')', $lang.NETWORK_ANALYZER+'('+$lang.VAR_SYSCFG_ALIAS+')', $lang.VAR_TERM_STATUS, $lang.VAR_ONLINE_RATES, $lang.VAR_CMD_CREATETIME, $lang.VAR_LAST_TIME],
            colModel:[
                {name:'id',                 index:'id',                 jsonmap:'id',               width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'sn',                 index:'sn',                 jsonmap:'sn',               width:100,  align:'center', hidden:false, search:false},
                {name:'ip',                 index:'ip',                 jsonmap:'ip',               width:100,  align:'center', hidden:false, search:false},
                {name:'name',               index:'name',               jsonmap:'name',             width:100,  align:'center', hidden:false, search:false},
                {name:'info',               index:'info',               jsonmap:'info',             width:100,  align:'center', hidden:false, search:false},
                {name:'status',             index:'status',             jsonmap:'status',           width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                    return v == '1' ? '<span style="color:#4caf50;">'+$lang.AT_WORK+'</span>' : '<span style="color:#999;">'+$lang.NOT_AT_WORK+'</span>'
                }},
                {name:'online_rate',        index:'online_rate',        jsonmap:'online_rate',      width:100,  align:'center', hidden:false, search:false, sortable:false, classes:'td_link'},
                {name:'create_time',        index:'create_time',        jsonmap:'create_time',      width:150,  align:'center', hidden:false, search:false},
                {name:'last_time',          index:'last_time',          jsonmap:'last_time',        width:150,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2',
            rowNum: $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'name',
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
            postData: {searchType:'oem_lixun_vna', act:'load_wfy'},
            rownumbers: true,
            rownumWidth: 40,
            jsonReader: {repeatitems: false},
            loadComplete: function(xhr){
                // 打开历史记录页面
                $('#list2 td.td_link').on('click',function(){
                    $('#list2').jqGrid('resetSelection');
                    var row_id = $(this).parent().attr('id');
                    var row = $("#list2").getRowData(row_id);
                    $('#gridFileList h4').html($lang.WORK_RECORD + '&nbsp;(' + row.ip + ' - ' + row.name + ')');
                    $('#gridFileList').attr('data-vna-id', $(this).parent().attr('id')).modal({
                        position: 'fit',
                        moveable: true
                    });
                });
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
                searchType: 'oem_lixun_vna',
                act: 'load_wfy',
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
                    url: tpurl('Rtu', 'wfy'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', $lang.VAR_DEVICE_URL_REPORT_TIME, $lang.VAR_TERM_STATUS, 'd1', 'd2', 'd3'],
                    colModel:[
                        {name:'id',                 index:'id',           jsonmap:'id',                 width:50,   align:'center', hidden:true,  search:false, key:true},
                        {name:'report_time',        index:'report_time',  jsonmap:'report_time',        width:100,  align:'center', hidden:false, search:false},
                        {name:'status',             index:'status',       jsonmap:'status',             width:100,  align:'center', hidden:false, search:false, formatter:function(v){
                            return v == '1' ? '<span style="color:#4caf50;">'+$lang.AT_WORK+'</span>' : '<span style="color:#999;">'+$lang.NOT_AT_WORK+'</span>'
                        }},
                        {name:'d1',                 index:'d1',           jsonmap:'d1',                 width:100,  align:'center', hidden:false, search:false, sortable:false, classes:'td_link d1'},
                        {name:'d2',                 index:'d2',           jsonmap:'d2',                 width:100,  align:'center', hidden:false, search:false, sortable:false, classes:'td_link d2'},
                        {name:'d3',                 index:'d3',           jsonmap:'d3',                 width:100,  align:'center', hidden:false, search:false, sortable:false, classes:'td_link d3'}
                    ],
                    pager: '#pager3',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'report_time',
                    sortorder: 'DESC',
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
                        searchType: 'oem_lixun_vna_report_record',
                        act: 'load_records',
                        vna_id: $('#gridFileList').attr('data-vna-id')
                    },
                    rownumbers: false,
                    rownumWidth: 30,
                    multiselect: false,
                    jsonReader: {repeatitems: false},
                    onCellSelect: function(rowid,iCol,cellcontent,e){
                        // 打开d1/d2/d3详情页面
                        if (iCol <= 2) return;
                        var fields = $('#list3').jqGrid('getGridParam', 'colNames');
                        var field = fields[iCol];
                        var row = $('#list3').getRowData(rowid);
                        $('#list3').jqGrid('resetSelection');
                        $('#gridData input').val(row.report_time);
                        $('#gridData label:eq(1)').html(field+'：');
                        ajax(tpurl('Rtu', 'wfy'), {act:'load_d123', id:rowid, field:field}, function(msg) {
                            $('#gridData textarea').val(msg.data);
                            $('#gridData').modal({
                                position: 'fit',
                                moveable: true
                            });
                        });
                    }
                });
            }else{
                $('#list3').setGridParam({page:1, postData:{
                    searchType: 'oem_lixun_vna_report_record',
                    act: 'load_records',
                    vna_id: $('#gridFileList').attr('data-vna-id')
                }}).trigger('reloadGrid');
            }
        });

        // Edit
        $('button[data-act=edit]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.VAR_SELECT_DATA);
                return;
            }
            var row = $('#list2').getRowData(id);
            $('#gridEdit input[name=id]').val(row.id);
            $('#gridEdit input[name=name]').val(row.name);
            $('#gridEdit input[name=info]').val(row.info);
            $('#gridEdit').modal({
                position: 'fit',
                moveable: true
            });
        });

        $('#gridEdit button.btn-success').click(function(){
            var params = $.serializeObject('#fm_edit_info');
            params.act = 'edit_info';
            ajax(tpurl('Rtu', 'wfy'), params, function(msg) {
                $.notice(msg);
                if (msg.status == 0) {
                    $('#list2').trigger('reloadGrid');
                    $('#gridEdit').modal('hide');
                }
            });
        });
    });
})(jQuery);