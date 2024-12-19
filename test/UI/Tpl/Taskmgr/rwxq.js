(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Taskmgr','loadTaskDetail'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames: ['id', $lang.VAR_SN2, $lang.VAR_CMD_STATUS, $lang.VAR_TASK_SEND_TIME, $lang.VAR_TASK_FINISH_TIME, $lang.VAR_TASK_FINISH_TIME2, $lang.VAR_TIPS_DETAIL_INFO, $lang.VAR_OPERATION],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'sn',           index:'sn',           jsonmap:'sn',          width:100,  align:'center', hidden:false, search:false},
                {name:'status',       index:'status',       jsonmap:'status',      width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    return get_task_status_color(rowObject);
                }},
                {name:'send_time',    index:'send_time',    jsonmap:'send_time',   width:100,  align:'center', hidden:false, search:false},
                {name:'recv_time',    index:'recv_time',    jsonmap:'recv_time',   width:100,  align:'center', hidden:false, search:false},
                {name:'finish_time',  index:'finish_time',  jsonmap:'finish_time', width:100,  align:'center', hidden:false, search:false, sortable:false},
                {name:'progress',     index:'progress',     jsonmap:'progress',    width:100,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return v==-1 ? '':'<div class="progress" style="margin-bottom:0;position:relative;"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="'+v+'" aria-valuemin="0" aria-valuemax="100" style="width: '+v+'%">\
                    </div><span class="progress-val">'+v+'%</span></div>';
                }},
                {name:'act',          index:'act',          jsonmap:'act',         width:50,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return ($.gf.cmd == 'packet_cap' || $.gf.cmd == 'upload_cfg') ? '<a href="'+tpurl('Taskmgr','cpFilesDownload','id='+rowObject.id+'&sn='+rowObject.sn)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>' : '';
                }}
            ],
            pager: '#pager2', //表格页脚的占位符(一般是div)的id
            rowNum: 10,//一页显示多少条
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'sn',    //初始化的时候排序的字段
            sortorder: 'asc',  //排序方式,可选desc,asc
            viewrecords: true, //定义是否要显示总记录数
            // caption: '表格的标题名字',
            // width: $('.jqgrid_c').width()-20,
            autowidth: true,
            // shrinkToFit: false,
            // autoheight: true,
            height: 'auto',
            multiselect: true,
            multiselectWidth: 30,
            page: 1, //起始页码
            pagerpos: 'center', //分页栏位置
            pgbuttons: true, //是否显示翻页按钮
            pginput: true, //是否显示翻页输入框
            postData: {searchType:'term_task_detail',tid:$.gf.tid}, //额外参数
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        $('#list3').jqGrid({
            url: tpurl('Syscfg','loadFiles'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', $lang.VAR_SN2, $lang.VAR_PACKAGE_NAME, $lang.VAR_PACKAGE_SIZE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME, $lang.VAR_OPERATION],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'sn',           index:'sn',           jsonmap:'sn',          width:80,  align:'center', hidden:false, search:false},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'filesize',     index:'filesize',     jsonmap:'filesize',    width:50,   align:'center', hidden:false, search:false},
                {name:'creator',      index:'creator',      jsonmap:'creator',     width:50,   align:'center', hidden:false, search:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:100,  align:'center', hidden:false, search:false},
                {name:'act',          index:'act',          jsonmap:'act',         width:50,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return '<a href="'+tpurl('Taskmgr','cpFilesDownload','fileid='+rowObject.id)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>';
                }}
            ],
            pager: '#pager3',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'create_time',
            sortorder: 'DESC',
            viewrecords: true,
            width: $('.jqgrid_c').width()-20,
            height: 'auto',
            multiselect: true,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'file_list',filetype:7},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'),$('.jqgrid_c'));
            jqgrid_set_width($('#list3'),$('.jqgrid_c'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'),$('.jqgrid_c'));
                jqgrid_set_width($('#list3'),$('.jqgrid_c'));
            }, 300);
        });

        //Delete, retry, refresh
        $('button.rwcs').click(function(){
            var act = $(this).attr('data-act'), btn_index = $(this).index();
            var tids = $('#list2').jqGrid('getGridParam','selarrrow');
            if (tids.length == 0){
				if (act == 'taskDetailRetry'){
					$.confirm($lang.VAR_RETRY_ALL_CONFIRM, function(){
						ajax(tpurl('Taskmgr','taskDetailRetry'), {tid:$.gf.tid, type:'all'}, function(msg){
							$.notice(msg);
							if (msg.status == 0){
								$("#list2").trigger('reloadGrid');
							}
						});
					});
					return;
				}else{
					$.notice(1,$lang.SELECT_TASK);
					return;
				}
            }
            var info_arr = [$lang.VAR_CONFIRM_DEL_TASK, $lang.VAR_RETRY_SINGLE_CONFIRM];
            $.confirm(info_arr[btn_index], function(){
                ajax(tpurl('Taskmgr',act), {ids:tids.join(',')}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        /*Retry all
        $('button.retry-all').click(function(){
            $.confirm($lang.VAR_RETRY_ALL_CONFIRM, function(){
                ajax(tpurl('Taskmgr','taskDetailRetry'), {tid:$.gf.tid, type:'all'}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });*/

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });

        //Change task status
        $('#tab_groups a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#search_fm').get(0).reset();
            $.gf.tsid = $(e.target).attr('data-tsid');
            $('#list2').setGridParam({page:1, postData:{
                tid: $.gf.tid,
                tsid: $.gf.tsid,
                searchType: 'term_task_detail',
                searchString: ''
            }}).trigger('reloadGrid');
        });

        //search task by username
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                tid: $.gf.tid,
                tsid: $.gf.tsid,
                searchType: 'term_task_detail',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        $('button.delete-photo').click(function(){
            var ids = $('#list3').jqGrid('getGridParam','selarrrow');
            if (ids.length == 0){
                $.notice(1,$lang.SELECT_TASK);
                return;
            }
            $.confirm($lang.VAR_CONFIRM_DEL_IMG, function(){
                ajax(tpurl('Syscfg','deleteResFile'), {ids:ids.join(','),filetype:7}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list3").trigger('reloadGrid');
                    }
                });
            });
        });
    });
})(jQuery);