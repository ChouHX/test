<?php
class jasper{
    private $user = '';
    private $pass = '';
    private $licenseKey = '';
    private $JASPER_URI = '';
    private $client = null;
    
    public function jasper(){
        $this->user = C('JASPER_USERNAME');
        $this->pass = C('JASPER_PASSWORD');
        $this->licenseKey = C('JASPER_LICENSEKEY');
        Vendor('jasper.nusoap');
        $this->JASPER_URI = 'http://api.jasperwireless.com/ws/schema';
		$env = 'api'; # Apitest URL. See "Get WSDL Files" in the API documentation for Production URL.
		$wsdlUrl = 'https://'.$env.'.10646.cn/ws/schema/Terminal.wsdl';	
		$this->client = new nusoap_client($wsdlUrl,true);
    }

    public function set_header(){
		$this->client->setHeaders(
            '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">'.
            '<wsse:UsernameToken>'.
            '<wsse:Username>'.$this->user.'</wsse:Username>'.
            '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->pass.'</wsse:Password>'.
            '</wsse:UsernameToken>'.
            '</wsse:Security>'
		);
    }
    
	//获取流量
	public function get_flux_usage($iccid){
        $this->set_header();
		$msg =
            '<GetTerminalDetailsRequest xmlns="'.$this->JASPER_URI.'">'.
            '<messageId></messageId>'.
            '<version></version>'.
            '<licenseKey>'.$this->licenseKey.'</licenseKey>'.
            '<iccids>';
        if (is_array($iccid)){
            foreach ($iccid as $k=>$v){
                $msg .= '<iccid>'.$v.'</iccid>';
            }
        }else{
            $msg .= '<iccid>'.$iccid.'</iccid>';
        }
        $msg .= '</iccids></GetTerminalDetailsRequest>';
		return $this->client->call('GetTerminalDetails',$msg);
	}
	
	//获取套餐
	public function get_rate_plan($iccid){
        $this->set_header();
		$msg =
            '<GetTerminalRatingRequest xmlns="'.$this->JASPER_URI.'">'.
            '<messageId></messageId>'.
            '<version></version>'.
            '<licenseKey>'.$this->licenseKey.'</licenseKey>'.
            '<iccid>'.$iccid.'</iccid>'.
            '</GetTerminalRatingRequest>';
		return $this->client->call('GetTerminalRating',$msg);
		// $ret = $ret['terminalRatings']['terminalRating'];
	}
	
}
?>