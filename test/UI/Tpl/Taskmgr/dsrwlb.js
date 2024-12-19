(function($){
    $.gf.getStaticsInfo = function(e){
        $('.my-loading:eq(0)').show();
        ajax(tpurl('Taskmgr','rwlbStatisticalInfo'), {tbname:'timed_term_task'}, function(msg){
            msg = msg.data;
            for (var x in msg){
                $('#'+x).html(msg[x]);
            }
            $('.my-loading:eq(0)').hide();
        });
    }

    $.gf.taskDetail = function(e, tsid){
        $('#gridviewTask h4').html($lang.VAR_MENU2_TASK + '&nbsp;(' + $(e).find('span.info-box-text').html() + ')');
        $('#gridviewTask').attr('data-task-status',tsid).modal({
            position: 'fit',
            moveable: true
        });
    }

    $.gf.taskDetailDel = function() {
        var ids = $('#list3').jqGrid('getGridParam','selarrrow');
        if (ids.length == 0){
            $.notice(1,$lang.SELECT_TASK);
            return;
        }
        $.confirm($lang.VAR_CONFIRM_DEL_TASK_BATCH.replace('%d',ids.length), function(){
            ajax(tpurl('Taskmgr','taskDetailDel'), {ids:ids.join(','), tbname:'timed_term_task_detail'}, function(msg){
                $.notice(msg);
                if (msg.status == 0 && msg.data > 0) {
                    $("#list3").trigger('reloadGrid');
                    $.gf.getStaticsInfo();
                }
            });
        });
    }

    if ($.gf.prev_page > 0){
        $.gf.cmd          = localStorage.getItem('dsrwlb_cmd');
        $.gf.searchString = localStorage.getItem('dsrwlb_searchString');
        $.gf.page         = localStorage.getItem('dsrwlb_page');
        $.gf.rowNum       = localStorage.getItem('dsrwlb_rowNum');
    }

    var tp = null;
    $(document).ready(function(){
        tp = $('#list1').taskpaging({
            url: tpurl('Taskmgr','dsrwlb'),
            sidx: 'id',
            sord: 'desc',
            rowNum: 10,
            pager: '#list1_paging',
            cmd: $.gf.cmd || 'all',
            searchString: $.gf.searchString || '',
            page: $.gf.page || 1,
            rowNum: $.gf.rowNum || 10,
            searchType: 'timed_term_task'
        });
        if ($.gf.cmd){
            $('#tab_groups li').removeClass('active');
            $('#tab_groups a[data-ttid='+$.gf.cmd+']').parent().addClass('active');
        }
        if ($.gf.searchString){
            $('input[name=searchString]').val($.gf.searchString);
        }

        $('.btn-task-acts button').click(function() {
            var act = $(this).attr('data-act'), is_enable = $(this).attr('data-is_enable'), btn_index = $(this).index();
            if (act == 'refresh'){
                tp.reload();
            }else{
                var tids = [];
                $('#list1 .task.active').each(function(){
                    tids.push($(this).attr('data-tid'));
                });
                if (tids.length == 0){
                    $.notice(1,$lang.SELECT_TASK);
                    return;
                }
                var info_arr = [$lang.TASK_ENABLE_CONFIRM, $lang.TASK_DISABLE_CONFIRM, $lang.VAR_CONFIRM_DEL_TASK];
                var info_arr_batch = [$lang.TASK_ENABLE_CONFIRM_BATCH, $lang.TASK_DISABLE_CONFIRM_BATCH, $lang.VAR_CONFIRM_DEL_TASK_BATCH];
                $.confirm(tids.length > 1 ? info_arr_batch[btn_index].replace('%d',tids.length) : info_arr[btn_index], function(){
                    ajax(tpurl('Taskmgr',act), {ids:tids.join(','),is_enable:is_enable, tbname:'timed_term_task'}, function(msg){
                        $.notice(msg);
                        if (msg.status == 0 && msg.data > 0) {
                            tp.reload();
                            if (act == 'taskDel') {
                                $.gf.getStaticsInfo();
                            }
                        }
                    });
                });
            }
        });

        //Change task type
        $('#tab_groups a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#search_fm').get(0).reset();
            var task_type = $(e.target).attr('data-ttid');
            tp.set_search_val('');
            tp.set_cmd(task_type);
            tp.reload();
        });

        //search task
        $('#search_fm').on('submit',function(){
            tp.set_search_val( $(this).find('input[name=searchString]').val() );
            tp.reload();
            return false;
        });

        //F5 clean localStorage
        document.onkeydown = function (e) {
            e = e || window.event;
            if ((e.ctrlKey && e.keyCode == 82) || e.keyCode == 116) {
                localStorage.removeItem('dsrwlb_cmd');
                localStorage.removeItem('dsrwlb_searchString');
                localStorage.removeItem('dsrwlb_page');
                localStorage.removeItem('dsrwlb_rowNum');
            }
        }

        //任务查看(异常，等待)
        $("#gridviewTask").on('shown.bs.modal', function() {
            if ($(this).attr('data-init') == '0'){
                $(this).attr('data-init','1');
                $('#list3').jqGrid({
                    url: tpurl('Taskmgr','loadTimedTaskDetail'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: [$lang.VAR_SN2, $lang.VAR_CMD_NAME, $lang.VAR_CMD_STATUS, $lang.VAR_CMD_CREATETIME, $lang.VAR_TASK_SEND_TIME, $lang.VAR_TASK_FINISH_TIME, $lang.VAR_CREATOR, $lang.VAR_CMD_PARAM, $lang.VAR_TASK_FINISH_TIME2, $lang.VAR_TIPS_DETAIL_INFO, $lang.VAR_OPERATION],
                    colModel:[
                        {name:'sn',           index:'sn',           jsonmap:'sn',          width:100,  align:'center', hidden:false, search:false},
                        {name:'cmd',          index:'cmd',          jsonmap:'cmd_text',    width:100,  align:'center', hidden:false, search:false},
                        {name:'status',       index:'status',       jsonmap:'status',      width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                            return get_task_status_color(rowObject);
                        }},
                        {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:150,  align:'center', hidden:false, search:false},
                        {name:'send_time',    index:'send_time',    jsonmap:'send_time',   width:150,  align:'center', hidden:false, search:false},
                        {name:'recv_time',    index:'recv_time',    jsonmap:'recv_time',   width:150,  align:'center', hidden:false, search:false},
                        {name:'username',     index:'username',     jsonmap:'username',    width:100,  align:'center', hidden:false, search:false},
                        {name:'value',        index:'value',        jsonmap:'value',       width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'finish_time',  index:'finish_time',  jsonmap:'finish_time', width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'progress',     index:'progress',     jsonmap:'progress',    width:100,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                            return v==-1 ? '':'<div class="progress" style="margin-bottom:0;position:relative;"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="'+v+'" aria-valuemin="0" aria-valuemax="100" style="width: '+v+'%">\
                            </div><span class="progress-val">'+v+'%</span></div>';
                        }},
                        {name:'act',          index:'act',          jsonmap:'act',         width:100,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                            return (rowObject.cmd == 'packet_cap' || rowObject.cmd == 'upload_cfg') ? '<a href="'+tpurl('Taskmgr','cpFilesDownload','id='+rowObject.id+'&sn='+rowObject.sn)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>' : '';
                        }}
                    ],
                    pager: '#pager3',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'create_time',
                    sortorder: 'DESC',
                    viewrecords: true,
                    width: $('#gridviewTask div.modal-body').width()-20,
                    shrinkToFit: false,
                    autoScroll: true,
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center', //分页栏位置
                    pgbuttons: true, //是否显示翻页按钮
                    pginput: true, //是否显示翻页输入框
                    postData: {
                        searchType: 'term_task_detail',
                        tid: -1,
                        tsid: $('#gridviewTask').attr('data-task-status')
                    },
                    rownumbers: true,
                    rownumWidth: 50,
                    multiselect: true,
                    jsonReader: {repeatitems: false}
                });
            }else{
                $('#list3').setGridParam({page:1, postData:{
                    tsid: $('#gridviewTask').attr('data-task-status')
                }}).trigger('reloadGrid');
            }
        });

        $.gf.getStaticsInfo();
    });
})(jQuery);