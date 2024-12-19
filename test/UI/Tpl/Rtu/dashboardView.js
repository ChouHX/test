(function($){
    $.gf.init = function(){
        var ctx = new fabric.Canvas('canvas',{});
        /*
        ctx.on('object:added', function(opt){
            if (opt.target.type != 'button') return;
            setTimeout(function(){
                ctx.renderAll();
            }, 1000);
        });
        */
        $.gf.ctx = ctx;
        var nodes = JSON.parse($.gf.data);
        for (var i=0, id=null; i<nodes.length; i++){
            if (nodes[i].type == 'ctx'){
                ctx.setWidth(nodes[i].width);
                ctx.setHeight(nodes[i].height);
                ctx.setBackgroundColor(nodes[i].bgcolor);
                if (nodes[i].bgimg){
                    ctx.setBackgroundImage(nodes[i].bgimg, ctx.renderAll.bind(ctx), {width:parseFloat(nodes[i].width), height:parseFloat(nodes[i].height)});
                }
                $('#attr_ctx_backgroundColor').css('background-color',nodes[i].bgcolor).val(nodes[i].bgcolor.substr(1));
            }else{
                id = 'node_' + uuid();
                $.gf.nodes[id] = new FBC(null, null, null, null, id, $.gf.ctx, nodes[i]);
            }
        }
        for (var x in $.gf.nodes){
            var sid = $.gf.nodes[x].sid, t = $.gf.nodes[x].type, alarmRules = $.gf.nodes[x].alarmRules;
            if (sid != 0){
                if ($.inArray(t, ['number_text','gauge1','gauge2','button']) != -1){
                    $.gf.current_v.push({nodeid:x, type:t, sid:sid});
                    if ($.inArray(sid, $.gf.current_v_sids) == -1){
                        $.gf.current_v_sids.push(sid);
                    }
                } else if ($.inArray(t, ['line1', 'line2', 'column', 'pie']) != -1) {
                    $.gf.history_v.push({nodeid:x, type:t, sid:sid});
                    if ($.inArray(sid, $.gf.history_v_sids) == -1){
                        $.gf.history_v_sids.push(sid);
                        $.gf.history_v_datanums.push($.gf.nodes[x].datanum || 10);
                    }
                }
            } else if (alarmRules){
                //设置了告警规则的图片
                alarmRules = alarmRules.split('|ROW|');
                for (var j=0,columns=null,rules=[]; j<alarmRules.length; j++){
                    columns = alarmRules[j].split('|XXX|');
                    rules.push({
                        sid: columns[0],
                        simbol: columns[1],
                        val: columns[2]
                    });
                    if ($.inArray(parseInt(columns[0]), $.gf.current_v_sids) == -1){
                        $.gf.current_v_sids.push(parseInt(columns[0]));
                    }
                }
                $.gf.current_v.push({nodeid:x, type:t, sid:0, rules:rules});
            }
        }
    }

    $.gf.set_current_v = function(nid, type, value){
        if (type == 'number_text'){
            $.gf.nodes[nid].node.set('text', value.toFixed(2)+'');
        } else if (type == 'gauge1' || type == 'gauge2'){
            var chart = $.gf.nodes[nid].chart, opts = chart.getOption();
            opts.series[0].data[0].value = value.toFixed(1);
            chart.setOption(opts);
        } else if (type == 'button'){
            setTimeout(function(){
                $.gf.nodes[nid].node._originalElement.src = $.img_path + 'switch'+value+'.png';
            }, 50);
        }
        setTimeout(function(){$.gf.ctx.requestRenderAll()}, 300);
    }

    $.gf.set_alarm_status = function(nid, type, is_alarm){
        var node = $.gf.nodes[nid].node;
        if (!node) return;
        if (is_alarm){
            node.filters[0] = new fabric.Image.filters.BlendColor({color:'#e60013', mode:'overlay', alpha:1});
        }else{
            node.filters.length = 0;
        }
        node.applyFilters();
        $.gf.ctx.requestRenderAll();
    }

    $.gf.set_history_v = function(nid, type, value){
        if (type == 'line1' || type == 'line2' || type == 'column'){
            var chart = $.gf.nodes[nid].chart, opts = chart.getOption();
            opts.xAxis[0].data = value.x;
            opts.series[0].data = value.y;
            chart.setOption(opts);
        }
        setTimeout(function(){$.gf.ctx.requestRenderAll()}, 300);
    }

    $.gf.check_rules = function(d, rules){
        //所有规则，满足一个即告警
        var ret = 0;
        for (var i=0,v=null,threshold=null,simbol=null; i<rules.length; i++){
            if (typeof d[rules[i].sid] != 'undefined'){
                v = d[rules[i].sid];
                threshold = parseFloat(rules[i].val);
                simbol = rules[i].simbol;
                if ((simbol == 'lt' && v < threshold) || (simbol == 'le' && v <= threshold) || (simbol == 'gt' && v > threshold) || (simbol == 'ge' && v >= threshold)){
                    ret = 1;
                    break;
                }
            }
        }
        return ret;
    }

    $.gf.refresh_current_v = function(){
        ajax(tpurl('Rtu','loadDashboardData'), {sn:$.gf.sn, data_type:0, sids:$.gf.current_v_sids.join(',')}, function(msg){
            for (var i=0,nid='',type='',sid='',rules=null,d=msg.data; i<$.gf.current_v.length; i++){
                nid = $.gf.current_v[i].nodeid;
                type = $.gf.current_v[i].type;
                sid = $.gf.current_v[i].sid;
                rules = $.gf.current_v[i].rules;
                if (sid == 0){
                    //img告警状态
                    var s = $.gf.check_rules(d, rules);
                    $.gf.set_alarm_status(nid, type, s);
                } else if (typeof d[sid] != 'undefined'){
                    $.gf.set_current_v(nid, type, d[sid]);
                }
            }
        });
    }

    $.gf.refresh_history_v = function(){
        ajax(tpurl('Rtu','loadDashboardData'), {sn:$.gf.sn, data_type:1, sids:$.gf.history_v_sids.join(','), pagesize:$.gf.history_v_datanums.join(',')}, function(msg){
            for (var i=0,nid='',type='',sid='',d=msg.data; i<$.gf.history_v.length; i++){
                nid = $.gf.history_v[i].nodeid;
                type = $.gf.history_v[i].type;
                sid = $.gf.history_v[i].sid;
                if (typeof d[sid] != 'undefined' && d[sid].x.length > 0){
                    $.gf.set_history_v(nid, type, d[sid]);
                }
            }
        });
    }

$(function(){
    $.gf.init();
    $.gf.ctx.selectable = false;
    // $.gf.ctx.skipTargetFind = true;

    if ($.gf.sn){
        //刷新数据
        if ($.gf.current_v_sids.length > 0){
            $.gf.refresh_current_v();
            window.setInterval(function(){$.gf.refresh_current_v()}, 3000);
        }
        if ($.gf.history_v_sids.length > 0){
            $.gf.refresh_history_v();
            window.setInterval(function(){$.gf.refresh_history_v()}, 10000);
        }
    }

    //滚动条
    $('body').niceScroll({cursorcolor:"#cccccc"});
});
})(jQuery);