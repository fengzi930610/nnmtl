/*
* @Author: Administrator
* @Date:   2018-01-17 11:26:38
* @Last Modified by:   Administrator
* @Last Modified time: 2018-01-17 11:26:47
*/
$(function(){
		var num1 = 0;//用来放小圆点的状态索引值
		//小圆点的点击事件
		var wNum = $('.com-Box').width();

		$('.dotList li').click(function(){
			$(this).addClass('currentDot').siblings('li').removeClass('currentDot');
			num1 = $(this).index();
			// $('.comlist li').eq(num1).fadeIn().siblings('li').fadeOut();
		});
		$('.dotList li:eq(0)').click(function(event) {
			$('.com-list').animate({marginLeft:0}, 2000);
		});
		$('.dotList li:eq(1)').click(function(event) {
			$('.com-list').animate({marginLeft:-wNum}, 2000);
		});
	})