(function($){
var set_g_width = function(){
    jqgrid_set_width($('#list2'),$('#tab_2'));
    // jqgrid_set_width($('#list2'),$('#tab_2'));
}
$(function () {
    $.gf.list2_format = function(v, options, rowObject){return v ? v : '--'};
    $.gf.colsStorageName = 'lora_list2_colModel';
    $.gf.list2Names = [$lang.VAR_TERM_STATUS, $lang.VAR_SN2, $lang.GATEWAY_SN, $lang.VAR_LAST_LOGIN, $lang.DEVICE_NAME, $lang.DETAIL_ADDR, $lang.CONTACT, $lang.TEL];
    $.gf.list2Cols = [
        {name:'status',       index:'status',       jsonmap:'status',      align:'center',  width:100,  hidden:false, search:false},
        {name:'sn',           index:'sn',           jsonmap:'sn',          align:'center',  width:150,  hidden:false, search:false, key:true},
        {name:'gateway_sn',   index:'gateway_sn',   jsonmap:'gateway_sn',  align:'center',  width:150,  hidden:true,  search:false},
        {name:'last_time',    index:'last_time',    jsonmap:'last_time',   align:'center',  width:150,  hidden:false, search:false},
        {name:'name',         index:'name',         jsonmap:'name',        align:'center',  width:150,  hidden:false, search:false},
        {name:'address',      index:'address',      jsonmap:'address',     align:'center',  width:150,  hidden:false, search:false},
        {name:'contact',      index:'contact',      jsonmap:'contact',     align:'center',  width:150,  hidden:false, search:false},
        {name:'tel',          index:'tel',          jsonmap:'tel',         align:'center',  width:150,  hidden:false, search:false}
    ];
    for (var i=0,key='',unit='',name='',col=null; i<$.gf.sensors.length; i++){
        // unit = $.gf.sensors[i].unit ? ('('+$.gf.sensors[i].unit+')') : '';
        name = $.gf.sensors[i].name;
        key = $.gf.sensors[i].slave_id + '_' + $.gf.sensors[i].addr;
        col = {
            name: key,
            index: key,
            jsonmap: key,
            align: 'center',
            width: 150,
            hidden: false,
            search: false,
            sortable: false,
            classes: 'td_link'
        };
        $.gf.list2Names.push(name);
        $.gf.list2Cols.push(col);
    }
    var lastColModel = JSON.parse(localStorage.getItem($.gf.colsStorageName));
    if (lastColModel && lastColModel.length != $.gf.list2Cols.length){
        lastColModel = null;
        localStorage.removeItem($.gf.colsStorageName);
    }

    //Formatter
    var list2CurrentCols = lastColModel || $.gf.list2Cols;
    if (list2CurrentCols){
        for (var i=0; i<list2CurrentCols.length; i++){
            if ($.inArray(list2CurrentCols[i].name, ['prjname','prjcode','appid']) != -1) {
                list2CurrentCols[i].formatter = $.gf.list2_format;
            }
        }
    }

    /**
     * [list2SaveColumns description]
     * @param  {string} t 默认为undefined，t = showCol表示显示所有列，t = hideCol表示隐藏所有列
     */
    $.gf.list2SaveColumns = function(t){
        var colModel = $("#list2").jqGrid('getGridParam','colModel'), arr = [];
        if (typeof t != 'undefined'){
            var cols = [];
            for (var j=0; j<$.gf.list2Cols.length; j++){
                cols.push($.gf.list2Cols[j].name);
            }
            $('#list2').jqGrid(t, cols);
        }
        for (var i=0; i<colModel.length; i++){
            if (typeof colModel[i].index != 'undefined'){
                arr.push(colModel[i]);
            }
        }
        localStorage.setItem($.gf.colsStorageName, JSON.stringify(arr));
    };

    $('#list2').jqGrid({
        url: tpurl('Rtu','loadLoraData'),
        datatype: 'json',
        mtype: 'post',
        colNames: $.gf.list2Names,
        colModel: list2CurrentCols,
        pager: '#pager2',
        rowNum: $.gf.jq_pagesize,
        rowList: [10, 15, 20, 30, 40, 50, 100],
        sortname: 'status',
        sortorder: 'desc',
        viewrecords: true,
        autowidth: true,
        shrinkToFit: false,
        autoScroll: true,
        height: $.gf.sm_screen ? 350 : $('.wrapper').height()-385,
        multiselect: true,
        multiselectWidth: 30,
        page: 1,
        pagerpos: 'center',
        pgbuttons: true,
        pginput: true,
        postData: {gid:-10},
        rownumbers: true,
        rownumWidth: 30,
        jsonReader: {repeatitems: false},
        onCellSelect: function(rowid,iCol,cellcontent,e){
            var t = $(e.target);
            if (t.hasClass('td_link') || t.parent().hasClass('td_link')) {
                $('#list2').jqGrid('resetSelection');
                $('#myLgModal3').modal({
                    position: 'fit',
                    moveable: true,
                    remote: tpurl('Rtu','getModalHtml','tpl_id=history_data&sn='+rowid+'&slave_id_addr='+$.gf.list2Cols[iCol-2].index)
                });
            }
        }
    });

    //对话框显示后，初始化jqgrid
    $('#myLgModal2').on('show.bs.modal', function() {
        var sn = $(this).attr('data-sn');
        if ($(this).attr('data-jqgrid-sensor-type') == '0'){
            $(this).attr('data-jqgrid-sensor-type','1');
            $('#list_sensor_type').jqGrid({
                url: tpurl('Rtu', 'loadSensorTypeData'),
                datatype: 'json',
                mtype: 'post',
                colNames: ['id', $lang.VAR_NAME, $lang.SLAVE_ID, $lang.ADDR, $lang.UNIT, $lang.VALUE_TYPE, $lang.MIN, $lang.MAX, $lang.VAR_NOTE],
                colModel:[
                    {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                    {name:'name',         index:'name',         jsonmap:'name',        width:100,  align:'center', hidden:false, search:false},
                    {name:'slave_id',     index:'slave_id',     jsonmap:'slave_id',    width:100,  align:'center', hidden:true,  search:false},
                    {name:'addr',         index:'addr',         jsonmap:'addr',        width:100,  align:'center', hidden:false, search:false},
                    {name:'unit',         index:'unit',         jsonmap:'unit',        width:100,  align:'center', hidden:false, search:false},
                    {name:'value_type',   index:'value_type',   jsonmap:'value_type',  width:100,  align:'center', hidden:false, search:false},
                    {name:'min',          index:'min',          jsonmap:'min',         width:100,  align:'center', hidden:false, search:false, editable:true, formatter:function(v, options, rowObject){
                        options.colModel.classes = rowObject.min_custom ? 'sensor-custom-color' : '';
                        return rowObject.min_custom ? rowObject.min_custom : v;
                    }},
                    {name:'max',          index:'max',          jsonmap:'max',         width:100,  align:'center', hidden:false, search:false, editable:true, formatter:function(v, options, rowObject){
                        options.colModel.classes = rowObject.max_custom ? 'sensor-custom-color' : '';
                        return rowObject.max_custom ? rowObject.max_custom : v;
                    }},
                    {name:'info',         index:'info',         jsonmap:'info',        width:100,  align:'center', hidden:false, search:false}
                ],
                pager: '#pager_sensor_type',
                rowNum: 10,
                rowList: [10,15,20,50,100],
                sortname: 'name',
                sortorder: 'asc',
                viewrecords: true,
                width: $('#myLgModal2 .modal-dialog').width()-50,
                autoScroll: true,
                height: 'auto',
                multiselect: false,
                multiselectWidth: 30,
                page: 1,
                pagerpos: 'center',
                pgbuttons: true,
                pginput: true,
                postData: {searchTable:'rtu_data_set', sn:sn, set_type:0}, //额外参数
                rownumbers: true,
                rownumWidth: 30,
                jsonReader: {repeatitems: false}
            })
        }else{
            $('#list_sensor_type').setGridParam({postData:{sn:sn}}).trigger('reloadGrid');
        }
    });

    $(window).bind('resize', function(){
        set_g_width();
    });
    $('a.sidebar-toggle').click(function(){
        setTimeout(function(){
            set_g_width();
        }, 300);
    });

    //Change tab
    $('#tab_device_list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        set_g_width();
        if (!$.gf.tp) {
            $.gf.tp = $('#list1').taskpaging({
                url: tpurl('Rtu','loadCardData'),
                sidx: 'last_time',
                sord: 'desc',
                rowNum: window.screen.height>=1000 ? 20 : 15,
                pager: '#list1_paging'
            });
        }
    });

    //Refresh data
    $('button.refresh-jqgrid').click(function(){
        var index = $(this).attr('data-index');
        if (index == '1'){
            $.gf.tp.reload();
        }else{
            $('#list2').trigger('reloadGrid');
        }
    });

    //Set grid columns
    $('#btn_set_columns').click(function(){
        var colNames = $("#list2").jqGrid('getGridParam','colNames');
        var colModel = $("#list2").jqGrid('getGridParam','colModel');
        for (var i=0,lis=''; i<colNames.length; i++){
            if (typeof colModel[i].index == 'undefined'){
                continue;
            }
            lis += '<li class="li_column"><input type="checkbox" name="columns" value="'+colModel[i].name+'" '+(colModel[i].hidden?'':'checked')+'><span>'+colNames[i]+'</span></li>';
        }
        $('#modal_fm_grid_columns ul').html(lis);
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
        localStorage && localStorage.removeItem($.gf.colsStorageName);
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

    /*Change group
    $('#ul_tg').on('click', 'button', function (e) {
        if ($(this).hasClass('btn-toggle')){
            //Expand-close
            $(this).blur();
            if ($(this).find('.fa-caret-down').size() > 0){
                $(this).html('<span class="fa fa-caret-up"></span>').attr('title', $lang.VAR_COLLAPSE);
                $('#ul_tg').css('max-height','none');
            } else {
                $(this).html('<span class="fa fa-caret-down"></span>').attr('title', $lang.VAR_EXPAND);
                $('#ul_tg').css('max-height','90px');
            }
            return;
        }
        if ($(this).attr('data-gid') == -100){
            $('#ztreeModal2').modal({
                position: 'fit',
                moveable: true,
                remote: tpurl('Term','getModalHtml','tpl_id=term_select_group&from=lora')
            });
            return;
        }
        $('#ul_tg button.btn-info').removeClass('btn-info').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-info').blur();
        $.gf.gid = $(this).attr('data-gid');
        $('#search_fm').get(0).reset();
        $('#search_fm2').get(0).reset();
        $('#search_fm, #search_fm2').submit();
    });*/
    $('#change_gid').on('click', function(){
        $('#ztreeModal2').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Term','getModalHtml','tpl_id=term_select_group&from=lora&current_gid='+$(this).find('span').attr('data-id'))
        });
    });

    //project info
    $('button.project-config').click(function(){
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0){
            $.notice(1,$lang.VAR_MSG_SELECT_TERM);
            return;
        }
        if (sns.length > 1){
            $.notice(1,$lang.SELECT_ONLY_ONE_TERM);
            return;
        }
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','sn='+sns[0]+'&from=lora&tpl_id=project_edit'+($.gf.oem=='WANKE'?'_wk':''))
        });
    });

    //Custom sensor type
    $('button.sensor-custom').click(function(){
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0){
            $.notice(1,$lang.VAR_MSG_SELECT_TERM);
            return;
        }
        if (sns.length > 1){
            $.notice(1,$lang.SELECT_ONLY_ONE_TERM);
            return;
        }
        $('#myLgModal2').attr('data-sn', sns[0]).modal({
            position: 'fit',
            moveable: true
        });
    });
    $('button.btn-sensor-custom').click(function(){
        var rowid = $('#list_sensor_type').jqGrid('getGridParam','selrow');
        if (!rowid){
            $.notice(1,$lang.SELECT_SENSOR);
            return;
        }
        $('#list_sensor_type').jqGrid('editRow',rowid);
        $(this).hide();
        $('button.btn-sensor-custom-submit').show().attr('data-rowid', rowid);
    });
    $('button.btn-sensor-custom-submit').click(function(){
        $(this).hide();
        $('button.btn-sensor-custom').show();
        var rowid = $(this).attr('data-rowid');
        $('#list_sensor_type').jqGrid('saveRow',rowid);
        var row = $('#list_sensor_type').getRowData(rowid);
        ajax(tpurl('Rtu', 'sensorTypeCustom'), {act:'custom', sn:$('#myLgModal2').attr('data-sn'), id:row.id, min:row.min, max:row.max}, function(msg){
            $.notice(msg);
            if (msg.status == 0){
                $("#list_sensor_type").trigger('reloadGrid');
            }
        });
    });

    //Export excel
    $('button.export-excel').click(function(){
        var sns = $('#list2').jqGrid('getGridParam','selarrrow'), params = {gid: $.gf.gid, sidx:'status', sord:'DESC'};
        if (sns.length != 0){
            params.sns = sns.join(',');
        }
        getExcelData(tpurl('Rtu','exportRtu'), params);
    });

    //Report project info
    $('button.re-report-info').click(function(){
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0){
            $.notice(1,$lang.VAR_MSG_SELECT_TERM);
            return;
        }
        for (var i=0,row=null; i<sns.length; i++){
            row = $('#list2').jqGrid('getRowData',sns[i]);
            if (row.prjname == '--') {
                $.notice(1,$lang.PLEASE_SET_PROJECT_INFO);
                return;
            }
        }
        ajax(tpurl('Rtu','reReportProjectInfo'), {sns:sns.join(',')}, function(msg){
            $.notice(msg);
        });
    });

    /*data send
    $('button.data-send').click(function(){
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        if (sns.length == 0){
            $.notice(1,$lang.VAR_MSG_SELECT_TERM);
            return;
        }
        if (sns.length > 1){
            $.notice(1,$lang.SELECT_ONLY_ONE_TERM);
            return;
        }
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','sn='+sns[0]+'&tpl_id=data_send')
        });
    });
    */

    $("#myLgModal,#myLgModal3,#ztreeModal2").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });

    // Search - card
    $('#search_fm').on('submit',function(){
        $.gf.tp.set_search_val($(this).find('input[name=searchString]').val(), $.gf.gid);
        $.gf.tp.set_page(1);
        $.gf.tp.reload();
        return false;
    });

    // Search - jqgrid
    $('#search_fm2').on('submit',function(){
        var p = $.serializeObject('#search_fm2');
        $('#list2').setGridParam({page:1, postData:{
            gid:$.gf.gid,
            searchType:'term',
            searchString:p.searchString
        }}).trigger('reloadGrid');
        return false;
    });
});
})(jQuery);