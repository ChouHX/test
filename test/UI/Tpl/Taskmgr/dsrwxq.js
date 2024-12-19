(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Taskmgr','loadTimedTaskDetail'),
            datatype: 'json',
            mtype: 'post',
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
                    return ($.gf.cmd == 'packet_cap' || $.gf.cmd == 'upload_cfg') ? '<a href="'+tpurl('Taskmgr','cpFilesDownload','tbname=timed_term_task&id='+rowObject.id+'&sn='+rowObject.sn)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>' : '';
                }}
            ],
            pager: '#pager2',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'sn',
            sortorder: 'asc',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            multiselect: true,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'timed_term_task_detail',tid:$.gf.tid},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'),$('.jqgrid_c'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'),$('.jqgrid_c'));
            }, 300);
        });

        //Delete, retry, refresh
        $('button.rwcs').click(function(){
            var act = $(this).attr('data-act'), btn_index = $(this).index();
            var tids = $('#list2').jqGrid('getGridParam','selarrrow');
            if (tids.length == 0) {
				$.notice(1,$lang.SELECT_TASK);
				return;
            }
            var info_arr = [$lang.VAR_CONFIRM_DEL_TASK, $lang.VAR_RETRY_SINGLE_CONFIRM];
            $.confirm(info_arr[btn_index], function(){
                ajax(tpurl('Taskmgr',act), {ids:tids.join(','), tbname:'timed_term_task_detail'}, function(msg){
                    $.notice(msg);
                    $("#list2").trigger('reloadGrid');
                });
            });
        });

        //Change task status
        $('#tab_groups a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#search_fm').get(0).reset();
            $.gf.tsid = $(e.target).attr('data-tsid');
            $('#list2').setGridParam({page:1, postData:{
                tid: $.gf.tid,
                tsid: $.gf.tsid,
                searchType: 'timed_term_task_detail',
                searchString: ''
            }}).trigger('reloadGrid');
        });

        //search task by username
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                tid: $.gf.tid,
                tsid: $.gf.tsid,
                searchType: 'timed_term_task_detail',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });
    });
})(jQuery);