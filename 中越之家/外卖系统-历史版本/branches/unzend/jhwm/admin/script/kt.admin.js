/**
 * Copy Right anhuike.com
 * Each engineer has a duty to keep the code elegant
 * $Id kt.admin.js shzhrui<anhuike@gmail.com>
 */

window.KT = window.KT || {"version":"1.0a"};
window.Widget = Widget || {};
(function(K, $){
//app[admin] 重写
var MsgBox = Widget.MsgBox = Widget.MsgBox || {};
window.MessageBox = function(msg,type,opt, callback){
    if(typeof(opt) == 'function'){
        callback = opt;
        opt = {};
    }    
    callback = callback || function(){};
    var options = $.extend({delay:1,callback:function(){}},opt||{});
    var $box = $("#MessageBox");
    if($box.size()<1){
        $box = $('<div id="MessageBox" style="display:block;position: fixed;"></div>');
        $box.appendTo("body");
    }
    type = type || "notice";
    if(type == "hide"){
        $box.stop(true,true);
        $box.fadeOut(200);
    }else{
        $box.stop(true,true).removeClass().addClass(type).css({opacity:0});
        $box.html('<em class="icon"></em><h4>'+msg+'</h4>');
        var l = ($(window).width()-$box.outerWidth())/2;
        var t = $(window).height()/2;
        t = t <= 120 ? t : 150;
        $box.css({left:l+"px",top:t+"px",opacity:1}).fadeIn(200).delay(options.delay*1000).fadeOut();
    }
};
Widget.Dialog = Widget.Dialog || {};
Widget.Dialog.Load = function(link,title,width,handler){
    var option = {width:500,autoOpen:false};
    var opt = $.extend({},option);
    handler = handler || function(){};
    title = title || "";
    opt.width = width || opt.width; 
    Widget.MsgBox.load("数据努力加载中。。。", 5000); 
    if(link.indexOf("?")<0){
        link += "?MINI=load";
    }else{
        link += "&MINI=load";
    }
    $.get(link, function(content){
        layer.open({
            type: 1,
            title:title,
            area: opt.width+'px',
            skin: 'layui-layer-rim',
            content: content,
            success : function(){
                Widget.MsgBox.hide();handler();
            }
        });
    });

 return ;
};
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
$(document).ready(function(){
    $(".page-data table.list tr:even, .page-data table.table tr:even").addClass("alt");
    $(".page-data table.list td,.page-data table.table td").parent("tr").mouseover(function(){
        $(this).addClass("over");})
    .mouseout(function(){
        $(this).removeClass("over");    
    });
    $(".page-data table.list tr:last,.page-data table.table tr:last").find("td").addClass("clear-td-bottom")
    //自动化处理mini请求,mini-act/mini-load
    $(document).off("click","[mini-act]").on("click","[mini-act]",function(e){
        e.stopPropagation();e.preventDefault();
        var act = $(this).attr("mini-act");
        if(!__MINI_CONFIRM(this)){
            return false;
        }
        var remove = null;
        if(act.indexOf('remove:')>=0){
            remove = act.replace("remove:","");
        }
        Widget.MsgBox.success("数据处理中...");  
        Widget.MsgBox.load("数据处理中...");
        var link = $(this).attr("action") || $(this).attr("href");
        $.getJSON(link,function(ret){
            if(ret.error){
                Widget.MsgBox.error(ret.message);
            }else{
                var msg = ret.message || ["操作成功!!"];
                if(remove && $("#"+remove).size()>0){
                    msg = ret.message || ["删除内容成功!!"];
                    Widget.MsgBox.success(msg);
                    $("#"+remove).remove();
                }else{
                    Widget.MsgBox.success(msg,{delay:3});
                    setTimeout(function(){
                        if(typeof(ret.forward) != 'undefined'){
                            window.location.href = ret.forward;
                        }else{
                            window.location.reload(true);
                        }
                    }, 3000);
                }
            }
        });
    });
    if($(".page-bar").size()>0){
        $(window).scroll(function(){
            if($(".page-bar").offset().top>($(window).height()-50)){
                $(".page-bar").css({position:'fixed ',bottom:"0px",width:"100%"});
            }else{
                $(".page-bar").css({position:'static ',bottom:"0px",width:"100%"});
            }
        });
        if($(".page-bar").offset().top>($(window).height()-50)){
            $(".page-bar").css({position:'fixed ',bottom:"0px",width:"100%"});
        }
    }

    $("[photo],[tips]").on("mouseenter", function(){
        $("#widget_tips").remove();
        var html = '';
        if($(this).attr("photo")){
            html = "<div id='widget_tips' style='z-index:1200;background:#FFF;padding:8px;overflow:hidden;display:none;max-width:350px;'><img src='"+$(this).attr("photo")+"' style='max-width:400px;'/></div>";
        }else if($(this).attr("tips")){
            html = "<div id='widget_tips' style='z-index:1200;background:#FFF;padding:8px;overflow:hidden;display:none;max-width:350px;'><span>"+$(this).attr("tips")+"</span></div>";
        }
        $(html).appendTo("body");
        var offset = $(this).offset();
        var top = offset.top + $(this).height() + 5;
        var left = offset.left-7;
        if (left > ($(document).width() - 250)) {
            left = left - 230 + $(this).width()+10;
        }
        $("#widget_tips").css({top:top,left: left,position: "absolute"}).show();
    }).on("mouseleave", function(){$("#widget_tips").hide();});   
    $("[ucard]").on("mouseenter", function(){
        $("#widget_ucard").remove();
        var uid = $(this).attr("ucard").replace("@", "");
        if(uid == '0'){
            var message = '我是匆匆过客....';
        }else{
            var message = '<p style="width:200px;height:90px; ">数据努力加载中.....</p>';
        }
        $('<div id="widget_ucard" style="width:230px;border:3px solid #d5d5d5;z-index:1200;background:#FFF;padding:8px;overflow:hidden;">'+message+'</div>').appendTo("body");
        var offset = $(this).offset();
        var top = offset.top + $(this).height() + 5;
        var left = offset.left-7;
        if (left > ($(document).width() - 250)) {
            left = left - 230 + $(this).width()+10;
        }
    //显示位置
    $("#widget_ucard").css({top:top,left: left,position: "absolute"}).show();
    $("#widget_ucard").load("?member/member-ucard-"+uid+".html");
    }).on("mouseleave", function(){$("#widget_ucard").hide();});
    $(document).off("click","[map-marker]").on("click","[map-marker]", function(e){
        e.stopPropagation();e.preventDefault();
        var input = $(this).attr("map-marker").split(",");
        var point = {lng:"", lat:""};
        if(input.length < 2){
            var d = $(input[0]).val().split(",");
            point.lng = d[0];
            point.lat = d[1];
        }else{
            point.lng = $(input[0]).val();
            point.lat = $(input[1]).val();
        }
        Widget.BMap.Marker(point, function(ret){
            if(input.length < 2){
                $(input[0]).val(ret.lng+","+ret.lat);
            }else{
                $(input[0]).val(ret.lng);
                $(input[1]).val(ret.lat);
            }
        });
    });

    $(document).off("click","[mini-select]").on("click","[mini-select]", function(e){
        e.stopPropagation(); e.preventDefault();
        var a = $(this).attr("mini-select").split("/");
        var elm = a[0].split(",");
        var multi = a[1] || 'N';
        var title = a[2] || ($(this).attr("title") || "请选择");
        var link = $(this).attr("action") || $(this).attr("href");
        var width = $(this).attr("mini-width") || 700;
        Widget.Dialog.Select(link, multi, function(ret){
            if(multi == 'Y'){
                var itemIds = [], itemNames = [];
                for(var i=0; i<ret.length; i++){
                    itemIds.push(ret[i][0]);
                    itemNames.push(ret[i][1].title)
                }
                $(elm[0]).val(itemIds.join(","));
                if(elm.length > 1){
                    $(elm[1]).val(itemNames.join(","));
                }
            }else{
                $(elm[0]).val(ret[0]);
                if(elm.length > 1){
                    $(elm[1]).val(ret[1].title);
                }
            }
        }, {title:title,width:width});
    });

    $(document).off("click","[mini-selectLink]").on("click","[mini-selectLink]", function(e){
        e.stopPropagation(); e.preventDefault();
        var a = $(this).attr("mini-selectLink").split("/");
        var elm = a[0].split(",");
        var multi = 'N';
        var title = a[2] || ($(this).attr("title") || "请选择");
        var link = $(this).attr("action") || $(this).attr("href");
        var width = $(this).attr("mini-width") || 700;
        window.__selectLink = elm;
        Widget.Dialog.Select(link, multi, function(ret){         
            $(elm[0]).val(ret[0]);
            if(elm.length > 1){
                //$(elm[1]).val(ret[1].title);
                for (var i = 1; i < elm.length; i++) {
                    if(i==4){
                        $(elm[i]).attr('src', ret[1][i]);
                    }else{
                        $(elm[i]).val(ret[1][i]);
                    }                    
                };
            }
        }, {title:title,width:width});
    });

});
Widget.Region = function(wid){
    var $province = $("#"+wid+" select[province]");
    var $city = $("#"+wid+" select[city]");
    var $area = $("#"+wid+" select[area]");
    var province_id = parseInt($province.attr("province"), 10);
    var city_id = parseInt($city.attr("city"), 10);
    var area_id = parseInt($area.attr("area"), 10);
    $province.on('change', function(){
        var province_id = $(this).val();
        if(!province_id){return false;}
        $.getJSON(link = "/index.php?magic-region-city-"+province_id+".html", function(ret){
            if(ret.error){
                Widget.MsgBox.error(ret.message);
            }else if(ret.citys.length>0){
                var html = "";
                for(var i=0; i<ret.citys.length; i++){              
                    if(ret.citys[i].city_id == city_id){
                        html += '<option value="'+ret.citys[i].city_id+'" selected="selected">'+ret.citys[i].city_name+'</option>';
                    }else{
                        html += '<option value="'+ret.citys[i].city_id+'">'+ret.citys[i].city_name+'</option>';
                    }
                }
                $city.html(html);
                $city.change();
            }else{
                $city.html('<option value="">--</option>');
                $city.change();
            }
        });
    });
    $city.on('change', function(){
        var city_id = $(this).val();
        if(!city_id){return false;}
        $.getJSON("/index.php?magic-region-area-"+city_id+".html", function(ret){
            if(ret.error){
                Widget.MsgBox.error(ret.message);
            }else if(ret.areas.length>0){
                var html = "";
                for(var i=0; i<ret.areas.length; i++){
                    if(ret.areas[i].area_id == area_id){
                        html += '<option value="'+ret.areas[i].city_id+'" selected="selected">'+ret.areas[i].city_name+'</option>';
                    }else{
                        html += '<option value="'+ret.areas[i].area_id+'">'+ret.areas[i].area_name+'</option>';
                    }
                    
                }
                $area.html(html);       
            }else{
                $area.html('<option value="">--</option>');
            }
        });
    });
    if(!province_id || !city_id){
        $province.change();
        $city.change();
    }
    if(!area_id){
        $city.change();
    }
}
    Widget.Dialog.iframe = function(link, title, width, handler){
        var option = {width:700,modal:true};
        var opt = $.extend({},option);
        opt.title = title || "";
        opt.width = width || 700;
        Widget.MsgBox.success("数据处理中...");
        Widget.MsgBox.load("数据努力加载中...");
        var callback = K.GGUID();
        if(link.indexOf("?")<0){
            link += "?MINI=LoadIframe&callback="+callback;
        }else{
            link += "&MINI=LoadIframe&callback="+callback;
        }
        
       

        layer.open({
            type: 2,
            title:title,
            area: [ opt.width+'px','500px'],
            skin: 'layui-layer-rim', //加上边框
            content: link,
            success : function(){
                Widget.MsgBox.hide();handler();
            },
            cancel: function(index){
                var callback = window.pay_callback || function(){};
                callback();
            }
        });
        return ;
        /*
         $('<div style="padding:0px;margin:0px;overflow:hidden;"><iframe id="widget-dialog-iframe-content" style="width:100%;height:100%;border:0px;padding:0px;margin:0px;" border=0/></div>').dialog($.extend({create:function(event,ui){
         window.Dialog_Iframe = $(this);
         $("#widget-dialog-iframe-content").attr("src", link);},close:function(event,ui){
         $(this).dialog("destroy");
         }},opt));
         */
    }
})(window.KT, window.jQuery);