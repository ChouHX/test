$.gf.device_params_define_dtu = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'order_number',   label:$lang.ORDER_NUMBER,   disabled:true},
        {name:'product_sn',     label:$lang.PRODUCT_SN,     disabled:true},
        {name:'uart_count',     label:$lang.UART_COUNT,     disabled:true},
        {name:'di_count',       label:$lang.DI_COUNT,       disabled:true},
        {name:'ai_count',       label:$lang.AI_COUNT,       disabled:true},
        {name:'do_count',       label:$lang.DO_COUNT,       disabled:true},
        {name:'spi_flash_size', label:$lang.SPI_FLASH_SIZE, disabled:true},
        {name:'gps_support',    label:$lang.GPS_SUPPORT, xtype:'checkbox', disabled:true}
    ]},
    {title: $lang.POSITIONING_PARAMETER, id:'params_tab_position_parameter', items:[
        {name:'gps_en',    label:$lang.GPS_EN, xtype:'checkbox'},
        {name:'gps_report_interval', label:$lang.GPS_REPORT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'gps_fastest_report_interval', label:$lang.GPS_FASTEST_REPORT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'gps_ip', label:$lang.GPS_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'gps_host_name', label:$lang.GPS_HOST_NAME, length:[0,63]},
        {name:'gps_port', label:$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'gps_ip2', label:$lang.GPS_IP+'2', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'gps_host_name2', label:$lang.GPS_HOST_NAME+'2', length:[0,63]},
        {name:'gps_port2', label:$lang.GPS_PORT+'2', xtype:'number', minValue:1, maxValue:65535},
        {name:'gps_socket_type', label:$lang.GPS_SOCKET_TYPE, xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]},
        {name:'gps_reconnect_interval', label:$lang.GPS_RECONNECT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'gps_insert_id', label:$lang.GPS_INSERT_ID, xtype:'checkbox'}
    ]},
    {title: $lang.SERIAL_PORT_PARAMETER, id:'params_tab_serial_port_parameter', items:[
        {name:'uart_type1', label:$lang.UART_TYPE1, xtype:'combo', data:[{id:'0', name:$lang.CONFIGURATION_MANAGEMENT}, {id:'1', name:$lang.MODBUS_DATA_COLLECTION}, {id:'2', name:'DTU '+$lang.DATA_TRANSMISSION}]},
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
        {name:'databit1',   label:$lang.DATABIT1,   xtype:'combo', data:[{id:'8', name:'8'}]},
        {name:'stopbit1',   label:$lang.STOPBIT1,   xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'paritybit1', label:$lang.PARITYBIT1, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]},
        // {name:'modbus_slave_count1', label:$lang.MODBUS_SLAVE_COUNT1, xtype:'number', minValue:0, maxValue:32},
        {name:'modbus_slave_addr1', label:$lang.MODBUS_SLAVE_ADDR1, emptyText:$lang.PLACEHOLDER_HEX},
        {name:'uart_type2', label:$lang.UART_TYPE2, xtype:'combo', data:[{id:'0', name:$lang.CONFIGURATION_MANAGEMENT}, {id:'1', name:$lang.MODBUS_DATA_COLLECTION}, {id:'2', name:'DTU '+$lang.DATA_TRANSMISSION}]},
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
        {name:'databit2',   label:$lang.DATABIT2,   xtype:'combo', data:[{id:'8', name:'8'}]},
        {name:'stopbit2',   label:$lang.STOPBIT2,   xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'paritybit2', label:$lang.PARITYBIT2, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]},
        // {name:'modbus_slave_count2', label:$lang.MODBUS_SLAVE_COUNT2, xtype:'number', minValue:0, maxValue:32},
        {name:'modbus_slave_addr2', label:$lang.MODBUS_SLAVE_ADDR2, emptyText:$lang.PLACEHOLDER_HEX}
    ]},
    {title: $lang.DTU_PARAMETER, id:'params_tab_dtu_parameter', items:[
        {name:'dtu_en',    label:$lang.DTU_EN, xtype:'checkbox'},
        {name:'dtu_run_type', label:$lang.DTU_RUN_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_TERM_STATUS_ONLINE}, {id:'1', name:$lang.WAKE_MODE}]},
        {title:$lang.DTU_WAKEUP_TYPE, xtype:'fieldset', name:'dtu_wakeup_type_fieldset', collapsible:true, collapsed:true, hidden:true, items:[
            {name:'dtu_wakeup_type_bit2', label:$lang.VAR_TIMING, xtype:'checkbox'},
            {name:'dtu_wakeup_type_bit1', label:$lang.VAR_SMS, xtype:'checkbox'},
            {name:'dtu_wakeup_type_bit0', label:$lang.VAR_DATA, xtype:'checkbox'}
        ]},
        {name:'dtu_wakeup_time', label:$lang.DTU_WAKEUP_TIME, hidden:true, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'dtu_idle_time', label:$lang.DTU_IDLE_TIME, hidden:true,  xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'dtu_reconnect_interval', label:$lang.DTU_RECONNECT_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]},
        {title:$lang.SERIAL_COMMUNICATION, xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'serial_ptcl',   label:$lang.SERIAL_PTCL,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_MODE}, {id:'1', name:$lang.COMMAND_MODE}]},
            {name:'packet_length', label:$lang.PACKET_LENGTH, xtype:'number', minValue:1, maxValue:1024, emptyText:$lang.VAR_BYTE},
            {name:'packet_delay', label:$lang.PACKET_DELAY, xtype:'number', minValue:100, maxValue:1000, emptyText:$lang.VAR_MILLISECOND}
        ]},
        {title:$lang.CONNECTION_SETTINGS, xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'dtu_ip', label:$lang.DTU_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'dtu_host_name', label:$lang.DTU_HOST_NAME, length:[0,63]},
            {name:'dtu_port', label:$lang.DTU_PORT, xtype:'number', minValue:1, maxValue:65535},
            {name:'dtu_socket_type', label:$lang.DTU_SOCKET_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]}
        ]},
        {name:'dtu_heartbeat_interval', label:$lang.DTU_HEARTBEAT_INTERVAL, xtype:'number', emptyText:'0~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'dtu_id',    label:$lang.DTU_ID, length:[0,15]},
        // {name:'dtu_protocal_type', label:$lang.DTU_PROTOCAL_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_MODE}, {id:'1', name:'drmp'}]},
        {name:'dtu_insert_id_en', label:$lang.DTU_INSERT_ID_EN, xtype:'checkbox'},
        {name:'dtu_insert_id_pos', label:$lang.DTU_INSERT_ID_POS, xtype:'number', minValue:0, maxValue:24, emptyText:'0~24'},
        {name:'dtu_heart_en', label:$lang.DTU_HEART_EN, xtype:'checkbox'},
        {name:'dtu_heart_packet', label:$lang.DTU_HEART_PACKET, emptyText:$lang.HEX_MAXLEN_24},
        {name:'dtu_heart_ack_en', label:$lang.DTU_HEART_ACK_EN, xtype:'checkbox'},
        {name:'dtu_heart_ack_packet', label:$lang.DTU_HEART_ACK_PACKET, emptyText:$lang.HEX_MAXLEN_24}
    ]},
    {title: $lang.ACQUISITION_CONTROL_PARAMETER, id:'params_tab_collection_control_parameter', items:[
        {name:'io_ctrl_en', label:$lang.IO_CTRL_EN, xtype:'checkbox'},
        {name:'local_modbus_id', label:$lang.LOCAL_MODBUS_ID, xtype:'number', minValue:1, maxValue:254, emptyText:'1~254'},
        {name:'io_comm_type', label:$lang.IO_COMM_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.M2M_30_PROTOCOL}, {id:'1', name:'modbus'}, {id:'2', name:$lang.CUSTOM_MADE}, {id:'3', name:'DDP+modbus'}, {id:'4', name:'DEMO'}]},
        {name:'io_report_interval', label:$lang.IO_REPORT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'io_reconnect_interval', label:$lang.IO_RECONNECT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'io_ip', label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'io_host_name', label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'io_port', label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'io_ip2', label:$lang.IO_IP+'2', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'io_host_name2', label:$lang.IO_HOST_NAME+'2', length:[0,63]},
        {name:'io_port2', label:$lang.IO_PORT+'2', xtype:'number', minValue:1, maxValue:65535},
        {name:'io_socket_type', label:$lang.IO_SOCKET_TYPE,   xtype:'combo', data:[{id:'0', name:'UDP-CLIENT'}, {id:'1', name:'TCP-CLIENT'}, {id:'2', name:'TCP-SERVER'}]},
        {name:'io_heartbeat_interval', label:$lang.IO_HEARTBEAT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'io_report_mode', label:$lang.REPORTING_MODE,   xtype:'combo', data:[{id:'0', name:$lang.TIMED_REPORTING}, {id:'1', name:$lang.CHANGE_REPORT}]},
        {name:'io_fast_interval', label:$lang.MIN_REPORTING_INTERVAL, xtype:'number', minValue:5, maxValue:65536},
        {name:'io_change_range', label:$lang.MINIMUM_CHANGE, xtype:'number', minValue:1, maxValue:100}
    ]},
    {title: $lang.DIALUP_NETWORK_PARAMETER, id:'params_tab_dialup_network_parameter', items:[
        {name:'apn', label:$lang.APN, length:[0,63]},
        {name:'ppp_name', label:$lang.PPP_NAME, length:[0,63]},
        {name:'ppp_pw', label:$lang.PPP_PW, length:[0,63]},
        {name:'ping_ip', label:$lang.PING_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'ping_name', label:$lang.PING_NAME, length:[0,63]},
        {name:'ping_interval', label:$lang.PING_INTERVAL, xtype:'number', minValue:0, maxValue:255, emptyText:'0~255 '+$lang.VAR_TIME_ARR[2]},
        {name:'pin_code', label:$lang.PIN_CODE, length:[4,4], disabled:true}
    ]},
    {title: $lang.LOAD_BALANCING, id:'params_tab_load_balancing', items:[
        {name:'lb_en', label:$lang.IS_ENABLE, xtype:'checkbox'},
        {name:'lb_ip', label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'lb_host_name', label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'lb_port', label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'lb_name', label:$lang.PLATFORM_LOGIN_ACCOUNT_NAME, length:[0,32]},
        {name:'lb_pw', label:$lang.PLATFORM_LOGIN_PASSWORD, length:[0,32]}
    ]},
    {title: $lang.OTHER_PARAMETER, id:'params_tab_other_parameter', items:[
        {name:'debug_en', label:$lang.DEBUG_EN, xtype:'checkbox'},
        {name:'conf_cmd', label:$lang.CONF_CMD, length:[6,6]},
        {name:'phone_number', label:$lang.PHONE_NUMBER, length:[0,127], emptyText:'0~127 '+$lang.SPLIT_BY_SEMICOLON}
    ]}
];