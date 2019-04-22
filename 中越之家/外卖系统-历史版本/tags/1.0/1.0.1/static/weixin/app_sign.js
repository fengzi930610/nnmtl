/**
 * 【签到】
 * Created by Deepseath on 2015/3/23.
 */

define(["zepto", "underscore"], function ($, _) {

	/**
	 * 构造
	 */
	function SIGN() {

	}

	SIGN.prototype = {

		/**
		 * 发送 签到/签退 请求
		 * @param r 地理位置信息数据
		 */
		"sign": function (r) {

			var __loading = $.loading({
				"content": "请稍候……"
			});

			// 签到 or 签退
			var workon = 2;
			if ($('._sign').find('._sign_on').length > 0) {
				workon = 1;// 签到
			}

			//var _self = this;
			/*
			// 签退时判断是否下班再在此处判断
			// 签退
			if (workon == 2) {
				// 签退容器对象
				var $workoff = $('#sign-off');
				if ($workoff.data('current') < $workoff.data('workoff')) {
					var dia = $.dialog({
						"content": "当前未到下班时间是否确定“签退”？",
						"button": ["取消", "确认"]
					});
					dia.on("dialog:action", function (e) {
						if (e.index == 0) {
							// 点击“取消”
							__loading.loading('hide');
							return;
						}
						_self._sign_ajax(r, workon, __loading);
					});
					return;
				}
			}
			*/

			this._sign_ajax(r, workon, __loading);
		},

		/**
		 * 上报地理位置
		 * @param r 地理位置信息数据
		 * @returns {boolean}
		 */
		"sign_location": function(r) {

			var __loading = $.loading({
				"content": "请稍候……"
			});
			var data;
			if (typeof(r) == 'undefined' || !r || r === null || typeof(r.latitude) == 'undefined') {
				data = {
					"latitude": '',
					"longitude": '',
					"precision": ''
				};
			} else {
				data = {
					"latitude": r.latitude,
					"longitude": r.longitude,
					"precision": r.accuracy
				}
			}
			$.ajax({
				"type": "POST",
				"url": "/api/sign/post/location",
				"data": data,
				"dataType": "json",
				"beforeSend": function () {
				},
				"success": function (data) {
					if (typeof(data['errcode']) == 'undefined') {
						$.dialog({
							"content": "提交数据发生错误，请重试",
							"button": ["关闭"]
						});
						return false;
					}
					if (data['errcode'] != 0) {
						$.dialog({
							"content": data['errmsg'] + "<br />[Err: " + data['errcode'] + "].",
							"button": ["关闭"]
						});
						return false;
					}

					__loading.loading('hide');

					// 请求的结果
					var result = data['result'];

					// 最后一次记录容器
					var $log_first_box = $('#location-log-first');
					// 记录列表容器
					var $log_list_box = $('#location-log-list');

					// 上一次上报记录，移除样式“ui-form-item-link”
					var history_log = $log_first_box.html().replace('ui-form-item-link', '');
					// 本次记录
					var current_log = $.tpl($('#sign-location-log-tpl').html(), {
						"time": result['time'],
						"address": result['address']
					});
					// 把当前记录写到最后一次记录容器内
					$log_first_box.html(current_log);
					// 把上一次记录移动到列表容器内
					if (history_log) {
						$log_list_box.prepend(history_log);
					}

					$('#location-log').show();
				},
				"complete": function () {
					__loading.loading('hide');
				}
			});

			return true;
		},

		/**
		 * 新增 或者 修改 备注
		 * @returns {boolean}
		 */
		"sign_reason": function () {

			// 触发备注修改的按钮对象
			var $reason_btn = $('#sign-reason-btn');
			var sr_id = $reason_btn.attr('data-srid');
			if (!sr_id || sr_id == 'undefined') {
				$.dialog({
					"content": '请先签到后再添加备注信息',
					"button": ["关闭"]
				});
				return false;
			}

			var dia=$.dialog({
				title:'签到备注',
				content:$.tpl($('#sign-reason-tpl').html(), {"content": $('#sign-reason-txt').text()}),
				button:["取消","保存"]
			});

			var self = this;
			dia.on("dialog:action",function(e){
				if (e.index == 0) {
					// 取消
					return false;
				}

				// 显示
				var content_source = $('#sign-reason-content').val();
				var content = $.trim(content_source);
				if (content == '') {
					$('#sign-reason-txt').html('');
					$('button._sign_reason').text('添加备注');
					$('#sign-reason').hide();
				} else {
					content = self._htmlspecialchars(content);
					$('#sign-reason-txt').html(content.replace(/\n/g, "<br />\n"));
					$('button._sign_reason').text('修改备注');
					$('#sign-reason').show();
				}

				// 提交忽略返回
				$.ajax({
					"type": "POST",
					"url": "/api/sign/post/reason",
					"data": {
						"reason": content_source,
						"id": sr_id
					},
					"dataType": "json",
					"success": function (r) {
						if (typeof(r['errcode']) == 'undefined') {
							$.dialog({
								"content": '更新备注信息发生网络错误，请重试',
								"button": ["关闭"]
							});
							return false;
						}
						if (r['errcode'] != 0) {
							$.dialog({
								"content": r['errmsg'] + "<br />[Err: " + r['errcode'] + "]",
								"button": ["关闭"]
							});
							return false;
						}
					}
				});
			});
		},

		/**
		 * 签到签退请求
		 * @param r 地理位置信息数据
		 * @param workon 1=上班 or 0=下班
		 * @param __loading
		 */
		"_sign_ajax": function(r, workon, __loading) {

			var data;
			if (typeof(r) == 'undefined' || !r || r === null || typeof(r.latitude) == 'undefined') {
				data = {
					"latitude": '',
					"longitude": '',
					"precision": '',
					"type": workon
				};
			} else {
				data = {
					"latitude": r.latitude,
					"longitude": r.longitude,
					"precision": r.accuracy,
					"type": workon
				}
			}

			$.ajax({
				"type": "POST",
				"url": "/api/sign/post/sign",
				"data": data,
				"dataType": "json",
				"beforeSend": function () {
				},
				"success": function (data) {
					if (typeof(data['errcode']) == 'undefined') {
						$.dialog({
							"content": "提交数据发生错误，请重试",
							"button": ["关闭"]
						});
						return false;
					}
					if (data['errcode'] != 0) {
						$.dialog({
							"content": data['errmsg'] + '(Err: ' + data['errcode'] + ')',
							"button": ["关闭"]
						});
						return false;
					}

					// 请求的结果
					var result = data['result'];

					var html = _.template($('#sign-tpl').html(), {
						"time": result['time'],
						"address": result['address']
					});

					// 签到原因注入当前签到ID
					var $reason_btn = $('#sign-reason-btn');
					$reason_btn.attr('data-srid', result['id']);

					var $sign_off = $('#sign-off');
					if (workon == 1) {
						var $sign_on = $('#sign-on');
						$sign_on.html(html).css('display', 'block');
						$sign_off.html('<button class="ui-btn ui-btn-primary _sign_btn _sign_off">签退</button>').css('display', 'block');
					} else {
						$sign_off.html(html).css('display', 'block');
					}

					$reason_btn.show();
				},
				"complete": function () {
					__loading.loading('hide');
				}
			});
		},

		/**
		 * 转义HTML字符串
		 * @param string
		 * @returns {XML}
		 * @private
		 */
		"_htmlspecialchars": function (string) {
			string = string.toString();
			string = string.replace(/&/g, '&amp;');
			string = string.replace(/"/g, '&quot;');
			string = string.replace(/'/g, "'");
			string = string.replace(/</g, '&lt;');
			string = string.replace(/>/g, '&gt;');
			return string;
		}
	};

	return SIGN;
});

