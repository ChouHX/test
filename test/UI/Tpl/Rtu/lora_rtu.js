(function($){
$(function () {
    $.gf.colsStorageName = 'lora_list3_colModel';
    $.gf.list2SaveColumns = function(){
        var arr = [], cks = $('#modal_fm_grid_columns input[name=columns]');
        for (var i=0,ck=null; i<cks.length; i++){
            ck = $(cks.get(i));
            if (ck.is(':checked')){
                arr.push(ck.val());
            }
        }
        localStorage.setItem($.gf.colsStorageName, arr.join(',')+',');
    };
    $.gf.changeColumns = function(){
        $.gf.list2SaveColumns();
        $.gf.tp.reload();
    }

    $.gf.tp = $('#list1').taskpaging({
        url: tpurl('Rtu','loadCardData'),
        sidx: 'last_time',
        sord: 'desc',
        rowNum: window.screen.height>=1000 ? 20 : 15,
        pager: '#list1_paging'
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
        var cols = localStorage && localStorage.getItem($.gf.colsStorageName) || null;
        for (var i=0,lis='',item=null,show=false; i<$.gf.sensors.length; i++){
            item = $.gf.sensors[i];
            show = !cols || cols.indexOf(item.slave_id+'_'+item.addr+',') != -1;
            lis += '<li class="li_column"><input type="checkbox" onchange="javascript:$.gf.changeColumns();" name="columns" value="'+item.slave_id+'_'+item.addr+'" '+(!show?'':'checked')+'><span>'+item.name+'</span></li>';
        }
        $('#modal_fm_grid_columns ul').html(lis);
        $('#modal_fm_grid_columns li span').click(function(){
            $(this).prev().click();
        });
        $('#gridColumnsModal').modal({
            position: 'fit',
            moveable: true
        });
    });
    $('button.grid_columns_reset').click(function(){
        localStorage && localStorage.removeItem($.gf.colsStorageName);
        $('#gridColumnsModal').modal('hide');
        $.gf.tp.reload();
    });
    $('button.grid_columns_checkall').click(function(){
        $('#modal_fm_grid_columns input[name=columns]').prop('checked',true);
        $.gf.list2SaveColumns();
        $.gf.tp.reload();
    });
    $('button.grid_columns_uncheckall').click(function(){
        $('#modal_fm_grid_columns input[name=columns]').prop('checked',false);
        $.gf.list2SaveColumns();
        $.gf.tp.reload();
    });
    /*$('button.grid_columns_save').click(function(){
        $.gf.list2SaveColumns();
        $('#gridColumnsModal').modal('hide');
        $.gf.tp.reload();
    });*/

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
                remote: tpurl('Term','getModalHtml','tpl_id=term_select_group&from=lora_rtu')
            });
            return;
        }
        $('#ul_tg button.btn-info').removeClass('btn-info').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-info').blur();
        $.gf.gid = $(this).attr('data-gid');
        $('#search_fm').get(0).reset();
        $('#search_fm').submit();
    });*/
    $('#change_gid').on('click', function(){
        $('#ztreeModal2').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Term','getModalHtml','tpl_id=term_select_group&from=lora_rtu&current_gid='+$(this).find('span').attr('data-id'))
        });
    });

    $("#myLgModal,#myLgModal3,#ztreeModal2").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });

    //Search lora
    $('#search_fm').on('submit',function(){
        $.gf.tp.set_search_val($(this).find('input[name=searchString]').val(), $.gf.gid);
        $.gf.tp.set_page(1);
        $.gf.tp.reload();
        return false;
    });
});
})(jQuery);