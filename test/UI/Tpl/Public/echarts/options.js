(function($){
$.ec_opts = {
    gauge1: {
        w: '300px',
        h: '300px',
        backgroundColor: '#1b1b1b',
        tooltip : {
            formatter: "{a} <br/>{c} {b}"
        },
        series : [
            {
                type:'gauge',
                min: 0,
                max: 100,
                splitNumber: 10,
                axisLine: {            // 坐标轴线
                    lineStyle: {       // 属性lineStyle控制线条样式
                        color: [[0.09, 'lime'],[0.82, '#1e90ff'],[1, '#ff4500']],
                        width: 3,
                        shadowColor : '#fff', //默认透明
                        shadowBlur: 10
                    }
                },
                axisTick: {            // 坐标轴小标记
                    length :15,        // 属性length控制线长
                    lineStyle: {       // 属性lineStyle控制线条样式
                        color: 'auto',
                        shadowColor : '#fff', //默认透明
                        shadowBlur: 10
                    }
                },
                splitLine: {           // 分隔线
                    length :25,         // 属性length控制线长
                    lineStyle: {       // 属性lineStyle（详见lineStyle）控制线条样式
                        width:3,
                        color: '#fff',
                        shadowColor : '#fff', //默认透明
                        shadowBlur: 10
                    }
                },
                pointer: {           // 分隔线
                    shadowColor : '#fff', //默认透明
                    shadowBlur: 5
                },
                axisLabel: {            // 坐标轴小标记
                    textStyle: {       // 属性lineStyle控制线条样式
                        fontWeight: 'bolder',
                        color: '#fff',
                        shadowColor : '#fff', //默认透明
                        shadowBlur: 10
                    }
                },
                title : {
                    textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                        fontWeight: 'bolder',
                        fontSize: 14,
                        fontStyle: 'italic',
                        color: '#fff',
                        shadowColor : '#fff', //默认透明
                        shadowBlur: 10
                    }
                },
                detail : {
                    formatter: '{value}℃',
                    backgroundColor: 'rgba(30,144,255,0.8)',
                    borderWidth: 1,
                    borderColor: '#fff',
                    shadowColor : '#fff', //默认透明
                    shadowBlur: 5,
                    offsetCenter: [0, '50%'],       // x, y，单位px
                    textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                        fontWeight: 'bolder',
                        fontSize: 14,
                        color: '#fff'
                    }
                },
                data:[{value: 0, name: $lang.VAR_TEMPERATURE}]
            }
        ]
    },

    gauge2:{
        w: '300px',
        h: '300px',
        backgroundColor: '#1b1b1b',
        series: [
            {
                type: 'gauge',
                min: 0,
                max: 100,
                axisLabel: {
                    textStyle: {
                        color: '#fff'
                    }
                },
                title : {
                    textStyle: {
                        fontWeight: 'bolder',
                        fontSize: 14,
                        fontStyle: 'italic',
                        color: '#fff',
                        shadowColor : '#fff',
                        shadowBlur: 10
                    }
                },
                detail: {
                    formatter: '{value}%',
                    offsetCenter: [0, '50%'],
                    textStyle: {
                        fontWeight: 'bolder',
                        fontSize: 14,
                        color: '#fff'
                    }
                },
                data: [{value: 0, name: $lang.VAR_HUMIDITY}]
            }
        ]
    },

    //平滑曲线
    line1: {
        w: '300px',
        h: '200px',
        backgroundColor: '#e2e5ec',
        textStyle: {color: '#000'},
        xAxis: [{
            type: 'category',
            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisLabel:{
                rotate: 50,
            }
        }],
        grid: {
            left: '15%',
            bottom: '30%'
        },
        yAxis: [{
            type: 'value'
        }],
        series: [{
            data: [820, 932, 901, 934, 1290, 1330, 1320],
            type: 'line',
            smooth: true
        }]
    },

    //区域曲线
    line2: {
        w: '300px',
        h: '200px',
        backgroundColor: '#e2e5ec',
        textStyle: {color: '#000'},
        xAxis: [{
            type: 'category',
            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisLabel:{
                rotate: 50,
            }
        }],
        grid: {
            left: '15%',
            bottom: '35%'
        },
        yAxis: [{
            type: 'value'
        }],
        series: [{
            data: [820, 932, 901, 934, 1290, 1330, 1320],
            type: 'line',
            areaStyle: {},
            smooth: true
        }]
    },

    //柱状图
    column: {
        w: '300px',
        h: '200px',
        backgroundColor: '#e2e5ec',
        textStyle: {color: '#000'},
        xAxis: [{
            type: 'category',
            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisLabel:{
                rotate: 50,
            }
        }],
        grid: {
            left: '15%',
            bottom: '35%'
        },
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'直接访问',
                type:'bar',
                barWidth: '60%',
                data:[10, 52, 200, 334, 390, 330, 220]
            }
        ]
    },

    //饼图
    pie: {
        w: '300px',
        h: '300px',
        backgroundColor: '#1b1b1b',
        title: {
            text: 'Pie',
            left: 'center',
            top: 20,
            textStyle: {
                color: '#ccc'
            }
        },

        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },

        visualMap: {
            show: false,
            min: 80,
            max: 600,
            inRange: {
                colorLightness: [0, 1]
            }
        },
        series : [
            {
                name:'test',
                type:'pie',
                radius : '55%',
                center: ['50%', '50%'],
                data:[
                    {value:335, name:'Part 1'},
                    {value:310, name:'Part 2'},
                    {value:274, name:'Part 3'},
                ].sort(function (a, b) { return a.value - b.value; }),
                roseType: 'radius',
                label: {
                    normal: {
                        textStyle: {
                            color: 'rgba(255, 255, 255, 0.3)'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        lineStyle: {
                            color: 'rgba(255, 255, 255, 0.3)'
                        },
                        smooth: 0.2,
                        length: 10,
                        length2: 20
                    }
                },
                itemStyle: {
                    normal: {
                        color: '#c23531',
                        shadowBlur: 200,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },

                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ]
    }
}
})(jQuery);