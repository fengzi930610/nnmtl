//
$(document).ready(function () {
	/*定位搜索*/
	if ($('.lctSer').length > 0)/*判断是否存在这个html代码*/
	{
		$('.page_center_box').css('top', '1.1rem');
	}
	/*头部切换*/
	if ($('.switchTab_box').length > 0)/*判断是否存在这个html代码*/
	{
		$('.switchTab_box .switchTab_list').width(100 / $('.switchTab_box .switchTab_list').length + '%');
		$('.page_center_box').css('top', '0.92rem');
	}
	/*头部切换*/
	if ($('.changeTab_box').length > 0)/*判断是否存在这个html代码*/
	{
		$('.page_center_box').css('top', '0.95rem');
	}
	/*底部*/
	if ($('footer').length > 0)/*判断是否存在这个html代码*/
	{
		$('.page_center_box').css('bottom', '0.6rem');
	}

	/*复选项选择开始*/
	$('.checkboxLabel').click(function(){
		if($(this).find('.radioInt').hasClass('on')){
			$(this).parent().find('.radioInt').removeClass('on');
		}
		else{
			$(this).parent().find('.radioInt').addClass('on');
		}
	});
	/*复选项选择结束*/
	/*店铺页面开始*/
	if ($('#shangjia_tab').length > 0)/*判断是否存在这个html代码*/
	{
		$('#shangjia_tab li').width(100 / $('#shangjia_tab li').length + '%');
		$('.page_center_box').css('top', '0.9rem');
	}//头部切换tab结束
	if ($('.dianpuPrompt').length > 0 && $('#shangjia_tab').length > 0)/*判断是否存在这个html代码*/
	{
		$('#shangjia_tab').css('top', '0.9rem');
		$('.page_center_box').css('top', '1.3rem');
	} else if ($('.dianpuPrompt').length > 0 || $('#shangjia_tab').length > 0) {
		$('.page_center_box').css('top', '0.9rem');
	}//头部提示结束
	/*店铺页面结束*/
	
	/*头部关注按钮心*/
	$(".attentionIco").click(function(){
		if($(this).hasClass("on")){
			$(this).removeClass("on");
		}
		else{
			$(this).addClass("on");
		}
	});	
	
	/*分享遮罩部分*/
	$(".share_show").click(function(){
		if($(".mask_box .share_mask").css("display")=="block"){
			$(".mask_box .share_mask").hide();
			$(".mask_box .share_mask").parent().find(".mask_bg").hide();
		}
		else{
			$(".mask_box .share_mask").show();
			$(".mask_box .share_mask").parent().find(".mask_bg").show();
		}
		
	});
	$(".mask_box .share_mask").parent().find(".mask_bg").click(function(){
		$(".mask_box .share_mask").hide();
		$(".mask_box .share_mask").parent().find(".mask_bg").hide();
	});
	
});