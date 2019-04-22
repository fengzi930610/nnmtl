window.Widget  = window.Widget || {};
var MsgBox = Widget.MsgBox = Widget.MsgBox || {};
MsgBox.success=function(msg, options, callback){
    MsgBox.hide();
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
    MsgBox.hide();
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
    MsgBox.hide();
    var options = {};
    callback = callback || function(ret){};
    options["end"] = callback;
    options["content"] = msg;
    layer.open({content: msg, btn: ['确认'], end: callback});
}
MsgBox.confirm = function(msg, options, callback){
    MsgBox.hide();
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
    MsgBox.hide();
    layer.open(options);
};
MsgBox.load=function(msg,options){
    MsgBox.hide();
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
//app client js call function
window.IJH = window.IJH || {};
window.JH = window.JH || {};
var IS_ANDROID = (navigator.userAgent.indexOf("Android") >=0) ? true : false;
IJH.app_login = function(params){
    IS_ANDROID ? JH.app_login(JSON.stringify(params)) : app_login(params);
}
IJH.app_loginout = function(params){
    IS_ANDROID ? JH.app_loginout(JSON.stringify(params)) : app_loginout(params);
}
IJH.app_return_items_title = function(params){
    IS_ANDROID ? JH.app_return_items_title(JSON.stringify(params)) : app_return_items_title(params);
}
IJH.app_go_pay = function(params){
    IS_ANDROID ? JH.app_go_pay(JSON.stringify(params)) : app_go_pay(params);
}
IJH.app_coin = function(params){
    IS_ANDROID ? JH.app_coin(JSON.stringify(params)) : app_coin(params);
}
IJH.app_return_link = function(params){
    IS_ANDROID ? JH.app_return_link(JSON.stringify(params)) : app_return_link(params);
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
        if(navigator.userAgent.indexOf("Android") >=0){
            return 'Android';
        }else{
            return 'IOS';
        }
    }else{
        return false;
    }
}
function checkIsAndroid(){
    if(navigator.userAgent.indexOf("Android") >=0){
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
//Gps转百度坐标
function GpsToBaidu(lng, lat) {
    var x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    var x = lng;
    var y = lat;
    var z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
    var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
    var bdlng = z * Math.cos(theta) + 0.0065;
    var bdlat = z * Math.sin(theta) + 0.006;

    return {"lng":bdlng.toFixed(5), "lat": bdlat.toFixed(5)};
}
function setUxLocation(uxl){
    UxLocation = uxl || {};
    localStorage['UxLocation'] = JSON.stringify(UxLocation);
    Cookie.set('UxLocation','{"lat":"'+UxLocation['lat']+'","lng":"'+UxLocation['lng']+'"}', 86400);
}

//取到当前的坐标(Biadu系坐标)
function getUxLocation(callback){
    callback = callback || function(ret){};
    if(UxLocation.lat && UxLocation.lng && UxLocation.addr){
        Widget.MsgBox.hide();
        UxLocation["error"] = 0;
        callback(UxLocation);
        return true;
    }else if(localStorage["UxLocation"]){
        Widget.MsgBox.hide();
        try{
            uxl = JSON.parse(localStorage["UxLocation"]) || {};
            if(uxl.lat && uxl.lng && uxl.addr){
                UxLocation = uxl;
                UxLocation["error"] = 0;
                Cookie.set('UxLocation','{"lat":"'+UxLocation['lat']+'","lng":"'+UxLocation['lng']+'"}', 86400);
                callback(UxLocation);
                return true;
            }
        }catch(e){
            setUxLocation({"lat":0, "lng":0, "addr":""});
            //alert(e);
            //Cookie中没有保存用户位置
        }
    }
    if(checkIsWeixin()){ //微信获位置坐标
        var it_wxloction = setInterval(function () {
            wx.ready(function(){
                wx.getLocation({
                    success: function (res) {
                        clearInterval(it_wxloction);
                        Widget.MsgBox.hide();
                        var poi = GpsToBaidu(res.longitude.toFixed(6), res.latitude.toFixed(6));
                        UxLocation["lat"] = poi.lat;
                        UxLocation["lng"] = poi.lng;
                        setUxLocation(UxLocation);
                        geocoder(UxLocation.lng, UxLocation.lat, function(ret){
                            if(ret.status ==0){
                                if(ret.result.pois.length>0){
                                    UxLocation['addr'] = ret.result.pois[0]['addr'];
                                    UxLocation["error"] = 0;
                                    setUxLocation(UxLocation);
                                    callback(UxLocation);                                    
                                }
                            }
                        });
                    },
                    fail: function (res) {
                        clearInterval(it_wxloction);
                        alert("fail:"+JSON.stringify(res));
                    },
                    cancel: function (res) { 
                        clearInterval(it_wxloction);
                        alert('用户拒绝授权获取地理位置');
                    },
                    complete: function (res) {
                        clearInterval(it_wxloction);
                        Widget.MsgBox.hide();
                        var poi = GpsToBaidu(res.longitude.toFixed(6), res.latitude.toFixed(6));
                        UxLocation["lat"] = poi.lat;
                        UxLocation["lng"] = poi.lng;
                        setUxLocation(UxLocation);
                        geocoder(UxLocation.lng, UxLocation.lat, function(ret){
                            if(ret.status ==0){
                                if(ret.result.pois.length>0){
                                    UxLocation['addr'] = ret.result.pois[0]['addr'];
                                    UxLocation["error"] = 0;
                                    setUxLocation(UxLocation);
                                    callback(UxLocation);                                    
                                }
                            }
                        });
                    }
                });
            });            
            // 退出循环            
            return false;
        }, 2000);
    }else if(navigator.geolocation){ //浏览器获取位置坐标
        navigator.geolocation.getCurrentPosition(function(position){
            var poi = GpsToBaidu(position.coords.longitude.toFixed(6), position.coords.latitude.toFixed(6));
            UxLocation["lat"] = poi.lat;
            UxLocation["lng"] = poi.lng;
            setUxLocation(UxLocation);
            geocoder(UxLocation.lng, UxLocation.lat, function(ret){
                if(ret.status ==0){
                    if(ret.result.pois.length>0){
                        UxLocation['addr'] = ret.result.pois[0]['addr'];
                        UxLocation["error"] = 0;                      
                        setUxLocation(UxLocation);                
                        callback(UxLocation);
                    }
                }
            });  
        },function(error){
            var error_msg = "";
            switch (error.code){
                case error.PERMISSION_DENIED:
                    error_msg = '获取位置信息失败,用户拒绝请求地理定位';
                    break; 
                case error.POSITION_UNAVAILABLE:
                    error_msg = '获取位置信息失败,位置信息不可用';
                    break; 
                case error.TIMEOUT:
                    error_msg = '获取位置信息失败,请求获取用户位置超时';
                    break; 
                case error.UNKNOWN_ERROR:
                    error_msg = '获取位置信息失败,定位系统失效';
                    break;
            }
            callback({"error":error.code, "message":error_msg});
        },{enableHighAccuracy:true});
    }else{
        callback({"error":-1, "message":error_msg});
    }
}
//调用BaiduAPI根据坐标获取标注点
function geocoder(lng, lat, callback){
    callback = callback || function(ret){};
    var callfun = GGUID();
    window[callfun] = function(ret){callback(ret);}
    $.getScript("http://api.map.baidu.com/geocoder/v2/?ak=824a595f958e444b737a5bc6325ad44f&callback="+callfun+"&location="+lat+","+lng+"&output=json&pois=1");
}
//调用BaiduAPI根据关键字和指定的城市取placelist
/*
use demo
placeapi("华润五彩", "合肥", function(ret){
    if(ret.status){
        Widget.MsgBox.error(ret.message);
    }else if(ret.results.length>0){

    }
})
*/
function placeapi(key, city, callback){
    city = city || Cookie.get("CITY_NAME");
    callback = callback || function(ret){};
    var callfun = GGUID();
    window[callfun] = function(ret){callback(ret);}
    $.getScript("http://api.map.baidu.com/place/v2/search?ak=824a595f958e444b737a5bc6325ad44f&output=json&callback="+callfun+"&query="+key+"&page_size=10&page_num=0&scope=1&region="+city);
}


//距离输出为公里
function GetDistance(lng1,lat1,lng2,lat2){
    var radLat1 = lat1 * Math.PI / 180.0;
    var radLat2 = lat2 * Math.PI / 180.0;
    var a = radLat1 - radLat2;
    var  b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
    var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a/2),2) +
    Math.cos(radLat1)*Math.cos(radLat2)*Math.pow(Math.sin(b/2),2)));
    s = s *6378.137 ;// EARTH_RADIUS;
    s = Math.round(s * 10000) / 10; //输出为公里
    return (s/1000);
}

// 格式化输出距离单位
function formatDistance(dist){
    dist = parseFloat(dist, 10);  //解析一个字符串，并返回一个浮点数,第二个参数表示10进制
    if(dist < 1000){
        return dist.toFixed(2) + "米";
    }else{
        return (dist/1000).toFixed(2) + "千米";
    }
}

/*
 * 跳转页面
 * （默认页面往左滑动，即 left）
 * （页面往右滑动，即 right
 */
function linkLoadPage(url, type) {
    if(checkIsMQQBrowser()){
        window.location.href = url;
        return ;
    }
    var animateCss = {}, animateAfterCss = {};
    type = type || 'left';
    switch (type) {
        case 'left':
            animateCss = {'left': '-' + $(window).width() + 'px'};
            animateAfterCss = {'left': '0px'};
            break;
        case 'right':
            animateCss = {'left': $(window).width() + 'px'};
            animateAfterCss = {'left': '0px'};
            window.location.href = url;
            return;
            break;
    }
    $('header,footer,section,#downOption,#shangjia_tab,.dianpuPrompt,.switchTab_box,.saixuan_pull').animate(animateCss, function () {
        Widget.MsgBox.load();
        window.addEventListener("pagehide", function () {
            $('header,footer,section,#downOption,#shangjia_tab,.dianpuPrompt,.switchTab_box').css(animateAfterCss);
            Widget.MsgBox.hide();
        });
        setTimeout(function () {
            window.location.href = url;
        });
    });
}


function build_refresher_items(url, json, tmpl, wapper, theme , first,type) {
    if (theme) {
        $('#wrapper ul').append('<div class="loading_ico"><img src="' + theme + '/static/images/load.gif" />正在加载中...</div>');
    }
    $.post(url, json, function (ret) {
        if (ret.error != 0) {
            layer.open({'content': ret.message});
        } else if (!ret.data.items) {
            if(first){
                if(type == 'shop'){
                    $('.loading_ico').remove();
                    $('#wrapper ul').append('<div class="youhui_no"><div class="iconBg"><i class="ico7"></i> </div><h2>暂无商铺</h2><p class="black9">抱歉，暂时没有符合您搜索的商铺！</p></div>');
                }
            }else{
                $('.loading_ico').remove();
                $("#pullUp .pullUpLabel").html('没有更多了');
            }
        } else if (ret.data.items.length == 0) {
            if(first){
                if(type == 'shop'){
                    $('.loading_ico').remove();
                    $('#wrapper ul').append('<div class="youhui_no"><div class="iconBg"><i class="ico7"></i> </div><h2>暂无商铺</h2><p class="black9">抱歉，暂时没有符合您搜索的商铺！</p></div>');
                }
            }else{
                $('.loading_ico').remove();
                $("#pullUp .pullUpLabel").html('没有更多了');
            }
        } else {
            $('.loading_ico').remove();
            $(tmpl).tmpl(ret.data.items).appendTo(wapper);
        }
    }, 'json');
}


function select(addrs,from) { //参数1：地址数组 参数2：来源

   var contant = '';
   var str = '';
   var html = '';

   html+='<div class="mask" style="bottom:0;">';
   html+='<div class="nr" style="padding:0;position:absolute;width:100%;bottom:0;">';
   html+='<div class="mask_prompt">';
   html+='<div class="title"><em></em>地址选择<span class="fr mr10" id="add_addr_span">新增地址</span></div>';
   html+='<div class="cont" style="padding:0;"><div style="height:400px;overflow-y:scroll;">';
   
   var i = 1;
   $.each(addrs, function(key, value){
    html+='<p style="text-align:left;">';
       if(value){
           i = i +1;
           if(i = 1){
             html+=('&nbsp;&nbsp;<input type="radio" name="addr" checked="checked" value="'+key+'" id="v'+key+'" style="margin:0;padding:0;font-size:0;height:" />&nbsp;<span mp="'+value.house+'" mb="'+value.mobile+'"  ct="'+value.contact+'">'+value.addr+'</span>');
           }else{
               html+=('&nbsp;&nbsp;<input type="radio" name="addr" value="'+key+'" id="v'+key+'" style="margin:0;padding:0;font-size:0;" />&nbsp;<span mp="'+value.house+'" mb="'+value.mobile+'" ct="'+value.contact+'" >'+value.addr+'</span>');
           }
       }
   html+='</p>';
   })

   html+='</div><input type="button" class="pub_btn" id="dz_btn" value="确定"><input type="button" class="cancel_btn" value="取消">';
   html+='</div>';
   html+='</div>';
   html+='</div>';
   html+='</div>';

   $("section").append(html);
   $('#dz_btn').click(function(){
       var val = ($('input[name="addr"]:checked'));
       var vid = val.attr('id');
       $('#sd').val($('#'+vid).parent().find('span').html()).css('color','#2cca77');
       $('#sd_val').val(val.val());
       $('#mp_val').val($('#'+vid).parent().find('span').attr('mp')).css('color','#2cca77');
       $('#mb_val').val($('#'+vid).parent().find('span').attr('mb')).css('color','#2cca77');
       $('#c_val').val($('#'+vid).parent().find('span').attr('ct')).css('color','#2cca77');

       if(from){
           localStorage.setItem(from+'_sd',$('#'+vid).parent().find('span').html());
           localStorage.setItem(from+'_sd_val',val.val());
           localStorage.setItem(from+'_mp_val',$('#'+vid).parent().find('span').attr('mp'));
           localStorage.setItem(from+'_mb_val',$('#'+vid).parent().find('span').attr('mb'));
           localStorage.setItem(from+'_c_val',$('#'+vid).parent().find('span').attr('ct'));
       }

       $('.mask').remove();
   })

   $('.cancel_btn').click(function(){
       $('.mask').remove();
   })

}


function select_time(time,from) {
    //time获取当前小时数
    var html = '';

    html+='<div class="mask" style="bottom:0;">';
    html+='<div class="nr" style="padding:0;position:fixed;width:100%;bottom:0;left:0;">';
    html+='<div class="mask_prompt">';
    html+='<div class="title"><em></em>时间选择</div>';
    html+='<div class="cont" style="padding:0;">';
    //时间选择结构
    html+='<div class="time" style="width:100%;">';

    html+='<div class="top">';

    html+='<ul class="title">';
    html+='<li class="no sel" val="1">今天</li><li class="no" val="2">明天</li><li class="no" val="3">后天</li><li class="no" style="border:0px" val="4">大后天</li>';
    html+='</ul>';
    html+='<div style="clear:both;"></div>';

    html+='<ul class="timers" id="ctime1">';


    for(var a = 8;a<=19;a++){

            if(parseInt(a) < parseInt(time)+1){
                html+='<li val="0">'+a+':00<span  style="color:#ff0000;">(满)</span></li>';
            }else{
                html+='<li val="'+a+'">'+a+':00</li>';
            }
 
    }

    html+='</ul>';
    html+='<div style="clear:both;"></div>';


    for(var i = 2;i<=4;i++){

        html+='<ul class="timers" id="ctime'+i+'">';

            for(var o = 8;o<=19;o++){
              
                    html+='<li val="'+o+'">'+o+':00</li>';
   
            }

        html+='</ul>';
        html+='<div style="clear:both;"></div>';

    }


    html+='</div>';


    html+='</div>';


    html+='<input type="button" class="pub_btn" id="rl_btn" value="确定"><input type="button" class="cancel_btn" value="取消">';
    html+='</div>';
    html+='</div>';
    html+='</div>';
    html+='</div>';

    $("section").append(html);

    $('#rl_btn').click(function(){
        
       var d = $(this).attr('val');
       var t = $(this).attr('tval');
       var dd = $(this).attr('dhtml');
       var tt = $(this).attr('thtml');

       if(!dd){
           dd='今天';
       }
       if(!d){
           d=1;
       }

       if(!t || t==0){
           alert('时间没有选择!');return false;
       }

       $('#sday_val').val(d);
       $('#stime_val').val(t);
       $('#stime').val(dd+tt).css('color','#2cca77');

       if(from){
           localStorage.setItem(from+'_stime',dd+tt);
           localStorage.setItem(from+'_stime_val',t);
           localStorage.setItem(from+'_sday_val',d);
       }

       $("footer").show();
       $("section").css('bottom','0.65rem');
       $('.mask').remove();
       
   })


     $('.cancel_btn').click(function(){
       $("footer").show();
       $("section").css('bottom','0.65rem');
       $('.mask').remove();
   })

   $('.title li').click(function(){
       var val = $(this).attr('val');
       $('.title li').removeClass('sel');
       $(this).addClass('sel');
       $('.timers').hide();
       $('#ctime'+val).show();
       $('.pub_btn').attr('val',val);
       var day_html = $(this).text();
       $('.pub_btn').attr('dhtml',day_html);
   })

   $('.timers li').click(function(){
       var tval = $(this).attr('val');
       if(tval > 0){
           $('.timers li').css('background','#2cca77').css('color','#ffffff');
           $(this).css('background','#ffffff').css('color','#2cca77');
       }

       $('.pub_btn').attr('tval',tval);
       var time_html = $(this).text();
       $('.pub_btn').attr('thtml',time_html);
   })


}


/*+=======================================
 + 外卖JS购物车
 +=======================================*/
window.cart_goods = window.cart_goods || {};
window.ele = {
    init: function () {
        var json = window.cookies.get('ele') || {};
        cart_goods = window.cookies.parse(json);
    },
    addcart: function (shop_id, data) {
        cart_goods[shop_id] = cart_goods[shop_id] || {};
        data['num'] = data['num'] || 1;
        if (typeof (cart_goods[shop_id][data['product_id']]) == 'undefined') {
            cart_goods[shop_id][data['product_id']] = data;
        } else if (cart_goods[shop_id][data['product_id']]['num'] >= 99) {
            layer.open({content: "店里没有那么多商品了"});
        } else {
            var num = cart_goods[shop_id][data['product_id']]['num'] || 0;
            data['num'] += parseInt(num, 10);
            cart_goods[shop_id][data['product_id']] = data;
        }
        var goods = window.cookies.stringify(cart_goods);
        window.cookies.set('ele', goods);
    },
    getcart: function () {
        return window.cart_goods;
    },
    dec: function (shop_id, product_id, num) {
        num = num || 1;
        cart_goods[shop_id] = cart_goods[shop_id] || {};
        if (typeof (cart_goods[shop_id][product_id]) == 'undefined') {
            return true;
        } else {
            cart_goods[shop_id][product_id]['num'] -= parseInt(num, 10);
        }
        var product_list = {};
        for (var k in cart_goods[shop_id]) {
            if (cart_goods[shop_id][k]['num'] > 0) {
                product_list[k] = cart_goods[shop_id][k];
            }
        }
        cart_goods[shop_id] = product_list;
        var goods = window.cookies.stringify(cart_goods);
        window.cookies.set('ele', goods);
    },
    count: function (shop_id) {
        var count = 0;
        if (typeof (cart_goods[shop_id]) != 'undefined') {
            for (var pid in cart_goods[shop_id]) {
                count += parseInt(cart_goods[shop_id][pid]['num'], 10);
            }
        }
        return count;
    },
    itemcount: function (product_id) {
        var count = 0;
        for (var sid in cart_goods) {
            for (var pid in cart_goods[sid]) {
                if (pid = product_id) {
                    count = cart_goods[sid][pid]['num'];
                    break;
                }
            }
        }
        return count;
    },
    catecount: function (shop_id) {
        var count = {};
        var goods = cart_goods[shop_id] || {};
        for (var pid in goods) {
            count[goods[pid]['cate_id']] = parseInt(count[goods[pid]['cate_id']], 10) || 0;
            count[goods[pid]['cate_id']] += parseInt(goods[pid]['num'], 10);
        }
        return count;
    },
    totalprice: function (shop_id) {
        var total_price = 0;
        var goods = cart_goods[shop_id] || {};
        for (var pid in goods) {
            //total_price += (goods[pid]['price'] + goods[pid]['package_price']) * goods[pid]['num'];
            total_price += parseFloat(goods[pid]['price'], 10) * parseInt(goods[pid]['num'], 10);
        }
        return total_price;
    },
    packprice: function (shop_id) {
        var total_price = 0;
        var goods = cart_goods[shop_id] || {};
        for (var pid in goods) {
            //total_price += (goods[pid]['price'] + goods[pid]['package_price']) * goods[pid]['num'];
            total_price += parseFloat(goods[pid]['package_price'], 10) * parseInt(goods[pid]['num'], 10);
        }
        return total_price;
    },
    removeby: function (shop_id) {
        var obj = {};
        for (var sid in cart_goods) {
            if (sid != shop_id) {
                obj[sid] = cart_goods[sid];
            }
        }
        cart_goods = obj;
        var goods = window.cookies.stringify(cart_goods);
        window.cookies.set('ele', goods);
    }
}

// 送达时间选择
function time_select(start, start_quarter, end, end_quarter) {
    start = parseInt(start, 10);
    start_quarter = parseInt(start_quarter, 10);
    end = parseInt(end, 10);
    end_quarter = parseInt(end_quarter, 10);
    var html = '';
    if (start_quarter > 0) {
        for (var q = start_quarter; q <= 3; q++) {
            if (q == 3) {
                html += '<li>' + start + ':' + q * 15 + '-' + (start + 1) + ':00' + '</li>';
            } else {
                html += '<li>' + start + ':' + q * 15 + '-' + start + ':' + (q + 1) * 15 + '</li>';
            }
        }
        if(start+1<end){
            for (var i = start + 1; i < end; i++) {
                for (var q = 0; q <= 3; q++) {
                    var end_time = i + ':' + (q + 1) * 15;
                    if (q == 3) {
                        end_time = (i + 1) + ':00';
                    }
                    var begin_time = i + ':' + q * 15;
                    if (q == 0) {
                        begin_time = i + ':00';
                    }
                    html += '<li>' + begin_time + '-' + end_time + '</li>';
                }
            }
        }
    }else if (end_quarter > 0) {
        for (var i = start; i < end; i++) {
            for (var q = 0; q <= 3; q++) {
                var end_time = i + ':' + (q + 1) * 15;
                if (q == 3) {
                    end_time = (i + 1) + ':00';
                }
                var begin_time = i + ':' + q * 15;
                if (q == 0) {
                    begin_time = i + ':00';
                }
                html += '<li>' + begin_time + '-' + end_time + '</li>';
            }
        }
        for (var q = 0; q < end_quarter; q++) {
            if (q == 0) {
                html += '<li>' + end + ':00-' + end + ':' + (q + 1) * 15 + '</li>';
            } else {
                html += '<li>' + end + ':' + q * 15 + '-' + end + ':' + (q + 1) * 15 + '</li>';
            }
        }
    }else{
        for (var i = start; i < end; i++) {
            for (var q = 0; q <= 3; q++) {
                var end_time = i + ':' + (q + 1) * 15;
                if (q == 3) {
                    end_time = (i + 1) + ':00';
                }
                var begin_time = i + ':' + q * 15;
                if (q == 0) {
                    begin_time = i + ':00';
                }
                html += '<li>' + begin_time + '-' + end_time + '</li>';
            }
        }
    }
    return html;
}


function position() {
    if (localStorage.getItem('lat') && localStorage.getItem('lng')) {
        //如果存在wx则转化为bd，并且赋值road

        var y = localStorage.getItem('lat');
        var x = localStorage.getItem('lng');

        var ggPoint = new BMap.Point(x, y);

        //坐标转换完之后的回调函数
        translateCallback = function (data) {
            if (data.status === 0) {
                var xx = data.points[0].lat;
                var yy = data.points[0].lng;

                //var map = new BMap.Map("allmap");
                var point = new BMap.Point(yy, xx);
                //map.centerAndZoom(point,12);
                var geoc = new BMap.Geocoder();

                geoc.getLocation(point, function (rs) {
                    var addComp = rs.addressComponents;
                    if (addComp) {
                        localStorage['road'] = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
                    }
                });
            }
        }
        setTimeout(function () {
            var convertor = new BMap.Convertor();
            var pointArr = [];
            pointArr.push(ggPoint);
            convertor.translate(pointArr, 1, 5, translateCallback)
        }, 1000);
    }
}

/*页面点击事件*/
if(window.WXJS_CFG && checkIsWeixin()){
    wx.config({debug:false,appId:WXJS_CFG.appId,timestamp:WXJS_CFG.timestamp,nonceStr:WXJS_CFG.nonceStr,signature:WXJS_CFG.signature,jsApiList:["getLocation"]});
}
$(document).ready(function () {
    FastClick.attach(document.body);
    $(document).on("click", "[link-load]", function () {
        var url = $(this).attr("link-url") || $(this).attr("href");
        var type = $(this).attr('link-type') || "left";
        linkLoadPage(url, type);
        return false;
    });
});

//获取地址参数
window.getURLArgs = function()
{
    var search = decodeURIComponent(location.search);
    if(search != ''){
        search = search.substring(1, search.length);
        search = search.split('&');
        for(i in search){
            kv = search[i].split('=');
            search[kv[0]] = kv[1];
            delete search[i];
        }
        return search;
    }
    return {};
}

//获取js对象长度
function getObjLen(data) {
    index = 0;
    for(i in data){
        index+=1;
    }
    return index;
}


/*数组去重复*/
function Unique(arr) {
    var result = [], hash = {};
    for (var i = 0, elem; (elem = arr[i]) != null; i++) {
        if (!hash[elem]) {
            result.push(elem);
            hash[elem] = true;
        }
    }
    return result;
}