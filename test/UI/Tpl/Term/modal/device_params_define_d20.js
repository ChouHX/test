$.gf.device_params_define_d20 = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'serial_type',     label:$lang.SERIAL_TYPE,   disabled:true},
        {name:'product_sn',     label:$lang.PRODUCT_SN,     disabled:true}
    ]},
    {title: $lang.DIALUP_NETWORK_PARAMETER, id:'params_tab_dialup_network_parameter', items:[
        {name:'apn',           label:$lang.APN, length:[0,63]},
        {name:'ppp_name',      label:$lang.PPP_NAME, length:[0,63]},
        {name:'ppp_pw',        label:$lang.PPP_PW, length:[0,63]},
        {name:'ping_ip',       label:$lang.PING_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'ping_name',     label:$lang.PING_NAME, length:[0,63]},
        {name:'ping_interval', label:$lang.PING_INTERVAL, xtype:'number', minValue:0, maxValue:255, emptyText:'0~255 '+$lang.VAR_TIME_ARR[2]},
        {name:'primary_dns',   label:$lang.WAN_DNS, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'secondary_dns', label:$lang.WAN_DNS2, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'pin_code',      label:$lang.PIN_CODE, length:[4,4]},
        {name:'network_mode',  label:$lang.NET_MODE, xtype:'combo', data:[
            {id:'0', name:'AUTO'},
            {id:'1', name:'GSM'},
            {id:'2', name:'WCDMA'},
            {id:'3', name:'LTE'},
            {id:'4', name:'TD-SCDMA'},
            {id:'5', name:'UMTS'},
            {id:'6', name:'CDMA'},
            {id:'7', name:'HDR'},
            {id:'8', name:'CDMA and EVDO'}
        ]}
    ]},
    {title: $lang.SERIAL_PORT_PARAMETER, id:'params_tab_channel_1_setting', items:[
        {name:'baudrate1', label:$lang.BAUDRATE1, xtype:'combo', data:[
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'databit1',       label:$lang.DATABIT1,   xtype:'combo', data:[{id:'8', name:'8'}]},
        {name:'stopbit1',       label:$lang.STOPBIT1,   xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'paritybit1',     label:$lang.PARITYBIT1, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]},
        {name:'packet_length1', label:$lang.PACKET_LENGTH, xtype:'number', minValue:1, maxValue:1024, emptyText:'1~1024 '+$lang.VAR_BYTE},
        {name:'packet_delay1',  label:$lang.PACKET_DELAY, xtype:'number', minValue:100, maxValue:1000, emptyText:'100~1000 '+$lang.VAR_MILLISECOND},
        {name:'serial_ptcl1',   label:$lang.SERIAL_PTCL,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_MODE}, {id:'1', name:$lang.COMMAND_MODE}]},
        {name:'ip1',            label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'host_name1',     label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'port1',          label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'socket_type1',   label:$lang.DTU_SOCKET_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]}
    ]},
    {title: $lang.SERIAL_PORT_PARAMETER, id:'params_tab_channel_2_setting', items:[
        {name:'baudrate2', label:$lang.BAUDRATE2, xtype:'combo', data:[
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'databit2',       label:$lang.DATABIT2,   xtype:'combo', data:[{id:'8', name:'8'}]},
        {name:'stopbit2',       label:$lang.STOPBIT2,   xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'paritybit2',     label:$lang.PARITYBIT2, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]},
        {name:'packet_length2', label:$lang.PACKET_LENGTH, xtype:'number', minValue:1, maxValue:1024, emptyText:'1~1024 '+$lang.VAR_BYTE},
        {name:'packet_delay2',  label:$lang.PACKET_DELAY, xtype:'number', minValue:100, maxValue:1000, emptyText:'100~1000 '+$lang.VAR_MILLISECOND},
        {name:'serial_ptcl2',   label:$lang.SERIAL_PTCL,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_MODE}, {id:'1', name:$lang.COMMAND_MODE}]},
        {name:'ip2',            label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'host_name2',     label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'port2',          label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'socket_type2',   label:$lang.DTU_SOCKET_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]}
    ]},
    {title: $lang.DTU_PARAMETER, id:'params_tab_protocol', items:[
        {name:'id',                     label:$lang.DEVICE_ID, length:[0,15], emptyText:'0~15 '+$lang.VAR_BYTE},
        {name:'protocal_type',          label:$lang.PROTOCAL_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_TRANSMISSION}, {id:'1', name:'DRMP'}, {id:'2', name:'HDDP'}, {id:'3', name:'MODBUS TCP 2 RTU'}, {id:'4', name:'MQTT'}]},
        {name:'heartbeat_interval',     label:$lang.LINK_HEARTBEAT_INTERVAL, xtype:'number'},
        {name:'insert_id_en',           label:$lang.DTU_INSERT_ID_EN, xtype:'checkbox'},
        {name:'insert_id_pos',          label:$lang.DTU_INSERT_ID_POS, xtype:'number', minValue:0, maxValue:24, emptyText:'0~24'},
        {name:'heart_en',               label:$lang.DTU_HEART_EN, xtype:'checkbox'},
        {name:'heart_packet',           label:$lang.DTU_HEART_PACKET, emptyText:$lang.HEX_MAXLEN_24},
        {name:'heart_ack_en',           label:$lang.DTU_HEART_ACK_EN, xtype:'checkbox'},
        {name:'heart_ack_packet',       label:$lang.DTU_HEART_ACK_PACKET, emptyText:$lang.HEX_MAXLEN_24},
        {name:'mqtt_client_id',         label:$lang.MQTT_CLIENT_ID, emptyText:$lang.VAR_TERM_GROUP_NAME_VALID},
        {name:'mqtt_user_name',         label:$lang.MQTT_USERNAME, emptyText:$lang.VAR_TERM_GROUP_NAME_VALID},
        {name:'mqtt_password',          label:$lang.MQTT_PASSWORD, emptyText:$lang.VAR_TERM_GROUP_NAME_VALID},
        {name:'mqtt_pub_topic',         label:$lang.MQTT_PUB_TOPIC, emptyText:$lang.VAR_TERM_GROUP_NAME_VALID},
        {name:'mqtt_sub_topic',         label:$lang.MQTT_SUB_TOPIC, emptyText:$lang.VAR_TERM_GROUP_NAME_VALID}
    ]},
    {title: $lang.DTU_PARAMETER, id:'params_tab_io_control', items:[
        {name:'input_alarm_mode1',      label:$lang.DI1_ALARM_MODE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.RISING_EDGE_ALARM}, {id:'2', name:$lang.FALLING_EDGE_ALARM}]},
        {name:'output_mode1',           label:$lang.DI1_TRIGGER_ACTION,   xtype:'combo', data:[{id:'0', name:$lang.SEND_MESSAGES}, {id:'1', name:$lang.DO1_OUTPUT_LOW_LEVEL}, {id:'2', name:$lang.DO1_OUTPUT_HIGH_LEVEL}, {id:'3', name:$lang.DO1_OUTPUT_LOW_LEVEL.replace('1','2')}, {id:'4', name:$lang.DO1_OUTPUT_HIGH_LEVEL.replace('1','2')}]},
        {name:'input_alarm_mode2',      label:$lang.DI2_ALARM_MODE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.RISING_EDGE_ALARM}, {id:'2', name:$lang.FALLING_EDGE_ALARM}]},
        {name:'output_mode2',           label:$lang.DI2_TRIGGER_ACTION,   xtype:'combo', data:[{id:'0', name:$lang.SEND_MESSAGES}, {id:'1', name:$lang.DO1_OUTPUT_LOW_LEVEL}, {id:'2', name:$lang.DO1_OUTPUT_HIGH_LEVEL}, {id:'3', name:$lang.DO1_OUTPUT_LOW_LEVEL.replace('1','2')}, {id:'4', name:$lang.DO1_OUTPUT_HIGH_LEVEL.replace('1','2')}]},
        {name:'alarm_sms',              label:$lang.ALARM_SMS, length:[0,63]}
    ]},
    {title: $lang.OPERATING_MODE, id:'params_tab_operating_mode', items:[
        {name:'run_type',               label:$lang.DTU_RUN_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_TERM_STATUS_ONLINE}, {id:'1', name:$lang.WAKE_MODE}, {id:'2', name:$lang.SMS_MODE}]},
        {name:'reconnect_interval',     label:$lang.GPS_RECONNECT_INTERVAL, xtype:'number'},
        {name:'idle_time',              label:$lang.DTU_IDLE_TIME, xtype:'number'},
        {name:'set_wake_p',             label:$lang.DISCARD_WAKEUP_PACKETS, xtype:'checkbox'},
        {name:'phone_number',           label:$lang.PHONE_NUMBER, length:[0,31], emptyText:'0~31 '+$lang.VAR_BYTE},
        {name:'en_recv_sms',            label:$lang.ALLOW_RECV_SMS, xtype:'checkbox'},
        {name:'sms_2_ux',               label:$lang.SMS_FORWARDING_SERIAL_PORT,   xtype:'combo', data:[{id:'0', name:$lang.INTERFACE_TYPE_0+' 1'}, {id:'1', name:$lang.INTERFACE_TYPE_0+' 2'}]},
        {name:'active_state_report',    label:$lang.DEBUG_INFORMATION,   xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.STATUS_INFORMATION}, {id:'2', name:$lang.DEBUG_INFORMATION}, {id:'3', name:'AT'}]},
        {name:'conf_cmd',               label:$lang.CONF_CMD, length:[6,6], emptyText:'6 '+$lang.VAR_BYTE},
        {name:'center_select',          label:$lang.SERIAL_DATA_SENT_CENTER,   xtype:'combo', data:[{id:'0', name:$lang.ONE_TO_ONE}, {id:'1', name:$lang.CENTER_ONE}, {id:'2', name:$lang.CENTER_TWO}, {id:'3', name:$lang.MASS_SEND}]},
        {name:'uart_select',            label:$lang.CENTER_DATA_SENT_SERIAL,   xtype:'combo', data:[{id:'0', name:$lang.ONE_TO_ONE}, {id:'1', name:$lang.SERIAL_ONE}, {id:'2', name:$lang.SERIAL_TWO}, {id:'3', name:$lang.MASS_SEND}]}
    ]},
    {title: $lang.REMOTE_MANAGEMENT, id:'params_tab_remote_manage', items:[
        {name:'master_run_type',            label:$lang.LOGIN_MODE,   xtype:'combo', data:[{id:'0', name:$lang.ONLINE_MODE}, {id:'1', name:$lang.TIMING_MODE}]},
        {name:'auto_cnt_interval',          label:$lang.START_INTERVAL, xtype:'number'},
        {name:'master_reconnect_interval',  label:$lang.RECONNECTION_INTERVAL, xtype:'number'},
        {name:'master_ip',                  label:$lang.DTU_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'master_host_name',           label:$lang.DTU_HOST_NAME, length:[0,63], emptyText:'0~63 '+$lang.VAR_BYTE},
        {name:'master_port',                label:$lang.DTU_PORT, xtype:'number'}
    ]}
];