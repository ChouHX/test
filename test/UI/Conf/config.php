<?php
include 'init.php';
$configSys = array(
    'DB_TYPE' => 'mysqli',
    'DB_PREFIX' => '',
    'HIDE_DEFAULT_GROUP' => false,

    //multi-language
    'LANG_SWITCH_ON' => true,
    'IS_WLINK' => true,
    'LANG_AUTO_DETECT' => false,
    'DEFAULT_LANG' => 'zh-cn',
    'LANG_LIST' => 'zh-cn,en-us,zh-tw',
    'VAR_LANGUAGE' => 'l',
    'LOAD_BALANCING' => false,

    //Map config
    'GOOGLE_MAP_API'        => '//maps.googleapis.com/maps/api/js?callback=map_init&key=AIzaSyCmL6jvCbEVVBzxV8zphqk0gyjYiop2puU',
    'GOOGLE_MAP_STATIC_API' => '//maps.googleapis.com/maps/api/staticmap?key=AIzaSyCmL6jvCbEVVBzxV8zphqk0gyjYiop2puU&center={$lat},{$lng}&zoom=13&size=100x100&markers={$lat},{$lng}',
    'GAODE_SERVICE_KEY'     => '69cfabf8696074d44d73a0965002dc1c',
    'GAODE_MAP_API'         => '//webapi.amap.com/maps?v=1.4.11&callback=map_init&key=90607d16ce8cb75c99ad2e025adf31f2',
    'GAODE_MAP_STATIC_API'  => '//restapi.amap.com/v3/staticmap?location={$lng},{$lat}&zoom=14&size=100*100&markers=mid,,:{$lng},{$lat}&key=ccf7cab65b30d393e18e43a0557aab5c',
    'GAODE_UI_LIB'          => '//webapi.amap.com/ui/1.0/main.js?v=1.0.11',
    'MAX_GPS_INDEX' => 100000,

    //system
    'SESSION_NAME' => 'm2m_ui30_user',
    'DATA_PATH' => '',

    'TOKEN_ON' => true,
    'TOKEN_NAME' => '__hash__',
    'TOKEN_TYPE' => 'md5',
    'TOKEN_RESET' => true,

    //Network mode
    'NET_MODE' => array(
        '0' => 'NONE',
        '1' => 'GSM',
        '2' => 'GPRS',
        '3' => 'CDMA&EVDO',
        '4' => 'CDMA',
        '5' => 'EVDO',
        '6' => 'WCDMA',
        '7' => 'HSDPA',
        '8' => 'HSUPA',
        '9' => 'HSPA+',
        '10' => 'TDSCDMA',
        '11' => 'FDD-LTE',
        '12' => 'TD-LTE',
        '13' => 'NB',
        '14' => 'LORA',
        '15' => '5G',
        '100' => 'Wired',
    ),

    'READ_STAT_FROM' => 'DB',

    'IMSI_MNC' => array(
        array('00', '02', '04', '07'),
        array('01', '06', '09'),
        array('03', '05', '11')
    ),

    'TERM_MODEL'  => array(
        'CM20'   => 'R200',
        'CM50'   => 'R520CM',
        'D10'    => 'D80',
        'D20'    => 'D82',
        'D21'    => 'D81',
        'DTAU'   => 'DTAU',
        'G20'    => 'G200',
        'G20V2'  => 'G200V2',
        'G50'    => 'G520',
        'G50V3'  => 'G50V3',
        'G51'    => 'G510',
        'G51V2'  => 'G510V2',
        'G90'    => 'G900',
        'G92V2'  => 'G930V2',
        'R10'    => 'R100',
        'R12'    => 'R130',
        'R20'    => 'R200',
        'R21'    => 'R210',
        'R23'    => 'R230',
        'R50'    => 'R520',
        'R51'    => 'R510',
        'ROUTER' => 'ROUTER',
        'RT10'   => 'RT601',
        'RT20'   => 'RT602',
        'RT30'   => 'RT603',
        'RT52'   => 'RT620',
        'V20'    => 'RT600',
        'V20_LR' => 'RT600_LR',
        'V21'    => 'RT610'
    ),
    'PARAMS_TYPE' => array(
        'router' => array('CM20', 'CM50', 'G20', 'G20V2', 'G50', 'G50V3', 'G51', 'G51V2', 'G90', 'G92V2', 'R10', 'R12', 'R20', 'R21', 'R23', 'R50', 'R51', 'ROUTER'),
        'rt52' => array('RT52'),
        'dtu' => array('V20'),
        'rt10'  => array('RT10'),
        'rt20'  => array('RT20'),
        'rt30'  => array('RT30'),
        'v20lr' => array('V20_LR'),
        'v21' => array('V21'),
        'd10' => array('D10'),
        'd20' => array('D20'),
        'd21' => array('D21'),
        'dtau' => array('DTAU')
    ),
    'ENABLE_RTU_SCRIPT_GET' => array('RT52', 'RT10', 'RT20', 'RT30', 'V20', 'V20_LR'),
    'ENABLE_CLEAR_FLASH' => array('V20', 'V21', 'RT20'),

    //界面样式
    'UI_VERSION' => '',

    // 移动设备界面，默认隐藏，设置成1时显示
    'ENABLE_PORTAL' => 0,

    //此处为原始GPS数据
    'GPS_DEFAULT_LNG' => 113.921219,
    'GPS_DEFAULT_LAT' => 22.574972,

    // UI设置只读模式
    'READ_ONLY_VERSION' => 0,

    // Positioning interval, 1s = 1 seconds, 2m = 2 minutes, 3h = 3 hours, 30d = 30 days
    'LBS_EXPIRED' => '30d',

    // Base station positioning, not empty means enabled
    // 'LBS_API_CELL' => 'http://api.cellocation.com:81/cell/?mcc=460&mnc=%d&lac=%d&ci=%d&output=json',

    // Wifi positioning, not empty means enabled
    // 'LBS_API_WIFI' => 'http://api.cellocation.com:81/loc/?wl=%s&output=json',

    //设置为1时month_flux显示为onelink接口查询到的流量
    'SHOW_ONELINK_MONTH_FLUX' => 0,
    'SHOW_ONENET_REPORT' => 0,

    // 是否显示点聚合DEMO页面
    'SHOW_MARKER_CLUSTER_DEMO' => 0,

    // 是否显示充电站页面
    'SHOW_CDZ' => 0,

    // 是否显示网分仪页面
    'SHOW_WFY' => 0,

    // SMTP客户端域名
    'E_HOSTNAME' => '',

    // 显示离线率报表
    'SHOW_OFFLINE_RATE_REPORT' => 0,

    'EMAIL_REG' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
);
return array_merge($config, $configSys);
?>