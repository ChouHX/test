(function($){
    $.gf.forms_len = 6;
    $.gf.changeParams = {};
    $.gf.isLoading = false;
    $.gf.alarm_groups = '';
    $.gf.reloadParams = function() {
        $.gf.isLoading = true;
        layer.load(1);
        ajax(tpurl('Syscfg', 'gjcl'), {}, function(msg){
            $.gf.changeParams = {};
            var cmp = null;
            for (var x in msg) {
                cmp = $('#spid_'+x);
                if (cmp) {
                    if (cmp.attr('type') == 'checkbox') {
                        cmp.prop('checked', msg[x] == 1 ? true : false);
                    } else {
                        cmp.val(msg[x]);
                    }
                }
            }
            $.gf.alarm_groups = msg.alarm_groups?msg.alarm_groups.split(","):[];
            ajax(tpurl('Term','getTermGroupTreeNodes'), {}, function(msg){
                var zNodes = [];
                if (msg.status == 0){
                    for (var i=0,len=msg.data.length; i<len; i++){
                        zNodes.push({
                            id: msg.data[i].id,
                            pId: msg.data[i].pId,
                            name: msg.data[i].name,
                            open: msg.data[i].open,
                            checked: $.inArray(msg.data[i].id, $.gf.alarm_groups) != -1
                        });
                    }
                }
                $.gf.termGroupTree1 = $.fn.zTree.init($("#termGroupTree1"), {
                    check:{
                        enable: true,
                        chkboxType: {'Y':'s', 'N':'s'}
                    },
                    data: {
                        simpleData: {
                            enable: true
                        }
                    }
                }, zNodes);

                fuzzySearch('termGroupTree1','.ztree_search',null,true); //初始化模糊搜索方法
            });
        },'',function(){
            $.gf.isLoading = false;
            layer.closeAll('loading');
        });
    };

    $.gf.refreshGrid3 = function() {
        var p = $.serializeObject('#search_fm');
        $('#list3').setGridParam({page:1, postData:{
            searchType: 'term_alarm_record',
            searchString: p.searchString,
            alarm_type: p.alarm_type,
            handle_status: p.handle_status
        }}).trigger('reloadGrid');
    };

    $.gf.viewEmailDetail = function(rowid) {
        var row = $('#list3').jqGrid('getRowData', rowid);
        $('#list3').jqGrid('resetSelection');
        $('#myLgModal3 .modal-body').html(row.email_content);
        $('#myLgModal3').modal({
            position: 'fit',
            moveable: true
        });
    };

    $('#fm1').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_email: {validators: {emailAddress: {message: $lang.EMAIL_INVALID}}}
        }
    });

    $('#fm2').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_interval_offline: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_offline_num_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_offline_percent_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_offline_time_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}}
        }
    });

    $('#fm3').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_interval_vpn: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_vpn_offline_time_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}}
        }
    });

    $('#fm4').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_interval_signal: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_signal_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}}
        }
    });

    $('#fm5').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_interval_flux: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_flux_month_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_flux_day_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}},
            alarm_term_flux_pool_threshold: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}}
        }
    });

    $('#fm6').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            alarm_interval_fence: {validators: {notEmpty: {message: $lang.FIELD_REQUIRED}, digits : { message : $lang.POSITIVE_INTEGER_VALIDATE}}}
        }
    });

    $(document).ready(function(){
        //Change tab
        $('#tab_device_list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('data-idx') != '1') return;
            if ($('#list3').html() == '') {
                $('#list3').jqGrid({
                    url: tpurl('Syscfg','alarmSendRecord'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', 'email_send_info', 'wx_send_info', $lang.RTU_WARN_TYPE, $lang.VAR_CMD_CREATETIME, 'detail', $lang.ALARM_INFO, $lang.HANDLE_STATUS, $lang.ALARM_RECEIVER,
                        $lang.ALARM_RECV_EMAILS, $lang.EMAIL_SEND_STATUS, $lang.EMAIL_SEND_TIME,
                        $lang.ALARM_RECV_WECHATS, $lang.WX_SEND_STATUS, $lang.WX_SEND_TIME
                    ],
                    colModel:[
                        {name:'id',                 index:'id',                 jsonmap:'id',                   width:50,   align:'center', hidden:true,  search:false, key:true},
                        {name:'email_send_info',    index:'email_send_info',    jsonmap:'email_send_info',      width:50,   align:'center', hidden:true,  search:false},
                        {name:'wx_send_info',       index:'wx_send_info',       jsonmap:'wx_send_info',         width:50,   align:'center', hidden:true,  search:false},
                        {name:'alarm_type',         index:'alarm_type',         jsonmap:'alarm_type',           width:150,  align:'center', hidden:false, search:false},
                        {name:'create_time',        index:'create_time',        jsonmap:'create_time',          width:150,  align:'center', hidden:false, search:false},
                        {name:'email_content',      index:'email_content',      jsonmap:'email_content',        width:150,  align:'center', hidden:true,  search:false},
                        {name:'email_content2',     index:'email_content',      jsonmap:'email_content',        width:150,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                            /*if (v.length <= 100) {
                                return v;
                            }*/
                            return '<span style="cursor:pointer;text-decoration:underline;color:#4caf50;display:inline-block;width:100%;height:100%;" onclick="$.gf.viewEmailDetail('+rowObject.id+')">'+$lang.VAR_VIEW+'</span>';
                        }},
                        {name:'handle_status',      index:'handle_status',      jsonmap:'handle_status',        width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                            var str = v == 0 ? $lang.UNTREATED : $lang.PROCESSED;
                            var clr = v == 0 ? 'gray' : '#4caf50';
                            var icon = v == 0 ? 'fa-minus-circle' : 'fa-check-circle';
                            return '<i style="color:'+clr+'" class="fa '+icon+'">&nbsp;'+str+'</i>';
                        }},
                        {name:'receiver_name',      index:'receiver_name',      jsonmap:'receiver_name',        width:100,  align:'center', hidden:false, search:false},
                        {name:'email',              index:'email',              jsonmap:'email',                width:150,  align:'center', hidden:false, search:false},
                        {name:'email_send_status',  index:'email_send_status',  jsonmap:'email_send_status',    width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                            return v == 0 ? '--' : get_alarm_send_status_txt(v, rowObject.email_send_info);
                        }},
                        {name:'email_send_ts',      index:'email_send_ts',      jsonmap:'email_send_ts',        width:150,  align:'center', hidden:false, search:false},
                        {name:'wx',                 index:'wx',                 jsonmap:'wx',                   width:150,  align:'center', hidden:false, search:false},
                        {name:'wx_send_status',     index:'wx_send_status',     jsonmap:'wx_send_status',       width:100,  align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                            return v == 0 ? '--' : get_alarm_send_status_txt(v, rowObject.wx_send_info);
                        }},
                        {name:'wx_send_ts',         index:'wx_send_ts',         jsonmap:'wx_send_ts',           width:150,  align:'center', hidden:false, search:false}
                    ],
                    pager: '#pager3',
                    rowNum: 5,
                    rowList: [5, 10, 20, 30, 40, 50, 100],
                    sortname: 'create_time',
                    sortorder: 'desc',
                    viewrecords: true,
                    width: $('#tab_2').width(),
                    height: 'auto',
                    shrinkToFit: true,
                    autoScroll: true,
                    multiselect: true,
                    multiselectWidth: 30,
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {searchType:'term_alarm_record', alarm_type:-1, handle_status:-1},
                    rownumbers: true,
                    rownumWidth: 30,
                    jsonReader: {repeatitems: false}
                });
                // $('#gbox_list3').css('margin', '0 auto');
            } else {
                $('#list3').trigger('reloadGrid');
            }
        });

        $('.reload-params').click(function(){
            $.gf.reloadParams();
        });

        $('.save-params').click(function() {
            var params = {};
            for (var i=1,obj=null; i<=$.gf.forms_len; i++) {
                obj = $('#fm'+i).data('bootstrapValidator');
                if (!obj) {
                    continue;
                }
                obj.validate();
                if (!obj.isValid()) {
                    $('#fm'+i+' div.has-error input').focus();
                    return;
                }
                $('#fm'+i+' input[id^=spid_]').each(function(){
                    var o = $(this);
                    params[o.attr('name')] = o.attr('type') == 'checkbox' ? (o.is(':checked') ? 1 : 0) : o.val().trim();
                });
            }
            if (params.alarm_enable_email == 1 && !params.alarm_email) {
                $.notice(1, $lang.PLS_SET_EMAIL);
                return;
            }
            if (params.alarm_enable_wx == 1 && !params.alarm_wx) {
                $.notice(1, $lang.PLS_SET_WX);
                return;
            }

            var nodes = $.gf.termGroupTree1.getCheckedNodes();
            for (var i=0,len=nodes.length,ids=[]; i<len; i++){
                ids.push(nodes[i].id);
            }

            params.alarm_groups = ids.join(',');

            ajax(tpurl('Syscfg', 'editAlarmParams'), params, function(msg){
                $.notice(msg);
                if (msg.status == 0) {
                    $.gf.reloadParams();
                }
            });
        });

        //搜索
        $('#search_fm').on('submit',function(){
            $.gf.refreshGrid3();
            return false;
        });

        // 切换告警类型、处理状态
        $('#select1, #select2').on('change', function(){
            $.gf.refreshGrid3();
        });

        //删除告警
        $('button[data-act=del]').click(function(){
            var ids = $('#list3').jqGrid('getGridParam','selarrrow');
            if (ids.length == 0){
                $.notice(1, $lang.VAR_MSG_SELECT_ALARM_RECORD);
                return;
            }
            $.confirm($lang.ALARM_RECORD_DEL_CONFIRM, function(){
                ajax(tpurl('Syscfg','alarmSendRecordDelete'), {ids:ids.join(',')}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list3").trigger('reloadGrid');
                    }
                });
            });
        });

        $.gf.reloadParams();

        $('button.select_all').click(function(){
            $.gf.termGroupTree1.checkAllNodes(true);
        });

        $('button.unselect_all').click(function(){
            $.gf.termGroupTree1.checkAllNodes(false);
        });

        $('.btn-my-search').click(function(){
            var i = $(this).find('i');
            if (i.hasClass('fa-search')) {
                $('.ztree_search').fadeIn(500);
                i.removeClass('fa-search').addClass('fa-close');
            } else {
                $('.ztree_search').val('').fadeOut(500);
                $('.ztree_search').trigger('input');
                i.removeClass('fa-close').addClass('fa-search');
            }
        });
    });
})(jQuery);