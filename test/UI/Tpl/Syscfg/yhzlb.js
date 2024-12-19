(function($){
    $.gf.set_role = function(id,name){
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_add&gid='+id)
        });
    }
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Syscfg','loadRoles'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['sn', $lang.VAR_NAME, $lang.VAR_USER_GROUP_PRIVILEGE, $lang.VAR_MENU_USER_LIST, $lang.VAR_CMD_CREATETIME, $lang.VAR_OPERATION],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,  align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:50,  align:'center', hidden:false, search:false},
                {name:'privileges',   index:'privileges',   jsonmap:'privileges',  width:100, align:'center', hidden:false, search:false, sortable:false},
                {name:'user_list',    index:'user_list',    jsonmap:'user_list',   width:100, align:'center', hidden:false, search:false, sortable:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:50,  align:'center', hidden:false, search:false},
                {name:'operation',    index:'operation',    jsonmap:'operation',   width:30,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return '<i class="fa fa-user-plus" style="cursor:pointer" title="'+$lang.VAR_USR_ADD+'" onclick="$.gf.set_role('+rowObject.id+',\''+rowObject.name+'\')"></i>';
                }}
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
            height: $.gf.sm_screen ? 315 : $('.wrapper').height()-325,
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'usr_group'},
            rownumbers: true,
            rownumWidth: 30,
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

        //search role by username
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'usr_group',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        //模态框关闭时清除内容
        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Add role
        $('button[data-act=add]').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_group_add')
            });
        });

        //Edit role
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
                remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_group_edit&id='+id+'&name='+encodeURIComponent(row.name))
            });
        });

        //Delete role
        $('button[data-act=del]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            if (id == 1){
                $.notice(1,$lang.VAR_CAN_NOT_DEL);
                return;
            }
            var row = $('#list2').getRowData(id),
                info = $lang.VAR_CONFIRM_DEL_UG.replace('%s', ' ('+row.name+')');
            $.confirm(info, function(){
                ajax(tpurl('Syscfg','userGroupDelete'), {id:id,name:row.name}, function(msg){
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