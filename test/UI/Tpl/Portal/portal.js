(function($){
    $.gf.ad_detail = function(id, name){
        $('#gridFileList h4').html($lang.VAR_MENU_AD_FILE + '&nbsp;(' + name + ')');
        $('#gridFileList').attr('data-ad-id', id).modal({
            position: 'fit',
            moveable: true
        });
    }

    $.gf.ad_add = function(){
        $('#modal_fm').get(0).reset();
        $('#modal_fm input[name=ad_id]').val('');
        $('#modal_fm input[name=name]').removeAttr('disabled');
        $('#myLgModal .modal-title').html($lang.VAR_AD_ADD);
        $('#myLgModal').modal();
    }

    $.gf.ad_add_file = function(id, name){
        $('#modal_fm').get(0).reset();
        $('#modal_fm input[name=ad_id]').val(id);
        $('#modal_fm input[name=name]').attr('disabled',true).val(name);
        $('#myLgModal .modal-title').html($lang.VAR_AD_ADD_FILE + '&nbsp;(' + name + ')');
        $('#myLgModal').modal();
    }

    $.gf.ad_delete = function(id){
        $.confirm($lang.VAR_AD_DEL_CONFIRM, function(){
            ajax(tpurl('Portal','portalDelete'), {ad_id:id}, function(msg){
                $.notice(msg);
                if (msg.status == 0){
                    $.gf.tp.reload();
                }
            });
        });
    }

    $.gf.ad_delete_file = function(){
        var ids = $('#list3').jqGrid('getGridParam','selarrrow');
        if (ids.length == 0){
            $.notice(1,$lang.SELECT_FILE);
            return;
        }
        $.confirm($lang.VAR_CONFIRM_DEL_ADFILE, function(){
            ajax(tpurl('Syscfg','deleteResFile'), {ids:ids.join(','), filetype:3, ad_id:$('#gridFileList').attr('data-ad-id')}, function(msg){
                $.notice(msg);
                if (msg.status == 0){
                    $("#list3").trigger('reloadGrid');
                    $.gf.tp.reload();
                }
            });
        });
    }

    //fileinput开始上传文件
    $.gf.ad_save_file = function() {
        var p = $.serializeObject('#modal_fm');
        if (typeof p.name != 'undefined' && p.name.trim() == '') {
            $.notice(1,$lang.VAR_ENTER_AD_NAME);
            $('#modal_fm input[name=name]').focus();
            return;
        }
        if ($('#filedata').fileinput('getFilesCount') == 0){
            $.notice(1,$lang.SELECT_FILE);
            return;
        }
        $('#filedata').fileinput('refresh', {
            uploadExtraData: {name:(typeof p.name != 'undefined' ? p.name.trim():''), ad_id:p.ad_id}
        });
        $('#filedata').fileinput('upload');
    }

    $(document).ready(function(){
        $.gf.tp = $('#list1').taskpaging({
            url: tpurl('Portal','portal'),
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

        //任务查看(异常，等待)
        $("#gridFileList").on('shown.bs.modal', function() {
            if ($(this).attr('data-init') == '0'){
                $(this).attr('data-init','1');
                $('#list3').jqGrid({
                    url: tpurl('Syscfg','loadFiles'),
                    datatype: 'json',
                    mtype: 'post',
                    colNames: ['id', $lang.VAR_AD_FILENAME, $lang.VAR_AD_FILESIZE, 'MD5', $lang.VAR_AD_CREATETIME, $lang.VAR_CREATOR],
                    colModel:[
                        {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false},
                        {name:'name',         index:'name',         jsonmap:'name',        width:200,  align:'center', hidden:false, search:false},
                        {name:'filesize',     index:'filesize',     jsonmap:'filesize',    width:150,  align:'center', hidden:false, search:false},
                        {name:'md5_num',      index:'md5_num',      jsonmap:'md5_num',     width:150,  align:'center', hidden:false, search:false},
                        {name:'create_time',  index:'create_time',  jsonmap:'create_time', width:150,  align:'center', hidden:false, search:false},
                        {name:'creator',      index:'creator',      jsonmap:'creator',     width:100,  align:'center', hidden:false, search:false}
                    ],
                    pager: '#pager3',
                    rowNum: 10,
                    rowList: [10, 20, 30, 40, 50, 100],
                    sortname: 'name',
                    sortorder: 'ASC',
                    viewrecords: true,
                    width: $('#gridFileList div.modal-body').width()-20,
                    shrinkToFit: false,
                    autoScroll: true,
                    height: 'auto',
                    page: 1,
                    pagerpos: 'center',
                    pgbuttons: true,
                    pginput: true,
                    postData: {
                        searchType: 'file_list',
                        filetype: 3,
                        ad_id: $('#gridFileList').attr('data-ad-id')
                    },
                    rownumbers: true,
                    rownumWidth: 30,
                    multiselect: true,
                    jsonReader: {repeatitems: false}
                });
            }else{
                $('#list3').setGridParam({page:1, postData:{
                    searchType: 'file_list',
                    filetype: 3,
                    ad_id: $('#gridFileList').attr('data-ad-id')
                }}).trigger('reloadGrid');
            }
        });

        //Bootstrap fileinput
        $('#filedata').fileinput({
            language: $.gf.lang,
            uploadUrl: tpurl('Portal','portalAdd'),
            showPreview: false,
            showUpload: false,
            showCaption: true,
            uploadAsync: false,
            showRemove: true,
            dropZoneEnabled: false,
            msgPlaceholder: $lang.SELECT_FILE,
            uploadExtraData: {}
        }).on("filebatchuploadsuccess", function (event, data, previewId, index){
            if (data.response.status == 0){
                $('#myLgModal').modal('hide');
                $.gf.tp.reload();
                $.notice(data.response);
            } else if (data.response.status == -1) {
                $.notice(-1, data.response.info);
            } else {
                var d = data.response.data
                $.notice(-1, data.response.info+$lang.FILE_NUM+'：'+d.total+'，'+$lang.UPLOAD_FAILED+'：'+d.error+'，'+$lang.DUPLICATE_FILENAME+'：'+d.duplicate);
            }
        });
    });
})(jQuery);