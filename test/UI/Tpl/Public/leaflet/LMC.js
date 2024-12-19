/**
 * leaflet地图点聚合
 * dd=[[lng, lat, ts, sn, gdt],[lng, lat, ts, sn, gdt]]
 */

function GMC(mapid, dd){
    this.pics = dd;
    this.markerClusterer = null;
    this.markers = null;
    this.infoWindow = null;
    this.center = [];
    this.icon = null;
    this.map = null;

    // 轨迹相关参数
    this.timer = null;
    this.interval = 500;
    this.index = 0;
    this.marker1 = null; //start
    this.marker2 = null; //end
    this.marker = null;  //current
    this.d = null;
    this.polylines = [];
    this.lastPoint = null;
    this.cb = null;
    this.init_zoom = 18;

    if (this.pics.length == 0) {
        this.center = $.gf.map_center;
    } else {
        this.center = [this.pics[0][1], this.pics[0][0]];
    }
    if ($.gf.lang == 'zh-cn') {
        var mtype = 'GaoDe', mtypetext = '高德地图', mapurl = 'https://www.amap.com/';
    } else {
        var mtype = 'Google', mtypetext = 'Google', mapurl = 'https://www.google.com/maps/';
    }
    var normalm = L.tileLayer.chinaProvider(mtype+'.Normal.Map', {
        attribution: '<a href="'+mapurl+'" target="_blank">'+mtypetext+'</a>',
        maxZoom: this.init_zoom,
        minZoom: 1
    }),
    imgm = L.tileLayer.chinaProvider(mtype+'.Satellite.Map', {
        attribution: '<a href="'+mapurl+'" target="_blank">'+mtypetext+'</a>',
        maxZoom: this.init_zoom,
        minZoom: 1
    }),
    imga = L.tileLayer.chinaProvider(mtype+'.Satellite.Annotion', {
        maxZoom: this.init_zoom,
        minZoom: 1
    });
    var normalMap = L.layerGroup([normalm]);
    this.map = L.map(mapid, {
        center: this.center,
        zoom: 6,
        layers: [normalMap],
        zoomControl: true
    });
    var me = this;
    L.control.layers({'Map':normalMap, 'Satellite':L.layerGroup([imgm, imga])}, null).addTo(this.map);
    L.easyButton('fa-search', function(btn, map) {
        var idx = layer.prompt({
            btn: [$lang.VAR_BTN_SURE, $lang.VAR_BTN_CANCLE],
            title: $lang.VAR_ENTER_QUERY_CONTENT+'&nbsp;('+$lang.VAR_SN2+'，'+$lang.VAR_SYSCFG_ALIAS+')',
            btnAlign: 'c'
        }, function(value, index, elem){
            layer.close(index);
            me.searchPoint(value);
            $('.layui-layer-input').off('keyup');
        });
        $('.layui-layer-input').on('keyup', function(e){
            if (e.keyCode == 13) {
                var v = $(this).val().trim();
                if (v == '') return;
                me.searchPoint(v);
                layer.close(idx);
                $('.layui-layer-input').off('keyup');
            }
        });
    }, $lang.VAR_QUERY).addTo(this.map);
    this.init();
}

GMC.prototype.init = function(){
    var me = this;
    if (me.pics.length == 0){
        return;
    }
    me.icon = L.icon({
        iconUrl: $.gf.public_path+'/leaflet/images/marker-icon.png',
        iconSize: [29, 37],
        iconAnchor: [15, 37],
        popupAnchor: [0, -36],
        shadowUrl: $.gf.public_path+'/leaflet/images/marker-shadow.png',
        shadowSize: [68, 95],
        shadowAnchor: [22, 94]
    });
    me.infoWindow = L.popup({autoPan:false, closeOnClick:false, offset:L.point(0, -20)}); //公用弹出框
    me.showMarkers();
}

GMC.prototype.showMarkers = function(){
    var me = this;
    me.markers = L.markerClusterGroup({ chunkedLoading: true });
    for (var i=0, marker=null, len=me.pics.length; i<len; i++) {
        marker = L.marker([me.pics[i][1], me.pics[i][0]], {
            // icon: me.icon,
            draggable: false,
            data: me.pics[i]
        });
        marker.popup = marker.bindPopup(me.getPopupContent(me.pics[i]));
        me.markers.addLayer(marker);
    }
    me.map.addLayer(me.markers);
};

GMC.prototype.getPopupContent = function(pic){
    var me = this, dt = new Date(pic[2]*1000);
    return '<div style="width:280px;">\
        <div style="width:280px; height:auto; letter-spacing:1px;">\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_SN2+'：</span>' + pic[3] + '</div>\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_SYSCFG_ALIAS+'：</span>' + pic[5] + '</div>\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_GPS_LNG+'：</span>' + round(pic[0], -6) + '</div>\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_GPS_LAT+'：</span>' + round(pic[1], -6) + '</div>\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_DEVICE_URL_REPORT_TIME+'：</span>' + dt.toYmdhis() + '</div>\
            <div style="width:280px; padding:0 0 10px 5px; color:gray;"><span style="width:95px; display:inline-block; font-weight:700;">'+$lang.VAR_OPERATION+'：</span>\
            <a href="javascript:;" onclick="javascript:window.gmc.showReplay(\''+pic[3]+'\');">'+$lang.TRACK_PLAYBACK+'</a></div>\
        </div>\
    </div>';
};

GMC.prototype.searchPoint = function(q){
    var me = this, mks = me.markers ? me.markers.getLayers() : [];
    for (var i=0; i<mks.length; i++){
        if (mks[i].options.data[3].indexOf(q) != -1 || mks[i].options.data[5].indexOf(q) != -1) {
            me.map.flyTo(mks[i].getLatLng(), me.init_zoom);
            me.markers.zoomToShowLayer(mks[i], function(){
                mks[i].popup.openPopup();
            });
            return;
        }
    }
    $.notice(2,$lang.NO_MATCH_TERM);
    $('#search_container input').val('');
}

GMC.prototype.showReplay = function(sn){
    $.gf.mapCurrentSn = sn;
    // me.disableSetAndReplay(1);
    $('#replay_container').show();
}

// 禁用(设置/回放)按钮
GMC.prototype.disableSetAndReplay = function(isDisabled){
    if (isDisabled){
        $('.infoWindowTable button').attr('disabled',true);
    }else{
        $('.infoWindowTable button').removeAttr('disabled');
    }
}

// 设置播放进度
GMC.prototype.setProgress = function(n){
    $('#replay_container .progress-bar').attr('aria-valuenow',n).css('width',n+'%');
}

// 设置播放速度
GMC.prototype.setSpeed = function(n){
    this.interval = 1100 - n;
}

// 清理container弹出层
GMC.prototype.resetContainer = function(){
    this.setProgress(0);
    $('#replay_container .input-group.date').datepicker('setDate', new Date());
    $("#time1").val('00:00:00').timepicker('setTime', '00:00:00');
    $("#time2").val('23:59:59').timepicker('setTime', '23:59:59');
    $.gf.mySlider.slider('setValue', 500);
    $('#tooltip_container').hide();
    $('#replay_container').hide();
    this.disableSetAndReplay(0);
    this.clearMap(true);
    $.gf.mapCurrentSn = '';
}

GMC.prototype.clearMap = function(clear_mk1){
    var me = this;
    if (clear_mk1 && me.marker1) me.map.removeLayer(me.marker1);
    if (me.marker2) me.map.removeLayer(me.marker2);
    if (me.marker) me.map.removeLayer(me.marker);
    if (clear_mk1) {
        if (me.marker1) me.marker1 = null;
    } else {
        if (me.marker1) me.marker1.closeTooltip();
    }
    me.marker2 = me.marker = null;
    var i, len = me.polylines.length;
    for (i=0; i<len; i++){
        me.map.removeLayer(me.polylines[i]);
    }
    me.polylines = [];
    me.d = null;
    me.index = 0;
    me.lastPoint = null;
}

GMC.prototype.prepareDraw = function(d, cb){
    var me = this;
    me.clearMap(true);
    me.d = d;

    me.marker1 = L.marker([parseFloat(d[0].lat), parseFloat(d[0].lng)], {
        title: 'A',
        draggable:false,
    }).addTo(me.map);
    me.marker1.bindTooltip($lang.VAR_START_POINT).openTooltip();

    me.marker2 = L.marker([parseFloat(d[d.length-1].lat), parseFloat(d[d.length-1].lng)], {
        title: 'B',
        draggable:false,
    }).addTo(me.map);
    me.marker2.bindTooltip($lang.VAR_END_POINT).openTooltip();

    me.marker = L.marker([parseFloat(d[0].lat), parseFloat(d[0].lng)], {
        draggable:false
    }).addTo(me.map);
    me.marker.bindTooltip($lang.VAR_CURRENT_POS).openTooltip();
    me.map.flyTo(L.latLng(parseFloat(d[0].lat), parseFloat(d[0].lng)), me.init_zoom);
    me.lastPoint = d[0];
    this.cb = cb;
    me.drawPolyline(d.length);
}

GMC.prototype.drawPolyline = function(len){
    var me = this;
    var currPoint = [parseFloat(me.d[me.index].lat), parseFloat(me.d[me.index].lng)];
    var tmp = L.polyline([[parseFloat(me.lastPoint.lat), parseFloat(me.lastPoint.lng)], currPoint], {
        color: 'blue'
    }).addTo(me.map);
    var latlng = L.latLng(currPoint[0], currPoint[1]);
    me.map.setView(latlng);
    me.marker.setLatLng(latlng);
    me.polylines.push(tmp);
    me.lastPoint = me.d[me.index];
    me.index += 1;
    me.setProgress(me.index / len * 100);
    if (me.index >= len){
        me.timer = null;
        this.cb();
    }else{
        window.setTimeout(function(){
            me.drawPolyline(len);
        }, me.interval);
    }
}