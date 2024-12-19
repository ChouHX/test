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
<link rel="stylesheet" type="text/css" href="__ROOT__/Tpl/Term/sbxq.css?rand=<?php echo (CACHE_VERSION); ?>">
<!--jqGrid-->
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/css/ui.jqgrid.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/jquery-ui-1.10.4.custom.min.css?rand=<?php echo (CACHE_VERSION); ?>">
<link rel="stylesheet" type="text/css" href="../Public/jqgrid/theme/gray/custom.css?rand=<?php echo (CACHE_VERSION); ?>">
  <div class="content-wrapper">
    <section class="content padding-top-0">
      <!-- 信息分类 -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-pills" id="tab_info_types">
          <li class="active"><a href="#params_tab_1" data-toggle="tab" data-show-div="tab1"><?php echo (L("BASIC_INFO")); ?></a></li>
          <li><a href="#params_tab_2" data-toggle="tab" data-show-div="tab2"><?php echo (L("RUNTIME_INFO")); ?></a></li>
          <li><a href="#params_tab_3" data-toggle="tab" data-show-div="tab3"><?php echo (L("VAR_MENU_TASK_LIST")); ?></a></li>
          <li><a href="#params_tab_4" data-toggle="tab" data-show-div="tab4" data-init="0"><?php echo (L("VAR_DEVICE_STATEMENT")); ?></a></li>
          <li><a href="#params_tab_5" data-toggle="tab" data-show-div="tab5"><?php echo (L("VAR_MENU_RESOURCE_FILE")); ?></a></li>
          <!--<li style="float: right; background: #f7f7f7;" id="back_page">
            <a href="javascript:;"><i class="fa fa-reply">&nbsp;</i><?php echo (L("VAR_BTN_BACK")); ?></a>
          </li>-->
          <li style="float: right; background: #f7f7f7;" id="refresh_page">
            <a href="javascript:;"><i class="fa fa-refresh">&nbsp;</i><?php echo (L("VAR_REFRESH")); ?></a>
          </li>
        </ul>
      </div>

      <!--设备基本信息-->
      <div class="row info-tab info-tab1">
        <div class="col-md-12">
          <div class="box box-solid">
            <!--<div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_TIPS_DETAIL_INFO")); ?></h3>
            </div>-->
            <div class="box-body jqgrid_c">
              <table class="table table-bordered table-hover tbl-params">
                <tr>
                  <th width="13%"><?php echo (L("VAR_TG")); ?>：</th>
                  <td width="20%" id="td_gname"><?php echo ($row['gname']); ?></td>
                  <th width="13%"><?php echo (L("VAR_SN2")); ?>：</th>
                  <td width="20%" id="td_sn"><?php echo ($row['sn']); ?></td>
                  <th width="13%"><?php echo (L("VAR_SN1")); ?>：</th>
                  <td width="20%" id="td_ud_sn"><?php echo ($row['ud_sn']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("GATEWAY_SN")); ?>：</th>
                  <td id="td_gateway_sn"><?php echo ($row['gateway_sn']); ?></td>
                  <th><?php echo (L("VAR_VSN")); ?>：</th>
                  <td id="td_vsn"><?php echo ($row['vsn']); ?></td>
                  <th><?php echo (L("DEVICE_MODEL")); ?>：</th>
                  <td id="td_term_model_text"><?php echo ($row['term_model_text']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_SYSCFG_ALIAS")); ?>：</th>
                  <td id="td_alias"><?php echo ($row['alias']); ?></td>
                  <th><?php echo (L("VAR_SWV")); ?>：</th>
                  <td id="td_sw_version"><?php echo ($row['sw_version']); ?></td>
                  <th><?php echo (L("PROTOCOL_VERSION")); ?>：</th>
                  <td id="td_protocol"><?php echo ($row['protocol']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_CMD_CREATETIME")); ?>：</th>
                  <td id="td_create_time"><?php echo ($row['create_time']); ?></td>
                  <th>SSID：</th>
                  <td id="td_wifi_ssid"><?php echo ($row['wifi_ssid']); ?></td>
                  <th><?php echo (L("GUIJI_CODE")); ?>：</th>
                  <td id="td_host_sn"><?php echo ($row['host_sn']); ?></td>
                </tr>
              </table>
              <table class="table table-bordered table-hover tbl-params" style="margin-top: 30px">
                <tr><th colspan="6" class="text-center" style="color: #0dc4ef"><?php echo (L("CHANNEL_1")); ?></th></tr>
                <tr>
                  <th width="13%">SIM：</th>
                  <td width="20%" id="td_sim"><?php echo ($row['sim']); ?></td>
                  <th width="13%">IMSI：</th>
                  <td width="20%" id="td_imsi"><?php echo ($row['imsi']); ?></td>
                  <th width="13%">ICCID：</th>
                  <td width="20%" id="td_iccid"><?php echo ($row['iccid']); ?></td>
                </tr>
                <tr>
                  <th>IMEI：</th>
                  <td id="td_imei"><?php echo ($row['imei']); ?></td>
                  <th><?php echo (L("TERM_MODULE_VENDOR")); ?>：</th>
                  <td id="td_module_vendor"><?php echo ($row['module_vendor']); ?></td>
                  <th><?php echo (L("TERM_MODULE_TYPE")); ?>：</th>
                  <td id="td_module_type"><?php echo ($row['module_type']); ?></td>
                </tr>
              </table>
              <table class="table table-bordered table-hover tbl-params" style="margin-top: 30px">
                <tr><th colspan="6" class="text-center" style="color: #0dc4ef"><?php echo (L("CHANNEL_2")); ?></th></tr>
                <tr>
                  <th width="13%">SIM：</th>
                  <td width="20%" id="td_sim2"><?php echo ($row['sim2']); ?></td>
                  <th width="13%">IMSI：</th>
                  <td width="20%" id="td_imsi2"><?php echo ($row['imsi2']); ?></td>
                  <th width="13%">ICCID：</th>
                  <td width="20%" id="td_iccid2"><?php echo ($row['iccid2']); ?></td>
                </tr>
                <tr>
                  <th>IMEI：</th>
                  <td id="td_imei2"><?php echo ($row['imei2']); ?></td>
                  <th><?php echo (L("TERM_MODULE_VENDOR")); ?>：</th>
                  <td id="td_module_vendor2"><?php echo ($row['module_vendor2']); ?></td>
                  <th><?php echo (L("TERM_MODULE_TYPE")); ?>：</th>
                  <td id="td_module_type2"><?php echo ($row['module_type2']); ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!--设备运行信息-->
      <div class="row info-tab info-tab2" style="display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <!--<div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_TIPS_DETAIL_INFO")); ?></h3>
            </div>-->
            <div class="box-body">
              <table class="table table-bordered table-hover tbl-params info-tab info-tab2" style="display: none;">
                <tr>
                  <th width="13%"><?php echo (L("VAR_TERM_STATUS")); ?>：</th>
                  <td width="20%" id="td_status"><?php echo ($row['status']); ?></td>
                  <th width="13%"><?php echo (L("NET_MODE")); ?>：</th>
                  <td width="20%" id="td_net_mode"><?php echo ($row['net_mode']); ?></td>
                  <th width="13%"><?php echo (L("VAR_TERM_SIGNAL")); ?>：</th>
                  <td width="20%" id="td_term_signal"><?php echo ($row['term_signal']); ?></td>
                </tr>
                <tr>
                  <th width="13%">RSSI：</th>
                  <td width="20%" id="td_rssi"><?php echo ($row['rssi']); ?></td>
                  <th width="13%">RSRP：</th>
                  <td width="20%" id="td_rsrp"><?php echo ($row['rsrp']); ?></td>
                  <th width="13%">RSRQ：</th>
                  <td width="20%" id="td_rsrq"><?php echo ($row['rsrq']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_IP")); ?>：</th>
                  <td id="td_ip"><?php echo ($row['ip']); ?></td>
                  <th><?php echo (L("VAR_PORT")); ?>：</th>
                  <td id="td_port"><?php echo ($row['port']); ?></td>
                  <th><?php echo (L("SIM_POS")); ?>：</th>
                  <td id="td_sim_pos"><?php echo (L("CARD")); ?> <?php echo ($row['sim_pos']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_TERM_FLUX")); ?>：</th>
                  <td id="td_flux"><?php echo ($row['flux']); ?></td>
                  <th><?php echo (L("TODAY_FLUX")); ?>：</th>
                  <td id="td_day_flux"><?php echo ($row['day_flux']); ?></td>
                  <th><?PHP $onelink = C('SHOW_ONELINK_MONTH_FLUX'); echo L($onelink ? 'ONELINK_MONTH_FLUX' : 'FLUX_CURRENT_MONTH'); ?>：</th>
                  <td id="td_month_flux"><?php echo ($row['month_flux']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("ONLINE_DURATION")); ?>：</th>
                  <td id="td_online_duration"><?php echo ($row['online_duration']); ?></td>
                  <th><?php echo (L("VAR_LOGOUT_RECORD")); ?>：</th>
                  <td id="td_offline_duration"><?php echo ($row['offline_duration']); ?></td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_FIRST_LOGIN")); ?>：</th>
                  <td id="td_first_login"><?php echo ($row['first_login']); ?></td>
                  <th><?php echo (L("VAR_DEVICE_LOGIN_TIME")); ?>：</th>
                  <td id="td_login_time"><?php echo ($row['login_time']); ?></td>
                  <th><?php echo (L("VAR_LAST_LOGIN")); ?>：</th>
                  <td id="td_last_time"><?php echo ($row['last_time']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("CPU_USAGE")); ?>：</th>
                  <td id="td_cpu_usage"><?php echo ($row['cpu_usage']); ?></td>
                  <th><?php echo (L("MEMORY_USAGE")); ?>：</th>
                  <td id="td_mem_usage"><?php echo ($row['mem_usage']); ?></td>
                  <th><?php echo (L("STORAGE_USAGE")); ?>：</th>
                  <td id="td_storage_usage"><?php echo ($row['storage_usage']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VPN_NUM")); ?>：</th>
                  <td id="td_vpn_num"><?php echo ($row['vpn_num']); ?></td>
                  <th><?php echo (L("CURRENT_LINK")); ?>：</th>
                  <td id="td_current_link_text"><?php echo ($row['current_link_text']); ?></td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                </tr>
              </table>
              <table class="table table-bordered table-hover tbl-params info-tab info-tab2" style="display: none; margin-top: 30px">
                <tr><th colspan="6" class="text-center" style="color: #0dc4ef"><?php echo (L("CHANNEL_1")); ?></th></tr>
                <tr>
                  <th width="13%"><?php echo (L("NET_MODE")); ?>：</th>
                  <td width="20%" id="td_net_mode_sim1"><?php echo ($row['net_mode_sim1']); ?></td>
                  <th width="13%"><?php echo (L("VAR_TERM_SIGNAL")); ?>：</th>
                  <td width="20%" id="td_term_signal_sim1"><?php echo ($row['term_signal_sim1']); ?></td>
                  <th width="13%"><?php echo (L("VAR_IP")); ?>：</th>
                  <td width="20%" id="td_ip_sim1"><?php echo ($row['ip_sim1']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_PORT")); ?>：</th>
                  <td id="td_port_sim1"><?php echo ($row['port_sim1']); ?></td>
                  <th><?php echo (L("VAR_TERM_FLUX")); ?>：</th>
                  <td id="td_flux_sim1"><?php echo ($row['flux_sim1']); ?></td>
                  <th><?php echo (L("VAR_LAST_LOGIN")); ?>：</th>
                  <td id="td_last_time_sim1"><?php echo ($row['last_time_sim1']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_OPERATOR")); ?></th>
                  <td id="td_operator_sim1"><?php echo ($row['operator_sim1']); ?></td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                </tr>
              </table>
              <table class="table table-bordered table-hover tbl-params info-tab info-tab2" style="display: none; margin-top: 30px">
                <tr><th colspan="6" class="text-center" style="color: #0dc4ef"><?php echo (L("CHANNEL_2")); ?></th></tr>
                <tr>
                  <th width="13%"><?php echo (L("NET_MODE")); ?>：</th>
                  <td width="20%" id="td_net_mode_sim2"><?php echo ($row['net_mode_sim2']); ?></td>
                  <th width="13%"><?php echo (L("VAR_TERM_SIGNAL")); ?>：</th>
                  <td width="20%" id="td_term_signal_sim2"><?php echo ($row['term_signal_sim2']); ?></td>
                  <th width="13%"><?php echo (L("VAR_IP")); ?>：</th>
                  <td width="20%" id="td_ip_sim2"><?php echo ($row['ip_sim2']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_PORT")); ?>：</th>
                  <td id="td_port_sim2"><?php echo ($row['port_sim2']); ?></td>
                  <th><?php echo (L("VAR_TERM_FLUX")); ?>：</th>
                  <td id="td_flux_sim2"><?php echo ($row['flux_sim2']); ?></td>
                  <th><?php echo (L("VAR_LAST_LOGIN")); ?>：</th>
                  <td id="td_last_time_sim2"><?php echo ($row['last_time_sim2']); ?></td>
                </tr>
                <tr>
                  <th><?php echo (L("VAR_OPERATOR")); ?></th>
                  <td id="td_operator_sim2"><?php echo ($row['operator_sim2']); ?></td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                  <th>&nbsp;</th>
                  <td>&nbsp;</td>
                </tr>
              </table>
              <?PHP if ($lang == 'zh-cn') { ?>
              <table class="table table-bordered table-hover tbl-params info-tab info-tab2" style="display: none; margin-top: 30px">
                <tr><th colspan="6" class="text-center" style="color: #0dc4ef"><?php echo (L("POSITIONING_PARAMETER")); ?></th></tr>
                <tr>
                  <th width="13%"><?php echo (L("VAR_BASE_ADDRESS")); ?>：</th>
                  <td width="20%" id="td_lac_cellid"><?php echo ($row['lac_cellid']); ?></td>
                  <th width="13%"><?php echo (L("VAR_WIFI_MAC")); ?>：</th>
                  <td width="20%" id="td_ap_mac"><?php echo ($row['ap_mac']); ?></td>
                  <th width="13%"><?php echo (L("VAR_POSITION")); ?>：</th>
                  <td width="20%" id="td_addr"><?php echo ($row['addr']); ?></td>
                </tr>
              </table>
              <?PHP } ?>
            </div>
          </div>
        </div>
      </div>

      <!-- 地图 -->
      <div class="row info-tab info-tab1" style="margin-top: 15px;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("DLWZ")); ?>&nbsp;/&nbsp;<?php echo (L("ELECTRIC_FENCE")); ?></h3>
              <div class="pull-right box-tools"></div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                <form class="form-inline" role="form">
                  <div class="btns">
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-fence-question" onclick="javascript:$('#infoModal').modal();">
                        <i class="fa fa-question-circle"></i>&nbsp;<?php echo (L("ELECTRIC_FENCE")); ?>
                      </button>
                    </div>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-fence-record" onclick="javascript:$('#recordModal').modal();">
                        <i class="fa fa-history"></i>&nbsp;<?php echo (L("ENTRY_EXIT_FENCE_RECORDS")); ?>
                      </button>
                    </div>
                    <?PHP if (!C('READ_ONLY_VERSION')) { ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-plus"></i>&nbsp;<?php echo (L("ADD_FENCE")); ?></button>
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:;" onclick="javascript:$.gf.gm.d_type = 0;"><?php echo (L("CIRCLE_FENCE")); ?></a></li>
                        <li><a href="javascript:;" onclick="javascript:$.gf.gm.d_type = 1;"><?php echo (L("RECT_FENCE")); ?></a></li>
                        <li><a href="javascript:;" onclick="javascript:$.gf.gm.d_type = 2;"><?php echo (L("POLYGON_FENCE")); ?></a></li>
                      </ul>
                    </div>
                    <?PHP } ?>
                    <div class="btn-group">
                      <button type="button" class="btn btn-default btn-fence-refresh" onclick="javascript:$('#refresh_page').click();">
                        <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                      </button>
                    </div>
                    <div class="checkbox" style="margin-top:5px; display:none;">
                      <label><input type="checkbox" id="enable_setgps" />&nbsp;<?php echo (L("GPS_SETUP")); ?></label>
                    </div>
                  </div>
                </form>
              </div>
              <div id="map_container" style="margin: 1px; width:100%; height: 400px; border: 1px solid RGB(236, 240, 245);"></div>
            </div>
          </div>
        </div>
      </div>

      <!--普通任务-->
      <div class="row info-tab info-tab3" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_MENU_TASK_ONCE")); ?></h3>
              <div class="pull-right box-tools"></div>
            </div>
            <div class="box-body">
              <div class="nav-tabs-custom">
                <ul class="nav nav-pills" id="tab_task_status">
                  <li class="active"><a href="#tab_ts_-1" data-toggle="tab" data-tsid=""><?php echo (L("ALL_STATUS")); ?></a></li>
                  <?PHP foreach ($tsa as $k=>$v){ if ($k != '1' && $k != '5' && $k != '8') { echo '<li><a href="#tab_ts_'.$k.'" data-toggle="tab" data-tsid="'.($k == 0 ? '0,8' : $k).'">'.$v.'</a></li>'; } }?>
                  <!-- <li class="pull-right"><button type="button" class="btn btn-sm btn-refresh" data-grid-id="#list3" title="<?php echo (L("VAR_REFRESH")); ?>"><i class="fa fa-refresh"></i></button></li> -->
                </ul>
              </div>
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                <form class="form-inline" role="form">
                  <div class="btns">
                    <button type="button" class="btn btn-default rwcs btn-del-task">
                      <i class="fa fa-close"></i>&nbsp;<?php echo (L("VAR_DEL")); ?>
                    </button>
                    <button type="button" class="btn btn-default rwcs btn-refresh"  data-grid-id="#list3" title="<?php echo (L("VAR_REFRESH")); ?>">
                      <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                    </button>
                  </div>
                </form>
              </div>
              <table id="list3"></table>
              <div id="pager3" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>

      <!--周期任务-->
      <div class="row info-tab info-tab3" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_MENU_TASK_TIMED")); ?></h3>
              <div class="pull-right box-tools"></div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                <form class="form-inline" role="form">
                  <div class="btns">
                    <button type="button" class="btn btn-default rwcs btn-del-timed-task">
                      <i class="fa fa-close"></i>&nbsp;<?php echo (L("VAR_DEL")); ?>
                    </button>
                    <button type="button" class="btn btn-default rwcs btn-refresh"  data-grid-id="#list33" title="<?php echo (L("VAR_REFRESH")); ?>">
                      <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                    </button>
                  </div>
                </form>
              </div>
              <table id="list33"></table>
              <div id="pager33" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>

      <!--统计报表-->
      <div class="row info-tab info-tab4" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_MENU_OVERVIEW")); ?></h3>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 5px 30px; width: 100%">
                  <button class="btn btn-time-range btn-info btn-today"><?php echo (L("VAR_TODAY")); ?></button>
                  <button class="btn btn-time-range btn-default btn-this-month"><?php echo (L("THIS_MONTH")); ?></button>
                  <div class="input-group" style="width: 300px;">
                    <span class="input-group-addon" style="background-color:#fff !important;"> <i class="fa fa-calendar"></i> </span>
                    <input type="text" id="start_dt" class="form-control" style="width: 182px" readonly>
                  </div>
                  <div class="input-group date" style="float:right; display: <?PHP echo C('OEM_VERSION') == 'rx-m2m' ? 'inline-table' : 'none'; ?>; width: 300px;">
                    <div class="input-group-addon">
                      <i class="fa fa-wifi"></i>
                    </div>
                    <select class="form-control" style="width:200px;" id="slt_sims" onchange="javascript:$('#refresh_page').click();">
                      <option value="0">Count all SIM cards</option>
                      <option value="1">SIM 1</option>
                      <option value="2">SIM 2</option>
                    </select>
                  </div>
                  <!-- <button class="btn btn-info" style="float: right; margin-right: 15px;"><i class="fa fa-file-excel-o"></i> <?php echo (L("VAR_DEVICE_EXPORT_EXCEL")); ?></button> -->
              </div>
              <div class="row" style="margin-top: 15px; padding: 0 10px;">
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("VAR_FLUX_DETAIL")); ?> (<span id="chart_flux_unit" style="color:#0dc4ef">MB</span>)</div>
                    <div class="box-body" id="chart_flux" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("VAR_SIGNAL_DETAIL")); ?> (0~31)</div>
                    <div class="box-body" id="chart_signal" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("VAR_ONLINE_RATES")); ?> (%)</div>
                    <div class="box-body" id="chart_online" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: 15px; padding: 0 10px;">
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("NETWORK_DISTRIBUTION")); ?></div>
                    <div class="box-body" id="chart_netmode" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("CPU_USAGE")); ?> (%)</div>
                    <div class="box-body" id="chart_CPU" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("MEMORY_USAGE")); ?> (%)</div>
                    <div class="box-body" id="chart_MEM" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: 15px; padding: 0 10px;">
                <div class="col-md-4">
                  <div class="box box-border">
                    <div class="box-header with-border box-header-bg"><?php echo (L("STORAGE_USAGE")); ?> (%)</div>
                    <div class="box-body" id="chart_storage" style="height: 260px;" data-url=""></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--上线记录-->
      <div class="row info-tab info-tab2" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_DEVICE_HISTORY")); ?></h3>
              <div class="pull-right box-tools"></div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                  <button class="btn btn-time-range2 btn-info btn-today2"><?php echo (L("VAR_TODAY")); ?></button>
                  <button class="btn btn-time-range2 btn-default btn-this-month2"><?php echo (L("THIS_MONTH")); ?></button>
                  <div class="input-group" style="width: 300px;">
                    <span class="input-group-addon" style="background-color:#fff !important;"> <i class="fa fa-calendar"></i> </span>
                    <input type="text" id="start_dt2" class="form-control" style="width: 182px" readonly>
                  </div>
              </div>
              <table id="list2"></table>
              <div id="pager2" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>

      <!--网络切换记录-->
      <div class="row info-tab info-tab2" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("NET_CHANGE_RECORD")); ?></h3>
              <div class="pull-right box-tools"></div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                  <button class="btn btn-time-range22 btn-info btn-today22"><?php echo (L("VAR_TODAY")); ?></button>
                  <button class="btn btn-time-range22 btn-default btn-this-month22"><?php echo (L("THIS_MONTH")); ?></button>
                  <div class="input-group" style="width: 300px;">
                    <span class="input-group-addon" style="background-color:#fff !important;"> <i class="fa fa-calendar"></i> </span>
                    <input type="text" id="start_dt22" class="form-control" style="width: 182px" readonly>
                  </div>
              </div>
              <table id="list22"></table>
              <div id="pager22" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>

      <!--上报的参数文件-->
      <div class="row info-tab info-tab5" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_CFG_FILENAME")); ?></h3>
              <div class="pull-right box-tools">
              </div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                <form class="form-inline" role="form">
                  <div class="btns">
                    <button type="button" class="btn btn-default rwcs btn-download" data-grid-id="#list5" title="<?php echo (L("VAR_CP_DOWNLOAD")); ?>" data-act="termDetailDownCfg">
                      <i class="fa fa-download"></i>&nbsp;<?php echo (L("VAR_CP_DOWNLOAD")); ?>
                    </button>
                    <button type="button" class="btn btn-default rwcs btn-refresh"  data-grid-id="#list5" title="<?php echo (L("VAR_REFRESH")); ?>">
                      <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                    </button>
                  </div>
                </form>
              </div>
              <table id="list5"></table>
              <div id="pager5" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>

      <!--抓包文件-->
      <div class="row info-tab info-tab5" style="margin-top: 15px; display: none;">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (L("VAR_CP_FILES")); ?></h3>
              <div class="pull-right box-tools">
              </div>
            </div>
            <div class="box-body">
              <div class="btn-toolbar" role="toolbar" style="margin: 0 0 15px; width: 100%">
                <form class="form-inline" role="form">
                  <div class="btns">
                    <button type="button" class="btn btn-default rwcs btn-download" data-grid-id="#list55" title="<?php echo (L("VAR_CP_DOWNLOAD")); ?>" data-act="termDetailDownCap">
                      <i class="fa fa-download"></i>&nbsp;<?php echo (L("VAR_CP_DOWNLOAD")); ?>
                    </button>
                    <button type="button" class="btn btn-default rwcs btn-refresh"  data-grid-id="#list55" title="<?php echo (L("VAR_REFRESH")); ?>">
                      <i class="fa fa-refresh"></i>&nbsp;<?php echo (L("VAR_REFRESH")); ?>
                    </button>
                  </div>
                </form>
              </div>
              <table id="list55"></table>
              <div id="pager55" style="margin-top: 30px !important;"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<!--模态框，功能说明-->
<div class="modal fade" id="infoModal">
  <div class="modal-dialog modal-lg" style="width: 900px; margin-top: 100px;">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
          <h4 class="modal-title"><?php echo (L("PAGE_DESC")); ?></h4>
      </div>
      <div class="modal-body">
        <ul>
          <li style="line-height:20px; margin: 0 0 10px 0;"><strong><?php echo (L("CIRCLE_FENCE")); ?>：</strong><?php echo (L("CIRCLE_FENCE_TIPS")); ?></li>
          <li style="line-height:20px; margin: 0 0 10px 0;"><strong><?php echo (L("RECT_FENCE")); ?>：</strong><?php echo (L("RECT_FENCE_TIPS")); ?></li>
          <li style="line-height:20px; margin: 0 0 10px 0;"><strong><?php echo (L("POLYGON_FENCE")); ?>：</strong><?php echo (L("POLYGON_FENCE_TIPS")); ?></li>
          <li style="line-height:20px; margin: 0 0 10px 0;"><strong><?php echo (L("DRAW_CANCEL")); ?>：</strong><?php echo (L("DRAW_CANCEL_TIPS")); ?></li>
          <li style="line-height:20px; margin: 0 0 10px 0;"><strong><?php echo (L("DELETE_FENCE")); ?>：</strong><?php echo (L("DELETE_FENCE_TIPS")); ?></li>
        </ul>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal"><?php echo (L("VAR_BTN_SURE")); ?></button>
      </div>
    </div>
  </div>
</div>
<!--模态框，出入围栏记录 -->
<div class="modal fade" id="recordModal" data-jqgrid-type="0">
  <div class="modal-dialog modal-lg" style="width: 900px; margin-top: 100px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php echo (L("VAR_CLOSE")); ?></span></button>
        <h4 class="modal-title"><?php echo (L("ENTRY_EXIT_FENCE_RECORDS")); ?></h4>
      </div>
      <div class="modal-body">
        <table id="list4"></table>
        <div id="pager4" style="margin-top: 30px !important;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="javascript:$('#list4').trigger('reloadGrid');"><?php echo (L("VAR_REFRESH")); ?></button>
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
<!--daterangepicker-->
<script type="text/javascript" src="../Public/daterangepicker/moment.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/daterangepicker/daterangepicker.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!--highcharts-->
<script type="text/javascript" src="../Public/highcharts/highcharts.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/highcharts/no-data-to-display.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<!--jqgrid-->
<script type="text/javascript" src="../Public/js/jquery.browser.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/i18n/<?PHP echo $lang?>.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/jqgrid/js/jquery.jqGrid.min.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript">
$.gf.sn = "<?php echo ($row['sn']); ?>";
//$.gf.gid = "<?php echo ($_REQUEST['gid']); ?>";
$.gf.ranges = {
  '<?php echo (L("LAST_7DAYS")); ?>': [moment().subtract('days', 7), moment().subtract('days', 1)],
  '<?php echo (L("LAST_30DAYS")); ?>': [moment().subtract('days', 30), moment().subtract('days', 1)],
  '<?php echo (L("LAST_MONTH")); ?>': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
  '<?php echo (L("THIS_YEAR")); ?>': [moment().startOf('year'), moment()],
  '<?php echo (L("LAST_YEAR")); ?>': [moment().subtract('year', 1).startOf('year'), moment().subtract('year', 1).endOf('year')]
};
</script>
<!-- leaflet -->
<link rel="stylesheet" type="text/css" href="../Public/leaflet/leaflet.css?rand=<?php echo (CACHE_VERSION); ?>" />
<link rel="stylesheet" type="text/css" href="../Public/leaflet/markercluster/leaflet.markercluster.css?rand=<?php echo (CACHE_VERSION); ?>" />
<link rel="stylesheet" type="text/css" href="../Public/leaflet/markercluster/leaflet.markercluster.default.css?rand=<?php echo (CACHE_VERSION); ?>" />
<script type="text/javascript" src="../Public/leaflet/leaflet.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/leaflet/leaflet.ChineseTmsProviders.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="../Public/leaflet/LMap.js?rand=<?php echo (CACHE_VERSION); ?>"></script>
<script type="text/javascript" src="__ROOT__/Tpl/Term/sbxq.js?rand=<?php echo (CACHE_VERSION); ?>&r=20230602-1"></script>