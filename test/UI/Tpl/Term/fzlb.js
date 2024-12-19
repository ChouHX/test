(function($){
    $(document).ready(function(){
        var jqgrid_h = $.gf.sm_screen ? 315 : $('.wrapper').height()-340;
        $('#list2').jqGrid({
            url: tpurl('Syscfg', 'loadTermGroup'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', 'pid', $lang.GROUP_NAME, $lang.VAR_CREATOR, $lang.DEVICE_MONTHLY_FLUX_LIMIT+'(MB)', $lang.DEVICE_DAILY_FLUX_LIMIT+'(MB)', $lang.PORTAL_DEVICE_MONTHLY_FLUX_LIMIT+'(MB)', $lang.PORTAL_DEVICE_DAILY_FLUX_LIMIT+'(MB)',$lang.VAR_CMD_CREATETIME],
            colModel:[
                {name:'id',                 index:'id',                 jsonmap:'id',               width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'pid',                index:'pid',                jsonmap:'pid',              width:50,   align:'center', hidden:true,  search:false},
                {name:'name',               index:'name',               jsonmap:'name',             width:100,  align:'center', hidden:false, search:false},
                {name:'creator',            index:'creator',            jsonmap:'creator',          width:100,  align:'center', hidden:false, search:false},
                {name:'month_flux_limit',   index:'month_flux_limit',   jsonmap:'month_flux_limit', width:100,  align:'center', hidden:false, search:false},
                {name:'day_flux_limit',     index:'day_flux_limit',     jsonmap:'day_flux_limit',   width:100,  align:'center', hidden:false, search:false},
                {name:'device_month_flux_limit',   index:'device_month_flux_limit',   jsonmap:'device_month_flux_limit', width:100,  align:'center', hidden:false, search:false},
                {name:'device_day_flux_limit',     index:'device_day_flux_limit',     jsonmap:'device_day_flux_limit',   width:100,  align:'center', hidden:false, search:false},
                {name:'create_time',        index:'create_time',        jsonmap:'create_time',      width:100,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2',
            rowNum: $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            autowidth: true,
            shrinkToFit: true,
            autoScroll: true,
            height: jqgrid_h,
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'term_group'},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'), $('.nav-tabs-custom'), -20);
            jqgrid_set_width($('#list3'), $('.nav-tabs-custom'), -20);
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'), $('.nav-tabs-custom'), -20);
                jqgrid_set_width($('#list3'), $('.nav-tabs-custom'), -20);
            }, 300);
        });

        //Search term group by name
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                tid: $.gf.tid,
                tsid: $.gf.tsid,
                searchType: 'term_group',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        //模态框关闭时清除内容
        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Add group
        $('button[data-act=add]').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg','getModalHtml','tpl_id=term_group_add')
            });
        });

        //Edit group
        $('button[data-act=edit]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            var row = $('#list2').getRowData(id);
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg','getModalHtml','tpl_id=term_group_edit&id='+id+'&pid='+row.pid+'&name='+escape(row.name)+'&month_flux_limit='+row.month_flux_limit+'&day_flux_limit='+row.day_flux_limit+'&device_month_flux_limit='+row.device_month_flux_limit+'&device_day_flux_limit='+row.device_day_flux_limit)
            });
        });

        //Delete group
        $('button[data-act=del]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            if (id == 2){
                $.notice(1,$lang.VAR_CAN_NOT_DEL);
                return;
            }
            var row = $('#list2').getRowData(id),
                info = $lang.VAR_CONFIRM_DEL_TG.replace('%s', ' ('+row.name+')');
            $.confirm(info, function(){
                ajax(tpurl('Syscfg','termGroupDel'), {id:id}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        $('#tab_params_list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(this).attr('href') == '#params_tab_2' && $(this).attr('data-init') == '0') {
                $(this).attr('data-init', '1');
                $('#list3').jqGrid({
                    url: tpurl('Syscfg', 'loadGroupRules'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', 'group_id', $lang.IS_ENABLE, $lang.RULE_FILED, $lang.RULE_KEYWORD, $lang.GROUP_NAME, $lang.VAR_SYSCFG_ALIAS],
                    colModel:[
                        {name:'id',                 index:'id',                 jsonmap:'id',               width:50,   align:'center', hidden:true,  search:false, key:true},
                        {name:'group_id',           index:'group_id',           jsonmap:'group_id',         width:50,   align:'center', hidden:true,  search:false},
                        {name:'is_enable',          index:'is_enable',          jsonmap:'is_enable',        width:50,   align:'center', hidden:false, search:false, formatter:function(v){
                            return '<img src="'+$.gf.public_path+'images/icons/'+(v == 1 ? 'accept' : 'nobuy')+'.png" />'
                        }},
                        {name:'rule_type',          index:'rule_type',          jsonmap:'rule_type_text',   width:100,  align:'center', hidden:false, search:false},
                        {name:'rule_key',           index:'rule_key',           jsonmap:'rule_key',         width:100,  align:'center', hidden:false, search:false},
                        {name:'gname',              index:'gname',              jsonmap:'gname',            width:100,  align:'center', hidden:false, search:false},
                        {name:'info',               index:'info',               jsonmap:'info',             width:150,  align:'center', hidden:false, search:false}
                    ],
                    pager: '#pager3',
                    rowNum: $.gf.jq_pagesize,
                    rowList: [10, 15, 20, 30, 40, 50, 100],
                    sortname: 'id',
                    sortorder: 'asc',
                    viewrecords: true,
                    autowidth: true,
                    shrinkToFit: true,
                    autoScroll: true,
                    height: jqgrid_h,
                    multiselect: false,
                    multiselectWidth: 30,
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {searchType:'term_group_rule'},
                    rownumbers: true,
                    rownumWidth: 30,
                    jsonReader: {repeatitems: false}
                });
            }
        });

        // Add rule
        $('button[data-act=add_rule]').click(function() {
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg', 'getModalHtml', 'tpl_id=group_rule_add')
            });
        });

        // Edit rule
        $('button[data-act=edit_rule]').click(function() {
            var id = $('#list3').jqGrid('getGridParam','selrow');
            if (!id) {
                $.notice(1, $lang.VAR_SELECT_DATA);
                return;
            }
            var row = $('#list3').getRowData(id);
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg', 'getModalHtml', 'tpl_id=group_rule_edit&id='+id)
            });
        });

        // Delete rule
        $('button[data-act=del_rule]').click(function() {
            var id = $('#list3').jqGrid('getGridParam','selrow');
            if (!id) {
                $.notice(1, $lang.VAR_SELECT_DATA);
                return;
            }
            $.confirm($lang.VAR_RULE_DEL_CONFIRM, function() {
                ajax(tpurl('Syscfg', 'groupRulesDel'), {id:id, act:'delete'}, function(msg) {
                    $.notice(msg);
                    $("#list3").trigger('reloadGrid');
                });
            });
        });

        // Exec rule
        $('button[data-act=exec_rule]').click(function() {
            var id = $('#list3').jqGrid('getGridParam','selrow');
            ajax(tpurl('Syscfg', 'execGroupRule'), {rule_id:id||0}, function(msg) {
                $.notice(msg);
            });
        });
    });
})(jQuery);