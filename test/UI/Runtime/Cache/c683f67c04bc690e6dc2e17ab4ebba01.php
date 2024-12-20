<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
  /*tab style*/
  #tbl_res tr{
    cursor: pointer;
  }
  #tbl_res input{
    vertical-align: middle;
  }
  .tr_selected_cfg{
    background: #aedae4;
  }
  .params-tab-content .tab-pane{
    padding-top: 10px;
  }
  #fm_rwcs{
    margin-top: 10px;
  }

  /*params input style*/
  .ck-top-0{
    top: 0;
  }
  .form-horizontal label.input-notes{
    text-align: left;
    color: gray;
  }

  /*loading*/
  .my-loading{
    position: absolute;
    top: 0;
    left: 0;
    z-index: 100;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,.6) url(../Public/images/loading.gif) no-repeat center;
  }

  /*task dest ul*/
  .task-dest{
    list-style: none;
    display: inline-block;
    margin: 0;
    padding: 0;
  }
  .task-dest li{
    float: left;
    background-color: #ecf0f5;
    margin: 0 20px 15px 0;
    line-height: 30px;
    padding: 0 5px;
    border-radius: 3px;
  }
  .timed-task-params{
    display: none;
  }
  .timed-task-params .checkbox-inline{
    min-width: 45px;
  }
</style>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
  <h4 class="modal-title"></h4>
</div>
<div class="modal-body">
  <form class="form-horizontal" role="form" id="fm_rwcs" style="margin-top: 20px; /*border: 1px solid #ddd; padding-top: 15px;*/">
    <div class="form-group">
      <label class="col-md-2 control-label"><?php echo (L("SELECT_MODULE")); ?>:</label>
      <div class="col-md-8">
        <select class="form-control" name="module">
          <option value="1" selected><?php echo (L("MODULE")); ?>1</option>
          <option value="2"><?php echo (L("MODULE")); ?>2</option>
        </select>
      </div>
    </div>
    <hr/>
    <div class="form-group">
      <label for="starttime" class="col-lg-2 control-label"><?php echo (L("TASK_START_TIME")); ?>：</label>
      <div class="col-lg-3">
        <input type="text" class="form-control" name="start_dt" id="start_dt" value="" />
      </div>
      <label class="col-lg-7 control-label input-notes"><?php echo (L("TASK_START_TIME_DESC")); ?></label>
    </div>
    <div class="form-group">
      <label for="endtime" class="col-lg-2 control-label"><?php echo (L("TASK_END_TIME")); ?>：</label>
      <div class="col-lg-3">
        <input type="text" class="form-control" name="end_dt" id="end_dt" value="" />
      </div>
      <label class="col-lg-7 control-label input-notes"><?php echo (L("TASK_END_TIME_DESC")); ?></label>
    </div>
    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo (L("AUTO_RETRY")); ?>：</label>
      <div class="col-lg-3">
        <label class="checkbox-inline">
          <input type="checkbox" name="auto_retry" class="ck-top-0" />
        </label>
      </div>
      <label class="col-lg-7 control-label input-notes"><?php echo (L("AUTO_RETRY_TIPS")); ?></label>
    </div>
    <div class="form-group">
      <label class="col-lg-2 control-label"><?php echo (L("SCHEDULED_EXEC")); ?>：</label>
      <div class="col-lg-3">
        <select class="form-control" id="select_timed_type">
          <option value="-1"><?php echo (L("VAR_DISABLE")); ?></option>
          <option value="1" data-show="1,3,6"><?php echo (L("MONTHLY_EXEC")); ?></option>
          <option value="2" data-show="2,3,6"><?php echo (L("WEEKLY_EXEC")); ?></option>
          <option value="3" data-show="4,6"><?php echo (L("DAILY_EXEC")); ?></option>
          <option value="4" data-show="5"><?php echo (L("HOURLY_EXEC")); ?></option>
        </select>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-1">
      <label class="col-lg-2 control-label"><?php echo (L("VAR_TRACK_DATE")); ?>：</label>
      <div class="col-lg-10">
        <?PHP for ($i=1; $i<32; $i++) { echo sprintf('<label class="checkbox-inline" style="margin-left:10px"><input type="checkbox" value="%s" name="days" />%s</label>', $i, $i); } ?>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-2">
      <label class="col-lg-2 control-label"><?php echo (L("VAR_WEEK")); ?>：</label>
      <div class="col-lg-10">
        <?PHP
 $week_arr = L('VAR_WEEK_ARR'); foreach ($week_arr as $key => $val) { echo sprintf('<label class="checkbox-inline"><input type="checkbox" name="weeks" value="%s" />%s&nbsp;&nbsp;</label>', $key, $val); } ?>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-3">
      <label class="col-lg-2 control-label"><?php echo (L("HOURS")); ?>：</label>
      <div class="col-lg-2">
        <div class="input-group">
          <select class="form-control">
            <?PHP for ($i=0; $i<24; $i++) { echo sprintf('<option value="%d">%s%d</option>', $i, ($i<10?'0':''), $i); } ?>
          </select>
          <div class="input-group-addon" style="background: #f4f4f4"><?php echo (L("BY_HOUR")); ?></div>
        </div>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-4">
      <label class="col-lg-2 control-label"><?php echo (L("HOURS")); ?>：</label>
      <div class="col-lg-10">
        <?PHP for ($i=0; $i<24; $i++) { echo sprintf('<label class="checkbox-inline" style="margin-left:10px"><input type="checkbox" name="hours" value="%d" />%s%d</label>', $i, ($i<10?'0':''), $i); } ?>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-5">
      <label class="col-lg-2 control-label"><?php echo (L("MINUTES")); ?>：</label>
      <div class="col-lg-10">
        <?PHP for ($i=0; $i<60; $i+=5) { echo sprintf('<label class="checkbox-inline" style="margin-left:10px"><input type="checkbox" name="minutes" value="%d" />%s%d</label>', $i, ($i<10?'0':''), $i); } ?>
      </div>
    </div>
    <div class="form-group timed-task-params timed-params-6">
      <label class="col-lg-2 control-label"><?php echo (L("MINUTES")); ?>：</label>
      <div class="col-lg-2">
        <div class="input-group">
          <select class="form-control">
            <?PHP for ($i=0; $i<60; $i+=5) { echo sprintf('<option value="%d">%s%d</option>', $i, ($i<10?'0':''), $i); } ?>
          </select>
          <div class="input-group-addon" style="background: #f4f4f4"><?php echo (L("VAR_MINUTE")); ?></div>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-success" onclick="javascript:$.gf.submit_task();"><?php echo (L("SAVE_TEMPLATE")); ?></button>
  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
</div>

<script type="text/javascript">
  (function($) {
    $.gf.daterangepicker_opt = {
      "singleDatePicker": true,
      "timePicker": true,
      "timePicker24Hour": true,
      // 'drops': $.inArray($.gf.tp.task_type, ['termRestart','configGet','cfgFileUpload','rtuScriptGet']) === -1 ? 'up' : 'down',
      "locale": {
        "format": "YYYY-MM-DD HH:mm:ss",
        "daysOfWeek": $lang.VAR_WEEK_ARR,
        "monthNames": $lang.VAR_MONTH.replace('[','').replace(']','').replace(/'/g,'').split(','),
        "applyLabel": $lang.VAR_BTN_SURE,
        "cancelLabel": $lang.VAR_BTN_CANCLE
      }
    }

    $.gf.submit_task = function() {
      var params = {
        start_ts: new Date($('#start_dt').val().replace(/-/g, '/')).getTime() / 1000,
        end_ts: new Date($('#end_dt').val().replace(/-/g, '/')).getTime() / 1000,
        act: $.gf.tp.dest_type,
        check_model: $.gf.tp.check_model,
        enable_model: $.gf.tp.enable_model,
        auto_retry: $('#fm_rwcs input[name=auto_retry]').is(':checked') ? 1 : 0
      };
      if ($.gf.tp.dest_type == 'term') {
        var sns = $('#list2').jqGrid('getGridParam','selarrrow');
        params.term_list = sns.join(',');
      } else if ($.gf.tp.dest_type == 'group') {
        params.gids = $('#change_gid span').attr('data-id');
      }

      // 定时任务参数
      params.timed_type = parseInt($('#select_timed_type').val());
      if (params.timed_type != -1) {
        var v1 = [], v2 = [], v3 = $('.timed-params-3 select').val(), v4 = [], v5 = [], v6 = $('.timed-params-6 select').val();
        $('.timed-params-1 input[name=days]').each(function(){
          if ($(this).is(':checked')) v1.push($(this).val());
        });
        $('.timed-params-2 input[name=weeks]').each(function(){
          if ($(this).is(':checked')) v2.push($(this).val());
        });
        $('.timed-params-4 input[name=hours]').each(function(){
          if ($(this).is(':checked')) v4.push($(this).val());
        });
        $('.timed-params-5 input[name=minutes]').each(function(){
          if ($(this).is(':checked')) v5.push($(this).val());
        });
        switch (params.timed_type) {
          case 1:
            if (v1.length == 0) {
              $.notice(1, $lang.TIPS_SELECT_EXEC_DATE);
              return;
            }
            params.timed_params = v6+'|'+v3+'|'+v1.join(',')+'|0';
            break;
          case 2:
            if (v2.length == 0) {
              $.notice(1, $lang.TIPS_SELECT_EXEC_DATE);
              return;
            }
            params.timed_params = v6+'|'+v3+'|0|'+v2.join(',');
            break;
          case 3:
            if (v4.length == 0) {
              $.notice(1, $lang.TIPS_SELECT_EXEC_TIME);
              return;
            }
            params.timed_params = v6+'|'+v4.join(',')+'|0|0';
            break;
          case 4:
            if (v5.length == 0) {
              $.notice(1, $lang.TIPS_SELECT_EXEC_TIME);
              return;
            }
            params.timed_params = v5.join(',')+'|0|0|0';
            break;
        }
      }

      // 指令下发相关参数
      var obj = $('#fm_rwcs').data('bootstrapValidator');
      obj.validate();
      if (!obj.isValid()){
          return;
      }
      var p2 = $.serializeObject('#fm_rwcs');
      $.extend(params, {
        module: p2.module,
      });

      var do_work = function() {
        ajax(tpurl('Task', $.gf.tp.task_type), params, function(msg) {
          msg.data ? $.notice(1, msg.info) : $.notice(msg);
          if (msg.status == 0 || msg.status == 1) {
            $('#rwcs2Modal').modal('hide');
            $.gf.getStaticsInfo();
          }
        });
      }

      if ($.gf.tp.dest_type == 'group' || $.gf.tp.dest_type == 'all') {
        $.confirm($lang.TASK_TARGET+'：' + ($.gf.tp.dest_type == 'group' ? $('#change_gid span').html() : $lang.VAR_ALL_ROUTER)+'<br>' +
          $lang.VAR_CMD_NAME+'：'+$.gf.tp.task_name+'<br>' +
          '<span style="color:red">'+$lang.BATCH_OPERATION_CONFIRM+'</span>', function(){
          do_work();
        });
      } else {
        do_work();
      }
    }

    $(document).ready(function() {
      $('#rwcs2Modal h4').html($.gf.tp.task_name);
      $('#start_dt').val(moment().format('YYYY-MM-DD HH:mm:00')).daterangepicker($.gf.daterangepicker_opt);
      $('#end_dt').val(moment().add(30, 'days').format('YYYY-MM-DD HH:mm:00')).daterangepicker($.gf.daterangepicker_opt);

      $('#fm_rwcs').bootstrapValidator({
          feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
          },
      });

      // 切换定期执行
      $('#select_timed_type').on('change', function() {
        var v = $(this).val(), ids = $(this).find('option:selected').attr('data-show');
        v != -1 ? $('#fm_rwcs input[name=auto_retry]').prop('checked', false).attr('disabled', true) : $('#fm_rwcs input[name=auto_retry]').removeAttr('disabled');
        for (var i=1,o=null; i<7; i++) {
          o = $('div.timed-params-'+i);
          if (v == -1) {
            o.hide();
          } else {
            ids.indexOf(i) != -1 ? o.show() : o.hide();
          }
        }
      });
    });
  })(jQuery);
</script>