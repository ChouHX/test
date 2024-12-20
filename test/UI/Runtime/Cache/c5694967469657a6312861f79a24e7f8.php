<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title><?PHP echo L('VAR_SYSTEM_TITLE').' - '.L('VAR_USER_LOGIN')?></title>
<link rel="icon" href="__ROOT__/favicon.ico" type="image/x-icon">
<link type="text/css" rel="stylesheet" href="../Public/login2/images/login2.css?rand=<?php echo (CACHE_VERSION); ?>">
<script type="text/javascript" src="../Public/js/jquery.min.js?rand=<?php echo (CACHE_VERSION); ?>" ></script>
</head>


<body>
<div class="swiper"></div>
<div class="swiper2"></div>
<div class="head">
	<div class="wrapper">
        <img src="../Public/login/images/web_logo.png" style="height:22px; float:left; margin-top:14px;">
        <div class="menu">
            <a href="javascript:;" id="menu2" tabindex="-1" style="text-decoration:none; cursor:default;"><?php echo (L("VAR_SYSTEM_TITLE")); ?></a>
            <span>|</span>
            <a href="http://www.wlink-tech.com/" id="menu4" target="_blank" tabindex="-1"><?php echo (L("VAR_VISIT_US")); ?></a>
            <?PHP if (C('LOAD_BALANCING')){ ?>
            <span>|</span>
            <a href="javascript:;" id="menu5" tabindex="-1"><?php echo (L("LOAD_BALANCING")); ?></a>
            <?PHP } ?>
        </div>
    </div>
</div>
<div class="banner_area" id="banner_list">
    <div class="main_cont">
        <div class="wrapper">
            <form name="loginForm" id="loginForm" action="<?php echo U('Index/checkLogin');?>" method="post">
                <dl class="xl_info clearfix">
                    <dt class="hide"><?php echo (L("VAR_USER_LOGIN")); ?></dt>
                    <dd><input type="text" name="na" class="srh" placeholder="<?php echo (L("PLEASE_ENTER_ACCOUNT")); ?>" autocomplete="off"></dd>
                    <dd><input type="password" name="pa" class="srh" placeholder="<?php echo (L("PLEASE_ENTER_PASSWORD")); ?>" autocomplete="off">
                        <input type="hidden" name="login_type" value="">
                    </dd>
                    <dd><input type="checkbox" name="remember" class="remember"><span><?php echo (L("REMEMBER_PASSWORD")); ?></span></dd>
                    <dd>
                        <input class="button blue radius4 shenqing" type="submit" value="<?php echo (L("VAR_BTN_LOGIN")); ?>">
                        <input class="button blue radius4 lj" type="button" value="<?php echo (L("VAR_BTN_RESET")); ?>" id="resetForm" style="background: #ededed; color: #7d6f6f; border-color: #d6c4c4;"  tabindex="-1">
                    </dd>
                </dl>
            </form>
        </div>
        <div class="bg"></div>
    </div>
</div>
<div class="foot">
    <p><a href="javascript:;" tabindex="-1">Copyright © 2014 - <?PHP echo date('Y')?></a></p>
    <p class="font14"><a href="javascript:;" tabindex="-1"><?php echo (L("VAR_COPYRIGHT_1")); ?>: <?php echo (UI_VERSION); ?></a>|<a href="javascript:;" tabindex="-1"><?php echo (L("VAR_COPYRIGHT_2")); ?>: <?php echo (UI_RELEASE_DATE); ?></a></p>
</div>
<script type="text/javascript">
$(function(){
    $('.swiper, .swiper2').css('height', $(window).height());
    $('.main_cont').css('top', $(window).height() * 0.32);
    $('#resetForm').click(function(){
        document.getElementById('loginForm').reset();
    });
    $('input[name=na]').focus();
    $('.xl_info dd input.remember').next('span').click(function(){
        $(this).prev().click();
    });

    $('#menu5').click(function(){
        var txt = '<?php echo (L("LOAD_BALANCING")); ?>';
        if ($(this).find('i').size() == 0){
            $(this).html('<i class="fa fa-check-circle-o">&nbsp;'+txt+'</i>');
            $('input[name=login_type]').val('fzjh');
        }else{
            $(this).html(txt);
            $('input[name=login_type]').val('');
        }
    });

    setInterval(function(){
        var w = $('.swiper').width();
        if ($('.swiper2').css('z-index') == '1') {
            // 隐藏 swiper
            $('.swiper').animate({left:-w}, 800, 'swing', function(){
                $('.swiper2').css('z-index', 2);
                $('.swiper').css('z-index', 1);
                $('.swiper').css('left', 0);
            });
        } else {
            //隐藏 swiper2
            $('.swiper2').animate({left:-w}, 800, 'swing', function(){
                $('.swiper').css('z-index', 2);
                $('.swiper2').css('z-index', 1);
                $('.swiper2').css('left', 0);
            });
        }
    }, 5000);
});
</script>
</body>
</html>