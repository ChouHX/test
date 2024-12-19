(function($){
    $.gf.getStaticsInfo = function() {
        ajax(tpurl('Term', 'jklbStatisticalInfo'), '', function(msg) {
            msg = msg.data;
            for (var x in msg){
                $('#'+x).html(msg[x]);
            }
        });
    }

    // n2n还是使用老的远程通道的命令
    $.gf.vpnAct = function(sn, vpn_type, vpn_name, cmd) {
        var url = 'vpnConnect', params = {cmd:cmd, act:'term', term_list:sn, value:'type='+vpn_type+'&name='+vpn_name};
        if (vpn_type == 'n2n' || vpn_type == 'zerotier') {
            url = cmd == 'vpn_connect' ? 'rcConnect' : 'rcDissconnect';
            params = {term_list:sn};
        }
        ajax(tpurl('Task', url), params, function(msg) {
            $.notice(msg);
        });
    }

    //修改参数 - 路由表 - 网络接口的选项动态生成
    $.gf.getInterfaceOpts = function() {
        $.ajax({
            url: $lang.curl.replace('Index/replace', 'Term/getInterfaceOptions'),
            data: {
                sn: ($.gf.tp.term_list != '' && $.gf.tp.term_list.indexOf(',') == -1 ? $.gf.tp.term_list : 0)
            },
            success: function(msg){
                msg = $.parseJSON(msg);
            }
        });
    }

    $.gf.list2ColNames = [
        $lang.VAR_TERM_STATUS, $lang.VAR_TG, $lang.VAR_SN2, $lang.VAR_SN1, $lang.GATEWAY_SN, $lang.VAR_VSN, $lang.DEVICE_MODEL, $lang.VAR_SYSCFG_ALIAS,
        $lang.NET_MODE, $lang.NET_MODE+'('+$lang.CHANNEL_1+')', $lang.NET_MODE+'('+$lang.CHANNEL_2+')',
        $lang.VAR_TERM_SIGNAL, $lang.VAR_TERM_SIGNAL+'('+$lang.CHANNEL_1+')', $lang.VAR_TERM_SIGNAL+'('+$lang.CHANNEL_2+')', $lang.CURRENT_LINK, 'RSSI', 'RSRP', 'RSRQ',
        $lang.VAR_IP, $lang.VAR_IP+'('+$lang.CHANNEL_1+')', $lang.VAR_IP+'('+$lang.CHANNEL_2+')',
        $lang.VAR_PORT, $lang.SIM_POS, $lang.VAR_PORT+'('+$lang.CHANNEL_1+')', $lang.VAR_PORT+'('+$lang.CHANNEL_2+')',
        $lang.VAR_TERM_FLUX, $lang.VAR_TERM_FLUX+'('+$lang.CHANNEL_1+')', $lang.VAR_TERM_FLUX+'('+$lang.CHANNEL_2+')',
        $lang.TODAY_FLUX,  $.gf.show_onelink_month_flux ? $lang.ONELINK_MONTH_FLUX : $lang.FLUX_CURRENT_MONTH,
        $lang.VAR_SWV, $lang.PROTOCOL_VERSION, $lang.ONLINE_DURATION, $lang.VAR_LOGOUT_RECORD,
        $lang.VAR_FIRST_LOGIN, $lang.VAR_DEVICE_LOGIN_TIME, $lang.VAR_LAST_LOGIN, $lang.VAR_LAST_LOGIN+'('+$lang.CHANNEL_1+')', $lang.VAR_LAST_LOGIN+'('+$lang.CHANNEL_2+')',
        'SIM 1', 'IMSI 1', 'ICCID 1', 'IMEI 1', $lang.TERM_MODULE_VENDOR+' 1', $lang.TERM_MODULE_TYPE+' 1',
        'SIM 2', 'IMSI 2', 'ICCID 2', 'IMEI 2', $lang.TERM_MODULE_VENDOR+' 2', $lang.TERM_MODULE_TYPE+' 2',
        $lang.VAR_CMD_CREATETIME, $lang.VAR_OPERATOR+' 1', $lang.VAR_OPERATOR+' 2', $lang.VPN_NUM, $lang.CPU_USAGE, $lang.MEMORY_USAGE, $lang.STORAGE_USAGE, 'SSID', $lang.GUIJI_CODE, $lang.VAR_BASE_ADDRESS, $lang.VAR_WIFI_MAC, $lang.VAR_POSITION
    ];
    $.gf.list2ColModel = [
        {name:'status',            index:'status',            jsonmap:'status',            width:100, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'gname',             index:'group_id',          jsonmap:'gname',             width:100, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'sn',                index:'term.sn',           jsonmap:'sn',                width:150, align:'center', hidden:false, search:false, key:true, classes:'td_link', group:'basic'},
        {name:'ud_sn',             index:'ud_sn',             jsonmap:'ud_sn',             width:150, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'gateway_sn',        index:'gateway_sn',        jsonmap:'gateway_sn',        width:100, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'vsn',               index:'vsn',               jsonmap:'vsn',               width:150, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'term_model_text',   index:'term_model',        jsonmap:'term_model_text',   width:100, align:'center', hidden:false, search:false, group:'basic'},
        {name:'alias',             index:'alias',             jsonmap:'alias',             width:100, align:'center', hidden:false, search:false, group:'basic'},
        {name:'net_mode',          index:'net_mode',          jsonmap:'net_mode',          width:100, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'net_mode_sim1',     index:'net_mode_sim1',     jsonmap:'net_mode_sim1',     width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'net_mode_sim2',     index:'net_mode_sim2',     jsonmap:'net_mode_sim2',     width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'term_signal',       index:'term_signal',       jsonmap:'term_signal',       width:100, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'term_signal_sim1',  index:'term_signal_sim1',  jsonmap:'term_signal_sim1',  width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'term_signal_sim2',  index:'term_signal_sim2',  jsonmap:'term_signal_sim2',  width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'current_link',      index:'current_link',      jsonmap:'current_link_text', width:100, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'rssi',              index:'rssi',              jsonmap:'rssi',              width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'rsrp',              index:'rsrp',              jsonmap:'rsrp',              width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'rsrq',              index:'rsrq',              jsonmap:'rsrq',              width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'ip',                index:'ip',                jsonmap:'ip',                width:150, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'ip_sim1',           index:'ip_sim1',           jsonmap:'ip_sim1',           width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'ip_sim2',           index:'ip_sim2',           jsonmap:'ip_sim2',           width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'port',              index:'port',              jsonmap:'port',              width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'sim_pos',           index:'sim_pos',           jsonmap:'sim_pos',           width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'port_sim1',         index:'port_sim1',         jsonmap:'port_sim1',         width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'port_sim2',         index:'port_sim2',         jsonmap:'port_sim2',         width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'flux',              index:'flux',              jsonmap:'flux',              width:100, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'flux_sim1',         index:'flux_sim1',         jsonmap:'flux_sim1',         width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'flux_sim2',         index:'flux_sim2',         jsonmap:'flux_sim2',         width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'day_flux',          index:'day_flux',          jsonmap:'day_flux',          width:100, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'month_flux',        index:'month_flux',        jsonmap:'month_flux',        width:150, align:'center', hidden:true,  search:false, group:'runinfo'},
        {name:'sw_version',        index:'sw_version',        jsonmap:'sw_version',        width:100, align:'center', hidden:false, search:false, group:'basic'},
        {name:'protocol',          index:'protocol',          jsonmap:'protocol',          width:100, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'online_duration',   index:'online_duration',   jsonmap:'online_duration',   width:100, align:'center', hidden:true,  search:false, sortable:false, group:'runinfo'},
        {name:'offline_duration',  index:'offline_duration',  jsonmap:'offline_duration',  width:150, align:'center', hidden:true,  search:false, sortable:false, group:'runinfo'},
        {name:'first_login',       index:'first_login',       jsonmap:'first_login',       width:150, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'login_time',        index:'login_time',        jsonmap:'login_time',        width:150, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'last_time',         index:'last_time',         jsonmap:'last_time',         width:150, align:'center', hidden:false, search:false, group:'runinfo'},
        {name:'last_time_sim1',    index:'last_time_sim1',    jsonmap:'last_time_sim1',    width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset3'},
        {name:'last_time_sim2',    index:'last_time_sim2',    jsonmap:'last_time_sim2',    width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset4'},
        {name:'sim',               index:'sim',               jsonmap:'sim',               width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'imsi',              index:'imsi',              jsonmap:'imsi',              width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'iccid',             index:'iccid',             jsonmap:'iccid',             width:180, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'imei',              index:'imei',              jsonmap:'imei',              width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'module_vendor',     index:'module_vendor',     jsonmap:'module_vendor',     width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'module_type',       index:'module_type',       jsonmap:'module_type',       width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset1'},
        {name:'sim2',              index:'sim2',              jsonmap:'sim2',              width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'imsi2',             index:'imsi2',             jsonmap:'imsi2',             width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'iccid2',            index:'iccid2',            jsonmap:'iccid2',            width:180, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'imei2',             index:'imei2',             jsonmap:'imei2',             width:150, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'module_vendor2',    index:'module_vendor2',    jsonmap:'module_vendor2',    width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'module_type2',      index:'module_type2',      jsonmap:'module_type2',      width:100, align:'center', hidden:true,  search:false, fieldset:'fieldset2'},
        {name:'create_time',       index:'create_time',       jsonmap:'create_time',       width:150, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'operator_sim1',     index:'operator_sim1',     jsonmap:'operator_sim1',     width:100, align:'center', hidden:true,  search:false, sortable:false, fieldset:'fieldset3'},
        {name:'operator_sim2',     index:'operator_sim2',     jsonmap:'operator_sim2',     width:100, align:'center', hidden:true,  search:false, sortable:false, fieldset:'fieldset4'},
        {name:'vpn_num',           index:'vpn_num',           jsonmap:'vpn_num',           width:100, align:'center', hidden:true,  search:false, sortable:false, classes:'td_link_vpn_num', group:'runinfo'},
        {name:'cpu_usage',         index:'cpu_usage',         jsonmap:'cpu_usage',         width:100, align:'center', hidden:true,  search:false, sortable:false, group:'runinfo'},
        {name:'mem_usage',         index:'mem_usage',         jsonmap:'mem_usage',         width:100, align:'center', hidden:true,  search:false, sortable:false, group:'runinfo'},
        {name:'storage_usage',     index:'storage_usage',     jsonmap:'storage_usage',     width:100, align:'center', hidden:true,  search:false, sortable:false, group:'runinfo'},
        {name:'wifi_ssid',         index:'wifi_ssid',         jsonmap:'wifi_ssid',         width:100, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'host_sn',           index:'host_sn',           jsonmap:'host_sn',           width:100, align:'center', hidden:true,  search:false, group:'basic'},
        {name:'lac_cellid',        index:'lac_cellid',        jsonmap:'lac_cellid',        width:150, align:'center', hidden:true,  search:false, sortable:false, group:'lbs'},
        {name:'ap_mac',            index:'ap_mac',            jsonmap:'ap_mac',            width:150, align:'center', hidden:true,  search:false, sortable:false, group:'lbs'},
        {name:'addr',              index:'addr',              jsonmap:'addr',              width:150, align:'center', hidden:true,  search:false, sortable:false, group:'lbs'}
    ];
    if ($.gf.oem == 'TH-M2M') {
        var str_col_model = JSON.stringify($.gf.list2ColModel);
        if (str_col_model.indexOf('"name":"tx"') == -1) {
            $.gf.list2ColNames.push('RX');
            $.gf.list2ColNames.push('TX');
            $.gf.list2ColModel.push({name:'rx',                index:'rx',                jsonmap:'rx',                width:150, align:'center', hidden:false,  search:false, sortable:false, group:'runinfo'});
            $.gf.list2ColModel.push({name:'tx',                index:'tx',                jsonmap:'tx',                width:150, align:'center', hidden:false,  search:false, sortable:false, group:'runinfo'});
        }
    }
    var colModel = JSON.parse(localStorage.getItem('jklb_list2_colModel'));
    if (colModel && colModel.length != $.gf.list2ColNames.length){
        colModel = null;
        localStorage.removeItem('jklb_list2_colModel');
    }
    $.gf.list2LoadData = null;
    $.gf.list2ColModel = colModel || $.gf.list2ColModel;
    /**
     * [list2SaveColumns description]
     * @param  {string} t 默认为undefined，t = showCol表示显示所有列，t = hideCol表示隐藏所有列
     */
    $.gf.list2SaveColumns = function(t){
        var colModel = $("#list2").jqGrid('getGridParam','colModel'), arr = [];
        if (typeof t != 'undefined'){
            var cols = [];
            for (var j=0; j<$.gf.list2ColModel.length; j++){
                cols.push($.gf.list2ColModel[j].name);
            }
            $('#list2').jqGrid(t, cols);
        }
        for (var i=0; i<colModel.length; i++){
            if (typeof colModel[i].index != 'undefined'){
                arr.push(colModel[i]);
            }
        }
        localStorage.setItem('jklb_list2_colModel', JSON.stringify(arr));
    };

    // 添加远程通道路由
    $.gf.addRouting = function() {
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0) {
            $.notice(1, $lang.VAR_MSG_SELECT_TERM);
            return;
        } else if (sns.length>1) {
            $.notice(1, $lang.ONLY_SELECT_ONE_DEVICE);
            return;
        }
        ajax(tpurl('Term','n2nRouteradd'), {sns:sns[0]}, function(msg){
            if (msg.status == 0) {
                var socket = new WebSocket("ws://127.0.0.1:31517");
                socket.onopen = function(event) {
                    socket.send(msg.data);
                }
                socket.onmessage = function(event) {
                    var res = event.data;
                    if (res == "ret=0") {
                        $.notice(0, $lang.OPERATION_SUCCESS);
                    } else {
                        $.notice(-1, $lang.VAR_CMD_SEND_FAILED);
                    }
                }
                socket.onclose = function(event) {
                    // console.log("WebSocketClosed!");
                }
                socket.onerror = function(event) {
                    if (event.target.readyState == 3) {
                        $.notice(-1, $lang.VAR_CMD_SEND_FAILED+","+$lang.RC_SZ_N2N);
                    }
                }
            } else {
                $.notice(msg);
            }
        });
    }

    $.gf.openRwcs2 = function(url) {
        if ($.gf.tp.task_type == 'configSet') {
            // 针对修改参数任务，因为每种设备的参数界面是不同的，所以需要先查询term_model
            ajax(tpurl('Term', 'checkUniqueModel'), {act:$.gf.tp.dest_type, term_list:$.gf.tp.term_list, gids:$.gf.tp.gids}, function(msg) {
                if (msg.status == 0) {
                    var t = get_params_type(msg.data);
                    $.gf.tp.params_type = t;
                    if (t == 'router') {
                        ajax(tpurl('Term', 'loadTermParams'), {params_type:t, sn: $.gf.tp.dest_type == 'term' && $.gf.tp.term_list.indexOf(',') == -1 ? $.gf.tp.term_list : '0'}, function(msg) {
                            $('#csModal .modal-body').html('<iframe id="ifm_params" src="'+msg.data+'" style="width:100%; height:500px; border:1px dotted #b6bbbf;"></iframe');
                            localStorage.removeItem("parameter_list");
                            $('#csModal').modal('show');
                        });
                    } else {
                        $('#rwcs2Modal').modal({remote: url+'&params_type='+$.gf.tp.params_type});
                    }
                } else {
                    $.notice(msg);
                }
            });
            return;
        }
        $('#rwcs2Modal').modal({remote: url});
    }

    $(function(){
        //保存查询条件(分组，搜索值)
        if ($.gf.prev_page > 0){
            $.gf.jklb_gid = localStorage.getItem('jklb_gid');
            $.gf.jklb_gname = localStorage.getItem('jklb_gname');
            $.gf.jklb_searchString = localStorage.getItem('jklb_searchString');
            $.gf.jklb_page = localStorage.getItem('jklb_page');
            $.gf.jklb_rowNum = localStorage.getItem('jklb_rowNum');
            //赋值
            if ($.gf.jklb_gid){
                $.gf.gid = $.gf.jklb_gid;
                $('#change_gid span').attr('data-id', $.gf.jklb_gid).html($.gf.jklb_gname);
            }
            if ($.gf.jklb_searchString){
                $('input[name=searchString]').val($.gf.jklb_searchString);
            }
        }

        $('#list2').jqGrid({
            url: tpurl('Term','loadTermData'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames:$.gf.list2ColNames,
            colModel:$.gf.list2ColModel,
            pager: '#pager2',
            rowNum: $.gf.jklb_rowNum || $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'status',    //初始化的时候排序的字段
            sortorder: 'desc',  //排序方式,可选desc,asc
            viewrecords: true, //定义是否要显示总记录数
            autowidth: true,
            shrinkToFit: false,
            autoScroll: true,
            height: $.gf.sm_screen ? 350 : $('.wrapper').height()-455,
            multiselect: true,
            multiselectWidth: 30,
            page: $.gf.jklb_page || 1, //起始页码
            pagerpos: 'center', //分页栏位置
            pgbuttons: true, //是否显示翻页按钮
            pginput: true, //是否显示翻页输入框
            postData: {
                gid: $.gf.jklb_gid || -10,
                searchString: $.gf.jklb_searchString || '',
                searchType: 'term'
            },
            rownumbers: true,
            rownumWidth: 55,
            jsonReader: {repeatitems: false},
            onRightClickRow: function(sn, iRow, iCol, e){
                if (iCol == 5) {
                    e.preventDefault();
                    window.prompt('Ctrl+C '+$lang.COPY_SN, sn);
                    return false;
                }
            },
            loadComplete: function(xhr){
                //jqgrid重载后，更新term_num
                // $('#term_num').html(xhr.records);
                $.gf.list2LoadData = xhr.rows;

                //更新统计信息
                $('#info_box_online').html(xhr.online);
                $('#info_box_total').html(xhr.records);
                var a = parseFloat(xhr.online), b = parseFloat(xhr.records), c = b > 0 ? a/b : 0;
                $('#info_box_online_rates').html((c > 0 ? (c*100).toFixed(1) : c) + '%');

                //跳转到终端详情
                $('td.td_link').on('click',function(){
                    $("#list2").jqGrid('resetSelection');
                    localStorage.setItem('jklb_gid', $.gf.gid);
                    localStorage.setItem('jklb_gname', $('#change_gid span').html());
                    localStorage.setItem('jklb_searchString', $('input[name=searchString]').val());
                    localStorage.setItem('jklb_page', $('#list2').getGridParam('page'));
                    localStorage.setItem('jklb_rowNum', $('#list2').getGridParam('rowNum'));
                    location.href = tpurl('Term','sbxq','sn='+$(this).parent().attr('id'));
                });

                //跳转到远程通道
                $('td.td_link_vc').on('click',function(){
                    $("#list2").jqGrid('resetSelection');
                    if ($(this).html() == '&nbsp;') return;
                    window.open('http://'+$(this).html());
                });

                //跳转到VPN列表
                $('td.td_link_vpn_num').on('click',function(){
                    $("#list2").jqGrid('resetSelection');
                    var sn = $(this).parent().attr('id');
                    $('#gridVPN').attr('data-sn', sn).modal({
                        position: 'fit',
                        moveable: true
                    });
                });
            }
        });

        /*创建jqGrid的操作按钮容器*/
        /*可以控制界面上增删改查的按钮是否显示*/
        // $('#list2').jqGrid('navGrid', '#pager2', {edit:false, add:false, del:false});

        // 设置jqgrid的宽度
        // setTimeout(function(){jqgrid_set_width($('#list2'),$('.jqgrid_c'));},300);
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'),$('.jqgrid_c'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'),$('.jqgrid_c'));
            }, 300);
        });

        $('a.addTerm').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=term_add&gid='+$.gf.gid)
            });
        });

        $('a.editTerm').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow');
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            if (sns.length > 1){
                $.notice(1,$lang.ONLY_SELECT_ONE_DEVICE);
                return;
            }
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=term_edit&sn='+sns[0])
            });
        });

        $('a.deleteTerm').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow'), info = '';
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            if (sns.length == 1){
                var row = $('#list2').getRowData(sns[0]);
                info = $lang.VAR_TERM_DEL_CONFIRM.replace('%s', ' ('+row.sn+')');
            }else{
                info = $lang.VAR_TERM_BATCH_DEL_CONFIRM.replace('%s', sns.length);
            }
            $.confirm(info, function(){
                ajax(tpurl('Term','termDel'), {sns:sns.join(',')}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        $('a.editGroup').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow');
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=term_edit_group&gid='+$.gf.gid+'&sns='+sns.join(','))
            });
        });

        $('a.cleanRunInfo').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow');
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            $.confirm($lang.CLEAN_RUN_INFO_CONFIRM, function(){
                var div = layer.load(1, {
                  shade: [0.1,'#fff']
                });
                ajax(tpurl('Term','cleanRunInfo'), {sns:sns.join(',')}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                },'',function(){
                    layer.close(div);
                });
            });
        });

        $('a.multiSetPos').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow');
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=terms_set_pos&sns='+sns.join(','))
            });
        });

        //模态框关闭时清除内容
        $("#myLgModal,#rwcs2Modal,#rwcs3Modal,#ztreeModal2").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        $('li.rwcs,button.rwcs').click(function() {
            //$.gf.tp用来存储远程任务参数，可在弹出框中使用
            $.gf.tp = {
              task_type: $(this).attr('data-type'),
              task_name: $(this).find('a').html(),
              check_model: $(this).attr('data-check-model'),   //此任务是否需要检查设备是否支持
              enable_model: $(this).attr('data-enable-model'), //此任务支持的设备型号列表
            };
            var rwcs_page = $(this).attr('data-rwcs-page'), term_list = $('#list2').jqGrid('getGridParam','selarrrow');
            if ($.inArray($.gf.tp.task_type, ['cfgFileUpload', 'catchPackage', 'dataTrans', 'interfaceSet']) != -1) {
                if (term_list.length == 0) {
                    $.notice(1, $lang.VAR_MSG_SELECT_TERM);
                    return;
                } else if (term_list.length > 1) {
                    $.notice(1, $lang.ONLY_SELECT_ONE_DEVICE);
                    return;
                }
            }
            if (term_list.length == 0) {
                $('#rwcs1Modal h4').html($.gf.tp.task_name);
                $('#rwcs1Modal select').val('term');
                $('#rwcs1Modal').attr('data-rwcs-page', rwcs_page).modal();
                return;
            }

            if($.gf.tp.task_type == 'interfaceSet' && term_list.length == 1){
                var sns = $('#list2').jqGrid('getGridParam','selarrrow');
                $.gf.openRwcs2(tpurl('Term', 'getModalHtml', 'tpl_id='+rwcs_page + '&sn='+sns[0]));
                return;
            }

            if($.gf.tp.task_type == 'relayControl' && term_list.length == 1){
                var sns = $('#list2').jqGrid('getGridParam','selarrrow');
                $.gf.tp.dest_type = 'term';
                $.gf.tp.term_list = term_list.join(',');
                $.gf.openRwcs2(tpurl('Term', 'getModalHtml', 'tpl_id='+rwcs_page + '&sn='+sns[0]));
                return;
            }

            $.gf.tp.dest_type = 'term';
            $.gf.tp.term_list = term_list.join(',');
            $.gf.openRwcs2(tpurl('Term', 'getModalHtml', 'tpl_id='+rwcs_page));
        });

        // 任务参数:下一步
        $('#rwcs1Modal .btn-task-next').click(function() {
            var term_list = $('#list2').jqGrid('getGridParam','selarrrow'), dest_type = $('#rwcs1Modal select').val();
            if (dest_type == 'term' && term_list.length == 0) {
                $.notice(1, $lang.VAR_MSG_SELECT_TERM);
                return;
            }
            $.gf.tp.dest_type = dest_type;
            $.gf.tp.term_list = term_list.join(',');
            $.gf.tp.gids = $('#change_gid span').attr('data-id');
            $('#rwcs1Modal').modal('hide');
            $.gf.openRwcs2(tpurl('Term', 'getModalHtml', 'tpl_id='+$('#rwcs1Modal').attr('data-rwcs-page')));
        });

        // router参数界面: 加入此页面参数
        $('#csModal .btn-save-params').click(function() {
            var ret = $('#ifm_params')[0].contentWindow.save();
            if (!ret) return;
            var parameter_list = localStorage.getItem('parameter_list');
            parameter_list = parameter_list ? JSON.parse(parameter_list) : {};
            parameter_list[$('#ifm_params')[0].contentWindow.current] = ret;
            localStorage.setItem('parameter_list', JSON.stringify(parameter_list));
            $.notice(2, $lang.MODIFY_QUEUE);
        });

        // router参数界面:下一步
        $('#csModal .btn-submit-params').click(function() {
            var parameter_list = localStorage.getItem('parameter_list');
            var names = [], vals = [];
            if (parameter_list) {
                parameter_list = JSON.parse(parameter_list);
                for (var page in parameter_list) {
                    names.push(parameter_list[page].names.join(','));
                    vals.push(parameter_list[page].vals.join('{###}'));
                }
                var params = {
                    names: names.join(','),
                    vals: vals.join('{###}')
                }
            } else {
                var ret = $('#ifm_params')[0].contentWindow.save();
                if (!ret) return;
                var params = {
                    names: ret.names.join(','),
                    vals: ret.vals.join('{###}')
                }
            }
            $.gf.tp.params = params;
            $('#csModal').modal('hide');
            $('#rwcs3Modal').modal({remote: tpurl('Term', 'getModalHtml', 'tpl_id=params_config_set_time')});
        });

        //router参数界面模态框关闭时清除内容
        $("#csModal").on("hidden.bs.modal", function(){
            localStorage.removeItem("parameter_list");
            // console.log(localStorage.getItem('parameter_list'));
        });

        // 打开导出报表窗口
        $('ul.export-excel li a').click(function(){
            var act = $(this).attr('data-act'), t = $(this).html();
            if (act == 'oneNetHistory') {
                $.gf.oneNetHistory();
                return;
            }
            $('#h4_export').html($lang.VAR_EXPORT + '：' + t);
            $.gf.initExportFluxTimeRange(0);
            $('#export_act').val('term');
            $('#export_sim_num').val('0');
            $('#export_type').val('cpu');
            $('#fm_export input[name=set_type]:eq(0)').prop('checked', true);
            act == 'exportTermFlux'         ? $('#fm_export div.radios').show() : $('#fm_export div.radios').hide();
            act == 'exportTerm'             ? $('#fm_export div.ranges').hide() : $('#fm_export div.ranges').show();
            act == 'exportCPUMemoryStorage' ? $('#fm_export div.types').show() : $('#fm_export div.types').hide();
            act != 'exportTerm' && act != 'exportNetChange' && act != 'exportOfflineRate' && act != 'exportCPUMemoryStorage' ? $('#fm_export div.sims').show() : $('#fm_export div.sims').hide();
            $('#exportFluxModal').modal({
                position: 'fit',
                moveable: true
            }).attr('data-act', act);
        });

        // OneNet数据上报记录
        $.gf.oneNetHistory = function() {
            var sns = $('#list2').jqGrid('getGridParam','selarrrow');
            if (sns.length == 0) {
                $.notice(1, $lang.VAR_MSG_SELECT_TERM);
                return;
            } else if (sns.length>1) {
                $.notice(1, $lang.ONLY_SELECT_ONE_DEVICE);
                return;
            }
            $('#gridOneNet').attr('data-sn', sns[0]).modal({
                position: 'fit',
                moveable: true
            });
        };

        // 开始导出excel
        $.gf.submit_export = function() {
            var act = $('#exportFluxModal').attr('data-act'),
                dest = $('#export_act').val(),
                sns = $('#list2').jqGrid('getGridParam','selarrrow'),
                type = $('#fm_export input[name=set_type]:checked').val(),
                t = $('#exportFluxTimeRange').val().split(' - ');
            if (dest == 'term' && sns.length == 0) {
                $.notice(1, $lang.VAR_MSG_SELECT_TERM);
                return;
            }
            var params = {
                dest: dest,
                gid: $('#change_gid span').attr('data-id'),
                term_list: sns.join(','),
                type: type,
                export_type: $('#export_type').val(),
                startdate: t[0].replace(/\-/g, '') + (type == 0 ? '' : '01'),
                enddate: t[1].replace(/\-/g, '') + (type == 0 ? '' : '31'),
                sim: $('#export_sim_num').val()
            };
            getExcelData(tpurl('Term',act), params);
            $('#exportFluxModal').modal('hide');
        };

        // 初始化时间范围控件
        $.gf.initExportFluxTimeRange = function(type){
            var options = {
                "locale": {
                    "format": type == 0 ? "YYYY-MM-DD" : "YYYY-MM",
                    "daysOfWeek": $lang.VAR_WEEK_ARR,
                    "monthNames": $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(',')
                },
                alwaysShowCalendars: true,
                showCustomRangeLabel: false,
                startDate: moment().startOf('month'),
                endDate: moment(),
                maxDate: moment(),
                autoApply: true
            };
            $('#exportFluxTimeRange').daterangepicker(options);
        };

        // 切换时间范围控件的显示(日范围、月范围)
        $('#fm_export input[name=set_type]').on('change',function(){
            $.gf.initExportFluxTimeRange($(this).val());
        });

        //Reset cell/wifi location
        $('a.refreshCellLocation, a.refreshWifiLocation').click(function(){
            var sns = $('#list2').jqGrid('getGridParam','selarrrow'), type = $(this).attr('data-type'), act = $(this).attr('data-act');
            if (sns.length == 0){
                $.notice(1,$lang.VAR_MSG_SELECT_TERM);
                return;
            }
            $.confirm($lang.OPERATE_CONFIRM, function(){
                ajax(tpurl('Term', act), {sns:sns.join(','),type:type}, function(msg){
                    $.notice(msg);
                });
            });
        });

        //Refresh grid
        $('#btn_refresh').click(function(){
            $("#list2").trigger('reloadGrid');
        });

        //Set grid columns
        $('#btn_set_columns').click(function(){
            var colNames = $("#list2").jqGrid('getGridParam','colNames');
            var colModel = $("#list2").jqGrid('getGridParam','colModel');
            var groups = {basic:'', runinfo:'', lbs:'', vpn:''}, fieldsets = {fieldset1:'', fieldset2:'', fieldset3:'', fieldset4:''};
            for (var i=0,tmp=''; i<colNames.length; i++){
                if (typeof colModel[i].index == 'undefined'){
                    continue;
                }
                tmp = '<li class="li_column"><input type="checkbox" name="columns" value="'+colModel[i].name+'" '+(colModel[i].hidden?'':'checked')+'><span>'+colNames[i]+'</span></li>';
                if (typeof colModel[i].group != 'undefined'){
                    groups[colModel[i].group] += tmp
                } else if (typeof colModel[i].fieldset != 'undefined') {
                    fieldsets[colModel[i].fieldset] += tmp;
                }
            }
            for (var x in groups){
                $('#ul_columns_'+x+' ul:eq(0)').html(groups[x]);
            }
            for (var x in fieldsets){
                $('#ul_columns_'+x+' ul:eq(0)').html(fieldsets[x]);
            }
            $('#modal_fm_grid_columns li span').click(function(){
                $(this).prev().click();
            });
            $('#modal_fm_grid_columns input[name=columns]').change(function(){
                $("#list2").jqGrid($(this).is(':checked')?'showCol':'hideCol', $(this).val());
                $.gf.list2SaveColumns();
            });
            $('#gridColumnsModal').modal({
                position: 'fit',
                moveable: true
            });
        });
        $('button.grid_columns_reset').click(function(){
            localStorage && localStorage.removeItem('jklb_list2_colModel');
            location.reload();
        });
        $('button.grid_columns_checkall').click(function(){
            $('#modal_fm_grid_columns input[name=columns]').prop('checked',true);
            $.gf.list2SaveColumns('showCol');
        });
        $('button.grid_columns_uncheckall').click(function(){
            $('#modal_fm_grid_columns input[name=columns]').prop('checked',false);
            $.gf.list2SaveColumns('hideCol');
        });

        //任务查看
        $('#btn_rwxq').click(function(){
            $('#gridviewTask').modal({
                position: 'fit',
                moveable: true
            });
        });
		$("#gridviewTask").on('shown.bs.modal', function() {
			if ($(this).attr('data-jqgrid-type') == '0'){
				$(this).attr('data-jqgrid-type','1');
				$('#list3').jqGrid({
					url: tpurl('Taskmgr','loadTaskDetail'),
					datatype: 'json',
					mtype: 'post',
					colNames: [$lang.VAR_SN2, $lang.VAR_CMD_STATUS, $lang.VAR_TASK_SEND_TIME, $lang.VAR_TASK_FINISH_TIME, $lang.VAR_TASK_FINISH_TIME2, $lang.VAR_TIPS_DETAIL_INFO, $lang.VAR_OPERATION],
					colModel:[
						{name:'sn',           index:'sn',           jsonmap:'sn',          width:100,  align:'center', hidden:false, search:false},
						{name:'status',       index:'status',       jsonmap:'status',      width:60,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
							return get_task_status_color(rowObject);
						}},
						{name:'send_time',    index:'send_time',    jsonmap:'send_time',   width:100,  align:'center', hidden:false, search:false},
						{name:'recv_time',    index:'recv_time',    jsonmap:'recv_time',   width:100,  align:'center', hidden:false, search:false},
						{name:'finish_time',  index:'finish_time',  jsonmap:'finish_time', width:60,   align:'center', hidden:false, search:false, sortable:false},
						{name:'progress',     index:'progress',     jsonmap:'progress',    width:80,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
							return v==-1 ? '':'<div class="progress" style="margin-bottom:0;position:relative;"><div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="'+v+'" aria-valuemin="0" aria-valuemax="100" style="width: '+v+'%">\
							</div><span class="progress-val">'+v+'%</span></div>';
						}},
						{name:'act',          index:'act',          jsonmap:'act',         width:50,   align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
							return ($.gf.cmd == 'packet_cap' || $.gf.cmd == 'upload_cfg') ? '<a href="'+tpurl('Taskmgr','cpFilesDownload','id='+rowObject.id+'&sn='+rowObject.sn)+'" target="_blank" title="'+$lang.VAR_CP_DOWNLOAD+'"><i class="fa fa-download"></i></a>' : '';
						}}
					],
					pager: '#pager3',
					rowNum: 10,
					rowList: [10, 20, 30, 40, 50, 100],
					sortname: 'sn',
					sortorder: 'asc',
					viewrecords: true,
                    width: $('#gridviewTask div.nav-tabs-custom').width()-20,
					height: 'auto',
					page: 1,
					pagerpos: 'center', //分页栏位置
					pgbuttons: true, //是否显示翻页按钮
					pginput: true, //是否显示翻页输入框
					postData: {searchType:'term_task_detail'},
					rownumbers: true,
					rownumWidth: 30,
					jsonReader: {repeatitems: false},
					loadComplete: function(xhr){
                        if (!xhr.rows) return;
						var data = xhr.rows[0];
						$('#is_enable_text').val(data['is_enable_text']);
						$('#cmd_text').val(data['cmd_text']);
						$('#create_time').val(data['create_time']);
						$('#start_time').val(data['start_time']);
						$('#end_time').val(data['end_time']);
						$('#rwcs_value').val(data['value']);
					}
				});
			} else {
                $('#gridviewTask ul.nav-tabs li:eq(0) a').click();
				$('#list3').setGridParam({page:1}).trigger('reloadGrid');
			}
		});

        // 查看VPN数量
        $("#gridVPN").on('shown.bs.modal', function() {
            if ($(this).attr('data-jqgrid-type') == '0'){
                $(this).attr('data-jqgrid-type','1');
                $('#list4').jqGrid({
                    url: tpurl('Term','loadVpnData'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: [$lang.VAR_TYPE, $lang.VAR_NAME, $lang.VAR_TERM_STATUS, 'IP', $lang.IPSEC_RECV, $lang.IPSEC_SEND, $lang.CONNECT_TIME, $lang.VAR_LAST_LOGIN, $lang.ONLINE_DURATION, $lang.VAR_OPERATION, $lang.VAR_NOTE],
                    colModel:[
                        {name:'vpn_type',     index:'vpn_type',     jsonmap:'vpn_type',    width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'vpn_name',     index:'vpn_name',     jsonmap:'vpn_name',    width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'status',       index:'status',       jsonmap:'status',      width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'ip',           index:'ip',           jsonmap:'ip',          width:100,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v){
                            return !v || v == '0.0.0.0' ? '--' : '<a href="http://'+v+'" target="_blank" style="color:green; text-decoration:underline;">'+v+'</a>';
                        }},
                        {name:'recv_flux',    index:'recv_flux',    jsonmap:'recv_flux',   width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'send_flux',    index:'send_flux',    jsonmap:'send_flux',   width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'login_time',   index:'login_time',   jsonmap:'login_time',  width:150,  align:'center', hidden:true,  search:false, sortable:false},
                        {name:'last_time',    index:'last_time',    jsonmap:'last_time',   width:150,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'online_time',  index:'online_time',  jsonmap:'online_time', width:100,  align:'center', hidden:true,  search:false, sortable:false},
                        {name:'act',          index:'act',          jsonmap:'act',         width:120,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, opt, row){
                            if (row.vpn_type != 'n2n' && row.vpn_type != 'zerotier') return '';
                            var cls1 = row.status.indexOf('term_on') != -1 ? 'disabled' : '', cls2 = cls1 == 'disabled' ? '' : 'disabled';
                            return '<button '+cls1+' class="btn btn-xs btn-success" onclick="javascript:$.gf.vpnAct(\''+row.sn+'\', \''+row.vpn_type+'\', \''+row.vpn_name+'\', \'vpn_connect\');">'+$lang.VAR_CONNECT+'</button>&nbsp;'+
                            '<button '+cls2+' class="btn btn-xs btn-warning" onclick="javascript:$.gf.vpnAct(\''+row.sn+'\', \''+row.vpn_type+'\', \''+row.vpn_name+'\', \'vpn_disconnect\');">'+$lang.VAR_DISCONNECT+'</button>';
                        }},
                        {name:'ext_info',     index:'ext_info',     jsonmap:'ext_info',    width:100,  align:'center', hidden:false, search:false, sortable:false}
                    ],
                    pager: '#pager4',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'last_time',
                    sortorder: 'DESC',
                    viewrecords: true,
                    width: $('#gridVPN div.modal-body').width(),
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {searchType:'term_virtual_channel', sn:$('#gridVPN').attr('data-sn')},
                    rownumbers: true,
                    rownumWidth: 30,
                    jsonReader: {repeatitems: false}
                });
            } else {
                $('#list4').setGridParam({page:1, postData:{
                    searchType: 'term_virtual_channel',
                    sn: $('#gridVPN').attr('data-sn')
                }}).trigger('reloadGrid');
            }
        });

        // 查看onenet上报记录
        $("#gridOneNet").on('shown.bs.modal', function() {
            if ($(this).attr('data-jqgrid-type') == '0'){
                $(this).attr('data-jqgrid-type','1');
                $('#list5').jqGrid({
                    url: tpurl('Term','loadOneNetData'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: [$lang.VAR_SN2, $lang.DEVICE_ID, $lang.VAR_SWV, 'lac', 'cellid', 'imei', 'imsi', 'iccid', $lang.VAR_DEVICE_URL_REPORT_TIME],
                    colModel:[
                        {name:'sn',             index:'sn',             jsonmap:'sn',           width:100,  align:'center', hidden:false, search:false, sortable:false},
                        {name:'dev_id',         index:'dev_id',         jsonmap:'dev_id',       width:100,  align:'center', hidden:false, search:false, sortable:true},
                        {name:'sw_version',     index:'sw_version',     jsonmap:'sw_version',   width:100,  align:'center', hidden:false, search:false, sortable:true},
                        {name:'lac',            index:'lac',            jsonmap:'lac',          width:80,   align:'center', hidden:false, search:false, sortable:true},
                        {name:'cellid',         index:'cellid',         jsonmap:'cellid',       width:80,   align:'center', hidden:false, search:false, sortable:true},
                        {name:'imei',           index:'imei',           jsonmap:'imei',         width:140,  align:'center', hidden:false, search:false, sortable:true},
                        {name:'imsi',           index:'imsi',           jsonmap:'imsi',         width:140,  align:'center', hidden:false, search:false, sortable:true},
                        {name:'iccid',          index:'iccid',          jsonmap:'iccid',        width:170,  align:'center', hidden:false, search:false, sortable:true},
                        {name:'report_time',    index:'report_time',    jsonmap:'report_time',  width:150,  align:'center', hidden:false, search:false, sortable:true}
                    ],
                    pager: '#pager5',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'report_time',
                    sortorder: 'DESC',
                    viewrecords: true,
                    width: $('#gridOneNet div.modal-body').width(),
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {searchType:'oem_onenet_report', sn:$('#gridOneNet').attr('data-sn')},
                    rownumbers: true,
                    rownumWidth: 30,
                    jsonReader: {repeatitems: false}
                });
            } else {
                $('#list5').setGridParam({page:1, postData:{
                    searchType: 'oem_onenet_report',
                    sn: $('#gridOneNet').attr('data-sn')
                }}).trigger('reloadGrid');
            }
        });

        // Change group
        $('#change_gid').on('click', function(){
            $('#ztreeModal2').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=term_select_group&from=jklb&current_gid='+$(this).find('span').attr('data-id'))
            });
        });

        // Get statics info
        $.gf.getStaticsInfo();

        //search router
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm'), ss = $.trim(p.searchString);
            /*if (ss != '' && $.gf.gid != -10){
                $.gf.gid = -10;
                $('#change_gid span').attr('data-id', $.gf.gid).html($lang.VAR_ALL_DEVICE);
            }*/
            $('#list2').setGridParam({page:1, postData:{
                gid: $.gf.gid,
                searchType:'term',
                searchString:ss
            }}).trigger('reloadGrid');
            return false;
        });

        //F5 clean localStorage
        document.onkeydown = function (e) {
            e = e || window.event;
            if ((e.ctrlKey && e.keyCode == 82) || e.keyCode == 116) {
                localStorage.removeItem('jklb_gid');
                localStorage.removeItem('jklb_gname');
                localStorage.removeItem('jklb_searchString');
                localStorage.removeItem('jklb_page');
                localStorage.removeItem('jklb_rowNum');
            }
        }

        //Auto refresh list2
        window.setInterval(function(){
            var sns = $('#list2').jqGrid('getDataIDs');
            if (sns.length == 0){
                return;
            }
            ajax(tpurl('Term','loadTermData'), {sns:sns.join(','), page:1, rows:100, sidx:'term.sn', sord:'asc'}, function(msg){
                for (var i=0; i<msg.rows.length; i++){
                    $('#list2').setRowData(msg.rows[i].sn, msg.rows[i]);
                }
            });
        }, 60000);
    });
})(jQuery);