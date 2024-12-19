(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Syscfg','fwq'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames: ['id', $lang.VAR_NAME, $lang.VAR_SYSCFG_ALIAS, $lang.VAR_IP, $lang.VAR_PORT, $lang.INT_ADDR, $lang.INT_PORT, $lang.CPU_USAGE, $lang.MEMORY_USAGE, $lang.PROTOCOL_VERSION, $lang.VAR_FIRST_LOGIN, $lang.VAR_LAST_LOGIN],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'info',         index:'info',         jsonmap:'info',        width:150,  align:'center', hidden:false, search:false},
                {name:'ip',           index:'ip',           jsonmap:'ip',          width:100,  align:'center', hidden:false, search:false},
                {name:'port',         index:'port',         jsonmap:'port',        width:50,   align:'center', hidden:false, search:false},
                {name:'inner_ip',     index:'inner_ip',     jsonmap:'inner_ip',    width:100,  align:'center', hidden:false, search:false},
                {name:'inner_port',   index:'inner_port',   jsonmap:'inner_port',  width:50,   align:'center', hidden:false, search:false},
                {name:'cpu',          index:'cpu',          jsonmap:'cpu',         width:50,   align:'center', hidden:false, search:false, formatter:function(v){
                    return v+'%';
                }},
                {name:'mem',          index:'mem',          jsonmap:'mem',         width:50,   align:'center', hidden:false, search:false, formatter:function(v){
                    return v+'%';
                }},
                {name:'protocol',     index:'protocol',     jsonmap:'protocol',    width:100,  align:'center', hidden:false, search:false},
                {name:'first_time',   index:'first_time',   jsonmap:'first_time',  width:150,  align:'center', hidden:false, search:false},
                {name:'last_time',    index:'last_time',    jsonmap:'last_time',   width:150,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2', //表格页脚的占位符(一般是div)的id
            rowNum: 10,//一页显示多少条
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'name',    //初始化的时候排序的字段
            sortorder: 'ASC',  //排序方式,可选desc,asc
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
            postData: {searchType:'app_server'}, //额外参数
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

        //Search term group by name
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'app_server',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        //模态框关闭时清除内容
        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //History
        $('button[data-act=history]').click(function(){
            var id = $('#list2').jqGrid('getGridParam','selrow');
            if (!id){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            var row = $('#list2').getRowData(id);
            location.href = tpurl('Syscfg','fwqyxjl','search_val='+row.name);
        });

        //Edit
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
                remote: tpurl('Syscfg','getModalHtml','tpl_id=app_server_edit&id='+id)
            });
        });

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });
    });
})(jQuery);