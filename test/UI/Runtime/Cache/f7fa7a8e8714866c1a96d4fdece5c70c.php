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
<link rel="stylesheet" type="text/css" href="../Public/daterangepicker/daterangepicker.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/css/header.css?rand=<?php echo (CACHE_VERSION); ?>">
<!--fileinput-->
<link rel="stylesheet" type="text/css" href="__ROOT__/Tpl/Public/bootstrap_fileinput/css/fileinput.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<!--jqGrid-->
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/css/ui.jqgrid.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/jquery-ui-1.10.4.custom.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/custom.css?rand=<?php echo (CACHE_VERSION); ?>">
  <div class="content-wrapper">
    <section class="content padding-top-0">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("SYSTEM_SETTING")); ?></h3>
            </div>
            <div class="box-body">
              <div class="nav-tabs-custom" style="position: relative; margin-bottom: 10px;">
                <div class="my-loading"></div><!--loading div-->
                <ul class="nav nav-tabs" id="tab_params_list">
                  <li class="active"><a href="#params_tab_1" data-toggle="tab"><?php echo (L("SYSTEM_INFO")); ?></a></li><!--系统信息-->
                  <li><a href="#params_tab_2" data-toggle="tab"><?php echo (L("SYSCFG_DEVICE_FLUX")); ?></a></li><!-- 移动设备配置-->
                  <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                  <li><a href="#params_tab_3" data-toggle="tab"><?php echo (L("SYSCFG_ROUTER_FLUX")); ?></a></li><!-- 设备流量限制-->
                  <!-- <li><a href="#params_tab_4" data-toggle="tab"><?php echo (L("SYSCFG_ROUTER_HB")); ?></a></li> 路由器流水记录-->
                  <!-- <li><a href="#params_tab_5" data-toggle="tab"><?php echo (L("SYSCFG_AUTO_CAP")); ?></a></li> 自动抓包-->
                  <li><a href="#params_tab_6" data-toggle="tab"><?php echo (L("SIM_CONFIG")); ?></a></li><!--sim卡自动切换-->
                  <?PHP } ?>
                  <!--<li><a href="#params_tab_8" data-toggle="tab"><?php echo (L("OTHER_SETTING")); ?></a></li> 其他设置-->
                  <li><a href="#params_tab_9" data-toggle="tab"><?php echo (L("VAR_TG_RULE")); ?></a></li><!--设备分组规则-->
                  <li><a href="#params_tab_10" data-toggle="tab"><?php echo (L("MAIL_SERVER_SETTINGS")); ?></a></li><!--发件箱配置-->
                  <li><a href="#params_tab_11" data-toggle="tab"><?php echo (L("ENTERPRISE_WECHAT_CONFIG")); ?></a></li><!--企业微信配置-->
                  <li><a href="#params_tab_7" data-toggle="tab"><?php echo (L("VAR_MENU_LOG")); ?></a></li><!--日志-->
                  <!--注意：修改了tab个数后要去文件SystemParamsjson.js中修改参数(maxFormId)-->
                </ul>
                <div class="tab-content params-tab-content">
                  <div class="tab-pane active" id="params_tab_1">
                    <form class="form-horizontal" role="form" id="form_param_1">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("LICENCE_TYPE_TITLE")); ?>：</label>
                        <div class="col-lg-3">
                          <input type="text" class="form-control" name="licence_type" id="tpid_licence_type" disabled="disabled">
                        </div>
                        <label class="col-lg-5 control-label input-notes"></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PLATFORM_VALID_PERIOD")); ?>：</label>
                        <div class="col-lg-3">
                          <input type="text" class="form-control" name="licence_end_time" id="tpid_licence_end_time" disabled="disabled">
                        </div>
                        <label class="col-lg-5 control-label input-notes" id="tpid_licence_end_time_tips"></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("LICENCE_LIMIT_TITLE")); ?>：</label>
                        <div class="col-lg-3">
                          <input type="text" class="form-control" name="licence_limit" id="tpid_licence_limit" disabled="disabled">
                        </div>
                        <label class="col-lg-5 control-label input-notes"><?php echo (L("LICENCE_LIMIT")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("VAR_COPYRIGHT_1")); ?>：</label>
                        <div class="col-lg-3">
                          <input type="text" class="form-control" name="version" value="<?php echo (UI_VERSION); ?>" disabled="disabled">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("VAR_COPYRIGHT_2")); ?>：</label>
                        <div class="col-lg-3">
                          <input type="text" class="form-control" name="release" value="<?php echo (UI_RELEASE_DATE); ?>" disabled="disabled">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_0")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_2">
                    <form class="form-horizontal" role="form" id="form_param_2">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("DEVICE_FLUX_LIMIT_VALUE_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="device_month_flux_limit" id="tpid_device_month_flux_limit">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("DEVICE_FLUX_LIMIT_VALUE")); ?></label>
                      </div>

                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("DAILY_FLUX_LIMIT")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="device_day_flux_limit" id="tpid_device_day_flux_limit">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("DAILY_FLUX_LIMIT_INFO")); ?></label>
                      </div>

                      <div class="form-group" style="display: none;">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("ENABLE_SINGLE_DEVICE_DATA_LIMIT")); ?>：</label>
                        <div class="col-lg-2">
                          <div class="checkbox-inline">
                            <input class="ck-top-0" type="checkbox" name="enable_device_conf_limit" id="tpid_enable_device_conf_limit">
                          </div>
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ENABLE_SINGLE_DEVICE_DATA_LIMIT_DESC")); ?></label>
                      </div>

                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("ENABLE_DEVICE_MONTH_FLUX_LIMIT")); ?>：</label>
                        <div class="col-lg-2">
                          <div class="checkbox-inline">
                            <input class="ck-top-0" type="checkbox" name="enable_device_month_flux_limit" id="tpid_enable_device_month_flux_limit">
                          </div>
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ENABLE_DEVICE_GROUP_DATA_LIMIT_DESC")); ?></label>
                      </div>

                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("ENABLE_DEVICE_DAY_FLUX_LIMIT")); ?>：</label>
                        <div class="col-lg-2">
                          <div class="checkbox-inline">
                            <input class="ck-top-0" type="checkbox" name="enable_device_day_flux_limit" id="tpid_enable_device_day_flux_limit">
                          </div>
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ENABLE_DEVICE_GROUP_DATA_LIMIT_DESC")); ?></label>
                      </div>

                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("DEVICE_AUTH_DURATION_TITLE")); ?>：</label>
                        <div class="col-lg-2 text-right">
                          <input type="text" class="form-control" name="device_auth_duration" id="tpid_device_auth_duration">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("DEVICE_AUTH_DURATION")); ?></label>
                      </div>

                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_1")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_3">
                    <form class="form-horizontal" role="form" id="form_param_3">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("DEVICE_MONTHLY_FLUX_LIMIT")); ?>：</label>
                        <div class="col-lg-1">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="enable_month_flux_limit" id="tpid_enable_month_flux_limit">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ROUTER_FLUX_LIMIT_VALUE")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("DEVICE_DAILY_FLUX_LIMIT")); ?>：</label>
                        <div class="col-lg-1">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="enable_day_flux_limit" id="tpid_enable_day_flux_limit">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ROUTER_FLUX_LIMIT_VALUE_DAY")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("ENABLE_GEO_FENCE")); ?>：</label>
                        <div class="col-lg-2">
                          <!--
                          <label class="checkbox-inline">
                            <input class="ck-top-0" type="checkbox" name="enable_electronic_fence" id="tpid_enable_electronic_fence">
                          </label>
                          -->
                          <select class="form-control" name="enable_electronic_fence" id="tpid_enable_electronic_fence">
                            <option value="0"><?php echo (L("VAR_DISABLE")); ?></option>
                            <option value="1"><?php echo (L("LEAVE_FENCE_LIMIT_DATA")); ?></option>
                            <option value="2"><?php echo (L("IN_FENCE_LIMIT_DATA")); ?></option>
                          </select>
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ENABLE_GEO_FENCE_DESC")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_2")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_4">
                    <form class="form-horizontal" role="form" id="form_param_4">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("ROUTER_HB_FLOW_ENABLE_TITLE")); ?>：</label>
                        <div class="col-lg-1 text-right">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="router_hb_flow_enable" id="tpid_router_hb_flow_enable">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ROUTER_HB_FLOW_ENABLE")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_3")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_5">
                    <form class="form-horizontal" role="form" id="form_param_5">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_ENABLE_TITLE")); ?>：</label>
                        <div class="col-lg-1 text-right">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="auto_cap_enable" id="tpid_auto_cap_enable">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("ROUTER_HB_FLOW_ENABLE")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_TRI_TIME_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="auto_cap_tri_time" id="tpid_auto_cap_tri_time">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("AUTO_CAP_TRI_TIME")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_TRI_FLUX_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="auto_cap_tri_flux" id="tpid_auto_cap_tri_flux">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("AUTO_CAP_TRI_FLUX")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_CAP_TIME_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="auto_cap_cap_time" id="tpid_auto_cap_cap_time">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("AUTO_CAP_CAP_TIME")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_CAP_LEVEL_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <select class="form-control" name="auto_cap_cap_level" id="tpid_auto_cap_cap_level">
                            <option value="-100"><?php echo (L("PLEASE_SELECT")); ?></option>
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                          </select>
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("AUTO_CAP_CAP_LEVEL")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CAP_SILENT_TIME_TITLE")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="auto_cap_silent_time" id="tpid_auto_cap_silent_time">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("AUTO_CAP_SILENT_TIME")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_4")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_6">
                    <form class="form-horizontal" role="form" id="form_param_6">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("SIM_CHANGE_THRESHOLD")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="change_sim_pos_flux" id="tpid_change_sim_pos_flux">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("SIM_CHANGE_THRESHOLD_DESC")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("PAGE_DESC")); ?>：</label>
                        <div class="col-sm-10">
                          <p class="form-control-static"><?php echo (L("PAGE_DESC_6")); ?></p>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_7">
                    <form class="form-horizontal" role="form" id="form_param_7">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTO_CLEAN_LOG")); ?>：</label>
                        <div class="col-lg-1 text-left">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="log_auto_clear" id="tpid_log_auto_clear">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("LOG_RETENTION_TIME")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="log_reserve" id="tpid_log_reserve">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("LOG_RETENTION_TIME_DESC")); ?></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("AUTOMATICALLY_CLEAN_GPS_DATA_FILES")); ?>：</label>
                        <div class="col-lg-1 text-left">
                            <label class="checkbox-inline">
                              <input class="ck-top-0" type="checkbox" name="auto_clear_gps" id="tpid_auto_clear_gps">
                            </label>
                        </div>
                        <div class="col-lg-1">&nbsp;</div>
                        <label class="col-lg-8 control-label input-notes"></label>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("GPS_DATA_RETENTION_TIME")); ?>：</label>
                        <div class="col-lg-2">
                          <input type="text" class="form-control" name="gps_reserve_days" id="tpid_gps_reserve_days">
                        </div>
                        <label class="col-lg-8 control-label input-notes"><?php echo (L("UNIT_IN_DAYS")); ?></label>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_9">
                    <form class="form-horizontal" role="form" id="form_param_9">
                      <div class="form-group">
                        <label class="col-lg-2 control-label fw700"><?php echo (L("RULE_RUN_MODE")); ?>：</label>
                        <div class="col-lg-3 text-left">
                          <select class="form-control" name="term_group_rule_mode" id="tpid_term_group_rule_mode">
                            <option value="0"><?php echo (L("FIRST_LOGIN_EXEC_RULE")); ?></option>
                            <option value="1"><?php echo (L("EVERY_LOGIN_EXEC_RULE")); ?></option>
                          </select>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_10">
                    <form class="form-horizontal" role="form" id="form_param_10">
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("SEND_SERVER_ADDRESS")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="email_config_host" id="tpid_email_config_host">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="endtime" class="col-lg-2 control-label">SSL：</label>
                        <div class="col-lg-4">
                          <label class="checkbox-inline">
                            <input type="checkbox" name="email_config_ssl" id="tpid_email_config_ssl">&nbsp;
                          </label>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("SERVER_PORT")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="email_config_port" id="tpid_email_config_port">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("ACCOUNT")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="email_config_account" id="tpid_email_config_account">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("VAR_PASSWD")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="email_config_password" id="tpid_email_config_password">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("SENDER_NAME")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="email_config_from" id="tpid_email_config_from">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="endtime" class="col-lg-2 control-label">Smtp auth：</label>
                        <div class="col-lg-4">
                          <label class="checkbox-inline">
                            <input type="checkbox" name="email_config_smtp_auth" id="tpid_email_config_smtp_auth">&nbsp;
                          </label>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="endtime" class="col-lg-2 control-label"><?php echo (L("TEST_EMAIL_CONFIG")); ?>：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" id="test_email">
                        </div>
                        <button type="button" class="btn btn-default" id="btn_test_email"><?php echo (L("VAR_TEST")); ?></button>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="params_tab_11">
                    <form class="form-horizontal" role="form" id="form_param_11">
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("REGISTER_ADDR")); ?>：</label>
                        <div class="col-lg-4">
                          <p class="form-control-static"><a href="https://work.weixin.qq.com/" target="_blank">https://work.weixin.qq.com/</a></p>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">corpid：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="weixin_config_corpid" id="tpid_weixin_config_corpid">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">corpsecret：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="weixin_config_corpsecret" id="tpid_weixin_config_corpsecret">
                        </div>
                        <button type="button" class="btn btn-default btn-test-wx" data-type="0"><?php echo (L("VAR_TEST")); ?></button>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">agentid：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="weixin_config_agentid" id="tpid_weixin_config_agentid">
                        </div>
                        <button type="button" class="btn btn-default btn-test-wx" data-type="1"><?php echo (L("VAR_TEST")); ?></button>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label">txl_secret：</label>
                        <div class="col-lg-4">
                          <input type="text" class="form-control" name="weixin_config_txl_secret" id="tpid_weixin_config_txl_secret">
                        </div>
                        <button type="button" class="btn btn-default btn-test-wx" data-type="2"><?php echo (L("VAR_TEST")); ?></button>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo (L("QR_CODE")); ?>：</label>
                        <div class="col-lg-4">
                          <img src="../Public/images/wxqyh.jpg" class="img-responsive img-qrcode">
                        </div>
                        <button type="button" class="btn btn-default btn-upload-qrcode"><?php echo (L("VAR_UPLOAD")); ?></button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div role="toolbar" class="text-center">
                <button type="button" class="btn btn-default btn-sm" id="btn_reload_params"><i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?></button>&nbsp;&nbsp;
                <button type="button" class="btn btn-default btn-sm" id="btn_confirm"><i class="fa fa-pencil"></i>&nbsp;<?php echo (L("VAR_UPDATE")); ?></button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--日志-->
      <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("SYSTEM_OPERATION_LOG")); ?></h3>
            </div>
            <div class="box-body" id="tab_1">
              <table id="list1"></table>
              <div id="pager1" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<!--模态框 edit qrcode-->
<div class="modal fade" id="myLgModal2">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_BTN_SURE")); ?></span></button>
                <h4 class="modal-title"><?php echo (L("QR_CODE")); ?></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="modal_fm2">
                    <div class="form-group">
                      <label class="col-md-2" style="text-align: right;"><?php echo (L("VAR_UPGRADE_FILE")); ?>:<span class="required-field"></span></label>
                      <div class="col-md-8">
                        <input type="file" id="filedata" name="filedata">
                      </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success save_edit_qrcode"><?php echo (L("VAR_BTN_SURE")); ?></button>
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
<!--jqgrid-->
<script type="text/javascript" src="../Public/js/jquery.browser.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/i18n/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/jquery.jqGrid.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!--fileinput-->
<script type="text/javascript" src="__ROOT__/Tpl/Public/bootstrap_fileinput/js/fileinput.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="__ROOT__/Tpl/Public/bootstrap_fileinput/js/locales/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript">$.gf.lang = '<?PHP echo $lang?>';</script>
<!--daterangepicker-->
<script type="text/javascript" src="../Public/daterangepicker/moment.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/daterangepicker/daterangepicker.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript">
  $.gf.task_type = "<?PHP echo $_REQUEST['type']?>";
  $.gf.gid = "<?PHP echo $_REQUEST['gid']?>";
  $.gf.sns = "<?PHP echo $_REQUEST['sns']?>";
</script>
<script type="text/javascript" src="__ROOT__/Tpl/Syscfg/SystemParamsJson.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="__ROOT__/Tpl/Syscfg/xtsz.js?rand=<?php echo (CACHE_VERSION); ?>"></script>