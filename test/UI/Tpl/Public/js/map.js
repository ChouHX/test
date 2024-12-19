//加载js文件
function mapjs_load(){
	var o = document.getElementById('map_js_id');
	if (!o){
	    var jsapi = document.createElement('script');
	    jsapi.charset = 'utf-8';
	    jsapi.src = $.gf.map_api;
	    jsapi.id = 'map_js_id';
	    document.head.appendChild(jsapi);
    }else{
    	window.map_init();
    }
}

//创建地图
function map_create(container_id, zoom, lng, lat){
	if ($.gf.lang == 'zh-cn'){
	    $.gf.map = new AMap.Map(container_id, {
	    	mapStyle: 'amap://styles/cac7a7a9cd5463e1402fec62d36aedea',
	        resizeEnable: true, //是否监控地图容器尺寸变化
	        zoom: typeof zoom == 'undefined' ? 2:zoom,
	        center: [typeof lng == 'undefined' ? 116.38:lng, typeof lat == 'undefined' ? 39.91:lat] //初始化地图中心点
	    });
	    map_add_control();
	}else{
	    $.gf.map = new google.maps.Map(document.getElementById(container_id), {
	        'zoom': typeof zoom == 'undefined' ? 2:zoom,
	        'center': new google.maps.LatLng(typeof lat == 'undefined' ? 39.91:lat, typeof lng == 'undefined' ? 116.38:lng),
	        // 'mapTypeId': google.maps.MapTypeId.HYBRID,
	        disableDefaultUI:true,
	        fullscreenControl: true,
	        mapTypeControl: true,
	        zoomControl:true,
	        // scrollwheel:false,
	        streetViewControl:true
	    });
	}
	return $.gf.map;
}

//添加地图控件
function map_add_control(){
	if ($.gf.lang == 'zh-cn'){
		if (typeof AMapUI == 'undefined') return;
		AMapUI.loadUI(['control/BasicControl'], function(BasicControl){
	        //缩放控件，显示Zoom值
	        $.gf.map.addControl(new BasicControl.Zoom({
	        	// position: 'rb',
	            position: {right:'5px', bottom:'5px'},
	            showZoomNum: false
	        }));

	        //图层切换控件
	        $.gf.map.addControl(new BasicControl.LayerSwitcher({
	            // position: 'lt',
	            position: {left:'5px', top:'5px'}
	        }));
		});
	}
}

/*添加点，返回值为marker
 * label
 * title
 */
function map_add_marker(lng, lat, label, title, draggable){
	var mk = null, opt = {
		map: $.gf.map
	};
	if (typeof title != 'undefined') opt.title = title;
  	if (typeof draggable != 'undefined') opt.draggable = draggable;
	if ($.gf.lang == 'zh-cn'){
		opt.position = [lng, lat];
		if (typeof label != 'undefined' && label){
   			opt.content = '<div class="custom-content-marker" style="width:19px; height:31px; background:url('+$.gf.public_path+'images/point.png) no-repeat; text-align:center; font-weight:700">'+label+'</div>';
		}else{
			opt.icon = $.gf.public_path + 'images/point.png';
		}
		mk = new AMap.Marker(opt);
	}else{
        opt.position = new google.maps.LatLng(lat, lng);
  		if (typeof label != 'undefined') opt.label = label;
        mk = new google.maps.Marker(opt);
	}
	return mk;
}

//添加折线
function map_add_polyline(last_lng, last_lat, curr_lng, curr_lat, strokeColor, strokeOpacity, strokeWeight){
	var line = null, opt = {
		map: $.gf.map,
		strokeColor: typeof strokeColor != 'undefined' ? strokeColor : '#0000ff',
		strokeOpacity: typeof strokeOpacity != 'undefined' ? strokeOpacity : 1.0,
		strokeWeight: typeof strokeWeight != 'undefined' ? strokeWeight : 2
	};
	if ($.gf.lang == 'zh-cn'){
		opt.path = [[last_lng,last_lat], [curr_lng,curr_lat]];
		opt.showDir = true;
		line = new AMap.Polyline(opt);
	}else{
		opt.path = [{lat:last_lat,lng:last_lng}, {lat:curr_lat,lng:curr_lng}];
	    line = new google.maps.Polyline(opt);
	}
	return line;
}

//设置中心位置
function map_set_center(lng, lat, map){
	var m = typeof map == 'undefined' ? $.gf.map : map;
	m.setCenter($.gf.lang == 'zh-cn' ? new AMap.LngLat(lng,lat) : new google.maps.LatLng(lat,lng));
}

//设置缩放级别
function map_set_zoom(z){
	$.gf.map.setZoom(z);
}

//添加事件
function map_add_event(obj, ename, cb){
	if ($.gf.lang == 'zh-cn'){
		/*obj.on(ename, function(p){
			cb(p, obj);
		})*/
		return AMap.event.addListener(obj, ename, function(p){
			cb(p, obj);
		});
	}else{
		return google.maps.event.addListener(obj, ename, function(p){
			cb(p, obj);
		});
	}
}

//移除事件
function map_remove_event(event_handler){
	if (!event_handler) return;
	if ($.gf.lang == 'zh-cn'){
		AMap.event.removeListener(event_handler);
	}else{
		google.maps.event.removeListener(event_handler);
	}
	event_handler = null;
}

//触发事件
function map_trigger_event(obj, ename){
	$.gf.lang == 'zh-cn' ? AMap.event.trigger(obj, ename) : google.maps.event.trigger(obj, ename);
}

//通过事件参数获取经纬度
function map_get_pos_by_event(e){
    var lng = 0, lat = 0;
	if ($.gf.lang == 'zh-cn'){
		if (e.type == 'dragend'){
			var lnglat = e.target.getPosition();
    	}else if (e.type == 'click'){
    		var lnglat = e.lnglat;
    	}
    	lng = lnglat.lng.toFixed(6);
    	lat = lnglat.lat.toFixed(6);
	}else{
        lng = e.latLng.lng().toFixed(6);
        lat = e.latLng.lat().toFixed(6);
	}
	return {lng:lng, lat:lat};
}

//设置marker位置
function map_set_marker_pos(mk, lng, lat){
	if ($.gf.lang == 'zh-cn'){
		mk.setPosition(new AMap.LngLat(lng, lat));
	}else{
		mk.setPosition(new google.maps.LatLng(lat, lng));
	}
}

//获取marker位置
function map_get_marker_pos(mk){
	var o = mk.getPosition();
	if ($.gf.lang == 'zh-cn'){
		return {lng:o.getLng(), lat:o.getLat()};
	}else{
		return {lng:o.lng().toFixed(5), lat:o.lat().toFixed(5)};
	}
}

//信息窗口
function map_get_infowindow(str){
	var w = null;
	if ($.gf.lang == 'zh-cn'){
		w = new AMap.InfoWindow({
			isCustom: false,
			showShadow: true,
			content: typeof str != 'undefined' ? str : '',
			offset: new AMap.Pixel(0,-20)
		});
	}else{
		w = new google.maps.InfoWindow();
		if (typeof str != 'undefined'){
			w.setContent(str);
		}
	}
	return w;
}

//Get point
function map_get_lnglat(lng, lat){
	return $.gf.lang == 'zh-cn' ? new AMap.LngLat(lng,lat) : new google.maps.LatLng(lat,lng);
}

//点聚合
function map_create_marker_cluster(map, markers){
	return $.gf.lang == 'zh-cn' ? new AMap.MarkerClusterer(map, markers, {gridSize: 80}) : new MarkerClusterer(map, markers);
}