/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id kK.js by @shzhrui<anhuike@gmail.com>
 */

window.KT = window.KT || {version: "1.0a"};
window.Widget = window.Widget || {};
(function(K, $){
K.$GUID = "KT";
//Global 容器
window.$_G = K._G = {};
$_G.get = function(key){
	return K._G[key];
};
$_G.set = function(key, value, protected_) {
	var b = !protected_ || (protected_ && typeof K.G[key] == "undefined");
	b && (K._G[key] = value);
	return K._G[key];
};

//生成全局GUID
K.GGUID = function(){
	var guid = K.$GUID;
	for (var i = 1; i <= 32; i++) {
		var n = Math.floor(Math.random() * 16.0).toString(16);
		guid += n;
	}
	return guid.toUpperCase();
};
K.Guid = function(){
	return K.$GUID + $_G._counter++;
};
$_G._counter = $_G._counter || 1;

//cookie
var Cookie = window.Cookie = window.Cookie || {};
//验证字符串是否合法的cookie键名
Cookie._valid_key = function(key){
    return (new RegExp("^[^\\x00-\\x20\\x7f\\(\\)<>@,;:\\\\\\\"\\[\\]\\?=\\{\\}\\/\\u0080-\\uffff]+\x24")).test(key);
}
Cookie.set = function(key, value, expire){
	if(Cookie._valid_key(key)){
		var a = key + "=" + escape(value);
		if(typeof(expire) != 'undefined'){
			var date = new Date();
			expire = parseInt(expire,10);
			data.setTime(date.getTime + expire*1000);
			a += "; expires="+data.toGMTString();
		}
		document.cookie = a;
	}
	return null;
};
Cookie.get = function(key){
	if(Cookie._valid_key(key)){
        var reg = new RegExp("(^| )" + key + "=([^;]*)(;|\x24)"),
            result = reg.exec(document.cookie);            
        if(result){
            return result[2] || null;
        }
	}
	return null;
};
Cookie.remove = function(key){
	document.cookie = key+"=;expires="+(new Date(0)).toGMTString();
};

Widget.Dialog = Widget.Dialog || {};

Widget.Dialog.Load = function(link,title,width,handler,params){
	var option = {width:500,autoOpen:false,modal:true,dialogClass:'ui-hack-widget-dialog',position:{my: "center top",at: "center top+120px",of: window},maxHeight:($(window).height()-100),maxWidth:($(window).width()-100)};
	var opt = $.extend({},option);
	handler = handler || function(){};
	params = params || {};
	title = title || "";
	opt.width = width || opt.width;	
	Widget.MsgBox.load("数据努力加载中。。。");	
	if(link.indexOf("?")<0){
		link += "?MINI=load";
	}else{
		link += "&MINI=load";
	}
	$('<div title="'+title+'" id="widget-dialog-load-content">数据努力加载中。。。</div>').dialog($.extend({create:function(event,ui){$("#widget-dialog-load-content").load(link,params,function(){
		if(!$(this).dialog("isOpen")){$(this).dialog("open");}Widget.MsgBox.hide();handler();
	});},close:function(event,ui){
		$(this).dialog("destroy");
	}},opt));
};
window.Dialog_callback = [];
Widget.Dialog.iframe = function(link, title, width, handler){
	var option = {width:700,modal:true,dialogClass:'ui-hack-widget-dialog',position:{my: "center top",at: "center top+80px",of: window},maxHeight:($(window).height()-100),maxWidth:($(window).width()-100)};
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
	$('<div style="padding:0px;margin:0px;overflow:hidden;"><iframe id="widget-dialog-iframe-content" style="width:100%;height:100%;border:0px;padding:0px;margin:0px;" border=0/></div>').dialog($.extend({create:function(event,ui){
		window.Dialog_Iframe = $(this);
		$("#widget-dialog-iframe-content").attr("src", link);},close:function(event,ui){
		$(this).dialog("destroy");
	}},opt));		
}
Widget.Dialog.Select = function(link, multi, handler, opt){
	var option = {width:700,height:560,modal:true,dialogClass:'ui-hack-widget-dialog',position:{my: "center top",at: "center top+80px",of: window},minHeight:500,maxWidth:600};
	Widget.MsgBox.success("数据处理中...");
	Widget.MsgBox.load("数据努力加载中...");
	opt = $.extend(opt||{},option);
	multi = multi || 'N';
	handler = handler || function(ret){return ret;};
	if(link.indexOf("?")<0){
		link += "?MINI=LoadIframe&multi="+multi;
	}else{
		link += "&MINI=LoadIframe&multi="+multi;
	}
	$('<div style="padding:0px;margin:0px;overflow:hidden;"><iframe id="widget-dialog-iframe-content" style="width:100%;height:100%;border:0px;padding:0px;margin:0px;" border=0/></div>').dialog($.extend({create:function(event,ui){
			var $Dialog = $(this);
			$("#widget-dialog-iframe-content").load(function(){
				Widget.MsgBox.hide();
				var h = $("#widget-dialog-iframe-content").contents().find('body').height();
				if((h + 200) > $(window).height()){
					$Dialog.dialog({"height": ($(window).height()-200)});
				}else if(h > 500){
					$Dialog.dialog({"height":660});
				}else{
					$Dialog.dialog({"height":h+110});
				}
			});
			$("#widget-dialog-iframe-content").attr("src", link);
		},
		close:function(event,ui){$(this).dialog("destroy");},
		buttons:{"确定选择": function() {
			 var items = [];
			 $("#widget-dialog-iframe-content").contents().find('input[CK="PRI"]').each(function(){
				if($(this).attr("checked")){
					var data = $(this).attr("data") || '{}';
					data = eval('(' + data + ')');
					if(multi == 'Y'){
						items.push([$(this).val(), data]);
					}else{
						items = [$(this).val(), data];
					}
				}
			 });
			handler(items);
			$(this).dialog("destroy");
		}
	}},opt));
}

Widget.Dialog.confirm = function(title,handler){

};
Widget.Dialog.notice = function(){

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
	//自动化处理mini请求,mini-act/mini-load
	$("[mini-act]").die("click").live("click",function(e){
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
			if(ret.error == 101){
				Widget.Login();
			}else if(ret.error){
				Widget.MsgBox.error(ret.message.join(","));
			}else{
				var msg = ret.message || ["操作成功!!"];
				if(remove && $("#"+remove).size()>0){
					msg = ret.message || ["删除内容成功!!"];
					Widget.MsgBox.success(msg.join(""));
					$("#"+remove).remove();
				}else{
					Widget.MsgBox.success(msg.join(""),{delay:5});
					if(typeof(ret.forward) != 'undefined'){						
						setTimeout(function(){window.location.href = ret.forward;}, 800);
					}else{
						setTimeout(function(){window.location.reload(true);}, 800);
					}
				}
			}
		});
	});
	$("[mini-load]").die("click").live("click",function(e){
		e.stopPropagation();e.preventDefault();
		if(!__MINI_CONFIRM(this)){
			return false;
		}
		var params = null;
		var link = $(this).attr("action") || $(this).attr("href");
		if($(this).attr("mini-batch")){
			var batch = $(this).attr("mini-batch");
			var $cks = $(":checkbox[CK='"+batch+"']");
			var itemIds = [];
			$cks.each(function(){if($(this).attr("checked")){itemIds.push($(this).val());}});
			if(itemIds.length<1){
				Widget.MsgBox.error('没有选择任何内容');
				return false;
			}
			if(link.indexOf("?")>-1){
				link += "&itemIds="+itemIds.join(',');
			}else{
				link += "?itemIds="+itemIds.join(',');
			}
		}else if($(this).attr("mini-form")){
			params = $($(this).attr("mini-form")).serialize();
			console.log(params);
		}
		var title = $(this).attr("mini-title") || ($(this).attr("mini-load") || "");
		var width = $(this).attr("mini-width") || 600;
		Widget.Dialog.Load(link, title, width, function(){}, params);
	});
	$("form[mini-form]").die("submit").live("submit",function(){
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
	$("[mini-submit],a[mini-submit]").die("click").live("click",function(e){
		e.stopPropagation();e.preventDefault();
		window.__MINI_LOAD = window.__MINI_LOAD || false;
		if(window.__MINI_LOAD){ //防止重复提交
			return false;
		}
		if(!__MINI_CONFIRM(this)){
			return false;
		}
		if($("#miniframe").size()<1){
			$("body").prepend('<iframe id="miniframe" name="miniframe" style="display:none;"></iframe>');
		}
		var form = $(this).attr("mini-submit");
		var action = $(this).attr("action") || $(form).attr("action");
		$(form).attr("action", action).attr("target", "miniframe").attr("method", "post");
		var value = $(this).attr("mini-value") || "true";
		Widget.MsgBox.success("数据处理中...");
		Widget.MsgBox.load("数据处理中...");
		if($(form).find("[name='MINI']").size()<1){
			$(form).prepend('<input type="hidden" name="MINI" value="iframe" />');
		}
		$(form).find("[name='MINI']").val('iframe');
		if($(form).find("input[type='file']").size()>0){
			$(form).attr("ENCTYPE", "multipart/form-data");
		}
		$(form).trigger("submit");
		return true;	
	});
	$("[mini-submit]").each(function(){
		var elm = $(this).attr("mini-submit")
		$(elm).find("input:text,input:password").click(function(e){
		    if(e.keyCode == 13){
		        e.stopPropagation();e.preventDefault();
		        $(elm).trigger("submit");
		        return false;
		    }
		});
	});
	$("form[mini-form]").each(function(){
		$(this).find("input:text,input:password").click(function(e){
		    if(e.keyCode == 13){
		        e.stopPropagation();e.preventDefault();
		        $(this).trigger("submit");
		        return false;
		    }
		});
	});
	$("[mini-iframe]").die("click").live("click",function(e){
		e.stopPropagation();e.preventDefault();
		if(!__MINI_CONFIRM(this)){
			return false;
		}
		var link = $(this).attr("action") || $(this).attr("href");
		var title = $(this).attr("mini-title") || ($(this).attr("mini-iframe") || "");
		var width = $(this).attr("mini-width") || 600;
		var handler = eval("("+($(this).attr("mini-handler") || "function(ret){}")+")");
		Widget.Dialog.iframe(link,title,width);
	});
	$("[win-load]").die("click").live("click", function(e){
		e.stopPropagation();e.preventDefault();
		var url = $(this).attr("action") ? $(this).attr("action") : $(this).attr("href");
		var w = $(window).width();var h = $(window).height();
		window.open (url, 'KT-WIN-Dialog','height='+h+',width='+w+',top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no') 
	});
});
Widget.Region = function(wid){
	var $province = $("#"+wid+" select[province]");
	var $city = $("#"+wid+" select[city]");
	var $area = $("#"+wid+" select[area]");
	var province_id = parseInt($province.attr("province"), 10);
	var city_id = parseInt($city.attr("city"), 10);
	var area_id = parseInt($area.attr("area"), 10);
	$province.live('change', function(){
		var province_id = $(this).val();
		if(!province_id){return false;}
		$.getJSON(link = "/index.php?magic-region-city-"+province_id+".html", function(ret){
			if(ret.error){
				Widget.MsgBox.error(ret.message.join(","));
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
	$city.live('change', function(){
		var city_id = $(this).val();
		if(!city_id){return false;}
		$.getJSON("/index.php?magic-region-area-"+city_id+".html", function(ret){
			if(ret.error){
				Widget.MsgBox.error(ret.message.join(","));
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
})(window.KT, window.jQuery);