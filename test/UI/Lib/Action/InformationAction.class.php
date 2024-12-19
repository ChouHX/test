<?php
class InformationAction extends CommonAction{
    // 平台概况
    public function ptgk() {
        $this->assign('web_path_final', sprintf('<li><a href="%s"><i class="fa fa-dashboard"></i> %s</a></li>', U('Information/ptgk'), L('VAR_PTGK')));
        $this->assign('web_path_1', array(L('VAR_PTGK')));
        $ugid = $this->getUgid();
        if ($ugid == 21 && is_file('./Tpl/Information/ptgk_21.html')) {
            $tgids = $this->getTgids();
            if (IS_AJAX) {
                $rs = M('rtu_data')->field('rtu_data.sn, rtu_data.value, rtu_data.addr')
                    ->join('LEFT JOIN term ON term.sn = rtu_data.sn')
                    ->where("term.group_id IN ($tgids) AND rtu_data.addr IN (1000,1001,1002,1003)")->select();
                die(json_encode(isset($rs) ? array_values($rs) : array()));
            }
            $rs = M('term_run_info')->field('term_run_info.sn, term.alias')
            ->join('LEFT JOIN term ON term.sn = term_run_info.sn')
            ->where("term.group_id IN ($tgids)")->order('term_run_info.last_time DESC')->select();
            $data_sets = M('rtu_data_set')->field('name, unit, min, max, addr')->where("addr IN (1000,1001,1002,1003)")->order('addr ASC')->select();
            $this->assign('rs', $rs);
            $this->assign('data_sets', $data_sets);
            $this->display('ptgk_21');
            exit;
        }
        $this->display('ptgk');
    }

    // 平台概况-统计信息
    public function ptgkStatisticalInfo() {
        session_write_close();
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $ugid = $_SESSION[C('SESSION_NAME')]['gid'];
        $data_swv = $data_netmode = $data_task = array();
        $today = date('Y-m-d 00:00:00');
        $device_num = 0;
        $online_num = 0;
        $today_task = 0;
        $day_flux = 0;
        $month_flux = 0;

        $q  = $uid == 1 ? 'group_id != 0' : sprintf("group_id IN(%s)", M('usr_group_privilege')->order('tgid ASC')->where("ugid = $ugid")->getField('tgid', true, true));
        $q2 = ($uid == 1 ? 'ugid != 0' : "ugid = $ugid")." AND create_time >= '$today'";
        $rs = M('term')->query("SELECT A.sn, A.sw_version, C.is_online, C.last_time, C.day_flux, C.month_flux, C.net_mode FROM term A
            LEFT JOIN term_group B ON B.id = A.group_id
            LEFT JOIN term_run_info C ON C.sn = A.sn WHERE A.$q"
        );

        $ts = time();
        $nm = C('NET_MODE');
        foreach ($rs as $key => $row) {
            $device_num += 1;
            $s = get_term_status_code($ts - strtotime($row['last_time']), $row['is_online']);
            if ($s == '1') {
                $online_num += 1;
            }

            $day_flux += $row['day_flux'];
            $month_flux += $row['month_flux'];

            $sw_key = empty($row['sw_version']) ? L('VAR_UNKNOWN') : $row['sw_version'];
            if (!isset($data_swv[$sw_key])){
                $data_swv[$sw_key] = array($sw_key, 0);
            }
            $data_swv[$sw_key][1] += 1;

            if (!isset($data_netmode[$row['net_mode']])){
                $data_netmode[$row['net_mode']] = array($nm[$row['net_mode']], 0);
            }
            $data_netmode[$row['net_mode']][1] += 1;
        }

        // 充电桩2个图表的数据
        $chart_station_model = array();
        $chart_charge_state = array();
        if (C('SHOW_CDZ')) {
            $rs1 = M('')->query('SELECT COUNT(*)num, station_model FROM oem_charge_station GROUP BY station_model ORDER BY num DESC');
            if ($rs1 && count($rs1) != 0) {
                foreach ($rs1 as $key => $row) {
                    array_push($chart_station_model, array('name' => $row['station_model'], 'y' => intval($row['num']), 'sliced' => $key === 0, 'selected' => $key === 0));
                }
            }
            // $cs_arr = L('CHARGE_STATE');
            $rs2 = M('')->query('SELECT COUNT(*)num, charge_state FROM oem_charge_station_port GROUP BY charge_state ORDER BY num DESC');
            if ($rs2 && count($rs2) != 0) {
                foreach ($rs2 as $key => $row) {
                    array_push($chart_charge_state, array($row['charge_state'], intval($row['num'])));
                }
            }
        }

        // 任务统计
        $rs = M('')->query("SELECT COUNT(*)num, term_task_detail.status FROM term_task_detail
            INNER JOIN term_task ON term_task.id = term_task_detail.task_id AND term_task.ugid = $ugid GROUP BY term_task_detail.status");
        $st = L('VAR_TASK_STATUS_ARR');
        $st_clrs =  array("0"=>"#ECF0F5", "1"=>"#d0d1ef", "2"=>"#999EFF", "3"=>"#7CB5EC", "4"=>"#F15C80", "5"=>"#c8ced6", "6"=>"#F39C12", "7"=>"#929498", "8"=>"#434348");
        foreach ($rs as $row) {
            $data_task[] = array('name' => $st[$row['status']], 'y' => intval($row['num']), 'color' => $st_clrs[$row['status']]);
        }

        $ret = array(
            'statics_info' => array(
                'info_box_online' => sprintf('%d/%d', $online_num, $device_num),
                'info_box_task' => M('term_task')->where($q2)->count() + M('timed_term_task')->where($q2)->count(),
                'info_box_flux' => bitsize($day_flux),
                'info_box_month_flux' => bitsize($month_flux),
            ),
            'chart_swv' => array_values($data_swv),
            'chart_netmode' => array_values($data_netmode),
            'chart_task' => $data_task,
            'chart_station_model' => $chart_station_model,
            'chart_charge_state' => $chart_charge_state
        );
        echo json_encode($ret);
    }

    // 上线趋势，流量趋势，每日新增设备
    public function loadDashboardData() {
        session_write_close();
        $uid = $_SESSION[C('SESSION_NAME')]['id'];
        $ugid = $_SESSION[C('SESSION_NAME')]['gid'];
        $ret = array(
            'chart_online' => array(),
            'chart_flux' => array(),
            'chart_new' => array()
        );
        try {
            $m = M('term');
            $datay1 = array();
            $datay2 = array();
            $datay3 = array();

            $categories = array();
            $now = strtotime(I('start_date',date('Y-m-d'),'string'));
            $flux_x = array();
            $flux_y = array();

            for ($i=1; $i<=7; $i++) {
                $tmpdate = $now - 24*3600*$i;
                array_push($datay, 0);

                array_unshift($categories, date('M d', $tmpdate));
                array_unshift($flux_x, date('Ymd', $tmpdate));
            }
            $len = count($flux_x);

            $q = $uid == 1 ? 'group_id != 0' : sprintf("group_id IN(%s)", M('usr_group_privilege')->order('tgid ASC')->where("ugid = $ugid")->getField('tgid', true, true));
            $sns = M('term')->where($q)->getField('sn', true);

            $exists_sns = array();
            $rs = $m->query("SELECT sn, report_day, flux FROM term_stat_info WHERE report_day BETWEEN {$flux_x[0]} AND {$flux_x[$len-1]}");
            foreach ($rs as $row) {
                if (!in_array($row['sn'], $sns, true)) {
                    continue;
                }
                $tmp_key = array_search($row['report_day'], $flux_x);
                // 最近7天上线
                if (!$exists_sns[$row['sn'].$row['report_day']]) {
                    $exists_sns[$row['sn'].$row['report_day']] = 1;
                    $datay1[$tmp_key] += 1;
                }
                // 最近7天流量统计
                $datay2[$tmp_key] += $row['flux'];
            }
            $max = count($datay2) > 0 ? max($datay2) : 0;
            $divisor = 1024;
            $flux_unit = 'KB';
            if ($max >= 1024 * 1024 * 1024) {
                $divisor = 1024 * 1024 * 1024;
                $flux_unit = 'GB';
            } elseif ($max >= 1024 * 1024) {
                $divisor = 1024 * 1024;
                $flux_unit = 'MB';
            }
            $ret['flux_unit'] = $flux_unit;
            foreach ($datay2 as $k => $v) {
                $datay2[$k] = floatval(round($v/$divisor, 2));
            }

            // 最近7天新增
            $rs = $m->query("SELECT term.sn, DATE_FORMAT(first_login,'%Y%m%d')ymd FROM term
                JOIN term_run_info ON term_run_info.sn = term.sn
                WHERE first_login IS NOT NULL AND first_login != '0000-00-00 00:00:00'
                HAVING ymd BETWEEN '{$flux_x[0]}' AND '{$flux_x[$len-1]}'");
            foreach ($rs as $row) {
                if (!in_array($row['sn'], $sns, true)) continue;
                $datay3[array_search($row['ymd'], $flux_x)] += 1;
            }

            foreach ($categories as $k => $v) {
                $ret['chart_online'][] = array($v, isset($datay1[$k]) ? $datay1[$k] : 0);
                $ret['chart_flux'][] = array($v, isset($datay2[$k]) ? $datay2[$k] : 0);
                $ret['chart_new'][] = array($v, isset($datay3[$k]) ? $datay3[$k] : 0);
            }
        } catch (ThinkException $e) {
            ;
        }
        echo json_encode($ret);
    }
}