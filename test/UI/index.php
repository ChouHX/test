<?php
header('Access-Control-Allow-Origin:*');
// header("Access-Control-Allow-Origin:http://192.168.10.198:9528");
// header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Methods:*'); // 响应类型
header('Access-Control-Allow-Headers:x-requested-with,content-type'); // 响应头设置

define('APP_DEBUG', true);
error_reporting(1);

if (!is_dir('./Conf')) {
    mkdir('./Conf');
}
if (!file_exists('./Conf/config.php')) {
    copy('../Conf/config.php', './Conf/config.php');
}
if (!file_exists('./Conf/init.php')) {
    copy('../Conf/init.php', './Conf/init.php');
}
if (!file_exists('./Conf/menu.json')) {
    copy('../Conf/menu.json', './Conf/menu.json');
}

define('APP_NAME', '.');
define('APP_PATH', './');
include './ThinkPHP/ThinkPHP.php';