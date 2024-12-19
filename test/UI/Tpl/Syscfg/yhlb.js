(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Syscfg','loadUsers'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', $lang.VAR_USER_NAME, $lang.VAR_USER_GROUP, $lang.VAR_USER_TYPE, $lang.VAR_DEVICE_EMAIL, $lang.MOBILE_NUMBER, $lang.VAR_USER_INFO],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'gname',        index:'gid',          jsonmap:'gname',       width:100,  align:'center', hidden:false, search:false},
                {name:'usr_type',     index:'usr_type',     jsonmap:'usr_type',    width:100,  align:'center', hidden:false, search:false},
                {name:'email',        index:'email',        jsonmap:'email',       width:100,  align:'center', hidden:false, search:false},
                {name:'sim',          index:'sim',          jsonmap:'sim',         width:100,  align:'center', hidden:false, search:false},
                {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false, sortable:false}
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
            postData: {searchType:'usr'},
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

        //search user by name
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'usr',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        //模态框关闭时清除内容
        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Add user
        $('button[data-act=add]').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_add')
            });
        });

        //Edit user
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
                remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_edit&id='+id)
            });
        });

        //Delete user
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
            $.confirm($lang.VAR_CONFIRM_DEL_USER, function(){
                ajax(tpurl('Syscfg','userDel'), {ids:id}, function(msg){
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