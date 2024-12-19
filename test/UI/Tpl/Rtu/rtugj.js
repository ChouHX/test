(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Rtu','rtugj'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['ID', $lang.VAR_NAME, $lang.TRIGGERING_CONDITIONS, $lang.RTU_WARN_TYPE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME],
            colModel:[
                {name:'id',             index:'id',             jsonmap:'id',             width:60,   align:'center', hidden:true,  search:false, key:true, sortable:false},
                {name:'name',           index:'name',           jsonmap:'name',           width:60,   align:'center', hidden:false, search:false, sortable:false},
                {name:'rule_detail',    index:'rule_detail',    jsonmap:'rule_detail',    width:100,  align:'center', hidden:false, search:false, sortable:false, formatter: function(v, options, rowObject){
                    var arr = [];
                    for (var i=0,bit_op='',unit='',minutes=''; i<v.length; i++){
                        bit_op = trans_bit_op(v[i].bit_op);
                        bit_op = '<span class="bit_op">'+(bit_op || '&nbsp;')+'</span>';
                        unit = v[i].unit ? (' ('+v[i].unit+')') : '';
                        minutes = v[i].duration ? $lang.CONTINUED_SOME_MINUTES.replace('{_minutes_}',v[i].duration) : ''
                        arr.push(bit_op + minutes + v[i].name + ' ' + v[i].op + ' ' + v[i].value + unit);
                    }
                    return arr.join('<br>');
                }},
                {name:'rule_type',      index:'rule_type',      jsonmap:'rule_type',      width:80,   align:'center', hidden:false, search:false, sortable:false, formatter: function(v, options, rowObject){
                    var arr = $lang.RTU_DATA_ALARM_TYPE;
                    return arr[v];
                }},
                {name:'creator',        index:'creator',        jsonmap:'creator',        width:40,   align:'center', hidden:false, search:false, sortable:false},
                {name:'create_time',    index:'create_time',    jsonmap:'create_time',    width:100,  align:'center', hidden:false, search:false, sortable:false}
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
            height: $('.wrapper').height()-325,
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false},
            loadComplete: function(xhr){
                var rule_list = {};
                for (var i=0,row=null; i<xhr.rows.length; i++){
                    row = xhr.rows[i];
                    rule_list[row.id] = {
                        name: row.name,
                        rule_type: row.rule_type,
                        rule_detail: row.rule_detail
                    }
                }
                localStorage.setItem('rtugj_rule_list', JSON.stringify(rule_list));
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

        //Search term group by name
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'rtu_data_set',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Add rule
        $('button[data-act=add]').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Rtu','getModalHtml','tpl_id=rtu_data_alarm_rule_add')
            });
        });

        //Edit rule
        $('button[data-act=edit]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1, $lang.VAR_SELECT_DATA);
                return;
            }
            var row = $('#list2').getRowData(id);
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Rtu','getModalHtml','tpl_id=rtu_data_alarm_rule_edit&id='+id)
            });
        });

        //Delete rule
        $('button[data-act=del]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1, $lang.VAR_SELECT_DATA);
                return;
            }
            $.confirm($lang.VAR_RULE_DEL_CONFIRM, function(){
                ajax(tpurl('Rtu','rtuRuleOp'), {id:id, act:'delete'}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });
    });
})(jQuery);