window.Widget  = window.Widget || {};
var MsgBox = Widget.MsgBox = Widget.MsgBox || {};
MsgBox.success=function(msg, options, callback){
    if(typeof(options) == 'function'){
        callback = options;
        options = {};
    }
    callback = callback || function(ret){};
    options = $.extend({/*style:"background-color: #000;filter: alpha(opacity=60);background-color: rgba(0,0,0,0.6);color: #fff;border: none;",*/"time":2},options||{});
    options["end"] = callback;
    options["content"] = msg;
    layer.open(options);
};
MsgBox.error=function(msg,options,callback){
    if(typeof(options) == 'function'){
        callback = options;
        options = {};
    }
    callback = callback || function(ret){}
    options = $.extend({/*style:"border:none; background-color:#78BA32; color:#fff;",*/"time":3},options||{});
    options["end"] = callback;
    options["content"] = msg;
    layer.open(options);
};
MsgBox.alert = function(msg, callback){
    callback = callback || function(ret){};
    options["end"] = callback;
    options["content"] = msg;
    layer.open({content: msg, btn: ['确认'], end: callback});
}
MsgBox.confirm = function(msg, options, callback){
    if(typeof(options) == 'function'){
        callback = options;
        options = {shadeClose: false, btn: ['确认', '取消']};
    }
    callback = callback || function(ret){};
    options["content"] = msg;
    options["btn"]  =options["btn"] || ['确认', '取消'];
    options["yes"] = function(index){callback(true);layer.close(index);}
    options["no"] = function(index){callback(false);layer.close(index);}
    layer.open(options);
}
MsgBox.notice=function(options){
    layer.open(options);
};
MsgBox.load=function(msg,options){
    options = $.extend({time:120,type:2,shade:false,shadeClose:false},options||{});
    layer.open(options);
    //layer.open({shade:false,shadeClose:false,time:10000,type:2,content:'<div class="loading"><div class="dot white"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>'});
};
MsgBox.show=function(options,callback){
    options = options||{};
    options['end'] = callback || function(){};
    layer.open(options);
};
MsgBox.hide=function(){
    layer.closeAll();
};
//重写toFixed方法  
Number.prototype.toFixed2=function(len) {  
    var tempNum = 0;  
    var s,temp;  
    var s1 = this + "";  
    var start = s1.indexOf(".");  
    if(s1.substr(start+len+1,1)>=5) {
        tempNum=1; 
    } 
    var temp = Math.pow(10,len);  
    s = Math.floor(this * temp) + tempNum;  
    return s/temp;  
}
String.prototype.toFixed=function(len)  {  
    var tempNum = 0;  
    var s,temp;  
    var s1 = this + "";  
    var start = s1.indexOf(".");  
    if(s1.substr(start+len+1,1)>=5) {
        tempNum=1;
    }
    var temp = Math.pow(10,len);  
    s = Math.floor(this * temp) + tempNum;  
    return s/temp; 
}
//cookie
window.Cookie = window.Cookie || {};
window.UxLocation = window.UxLocation || {"lat":0, "lng":0, "addr":""};
window.CFG = window.CFG || {"domain":"","url":"/", "title":"外卖系统", "res":"/static", "img":"/attachs","C_PREFIX":"KT-"};
window.LoadData = window.LoadData || {"LOCK":false, "LOAD_END":false, "params":{"page":1}};
//验证字符串是否合法的cookie键名
Cookie._valid_key = function(key){
    return (new RegExp("^[^\\x00-\\x20\\x7f\\(\\)<>@,;:\\\\\\\"\\[\\]\\?=\\{\\}\\/\\u0080-\\uffff]+\x24")).test(key);
}
Cookie.set = function(key, value, expire, path){
    path = path || "/";
    key = window.CFG['C_PREFIX']+key;
    if(Cookie._valid_key(key)){
        //var a = key + "=" + escape(value);
        var a = key + "=" + value;
        if(typeof(expire) != 'undefined'){
            var date = new Date();
            expire = parseInt(expire,10);
            date.setTime(date.getTime() + expire*1000);
            a += "; expires="+date.toGMTString();
        }
        a += ";path="+path;
        document.cookie = a;
    }
    return null;
};
Cookie.get = function(key){
    key = window.CFG['C_PREFIX']+key;
    if(Cookie._valid_key(key)){
        var reg = new RegExp("(^| )" + key + "=([^;]*)(;|\x24)"),
        result = reg.exec(document.cookie);
        if(result){
            //return unescape(result[2]) || null;
            return result[2] || null;
        }
    }
    return null;
};
Cookie.remove = function(key){
    key = window.CFG['C_PREFIX']+key;
    document.cookie = key+"=;expires="+(new Date(0)).toGMTString();
};
//生成全局GUID
function GGUID(){
    var guid = '';
    for (var i = 1; i <= 32; i++) {
        var n = Math.floor(Math.random() * 16.0).toString(16);
        guid += n;
    }
    return "KT"+guid.toUpperCase();
}
//判断是否为手机访问
function checkIsMobile(){
    if(/(iphone|ipad|ipod|android|windows phone)/.test(navigator.userAgent.toLowerCase())){
        return true;
    }else{
        return false;
    }
}
//判断是否为腾讯手机浏览器
function checkIsMQQBrowser(){
    if(/(mqqbrowser)/.test(navigator.userAgent.toLowerCase())){
        return true;
    }else{
        return false;
    }
}
//判断是否微信
function checkIsWeixin(){
    if(/(micromessenger)/.test(navigator.userAgent.toLowerCase())){
        return true;
    }else{
        return false;
    }
}
//判断是否为APPwebView调用
function checkIsAppClient(){
    if(/(ijh.waimai|com.jhcms)/.test(navigator.userAgent.toLowerCase())){
        return true;
    }else{
        return false;
    }
}
//Android版本
function getAndroidVersion(){
    var index = navigator.userAgent.indexOf("Android");
    if(index >= 0){
        var androidVersion = parseFloat(navigator.userAgent.slice(index+8));
        if(androidVersion > 1){
            return androidVersion;
        }else{
            return 100;
        }
    }else{
        return 100;
    }
}
window.__MINI_CONFIRM = window.__MINI_CONFIRM || function(elm){
    var cfm = null;
    if($(elm).attr("mini-confirm")){
        cfm = $(elm).attr("mini-confirm");
    }else if(($(elm).attr("mini-act") || "").indexOf("confirm:")>-1){
        cfm = $(elm).attr("mini-act").replace("confirm:","");
    }else if(($(elm).attr("mini-act") || "").indexOf("remove:")>-1){
        cfm = "您确定要删除这条记录吗??\n三思啊.黄金有价数据无价!!";
    }
    if(cfm && !confirm(cfm)){
        return false;
    }
    return true;
}
$(document).off("submit","form[mini-form]").on("submit","form[mini-form]",function(){
    window.__MINI_LOAD = window.__MINI_LOAD || false;
    if(window.__MINI_LOAD){ //防止重复提交
        return false;
    }
    window.__MINI_LOAD = true;
    Widget.MsgBox.success("数据处理中...");
    Widget.MsgBox.load("数据处理中...");
    if($(this).find("[name='MINI']").size()<1){
        $(this).prepend('<input type="hidden" name="MINI" value="form" />');
    }
    $(this).find("[name='MINI']").val('iframe');
    $(this).attr("target", "miniframe");
    if($(this).find("input[type='file']").size()>0){
        $(this).attr("ENCTYPE", "multipart/form-data");
    }
    return true;
});
$(document).off("click","[mini-submit],a[mini-submit]").on("click","[mini-submit],a[mini-submit]",function(e){
    e.stopPropagation();e.preventDefault();
    window.__MINI_LOAD = window.__MINI_LOAD || false;
    if(window.__MINI_LOAD){ //防止重复提交
        return false;
    }
    if($("#miniframe").size()<1){
        $("body").prepend('<iframe id="miniframe" name="miniframe" style="display:none;"></iframe>');
    }
    var form = $(this).attr("mini-submit");
    var action = $(this).attr("action") || $(form).attr("action");
    $(form).attr("action", action).attr("target", "miniframe").attr("method", "post");
    var value = $(this).attr("mini-value") || "true";
    Widget.MsgBox.load("数据处理中...", 30);
    window.__MINI_LOAD_TIMEER = setTimeout(function(){
        Widget.MsgBox.alert("网络不给力,稍后重试");
    }, 30000);
    //clearTimeout(window.__MINI_LOAD_TIMEER);
    if($(form).find("[name='MINI']").size()<1){
        $(form).prepend('<input type="hidden" name="MINI" value="iframe" />');
    }
    $(form).find("[name='MINI']").val('iframe');
    if($(form).find("input[type='file']").size()>0){
        $(form).attr("ENCTYPE", "multipart/form-data");
    }
    $(form).submit();
    return true;    
});