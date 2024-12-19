<?php
class HemodialysisAction extends CommonAction{
    private $p = null;
    public function _initialize(){
        session_write_close();
        $str = file_get_contents("php://input");
        $p = json_decode($str, true);
        if (!$p) {
            die(json_encode(array('status' => -1, 'info' => 'JSON解析失败', 'data' => '')));
        }
        $this->p = $p;
        $sn = trim($this->p['sn']);
        if (empty($sn) || M('term')->where("sn = '%s'", $sn)->count() == 0) {
            die(json_encode(array('status' => -2, 'info' => 'SN对应的设备不存在', 'data' => '')));
        }
    }

    // 上报数据
    public function push() {
        $m = M('oem_hemodialysis_machine_data');
        $sn = trim($this->p['sn']);
        $data = $this->p['data'];
        $d = array(
            'sn' => $sn,
            'data' => $data,
        );
        if ($m->add($d)){
            die(json_encode(array('status' => 0, 'info' => L('OPERATION_SUCCESS'), 'data' => '')));
        }else{
            die(json_encode(array('status' => -1, 'info' => L('VAR_CMD_SEND_FAILED'), 'data' => '')));
        }

    }

    // 获取最新一条透析数据
    public function latestRecord() {
        $m = M('oem_hemodialysis_machine_data');
        $sn = trim($this->p['sn']);
        $row = $m->where("sn = '$sn'")->order('report_time DESC')->find();
        die(json_encode(array('status' => 0, 'info' => L('OPERATION_SUCCESS'), 'data' => $row['data'])));
    }

    // 获取时间段内的透析数据
    public function records() {
        $m = M('oem_hemodialysis_machine_data');
        $sn = trim($this->p['sn']);
        $startTime = $this->p['startTime'];
        $endTime = $this->p['endTime'];
        $rs = $m->where("sn = '$sn' AND report_time BETWEEN '$startTime' AND '$endTime'")->order('report_time DESC')->select();
        $data_arr = array();
        foreach ($rs as $row) {
            array_push($data_arr, $row['data']);
        }
        die(json_encode(array('status' => 0, 'info' => L('OPERATION_SUCCESS'), 'data' => $data_arr)));
    }
}