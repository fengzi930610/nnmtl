/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: kt.upload.js 9343 2015-03-24 07:07:00Z youyi $
 */
(function(K, $){
var SWF = K.SWFUpload = K.SWFUpload || {};
SWF.Events = {
	swfupload_loaded: function(event){
		
	},
	file_queued: function(event, file){
		var swf = $(this).data('__SWF');			
		for(var i=0; i<swf._queue_list.length; i++){
			if(file.name==swf._queue_list[i].name &&
			file.size==swf._queue_list[i].size &&
			file.creationdate.getTime()==swf._queue_list[i].creationdate.getTime() &&
			file.modificationdate.getTime()==swf._queue_list[i].modificationdate.getTime()){
				swf.cancelUpload(file.id);
				return false;
			}
		}
		swf._queue_list.push(file);
		swf._queue_size += file.size;
		$(this).data('__SWF',swf);
	},
	file_queue_error: function(event, file, error, message){
		var swf = $(this).data("__SWF");
		try {
			var errorName='';
			switch (error){
				case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
					errorName = "只能同时上传 "+swf.settings.file_upload_limit+" 个文件";
					break;
				case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
					errorName = "选择的文件超过了当前大小限制："+swf.settings.file_size_limit;
					break;
				case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
					errorName = "零大小文件";
					break;
				case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
					errorName = "文件扩展名必需为："+swf.settings.file_types_description+" ("+swf.settings.file_types+")";
					break;
				default:
					errorName = "未知错误";
					break;
			}
			alert(errorName);
		} catch (ex) {
			swf.debug(ex);
		}
	},
	file_dialog_start: function(event){
		
	},
	file_dialog_complete: function(event, numFilesSelected, numFilesQueued){
		var swf = $(this).data('__SWF');
		try {
			swf._queue_limit = numFilesQueued;
			if (numFilesQueued > 0) {
				Widget.MsgBox.success("开始上传图片");
				Widget.MsgBox.load("正在上传图片...",{mask:true});
				swf.startUpload();
			}else{
				swf.startUpload();
			}
		} catch (ex) {
			swf.debug(ex);
		}
		$(this).data("__SWF",swf);
	},
	upload_start: function(event, file){
		
	},
	upload_progress: function(event, file, complete, total){
		var swf = $(this).data('__SWF');
		try {
			var p = parseInt(complete/total*100,10);
			var n = swf._queue_list.length
			var l = n - swf._queue_limit + 1 ;
			$("[rel='l']",this).show();
			$("[rel='m']",this).html('<em style="width:'+p+'%;"></em>');
			$("[rel='n']",this).html('<strong>'+l+'</strong>/'+n);
			if(p == 100){
				Widget.MsgBox.load("正在处理图片...",{mask:true});
			}
		}catch(ex){
			swf.debug(ex);
		}
	},
	upload_success: function(event, file, data){
		var swf = $(this).data('__SWF');
		try{
			swf._upload_size += file.size;
			swf._uplaod_list.push(file);
			var data = eval("("+data+")");
		}catch(ex){
			swf.debug(ex);
		}
		$(this).data("__SWF",swf);
	},
	upload_error: function(event, file, errorCode, message){
		var swf = $(this).data('__SWF');
		try {
			alert("upload_error:"+message);
		} catch (ex) {
			swf.debug(ex);
		}
	},
	upload_complete: function(event, file){
		var swf = $(this).data('__SWF');
		try {
			swf._queue_limit = swf.getStats().files_queued
			if (swf._queue_limit > 0) {
				Widget.MsgBox.load("正在上传图片...");
				swf.startUpload();
			} else {
				$("[rel='l']",this).hide();
				$(this).trigger('upload_all_complete', swf._upload_list);
				/*
				if(typeof(swf.upload_all_complete) == 'function'){
					swf._cust_upload_complete(file,this);
				}*/
			}
		} catch (ex) {
			swf.debug(ex);
		}
		$(this).data("__SWF",swf);
	}
};

SWF.Handlers = ['swfupload_loaded_handler','file_queued_handler','file_queue_error_handler','file_dialog_start_handler','file_dialog_complete_handler','upload_start_handler','upload_progress_handler','upload_error_handler','upload_success_handler','upload_complete_handler','queue_complete_handler', 'upload_all_complete_handler'];
SWF.Options = {
		flash_url : "/static/swfupload/swfupload.swf",
		upload_url: "upload.php",
		post_params: {submit:"true",OTOKEN:""},
		file_size_limit : "2 MB",
		file_types : "*.jpg;*.gif;*.jpeg;*.png",
		file_types_description : "添加图片",
		file_upload_limit : 30,
		file_queue_limit : 0,
		button_image_url:"/static/swfupload/photo.jpg",
		button_width: "84",
		button_height: "24",
		button_placeholder_id: "",
		button_placeholder: null,
		button_text_left_padding: 12,
		button_text_top_padding: 3,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,	
		debug:false
};

$.fn.SWFUpload = function(){
	var events = {},$$this = $(this),args = $.makeArray(arguments);
	$.extend(events,SWF.Events);
	if(args.length == 1 && typeof(args[0].events) == 'object'){
		$.extend(events,args[0].events);
		delete args[0]["events"];
	}
	$.each(events, function(key, val){
		$$this.unbind(key).bind(key, val);
	});
	return this.each(function(){
		var $this = $(this),__SWF = null;
		if (args.length == 1 && typeof(args[0]) == 'object') {
			__SWF = $this.data('__SWF');
			if (!__SWF) {
				var handlers = [], options = {},$control = $(this);
				if(typeof(args[0].handlers) == 'object'){
					handlers = $.makeArray(args[0].handlers);
					delete args[0]["handlers"];
				}
				if(typeof(args[0].post_params) == 'undefined' &&  $(this).attr("params")){
					args[0].post_params = $.parseJSON($(this).attr("params"));
				}
				$.extend(options,SWF.Options);	
				$.extend(options,args[0]);
				$.merge(handlers, SWF.Handlers);
				$.each(handlers, function(i, v){
					var eventName = v.replace(/_handler$/, '');
					options[v] = function() {
						var event = $.Event(eventName);
						$control.trigger(eventName, $.makeArray(arguments));
						return !event.isDefaultPrevented();
					};
				});
				var __SWF = new SWFUpload(options);
				__SWF._queue_list  = [];
				__SWF._queue_limit = 0;
				__SWF._upload_list = [];
				__SWF._upload_size = 0;
				$(this).data('__SWF', __SWF);
			}
		} else if (args.length > 0 && typeof(args[0]) == 'string') {
			var methodName = args.shift();
			__SWF = $(this).data('__SWF');
			if (__SWF && __SWF[methodName]) {
				__SWF[methodName].apply(__SWF, args);
			}
		}
	});
};

})(window.KT, window.jQuery);