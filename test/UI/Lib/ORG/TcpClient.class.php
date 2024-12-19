<?php
class TcpClient {
	private $socket;
	private $port = 0;
	private $host = '';
	private $byte = '';
    private $header_length = 12;
    private $length = '';
    private $cmd = '';
    private $serial_number = '';
    private $data_body = '';
    private $content = '';

    /**
     * @param string  主机地址
     * @param integer 端口
     * @param string  项目名称
     * @param string  项目密码
     * @param integer 项目数据体长度
     */
	public function __construct($host = '', $port = 0, $project_name = '', $project_pwd = '', $project_data_len = 0){
		$this->host = $host;
		$this->port = $port;
        $this->serial_number = pack('H*','00000001');
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (!$this->socket){
            $errmsg = 'Create socket failed';
            IS_AJAX ? $this->ajaxReturn('', $errmsg, -1) : die($errmsg);
		}
		if (!socket_connect($this->socket,$this->host,$this->port)){
            $errmsg = sprintf('Unable to connect to the host, %s:%d', $this->host, $this->port);
            IS_AJAX ? $this->ajaxReturn('', $errmsg, -2) : die($errmsg);
        }
        $timeout = array('sec'=>3, 'usec'=>0);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);

        $this->project_name = $project_name;
        $this->project_pwd = $project_pwd;
        $this->project_data_len = $project_data_len;
	}

    public function __desctruct(){
        socket_close($this->socket);
    }

    private function my_pack(){
        //包头 + 长度 + 命令字 + 流水号 + 数据体
        $this->content = pack('H*','ABCD') . $this->length . $this->cmd . $this->serial_number . $this->data_body;
        file_put_contents('hex.txt', $this->content);
    }

    private function login(){
        //命令字，2字节
        $this->cmd = pack('H*','0001');

        //项目名称，32字节，不足在后面补0
        $tmp = pack('a*', $this->project_name);
        $this->data_body = $tmp . pack('H*', str_repeat('00',32-strlen($tmp))) ;

        //密码，32字节，不足在后面补0
        $tmp = pack('a*', $this->project_pwd);
        $this->data_body .= $tmp . pack('H*', str_repeat('00',32-strlen($tmp))) ;

        //是否写入客户端，1字节，默认为0
        $this->data_body .= pack('H*', '00');

        //保留区，3字节，全为0
        // $this->data_body .= pack('H3', 0x000000);
        $this->data_body .= pack('H*', '000000');

        //长度
        $this->length = pack('N', $this->header_length + 68);

        $this->my_pack();
        socket_write($this->socket, $this->content);

        //login_ack = AB CD 00 00 00 0D 80 01 00 00 00 01 00
        $ret = socket_read($this->socket, 13, PHP_BINARY_READ);
        if ($ret){
            $ret = unpack('H*',$ret);
            if (substr($ret[1],-2) == '00'){
                return 0;
            }
        }
        return -1;
    }

    /**
     * @param  string  sn
     * @param  integer 时间戳
     * @param  string
     * @return integer 返回0表示成功
     */
	public function write($sn = '', $ts = 0, $value = ''){
        if ($this->login() != 0) return;

		//命令字，2字节
        $this->cmd = pack('H*','0004');

        //sn，32字节，不足后面补0
		$tmp = pack('a*', $sn);
        $this->data_body = $tmp . pack('H*', str_repeat('00',32-strlen($tmp)));

        //上报时间，4字节时间戳。
        $tmp = pack('N*', $ts);
		$this->data_body .= $tmp;

        //数据，length = 项目长度，不足后面补0
		$tmp = pack('a*', $value);
		$this->data_body .= $tmp . pack('H*', str_repeat('00',$this->project_data_len-strlen($tmp))) ;

        //长度
        $this->length = pack('N', $this->header_length + 36 + $this->project_data_len);

        $this->my_pack();
        return socket_write($this->socket, $this->content);
	}

    /**
     * 读取一台设备一段时间内的数据
     * @param  string  sn
     * @param  integer 开始时间戳
     * @param  integer 结束时间戳
     * @return array
     */
	public function read($sn = '', $start_ts = 0, $end_ts = 0){
        if ($this->login() != 0) return;

		//命令字，2字节
        $this->cmd = pack('H*','0005');

		//sn，32字节，不足后面补0
		$tmp = pack('a*', $sn);
        $this->data_body = $tmp . pack('H*', str_repeat('00',32-strlen($tmp)));

		 //开始时间，4字节时间戳。
		$this->data_body .= pack('N', $start_ts);

        //结束时间，4字节时间戳。
		$this->data_body .= pack('N', $end_ts);

        //长度
        $this->length = pack('N', $this->header_length + 40);

        $this->my_pack();
        socket_write($this->socket, $this->content);

		return $this->get_read_ack();
	}

    /**
     * 获取一批设备的最新数据
     * @param  string 多个sn以逗号分隔
     * @return json
     */
    public function read_last($sns = ''){
        if ($this->login() != 0) return;

        $arr = explode(',', $sns);
        $total = count($arr);

        //命令字，2字节
        $this->cmd = pack('H*','0006');
        $this->data_body = '';

        //数据，32字节，不足后面补0
        foreach($arr as $k=>$v){
            $tmp = pack('a*', $v);
            $this->data_body .=  $tmp . pack('H*', str_repeat('00',32-strlen($tmp)));
        }

        //长度
        $this->length = pack('N', $this->header_length + $total*32);

        $this->my_pack();
        socket_write($this->socket, $this->content);

        return $this->get_read_ack();
    }

    /**
     * 1B signal + 4B flux + 1B net_mode + 1B sim_pos + 5B reserve
     * @return json
     */
    private function get_read_ack(){
        $ret = socket_read($this->socket, 13, PHP_BINARY_READ);
        if ($ret){
            $ret = unpack('H*',$ret);
            if (substr($ret[1],-2) == '00'){
                $len = hexdec(substr($ret[1],4,8));
                $total = ($len - 13) / (4 + $this->project_data_len);
                for ($i=0; $i<$total; $i++){
                    $ts = unpack('N*', socket_read($this->socket, 4, PHP_BINARY_READ));
                    /*$value = unpack('a*', socket_read($this->socket, $this->project_data_len, PHP_BINARY_READ));
                    $rs[] = array(
                        'ts' => $ts[1] == 0 ? '0000-00-00 00:00:00' : date('Y-m-d H:i:s', $ts[1]),
                        'value' => $ts[1] == 0 ? '' : $value[1]
                    );*/
                    $value = unpack('H*', socket_read($this->socket, $this->project_data_len, PHP_BINARY_READ));
                    $rs[] = array(
                        'id' => $i,
                        'net_mode'      => hexdec(substr($value[1], 10,2)),
                        'term_signal'   => hexdec(substr($value[1], 0,2)),
                        'flux'          => hexdec(htonl(substr($value[1], 2,8))),
                        'sim_pos'       => hexdec(substr($value[1], 12,2)),
                        'report_time'   => date('Y-m-d H:i:s', $ts[1])
                    );
                }
                return $rs;
            }
        }
        return array();
    }
}