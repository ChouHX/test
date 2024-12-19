function loadScript(id, src){
    var e = document.getElementById(id);
    if (e) return;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.id = id;
    script.src = src;
    document.getElementsByTagName('head')[0].appendChild(script);
}

function loadStyle(id, url){
    var e = document.getElementById(id);
    if (e) return;
    var link = document.createElement('link');
    link.id = id;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = url;
    document.getElementsByTagName('head')[0].appendChild(link);
}

function isEmptyObject(obj) {
    for (var x in obj){
        return false;
    }
    return true;
}

function load_map_api(cb){
    typeof google == 'undefined' ? $.getScript($.gf.gmap_api,function(){
        cb();
    }) : cb();
}

//生成ThinkPHP URL
function tpurl(a,b,c){
    if (!a){
        a = 'Index';
    }
    var u = $lang.curl.replace('Index/replace', a+'/'+b);
    if (c){
        u += '?'+c;
    }
    return u;
}

//jqgrid set width
function jqgrid_set_width($e, $c, $num){
    $e.jqGrid('setGridWidth', $c.width() + (typeof $num != 'undefined' ? $num : 0));
}

function ajax(url, data, successfn, errorfn, completefn, async, type, dataType){
    async = (async==null || async=="" || typeof(async)=="undefined")? "true" : async;
    type = (type==null || type=="" || typeof(type)=="undefined")? "post" : type;
    dataType = (dataType==null || dataType=="" || typeof(dataType)=="undefined")? "json" : dataType;
    data = (data==null || data=="" || typeof(data)=="undefined")? {"date": new Date().getTime()} : data;
    $.ajax({
        url: url,
        data: data,
        async: async,
        type: type,
        dataType: dataType,
        success: function(d){
            if (typeof successfn != 'undefined' && successfn != ''){
                successfn(d);
            }
        },
        error: function(xhr,status,error){
            if (typeof errorfn == 'undefined' || errorfn == ''){
                if (console){
                    console.log('Ajax error: '+new Date().getTime());
                    console.log('Status: '+status);
                    console.log('Error: '+error);
                }
            }else{
                errorfn(xhr,status,error);
            }
        },
        complete: function(xhr,status){
            if (typeof completefn != 'undefined' && completefn != ''){
                completefn(xhr,status);
            }
        }
    });
};

$.serializeObject = function(form_id){
    var arr = $(form_id).serializeArray(), obj = {};
    for (var i=0; i<arr.length; i++){
        obj[arr[i].name] = arr[i].value;
    }
    return obj;
}


/*params(type,text)
 *type: -1=error, 0=success, 1=warning, 2=精简黑框alert
*/
$.notice = function() {
    var type = arguments[0], text = arguments[1];
    if (type === 2){
        layer.msg(text);
        return;
    }
    if (typeof arguments[0] == 'object'){
        type = arguments[0].status;
        text = arguments[0].info;
    }
    var opts = {
        title: $lang.VAR_PROMPT,
        text: text,
        delay: 1000*(type == 0 ? 3 : (type < 0 ? 30 : 15)), //error时延迟关闭时间为30秒，warning延迟15秒，成功3秒
        hide: true, //是否自动关闭
        mouse_reset: true //鼠标悬停时，时间重置
    };
    if (type == 0){
        opts.type = 1
    }else if (type < 0){
        opts.type = 2
    }else{
        opts.type = 0
    }
    /**
     * layer config
     * icon: 0 = warning, 1 = success, 2 = failed
     */
    layer.open({
        type: 0,
        title: opts.title,
        content: opts.text,
        skin: '',
        area: 'auto',
        offset: 'auto',
        icon: opts.type,
        btn: [],
        // btnAlign: 'c',
        // closeBtn: false,
        shadeClose:true,
        time: opts.delay
    });
}

$.confirm = function(text, ok_fn, no_fn, icon, btn0, btn1){
    /*
    (new PNotify({
        text: text,
        hide: false,
        confirm: {
            confirm: true,
            buttons: [{
                text: $lang.VAR_BTN_SURE
            },{
                text: $lang.VAR_BTN_CANCLE
            }]
        },
        buttons: {
            closer: false,
            sticker: false
        },
        history: {
            history: false
        },
        addclass: 'stack-modal',
        stack: {
            'dir1': 'down',
            'dir2': 'right',
            'modal': true
        }
    })).get().on('pnotify.confirm', function() {
        ok_fn();
    }).on('pnotify.cancel', function() {
        if (typeof no_fn != 'undefined'){
            no_fn();
        }
    });
    */
    layer.confirm(text, {
        icon: typeof icon != 'undefined' ? icon : 3,
        title: $lang.VAR_CONFIRM,
        btn: [btn0 || $lang.VAR_BTN_SURE, btn1 || $lang.VAR_BTN_CANCLE],
        btnAlign:'c'
    }, function(index){
        if (typeof ok_fn != 'undefined'){
            ok_fn();
        }
        layer.close(index);
    }, function(index){
        if (typeof no_fn != 'undefined'){
            no_fn();
        }
        layer.close(index);
    });
}

//注意：该函数大量使用正则，效率低下
Date.prototype.format = function (fmt) {
    var o = {
        "M+": this.getMonth() + 1,  //月份
        "d+": this.getDate(),       //日
        "h+": this.getHours(),      //小时
        "m+": this.getMinutes(),    //分
        "s+": this.getSeconds(),    //秒
        "q+": Math.floor((this.getMonth() + 3) / 3),    //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

//ts = 毫秒
Date.prototype.toYmdhis = function (ts) {
    var y = this.getFullYear(),
        m = this.getMonth() + 1,
        d = this.getDate(),
        h = this.getHours(),
        i = this.getMinutes(),
        s = this.getSeconds();
    if (m < 10) m = '0'+m;
    if (d < 10) d = '0'+d;
    if (h < 10) h = '0'+h;
    if (i < 10) i = '0'+i;
    if (s < 10) s = '0'+s;
    return y+'-'+m+'-'+d+' '+h+':'+i+':'+s;
}

function getExcelData(ajaxurl, params){
    var div = layer.load(1, {
      shade: [0.1,'#fff']
    });
    ajax(ajaxurl, params, function(msg){
        if (msg.status == 0){
            generateExcel(msg.data);
        } else {
            $.notice(msg);
        }
    },'',function(){
        layer.close(div);
    });
}

function get_router_timezone(){
    if (typeof window.RouterTimezone != 'undefined'){
        return window.RouterTimezone;
    }
    RouterTimezone = [];
    for (var i=0; i<$lang.TM_SEL_ARR.length; i++){
        window.RouterTimezone.push({
            id:$lang.TM_SEL_ARR[i][0],
            name:$lang.TM_SEL_ARR[i][1]
        });
    }
    return RouterTimezone;
}

//任务执行状态颜色
function get_task_status_color(rowObject){
    var s = parseInt(rowObject.status_o), cls = '';
    if (s == 3){
        cls = 'warn0';
    }else if (s == 4 || s == 6){
        cls = 'warn1';
    }else if (s == 7){
        cls = 'gray';
    }
    return '<span class="'+cls+'" style="width:100%; display:inline-block;">'+rowObject.status+'</span>';
}

/**
 * 根据设备型号获取参数类型
 * @return string [router, dtu, other]
 * $.inArray(value, array [, fromIndex ])  用于在数组中查找指定值，并返回它的索引值（如果没有找到，则返回-1）
 */
function get_params_type(tm){
    for (var x in $.gf.term_params_type) {
        if ($.inArray(tm, $.gf.term_params_type[x]) != -1){
            return x;
        }
    }
    return 'other';
}

//颜色RGB转hex
function color_rgb_to_hex(color) {
    var rgb = color.split(',');
    var r = parseInt(rgb[0].split('(')[1]);
    var g = parseInt(rgb[1]);
    var b = parseInt(rgb[2].split(')')[0]);

    var hex = "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    return hex;
}

//生成唯一数
function uuid(){
    if (typeof window.last_uuid == 'undefined'){
        window.last_uuid = 0;
    }
    var v = new Date().getTime();
    if (v <= window.last_uuid){
        v = window.last_uuid+1;
    }
    window.last_uuid = v;
    return v;
}

//格式化告警信息
function format_warning_info(v, rowObject){
    if (rowObject.set_type == 1){
        return rowObject.info + ' (' + $lang.CURRENT_VALUE_IS + '：'+v+')';
    }
    var min = parseFloat(rowObject.min), max = parseFloat(rowObject.max);
    v = parseFloat(v);
    var str = (v > max ? $lang.ALARM_STR_GT_MAX : $lang.ALARM_STR_LT_MIN);
    str = str.replace('%a', rowObject.sensor_name);
    str = str.replace('%b', v);
    str = str.replace('%c', v > max ? max : min);
    return str;
}

//格式为位操作符
function trans_bit_op(op){
    if (op == '&') {
        return 'And';
    } else if (op == '|') {
        return 'Or';
    } else {
        return op;
    }
}

// JSON对象转url格式字符串：a=1&c=2
function json2url(obj) {
    return Object.keys(obj).map(function (key) {
        return encodeURIComponent(key) + "=" + encodeURIComponent(obj[key]);
    }).join("&");

}

// 获取email/微信/短信发送状态
function get_alarm_send_status_txt(v, info) {
    var str  = v == 0 ? $lang.UNSENT : (v == 1 ? $lang.VAR_ALARM_SEND_STATUS_ARR[0] : $lang.VAR_ALARM_SEND_STATUS_ARR[-1]);
    var clr  = v == 0 ? 'gray'       : (v == 1 ? '#4caf50' : 'red');
    var err  = v == 2 ? '：'+info : '';
    var icon = v == 0 ? 'fa-minus-circle' : (v == 1 ? 'fa-check-circle' : 'fa-times-circle');
    return '<i style="color:'+clr+'" class="fa '+icon+'">&nbsp;</i>' + str + err;
}

// js浮点数保留小数位
// exp为正用作整数，exp未负用作小数, round(31216, 1) = 31220, round(1.13265, -3) = 1.133
// type = round, floor, ceil
function round(value, exp, type) {
    if (typeof exp == 'undefined') exp = 0;
    if (typeof type == 'undefined') type = 'round';
    // If the exp is undefined or zero...
    if (typeof exp === 'undefined' || +exp === 0) {
        return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // If the value is not a number or the exp is not an integer...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
        return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? +value[1] - exp : -exp)));
    // Shift back
    value = value.toString().split('e');
    value = +(value[0] + 'e' + (value[1] ? +value[1] + exp : exp));
    return value;
}

// 自定义loading显示/隐藏
function auto_loading(mseconds, cb, id) {
    var o = id ? $('#'+id) : $('.my-loading:eq(0)');
    o.show();
    window.setTimeout( function() {
        if (cb) cb();
        o.hide();
    }, mseconds);
}

//生成随机数
function generate_wep(u)
{
    function _wepgen(pass, i)
    {
        while (pass.length < 64) pass += pass;
        return hex_md5(pass.substr(0, 64)).substr(i, ($('[id = "tpid_wl' + u + '_wep_bit"]').val() == 128) ? 26 : 10);
    }

    // var e = E('_wl'+u+'_passphrase');
    var e = $('[id = "tpid_wl' + u + '_passphrase"]')
    var pass = e.val();
    // console.log(pass);
    // if (!v_length(e, false, 3)) return;
    $('[id = "tpid_wl' + u + '_key1"]').val(_wepgen(pass, 0)).trigger('change');
    // E('_wl'+u+'_key1').value = _wepgen(pass, 0);
    pass += '#$%';
    $('[id = "tpid_wl' + u + '_key2"]').val(_wepgen(pass, 2)).trigger('change');
    // E('_wl'+u+'_key2').value = _wepgen(pass, 2);
    pass += '!@#';
    $('[id = "tpid_wl' + u + '_key3"]').val(_wepgen(pass, 4)).trigger('change');
    // E('_wl'+u+'_key3').value = _wepgen(pass, 4);
    pass += '%&^';
    $('[id = "tpid_wl' + u + '_key4"]').val(_wepgen(pass, 5)).trigger('change');
    // E('_wl'+u+'_key4').value = _wepgen(pass, 6);
    // verifyFields(null, 1);
}

//随机生成
function random_wep(u){
    // E('_wl'+u+'_passphrase').value = random_x(16);
    $('[id = "tpid_wl' + u + '_passphrase"]').val(random_x(16)).trigger('change');
    generate_wep(u);
}
function random_psk(id){
    // var e = E(id);
    // e.value = random_x(63);
    // verifyFields(null, 1);
    var e = $('[id = "tpid_' + id + '"]');
    e.val(random_x(63));
    
}

// 获取checkbox选中和未选中的值，cmp=jquery对象，x为控件name
function get_checkbox_check_val(cmp, x) {
    var chk_v = cmp.attr('checkedValue'), un_chk_v = cmp.attr('unCheckedValue');
    // 自定义了选中和未选中值的
    if (chk_v && un_chk_v) {
        ;
    } else if (x.indexOf('_closed') != -1) {
        // 因为可能包含多个wifi，不能使用x == 'wl0_closed来判断
        // 此时选中时为0，不选为1
        chk_v = 0;
        un_chk_v = 1;
    } else if (x == 'lan_proto') {
        // 选中为dhcp，未选中为static
        chk_v = 'dhcp';
        un_chk_v = 'static';
    } else {
        // 默认情况，1=选中，0=未选中
        chk_v =  1;
        un_chk_v = 0;
    }
    return [chk_v, un_chk_v];
}

//生成随机数
function random_x(max){
    var c = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    var s = '';
    while (max-- > 0) s += c.substr(Math.floor(c.length * Math.random()), 1);
    return s;
}

//md5
function hex_md5(s){return binl2hex(core_md5(str2binl(s),s.length*8));}
function core_md5(x,len){x[len>>5]|=0x80<<((len)%32);x[(((len+64)>>>9)<<4)+14]=len;var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;for(var i=0;i<x.length;i+=16){var olda=a;var oldb=b;var oldc=c;var oldd=d;a=md5_ff(a,b,c,d,x[i+0],7,-680876936);d=md5_ff(d,a,b,c,x[i+1],12,-389564586);c=md5_ff(c,d,a,b,x[i+2],17,606105819);b=md5_ff(b,c,d,a,x[i+3],22,-1044525330);a=md5_ff(a,b,c,d,x[i+4],7,-176418897);d=md5_ff(d,a,b,c,x[i+5],12,1200080426);c=md5_ff(c,d,a,b,x[i+6],17,-1473231341);b=md5_ff(b,c,d,a,x[i+7],22,-45705983);a=md5_ff(a,b,c,d,x[i+8],7,1770035416);d=md5_ff(d,a,b,c,x[i+9],12,-1958414417);c=md5_ff(c,d,a,b,x[i+10],17,-42063);b=md5_ff(b,c,d,a,x[i+11],22,-1990404162);a=md5_ff(a,b,c,d,x[i+12],7,1804603682);d=md5_ff(d,a,b,c,x[i+13],12,-40341101);c=md5_ff(c,d,a,b,x[i+14],17,-1502002290);b=md5_ff(b,c,d,a,x[i+15],22,1236535329);a=md5_gg(a,b,c,d,x[i+1],5,-165796510);d=md5_gg(d,a,b,c,x[i+6],9,-1069501632);c=md5_gg(c,d,a,b,x[i+11],14,643717713);b=md5_gg(b,c,d,a,x[i+0],20,-373897302);a=md5_gg(a,b,c,d,x[i+5],5,-701558691);d=md5_gg(d,a,b,c,x[i+10],9,38016083);c=md5_gg(c,d,a,b,x[i+15],14,-660478335);b=md5_gg(b,c,d,a,x[i+4],20,-405537848);a=md5_gg(a,b,c,d,x[i+9],5,568446438);d=md5_gg(d,a,b,c,x[i+14],9,-1019803690);c=md5_gg(c,d,a,b,x[i+3],14,-187363961);b=md5_gg(b,c,d,a,x[i+8],20,1163531501);a=md5_gg(a,b,c,d,x[i+13],5,-1444681467);d=md5_gg(d,a,b,c,x[i+2],9,-51403784);c=md5_gg(c,d,a,b,x[i+7],14,1735328473);b=md5_gg(b,c,d,a,x[i+12],20,-1926607734);a=md5_hh(a,b,c,d,x[i+5],4,-378558);d=md5_hh(d,a,b,c,x[i+8],11,-2022574463);c=md5_hh(c,d,a,b,x[i+11],16,1839030562);b=md5_hh(b,c,d,a,x[i+14],23,-35309556);a=md5_hh(a,b,c,d,x[i+1],4,-1530992060);d=md5_hh(d,a,b,c,x[i+4],11,1272893353);c=md5_hh(c,d,a,b,x[i+7],16,-155497632);b=md5_hh(b,c,d,a,x[i+10],23,-1094730640);a=md5_hh(a,b,c,d,x[i+13],4,681279174);d=md5_hh(d,a,b,c,x[i+0],11,-358537222);c=md5_hh(c,d,a,b,x[i+3],16,-722521979);b=md5_hh(b,c,d,a,x[i+6],23,76029189);a=md5_hh(a,b,c,d,x[i+9],4,-640364487);d=md5_hh(d,a,b,c,x[i+12],11,-421815835);c=md5_hh(c,d,a,b,x[i+15],16,530742520);b=md5_hh(b,c,d,a,x[i+2],23,-995338651);a=md5_ii(a,b,c,d,x[i+0],6,-198630844);d=md5_ii(d,a,b,c,x[i+7],10,1126891415);c=md5_ii(c,d,a,b,x[i+14],15,-1416354905);b=md5_ii(b,c,d,a,x[i+5],21,-57434055);a=md5_ii(a,b,c,d,x[i+12],6,1700485571);d=md5_ii(d,a,b,c,x[i+3],10,-1894986606);c=md5_ii(c,d,a,b,x[i+10],15,-1051523);b=md5_ii(b,c,d,a,x[i+1],21,-2054922799);a=md5_ii(a,b,c,d,x[i+8],6,1873313359);d=md5_ii(d,a,b,c,x[i+15],10,-30611744);c=md5_ii(c,d,a,b,x[i+6],15,-1560198380);b=md5_ii(b,c,d,a,x[i+13],21,1309151649);a=md5_ii(a,b,c,d,x[i+4],6,-145523070);d=md5_ii(d,a,b,c,x[i+11],10,-1120210379);c=md5_ii(c,d,a,b,x[i+2],15,718787259);b=md5_ii(b,c,d,a,x[i+9],21,-343485551);a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd);}return Array(a,b,c,d);}
function md5_cmn(q,a,b,x,s,t){return safe_add(bit_rol(safe_add(safe_add(a,q),safe_add(x,t)),s),b);}
function md5_ff(a,b,c,d,x,s,t){return md5_cmn((b&c)|((~b)&d),a,b,x,s,t);}
function md5_gg(a,b,c,d,x,s,t){return md5_cmn((b&d)|(c&(~d)),a,b,x,s,t);}
function md5_hh(a,b,c,d,x,s,t){return md5_cmn(b^c^d,a,b,x,s,t);}
function md5_ii(a,b,c,d,x,s,t){return md5_cmn(c^(b|(~d)),a,b,x,s,t);}
function safe_add(x,y){var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<<16)|(lsw&0xFFFF);}
function bit_rol(num,cnt){return(num<<cnt)|(num>>>(32-cnt));}
function str2binl(str){var bin=Array();var mask=(1<<8)-1;for(var i=0;i<str.length*8;i+=8)bin[i>>5]|=(str.charCodeAt(i/8)&mask)<<(i%32);return bin;}
function binl2hex(binarray){var hex_tab="0123456789ABCDEF";var str="";for(var i=0;i<binarray.length*4;i++){str+=hex_tab.charAt((binarray[i>>2]>>((i%4)*8+4))&0xF)+hex_tab.charAt((binarray[i>>2]>>((i%4)*8))&0xF);}return str;}
