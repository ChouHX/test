(function($){
    $(document).ready(function(){
        $.gf.finish_upgrade = function() {
            alert('ok');
        }

        $('#list2').jqGrid({
            url: tpurl('Syscfg', 'loadUpgradeRecord'),
            datatype: 'json',
            mtype: 'post',
            colNames: ['id', $lang.UPGRADE_TIME, $lang.VAR_TERM_STATUS, $lang.OLD_VERSION, $lang.NEW_VERSION, $lang.VAR_MENU_LOG],
            colModel:[
                {name:'id',           index:'id',           jsonmap:'id',          width:50,   align:'center', hidden:true,  search:false, key:true},
                {name:'upgrade_time', index:'upgrade_time', jsonmap:'upgrade_time',width:150,  align:'center', hidden:false, search:false},
                {name:'status',       index:'status',       jsonmap:'status',      width:75,   align:'center', hidden:false, search:false, formatter:function(v, options, rowObject){
                    var clr = rowObject.old_version == rowObject.new_version ? '#f00' : '#7bf57b', txt = rowObject.old_version == rowObject.new_version ? $lang.VAR_ALARM_SEND_STATUS_ARR['-1'] : $lang.VAR_ALARM_SEND_STATUS_ARR['0'];
                    return '<span style="color:'+clr+'">'+txt+'</span>';
                }},
                {name:'old_version',  index:'old_version',  jsonmap:'old_version', width:150,  align:'center', hidden:false, search:false},
                {name:'new_version',  index:'new_version',  jsonmap:'new_version', width:150,  align:'center', hidden:false, search:false},
                {name:'log_filename', index:'log_filename', jsonmap:'log_filename',width:150,  align:'center', hidden:false, search:false, formatter:function(v){
                    return '<a target="_blank" style="color:skyblue; text-decoration:underline;" href="'+$.gf.root_path+'Log/upgrade/'+v+'.txt">'+v+'.txt</a>';
                }}
            ],
            pager: '#pager2',
            rowNum: 10,
            rowList: [10, 20, 30, 40, 50, 100],
            sortname: 'id',
            sortorder: 'DESC',
            viewrecords: true,
            autowidth: true,
            height: 'auto',
            multiselect: false,
            multiselectWidth: 30,
            page: 1,
            pagerpos: 'center',
            pgbuttons: true,
            pginput: true,
            postData: {},
            rownumbers: true,
            rownumWidth: 30,
            jsonReader: {repeatitems: false}
        });

        // 设置jqgrid的宽度
        $(window).bind('resize', function(){
            jqgrid_set_width($('#list2'),$('.jqgrid_c'));
        });
        $('a.sidebar-toggle').click(function(){
            setTimeout(function(){
                jqgrid_set_width($('#list2'),$('.jqgrid_c'));
            }, 300);
        });

        //Edit basic info
        $('#btn_edit_basic').click(function(){
            ajax(tpurl('Syscfg','editMyInfo'), {
                email: $('.td-input input[name=email]').val(),
                sim: $('.td-input input[name=sim]').val(),
                info: $('.td-input input[name=info]').val(),
            }, function(msg){
                $.notice(msg);
            });
        });

        // Upgrade
        $('#btn_upgrade').click(function() {
            ajax(tpurl('Syscfg', 'kssj'), {}, function(msg){
                if (msg.status != 0) {
                    $.notice(msg);
                    return;
                }
                $.confirm($lang.START_UPGRADE_TIPS, function(){
                    layer.open({
                      type: 2,
                      closeBtn: 1,
                      title: '<span style="font-weight:700;color:skyblue;">'+$lang.UPGRADE_PROGRESS+'</span>',
                      area: ['1000px', '500px'],
                      shade: [0.8, '#000'],
                      anim: 2,
                      maxmin: true,
                      content: tpurl('Syscfg', 'kssj'),
                      cancel: function(){
                        $('#btn_reload_params').click();
                      }
                    });
                });
            });
        });

        $('#btn_confirm').click(function(){
            var params = $.gf.sp.getParams();
            if (!params){
                return;
            }
            $.confirm($lang.OPERATE_CONFIRM, function(){
                ajax(tpurl('Syscfg','editSystemParams'), {params:params}, function(msg){
                    $.notice(msg);
                });
            });
        });

        //Load params
        $.gf.sp = new SystemParams('swu_');
        $.gf.sp.load();
        $('#btn_reload_params').click(function(){
            $.gf.sp.load();
            $('#list2').trigger('reloadGrid');
        });
    });
})(jQuery);