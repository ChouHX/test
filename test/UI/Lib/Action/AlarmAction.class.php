<?php
class AlarmAction extends CommonAction{
    private $tgids_arr = array();
    // 检查告警频率，是否能继续生成告警
    // $interval 单位分钟
    protected function checkContinueAlarm($receiver_name, $alarm_type, $interval) {
        $last_ts = F('Alarm_create_ts_'.$receiver_name.'_'.$alarm_type, '', './Runtime/');
        if (!$last_ts) return true;
        $interval *= 60;
        return time() - $last_ts > $interval ? true : false;
    }

    //筛选需要告警设备 //public
    private function getAlarmTgids($uid) {
        $m = M('usr');
        $row = $m->where('id = %d', $uid)->field('recv_alarm_cfg')->find();
        if ($row['recv_alarm_cfg']) {
            $groups2 = explode(",", json_decode($row['recv_alarm_cfg'], true)['alarm_groups']);
        }
        $groups1 = explode(",", $this->getTgids('string', $uid));
        return implode(",", array_intersect($groups1, $groups2));
    }

    // 离线告警
    protected function offlineAlarm() {
        $alarm_type = 0;
        $datas = array();
        $ymdhis = date('Y-m-d H:i:s');
        foreach ($this->AUSERS as $key => $user) {
            if (!$this->checkContinueAlarm($user['name'], $alarm_type, $user['cfg']['alarm_interval_offline'])) {
                continue;
            }
            $str_wx = array();
            $str_email = array();
            $tm = date('Y-m-d H:i:s', time() - C('TERM_OFFLINE_TIME'));
            if (!isset($this->tgids_arr[$user['id']])) {
                $this->tgids_arr[$user['id']] = $this->getAlarmTgids($user['id']);
            }
            $total = M('term')->where('term_type = 0 AND group_id IN(%s)', $this->tgids_arr[$user['id']])->count(); // 0 : 普通设备，1：lora设备
            if (C('OEM_VERSION') == 'rx-m2m') {
                $q = sprintf("term_type = 0 AND (is_online = 0 OR last_time < '%s' OR last_time_sim1 < '%s' OR last_time_sim2 < '%s') AND group_id IN(%s)", $tm, $tm, $tm, $this->tgids_arr[$user['id']]);
            } else {
                $q = sprintf("term_type = 0 AND (is_online = 0 OR last_time < '%s') AND group_id IN(%s)", $tm, $this->tgids_arr[$user['id']]);
            }
            $rs = M('term_run_info')
                ->join('INNER JOIN term ON term.sn = term_run_info.sn')
                ->where($q)
                ->order('term_run_info.last_time ASC')
                ->field('term_run_info.sn, term_run_info.last_time, term_run_info.last_time_sim1, term_run_info.last_time_sim2, term.ud_sn, term_run_info.is_online, term.alias')
                ->select();
            if (!$rs) {
                $rs = array();
            }
            if ($user['cfg']['alarm_term_offline_num'] == '1' && count($rs) >= intval($user['cfg']['alarm_term_offline_num_threshold'])) {
                if (C('OEM_VERSION') == 'rx-m2m') {
                    // WLINK-RX客户定制告警内容
                    $arr = array();
                    foreach ($rs as $row) {
                        $s1 = $row['is_online'] == 1 && $row['last_time_sim1'] >= $tm ? 1 : 0;
                        $s2 = $row['is_online'] == 1 && $row['last_time_sim2'] >= $tm ? 1 : 0;
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                            $row['ud_sn'],
                            $row['sn'],
                            L($s1 == 1 ? 'VAR_TERM_STATUS_ONLINE' : 'VAR_TERM_STATUS_OFFLINE'),
                            L($s2 == 1 ? 'VAR_TERM_STATUS_ONLINE' : 'VAR_TERM_STATUS_OFFLINE'),
                            $s1 == 1 ? 0 : format_time($row['last_time_sim1'], $ymdhis),
                            $s2 == 1 ? 0 : format_time($row['last_time_sim2'], $ymdhis)
                        ));
                    }
                    $str_email[] = sprintf('<table border=1 style="text-align: center;"><tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th>
                        <th style="text-align:center;">SIM1 %s</th><th style="text-align:center;">SIM2 %s</th><th style="text-align:center;">SIM1 %s</th><th style="text-align:center;">SIM2 %s</th></tr>',
                        L('VAR_SN1'), L('VAR_SN2'), L('VAR_TERM_STATUS'), L('VAR_TERM_STATUS'), L('VAR_LOGOUT_TIME'), L('VAR_LOGOUT_TIME')).implode('', $arr).'</table>';
                } else {
                    $str_wx[] = sprintf(L('OFFLINE_NUM_ALARM_TEXT'), count($rs));
                    $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="3" style="text-align: center;">%s</th></tr>',
                        sprintf(L('OFFLINE_NUM_ALARM_TEXT'), count($rs))
                    );
                    $arr = array();
                    foreach ($rs as $row) {
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias']));
                    }
                    $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                        L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS')) . implode('', $arr) . '</table>';
                }
            }

            if ($user['cfg']['alarm_term_offline_percent'] == '1' && $total != 0) {
                $percent = count($rs)/$total*100;
                $pt = floatval($user['cfg']['alarm_term_offline_percent_threshold']);
                if ($percent >= $pt) {
                    $str_wx[] = sprintf(L('OFFLINE_PERCENT_ALARM_TEXT'), round($percent,2));
                    $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="3" style="text-align: center;">%s</th></tr>',
                        sprintf(L('OFFLINE_PERCENT_ALARM_TEXT'), round($percent, 2))
                    );
                    $arr = array();
                    foreach ($rs as $row) {
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
                            $row['sn'],
                            $row['ud_sn'],
                            $row['alias']
                        ));
                    }
                    $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                        L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS')) . implode('', $arr) . '</table>';
                }
            }

            if ($user['cfg']['alarm_term_offline_time'] == '1') {
                $num = 0;
                $tm2 = date('Y-m-d H:i:s', time() - $user['cfg']['alarm_term_offline_time_threshold']*60);
                foreach ($rs as $key => $row) {
                    if ($row['last_time'] < $tm2) {
                        $num += 1;
                    }
                }
                if ($num > 0) {
                    $str_wx[] = sprintf(L('OFFLINE_TIME_ALARM_TEXT'), $num, $user['cfg']['alarm_term_offline_time_threshold']);
                    $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="4" style="text-align: center;">%s</th></tr>',
                        sprintf(L('OFFLINE_TIME_ALARM_TEXT'), $num, $user['cfg']['alarm_term_offline_time_threshold'])
                    );
                    $arr = array();
                    foreach ($rs as $row) {
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias'], format_time($row['last_time'], $ymdhis)));
                    }
                    $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                        L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_LOGOUT_RECORD')) . implode('', $arr) . '</table>';
                }
            }
            if (count($str_wx) == 0 && count($str_email) == 0)  {
                continue;
            }
            $email_content = implode('<br>', $str_email);
            $wx_content = implode('<br>', $str_wx);
            array_push($datas, array(
                'alarm_type'        => $alarm_type,
                'receiver_name'     => $user['name'],
                'email'             => $user['email'] ? $user['email'] : null,
                'email_content'     => $user['email'] ? $email_content : null,
                'wx'                => $user['wx'] ? $user['wx'] : null,
                'wx_content'        => $user['wx'] ? $wx_content : null
            ));
            F('Alarm_create_ts_'.$user['name'].'_'.$alarm_type, time(), './Runtime/');
        }
        M('term_alarm_record')->addAll($datas);
    }

    // VPN离线告警
    protected function vpnAlarm() {
        $alarm_type = 3;
        $datas = array();
        foreach ($this->AUSERS as $key => $user) {
            if (!$this->checkContinueAlarm($user['name'], $alarm_type, $user['cfg']['alarm_interval_vpn']) || $user['cfg']['alarm_vpn_offline_time'] != '1') {
                continue;
            }
            $str_wx = array();
            $str_email = array();
            if (!isset($this->tgids_arr[$user['id']])) {
                $this->tgids_arr[$user['id']] = $this->getAlarmTgids($user['id']);
            }
            $rs = M('term_virtual_channel')
                ->join('INNER JOIN term ON term.sn = term_virtual_channel.sn')
                ->where("LOWER(vpn_type) != 'n2n' AND last_time < '%s' AND group_id IN(%s)", array(date('Y-m-d H:i:s', time() - $user['cfg']['alarm_vpn_offline_time_threshold']*60), $this->tgids_arr[$user['id']]))
                ->field('DISTINCT(term_virtual_channel.sn), term.ud_sn, term.alias')
                ->select();
            if ($rs) {
                $str_wx[] = sprintf(L('VPN_OFFLINE_ALARM_TEXT'), count($rs));
                $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="3" style="text-align: center;">%s</th></tr>',
                    sprintf(L('VPN_OFFLINE_ALARM_TEXT'), count($rs))
                );
                $arr = array();
                foreach ($rs as $row) {
                    array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias']));
                }
                $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                    L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS')) . implode('', $arr) . '</table>';
            }
            if (count($str_wx) == 0 && count($str_email) == 0)  {
                continue;
            }
            $email_content = implode('<br>', $str_email);
            $wx_content = implode('<br>', $str_wx);
            array_push($datas, array(
                'alarm_type'        => $alarm_type,
                'receiver_name'     => $user['name'],
                'email'             => $user['email'] ? $user['email'] : null,
                'email_content'     => $user['email'] ? $email_content : null,
                'wx'                => $user['wx'] ? $user['wx'] : null,
                'wx_content'        => $user['wx'] ? $wx_content : null
            ));
            F('Alarm_create_ts_'.$user['name'].'_'.$alarm_type, time(), './Runtime/');
        }
        M('term_alarm_record')->addAll($datas);
    }

    // 信号强度告警，只看在线设备
    protected function signalAlarm() {
        $alarm_type = 1;
        $datas = array();
        foreach ($this->AUSERS as $key => $user) {
            if (!$this->checkContinueAlarm($user['name'], $alarm_type, $user['cfg']['alarm_interval_signal']) || $user['cfg']['alarm_term_signal'] != '1') {
                continue;
            }
            $str_wx = array();
            $str_email = array();
            if (!isset($this->tgids_arr[$user['id']])) {
                $this->tgids_arr[$user['id']] = $this->getAlarmTgids($user['id']);
            }
            $tm = date('Y-m-d H:i:s', time() - C('TERM_OFFLINE_TIME'));
            $rs = M('term_run_info')
                ->join('INNER JOIN term ON term.sn = term_run_info.sn')
                ->where("term_type = 0 AND term_signal < %d AND is_online = 1 AND last_time >= '%s' AND group_id IN(%s)", array($user['cfg']['alarm_term_signal_threshold'], $tm, $this->tgids_arr[$user['id']]))
                ->field('term_run_info.sn, term.ud_sn, term.alias, term_signal')
                ->select();
            $num = $rs ? count($rs) : 0;
            if ($num > 0) {
                $str_wx[] = sprintf(L('SIGNAL_ALARM_TEXT'), $num, $user['cfg']['alarm_term_signal_threshold']);
                $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="4" style="text-align: center;">%s</th></tr>',
                    sprintf(L('SIGNAL_ALARM_TEXT'), $num, $user['cfg']['alarm_term_signal_threshold'])
                );
                $arr = array();
                foreach ($rs as $row) {
                    array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias'], $row['term_signal']));
                }
                $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                    L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_TERM_SIGNAL')) . implode('', $arr) . '</table>';
            }
            if (count($str_wx) == 0 && count($str_email) == 0)  {
                continue;
            }
            $email_content = implode('<br>', $str_email);
            $wx_content = implode('<br>', $str_wx);
            array_push($datas, array(
                'alarm_type'        => $alarm_type,
                'receiver_name'     => $user['name'],
                'email'             => $user['email'] ? $user['email'] : null,
                'email_content'     => $user['email'] ? $email_content : null,
                'wx'                => $user['wx'] ? $user['wx'] : null,
                'wx_content'        => $user['wx'] ? $wx_content : null
            ));
            F('Alarm_create_ts_'.$user['name'].'_'.$alarm_type, time(), './Runtime/');
        }
        M('term_alarm_record')->addAll($datas);
    }

    // 流量告警
    protected function fluxAlarm() {
        $alarm_type = 2;
        $datas = array();
        foreach ($this->AUSERS as $key => $user) {
            if (!$this->checkContinueAlarm($user['name'], $alarm_type, $user['cfg']['alarm_interval_flux'])) {
                continue;
            }
            $str_wx = array();
            $str_email = array();
            if (!isset($this->tgids_arr[$user['id']])) {
                $this->tgids_arr[$user['id']] = $this->getAlarmTgids($user['id']);
            }
            if ($user['cfg']['alarm_term_flux_month'] == '1') {
                $rs = M('term_run_info')
                    ->join('INNER JOIN term ON term.sn = term_run_info.sn')
                    ->where('term_type = 0 AND month_flux >= %d AND group_id IN(%s)', array($user['cfg']['alarm_term_flux_month_threshold']*1024*1024, $this->tgids_arr[$user['id']]))
                    ->field('term_run_info.sn, term.ud_sn, term.alias, month_flux')
                    ->select();
                $num = $rs ? count($rs) : 0;
                if ($num > 0) {
                    $str_wx[] = sprintf(L('MONTH_FLUX_ALARM_TEXT'), $num, $user['cfg']['alarm_term_flux_month_threshold']);
                    $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="4" style="text-align: center;">%s</th></tr>',
                        sprintf(L('MONTH_FLUX_ALARM_TEXT'), $num, $user['cfg']['alarm_term_flux_month_threshold'])
                    );
                    $arr = array();
                    foreach ($rs as $row) {
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias'], bitsize($row['month_flux'])));
                    }
                    $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                        L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('FLUX_CURRENT_MONTH')) . implode('', $arr) . '</table>';
                }
            }

            if ($user['cfg']['alarm_term_flux_day'] == '1') {
                $rs2 = M('term_run_info')
                    ->join('INNER JOIN term ON term.sn = term_run_info.sn')
                    ->where('term_type = 0 AND day_flux >= %d AND group_id IN(%s)', array($user['cfg']['alarm_term_flux_day_threshold']*1024*1024, $this->tgids_arr[$user['id']]))
                    ->field('term_run_info.sn, term.ud_sn, term.alias, day_flux')
                    ->select();
                $num2 = $rs2 ? count($rs2) : 0;
                if ($num2 > 0) {
                    $str_wx[] = sprintf(L('DAY_FLUX_ALARM_TEXT'), $num2, $user['cfg']['alarm_term_flux_day_threshold']);
                    $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="4" style="text-align: center;">%s</th></tr>',
                        sprintf(L('DAY_FLUX_ALARM_TEXT'), $num2, $user['cfg']['alarm_term_flux_day_threshold'])
                    );
                    $arr = array();
                    foreach ($rs2 as $row) {
                        array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $row['sn'], $row['ud_sn'], $row['alias'], bitsize($row['day_flux'])));
                    }
                    $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                        L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('TODAY_FLUX')) . implode('', $arr) . '</table>';
                }
            }

            if ($user['cfg']['alarm_term_flux_pool'] == '1') {
                $pool_flux = M('term_run_info')->join('INNER JOIN term ON term.sn = term_run_info.sn')->where('group_id IN(%s)', $this->tgids_arr[$user['id']])->sum('month_flux');
                if ($pool_flux >= $user['cfg']['alarm_term_flux_pool_threshold']*1024*1024) {
                    $str_wx[] = sprintf(L('POOL_FLUX_ALARM_TEXT'), bitsize($pool_flux));
                    $str_email[] = sprintf(L('POOL_FLUX_ALARM_TEXT'), bitsize($pool_flux));
                }
            }
            if (count($str_wx) == 0 && count($str_email) == 0)  {
                continue;
            }
            $email_content = implode('<br>', $str_email);
            $wx_content = implode('<br>', $str_wx);
            array_push($datas, array(
                'alarm_type'        => $alarm_type,
                'receiver_name'     => $user['name'],
                'email'             => $user['email'] ? $user['email'] : null,
                'email_content'     => $user['email'] ? $email_content : null,
                'wx'                => $user['wx'] ? $user['wx'] : null,
                'wx_content'        => $user['wx'] ? $wx_content : null
            ));
            F('Alarm_create_ts_'.$user['name'].'_'.$alarm_type, time(), './Runtime/');
        }
        M('term_alarm_record')->addAll($datas);
    }

    // 出围栏告警
    protected function fenceAlarm() {
        $alarm_type = 5;
        $datas = array();
        foreach ($this->AUSERS as $key => $user) {
            if (!$this->checkContinueAlarm($user['name'], $alarm_type, $user['cfg']['alarm_interval_fence']) || $user['cfg']['alarm_fence'] != '1') {
                continue;
            }
            $str_wx = array();
            $str_email = array();
            if (!isset($this->tgids_arr[$user['id']])) {
                $this->tgids_arr[$user['id']] = $this->getAlarmTgids($user['id']);
            }
            $last_id = F('fence_record_last_id_'.$user['id'], '', './Lib/');
            if (!$last_id) {
                $last_id = 0;
            }
            $rs = M('term_electronic_fence_record')
                ->join('INNER JOIN term ON term.sn = term_electronic_fence_record.sn')
                ->field('term_electronic_fence_record.*, term.sn, term.ud_sn, term.alias')
                ->where('term_electronic_fence_record.id > %d AND act = 1 AND group_id IN(%s)', array($last_id, $this->tgids_arr[$user['id']]))->order('term_electronic_fence_record.id ASC')->select();
            if (!$rs) {
                $rs = array();
            }
            foreach ($rs as $key => $row) {
                $this->lnglatFormat($row['report_longitude'], $row['report_latitude']);
                $str_wx[] = sprintf(L('FENCE_ALARM_TEXT'), $row['sn'], $row['report_longitude'].','.$row['report_latitude'], $row['report_time']);
                $str_email[] = sprintf('<table border=1 style="text-align: center;width: 100%%"><tr><th colspan="5" style="text-align: center;">%s</th></tr>', L('FENCING_ALARM'));
                $arr = array();
                array_push($arr, sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                    $row['sn'],
                    $row['ud_sn'],
                    $row['alias'],
                    $row['report_longitude'],
                    $row['report_latitude']
                ));
                $str_email[] = sprintf('<tr><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th><th style="text-align:center;">%s</th></tr>',
                    L('VAR_SN2'), L('VAR_SN1'), L('VAR_SYSCFG_ALIAS'), L('VAR_GPS_LNG'), L('VAR_GPS_LAT')) . implode('', $arr) . '</table>';
            }
            if (count($str_wx) == 0 && count($str_email) == 0)  {
                continue;
            } else {
                F('fence_record_last_id_'.$user['id'], $rs[count($rs)-1]['id'], './Lib/');
            }
            $email_content = implode('<br>', $str_email);
            $wx_content = implode('<br>', $str_wx);
            array_push($datas, array(
                'alarm_type'        => $alarm_type,
                'receiver_name'     => $user['name'],
                'email'             => $user['email'] ? $user['email'] : null,
                'email_content'     => $user['email'] ? $email_content : null,
                'wx'                => $user['wx'] ? $user['wx'] : null,
                'wx_content'        => $user['wx'] ? $wx_content : null
            ));
            F('Alarm_create_ts_'.$user['name'].'_'.$alarm_type, time(), './Runtime/');
        }
        M('term_alarm_record')->addAll($datas);
    }

    // 数据推送，只推送最新数据
    // 计划任务调用，间隔1分钟
    public function dataPush() {
        import("ORG.Net.Http");
        import('@.ORG.Mlog');
        $log = new Mlog('./Log/', 'Gzbc_data_push');
        $api_token_url = 'http://yun.api.gznw-iot.com/boss-auth-server/auth/oauth/token';
        $api_data_url = 'http://yun.api.gznw-iot.com/hms-server/v2/record/data/accept';
        $records = array();

        $last_push_times = F('last_push_times', '', './Lib/ORG/');
        if (!$last_push_times) {
            $last_push_times = array();
        }

        // Get token
        $bc_token = S('bc_token');
        if (!$bc_token) {
            $ret = Http::post2($api_token_url, 'form', array(
                    'Authorization' => 'Basic Ym9zc190ZW1wOmJvc3NfdGVtcA==',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ), array(
                    'grant_type' => 'password',
                    'scope' => 'boss_app',
                    'username' => 'gzhmjcsjjr',
                    'password' => '12345678'
                )
            );
            if ($ret['http_code'] == 200 && is_array($ret['res']) && !empty($ret['res']['access_token'])) {
                $bc_token = $ret['res']['access_token'];
                S('bc_token', $bc_token, $ret['res']['expires_in'] - 1);
                $log->mwrite(sprintf("%s [GET_TOKEN] request success：token = %s\r\n", date('Y-m-d H:i:s'), $bc_token));
            } else {
                $log->mwrite(sprintf("%s [GET_TOKEN] request failed：http_code = %d, ret = %s\r\n", date('Y-m-d H:i:s'), $ret['http_code'], json_encode($ret['res'])));
            }
        }
        // 监测点配置
        // 流量C = 2010，瞬时流量A = 2004，流速A = 2001，浊度 = 2000，水分 = 2009，超声液位A = 2003
        // 瞬时流量 = flux， 瞬时流速 = flowRate，液位 = level，浑浊度 = td
        // 04号设备因为没有数据，暂时未配置上报，还有最后一个新设备没有上报
        $cfg = array(
            '01' => array('rtu_code' => 'GZBYJC12', 'addrs' => array('2010' => 'flux', '2009' => 'hum', '2000' => 'td')),
            '02' => array('rtu_code' => 'GZBYJC02', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '03' => array('rtu_code' => 'GZBYJC03', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '09' => array('rtu_code' => 'GZBYJC09', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '13' => array('rtu_code' => 'GZBYJC10', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '20' => array('rtu_code' => 'GZBYJC05', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '23' => array('rtu_code' => 'GZBYJC11', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '27' => array('rtu_code' => 'GZBYJC06', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '29' => array('rtu_code' => 'GZBYJC08', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td')),
            '93' => array('rtu_code' => 'GZBYJC07', 'addrs' => array('2001' => 'flowRate', '2004' => 'flux', '2003' => 'level', '2000' => 'td', '2009' => 'hum')),
        );
        $sns = array('01', '02', '03', '09', '13', '20', '23', '27', '29', '93');
        $addrs = array(2000, 2001, 2003, 2004, 2009, 2010);
        $rs = M('rtu_data')->field('sn, addr, report_time, value')
            ->where(sprintf("sn IN(%s) AND addr IN(%s)", implode(',', $sns), implode(',', $addrs)))
            ->order('sn ASC, addr ASC')->select();
        foreach ($rs as $key => $row) {
            $tmp = $cfg[$row['sn']];
            if (!isset($tmp['addrs'][$row['addr']])) {
                continue;
            }
            $ts_key = $tmp['rtu_code'].'_'.$row['addr'];
            if (isset($last_push_times[$ts_key]) && $last_push_times[$ts_key] == $row['report_time']) {
                continue;
            }
            $sensor_key = $tmp['addrs'][$row['addr']];
            // $tm = explode(' ', $row['report_time']);
            $tm = explode(' ', date('Y-m-d H:i:s')); //共用一个上报时间
            if (!isset($records[$row['sn']])) {
                $records[$row['sn']] = array(
                    'collectTime' => $tm[0].'T'.$tm[1].'+0800',
                    'devId' => $tmp['rtu_code'],
                    'devType' => '0F',
                    'props' => array()
                );
            }
            $records[$row['sn']]['props'][$sensor_key] = $row['value'];
            $last_push_times[$ts_key] = $row['report_time'];
        }
        if (count($records) != 0) {
            $post_data = array(
                'records' => array_values($records),
                'source' => 'Byhm-Monitor'
            );
            $ret = Http::post2($api_data_url, 'raw', array(
                    'Authorization' => 'Bearer'.$bc_token,
                    'Content-Type' => 'application/json'
                ), $post_data
            );
            $params = json_encode($post_data);
            if ($ret['http_code'] == 200 && is_array($ret['res']) && !empty($ret['res']['code'] == 200)) {
                $log->mwrite(sprintf("%s [PUSH_DATA] request success：params = %s\r\n", date('Y-m-d H:i:s'), $params));
                F('last_push_times', $last_push_times, './Lib/ORG/');
            } else {
                $log->mwrite(sprintf("%s [PUSH_DATA] request failed：http_code = %d, ret = %s, params = %s\r\n", date('Y-m-d H:i:s'), $ret['http_code'], json_encode($ret['res']), $params));
            }
        }
    }
}