<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
#gmc_container{
    background-color: #e4e9f0;
    margin: 0;
    padding: 0;
    height: 201px;
    /*margin-left: 15px;*/
    border: 1px dashed #D2D6DE;
}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
    <h4 class="modal-title" id="h4_add_edit"><?php echo (L("PROJECT_CONFIGURATION")); ?></h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="modal_fm">
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("DEVICE_NAME")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input type="hidden" name="sn" value="%s">
                <input type="hidden" name="lat" value="0">
                <input type="hidden" name="lng" value="0">
                <input type="hidden" name="set_gps" value="0">
                <input type="text" class="form-control" name="name" value="%s">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("DETAIL_ADDR")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input class="form-control" name="address" value="%s">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("CONTACT")); ?>:</label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="contact" value="%s">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("TEL")); ?>:</label>
            <div class="col-md-8">
                <input class="form-control" name="tel" value="%s">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("GPS_SETUP")); ?>:</label>
            <div class="col-md-8">
                <div id="gmc_container"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3"><?php echo (L("POS_QUERY")); ?>:</label>
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="addr_content" placeholder="<?php echo (L("POS_QUERY_PLACEHOLDER")); ?>" />
                    <span class="input-group-addon" style="cursor: pointer;" id="parse_addr"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-success submit_project_edit"><?php echo (L("SAVE_TEMPLATE")); ?></button>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
</div>
<script type="text/javascript">
(function($){
$(document).ready(function(){
    $('.submit_project_edit').on('click', function(){
        var obj = $("#modal_fm").data('bootstrapValidator');
        obj.validate();
        if (!obj.isValid()){
            return;
        }
        ajax(tpurl('Rtu','projectEdit'), $('#modal_fm').serialize(), function(msg){
            $.notice(msg);
            var from = "<?php echo ($_REQUEST['from']); ?>";
            if (msg.status == 0){
                $('#myLgModal').modal('hide');
                from == 'lora' ? $('#list2').trigger('reloadGrid') : location.reload();
            }
        });
    });

    $('#modal_fm').bootstrapValidator({
       feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            },
            address: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            }
        }
    });

    var myLatLng = {lat:%s, lng:%s};
    $.gf.setFormGps = function(lng, lat){
        $('#modal_fm input[name=lng]').val(lng);
        $('#modal_fm input[name=lat]').val(lat);
    }
    window.map_init = function(){
        $.getScript($.gf.lang == 'zh-cn' ? $.gf.gaode_ui:$.gf.empty_js, function(){
            map_create('gmc_container', myLatLng.lat == 0 ? 2:8, myLatLng.lng, myLatLng.lat);
            $.gf.marker = map_add_marker(myLatLng.lng, myLatLng.lat, '', $lang.DRAG_GET_LNGLAT, true);
            map_add_event($.gf.marker, 'dragend', function(e){
                var ret = map_get_pos_by_event(e);
                $.gf.setFormGps(ret.lng, ret.lat);
                $('#modal_fm input[name=set_gps]').val(1);
            });
            map_add_event($.gf.map, 'click', function(e) {
                var ret = map_get_pos_by_event(e);
                map_set_marker_pos($.gf.marker, ret.lng, ret.lat);
                ret = $.gf.marker.getPosition();
                map_set_center(ret.lng, ret.lat);
                $.gf.setFormGps(ret.lng, ret.lat);
                $('#modal_fm input[name=set_gps]').val(1);
            });
        });
    };
    mapjs_load();

    // 地址解析成坐标
    $('#parse_addr').on('click', function(){
        var v = $('#addr_content').val().trim();
        if (v == '') return;
        ajax(tpurl('Term', 'geocode'), {address:v, type:0}, function(msg){
            var coord = msg.data.split(',');
            map_set_marker_pos($.gf.marker, coord[0], coord[1]);
            map_set_center(coord[0], coord[1]);
            map_set_zoom(15);
            $.gf.setFormGps(coord[0], coord[1]);
            $('#modal_fm input[name=set_gps]').val(1);
        });
    });
    $('#addr_content').on('keydown', function(e){
        if (e.keyCode == 13) {
            $('#parse_addr').click();
            return false;
        }
    });
});
})(jQuery);
</script>