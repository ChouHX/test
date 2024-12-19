function SystemParams(){
    this.changedParams = {};
    this.paramsLoading = false;
}
SystemParams.prototype.saveChangedParams =  function(name, v){
    if (!this.paramsLoading) {
        this.changedParams[name] = v;
    }
    // console.log(this.changedParams);
}
SystemParams.prototype.bindEvent = function(){
    var me = this;
    var inputs = document.getElementsByTagName('input');
    for (var i=0,len=inputs.length; i<len; i++){
        if (inputs[i].id.indexOf('spid_') != -1){
            inputs[i].addEventListener('input',function(){
                me.saveChangedParams(this.name, this.value);
            });
        }
    }
    //Switch
    var switchs = mui('.mui-switch');
    for (var i=0,len=switchs.length; i<len; i++){
        if (switchs[i].id.indexOf('spid_') != -1){
            switchs[i].addEventListener('toggle',function(){
                me.saveChangedParams(event.srcElement.id.replace('spid_',''), event.detail.isActive ? 1 : 0);
            });
        }
    }
    //Select
    var selects = document.getElementsByTagName('select');
    for (var i=0,len=selects.length; i<len; i++){
        if (selects[i].id.indexOf('spid_') != -1){
            selects[i].addEventListener('change',function(){
                me.saveChangedParams(this.name, this.value);
            });
        }
    }
}
SystemParams.prototype.setFieldsValue = function(msg){
    for (var x in msg){
        var obj = document.getElementById('spid_'+x);
        if (obj){
            if (obj.tagName == 'INPUT'){
                obj.value = msg[x];
            }else if (obj.tagName == 'DIV'){
                if (parseInt(msg[x]) == 1){
                    mui('#spid_'+x).switch().toggle();
                    mui('#spid_'+x+' .mui-switch-handle')[0].style.transform = 'translate(43px, 0px)';
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