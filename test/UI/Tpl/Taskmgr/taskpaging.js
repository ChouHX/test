(function($){
    function obj(ele, opt){
        this.ele = ele;
        this.def = {
            url: '', //数据请求地址
            pager: '', //分页div的id，带#
            rowNum: 10,
            rowList: [10, 15, 20, 30, 50, 100],
            page: 1, //当前页
            total: 1, //总页数
            pagerpos: 'right',
            postData: {},
            dataType: 'json',
            postType: 'post',
            sidx: 'id', //排序字段
            sord: 'asc', //排序方向
            onLoadComplete: null
        };
        this.opt = $.extend({}, this.def, opt);
        this.init();
    }
    obj.prototype = {
        init: function(){
            this.opt.postData = {
                page: this.opt.page,
                sidx: this.opt.sidx,
                sord: this.opt.sord,
                rows: this.opt.rowNum || 10,
                cmd: this.opt.cmd || 'all',
                searchField: 'username',
                searchString: this.opt.searchString || '',
                searchType: this.opt.searchType || 'task'
            }
            this.reload();
            return this;
        },
        reload: function(){
            var me = this;
            $('.jqgrid_c .my-loading').show();
            $.ajax({
                url: me.opt.url,
                dataType: me.opt.dataType,
                type: me.opt.postType,
                data: me.opt.postData,
                success: function(msg){
                    me.content(msg);
                    if (typeof me.opt.onLoadComplete == 'function'){
                        me.opt.onLoadComplete();
                    }
                },
                complete:function(){
                    $('.jqgrid_c .my-loading').hide();
                },
                error:function(){
                    $('.jqgrid_c .my-loading').hide();
                }
            });
        },
        content: function(d){
            var str = '', me = this, tsa = $lang.VAR_TASK_STATUS_ARR;
            if (d.rows === null){
                d.rows = [];
            }
            for (var i=0,row=null,heading_str='',mr=''; i<d.rows.length; i++){
                row = d.rows[i];
                heading_str = row.is_enable != '1' ? ' style="color:#ff7f74" title="'+$lang.TASK_DISABLED+'"' : '';
                mr = i%5==4 ? 'margin-right:0' : '';
                str += '<div class="task" data-tid="'+row.id+'" data-period-type="'+row.period_type+'" style="'+mr+'">\
                  <div class="task_header"'+heading_str+' title="'+$lang.SELECT_TASK+'">\
                    <input type="checkbox" class="chk" />\
                    <div class="task_cmd">'+row.cmd+'</div>\
                    <div class="task_time text-center">\
                        <span>'+row.create_time+'</span>\
                    </div>\
                  </div>\
                  <div class="task_total" style="line-height: 30px;" title="'+$lang.VAR_TASK_DETAIL+'">\
                    <span class="task-count text">'+$lang.VAR_CREATOR+': </span>\
                    <span class="task-count"><font>'+row.username+'</font></span>\
                  </div>\
                  <ul title="'+$lang.VAR_TASK_DETAIL+'">\
                    <li>\
                      <font>'+$lang.TASK_TOTAL+'</font>\
                      <b class="st_total">'+row.status_all+'</b>\
                    </li>\
                    <li>\
                      <font>'+tsa[0]+'</font>\
                      <b class="st_todo">'+row.status_0+'</b>\
                    </li>\
                    <li>\
                      <font>'+tsa[3]+'</font>\
                      <b class="st_succeed">'+row.status_3+'</b>\
                    </li>\
                    <li>\
                      <font>'+$lang.TASK_ABNORMAL+'</font>\
                      <b class="st_failed">'+row.status_other+'</b>\
                    </li>\
                  </ul>\
                </div>';

            }
            me.ele.html(str==''?'<p style="text-align:center;font-weight:bold;">'+$lang.EXT_PAGING_1+'</p>':str);
            //分页
            me.opt.page = d.page;
            me.opt.total = d.total;
            for (var j=0,pagesize=''; j<me.opt.rowList.length; j++){
                pagesize += '<li '+(me.opt.rowNum==me.opt.rowList[j]?'class="active" ':'')+'><a href="javascript:;">'+me.opt.rowList[j]+'</a></li>';
            }
            var start = (me.opt.page-1)*me.opt.rowNum+1, end = 0; //起始页码
            if (me.opt.page == d.total && d.records % me.opt.rowNum != 0) {
                end = (d.total-1)*me.opt.rowNum + d.records % me.opt.rowNum; //最后一页
            }else{
                end = me.opt.page * me.opt.rowNum;
            }
            /**
             * a - 显示1到15,共30条
             * b - 每页显示15条
             * c - 1/2
             */
            var a = $lang.EXT_PAGING_0.replace('{0}',start), b = $lang.VAR_PAGESIZE.replace('{0}',me.opt.rowNum), c = d.page+'/'+(d.total<=0?1:d.total);
            a = a.replace('{1}', end);
            a = a.replace('{2}', d.records);
            str = '<div style="margin:0;" class="pager form-inline">\
                    <i class="pagedir fa fa-step-backward" title="'+$lang.FIRST_PAGE+'"></i> \
                    <i class="pagedir fa fa-chevron-left" title="'+$lang.PREV_PAGE+'"></i> \
                    <strong>'+c+'</strong> &nbsp; \
                    <i class="pagedir fa fa-chevron-right" title="'+$lang.NEXT_PAGE+'"></i> \
                    <i class="pagedir fa fa-step-forward" title="'+$lang.LAST_PAGE+'"></i> \
                    <div class="dropdown dropup"><a href="javascript:;" data-toggle="dropdown">'+b+'<span class="caret"></span></a>\
                        <ul class="dropdown-menu pagesize">'+pagesize+'</ul>\
                    </div> \
                    <span style="margin-left:5px">'+a+'</span>\
                </div>';
            var pager = $(me.opt.pager);
            if (pager){
                pager.html(str);
                pager.show();
            }
            me.pagesize_click_event();
            me.task_check_event();
            me.btn_paging_event();
            me.btn_act_event();
        },
        pagesize_click_event: function(){
            var me = this;
            $('#list1_paging .pagesize li').click(function(){
                if (!$(this).hasClass('active')){
                    me.opt.rowNum = $(this).find('a').html();
                    me.opt.postData.rows = me.opt.rowNum;
                    me.opt.postData.page = 1;
                    me.reload();
                }
            });
        },
        task_check_event: function(){
            var me = this;
            $('#list1 .task_total, #list1 ul').click(function(e){
                var period_type = parseInt($(this).parent().attr('data-period-type')), prefix = period_type > 0 ? 'dsrwlb':'rwlb';
                localStorage.setItem(prefix+'_cmd', me.opt.postData.cmd);
                localStorage.setItem(prefix+'_searchString', me.opt.postData.searchString);
                localStorage.setItem(prefix+'_page', me.opt.postData.page);
                localStorage.setItem(prefix+'_rowNum', me.opt.postData.rows);
                var pt = $(this).parent().attr('data-period-type');
                location.href = tpurl('Taskmgr', (pt && parseInt(pt) > 0 ? 'dsrwxq':'rwxq'), 'tid='+$(this).parent().attr('data-tid'));
            });

            $('#list1 .task_header').click(function(e){
                var ck = $(this).find('.chk'), header = $(this).parent();
                if (header.hasClass('active')){
                    ck.prop('checked', false);
                    header.removeClass('active');
                }else{
                    ck.prop('checked', true);
                    header.addClass('active');
                }
            });
        },
        btn_paging_event: function(){
            var me = this;
            if (me.opt.page != 1){
                $('i.pagedir:eq(0)').addClass('pager_btn_enable').click(function(){
                    me.opt.postData.page = 1;
                    me.reload();
                });
                $('i.pagedir:eq(1)').addClass('pager_btn_enable').click(function(){
                    me.opt.postData.page = parseInt(me.opt.page) - 1;
                    me.reload();
                });
            }
            if (me.opt.page != me.opt.total && me.opt.total != 0){
                $('i.pagedir:eq(3)').addClass('pager_btn_enable').click(function(){
                    me.opt.postData.page = me.opt.total;
                    me.reload();
                });
                $('i.pagedir:eq(2)').addClass('pager_btn_enable').click(function(){
                    me.opt.postData.page = parseInt(me.opt.page) + 1;
                    me.reload();
                });
            }
        },
        btn_act_event: function(){
            var me = this;
            $('ul.task_act li').click(function(){
                var i = $(this).index(),
                    id = $(this).parent().attr('data-id'),
                    tip_arr = [$lang.VAR_CONFIRM_DEL_TASK, $lang.TASK_ENABLE_CONFIRM, $lang.TASK_DISABLE_CONFIRM],
                    url_arr = ['taskDel', 'setTaskEnable', 'setTaskEnable'];
                if (confirm(tip_arr[i])){
                    var data = {ids:id};
                    if (i > 0){
                        data.is_enable = i==1?1:0;
                    }
                    ajax(tpurl('Taskmgr',url_arr[i]), data, function(msg){
                        zalert(msg);
                        me.reload();
                    });
                }
            });
        },
        set_cmd: function(cmd){
            var me = this;
            me.opt.postData.cmd = cmd;
        },
        set_search_val: function(v){
            var me = this;
            me.opt.postData.searchType = me.opt.searchType || 'task';
            me.opt.postData.searchString = v;
            me.opt.postData.page = 1;
        }
    };
    $.fn.taskpaging = function(options){
        var o = new obj(this, options);
        return o;
    };
 })(jQuery);