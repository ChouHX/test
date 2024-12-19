(function($){
    function obj(ele, opt){
        this.ele = ele;
        this.def = {
        };
        this.opt = $.extend({}, this.def, opt);
        this.init();
    }
    obj.prototype = {
        init: function(){
            var me = this;
            me.ele.find('input.myValidField,select.myValidField').each(function(){
                var vinfo = '', vtype = $(this).attr('data-my-valid-type') || 'required';
                if (vtype == 'required'){
                    vinfo = $lang.FIELD_REQUIRED
                }else{
                    vinfo = $(this).attr('data-my-valid-text');
                }
                // $(this).parent().after('<span class="col-lg-2 myValidFormSpan">*'+vinfo+'</span>');
                $(this).parent().after('<span class="myValidFormSpan">*'+vinfo+'</span>');
            });
        },
        check: function(){
            var me = this, valid = true;
            me.ele.find('input.myValidField,select.myValidField').each(function(){
                var currentValid = true, v = $(this).val().trim(), vtype = $(this).attr('data-my-valid-type') || 'required';
                if (vtype == 'required' && (v == '' || v == 'NULL')){
                    currentValid = valid = false;
                }else if (vtype == 'reg') {
                    var reg = new RegExp($(this).attr('data-my-valid-reg'));
                    if (!reg.test(v)){
                        currentValid = valid = false;
                    }
                }else if (vtype == 'equal'){
                    var pid = $(this).attr('data-my-valid-equalid');
                    if (v != $('#'+pid).val()){
                        currentValid = valid = false;
                    }
                }
                if (currentValid){
                    $(this).removeClass('borderRed');
                    $(this).parent().next().hide();
                }else{
                    $(this).addClass('borderRed');
                    $(this).parent().next().show();
                }
            });
            return valid;
       }
    };
    $.fn.myFormValid = function(options){
        var o = new obj(this, options);
        return o;
    };
})(jQuery);