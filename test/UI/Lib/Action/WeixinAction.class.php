<?php
class WeixinAction extends CommonAction{
    /**
     * 1. 注册企业号，并在微信插件里面上传logo
     * 2. 下载企业号二维码到UI下面，页面可显示，以便关注
     * 3. 添加一个告警部门(获得部门id)，添加一个告警应用(获得应用id)，将该应用的“可见范围”设置为“该部门”。
     * 4. 管理工具 -> 通讯录同步 -> 开启API接口同步 -> 权限(可编辑通讯录) -> 到init.php中设置 Secret参数
     * 5. 在init.php中配置微信开发者key相关参数
     * 7.要成员扫微信插件里面(或UI页面上)的二维码关注
     * 8.UI调用接口添加成员
     */
    private function wx_get_token($token_type = '', $params = null){
        if ($token_type == 'txl'){
            $cache_name = 'wx_token_txl';
            $corpsecret = is_array($params) ? $params['txl_secret'] : C('WX_TXL_SECRET');
        }else{
            $cache_name = 'wx_token_alarm';
            $corpsecret = is_array($params) ? $params['corpsecret'] : C('WX_CORPSECRET');
        }
        import("ORG.Net.Http");
        $token = is_array($params) ? '' : S($cache_name);
        $ret = array('errmsg'=>'OK', 'errcode'=>0);
        if (!$token){
            $corpid = is_array($params) ? $params['corpid'] : C('WX_CORPID');
            $url = sprintf('https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s', $corpid, $corpsecret);
            $ret = Http::curlRequest($url);
            if ($ret && $ret['errcode'] == 0){
                S($cache_name, $ret['access_token'], 7200);
                $token = $ret['access_token'];
            }
        }
        return array(
        	'info' => empty($ret['errmsg']) ? 'Failed.' : $ret['errmsg'],
        	'status' => isset($ret['errcode']) ? $ret['errcode'] : -1,
        	'token'=> $token
    	);
    }

    //测试微信企业号配置
    public function wx_test_config(){
        $type = I('type',0,'intval');
        $params = array(
        	'corpid' => I('weixin_config_corpid'),
        	'corpsecret' => I('weixin_config_corpsecret'),
        	'agentid' => I('weixin_config_agentid'),
        	'txl_secret' => I('weixin_config_txl_secret')
    	);
    	$ret = $this->wx_get_token($type==2?'txl':'', $params);
        if ($type == 0 || $ret['status'] != 0){
        	$this->ajaxReturn($ret);
        }
        if ($type == 1){
	        $url = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token='.$ret['token'].'&agentid='.$params['agentid'];
	        $ret = Http::curlRequest($url);
	        $this->ajaxReturn('Agent name = '.$ret['name'], $ret['errmsg'], $ret['errcode']);
        }
        if ($type == 2){
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token='.$ret['token'].'&department_id=1&fetch_child=0';
			$ret = Http::curlRequest($url);
        	$this->ajaxReturn($ret['userlist'], $ret['errmsg'], $ret['errcode']);
        }
    }

    public function wx_send_msg($type = 'textcard', $params = null){
        $ret = $this->wx_get_token();
        if ($ret['status'] == 0){
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$ret['token'];
            $arr = array(
                'touser' => $params['touser'],
                // 'toparty' => '@all',
                // 'totag' => '@all',
                'agentid' => C('WX_AGENTID'),
                'msgtype' => $type,
            );
            if ($type == 'text'){
                $arr['text'] = array(
                    //其中text参数的content字段可以支持换行、以及A标签，即可打开自定义的网页（可参考以上示例代码）(注意：换行符请用转义过的\n)
                    //最大2048
                    'content' => $params['content']
                );
            }elseif ($type == 'file'){
                $arr['file'] = array(
                    'media_id' => $params['media_id']
                );
            }elseif ($type == 'textcard'){
                $arr['textcard'] = array(
                    'title' => $params['title'],
                    'description' => $params['content'],
                    'url' => $params['url']
                );
            }
            $ret = Http::curlRequest($url, 'post', $arr);
        }
        return $ret;
    }

    //$ret = $obj->wx_upload_media(array('media'=>'@'.$path.$tmpfile), 'file');
    public function wx_upload_media($params,$type){
        $ret = $this->wx_get_token();
        if ($ret['status'] == 0){
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token='.$ret['token'].'&type='.$type;
            $ret = Http::curlRequest($url, 'post', $params, true);
        }
        return $ret;
    }

    //添加成员
    public function wx_add_user($params){
        if (empty($params['userid'])) die('Userid is empty!');
        $ret = $this->wx_get_token('txl');
        if ($ret['status'] == 0){
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token='.$ret['token'];
            //userid 是成员在企业内的唯一标识，可以使用工号、手机号、邮箱等公司系统内统一的ID
            $ret = Http::curlRequest($url, 'post', $params);
        }
        return $ret;
    }

    //更新成员
    public function wx_update_user($params){
        if (empty($params['userid'])) die('Userid is empty!');
        $ret = $this->wx_get_token('txl');
        if ($ret['status'] == 0){
            $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token='.$ret['token'];
            $ret = Http::curlRequest($url, 'post', $params);
        }
        return $ret;
    }

    //删除成员 @param = (user id) 13417514264
    public function wx_delete_user($useridlist){
        if ($useridlist == '' || (is_array($useridlist) && count($useridlist) == 0)){
            die('Useridlist is empty!');
        }
        $ret = $this->wx_get_token('txl');
        if ($ret['status'] == 0){
            if (is_array($useridlist)){
                $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token='.$ret['token'];
                $ret = Http::curlRequest($url, 'post', array('useridlist'=>$useridlist));
            }else{
                $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token='.$ret['token'].'&userid='.$useridlist;
                $ret = Http::curlRequest($url);
            }
        }
        return $ret;
    }
}
?>