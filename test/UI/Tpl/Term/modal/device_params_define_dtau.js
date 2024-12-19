$.gf.device_params_define_dtau = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'sn',             label:$lang.PRODUCT_SN,     disabled:true}
    ]},
    {title: $lang.DIAL, id:'params_tab_dial', items:[
        {name:'apn1',        label:'APN', length:[0,63]},
        {name:'ppp_name1',   label:'APN '+$lang.VAR_USER_NAME, length:[0,63]},
        {name:'ppp_pw1',     label:'APN '+$lang.VAR_PASSWD, length:[0,63]},
        {name:'auth_mode1',  label:$lang.AUTH_TYPE, xtype:'combo', data:[
            {id:'0', name:'AUTO'},
            {id:'1', name:'PAP'},
            {id:'2', name:'CHAP'}
        ]},
        {name:'main_dns1',   label:$lang.WAN_DNS, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'sec_dns1',    label:$lang.WAN_DNS2, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'pin_code1',   label:$lang.PIN_CODE, length:[4,4]},
        {name:'network_mode1',  label:$lang.NET_MODE, xtype:'combo', data:[
            {id:'0', name:'AUTO'},
            {id:'1', name:'4G'},
            {id:'2', name:'3G'},
            {id:'3', name:'2G_3G'},
            {id:'4', name:'2G'}
        ]},
    ]},
    {title: $lang.VAR_RS485, id:'params_tab_rs485', items:[
        {name:'om_baudrate',    label:$lang.IBST_SERIAL_RATE, xtype:'combo', data:[
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'om_databit',     label:$lang.IBST_SERIAL_DATABITS,   xtype:'combo', data:[{id:'7', name:'7'}, {id:'8', name:'8'}]},
        {name:'om_stopbit',     label:$lang.IBST_SERIAL_STOPBITS,   xtype:'combo', data:[{id:'1', name:'1 '+$lang.IBST_SERIAL_STOPBITS}, {id:'2', name:'2 '+$lang.IBST_SERIAL_STOPBITS}]},
        {name:'om_paritybit',   label:$lang.IBST_SERIAL_PARITY,     xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]}
    ]},
    {title: $lang.RAIN_BUCKET, id:'params_tab_rain_bucket', items:[
        {name:'rf_enable',      label:$lang.IS_ENABLE, xtype:'checkbox'},
        {name:'rf_accuracy',    label:$lang.VAR_ACCURACY, xtype:'number', emptyText:$lang.VAR_MM},
        {name:'rf_di_x',        label:$lang.DI_INTERFACE, xtype:'combo', data:[
            {id:'0', name:'DI1'},
            {id:'1', name:'DI2'}
        ]},
        {name:'rf_threshold',   label:$lang.VAR_THRESHOLD, xtype:'number', emptyText:$lang.VAR_MM}
    ]},
    {title: $lang.OSMOMETER, id:'params_tab_osmometer', items:[
        {name:'om_type',        label:$lang.TERM_MODEL, xtype:'combo', data:[
            {id:'0', name:$lang.VAR_DISABLE},
            {id:'1', name:$lang.MODEL_1},
            {id:'2', name:'DZ-42'}
        ]},
        {name:'om_uart_x',      label:$lang.CONNECT_SERIAL_PORT, xtype:'combo', data:[
            {id:'0', name:'485-1'},
            {id:'1', name:'485-2'}
        ]},
        {name:'om_threshold',   label:$lang.VAR_THRESHOLD,          xtype:'number', hidden:true},
        {name:'om_offset',      label:$lang.RETURN_ZERO,            xtype:'number', hidden:true},
        {name:'om_yl_id',       label:$lang.OSMOMETER+' ID', emptyText:$lang.HEX_MAXLEN_24.replace(24,8), hidden:true},
        {name:'om_enable_1',    label:$lang.CHANNEL_1+'&nbsp;'+$lang.VAR_ENABLE,  xtype:'checkbox', hidden:true},
        {name:'om_threshold_1', label:$lang.CHANNEL_1+'&nbsp;'+$lang.VAR_THRESHOLD, xtype:'number', hidden:true},
        {name:'om_offset_1',    label:$lang.CHANNEL_1+'&nbsp;'+$lang.RETURN_ZERO, hidden:true},
        {name:'om_k_1',         label:$lang.CHANNEL_1+'&nbsp;'+'k', hidden:true},
        {name:'om_b_1',         label:$lang.CHANNEL_1+'&nbsp;'+'b', hidden:true},
        {name:'om_f0_1',        label:$lang.CHANNEL_1+'&nbsp;'+'f0', hidden:true},
        {name:'om_t0_1',        label:$lang.CHANNEL_1+'&nbsp;'+'t0', hidden:true},
        {name:'om_enable_2',    label:$lang.CHANNEL_2+'&nbsp;'+$lang.VAR_ENABLE,  xtype:'checkbox', hidden:true},
        {name:'om_threshold_2', label:$lang.CHANNEL_2+'&nbsp;'+$lang.VAR_THRESHOLD, xtype:'number', hidden:true},
        {name:'om_offset_2',    label:$lang.CHANNEL_2+'&nbsp;'+$lang.RETURN_ZERO, hidden:true},
        {name:'om_k_2',         label:$lang.CHANNEL_2+'&nbsp;'+'k', hidden:true},
        {name:'om_b_2',         label:$lang.CHANNEL_2+'&nbsp;'+'b', hidden:true},
        {name:'om_f0_2',        label:$lang.CHANNEL_2+'&nbsp;'+'f0', hidden:true},
        {name:'om_t0_2',        label:$lang.CHANNEL_2+'&nbsp;'+'t0', hidden:true},
        {name:'om_enable_3',    label:$lang.CHANNEL_3+'&nbsp;'+$lang.VAR_ENABLE,  xtype:'checkbox', hidden:true},
        {name:'om_threshold_3', label:$lang.CHANNEL_3+'&nbsp;'+$lang.VAR_THRESHOLD, xtype:'number', hidden:true},
        {name:'om_offset_3',    label:$lang.CHANNEL_3+'&nbsp;'+$lang.RETURN_ZERO, hidden:true},
        {name:'om_k_3',         label:$lang.CHANNEL_3+'&nbsp;'+'k', hidden:true},
        {name:'om_b_3',         label:$lang.CHANNEL_3+'&nbsp;'+'b', hidden:true},
        {name:'om_f0_3',        label:$lang.CHANNEL_3+'&nbsp;'+'f0', hidden:true},
        {name:'om_t0_3',        label:$lang.CHANNEL_3+'&nbsp;'+'t0', hidden:true},
        {name:'om_enable_4',    label:$lang.CHANNEL_4+'&nbsp;'+$lang.VAR_ENABLE,  xtype:'checkbox', hidden:true},
        {name:'om_threshold_4', label:$lang.CHANNEL_4+'&nbsp;'+$lang.VAR_THRESHOLD, xtype:'number', hidden:true},
        {name:'om_offset_4',    label:$lang.CHANNEL_4+'&nbsp;'+$lang.RETURN_ZERO, hidden:true},
        {name:'om_k_4',         label:$lang.CHANNEL_4+'&nbsp;'+'k', hidden:true},
        {name:'om_b_4',         label:$lang.CHANNEL_4+'&nbsp;'+'b', hidden:true},
        {name:'om_f0_4',        label:$lang.CHANNEL_4+'&nbsp;'+'f0', hidden:true},
        {name:'om_t0_4',        label:$lang.CHANNEL_4+'&nbsp;'+'t0', hidden:true}
    ]},
    {title: $lang.INCLINOMETER, id:'params_tab_inclinometer', items:[
        {name:'cl_type',        label:$lang.TERM_MODEL, xtype:'combo', data:[
            {id:'0', name:$lang.VAR_DISABLE},
            {id:'1', name:$lang.MODEL_1},
            {id:'2', name:$lang.MODEL_1.replace('1', '2')},
        ]},
        {name:'cl_threshold',   label:$lang.VAR_THRESHOLD,          xtype:'number'},
        {name:'cl_count',       label:$lang.NUMBER_OF_SENSORS,      xtype:'number'},
        {name:'cl_offset_x1',   label:'1# '+$lang.RETURN_ZERO+' X', xtype:'number'},
        {name:'cl_offset_y1',   label:'1# '+$lang.RETURN_ZERO+' Y', xtype:'number'},
        {name:'cl_offset_x2',   label:'2# '+$lang.RETURN_ZERO+' X', xtype:'number'},
        {name:'cl_offset_y2',   label:'2# '+$lang.RETURN_ZERO+' Y', xtype:'number'},
        {name:'cl_offset_x3',   label:'3# '+$lang.RETURN_ZERO+' X', xtype:'number'},
        {name:'cl_offset_y3',   label:'3# '+$lang.RETURN_ZERO+' Y', xtype:'number'},
        {name:'cl_offset_x4',   label:'4# '+$lang.RETURN_ZERO+' X', xtype:'number'},
        {name:'cl_offset_y4',   label:'4# '+$lang.RETURN_ZERO+' Y', xtype:'number'}
    ]},
    {title: $lang.POSITIONING_PARAMETER, id:'params_tab_position_parameter', items:[
        {name:'ns_enable',              label:$lang.IS_ENABLE, xtype:'checkbox'},
        {name:'ns_ip',                  label:$lang.GPS_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'ns_host',                label:$lang.GPS_HOST_NAME, length:[0,63]},
        {name:'ns_port',                label:$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'ns_ip_b',                label:$lang.GPS_IP+' (B)', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'ns_host_b',              label:$lang.GPS_HOST_NAME+' (B)', length:[0,63]},
        {name:'ns_port_b',              label:$lang.GPS_PORT+' (B)', xtype:'number', minValue:1, maxValue:65535},
        {name:'ns_gps_ck_interval',     label:$lang.DEVICE_INSPECTION_INTERVAL, xtype:'number'},
        {name:'ns_raw_output_interval', label:$lang.DATA_REPORTING_INTERVAL, xtype:'number'},
        {name:'ns_type',                label:$lang.TERM_MODEL, xtype:'combo', data:[{id:'0', name:$lang.SLAVES}, {id:'1', name:$lang.BASE_STATION}]},
        {name:'ns_threshold',           label:$lang.VAR_THRESHOLD, xtype:'number'}
    ]},
    {title: $lang.OTHER_PARAMETER, id:'params_tab_other_parameter', items:[
        {name:'phone_number',   label:$lang.PHONE_NUMBER, length:[0,15], emptyText:'0~15 '+$lang.VAR_BYTE},
        {name:'debug_enable',   label:$lang.DEBUG_EN, xtype:'checkbox'}
    ]},
    {title: $lang.DATA_CENTER, id:'params_tab_data_center', items:[
        {name:'id',                     label:$lang.DEVICE_ID, length:[0,15], emptyText:'0~15 '+$lang.VAR_BYTE},
        {name:'m2m_ip',                 label:$lang.GPS_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'m2m_host',               label:$lang.GPS_HOST_NAME, length:[0,63]},
        {name:'m2m_port',               label:$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'m2m_ip_b',               label:$lang.GPS_IP+' (B)', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'m2m_host_b',             label:$lang.GPS_HOST_NAME+' (B)', length:[0,63]},
        {name:'m2m_port_b',             label:$lang.GPS_PORT+' (B)', xtype:'number', minValue:1, maxValue:65535},
        {name:'m2m_heartbeat_interval', label:$lang.IO_HEARTBEAT_INTERVAL, xtype:'number'},
        {name:'m2m_reconnect_interval', label:$lang.IO_RECONNECT_INTERVAL, xtype:'number'},
        {name:'m2m_report_interval',    label:$lang.IO_REPORT_INTERVAL, xtype:'number'}
    ]}
];