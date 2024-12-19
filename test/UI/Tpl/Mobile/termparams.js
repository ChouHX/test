function TermParams(){
    this.webhostfilters = '';
    this.changedParams = [];
    this.paramsLoading = false;
}
TermParams.prototype.saveChangedParams =  function(name, v){
    if (!this.paramsLoading) {
        this.changedParams[name] = v;
    }
}
TermParams.prototype.addRule = function(data){
    var tr = document.createElement('tr');
    tr.className = 'tr_rule';
    tr.innerHTML = '<td><div class="mui-switch '+(data.enable==1?'mui-active':'')+' switch_rule"><div class="mui-switch-handle"></div></div></td>\
        <td><input class="domain_rule" type="text" value="'+data.domain+'" /></td>\
        <td><input class="info_rule" type="text" value="'+data.info+'" /></td>';
    var tb = document.getElementById('tb_rule');
    var last = tb.lastChild.childNodes[tb.lastChild.childNodes.length - 2];
    tb.lastChild.insertBefore(tr, last);
    mui('.switch_rule').switch();
}
TermParams.prototype.setRules = function(webhostfilters){
    var webhostfilters=webhostfilters.split('>'), arr=[], i, len=webhostfilters.length, temp, me = this;
    for (i=0;i<len;i++){
        temp = webhostfilters[i].split("<");
        me.addRule({enable:parseInt(temp[0]), domain:temp[1], info:temp[2]});
    }
}
TermParams.prototype.getRules = function(){
    var arr=[], str;
    var switchs = mui('.switch_rule'), domains = mui('.domain_rule'), descs = mui('.info_rule');
    for (var i=0,len=switchs.length,checked,domain,desc; i<len; i++){
        checked = switchs[i].classList.contains('mui-active') ? '1':'0';
        domain = domains[i].value;
        desc = descs[i].value;
        if (domain != ''){
            arr.push(checked+"<"+domain+'<'+desc);
        }
    }
    if (arr.length != 0){
        str = arr.join(">")
        if (str != this.webhostfilters){
            this.changedParams['webhostfilters'] = str;
        }
    }
}
TermParams.prototype.bindEvent = function(){
    var me = this;
    var inputs = document.getElementsByTagName('input');
    for (var i=0,len=inputs.length; i<len; i++){		
        if (inputs[i].id.indexOf('tpid_') != -1){
             inputs[i].addEventListener('input',function(e){
                me.saveChangedParams(e.target.name, e.target.value);
            });
			
        }
    }
    //Switch
    var switchs = mui('.mui-switch');
    for (var i=0,len=switchs.length; i<len; i++){
        if (switchs[i].id.indexOf('tpid_') != -1){
            switchs[i].addEventListener('toggle',function(){
                var t = 1, f = 0, name = this.getAttribute('name');
                if (name == 'ibst_mode'){
                    t = 'enable';
                    f = 'disable';
                }else if (name == 'lan_proto'){
                    t = 'dhcp';
                    f = 'static';
                }
                me.saveChangedParams(event.srcElement.id.replace('tpid_',''), event.detail.isActive ? t : f);
            });
        }
    }
    //Select
    var selects = document.getElementsByTagName('select');
    for (var i=0,len=selects.length; i<len; i++){
        if (selects[i].id.indexOf('tpid_') != -1){
            selects[i].addEventListener('change',function(){             
                me.saveChangedParams(this.name, this.value);
            });
        }
    }
}
TermParams.prototype.setFieldsValue = function(msg){
    for (var x in msg){
        if (x == 'webhostfilters'){
            if (msg[x]){
                this.webhostfilters = msg[x];
                this.setRules(msg[x]);
            }
        }else if (x == 'wan_dns'){
            var tmp = msg[x].split(' ');
            document.getElementById('tpid_wan_dns').value = tmp[0];
            document.getElementById('tpid_wan_dns2').value = tmp[1];
        }else{
            var obj = document.getElementById('tpid_'+x);
            if (obj){
                if (obj.tagName == 'INPUT'){
                    obj.value = msg[x];
                }else if (obj.tagName == 'DIV'){
                    if (parseInt(msg[x]) == 1 || msg[x] == 'enable' || msg[x] == 'dhcp'){
                        mui('#tpid_'+x).switch().toggle();
                        mui('#tpid_'+x+' .mui-switch-handle')[0].style.transform = 'translate(43px, 0px)';
                    }
                }else if (obj.tagName == 'SELECT'){
                    for (var j=0,len=obj.options.length; j<len; j++){
                        if (obj.options[j].value == msg[x]){
                            obj.options[j].selected = true;
                            break;
                        }
                    }
                }
            }
        }
    }
}