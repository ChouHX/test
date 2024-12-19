(function($){
    var currGroupId = -10;
    $(document).ready(function(){
        $('#gmc_container').css('height',window.innerHeight-101);
        $('body').css('overflow','hidden');
        $.ajax({
            url: $lang.curl.replace('Index/replace', 'Term/loadLatestPos')+'?gid='+currGroupId,
            success: function(msg){
                msg = $.parseJSON(msg);
                if (typeof msg != 'object') return;
                window.gmc = new GMC('gmc_container', msg);
            }
        });

        //Search
        $('#search_container input').keyup(function(e){
            if (e.keyCode == 13){
                gmc.searchPoint(this.value);
            }
        });
        $('#search_container span').click(function(e){
            gmc.searchPoint($(this).prev().val());
        });

        //Finish gps edit
        $('#tooltip_container .btn-save-gps').click(function(){
            var markers = gmc.getAddedMarkers(), sn = $.gf.mapCurrentSn;
            if (markers == ''){
                $.notice(1,$lang.PLEASE_MARK_FIRST);
                return;
            }
            map_remove_event(gmc.mapClickListener);
            $('.infoWindowTable tr:last button').removeAttr('disabled');
            gmc.clearMarkersTmp();
            $('#tooltip_container').hide();
            ajax(tpurl('Term','setgps'),{sn:sn, markers:markers},function(msg){
                if (msg.data){
                    markers = markers.split(',');
                    gmc.moveto(sn, parseFloat(markers[0]), parseFloat(markers[1]));
                }
                $.notice(2,msg.info);
            });
        });

        // Replay toolbar
        $('#replay_container .input-group.date').datepicker({
            language: $.gf.lang,
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true
        });
        $('#time1').timepicker({
            defaultTime: '00:00:00',
            showMeridian: false,
            showSeconds:true
        });
        $('#time2').timepicker({
            defaultTime: '23:59:59',
            showMeridian: false,
            showSeconds:true
        });

        //Slider
        $.gf.mySlider = $('#replay_container .slider').slider();
        $.gf.mySlider.on("slide", function(slideEvt) {
            gmc.setSpeed(slideEvt.value);
        });


        //Replay
        $('.btn-replay').click(function(){
            ajax(tpurl('Term','loadGpsData'), {
                sn:$.gf.mapCurrentSn,
                date: new Date($('#replay_container .input-group.date').datepicker('getDate')).getTime() / 1000,
                start: $("#time1").val(),
                end: $("#time2").val()
            }, function(msg){
                if (typeof msg.data != 'object' || msg.data.length == 0){
                    $.notice(1,$lang.VAR_NO_GPS_DATA_RESET_TIME);
                    return;
                }
                $('#replay_container button').attr('disabled',true);
                gmc.prepareDraw(msg.data, function(){
                    $('#replay_container button').removeAttr('disabled');
                });
            });
        });

        //Close container
        $('.btn-close').click(function(){
            gmc.resetContainer();
        });
    });
})(jQuery);