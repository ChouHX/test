$.gf.device_params_define_rt52 = [
    {title: $lang.VAR_TERM_PARAM_SECURITY, id:'params_tab_security', items:[
        {name:'http_passwd', label:$lang.HTTP_PASSWD}
    ]},
    {title:$lang.ACQUISITION_FUNCTION_SETTINGS,  collapsible:true, collapsed:false, xtype:'fieldset', id:'params_tab_rtu_collection_settings', items:[
        {name:'slave_id1', label:$lang.SLAVE_ID, emptyText:'eg:1,2,3'},
        {name:'serial_rate1', label:$lang.IBST_SERIAL_RATE, xtype:'combo', data:[
            {id:'300', name:'300'},
            {id:'600', name:'600'},
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'serial_parity1',   label:$lang.IBST_SERIAL_PARITY,   xtype:'combo', data:[{id:'none', name:$lang.VAR_NONE}, {id:'odd', name:$lang.VAR_ODD}, {id:'even', name:$lang.VAR_EVEN}]},
        {name:'serial_databits1', label:$lang.IBST_SERIAL_DATABITS, xtype:'combo', data:[{id:'5', name:'5'}, {id:'6', name:'6'}, {id:'7', name:'7'}, {id:'8', name:'8'}]},
        {name:'serial_stopbits1', label:$lang.IBST_SERIAL_STOPBITS, xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'slave_id2', label:$lang.SLAVE_ID+' 2', emptyText:'eg:1,2,3'},
        {name:'serial_rate2', label:$lang.IBST_SERIAL_RATE+' 2', xtype:'combo', data:[
            {id:'300', name:'300'},
            {id:'600', name:'600'},
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'serial_parity2',   label:$lang.IBST_SERIAL_PARITY+' 2',   xtype:'combo', data:[{id:'none', name:$lang.VAR_NONE}, {id:'odd', name:$lang.VAR_ODD}, {id:'even', name:$lang.VAR_EVEN}]},
        {name:'serial_databits2', label:$lang.IBST_SERIAL_DATABITS+' 2', xtype:'combo', data:[{id:'5', name:'5'}, {id:'6', name:'6'}, {id:'7', name:'7'}, {id:'8', name:'8'}]},
        {name:'serial_stopbits2', label:$lang.IBST_SERIAL_STOPBITS+' 2', xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'slave_id3', label:$lang.SLAVE_ID+' 3', emptyText:'eg:1,2,3'},
        {name:'serial_rate3', label:$lang.IBST_SERIAL_RATE+' 3', xtype:'combo', data:[
            {id:'300', name:'300'},
            {id:'600', name:'600'},
            {id:'1200', name:'1200'},
            {id:'2400', name:'2400'},
            {id:'4800', name:'4800'},
            {id:'9600', name:'9600'},
            {id:'19200', name:'19200'},
            {id:'38400', name:'38400'},
            {id:'57600', name:'57600'},
            {id:'115200', name:'115200'}
        ]},
        {name:'serial_parity3',   label:$lang.IBST_SERIAL_PARITY+' 3',   xtype:'combo', data:[{id:'none', name:$lang.VAR_NONE}, {id:'odd', name:$lang.VAR_ODD}, {id:'even', name:$lang.VAR_EVEN}]},
        {name:'serial_databits3', label:$lang.IBST_SERIAL_DATABITS+' 3', xtype:'combo', data:[{id:'5', name:'5'}, {id:'6', name:'6'}, {id:'7', name:'7'}, {id:'8', name:'8'}]},
        {name:'serial_stopbits3', label:$lang.IBST_SERIAL_STOPBITS+' 3', xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}]},
        {name:'iot_cycle_interval', label:$lang.IOT_CYCLE_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'rtu_pub_interval', label:$lang.REPORT_FREQUENCY, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]}
    ]},
    {title: $lang.MODBUS_COMMAND_LIST, id:'params_tab_rtu_modbus_cmd_list', items:[]},
    {title: $lang.MQTT_CONN_SETTINGS, id:'params_tab_mqtt_settings', items:[
        {name:'iot_hostname', label:$lang.PPTP_CLIENT_SRVIP, hidden:false},
        {name:'iot_port', label:$lang.GPS_PORT, hidden:false},
        {name:'iot_clientid', label:$lang.CLIENT_ID, hidden:false},
        {name:'iot_username', label:$lang.ACCOUNT, hidden:false},
        {name:'iot_passwd', label:$lang.VAR_PASSWD, hidden:false},
        {name:'iot_root_ca', label:$lang.CERTIFICATE_AUTHORITY, xtype:'textarea', hidden:false},
        {name:'iot_client_ca', label:$lang.CLIENT_CERTIFICATE, xtype:'textarea', hidden:false},
        {name:'iot_client_key', label:$lang.CLIENT_KEY, xtype:'textarea', hidden:false}
    ]},
    {title:$lang.NETWORK_SETTINGS, id:'params_tab_network_settings', items:[
        {name:'wan_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'dhcp', name:$lang.DYNAMICALLY_GET_ADDRESS}, {id:'pppoe', name:$lang.PPPOE_DIALING}, {id:'static', name:$lang.STATIC_ADDRESS}, {id:'ppp3g', name:$lang.VAR_3G4G_DIALING}]},
        {name:'ppp_username', label:$lang.VAR_USER_NAME, hidden:true},
        {name:'ppp_passwd', label:$lang.VAR_PASSWD, hidden:true},
        {name:'ppp_service', label:$lang.SERVICE_NAME, hidden:true},
        {name:'wan_demand', label:$lang.DIAL_MODE, xtype:'combo', data:[{id:'0', name:$lang.LINK_RETENTION}, {id:'1', name:$lang.DIAL_ON_DEMAND}], hidden:true},
        {name:'ppp_redialperiod', label:$lang.CHECK_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
        {name:'ppp_idletime', label:$lang.MAXIMUM_IDLE_TIME, xtype:'number', emptyText:$lang.VAR_TIME_ARR[2], hidden:true},
        {name:'wan_ipaddr', label:$lang.VAR_IP, hidden:true},
        {name:'wan_netmask', label:$lang.LAN_NETMASK, hidden:true},
        {name:'wan_gateway', label:$lang.VAR_GATEWAY, hidden:true},
        {name:'is_ecm_dial', label:$lang.DIAL_MODE, xtype:'combo', data:[{id:'0', name:'PPP'}, {id:'1', name:'ECM'}], hidden:true},
        {label:'MTU', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
            {name:'mtu_enable', label:'', xtype:'combo', data:[{id:'0', name:$lang.VAR_DEFAULT}, {id:'1', name:$lang.VAR_CUSTOM}]},
            {name:'wan_mtu', label:''}
        ]},
        {name:'ppp_mlppp', label:$lang.MULTILINK_OVERLAY, xtype:'checkbox', hidden:true},
        {name:'wan_aslan', label:$lang.WAN_AS_LAN, xtype:'checkbox', hidden:true}
    ]},
    {title: $lang.VAR_TERM_PARAM_3G, id:'params_tab_3g', items:[
        {name:'modem_type', label:$lang.MOBILE_NETWORK_TYPE, disabled:true},
        {name:'PingEnable', label:$lang.PINGENABLE, xtype:'checkbox'},
        {name:'UtmsPingAddr', label:$lang.UTMSPINGADDR, hidden:true},
        {name:'UtmsPingAddr1', label:$lang.UTMSPINGADDR1, hidden:true},
        {name:'PingInterval', label:$lang.PINGINTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
        {name:'PingMax', label:$lang.VAR_RETRY, xtype:'number', emptyText:$lang.VAR_FREQUENCY, hidden:true},
        {name:'icmp_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.REDIAL}, {id:'1', name:$lang.REBOOT_SYSTEM}], hidden:true},
        {name:'rx_tx_enable', label:$lang.RX_TX_ENABLE, xtype:'checkbox'},
        {name:'rx_tx_mode', label:$lang.RX_TX_MODE, xtype:'combo', data:[{id:'0', name:'Rx'}, {id:'1', name:'Tx'}, {id:'2', name:'Tx & Rx'}], hidden:true},
        {name:'rx_tx_check_int', label:$lang.PINGINTERVAL, xtype:'number', minValue:1, maxValue:1440, emptyText:'1~1440,'+$lang.VAR_TIME_ARR[2], hidden:true},
        {name:'rx_tx_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.REDIAL}, {id:'1', name:$lang.REBOOT_SYSTEM}], hidden:true},
        {label:$lang.CIMI_SENT_TO, xtype:'fieldcontainer', layout:'hbox', items:[
            {name:'tcp_server', label:'', emptyText:'Server'},
            {name:'tcp_port', label:'', emptyText:'Port'}
        ]},
        {name:'smspasswd', label:$lang.SMS_VERIFICATION_CODE},
        {name:'CelldialPincode', label:'PIN'},
        {name:'cops_oper', label:$lang.CARRIER_LOCK, emptyText:'46001'},
        {name:'cellType', label:$lang.NET_MODE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'LTE(FDD/TDD)'}, {id:'2', name:'3G(WCDMA/TD-SCDMA/HSPA)'}, {id:'3', name:'3G(CDMA 2000/CDMA 1x)'}]},
        {name:'CelldialApn', label:$lang.APN},
        {name:'CelldialUser', label:$lang.PPP_NAME},
        {name:'CelldialPwd', label:$lang.PPP_PW},
        {name:'auth_type', label:$lang.AUTH_TYPE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'PAP'}, {id:'2', name:'CHAP'}, {id:'3', name:'MS-CHAP'}, {id:'4', name:'MS-CHAPv2'}]},
        {name:'local_ip', label:$lang.LOCAL_IP}
    ]},
    {title:$lang.LOCAL_AREA_NETWORK_SETTINGS, id:'params_tab_lan', items:[
        {name:'lan_ipaddr', label:$lang.LAN_IPADDR},
        {name:'lan_netmask', label:$lang.LAN_NETMASK},
        {name:'lan_proto', label:$lang.LAN_PROTO, xtype:'checkbox'},
        {label:$lang.VAR_IP_RANGE, xtype:'fieldcontainer', hidden:true, layout:'hbox', items:[
            {name:'dhcpd_startip', label:'', emptyText:$lang.DHCP_STARTIP},
            {name:'dhcpd_endip', label:'', emptyText:$lang.DHCP_ENDIP}
        ]},
        {name:'dhcp_lease', label:$lang.DHCP_LEASE, xtype:'number', emptyText:$lang.VAR_MINUTE, hidden:true},
        {name:'dhcpd_dmdns', label:$lang.DHCPD_DMDNS, xtype:'checkbox'},
        {name:'wan_dns', label:$lang.WAN_DNS},
        {name:'wan_dns_part2', label:$lang.WAN_DNS2}
    ]},
    {title: $lang.BASIC_PARAMETER_SETTING, id:'params_tab_wifi', items:[
        {name:'wl0_radio', label:$lang.ENABLE_WIRELESS, xtype:'checkbox'},
        {name:'wl0_hwaddr', label:$lang.VAR_DEVICE_MAC, disabled:true},
        {name:'wl0_mode', label:$lang.WIRELESS_MODE, xtype:'combo', data:[{id:'ap', name:$lang.WIRELESS_ACCESS_POINT_AP}]},
        {name:'wl0_net_mode', label:$lang.OPERATING_MODE, xtype:'combo', data:[{id:'mixed', name:$lang.VAR_AUTO}, {id:'b-only', name:'802.11b'}, {id:'g-only', name:'802.11g'}, {id:'bg-mixed', name:'B+G'}, {id:'n-only', name:'802.11n'}]},
        {name:'wl0_ssid', label:'SSID'},
        {name:'wl0_closed', label:$lang.BROADCAST_SSID, xtype:'checkbox'}/*选中时wl0_closed=0*/
    ]},
    {title:$lang.SCHEDULED_REBOOT, collapsible:true, collapsed:false, xtype:'fieldset', id:'params_tab_scheduled_reboot', items:[
        {name:'rboot_enabled', label:$lang.IS_ENABLE, xtype:'checkbox'},
        {label:$lang.EXEC_TIME, xtype:'fieldcontainer', layout:'hbox', items:[
            {name:'rboot_time', label:'', xtype:'combo', data:[
                {id:'0', name:'12:00 AM'},
                {id:'15', name:'12:15 AM'},
                {id:'30', name:'12:30 AM'},
                {id:'45', name:'12:45 AM'},
                {id:'60', name:'1:00 AM'},
                {id:'75', name:'1:15 AM'},
                {id:'90', name:'1:30 AM'},
                {id:'105', name:'1:45 AM'},
                {id:'120', name:'2:00 AM'},
                {id:'135', name:'2:15 AM'},
                {id:'150', name:'2:30 AM'},
                {id:'165', name:'2:45 AM'},
                {id:'180', name:'3:00 AM'},
                {id:'195', name:'3:15 AM'},
                {id:'210', name:'3:30 AM'},
                {id:'225', name:'3:45 AM'},
                {id:'240', name:'4:00 AM'},
                {id:'255', name:'4:15 AM'},
                {id:'270', name:'4:30 AM'},
                {id:'285', name:'4:45 AM'},
                {id:'300', name:'5:00 AM'},
                {id:'315', name:'5:15 AM'},
                {id:'330', name:'5:30 AM'},
                {id:'345', name:'5:45 AM'},
                {id:'360', name:'6:00 AM'},
                {id:'375', name:'6:15 AM'},
                {id:'390', name:'6:30 AM'},
                {id:'405', name:'6:45 AM'},
                {id:'420', name:'7:00 AM'},
                {id:'435', name:'7:15 AM'},
                {id:'450', name:'7:30 AM'},
                {id:'465', name:'7:45 AM'},
                {id:'480', name:'8:00 AM'},
                {id:'495', name:'8:15 AM'},
                {id:'510', name:'8:30 AM'},
                {id:'525', name:'8:45 AM'},
                {id:'540', name:'9:00 AM'},
                {id:'555', name:'9:15 AM'},
                {id:'570', name:'9:30 AM'},
                {id:'585', name:'9:45 AM'},
                {id:'600', name:'10:00 AM'},
                {id:'615', name:'10:15 AM'},
                {id:'630', name:'10:30 AM'},
                {id:'645', name:'10:45 AM'},
                {id:'660', name:'11:00 AM'},
                {id:'675', name:'11:15 AM'},
                {id:'690', name:'11:30 AM'},
                {id:'705', name:'11:45 AM'},
                {id:'720', name:'12:00 PM'},
                {id:'735', name:'12:15 PM'},
                {id:'750', name:'12:30 PM'},
                {id:'765', name:'12:45 PM'},
                {id:'780', name:'1:00 PM'},
                {id:'795', name:'1:15 PM'},
                {id:'810', name:'1:30 PM'},
                {id:'825', name:'1:45 PM'},
                {id:'840', name:'2:00 PM'},
                {id:'855', name:'2:15 PM'},
                {id:'870', name:'2:30 PM'},
                {id:'885', name:'2:45 PM'},
                {id:'900', name:'3:00 PM'},
                {id:'915', name:'3:15 PM'},
                {id:'930', name:'3:30 PM'},
                {id:'945', name:'3:45 PM'},
                {id:'960', name:'4:00 PM'},
                {id:'975', name:'4:15 PM'},
                {id:'990', name:'4:30 PM'},
                {id:'1005', name:'4:45 PM'},
                {id:'1020', name:'5:00 PM'},
                {id:'1035', name:'5:15 PM'},
                {id:'1050', name:'5:30 PM'},
                {id:'1065', name:'5:45 PM'},
                {id:'1080', name:'6:00 PM'},
                {id:'1095', name:'6:15 PM'},
                {id:'1110', name:'6:30 PM'},
                {id:'1125', name:'6:45 PM'},
                {id:'1140', name:'7:00 PM'},
                {id:'1155', name:'7:15 PM'},
                {id:'1170', name:'7:30 PM'},
                {id:'1185', name:'7:45 PM'},
                {id:'1200', name:'8:00 PM'},
                {id:'1215', name:'8:15 PM'},
                {id:'1230', name:'8:30 PM'},
                {id:'1245', name:'8:45 PM'},
                {id:'1260', name:'9:00 PM'},
                {id:'1275', name:'9:15 PM'},
                {id:'1290', name:'9:30 PM'},
                {id:'1305', name:'9:45 PM'},
                {id:'1320', name:'10:00 PM'},
                {id:'1335', name:'10:15 PM'},
                {id:'1350', name:'10:30 PM'},
                {id:'1365', name:'10:45 PM'},
                {id:'1380', name:'11:00 PM'},
                {id:'1395', name:'11:15 PM'},
                {id:'1410', name:'11:30 PM'},
                {id:'1425', name:'11:45 PM'},
                {id:'-60', name:'Every Hour'},
                {id:'-720', name:'Every 12 Hours'},
                {id:'-1440', name:'Every 24 Hours'},
                {id:'e', name:'Every...'}
            ]},
            {name:'rboot_every', label:'', xtype:'number', minValue:60, maxValue:86400, disabled:true, placeholder:'60~86400 '+$lang.VAR_TIME_ARR[2]}
        ]},
        {name:'ck_days', label:$lang.EXEC_INTERVAL, xtype:'ck_days'}
    ]},
    {title:$lang.TIME_SETTING, collapsible:true, collapsed:false, xtype:'fieldset', id:'params_tab_timezone', items:[
        {name:'tm_sel', label:$lang.TIMEZONE, xtype:'combo', data:get_router_timezone()},
        {name:'tm_dst', label:$lang.TM_DST, xtype:'checkbox', hidden:true, disabled:true},
        {name:'tm_tz', label:$lang.TM_TZ, hidden:true},
        {name:'ntp_updates', label:$lang.NTP_UPDATES, xtype:'combo', data:[
            {id:'-1', name:$lang.NEVER_SYNC},
            {id:'0',  name:$lang.UPDATE_ON_STARTUP},
            {id:'1',  name:$lang.EVERY_N_HOURS.replace('n','1')},
            {id:'2',  name:$lang.EVERY_N_HOURS.replace('n','2')},
            {id:'4',  name:$lang.EVERY_N_HOURS.replace('n','4')},
            {id:'6',  name:$lang.EVERY_N_HOURS.replace('n','6')},
            {id:'8',  name:$lang.EVERY_N_HOURS.replace('n','8')},
            {id:'12',  name:$lang.EVERY_N_HOURS.replace('n','12')},
            {id:'24',  name:$lang.EVERY_1_DAY}
        ]},
        {name:'ntp_tdod', label:$lang.NTP_TDOD, xtype:'checkbox', hidden:true},
        {name:'ntp_server', label:$lang.NTP_SERVER, emptyText:$lang.IP_DOMAIN_SPLIT_BY_SPACE, hidden:true}
    ]},
    {title: $lang.VAR_TERM_PARAM_PLATFORM, id:'params_tab_m2m', items:[
        // {name:'m2m_mode', label:$lang.IS_ENABLE, xtype:'checkbox', inputValue:'enable', uncheckedValue:'disable'},
        {name:'m2m_error_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.RESTART_M2M}, {id:'1', name:$lang.RECONNECT_NETWORK}, {id:'2', name:$lang.REBOOT_SYSTEM}]},
        {name:'m2m_product_id', label:$lang.DEVICE_ID, length:[1,14]},
        {label:$lang.M2M_SERVER_PORT, xtype:'fieldcontainer', layout:'hbox', items:[
            {name:'m2m_server_domain', label:'', emptyText:'IP'},
            {name:'m2m_server_port', label:'', xtype:'number', emptyText:'Port'}
        ]},
        {name:'m2m_heartbeat_intval', label:$lang.M2M_HEARTBEAT_INTVAL, xtype:'number', minValue:1, emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'m2m_heartbeat_retry', label:$lang.M2M_HEARTBEAT_RETRY, xtype:'number', minValue:10, maxValue:1000, emptyText:'10-1000'},
        {name:'n2n_bootmode', label:$lang.N2N_BOOTMODE, xtype:'combo', data:[{id:'0', name:$lang.REMOTE_CONNECT}, {id:'1', name:$lang.AUTO_CONNECT}]},
        {name:'n2n_server', label:$lang.REMOTE_CHANNEL_PORT, xtype:'number', minValue:1024, maxValue:65535, emptyText:'1024-65535'},
        {name:'n2n_online', label:$lang.N2N_STATUS, xtype:'combo', data:[{id:'0', name:'Offline'}, {id:'1', name:'Online'}], disabled:true},
        {name:'n2n_ipaddr', label:$lang.N2N_IPADDR, disabled:true}
    ]},
    {title: $lang.ACCESS_SETTINGS, id:'params_tab_access_settings', items:[
        {name:'remote_management',    label:$lang.REMOTE_ACCESS, xtype:'combo', data:[{id:'0', name:$lang.VAR_CLOSE}, {id:'1', name:'HTTP'}, {id:'2', name:'HTTPS'}]},
        {name:'http_wanport',   label:$lang.ACCESS_PORT},
        {name:'rmgt_sip',       label:$lang.REMOTE_ACCESS_ALLOW_IPS,    emptyText:$lang.REMOTE_ACCESS_ALLOW_IPS_TIPS},
        {name:'http_wireless',  label:$lang.ALLOW_WIRELESS_ACCESS, xtype:'checkbox'},
        {name:'telnetd_remote', label:$lang.ENABLE_TELNET_REMOTE_ACCESS, xtype:'checkbox'}
    ]}
];