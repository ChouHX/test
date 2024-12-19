<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

/**
 * 短信网关
 */
class tianyuanSms {
    private $username = '';
    private $password = '';
    private $sms_url = 'http://123.56.233.239:8080/msg-core-web/msg/sendMsg';
    private $balance_url = 'http://123.56.233.239:8080/msg-core-web/msg/balance';
    private $err_code = array(
        '0'    => '调用成功',
        '1001' => '缺少参数',
        '1003' => '表示用户或密码错误',
        '1004' => 'Ip鉴权失败',
        '1005' => '用户余额不足',
        '1006' => '系统异常',
        '1008' => '一次提交手机号太多，get请求最多1000个手机号，post请求无限制',
        '1009' => '账号不支持调用SDK接口',
    );
    private $tpl_prefix = '【阳泉市经济信息中心】你本次登陆的验证码是：';
    private $ch = null;

    public function __construct(){
        $this->username = C('SMS_USERNAME');
        $this->password = C('SMS_PASSWORD');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS , 3000);
        curl_setopt($ch, CURLOPT_TIMEOUT , 5);
        curl_setopt($ch, CURLOPT_POST, 1);
        $this->ch = $ch;
    }

    public function __destruct(){
        if ($this->ch){
            curl_close($this->ch);
        }
    }

    public function send($mobile, $content, $req_send_time){
        curl_setopt($this->ch, CURLOPT_URL, $this->sms_url);
        $post_data = array(
            'sn' => $this->username,
            'password' => $this->password,
            'mobile' => $mobile,
            'content' => $this->tpl_prefix . $content,
            'sendTime' => $req_send_time == '0000-00-00 00:00:00' ? '' : $req_send_time
        );
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $ret = curl_exec($this->ch);
        $ret = json_decode($ret, true);

        if ($ret){
            return array(0, $ret['status']['message']);
        }else{
            return array(-1, 'Json decode failed.');
        }
    }

    public function balance(){
        curl_setopt($this->ch, CURLOPT_URL, $this->balance_url);
        $post_data = array(
            'sn' => $this->username,
            'password' => $this->password
        );
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        $ret = curl_exec($this->ch);
        $ret = json_decode($ret, true);

        if ($ret){
            return array(0, $ret['status']['message'], $ret['data']);
        }else{
            return array(-1, 'Json decode failed.');
        }
    }
}