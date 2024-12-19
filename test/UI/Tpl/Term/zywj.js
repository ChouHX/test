(function($){
var set_g_width = function(){
    jqgrid_set_width($('#list1'),$('#tab_1'));
}
$(function () {
    // 打开 “选择设备” 窗口
    $.gf.select_term = function(idx) {
        $('#termModal').attr('data-idx', idx).modal({
            position: 'fit',
            moveable: true
        });
    }

    // 选中设备，赋值到“上位机/下位机”表格中
    $.gf.confirm_select_dev = function() {
        var sns = $('#list4').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0) {
            $.notice(1,$lang.VAR_MSG_SELECT_TERM);
            return;
        }
        for (var i=0,row=null; i<sns.length; i++) {
            row = $('#dev0').jqGrid('getRowData', sns[i]);
            if (row.sn) {
                $.notice(1, $lang.EXISTS_IN_DEV_LIST.replace('%s', sns[i]));
                return;
            }
        }
        var idx = $('#termModal').attr('data-idx');
        for (var i=0,row=null; i<sns.length; i++) {
            row = $('#list4').jqGrid('getRowData', sns[i]);
            $('#dev' + idx).jqGrid('addRowData', sns[i], {
                sn: row.sn,
                ud_sn: row.ud_sn,
                alias: row.alias,
                act: sns[i]
            });
        }
        $('#termModal').modal('hide');
    }

    // 从“上位机/下位机”表格中移除一行
    $.gf.remove_dev_sn = function(idx, id) {
        $('#dev'+idx).jqGrid('delRowData', id);
    }

    $('#termModal').on('shown.bs.modal', function() {
        if ($(this).attr('data-jqgrid-type') == '0'){
            $(this).attr('data-jqgrid-type','1');
            $('#list4').jqGrid({
                url: tpurl('Rtu', 'sjtc'),
                datatype: 'json',
                mtype: 'post',
                colNames: [$lang.VAR_SN2, $lang.VAR_SN1, $lang.VAR_SYSCFG_ALIAS, 'ICCID'],
                colModel:[
                    {name:'sn',                 index:'sn',                 jsonmap:'sn',               width:100,  align:'center', hidden:false, search:false, key:true},
                    {name:'ud_sn',              index:'ud_sn',              jsonmap:'ud_sn',            width:100,  align:'center', hidden:false, search:false},
                    {name:'alias',              index:'alias',              jsonmap:'alias',            width:100,  align:'center', hidden:false, search:false},
                    {name:'iccid',              index:'iccid',              jsonmap:'iccid',            width:100,  align:'center', hidden:false, search:false}
                ],
                pager: '#pager4',
                rowNum: 10,
                rowList: [10, 20, 30, 40, 50, 100],
                sortname: 'sn',
                sortorder: 'ASC',
                shrinkToFit: true,
                viewrecords: true,
                width: $('#termModal div.modal-body').width(),
                height: 'auto',
                multiselect: true,
                multiselectWidth: 30,
                page: 1,
                pagerpos: 'center',
                pgbuttons: true,
                pginput: true,
                postData: {
                    gid: -10,
                    searchString: '',
                    searchType: 'term',
                    act: 'load_terms'
                },
                rownumbers: true,
                rownumWidth: 55,
                jsonReader: {repeatitems: false}
            });
        } else {
            $('#list4').setGridParam({page:1, postData:{
                gid: -10,
                searchString: '',
                searchType: 'term',
                act: 'load_terms'
            }}).trigger('reloadGrid');
        }
    });

    $('#list1').jqGrid({
        url: tpurl('Syscfg','loadFiles'),
        datatype: 'json',
        mtype: 'post',
        colNames: ['id', $lang.VAR_PACKAGE_NAME, $lang.VAR_PACKAGE_SIZE, $lang.VAR_CREATOR, $lang.VAR_CMD_CREATETIME, $lang.VAR_CFG_INFO],
        colModel:[
            {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
            {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
            {name:'filesize',     index:'filesize',     jsonmap:'filesize',    width:50,   align:'center', hidden:false, search:false},
            {name:'creator',      index:'creator',      jsonmap:'creator',     width:50,   align:'center', hidden:false, search:false},
            {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:100,  align:'center', hidden:false, search:false},
            {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false, sortable:false}
        ],
        pager: '#pager1',
        rowNum: $.gf.jq_pagesize,
        rowList: [10, 15, 20, 30, 40, 50, 100],
        sortname: 'name',
        sortorder: 'asc',
        viewrecords: true,
        autowidth: true,
        shrinkToFit: true,
        autoheight: true,
        height: $.gf.sm_screen ? 315 : $('.wrapper').height()-340,
        multiselect: true,
        multiselectWidth: 30,
        page: 1,
        pagerpos: 'center',
        pgbuttons: true,
        pginput: true,
        postData: {searchType:'file_list', filetype:1},
        rownumbers: true,
        rownumWidth: 30,
        jsonReader: {repeatitems: false},
        loadComplete: function(msg){
            $('#tab_device_list li.active .num').html('('+msg.records+')');
        }
    });

    //Change tab
    $('#tab_device_list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var tabid = $('#tab_device_list li.active a').attr('href').replace('#tab_','');
        $('div.btns button:eq(0)').attr('disabled',tabid==6?true:false);
        $('#search_fm').get(0).reset();
        $('#list1').setGridParam({page:1, postData:{
            searchTable: 'file_list',
            filetype: tabid,
            searchType: 'file_list',
            searchString: ''
        }}).trigger('reloadGrid');
        if (tabid == 2) {
            $('#auto_down').show();
        } else {
            $('#auto_down').hide();
            $('#dev0').jqGrid('GridUnload');
            $('#auto_down_dev').hide();
        }
    });

    $('#auto_down select:eq(0)').on('change', function(v){
        if ($(this).val() == 'term') {
            $('#auto_down_dev').show();
            $('#dev0').jqGrid({
                datatype: 'local',
                mtype: 'post',
                colNames: [$lang.VAR_SN2, $lang.VAR_SN1, $lang.VAR_SYSCFG_ALIAS, $lang.VAR_OPERATION],
                colModel:[
                    {name:'sn',                 index:'sn',                 jsonmap:'sn',               width:100,  align:'center', hidden:false, search:false, key:true},
                    {name:'ud_sn',              index:'ud_sn',              jsonmap:'ud_sn',            width:100,  align:'center', hidden:false, search:false},
                    {name:'alias',              index:'alias',              jsonmap:'alias',            width:100,  align:'center', hidden:false, search:false},
                    {name:'act',                index:'act',                jsonmap:'act',              width:50,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                        return '<i class="fa fa-close" style="cursor:pointer; color:red;" onclick="$.gf.remove_dev_sn(0, \''+rowObject.sn+'\')"></i>'
                    }}
                ],
                rowNum: $.gf.jq_pagesize,
                viewrecords: true,
                autowidth: true,
                shrinkToFit: true,
                autoScroll: true,
                height: 'auto',
                rownumbers: true,
                rownumWidth: 30,
                jsonReader: {repeatitems: false}
            });
        } else {
            $('#dev0').jqGrid('GridUnload');
            $('#auto_down_dev').hide();
        }
    })

    // 设置jqgrid的宽度
    $(window).bind('resize', function(){
        set_g_width();
    });
    $('a.sidebar-toggle').click(function(){
        setTimeout(function(){
            set_g_width();
        }, 300);
    });

    //Search file by name
    $('#search_fm').on('submit',function(){
        var p = $.serializeObject('#search_fm');
        $('#list1').setGridParam({page:1, postData:{
            tid: $.gf.tid,
            tsid: $.gf.tsid,
            searchType: 'file_list',
            searchString: p.searchString
        }}).trigger('reloadGrid');
        return false;
    });

    //Bootstrap fileinput
    $('#filedata').fileinput({
        language: $.gf.lang,
        uploadUrl: tpurl('Syscfg','addResFile'),
        showUpload: false,
        showRemove: true,
        showPreview: false,
        dropZoneEnabled: false,
        showCaption: true,
        // allowedFileExtensions:  ['trx', 'bin', 'patch'],
        maxFileSize: 50*1024, //KB
        maxFileCount: 1,
        uploadExtraData: {}
    }).on("fileuploaded", function (event, data, previewId, index){
        $.notice(data.response);
        if (data.response.status == 0){
            $('#myLgModal').modal('hide');
            $("#list1").trigger('reloadGrid');
        }
    });

    //Add file
    $('button[data-act=add]').click(function(){
        var o = $('#tab_device_list li.active'), title = o.attr('data-title'), filetype = o.attr('data-filetype');
        $('#modal_fm').get(0).reset();
        $('#myLgModal .modal-title').html(title);
        $('#modal_fm input[name=filetype]').val(filetype);
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true
        });
    });
    $('button.save_add_file').click(function(){
        if ($('#filedata').fileinput('getFilesCount') == 0){
            $.notice(1,$lang.SELECT_FILE);
            return;
        }
        var p = $.serializeObject('#modal_fm'), sns = [];
        if (p.auto_down == 'term') {
            sns = $('#dev0').jqGrid('getDataIDs');
            if (sns.length == 0) {
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
        }
        $('#filedata').fileinput('refresh', {
            uploadExtraData: {filetype:p.filetype, info:p.info, auto_down:p.auto_down, term_list:sns.join(','), act:p.auto_down}
        });
        $('#filedata').fileinput('upload');
    });

    //Delete file
    $('button[data-act=del]').click(function(){
        var ids = $('#list1').jqGrid('getGridParam','selarrrow');
        if (ids.length == 0){
            $.notice(1,$lang.SELECT_FILE);
            return;
        }
        var info = $('#tab_device_list li.active').attr('data-del-info'+(ids.length>1?'-batch':'')).replace('%d',ids.length), filetype = $('#tab_device_list li.active').attr('data-filetype');
        $.confirm(info, function(){
            ajax(tpurl('Syscfg','deleteResFile'), {ids:ids.join(','), filetype:filetype}, function(msg){
                $.notice(msg);
                if (msg.status == 0){
                    $("#list1").trigger('reloadGrid');
                }
            });
        });
    });

    //Download
    $('button[data-act=download]').click(function(){
        var ids = $('#list1').jqGrid('getGridParam','selarrrow');
        if (ids.length == 0){
            $.notice(1,$lang.SELECT_FILE);
            return;
        }
        if (ids.length > 1){
            $.notice(1,$lang.SELECT_ONLY_ONE_FILE);
            return;
        }
        window.open(tpurl('Syscfg','resDownload','id='+ids[0]));
    });

    //Refresh grid
    $('button[data-act=refresh]').click(function(){
        $("#list1").trigger('reloadGrid');
    });

    $('#myLgModal').on('hidden.bs.modal', function() {
        $('#dev0').jqGrid('GridUnload');
        $('#auto_down_dev').hide();
    });
});
})(jQuery);