(function($){
    $(document).ready(function(){
        //任务列表
        $('#list2').jqGrid({
            url: tpurl('Syscfg','loadUserLoginRecord'),
            datatype: 'json', //请求数据返回的类型。可选json,xml,txt
            mtype: 'post', //向后台请求数据的ajax的类型。可选post,get
            colNames: ['id', $lang.VAR_DEVICE_LOGIN_TIME, 'IP', $lang.VAR_POSITION],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:150,  align:'center', hidden:false, search:false},
                {name:'ip',           index:'ip',           jsonmap:'ip',          width:150,  align:'center', hidden:false, search:false},
                {name:'position',     index:'position',     jsonmap:'position',    width:150,  align:'center', hidden:false, search:false, sortable:false}
            ],
            pager: '#pager2', //表格页脚的占位符(一般是div)的id
            rowNum: 10,//一页显示多少条
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'create_time',    //初始化的时候排序的字段
            sortorder: 'desc',  //排序方式,可选desc,asc
            viewrecords: true, //定义是否要显示总记录数
            // caption: '表格的标题名字',
            // width: $('.jqgrid_c').width()-20,
            autowidth: true,
            // shrinkToFit: false,
            // autoheight: true,
            height: 'auto',
            multiselect: false,
            multiselectWidth: 30,
            page: 1, //起始页码
            pagerpos: 'center', //分页栏位置
            pgbuttons: true, //是否显示翻页按钮
            pginput: true, //是否显示翻页输入框
            postData: {sn: $.gf.sn}, //额外参数
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

        //模态框关闭时清除内容
        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Edit head
        $('.th-head-container').click(function(){
            $('#modal_fm2').get(0).reset();
            $('#myLgModal2').modal({
                position: 'fit',
                moveable: true
            });
        });

        //Edit pass
        $('#btn_edit_pass').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Syscfg','getModalHtml','tpl_id=usr_edit_pass')
            });
        });

        //Edit basic info
        $('#btn_edit_basic').click(function(){
            var email = $('.td-input input[name=email]').val();
            if ($.gf.email_reg && !$.gf.email_reg.test(email)) {
                $.notice(1, $lang.EMAIL_INVALID);
                return;
            }
            ajax(tpurl('Syscfg','editMyInfo'), {
                email: email,
                sim: $('.td-input input[name=sim]').val(),
                info: $('.td-input input[name=info]').val(),
            }, function(msg){
                $.notice(msg);
            });
        });

        //Bootstrap fileinput
        $('#filedata').fileinput({
            language: $.gf.lang,
            uploadUrl: tpurl('Syscfg','editHeadImg'),
            showUpload: false,
            showRemove: true,
            showPreview: false,
            dropZoneEnabled: false,
            showCaption: true,
            msgPlaceholder: $lang.SELECT_IMG,
            allowedFileExtensions:  ['jpg', 'png', 'gif', 'jpeg'],
            maxFileSize: 10*1024, //KB
            maxFileCount: 1,
            uploadExtraData: {}
        }).on("fileuploaded", function (event, data, previewId, index){
            $.notice(data.response);
            if (data.response.status == 0){
                $('#myLgModal2').modal('hide');
                var o = $('img.user-head-img-edit'), src = o.attr('src').split('headimg');
                o.attr('src', src[0]+'headimg/'+data.response.data);
            }
        });
        $('button.save_edit_headimg').click(function(){
            if ($('#filedata').fileinput('getFilesCount') == 0){
                $.notice(1,$lang.SELECT_IMG);
                return;
            }
            /*$('#filedata').fileinput('refresh', {
                uploadExtraData: {}
            });*/
            $('#filedata').fileinput('upload');
        });
    });
})(jQuery);