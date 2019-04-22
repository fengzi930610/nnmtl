/**
 * Copy	Right Anhuike.com
 * Each engineer has a duty to keep the code elegant
 * $Id widget.msgbox.js shzhrui$
 */
window.KT = window.KT || { "verison" : "1.0a" };
window.Widget  = window.Widget || {};
(function(K, $){
var MsgBox = Widget.MsgBox = Widget.MsgBox || {};
window.MessageBox = function(msg,type,opt,callback){
	if(typeof(opt) == 'function'){
		callback = opt;
		opt = {};
	}
	var options = $.extend({mask:false,opacity:0.3,delay:2},opt||{});
	var $box = $("#MessageBox");
	var $mask = $("#MessageMask");
	if($box.size()<1){
		$box = $('<div id="MessageBox"></div>');
		$box.appendTo("body");
	}
	type = type || "notice";
	if(type == "hide"){
		$box.stop(true,true);
		$box.fadeOut(200,function(){$mask.hide();});
	}else{
		if(options.mask){
			if($mask.size()<1){
				$mask = $('<div id="MessageMask"></div>');
				$mask.appendTo("body");
			}
			$mask.css({position:"fixed",'z-index':'1100',top:"0px",left:"0px",width:$("body").width()+"px",height:$("body").height()+"px",opacity:options.opacity}).show();
		}
		$box.stop(true,true).removeClass().addClass(type).hide();
		$box.html('<em class="icon"></em><h4>'+msg+'</h4>');
		var l = $(window).width()/2 - $box.outerWidth()/2;
		var t = $(window).height()/2; t = t > 240 ? 240 : t; t = $(window).scrollTop() + t;
		$box.css({left:l+"px",top:t+"px",opacity:1}).fadeIn(200).delay(options.delay*1000).fadeOut(function(){$mask.hide()});
	}
	if(typeof(callback) == 'function'){
		setTimeout(function(){callback() }, options.delay*1000);
	}
};

var NoticeBox = Widget.NoticeBox = Widget.NoticeBox || {};
window.NoticeBox = function(elm,msg,type,opt){
	var $elm = $(lem);
	var o = $elm.offset();
	o.width = $elm.outerWidth();
	o.height = $elm.outerHeight();
	var options = $.extend({delay:1,callback:function(){}},opt||{});
	var $box = $("#NoticeBox");
	if($box.size()<1){
		$box = $('<div id="NoticeBox"></div>');
		$box.appendTo("body");
	}
	type = type || "notice";
	if(type == "hide"){
		$box.stop(true,true);
		var t = o.top + o.height;
		$box.animate({opacity:0,top:t+"px"},200);
	}else{
		$box.stop(true,true).removeClass().addClass(type).hide();
		$box.html('<i class="icon"></i><h4>'+msg+'</h4><div class="la_jt"></div>');
		var l = o.left + o.width/2 + $box.outerWidth()/2;
		var t = o.top + o.height;
		$box.css({left:l+"px",top:t+"px",opacity:0,"display":"block"});
		$box.animate({top:o.top+"px",opacity:1},200).delay(options.delay*1000).animate({top:o.top+"px",opacity:0},200);
	}
}

MsgBox.success=function(msg,options, callback){
	layer.closeAll('loading');
	var opt = {end:null,time:3000};
	if(typeof(callback) == 'function'){
		opt.end = callback;
	}	
	return layer.msg(msg||"操作成功!", opt);
    return new MessageBox(msg||"操作成功!",'success',options,callback);
};
MsgBox.error=function(msg,options,callback){
	layer.closeAll('loading');
	var opt = {'icon':2, end:null,time:3000};
	if(typeof(callback) == 'function'){
		opt.end = callback;
	}
	return layer.msg(msg||"操作失败!", opt);

    return new MessageBox(msg||"操作失败!",'error',options,callback);
};
MsgBox.notice=function(msg,options){
	layer.closeAll('loading');
	return layer.msg(msg||"操作成功!");
	return new MessageBox(msg||"操作成功!",'notice',options,callback);   
};
MsgBox.load=function(msg,options, callback){
	layer.load(0, {shade: false});
	return true;
	layer.msg(msg, {icon: 16});
	setTimeout(function(){layer.closeAll('loading');}, 120*1000)
	return true;
	return layer.load(2, {time: 10*1000,content:msg});
	options = $.extend({delay:120,callback:function(){MsgBox.error("很抱歉,操作失败!!");}},options||{});
	return new MessageBox(msg||"数据处理中..",'load',options,callback);   
};
MsgBox.show=function(msg,type,options,callback){
	return MsgBox.success(msg, options, callback);
	type = type || "notice";
	return new MessageBox(msg||"操作成功!",type,options,callback);   
};
MsgBox.hide=function(msg, callback){
	return layer.closeAll('loading');
};
})(window.KT, window.jQuery);