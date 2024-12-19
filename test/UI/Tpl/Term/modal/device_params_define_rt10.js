$.gf.device_params_define_rt10 = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'product_sn',     label:$lang.PRODUCT_SN,     disabled:true},
        {name:'uart_count',     label:$lang.UART_COUNT,     disabled:true},
        {name:'di_count',       label:$lang.DI_COUNT,       disabled:true},
        {name:'ai_count',       label:$lang.AI_COUNT,       disabled:true},
        {name:'do_count',       label:$lang.DO_COUNT,       disabled:true},
        {name:'spi_flash_size', label:$lang.SPI_FLASH_SIZE, disabled:true}
    ]},
    {title: $lang.POSITIONING_PARAMETER, id:'params_tab_lora', items:[
        {name:'lr_freq', label:$lang.VAR_FREQ, xtype:'number', minValue:0, maxValue:255, emptyText:'470000000~510000000'},
        {name:'lr_bw', label:$lang.VAR_BANDWIDTH, xtype:'combo', data:[
            {id:'144', name:'BW500KHZ'},
            {id:'128', name:'BW250KHZ'},
            {id:'112', name:'BW125KHZ'},
            {id:'96',  name:'BW62_50KHZ'},
            {id:'80',  name:'BW41_66KHZ'},
            {id:'64',  name:'BW31_25KHZ'},
            {id:'48',  name:'BW20_83KHZ'},
            {id:'32',  name:'BW15_62KHZ'},
            {id:'16',  name:'BW10_41KHZ'},
            {id:'0',   name:'BW7_81KHZ'}
        ]},
        {name:'lr_sf', label:$lang.SPREAD_FACTOR, xtype:'combo', data:[
            {id:'192', name:'SF12'},
            {id:'176', name:'SF11'},
            {id:'160', name:'SF10'},
            {id:'144', name:'SF09'},
            {id:'128', name:'SF08'},
            {id:'112', name:'SF07'},
            {id:'96',  name:'SF06'}
        ]},
        {name:'lr_pwfcfg', label:$lang.TRANSMIT_POWER, xtype:'number', minValue:1, maxValue:15, emptyText:'1~15'},
        {name:'lr_netid', label:$lang.NETWORK_ID, xtype:'number', minValue:1, maxValue:254, emptyText:'1~254'}
    ]},
    {title: $lang.SERIAL_PORT_PARAMETER, id:'params_tab_serial_port_parameter', items:[
        {name:'uart_type1', label:$lang.UART_TYPE1, xtype:'combo', data:[{id:'0', name:$lang.CONFIGURATION_MANAGEMENT}, {id:'1', name:$lang.MODBUS_DATA_COLLECTION}]},
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
        {name:'uart_type2', label:$lang.UART_TYPE2, xtype:'combo', data:[{id:'0', name:$lang.CONFIGURATION_MANAGEMENT}, {id:'1', name:$lang.MODBUS_DATA_COLLECTION}]},
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
        {name:'paritybit2', label:$lang.PARITYBIT2, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]}
    ]},
    {title: $lang.ACQUISITION_CONTROL_PARAMETER, id:'params_tab_collection_control_parameter', items:[
        {name:'local_modbus_id', label:$lang.LOCAL_MODBUS_ID, xtype:'number', minValue:1, maxValue:254, emptyText:$lang.PLACEHOLDER_HEX+' 1~254'},
        {name:'io_reconnect_interval', label:$lang.IO_RECONNECT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535'},
        {name:'io_report_mode', label:$lang.REPORTING_MODE, xtype:'combo', data:[{id:'0', name:$lang.TIMED_REPORTING}, {id:'1', name:$lang.CHANGE_REPORT}]},
        {name:'io_report_interval', label:$lang.IO_REPORT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535'},
        {name:'io_fast_interval', label:$lang.GPS_FASTEST_REPORT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535'},
        {name:'io_change_range', label:$lang.MINIMUM_CHANGE, xtype:'number'}
    ]},
    {title: $lang.DIALUP_NETWORK_PARAMETER, id:'params_tab_dialup_network_parameter', items:[
        {name:'apn1', label:'APN1', length:[0,63]},
        {name:'ppp_name1', label:'APN1 '+$lang.VAR_USER_NAME, length:[0,63]},
        {name:'ppp_pw1', label:'APN1 '+$lang.VAR_PASSWD, length:[0,63]},
        {name:'apn2', label:'APN2', length:[0,63]},
        {name:'ppp_name2', label:'APN2 '+$lang.VAR_USER_NAME, length:[0,63]},
        {name:'ppp_pw2', label:'APN2 '+$lang.VAR_PASSWD, length:[0,63]},
        {name:'io_ip', label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'io_host_name', label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'io_port', label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'io_heartbeat_interval', label:$lang.IO_HEARTBEAT_INTERVAL, xtype:'number', minValue:5, maxValue:65535, emptyText:'5~65535 '+$lang.VAR_TIME_ARR[3]},
        {name:'pin_code', label:$lang.PIN_CODE, length:[4,4]}
    ]},
    {title: $lang.OTHER_PARAMETER, id:'params_tab_other_parameter', items:[
        {name:'debug_en', label:$lang.DEBUG_EN, xtype:'checkbox'},
        {name:'conf_cmd', label:$lang.CONF_CMD, length:[6,6]},
        {name:'phone_number', label:$lang.PHONE_NUMBER, length:[0,127], emptyText:'0~127 '+$lang.SPLIT_BY_SEMICOLON}
    ]},
    {title: $lang.LOAD_BALANCING, id:'params_tab_load_balancing', items:[
        {name:'lb_en', label:$lang.IS_ENABLE, xtype:'checkbox'},
        {name:'lb_ip', label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'lb_host_name', label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'lb_port', label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'lb_name', label:$lang.PLATFORM_LOGIN_ACCOUNT_NAME, length:[0,32]},
        {name:'lb_pw', label:$lang.PLATFORM_LOGIN_PASSWORD, length:[0,32]}
    ]}
];