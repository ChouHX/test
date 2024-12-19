function SystemParams(){
    this.maxFormId = 11;
    this.webhostfilters = '';
    this.changedParams = [];
    this.paramsLoading = false;
    this.paramsName = typeof arguments[0] != 'undefined' ? arguments[0] : '';
    if (typeof store_specific_rules != 'undefined' && store_specific_rules){
        store_specific_rules.removeAll();
    }
    this.bindEvent();
}

SystemParams.prototype.saveChangedParams =  function(name, v){
    if (!this.paramsLoading) {
        this.changedParams[name] = v;
    }
    // console.log(this.changedParams);
}

SystemParams.prototype.bindEvent = function(){
    var _form, _fields, me = this, name = '', value = '';
    for (var i=1; i<=me.maxFormId; i++){
        _form = $('#form_param_'+i);
        _fields = _form.find('input,select,textarea');
        _fields.each(function(){
            if (this.id.indexOf('tpid_') == -1) {
                return true;
            }
            $(this).on('change', function(){
                if (!me.paramsLoading){
                    name = this.name;
                    if (this.type == 'checkbox'){
                        value = $(this).is(':checked') ? 1 : 0;
                    } else {
                        value = $(this).val();
                    }
                    me.saveChangedParams(name, value);
                }
            });
        });
    }
}

SystemParams.prototype.setFieldsValue = function(msg){
    for (var i=1,fm=null; i<=this.maxFormId; i++){
        fm = $('#form_param_'+i).get(0);
        if (fm) {
            fm.reset();
        }
    }
    for (var x in msg){
        var cmp = $('#tpid_'+x);
        if (cmp){
            var tag = cmp.prop('nodeName') + '';
            if (cmp.attr('type') == 'checkbox'){
                cmp.prop('checked', msg[x] == 1 ? true : false);
            } else if (tag.toLocaleLowerCase() == 'label') {
                cmp.html(msg[x]);
            } else {
                cmp.val(msg[x]);
            }
        }
    }
}

SystemParams.prototype.load = function(msg){
    var me = this;
    me.changedParams = [];
    me.paramsLoading = true;
    $('.my-loading').show();
    $.ajax({
        type: 'post',
        url: $lang.curl.replace('Index/replace', 'Syscfg/loadSystemParams'),
        data: {name: me.paramsName},
        success: function(msg){
            msg = $.parseJSON(msg);
            me.setFieldsValue(msg);
            me.paramsLoading = false;
        },
        complete:function(){
            $('.my-loading').hide();
        },
        error: function(){
            me.paramsLoading = false;
        }
    });
}

SystemParams.prototype.getParams = function(){
    var me = this, arr = [];
    for (key in me.changedParams) {
        arr.push(key+'='+me.changedParams[key]);
    }
    if (arr.length == 0) {
        $.notice(1,$lang.VAR_NO_CHANGE);
        return false;
    }
    return arr.join(',');
}

SystemParams.prototype.resetParams = function(){
    this.changedParams = [];
}