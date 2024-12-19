(function($){
    $(document).ready(function(){
        $('#list2').jqGrid({
            url: tpurl('Rtu', 'loadSensorTypeData'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', $lang.VAR_NAME, $lang.SLAVE_ID, $lang.ADDR, $lang.UNIT, $lang.VALUE_TYPE, $lang.MIN, $lang.MAX, $lang.DATA_CORRECTION, $lang.VAR_CODE, $lang.TRIGGER_ALARM_LEVEL, $lang.VAR_NOTE],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                {name:'slave_id',     index:'slave_id',     jsonmap:'slave_id',    width:100,  align:'center', hidden:true,  search:false},
                {name:'addr',         index:'addr',         jsonmap:'addr',        width:100,  align:'center', hidden:false, search:false},
                {name:'unit',         index:'unit',         jsonmap:'unit',        width:100,  align:'center', hidden:false, search:false},
                {name:'value_type',   index:'value_type',   jsonmap:'value_type',  width:100,  align:'center', hidden:false, search:false},
                // {name:'value_len',    index:'value_len',    jsonmap:'value_len',   width:100,  align:'center', hidden:false, search:false},
                {name:'min',          index:'min',          jsonmap:'min',         width:100,  align:'center', hidden:false, search:false},
                {name:'max',          index:'max',          jsonmap:'max',         width:100,  align:'center', hidden:false, search:false},
                {name:'op',           index:'op',           jsonmap:'op',          width:100,  align:'center', hidden:false, search:false, sortable:false, formatter:function(v, options, rowObject){
                    return rowObject.operator == '' ? $lang.VAR_NONE : ((rowObject.operator == '*' ? $lang.OP_MULTIPLY : $lang.OP_ADD) + ' ' + rowObject.op_value);
                }},
                {name:'code',         index:'code',         jsonmap:'code',        width:100,  align:'center', hidden:false, search:false},
                {name:'warn_level',   index:'warn_level',   jsonmap:'warn_level',  width:100,  align:'center', hidden:false, search:false},
                // {name:'revised',      index:'revised',      jsonmap:'revised',     width:100,  align:'center', hidden:false, search:false},
                {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false}
            ],
            pager: '#pager2',
            rowNum: $.gf.jq_pagesize,
            rowList: [10, 15, 20, 30, 40, 50, 100],
            sortname: 'name',
            sortorder: 'asc',
            viewrecords: true,
            autowidth: true,
            shrinkToFit: true,
            autoScroll: true,
            height: $.gf.sm_screen ? 330 : $('.wrapper').height()-325,
            multiselect: true,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {searchType:'rtu_data_set'},
            rownumbers: true,
            rownumWidth: 40,
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

        //Search term group by name
        $('#search_fm').on('submit',function(){
            var p = $.serializeObject('#search_fm');
            $('#list2').setGridParam({page:1, postData:{
                searchType: 'rtu_data_set',
                searchString: p.searchString
            }}).trigger('reloadGrid');
            return false;
        });

        $("#myLgModal").on("hidden.bs.modal", function(){
            $(this).removeData("bs.modal");
            $(this).find(".modal-content").children().remove();
        });

        //Add sensor
        $('button[data-act=add]').click(function(){
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Rtu','getModalHtml','tpl_id=sensor_add')
            });
        });

        //Edit sensor
        $('button[data-act=edit]').click(function(){
            var ids = $('#list2').jqGrid('getGridParam','selarrrow');
            if (ids.length != 1){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Rtu','getModalHtml','tpl_id=sensor_edit&id='+ids[0])
            });
        });

        //Delete sensor
        $('button[data-act=del]').click(function(){
            var ids = $('#list2').jqGrid('getGridParam','selarrrow');
            if (ids.length == 0){
                $.notice(1,$lang.ONLY_SELECT_ONE);
                return;
            }
            $.confirm($lang.VAR_CONFIRM_DEL_SENSOR_N.replace('%d',ids.length), function(){
                ajax(tpurl('Rtu', 'sensorTypeDel'), {ids:ids.join(','), act:'delete'}, function(msg){
                    $.notice(msg);
                    if (msg.status == 0){
                        $("#list2").trigger('reloadGrid');
                    }
                });
            });
        });

        $('button[data-act=import]').click(function(){
            $('#modal_fm2').get(0).reset();
            $('#myLgModal2').modal({
                position: 'fit',
                moveable: true
            });
        });

        //Refresh grid
        $('button[data-act=refresh]').click(function(){
            $("#list2").trigger('reloadGrid');
        });

        //Download excel template
        $('button.download_tpl').click(function(){
            getExcelData(tpurl('Rtu', 'sensorTypeExcelDownload', 'act=download_tpl'));
        });

        //Bootstrap fileinput
        $('#filedata').fileinput({
            language: $.gf.lang,
            uploadUrl: tpurl('Rtu', 'sensorTypeImport', 'act=import'),
            showUpload: false,
            showRemove: true,
            showPreview: false,
            dropZoneEnabled: false,
            showCaption: true,
            msgPlaceholder: 'Excel',
            allowedFileExtensions:  ['xls','xlsx'],
            maxFileSize: 10*1024, //KB
            maxFileCount: 1,
            uploadExtraData: {}
        }).on("fileuploaded", function (event, data, previewId, index){
            $.notice(data.response);
            if (data.response.status == 0){
                $('#myLgModal2').modal('hide');
                $("#list2").trigger('reloadGrid');
            }
        });
        $('button.save_import').click(function(){
            if ($('#filedata').fileinput('getFilesCount') == 0){
                $.notice(1,$lang.SELECT_FILE);
                return;
            }
            $('#filedata').fileinput('upload');
        });
    });
})(jQuery);