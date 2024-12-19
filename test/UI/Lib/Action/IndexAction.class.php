<?php
class IndexAction extends CommonAction{
    private $termTotal = null;

    public function index(){
        $this->display('index_new'.(C('IS_WLINK') ? '_wlink' : ''));
        // $this->display('index' . ($this->ui_version == 'RTU' ? '_rtu':''));
    }

    public function checkLogin(){
		$name = I('na','','string');
		$password = I('pa','','string');
        $remember = I('remember','','string');
        $login_type = I('login_type','','string');

		try {
			$m = M('usr');
		} catch (ThinkException $e) {
            $this->error($e->getMessage(), U('Index/index'));
		}

        if (!$m->autoCheckToken($_POST)){
            // $this->error(L('VAR_TOKEN_TIMEOUT'), U('Index/index'));
        }

        $row = $m->where("name='%s' AND password='%s'",array($name,md5($password)))->find();
        if ($row){
            if ($row['is_enable'] != 1){
                $this->error(L('ACCOUNT_DISABLED'), U('Index/index'));
            }
            if ($row['never_expired'] != 1 && $row['expired_time'] <= date('Y-m-d H:i:s')){
                $this->error(L('ACCOUNT_EXPIRED'), U('Index/index'));
            }

            $row['member_since'] = date('Y.m.d',strtotime($row['create_time']));
            $row['login_type'] = $login_type;
            $_SESSION[C('SESSION_NAME')] = $row;

            if ($remember == 'on'){
                cookie(C('SESSION_NAME'), base64_encode($row['name'].'|###|'.$row['password'].'|###|'.$row['login_type']), 365*24*3600);
            }

            if ($login_type == 'fzjh'){
                $url = U('Syscfg/fwq');
			} else {
				$url = U(C('LOGIN_JUMP_PAGE') ? C('LOGIN_JUMP_PAGE') : 'Information/ptgk');
            }

            //login record
            $ip = get_client_ip();
            $ret = get_position_by_ip($ip);
            M('usr_login_record')->add(array(
                'usr_id' => $row['id'],
                'ip' => $ip,
                'country' => $ret['country'],
                'province' => $ret['province'],
                'city' => $ret['city'],
            ));
            // $this->success(L('VAR_LOGIN_SUCCESS'), $url);
            header('Location:'.$url);
        }else{
            $this->error(L('VAR_LOGIN_ERROR'), U('Index/index'));
        }
    }

    public function logout(){
		session(C('SESSION_NAME'), null);
		cookie(C('SESSION_NAME'),null);
        header('Location:'.U('Index/index'));
    }

    public function changeLang(){
        cookie('think_language', I('lang'));
    }
}