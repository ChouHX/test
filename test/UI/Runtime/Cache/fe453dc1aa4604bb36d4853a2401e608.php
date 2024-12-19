<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?PHP echo (strpos(C('SESSION_NAME'), 'm2m_ui30_user') !== false ? L('VAR_SYSTEM_TITLE') : $_SESSION[C('SESSION_NAME')]['pinfo']['alias']).' - '.$web_path_1[count($web_path_1)-1]?></title>
  <link rel="icon" href="__ROOT__/favicon.ico" type="image/x-icon">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script type="text/javascript">
  function handle404Error(el, failed) {
    if (failed) {
      el.href = el.attributes['data-local'].nodeValue;
    } else {
      try{
        var a = el.sheet.cssRules;
      }catch(err){
        el.href = el.attributes['data-local'].nodeValue;
      }
    }
  }
  </script>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../Public/bower_components/bootstrap/css/bootstrap.min.css?rand=<?php echo (CACHE_VERSION); ?>" />

  <!-- Font Awesome -->
  <link rel="stylesheet" <?php echo ($href_src[0]); ?>="../Public/bower_components/font-awesome/font-awesome.min.css?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[1]); ?>="http://sinoios.net/m2m3.0/font-awesome.min.qiniu.css" />

  <!-- Ionicons -->
  <link rel="stylesheet" <?php echo ($href_src[0]); ?>="../Public/bower_components/Ionicons/ionicons.min.css?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[1]); ?>="http://sinoios.net/m2m3.0/ionicons.min.qiniu.css" />

  <!-- Theme style -->
  <link rel="stylesheet" href="../Public/css/AdminLTE.min.css?rand=<?php echo (CACHE_VERSION); ?>" />

  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../Public/css/_all-skins.css?rand=<?php echo (CACHE_VERSION); ?>" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <script type="text/javascript" src="__ROOT__/Runtime/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
</head>
<script type="text/javascript">
  var skin = (typeof Storage != 'undefined' && localStorage.getItem('skin')) || 'skin-blue';
  var sidebar_status = (typeof Storage != 'undefined' && localStorage.getItem('sidebar_status')) || '';
  document.write('<body class="hold-transition '+skin+' sidebar-mini '+sidebar_status+'">');
</script>
<div class="wrapper">

  <header class="main-header" style="position:fixed; width:100%;">
    <!-- Logo
    <a href="javascript:;" class="logo" style="cursor: default;">
      <span class="logo-mini">M2M</span>
      <span class="logo-lg"><i class="fa fa-home"></i>Title</span>
    </a>-->
    <h1 id="h1-logo">
      <a href="javascript:;" class="logo">
        <span class="logo-mini"><i class="fa fa-home"></i></span>
        <span class="logo-lg">
          <i class="fa fa-home"></i>
          <b><?PHP echo strpos(C('SESSION_NAME'), 'm2m_ui30_user') !== false ? L('VAR_SYSTEM_TITLE') : $_SESSION[C('SESSION_NAME')]['pinfo']['alias']; ?></b>
        </span>
      </a>
    </h1>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="javascript:;" class="sidebar-toggle" data-toggle="push-menu" role="button" title="<?php echo (L("VAR_COLLAPSE")); ?>/<?php echo (L("VAR_EXPAND")); ?>">
        <script>document.write('<i id="sidebar_toggle_icon" class="fa fa-'+(sidebar_status==='sidebar-collapse'?'indent':'outdent')+'"></i>')</script>
      </a>

      <!-- web path -->
      <ul class="path">
      <?PHP
 if ($web_path_final){ echo $web_path_final; }else{ echo $web_path_0.'<li>&gt;</li>'; foreach ($web_path_1 as $k => $v){ echo sprintf('<li class="%s">%s</li>', $k==count($web_path_1)-1 ? 'active':'', $v); if ($k != count($web_path_1)-1){ echo '<li>&gt;</li>'; } } }?>
      </ul>

      <!-- Right button -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- 通知消息: style can be found in dropdown.less
          <li class="dropdown notifications-menu">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">0</span>
            </a>
            <ul class="dropdown-menu" style="width: auto">
              <li class="header"><?PHP echo str_replace('%d',0,L('HAVE_N_ALERT'))?></li>
              <li>
                <ul class="menu">
                </ul>
              </li>
              <li class="footer"><a href="<?php echo U('Rtu/gjjl');?>"><?php echo (L("VIEW_ALL")); ?></a></li>
            </ul>
          </li> -->

          <!-- 邮件消息:
          <li class="dropdown messages-menu">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">0</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 0 messages</li>
              <li>
                <ul class="menu">
                </ul>
              </li>
              <li class="footer"><a href="javascript:;">See All Messages</a></li>
            </ul>
          </li>-->

          <!-- 任务消息：
          <li class="dropdown tasks-menu">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 0 tasks</li>
              <li>
                <ul class="menu">
                </ul>
              </li>
              <li class="footer">
                <a href="javascript:;">View all tasks</a>
              </li>
            </ul>
          </li>-->

          <!-- 账户管理: style can be found in dropdown.less -->
          <?PHP if (strpos(C('SESSION_NAME'), 'm2m_ui30_user') !== false) { ?>
          <li class="dropdown user user-menu">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo (C("PROJECT_PATH")); ?>/Upload/headimg/<?PHP echo $_SESSION[C('SESSION_NAME')]['head']?>" class="user-image user-head-img-edit" alt="User Image">
              <span class="hidden-xs"><?php echo ($uname); ?></span>&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo (C("PROJECT_PATH")); ?>/Upload/headimg/<?PHP echo $_SESSION[C('SESSION_NAME')]['head']?>" class="img-circle user-head-img-edit" alt="User Image">

                <p><?php echo ($uname); ?> - <?PHP echo $utt?><small><?PHP echo L('REGISTER_TIME').'：'.$_SESSION[C('SESSION_NAME')]['member_since']?></small>
                </p>
              </li>
              <li class="user-body">
                <?PHP if (!C('IS_WLINK')){ ?>
                <div class="row change_lang">
                  <div class="col-xs-4 text-center">
                    <a href="javascript:;" lang="zh-cn">中文</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="javascript:;" lang="en-us">English</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="javascript:;" lang="zh-tw">繁體</a>
                  </div>
                </div>
                <?PHP } ?>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo U('Syscfg/grzx');?>" class="btn btn-default btn-flat"><?php echo (L("PERSONAL_CENTER")); ?></a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo U('Index/logout');?>" class="btn btn-default btn-flat"><?php echo (L("VAR_LOGOUT")); ?></a>
                </div>
              </li>
            </ul>
          </li><?PHP } ?>
          <!--<li>
            <a href="javascript:;" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>-->
        </ul>
      </div>
    </nav>
  </header>

  <!-- 左侧边栏 -->
  <aside class="main-sidebar" style="position:fixed;">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel
      <div class="user-panel">
        <div class="pull-left image">
          <img src="../Public/img/avatar0.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>admin</p>
          <a href="<?php echo U('Syscfg/userCenter');?>"><i class="fa fa-circle text-success"></i> <?PHP echo $utt?></a>
        </div>
      </div>-->

      <!-- 搜索框
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>-->

      <!-- 菜单: style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <!-- <li class="header">Main navigation</li>
        <li class="treeview Homepage">
          <a href="javascript:;">
            <i class="fa fa-home"></i> <span><?php echo (L("VAR_HOME_PAGE")); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="ptgk"><a href="<?php echo U('Homepage/ptgk');?>"><i class="fa fa-angle-right"></i> <?php echo (L("VAR_PTGK")); ?></a></li>
            <li class="gis"><a href="<?php echo U('Homepage/gis');?>"><i class="fa fa-angle-right"></i> <?php echo (L("PANORAMA")); ?></a></li>
          </ul>
        </li>
        -->
        <?php echo ($menus); ?>
      </ul>
    </section>
  </aside>

  <!-- 右侧边栏 -->
  <aside class="control-sidebar control-sidebar-dark" id="control-sidebar-01">
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-theme-demo-options-tab" data-toggle="tab"><i class="fa fa-wrench"></i></a></li>
      <!-- <li><a href="#control-sidebar-theme-demo-options-tab-2" data-toggle="tab"><i class="fa fa-home"></i></a></li> -->
      <!-- <li><a href="#control-sidebar-theme-demo-options-tab-3" data-toggle="tab"><i class="fa fa-gear"></i></a></li> -->
    </ul>
    <div class="tab-content"></div>
  </aside>
<link rel="stylesheet" type="text/css" href="../Public/css/header.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="__ROOT__/Tpl/Term/ztree_container.css?rand=<?php echo (CACHE_VERSION); ?>">
<!--jqGrid-->
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/css/ui.jqgrid.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/jquery-ui-1.10.4.custom.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/custom.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/bootstrap_validator/bv.min.css?rand=<?php echo (CACHE_VERSION); ?>">
  <div class="content-wrapper">
    <section class="content padding-top-0">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" id="tab_device_list">
          <li class="active"><a href="#tab_1" data-toggle="tab" data-idx="0"><?php echo (L("GENERAL_SETTINGS")); ?></a></li>
          <li><a href="#tab_2" data-toggle="tab" data-idx="1"><?php echo (L("VAR_ALARM_SEND_RECORD")); ?></a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <form class="form-horizontal" role="form" id="fm1">
              <div class="form-group">
                <label class="col-lg-2 control-label"><?php echo (L("RECEIVE_EMAIL_ALARM")); ?>：</label>
                <div class="col-lg-10">
                  <label class="checkbox-inline">
                    <input type="checkbox" name="alarm_enable_email" id="spid_alarm_enable_email" />&nbsp;
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label"><?php echo (L("VAR_DEVICE_EMAIL")); ?>：</label>
                <div class="col-lg-4">
                  <input type="text" class="form-control" name="alarm_email" id="spid_alarm_email" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label"><?php echo (L("RECEIVE_WX_ALARM")); ?>：</label>
                <div class="col-lg-10">
                  <label class="checkbox-inline">
                    <input type="checkbox" name="alarm_enable_wx" id="spid_alarm_enable_wx" />&nbsp;
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-2 control-label"><?php echo (L("ALARM_RECV_WECHATS_DESC")); ?>：</label>
                <div class="col-lg-4">
                  <input type="text" class="form-control" name="alarm_wx" id="spid_alarm_wx" />
                </div>
              </div>
            </form>
          </div>

          <div class="tab-pane" id="tab_2" style="padding-top: 5px">
            <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
              <form class="form-inline" role="form" id="search_fm">
                <div class="input-group" style="float: left; margin-right: 10px">
                  <div class="input-group-addon" style="background: #f4f4f4">
                    <i class="fa fa-tasks"></i>
                  </div>
                  <select id="select1" name="alarm_type" class="form-control">
                    <option value="-1" selected="selected"><?php echo (L("RTU_WARN_TYPE")); ?>：<?php echo (L("LOG_LEVEL_3")); ?></option>
                    <?PHP $atd = L('ALARM_TYPE_DEFINE'); foreach ($atd as $key => $v) { ?>
                    <option value="<?php echo ($key); ?>"><?php echo ($v); ?></option>
                    <?PHP } ?>
                  </select>
                </div>
                <div class="input-group" style="float: left; margin-right: 10px">
                  <div class="input-group-addon" style="background: #f4f4f4">
                    <i class="fa fa-bolt"></i>
                  </div>
                  <select id="select2" name="handle_status" class="form-control">
                    <option value="-1" selected="selected"><?php echo (L("HANDLE_STATUS")); ?>：<?php echo (L("LOG_LEVEL_3")); ?></option>
                    <option value="0"><?php echo (L("UNTREATED")); ?></option>
                    <option value="1"><?php echo (L("PROCESSED")); ?></option>
                  </select>
                </div>
                <div class="btns">
                  <button type="button" class="btn btn-default rwcs" data-act="del">
                    <i class="fa fa-close"></i>&nbsp;<?php echo (L("VAR_DEL")); ?>
                  </button>
                  <button type="button" class="btn btn-default" data-act="refresh" onclick="javascript:$('#list3').trigger('reloadGrid');">
                    <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                  </button>
                </div>
                <?PHP if ($_SESSION[C('SESSION_NAME')]['id'] == 1) { ?>
                <div class="input-group">
                  <input type="text" name="searchString" class="form-control" placeholder="<?php echo (L("ALARM_RECEIVER")); ?>" style="width: 130px" />
                  <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat btn-search-border"><i class="fa fa-search"></i></button>
                  </span>
                </div>
                <?PHP }?>
              </form>
            </div>
            <table id="list3"></table>
            <div id="pager3" style="margin-top: 15px !important;"></div>
          </div>
        </div>
      </div>

      <div class="row">

        <!-- 设置告警分组 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("EQUIPMENT_ALARM_GROUPING")); ?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm2">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("VAR_TG")); ?>：</label>
                  <div class="col-lg-4">
                    <input type="text" class="form-control ztree_search" placeholder="<?php echo (L("GROUP_NAME")); ?>" style="top: 2px; right: 57px;" />
                    <button type="button" class="btn btn-xs btn-default btn-my-search" style="top: 2px; right: 34px;"><i class="fa fa-search"></i></button>
                    <ul id="termGroupTree1" class="ztree" style="border: 1px solid #d2d6de; overflow-y: scroll; max-height: 300px; min-height: 26px;"></ul>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info select_all"><?php echo (L("VAR_SELECT_ALL")); ?></button>
                <button type="button" class="btn btn-warning unselect_all"><?php echo (L("VAR_UN_SELECT_ALL")); ?></button>
            </div>
          </div>
        </div>

        <!-- 设置离线告警配置 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("DEVICE_OFFLINE_ALARM")); ?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm2">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("ALARM_INTERVAL")); ?>：</label>
                  <div class="col-lg-1"></div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_interval_offline" id="spid_alarm_interval_offline" value="60" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?PHP $vta=L('VAR_TIME_ARR'); echo $vta[2];?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("BY_NUMBER")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_offline_num" id="spid_alarm_term_offline_num" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_offline_num_threshold" id="spid_alarm_term_offline_num_threshold" value="1" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("BY_NUMBER_DESC")); ?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("BY_PERCENT")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_offline_percent" id="spid_alarm_term_offline_percent" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_offline_percent_threshold" id="spid_alarm_term_offline_percent_threshold" value="10" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("BY_PERCENT_DESC")); ?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("BY_OFFLINE_TIME")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_offline_time" id="spid_alarm_term_offline_time" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_offline_time_threshold" id="spid_alarm_term_offline_time_threshold" value="30" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("BY_OFFLINE_TIME_DESC")); ?></label>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- VPN告警配置 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?PHP echo $atd[3];?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm3">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("ALARM_INTERVAL")); ?>：</label>
                  <div class="col-lg-1"></div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_interval_vpn" id="spid_alarm_interval_vpn" value="60" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?PHP $vta=L('VAR_TIME_ARR'); echo $vta[2];?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("BY_OFFLINE_TIME")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_vpn_offline_time" id="spid_alarm_vpn_offline_time" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_vpn_offline_time_threshold" id="spid_alarm_vpn_offline_time_threshold" value="5" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("BY_OFFLINE_TIME_DESC")); ?></label>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- 信号强度告警配置 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("SIGNAL_ALARM")); ?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm4">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("ALARM_INTERVAL")); ?>：</label>
                  <div class="col-lg-1"></div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_interval_signal" id="spid_alarm_interval_signal" value="60" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?PHP echo $vta[2];?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("BY_VALUE")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_signal" id="spid_alarm_term_signal" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_signal_threshold" id="spid_alarm_term_signal_threshold" value="5" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("SIGNAL_ALARM_DESC")); ?></label>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- 流量告警配置 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("FLUX_ALARM")); ?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm5">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("ALARM_INTERVAL")); ?>：</label>
                  <div class="col-lg-1"></div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_interval_flux" id="spid_alarm_interval_flux" value="1440" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?PHP echo $vta[2];?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?PHP $aett=L('ALARM_EVENT_TYPE_TEXT'); echo $aett[0];?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_flux_month" id="spid_alarm_term_flux_month" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_flux_month_threshold" id="spid_alarm_term_flux_month_threshold" value="500" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("MONTH_FLUX_ALARM_DESC")); ?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo ($aett[1]); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_flux_day" id="spid_alarm_term_flux_day" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_flux_day_threshold" id="spid_alarm_term_flux_day_threshold" value="10" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("DAY_FLUX_ALARM_DESC")); ?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo ($aett[2]); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_term_flux_pool" id="spid_alarm_term_flux_pool" />&nbsp;
                    </label>
                  </div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_term_flux_pool_threshold" id="spid_alarm_term_flux_pool_threshold" value="1024" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("FLUX_POOL_ALARM_DESC")); ?></label>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- 电子围栏告警配置 -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("OUT_FENCE_WARNING")); ?></h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" role="form" id="fm6">
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("ALARM_INTERVAL")); ?>：</label>
                  <div class="col-lg-1"></div>
                  <div class="col-lg-3">
                    <input type="text" class="form-control" name="alarm_interval_fence" id="spid_alarm_interval_fence" value="60" />
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?PHP echo $vta[2];?></label>
                </div>
                <div class="form-group">
                  <label class="col-lg-2 control-label"><?php echo (L("IS_ENABLE")); ?>：</label>
                  <div class="col-lg-1 text-right">
                    <label class="checkbox-inline">
                      <input type="checkbox" name="alarm_fence" id="spid_alarm_fence" />&nbsp;
                    </label>
                  </div>
                  <label class="col-lg-5 control-label input-notes"><?php echo (L("OUT_FENCE_WARNING_DESC")); ?></label>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 text-center">
          <button class="btn btn-default btn-sm reload-params"><i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?></button>&nbsp;&nbsp;
          <button class="btn btn-default btn-sm save-params"><i class="fa fa-pencil"></i>&nbsp;<?php echo (L("VAR_UPDATE")); ?></button>
        </div>
      </div>
    </section>
  </div>
<!--模态框 email detail-->
<div class="modal fade" id="myLgModal3">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_BTN_SURE")); ?></span></button>
                <h4 class="modal-title"><?php echo (L("ALARM_INFO")); ?></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
            </div>
        </div>
    </div>
</div>
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <?php echo (L("VAR_COPYRIGHT_1")); ?>: <?php echo (UI_VERSION); ?>
    </div>
    <strong>Copyright &copy; <?PHP echo date('Y')?></strong> All rights reserved.
  </footer>
</div>
<script <?php echo ($href_src[2]); ?>="../Public/js/jquery.min.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/jquery.min.js"></script>
<script <?php echo ($href_src[2]); ?>="../Public/js/jquery.cookie.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/jquery.cookie.js"></script>

<!-- Bootstrap 3.3.7 -->
<script <?php echo ($href_src[2]); ?>="../Public/bower_components/bootstrap/js/bootstrap.min.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/bootstrap.min.js"></script>

<!-- Slimscroll -->
<script <?php echo ($href_src[2]); ?>="../Public/bower_components/jquery-slimscroll/jquery.slimscroll.min.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/jquery.slimscroll.min.js"></script>

<!-- AdminLTE App -->
<script <?php echo ($href_src[2]); ?>="../Public/js/adminlte.min.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/adminlte.min.js"></script>

<!-- notify -->
<script src="../Public/layer/layer.js?rand=<?php echo (CACHE_VERSION); ?>"></script>

<!--jquery ui-->
<script <?php echo ($href_src[2]); ?>="../Public/bower_components/jquery-ui/jquery-ui.min.js?rand=<?php echo (CACHE_VERSION); ?>" <?php echo ($href_src[3]); ?>="http://sinoios.net/m2m3.0/jquery-ui.min.js"></script>

<!-- nice scroll -->
<!-- <script src="../Public/js/jquery.nicescroll.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script> -->

<!-- <script src="../Public/js/demo.js?rand=<?php echo (CACHE_VERSION); ?>"></script> -->

<script type="text/javascript">
  $.gf = {
    root_path: '__ROOT__/',
    public_path:'../Public/',
    highChartsOptions: {
      global: { useUTC: false },
      lang: {
        loading: $lang.VAR_LOADING,
        noData: $lang.EXT_PAGING_1
      }
    },
    oem: '<?php echo ($oem); ?>',
    email_reg: <?PHP echo C('EMAIL_REG') ? C('EMAIL_REG') : 'null'; ?>,
    sm_screen: window.screen.height <= 768 ? true : false,
    jq_pagesize: window.screen.height <= 768 ? 10 : 15,
    lang: "<?php echo ($lang); ?>",
    map_api: "<?php echo (C("MAP_API")); ?>",
    gaode_ui: "<?php echo (C("GAODE_UI_LIB")); ?>",
    empty_js: "../Public/js/empty.js",
    default_gps_png: "../Public/images/default_gps_<?PHP echo $lang=='zh-cn'?'zh-cn':'en-us'; ?>.png",
    map_static_api: "<?php echo (C("MAP_STATIC_API")); ?>",
    show_onelink_month_flux: <?php echo (C("SHOW_ONELINK_MONTH_FLUX")); ?>,
    map_center: [<?php echo (C("GPS_DEFAULT_LAT")); ?>, <?php echo (C("GPS_DEFAULT_LNG")); ?>],
    current_link_arr: <?PHP echo json_encode(L('CURRENT_LINK_ARR')); ?>,
    refresh_rtu_data_alarm: function(){
      ajax(tpurl('Rtu','getWarningInfo'), {}, function(msg){
          if (msg.status == 0){
            $('li.notifications-menu span.label-warning').html(msg.data.num);
            $('li.notifications-menu li.header').html($lang.HAVE_N_ALERT.replace('%d',msg.data.num));
            var str = '';
            if (msg.data.rows){
              for (var i=0,row=null,content=''; i<msg.data.rows.length; i++){
                row = msg.data.rows[i];
                switch (row.warning_type){
                  case '0':
                    content = format_warning_info(row.value, row);
                    break;
                  case '1':
                    content = $lang.RTU_DATA_ALARM_TYPE[0];
                    break;
                  case '2':
                    content = row.content;
                    break;
                  default:
                    break;
                }
                str += '<li><a href="<?php echo U('Rtu/gjjl');?>"><i class="fa fa-bell-o text-yellow"></i>&nbsp;'+row.sn+'：'+content+'</a></li>';
              }
            }
            $('li.notifications-menu ul.menu').html(str);
          }
      });
    }
  };

  $(function(){
    //menus active
    var module_name = "<?PHP echo MODULE_NAME?>", act_name = "<?PHP echo ACTION_NAME?>";
    /*
    $('li.treeview.'+module_name).addClass('active');
    $('.treeview-menu li.'+act_name).addClass('active');
    */

    //menus auto click
    $('.treeview>a').click(function(){
      var new_module_name = $(this).parent().get(0).classList[1];
      if (new_module_name && new_module_name != module_name){
        var href = $(this).next().find('a:eq(0)').attr('href');
        if (href){
          location.href = href;
        }
      }
    });

    //Change lang
    $('.change_lang a').click(function(){
      $.ajax({
        url: tpurl('Index','changeLang'),
        data: {lang:$(this).attr('lang')},
        success:function(msg){
          location.reload();
        }
      });
    });
    var lang = ($.cookie('think_language') || 'en-us').toLowerCase();
    $('.change_lang a[lang='+lang+']').css({
      'font-weight':700,
      'text-decoration':'underline'
    });

    //Tooltip
    $("[data-toggle='tooltip']").tooltip();

    //Homepage href
    var a = $('ul.sidebar-menu li:first ul li:first a');
    if (a.size() != 0){
      $('#h1-logo a').attr('href', a.attr('href'));
    }

    //Dialog move
    $('.modal-dialog').draggable({handle: ".modal-header"});

    //滚动条
    // $('body').niceScroll({cursorcolor:"#cccccc"});

    /*刷新告警信息
    $.gf.refresh_rtu_data_alarm();
    window.setInterval(function(){
      $.gf.refresh_rtu_data_alarm();
    }, 10000);*/

    //header和aside增加fixed，content-wrapper增加margin-top:50px
    /*固定sidebar
    window.setTimeout(function(){
      $('body').addClass('fixed');
    }, 600);*/
  });
</script>
<script src="../Public/js/function.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
</body>
</html>
<script type="text/javascript" src="../Public/bootstrap_validator/bv.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/bootstrap_validator/lang/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!-- ztree -->
<link rel="stylesheet" type="text/css" href="../Public/ztree/css/zTreeStyle/zTreeStyle.css?rand=<?php echo (CACHE_VERSION); ?>">
<script type="text/javascript" src="../Public/ztree/js/jquery.ztree.all.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/ztree/js/jquery.ztree.exhide.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/ztree/js/fuzzysearch.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!-- jqgrid -->
<script type="text/javascript" src="../Public/js/jquery.browser.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/i18n/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/jquery.jqGrid.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="__ROOT__/Tpl/Syscfg/gjcl.js?rand=<?php echo (CACHE_VERSION); ?>&r=20230418-1"></script>