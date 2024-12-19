$.gf.device_params_define_d21 = [
    {title: $lang.PRODUCT_INFORMATION, id:'params_tab_product_info', items:[
        {name:'product_type',   label:$lang.PRODUCT_TYPE,   disabled:true},
        {name:'product_model',  label:$lang.PRODUCT_MODEL,  disabled:true},
        {name:'product_sn',     label:$lang.PRODUCT_SN,     disabled:true}
    ]},
    {title: $lang.DIALUP_NETWORK_PARAMETER, id:'params_tab_dialup_network_parameter', items:[
        {name:'apn',           label:$lang.APN, length:[0,63]},
        {name:'ppp_name',      label:$lang.PPP_NAME, length:[0,63]},
        {name:'ppp_pw',        label:$lang.PPP_PW, length:[0,63]},
		{name:'auth_mode',     label:$lang.AUTH_TYPE, xtype:'combo', data:[
            {id:'0', name:'AUTO'},
            {id:'1', name:'PAP'},
            {id:'2', name:'CHAP'}
        ]},
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
	{title: $lang.DTU_PARAMETER, id:'params_tab_serial_port_settings',items:[
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
		{name:'packet_length1', label:$lang.SERIAL_PORT_PACKET_LENGTH_1, xtype:'number'},
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
		{name:'packet_length2', label:$lang.SERIAL_PORT_PACKET_LENGTH_2, xtype:'number'},
		{name:'data_uart',      label:$lang.SERIAL_DATA,xtype:'combo', data:[{id:'1', name:'1(232)'}, {id:'2', name:'2(485)'}]},
	]},
    {title: $lang.DATA_CENTER, id:'params_tab_data_center', items:[
		{name:'chl_type',               label:$lang.CONNECT_METHOD, xtype:'combo', data:[{id:'0', name:'Client'}, {id:'1', name:'TCP Server'}, {id:'2', name:'UDP Server'}, {id:'3', name:'MODBUS TCP-RTU'}]},
        {title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'client_fieldset', hidden:false, items:[
			{name:'protocal_type',          label:$lang.PROTOCAL_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.TRANSPARENT_TRANSMISSION}, {id:'1', name:'HDDP'}]},
			{name:'heartbeat_interval',     label:$lang.LINK_HEARTBEAT_INTERVAL, xtype:'number', emptyText:'0~65535s'},
			{name:'cc_reconnect_interval',  label:$lang.RECONNECTION_INTERVAL, xtype:'number', emptyText:'0~65535s'},			
			{name:'cc_enable1',             label:$lang.CENTER+'1'+$lang.VAR_ENABLE, xtype:'checkbox'},
			{name:'cc_ip1',            		label:$lang.CENTER+'1IP', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
			{name:'cc_host1',     			label:$lang.CENTER+'1'+$lang.GPS_HOST_NAME, length:[0,63]},
			{name:'cc_port1',          		label:$lang.CENTER+'1'+$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
			{name:'cc_so_type1',   			label:$lang.CENTER+'1'+$lang.CONNECTION_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]},
			{name:'cc_enable2',             label:$lang.CENTER+'2'+$lang.VAR_ENABLE, xtype:'checkbox'},
			{name:'cc_ip2',            		label:$lang.CENTER+'2IP', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
			{name:'cc_host2',     			label:$lang.CENTER+'2'+$lang.GPS_HOST_NAME, length:[0,63]},
			{name:'cc_port2',          		label:$lang.CENTER+'2'+$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
			{name:'cc_so_type2',   			label:$lang.CENTER+'2'+$lang.CONNECTION_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]},
			{name:'cc_enable3',             label:$lang.CENTER+'3'+$lang.VAR_ENABLE, xtype:'checkbox'},
			{name:'cc_ip3',            		label:$lang.CENTER+'3IP', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
			{name:'cc_host3',     			label:$lang.CENTER+'3'+$lang.GPS_HOST_NAME, length:[0,63]},
			{name:'cc_port3',          		label:$lang.CENTER+'3'+$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
			{name:'cc_so_type3',   			label:$lang.CENTER+'3'+$lang.CONNECTION_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]},
			{name:'cc_enable4',             label:$lang.CENTER+'4'+$lang.VAR_ENABLE, xtype:'checkbox'},
			{name:'cc_ip4',            		label:$lang.CENTER+'4IP', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
			{name:'cc_host4',     			label:$lang.CENTER+'4'+$lang.GPS_HOST_NAME, length:[0,63]},
			{name:'cc_port4',          		label:$lang.CENTER+'4'+$lang.GPS_PORT, xtype:'number', minValue:1, maxValue:65535},
			{name:'cc_so_type4',   			label:$lang.CENTER+'4'+$lang.CONNECTION_TYPE,   xtype:'combo', data:[{id:'0', name:'TCP'}, {id:'1', name:'UDP'}]},
			{name:'heart_en',               label:$lang.DTU_HEART_EN, xtype:'checkbox'},
			{name:'heart_packet',           label:$lang.DTU_HEART_PACKET, emptyText:$lang.HEX_MAXLEN_24},
			{name:'heart_ack_en',           label:$lang.DTU_HEART_ACK_EN, xtype:'checkbox'},
			{name:'heart_ack_packet',       label:$lang.DTU_HEART_ACK_PACKET, emptyText:$lang.HEX_MAXLEN_24},
			{name:'insert_id_en',           label:$lang.DTU_INSERT_ID_EN, xtype:'checkbox'},
			{name:'insert_id_pos',          label:$lang.DTU_INSERT_ID_POS, xtype:'number', minValue:0, maxValue:24, emptyText:'0~24'}			
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'tcp_server_fieldset', hidden:true, items:[			
			{name:'ct_port',     			label:$lang.SERVICE_PORT, xtype:'number'},
			{name:'ct_dest_ip',  			label:$lang.REMOTE_IP, xtype:'number'},
			{name:'ct_dest_port',  			label:$lang.REMOTE_PORT, xtype:'number'},	
			{name:'ct_idle_timeout',  		label:$lang.LINK_IDLE_INTERVAL, xtype:'number'}
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'udp_server_fieldset', hidden:true, items:[			
			{name:'cu_port',     			label:$lang.SERVICE_PORT, xtype:'number'},
			{name:'cu_dest_ip',  			label:$lang.REMOTE_IP, xtype:'number'},
			{name:'cu_dest_port',  			label:$lang.REMOTE_PORT, xtype:'number'}				
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'modbus_tcp_rtu_fieldset', hidden:true, items:[			
			{name:'cm_port',     			label:$lang.SERVICE_PORT, xtype:'number'},
			{name:'cm_dest_ip',  			label:$lang.REMOTE_IP, xtype:'number'},
			{name:'cm_dest_port',  			label:$lang.REMOTE_PORT, xtype:'number'},	
			{name:'cm_idle_timeout',  		label:$lang.LINK_IDLE_INTERVAL, xtype:'number'}
		]}
		
    ]},   
	{title: $lang.IO_CONTROL, id:'params_tab_di1', items:[
        {name:'di_alarm_mode1',      	label:$lang.ALARM_MODE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.LEVEL_MODE}, {id:'2', name:$lang.PULSE_MODE}]},
        {title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'level_mode1_fieldset', hidden:true, items:[			
			{name:'di_a_level1',      	label:$lang.ALARM_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'pulse_mode1_fieldset', hidden:true, items:[			
			{name:'di_p_start_level1',  label:$lang.THE_DEFAULT_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
			{name:'di_p_width1',     	label:$lang.THE_PULSE_WIDTH, xtype:'number', emptyText:'0~65535s'},
			{name:'di_p_interval1',  	label:$lang.PULSE_INTERVAL, xtype:'number', emptyText:'0~65535s'},
			{name:'di_p_count1',  		label:$lang.PULSE_NUMBER, xtype:'number', emptyText:'0~65535s'},
		]},
        {name:'di_trigger1',       		label:$lang.ALARM_LINKAGE, xtype:'ck1_sms'},				
        {name:'di_dox1',      			label:$lang.DO_ECHO,   xtype:'combo', data:[{id:'0', name:'DO1'}, {id:'1', name:'DO2'}]},	
        {name:'di_sms_context1',        label:$lang.ALARM_SMS, length:[0,63]}
    ]},
	{title: $lang.IO_CONTROL, id:'params_tab_di2', items:[
        {name:'di_alarm_mode2',      	label:$lang.ALARM_MODE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.LEVEL_MODE}, {id:'2', name:$lang.PULSE_MODE}]},
        {title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'level_mode2_fieldset', hidden:true, items:[			
			{name:'di_a_level2',      	label:$lang.ALARM_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'pulse_mode2_fieldset', hidden:true, items:[			
			{name:'di_p_start_level2',  label:$lang.THE_DEFAULT_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
			{name:'di_p_width2',     	label:$lang.THE_PULSE_WIDTH, xtype:'number', emptyText:'0~65535s'},
			{name:'di_p_interval2',  	label:$lang.PULSE_INTERVAL, xtype:'number', emptyText:'0~65535s'},
			{name:'di_p_count2',  		label:$lang.PULSE_NUMBER, xtype:'number', emptyText:'0~65535s'},
		]},
        {name:'di_trigger2',       		label:$lang.ALARM_LINKAGE, xtype:'ck2_sms'},		
        {name:'di_dox2',      			label:$lang.DO_ECHO,   xtype:'combo', data:[{id:'0', name:'DO1'}, {id:'1', name:'DO2'}]},	
        {name:'di_sms_context2',        label:$lang.ALARM_SMS, length:[0,63]}
    ]},
	{title: $lang.IO_CONTROL, id:'params_tab_do1', items:[
        {name:'do_output_mode1',      	label:$lang.OUTPUT_WAY,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.LEVEL_MODE}, {id:'2', name:$lang.PULSE_MODE}]},
        {name:'do_default_level1',  	label:$lang.THE_DEFAULT_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'level_mode3_fieldset', hidden:true, items:[			
			{name:'do_o_level1',      	label:$lang.OUTPUT_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		]},		
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'pulse_mode3_fieldset', hidden:true, items:[			
			{name:'do_p_width1',     	label:$lang.THE_PULSE_WIDTH, xtype:'number', emptyText:'0~65535s'},
			{name:'do_p_interval1',  	label:$lang.PULSE_INTERVAL, xtype:'number', emptyText:'0~65535s'},
			{name:'do_p_count1',  		label:$lang.PULSE_NUMBER, xtype:'number', emptyText:'0~65535s'},
		]}        
    ]},
	{title: $lang.IO_CONTROL, id:'params_tab_do2', items:[
        {name:'do_output_mode2',      	label:$lang.OUTPUT_WAY,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.LEVEL_MODE}, {id:'2', name:$lang.PULSE_MODE}]},
        {name:'do_default_level2',  	label:$lang.THE_DEFAULT_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'level_mode4_fieldset', hidden:true, items:[			
			{name:'do_o_level2',      	label:$lang.ALARM_LEVEL,   xtype:'combo', data:[{id:'0', name:$lang.HIGH_LEVEL}, {id:'1', name:$lang.LOW_LEVEL}]},
		]},
		{title:'', collapsible:true, collapsed:false, xtype:'fieldset', name:'pulse_mode4_fieldset', hidden:true, items:[						
			{name:'do_p_width2',     	label:$lang.THE_PULSE_WIDTH, xtype:'number', emptyText:'0~65535s'},
			{name:'do_p_interval2',  	label:$lang.PULSE_INTERVAL, xtype:'number', emptyText:'0~65535s'},
			{name:'do_p_count2',  		label:$lang.PULSE_NUMBER, xtype:'number', emptyText:'0~65535s'},
		]}        
    ]},
	{title: $lang.SMS_CONFIG, id:'params_tab_sms_config', items:[]},
	{title: $lang.DTU_PARAMETER, id:'params_tab_serial_port_command', items:[
        {name:'cuc_total_interval',     label:$lang.EACH_SEND_INTERVAL, xtype:'number', emptyText:'0~65535s'},
        {name:'cuc_cmd_interval',       label:$lang.COMMAND_SEND_INTERVAL, xtype:'number', emptyText:'0~65535s'},
        {name:'cuc_cmd_en1',      		label:$lang.VAR_COMMAND+'1'+$lang.VAR_ENABLE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]},
        {name:'cuc_cmd1',           	label:$lang.VAR_COMMAND+'1', length:[0,32], emptyText:$lang.HEX_INPUT,disabled:true},
		{name:'cuc_cmd_en2',      		label:$lang.VAR_COMMAND+'2'+$lang.VAR_ENABLE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]},
        {name:'cuc_cmd2',           	label:$lang.VAR_COMMAND+'2', length:[0,32], emptyText:$lang.HEX_INPUT,disabled:true},
		{name:'cuc_cmd_en3',      		label:$lang.VAR_COMMAND+'3'+$lang.VAR_ENABLE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]},
        {name:'cuc_cmd3',           	label:$lang.VAR_COMMAND+'3', length:[0,32], emptyText:$lang.HEX_INPUT,disabled:true}
    ]},
    {title: $lang.OPERATING_MODE, id:'params_tab_operating_mode', items:[
        {name:'run_type',               label:$lang.DTU_RUN_TYPE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_TERM_STATUS_ONLINE}, {id:'1', name:$lang.WAKE_MODE}]},
        {name:'id',                     label:$lang.DEVICE_ID, length:[0,15], emptyText:'0~15 '+$lang.VAR_BYTE},
        {name:'idle_time',              label:$lang.DTU_IDLE_TIME, xtype:'number'},
        {name:'debug_enable',           label:$lang.DEBUG_INFORMATION,  xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]},
        {name:'tcp_keepalive',          label:$lang.TCP_KEEPALIVE, xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]}    
    ]},
    {title: $lang.REMOTE_MANAGEMENT, id:'params_tab_remote_manage', items:[
		{name:'m2m_enable',            		label:$lang.M2M_ENABLE,   xtype:'combo', data:[{id:'0', name:$lang.VAR_DISABLE}, {id:'1', name:$lang.VAR_ENABLE}]},
        {name:'master_run_type',            label:$lang.LOGIN_MODE,   xtype:'combo', data:[{id:'0', name:$lang.ONLINE_MODE}, {id:'1', name:$lang.TIMING_MODE}]},
        {name:'auto_cnt_interval',          label:$lang.START_INTERVAL, xtype:'number'},
        {name:'master_reconnect_interval',  label:$lang.RECONNECTION_INTERVAL, xtype:'number'},
        {name:'master_ip',                  label:$lang.DTU_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
        {name:'master_host_name',           label:$lang.DTU_HOST_NAME, length:[0,63], emptyText:'0~63 '+$lang.VAR_BYTE},
        {name:'master_port',                label:$lang.DTU_PORT, xtype:'number'}
    ]}
];