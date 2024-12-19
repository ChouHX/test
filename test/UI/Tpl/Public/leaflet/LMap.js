function GMap(p){
    this.currentPos = p;
    this.map = null;
    this.init_zoom = 16;
    this.marker1 = null;
    this.marker_setgps = null;
    this.infoWindow = null;
    this.d_type = -1; //围栏类型，0=circle, 1=rect, 2=polygon
    this.circles = {};
    this.rects = {};
    this.polygons = {};
    this.cs = {color:'red', fillColor:'skyblue', fillOpacity:0.5, weight:1};
    this.d_circle = null;
    this.d_center = null;
    this.d_radius = 0;
    this.d_rect = null;
    this.d_bounds = null;
    this.d_polygon = null;
    this.d_polygon_latlngs = [];
    this.icon_blue = null;
    this.icon_red = null;
    this.polylines = [];
    this.initMap();
}

GMap.prototype.clearMap = function(){
    var me = this;
    var i, len = me.polylines.length;
    for (i=0; i<len; i++){
        me.map.removeLayer(me.polylines[i]);
    }
    me.polylines = [];
    me.clearFence();
}

GMap.prototype.clearFence = function(){
    var me = this;
    for (var x in me.circles) me.map.removeLayer(me.circles[x]);
    for (var x in me.rects) me.map.removeLayer(me.rects[x]);
    for (var x in me.polygons) me.map.removeLayer(me.polygons[x]);
    me.circles = {};
    me.rects = {};
    me.polygons = {};
    me.d_type = -1;
    if (me.d_circle) {
        me.map.removeLayer(me.d_circle);
        me.d_circle = null;
    }
    me.d_center = null;
    me.d_radius = 0;
    if (me.d_rect) {
        me.map.removeLayer(me.d_rect);
        me.d_rect = null;
    }
    me.d_bounds = null;
    if (me.d_polygon) {
        me.map.removeLayer(me.d_polygon);
        me.d_polygon = null;
    }
    me.d_polygon_latlngs = [];
}

//打开信息窗口
GMap.prototype.getPopupContent = function(d){
    var me = this;
    return '<div style="width:280px;">\
        <div style="width:280; padding-left:5px;">\
            <a href="javascript:;" style="color:gray; text-decoration:none; cursor:default;">\
                <h4 style="margin:0 0 5px 0; padding:0.2em 0; font-size:13px; font-weight:700;">'+$lang.VAR_SN+'：'+d.sn+'<span style="color:red;'+(me.currentPos.fstatus != 1 ? 'display:none' : '')+'">&nbsp;&nbsp;['+$lang.OUT_FENCE_WARNING+']</span></h4>\
            </a>\
        </div>\
        <div style="width:280px; height:auto; letter-spacing:1px;">\
            <div style="width:280px; padding:0 0 3px 5px; color:gray;">'+$lang.VAR_GPS_LNG+'：' + round(d.lng, -6) + '</div>\
            <div style="width:280px; padding:0 0 3px 5px; color:gray;">'+$lang.VAR_GPS_LAT+'：' + round(d.lat, -6) + '</div>\
            <div style="width:280px; padding:0 0 3px 5px; color:gray;">'+$lang.VAR_DEVICE_URL_REPORT_TIME+'：' + d.ts + '</div>\
        </div>\
    </div>';
}

GMap.prototype.getIcon = function(img_name){
    return L.icon({
        iconUrl: $.gf.public_path+'/leaflet/images/' + img_name,
        iconSize: [25, 40],
        iconAnchor: [13, 40],
        popupAnchor: [0, -39],
        shadowUrl: $.gf.public_path+'/leaflet/images/marker-shadow.png',
        shadowSize: [68, 95],
        shadowAnchor: [22, 94]
    });
}

GMap.prototype.initMap = function(){
    var me = this, center = $.gf.map_center, hasPoint = false;
    if (me.currentPos.lng && me.currentPos.lat) {
        center = [me.currentPos.lat, me.currentPos.lng];
        hasPoint = true;
    }
    if ($.gf.lang == 'zh-cn') {
        var mtype = 'GaoDe', mtypetext = '高德地图', mapurl = 'https://www.amap.com/';
    } else {
        var mtype = 'Google', mtypetext = 'Google', mapurl = 'https://www.google.com/maps/';
    }
    var normalm = L.tileLayer.chinaProvider(mtype+'.Normal.Map', {
        attribution: '<a href="'+mapurl+'" target="_blank">'+mtypetext+'</a>',
        maxZoom: me.init_zoom,
        minZoom: 1
    }),
    imgm = L.tileLayer.chinaProvider(mtype+'.Satellite.Map', {
        attribution: '<a href="'+mapurl+'" target="_blank">'+mtypetext+'</a>',
        maxZoom: me.init_zoom,
        minZoom: 1
    }),
    imga = L.tileLayer.chinaProvider(mtype+'.Satellite.Annotion', {
        maxZoom: me.init_zoom,
        minZoom: 1
    });
    var normalMap = L.layerGroup([normalm]);
    me.map = L.map('map_container', {
        center: center,
        zoom: hasPoint ? me.init_zoom : 4,
        layers: [normalMap],
        zoomControl: true,
        closePopupOnClick: false,
        doubleClickZoom: typeof me.currentPos.fence == 'undefined'
    });
    L.control.layers({'Map':normalMap, 'Satellite':L.layerGroup([imgm, imga])}, null).addTo(this.map);
    if (hasPoint) {
        me.marker1 = L.marker([me.currentPos.lat, me.currentPos.lng], {
            title: me.currentPos.name,
            icon: me.currentPos.fstatus == 1 ? me.getIcon('marker-icon-red.png') : me.getIcon('marker-icon.png'),
            draggable:false,
            data: me.currentPos
        }).addTo(me.map);
        me.marker1.bindPopup(me.getPopupContent(me.currentPos)).openPopup();
    }
}

GMap.prototype.moveTo = function(point){
    var me = this;
    me.currentPos = point;
    if (me.marker1) {
        me.marker1.setLatLng(L.latLng(point.lat, point.lng));
        me.marker1.bindPopup(me.getPopupContent(me.currentPos)).openPopup();
        me.marker1.setIcon(me.currentPos.fstatus == 1 ? me.getIcon('marker-icon-red.png') : me.getIcon('marker-icon.png'));
    } else if (point.ts) {
        me.marker1 = L.marker([point.lat, point.lng], {
            title: me.currentPos.name,
            icon: me.currentPos.fstatus == 1 ? me.getIcon('marker-icon-red.png') : me.getIcon('marker-icon.png'),
            draggable:false,
            data: me.currentPos
        }).addTo(me.map);
        me.map.flyTo(L.latLng(point.lat, point.lng), me.init_zoom);
        me.marker1.bindPopup(me.getPopupContent(me.currentPos)).openPopup();
    }
}

GMap.prototype.bindRemove = function(mk, arr_name){
    var me = this;
    mk.on('contextmenu', function(o){
        if (me.d_type != -1) return;
        $.confirm($lang.DEL_FENCE_CONFIRM, function(){
            me.map.removeLayer(o.target);
            delete me[arr_name][o.target._leaflet_id];
            $.gf.setFence();
        });
    });
}

GMap.prototype.drawFence = function(point){
    var me = this;

    // 鼠标左键单击开始绘图
    me.map.on('click', function(e){
        if (me.d_type == 0) {
            if (!me.d_circle) {
                me.d_circle = L.circle(e.latlng, me.cs);
                me.d_center = e.latlng;
                me.d_radius = 0;
                me.map.addLayer(me.d_circle);
            } else {
                me.circles[me.d_circle._leaflet_id] = me.d_circle;
                me.bindRemove(me.d_circle, 'circles');
                me.d_circle = null;
                me.d_type = -1;
                layer.msg($lang.DRAW_OK, {offset:['50%', '50%'], time:1000, icon:1});
                $.gf.setFence();
            }
        }
        if (me.d_type == 1) {
            if (!me.d_rect) {
                me.d_bounds = [[e.latlng.lat, e.latlng.lng], [e.latlng.lat, e.latlng.lng]];
                me.d_rect = L.rectangle(me.d_bounds, me.cs);
                me.map.addLayer(me.d_rect);
            } else {
                me.rects[me.d_rect._leaflet_id] = me.d_rect;
                me.bindRemove(me.d_rect, 'rects');
                me.d_rect = null;
                me.d_type = -1;
                layer.msg($lang.DRAW_OK, {offset:['50%', '50%'], time:1000, icon:1});
                $.gf.setFence();
            }
        }
        if (me.d_type == 2 && e.originalEvent.button == 0) {
            me.d_polygon_latlngs.push([e.latlng.lat, e.latlng.lng]);
            if (!me.d_polygon) {
                me.d_polygon = L.polygon(me.d_polygon_latlngs, me.cs);
                me.map.addLayer(me.d_polygon);
            } else {
                me.d_polygon.setLatLngs(me.d_polygon_latlngs);
            }
        }
    });

    // 移动鼠标动态绘制圆形和矩形
    me.map.on('mousemove', function(e2){
        if (me.d_type == 0 && me.d_circle) {
            me.d_radius = me.d_center.distanceTo(e2.latlng);
            me.d_circle.setRadius(me.d_radius);
        }
        if (me.d_type == 1 && me.d_rect) {
            me.d_bounds[1] = [e2.latlng.lat, e2.latlng.lng];
            me.d_rect.setBounds(me.d_bounds);
        }
    });

    // 双击鼠标左键完成绘图，并添加删除事件
    me.map.on('dblclick', function(){
        // me.map.off('mousedown');
        if (me.d_type == 2 && me.d_polygon) {
            me.polygons[me.d_polygon._leaflet_id] = me.d_polygon;
            me.bindRemove(me.d_polygon, 'polygons');
            me.d_polygon = null;
            me.d_polygon_latlngs = [];
            me.d_type = -1;
            layer.msg($lang.DRAW_OK, {offset:['50%', '50%'], time:1000, icon:1});
            $.gf.setFence();
        }
    });

    // 点击鼠标右键，取消绘图
    me.map.on('contextmenu', function(){
        if (me.d_type != -1) {
            layer.msg($lang.DRAW_CANCEL, {offset:['50%', '50%'], time:1000, icon:0});
        }
        if (me.d_type == 0 && me.d_circle) {
            me.map.removeLayer(me.d_circle);
            me.d_circle = null;
            me.d_center = null;
            me.d_radius = 0;
        } else if (me.d_type == 1 && me.d_rect) {
            me.map.removeLayer(me.d_rect);
            me.d_rect = null;
            me.d_bounds = null;
        } else if (me.d_type == 2 && me.d_polygon) {
            me.map.removeLayer(me.d_polygon);
            me.d_polygon = null;
            me.d_polygon_latlngs = [];
        }
        me.d_type = -1;
    });
}

GMap.prototype.initFence = function(fence){
    var me = this;
    for (var i=0, f=null; i<fence.length; i++) {
        f = fence[i];
        if (f.ftype == 1) {
            var circle = L.circle(L.latLng(f.lat, f.lng), me.cs);
            circle.setRadius(f.radius);
            me.map.addLayer(circle);
            me.circles[circle._leaflet_id] = circle;
            me.bindRemove(circle, 'circles');
        } else if (f.ftype == 2) {
            var rect = L.rectangle([[f.lat1, f.lng1], [f.lat2, f.lng2]], me.cs);
            me.map.addLayer(rect);
            me.rects[rect._leaflet_id] = rect;
            me.bindRemove(rect, 'rects');
        } else if (f.ftype == 3) {
            var polygon = L.polygon(f.points, me.cs);
            me.map.addLayer(polygon);
            me.polygons[polygon._leaflet_id] = polygon;
            me.bindRemove(polygon, 'polygons');
        }
    }
}