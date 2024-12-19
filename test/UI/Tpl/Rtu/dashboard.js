(function($){
$(function(){
    $.gf.projectEdit = function(sn){
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=project_edit_rtu&sn='+sn)
        });
    }

    //模态框关闭时清除内容
    $("#myLgModal,#rwcsModal,#myLgModal2").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });

	//Map
    window.map_init = function(){
        $.getScript($.gf.lang == 'zh-cn' ? $.gf.gaode_ui:$.gf.empty_js, function(){
            $.gf.lssj_map = map_create('map_container', 2);
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
                    $.gf.lssj_marker = marker;
                }
            }
        });
    };
    mapjs_load();

    $.gf.reset_cmp_width = function(){
        for (var x in $.gf.charts){
            if (typeof $.gf.charts[x].chart != 'undefined'){
                $.gf.charts[x].chart.reflow();
            }
        }
        var w = $('div[id^=history_data_chart_]:eq(0)').width();
        for (var i=0, id=''; i<$.gf.sensors.length; i++){
            id = $.gf.sensors[i].id;
            if ($('#history_data_grid_'+id).attr('data-init') == '1'){
                $('#history_data_list_'+id).jqGrid('setGridWidth', w);
            }
        }
    };

    // 设置jqgrid的宽度
    $(window).bind('resize', function(){
        setTimeout(function(){
            $.gf.reset_cmp_width();
        }, 300);
    });
    $('a.sidebar-toggle').click(function(){
        setTimeout(function(){
            $.gf.reset_cmp_width();
        }, 300);
    });
});
})(jQuery);