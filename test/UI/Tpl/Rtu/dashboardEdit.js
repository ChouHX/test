(function($){
$.gf = {
    ctx: null,
    nodes: {}, //FBCs
    last_node_id: null, //上一个选中的node的id
    attr_loading: false, //是否正在初始化属性(input,colorpicker)
    chart_type_arr: ['gauge1', 'gauge2', 'line1', 'line2', 'column', 'pie'],
    drop: function(ev){
        var node = null;
        //拖拽完成
        ev.preventDefault();
        var offset = $('#canvas').offset();
        var x = ev.clientX-offset.left, y = ev.clientY-offset.top, type = ev.dataTransfer.getData('type'), bind = ev.dataTransfer.getData('bind') == '1' ? true : false;
        var id = 'node_' + uuid(), fbc = new FBC(x, y, type, bind, id, $.gf.ctx);
        $.gf.nodes[id] = fbc;
    },
    enable_btns: function(len){
        var btns = {
            index_top: [1],
            index_bottom: [1],
            index_up: [1],
            index_down: [1],
            align_top: [2],
            align_bottom: [2],
            align_left: [2],
            align_right: [2],
            i_copy: [1,2],
            i_delete: [1,2]
        };
        var o = null, len = len > 2 ? 2 : len;
        for (var x in btns){
            o = $('i.' + x).parent();
            if ($.inArray(len,btns[x]) != -1){
                o.removeClass('item-disabled');
            }else{
                o.addClass('item-disabled');
            }
        }
    },
    enable_attr: function(len){
        $.gf.attr_loading = true;
        $('div.list-item[id^=list_item_]').hide();
        if (len == 0){
            $('#list_item_body').show();
            //显示画布属性
            $('#attr_ctx_width').val($.gf.ctx.width);
            $('#attr_ctx_height').val($.gf.ctx.height);
            var bg = $.gf.ctx.get('backgroundColor') || '#FFFFFF';
            $('#attr_ctx_backgroundColor').colorpicker('setValue', bg);
        }else if (len == 1){
            $('#list_item_attr').show();
            $('#list_item_style').show();
            $('#list_item_padding').show();
            $('#list_item_data').show();
            //显示node属性
            var obj = $.gf.nodes[$.gf.ctx.getActiveObject().id];
            obj.showAttr();
            //特殊属性
            var type = obj.node.type,
                sid = typeof obj.node.sid != 'undefined' ? obj.node.sid : '0';
            var ids = {
                attr_node_text: ['static_text'],
                attr_node_mltext: ['ml_text'],
                attr_node_datanum: ['line1', 'line2', 'column'],
                attr_node_alarmRules: ['img'],
                attr_node_sid: ['number_text', 'gauge1', 'gauge2', 'line1', 'line2', 'column', 'pie', 'button']
            };
            for (var x in ids){
                var o = $('#'+x).parent().parent();
                if ($.inArray(type,ids[x]) != -1){
                    o.show();
                    if (x == 'attr_node_sid' && $('#'+x).find('option').size() == 0){
                        ajax(tpurl('Rtu','getSensors'), {}, function(msg){
                            for (var i=0,str=''; i<msg.data.length; i++){
                                str += '<option value="'+msg.data[i].id+'" data-unit="'+msg.data[i].unit+'" data-min="'+msg.data[i].min+'" data-max="'+msg.data[i].max+'">'+msg.data[i].name+'</option>';
                            }
                            $('#'+x).append(str).val(sid);
                        });
                    }
                }else{
                    o.hide();
                }
            }
        }
        $.gf.attr_loading = false;
    },
    remove_node: function(){
        var objs = $.gf.ctx.getActiveObjects(), len = objs.length;
        for (var i=0, id=null; i<objs.length; i++){
            id = objs[i].id;
            if (typeof $.gf.nodes[id] != 'undefined'){
                $.gf.nodes[id].destroy();
                delete $.gf.nodes[id];
            }
            $.gf.ctx.remove(objs[i]);
            len -= 1;
        }
        $.gf.ctx.discardActiveObject();
        $.gf.enable_btns(len);
        $.gf.enable_attr(len);
    },
    //设置上下左右对齐
    align: function(o,dir){
        if ($(o).hasClass('item-disabled')) return;
        var ids = [], nodes = $.gf.ctx.getActiveObjects();
        for (var i=0; i<nodes.length; i++){
            ids.push(nodes[i].id);
        }
        $.gf.ctx.discardActiveObject().requestRenderAll();
        var align = 0;
        for (var i=0,tmp=null,node=null; i<ids.length; i++){
            node = $.gf.nodes[ids[i]].node;
            if (dir == 'top' || dir == 'left'){
                tmp = node[dir];
            } else if (dir == 'bottom'){
                tmp = node.top + node.height * node.scaleY;
            } else if (dir == 'right'){
                tmp = node.left + node.width * node.scaleX;
            }
            if (i == 0){
                align = tmp;
            }else{
                if ((dir == 'top' || dir == 'left') && tmp < align){
                    align = tmp;
                } else if ((dir == 'bottom' || dir == 'right') && tmp > align){
                    align = tmp;
                }
            }
        }
        for (var i=0,node=null; i<ids.length; i++){
            node = $.gf.nodes[ids[i]].node;
            if (dir == 'top' || dir == 'left'){
                node.set(dir, align);
            } else if (dir == 'bottom'){
                node.set('top', align - node.height * node.scaleY);
            } else if (dir == 'right'){
                node.set('left', align - node.width * node.scaleX);
            }
            node.setCoords();
        }
        $.gf.ctx.requestRenderAll();
    },
    //上传资源文件，或背景图
    upload_res: function(type){
        $('#fm_res').get(0).reset();
        $('#myLgModal h4').html($lang.BG_IMG);
        $.gf.filetype = 'bg';
        $('#filedata').fileinput('refresh', {
            uploadExtraData: {filetype:$.gf.filetype}
        });
        $('#myLgModal').modal({
            position: 'fit',
            moveable: true
        });
    },
    //重置canvas背景为纯色
    reset_bg: function(){
        var canvas = $.gf.ctx;
        canvas.setBackgroundColor($('#attr_ctx_backgroundColor').colorpicker('getValue'));
        canvas.setBackgroundImage(null, canvas.renderAll.bind(canvas));
    },
    //资源图片选中时+边框
    res_img_select: function(ele){
        $(ele).siblings().removeClass('selected');
        $(ele).addClass('selected');
    },
    //加载图片列表
    load_res_img: function(){
        ajax(tpurl('Syscfg','loadCanvasImg'), {filetype:$.gf.filetype}, function(msg){
            if (msg.status == 0){
                for (var i=0,str=''; i<msg.data.length; i++){
                    str += '<li onclick="$.gf.res_img_select(this)" data-filename="'+msg.data[i].filename+'"><img src="'+msg.data[i].path+'" /></li>';
                }
                $('#myLgModal ul.res-imgs').html(str);
            }
        });
    },
    //删除，选择图片
    res_img_op: function(act){
        var o = $('#myLgModal .res-imgs li.selected');
        if (o.size() == 0){
            $.notice(1,$lang.SELECT_IMG);
            return;
        }
        if (act == 'delete'){
            $.confirm($lang.VAR_CONFIRM_DEL_IMG, function(){
                ajax(tpurl('Syscfg','deleteCanvasImg'), {filetype:$.gf.filetype, filename:o.attr('data-filename')}, function(msg){
                    if (msg.status == 0){
                        $.gf.load_res_img();
                    }else{
                        $.notice(msg);
                    }
                });
            });
        } else if (act == 'select'){
            var canvas = $.gf.ctx;
            if ($.gf.filetype == 'bg'){
                canvas.setBackgroundImage(o.find('img').attr('src'), canvas.renderAll.bind(canvas), {width:parseFloat(canvas.width), height:parseFloat(canvas.height)});
            } else if ($.gf.filetype == 'img'){
                var id = 'node_' + uuid(), fbc = new FBC(0, 0, 'img', false, id, canvas, undefined, false, o.find('img').attr('src'));
                $.gf.nodes[id] = fbc;
            }
            $('#myLgModal').modal('hide');
        }
    },
    init: function(){
        var ctx = new fabric.Canvas('canvas',{
        });
        $.gf.ctx = ctx;

        //监听canvas事件
        ctx.on('dragover', function(opt){
            //允许drop到canvas上
            event.preventDefault();
        });
        ctx.on('drop', function(opt){
            //拖拽完成
            $.gf.drop(event);
        });
        ctx.on('mouse:up',function(opt){
            // 点击画板
            var len = this.getActiveObjects().length;
            $.gf.last_node_id = len == 1 ? this.getActiveObject().id : null;
            $.gf.enable_btns(len);
            $.gf.enable_attr(len);
        });
        ctx.on('object:scaled', function(opt){
            //缩放后
            if ($.inArray(opt.target.type, $.gf.chart_type_arr) != -1){
                $.gf.nodes[opt.target.id].refreshChartSize();
            }
        });
        $(window).on('keydown', function(){
            if (event.keyCode == 46){
                $.gf.remove_node();
            }
        });
        //+-改变input值
        $('.item-input .decrease').on('click',function(){
            var o = $(this).parent().find('input[id^=attr_]');
            o.val(Number(o.val()) - 1).change();
        });
        $('.item-input .increase').on('click',function(){
            var o = $(this).parent().find('input[id^=attr_]');
            o.val(Number(o.val()) + 1).change();
        });

        //监听input/select改变
        $('.list-item-content-form input[id^=attr_],.list-item-content-form select[id^=attr_],.list-item-content-form textarea[id^=attr_]').on('change',function(){
            if ($.gf.attr_loading) return;
            var id = $(this).attr('id'), v = $(this).val(), min = parseFloat($(this).attr('data-min')), max = parseFloat($(this).attr('data-max'));
            if (min && v < min){
                $(this).val(min);
                v = min;
            }
            if (max && v > max){
                $(this).val(max);
                v = max;
            }
            if (id.indexOf('attr_ctx_') != -1){
                id.indexOf('width') != -1 ? $.gf.ctx.setWidth(v) : $.gf.ctx.setHeight(v);
            } else if (id.indexOf('attr_node_') != -1) {
                // $.gf.nodes[$.gf.ctx.getActiveObject().id].setAttr(id.split('_')[2], v);
                $.gf.nodes[$.gf.last_node_id].setAttr(id.split('_')[2], v);
            }
        });

        //置顶
        $('i.fa-arrow-circle-up').parent().on('click',function(){
           $.gf.ctx.bringToFront($.gf.ctx.getActiveObject());
        });

        //置底
        $('i.fa-arrow-circle-down').parent().on('click',function(){
           $.gf.ctx.sendToBack($.gf.ctx.getActiveObject());
        });

        //上一级
        $('i.fa-chevron-up').parent().on('click',function(){
           $.gf.ctx.bringForward($.gf.ctx.getActiveObject(), true);
        });

        //下一级
        $('i.fa-chevron-down').parent().on('click',function(){
           $.gf.ctx.sendBackwards($.gf.ctx.getActiveObject(), true);
        });

        //复制
        $('i.fa-copy').parent().on('click',function(){
            if ($(this).hasClass('item-disabled')) return;
            /*
            var canvas = $.gf.ctx, _clipboard = canvas.getActiveObject();
            _clipboard.clone(function(clonedObj) {
                canvas.discardActiveObject();
                clonedObj.set({
                    left: clonedObj.left + 30,
                    top: clonedObj.top + 30,
                    evented: true,
                    transparentCorners: false,
                    cornerSize: 8
                });
                if (clonedObj.type === 'activeSelection') {
                    // active selection needs a reference to the canvas.
                    clonedObj.canvas = canvas;
                    clonedObj.forEachObject(function(obj) {
                        canvas.add(obj);
                    });
                    // this should solve the unselectability
                    clonedObj.setCoords();
                } else {
                    canvas.add(clonedObj);
                }
                canvas.setActiveObject(clonedObj);
                canvas.requestRenderAll();
            });
            */
            var ctx = $.gf.ctx, objs = ctx.getActiveObjects();
            ctx.discardActiveObject();
            for (var i=0, id=null, node=null; i<objs.length; i++){
                id = 'node_' + uuid();
                node = objs[i];
                $.gf.nodes[id] = new FBC(null, null, null, null, id, $.gf.ctx, node, true);
            }
        });

        //移除
        $('i.fa-trash').parent().on('click',function(){
            if ($(this).hasClass('item-disabled')) return;
            $.gf.remove_node();
        });

        //收起左侧
        $('i.fa-outdent').parent().on('click',function(){
            if ($('div.left').attr('data-hide') == '1'){
                $('div.left').attr('data-hide','0').show(300);
                $('section.main').css('padding-left', '282px');
            }else{
                $('div.left').attr('data-hide','1').hide(300);
                $('section.main').css('padding-left', '0');
            }
        });

        //收起右侧
        $('i.fa-indent').parent().on('click',function(){
            if ($('div.right').attr('data-hide') == '1'){
                $('div.right').attr('data-hide','0').show(300);
                $('section.main').css('padding-right', '256px');
            }else{
                $('div.right').attr('data-hide','1').hide(300);
                $('section.main').css('padding-right', '0');
            }
        });

        //资源
        $('i.fa-cloud').parent().on('click',function(){
            $('#fm_res').get(0).reset();
            $('#myLgModal h4').html($lang.VAR_RESOURCE);
            $.gf.filetype = 'img';
            $('#filedata').fileinput('refresh', {
                uploadExtraData: {filetype:$.gf.filetype}
            });
            $('#myLgModal').modal({
                position: 'fit',
                moveable: true
            });
        });

        //预览
        $('i.fa-tv').parent().on('click',function(){
            window.open(tpurl('Rtu','dashboardView','dashboard_id='+$.dashboard_id));
        });

        //保存
        $('i.fa-save').parent().on('click',function(){
            // {svg:$.gf.ctx.toSVG()}
            $.gf.ctx.discardActiveObject();
            var ctx_bg = $.gf.ctx.get('backgroundImage');
            var nodes = [{type:'ctx', width:$.gf.ctx.getWidth(), height:$.gf.ctx.getHeight(), bgcolor:$.gf.ctx.get('backgroundColor') || '#FFFFFF', bgimg:(ctx_bg ? ctx_bg._element.src : '')}];
            var node = null;
            for (var x in $.gf.nodes){
                node = {
                    left: $.gf.nodes[x].node.left,
                    top: $.gf.nodes[x].node.top,
                    type: $.gf.nodes[x].node.type,
                    bind: $.gf.nodes[x].node.bind,
                    angle: $.gf.nodes[x].node.angle,
                    fontFamily: $.gf.nodes[x].node.fontFamily,
                    fontSize: $.gf.nodes[x].node.fontSize,
                    fontWeight: $.gf.nodes[x].node.fontWeight,
                    backgroundColor: $.gf.nodes[x].node.backgroundColor,
                    fill: $.gf.nodes[x].node.fill,
                    width: $.gf.nodes[x].node.width,
                    height: $.gf.nodes[x].node.height,
                    scaleX: $.gf.nodes[x].node.scaleX,
                    scaleY: $.gf.nodes[x].node.scaleY,
                    strokeWidth: $.gf.nodes[x].node.strokeWidth,
                    stroke: $.gf.nodes[x].node.stroke,
                    strokeDashArray: $.gf.nodes[x].node.strokeDashArray,
                    originX: $.gf.nodes[x].node.originX,
                    originY: $.gf.nodes[x].node.originY,
                    flipX: $.gf.nodes[x].node.flipX,
                    flipY: $.gf.nodes[x].node.flipY,
                    text: $.gf.nodes[x].node.text,
                    imgpath: $.gf.nodes[x].node.imgpath,
                    alarmRules: $.gf.nodes[x].node.alarmRules
                };
                if (typeof $.gf.nodes[x].node.sid != 'undefined'){
                    node.sid = $.gf.nodes[x].node.sid;
                    node.sunit = $.gf.nodes[x].node.sunit;
                    node.smin = $.gf.nodes[x].node.smin;
                    node.smax = $.gf.nodes[x].node.smax;
                    node.sname = $.gf.nodes[x].node.sname;
                    node.datanum = $.gf.nodes[x].node.datanum || 10;
                }
                /*
                for (var y in $.gf.nodes[x].node){
                    if (typeof $.gf.nodes[x].node[y] == 'function') continue;
                    node[y] = $.gf.nodes[x].node[y];
                }*/
                nodes.push(node);
            }
            ajax(tpurl('Rtu','dashboardEdit'), {svg:JSON.stringify(nodes), dashboard_id:$.dashboard_id}, function(msg){
                $.notice(msg);
            });
        });

        //初始化menu
        $('.main .left-tab-item').on('click',function(){
            //tab show
            $(this).parent().find('.left-tab-item').removeClass('left-tab-item-selected');
            $(this).addClass('left-tab-item-selected');
            //content show
            $('.left-content-list').hide();
            $('#left-content-list-'+$(this).attr('data-id')).show();
        });

        //初始化right-menu
        $('.right .bar-tab').on('click',function(){
            //tab show
            $(this).parent().find('.bar-tab').removeClass('tab-selected');
            $(this).addClass('tab-selected');
            //content show
            $('.right-bar-wrap div.full-container').hide();
            $('.right-bar-wrap div.full-container:eq('+$(this).index()+')').show();
        });
        $('.list-item-title-button').click(function(){
            var o = $(this).parent();
            if (o.hasClass('active')){
                o.removeClass('active');
                o.next().hide();
                $(this).find('.fa-angle-right').show();
                $(this).find('.fa-angle-down').hide();
            }else{
                o.addClass('active');
                o.next().show();
                $(this).find('.fa-angle-right').hide();
                $(this).find('.fa-angle-down').show();
            }
        });

        //图形拖拽
        $('div.left-content-list-item').on('dragstart',function(){
            event.dataTransfer.setDragImage(event.target, 0, 0);
            event.dataTransfer.setData('type', event.target.dataset.type);
            event.dataTransfer.setData('bind', event.target.dataset.enableBd);
        });
    },
    add_rule: function(){
        $('#alarmRuleModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=canvas_alarm_rule_add')
        });
    },
    edit_rule: function(e){
        var tr = $(e).parent().parent();
        $('#alarmRuleModal').modal({
            position: 'fit',
            moveable: true,
            remote: tpurl('Rtu','getModalHtml','tpl_id=canvas_alarm_rule_edit&tr_index='+tr.index()+'&data_v='+tr.attr('data-v'))
        });
    },
    delete_rule: function(e){
        $(e).parent().parent().detach();
        $.gf.save_rule();
    },
    save_rule: function(){
        var rules = [];
        $('table.tbl-img-enable-alarm tr:gt(0)').each(function(){
            rules.push($(this).attr('data-v'));
        });
        $.gf.nodes[$.gf.last_node_id].setAttr('alarmRules', rules.join('|ROW|'));
    }
}
$(function () {
    $('.main').css('height', $(window).height()-105);
    $(window).resize(function(){
        $('.main').css('height', $(window).height()-105);
    });
    window.onbeforeunload = function(event){
        // return confirm();
    };

    //colorpicker
    $('div.colorpicker-component').colorpicker({
      format: null,
      container: true,
      useAlpha: true,
      horizontal: true
    }).on('change', function (e) {
        if (typeof e.value != 'undefined') {
            if ($.gf.attr_loading) return;
            var clr = e.value, id = $(this).attr('id');
            if (id.indexOf('attr_ctx_') != -1){
                $.gf.ctx.setBackgroundColor(clr);
                $.gf.ctx.requestRenderAll();
            }else{
                $.gf.nodes[$.gf.last_node_id].setAttr(id.split('_')[2], clr);
            }
        }
    });

    //Bootstrap fileinput
    $('#filedata').fileinput({
        language: $.lang,
        uploadUrl: tpurl('Syscfg','addCanvasImg'),
        showUpload: false,
        showRemove: true,
        showPreview: false,
        dropZoneEnabled: false,
        showCaption: true,
        allowedFileExtensions:  ['jpg', 'jpeg', 'png', 'gif'],
        maxFileSize: 2*1024, //KB
        maxFileCount: 1,
        uploadExtraData: {}
    }).on("fileuploaded", function (event, data, previewId, index){
        if (data.response.status == 0){
            $.gf.load_res_img();
        }else{
            $.notice(data.response);
        }
    }).on('fileselect', function (event, numFiles, label){
        var files = $('#filedata').fileinput('getFileStack');
        if (files.length == 0) return;
        $('#filedata').fileinput('upload');
    }).on('fileuploaderror', function(event, data, msg){
        $.notice(1, msg);
    });

    //资源框打开时加载图片
    $('#myLgModal').on('shown.bs.modal', function () {
        $.gf.load_res_img();
    });

    //模态框关闭时清除内容
    $("#alarmRuleModal").on("hidden.bs.modal", function(){
        $(this).removeData("bs.modal");
        $(this).find(".modal-content").children().remove();
    });

    //Dialog move
    $('.modal-dialog').draggable({handle: ".modal-header"});

    //初始化画板
    $.gf.init();

    //载入保存的dashboard
    ajax(tpurl('Rtu','dashboardView'), {dashboard_id:$.dashboard_id}, function(msg){
        if (msg.status == 0){
            var nodes = JSON.parse(msg.data), ctx = $.gf.ctx;
            for (var i=0, id=null; i<nodes.length; i++){
                if (nodes[i].type == 'ctx'){
                    $('#attr_ctx_width').val(nodes[i].width).change();
                    $('#attr_ctx_height').val(nodes[i].height).change();
                    $.gf.ctx.setBackgroundColor(nodes[i].bgcolor);
                    if (nodes[i].bgimg){
                        ctx.setBackgroundImage(nodes[i].bgimg, ctx.renderAll.bind(ctx), {width:parseFloat(nodes[i].width), height:parseFloat(nodes[i].height)});
                    }
                    $('#attr_ctx_backgroundColor').colorpicker('setValue', nodes[i].bgcolor);
                }else{
                    id = 'node_' + uuid();
                    $.gf.nodes[id] = new FBC(null, null, null, null, id, $.gf.ctx, nodes[i]);
                }
            }
        }
    });
});
})(jQuery);