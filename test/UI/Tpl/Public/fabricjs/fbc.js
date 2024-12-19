function FBC(x, y, type, bind, id, ctx, cnode, is_clone, imgpath){
    var CP = typeof cnode != 'undefined' ? cnode : null;
    this.ctx                = ctx;
    this.node               = null;                                 //fabricjs节点
    this.chart              = null;                                 //echarts对象
    this.left               = CP ? CP.left+(is_clone?50:0) : x;     //横坐标
    this.top                = CP ? CP.top+(is_clone?50:0) : y;      //纵坐标
    this.type               = CP ? CP.type : type;                  //节点类型
    this.bind               = CP ? CP.bind : bind;                  //是否可绑定传感量
    this.id                 = id;
    this.angle              = CP ? CP.angle : 0;
    this.fontFamily         = CP ? CP.fontFamily : 'Arial';
    this.fontSize           = CP ? CP.fontSize : 30;
    this.fontWeight         = CP ? CP.fontWeight : 'normal';
    this.backgroundColor    = CP ? CP.backgroundColor : '#FFFFFF',
    this.fill               = CP ? CP.fill : '#000000';
    this.transparentCorners = false;                                //拖拽控件背景是否透明
    this.cornerSize         = 8;                                    //拖拽控件大小
    this.borderDashArray    = [3,3];                                //拖拽边框设置为虚线
    this.objectCaching      = CP ? CP.objectCaching : false;
    this.width              = CP ? CP.width : 'auto';
    this.height             = CP ? CP.height : 'auto';
    this.scaleX             = CP ? CP.scaleX : 1;               //X轴缩放，默认为1不缩放
    this.scaleY             = CP ? CP.scaleY : 1;               //y轴缩放，默认为1不缩放
    this.strokeWidth        = CP ? CP.strokeWidth : 0;          //边框宽度，默认为0
    this.stroke             = CP ? CP.stroke : '#000000';       //边框颜色
    this.strokeDashArray    = CP ? CP.strokeDashArray : [];     //边框样式，实线=[]，点状=[0.5,1]，虚线=[3,3]
    this.originX            = CP ? CP.originX : 'left';         //X横轴中心点，center, left, right
    this.originY            = CP ? CP.originY : 'top';          //Y横轴中心点，center, left, right
    this.flipX              = CP ? CP.flipX : false;            //水平翻转，boolean
    this.flipY              = CP ? CP.flipY : false;            //垂直翻转，boolean
    this.text               = CP ? CP.text : '';
    this.imgpath            = CP ? CP.imgpath : imgpath;        //图片路径
    //传感量相关属性
    this.sid                = CP ? CP.sid   : 0;
    this.sunit              = CP ? CP.sunit : '';
    this.smin               = CP ? CP.smin  : 0;
    this.smax               = CP ? CP.smax  : 0;
    this.sname              = CP ? CP.sname : '';
    this.datanum            = CP ? CP.datanum : 10;
    this.alarmRules         = CP ? CP.alarmRules : '';
    this.init();
}

FBC.prototype.init = function(){
    switch (this.type){
        case 'static_text':
            this.addStaticText();
            break;
        case 'number_text':
            this.addNumberText();
            break;
        case 'ml_text':
            this.addMultipleText();
            break;
        case 'button':
            this.addButton();
            break;
        case 'gauge1':
            this.addGauge1();
            break;
        case 'gauge2':
            this.addGauge2();
            break;
        case 'line1':
            this.addLine1();
            break;
        case 'line2':
            this.addLine2();
            break;
        case 'column':
            this.addColumn();
            break;
        case 'pie':
            this.addPie();
            break;
        case 'img':
            this.addImg();
            break;
        default:
            break;
    }
}

FBC.prototype.getOptions = function(){
    var opts = {
        fill: this.fill,
        left: this.left,
        top: this.top,
        angle: this.angle,
        transparentCorners: this.transparentCorners,
        cornerSize: this.cornerSize,
        borderDashArray: this.borderDashArray,
        scaleX: this.scaleX,
        scaleY: this.scaleY,
        strokeWidth: this.strokeWidth,
        stroke: this.stroke,
        strokeDashArray: this.strokeDashArray,
        originX: this.originX,
        originY: this.originY,
        flipX: this.flipX,
        flipY: this.flipY,
        imgpath: this.imgpath,
        //传感量相关属性
        sid  : this.sid,
        sunit: this.sunit,
        smin : this.smin,
        smax : this.smax,
        sname: this.sname,
        alarmRules: this.alarmRules
    };
    if ($.inArray(this.type, ['static_text', 'number_text', 'ml_text']) != -1){
        $.extend(opts, {
            fontFamily: this.fontFamily,
            fontSize: this.fontSize,
            fontWeight: this.fontWeight,
            backgroundColor: this.backgroundColor
        });
    } else if (this.type == 'button' || this.type == 'img'){
        $.extend(opts, {backgroundColor:(this.backgroundColor != '#FFFFFF' ? this.backgroundColor : 'rgba(255, 255, 255, 0)')});
    } else if ($.inArray(this.type, ['gauge1','gauge2','pie']) != -1){
        $.extend(opts, {backgroundColor:(this.backgroundColor != '#FFFFFF' ? this.backgroundColor : '#1B1B1B')});
    } else if ($.inArray(this.type, ['line1', 'line2', 'column']) != -1){
        $.extend(opts, {backgroundColor:(this.backgroundColor != '#FFFFFF' ? this.backgroundColor : '#E2E5EC'), datanum:this.datanum});
    }
    opts.id = this.id;
    opts.type = this.type;
    opts.bind = this.bind;
    if (location.href.indexOf('dashboardView') != -1){
        //设置预览页面的node不可编辑
        // opts.hasControls = false;
        opts.selectable = false;
        // opts.hasBorders = false;
    }
    return opts;
}

FBC.prototype.addStaticText = function(){
    this.node = new fabric.Text(this.text || $lang.STATIC_TEXT, this.getOptions());
    this.ctx.add(this.node);
}

FBC.prototype.addNumberText = function(){
    this.node = new fabric.Text('0.00', this.getOptions());
    this.ctx.add(this.node);
}

FBC.prototype.addMultipleText = function(){
    this.node = new fabric.Text(this.text || $lang.MULTILINE_TEXT+"\n"+$lang.MULTILINE_TEXT, this.getOptions());
    this.ctx.add(this.node);
}

FBC.prototype.addButton = function(){
    var me = this;
    fabric.Image.fromURL($.img_path+'switch0.png', function(node) {
        node.set(me.getOptions());
        // node.scale(0.4);
        node.setCoords();
        me.ctx.add(node);
        me.ctx.calcOffset();
        me.node = node;
    });
}

FBC.prototype.addGauge1 = function(){
    this.addChart();
}

FBC.prototype.addGauge2 = function(){
    this.addChart();
}

FBC.prototype.addLine1 = function(){
    this.addChart();
}

FBC.prototype.addLine2 = function(){
    this.addChart();
}

FBC.prototype.addColumn = function(){
    this.addChart();
}

FBC.prototype.addPie = function(){
    this.addChart();
}

FBC.prototype.addImg = function(){
    var me = this;
    fabric.Image.fromURL(me.imgpath, function(node) {
        node.set(me.getOptions());
        node.setCoords();
        me.ctx.add(node);
        me.ctx.calcOffset();
        me.node = node;
    });
}

FBC.prototype.addChart = function(){
    var me = this, opts = me.getOptions(), ele = document.createElement('canvas');
    ele.id = opts.id.replace('node','ele');
    ele.width  = me.width  != 'auto' ? me.width  : $.ec_opts[me.type].w.replace('px','');
    ele.height = me.height != 'auto' ? me.height : $.ec_opts[me.type].h.replace('px','');
    ele.style.display = 'none';
    document.body.insertBefore(ele, $('.menu').get(0));
    me.chart = echarts.init(document.getElementById(ele.id));
    $.ec_opts[me.type].backgroundColor = opts.backgroundColor; //设置图表背景色
    me.chart.setOption($.ec_opts[me.type]);
    me.node = new fabric.Image(ele, opts);
    var opts = me.chart.getOption();
    if (me.sid != 0 && (me.type == 'gauge1' || me.type == 'gauge2')){
        opts.series[0].min = me.smin;
        opts.series[0].max = me.smax;
        opts.series[0].data[0].name = me.sname;
        opts.series[0].data[0].value = 0;
        opts.series[0].detail.formatter = '{value}' + me.sunit;
    }
    if (me.fill != '#000000'){
        if (me.type == 'gauge1' || me.type == 'gauge2'){
            opts.series[0].axisLabel.textStyle.color = me.fill;
            opts.series[0].title.textStyle.color = me.fill;
            opts.series[0].detail.textStyle.color = me.fill;
        } else {
            opts.textStyle.color = me.fill;
        }
    }
    if ($.inArray(me.type, ['line1','line2','column']) != -1 && location.href.indexOf('dashboardView') != -1){
        opts.xAxis[0].data = ['None'];
        opts.series[0].data = [0];
    }
    me.chart.setOption(opts);
    me.ctx.add(me.node);
}

//显示属性
FBC.prototype.showAttr = function(){
    var node = this.node;
    var attr = {
        left: Number(node.left).toFixed(1),
        top: Number(node.top).toFixed(1),
        scaledWidth: (node.width * node.scaleX).toFixed(1),
        scaledHeight: (node.height * node.scaleY).toFixed(1),
        width: node.width.toFixed(1),
        height: node.height.toFixed(1),
        angle: Number(node.angle).toFixed(1),
        backgroundColor: node.backgroundColor || '#FFFFFF',
        fill: node.fill || '#000000',
        strokeWidth: node.strokeWidth || 0,
        stroke: node.stroke || '#000000',
        strokeDashArray: node.strokeDashArray || [],
        text: node.text || '',
        mltext: node.text || '',
        sid: node.sid || '0',
        datanum: node.datanum || 10,
        alarmRules: node.alarmRules || ''
    };
    var obj = null;
    for (var x in attr){
        obj = $('#attr_node_'+x);
        if (obj.length == 0) continue;
        if (obj.hasClass('colorpicker-component')){
            obj.colorpicker('setValue', attr[x]);
            continue;
        } else if (x == 'strokeDashArray'){
            this.processStokeDashArray('set',attr[x]);
            continue;
        } else if (x == 'alarmRules'){
            obj.find('tr:gt(0)').detach();
            if (!attr[x]) continue;
            var rules = attr[x].split('|ROW|');
            for (var i=0,columns='',trs=''; i<rules.length; i++){
                columns = rules[i].split('|XXX|');
                trs += '<tr data-v="'+rules[i]+'">\
                        <td class="td1">'+(typeof $.sensors[columns[0]] != 'undefined' ? $.sensors[columns[0]].name : columns[0])+'</td>\
                        <td class="td2">&'+columns[1]+';</td>\
                        <td class="td3">'+columns[2]+'</td>\
                        <td class="td4"><i class="fa fa-pencil" onclick="$.gf.edit_rule(this)"></i>&nbsp;<i onclick="$.gf.delete_rule(this)" class="fa fa-close" style="color:#e60013"></i></td>\
                    </tr>';
            }
            obj.append(trs);
            continue;
        }
        // obj.val( (attr[x]+'').replace('#','') );
        obj.val(attr[x]);
    }
}

//设置属性
FBC.prototype.setAttr = function(name, value){
    var node = this.node;
    if (name == 'scaledWidth'){
        name = 'scaleX';
        value = value / node.width;
    } else if (name == 'scaledHeight'){
        name = 'scaleY';
        value = value / node.height;
    } else if (name == 'strokeDashArray'){
        value = this.processStokeDashArray('get');
    } else if (name == 'mltext'){
        name = 'text';
    } else if (name == 'sid'){
        var o = $('#attr_node_sid option:selected'), unit = o.attr('data-unit'), smin = o.attr('data-min'), smax = o.attr('data-max'), sname = o.text();
        node.set('sunit', unit);
        node.set('smin', smin);
        node.set('smax', smax);
        node.set('sname', sname);
        if (node.type == 'gauge1' || node.type == 'gauge2'){
            var opts = this.chart.getOption();
            opts.series[0].min = smin;
            opts.series[0].max = smax;
            opts.series[0].data[0].name = sname;
            opts.series[0].data[0].value = 0;
            opts.series[0].detail.formatter = '{value}' + unit;
            this.chart.setOption(opts);
        }
    } else if (name == 'backgroundColor' && $.inArray(this.type, $.gf.chart_type_arr) != -1){
        var opts = this.chart.getOption();
        opts.backgroundColor = value;
        this.chart.setOption(opts);
    } else if (name == 'fill' && $.inArray(this.type, $.gf.chart_type_arr) != -1){
        var opts = this.chart.getOption();
        if (this.type == 'gauge1' || this.type == 'gauge2'){
            opts.series[0].axisLabel.textStyle.color = value;
            opts.series[0].title.textStyle.color = value;
            opts.series[0].detail.textStyle.color = value;
        } else {
            opts.textStyle.color = value;
        }
        this.chart.setOption(opts);
    }
    node.set(name, (name == 'text' || isNaN(value)) ? value : Number(value));
    node.setCoords();
    this.ctx.requestRenderAll();
}

//处理stokeDashAray属性
FBC.prototype.processStokeDashArray = function(t,v){
    if (t == 'set'){
        $('#attr_node_strokeDashArray').val(v.length==0 ? 'solid' : (v[0]==0.5 ? 'dotted':'dashed'));
    }else{
        v = $('#attr_node_strokeDashArray').val();
        return v=='solid' ? [] : (v=='dotted'?[0.5,1]:[3,3]);
    }
}

//刷新Chart大小
FBC.prototype.refreshChartSize = function(){
    var node = this.node, sx = node.get('scaleX'), sy = node.get('scaleY'), w = node.get('width') * sx, h = node.get('height') * sy;
    node.set('scaleX', 1);
    node.set('scaleY', 1);
    node.set('width', w);
    node.set('height', h);
    this.chart.resize({width:w, height:h});
    $('#'+node.id.replace('node','ele')).hide();
    this.ctx.requestRenderAll();
}

//设置传感量sid
FBC.prototype.setSensor = function(){
    if (this.bind){
        //绑定传感量
        this.node.sid = this.sid;
        this.node.smin = this.smin;
        this.node.sunit = this.sunit;
        this.node.smax = this.smax;
        this.node.sname = this.sname;
    }
}

//注销node
FBC.prototype.destroy = function(){
    $('#' + this.node.id.replace('node','ele')).detach();
    this.node = null;
}

