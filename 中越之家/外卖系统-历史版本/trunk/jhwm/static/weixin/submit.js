/**
 * 提交
 *
 * // 返回格式, 和 api 接口保持一致
 * // 如果需要跳转, 则 result = array('url' => 'https://...', 'message' => 'succeed');
 * // 如果只是提示, 则 result = 'succeed';
 * require(["zepto", "underscore", "submit", "frozen"], function($, _, submit, fz) {
 * 	   var sbt = new submit();
 * 	   sbt.init({
 * 	       "form": $("#sbtfrm"),
 * 	       "src": $("#btn"), // 可选
 * 	       "src_event": "click" // 可选
 * 	       "submit": function(frm) {
 * 	           // submit
 * 	       }
 * 	   });
 * });
 */

define(['zepto', 'underscore', 'frozen'], function($, _) {

	function Submit() {

		// ajax 数据回调方法
		this._cb_success = null;
		// ajax 数据读取错误回调方法
		this._cb_error = null;
		// form
		this._form = null;
		// src(源对象)
		this._src = null;
		this._src_event = "tap";
		// submit 方法
		this._submit = null;
		// tips
		this._tips = null;
		// action
		this._action = "";
		this._ajax = {};
		this._loading = false;
	}

	Submit.prototype = {
		/**
		 * 初始化
		 * @param sets 设置
		 * @param ajax ajax 对象
		 * @returns {boolean}
		 */
		init: function(sets, ajax) {

			// 如果 ajax 不是对象
			if (!_.isObject(sets)) {
				return false;
			}

			// 如果 form 不存在
			if (!_.has(sets, 'form')) {
				return false;
			}

			// 表单对象
			this._form = $(sets["form"]);

			// 如果有回调函数
			if (_.has(sets, 'submit')) {
				this._submit = sets["submit"];
			}

			if (!_.isObject(ajax)) {
				ajax = {};
			}

			// 判断 ajax 是否有获取数据成功的回调方法
			if (_.has(ajax, 'success') && _.isFunction(ajax.success)) {
				this._cb_success = ajax.success;
			}

			var self = this;
			/**
			 * ajax 数据读取成功时的回调
			 * @param {*} data 返回数据
			 * @param {int} status 状态
			 * @param {XMLHttpRequest} xhr
			 */
			ajax.success = function(data, status, xhr) {

				// 如果提示对象不为空
				if (!_.isNull(self._tips)) {
					self._tips.loading("hide");
					self._tips = null;
				}

				// ajax 调用成功
				self._ajax_success(data, status, xhr);
			};

			// 错误处理方法
			if (_.has(ajax, 'error')) {
				this._cb_error = ajax.error;
			}

			/**
			 * 错误处理
			 * @param {XMLHttpRequest} xhr
			 * @param {int} errorType
			 * @param {string} error
			 * @returns {*}
			 */
			ajax.error = function(xhr, errorType, error) {

				// 调用错误处理方法
				self._tips.loading("hide");
				self._loading = false;
				if (null != self._cb_error) {
					return self._cb_error(xhr, errorType, error);
				}

				// 显示错误
				self.show_error('数据读取错误.', null, null);
				return true;
			};

			this._ajax = ajax;
			// 监听事件
			this._form.on("submit", function(e) {

				if (true == self._loading) {
					return false;
				}

				// 如果有 submit
				if (_.isFunction(self._submit)) {
					if (!self._submit(self._form)) {
						return false;
					}
				}

				self._loading = true;
				self._tips = $.loading({content:'加载中...'});
				var action = 0 < self._action.length ? self._action : $(this).attr("action");
				var method = $(this).attr("method");
				if (_.isUndefined(method)) {
					method = "GET";
				}

				method = method.toUpperCase();
				self._ajax["type"] = method;
				self._ajax["url"] = self._createurl(action, {"MINI": 1});
				self._ajax["data"] = $(this).serialize();
				self._ajax["dataType"] = "json";

				$.ajax(self._ajax);
				return false && e;
			});
			// 监听触发事件
			this._init_src_event(sets);
		},
		/**
		 * 初始化源对象事件
		 * @param sets 配置
		 * @returns {boolean}
		 * @private
		 */
		_init_src_event: function(sets) {

			// 事件类型
			if (!_.has(sets, "src_event")) {
				sets["src_event"] = "click";
			}

			this._src_event = sets["src_event"];
			// 如果 src 不存在
			if (!_.has(sets, "src")) {
				return true;
			}

			// 不存在
			this._src = $(sets["src"]);
			if (0 >= this._src) {
				return true;
			}

			// 事件监听
			var self = this;
			this._src.on(this._src_event, function(e) {

				self._form.submit();
				return false;
			});

			return true;
		},
		/**
		 * 创建url
		 * @param url url字串
		 * @param params 链接参数
		 * @returns {*}
		 * @private
		 */
		_createurl: function(url, params) {

			if (_.isUndefined(params) || _.isEmpty(params)) {
				return url;
			}

			var urls = parseURL(url);
			// http 协议/域名
			url = urls.protocol + "://" + urls.host;
			// 端口
			if (urls.port) {
				url += ":" + urls.port;
			}

			// 请求的地址
			url += urls.path;
			// 参数
			var urlparams = [];
			params = _.extend(urls.params, params);
			_.each(params, function(pr, k) {
				urlparams.push(k + "=" + pr);
			});

			// 拼接参数
			if (0 < urlparams.length) {
				url += '?' + urlparams.join('&');
			}

			return url;
		},
		/**
		 * ajax 请求成功之后的回调
		 * @param {*} data
		 * @param {int} status
		 * @param {XMLHttpRequest} xhr
		 * @returns {*}
		 */
		_ajax_success: function(result, status, xhr) {

			this._loading = false;
			// 如果数据请求错误
			if (!_.has(result, "error") || !_.has(result, "message")) {
				this.show_error("数据返回错误", null, null);
				return false;
			}
			// 如果返回的了错误码
			if (0 != result["error"]) {
				this.show_error(result["message"].join("") + "(code:" + result["error"] + ")", null, null);
				return false;
			}

			// 预先处理数据
			if (null != this._cb_success) {
				return this._cb_success(result, status, xhr);
			}

			if (_.has(result, "forward") && _.isString(result["forward"]) && 0 < result["forward"].length) {
				// 如果有提示, 则
				if (!_.has(result, "message")) {
					result["message"] = "操作成功...";
				} else {
					result["message"] = _.isEmpty(result["message"]) ? "操作成功..." : result["message"];
				}

				$.loading({content:result["message"]});
				// 延迟跳转
				setTimeout(function() {
					window.location = result["forward"];
				}, 500);
			} else {
				this.show_error(result["message"] + (0 < result["message"] ? "(code:" + result["error"] + ")" : ""), null, null);
			}

			return true;
		},
		/**
		 * 错误提示
		 * @param {string} tips 错误提示文字
		 * @param {string} title
		 * @param {*} btns
		 */
		show_error: function(tips, title, btns) {

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
	};

	return Submit;
});
