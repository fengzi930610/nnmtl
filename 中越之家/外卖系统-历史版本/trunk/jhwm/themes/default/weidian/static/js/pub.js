//
$(function(){

	//无头部
	if ($('header').length == 0)
	{
		$('.page_center_box').css('padding-top', 0);
	}

	//无底部
	if ($('footer').length == 0)
	{
		// $('.page_center_box').css('padding-bottom', 0);
	}

	//头部筛选
	if ($('.topShaixuan').length > 0)
	{
		$('.page_center_box').css('padding-top', '0.4rem');
		$('.topShaixuan .list').width(100 / $('.topShaixuan .list').length + '%');
	}
	if ($('.topShaixuan').length > 0 && $('header').length > 0)
	{
		$('.page_center_box').css('padding-top', '0.9rem');
	}

	//头部提示
	if ($('.cantuan_topts').length > 0 && $('.topShaixuan').length > 0)
	{
		$('.page_center_box').css('padding-top', '0.8rem');
		$('.topShaixuan').css('top', '0.4rem');
	}

	//底部导航
	if ($('footer .list').length > 0)
	{
		$('footer .list').width(100 / $('footer .list').length + '%');
	}

});

//回到顶部
function backtop(){
	$('html,body').animate({'scrollTop':0},600);
}
$(window).scroll(function(){
	if($(window).scrollTop() > 150) {
		$('.backtop').show();
	} else {
		$('.backtop').hide();
	}
});

