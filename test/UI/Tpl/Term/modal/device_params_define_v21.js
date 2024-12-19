$.gf.device_params_define_v21 = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'sn',             label:$lang.PRODUCT_SN,     disabled:true}
    ]},
    {title: $lang.DIALUP_NETWORK_PARAMETER, id:'params_tab_dialup_network_parameter', items:[
        {name:'apn',           label:$lang.APN, length:[0,63]},
        {name:'ppp_name',     label:$lang.PPP_NAME, length:[0,63]},
        {name:'ppp_pw',        label:$lang.PPP_PW, length:[0,63]},
        {name:'pin_code',      label:$lang.PIN_CODE, length:[4,4]},
        {name:'network_mode',  label:$lang.NET_MODE,   xtype:'combo', data:[{id:'0', name:'AUTO'}, {id:'1', name:'4G'}, {id:'2', name:'3G'}, {id:'3', name:'2G_3G'}, {id:'4', name:'2G'}]}
    ]},
    {title: $lang.SERIAL_PORT_SETTINGS, id:'params_tab_serial_port_settings', items:[
        {name:'baudrate', label:$lang.IBST_SERIAL_RATE, xtype:'combo', data:[
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'databit',       label:$lang.IBST_SERIAL_DATABITS,   xtype:'combo', data:[{id:'8', name:'8'}]},
        {name:'stopbit',       label:$lang.IBST_SERIAL_STOPBITS,   xtype:'combo', data:[{id:'1', name:'1 '+$lang.IBST_SERIAL_STOPBITS}, {id:'2', name:'2 '+$lang.IBST_SERIAL_STOPBITS}]},
        {name:'paritybit',     label:$lang.IBST_SERIAL_PARITY,     xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.VAR_ODD}, {id:'2', name:$lang.VAR_EVEN}]}
    ]},
    {title: $lang.VAR_MENU_RTU, id:'params_tab_data_collection', items:[
        {name:'preheat',            label:$lang.PREHEATING_TIME, xtype:'number'},
        {name:'report_interval',    label:$lang.DATA_GENERATION_INTERVAL, xtype:'number'}
    ]},
    {title: $lang.DATA_REPORT, id:'params_tab_data_report', items:[
        {name:'ptcl',           label:$lang.IBST_SERIAL_STOPBITS,   xtype:'combo', data:[{id:'0', name:$lang.DT_PROTOCOL}, {id:'1', name:'MQTT'}]},
        {name:'open_m2m',       label:$lang.ENABLE_M2M_TIP, xtype:'checkbox'},
        {name:'mqtt_ip',        label:'IP', hidden:true},
        {name:'mqtt_host',      label:$lang.VAR_RULE_DOMAIN, length:[0,63], hidden:true},
        {name:'mqtt_port',      label:$lang.SERVER_PORT, xtype:'number', minValue:1, maxValue:65535, hidden:true},
        {name:'mqtt_client_id', label:'Client ID', length:[0,32], hidden:true},
        {name:'mqtt_username',  label:$lang.VAR_USER_NAME, length:[0,100], hidden:true},
        {name:'mqtt_password',  label:$lang.VAR_PASSWD, length:[0,200], hidden:true},
        {name:'mqtt_pub_topic', label:$lang.MQTT_PUB_TOPIC, length:[0,100], hidden:true},
        {name:'mqtt_ver',       label:$lang.MQTT_VERSION,   xtype:'combo', data:[{id:'0', name:'3.1'}, {id:'1', name:'3.11'}], hidden:true},
        {name:'mqtt_keepalive', label:$lang.KEEPALIVE_INTERVAL, xtype:'number', hidden:true},
        {name:'mqtt_qos',       label:'QOS',   xtype:'combo', data:[{id:'0', name:'0'}, {id:'1', name:'1'}, {id:'2', name:'2'}], hidden:true},
        {name:'mqtt_retain',    label:$lang.KEEP_MESSAGES, xtype:'checkbox', hidden:true},
        {name:'mqtt_ssl_en',    label:$lang.ENABLE_SSL, xtype:'checkbox', hidden:true},
        {name:'mqtt_ssl_ver',   label:$lang.SSL_VERSION, xtype:'combo', data:[{id:'0', name:'SSL3.0'}, {id:'1', name:'TLS1.0'}, {id:'2', name:'TLS1.1'}, {id:'3', name:'TLS1.2'}], hidden:true},
        {name:'mqtt_ca_en',     label:$lang.AUTH_TYPE, xtype:'combo', data:[{id:'0', name:'no auth'}, {id:'1', name:'server auth'}, {id:'2', name:'server & client auth'}], hidden:true}
    ]},
    {title: $lang.OPERATING_MODE, id:'params_tab_operating_mode', items:[
        {name:'work_mode',     label:$lang.DTU_RUN_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_TERM_STATUS_ONLINE}, {id:'1', name:$lang.LOW_POWER_MODE}]},
        {name:'id',            label:$lang.DEVICE_ID, length:[0,15]},
        {name:'phone_number',  label:$lang.SMS_PHONE, length:[0,15]}
    ]},
    {title: $lang.REMOTE_MANAGEMENT, id:'params_tab_data_center', items:[
        {name:'m2m_ip',                 label:$lang.IO_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'m2m_host',               label:$lang.IO_HOST_NAME, length:[0,63]},
        {name:'m2m_port',               label:$lang.IO_PORT, xtype:'number', minValue:1, maxValue:65535},
        {name:'m2m_heartbeat_interval', label:$lang.HEARTBEAT_INTVAL, xtype:'number'}
    ]}
];