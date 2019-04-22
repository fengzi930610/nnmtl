/**
 * 获取位置组件
 * Created by Deepseath on 2015/3/23.
 */
define(["zepto", "underscore", "jweixin", "h5mod/jweixin-error"], function ($, _, wx, jweixin_error) {
	function GET_LOCATION() {
		var __self = this;
		// 已获取的经纬度信息缓存
		__self._location_data = {};
		//var location_data = localStorage.getItem('location_address');
		//if (location_data != null && location_data != 'undefined') {
		//	__self._location_data = location_data;
		//}
		// 微信接口是否已加载完毕
		__self._wx_loaded = false;
		// 事件监听，初始化接口加载状态
		$(function () {
			// 微信接口加载完毕
			wx.ready(function () {
				__self._wx_loaded = true;
			});
			// 微信接口加载错误
			wx.error(function (res) {
				__self._errhandle(res, 'config');
			});
		});
	}
	GET_LOCATION.prototype = {
		/**
		 * 获取经纬度
		 * @param callback 回调的函数名
		 * @param options 相关配置信息
		 * + get_address 是否获取位置信息
		 * @returns {boolean}
		 */
		"get": function (callback, options) {
			// 默认配置信息
			var default_options = {
				"get_address": true,
				"show_error": true
			};
			// 定义配置信息
			if (typeof(options) == 'undefined') {
				options = default_options;
			} else {
				options = $.extend(options, default_options);
			}
			// 对象指针
			var __self = this;
			// 之前已获取过位置数据，不再请求
			if (!_.isEmpty(__self._location_data)) {
				//alert('历史数据' + JSON.stringify(__self._location_data));
				if (typeof(callback) == "function") {
					callback(__self._location_data);
				}
				return true;
			}
			// loading 启动
			var __loading = $.loading({
				"content": "请稍候……"
			});
			// 循环检查微信接口是否加载
			var it = setInterval(function () {
				if (__self._wx_loaded) {
					// loading 关闭
					wx.getLocation({
						"success": function (res) {
							__self._location_data = __self.get_address(res, options);
							if (typeof(callback) == "function") {
								if(options.get_address){
									$.getJSON("/weixin/api/location?location="+__self._location_data.latitude+","+__self._location_data.longitude, function(ret){
										__self._location_data.address = ret.address;
										callback(__self._location_data);;
									})
								}else{
									callback(__self._location_data);
								}
							}
						},
						"fail": function (res) {
							__self._errhandle(res, 'fail');
						},
						"cancel": function (res) {
							__self._errhandle(res, 'cancel');
						},
						"complete": function () {
							__loading.loading("hide");
						}
					});
					// 退出循环
					clearInterval(it);
					return false;
				}
			}, 500);
		},
		/**
		 * 获取经纬度所在位置信息
		 * @param res
		 * @param options
		 * @returns {Object}
		 */
		"get_address": function (res, options) {
			// 初始化要输出的数据
			var data = {
				"longitude": res.longitude,// 纬度，浮点数，范围为90 ~ -90
				"latitude": res.latitude,// 经度，浮点数，范围为180 ~ -180。
				"speed": res.speed,// 速度，以米/每秒计
				"accuracy": res.accuracy,// 位置精度
				"address": '',// 所在地理位置信息
				"_time": _.now()// 当前时间戳
			};
			return data;
		},
		/**
		 * 失败错误处理
		 * @param res
		 * @param wxjsType
		 */
		"_errhandle": function (res, wxjsType) {
			var s = new jweixin_error();
			s.errhandle(res, 'getLocation', wxjsType, true, true);
			return true;
		}
	};
	return GET_LOCATION;
});
