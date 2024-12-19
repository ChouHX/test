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
<link rel="stylesheet" type="text/css" href="../Public/daterangepicker/daterangepicker.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/bootstrap_validator/bv.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<!-- <link rel="stylesheet" href="../Public/bower_components/bootstrap/css/bootstrap_move_left_bug.css?rand=<?php echo (CACHE_VERSION); ?>"> -->
<link rel="stylesheet" type="text/css" href="__ROOT__/Tpl/Term/ztree_container.css?rand=<?php echo (CACHE_VERSION); ?>">
<!--jqGrid-->
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/css/ui.jqgrid.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/jquery-ui-1.10.4.custom.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/custom.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="__ROOT__/Tpl/Term/jklb.css?rand=<?php echo (CACHE_VERSION); ?>">
  <div class="content-wrapper">
    <section class="content padding-top-0">
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <!-- <span class="info-box-icon bg-aqua"><i class="glyphicon glyphicon-edit"></i></span> -->
            <span class="info-box-icon"><img src="../Public/images/banner/count.png" /></span>
            <div class="info-box-content">
              <span class="info-box-text"><?php echo (L("VAR_DEVICE_TOTAL")); ?></span>
              <span class="info-box-number">
                <span id="info_box_online" style="color: #4caf50">0</span> / <span id="info_box_total">0</span>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <!-- <span class="info-box-icon bg-green"><i class="ion ion-pie-graph"></i></span> -->
            <span class="info-box-icon"><img src="../Public/images/banner/online.png" /></span>
            <div class="info-box-content">
              <span class="info-box-text"><?php echo (L("VAR_ONLINE_RATES")); ?></span>
              <span class="info-box-number" id="info_box_online_rates">0%</span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <!-- <span class="info-box-icon bg-yellow"><i class="ion ion-settings"></i></span> -->
            <span class="info-box-icon"><img src="../Public/images/banner/task.png" /></span>
            <div class="info-box-content">
              <span class="info-box-text"><?php echo (L("TODAY_TASK")); ?></span>
              <span class="info-box-number" id="info_box_taday_task">0</span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <!-- <span class="info-box-icon bg-blue"><i class="glyphicon glyphicon-hdd"></i></span> -->
            <span class="info-box-icon"><img src="../Public/images/banner/device.png" /></span>
            <div class="info-box-content">
              <span class="info-box-text"><?php echo (L("TODAY_ADDED_DEVICE")); ?></span>
              <span class="info-box-number" id="info_box_today_new_device">0</span>
            </div>
          </div>
        </div>
      </div>
      <div class="row" style="/*margin-top: 15px;*/">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_DEVICE_LIST")); ?></h3>
            </div>
            <div class="box-body jqgrid_c">
              <div class="btn-toolbar" role="toolbar" style="margin: 5px 0 20px; width: 100%">
                <form class="form-inline" role="form" id="search_fm">
                  <div class="btns">
                    <div class="btn-group">
                      <button type="button" class="btn btn-info" title="<?php echo (L("VAR_EXPAND")); ?>" id="change_gid">
                        <span data-id="-10"><?php echo (L("VAR_ALL_DEVICE")); ?></span>&nbsp;<i class="fa fa-caret-down"></i>
                      </button>
                    </div>

                    <!-- 设备编辑 -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-edit"></i>&nbsp;<?php echo (L("VAR_MENU1")); ?>
                      </button>
                      <ul class="dropdown-menu">
                        <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                        <li><a href="javascript:;" class="addTerm"><?php echo (L("VAR_ADD")); ?></a></li>
                        <li><a href="javascript:;" class="editTerm"><?php echo (L("VAR_EDIT")); ?></a></li>
                        <li><a href="javascript:;" class="deleteTerm"><?php echo (L("VAR_DEL")); ?></a></li>
                        <?PHP } ?>
                        <li><a href="javascript:;" class="editGroup"><?php echo (L("VAR_BATCH_EDIT_GROUP")); ?></a></li>
                        <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                        <li><a href="javascript:;" class="cleanRunInfo"><?php echo (L("CLEAN_RUN_INFO")); ?></a></li>
                        <li><a href="javascript:;" class="multiSetPos"><?php echo (L("MULTI_SET_POS")); ?></a></li>
                        <?PHP if ($lang == 'zh-cn') { ?>
                        <li class="divider"></li>
                        <li><a href="javascript:;" class="refreshCellLocation" data-type="term_cell" data-act="resetCell"><?php echo (L("REFRESH_BASE_STATION_LOCATION")); ?></a></li>
                        <li><a href="javascript:;" class="refreshWifiLocation" data-type="term_wifi_ap" data-act="resetWifi"><?php echo (L("REFRESH_WIFI_LOCATION")); ?></a></li>
                        <?PHP }} ?>
                      </ul>
                    </div>

                    <!-- 参数管理 -->
                    <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>&nbsp;<?php echo (L("VAR_MENU2")); ?>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="rwcs" data-type="configGet"      data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_TERM_GET_PARAM")); ?></a></li>
                        <li class="rwcs" data-type="configSet"      data-rwcs-page="params_config_set"      data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_EDIT_PARAM")); ?></a></li>
                        <li class="rwcs" data-type="configSet2"     data-rwcs-page="params_config_set2"     data-check-model="0" style="display: none;"><a href="javascript:;"><?php echo (L("VAR_EDIT_PARAM_CUSTOM")); ?></a></li>
                        <!-- <li class="rwcs" data-type="cfgFileUpload"  data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_TERM_GET_ALL_PARAM")); ?></a></li> -->
                        <li class="rwcs" data-type="interfaceSet"   data-rwcs-page="params_interface_set"   data-check-model="0"><a href="javascript:;"><?php echo (L("ETHERNET_PORT_STATUS")); ?></a></li>
                        <li class="divider"></li>
                        <li class="rwcs" data-type="cfgFileUpload"  data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("CFG_FILE_UPLOAD")); ?></a></li>
                        <li class="rwcs" data-type="downCfg"        data-rwcs-page="params_down_cfg"        data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_CFG_ISSUED")); ?></a></li>
                        <li class="divider"></li>
                        <?PHP $tta = L('VAR_TASK_TYPE_ARR'); ?>
                        <li class="rwcs" data-type="rtuScriptGet"   data-rwcs-page="params_common"          data-check-model="1" data-enable-model="ENABLE_RTU_SCRIPT_GET"><a href="javascript:;"><?php echo (L("GET_RTU_SCRIPT")); ?></a></li>
                        <li class="rwcs" data-type="rtuScriptSet"   data-rwcs-page="params_rtu_script_edit" data-check-model="1" data-enable-model="ENABLE_RTU_SCRIPT_GET"><a href="javascript:;"><?php echo (L("SET_RTU_SCRIPT")); ?></a></li>
                      </ul>
                    </div>

                    <!-- 远程控制 -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa  fa-paper-plane-o"></i>&nbsp;<?php echo (L("VAR_MENU3")); ?>
                        <!-- <span class="caret"></span> -->
                      </button>
                      <ul class="dropdown-menu">
                        <li class="rwcs" data-type="termRestart"    data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_RESTART")); ?></a></li>

                        <li class="rwcs" data-type="termUpgrade"    data-rwcs-page="params_upgrade"         data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_UPGRADE")); ?></a></li>
                        <li class="rwcs" data-type="catchPackage"   data-rwcs-page="params_cap"             data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_CATCH_PACKAGE")); ?></a></li>
                        <?PHP if (C('ENABLE_PORTAL')) { ?>
                        <li class="rwcs" data-type="adSend"         data-rwcs-page="params_ad"              data-check-model="0"><a href="javascript:;"><?php echo (L("VAR_AD_ISSUED")); ?></a></li>
                        <?PHP } ?>
                        <!--清除Flash目前只有V20支持-->
                        <li class="rwcs" data-type="clearFlash"     data-rwcs-page="params_common"          data-check-model="1" data-enable-model="ENABLE_CLEAR_FLASH"><a href="javascript:;"><?php echo (L("CLEAR_FLASH")); ?></a></li>
                        <li class="rwcs" data-type="rtuDataSend"    data-rwcs-page="params_data_send"       data-check-model="0"><a href="javascript:;"><?php echo (L("DATA_SEND")); ?></a></li>
                        <!-- <li class="rwcs" data-type="dataTrans" data-rwcs-page="params_data_trans"      data-check-model="0"><a href="javascript:;"><?php echo (L("DATA_TRANSMISSION")); ?></a></li> -->
                        <!-- <li class="rwcs" data-type="takePhoto" data-rwcs-page="params_take_photo"      data-check-model="0"><a href="javascript:;"><?PHP $ttr=L('VAR_TASK_TYPE_ARR'); echo $ttr['take_photo']; ?></a></li> -->
                        <li class="rwcs" data-type="simChange"      data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("SIM_CHANGE")); ?></a></li>
                        <li class="rwcs" data-type="relayControl"   data-rwcs-page="params_relay_control"   data-check-model="0"><a href="javascript:;"><?php echo (L("RELAY_CONTROL")); ?></a></li>
                        <li class="divider"></li>
                        <li class="rwcs" data-type="restartModule"  data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("RECOVERY_MODULE")); ?></a></li>
                        <li class="rwcs dropright-menu" data-type="termModuleRestart" data-rwcs-page="params_module_restart" data-check-model="0"><a href="javascript:;"><?php echo (L("MODULE_RESTART")); ?></a>
                          <!--
                          <ul class="dropright">
                            <li class="rwcs" data-type="termModuleRestart" data-rwcs-page="params_common"   data-check-model="0"><a href="javascript:;"><?php echo (L("MODULE")); ?>1</a>
                            <li class="rwcs" data-type="termModuleRestart" data-rwcs-page="params_common"   data-check-model="0"><a href="javascript:;"><?php echo (L("MODULE")); ?>2</a>
                          </ul>
                          -->
                        </li>
                        <li class="divider"></li>
                        <li class="rwcs" data-type="rcConnect"      data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("RC_CONNECT")); ?></a></li>
                        <li class="rwcs" data-type="rcDissconnect"  data-rwcs-page="params_common"          data-check-model="0"><a href="javascript:;"><?php echo (L("RC_DISCONNECT")); ?></a></li>
                        <!-- <li class="----" data-type="rcrouteradd"    onclick="$.gf.addRouting()"><a href="javascript:;"><?php echo (L("N2N_ROUTER_ADD")); ?></a></li> -->
                        <li class="----" data-type="downloadClient"><a href="javascript:;" onclick="javascript:window.open('__ROOT__/Upload/ZeroTier One.msi')"><?php echo (L("REMOTE_CHANNEL_DOWNLOAD_CLIENT")); ?></a></li>
                      </ul>
                    </div>
                    <?PHP } ?>

                    <!-- 统计报表 -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-line-chart"></i>&nbsp;<?php echo (L("VAR_MENU_OVERVIEW")); ?>
                      </button>
                      <ul class="dropdown-menu export-excel">
                        <!--stack-overflow, rss, history, sellsy-->
                        <li><a href="javascript:;" data-act="exportTerm"       ><?php echo (L("DEVICE_REPORT")); ?></a></li>
                        <li><a href="javascript:;" data-act="exportTermFlux"   ><?php echo (L("FLUX_REPORT")); ?></a></li>
                        <li><a href="javascript:;" data-act="exportSignal"     ><?php echo (L("SIGNAL_REPORT")); ?></a></li>
                        <li><a href="javascript:;" data-act="exportLogins"     ><?php echo (L("VAR_LOGIN_RECORD")); ?></a></li>
                        <?PHP if (C('SHOW_OFFLINE_RATE_REPORT')) { ?>
                        <li><a href="javascript:;" data-act="exportOfflineRate"><?php echo (L("OFFLINE_RATE_REPORT")); ?></a></li>
                        <?PHP } if (C('OEM_VERSION') == 'rx-m2m') { ?>
                        <li><a href="javascript:;" data-act="exportCPUMemoryStorage"><?php echo (L("CPU_MEMORY_STORAGE_REPORT")); ?></a></li>
                        <?PHP } ?>
                        <li><a href="javascript:;" data-act="exportNetChange"  ><?php echo (L("NET_CHANGE_RECORD")); ?></a></li>
                        <?PHP if (C('SHOW_ONENET_REPORT')) { ?>
                        <li><a href="javascript:;" data-act="oneNetHistory"   ><?php echo (L("ONENET_REPORT_RECORD")); ?></a></li>
                        <?PHP } ?>
                      </ul>
                    </div>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default" id="btn_refresh" title="<?php echo (L("VAR_REFRESH")); ?>">
                        <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                      </button>
                    </div>
                    <div class="btn-group input-group">
                      <input type="text" name="searchString" class="form-control" placeholder="<?php echo (L("VAR_QUERY")); ?>" style="width: 130px" data-toggle="tooltip"  title="<?PHP echo sprintf('%s，%s，ICCID',L('VAR_SN2'),L('VAR_SYSCFG_ALIAS'))?>">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-flat btn-search-border"><i class="fa fa-search"></i></button>
                      </span>
                    </div>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default" id="btn_set_columns" title="<?php echo (L("VAR_CFG_FIELDS")); ?>">
                        <i class="glyphicon glyphicon-th icon-th"></i>&nbsp;<?php echo (L("VAR_CFG_FIELDS")); ?>
                      </button>
                    </div>
                    <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default" id="btn_rwxq" title="<?php echo (L("VAR_MENU2_TASK")); ?>">
                        <i class="fa fa-eye"></i>&nbsp;<?php echo (L("VAR_TASK")); ?>
                      </button>
                    </div>
                    <?PHP } ?>
                  </div>
                </form>
              </div>
              <table id="list2"></table>
              <div id="pager2" style="margin-top: 5px !important;"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<!--模态框-->
<div class="modal fade" id="myLgModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!--模态框，任务查看 -->
<div class="modal fade" id="gridviewTask" data-jqgrid-type="0">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
        <h4 class="modal-title" id="h4_add_edit"><?php echo (L("VAR_MENU2_TASK")); ?></h4>
      </div>
      <div class="modal-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#task_rwxq" data-toggle="tab"><?php echo (L("VAR_TASK_DETAIL")); ?></a></li>
            <li><a href="#task_rwcs" data-toggle="tab"><?php echo (L("VAR_CMD_PARAM")); ?></a></li>
          </ul>
          <div class="tab-content" style="padding-top: 20px">
            <div class="tab-pane active" id="task_rwxq">
              <table id="list3"></table>
              <div id="pager3" style="margin-top: 30px !important;"></div>
            </div>
            <div class="tab-pane" id="task_rwcs">
  						<form class="form-horizontal" role="form">
  							<div class="form-group">
  								<label class="col-lg-2 control-label"><?php echo (L("IS_ENABLE")); ?>:</label>
  								<div class="col-lg-5">
  									<input type="text" class="form-control" id = "is_enable_text" value="" readonly="readonly">
  								</div>
  							</div>
  							<div class="form-group">
  								<label class="col-lg-2 control-label"><?php echo (L("VAR_CMD_NAME")); ?>:</label>
  								<div class="col-lg-5">
  									<input type="text" class="form-control" id = "cmd_text" value="" readonly="readonly">
  								</div>
  							</div>
  							<div class="form-group">
  								<label class="col-lg-2 control-label"><?php echo (L("VAR_CMD_CREATETIME")); ?>:</label>
  								<div class="col-lg-5">
  									<input type="text" class="form-control" id = "create_time" value="" readonly="readonly">
  								</div>
  							</div>
  							<div class="form-group">
  								<label class="col-lg-2 control-label"><?php echo (L("VAR_CP_START")); ?>:</label>
  								<div class="col-lg-5">
  									<input type="text" class="form-control" id = "start_time" value="" readonly="readonly">
  								</div>
  							</div>
  							<div class="form-group">
  								<label class="col-lg-2 control-label"><?php echo (L("EXPIRED_TIME")); ?>:</label>
  								<div class="col-lg-5">
  									<input type="text" class="form-control" id = "end_time" value="" readonly="readonly">
  								</div>
  							</div>
  							<div class="form-group">
  								<label for="endtime" class="col-lg-2 control-label"><?php echo (L("VAR_CMD_PARAM")); ?></label>
  								<div class="col-lg-5">
  									<textarea class="form-control" readonly="readonly" rows="3" style="resize: none;" id ="rwcs_value"></textarea>
  								</div>
  							</div>
  						</form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick="javascript:$('#list3').trigger('reloadGrid');"><?php echo (L("VAR_REFRESH")); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
      </div>
    </div>
  </div>
</div>
<!--模态框，查看VPN数量 -->
<div class="modal fade" id="gridVPN" data-jqgrid-type="0" data-sn="">
  <div class="modal-dialog modal-lg" style="width: 1150px; margin-top: 100px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
        <h4 class="modal-title"><?php echo (L("VPN_LIST")); ?></h4>
      </div>
      <div class="modal-body">
        <table id="list4"></table>
        <div id="pager4" style="margin-top: 30px !important;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick="javascript:$('#list4').trigger('reloadGrid');"><?php echo (L("VAR_REFRESH")); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
      </div>
    </div>
  </div>
</div>
<!--模态框，查看onenet上报记录 -->
<div class="modal fade" id="gridOneNet" data-jqgrid-type="0" data-sn="">
  <div class="modal-dialog modal-lg" style="width: 1150px; margin-top: 100px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
        <h4 class="modal-title"><?php echo (L("ONENET_REPORT_RECORD")); ?></h4>
      </div>
      <div class="modal-body">
        <table id="list5"></table>
        <div id="pager5" style="margin-top: 30px !important;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick="javascript:$('#list5').trigger('reloadGrid');"><?php echo (L("VAR_REFRESH")); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
      </div>
    </div>
  </div>
</div>
<!--模态框，设置grid columns -->
<div class="modal fade" id="gridColumnsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
              <h4 class="modal-title" id="h4_add_edit"><?php echo (L("VAR_CFG_FIELDS")); ?></h4>
          </div>
          <div class="modal-body">
              <form class="form-horizontal" id="modal_fm_grid_columns">
                <!-- <li><input type="checkbox" name="columns"><span><?php echo (L("VAR_TERM_STATUS")); ?></span></li> -->
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#ul_columns_basic" data-toggle="tab"><?php echo (L("BASIC_INFO")); ?></a></li>
                    <li><a href="#ul_columns_runinfo" data-toggle="tab"><?php echo (L("RUNTIME_INFO")); ?></a></li>
      	            <?PHP if ($lang == 'zh-cn') { echo '<li><a href="#ul_columns_lbs" data-toggle="tab">'.L('POSITIONING_PARAMETER').'</a></li>'; } ?>
                  </ul>
                  <div class="tab-content" style="padding-top: 20px">
                    <div class="tab-pane active" id="ul_columns_basic">
                      <ul></ul>
                      <fieldset id="ul_columns_fieldset1" class="fieldset-custom">
                        <legend class="legend-custom"><?php echo (L("CHANNEL_1")); ?></legend>
                        <ul></ul>
                      </fieldset>
                      <fieldset id="ul_columns_fieldset2" class="fieldset-custom">
                        <legend class="legend-custom"><?php echo (L("CHANNEL_2")); ?></legend>
                        <ul></ul>
                      </fieldset>
                    </div>
                    <div class="tab-pane" id="ul_columns_runinfo">
                      <ul></ul>
                      <fieldset id="ul_columns_fieldset3" class="fieldset-custom">
                        <legend class="legend-custom"><?php echo (L("CHANNEL_1")); ?></legend>
                        <ul></ul>
                      </fieldset>
                      <fieldset id="ul_columns_fieldset4" class="fieldset-custom">
                        <legend class="legend-custom"><?php echo (L("CHANNEL_2")); ?></legend>
                        <ul></ul>
                      </fieldset>
                    </div>
                    <div class="tab-pane" id="ul_columns_lbs"><ul></ul></div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default grid_columns_reset"><?php echo (L("VAR_BTN_RESET")); ?></button>
              <button type="button" class="btn btn-info grid_columns_checkall"><?php echo (L("VAR_SELECT_ALL")); ?></button>
              <button type="button" class="btn btn-warning grid_columns_uncheckall"><?php echo (L("VAR_UN_SELECT_ALL")); ?></button>
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_CLOSE")); ?></button>
          </div>
        </div>
    </div>
</div>
<!--模态框，任务目标-->
<div class="modal fade" id="rwcs1Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
            <h4 class="modal-title"></h4>
          </div>
          <style>
            .form-horizontal label.input-notes{
              text-align: left;
              color: gray;
            }
          </style>
          <div class="modal-body">
            <form class="form-horizontal" role="form">
              <div class="form-group">
                <label class="col-lg-2 control-label"><?php echo (L("TASK_TARGET")); ?>：</label>
                <div class="col-lg-3">
                  <select class="form-control">
                    <option value="term"><?php echo (L("SELECTED_DEVICE")); ?></option>
                    <option value="group"><?php echo (L("SELECTED_GROUP")); ?></option>
                    <option value="all"><?php echo (L("VAR_ALL_ROUTER")); ?></option>
                  </select>
                  <textarea disabled="disabled" style="display:none; width: 80%; height: 50px; border-color: #d2d6de"></textarea>
                </div>
                <label class="col-lg-7 control-label input-notes"><?php echo (L("TASK_DEST_TIPS")); ?></label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success btn-task-next"><?php echo (L("NEXT_STEP")); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_BTN_CANCLE")); ?></button>
          </div>
        </div>
    </div>
</div>
<!--任务详细参数-->
<div class="modal fade" id="rwcs2Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!--编辑参数任务的时间参数-->
<div class="modal fade" id="rwcs3Modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!--router参数界面-->
<div class="modal fade" id="csModal">
    <div class="modal-dialog modal-lg" style="width: 1200px;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
            <h4 class="modal-title"><?php echo (L("VAR_EDIT_PARAM")); ?></h4>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-save-params"><i class="fa fa-cart-arrow-down"></i>&nbsp;&nbsp;<?php echo (L("ADD_PAGE_PARAMS")); ?></button>
            <button type="button" class="btn btn-success btn-submit-params"><?php echo (L("NEXT_STEP")); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_BTN_CANCLE")); ?></button>
          </div>
        </div>
    </div>
</div>
<!--模态框，导出报表条件 -->
<div class="modal fade" id="exportFluxModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
              <h4 class="modal-title" id="h4_export"></h4>
          </div>
          <div class="modal-body">
              <form class="form-horizontal" id="fm_export">
                <div class="form-group">
                  <label class="col-md-2 control-label"><?php echo (L("EXPORT_TARGET")); ?>：</label>
                  <div class="col-md-6">
                    <select id="export_act" class="form-control">
                      <option value="term"><?php echo (L("SELECTED_DEVICE")); ?></option>
                      <option value="group"><?php echo (L("SELECTED_GROUP")); ?></option>
                      <option value="all"><?php echo (L("VAR_ALL_ROUTER")); ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group sims">
                  <label class="col-md-2 control-label"><?php echo (L("EXPORT_WHICH_SIM")); ?>：</label>
                  <div class="col-md-6">
                    <select id="export_sim_num" class="form-control">
                      <option value="0"><?php echo (L("VAR_EXPORT_ALL")); ?></option>
                      <option value="1">SIM 1</option>
                      <option value="2">SIM 2</option>
                    </select>
                  </div>
                </div>
                <div class="form-group types">
                  <label class="col-md-2 control-label"><?php echo (L("EXPORT_TYPE")); ?>：</label>
                  <div class="col-md-6">
                    <select id="export_type" class="form-control">
                      <option value="cpu"><?php echo (L("CPU_USAGE")); ?></option>
                      <option value="mem"><?php echo (L("MEMORY_USAGE")); ?></option>
                      <option value="storage"><?php echo (L("STORAGE_USAGE")); ?></option>
                    </select>
                  </div>
                </div>
                <div class="form-group radios">
                  <label class="col-md-2 control-label"><?php echo (L("EXPORT_METHOD")); ?>：</label>
                  <div class="col-md-6">
                      <label class="radio-inline">
                          <input type="radio" name="set_type" value="0" checked="checked" />&nbsp;<?php echo (L("VAR_COUNT_DAY")); ?>
                      </label>
                      <label class="radio-inline">
                          <input type="radio" name="set_type" value="1" />&nbsp;<?php echo (L("VAR_COUNT_MONTH")); ?>
                      </label>
                  </div>
                </div>
                <div class="form-group ranges">
                  <label class="col-md-2 control-label"><?php echo (L("VAR_TIME_RANGE")); ?>：</label>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon" style="background-color:#fff !important;"> <i class="fa fa-calendar"></i> </span>
                      <input type="text" id="exportFluxTimeRange" class="form-control" />
                    </div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-success" onclick="$.gf.submit_export()"><?php echo (L("VAR_BTN_SURE")); ?></button>
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_BTN_CANCLE")); ?></button>
          </div>
        </div>
    </div>
</div>
<!--模态框，ztree-->
<div class="modal fade" id="ztreeModal">
    <div class="modal-dialog modal-lg" style="width: 500px">
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
              <h4 class="modal-title" id="h4_ztree"><?php echo (L("BATCH_OPERATION")); ?></h4>
          </div>
          <div class="modal-body">
            <ul id="termGroupTree1" class="ztree"></ul>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-success submit_select_group"><?php echo (L("VAR_BTN_SURE")); ?></button>
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo (L("VAR_BTN_CANCLE")); ?></button>
          </div>
        </div>
    </div>
</div>
<!--模态框，ztree2-->
<div class="modal fade" id="ztreeModal2">
    <div class="modal-dialog modal-lg" style="width: 500px">
        <div class="modal-content">
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
<!-- jqgrid -->
<script type="text/javascript" src="../Public/js/jquery.browser.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/i18n/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/jquery.jqGrid.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!-- ztree -->
<link rel="stylesheet" type="text/css" href="../Public/ztree/css/zTreeStyle/zTreeStyle.css?rand=<?php echo (CACHE_VERSION); ?>">
<script type="text/javascript" src="../Public/ztree/js/jquery.ztree.all.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/ztree/js/jquery.ztree.exhide.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/ztree/js/fuzzysearch.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript">
  $.gf.gid = -10;
  $.gf.term_model = <?PHP  echo json_encode(array_keys(C('TERM_MODEL')))?>;
  $.gf.term_params_type = <?PHP  echo json_encode(C('PARAMS_TYPE'))?>;
  $.gf.enable_rtu_script_get = <?PHP  echo json_encode(C('ENABLE_RTU_SCRIPT_GET'))?>;
  $.gf.prev_page = "<?php echo ($_SERVER['HTTP_REFERER']); ?>".indexOf('sbxq');
  // router, dtu params
  $.gf.params_def_src = "__ROOT__/Tpl/Term/modal/device_params_define_%s.js";
</script>
<!--daterangepicker-->
<script type="text/javascript" src="../Public/daterangepicker/moment.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/daterangepicker/daterangepicker.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!--jklb-->
<script type="text/javascript" src="__ROOT__/Tpl/Term/<?PHP echo C('OEM_VERSION') == 'rx-m2m' ? 'TermParamsJson_rx_m2m' : 'TermParamsJson'; ?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="__ROOT__/Tpl/Term/jklb.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!-- export excel -->
<script type="text/javascript" src="../Public/js/buttons/jszip.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/js/buttons/buttons.html5.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!--map-->
<script type="text/javascript" src="__ROOT__/Tpl/Public/js/map.js?rand=<?php echo (CACHE_VERSION); ?>"></script>