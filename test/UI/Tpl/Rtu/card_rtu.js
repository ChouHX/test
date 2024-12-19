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
                rows: this.opt.rowNum,
                cmd: 'all',
                searchField: 'sn',
                searchString: '',
                searchTable: 'term',
                gid: -10
            }
            this.reload();
            return this;
        },
        reload: function(){
            var me = this;
            $('.my-loading').show();
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
                    $('.my-loading').hide();
                },
                error:function(){
                    $('.my-loading').hide();
                }
            });
        },
        content: function(d){
            var str = '', me = this, tsa = $lang.VAR_TASK_STATUS_ARR;
            var card_width = window.screen.height >= 1000 ? 3 : 4;
            if (d.rows === null){
                d.rows = [];
            }
            var row_size = card_width == 3 ? 4 : 3;
            var cols = localStorage && localStorage.getItem($.gf.colsStorageName) || null;
            for (var i=0,row=null,pr='',mr='',title='',warning=''; i<d.rows.length; i++){
                row = d.rows[i];
                pr = (i+1)%row_size == 0 ? 'padding:0;'     : 'padding:0 15px 0 0;';
                mr = (i+1)%row_size == 0 ? 'margin-right:0' : '';
                warning = row.warning_num != '0' ? 'background-color:red !important;' : '';
                title = warning != '' ? $lang.ALARM_GENERATION : '';
                str +='<li class="card col-lg-'+card_width+'" data-sn="'+row.sn+'" style="'+pr+mr+'">\
					<div class="dialog" style="cursor:pointer;">\
							<div class="table_header" style="'+warning+'" title="'+title+'">\
								<ul class="commonUL">\
									<li class="w3">'+row.status+'&nbsp;'+row.sn+'</li>\
								</ul>\
							</div>\
							<div class="table_body mCustomScrollbar _mCS_3" style="height:80%;overflow:auto;">\
								<div id="mCSB_3" class="mCustomScrollBox mCS-light mCSB_vertical mCSB_inside" tabindex="0" style="">\
									<div id="mCSB_3_container" class="mCSB_container" style="position: relative; top: 0px; left: 0px; border:none;" dir="ltr">\
										<ul class="commonUL" style="color:black;font-weight: 700;">\
											<li class="w1">'+$lang.SENSOR_NAME+'</li><li class="w2">'+$lang.CURRENT_VALUE+'</li>\
										</ul>';
				for (var j=0,key='',datas=typeof row['datas'],color='',w2_title=''; j<$.gf.sensors.length; j++){
					key = $.gf.sensors[j].slave_id + '_' + $.gf.sensors[j].addr;
                    if (cols && cols.indexOf(key+',') == -1) continue;
					if (datas != 'undefined' && typeof row['datas'][key] != 'undefined'){
                        w2_title = $lang.VAR_DEVICE_URL_REPORT_TIME+'：'+row['datas'][key].report_time;
                        color = row['datas'][key].color;
                        if (color == 'threshold'){
                            w2_title = $lang.DATA_TITLE_THRESHOLD+"\n" + w2_title;
                            color = '#f00';
                        }else if (color == 'timeout'){
                            w2_title = $lang.DATA_TITLE_TIMEOUT+"\n" + w2_title;
                            color = '#c1bbbb';
                        }else{
                            color = '#000';
                        }
						str += '<ul class="commonUL">\
							      <li class="w1">'+$.gf.sensors[j].name+'</li><li class="w2" title="'+w2_title+'" style="color:'+color+'">'+row['datas'][key].value+'&nbsp;'+($.gf.sensors[j].unit || '')+'</li>\
						        </ul>';
					}
				};
				str += '</div></div></div></div></li>';
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
            me.btn_paging_event();
			me.btn_check_event();
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
        btn_check_event: function(){
            var me = this;
            $('.card-container li.card div.dialog').click(function(e){
				if (e.ctrlKey === false){
                    var cols = localStorage && localStorage.getItem('lora_list3_colModel') || '';
					location.href = tpurl('Rtu','lssj', '&gateway_sn='+$(this).parent().attr('data-sn')+'&cols='+cols);
				}
            });
        },
        set_cmd: function(cmd){
            var me = this;
            me.opt.postData.cmd = cmd;
        },
        set_search_val: function(v, gid){
            var me = this;
            me.opt.postData.searchType = 'term';
            me.opt.postData.searchString = v;
            me.opt.postData.gid = gid;
        },
        set_page:function(page){
            this.opt.postData.page = page;
        }
    };
    $.fn.taskpaging = function(options){
        var o = new obj(this, options);
        return o;
    };
 })(jQuery);