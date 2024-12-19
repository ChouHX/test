(function($){
  $.gf.add = function(){
    $.gf.save(0, 'add');
  }

  $.gf.edit = function(ele){
    location.href = tpurl('Rtu','dashboardEdit', 'dashboard_id='+$(ele).parent().parent().attr('data-id'));
  }

  $.gf.use = function(ele, type){
    if ($(ele).css('cursor') == 'default') return;
    $.gf.save($(ele).parent().parent().attr('data-id'), 'use', {type:type});
  }

  $.gf.copy = function(ele){
    $.gf.save($(ele).parent().parent().parent().attr('data-id'), 'copy');
  }

  $.gf.rename = function(ele){
    var o = $(ele).parent().parent().parent(), id = o.attr('data-id'), name = o.attr('data-name');
    $('#myLgModal').modal({
        position: 'fit',
        moveable: true,
        remote: tpurl('Rtu','getModalHtml','tpl_id=dashboard_rename&act=rename&id='+id+'&name='+escape(name))
    });
  }

  $.gf.delete = function(ele){
    $.confirm($lang.OPERATE_CONFIRM, function(){
      $.gf.save($(ele).parent().parent().parent().attr('data-id'), 'delete');
    });
  }

  $.gf.save = function(id, act, params){
    var p = {id:id, act:act};
    if (typeof params != 'undefined'){
      $.extend(p, params);
    }
    ajax(tpurl('Rtu','dashboardOperating'), p, function(msg){
        // $.notice(msg);
        if (msg.status == 0){
            if (act == 'add'){
              location.href = tpurl('Rtu', 'dashboardEdit', 'dashboard_id='+msg.data);
            }else{
              $.gf.load();
            }
        }
    });
  }

  $.gf.load = function(){
    var name = $('#search_fm input[name=searchString]').val();
    $('ul.dashboards').html('');
    $('.my-loading').show();
    ajax(tpurl('Rtu','loadDashboards','name='+name), {}, function(msg){
      $('.my-loading').hide();
      for (var i=0,str=''; i<msg.data.length; i++){
          str += '<li class="dashboard '+(msg.data[i].use==1?'use':'nouse')+'"" data-id="'+msg.data[i].id+'" data-name="'+msg.data[i].name+'" data-use="'+msg.data[i].use+'" data-create_time="'+msg.data[i].create_time+'">\
            <div class="iot">\
              <div class="using">'+$lang.VAR_USING+'</div>\
              <div class="edit" title="'+$lang.VAR_EDIT+'" onclick="$.gf.edit(this)"><img src="'+$.gf.public_path+'images/edit.png" /></div>\
            </div>\
            <div class="middle">\
              <div class="name">'+(msg.data[i].name ? msg.data[i].name : $lang.UNNAMED_DASHBOARD)+'</div>\
              <div class="time">'+msg.data[i].create_time+'</div>\
              '+(msg.data[i].use==1?  '<button class="btn use" onclick="$.gf.use(this,0)">'+$lang.VAR_DISABLE+'</button>' : '<button class="btn nouse" onclick="$.gf.use(this,1)">'+$lang.VAR_ENABLE+'</button>') + '\
            </div>\
            <div class="tools">\
              <ul>\
                <li onclick="$.gf.copy(this)"><span><i class="fa fa-copy">&nbsp;</i>'+$lang.VAR_COPY+'</span></li>\
                <li onclick="$.gf.rename(this)"><span><i class="fa fa-edit">&nbsp;</i>'+$lang.VAR_RENAME+'</span></li>\
                <li onclick="$.gf.delete(this)"><span><i class="fa fa-remove">&nbsp;</i>'+$lang.VAR_DEL+'</span></li>\
              </ul>\
            </div>\
          </li>'
      }
      $('ul.dashboards').html(str);
    });
  }
$(function(){
  //模态框关闭时清除内容
  $("#myLgModal").on("hidden.bs.modal", function(){
      $(this).removeData("bs.modal");
      $(this).find(".modal-content").children().remove();
  });

  //搜索
  $('#search_fm').on('submit',function(){
    $.gf.load();
    return false;
  });

  $.gf.load();
});
})(jQuery);