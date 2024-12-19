/**
 * google地图点聚合
 * dd=[[lng, lat, ts, sn, gdt],[lng, lat, ts, sn, gdt]]
 */

function GMC(mapid, dd, replay){
    this.pics = dd;
    this.replay = replay;
    this.markerClusterer = null;
    this.markers = [];
    this.infoWindow = null;
    this.markersTmp = [];
    this.mapClickListener = null; //地图点击事件监听

    //轨迹相关
    this.timer = null;
    this.interval = 500;
    this.index = 0;
    this.map = null;
    this.marker1 = null;
    this.marker2 = null;
    this.marker1_iw = null;
    this.marker2_iw = null;
    this.marker = null;
    this.d = null;
    this.polylines = [];
    this.lastPoint = null;
    this.cb = null;

    if (this.pics.length == 0){
        this.map = map_create(mapid, 6);
    }else{
        this.map = map_create(mapid, 6, this.pics[0][0], this.pics[0][1]);
    }
}

GMC.prototype.init = function(){
    var me = this;
    if (me.pics.length == 0){
        return;
    }
    me.infoWindow = map_get_infowindow();
    me.showMarkers();
}

GMC.prototype.showMarkers = function(){
    var me = this;
    me.markers = [];

    if (me.markerClusterer){
        me.markerClusterer.clearMarkers();
    }

    for (var i=0, len=me.pics.length; i<len; i++) {
        var marker = map_add_marker(me.pics[i][0], me.pics[i][1]);
        marker.pics = me.pics[i];

        map_add_event(marker, 'click', function(e, that){
            var pic = that.pics, sn = pic[3], disabled = $.gf.mapCurrentSn ? 'disabled' : '';
            var infoHtml ='<table class="table-condensed infoWindowTable" style="font-size:12px">\
                <tr><td width="70px">'+$lang.VAR_SN2+':</td><td>'+sn+'</td></tr>\
                <tr><td width="70px">'+$lang.VAR_DEVICE_URL_REPORT_TIME+':</td><td>'+new Date(pic[2]*1000).format('yyyy-MM-dd hh:mm:ss')+'</td></tr>';
            if (me.replay){
                infoHtml += '<tr><td width="70px"><button class="btn btn-default btn-xs setgps" '+disabled+' onclick="javascript:gmc.showSetgps(\''+sn+'\')"><span class="fa fa-plus-circle"></span>&nbsp;'+$lang.GPS_SETUP+'</button></td>\
                    <td><button class="btn btn-default btn-xs replay" '+disabled+' onclick="javascript:gmc.showReplay(\''+sn+'\')"><span class="fa fa-play-circle"></span>&nbsp;'+$lang.TRACK_PLAYBACK+'</button></td></tr>';
            }
            infoHtml += '</table>';
            me.infoWindow.setContent(infoHtml);
            me.infoWindow.setPosition(map_get_lnglat(pic[0],pic[1]));
            me.infoWindow.open(me.map);
        });
        me.markers.push(marker);
    }
    me.markerClusterer = map_create_marker_cluster(me.map, me.markers);
};

GMC.prototype.showSetgps = function(sn){
    var me = this;
    //设置GPS
    // $('.infoWindowTable .setgps').on('click',function(){
        me.setCurrentSn(sn);
        me.disableSetAndReplay(1);
        $('#tooltip_container').show();
        me.mapClickListener = map_add_event(me.map, 'click', function(e, obj) {
            var lnglat = map_get_pos_by_event(e);
            if (me.markersTmp.length == 0){
                me.markersTmp.push(map_add_marker(lnglat.lng, lnglat.lat, '+', '', true));
            }else{
                map_set_marker_pos(me.markersTmp[0], lnglat.lng, lnglat.lat);
            }
        });
    // });
}

GMC.prototype.showReplay = function(sn){
    var me = this;
    //轨迹回放
    // $('.infoWindowTable .replay').on('click',function(){
        me.setCurrentSn(sn);
        me.disableSetAndReplay(1);
        $('#replay_container').show();
    // });
}

//保存当前操作的终端sn
GMC.prototype.setCurrentSn = function(sn){
    $.gf.mapCurrentSn = sn;
    $('#search_container input').val(sn);
}

//禁用(设置/回放)按钮
GMC.prototype.disableSetAndReplay = function(isDisabled){
    if (isDisabled){
        $('.infoWindowTable button').attr('disabled',true);
    }else{
        $('.infoWindowTable button').removeAttr('disabled');
    }
}

//设置播放进度
GMC.prototype.setProgress = function(n){
    $('#replay_container .progress-bar').attr('aria-valuenow',n).css('width',n+'%');
}

//设置播放速度
GMC.prototype.setSpeed = function(n){
    this.interval = 1100 - n;
}

//清理container弹出层
GMC.prototype.resetContainer = function(){
    this.setProgress(0);
    $('#replay_container .input-group.date').datepicker('setDate', new Date());
    $("#time1").val('00:00:00').timepicker('setTime', '00:00:00');
    $("#time2").val('23:59:59').timepicker('setTime', '23:59:59');
    $.gf.mySlider.slider('setValue', 500);
    $('#tooltip_container').hide();
    $('#replay_container').hide();
    this.disableSetAndReplay(0);
    this.clearMap();
    this.setCurrentSn('');
    this.clearMarkersTmp();
    map_remove_event(this.mapClickListener);
}

//关闭 infoWindow
GMC.prototype.closeInfoWindow = function(){
    if (this.infoWindow){
        this.infoWindow.close();
    }
}

GMC.prototype.clear = function(){
    var me = this;
    for (var i=0, len=me.markers.length; i<len; i++) {
        me.markers[i].setMap(null);
    }
    if (me.markerClusterer){
        me.markerClusterer.clearMarkers();
    }
};

GMC.prototype.clearMarkersTmp = function(){
    var me = this;
    for (var i=0, len=me.markersTmp.length; i<len; i++){
        me.markersTmp[i].setMap(null);
    }
    me.markersTmp = [];
};

GMC.prototype.getAddedMarkers = function(){
    var me = this, arr = [];
    for (var i=0, len=me.markersTmp.length, ret=null; i<len; i++){
        ret = map_get_marker_pos(me.markersTmp[i]);
        arr.push(ret.lng+','+ret.lat);
    }
    return arr.join('#');
}

GMC.prototype.change = function(dd){
    var me = this;
    me.clear();
    me.pics = dd;
    if (me.pics.length == 0){
        return;
    }
    me.showMarkers();
    me.map.setCenter(new google.maps.LatLng(dd[0][1], dd[0][0]), me.map.getZoom());
};

GMC.prototype.searchPoint = function(sn){
    var me = this;
    for (var i=0; i<me.markers.length; i++){
        if (sn == me.markers[i].pics[3]){
            me.map.panTo(me.markers[i].getPosition());
            me.map.setZoom(18);
            map_trigger_event(me.markers[i], 'click');
            return;
        }
    }
    $.notice(2,$lang.NO_MATCH_TERM);
    $('#search_container input').val('');
}

GMC.prototype.moveto = function(sn, lng, lat){
    var me = this;
    for (var i=0; i<me.markers.length; i++){
        if (sn == me.markers[i].pics[3]){
            map_set_marker_pos(me.markers[i], lng, lat)
            me.infoWindow.setPosition(map_get_lnglat(lng, lat));
            me.markers[i].pics[0] = lng;
            me.markers[i].pics[1] = lat;
            map_set_center(lng, lat);
            break;
        }
    }
}

GMC.prototype.prepareDraw = function(d, cb){
    var me = this;
    me.clearMap();
    me.d = d;

    //起点
    me.marker1 = map_add_marker(parseFloat(d[0].lng), parseFloat(d[0].lat), $lang.POLYLINE_START);
    /*
    if (!me.marker1_iw){
        me.marker1_iw = map_get_infowindow($lang.VAR_START_POINT);
    }
    me.marker1_iw.setPosition(me.marker1.getPosition());
    me.marker1_iw.open(me.map);*/

    //终点
    me.marker2 = map_add_marker(parseFloat(d[d.length-1].lng), parseFloat(d[d.length-1].lat), $lang.POLYLINE_END);
    /*
    if (!me.marker2_iw){
        me.marker2_iw = map_get_infowindow($lang.VAR_END_POINT);
    }
    me.marker2_iw.setPosition(me.marker2.getPosition());
    me.marker2_iw.open(me.map);*/

    //行驶点
    me.marker = map_add_marker(parseFloat(d[0].lng), parseFloat(d[0].lat), $lang.POLYLINE_THROUGH);
    map_set_center(parseFloat(d[0].lng), parseFloat(d[0].lat));
    me.lastPoint = d[0];
    this.cb = cb;
    me.drawPolyline();
}

GMC.prototype.drawPolyline = function(){
    var me = this, len = me.d.length, lng = 0, lat = 0, line = null;
    lng = parseFloat(me.d[me.index].lng);
    lat = parseFloat(me.d[me.index].lat);
    line = map_add_polyline(parseFloat(me.lastPoint.lng), parseFloat(me.lastPoint.lat), lng, lat);
    //判断点是否在地图可视区域内
    var bounds = me.map.getBounds(), pt = map_get_lnglat(lng,lat);
    if (!bounds.contains(pt)) {
        me.map.panTo(pt);
    }
    map_set_marker_pos(me.marker, lng, lat);
    me.polylines.push(line);
    me.lastPoint = me.d[me.index];
    me.index += 1;
    var percent = me.index / len;
    me.setProgress(percent*100);
    if (me.index >= len){
        me.timer = null;
        me.cb();
    }else{
        window.setTimeout(function(){
            me.drawPolyline();
        }, me.interval);
    }
}

GMC.prototype.clearMap = function(){
    var me = this;
    if (me.marker1) me.marker1.setMap(null);
    if (me.marker2) me.marker2.setMap(null);
    if (me.marker) me.marker.setMap(null);
    me.marker1 = me.marker2 = me.marker = null;
    if (me.marker1_iw) me.marker1_iw.setMap(null);
    if (me.marker2_iw) me.marker2_iw.setMap(null);
    me.marker1_iw = me.marker2_iw = null;
    var i, len = me.polylines.length;
    for (i=0; i<len; i++){
        me.polylines[i].setMap(null);
    }
    me.polylines.length = 0;
    me.d = null;
    me.index = 0;
    me.lastPoint = null;
}
