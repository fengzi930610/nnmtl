/**
 * Copy	Right Anhuike.com
 * $Id upload.js shzhrui$
 * SWFUpload Queue Plug-in
 */

var SWFUpload;
if (typeof(SWFUpload) === "function") {
	SWFUpload.queue = {};
	SWFUpload.prototype.initSettings = function (oldInitSettings) {
		return function (userSettings) {
			if (typeof(oldInitSettings) === "function") {
				oldInitSettings.call(this, userSettings);
			}
			this._queue_list = [];	//队列的文件列表
			this._queue_size = 0;	//队列的文件大小
			this._upload_list = [];	//已经上传的文件列表
			this._upload_size = 0;	//已经上传的文件大小			
		};
	}(SWFUpload.prototype.initSettings);
}