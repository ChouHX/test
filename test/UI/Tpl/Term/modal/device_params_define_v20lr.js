$.gf.device_params_define_v20lr = [
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
    {title: $lang.DTU_SETTINGS, id:'params_tab_dtu_settings', items:[
        {name:'ux_packet_length', label:$lang.UX_PACKET_LENGTH, xtype:'number', minValue:1, maxValue:1024, emptyText:'1~1024'},
        {name:'ux_packet_delay', label:$lang.UX_PACKET_DELAY, xtype:'number', minValue:100, maxValue:1000, emptyText:'100~1000 '+$lang.VAR_MILLISECOND}
    ]},
    {title: $lang.OTHER_PARAMETER, id:'params_tab_other_parameter', items:[
        {name:'run_mode', label:$lang.DTU_RUN_TYPE, xtype:'combo', data:[{id:'0', name:$lang.PTOP_TRANSPARENT_TRANS}, {id:'1', name:$lang.NETWORK_TRANSPARENT_TRANS}, {id:'2', name:$lang.NETWORK_COLLECTION}, {id:'3', name:$lang.VAR_RELAY}, {id:'4', name:$lang.M2M_RELAY}]},
        {name:'debug_en', label:$lang.DEBUG_EN, xtype:'checkbox'}
    ]}
];