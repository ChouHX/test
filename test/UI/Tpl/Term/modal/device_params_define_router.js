$.gf.device_params_define_router_url = {
    '-100':'',
    '0':'',
    '3322':'http://www.3322.org/',
    '3322-static':'http://www.3322.org/',
    'dnsexit':'http://www.dnsexit.com/',
    'dnsomatic':'http://www.dnsomatic.com/',
    'dyndns':'http://www.dyndns.com/',
    'dyndns-static':'http://www.dyndns.com/',
    'dyndns-custom':'http://www.dyndns.com/',
    'sdyndns':'http://www.dyndns.com/',
    'sdyndns-static':'http://www.dyndns.com/',
    'sdyndns-custom':'http://www.dyndns.com/',
    'dyns':'http://www.dyns.cx/',
    'easydns':'http://www.easydns.com/',
    'seasydns':'http://www.easydns.com/',
    'editdns':'http://www.editdns.net/',
    'everydns':'http://www.everydns.net/',
    'minidns':'http://www.minidns.net/',
    'enom':'http://www.enom.com/',
    'afraid':'http://freedns.afraid.org/',
    'heipv6tb':'http://www.tunnelbroker.net/',
    'ieserver':'http://www.ieserver.net/',
    'namecheap':'http://www.namecheap.com/',
    'noip':'http://www.no-ip.com/',
    'opendns':'http://www.opendns.com/',
    'tzo':'http://www.tzo.com/',
    'zoneedit':'http://www.zoneedit.com/',
    'szoneedit':'http://www.zoneedit.com/',
    'custom':'',
},
$.gf.device_params_define_router = [
    {title:$lang.WAN_NETWORK, id:'params_tab_wan', items:[
        {name:'wan_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'disabled', name:$lang.VAR_CLOSE}, {id:'dhcp', name:$lang.DYNAMICALLY_GET_ADDRESS}, {id:'pppoe', name:$lang.PPPOE_DIALING}, {id:'static', name:$lang.STATIC_ADDRESS}]},
        {name:'ppp_username', label:$lang.VAR_USER_NAME, hidden:true},
        {name:'ppp_passwd', label:$lang.VAR_PASSWD, hidden:true},
        {name:'ppp_service', label:$lang.SERVICE_NAME, hidden:true},
        {name:'wan_demand', label:$lang.DIAL_MODE, xtype:'combo', data:[{id:'1', name:$lang.DIAL_ON_DEMAND}, {id:'0', name:$lang.LINK_RETENTION}], hidden:true},
        {name:'ppp_redialperiod', label:$lang.CHECK_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
        {name:'ppp_idletime', label:$lang.MAXIMUM_IDLE_TIME, xtype:'number', emptyText:$lang.VAR_TIME_ARR[2], hidden:true},
        {name:'wan1_ipaddr', label:$lang.VAR_IP, hidden:true},
        {name:'wan1_netmask', label:$lang.LAN_NETMASK, hidden:true},
        {name:'wan1_gateway', label:$lang.VAR_GATEWAY, hidden:true},
        {name:'wan1_mtu', label:$lang.MTU, xtype:'number', hidden:true, emptyText:$lang.IS_THE_SYSTEM_DEFAULT},
        {name:'ppp_mlppp', label:$lang.MULTILINK_OVERLAY, xtype:'checkbox', hidden:true},
        {name:'dns_1', label:$lang.WAN_DNS, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP, hidden:true},
        {name:'dns_2', label:$lang.WAN_DNS2, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP, hidden:true}

    ]},
    {title: $lang.VAR_TERM_PARAM_3G, id:'params_tab_3g', items:[
        {name:'enable_modem', label:$lang.ENABLE_MODULE, xtype:'checkbox'},
        // {name:'enable_modem2', label:$lang.ENABLE_MODULE2, xtype:'checkbox'},
        {title:$lang.BASIC_PARAMETER_SETTING, id: 'basicSet', collapsible: true, collapsed: true, xtype:'fieldset', items:[
            // {name:'lte_use_ppp', label:$lang.ENABLE_PPP_MODE, xtype:'checkbox'},
            {name:'PingEnable', label:$lang.PINGENABLE, xtype:'checkbox'},
            {name:'UtmsPingAddr', label:$lang.UTMSPINGADDR, emptyText:$lang.VAR_TERM_PARAM_CORRECT_IPDOMAIN, hidden:true},
            {name:'UtmsPingAddr1', label:$lang.UTMSPINGADDR1, emptyText:$lang.VAR_TERM_PARAM_CORRECT_IPDOMAIN, hidden:true},
            {name:'PingInterval', label:$lang.PINGINTERVAL, xtype:'number', minValue:1, maxValue:1440, emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'PingMax', label:$lang.PINGMAX, xtype:'number', minValue:1, maxValue:1440, hidden:true},
            {name:'icmp_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.REDIAL}, {id:'1', name:$lang.REBOOT_SYSTEM}], hidden:true},
            {name:'rx_tx_enable', label:$lang.RX_TX_ENABLE, xtype:'checkbox'},
            {name:'rx_tx_mode', label:$lang.RX_TX_MODE, xtype:'combo', data:[{id:'0', name:'Rx'}, {id:'1', name:'Tx'}, {id:'2', name:'Tx & Rx'}], hidden:true},
            {name:'rx_tx_check_int', label:$lang.PINGINTERVAL, xtype:'number', minValue:1, maxValue:1440, emptyText:$lang.VAR_TIME_ARR[2], hidden:true},
            {name:'rx_tx_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.REDIAL}, {id:'1', name:$lang.REBOOT_SYSTEM}], hidden:true},
            {name:'modem_mtu', label:$lang.MTU, xtype:'number', emptyText:$lang.IS_THE_SYSTEM_DEFAULT},
            {label:$lang.CIMI_SENT_TO, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'tcp_server', label:''},
                {name:'tcp_port', label:'', emptyText:$lang.VAR_PORT}
            ]},
            {name:'smspasswd', label:$lang.SMS_VERIFICATION_CODE},
            {name:'cops_oper', label:$lang.CARRIER_LOCK, emptyText:$lang.EXAMPLE_46001}, 
        ]},
        {title:'SIM 1', id: 'SIM1',  collapsible:true, collapsed: true, xtype:'fieldset', items:[
            {name:'cellType', label:'SIM 1 '+$lang.NET_MODE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'LTE(FDD/TDD)'}, {id:'2', name:'3G(WCDMA/TD-SCDMA/HSPA)'}, {id:'3', name:'3G(CDMA 2000/CDMA 1x)'}]},
            {name:'CelldialPincode', label:'SIM 1 PIN', length:[0,6]},
            {name:'CelldialApn', label:$lang.CELLDIALAPN, length:[0,60]},
            {name:'CelldialUser', label:$lang.CELLDIALUSER, length:[0,60]},
            {name:'CelldialPwd', label:$lang.CELLDIALPWD, length:[0,60]},
            {name:'CelldialNum', label:'SIM 1 '+$lang.CELLDIALNUM, length:[0,25]},
            {name:'auth_type', label:'SIM 1 '+$lang.AUTH_TYPE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'PAP'}, {id:'2', name:'CHAP'}, {id:'3', name:'MS-CHAP'}, {id:'4', name:'MS-CHAPv2'}]},
            {name:'local_ip', label:'SIM 1 '+$lang.LOCAL_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP}
        ]},
        {title:'SIM 2', id: 'SIM2', collapsible:true, collapsed: true, xtype:'fieldset', items:[
            {name:'cellType2', label:'SIM 2 '+$lang.NET_MODE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'LTE(FDD/TDD)'}, {id:'2', name:'3G(WCDMA/TD-SCDMA/HSPA)'}, {id:'3', name:'3G(CDMA 2000/CDMA 1x)'}]},
            {name:'CelldialPincode2', label:'SIM 2 PIN', length:[0,6]},
            {name:'CelldialApn2', label:$lang.CELLDIALAPN2, length:[0,60]},
            {name:'CelldialUser2', label:$lang.CELLDIALUSER2, length:[0,60]},
            {name:'CelldialPwd2', label:$lang.CELLDIALPWD2, length:[0,60]},
            {name:'CelldialNum2', label:'SIM 2 '+$lang.CELLDIALNUM, length:[0,25]},
            {name:'auth_type2', label:'SIM 2 '+$lang.AUTH_TYPE, xtype:'combo', data:[{id:'0', name:'Auto'}, {id:'1', name:'PAP'}, {id:'2', name:'CHAP'}, {id:'3', name:'MS-CHAP'}, {id:'4', name:'MS-CHAPv2'}]},
            {name:'local_ip2', label:'SIM 2 '+$lang.LOCAL_IP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP}
        ]}
    ]},
    {title:$lang.VAR_TERM_PARAM_LAN, id:'params_tab_lan', items:[
        {title:'LAN 1', xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'lan_ipaddr', label:$lang.LAN_IPADDR, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan_netmask', label:$lang.LAN_NETMASK, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan_proto', label:$lang.LAN_PROTO, xtype:'combo', data:[{id:'dhcp', name:$lang.VAR_ENABLE}, {id:'static', name:$lang.VAR_DISABLE}]},
            {label:$lang.VAR_IP_RANGE, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'dhcpd_startip', label:'', emptyText:$lang.DHCP_STARTIP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
                {name:'dhcpd_endip', label:'', emptyText:$lang.DHCP_ENDIP, vtype:'dhcpIpRange'}
            ]},
            {name:'dhcp_lease', label:$lang.DHCP_LEASE, xtype:'number', minValue:0, maxValue:10080, emptyText:$lang.VAR_MINUTE}
        ]},
        {title:'LAN 2', xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'lan1_ipaddr', label:$lang.LAN_IPADDR +' 2', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan1_netmask', label:$lang.LAN_NETMASK +' 2', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan1_proto', label:$lang.LAN_PROTO, xtype:'combo', data:[{id:'dhcp', name:$lang.VAR_ENABLE}, {id:'static', name:$lang.VAR_DISABLE}]},
            {label:$lang.VAR_IP_RANGE, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'dhcpd1_startip', label:'', emptyText:$lang.DHCP_STARTIP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
                {name:'dhcpd1_endip', label:'', emptyText:$lang.DHCP_ENDIP, vtype:'dhcpIpRange'}
            ]},
            {name:'dhcp1_lease', label:$lang.DHCP_LEASE, xtype:'number', minValue:0, maxValue:10080, emptyText:$lang.VAR_MINUTE}
        ]},
        {title:'LAN 3', xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'lan2_ipaddr', label:$lang.LAN_IPADDR +' 3', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan2_netmask', label:$lang.LAN_NETMASK +' 3', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan2_proto', label:$lang.LAN_PROTO, xtype:'combo', data:[{id:'dhcp', name:$lang.VAR_ENABLE}, {id:'static', name:$lang.VAR_DISABLE}]},
            {label:$lang.VAR_IP_RANGE, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'dhcpd2_startip', label:'', emptyText:$lang.DHCP_STARTIP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
                {name:'dhcpd2_endip', label:'', emptyText:$lang.DHCP_ENDIP, vtype:'dhcpIpRange'}
            ]},
            {name:'dhcp2_lease', label:$lang.DHCP_LEASE, xtype:'number', minValue:0, maxValue:10080, emptyText:$lang.VAR_MINUTE}
        ]},
        {title:'LAN 4', xtype:'fieldset', collapsible:true, collapsed:true, items:[
            {name:'lan3_ipaddr', label:$lang.LAN_IPADDR +' 4', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan3_netmask', label:$lang.LAN_NETMASK +' 4', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'lan3_proto', label:$lang.LAN_PROTO, xtype:'combo', data:[{id:'dhcp', name:$lang.VAR_ENABLE}, {id:'static', name:$lang.VAR_DISABLE}]},
            {label:$lang.VAR_IP_RANGE, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'dhcpd3_startip', label:'', emptyText:$lang.DHCP_STARTIP, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
                {name:'dhcpd3_endip', label:'', emptyText:$lang.DHCP_ENDIP, vtype:'dhcpIpRange'}
            ]},
            {name:'dhcp3_lease', label:$lang.DHCP_LEASE, xtype:'number', minValue:0, maxValue:10080, emptyText:$lang.VAR_MINUTE}
        ]},
        {name:'custom_dns_enable', label:$lang.USE_CUSTOM_DNS, xtype:'checkbox'},
        {name:'custom_dns_part1', label:$lang.PRIMARY_DNS_SERVER, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP, hidden:true},
        {name:'custom_dns_part2', label:$lang.ALTERNATE_DNS_SERVER, regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP, hidden:true}
    ]},
    // {title: $lang.VLAN, id:'params_tab_vlan', items:[]},
    {title: $lang.LINK_SCHEDULING, id:'params_tab_link_scheduling', items:[]},
    {title: $lang.DYNAMIC_DOMAIN_NAME, id:'params_tab_dynamic_domain_name', items:[
        {title:$lang.DYNAMIC_DOMAIN_NAME, collapsible: true, xtype:'fieldset', items:[
            {name:'ddnsx_ip_type', label:$lang.VAR_IP, xtype:'combo', data:[{id:'wan', name: $lang.RECOMMENDED_USE}, {id:'custom', name: $lang.CUSTOM_IP_ADDRESS}]},
            {name:'ddnsx_ip', label:$lang.CUSTOM_IP_ADDRESS, hidden:true},
            {name:'ddnsx_refresh', label:$lang.AUTO_REFRESH_EVERY, xtype:'number', emptyText:$lang.MINUTES_0},
        ]},
        {title:$lang.DYNAMIC_DOMAIN_NAME + '1', collapsible: true, xtype:'fieldset', items:[
            {name:'service0', label:$lang.SERVICE_PROVIDER, xtype:'combo', data:[
                 {id:'0', name: $lang.VAR_NONE}, 
                 {id:'3322', name: '3322'},
                 {id:'3322-static', name: $lang.STATIC_3322},
                 {id:'dnsexit', name: $lang.DNS_EXIT},
                 {id:'dnsomatic', name: $lang.DNS_O_MATIC},
                 {id:'dyndns', name: $lang.DYNDNS_DYNAMIC},
                 {id:'dyndns-static', name: $lang.DYNDNS_STATIC},
                 {id:'dyndns-custom', name: $lang.DYNDNS_CUSTOM},
                 {id:'sdyndns', name: $lang.DYNDNS_HTTPS_DYNAMIC},
                 {id:'sdyndns-static', name: $lang.DYNDNS_HTTPS_STATIC},
                 {id:'sdyndns-custom', name: $lang.DYNDNS_HTTPS_CUSTOM},
                 {id:'dyns', name: $lang.DYNS},
                 {id:'easydns', name: $lang.EASYDNS},
                 {id:'seasydns', name: $lang.EASYDNS_HTTPS},
                 {id:'editdns', name: $lang.EDITDNS},
                 {id:'everydns', name: $lang.EVERYDNS},
                 {id:'minidns', name: $lang.MINIDNS},
                 {id:'enom', name: $lang.ENOM},
                 {id:'afraid', name: $lang.FREEDNS_AFRAID_ORG},
                 {id:'heipv6tb', name: $lang.HE_NET_IPV6_TUNNEL_BROKER},
                 {id:'ieserver', name: $lang.IESERVER_NET},
                 {id:'namecheap', name: $lang.NAMECHEAP},
                 {id:'noip', name: $lang.NO_IP_COM},
                 {id:'opendns', name: $lang.OPENDNS},
                 {id:'tzo', name: $lang.TZO},
                 {id:'zoneedit', name: $lang.ZONEEDIT},
                 {id:'szoneedit', name: $lang.ZONEEDIT_HTTPS},
                 {id:'custom', name: $lang.CUSTOM_URL},
            ]},
            {name:'hyperlink0', label:$lang.DOMAIN_URL, xtype:'hyperlink' , hidden:true},
            {name:'cust0', label:$lang.DOMAIN_URL, hidden:true},
            {name:'hosttop0', label:$lang.WAN_HOSTNAME, hidden:true},
            {name:'user0', label:$lang.VAR_USER_NAME, hidden:true},
            {name:'pass0', label:$lang.PPTP_CLIENT_PASSWD, hidden:true},
            {name:'host0', label:$lang.WAN_HOSTNAME, hidden:true},
            {name:'wild0', label:$lang.WILDCARD, xtype:'checkbox', hidden:true},
            {name:'mx0', label:$lang.MX, hidden:true},
            {name:'bmx0', label:$lang.BACKUP_MX, xtype:'checkbox', hidden:true},
            {name:'ddnsx_save0', label:$lang.SAVE_STATE_WHEN_IP_CHANGES, xtype:'checkbox', hidden:true},
            {name:'opendns0', label:$lang.USE_AS_DNS, hidden:true},
            {name:'afraid0', label:$lang.TOKEN_URL, hidden:true},
            {name:'force0', label:$lang.FORCE_NEXT_UPDATE, xtype:'checkbox', hidden:true},
        ]},
        {title:$lang.DYNAMIC_DOMAIN_NAME + '2', collapsible: true, xtype:'fieldset', items:[
            {name:'service1', label:$lang.SERVICE_PROVIDER, xtype:'combo', data:[
                 {id:'0', name: $lang.VAR_NONE}, 
                 {id:'3322', name: '3322'},
                 {id:'3322-static', name: $lang.STATIC_3322},
                 {id:'dnsexit', name: $lang.DNS_EXIT},
                 {id:'dnsomatic', name: $lang.DNS_O_MATIC},
                 {id:'dyndns', name: $lang.DYNDNS_DYNAMIC},
                 {id:'dyndns-static', name: $lang.DYNDNS_STATIC},
                 {id:'dyndns-custom', name: $lang.DYNDNS_CUSTOM},
                 {id:'sdyndns', name: $lang.DYNDNS_HTTPS_DYNAMIC},
                 {id:'sdyndns-static', name: $lang.DYNDNS_HTTPS_STATIC},
                 {id:'sdyndns-custom', name: $lang.DYNDNS_HTTPS_CUSTOM},
                 {id:'dyns', name: $lang.DYNS},
                 {id:'easydns', name: $lang.EASYDNS},
                 {id:'seasydns', name: $lang.EASYDNS_HTTPS},
                 {id:'editdns', name: $lang.EDITDNS},
                 {id:'everydns', name: $lang.EVERYDNS},
                 {id:'minidns', name: $lang.MINIDNS},
                 {id:'enom', name: $lang.ENOM},
                 {id:'afraid', name: $lang.FREEDNS_AFRAID_ORG},
                 {id:'heipv6tb', name: $lang.HE_NET_IPV6_TUNNEL_BROKER},
                 {id:'ieserver', name: $lang.IESERVER_NET},
                 {id:'namecheap', name: $lang.NAMECHEAP},
                 {id:'noip', name: $lang.NO_IP_COM},
                 {id:'opendns', name: $lang.OPENDNS},
                 {id:'tzo', name: $lang.TZO},
                 {id:'zoneedit', name: $lang.ZONEEDIT},
                 {id:'szoneedit', name: $lang.ZONEEDIT_HTTPS},
                 {id:'custom', name: $lang.CUSTOM_URL},
            ]},
            {name:'hyperlink1', label:$lang.DOMAIN_URL, xtype:'hyperlink' , hidden:true},
            {name:'cust1', label:$lang.DOMAIN_URL, hidden:true},
            {name:'hosttop1', label:$lang.WAN_HOSTNAME, hidden:true},
            {name:'user1', label:$lang.VAR_USER_NAME, hidden:true},
            {name:'pass1', label:$lang.PPTP_CLIENT_PASSWD, hidden:true},
            {name:'host1', label:$lang.WAN_HOSTNAME, hidden:true},
            {name:'wild1', label:$lang.WILDCARD, xtype:'checkbox', hidden:true},
            {name:'mx1', label:$lang.MX, hidden:true},
            {name:'bmx1', label:$lang.BACKUP_MX, xtype:'checkbox', hidden:true},
            {name:'ddnsx_save1', label:$lang.SAVE_STATE_WHEN_IP_CHANGES, xtype:'checkbox', hidden:true},
            {name:'opendns1', label:$lang.USE_AS_DNS, hidden:true},
            {name:'afraid1', label:$lang.TOKEN_URL, hidden:true},
            {name:'force1', label:$lang.FORCE_NEXT_UPDATE, xtype:'checkbox', hidden:true},
        ]},
    ]},
    {title: $lang.ROUTING_TABLE_SETTINGS, id:'params_tab_route_table_settings', items:[
        {grid_id:'routes_static', xtype:'grid_container'},
        {title:$lang.OSPF, collapsible: true, xtype:'fieldset', items:[
            {name:'ospf_on', label:$lang.ENABLE_OSPF, xtype:'checkbox'},
            {name:'ospf_rfc1583', label:'RFC1583', xtype:'checkbox'},
            {name:'ospf_id', label:$lang.ROUTER_ID, xtype:'number'},
        ]},
        {grid_id:'ospf', xtype:'grid_container'},
        {title:$lang.OTHER_SETTING, collapsible: true, xtype:'fieldset', items:[
            {name:'wk_mode', label:$lang.NET_MODE, xtype:'combo', data:[{id:'gateway', name: $lang.VAR_GATEWAY}, {id:'router', name:$lang.ROUTER}]},
            {name:'dr_setting', label:'RIPv1 & v2', xtype:'combo', data:[{id:'0', name: $lang.VAR_CLOSE}, {id:'1', name: 'LAN'}, {id:'2', name: 'WAN'}, {id:'3', name: 'Both'}]},
            {name:'emf_enable', label:$lang.EFFICIENT_MULTICAST_FORWARDING, xtype:'checkbox'},
            {name:'dhcp_routes', label:$lang.DHCP_ROUTING, xtype:'checkbox'},
            {name:'lan_stp', label:$lang.SPANNING_TREE_PROTOCOL, xtype:'checkbox'},
        ]},
    ]},
    {title: $lang.BASIC_PARAMETER_SETTING, id:'params_tab_basic_parameter_settings', items:[
            {name:'wl0_vifs', label:'wl0_vifs', hidden:true},
            {name:'wl1_vifs', label:'wl1_vifs', hidden:true},
            {name:'wl0_radio', label:$lang.ENABLE_WIRELESS, xtype:'checkbox'},
            {name:'wl0_mode', label:$lang.WIRELESS_MODE, xtype:'combo', data:[{id:'ap', name: $lang.WIRELESS_ACCESS_POINT_AP}, {id:'sta', name:$lang.WIRELESS_CLIENT}, {id:'wet', name:$lang.WIRELESS_BRIDGE}]},
            {name:'wl0_net_mode', label:$lang.OPERATING_MODE, xtype:'combo', data:[{id:'mixed', name: $lang.VAR_AUTO}, {id:'b-only', name:$lang.ONLY_802B}, {id:'g-only', name:$lang.ONLY_802G}, {id:'bg-mixed', name:$lang.B_G_MIX}, {id:'n-only', name:$lang.ONLY_802N}], hidden:true},
            {name:'wl0_ssid', label:$lang.SSID, hidden:true},
            // {name:'w10_bridging', label:$lang.BRIDGING, xtype:'combo', data:[{id:'0', name:'LAN (br0)'}, {id:'1', name:'LAN1 (br1)'}, {id:'2', name:'LAN2 (br2)'}, {id:'3', name:'LAN3 (br3)'}]},
            {name:'wl0_closed', label:$lang.BROADCAST_SSID, xtype:'checkbox', hidden:true},
            {name:'wl0_channel', label:$lang.CHANNEL, xtype:'combo', data:[
                {id:'0', name:$lang.VAR_AUTO},
                {id:'1', name:'1 - 2.412 GHz'},
                {id:'2', name:'2 - 2.417 GHz'},
                {id:'3', name:'3 - 2.422 GHz'},
                {id:'4', name:'4 - 2.427 GHz'},
                {id:'5', name:'5 - 2.432 GHz'},
                {id:'6', name:'6 - 2.437 GHz'},
                {id:'7', name:'7 - 2.442 GHz'},
                {id:'8', name:'8 - 2.447 GHz'},
                {id:'9', name:'9 - 2.452 GHz'},
                {id:'10', name:'10 - 2.457 GHz'},
                {id:'11', name:'11 - 2.462 GHz'},
                {id:'12', name:'12 - 2.467 GHz'},
                {id:'13', name:'13 - 2.472 GHz'}
                ], hidden:true},
            {name:'wl0_nbw_cap', label:$lang.BANDWIDTH, xtype:'combo', data:[{id:'0', name: $lang.MHZ_20}, {id:'1', name:$lang.MHZ_40}], hidden:true},
            {name:'wl0_nctrlsb', label:$lang.CONTROL_SIDEBAND, xtype:'combo', data:[{id:'lower', name: $lang.PRIORITY_LEVELS[3]}, {id:'upper', name:$lang.PRIORITY_LEVELS[1]}], hidden:true},
            {name:'wl0_maxassoc', label:$lang.MAXIMUM_NUMBER_OF_WIRELESS_CLIENTS, emptyText:$lang.RANGE_1_255, hidden:true},
            {name:'wl0_security_mode', label:$lang.SECURITY_OPTIONS, xtype:'combo', data:[{id:'disabled', name: $lang.VAR_CLOSE}, {id:'wep', name:$lang.WEP}, {id:'wpa_personal', name:$lang.WPA_PERSONAL}, {id:'wpa_enterprise', name:$lang.WPA_ENTERPRISE}, {id:'wpa2_personal', name:$lang.WPA2_PERSONAL}, {id:'wpa2_enterprise', name:$lang.WPA2_ENTERPRISE}, {id:'wpaX_personal', name:$lang.WPA_WPA2_PERSONAL}, {id:'wpaX_enterprise', name:$lang.WPA_WPA2_ENTERPRISE}, {id:'radius', name:$lang.RADIUS}], hidden:true},
            {name:'wl0_crypto', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'tkip', name: $lang.TKIP}, {id:'aes', name:$lang.AES}, {id:'tkip+aes', name:$lang.TKIP_AES}], hidden:true},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_wpa_psk', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0_wpa_psk')
                }}
            ]},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_radius_key', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0_radius_key')
                }}
            ]},
            {name:'wl0_wpa_gtk_rekey', label:$lang.GROUP_KEY_UPDATE, emptyText:$lang.VAR_SECOND, hidden:true},
            {label: $lang.RADIUS_SERVER, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_radius_ipaddr', label:''},
                {name:'wl0_radius_port', label:''},
            ]}, 
            {name:'wl0_wep_bit', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'128', name: $lang.BITS_128}, {id:'64', name:$lang.BITS_64}], hidden:true},
            {label:$lang.PASSWORD_SEED, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_passphrase', label:'', length:[8,64]},
                {label:"", xtype:'fieldcontainer', itemw:"4", layout:'hbox', items:[
                    {text:$lang.GENERATE, xtype:'button', handler:function(){
                        if ($('[id="tpid_wl0_passphrase"]').val().length < 3) {
                            alert($lang.INVALID_LENGTH);
                        }else{
                            generate_wep(0);
                        }
                    }},
                    {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                        random_wep(0)
                    }}
                ]}
            ]},
            {label: $lang.VAR_PASSWD + '1', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_key1', label:''},
                {name:'wl0_wepidx', label:'', xtype:'radio', checked: 'checked'},
            ]},
            {label: $lang.VAR_PASSWD + '2', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_key2', label:''},
                {name:'wl0_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '3', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_key3', label:''},
                {name:'wl0_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '4', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0_key4', label:''},
                {name:'wl0_wepidx', label:'', xtype:'radio'},
            ]},
            {name:'wl0_sta_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'dhcp', name: $lang.DYNAMICALLY_GET_ADDRESS}, {id:'static', name:$lang.STATIC_ADDRESS}], hidden:true},
            {name:'wl0_sta_mtu', label:$lang.MTU, emptyText:$lang.IS_THE_SYSTEM_DEFAULT, hidden:true},
            {name:'wl0_sta_ipaddr', label:$lang.VAR_IP, hidden:true},
            {name:'wl0_sta_netmask', label:$lang.LAN_NETMASK, hidden:true},
            {name:'wl0_sta_gateway', label:$lang.VAR_GATEWAY, hidden:true},
            {name:'wl0_sta_dns_1', label:$lang.PRIMARY_DNS_SERVER, hidden:true},
            {name:'wl0_sta_dns_2', label:$lang.ALTERNATE_DNS_SERVER, hidden:true},
    ]},
    {title: $lang.BASIC_PARAMETER_SETTING, id:'params_tab_basic_parameter_settings1', items:[
            // {name:'wl0.1_bss_enabled', label:$lang.VAR_TERM_TERM_PARAMS_ACTIVATE, xtype:'checkbox'},
            {name:'wl0.1_radio', label:$lang.ENABLE_WIRELESS, xtype:'checkbox'},
            {name:'wl0.1_mode', label:$lang.WIRELESS_MODE, xtype:'combo', data:[{id:'ap', name: $lang.WIRELESS_ACCESS_POINT_AP}, {id:'sta', name:$lang.WIRELESS_CLIENT}, {id:'wet', name:$lang.WIRELESS_BRIDGE}]},
            // {name:'wl0.1_net_mode', label:$lang.OPERATING_MODE, xtype:'combo', data:[{id:'mixed', name: $lang.VAR_AUTO}, {id:'b-only', name:$lang.ONLY_802B}, {id:'g-only', name:$lang.ONLY_802G}, {id:'bg-mixed', name:$lang.B_G_MIX}, {id:'n-only', name:$lang.ONLY_802N}], hidden:true},
            {name:'wl0.1_ssid', label:$lang.SSID, hidden:true},
            // {name:'w10.1_bridging', label:$lang.BRIDGING, xtype:'combo', data:[{id:'0', name:'LAN (br0)'}, {id:'1', name:'LAN1 (br1)'}, {id:'2', name:'LAN2 (br2)'}, {id:'3', name:'LAN3 (br3)'}]},
            {name:'wl0.1_closed', label:$lang.BROADCAST_SSID, xtype:'checkbox', hidden:true},
            // {name:'wl0.1_nbw_cap', label:$lang.BANDWIDTH, xtype:'combo', data:[{id:'0', name: $lang.MHZ_20}, {id:'1', name:$lang.MHZ_40}], hidden:true},
            // {name:'wl0.1_nctrlsb', label:$lang.CONTROL_SIDEBAND, xtype:'combo', data:[{id:'lower', name: $lang.PRIORITY_LEVELS[3]}, {id:'upper', name:$lang.PRIORITY_LEVELS[1]}], hidden:true},
            // {name:'wl0.1_maxassoc', label:$lang.MAXIMUM_NUMBER_OF_WIRELESS_CLIENTS, emptyText:$lang.RANGE_1_255, hidden:true},
            {name:'wl0.1_security_mode', label:$lang.SECURITY_OPTIONS, xtype:'combo', data:[{id:'disabled', name: $lang.VAR_CLOSE}, {id:'wep', name:$lang.WEP}, {id:'wpa_personal', name:$lang.WPA_PERSONAL}, {id:'wpa_enterprise', name:$lang.WPA_ENTERPRISE}, {id:'wpa2_personal', name:$lang.WPA2_PERSONAL}, {id:'wpa2_enterprise', name:$lang.WPA2_ENTERPRISE}, {id:'wpaX_personal', name:$lang.WPA_WPA2_PERSONAL}, {id:'wpaX_enterprise', name:$lang.WPA_WPA2_ENTERPRISE}, {id:'radius', name:$lang.RADIUS}], hidden:true},
            {name:'wl0.1_crypto', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'tkip', name: $lang.TKIP}, {id:'aes', name:$lang.AES}, {id:'tkip+aes', name:$lang.TKIP_AES}], hidden:true},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_wpa_psk', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.1_wpa_psk')
                }}
            ]},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_radius_key', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.1_radius_key')
                }}
            ]},
            {name:'wl0.1_wpa_gtk_rekey', label:$lang.GROUP_KEY_UPDATE, emptyText:$lang.VAR_SECOND, hidden:true},
            {label: $lang.RADIUS_SERVER, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_radius_ipaddr', label:''},
                {name:'wl0.1_radius_port', label:''},
            ]}, 
            {name:'wl0.1_wep_bit', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'128', name: $lang.BITS_128}, {id:'64', name:$lang.BITS_64}], hidden:true},
            {label:$lang.PASSWORD_SEED, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_passphrase', label:'', length:[8,64]},
                {label:"", xtype:'fieldcontainer', itemw:"4", layout:'hbox', items:[
                    {text:$lang.GENERATE, xtype:'button', handler:function(){
                        if ($('[id="tpid_wl0.1_passphrase"]').val().length < 3) {
                            alert($lang.INVALID_LENGTH);
                        }else{
                            generate_wep(0.1);
                        }
                    }},
                    {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                        random_wep(0.1)
                    }}
                ]}
            ]},
            {label: $lang.VAR_PASSWD + '1', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_key1', label:''},
                {name:'wl0.1_wepidx', label:'', xtype:'radio', checked: 'checked'},
            ]},
            {label: $lang.VAR_PASSWD + '2', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_key2', label:''},
                {name:'wl0.1_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '3', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_key3', label:''},
                {name:'wl0.1_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '4', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.1_key4', label:''},
                {name:'wl0.1_wepidx', label:'', xtype:'radio'},
            ]},
            {name:'wl0.1_sta_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'dhcp', name: $lang.DYNAMICALLY_GET_ADDRESS}, {id:'static', name:$lang.STATIC_ADDRESS}], hidden:true},
            {name:'wl0.1_sta_mtu', label:$lang.MTU, emptyText:$lang.IS_THE_SYSTEM_DEFAULT, hidden:true},
            {name:'wl0.1_sta_ipaddr', label:$lang.VAR_IP, hidden:true},
            {name:'wl0.1_sta_netmask', label:$lang.LAN_NETMASK, hidden:true},
            {name:'wl0.1_sta_gateway', label:$lang.VAR_GATEWAY, hidden:true},
            {name:'wl0.1_sta_dns_1', label:$lang.PRIMARY_DNS_SERVER, hidden:true},
            {name:'wl0.1_sta_dns_2', label:$lang.ALTERNATE_DNS_SERVER, hidden:true},
    ]},
    {title: $lang.BASIC_PARAMETER_SETTING, id:'params_tab_basic_parameter_settings2', items:[
            // {name:'wl0.2_bss_enabled', label:$lang.VAR_TERM_TERM_PARAMS_ACTIVATE, xtype:'checkbox'},
            {name:'wl0.2_radio', label:$lang.ENABLE_WIRELESS, xtype:'checkbox'},
            {name:'wl0.2_mode', label:$lang.WIRELESS_MODE, xtype:'combo', data:[{id:'ap', name: $lang.WIRELESS_ACCESS_POINT_AP}, {id:'sta', name:$lang.WIRELESS_CLIENT}, {id:'wet', name:$lang.WIRELESS_BRIDGE}]},
            // {name:'wl0.2_net_mode', label:$lang.OPERATING_MODE, xtype:'combo', data:[{id:'mixed', name: $lang.VAR_AUTO}, {id:'b-only', name:$lang.ONLY_802B}, {id:'g-only', name:$lang.ONLY_802G}, {id:'bg-mixed', name:$lang.B_G_MIX}, {id:'n-only', name:$lang.ONLY_802N}], hidden:true},
            {name:'wl0.2_ssid', label:$lang.SSID, hidden:true},
            // {name:'w10.2_bridging', label:$lang.BRIDGING, xtype:'combo', data:[{id:'0', name:'LAN (br0)'}, {id:'1', name:'LAN1 (br1)'}, {id:'2', name:'LAN2 (br2)'}, {id:'3', name:'LAN3 (br3)'}]},
            {name:'wl0.2_closed', label:$lang.BROADCAST_SSID, xtype:'checkbox', hidden:true},
            // {name:'wl0.2_nbw_cap', label:$lang.BANDWIDTH, xtype:'combo', data:[{id:'0', name: $lang.MHZ_20}, {id:'1', name:$lang.MHZ_40}], hidden:true},
            // {name:'wl0.2_nctrlsb', label:$lang.CONTROL_SIDEBAND, xtype:'combo', data:[{id:'lower', name: $lang.PRIORITY_LEVELS[3]}, {id:'upper', name:$lang.PRIORITY_LEVELS[1]}], hidden:true},
            // {name:'wl0.2_maxassoc', label:$lang.MAXIMUM_NUMBER_OF_WIRELESS_CLIENTS, emptyText:$lang.RANGE_1_255, hidden:true},
            {name:'wl0.2_security_mode', label:$lang.SECURITY_OPTIONS, xtype:'combo', data:[{id:'disabled', name: $lang.VAR_CLOSE}, {id:'wep', name:$lang.WEP}, {id:'wpa_personal', name:$lang.WPA_PERSONAL}, {id:'wpa_enterprise', name:$lang.WPA_ENTERPRISE}, {id:'wpa2_personal', name:$lang.WPA2_PERSONAL}, {id:'wpa2_enterprise', name:$lang.WPA2_ENTERPRISE}, {id:'wpaX_personal', name:$lang.WPA_WPA2_PERSONAL}, {id:'wpaX_enterprise', name:$lang.WPA_WPA2_ENTERPRISE}, {id:'radius', name:$lang.RADIUS}], hidden:true},
            {name:'wl0.2_crypto', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'tkip', name: $lang.TKIP}, {id:'aes', name:$lang.AES}, {id:'tkip+aes', name:$lang.TKIP_AES}], hidden:true},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_wpa_psk', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.2_wpa_psk')
                }}
            ]},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_radius_key', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.2_radius_key')
                }}
            ]},
            {name:'wl0.2_wpa_gtk_rekey', label:$lang.GROUP_KEY_UPDATE, emptyText:$lang.VAR_SECOND, hidden:true},
            {label: $lang.RADIUS_SERVER, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_radius_ipaddr', label:''},
                {name:'wl0.2_radius_port', label:''},
            ]}, 
            {name:'wl0.2_wep_bit', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'128', name: $lang.BITS_128}, {id:'64', name:$lang.BITS_64}], hidden:true},
            {label:$lang.PASSWORD_SEED, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_passphrase', label:'', length:[8,64]},
                {label:"", xtype:'fieldcontainer', itemw:"4", layout:'hbox', items:[
                    {text:$lang.GENERATE, xtype:'button', handler:function(){
                        if ($('[id="tpid_wl0.2_passphrase"]').val().length < 3) {
                            alert($lang.INVALID_LENGTH);
                        }else{
                            generate_wep(0.2);
                        }
                    }},
                    {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                        random_wep(0.2)
                    }}
                ]}
            ]},
            {label: $lang.VAR_PASSWD + '1', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_key1', label:''},
                {name:'wl0.2_wepidx', label:'', xtype:'radio', checked: 'checked'},
            ]},
            {label: $lang.VAR_PASSWD + '2', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_key2', label:''},
                {name:'wl0.2_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '3', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_key3', label:''},
                {name:'wl0.2_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '4', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.2_key4', label:''},
                {name:'wl0.2_wepidx', label:'', xtype:'radio'},
            ]},
            {name:'wl0.2_sta_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'dhcp', name: $lang.DYNAMICALLY_GET_ADDRESS}, {id:'static', name:$lang.STATIC_ADDRESS}], hidden:true},
            {name:'wl0.2_sta_mtu', label:$lang.MTU, emptyText:$lang.IS_THE_SYSTEM_DEFAULT, hidden:true},
            {name:'wl0.2_sta_ipaddr', label:$lang.VAR_IP, hidden:true},
            {name:'wl0.2_sta_netmask', label:$lang.LAN_NETMASK, hidden:true},
            {name:'wl0.2_sta_gateway', label:$lang.VAR_GATEWAY, hidden:true},
            {name:'wl0.2_sta_dns_1', label:$lang.PRIMARY_DNS_SERVER, hidden:true},
            {name:'wl0.2_sta_dns_2', label:$lang.ALTERNATE_DNS_SERVER, hidden:true},
    ]},
    {title: $lang.BASIC_PARAMETER_SETTING, id:'params_tab_basic_parameter_settings3', items:[
            {name:'wl0.3_radio', label:$lang.ENABLE_WIRELESS, xtype:'checkbox'},
            {name:'wl0.3_mode', label:$lang.WIRELESS_MODE, xtype:'combo', data:[{id:'ap', name: $lang.WIRELESS_ACCESS_POINT_AP}, {id:'sta', name:$lang.WIRELESS_CLIENT}, {id:'wet', name:$lang.WIRELESS_BRIDGE}]},
            // {name:'wl0.3_net_mode', label:$lang.OPERATING_MODE, xtype:'combo', data:[{id:'mixed', name: $lang.VAR_AUTO}, {id:'b-only', name:$lang.ONLY_802B}, {id:'g-only', name:$lang.ONLY_802G}, {id:'bg-mixed', name:$lang.B_G_MIX}, {id:'n-only', name:$lang.ONLY_802N}], hidden:true},
            {name:'wl0.3_ssid', label:$lang.SSID, hidden:true},
            // {name:'w10.3_bridging', label:$lang.BRIDGING, xtype:'combo', data:[{id:'0', name:'LAN (br0)'}, {id:'1', name:'LAN1 (br1)'}, {id:'2', name:'LAN2 (br2)'}, {id:'3', name:'LAN3 (br3)'}]},
            {name:'wl0.3_closed', label:$lang.BROADCAST_SSID, xtype:'checkbox', hidden:true},
            // {name:'wl0.3_nbw_cap', label:$lang.BANDWIDTH, xtype:'combo', data:[{id:'0', name: $lang.MHZ_20}, {id:'1', name:$lang.MHZ_40}], hidden:true},
            // {name:'wl0.3_nctrlsb', label:$lang.CONTROL_SIDEBAND, xtype:'combo', data:[{id:'lower', name: $lang.PRIORITY_LEVELS[3]}, {id:'upper', name:$lang.PRIORITY_LEVELS[1]}], hidden:true},
            // {name:'wl0.3_maxassoc', label:$lang.MAXIMUM_NUMBER_OF_WIRELESS_CLIENTS, emptyText:$lang.RANGE_1_255, hidden:true},
            {name:'wl0.3_security_mode', label:$lang.SECURITY_OPTIONS, xtype:'combo', data:[{id:'disabled', name: $lang.VAR_CLOSE}, {id:'wep', name:$lang.WEP}, {id:'wpa_personal', name:$lang.WPA_PERSONAL}, {id:'wpa_enterprise', name:$lang.WPA_ENTERPRISE}, {id:'wpa2_personal', name:$lang.WPA2_PERSONAL}, {id:'wpa2_enterprise', name:$lang.WPA2_ENTERPRISE}, {id:'wpaX_personal', name:$lang.WPA_WPA2_PERSONAL}, {id:'wpaX_enterprise', name:$lang.WPA_WPA2_ENTERPRISE}, {id:'radius', name:$lang.RADIUS}], hidden:true},
            {name:'wl0.3_crypto', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'tkip', name: $lang.TKIP}, {id:'aes', name:$lang.AES}, {id:'tkip+aes', name:$lang.TKIP_AES}], hidden:true},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_wpa_psk', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.3_wpa_psk')
                }}
            ]},
            {label: $lang.SHARED_KEY, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_radius_key', label:''},
                {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                    random_psk('wl0.3_radius_key')
                }}
            ]},
            {name:'wl0.3_wpa_gtk_rekey', label:$lang.GROUP_KEY_UPDATE, emptyText:$lang.VAR_SECOND, hidden:true},
            {label: $lang.RADIUS_SERVER, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_radius_ipaddr', label:''},
                {name:'wl0.3_radius_port', label:''},
            ]}, 
            {name:'wl0.3_wep_bit', label:$lang.ENCRYPT_TYPE, xtype:'combo', data:[{id:'128', name: $lang.BITS_128}, {id:'64', name:$lang.BITS_64}], hidden:true},
            {label:$lang.PASSWORD_SEED, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_passphrase', label:'', length:[8,64]},
                {label:"", xtype:'fieldcontainer', itemw:"4", layout:'hbox', items:[
                    {text:$lang.GENERATE, xtype:'button', handler:function(){
                        if ($('[id="tpid_wl0.3_passphrase"]').val().length < 3) {
                            alert($lang.INVALID_LENGTH);
                        }else{
                            generate_wep(0.3);
                        }
                    }},
                    {text:$lang.RANDOM_GENERATE, xtype:'button', handler:function(){
                        random_wep(0.3)
                    }}
                ]}
            ]},
            {label: $lang.VAR_PASSWD + '1', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_key1', label:''},
                {name:'wl0.3_wepidx', label:'', xtype:'radio', checked: 'checked'},
            ]},
            {label: $lang.VAR_PASSWD + '2', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_key2', label:''},
                {name:'wl0.3_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '3', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_key3', label:''},
                {name:'wl0.3_wepidx', label:'', xtype:'radio'},
            ]},
            {label: $lang.VAR_PASSWD + '4', xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
                {name:'wl0.3_key4', label:''},
                {name:'wl0.3_wepidx', label:'', xtype:'radio'},
            ]},
            {name:'wl0.3_sta_proto', label:$lang.CONNECTION_TYPE, xtype:'combo', data:[{id:'dhcp', name: $lang.DYNAMICALLY_GET_ADDRESS}, {id:'static', name:$lang.STATIC_ADDRESS}], hidden:true},
            {name:'wl0.3_sta_mtu', label:$lang.MTU, emptyText:$lang.IS_THE_SYSTEM_DEFAULT, hidden:true},
            {name:'wl0.3_sta_ipaddr', label:$lang.VAR_IP, hidden:true},
            {name:'wl0.3_sta_netmask', label:$lang.LAN_NETMASK, hidden:true},
            {name:'wl0.3_sta_gateway', label:$lang.VAR_GATEWAY, hidden:true},
            {name:'wl0.3_sta_dns_1', label:$lang.PRIMARY_DNS_SERVER, hidden:true},
            {name:'wl0.3_sta_dns_2', label:$lang.ALTERNATE_DNS_SERVER, hidden:true},
    ]},
    {title: $lang.PORT_FORWARDING, id:'params_tab_port_forwarding', items:[]},
    {title: $lang.PORT_REDIRECTING, id:'params_tab_port_redirecting', items:[]},
    {title: 'DMZ', id:'params_tab_dmz', items:[
        {name:'dmz_enable', label:$lang.ENABLE_DMZ, xtype:'checkbox'},
        {name:'dmz_ipaddr', label:$lang.DMZ_IPADDR},
        {name:'dmz_sip', label:$lang.SOURCE_ADDRESS_RESTRICTION, emptyText:'(optional; ex: "1.1.1.1", "1.1.1.0/24", "1.1.1.1 - 2.2.2.2" or "me.example.com")'},
        {name:'dmz_cli', label:$lang.LEAVE_CLI_REMOTE_ACCESS, xtype:'checkbox'},
        {name:'dmz_ra', label:$lang.LEAVE_WEB_REMOTE_ACCESS, xtype:'checkbox'}
    ]},
    {title: 'IP_PENETRATION', id:'params_tab_ip_penetration', items:[
        {name:'ippass_enable', label:$lang.VAR_TERM_TERM_PARAMS_ACTIVATE, xtype:'checkbox'},
        {name:'ippass_addr', label:$lang.VAR_DEVICE_MAC},
        {name:'ippass_gateway_static', label:$lang.VAR_GATEWAY},
    ]},
    {title: 'PORT_TRIGGER', id:'params_tab_port_trigger', items:[]},
    {title: $lang.PORTAL, id:'params_tab_portal', items:[
        {name:'xdog_on', label:$lang.IS_ENABLE, xtype:'checkbox'},
        {name:'xdog_auth', label:$lang.AUTH_TYPE, xtype:'combo', data:[{id:'0', name:'NONE'}]},
        {name:'xdog_root', label:$lang.ROUTER_WEB_DIR, xtype:'combo', data:[{id:'0', name:$lang.VAR_DEFAULT}, {id:'1', name:$lang.INTERNAL_STORAGE}, {id:'2', name:$lang.EXTERNAL_STORAGE}]},
        {name:'xdog_whost', label:$lang.XDOG_WHOST, length:[0,255]},
        {name:'xdog_phost', label:$lang.XDOG_PHOST, length:[0,255]},
        {name:'xdog_login_timeout', label:$lang.XDOG_LOGIN_TIMEOUT, xtype:'number', minValue:0, maxValue:1440, emptyText:$lang.VAR_MINUTE},
        {name:'xdog_idle_timeout', label:$lang.CLIENTIDLETIMEOUT_2, xtype:'number', minValue:0, maxValue:1440, emptyText:$lang.VAR_MINUTE},
        {name:'xdog_iglan', label:$lang.PORTAL_IGNORE_LAN, xtype:'checkbox'},
        {name:'xdog_redir', label:$lang.REDIRECTURL_2, length:[0,255]},
        {name:'xdog_trustmac', label:$lang.TRUSTEDMACLIST_2, length:[0,255]},
        {name:'xdog_qos_don', label:$lang.XDOG_QOS_DON, xtype:'checkbox'},
        {name:'xdog_qos_dt', label:$lang.XDOG_QOS_DT, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s', vtype:'xdogQosDt', vtypeIds:'tpid_xdog_qos_dsc'},
        {name:'xdog_qos_ds', label:$lang.XDOG_QOS_DS, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s'},
        {name:'xdog_qos_dsc', label:$lang.XDOG_QOS_DSC, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s', vtype:'xdogQosDt', vtypeIds:'tpid_xdog_qos_ds'},
        {name:'xdog_qos_uon', label:$lang.F_XDOG_QOS_UON, xtype:'checkbox'},
        {name:'xdog_qos_ut', label:$lang.TOTAL_UPLOAD_BANDWIDTH, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s', vtype:'xdogQosDt', vtypeIds:'tpid_xdog_qos_usc'},
        {name:'xdog_qos_us', label:$lang.XDOG_QOS_DS, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s'},
        {name:'xdog_qos_usc', label:$lang.XDOG_QOS_DSC, xtype:'number', minValue:0, maxValue:999999, emptyText:'kbit/s', vtype:'xdogQosDt', vtypeIds:'tpid_xdog_qos_us'}
    ]},
    {title:$lang.SERIAL_APPLICATION, id:'params_tab_serial_port_application', items:[
        {name:'ipoc_mode', label:$lang.IPOC_MODE, xtype:'combo', data:[{id:'serial', name:'Serial'}, {id:'modbus', name:'Modbus'}, {id:'dt', name:'DT'}]},
        {name:'dtu_mode', label:$lang.SERIAL_APP_NETWORK_MODE, xtype:'combo', data:[{id:'disable', name: $lang.VAR_CLOSE}, {id:'server', name: $lang.SERVER}, {id:'client', name: $lang.CLIENT}], hidden:true},
        {name:'modbus_mode', label:$lang.MODE_ENABLED, xtype:'combo', data:[{id:'0', name: $lang.VAR_CLOSE}, {id:'1', name: $lang.VAR_TERM_TERM_PARAMS_ACTIVATE}], hidden:true},
        {label:$lang.CENTRAL_HOST_IP_PORT, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
            {name:'server_ip', label:'', placeholder:'IP'},
            {name:'server_port', label:'', xtype:'number', minValue:1, maxValue:65535, placeholder:'Port'}
        ]},
        {name:'local_port', label:$lang.LOCAL_PORT1, hidden:true},
        {name:'socket_type', label:$lang.SOCKET_TYPE, xtype:'combo', data:[{id:'tcp', name:'TCP'}, {id:'udp', name:'UDP'}], hidden:true},
        {name:'socket_timeout', label:$lang.SOCKET_TIMEOUT1, xtype:'number', minValue:1, emptyText:$lang.VAR_MILLISECOND, hidden:true},
        {name:'serial_timeout', label:$lang.SERIAL_TIMEOUT1, xtype:'number', minValue:1, emptyText:$lang.VAR_MILLISECOND, hidden:true},
        {name:'packet_len', label:$lang.PROTOCOL_PACKAGE_SIZE, xtype:'number', minValue:1, emptyText:$lang.VALUE_LEN, hidden:true},
        // {name:'m2m_product_id', label:$lang.LINK_HEART_PACKET_CONTENT, hidden:true},
        {name:'heartbeat_intval', label:$lang.LINK_HEARTBEAT_INTERVAL, emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
        {name:'modbus_tcp_mode', label:$lang.NET_MODE, xtype:'combo', data:[{id:'0', name: $lang.CLIENT}, {id:'1', name: $lang.SERVER}], hidden:true},
        {label:$lang.CENTRAL_HOST_IP_PORT, xtype:'fieldcontainer', layout:'hbox', hidden:true, items:[
            {name:'modbus_server_domain', label:'', placeholder:'IP'},
            {name:'modbus_server_port', label:'', xtype:'number', minValue:1, maxValue:65535, placeholder:'Port'}
        ]},
        {name:'modbus_bind_port', label:$lang.LOCAL_PORT1, hidden:true},
        {name:'modbus_protocol', label:$lang.MODBUS_PROTOCOL, xtype:'combo', data:[{id:'rtu', name:'RTU'}], hidden:true},
        {name:'modbus_serial_rate', label:$lang.IBST_SERIAL_RATE, xtype:'combo', hidden:true, data:[
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
        {name:'modbus_serial_parity',   label:$lang.IBST_SERIAL_PARITY,   xtype:'combo', data:[{id:'none', name:$lang.VAR_NONE}, {id:'odd', name:$lang.VAR_ODD}, {id:'even', name:$lang.VAR_EVEN}], hidden:true},
        {name:'modbus_serial_databits', label:$lang.IBST_SERIAL_DATABITS, xtype:'combo', data:[{id:'5', name:'5'}, {id:'6', name:'6'}, {id:'7', name:'7'}, {id:'8', name:'8'}], hidden:true},
        {name:'modbus_serial_stopbits', label:$lang.IBST_SERIAL_STOPBITS, xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}], hidden:true},
        {name:'port_type', label:$lang.PORT_TYPE, xtype:'combo', data:[{id:'0', name:'Console'}, {id:'1', name:'RS485/RS232'}]},
        {name:'cache_enable', label:$lang.CACHE_ENABLED, xtype:'checkbox', hidden:true},
        {name:'debug_enable', label:$lang.DEBUG_ENABLE, xtype:'checkbox', hidden:true},
        {name:'debug_num', label:$lang.DEBUG_DATA_LENGTH, xtype:'number', minValue:1, maxValue:1024, hidden:true},
        {name:'serial_rate', label:$lang.IBST_SERIAL_RATE, xtype:'combo', hidden:true, data:[
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
        {name:'serial_parity',   label:$lang.IBST_SERIAL_PARITY,   xtype:'combo', data:[{id:'none', name:$lang.VAR_NONE}, {id:'odd', name:$lang.VAR_ODD}, {id:'even', name:$lang.VAR_EVEN}], hidden:true},
        {name:'serial_databits', label:$lang.IBST_SERIAL_DATABITS, xtype:'combo', data:[{id:'5', name:'5'}, {id:'6', name:'6'}, {id:'7', name:'7'}, {id:'8', name:'8'}], hidden:true},
        {name:'serial_stopbits', label:$lang.IBST_SERIAL_STOPBITS, xtype:'combo', data:[{id:'1', name:'1'}, {id:'2', name:'2'}], hidden:true},
        {name:'dt_phone', label:$lang.TELEPHONE_NUMBER, hidden:true},
        {name:'max_lost_ack', label:$lang.MAX_LOST_ACK, xtype:'number', hidden:true},
        {name:'dt_fail_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:'Restart DTU'}, {id:'1', name:$lang.RECONNECT_NETWORK}, {id:'2', name:$lang.REBOOT_SYSTEM}], hidden:true}
    ]},
    {title: $lang.UPnP_SETTING, id:'params_tab_upnp_setting', items:[
        {name:'enable_upnp', label:$lang.ENABLE_UPNP, xtype:'checkbox'},
        {name:'enable_natpmp', label:$lang.ENABLE_NAT_PMP, xtype:'checkbox'},
        {name:'upnp_clean', label:$lang.AUTOMATICALLY_DELETE_INVALID_RULES, xtype:'checkbox'},
        {name:'upnp_clean_interval', label:$lang.DELETE_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'upnp_clean_threshold', label:$lang.DELETE_THRESHOLD, xtype:'number', emptyText:$lang.REDIRECT},
        {name:'upnp_secure', label:$lang.SAFE_MODE, xtype:'checkbox', emptyText:$lang.UPNP_CLIENTS_CAN_ONLY_MAP_TO_THEIR_IP},
        {name:'upnp_mnp', label:$lang.SHOW_IN_MY_NETWORK_PLACES, xtype:'checkbox'},

    ]},
    {title: $lang.BANDWIDTH_SPEED_LIMIT, id:'params_tab_bandwidth_speed_limit', items:[
        {title:$lang.BANDWIDTH_SPEED_LIMIT, collapsible: true, xtype:'fieldset', items:[
            {name:'new_qoslimit_enable', label:$lang.ENABLE_SPEED_LIMIT, xtype:'checkbox'},
            {name:'qos_ibw', label:$lang.TOTAL_DOWNLOAD_RATE, xtype:'number', placeholder:'kbit/s', hidden:true},
            {name:'qos_obw', label:$lang.TOTAL_UPLOAD_RATE,   xtype:'number', placeholder:'kbit/s', hidden:true},
        ]},
        {grid_id:'new_qoslimit_rules', xtype:'grid_container'},
        {title:$lang.DEFAULT_GROUP, collapsible:true, xtype:'fieldset', name:'qosl_fieldset', items:[
            {name:'qosl_enable', label:$lang.ENABLE_DEFAULT_GROUP, xtype:'checkbox'},
            {name:'qosl_dlr', label:$lang.DOWNLOAD_RATE, xtype:'number', placeholder:'kbit/s', hidden:true},
            {name:'qosl_dlc', label:$lang.MAXIMUM_DOWNLOAD_RATE, xtype:'number', placeholder:'kbit/s', hidden:true},
            {name:'qosl_ulr', label:$lang.UPLOAD_RATE, xtype:'number', placeholder:'kbit/s', hidden:true},
            {name:'qosl_ulc', label:$lang.MAXIMUM_UPLOAD_RATE, xtype:'number', placeholder:'kbit/s', hidden:true}
        ]}
    ]},
    {title: $lang.VRRP, id:'params_tab_vrrp', items:[
        {name:'vrrp_enable', label:$lang.ENABLE_VRRP, xtype:'checkbox'},
        {name:'vrrp_state', label:$lang.NET_MODE, xtype:'combo', data:[{id:'1', name:$lang.HOST}, {id:'0', name:$lang.PREPARE}]},
        {name:'vrrp_vip', label:$lang.VIRTUAL_IP_ADDRESS},
        {name:'vrrp_vrid', label:$lang.VIRTUAL_ROUTER_ID},
        {name:'vrrp_priority', label:$lang.PRIORITY, xtype:'number'},
        {name:'vrrp_auth', label:$lang.CERTIFICATION, xtype:'checkbox'},
        {name:'vrrp_pass', label:$lang.VAR_PASSWD, xtype:'number'},
        {name:'vrrp_script_type', label:$lang.SCRIPT_TYPE, xtype:'combo', data:[{id:'0', name:'Default'}, {id:'1', name:'ICMP'}]},
        {name:'vrrp_script_ip', label:$lang.VAR_IP, emptyText:$lang.EXAMPLE_A_B_C_D, hidden:true},
        {name:'vrrp_script_interval', label:$lang.CHECK_INTERVAL, xtype:'number'},
        {name:'vrrp_script_weight', label:$lang.WEIGHTS, xtype:'number'},
    ]},
    {title: $lang.STATIC_DHCP, id:'params_tab_static_dhcp', items:[]},
    {title: $lang.IP_URL_FILTER, id:'params_tab_url_filter', items:[]},
    {title: $lang.DOMAIN_FILTER, id:'params_tab_domain_filter', items:[
        {title:$lang.DOMAIN_FILTER, collapsible: true, xtype:'fieldset', items:[
            {name:'portfilterenabled', label:$lang.IS_ENABLE, xtype:'checkbox'},
            {name:'defaultfirewallpolicy', label:$lang.DEFAULTFIREWALLPOLICY, xtype:'combo', data:[{id:'1', name:$lang.VAR_TERM_PARAM_BLACKLIST}, {id:'0', name:$lang.VAR_TERM_PARAM_WHITELIST}]}
        ]}
    ]},
    {title: $lang.VPN_GRE, id:'params_tab_vpn_gre', items:[]},
    {title: $lang.OPEN_VPN_CLIENT, id:'params_tab_open_vpn_client', items:[
        {title:$lang.OPENVPN_CLIENT_BASIC_SETTINGS_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client1_eas', label:$lang.VIA_INTERNET, xtype:'checkbox'},
            {name:'vpn_client1_if', label:$lang.INTERFACE_TYPE, xtype:'combo', data:[{id:'tap', name:'TAP'}, {id:'tun', name:'TUN'}]},
            {name:'vpn_client1_proto', label:$lang.PROTOCOL, xtype:'combo', data:[{id:'udp', name:'UDP'}, {id:'tcp-client', name:'TCP'}]},
            {label:$lang.PPTP_CLIENT_SRVIP, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'vpn_client1_addr', label:''},
                {name:'vpn_client1_port', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
            {name:'vpn_client1_firewall', label:$lang.FIREWALL, xtype:'combo', data:[{id:'auto', name:'Automatic'},{id:'custom', name:'Custom'}]},
            {name:'vpn_client1_crypt', label:$lang.AUTHENTICATION_TYPE, xtype:'combo', data:[{id:'tls', name:'TLS'},{id:'secret', name:'Static Key'},{id:'custom', name:'Custom'}]},
            {name:'vpn_client1_userauth', label:$lang.USERNAME_PASSWORD_AUTHENTICATION, xtype:'checkbox', hidden:true},
            {name:'vpn_client1_username', label:$lang.VAR_USER_NAME},
            {name:'vpn_client1_password', label:$lang.PPTP_CLIENT_PASSWD},
            {name:'vpn_client1_useronly', label:$lang.AUTHENTICATE_USERNAME_ONLY, xtype:'checkbox', hidden:true},
            {name:'vpn_client1_hmac', label:$lang.HMAC_CERTIFICATION, xtype:'combo', hidden:true, data:[{id:'-1', name:'Disabled'},{id:'2', name:'Bi-directional'},{id:'0', name:'Incoming (0)'},{id:'1', name:'Outgoing (1)'}]},
            {name:'vpn_client1_bridge', label:$lang.THE_SERVER_IS_ON_THE_SAME_SUBNET, xtype:'checkbox', hidden:true},
            {name:'vpn_client1_nat', label:$lang.ALLOW_TUNNEL_NAT, xtype:'checkbox', emptyText:$lang.ROUTING_MUST_BE_SET_MANUALLY, hidden:true},
            {label:$lang.LOCAL_REMOTE_NODE_ADDRESS, xtype:'fieldcontainer', hidden:true, layout:'hbox', items:[
                {name:'vpn_client1_local', label:''},
                {name:'vpn_client1_remote', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
            {label:$lang.TUNNEL_ADDRESS_MASK, xtype:'fieldcontainer', hidden:true, layout:'hbox', items:[
                {name:'vpn_client1_local', label:''},
                {name:'vpn_client1_nm', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
        ]},
        {title:$lang.OPENVPN_CLIENT_ADVANCED_SETTINGS_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client1_poll', label:$lang.POLLING_INTERVAL, xtype:'number', emptyText:$lang.IN_MINUTES},
            {name:'vpn_client1_rgw', label:$lang.ALLOW_AS_DEFAULT_ROUTE, xtype:'checkbox'},
            {name:'vpn_client1_gw', label:'Gateway', hidden: true},
            {name:'vpn_client1_adns', label:$lang.RECEIVE_PEER_DNS_CONFIGURATION, xtype:'combo', hidden: true, data:[{id:'0', name:'Disabled'},{id:'1', name:'Relaxed'},{id:'2', name:'Strict'},{id:'3', name:'Exclusive'}]},
            {name:'vpn_client1_cipher', label:$lang.ENCRYPTION_ALGORITHM, xtype:'combo', data:[
                {id:'default', name:$lang.USE_DEFAULT},
                {id:'none', name:$lang.VAR_NONE},
                {id:'AES-128-CBC', name:'AES-128-CBC'},
                {id:'AES-128-CFB', name:'AES-128-CFB'},
                {id:'AES-128-OFB', name:'AES-128-OFB'},
                {id:'AES-192-CBC', name:'AES-192-CBC'},
                {id:'AES-192-CFB', name:'AES-192-CFB'},
                {id:'AES-192-OFB', name:'AES-192-OFB'},
                {id:'AES-256-CBC', name:'AES-256-CBC'},
                {id:'AES-256-CFB', name:'AES-256-CFB'},
                {id:'AES-256-OFB', name:'AES-256-OFB'},
                {id:'BF-CBC', name:'BF-CBC'},
                {id:'BF-CFB', name:'BF-CFB'},
                {id:'BF-OFB', name:'BF-OFB'},
                {id:'CAST5-CBC', name:'CAST5-CBC'},
                {id:'CAST5-CFB', name:'CAST5-CFB'},
                {id:'CAST5-OFB', name:'CAST5-OFB'},
                {id:'DES-CBC', name:'DES-CBC'},
                {id:'DES-CFB', name:'DES-CFB'},
                {id:'DES-EDE3-CBC', name:'DES-EDE3-CBC'},
                {id:'DES-EDE3-CFB', name:'DES-EDE3-CFB'},
                {id:'DES-EDE3-OFB', name:'DES-EDE3-OFB'},
                {id:'DES-EDE-CBC', name:'DES-EDE-CBC'},
                {id:'DES-EDE-CFB', name:'DES-EDE-CFB'},
                {id:'DES-EDE-OFB', name:'DES-EDE-OFB'},
                {id:'DES-OFB', name:'DES-OFB'},
                {id:'DESX-CBC', name:'DESX-CBC'},
                {id:'IDEA-CBC', name:'IDEA-CBC'},
                {id:'IDEA-CFB', name:'IDEA-CFB'},
                {id:'IDEA-OFB', name:'IDEA-OFB'},
                {id:'RC2-40-CBC', name:'RC2-40-CBC'},
                {id:'RC2-64-CBC', name:'RC2-64-CBC'},
                {id:'RC2-CBC', name:'RC2-CBC'},
                {id:'RC2-CFB', name:'RC2-CFB'},
                {id:'RC2-OFB', name:'RC2-OFB'},
                {id:'RC5-CBC', name:'RC5-CBC'},
                {id:'RC5-CFB', name:'RC5-CFB'},
                {id:'RC5-OFB', name:'RC5-OFB'}
            ]},
            {name:'vpn_client1_comp', label:$lang.COMPRESSION, xtype:'combo', data:[{id:'-1', name:'Disabled'}, {id:'no', name:'None'}, {id:'yes', name:'Enabled'}, {id:'adaptive', name:'Adaptive'}]},
            {name:'vpn_client1_reneg', label:$lang.TLS_RENEGOTIATION_TIME, xtype:'number', hidden: true, emptyText:$lang.IN_SECONDS2},
            {name:'vpn_client1_retry', label:$lang.NUMBER_OF_RECONNECTIONS, xtype:'number', emptyText:$lang.IN_SECONDS1},
            {name:'vpn_client1_tlsremote', label:$lang.AUTHENTICATION_SERVER_CERTIFICATE, hidden: true, xtype:'checkbox'},
            {name:'vpn_client1_cn', label:'Common Name', hidden: true},
            {name:'vpn_client1_custom', label:$lang.CUSTOM_OPTIONS},
        ]},
        {title:$lang.OPENVPN_CLIENT_KEY_SETUP_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client1_static', label:$lang.STATIC_KEY},
            {name:'vpn_client1_ca', label:$lang.CERTIFICATE_AUTHORITY},
            {name:'vpn_client1_crt', label:$lang.CLIENT_CERTIFICATE},
            {name:'vpn_client1_key', label:$lang.CLIENT_KEY},
        ]},
        {title:$lang.OPENVPN_CLIENT_BASIC_SETTINGS_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client2_eas', label:$lang.VIA_INTERNET, xtype:'checkbox'},
            {name:'vpn_client2_if', label:$lang.INTERFACE_TYPE, xtype:'combo', data:[{id:'tap', name:'TAP'}, {id:'tun', name:'TUN'}]},
            {name:'vpn_client2_proto', label:$lang.PROTOCOL, xtype:'combo', data:[{id:'udp', name:'UDP'}, {id:'tcp-client', name:'TCP'}]},
            {label:$lang.PPTP_CLIENT_SRVIP, xtype:'fieldcontainer', layout:'hbox', items:[
                {name:'vpn_client2_addr', label:''},
                {name:'vpn_client2_port', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
            {name:'vpn_client2_firewall', label:$lang.FIREWALL, xtype:'combo', data:[{id:'auto', name:'Automatic'},{id:'custom', name:'Custom'}]},
            {name:'vpn_client2_crypt', label:$lang.AUTHENTICATION_TYPE, xtype:'combo', data:[{id:'tls', name:'TLS'},{id:'secret', name:'Static Key'},{id:'custom', name:'Custom'}]},
            {name:'vpn_client2_userauth', label:$lang.USERNAME_PASSWORD_AUTHENTICATION, xtype:'checkbox', hidden:true},
            {name:'vpn_client2_username', label:$lang.VAR_USER_NAME},
            {name:'vpn_client2_password', label:$lang.PPTP_CLIENT_PASSWD},
            {name:'vpn_client2_useronly', label:$lang.AUTHENTICATE_USERNAME_ONLY, xtype:'checkbox', hidden:true},
            {name:'vpn_client2_hmac', label:$lang.HMAC_CERTIFICATION, xtype:'combo', hidden:true, data:[{id:'-1', name:'Disabled'},{id:'2', name:'Bi-directional'},{id:'0', name:'Incoming (0)'},{id:'1', name:'Outgoing (1)'}]},
            {name:'vpn_client2_bridge', label:$lang.THE_SERVER_IS_ON_THE_SAME_SUBNET, xtype:'checkbox', hidden:true},
            {name:'vpn_client2_nat', label:$lang.ALLOW_TUNNEL_NAT, xtype:'checkbox', emptyText:$lang.ROUTING_MUST_BE_SET_MANUALLY, hidden:true},
            {label:$lang.LOCAL_REMOTE_NODE_ADDRESS, xtype:'fieldcontainer', hidden:true, layout:'hbox', items:[
                {name:'vpn_client2_local', label:''},
                {name:'vpn_client2_remote', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
            {label:$lang.TUNNEL_ADDRESS_MASK, xtype:'fieldcontainer', hidden:true, layout:'hbox',  items:[
                {name:'vpn_client2_local', label:''},
                {name:'vpn_client2_nm', label:'', xtype:'number', minValue:1, maxValue:65535}
            ]},
        ]},
        {title:$lang.OPENVPN_CLIENT_ADVANCED_SETTINGS_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client2_poll', label:$lang.POLLING_INTERVAL, xtype:'number', emptyText:$lang.IN_MINUTES},
            {name:'vpn_client2_rgw', label:$lang.ALLOW_AS_DEFAULT_ROUTE, xtype:'checkbox'},
            {name:'vpn_client2_gw', label:'Gateway', hidden: true},
            {name:'vpn_client2_adns', label:$lang.RECEIVE_PEER_DNS_CONFIGURATION, xtype:'combo', data:[{id:'0', name:'Disabled'},{id:'1', name:'Relaxed'},{id:'2', name:'Strict'},{id:'3', name:'Exclusive'}]},
            {name:'vpn_client2_cipher', label:$lang.ENCRYPTION_ALGORITHM, xtype:'combo', data:[
                {id:'default', name:$lang.USE_DEFAULT},
                {id:'none', name:$lang.VAR_NONE},
                {id:'AES-128-CBC', name:'AES-128-CBC'},
                {id:'AES-128-CFB', name:'AES-128-CFB'},
                {id:'AES-128-OFB', name:'AES-128-OFB'},
                {id:'AES-192-CBC', name:'AES-192-CBC'},
                {id:'AES-192-CFB', name:'AES-192-CFB'},
                {id:'AES-192-OFB', name:'AES-192-OFB'},
                {id:'AES-256-CBC', name:'AES-256-CBC'},
                {id:'AES-256-CFB', name:'AES-256-CFB'},
                {id:'AES-256-OFB', name:'AES-256-OFB'},
                {id:'BF-CBC', name:'BF-CBC'},
                {id:'BF-CFB', name:'BF-CFB'},
                {id:'BF-OFB', name:'BF-OFB'},
                {id:'CAST5-CBC', name:'CAST5-CBC'},
                {id:'CAST5-CFB', name:'CAST5-CFB'},
                {id:'CAST5-OFB', name:'CAST5-OFB'},
                {id:'DES-CBC', name:'DES-CBC'},
                {id:'DES-CFB', name:'DES-CFB'},
                {id:'DES-EDE3-CBC', name:'DES-EDE3-CBC'},
                {id:'DES-EDE3-CFB', name:'DES-EDE3-CFB'},
                {id:'DES-EDE3-OFB', name:'DES-EDE3-OFB'},
                {id:'DES-EDE-CBC', name:'DES-EDE-CBC'},
                {id:'DES-EDE-CFB', name:'DES-EDE-CFB'},
                {id:'DES-EDE-OFB', name:'DES-EDE-OFB'},
                {id:'DES-OFB', name:'DES-OFB'},
                {id:'DESX-CBC', name:'DESX-CBC'},
                {id:'IDEA-CBC', name:'IDEA-CBC'},
                {id:'IDEA-CFB', name:'IDEA-CFB'},
                {id:'IDEA-OFB', name:'IDEA-OFB'},
                {id:'RC2-40-CBC', name:'RC2-40-CBC'},
                {id:'RC2-64-CBC', name:'RC2-64-CBC'},
                {id:'RC2-CBC', name:'RC2-CBC'},
                {id:'RC2-CFB', name:'RC2-CFB'},
                {id:'RC2-OFB', name:'RC2-OFB'},
                {id:'RC5-CBC', name:'RC5-CBC'},
                {id:'RC5-CFB', name:'RC5-CFB'},
                {id:'RC5-OFB', name:'RC5-OFB'}
            ]},
            {name:'vpn_client2_comp', label:$lang.COMPRESSION, xtype:'combo', data:[{id:'-1', name:'Disabled'}, {id:'no', name:'None'}, {id:'yes', name:'Enabled'}, {id:'adaptive', name:'Adaptive'}]},
            {name:'vpn_client2_reneg', label:$lang.TLS_RENEGOTIATION_TIME, xtype:'number', emptyText:$lang.IN_SECONDS2},
            {name:'vpn_client2_retry', label:$lang.NUMBER_OF_RECONNECTIONS, xtype:'number', emptyText:$lang.IN_SECONDS1},
            {name:'vpn_client2_tlsremote', label:$lang.AUTHENTICATION_SERVER_CERTIFICATE, xtype:'checkbox'},
            {name:'vpn_client2_cn', label:'Common Name', hidden: true},
            {name:'vpn_client2_custom', label:$lang.CUSTOM_OPTIONS},
        ]},
        {title:$lang.OPENVPN_CLIENT_KEY_SETUP_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'vpn_client2_static', label:$lang.STATIC_KEY},
            {name:'vpn_client2_ca', label:$lang.CERTIFICATE_AUTHORITY},
            {name:'vpn_client2_crt', label:$lang.CLIENT_CERTIFICATE},
            {name:'vpn_client2_key', label:$lang.CLIENT_KEY},
        ]},
    ]},
    {title: $lang.VPN_PPTP_L2TP, id:'params_tab_pptp_l2tp', items:[]},
    {title: $lang.IPSEC, id:'params_tab_ipsec', items:[
        {title:$lang.VAR_TERM_PARAM_IPSEC_GROUP_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec1_mode', label:$lang.IPSEC1_MODE, xtype:'checkbox'},
            {name:'ipsec1_ext', label:$lang.IPSEC1_EXT, xtype:'combo', data:[{id:'0', name:'Normal'}, {id:'1', name:'GRE over IPSec'}, {id:'2', name:'L2TP over IPSec'}]},
            {name:'ipsec1_left', label:$lang.IPSEC1_LEFT, xtype:'combo', data:[{id:'3g', name:'Cellular'}]},
            {name:'ipsec1_leftsubnet', label:$lang.IPSEC1_LEFTSUBNET, emptyText:$lang.EG_192_168_1_0_24, hidden:true},
            {name:'ipsec1_leftfirewall', label:$lang.IPSEC1_LEFTFIREWALL, xtype:'checkbox'},
            {name:'ipsec1_right', label:$lang.IPSEC1_RIGHT},
            {name:'ipsec1_rightsubnet', label:$lang.IPSEC1_RIGHTSUBNET, emptyText:$lang.EG_192_168_88_0_24, hidden:true},
            {name:'ipsec1_rightfirewall', label:$lang.IPSEC1_RIGHTFIREWALL, xtype:'checkbox'}
        ]},
        {title:$lang.VAR_TERM_PARAM_IPSEC_BASIC_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec1_keyexchange', label:$lang.IPSEC1_AUTHBY, xtype:'combo', data:[{id:'ikev1', name:'IKE with Preshared Key'}, {id:'ikev2', name:'IKEv2 with Preshared Key'}]},
            {name:'ipsec1_ph1_group', label:$lang.IPSEC1_PH1_GROUP, xtype:'combo', data:[
                {id:'modp768', name:'Group 1 - modp768'},
                {id:'modp1024', name:'Group 2 - modp1024'},
                {id:'modp1536', name:'Group 5 - modp1536'}
            ]},
            {name:'ipsec1_ike_enc', label:$lang.IPSEC1_IKE_ENC, xtype:'combo', data:[
                {id:'3des', name:'3DES (168-bit)'},
                {id:'aes128', name:'AES-128 (128-bit)'},
                {id:'aes192', name:'AES-192 (192-bit)'},
                {id:'aes256', name:'AES-256 (256-bit)'}
            ]},
            {name:'ipsec1_ike_auth', label:$lang.IPSEC1_IKE_AUTH, xtype:'combo', data:[
                {id:'md5', name:'MD5 HMAC (96-bit)'},
                {id:'sha', name:'SHA1 HMAC (96-bit)'},
                {id:'sha256', name:'SHA2_256_128 HMAC (128-bit)'},
                {id:'sha384', name:'SHA2_384_192 HMAC (192-bit)'},
                {id:'aes512', name:'SHA2_512_256 HMAC (256-bit)'}
            ]},
            {name:'ipsec1_ike_lifetime', label:$lang.IPSEC1_IKELIFETIME},
            {name:'ipsec1_ph2_group', label:$lang.IPSEC1_PH2_GROUP, xtype:'combo', data:[
                {id:'modp768', name:'Group 1 - modp768'},
                {id:'modp1024', name:'Group 2 - modp1024'},
                {id:'modp1536', name:'Group 5 - modp1536'}
            ]},
            {name:'ipsec1_esp_enc', label:$lang.IPSEC1_ESP_ENC, xtype:'combo', data:[
                {id:'null', name:'NULL'},
                {id:'3des', name:'3DES (168-bit)'},
                {id:'aes128', name:'AES-128 (128-bit)'},
                {id:'aes192', name:'AES-192 (192-bit)'},
                {id:'aes256', name:'AES-256 (256-bit)'}
            ]},
            {name:'ipsec1_esp_auth', label:$lang.IPSEC1_ESP_AUTH, xtype:'combo', data:[
                {id:'null', name:'NULL'},
                {id:'md5', name:'MD5 HMAC (96-bit)'},
                {id:'sha', name:'SHA1 HMAC (96-bit)'},
                {id:'sha256', name:'SHA2_256_128 HMAC (128-bit)'},
                {id:'sha384', name:'SHA2_384_192 HMAC (192-bit)'},
                {id:'aes512', name:'SHA2_512_256 HMAC (256-bit)'}
            ]},
            {name:'ipsec1_keylife', label:$lang.IPSEC1_KEYLIFE},
            {name:'ipsec1_pskkey', label:$lang.IPSEC1_PSKKEY}
        ]},
        {title:$lang.VAR_TERM_PARAM_IPSEC_BASIC_ADVANCED_1, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec1_aggressive', label:$lang.IPSEC1_AGGRESSIVE, xtype:'checkbox'},
            {name:'ipsec1_compress', label:$lang.IPSEC1_COMPRESS, xtype:'checkbox'},
            {name:'ipsec1_dpdaction', label:$lang.IPSEC1_DPDACTION, xtype:'checkbox'},
            {name:'ipsec1_dpddelay', label:$lang.DETECTION_CYCLE, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec1_dpdtimeout', label:$lang.DETECTION_TIMEOUT_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec1_icmp_check', label:$lang.IPSEC1_ICMP_CHECK, xtype:'checkbox'},
            {name:'ipsec1_icmp_intval', label:$lang.DETECTION_CYCLE, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec1_icmp_count', label:$lang.DETECTION_TIMEOUT_TIMES, xtype:'number', emptyText:$lang.VAR_FREQUENCY, hidden:true},
            {name:'ipsec1_icmp_addr', label:$lang.UTMSPINGADDR, hidden:true},
            {name:'ipsec1_custom1', label:$lang.IPSEC1_CUSTOM1},
            {name:'ipsec1_custom2', label:$lang.IPSEC1_CUSTOM2},
            {name:'ipsec1_custom3', label:$lang.IPSEC1_CUSTOM3},
            {name:'ipsec1_custom4', label:$lang.IPSEC1_CUSTOM4}
        ]},
        {title:$lang.VAR_TERM_PARAM_IPSEC_GROUP_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec2_mode', label:$lang.IPSEC1_MODE, xtype:'checkbox'},
            {name:'ipsec2_ext', label:$lang.IPSEC1_EXT, xtype:'combo', data:[{id:'0', name:'Normal'}, {id:'1', name:'GRE over IPSec'}, {id:'2', name:'L2TP over IPSec'}]},
            {name:'ipsec2_left', label:$lang.IPSEC1_LEFT, xtype:'combo', data:[{id:'3g', name:'Cellular'}]},
            {name:'ipsec2_leftsubnet', label:$lang.IPSEC1_LEFTSUBNET, emptyText:$lang.EG_192_168_1_0_24, hidden:true},
            {name:'ipsec2_leftfirewall', label:$lang.IPSEC1_LEFTFIREWALL, xtype:'checkbox'},
            {name:'ipsec2_right', label:$lang.IPSEC1_RIGHT},
            {name:'ipsec2_rightsubnet', label:$lang.IPSEC1_RIGHTSUBNET, emptyText:$lang.EG_192_168_88_0_24, hidden:true},
            {name:'ipsec2_rightfirewall', label:$lang.IPSEC1_RIGHTFIREWALL, xtype:'checkbox'}
        ]},
        {title:$lang.VAR_TERM_PARAM_IPSEC_BASIC_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec2_keyexchange', label:$lang.IPSEC1_AUTHBY, xtype:'combo', data:[{id:'ikev1', name:'IKE with Preshared Key'}, {id:'ikev2', name:'IKEv2 with Preshared Key'}]},
            {name:'ipsec2_ph1_group', label:$lang.IPSEC1_PH1_GROUP, xtype:'combo', data:[
                {id:'modp768', name:'Group 1 - modp768'},
                {id:'modp1024', name:'Group 2 - modp1024'},
                {id:'modp1536', name:'Group 5 - modp1536'}
            ]},
            {name:'ipsec2_ike_enc', label:$lang.IPSEC1_IKE_ENC, xtype:'combo', data:[
                {id:'3des', name:'3DES (168-bit)'},
                {id:'aes128', name:'AES-128 (128-bit)'},
                {id:'aes192', name:'AES-192 (192-bit)'},
                {id:'aes256', name:'AES-256 (256-bit)'}
            ]},
            {name:'ipsec2_ike_auth', label:$lang.IPSEC1_IKE_AUTH, xtype:'combo', data:[
                {id:'md5', name:'MD5 HMAC (96-bit)'},
                {id:'sha', name:'SHA1 HMAC (96-bit)'},
                {id:'sha256', name:'SHA2_256_128 HMAC (128-bit)'},
                {id:'sha384', name:'SHA2_384_192 HMAC (192-bit)'},
                {id:'aes512', name:'SHA2_512_256 HMAC (256-bit)'}
            ]},
            {name:'ipsec2_ike_lifetime', label:$lang.IPSEC1_IKELIFETIME},
            {name:'ipsec2_ph2_group', label:$lang.IPSEC1_PH2_GROUP, xtype:'combo', data:[
                {id:'modp768', name:'Group 1 - modp768'},
                {id:'modp1024', name:'Group 2 - modp1024'},
                {id:'modp1536', name:'Group 5 - modp1536'}
            ]},
            {name:'ipsec2_esp_enc', label:$lang.IPSEC1_ESP_ENC, xtype:'combo', data:[
                {id:'null', name:'NULL'},
                {id:'3des', name:'3DES (168-bit)'},
                {id:'aes128', name:'AES-128 (128-bit)'},
                {id:'aes192', name:'AES-192 (192-bit)'},
                {id:'aes256', name:'AES-256 (256-bit)'}
            ]},
            {name:'ipsec2_esp_auth', label:$lang.IPSEC1_ESP_AUTH, xtype:'combo', data:[
                {id:'null', name:'NULL'},
                {id:'md5', name:'MD5 HMAC (96-bit)'},
                {id:'sha', name:'SHA1 HMAC (96-bit)'},
                {id:'sha256', name:'SHA2_256_128 HMAC (128-bit)'},
                {id:'sha384', name:'SHA2_384_192 HMAC (192-bit)'},
                {id:'aes512', name:'SHA2_512_256 HMAC (256-bit)'}
            ]},
            {name:'ipsec2_keylife', label:$lang.IPSEC1_KEYLIFE},
            {name:'ipsec2_pskkey', label:$lang.IPSEC1_PSKKEY}
        ]},
        {title:$lang.VAR_TERM_PARAM_IPSEC_BASIC_ADVANCED_2, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec2_aggressive', label:$lang.IPSEC1_AGGRESSIVE, xtype:'checkbox'},
            {name:'ipsec2_compress', label:$lang.IPSEC1_COMPRESS, xtype:'checkbox'},
            {name:'ipsec2_dpdaction', label:$lang.IPSEC1_DPDACTION, xtype:'checkbox'},
            {name:'ipsec2_dpddelay', label:$lang.DETECTION_CYCLE, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec2_dpdtimeout', label:$lang.DETECTION_TIMEOUT_INTERVAL, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec2_icmp_check', label:$lang.IPSEC1_ICMP_CHECK, xtype:'checkbox'},
            {name:'ipsec2_icmp_intval', label:$lang.DETECTION_CYCLE, xtype:'number', emptyText:$lang.VAR_TIME_ARR[3], hidden:true},
            {name:'ipsec2_icmp_count', label:$lang.DETECTION_TIMEOUT_TIMES, xtype:'number', emptyText:$lang.VAR_FREQUENCY, hidden:true},
            {name:'ipsec2_icmp_addr', label:$lang.UTMSPINGADDR, hidden:true},
            {name:'ipsec2_custom1', label:$lang.IPSEC1_CUSTOM1},
            {name:'ipsec2_custom2', label:$lang.IPSEC1_CUSTOM2},
            {name:'ipsec2_custom3', label:$lang.IPSEC1_CUSTOM3},
            {name:'ipsec2_custom4', label:$lang.IPSEC1_CUSTOM4}
        ]},
        {title:$lang.LINK_SCHEDULING, collapsible:true, collapsed:false, xtype:'fieldset', items:[
            {name:'ipsec_schedule', label:$lang.POLICY, xtype:'combo', data:[{id:'0', name:$lang.VAR_NONE}, {id:'1', name:$lang.AUTO_SWITCH}, {id:'2', name:$lang.VAR_BACKUP}]},
        ]},
    ]},
    {title:$lang.ROUTER_IDENTIFICATION, id:'params_tab_router_identified', items:[
        {name:'router_name', label:$lang.ROUTER_NAME, length:[0,32]},
        {name:'wan_hostname', label:$lang.WAN_HOSTNAME, length:[0,32]},
        {name:'wan_domain', label:$lang.WAN_DOMAIN, length:[0,32]}
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
    {title:$lang.ACCESS_SETTINGS, id:'params_tab_access_settings', items:[
        {title:$lang.WEB_ACCESS_SETTINGS, collapsible: true, xtype:'fieldset', items:[
            {name:'http_local', label:$lang.LOCAL_ACCESS, xtype:'combo', data:[{id:'0', name:$lang.VAR_CLOSE}, {id:'1', name:'HTTP'}, {id:'2', name:'HTTPS'}, {id:'3', name:'HTTP & HTTPS'}]},
            {name:'http_lanport', label:$lang.HTTP_ACCESS_PORT, xtype:'number', hidden:true},
            {name:'https_lanport', label:$lang.HTTPS_ACCESS_PORT, xtype:'number', hidden:true},
            // {name:'https_crt_cn', label:$lang.COMMON_NAME, emptyText:$lang.OPTIONAL_PARAMETERS_SEPARATE_PARAMETERS_WITH_SPACES, hidden:true},
            // {name:'https_crt_gen', label:$lang.REGENERATE, xtype:'checkbox', hidden:true},
            {name:'https_crt_save', label:$lang.SAVE_TO_NVRAM, xtype:'checkbox', hidden:true},
            {name:'http_remote', label:$lang.REMOTE_ACCESS, xtype:'combo', data:[{id:'0', name:$lang.VAR_CLOSE}, {id:'1', name:'HTTP'}, {id:'2', name:'HTTPS'}]},
            {name:'http_wanport', label:$lang.ACCESS_PORT, xtype:'number', hidden:true},
            {name:'rmgt_sip', label:$lang.REMOTE_ACCESS_ALLOW_IPS, emptyText:$lang.OPTIONAL_ARGUMENT, hidden:true},
            {name:'http_wireless', label:$lang.ALLOW_WIRELESS_ACCESS, xtype:'checkbox'},
            {name:'block_wan', label:$lang.WAN_PORT_BAN_PING, xtype:'checkbox'},
            {name:'sshd_eas', label:$lang.SSH_BOOT_UP, xtype:'checkbox'},
            {name:'telnetd_remote', label:$lang.ENABLE_TELNET_REMOTE_ACCESS, xtype:'checkbox'},
        ]},
        {title:$lang.PASSWORD_SETTING, collapsible: true, xtype:'fieldset', items:[
            {name:'http_passwd', label:$lang.PLEASE_ENTER_YOUR_PASSWORD_ADMIN},
            {name:'http_guestpass', label:$lang.PLEASE_ENTER_YOUR_PASSWORD_USER}
        ]},
        {title:$lang.CERTIFICATE_SETTINGS, collapsible: true, xtype:'fieldset', items:[
            {name:'https_certificate', label:$lang.CERTIFICATE, xtype:'textarea'},
            {name:'https_private_key', label:$lang.SECRET_KEY,  xtype:'textarea'}
        ]}
    ]},
    {title:$lang.SCHEDULED_REBOOT, id:'params_tab_scheduled_reboot', items:[
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
                {id:'-60', name:$lang.EVERY_HOUR},
                {id:'-720', name:$lang.EVERY_12_HOURS},
                {id:'-1440', name:$lang.EVERY_24_HOURS},
                {id:'e', name:$lang.EVERY}
            ]},
            {name:'rboot_every', label:'', xtype:'number', minValue:60, maxValue:86400, disabled:true, placeholder:'60~86400 '+$lang.VAR_TIME_ARR[2]}
        ]},
        {name:'ck_days', label:$lang.EXEC_INTERVAL, xtype:'ck_days'}
    ]},
    {title:$lang.STORAGE_MANAGE, collapsible:true, collapsed:false, xtype:'fieldset', id:'params_tab_storage', items:[
        {name:'storage_udisk', label:$lang.STORAGE_UDISK, xtype:'combo', data:[{id:'0', name:$lang.ROUTER}, {id:'1', name:$lang.MOVING_MEDIA}]}
    ]},
    {title: $lang.VAR_TERM_PARAM_PLATFORM, id:'params_tab_m2m', items:[
        {name:'m2m_mode', label:$lang.ENABLE_M2M_PLATFORM_MANAGEMENT, xtype:'checkbox', checkedValue:'enable', unCheckedValue:'disable'},
        {name:'m2m_error_action', label:$lang.EXCEPTION_HANDLING, xtype:'combo', data:[{id:'0', name:$lang.RESTART_M2M}, {id:'1', name:$lang.RECONNECT_NETWORK}, {id:'2', name:$lang.REBOOT_SYSTEM}]},
        {name:'m2m_product_id', label:$lang.PRODUCT_ID, length:[0,14], emptyText:$lang.M2M_PRODUCT_ID_LENGTH_VALID},
        {label:$lang.M2M_SERVER_DOMAIN_PORT, xtype:'fieldcontainer', layout:'hbox', items:[
            {name:'m2m_server_domain', label:''},   
            {name:'m2m_server_port', label:'', xtype:'number', minValue:1},   
        ]},
        {name:'m2m_heartbeat_intval', label:$lang.HEARTBEAT_PACKET_REPORTING_FREQUENCY, xtype:'number', minValue:1, emptyText:$lang.VAR_TIME_ARR[3]},
        {name:'m2m_heartbeat_retry', label:$lang.M2M_HEARTBEAT_RETRY, xtype:'number', minValue:10, maxValue:1000, emptyText:$lang.M2M_HEARTBEAT_RETRY_DESC},
        {name:'n2n_bootmode', label:$lang.HOW_TO_START_NAMED_PIPES, xtype:'combo', data:[{id:'0', name:$lang.REMOTE_CONNECT}, {id:'1', name:$lang.AUTO_CONNECT}]},
        {name:'n2n_server', label:$lang.NAMED_PIPES_SERVICE_PORT, xtype:'number', minValue:1024, maxValue:65535, emptyText:$lang.RANGE_1024_65535}
    ]},
    {title: $lang.MQTT_CONN_SETTINGS, id:'params_tab_mqtt', items:[
        {name:'mqtt_enable', label:'MQTT enabled', xtype:'checkbox'},
        {name:'ping_server_ip', label:'Ping server ip'},
        {name:'ping_client_ip', label:'Ping client ip'},
        {name:'ping_count', label:$lang.PING_NUM, xtype:'number', minValue:1, maxValue:10, emptyText:'1-10'},
        {name:'mqtt_time_interval', label:$lang.IO_REPORT_INTERVAL, xtype:'number', minValue:1, maxValue:7200, emptyText:'1-7200 '+$lang.VAR_TIME_ARR[3]},
        {name:'register_code', label:$lang.REG_CODE},
        {name:'mqtt_heartbeat_interval', label:'MQTT Heartbeat Interval', xtype:'number', minValue:1, emptyText:$lang.VAR_TIME_ARR[3]}
    ]},
    {title: $lang.LOG_MANAGEMENT, id:'params_tab_log_management', items:[
        {name:'log_file', label:$lang.LOG_TO_LOCAL_SYSTEM, xtype:'checkbox'},
        {name:'log_remote', label:$lang.LOG_TO_REMOTE_SYSTEM, xtype:'checkbox'},
        {label:$lang.HOST_OR_IP_ADDRESS_PORT, xtype:'fieldcontainer', layout:'hbox', items:[
            {name:'log_remoteip', label:''},   
            {name:'log_remoteport', label:'', xtype:'number', minValue:1},   
        ]},
        {name:'log_mark', label:$lang.MARK_GENERATION_INTERVAL, xtype:'combo', data:[{id:'0', name:$lang.VAR_CLOSE}, {id:'30', name:$lang.EVERY_30_MINUTES}, {id:'60', name:$lang.EVERY_1_HOUR}, {id:'120', name:$lang.EVERY_2_HOUR}, {id:'360', name:$lang.EVERY_6_HOUR}, {id:'720', name:$lang.EVERY_12_HOUR}, {id:'1440', name:$lang.EVERY_OTHER_DAY}, {id:'10080', name:$lang.EVERY_7_DAYS}]},
        {name:'log_limit', label:$lang.LOGGING_LIMITS, xtype:'number', minValue:1024, maxValue:65535, emptyText:$lang.MESSAGES_PER_MINUTE_0_MEANS_UNLIMITED}
    ]},
    {title: 'Http send', id:'params_tab_http_send', items:[
        {name:'send_script', label:'script', xtype:'textarea'},
    ]}
];