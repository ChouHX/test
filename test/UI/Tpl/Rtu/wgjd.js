(function($){
$(function () {
    ajax(tpurl('Rtu','getSensorData'),{data_num:$.gf.data_num, gateway_sn:$.gf.gateway_sn, sidx:'status', sord:'DESC'},function(msg){
        for (var i=0,li=null,row=null,vt=''; i<msg.data.length; i++){
            row = msg.data[i];
            li = $('#'+row.sn+'_'+row.slave_id+'_'+row.addr);
            if (li.length == 0){
                continue;
            }
            vt = parseInt(li.attr('data-value_type'));
            li.find('.current_value').html(row['value']);
            li.find('.report_time').html(row.report_time);
            if (row.warning_value){
                li.addClass('has-warning');
            }
        }
        //没有数据的li隐藏
        var _24hours_ago = new Date().getTime() - 24*3600*1000;
        $('li.sensor').each(function(){
            var rtm = $(this).find('.report_time').html(), hide = 0;
            if (rtm == '--'){
                $(this).addClass('no-data');
                hide = 1;
            }else if (new Date(rtm).getTime() < _24hours_ago){
                hide = 1;
            }
            if (hide){
                $(this).css('display', $.gf.wgjd_show_type==1?'none':'block');
            }
        });
    });

    $('li.sensor').click(function(){
        if ($(this).hasClass('no-data')) return;
        // location.href = tpurl('Rtu','rtuxq','backpage=wgjd&data_num='+$.gf.data_num+'&sn='+$(this).parent().attr('data-sn')+'&gateway_sn='+$.gf.gateway_sn); return;
        var rowid = $(this).parent().attr('data-sn'), slave_id_addr = $(this).attr('data-slave_id')+'_'+$(this).attr('data-addr');
        $('#myLgModal3').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=history_data&sn='+rowid+'&slave_id_addr='+slave_id_addr)
        });
    });

    //仅显示有数据的
    $('input[name=wgjd_show_type]').on('change', function(){
        var c = $(this).is(':checked');
        $.cookie('wgjd_show_type', c?1:0, {path:'/', expires:365});
        //没有数据的li隐藏/显示
        var _24hours_ago = new Date().getTime() - 24*3600*1000;
        $('li.sensor').each(function(){
            var rtm = $(this).find('.report_time').html(), hide = 0;
            if (rtm == '--'){
                $(this).addClass('no-data');
                hide = 1;
            }else if (new Date(rtm).getTime() < _24hours_ago){
                hide = 1;
            }
            if (hide){
                $(this).css('display', c?'none':'block');
            }
        });
    });

    //Map
    window.map_init = function(){
        $.getScript($.gf.lang == 'zh-cn' ? $.gf.gaode_ui:$.gf.empty_js, function(){
            map_create('map_container', 2);
            $.gf.infoWindow = map_get_infowindow();
            for (var i=0,j=0, point=null, marker=null; i<$.gf.gps.length; i++){
                if (!$.gf.gps[i].latitude || !$.gf.gps[i].longitude){
                    continue;
                }
                marker = map_add_marker($.gf.gps[i].longitude, $.gf.gps[i].latitude);
                marker.pic = $.gf.gps[i];
                map_add_event(marker, 'click', function(e, that){
                    var c ='<table class="table-condensed infoWindowTable" style="font-size:12px">\
                        <tr><td width="70px">'+$lang.VAR_SN2+':</td><td>'+that.pic.sn+'</td></tr>\
                        <tr><td width="70px">'+$lang.VAR_DEVICE_URL_REPORT_TIME+':</td><td>'+that.pic.report_time+'</td></tr>\
                    </table>';
                    $.gf.infoWindow.setContent(c);
                    $.gf.infoWindow.setPosition(map_get_lnglat(that.pic.longitude, that.pic.latitude));
                    $.gf.infoWindow.open($.gf.map);
                });
                if (j++ == 0){
                    map_set_zoom(8);
                    map_set_center($.gf.gps[i].longitude, $.gf.gps[i].latitude);
                }
            }
        });
    };
    mapjs_load();

    $.gf.projectEdit = function(sn){
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=project_edit&sn='+sn)
        });
    }

    //模态框关闭时清除内容
    $("#myLgModal,#myLgModal3").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });
});
})(jQuery);