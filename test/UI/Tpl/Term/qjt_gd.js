(function($){
    var currGroupId = -10;
    $(document).ready(function(){
        $('#gmc_container').css('height',window.innerHeight-101);
        $('body').css('overflow','hidden');

        var options = {
            'zoom': 2,
            'center': [116.38, 39.91],
            // 'mapTypeId': google.maps.MapTypeId.ROADMAP
            // disableDefaultUI:true,
            // fullscreenControl: true,
            // mapTypeControl: true,
            // zoomControl:true,
            // streetViewControl:true,
            // scrollwheel:false
        };
        window.map = new AMap.Map(document.getElementById('container'), options);
        window.map.setDefaultCursor("pointer");
        AMap.plugin([
            'AMap.ToolBar',
        ], function(){
            window.map.addControl(new AMap.ToolBar());
        });

        window.renderMarker = function(context){
            var pic = context.data[0].pic;
            if (!window.infoWindow) {
                window.infoWindow = new AMap.InfoWindow({offset: new AMap.Pixel(0, -30)});
            }
            context.marker.on('click', (e)=>{
                var infoHtml ='<table class="tbl">\
                                <tr><td><b>'+$lang.VAR_SN1+'</b>:&nbsp;</td><td>'+pic.sn1+'</td></tr>\
                                <tr><td><b>'+$lang.VAR_SN2+'</b>:&nbsp;</td><td>'+pic.sn2+'</td></tr>\
                                <tr><td><b>'+$lang.VAR_INFO+'</b>:&nbsp;</td><td>'+pic.alias+'</td></tr>\
                                <tr><td><b>'+$lang.VAR_DEVICE_URL_REPORT_TIME+'</b>:&nbsp;</td><td>'+pic.ts+'</td></tr></table>';

                window.infoWindow.setContent(infoHtml);
                window.infoWindow.open(window.map,[e.lnglat.lng, e.lnglat.lat]);
            });
        }

        if (gps_data && gps_data != '') {
            var arr = [];
            var msg = gps_data.split(',');
            for (var i=0; i<msg.length; i++) {
                arr.push(
                    {
                        lnglat: msg[i].split('/')
                    }
                );
            }
            var gridSize = 60;
            var markerClusterer = new AMap.MarkerCluster(window.map, arr, {gridSize: 60, renderMarker: window.renderMarker});
            markerClusterer.on('click', (item) => {
              if(item.clusterData.length <= 1) {
                return;
              }
              let alllng = 0, alllat = 0;
              for(const mo of item.clusterData) {
                alllng += mo.lnglat.lng;
                alllat += mo.lnglat.lat;
              }
              const lat = alllat / item.clusterData.length;
              const lng = alllng / item.clusterData.length;
              window.map.setZoomAndCenter(window.map.getZoom() + 3, [lng, lat]);
            });
            $('.overlay').hide();
        }
    });
})(jQuery);