<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
#modal_fm div.checkbox-inline{
    margin-right: 15px;
}
#modal_fm div.checkbox-inline label{
    cursor: pointer;
}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
    <h4 class="modal-title" id="h4_add_edit"><?php echo (L("VAR_ADD")); ?></h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" id="modal_fm">
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("CONFIG_TYPE")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <label class="radio-inline">
                    <input type="radio" name="set_type" value="0" checked="checked">&nbsp;<?php echo (L("COLLECTION_CONFIG")); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="set_type" value="1">&nbsp;<?php echo (L("ALARM_CONFIG")); ?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("VAR_NAME")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="name">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("SLAVE_ID")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="slave_id" value="1" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("ADDR")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="addr">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("UNIT")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <input type="text" class="form-control" name="unit">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("VALUE_TYPE")); ?>:<span class="required-field"></span></label>
            <div class="col-md-8">
                <select class="form-control" name="value_type">
                    <option value="4" selected><?php echo (L("ANALOG_QUANTITY")); ?></option>
                    <option value="3"><?php echo (L("SINGLE_BYTES")); ?></option>
                    <option value="2"><?php echo (L("DOUBLE_BYTES")); ?></option>
                    <option value="1"><?php echo (L("FOUR_BYTES")); ?></option>
                </select>
            </div>
        </div>
        <div id="minmax_fields">
            <div class="form-group">
                <label class="col-md-2"><?php echo (L("MIN")); ?>:<span class="required-field"></span></label>
                <div class="col-md-8">
                    <input class="form-control" name="min">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2"><?php echo (L("MAX")); ?>:<span class="required-field"></span></label>
                <div class="col-md-8">
                    <input class="form-control" name="max">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2"><?php echo (L("DATA_CORRECTION")); ?>:</label>
            <div class="col-md-4">
                <select class="form-control" name="operator" id="operator">
                    <option value=""><?php echo (L("SELECT_OPERATOR_TIPS")); ?></option>
                    <option value="*"><?php echo (L("OP_MULTIPLY")); ?></option>
                    <option value="+"><?php echo (L("OP_ADD")); ?></option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="op_value" id="op_value" placeholder="<?php echo (L("OPERATIONAL_COEFFICIENT")); ?>" readonly />
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label"><?php echo (L("MORE_ITEMS")); ?>:</label>
            <div class="col-md-8">
                <i class="fa fa-angle-down" id="show_addon_fields" style="cursor: pointer; margin-top: 6px; font-weight: 700; font-size: 15px"></i>
                <i class="fa fa-angle-up" id="hide_addon_fields" style="display: none; cursor: pointer; margin-top: 6px; font-weight: 700; font-size: 15px"></i>
            </div>
        </div>
        <div id="addon_fields" style="display: none;">
            <div class="form-group">
                <label class="col-md-2"><?php echo (L("VAR_CODE")); ?>:</label>
                <div class="col-md-8">
                    <input class="form-control" name="code">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2"><?php echo (L("TRIGGER_ALARM_LEVEL")); ?>:</label>
                <div class="col-md-8">
                    <input class="form-control" name="warn_level">
                </div>
            </div>
            <!--<div class="form-group">
                <label class="col-md-2"><?php echo (L("CORRECTION_VALUE")); ?>:</label>
                <div class="col-md-8">
                    <input class="form-control" name="revised">
                </div>
            </div>-->
            <div class="form-group">
                <label class="col-md-2"><?php echo (L("VAR_NOTE")); ?>:</label>
                <div class="col-md-8">
                    <textarea class="form-control" name="info"></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-success submit_sensor_add"><?php echo (L("SAVE_TEMPLATE")); ?></button>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
</div>
<script type="text/javascript">
(function($){
$(document).ready(function(){
    $('.submit_sensor_add').on('click', function(){
        var obj = $("#modal_fm").data('bootstrapValidator');
        obj.validate();
        if (!obj.isValid()){
            return;
        }
        ajax(tpurl('Rtu', 'sensorTypeAdd'), $('#modal_fm').serialize()+'&act=add', function(msg){
            $.notice(msg);
            if (msg.status == 0){
                $("#list2").trigger('reloadGrid');
                $('#myLgModal').modal('hide');
            }
        });
    });

    $('#operator').on('change', function(){
        var o = $('#op_value'), v = $(this).val();
        if (v == '') {
            o.val('').attr('readonly', true);
        } else {
            o.removeAttr('readonly');
        }
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
            slave_id: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            },
            addr: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            },
            unit: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            },
            value_type: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    }
                }
            },
            min: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    },
                    regexp: {
                        regexp: /^[+|-]?\d*\.?\d*$/,
                        message: $lang.NUMBER_VALIDATE
                    },
                    stringLength: {
                        min: 1,
                        max: 7,
                        message: $lang.LENGTH_1_TO_7
                    }
                }
            },
            max: {
                validators: {
                    notEmpty: {
                        message: $lang.FIELD_REQUIRED
                    },
                    regexp: {
                        regexp: /^[+|-]?\d*\.?\d*$/,
                        message: $lang.NUMBER_VALIDATE
                    },
                    stringLength: {
                        min: 1,
                        max: 7,
                        message: $lang.LENGTH_1_TO_7
                    }
                }
            }
        }
    });

    $('#show_addon_fields').on('click',function(){
        $('#addon_fields').show();
        $(this).hide().next().show();
    });
    $('#hide_addon_fields').on('click',function(){
        $('#addon_fields').hide();
        $(this).hide().prev().show();
    });

    $('input[name=set_type]').on('change',function(){
        var v = $(this).val(), mm = $('#minmax_fields'), unit_require = $('input[name=unit]').parent().parent().find('.required-field');
        var info = $('textarea[name=info]').parent().parent();
        $('#modal_fm').bootstrapValidator('enableFieldValidators', 'min', v==0);
        $('#modal_fm').bootstrapValidator('enableFieldValidators', 'max', v==0);
        v == 1 ? mm.hide() : mm.show();
        $('select[name=value_type]').val(v == 1 ? '3' : '4');

        v == 1 ? unit_require.hide() : unit_require.show();
        $('#modal_fm').bootstrapValidator('enableFieldValidators', 'unit', v==0);

        if (v == 1){
            $('#minmax_fields').before(info);
            info.find('label').html($lang.ALARM_INFO+':');
        }else{
            $('#addon_fields').append(info);
            info.find('label').html($lang.VAR_NOTE+':');
        }
    });
});
})(jQuery);
</script>