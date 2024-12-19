(function($){
    $(document).ready(function(){
        $('#btn_confirm').click(function(){
            var params = $.gf.sp.getParams();
            if (!params){
                return;
            }
            $.confirm($lang.OPERATE_CONFIRM, function(){
                ajax(tpurl('Syscfg','editSystemParams'), {params:params}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0) {
                        $.gf.sp.resetParams();
                    }
                });
            });
        });

        $('#list1').jqGrid({
            url: tpurl('Syscfg','loadLogData'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames: ['id', $lang.OPERATOR, 'IP', $lang.VAR_CMD_NAME, $lang.VAR_CMD_CREATETIME],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:100,  align:'center', hidden:true,  search:false, key:true},
                {name:'username',     index:'username',     jsonmap:'username',    width:100,  align:'center', hidden:false, search:false},
                {name:'ip',           index:'ip',           jsonmap:'ip',          width:100,  align:'center', hidden:false, search:false},
                {name:'cmd',          index:'cmd',          jsonmap:'cmd',         width:200,  align:'center', hidden:false, search:false},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:100,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager1',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'create_time',
            sortorder: 'desc',
            viewrecords: true,
            // width: $('#tab_1').width()-20,
            autowidth: true,
            // shrinkToFit: false,
            // autoheight: true,
            height: 'auto',
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {},
            rownumbers: true,
            rownumWidth: 50,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list1'),$('#tab_1'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list1'),$('#tab_1'));
            }, 300);
        });

        $('#btn_test_email').click(function(){
            if ($('#test_email').val() == ''){
                $('#test_email').focus();
                return;
            }
            layer.load(1);
            ajax(tpurl('Syscfg','emailConfigTest'), {
                email_config_host: $('#tpid_email_config_host').val(),
                email_config_ssl: $('#tpid_email_config_ssl').is(':checked') ? 1 : 0,
                email_config_port: $('#tpid_email_config_port').val(),
                email_config_account: $('#tpid_email_config_account').val(),
                email_config_password: $('#tpid_email_config_password').val(),
                email_config_from: $('#tpid_email_config_from').val(),
                email_config_smtp_auth: $('#tpid_email_config_smtp_auth').is(':checked') ? 1 : 0,
                email_config_test: $('#test_email').val()
            }, function(msg){
                $.notice(msg);
            },'',function(){
                layer.closeAll('loading');
            });
        });

        $('.btn-test-wx').click(function(){
            layer.load(1);
            ajax(tpurl('Weixin','wx_test_config'), {
                type: $(this).attr('data-type'),
                weixin_config_corpid: $('#tpid_weixin_config_corpid').val(),
                weixin_config_corpsecret: $('#tpid_weixin_config_corpsecret').val(),
                weixin_config_agentid: $('#tpid_weixin_config_agentid').val(),
                weixin_config_txl_secret: $('#tpid_weixin_config_txl_secret').val()
            }, function(msg){
                $.notice(msg);
            },'',function(){
                layer.closeAll('loading');
            });
        });

        $('.btn-upload-qrcode').click(function(){
            $('#modal_fm2').get(0).reset();
            $('#myLgModal2').modal({
                position: 'fit',
                moveable: true
            });
        });
        $('#filedata').fileinput({
            language: $.gf.lang,
            uploadUrl: tpurl('Syscfg','setQrcode'),
            showUpload: false,
            showRemove: true,
            showPreview: false,
            dropZoneEnabled: false,
            showCaption: true,
            // msgPlaceholder: '110px * 110px',
            // allowedFileExtensions:  ['trx', 'bin', 'patch'],
            maxFileSize: 10*1024, //KB
            maxFileCount: 1,
            uploadExtraData: {}
        }).on("fileuploaded", function (event, data, previewId, index){
            $.notice(data.response);
            if (data.response.status == 0){
                $('#myLgModal2').modal('hide');
                var o = $('img.img-qrcode');
                o.attr('src', o.attr('src').split('?rand=')[0]+'?rand='+Math.random());
            }
        });
        $('button.save_edit_qrcode').click(function(){
            if ($('#filedata').fileinput('getFilesCount') == 0){
                $.notice(1,$lang.SELECT_FILE);
                return;
            }
            $('#filedata').fileinput('upload');
        });

        //Load params
        $.gf.sp = new SystemParams();
        $.gf.sp.load();
        $('#btn_reload_params').click(function(){
            $.gf.sp.load();
        });
    });
})(jQuery);