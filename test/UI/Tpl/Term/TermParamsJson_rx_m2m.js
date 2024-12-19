function TermParams(type){
    this.rules = {};
    this.changedParams = [];
    this.paramsLoading = false;
    this.paramsType = type;
    this.tp_names = {
        new_qoslimit_rules: ['id', $lang.IP_SEGMENT_MAC, $lang.DOWNLOAD_RATE, $lang.MAXIMUM_DOWNLOAD_RATE, $lang.UPLOAD_RATE, $lang.MAXIMUM_UPLOAD_RATE, $lang.PRIORITY],
        webhostfilters: ['id', $lang.VAR_RULE_ENABLE, $lang.VAR_RULE_DOMAIN, $lang.VAR_RULE_DESC],
        greparam: ['id', $lang.VAR_RULE_ENABLE, $lang.TUNNEL_NUMBER, $lang.TUNNEL_ADDRESS, $lang.SOURCE_TUNNEL_ADDRESS, $lang.TUNNEL_DEST_ADDRESS, $lang.KEEPALIVE, $lang.PINGINTERVAL, $lang.PINGMAX, $lang.VAR_RULE_DESC],
        greroute: ['id', $lang.VAR_RULE_ENABLE, $lang.TUNNEL_NUMBER, $lang.DEST_ADDRESS, $lang.VAR_RULE_DESC],
        portforward: ['id', $lang.VAR_RULE_ENABLE, $lang.SOCKET_TYPE, 'Interface', $lang.SRC_ADDR, $lang.EXT_PORTS, $lang.INT_PORT, $lang.INT_ADDR, $lang.VAR_RULE_DESC],
        portredirect: ['id', $lang.VAR_RULE_ENABLE, $lang.SOCKET_TYPE, $lang.INT_PORT, $lang.EXT_ADDR, $lang.EXT_PORTS, $lang.VAR_RULE_DESC],
        trigforward: ['id', $lang.VAR_RULE_ENABLE, $lang.SOCKET_TYPE, $lang.TRIGGER_PORT, $lang.MAP_PORT, $lang.VAR_RULE_DESC],
        // vlan: ['id','VID', $lang.WAN_LAN, $lang.TAG, $lang.LAN + '1', $lang.TAG + '1', $lang.LAN + '2', $lang.TAG + '2', $lang.LAN + '3', $lang.TAG + '3', $lang.LAN + '4', $lang.TAG + '4', $lang.BRIDGING],
        linkscheck: ['id', $lang.ENABLE, $lang.LINK, $lang.DEST_ADDRESS, $lang.PINGINTERVAL, $lang.PINGMAX, $lang.VAR_RULE_DESC],
        linkschedule: ['id', $lang.ENABLE, $lang.LINK + '1', $lang.LINK + '2', $lang.POLICY, $lang.VAR_RULE_DESC],
        ospf_network: ['id', $lang.ENABLE, $lang.WEBSITE_ADDRESS, $lang.VAR_AREA],
        ipportfilterrules: ['id', $lang.VAR_RULE_ENABLE, $lang.SRC_MAC, $lang.SRC_ADDR, $lang.DEST_ADDR, $lang.SOCKET_TYPE, $lang.SRC_PORT, $lang.DEST_PORT, $lang.POLICY, $lang.VAR_RULE_DESC],
        keywordfilters: ['id', $lang.VAR_RULE_ENABLE, $lang.KEYWORDS, $lang.VAR_RULE_DESC],
        weburlfilters: ['id', $lang.VAR_RULE_ENABLE, 'URL', $lang.VAR_RULE_DESC],
        routeraccessrules: ['id', $lang.VAR_RULE_ENABLE, $lang.SRC_MAC, $lang.SRC_ADDR, $lang.DEST_ADDR, $lang.SOCKET_TYPE, $lang.SRC_PORT, $lang.DEST_PORT, $lang.POLICY, $lang.VAR_RULE_DESC],
        dhcpd_static: ['id', $lang.VAR_DEVICE_MAC, $lang.VAR_IP, $lang.WAN_HOSTNAME, $lang.VAR_RULE_DESC],
        modbusCmdTable: ['id', $lang.VAR_COMMAND, $lang.ADDR, $lang.VALUE_TYPE, $lang.SIGNAL_ID, $lang.ALARM_VOLUME, $lang.VAR_RULE_DESC],
		xtpbasic: ['id', $lang.VAR_RULE_ENABLE, $lang.PROTOCAL_TYPE, $lang.VAR_NAME, $lang.APP_SERVER, $lang.PPTP_CLIENT_USERNAME, $lang.PPTP_CLIENT_PASSWD, $lang.FIREWALL, $lang.DEFAULT_ROUTE, $lang.LOCAL_IP],
		l2advanced: ['id', $lang.VAR_RULE_ENABLE, $lang.VAR_NAME, $lang.RECEIVE_PEER_DNS, 'MTU', 'MRU', $lang.TUNNEL_AUTHENTICATION, $lang.TUNNEL_PASSWORD, $lang.CUSTOM_DIALING_OPTIONS],
		ppadvanced: ['id', $lang.VAR_RULE_ENABLE, $lang.VAR_NAME, $lang.RECEIVE_PEER_DNS, 'MTU', 'MRU', 'MPPE', $lang.MPPE_STATUS_CONNECTION, $lang.CUSTOM_DIALING_OPTIONS],
		xtpschedule: ['id', $lang.VAR_RULE_ENABLE, $lang.VAR_NAME+' 1', $lang.VAR_NAME+' 2', $lang.POLICY, $lang.VAR_RULE_DESC],
        routes_static: ['id', $lang.DEST_ADDRESS, $lang.VAR_GATEWAY, $lang.LAN_NETMASK, $lang.HOPS, $lang.NETWORK_INTERFACE, $lang.VAR_RULE_DESC],
        iot_topic_table: ['id', $lang.DEVICE_CODE, $lang.REGISTER_ADDR]
	};
    this.tp_columns = {
        new_qoslimit_rules: [
            {name:'id',             index:'id',             jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'ip_mac',         index:'ip_mac',         jsonmap:'ip_mac',       width:100, align:'center', editable:true,   edittype:'text'},
            {name:'download',       index:'download',       jsonmap:'download',     width:100, align:'center', editable:true,   edittype:'text'},
            {name:'max_download',   index:'max_download',   jsonmap:'max_download', width:100, align:'center', editable:true,   edittype:'text'},
            {name:'upload',         index:'upload',         jsonmap:'upload',       width:100, align:'center', editable:true,   edittype:'text'},
            {name:'max_upload',     index:'max_upload',     jsonmap:'max_upload',   width:100, align:'center', editable:true,   edittype:'text'},
            {name:'priority',       index:'priority',       jsonmap:'priority',     width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"0:"+$lang.PRIORITY_LEVELS[0]+";1:"+$lang.PRIORITY_LEVELS[1]+";2:"+$lang.PRIORITY_LEVELS[2]+";3:"+$lang.PRIORITY_LEVELS[3]+";4:"+$lang.PRIORITY_LEVELS[4]}, formatter:function(v){
                if (isNaN(v)) return v;
                return $lang.PRIORITY_LEVELS[v];
            }},
        ],
        webhostfilters: [
            {name:'id',             index:'id',        jsonmap:'id',        width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',    jsonmap:'enable',    width:50,  align:'center', editable:true, edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'domain',         index:'domain',    jsonmap:'domain',    width:100, align:'center', editable:true,   edittype:'text'},
            {name:'info',           index:'info',      jsonmap:'info',      width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        greparam: [
            {name:'id',             index:'id',        jsonmap:'id',        width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',    jsonmap:'enable',    width:50,  align:'center', editable:true, edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'idx',            index:'idx',       jsonmap:'idx',       width:100, align:'center', editable:true, edittype:'text', minValue:1, maxValue:8},
            {name:'ip1',            index:'ip1',       jsonmap:'ip1',       width:100, align:'center', editable:true, edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'ip2',            index:'ip2',       jsonmap:'ip2',       width:100, align:'center', editable:true, edittype:'text'},
            {name:'ip3',            index:'ip3',       jsonmap:'ip3',       width:100, align:'center', editable:true, edittype:'text'},
            {name:'keepalive',      index:'keepalive', jsonmap:'keepalive', width:50,  align:'center', editable:true, edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'interval',       index:'interval',  jsonmap:'interval',  width:50,  align:'center', editable:true, edittype:'text', minValue:0, maxValue:255},
            {name:'retry',          index:'retry',     jsonmap:'retry',     width:50,  align:'center', editable:true, edittype:'text', minValue:0, maxValue:255},
            {name:'info',           index:'info',      jsonmap:'info',      width:100, align:'center', editable:true, edittype:'text', regex:$.gf.reg_not_zh}
        ],
        greroute: [
            {name:'id',             index:'id',            jsonmap:'id',            width:50,   hidden:true, key:true},
            {name:'enable',         index:'enable',        jsonmap:'enable',        width:50,   align:'center', editable:true, edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tunnel_number',  index:'tunnel_number', jsonmap:'tunnel_number', width:100,  align:'center', editable:true, edittype:'text', minValue:1, maxValue:8},
            {name:'dest_address',   index:'dest_address',  jsonmap:'dest_address',  width:100,  align:'center', editable:true, edittype:'text'},
            {name:'info',           index:'info',          jsonmap:'info',          width:100,  align:'center', editable:true, edittype:'text', regex:$.gf.reg_not_zh}
        ],
        portforward: [
            {name:'id',             index:'id',        jsonmap:'id',        width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',    jsonmap:'enable',    width:50,  align:'center', editable:true, edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'protocol',       index:'protocol',  jsonmap:'protocol',  width:100, align:'center', editable:true, edittype:'select',   editoptions:{value:"1:TCP;2:UDP;3:TCP/UDP"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? 'TCP' : (v == 2 ? 'UDP' : 'TCP/UDP');
            }},
            {name:'interface',      index:'interface',      jsonmap:'interface',    width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:Default;1:Modem;2:Modem2"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 0 ? 'Default' : (v == 1 ? 'Modem' : 'Modem2');
            }},
            {name:'src_addr',       index:'src_addr',  jsonmap:'src_addr',  width:100, align:'center', editable:true, edittype:'text'},
            {name:'ext_ports',      index:'ext_ports', jsonmap:'ext_ports', width:100, align:'center', editable:true, edittype:'text'},
            {name:'int_port',       index:'int_port',  jsonmap:'int_port',  width:100, align:'center', editable:true, edittype:'text', minValue:1, maxValue:65535},
            {name:'int_addr',       index:'int_addr',  jsonmap:'int_addr',  width:100, align:'center', editable:true, edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'info',           index:'info',      jsonmap:'info',      width:100, align:'center', editable:true, edittype:'text', regex:$.gf.reg_not_zh}
        ],
        portredirect: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'protocol',       index:'protocol',   jsonmap:'protocol',     width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:TCP;2:UDP;3:Both"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? 'TCP' : (v == 2 ? 'UDP' : 'Both');
            }},
            {name:'int_port',       index:'int_port',   jsonmap:'int_port',     width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'ext_addr',       index:'ext_addr',   jsonmap:'ext_addr',     width:100, align:'center', editable:true,   edittype:'text'},
            {name:'ext_ports',      index:'ext_ports',  jsonmap:'ext_ports',    width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        trigforward: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'protocol',       index:'protocol',   jsonmap:'protocol',     width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:TCP;2:UDP;3:Both"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? 'TCP' : (v == 2 ? 'UDP' : 'Both');
            }},
            {name:'trigger_port',   index:'trigger_port', jsonmap:'trigger_port', width:100, align:'center', editable:true,   edittype:'text'},
            {name:'map_port',       index:'map_port',   jsonmap:'map_port',     width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        vlan: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'VID',            index:'VID',        jsonmap:'VID',          width:50,  align:'center', editable:true},
            {name:'wan_lan',        index:'wan_lan',    jsonmap:'wan_lan',      width:80,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tag',            index:'tag',        jsonmap:'tag',          width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'lan1',           index:'lan1',       jsonmap:'lan1',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tag1',           index:'tag1',       jsonmap:'tag1',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'lan2',           index:'lan2',       jsonmap:'lan2',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tag2',           index:'tag2',       jsonmap:'tag2',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'lan3',           index:'lan3',       jsonmap:'lan3',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tag3',           index:'tag3',       jsonmap:'tag3',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'lan4',           index:'lan4',       jsonmap:'lan4',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'tag4',           index:'tag4',       jsonmap:'tag4',         width:50, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'bridging',       index:'bridging',   jsonmap:'bridging',     width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:none;2:WAN;3:br0;4:br1;5:br2;6:br3"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? 'none' : (v == 2 ? 'WAN' : (v == 3 ? 'br0' : (v == 4 ? 'br1' : (v == 5 ? 'br2' : 'br3'))));
            }},
        ],
        linkscheck: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'link',           index:'link',          jsonmap:'link',          width:100, align:'center', editable:true,   edittype:'text'},
            {name:'dest_address',   index:'dest_address',  jsonmap:'dest_address',  width:100, align:'center', editable:true,   edittype:'text'},
            {name:'pinginterval',   index:'pinginterval',  jsonmap:'pinginterval',  width:100, align:'center', editable:true,   edittype:'text'},
            {name:'pingmax',        index:'pingmax',       jsonmap:'pingmax',       width:100, align:'center', editable:true,   edittype:'text'},
            {name:'var_rule_desc',  index:'var_rule_desc', jsonmap:'var_rule_desc', width:100, align:'center', editable:true,   edittype:'text'}
        ],
        linkschedule: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'link1',          index:'link1',      jsonmap:'link1',        width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"modem:modem;wan:wan"}},
            {name:'link2',          index:'link2',      jsonmap:'link2',        width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"modem:modem;wan:wan"}},
            {name:'policy',         index:'policy',     jsonmap:'policy',       width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.AUTO_SWITCH+";0:"+$lang.VAR_BACKUP}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.AUTO_SWITCH : $lang.VAR_BACKUP;
            }},
            {name:'var_rule_desc',  index:'var_rule_desc', jsonmap:'var_rule_desc', width:100, align:'center', editable:true,   edittype:'text'}
        ],
        ospf_network: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'website_address',index:'website_address',jsonmap:'website_address', width:100, align:'center', editable:true,   edittype:'select', editoptions:{value:"LAN:LAN;WAN:WAN"}},
            {name:'area',          index:'area',      jsonmap:'area',  width:200, align:'center', editable:true,   edittype:'text'},
        ],
        ipportfilterrules: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'src_mac',        index:'src_mac',    jsonmap:'src_mac',      width:100, align:'center', editable:true,   edittype:'text'},
            {name:'src_addr',       index:'src_addr',   jsonmap:'src_addr',     width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'dest_addr',      index:'dest_addr',  jsonmap:'dest_addr',    width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'protocol',       index:'protocol',   jsonmap:'protocol',     width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:NONE;1:TCP;2:UDP;3:ICMP"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 0 ? 'NONE' : (v == 1 ? 'TCP' : (v == 2 ? 'UDP' : 'ICMP'));
            }},
            {name:'src_port',       index:'src_port',   jsonmap:'src_port',     width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'dest_port',      index:'dest_port',  jsonmap:'dest_port',    width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'policy',         index:'policy',     jsonmap:'policy',       width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:Drop;1:Accept"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 0 ? 'Drop' : 'Accept';
            }},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        keywordfilters: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'keywords',       index:'keywords',   jsonmap:'keywords',     width:100, align:'center', editable:true,   edittype:'text'},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        weburlfilters: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'url',            index:'url',        jsonmap:'url',          width:100, align:'center', editable:true,   edittype:'text'},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        routeraccessrules: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'enable',         index:'enable',     jsonmap:'enable',       width:50,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'src_mac',        index:'src_mac',    jsonmap:'src_mac',      width:100, align:'center', editable:true,   edittype:'text'},
            {name:'src_addr',       index:'src_addr',   jsonmap:'src_addr',     width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'dest_addr',      index:'dest_addr',  jsonmap:'dest_addr',    width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'protocol',       index:'protocol',   jsonmap:'protocol',     width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:NONE;1:TCP;2:UDP;3:ICMP"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 0 ? 'NONE' : (v == 1 ? 'TCP' : (v == 2 ? 'UDP' : 'ICMP'));
            }},
            {name:'src_port',       index:'src_port',   jsonmap:'src_port',     width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'dest_port',      index:'dest_port',  jsonmap:'dest_port',    width:100, align:'center', editable:true,   edittype:'text', minValue:1, maxValue:65535},
            {name:'policy',         index:'policy',     jsonmap:'policy',       width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:Drop;1:Accept"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 0 ? 'Drop' : 'Accept';
            }},
            {name:'info',           index:'info',       jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        dhcpd_static: [
            {name:'id',        index:'id',       jsonmap:'id',        width:50,  hidden:true, key:true},
            {name:'mac',       index:'mac',      jsonmap:'mac',       width:200, align:'center', editable:true,   edittype:'text'},
            {name:'ip',        index:'ip',       jsonmap:'ip',        width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_ip, regexText:$lang.VAR_TERM_PARAM_CORRECT_IP},
            {name:'hostname',  index:'hostname', jsonmap:'hostname',  width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh},
            {name:'info',      index:'info',     jsonmap:'info',      width:100, align:'center', editable:true,   edittype:'text', regex:$.gf.reg_not_zh}
        ],
        modbusCmdTable: [
            {name:'id',             index:'id',         jsonmap:'id',           width:50,  hidden:true, key:true},
            {name:'cmd',            index:'cmd',        jsonmap:'cmd',          width:100, align:'center', editable:true,   edittype:'text'},
            {name:'addr',           index:'addr',       jsonmap:'addr',         width:100, align:'center', editable:true,   edittype:'text'},
            {name:'value_type',     index:'value_type', jsonmap:'value_type',   width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"1:"+$lang.ANALOG_QUANTITY+";2:"+$lang.SINGLE_BYTES+";3:"+$lang.DOUBLE_BYTES+";4:"+$lang.FOUR_BYTES}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.ANALOG_QUANTITY : (v == 2 ? $lang.SINGLE_BYTES : (v == 3 ? $lang.DOUBLE_BYTES : $lang.FOUR_BYTES));
            }},
            {name:'signal_id',      index:'signal_id',    jsonmap:'signal_id',    width:100, align:'center', editable:true,   edittype:'text'},
            {name:'alarm_volume',   index:'alarm_volume', jsonmap:'alarm_volume', width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"0:"+$lang.VAR_NO+";1:"+$lang.VAR_YES}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'info',           index:'info',      	  jsonmap:'info',         width:100, align:'center', editable:true,   edittype:'text'}
        ],
        xtpbasic: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'enable',         index:'enable',     	jsonmap:'enable',       width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
            {name:'protocol',       index:'protocol',   	jsonmap:'protocol',     width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"1:L2TP;2:PPTP"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? 'L2TP' : 'PPTP';
            }},
            {name:'name',         	index:'name',         	jsonmap:'name',       	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'server',       	index:'server',       	jsonmap:'server',     	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'user',   		index:'user',   		jsonmap:'user', 		width:100, align:'center', editable:true,   edittype:'text'},
            {name:'pwd',         	index:'pwd', 	        jsonmap:'pwd',       	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'firewall',       index:'firewall',       jsonmap:'firewall',     width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_ENABLE+";0:"+$lang.VAR_DISABLE}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
            }},
            {name:'default_route',  index:'default_route', jsonmap:'default_route', width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_ENABLE+";0:"+$lang.VAR_DISABLE}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
            }},
			{name:'local_ip',       index:'local_ip',       jsonmap:'local_ip',     width:100, align:'center', editable:true,   edittype:'text'}
        ],
		l2advanced: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'enable',         index:'enable',     	jsonmap:'enable',       width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
			{name:'name',         	index:'name',         	jsonmap:'name',       	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'recv_peer_dns',  index:'recv_peer_dns',  jsonmap:'recv_peer_dns',width:70,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_NO+";2:"+$lang.VAR_YES}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_NO : $lang.VAR_YES;
            }},
            {name:'mtu',       		index:'mtu',	       	jsonmap:'mtu',	     	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'mru',   			index:'mru',   			jsonmap:'mru', 			width:100, align:'center', editable:true,   edittype:'text'},
            {name:'tunnel_auth',    index:'tunnel_auth',    jsonmap:'tunnel_auth',  width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_ENABLE+";0:"+$lang.VAR_DISABLE}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
            }},
            {name:'tunnel_pwd',     index:'tunnel_pwd', 	jsonmap:'tunnel_pwd',   width:100, align:'center', editable:true,   edittype:'text'},
			{name:'custom_dial', 	index:'custom_dial', 	jsonmap:'custom_dial',	width:100, align:'center', editable:true,   edittype:'text'}
		],
		ppadvanced: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'enable',         index:'enable',     	jsonmap:'enable',       width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
			{name:'name',         	index:'name',         	jsonmap:'name',       	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'recv_peer_dns',  index:'recv_peer_dns',  jsonmap:'recv_peer_dns',width:70,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_NO+";2:"+$lang.VAR_YES}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_NO : $lang.VAR_YES;
            }},
            {name:'mtu',       		index:'mtu',	       	jsonmap:'mtu',	     	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'mru',   			index:'mru',   			jsonmap:'mru', 			width:100, align:'center', editable:true,   edittype:'text'},
            {name:'mppe',    		index:'mppe',		    jsonmap:'mppe',			width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_ENABLE+";0:"+$lang.VAR_DISABLE}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
            }},
            {name:'mppe_status',    index:'mppe_status',	jsonmap:'mppe_status',	width:100,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_ENABLE+";0:"+$lang.VAR_DISABLE}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_ENABLE : $lang.VAR_DISABLE;
            }},
			{name:'custom_dial', 	index:'custom_dial', 	jsonmap:'custom_dial',	width:100, align:'center', editable:true,   edittype:'text'}
		],
		xtpschedule: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'enable',         index:'enable',     	jsonmap:'enable',       width:60,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.VAR_YES+";0:"+$lang.VAR_NO}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.VAR_YES : $lang.VAR_NO;
            }},
			{name:'name1',         	index:'name1',         	jsonmap:'name1',       	width:100, align:'center', editable:true,   edittype:'text'},
			{name:'name2',         	index:'name2',         	jsonmap:'name2',       	width:100, align:'center', editable:true,   edittype:'text'},
            {name:'policy',         index:'policy',         jsonmap:'policy',       width:70,  align:'center', editable:true,   edittype:'select', editoptions:{value:"1:"+$lang.AUTO_SWITCH+";2:"+$lang.VAR_BACKUP}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 1 ? $lang.AUTO_SWITCH : $lang.VAR_BACKUP;
            }},
			{name:'desc',         	index:'desc',         	jsonmap:'desc',       	width:100, align:'center', editable:true,   edittype:'text'}
		],
        routes_static: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'dest',           index:'dest',           jsonmap:'dest',         width:100, align:'center', editable:true,   edittype:'text'},
            {name:'gateway',        index:'gateway',        jsonmap:'gateway',      width:100, align:'center', editable:true,   edittype:'text'},
            {name:'mask',           index:'mask',           jsonmap:'mask',         width:100, align:'center', editable:true,   edittype:'text'},
            {name:'hops',           index:'hops',           jsonmap:'hops',         width:100, align:'center', editable:true,   edittype:'text'},
            {name:'interface',      index:'interface',      jsonmap:'interface',    width:100, align:'center', editable:true,   edittype:'select',   editoptions:{value:"lan:lan;wan:wan"}, formatter:function(v){
                if (isNaN(v)) return v;
                return v == 'lan' ? 'lan' : 'wan';
            }},
            {name:'desc',           index:'desc',           jsonmap:'desc',         width:100, align:'center', editable:true,   edittype:'text'}
        ],
        iot_topic_table: [
            {name:'id',             index:'id',             jsonmap:'id',           width:60,  hidden:true, key:true},
            {name:'code',           index:'code',           jsonmap:'code',         width:100, align:'center', editable:true,   edittype:'text'},
            {name:'addr',           index:'addr',           jsonmap:'addr',         width:100, align:'center', editable:true,   edittype:'text'}
        ]
    };
    this.tp_grids = ['new_qoslimit_rules', 'webhostfilters', 'greparam', 'greroute', 'portforward', 'portredirect', 'trigforward', /*'vlan',*/ 'linkscheck', 'linkschedule','ospf_network', 'ipportfilterrules', 'keywordfilters', 'weburlfilters','routeraccessrules', 'dhcpd_static', 'modbusCmdTable', 'xtpbasic', 'l2advanced', 'ppadvanced', 'xtpschedule', 'routes_static', 'iot_topic_table'];
    this.init();
}

TermParams.prototype.saveChangedParams =  function(name, v, del){
    if (!this.paramsLoading) {
        if (del){
            delete this.changedParams[name];
        }else{
            this.changedParams[name] = v;
        }
    }
    // console.log(this.changedParams);
}

TermParams.prototype.cleanRules =  function(name, v){
    var me = this, grid = null;
    for (var i=0; i<me.tp_grids.length; i++){
        grid = $('#grid_'+me.tp_grids[i]);
        if (grid.length != 0){
            grid.clearGridData();
        }
    }
}

TermParams.prototype.setRules = function(grid_id, rs){
    var me = this;
    me.rules[grid_id] = rs;
    rs = rs.split('>');
    for (var i=0, temp=null, row=null; i<rs.length; i++){
        if (rs[i] == ''){
            continue;
        }
        temp = rs[i].split("<");
        row = {};
        for (var j=0; j<temp.length; j++){
            if (typeof me.tp_columns[grid_id][j+1] == 'undefined'){
                continue;
            }
            row[me.tp_columns[grid_id][j+1].name] = temp[j];
        }
        $('#grid_'+grid_id).jqGrid('addRowData', i, row);
    }
}

TermParams.prototype.getRules = function(){
    var me = this;
    for (var i=0, arr=[], str, grid_id; i<me.tp_grids.length; i++){
        arr.length = 0;
        str = '';
        grid_id = me.tp_grids[i];

        var ids = $('#grid_'+grid_id).jqGrid('getDataIDs');
        for (var key = 0; key<ids.length; key++){
            $('#grid_'+grid_id).jqGrid('saveRow', ids[key], false, 'clientArray');
        }

        var rows = $('#grid_'+grid_id).jqGrid('getRowData');
        for (var j=0, row=null; j<rows.length; j++) {
            row = [];
            for (var k=1, v=''; k<me.tp_columns[grid_id].length; k++){
                v = rows[j][me.tp_columns[grid_id][k].name];
                if (me.tp_columns[grid_id][k].edittype == 'select') {
                    // editoptions:{value:"0:NONE;1:TCP;2:UDP;3:ICMP"}
                    // editoptions:{value:"1:NONE(xx);2:TCP(xx);3:UDP(xx)"}
                    v = v.replace('(',"\\(");
                    v = v.replace(')',"\\)");
                    var r = new RegExp("(\\d):"+v);
                    v2 = r.exec(me.tp_columns[grid_id][k].editoptions.value);
                    v = v2 ? v2[1] : v;
                }
                row.push(v);
            }
            //带宽限速-尾部补0<0
            if (grid_id == 'new_qoslimit_rules' && row.length != 0){
                row.push('0');
                row.push('0');
            }
            arr.push(row.join('<'));
        }
        // if (arr.length != 0){
            str = arr.join(">");
            if (str != me.rules[grid_id]){
                me.changedParams[grid_id] = str;
            }
        // }
    }
}

TermParams.prototype.rboot = function(){
    var enable = $('#tpid_rboot_enabled').is(':checked') ? 1 : 0, interval = 0;
    console.log(enable);
    for (var i=0; i<=6; i++){
        interval += $('#ck_days_'+i).is(':checked') ? Math.pow(2, i) : 0;
    }
    var time = $('#tpid_rboot_time').val(), rboot_every = $('#tpid_rboot_every').val();
    var value = enable+','+(time != 'e' ? time : -rboot_every)+','+interval;
    this.saveChangedParams('sch_rboot', value, time == '-100');
}

// 不需要修改的参数名称，这些参数为其它参数服务
TermParams.prototype.noNeedChangeParams = ['ddnsx_ip_type', 'service0', 'user0', 'pass0', 'host0', 'wild0', 'mx0', 'bmx0', 'service1', 'user1', 'pass1', 'host1', 'wild1', 'mx1', 'bmx1', 'enable_upnp', 'enable_natpmp', 'http_local', 'http_remote', 'http_wireless'];

// 修改赋值
TermParams.prototype.bindEvent = function(){
    var _form, _fields, me = this, name = '', value = '';
    $(".params-tab-content form").each(function(){
        _form = $(this);
        _fields = _form.find('input,select,textarea');
        _fields.each(function(){
            if (this.id.indexOf('tpid_') == -1) {
                return true;
            }
            $(this).on('change', function(){
                if (!me.paramsLoading){
                    name = this.name;
                    if (this.type == 'checkbox') {
                        var chk_vals = get_checkbox_check_val($(this), name);
                        value = $(this).is(':checked') ? chk_vals[0] : chk_vals[1];
                    } else {
                        if (this.name == 'wan_dns' || this.name == 'wan_dns_part2') {
                            var w2 = $.trim($('#tpid_wan_dns_part2').val());
                            value = $('#tpid_wan_dns').val() + (w2==''?'':(' '+w2));
                            name = 'wan_dns';
                        } else if (this.name == 'custom_dns_part1' || this.name == 'custom_dns_part2') {
                            var w2 = $.trim($('#tpid_custom_dns_part2').val());
                            value = $('#tpid_custom_dns_part1').val() + (w2==''?'':(' '+w2));
                            name = 'custom_dns';
                        } else if (this.name == 'dns_1' || this.name == 'dns_2') {
                            var w2 = $.trim($('#tpid_dns_2').val());
                            value = $('#tpid_dns_1').val() + (w2==''?'':(' '+w2));
                            name = 'wan1_get_dns';
                        }else{
                            value = $(this).val();
                        }
                    }

                    if (this.name == 'wan_proto' && value == 'ppp3g' && $('#tpid_is_ecm_dial').val() == '1'){
                        value = 'dhcp';
                    }else if (this.name == 'is_ecm_dial' && value == '1' && $('#tpid_wan_proto').val() == 'ppp3g'){
                        me.saveChangedParams('wan_proto', 'dhcp');
                    }

                    //唤醒类型
                    if (this.name.indexOf('dtu_wakeup_type_bit') != -1){
                        var bit2 = $('#tpid_dtu_wakeup_type_bit2').is(':checked') ? 1 : 0,
                            bit1 = $('#tpid_dtu_wakeup_type_bit1').is(':checked') ? 1 : 0,
                            bit0 = $('#tpid_dtu_wakeup_type_bit0').is(':checked') ? 1 : 0;
                        me.saveChangedParams('dtu_wakeup_type', bit2*4+bit1*2+bit0);
                        return;
                    }

                    //定时重启
                    if (this.name == 'rboot_enabled' || this.name == 'rboot_time' || this.name == 'rboot_every'){
                        if (this.name == 'rboot_time') {
                            $(this).val() == 'e' ? $('#tpid_rboot_every').removeAttr('disabled') : $('#tpid_rboot_every').attr('disabled',true);
                        }
                        me.rboot();
                        return;
                    }

                    // 动态域名
                    if (this.name == 'ddnsx_ip_type') {
                        me.saveChangedParams('ddnsx_ip', 'wan');
                    }
                    if ($.inArray(this.name, ['service0', 'user0', 'pass0', 'host0', 'wild0', 'mx0', 'bmx0']) != -1) {
                        me.saveChangedParams('ddnsx0', $('#tpid_service0').val()+'<'+$('#tpid_user0').val()+':'+$('#tpid_pass0').val()+'<'+$('#tpid_host0').val()+'<'+($('#tpid_wild0').is(':checked') ? '1' : '0')+'<'+$('#tpid_mx0').val()+'<'+($('#tpid_bmx0').is(':checked') ? '1' : '0')+'<');
                    }
                    if ($.inArray(this.name, ['service1', 'user1', 'pass1', 'host1', 'wild1', 'mx1', 'bmx1']) != -1) {
                        me.saveChangedParams('ddnsx1', $('#tpid_service1').val()+'<'+$('#tpid_user1').val()+':'+$('#tpid_pass1').val()+'<'+$('#tpid_host1').val()+'<'+($('#tpid_wild1').is(':checked') ? '1' : '1')+'<'+$('#tpid_mx1').val()+'<'+($('#tpid_bmx0').is(':checked') ? '1' : '0')+'<');
                    }

                    // 启用UPnP，启用NAT-PMP
                    if (this.name == 'enable_upnp' || this.name == 'enable_natpmp') {
                        var upnp_enable = 0;
                        if ($('#tpid_enable_upnp').is(':checked')) upnp_enable = 1;
                        if ($('#tpid_enable_natpmp').is(':checked')) upnp_enable |= 2;
                        me.saveChangedParams('upnp_enable',  upnp_enable);
                    }

                    // 修改lan1 ~ lan4时要同时下发lan_ifname=br0 ~ br3
                    var lan_index = $.inArray(this.name, ['lan_ipaddr', 'lan1_ipaddr', 'lan2_ipaddr', 'lan3_ipaddr']);
                    if (lan_index != -1) {
                        me.saveChangedParams('lan'+(lan_index == 0 ? '' : lan_index)+'_ifname',  'br'+lan_index);
                    }

                    // 修改wifi1 ~ wifi4时要同时下发lan_ifnames [vlan1 eth1, wl0.1, wl0.2, wl0.3]
                    var wifi_index = $.inArray(this.name, ['wl0_radio', 'wl0.1_radio', 'wl0.2_radio', 'wl0.3_radio']);
                    if (wifi_index != -1) {
                        me.saveChangedParams('lan'+(wifi_index == 0 ? '' : wifi_index)+'_ifnames',  wifi_index == 0 ? 'vlan1 eth1' : ('wl0.'+wifi_index));
                        me.saveChangedParams('wl0'+(wifi_index == 0 ? '' : ('.'+wifi_index))+'_ifname',  wifi_index == 0 ? 'eth1' : ('wl0.'+wifi_index));
                        if (value == 1) {
                            // 开启wifi时，要同时提交无线模式参数
                            var wifi_mode_name = wifi_index == 0? 'wl0_mode' : ('wl0.'+wifi_index+'_mode'),
                                _wep_name = wifi_index == 0 ? 'wl_wep' : ('wl0.'+wifi_index+'_wep'),
                                _bss_name = wifi_index == 0 ? 'wl_bss_enabled' : ('wl0.'+wifi_index+'_bss_enabled');
                            me.saveChangedParams(wifi_mode_name, $('[id="tpid_'+wifi_mode_name+'"]').val());
                            me.saveChangedParams(_wep_name, 'enabled');  // 附加参数 _wep
                            me.saveChangedParams(_bss_name, 1);          // 附加参数 _bss_enabled
                            me.saveChangedParams('wl0'+(wifi_index == 0 ? '' : ('.'+wifi_index))+'_key', 1);                // 附加参数：wl0_key， wl0.1_key
                            me.saveChangedParams('wl0'+(wifi_index == 0 ? '' : ('.'+wifi_index))+'_auth_mode', 'none');     // 附加参数：wl0_auth_mode，wl0.1_auth_mode
                            me.saveChangedParams('wl0'+(wifi_index == 0 ? '' : ('.'+wifi_index))+'_auth', 0);               // 附加参数：wl0_auth，wl0.1_auth
                            me.saveChangedParams('wl0'+(wifi_index == 0 ? '' : ('.'+wifi_index))+'_akm', '');               // 附加参数：wl0_akm，wl0.1_akm
                            // 多SSID中新增的都要在wl0_vifs中添加相应的接口
                            // 如果是5G的WIFI那就在wl1_vifs中添加相应的接口
                            // wl0_vifs = wl0.1 wl0.2
                            if (wifi_index != 0) {
                                var wl0_vifs = $('#tpid_wl0_vifs').val().trim(), wifi_name = 'wl0.'+wifi_index;
                                if (wl0_vifs.indexOf(wifi_name) == -1) {
                                    $('#tpid_wl0_vifs').val($.trim(wl0_vifs + ' ' + wifi_name));
                                    me.saveChangedParams('wl0_vifs', $.trim(wl0_vifs + ' ' + wifi_name));
                                }
                            }
                        }
                    }

                    // 两个openvpn的经由internet网络参数，vpn_client1_eas，vpn_client2_eas是合并到一个参数内的 vpn_client_eas
                    if (this.name == 'vpn_client1_eas' || this.name == 'vpn_client2_eas') {
                        me.saveChangedParams('vpn_client1_eas', $('#tpid_vpn_client1_eas').is(':checked') ? 1 : 0);
                        me.saveChangedParams('vpn_client2_eas', $('#tpid_vpn_client2_eas').is(':checked') ? 1 : 0);
                    }

                    // 访问设置：本地访问，远程访问参数，无线访问
                    if (this.name == 'http_local' || this.name == 'http_remote') {
                        var a = $('#tpid_http_local').val() * 1, b = $('#tpid_http_remote').val();
                        me.saveChangedParams('http_enable', (a & 1) ? 1 : 0);
                        me.saveChangedParams('https_enable', (a & 2) ? 1 : 0);
                        me.saveChangedParams('remote_management', (b != 0) ? 1 : 0);
                        me.saveChangedParams('remote_mgt_https', (b == 2) ? 1 : 0);
                    }
                    if (this.name == 'http_wireless') {
                        me.saveChangedParams('web_wl_filter', $('#tpid_http_wireless').is(':checked') ? 0 : 1);
                    }

                    if ($.inArray(this.name, me.noNeedChangeParams) == -1) {
                        me.saveChangedParams(name, value, this.type=='select-one' && $(this).val() == '-100');
                    }
                }

                //Cascade
                if (this.name == 'dualsim') {
                    $('#tpid_dualsim_fieldset_1').toggle($(this).val() == '3');
                } else if (this.name == 'wl0_radio'){
                    $('#tpid_wifi_fieldset_1').toggle($(this).is(':checked'));
                } else if (this.name == 'wl1_radio'){
                    $('#tpid_wifi_fieldset_2').toggle($(this).is(':checked'));
                } else if (this.name == 'dtu_mode1'){
                    $('#tpid_gps_fieldset_1').toggle($(this).val() == 'server');
                    $('#tpid_gps_fieldset_3').toggle($(this).val() == 'client');
                } else if (this.name == 'gps_data'){
                    $('#tpid_gps_fieldset_2').toggle($(this).val() == 'm2m_fmt');
                } else if (this.name == 'tm_sel'){
                    var v = $(this).val();
                    $('#tpid_tm_tz').parents('.form-group').toggle(v == 'custom');
                    $('#tpid_tm_dst').parents('.form-group').toggle(v != 'custom' && v != '-100');
                } else if (this.name == 'ntp_updates'){
                    var v = parseInt($(this).val());
                    $('#tpid_ntp_tdod').parents('.form-group').toggle(v > 0);
                    $('#tpid_ntp_server').parents('.form-group').toggle(v > -1);
                } else if (this.name == 'dhcpd_dmdns') {
                    $('#tpid_wan_dns').parents('.form-group').toggle(!$(this).is(':checked'));
                    $('#tpid_wan_dns_part2').parents('.form-group').toggle(!$(this).is(':checked'));
                } else if (this.name == 'dtu_run_type') {
                    $('#tpid_dtu_wakeup_type_fieldset').toggle($(this).val() == '1');
                    $('#tpid_dtu_wakeup_time').parents('.form-group').toggle($(this).val() == '1');
                    $('#tpid_dtu_idle_time').parents('.form-group').toggle($(this).val() == '1');
                    $('#tpid_dtu_reconnect_interval').parents('.form-group').toggle($(this).val() == '0');
                } else if (this.name == 'new_qoslimit_enable'){
                    // $('#tpid_qosl_fieldset').toggle($(this).is(':checked'));
                    var toggle_arrs = ['qos_ibw', 'qos_obw'];
                } else if (this.name == 'qosl_enable'){
                    var toggle_arrs = ['qosl_dlr', 'qosl_dlc', 'qosl_ulr', 'qosl_ulc'];
                } else if (this.name == 'wan_proto'){
                    var v = $(this).val(),
                        controls = ['ppp_username', 'ppp_passwd', 'ppp_service', 'wan_demand', 'ppp_redialperiod', 'ppp_idletime', 'wan_ipaddr', 'wan_netmask', 'wan_gateway', 'is_ecm_dial', 'wan1_mtu', 'ppp_mlppp', 'wan1_ipaddr', 'wan1_netmask', 'wan1_gateway', 'dns_1', 'dns_2'],
                        elms = {
                            '-100': [],
                            'disabled': [],
                            'dhcp': ['wan1_mtu', 'wan_aslan'],
                            'pppoe': ['ppp_username', 'ppp_passwd', 'ppp_service', 'wan_demand', 'wan1_mtu', 'ppp_mlppp', 'wan_aslan'],
                            'static': ['wan_ipaddr', 'wan_netmask', 'wan_gateway', 'wan1_mtu', 'wan_aslan', 'wan1_ipaddr', 'wan1_netmask', 'wan1_gateway', 'dns_1', 'dns_2'],
                            'ppp3g': ['is_ecm_dial', 'wan_aslan']
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                } else if (this.name == 'service0' || this.name == 'service1'){
                    if(this.name == 'service0'){
                        var i = 0;
                    }else if(this.name == 'service1'){
                        var i = 1;
                    }
                    var  v = $(this).val();
                        //修改a标签的href链接：
                        $('#tpid_hyperlink' + i).attr('href',$.gf.device_params_define_router_url[v]); 
                        //修改文字：
                        $("#tpid_hyperlink" + i).text($.gf.device_params_define_router_url[v]);
                    var controls = ['hyperlink' + i,'cust' + i ,'hosttop' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i,'opendns' + i,'afraid' + i, 'force' + i],
                        elms = {
                            '0': [],
                            '3322': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'force' + i],
                            '3322-static': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'force' + i],
                            'dnsexit': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'dnsomatic': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'dyndns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'dyndns-static': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'dyndns-custom': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'sdyndns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'sdyndns-static': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'sdyndns-custom': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'bmx' + i, 'ddnsx_save' + i, 'force' + i],
                            'dyns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'easydns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'force' + i],
                            'seasydns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'wild' + i, 'mx' + i, 'force' + i],
                            'editdns': ['hyperlink' + i,'host' + i, 'pass' + i, 'force' + i],
                            'everydns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'minidns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'enom': ['hyperlink' + i,'hosttop' + i, 'user' + i, 'pass' + i, 'force' + i],
                            'afraid': ['hyperlink' + i,'afraid' + i, 'force' + i],
                            'heipv6tb': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'ieserver': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'namecheap': ['hyperlink' + i,'hosttop' + i, 'user' + i, 'pass' + i, 'force' + i],
                            'noip': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'opendns': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i,'opendns' + i, 'force' + i],
                            'tzo': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'zoneedit': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'szoneedit': ['hyperlink' + i, 'user' + i, 'pass' + i, 'host' + i, 'force' + i],
                            'custom': ['cust' + i, 'force' + i],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'wl0_mode' || this.name == 'wl0.1_mode' || this.name == 'wl0.2_mode' || this.name == 'wl0.3_mode'){
                    if(this.name == 'wl0_mode'){
                        var i = '';
                    }else if(this.name == 'wl0.1_mode'){
                        var i = '.1';
                    }else if(this.name == 'wl0.2_mode'){
                        var i = '.2';
                    }else if(this.name == 'wl0.3_mode'){
                        var i = '.3';
                    }
                    var v = $(this).val(),
                        controls = ['wl0'+ i +'_net_mode', 'wl0'+ i +'_ssid', 'wl0'+ i +'_closed', 'wl0'+ i +'_channel', 'wl0'+ i +'_nbw_cap', 'wl0'+ i +'_maxassoc', 'wl0'+ i +'_security_mode', 'wl0'+ i +'_sta_proto', 'wl0'+ i +'_sta_mtu'],
                        elms = {
                            '-100': [],
                            'ap': ['wl0'+ i +'_net_mode','wl0'+ i +'_ssid','wl0'+ i +'_closed','wl0'+ i +'_channel','wl0'+ i +'_nbw_cap','wl0'+ i +'_maxassoc','wl0'+ i +'_security_mode'],
                            'sta': ['wl0'+ i +'_net_mode','wl0'+ i +'_ssid','wl0'+ i +'_security_mode','wl0'+ i +'_sta_proto','wl0'+ i +'_sta_mtu'],
                            'wet': ['wl0'+ i +'_net_mode','wl0'+ i +'_ssid','wl0'+ i +'_security_mode'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('[id = "tpid_' + controls[j] + '"]').parents('.form-group').show();
                        }else{
                            $('[id = "tpid_' + controls[j] + '"]').parents('.form-group').hide();
                        }
                    }
                    if ($('[id = "tpid_wl0'+ i +'_nbw_cap"]').parents('.form-group').is(':hidden')){
                        $('[id = "tpid_wl0'+ i +'_nctrlsb"]').parents('.form-group').hide();
                    }else if ($('[id = "tpid_wl0'+ i +'_nbw_cap"]').val() == '1' && $('[id = "tpid_wl0'+ i +'_nbw_cap"]').parents('.form-group').is(':visible')){
                        $('[id = "tpid_wl0'+ i +'_nctrlsb"]').parents('.form-group').show();
                    }
                    if($('[id = "tpid_wl0'+ i +'_sta_proto"]').parents('.form-group').is(':hidden')){
                        $('[id = "tpid_wl0'+ i +'_sta_ipaddr"]').parents('.form-group').hide();
                        $('[id = "tpid_wl0'+ i +'_sta_netmask"]').parents('.form-group').hide();
                        $('[id = "tpid_wl0'+ i +'_sta_gateway"]').parents('.form-group').hide();
                        $('[id = "tpid_wl0'+ i +'_sta_dns_1"]').parents('.form-group').hide();
                        $('[id = "tpid_wl0'+ i +'_sta_dns_2"]').parents('.form-group').hide();
                    }else if ($('[id= "tpid_wl0'+ i +'_sta_proto"]').val() == 'static' && $('[id = "tpid_wl0'+ i +'_sta_proto"]').parents('.form-group').is(':visible')){
                        $('[id = "tpid_wl0'+ i +'_sta_ipaddr"]').parents('.form-group').show();
                        $('[id = "tpid_wl0'+ i +'_sta_netmask"]').parents('.form-group').show();
                        $('[id = "tpid_wl0'+ i +'_sta_gateway"]').parents('.form-group').show();
                        $('[id = "tpid_wl0'+ i +'_sta_dns_1"]').parents('.form-group').show();
                        $('[id = "tpid_wl0'+ i +'_sta_dns_2"]').parents('.form-group').show();
                    }
                }else if (this.name == 'wl0_security_mode' || this.name == 'wl0.1_security_mode' || this.name == 'wl0.2_security_mode' || this.name == 'wl0.3_security_mode'){
                    if(this.name == 'wl0_security_mode'){
                        var i = '';
                    }else if(this.name == 'wl0.1_security_mode'){
                        var i = '.1';
                    }else if(this.name == 'wl0.2_security_mode'){
                        var i = '.2';
                    }else if(this.name == 'wl0.3_security_mode'){
                        var i = '.3';
                    }
                    var v = $(this).val(),
                        controls = ['wl0'+ i +'_crypto', 'wl0'+ i +'_wpa_psk',  'wl0'+ i +'_radius_key','wl0'+ i +'_wpa_gtk_rekey', 'wl0'+ i +'_wep_bit', 'wl0'+ i +'_passphrase', 'wl0'+ i +'_radius_ipaddr', 'wl0'+ i +'_key1', 'wl0'+ i +'_key2', 'wl0'+ i +'_key3', 'wl0'+ i +'_key4', 'wl0'+ i +'_sta_proto', 'wl0'+ i +'_sta_mtu'],
                        elms = {
                            '-100': [],
                            'disabled': [],
                            'wep': ['wl0'+ i +'_wep_bit','wl0'+ i +'_passphrase','wl0'+ i +'_key1','wl0'+ i +'_key2','wl0'+ i +'_key3','wl0'+ i +'_key4'],
                            'wpa_personal': ['wl0'+ i +'_crypto','wl0'+ i +'_wpa_psk','wl0'+ i +'_wpa_gtk_rekey'],
                            'wpa_enterprise': ['wl0'+ i +'_crypto','wl0'+ i +'_radius_key','wl0'+ i +'_wpa_gtk_rekey','wl0'+ i +'_radius_ipaddr'],
                            'wpa2_personal': ['wl0'+ i +'_crypto','wl0'+ i +'_wpa_psk','wl0'+ i +'_wpa_gtk_rekey'],
                            'wpa2_enterprise': ['wl0'+ i +'_crypto','wl0'+ i +'_radius_key','wl0'+ i +'_wpa_gtk_rekey','wl0'+ i +'_radius_ipaddr'],
                            'wpaX_personal': ['wl0'+ i +'_crypto','wl0'+ i +'_wpa_psk','wl0'+ i +'_wpa_gtk_rekey'],
                            'wpaX_enterprise': ['wl0'+ i +'_crypto','wl0'+ i +'_radius_key','wl0'+ i +'_wpa_gtk_rekey','wl0'+ i +'_radius_ipaddr'],
                            'radius': ['wl0'+ i +'_radius_key','wl0'+ i +'_wpa_gtk_rekey','wl0'+ i +'_radius_ipaddr','wl0'+ i +'_wep_bit','wl0'+ i +'_passphrase','wl0'+ i +'_key1','wl0'+ i +'_key2','wl0'+ i +'_key3','wl0'+ i +'_key4'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('[id = "tpid_'+controls[j] + '"]').parents('.form-group').show();
                        }else{
                            $('[id = "tpid_'+controls[j] + '"]').parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'wl0_sta_proto' || this.name == 'wl0.1_sta_proto' || this.name == 'wl0.2_sta_proto' || this.name == 'wl0.3_sta_proto'){
                    if(this.name == 'wl0_sta_proto'){
                        var i = '';
                    }else if(this.name == 'wl0.1_sta_proto'){
                        var i = '.1';
                    }else if(this.name == 'wl0.2_sta_proto'){
                        var i = '.2';
                    }else if(this.name == 'wl0.3_sta_proto'){
                        var i = '.3';
                    }
                    var v = $(this).val(),
                        controls = ['wl0'+ i +'_sta_ipaddr', 'wl0'+ i +'_sta_netmask',  'wl0'+ i +'_sta_gateway','wl0'+ i +'_sta_dns_1', 'wl0'+ i +'_sta_dns_2'],
                        elms = {
                            '-100': [],
                            'dhcp': [],
                            'static': ['wl0'+ i +'_sta_ipaddr', 'wl0'+ i +'_sta_netmask',  'wl0'+ i +'_sta_gateway','wl0'+ i +'_sta_dns_1', 'wl0'+ i +'_sta_dns_2'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('[id = "tpid_'+controls[j] + '"]').parents('.form-group').show();
                        }else{
                            $('[id = "tpid_'+controls[j] + '"]').parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'ipoc_mode'){
                    var v = $(this).val();
                    var elms = {
                        serial: ['dtu_mode'],
                        modbus: ['modbus_mode'],
                        dt: ['dt_phone', 'max_lost_ack', 'dt_fail_action'],
                    }
                    for (var x in elms) {
                        elms[x].forEach(function(item, idx, self){
                            var tmp_o = $('#tpid_'+item).parents('.form-group');
                            v == x ? tmp_o.show() : tmp_o.hide();
                        });
                    }
                    $('#tpid_modbus_mode').val('0').change();
                    $('#tpid_dtu_mode').val('disable').change();
                    if (v == 'dt') {
                        $('#tpid_dtu_mode').val('client').change();
                    }
                }else if (this.name == 'dtu_mode'){
                    var v = $(this).val(),
                        controls = ['local_port', 'socket_type',  'socket_timeout', 'serial_timeout', 'packet_len', 'cache_enable', 'debug_enable', 'debug_num', 'serial_rate','serial_parity','serial_databits','serial_stopbits','server_ip','heartbeat_intval'],
                        elms = {
                            '-100': [],
                            'disable': [],
                            'server': ['local_port', 'socket_type',  'socket_timeout','serial_timeout', 'packet_len', 'cache_enable', 'debug_enable', 'debug_num', 'serial_rate','serial_parity','serial_databits','serial_stopbits'],
                            'client': ['server_ip','socket_type','socket_timeout', 'serial_timeout','packet_len','heartbeat_intval', 'cache_enable', 'debug_enable', 'debug_num', 'serial_rate', 'serial_parity', 'serial_databits', 'serial_stopbits'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'modbus_mode'){
                    var v = $(this).val(),
                        controls = ['modbus_tcp_mode', 'modbus_protocol',  'modbus_serial_rate', 'modbus_serial_parity', 'modbus_serial_databits', 'modbus_serial_stopbits'],
                        elms = {
                            '-100': [],
                            '0': [],
                            '1': ['modbus_tcp_mode', 'modbus_protocol',  'modbus_serial_rate', 'modbus_serial_parity', 'modbus_serial_databits', 'modbus_serial_stopbits'],
                        };
                  for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'modbus_tcp_mode' && $('#tpid_ipoc_mode').val() == 'modbus'){
                    var v = $(this).val(), a = $('#tpid_modbus_server_domain').parents('.form-group'), b = $('#tpid_modbus_bind_port').parents('.form-group');
                    v == '0' ? a.show() : a.hide();
                    v == '1' ? b.show() : b.hide();
                }else if (this.name == 'http_local'){
                    var v = $(this).val(),
                        controls = ['http_lanport', 'https_lanport',  'https_crt_cn', 'https_crt_gen', 'https_crt_save'],
                        elms = {
                            '-100': [],
                            '0': [],
                            '1': ['http_lanport'],
                            '2': ['https_lanport', 'https_crt_cn',  'https_crt_gen', 'https_crt_save'],
                            '3': ['http_lanport', 'https_lanport',  'https_crt_cn', 'https_crt_gen', 'https_crt_save'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'ipsec1_ext'){
                    var v = $(this).val(),
                        controls = ['ipsec1_leftsubnet', 'ipsec1_rightsubnet'],
                        elms = {
                            '-100': [],
                            '0': ['ipsec1_leftsubnet', 'ipsec1_rightsubnet'],
                            '1': [],
                            '2': [],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'ipsec2_ext'){
                    var v = $(this).val(),
                        controls = ['ipsec2_leftsubnet', 'ipsec2_rightsubnet'],
                        elms = {
                            '-100': [],
                            '0': ['ipsec2_leftsubnet', 'ipsec2_rightsubnet'],
                            '1': [],
                            '2': [],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'vpn_client1_crypt'){
                    var v = $(this).val(),
                        controls = ['vpn_client1_userauth', 'vpn_client1_hmac', 'vpn_client1_remote','vpn_client1_adns','vpn_client1_reneg','vpn_client1_tlsremote','vpn_client1_static','vpn_client1_ca','vpn_client1_crt','vpn_client1_key'],
                        elms = {
                            '-100': [],
                            'tls': ['vpn_client1_userauth', 'vpn_client1_hmac','vpn_client1_adns','vpn_client1_reneg','vpn_client1_tlsremote','vpn_client1_ca','vpn_client1_crt','vpn_client1_key'],
                            'secret': ['vpn_client1_remote','vpn_client1_static'],
                            'custom': [],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                    if($('#tpid_vpn_client1_crypt').val() != 'tls'){
                        $('#tpid_vpn_client1_username').parents('.form-group').hide();
                        $('#tpid_vpn_client1_password').parents('.form-group').hide();
                        $('#tpid_vpn_client1_useronly').parents('.form-group').hide();
                    }else if($('#tpid_vpn_client1_crypt').val() == 'tls' && $('#tpid_vpn_client1_userauth').is(':checked')){
                        $('#tpid_vpn_client1_username').parents('.form-group').show();
                        $('#tpid_vpn_client1_password').parents('.form-group').show();
                        $('#tpid_vpn_client1_useronly').parents('.form-group').show();
                    }

                    if($('#tpid_vpn_client1_crypt').val() == 'secret' && $('#tpid_vpn_client1_if').val() == 'tun'){
                        $('#tpid_vpn_client1_remote').parents('.form-group').show();
                    }else if($('#tpid_vpn_client1_crypt').val() != 'secret' || $('#tpid_vpn_client1_if').val() != 'tun'){
                        $('#tpid_vpn_client1_remote').parents('.form-group').hide();
                    }
                }else if (this.name == 'vpn_client1_hmac' && $('#tpid_vpn_client1_hmac').is(':visible')){
                    var v = $(this).val(),
                        controls = ['vpn_client1_static'],
                        elms = {
                            '-100': [],
                            '-1': [],
                            '2': ['vpn_client1_static'],
                            '0': ['vpn_client1_static'],
                            '1': ['vpn_client1_static'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'vpn_client1_useronly' && $('#tpid_vpn_client1_useronly').is(':visible')){
                   if($('#tpid_vpn_client1_useronly').is(':checked')){
                        $('#tpid_vpn_client1_crt').parents('.form-group').hide();
                        $('#tpid_vpn_client1_key').parents('.form-group').hide();
                    }else{
                        $('#tpid_vpn_client1_crt').parents('.form-group').show();
                        $('#tpid_vpn_client1_key').parents('.form-group').show();
                    }
                }else if (this.name == 'vpn_client2_crypt'){
                    var v = $(this).val(),
                        controls = ['vpn_client2_userauth', 'vpn_client2_hmac','vpn_client2_remote','vpn_client2_adns','vpn_client2_reneg','vpn_client2_tlsremote','vpn_client2_static','vpn_client2_ca','vpn_client2_crt','vpn_client2_key'],
                        elms = {
                            '-100': [],
                            'tls': ['vpn_client2_userauth', 'vpn_client2_hmac','vpn_client2_adns','vpn_client2_reneg','vpn_client2_tlsremote','vpn_client2_ca','vpn_client2_crt','vpn_client2_key'],
                            'secret': ['vpn_client2_remote','vpn_client2_static'],
                            'custom': [],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                    if($('#tpid_vpn_client2_crypt').val() != 'tls'){
                        $('#tpid_vpn_client2_username').parents('.form-group').hide();
                        $('#tpid_vpn_client2_password').parents('.form-group').hide();
                        $('#tpid_vpn_client2_useronly').parents('.form-group').hide();
                    }else if($('#tpid_vpn_client2_crypt').val() == 'tls' && $('#tpid_vpn_client2_userauth').is(':checked')){
                        $('#tpid_vpn_client2_username').parents('.form-group').show();
                        $('#tpid_vpn_client2_password').parents('.form-group').show();
                        $('#tpid_vpn_client2_useronly').parents('.form-group').show();
                    }

                    if($('#tpid_vpn_client2_crypt').val() == 'secret' && $('#tpid_vpn_client2_if').val() == 'tun'){
                        $('#tpid_vpn_client2_remote').parents('.form-group').show();
                    }else if($('#tpid_vpn_client2_crypt').val() != 'secret' || $('#tpid_vpn_client2_if').val() != 'tun'){
                        $('#tpid_vpn_client2_remote').parents('.form-group').hide();
                    }
                }else if (this.name == 'vpn_client2_hmac' && $('#tpid_vpn_client2_hmac').is(':visible')){
                    var v = $(this).val(),
                        controls = ['vpn_client2_static'],
                        elms = {
                            '-100': [],
                            '-1': [],
                            '2': ['vpn_client2_static'],
                            '0': ['vpn_client2_static'],
                            '1': ['vpn_client2_static'],
                        };
                    for (var j=0; j<controls.length; j++){
                        if ($.inArray(controls[j], elms[v]) != -1){
                            $('#tpid_'+controls[j]).parents('.form-group').show();
                        }else{
                            $('#tpid_'+controls[j]).parents('.form-group').hide();
                        }
                    }
                }else if (this.name == 'vpn_client2_useronly'){
                    if($('#tpid_vpn_client2_useronly').is(':visible') && $('#tpid_vpn_client2_useronly').is(':checked')){
                        $('#tpid_vpn_client2_crt').parents('.form-group').hide();
                        $('#tpid_vpn_client2_key').parents('.form-group').hide();
                    }else{
                        $('#tpid_vpn_client2_crt').parents('.form-group').show();
                        $('#tpid_vpn_client2_key').parents('.form-group').show();
                    }
                }else if (this.name == 'http_remote'){
                    var v = $(this).val(), a = $('#tpid_http_wanport').parents('.form-group'), b = $('#tpid_rmgt_sip').parents('.form-group');
                    v == '1' || v == '2'? a.show() : a.hide();
                    v == '1' || v == '2'? b.show() : b.hide();
                }else if (this.name == 'wan_demand'){
                    var v = $(this).val(), a = $('#tpid_ppp_redialperiod').parents('.form-group'), b = $('#tpid_ppp_idletime').parents('.form-group');
                    v == '0' ? a.show() : a.hide();
                    v == '1' ? b.show() : b.hide();
                }else if (this.name == 'vpn_client1_if'){
                    var v = $(this).val(), 
                    a = $('#tpid_vpn_client1_remote').parents('.form-group'), 
                    b = $('#tpid_vpn_client1_bridge').parents('.form-group'), 
                    c = $('#tpid_vpn_client1_nat').parents('.form-group'), 
                    d = $('#tpid_vpn_client1_gw').parents('.form-group'), 
                    e = $('#tpid_vpn_client1_nm').parents('.form-group');
                    if(v == 'tun' && $('#tpid_vpn_client1_crypt').val() == 'secret'){
                        a.show();
                    }else if(v != 'tun' || $('#tpid_vpn_client1_crypt').val() != 'secret'){
                        a.hide();
                    }
                    v == 'tap' ? b.show() : b.hide();
                    if(v == 'tun' && $('#tpid_vpn_client1_firewall').val() == 'auto'){
                        c.show();
                    }else if(v != 'tun' || !$('#tpid_vpn_client1_firewall').val() != 'auto'){
                        c.hide();
                    }
                    if(v == 'tap' && $('#tpid_vpn_client1_rgw').is(':checked')){
                        d.show();
                    }else if(v != 'tap' || !$('#tpid_vpn_client1_rgw').is(':checked')){
                        d.hide();
                    }
                }else if (this.name == 'vpn_client1_firewall'){
                    var v = $(this).val(), 
                    a = $('#tpid_vpn_client1_nat').parents('.form-group');
                    if(v == 'auto' && $('#tpid_vpn_client1_if').val() == 'tun'){
                        a.show();
                    }else if(v != 'auto' || $('#tpid_vpn_client1_if').val() != 'tun'){
                        a.hide();
                    }
                }else if (this.name == 'vpn_client2_if'){
                    var v = $(this).val(), 
                    a = $('#tpid_vpn_client2_remote').parents('.form-group'), 
                    b = $('#tpid_vpn_client2_bridge').parents('.form-group'), 
                    c = $('#tpid_vpn_client2_nat').parents('.form-group'), 
                    d = $('#tpid_vpn_client2_gw').parents('.form-group'), 
                    e = $('#tpid_vpn_client2_nm').parents('.form-group');
                    if(v == 'tun' && $('#tpid_vpn_client2_crypt').val() == 'secret'){
                        a.show();
                    }else if(v != 'tun' || $('#tpid_vpn_client2_crypt').val() != 'secret'){
                        a.hide();
                    }
                    v == 'tap' ? b.show() : b.hide();
                    if(v == 'tun' && $('#tpid_vpn_client2_firewall').val() == 'auto'){
                        c.show();
                    }else if(v != 'tun' || !$('#tpid_vpn_client2_firewall').val() != 'auto'){
                        c.hide();
                    }
                    if(v == 'tap' && $('#tpid_vpn_client2_rgw').is(':checked')){
                        d.show();
                    }else if(v != 'tap' || !$('#tpid_vpn_client2_rgw').is(':checked')){
                        d.hide();
                    }
                }else if (this.name == 'vpn_client2_firewall'){
                    var v = $(this).val(), 
                    a = $('#tpid_vpn_client2_nat').parents('.form-group');
                    if(v == 'auto' && $('#tpid_vpn_client2_if').val() == 'tun'){
                        a.show();
                    }else if(v != 'auto' || $('#tpid_vpn_client2_if').val() != 'tun'){
                        a.hide();
                    }
                }else if (this.name == 'ddnsx_ip_type'){
                    var v = $(this).val(), a = $('#tpid_ddnsx_ip').parents('.form-group');
                    v == 'custom' ? a.show() : a.hide();
                }else if (this.name == 'wl0_nbw_cap'){
                    var v = $(this).val(), a = $('#tpid_wl0_nctrlsb').parents('.form-group');
                    v == '1' ? a.show() : a.hide();
                }else if (this.name == 'wl0.1_nbw_cap'){
                    var v = $(this).val(), a = $('[id = "tpid_wl0.1_nctrlsb"]').parents('.form-group');
                    v == '1' ? a.show() : a.hide();
                }else if (this.name == 'wl0.2_nbw_cap'){
                    var v = $(this).val(), a = $('[id = "tpid_wl0.2_nctrlsb"]').parents('.form-group');
                    v == '1' ? a.show() : a.hide();
                }else if (this.name == 'wl0.3_nbw_cap'){
                    var v = $(this).val(), a = $('[id = "tpid_wl0.3_nctrlsb"]').parents('.form-group');
                    v == '1' ? a.show() : a.hide();
                }else if (this.name == 'vrrp_script_type'){
                    var v = $(this).val(), a = $('#tpid_vrrp_script_ip').parents('.form-group');
                    v == '1' ? a.show() : a.hide();
                }else if (this.name == 'PingEnable'){
                    var toggle_arrs = ['UtmsPingAddr', 'UtmsPingAddr1', 'PingInterval', 'PingMax', 'icmp_action'];
                }else if (this.name == 'rx_tx_enable'){
                    var toggle_arrs = ['rx_tx_mode', 'rx_tx_check_int', 'rx_tx_action'];
                }else if (this.name == 'custom_dns_enable'){
                    var toggle_arrs = ['custom_dns_part1', 'custom_dns_part2'];
                }else if (this.name == 'ipsec1_dpdaction'){
                    var toggle_arrs = ['ipsec1_dpddelay', 'ipsec1_dpdtimeout'];
                }else if (this.name == 'ipsec2_dpdaction'){
                    var toggle_arrs = ['ipsec2_dpddelay', 'ipsec2_dpdtimeout'];
                }else if (this.name == 'ipsec1_icmp_check'){
                    var toggle_arrs = ['ipsec1_icmp_intval', 'ipsec1_icmp_count', 'ipsec1_icmp_addr'];
                }else if (this.name == 'ipsec2_icmp_check'){
                    var toggle_arrs = ['ipsec2_icmp_intval', 'ipsec2_icmp_count', 'ipsec2_icmp_addr'];
                }else if (this.name == 'vpn_client1_rgw'){
                    if($('#tpid_vpn_client1_if').val() == 'tap'){
                        var toggle_arrs = ['vpn_client1_gw'];
                    }
                }else if (this.name == 'vpn_client1_tlsremote'){
                    var toggle_arrs = ['vpn_client1_cn'];
                }else if (this.name == 'vpn_client2_rgw'){
                    if($('#tpid_vpn_client2_if').val() == 'tap'){
                        var toggle_arrs = ['vpn_client2_gw'];
                    }
                }else if (this.name == 'vpn_client2_tlsremote'){
                    var toggle_arrs = ['vpn_client2_cn'];
                }else if (this.name.indexOf('cuc_cmd_en') != -1){
					for (var i=1; i<4; i++){
						if (this.name == 'cuc_cmd_en'+i){
							$(this).val() == '1' ? $('#tpid_cuc_cmd'+i).removeAttr('disabled') : $('#tpid_cuc_cmd'+i).attr('disabled',true);
						}
					}
				} else if (this.name == 'chl_type'){
					var v = $('#tpid_chl_type').val(), a = $('#tpid_client_fieldset'), b = $('#tpid_tcp_server_fieldset'), c = $('#tpid_udp_server_fieldset'), d = $('#tpid_modbus_tcp_rtu_fieldset');
					if (v == 1){
						a.hide();
						b.show();
						c.hide();
						d.hide();
					}else if (v == 2){
						a.hide();
						b.hide();
						c.show();
						d.hide();
					}else if (v == 3){
						a.hide();
						b.hide();
						c.hide();
						d.show();
					}else {
						a.show();
						b.hide();
						c.hide();
						d.hide();
					}
				} else if (this.name.indexOf('di_alarm_mode') != -1 || this.name.indexOf('do_output_mode') != -1){
					for (var i=1; i<3; i++) {
						var a = $('#tpid_level_mode'+i+'_fieldset'),
                            b = $('#tpid_pulse_mode'+i+'_fieldset'),
						    c = $('#tpid_level_mode'+(i+2)+'_fieldset'),
                            d = $('#tpid_pulse_mode'+(i+2)+'_fieldset'),
                            v = $(this).val();
						if (this.name == 'di_alarm_mode'+i){
							if (v == 0){
								a.hide();
								b.hide();
							} else if (v == 1){
								a.show();
								b.hide();
							} else if (v == 2){
								a.hide();
								b.show();
							}
						} else if (this.name == 'do_output_mode'+i){
							if (v == 0){
								c.hide();
								d.hide();
							} else if (v == 1){
								c.show();
								d.hide();
							} else if (v == 2){
								c.hide();
								d.show();
							}
						}
					}
				} else if (this.name == 'om_type') {
                    var arr_v1 = ['om_threshold', 'om_offset', 'om_yl_id'],
                        arr_v2 = [
                            'om_enable_1', 'om_ctype_1', 'om_threshold_1', 'om_offset_1', 'om_k_1', 'om_b_1', 'om_f0_1', 'om_t0_1', 'om_r1_1', 'om_r2_1',
                            'om_enable_2', 'om_ctype_2', 'om_threshold_2', 'om_offset_2', 'om_k_2', 'om_b_2', 'om_f0_2', 'om_t0_2', 'om_r1_2', 'om_r2_2',
                            'om_enable_3', 'om_ctype_3', 'om_threshold_3', 'om_offset_3', 'om_k_3', 'om_b_3', 'om_f0_3', 'om_t0_3', 'om_r1_3', 'om_r2_3',
                            'om_enable_4', 'om_ctype_4', 'om_threshold_4', 'om_offset_4', 'om_k_4', 'om_b_4', 'om_f0_4', 'om_t0_4', 'om_r1_4', 'om_r2_4'
                        ];
                    var v = parseInt($(this).val()), show_arr = null, hide_arr;
                    if (v == 1) {
                        show_arr = arr_v1;
                        hide_arr = arr_v2;
                    } else if (v == 2) {
                        show_arr = arr_v2;
                        hide_arr = arr_v1;
                    } else {
                        show_arr = [];
                        hide_arr = arr_v1.concat(arr_v2);
                    }
                    for (var i=0; i<show_arr.length; i++) $('#tpid_'+show_arr[i]).parents('.form-group').show();
                    for (var j=0; j<hide_arr.length; j++) $('#tpid_'+hide_arr[j]).parents('.form-group').hide();
                } else if (this.name == 'enable_modem'){
                    if ($('#tpid_enable_modem').is(':checked')){
                        $('#tpid_enable_modem').parents('form[id=form_param_1]').find('fieldset legend i').removeClass('fa fa-toggle-down').addClass('fa fa-toggle-up');
                        $('#tpid_enable_modem').parents('form[id=form_param_1]').find('fieldset div[fieldset="1"]').show();
                    } else {
                        $('#tpid_enable_modem').parents('form[id=form_param_1]').find('fieldset legend i').removeClass('fa fa-toggle-up').addClass('fa fa-toggle-down');
                        $('#tpid_enable_modem').parents('form[id=form_param_1]').find('fieldset div[fieldset="1"]').hide();
                    }
                }

                if (typeof toggle_arrs != 'undefined'){
                    for (var k=0; k<toggle_arrs.length; k++){
                        $('#tpid_'+toggle_arrs[k]).parents('.form-group').toggle($(this).is(':checked'));
                    }
                }
            });
        });
    });
    //Fieldset open/close
    $(".params-tab-content fieldset legend").click(function () {
        var i = $(this).find('i');
        if (i.hasClass('fa fa-toggle-up')){
            i.removeClass('fa fa-toggle-up').addClass('fa fa-toggle-down');
        } else {
            i.removeClass('fa fa-toggle-down').addClass('fa fa-toggle-up');
        }
        $(this).nextAll().slideToggle();
    });
    //rboot
    $('input[id^=ck_days]').on('change', function(){
        me.rboot();
    })

	//报警联动
	$('input[id^=ck1_sms]').on('change', function(){
		var bit1 = $('#ck1_sms1').is(':checked') ? 1 : 0,
			bit0 = $('#ck1_sms0').is(':checked') ? 1 : 0;
		me.saveChangedParams('di_trigger1', bit1*2+bit0);
	});

	$('input[id^=ck2_sms]').on('change', function(){
		var bit1 = $('#ck2_sms1').is(':checked') ? 1 : 0,
			bit0 = $('#ck2_sms0').is(':checked') ? 1 : 0;
		me.saveChangedParams('di_trigger2', bit1*2+bit0);
	});

}

TermParams.prototype.getTableValue = function(){
	var me = this;
	for (var i=1;i<11;i++) {
		if ($('#phone_number'+i).val() != "") {
			var bit0 = $('#di'+i).is(':checked') ? 1 : 0,
				bit1 = $('#do'+i).is(':checked') ? 1 : 0,
				bit2 = $('#wake'+i).is(':checked') ? 1 : 0,
				bit3 = $('#configSet'+i).is(':checked') ? 1 : 0,
				bit4 = $('#resart'+i).is(':checked') ? 1 : 0,
				bit5 = $('#m2m'+i).is(':checked') ? 1 : 0;
			var pemission = bit5*32+bit4*16+bit3*8+bit2*4+bit1*2+bit0;
			if ($('#phone_number'+i).val() != me.rules['phone_number'+i]) {
				me.saveChangedParams('phone_number'+i, $('#phone_number'+i).val());
			}
			if (pemission != me.rules['pemission'+i]) {
				me.saveChangedParams('pemission'+i, pemission);
			}
		}
	}
}

// 初始化赋值
TermParams.prototype.setFieldsValue = function(msg){
    /*$(".tab-pane[id^=params_tab_] form").each(function(){
        $(this).get(0).reset();
    });*/
    var me = this, pn_reg = /^phone_number\d$/;
    for (var grid_id in me.tp_names) {
        me.rules[grid_id] = '';
    }
    for (var x in msg) {
        if ($.inArray(x,me.tp_grids) != -1){
            if (msg[x].substr(msg[x].length - 1, 1) == '>') {
                msg[x] = msg[x].substr(0, msg[x].length - 1);   
            }
            me.setRules(x, msg[x]);
        } else if (x == 'wan_dns') {
            var tmp = msg[x].split(' ');
            $('#tpid_wan_dns').val(tmp[0]);
            $('#tpid_wan_dns_part2').val(tmp[1]);
        } else if (x == 'custom_dns') {
            var tmp = msg[x].split(' ');
            $('#tpid_custom_dns_part1').val(tmp[0]);
            $('#tpid_custom_dns_part2').val(tmp[1]);
        } else if (x == 'wan1_get_dns') {
            var tmp = msg[x].split(' ');
            $('#tpid_dns_1').val(tmp[0]);
            $('#tpid_dns_2').val(tmp[1]);
        } else if (x == 'sch_rboot'){
            var arr = msg[x].split(',');
            $('#tpid_rboot_enabled').prop('checked',arr[0] == '1');
            var time = parseInt(arr[1]);
            $('#tpid_rboot_time').val(time >= 0 ? time : 'e');
            $('#tpid_rboot_every').val(time >= 0 ? '' : Math.abs(time));
            time > 0 ? $('#tpid_rboot_every').attr('disabled',true) : $('#tpid_rboot_every').removeAttr('disabled');
            var interval = parseInt(arr[2]).toString(2);
            for (var i=interval.length-1,j=0; i>=0; i--,j++){
                $('#ck_days_'+j).prop('checked', interval.substr(i,1) == '1');
            }
        } else if (x.indexOf('di_trigger') != -1){
			for (var i=1;i<3;i++) {
				if (x=='di_trigger'+i) {
					var tmp = parseInt(msg[x]).toString(2), val = this.polish(tmp,2);
					$('#ck'+i+'_sms0').prop('checked', val.substr(-1) == "1");
					$('#ck'+i+'_sms1').prop('checked', val.substr(-2,1) == "1");
				}
			}
		} else if (x.indexOf('pemission') != -1) {
            me.rules[x] = msg[x];
            var tmp = parseInt(msg[x]).toString(2), val = this.polish(tmp,6), i = x.replace('pemission','');
			$('#di'+i).prop('checked', val.substr(-1) == "1");
			$('#do'+i).prop('checked', val.substr(-2,1) == "1");
			$('#wake'+i).prop('checked', val.substr(-3,1) == "1");
			$('#configSet'+i).prop('checked', val.substr(-4,1) == "1");
			$('#resart'+i).prop('checked', val.substr(-5,1) == "1");
			$('#m2m'+i).prop('checked', val.substr(-6,1) == "1");
        } else if (x == 'upnp_enable') {
            $('#tpid_enable_upnp').prop('checked',      msg[x] & 1 ? true : false);
            $('#tpid_enable_natpmp').prop('checked',    msg[x] & 2 ? true : false);
        } else if (pn_reg.test(x)) {
            me.rules[x] = msg[x];
            $('#'+x).val(msg[x]);
        } else {
            // 控件名称包含.的，用$('#id')获取时会报错，此处要特殊处理使用 $('[id=xxx]')
            var cmp = x.indexOf('.') != -1 ? $('[id = "tpid_'+ x + '"]') : $('#tpid_'+x);
            if (!cmp) continue;
            if (cmp.attr('type') == 'checkbox') {
                var chk_vals = get_checkbox_check_val(cmp, x);
                cmp.prop('checked', msg[x] == chk_vals[0] ? true : false);
            } else {
                cmp.val(msg[x]);
            }
            cmp.change();
        }
    }
}

TermParams.prototype.load = function(msg){
    var me = this;
    me.changedParams = [];
    me.paramsLoading = true;
    $('.my-loading').show();
    $.ajax({
        url: $lang.curl.replace('Index/replace', 'Term/loadTermParams'),
        data: {
            sn: ($.gf.tp.term_list != '' && $.gf.tp.term_list.indexOf(',') == -1 ? $.gf.tp.term_list : 0)
        },
        success: function(msg){
            msg = $.parseJSON(msg);
            me.cleanRules();
            me.setFieldsValue(msg);
            me.paramsLoading = false;
        },
        complete:function(){
            $('.my-loading').hide();
        },
        error: function(){
            me.paramsLoading = false;
        }
    });
}

TermParams.prototype.getParams = function(){
    var me = this;
    me.getRules();
	if (this.paramsType == "d21") {
		me.getTableValue();
	}

    if (typeof me.changedParams.om_uart_x != 'undefined' || typeof me.changedParams.cl_uart_x != 'undefined') {
        var a = $('#tpid_om_uart_x').val(), b = $('#tpid_cl_uart_x').val();
        if (a == b && a >= 0) {
            $.notice(-1, $lang.CONN_SERIAL_PORT_CHECK_TIPS);
            return;
        }
    }

    var names=[], vals=[];
    for (key in me.changedParams) {
        if (key == 'm2m_product_id' && me.changedParams[key].length > 14) {
            $.notice(1, $lang.PRODUCT_ID + '：' + $lang.M2M_PRODUCT_ID_LENGTH_VALID);
            return false;
        }
        names.push(key);
        vals.push(me.changedParams[key]);
    }
    if (names.length == 0) {
        $.notice(1,$lang.VAR_NO_CHANGE);
        return false;
    }
    return {
        names: names.join(','),
        vals: vals.join('{###}')
    };
}

/**
 * 生成参数表单
 */
TermParams.prototype.setOtherProperty = function(o,row){
    if (typeof row.name != 'undefined'){
        $(o).attr({
            name: row.name,
            id: 'tpid_'+row.name
        });
    }
    if (typeof row.checkedValue != 'undefined' && typeof row.unCheckedValue != 'undefined') {
        $(o).attr({
            checkedValue: row.checkedValue,
            unCheckedValue: row.unCheckedValue
        });
    }
    if (typeof row.disabled != 'undefined'){
        $(o).attr('disabled', row.disabled);
    }
    if (typeof row.placeholder != 'undefined'){
        $(o).attr('placeholder', row.placeholder);
    }
    /*
    if (typeof row.label != 'undefined'){
        o.fieldLabel = row.label;
    }
    if (typeof row.emptyText != 'undefined'){
        o.emptyText = row.emptyText;
    }
    if (typeof row.width != 'undefined'){
        o.width = row.width;
    }
    if (typeof row.anchor != 'undefined'){
        o.anchor = row.anchor;
    }
    if (typeof row.handler != 'undefined'){
        o.handler = row.handler;
    }
    if (typeof row.listeners != 'undefined'){
        o.listeners = row.listeners;
    }
    if (typeof row.hidden != 'undefined'){
        o.hidden = row.hidden;
    }
    if (typeof row.length != 'undefined'){
        o.minLength = row.length[0];
        o.maxLength = row.length[1];
    }
    if (typeof row.regex != 'undefined'){
        o.regex = row.regex;
    }
    if (typeof row.regexText != 'undefined'){
        o.regexText = row.regexText;
    }
    if (typeof row.vtype != 'undefined'){
        o.vtype = row.vtype;
    }
    if (typeof row.vtypeIds != 'undefined'){
        o.vtypeIds = row.vtypeIds;
    }
    if (typeof row.style != 'undefined'){
        o.style = row.style;
    }else if (row.index != 0 && row.label != ''){
        o.style = tp_margin_top;
    }
    */
    return o;
}

//文本框
TermParams.prototype.getTextField = function(row){
    var o = $('<input type="text" class="form-control" />');
    return this.setOtherProperty(o,row);
}

//选择框
TermParams.prototype.getCheckbox = function(row){
    var o = this.setOtherProperty($('<input class="ck-top-0" type="checkbox" />'), row);
    var parent = $('<label class="checkbox-inline"></label>');
    parent.append(o);
    return parent;
}

//数字框
TermParams.prototype.getNumberbox = function(row){
    var o = $('<input type="text" class="form-control" />');
    return this.setOtherProperty(o,row);
}
//超链接
TermParams.prototype.getHyperlink = function(row){
    var o = $('<a href="' + row.url + '">' + row.url + '</a>');
    return this.setOtherProperty(o,row);
}
//radio
TermParams.prototype.getRadio = function(row){
    var o = $('<input type="radio" ' + (row.checked?row.checked:"") +' style="vertical-align: bottom;" />');
    return this.setOtherProperty(o,row);
}
//Combobox
TermParams.prototype.getCombo = function(row){
    var o = $('<select class="form-control"></select>');
    o.append($('<option value="-100">'+$lang.PLEASE_SELECT+'</option>'));
    for (var i=0; i<row.data.length; i++){
        o.append($('<option value="'+row.data[i].id+'">'+row.data[i].name+'</option>'));
    }
    return this.setOtherProperty(o,row);
}

//Button
TermParams.prototype.getButton = function(row){
    var o = $('<button type="button" class="btn btn-sm">'+row.text+'</button>');
    if (typeof row.handler != 'undefined'){
        $(o).click(row.handler);
    }
    return this.setOtherProperty(o,row);
}

//Textarea
TermParams.prototype.getTextarea = function(row){
    var o = $('<textarea class="form-control"></textarea>');
    return this.setOtherProperty(o,row);
}

//Grid container
TermParams.prototype.getGridContainer = function(row){
    var o = $('<div><table id="grid_'+row.grid_id+'" style="width:100%; background:#ccc;"></table></div><div id="pager_'+row.grid_id+'" style="margin-top: 15px !important;"></div>');
    return this.setOtherProperty(o,row);
}

//Fieldcontainer
TermParams.prototype.getFieldcontainer = function(row){
    var o = $('<div class="row" fieldcontainer="1"></div>');
    this.getFormItems(row.items, o, 4);
    return o;
}

//Fieldset
TermParams.prototype.getFieldset = function(row){
    var o = $('<div fieldset="1" style="display:'+(row.collapsed ? 'none':'block')+'";><div>');
    this.getFormItems(row.items, o);
    return o;
}

//补齐位数
TermParams.prototype.polish = function(num,n){
	var len = num.toString().length;
    while (len < n) {
        num = "0" + num;
        len++;
    }
    return num;
}

//Grid
TermParams.prototype.getGrid = function(div, grid_id, title){
    var me = this;
    if ($('grid_'+grid_id).size() == 0) {
        div.append($('<table id="grid_'+grid_id+'" style="width:100%; background:#ccc;"></table>'));
        div.append($('<div id="pager_'+grid_id+'" style="margin-top: 15px !important;"></div>'));
    }
    $('#grid_'+grid_id).jqGrid({
        datatype: 'local',
        data: [],
        colNames: me.tp_names[grid_id],
        colModel: me.tp_columns[grid_id],
        caption: title,
        autoScroll: true,
        width: $('.params-tab-content').width(),
        height: 'auto',
        shrinkToFit: true,
        rownumbers: grid_id != 'vlan'?true:false,
        rownumWidth: 30,
        pager: '#pager_'+grid_id,
        page: 1,
        pagerpos: 'center',
        pgbuttons: false,
        pginput: false,
        lastRowid: 0,
        viewrecords: true,
        onSelectRow: function(id) {
            $('#grid_'+grid_id).jqGrid('editRow',id,true);
        }
    });
    $('#grid_'+grid_id).navGrid('#pager_'+grid_id,{edit:false,add:false,del:false,search:false,refresh:false})
    if (grid_id != 'vlan') {
        $('#grid_'+grid_id).navButtonAdd('#pager_'+grid_id, {
            caption: '',
            buttonicon: 'ui-icon-plus',
            position: 'last',
            onClickButton:function(){
                var rowid = new Date().getTime();
                $('#grid_'+grid_id).jqGrid('addRowData', rowid, {enable:1, protocol:1});
                $('#grid_'+grid_id).jqGrid('editRow',rowid);
            }
        }).navButtonAdd('#pager_'+grid_id, {
            caption: '',
            buttonicon: 'ui-icon-trash',
            position: 'last',
            onClickButton:function(){
                var rowid = $('#grid_'+grid_id).jqGrid('getGridParam','selrow');
                if (!rowid){
                    $.notice(1,$lang.ONLY_SELECT_ONE);
                    return;
                }
                $('#grid_'+grid_id).delRowData(rowid);
            }
        });
    }
}

TermParams.prototype.getFormItems = function(items, ele, col_w){
    var me = this;
    for (var i=0, row=null; i<items.length; i++){
        row = items[i];
        row.index = i;

        if (typeof row.xtype == 'undefined'){
            row.xtype = 'textfield';
        }

        if (row.xtype == 'fieldset'){
            var fieldset = $('<fieldset style="display:'+(typeof row.hidden != 'undefined' && row.hidden ? 'none':'block')+'; padding:15px 0; margin:0 2px 15px; border:1px solid silver; border-radius:5px;">\
                <legend style="cursor:pointer; border:0; width:auto; margin-left:15px; color:silver;">\
                <i class="fa fa-toggle-'+(row.collapsed ? 'down' : 'up')+'"> '+(typeof row.title != 'undefined' ? row.title : '')+'</i></legend></fieldset>');
            fieldset.append(me.getFieldset(row));
            if (typeof row.name != 'undefined'){
                fieldset.attr('id', 'tpid_'+row.name);
            }
            ele.append(fieldset);
            continue;
        }

        if (row.xtype == 'grid_container') {
            ele.append(me.getGridContainer(row));
            continue;
        }

        var parent = $('<div class="form-group"><label class="col-lg-3 control-label">'+row.label+'：</label></div>');
        if (typeof row.hidden != 'undefined' && row.hidden){
            parent.css('display','none');
        }
        var col_num = 0;
        if(typeof row.itemw == "undefined"){
            col_num = (row.xtype=='fieldcontainer' || row.xtype=='ck_days' ? 9 : (typeof col_w == 'undefined' ? 3 : col_w));
        }else{
            col_num = 4;
        }
        var child = $('<div class="col-lg-'+ col_num +'"></div>');
        switch (row.xtype){
            case 'textfield':
                child.append(me.getTextField(row));
                break;
            case 'checkbox':
                child.append(me.getCheckbox(row));
                break;
            case 'number':
                child.append(me.getNumberbox(row));
                break;
            case 'combo':
                child.append(me.getCombo(row));
                break;
            case 'hyperlink':
                child.append(me.getHyperlink(row));
                break;
            case 'radio':
                child.append(me.getRadio(row));
                break;
            case 'fieldcontainer':
                child.append(me.getFieldcontainer(row));
                break;
            case 'button':
                child.append(me.getButton(row));
                break;
            case 'splitter':
                child.append($('<span>&nbsp;</span>'));
                break;
            case 'textarea':
                child.append(me.getTextarea(row));
                break;
            case 'ck_days':
            {
                var week = $lang.VAR_WEEK_ARR;
                var week_arr = ['rboot_sun','rboot_mon','rboot_tue','rboot_wed','rboot_thu','rboot_fri','rboot_sat'];
                for (var index = 0; index < 7; index++) {
                    child.append($('<label class="checkbox-inline"><input type="checkbox" id="ck_days_'+index+'" value="'+index+'" name="'+week_arr[index]+'">'+week[index]+'&nbsp;&nbsp;</label>'));
                }
                break;
            }
			case 'ck1_sms':
			{
				var arrs = $lang.ALARM_ARRS;
				for (var index = 0; index < 2; index++) {
                    child.append($('<label class="checkbox-inline"><input type="checkbox" id="ck1_sms'+index+'">'+arrs[index]+'&nbsp;&nbsp;</label>'));
                }
                break;
			}
			case 'ck2_sms':
			{
				var arrs = $lang.ALARM_ARRS;
				for (var index = 0; index < 2; index++) {
                    child.append($('<label class="checkbox-inline"><input type="checkbox" id="ck2_sms'+index+'">'+arrs[index]+'&nbsp;&nbsp;</label>'));
                }
                break;
			}
            default:
                break;
        }

        if ($(ele).attr('fieldcontainer') == '1'){
            ele.append(child);
        }else{
            parent.append(child);
            if (typeof row.emptyText != 'undefined'){
                parent.append($('<label class="col-lg-5 control-label input-notes">('+row.emptyText+')</label>'));
            }
            try{
                ele.append(parent);
            }catch(e){
                console.log(row);
            }
        }
    }
}

TermParams.prototype.init = function(){
    var me = this, arr = $.gf['device_params_define_'+me.paramsType] || [];
    for (var i=0, j=0; i<arr.length; i++){
        var div = $('<div class="tab-pane" id="'+arr[i].id+'"></div>');
        if (arr[i].items.length > 0) {
            var form = $('<form class="form-horizontal" role="form" id="'+'form_param_'+(j++)+'"></form>');
            if (i == 0){
                div.addClass('active');
            }
            me.getFormItems(arr[i].items, form);
            div.append(form);
        }
        $('.params-tab-content').append(div);

        if (arr[i].id == 'params_tab_port_forwarding'){
            //端口转发
            me.getGrid(div, 'portforward', $lang.PORT_FORWARDING);
            var ul = $($lang.PORT_FORWARDING_TIPS);
            ul.attr('style', 'margin:20px 5px 0');
            div.append(ul);
        } else if (arr[i].id == 'params_tab_port_redirecting'){
            //端口重定向
            me.getGrid(div, 'portredirect', $lang.PORT_REDIRECTING);
        } else if (arr[i].id == 'params_tab_port_trigger'){
            //端口触发
            me.getGrid(div, 'trigforward', $lang.PORT_TRIGGER);
        } else if (arr[i].id == 'params_tab_vlan'){
            //VLAN
            me.getGrid(div, 'vlan', $lang.VLAN);
        } else if (arr[i].id == 'params_tab_link_scheduling'){
            //链路调度
            me.getGrid(div, 'linkscheck', $lang.PINGENABLE);
            $('#gbox_grid_linkscheck').css('margin-bottom','20px');
            me.getGrid(div, 'linkschedule', $lang.LINK_SCHEDULING);
        } else if (arr[i].id == 'params_tab_bandwidth_speed_limit'){
            //带宽限速
            me.getGrid(div, 'new_qoslimit_rules', $lang.BANDWIDTH_SPEED_LIMIT);
            $('#gbox_grid_new_qoslimit_rules').css('margin-top','20px');
            $('#gbox_grid_new_qoslimit_rules').css('margin-bottom','20px');
        } else if (arr[i].id == 'params_tab_url_filter'){
            //IP/URL过滤
            me.getGrid(div, 'ipportfilterrules', $lang.IP_MAC_PORT_FILTER);
            $('#gbox_grid_ipportfilterrules').css('margin-bottom','20px');
            me.getGrid(div, 'keywordfilters', $lang.KEYWORDS_FILTER);
            $('#gbox_grid_keywordfilters').css('margin-bottom','20px');
            me.getGrid(div, 'weburlfilters', $lang.URL_FILTER);
            $('#gbox_grid_weburlfilters').css('margin-bottom','20px');
            me.getGrid(div, 'routeraccessrules', $lang.ACCESS_FILTERING);
        } else if (arr[i].id == 'params_tab_static_dhcp'){
            //静态 DHCP
            me.getGrid(div, 'dhcpd_static', $lang.STATIC_DHCP);
        } else if (arr[i].id == 'params_tab_domain_filter'){
            //域名过滤
            me.getGrid(div, 'webhostfilters', '');
            $('#gbox_grid_webhostfilters').css('margin-top','20px');
        } else if (arr[i].id == 'params_tab_vpn_gre'){
            //GRE设置
            me.getGrid(div, 'greparam', $lang.GRE_TUNNEL_SETTINGS);
            $('#gbox_grid_greparam').css('margin-bottom','20px');
            me.getGrid(div, 'greroute', $lang.GRE_ROUTE_SETTINGS);
		} else if (arr[i].id == 'params_tab_pptp_l2tp'){
			//PPTP/L2TP设置
			me.getGrid(div, 'xtpbasic', $lang.PPTP_L2TP_BASIC);
            $('#gbox_grid_xtpbasic').css('margin-bottom','20px');

            me.getGrid(div, 'l2advanced', $lang.L2TP_ADVANCED);
            $('#gbox_grid_l2advanced').css('margin-bottom','20px');

            me.getGrid(div, 'ppadvanced', $lang.PPTP_ADVANCED);
            $('#gbox_grid_ppadvanced').css('margin-bottom','20px');

            me.getGrid(div, 'xtpschedule', $lang.LINK_SCHEDULING);
        } else if (arr[i].id == 'params_tab_rtu_modbus_cmd_list'){
            //Modbus命令列表
            me.getGrid(div, 'modbusCmdTable', $lang.MODBUS_COMMAND_LIST);
        } else if (arr[i].id == 'params_tab_sms_config'){
			var str = '<table id="grid_sms_config" class="table" style="width:100%;">\<thead><tr class="ui-jqgrid-labels">\
				<th style="text-align:center;">'+$lang.SMS_PHONE+'</th><th style="text-align:center;">'+$lang.DI_ALARM+'</th><th style="text-align:center;">'+$lang.DO_ECHO+'</th><th style="text-align:center;">'+$lang.STANDBY_AWAKEN+'</th>\
				<th style="text-align:center;">'+$lang.CONFIGSET+'</th><th style="text-align:center;">'+$lang.VAR_RESTART+'</th><th style="text-align:center;">'+$lang.M2M_SUMMON+'</th></tr></thead><tbody>';
			for (var m=1; m<11; m++) {
				str += '<tr class="ui-widget-content jqgrow ui-row-ltr"><td style="text-align:center;width:186px;"><input type="text" class="form-control" id ="phone_number'+m+'" name="phone_number'+m+'" /></td><td style="text-align:center"><input class="ck-top-0" type="checkbox" id="di'+m+'" name="di'+m+'"/></td>\
				    <td style="text-align:center"><input class="ck-top-0" type="checkbox" id="do'+m+'" name="do'+m+'"/></td><td style="text-align:center"><input class="ck-top-0" type="checkbox" id="wake'+m+'" name="wake'+m+'"/></td><td style="text-align:center"><input class="ck-top-0" type="checkbox"  id="configSet'+m+'" name="configSet'+m+'"/></td>\
				    <td style="text-align:center"><input class="ck-top-0" type="checkbox" id="resart'+m+'" name="resart'+m+'"/></td><td style="text-align:center"><input class="ck-top-0" type="checkbox" id="m2m'+m+'" name="m2m'+m+'"/></td></tr>';
			}
			str += '</tbody></table>';
			div.append(str);
        } else if (arr[i].id == 'params_tab_route_table_settings'){
            //静态路由
            me.getGrid(div, 'routes_static', $lang.STATIC_ROUTING_TABLE);
            //OSPF
            me.getGrid(div, 'ospf_network', 'OSPF');
            $('#gbox_grid_ospf').css('margin-bottom','20px');
        } else if (arr[i].id == 'params_tab_mqtt_settings'){
            //静态路由
            me.getGrid(div, 'iot_topic_table', $lang.DEVICE_CODE_TABLE);
        }

        //Ipsec 2
        if (arr[i].id == 'params_tab_ipsec1'){
            var div2 = div.clone(false);
            div2 = div2.get(0);
            div2.title = 'IPSEC 2';
            div2.id = 'params_tab_ipsec2';
            div2.firstChild.id = 'form_param_' + j++;
            for (var k=0; k<div2.firstChild.childNodes.length; k++){
                var fieldset = $(div2.firstChild.childNodes[k]), legend_str = fieldset.find('legend i').html();
                fieldset.find('legend i').html(legend_str.replace('1','2'));
                fieldset.find('[id^=tpid_ipsec1_]').each(function(){
                    $(this).attr({
                        name: $(this).attr('name').replace('ipsec1_', 'ipsec2_'),
                        id: $(this).attr('id').replace('ipsec1_', 'ipsec2_')
                    });
                });
            }
            $('.params-tab-content').append(div2);
        }
    }
    me.bindEvent();
};