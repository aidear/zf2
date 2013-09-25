
var Validator = function(){
    this.isValid = true;
    $(".help-inline").remove();
};

Validator.prototype.errorMsg = function(dom , msg){
    this.isValid = false;
    dom.after("<span class='help-inline error'>" + msg + "<span>");
};

Validator.prototype.username = function(dom){
    if(!/^[\w@]+$/.test(dom.val())){
        this.errorMsg(dom , '用户名错误');
    }
};

Validator.prototype.notEmpty = function(dom){
    if(dom.val() == ''){
        this.errorMsg(dom , '密码不能为空');
    }
};

Validator.prototype.email = function(dom){
    if(dom.val() != '' && !/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(dom.val())){
        this.errorMsg(dom , '邮箱格式错误');
    }
};

Validator.prototype.url = function(dom){
    if(dom.val() != '' && !/^[a-zA-z]+:\/\/[^\s]*$/.test(dom.val())){
        this.errorMsg(dom, '网址格式错误');
    }
};

Validator.prototype.number = function(dom){
    if(dom.val() != '' && !/^[1-9]\d*$/.test(dom.val())){
        this.errorMsg(dom, '数字格式错误');
    }
};