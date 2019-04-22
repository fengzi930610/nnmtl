
function onBridgeReady() {
	WeixinJSBridge.invoke("getNetworkType", {}, function(e) {
		WeixinJSBridge.log(e.err_msg);
	});
}

// 回退
function wx_history_go(index) {
	window.history.go(index);
}

// 关闭微信浏览器
function wx_close_window() {
	WeixinJSBridge.invoke("closeWindow",{});
}

// 解析 url
function parseURL(url) {
	var a =  document.createElement("a");
	a.href = url;
	return {
		source: url,
		protocol: a.protocol.replace(":", ""),
		host: a.hostname,
		port: a.port,
		query: a.search,
		params: (function() {
			var ret = {},
				seg = a.search.replace(/^\?/, "").split("&"),
				len = seg.length, i = 0, s;
			for (; i < len; i ++) {
				if (!seg[i]) continue;
				s = seg[i].split("=");
				ret[s[0]] = s[1];
			}

			return ret;
		})(),
		file: (a.pathname.match(/\/([^\/?#]+)$/i) || [, ""])[1],
		hash: a.hash.replace("#", ""),
		path: a.pathname.replace(/^([^\/])/, "/$1"),
		relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [, ""])[1],
		segments: a.pathname.replace(/^\//, "").split("/")
	};
}

/**
 * 继承操作
 * @param {object} child 子类
 * @param {object} parent 父类
 */
function extend(child, parent) {

	var F = function() {};
	F.prototype = parent.prototype;
	child.prototype = new F();
	child.prototype.constructor = child;
	// 调用父类方法的方法
	child.prototype.super = function() {
		var method = arguments.callee.caller;
		var f;
		for (var fn in this) {
			if (this[fn] == method) {
				f = fn;break;
			}
		}

		return parent.prototype[f].apply(this, arguments);
	};

	if (parent.prototype.constructor == Object.prototype.constructor) {
		parent.prototype.constructor = parent;
	}
}

function isWeiXin(){ 
	var ua = window.navigator.userAgent.toLowerCase(); 
	if(ua.match(/MicroMessenger/i) == 'micromessenger'){ 
		return true; 
	}else{ 
		return false; 
	} 
}

// 模块加载配置
var require = {
	baseUrl: window._js_path || "/static/weixin",
	// 别名配置
	paths: {
		
		"zepto": "zepto",
		"frozen": "frozen",
		"underscore": "underscore-min",
		"jweixin": "https://res.wx.qq.com/open/js/jweixin-1.0.0"
	},
	// 非AMD类库
	shim: {
		"zepto": {exports: "$"},
        "frozen": {exports: "fz", deps: ["zepto"]},
		"underscore": {exports: "_"}
	},
	waitSeconds: 20
};

// 如果是debug
if (window._debug) {
	require["urlArgs"] = "ts=" + (new Date()).getTime();
}

function show_dialog(content){
	var dia=$.dialog({
		title:'温馨提示',
		content:content,
		button:["确认","取消"]
	});
	dia.on("dialog:action",function(e){
		console.log(e.index)
	});
	dia.on("dialog:hide",function(e){
		console.log("dialog hide")
	});
}

function show_confirm(content){
	var dia=$.dialog({
		title:'温馨提示',
		content:content,
		button:["确认","取消"]
	});
	dia.on("dialog:action",function(e){
		console.log(e.index)
	});
	dia.on("dialog:hide",function(e){
		console.log("dialog hide")
	});
}

function show_alert(tips, title, btns) {
	// 错误提示标题
	if (_.isUndefined(title) || _.isNull(title)) {
		title = '信息提示';
	}

	// 错误窗口按钮
	if (_.isUndefined(btns) || _.isNull(btns)) {
		btns = ["确认"];
	} else if (_.isString(btns)) {
		btns = [btns];
	}

	// dailog
	var dialog = $.dialog({"title": title, "content": tips, "button": btns});
	dialog.on("dialog:hide", function(e) {
		// To do sth when dialog hide
	});
	return true;
}

function check_mail(){


}

function check_mobile(mobile){
 	var myreg = /^(((13|15|18)[0-9]{1})+\d{8})$/;
    if(!myreg.test(mobile)){
        return false;
    }
    return true;
}

function check_url(){

}

function check_number(){

}

function check_phone(){

}

