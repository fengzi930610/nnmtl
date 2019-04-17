/*
* @Author: Administrator
* @Date:   2018-01-17 11:23:05
* @Last Modified by:   Administrator
* @Last Modified time: 2018-01-17 11:23:32
*/
$(function(){
		$('#msg').on('input propertychange', function(event) {
			event.preventDefault();
			if($(this).text() == '您的留言'){
				$(this).css('color','#757676');
			}else{
				$(this).css('color','#fff');
			}
		});
		$('#msg').focus(function(event) {
			if($(this).text() == '您的留言'){
				$(this).text('');
			}else{
				$(this).text('');
			}
		}).blur(function(event) {
			if($(this).text() == ''){
				$(this).text('您的留言');
			}
		});
		function fn2 (param1,param2){
			$(param1).focus(function(event) {
				if($(param1).val() == param2){
					$(param1).val('');
				}
			}).blur(function(event) {
				if($(param1).val() == ''){
					$(param1).val(param2);
				}
			});
			$(param1).on('input propertychange', function(event) {
				event.preventDefault();
				if($(this).val() == param2){
					$(this).css('color','#757676');
				}else{
					$(this).css('color','#fff');
				}
			});


		}
		fn2('#user-name','您的名字');
		fn2('#tel','您的电话');
		fn2('#test-code','请输入验证码');
	});