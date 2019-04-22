//验证码倒计时方法
//btn_id 按钮ID
//mobile_id 手机号id
//link 获取验证码的链接地址
function get_yzm(btn_id, mobile_id, link) {
    $(btn_id).click(function () {
        if ($(btn_id).hasClass('lock')) {
            return false;
        } else {
            //ajax
            var mobile = $(mobile_id).val();
            $.post(link, {mobile: mobile}, function (ret) {
                if (ret.error == 0) {
                    $(btn_id).addClass('lock');
                    var time = 60;//倒计时秒数
                    var djs = setInterval(function () {
                        time = time - 1;
                        $(btn_id).text(time + '秒');
                        if (time < 1) {
                            clearInterval(djs);
                            $(btn_id).text('获取验证码');
                            $(btn_id).removeClass('lock');
                        }
                    }, 1000);
                }
                layer.msg(ret.message);
            }, 'json');
        }
    })
}

//迷你弹出层调取方法，请求passport/mini_login get得到html写入当前页面,参数传login地址
function mini_login(url) {
    $.get(url, {}, function (ret) {
        $('body').append(ret);
    }, 'html');
}

//密码登录
//login_btn_id 按钮ID
//mobile_id 手机号id
//passwd_id 密码ID
//url 密码登录的链接地址
//out_url 登出地址
//用户中心地址
//jump 可选参数，默认不会跳转（mini弹窗登录用），如需要跳转，请传该参数
function passport_login(login_btn_id, mobile_id, passwd_id, url, out_url,ucenter_url,jump) {
    $(login_btn_id).click(function () { //密码登录
        var mobile = $(mobile_id).val();
        var passwd = $(passwd_id).val();
        $.post(url, {"data[mobile]": mobile, "data[passwd]": passwd}, function (ret) {
            if (ret.error == 0) {
                layer.msg(ret.message);
                if(jump){
                    setTimeout(function () {
                        window.location.href = ret.forward;
                    }, 1000)
                }else{
                    var html = '<a href="'+ucenter_url+'" class="user fl maincl">'+ret.nickname+'<i class="arrow_ico"></i></a>';
                    html+='<a href="'+ucenter_url+'message" class="message fl black6"><i class="ico"></i>消息<span class="fontcl1">'+ret.msg+'</span><i class="arrow_ico"></i></a>';
                    html+='<a href="'+out_url+'" class="message fl black6" style="margin-left:10px;display:inline;">退出</a>';
                    $('.ajax_login_box').html(html);
                    $('#index_tit').html('您好，'+ret.nickname);
                    $('#mini_log_html').remove();
                }
            }else{
                layer.msg(ret.message);
            }
        }, 'json');
    })
}

//手机验证码登录同密码登录
function yzm_login(login_btn_id, mobile_id, yzm_id, url, out_url,ucenter_url,jump) {
    $(login_btn_id).click(function () { // 手机验证码登录
        var mobile = $(mobile_id).val();
        var yzm = $(yzm_id).val();
        $.post(url, {"data[mobile]": mobile, "data[code]": yzm}, function (ret) {
            if (ret.error == 0) {
                layer.msg(ret.message);
                if(jump){
                    setTimeout(function () {
                        window.location.href = ret.forward;
                    }, 1000)
                }else{
                    var html = '<a href="'+ucenter_url+'" class="user fl maincl">'+ret.nickname+'<i class="arrow_ico"></i></a>';
                    html+='<a href="'+ucenter_url+'message" class="message fl black6"><i class="ico"></i>消息<span class="fontcl1">'+ret.msg+'</span><i class="arrow_ico"></i></a>';
                    html+='<a href="'+out_url+'" class="message fl black6" style="margin-left:10px;display:inline;">退出</a>';
                    $('.ajax_login_box').html(html);
                    $('#index_tit').html('您好，'+ret.nickname);
                    $('#mini_log_html').remove();
                }
            }else{
                layer.msg(ret.message);
            }
        }, 'json');
    })
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
        var a = key + "=" + (value);
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
            return (result[2]) || null;
        }
    }
    return null;
};
Cookie.remove = function(key){
    key = window.CFG['C_PREFIX']+key;
    document.cookie = key+"=;expires="+(new Date(0)).toGMTString();
};