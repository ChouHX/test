(function($){
    // 查看详情
    $.gf.ad_detail = function(id, name) {
        $('#gridFileList h4').html($lang.VAR_DEVICE_LIST + '&nbsp;(' + name + ')');
        $('#gridFileList').attr('data-ad-id', id).modal({
            position: 'fit',
            moveable: true
        });
    }

    // 打开 “新增转发配置” 窗口
    $.gf.ad_add = function() {
        $('#modal_fm input[name=act]').val('add_cfg');
        $('#modal_fm input[name=config_id]').val('');
        $('#myLgModal h4').html($lang.ADD_TRANS_CONFIG);
        // 清空输入框
        $('#modal_fm input[name=name]').val('');
        $('#modal_fm input[name=is_enable]').prop('checked', true);
        $('#modal_fm textarea[name=info]').val('');
        $('#myLgModal').modal();
    }

    // 打开 “编辑转发配置” 窗口
    $.gf.ad_edit = function(id, name, is_enable, info) {
        $('#modal_fm input[name=act]').val('edit_cfg');
        $('#modal_fm input[name=config_id]').val(id);
        $('#myLgModal h4').html($lang.EDIT_TRANS_CONFIG + ' (' + name + ')');
        // 赋值输入框
        $('#modal_fm input[name=name]').val(name);
        $('#modal_fm input[name=is_enable]').prop('checked', is_enable == 1);
        $('#modal_fm textarea[name=info]').val(info);
        $('#myLgModal').modal();
    }

    // 保存配置，新增或编辑用此同一个接口
    $.gf.save_config = function() {
        var obj = $("#modal_fm").data('bootstrapValidator');
        obj.validate();
        if (!obj.isValid()){
            return;
        }
        var ids0 = $('#dev0').jqGrid('getDataIDs'), ids1 = $('#dev1').jqGrid('getDataIDs');
        if (ids0.length == 0) {
            $.notice(1,$lang.TIPS_SET_UPPER_MACHINE);
            return;
        }
        if (ids1.length == 0) {
            $.notice(1,$lang.TIPS_SET_LOWER_MACHINE);
            return;
        }
        ajax(tpurl('Rtu','sjtcOperation'), {
            name: $('#modal_fm input[name=name]').val(),
            act: $('#modal_fm input[name=act]').val(),
            config_id: $('#modal_fm input[name=config_id]').val(),
            is_enable: $('#modal_fm input[name=is_enable]').is(':checked') ? 1 : 0,
            info: $('#modal_fm textarea[name=info]').val(),
            dev0: ids0.join(','),
            dev1: ids1.join(',')
        }, function(msg){
            $.notice(msg);
            if (msg.status == 0) {
                $('#myLgModal').modal('hide');
                $.gf.tp.reload();
            }
        });
    }

    // 删除转发配置
    $.gf.ad_delete = function(id){
        $.confirm($lang.VAR_RULE_DEL_CONFIRM, function(){
            ajax(tpurl('Rtu','sjtcOperation'), {act:'delete_cfg', config_id:id}, function(msg){
                $.notice(msg);
                if (msg.status == 0){
                    $.gf.tp.reload();
                }
            });
        });
    }

    // 打开 “选择设备” 窗口
    $.gf.select_term = function(idx) {
        $('#termModal h4').html($lang.CONFIG_TERM + ' (' + (idx == 0 ? $lang.UPPER_MACHINE : $lang.LOWER_MACHINE) + ')');
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
                $.notice(1, $lang.EXISTS_IN_UPPER_LIST.replace('%s', sns[i]));
                return;
            }
            row = $('#dev1').jqGrid('getRowData', sns[i]);
            if (row.sn) {
                $.notice(1, $lang.EXISTS_IN_LOWER_LIST.replace('%s', sns[i]));
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

    $(document).ready(function(){
        $.gf.tp = $('#list1').taskpaging({
            url: tpurl('Rtu','sjtc') + '?act=load_config',
            sidx: 'name',
            sord: 'ASC',
            rowNum: 10,
            pager: '#list1_paging',
            cmd: $.gf.cmd || 'all',
            searchString: $.gf.searchString || '',
            page: $.gf.page || 1,
            rowNum: $.gf.rowNum || 10
        });

        //刷新
        $('button[data-act=refresh]').click(function(){
            $.gf.tp.reload();
        });

        //搜索
        $('#search_fm').on('submit',function(){
            $.gf.tp.set_search_val( $(this).find('input[name=searchString]').val() );
            $.gf.tp.reload();
            return false;
        });

        //search router
        $('#search_fm2').on('submit',function(){
            var p = $.serializeObject('#search_fm2'), ss = $.trim(p.searchString);
            $('#list4').setGridParam({page:1, postData:{
                gid: -10,
                searchString: ss,
                searchType: 'term',
                act: 'load_terms'
            }}).trigger('reloadGrid');
            return false;
        });

        // 查看上/下位机列表
        $("#gridFileList").on('shown.bs.modal', function() {
            if ($(this).attr('data-init') == '0'){
                $(this).attr('data-init','1');
                $('#list3').jqGrid({
                    url: tpurl('Rtu','sjtc'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', $lang.VAR_SN2, $lang.VAR_SN1, $lang.VAR_SYSCFG_ALIAS, $lang.TERM_MODEL, $lang.RECV_COUNT, $lang.VAR_AD_CREATETIME, $lang.VAR_LAST_LOGIN],
                    colModel:[
                        {name:'id',           index:'id',           jsonmap:'id',               width:50,   align:'center', hidden:true,  search:false},
                        {name:'sn',           index:'sn',           jsonmap:'sn',               width:100,  align:'center', hidden:false, search:false},
                        {name:'ud_sn',        index:'ud_sn',        jsonmap:'ud_sn',            width:100,  align:'center', hidden:false, search:false},
                        {name:'alias',        index:'alias',        jsonmap:'alias',            width:150,  align:'center', hidden:false, search:false},
                        {name:'term_type',    index:'term_type',    jsonmap:'term_type_text',   width:100,  align:'center', hidden:false, search:false},
                        {name:'recv_count',   index:'recv_count',   jsonmap:'recv_count',       width:100,  align:'center', hidden:false, search:false},
                        {name:'create_time',  index:'create_time',  jsonmap:'create_time',      width:150,  align:'center', hidden:false, search:false},
                        {name:'last_time',    index:'last_time',    jsonmap:'last_time',        width:150,  align:'center', hidden:false, search:false}
                    ],
                    pager: '#pager3',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'sn',
                    sortorder: 'ASC',
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
                        searchType: 'term_transfer_config_sn',
                        act: 'load_config_sn',
                        config_id: $('#gridFileList').attr('data-ad-id')
                    },
                    rownumbers: true,
                    rownumWidth: 30,
                    multiselect: false,
                    jsonReader: {repeatitems: false}
                });
            }else{
                $('#list3').setGridParam({page:1, postData:{
                    searchType: 'term_transfer_config_sn',
                    act: 'load_config_sn',
                    config_id: $('#gridFileList').attr('data-ad-id')
                }}).trigger('reloadGrid');
            }
        });

        $('#modal_fm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: {
                    validators: {
                        notEmpty: {
                            message: $lang.FIELD_REQUIRED
                        }
                    }
                }
            }
        });
    });



    $("#myLgModal").on('shown.bs.modal', function() {
        $("#modal_fm").data('bootstrapValidator').resetForm();
        if ($('#dev0').html() == '') {
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
                height: 300,
                rownumbers: true,
                rownumWidth: 30,
                jsonReader: {repeatitems: false}
            });
        }
        if ($('#dev1').html() == '') {
            $('#dev1').jqGrid({
                datatype: 'local',
                mtype: 'post',
                colNames: [$lang.VAR_SN2, $lang.VAR_SN1, $lang.VAR_SYSCFG_ALIAS, $lang.VAR_OPERATION],
                colModel:[
                    {name:'sn',                 index:'sn',                 jsonmap:'sn',               width:100,  align:'center', hidden:false, search:false, key:true},
                    {name:'ud_sn',              index:'ud_sn',              jsonmap:'ud_sn',            width:100,  align:'center', hidden:false, search:false},
                    {name:'alias',              index:'alias',              jsonmap:'alias',            width:100,  align:'center', hidden:false, search:false},
                    {name:'act',                index:'act',                jsonmap:'act',              width:50,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                        return '<i class="fa fa-close" style="cursor:pointer; color:red;" onclick="$.gf.remove_dev_sn(1, \''+rowObject.sn+'\')"></i>'
                    }}
                ],
                rowNum: $.gf.jq_pagesize,
                viewrecords: true,
                autowidth: true,
                shrinkToFit: true,
                autoScroll: true,
                height: 300,
                rownumbers: true,
                rownumWidth: 30,
                jsonReader: {repeatitems: false}
            });
        }
        var act = $('#modal_fm input[name=act]').val();
        $('#dev0').jqGrid('clearGridData');
        $('#dev1').jqGrid('clearGridData');
        if (act == 'edit_cfg') {
            // 加载(上位机/下位机)数据
            $.ajax({
                url: tpurl('Rtu','sjtc'),
                method: 'post',
                data: {
                    searchType: 'term_transfer_config_sn',
                    act: 'load_config_sn',
                    config_id: $('#modal_fm input[name=config_id]').val(),
                    rows: 10000,
                    page: 1,
                    sidx: 'sn',
                    sord: 'asc'
                },
                success: function(msg) {
                    msg = JSON.parse(msg)
                    if (!msg.rows) return;
                    for (var i=0; i<msg.rows.length; i++) {
                        $('#dev' + msg.rows[i].term_type).jqGrid('addRowData', msg.rows[i].sn, {
                            sn: msg.rows[i].sn,
                            ud_sn: msg.rows[i].ud_sn,
                            alias: msg.rows[i].alias
                        })
                    }
                }
            });
        }
    });

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
})(jQuery);