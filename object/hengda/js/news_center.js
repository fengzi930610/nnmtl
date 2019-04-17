// JavaScript Document
$(function(){
	
	$('.news_center .content .left .sidebar li:first-child').addClass('current');
	$('.news_center .content .right>div:first-child').addClass('current');
	
	$('.news_center .content .left .sidebar li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.news_center .content .left .sidebar li').eq(i).addClass('current');
			$('.news_center .content .left .sidebar li').not($('.news_center .content .left .sidebar li').eq(i)).removeClass('current');
			$('.news_center .content .right>div').eq(i).show(100);
			$('.news_center .content .right>div').not($('.news_center .content .right>div').eq(i)).hide(100);
			$('.news_center .content .right>div').eq(i).addClass('current');
			$('.news_center .content .right>div').not($('.news_center .content .right>div').eq(i)).removeClass('current');
		});
    });
	
	
	
	$('.max .foot .ct_us>li').eq(1).mouseover(function(){
	
		$('.max .foot>#chatpic').show(0);
	
	});
	$('.max .foot .ct_us>li').eq(1).mouseout(function(){
	
		$('.max .foot>#chatpic').hide(0);
	
	});
	
	
	$('.news_center .title #menu_click ul li:first-child').addClass('current');
	$('.news_center .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.news_center .title #menu_click ul li').eq(i).addClass('current');
			$('.news_center .title #menu_click ul li').not($('.news_center .title #menu_click ul li').eq(i)).removeClass('current');
			$('.news_center .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.news_center .title #menu_click .lv_2 ol').not($('.news_center .title #menu_click .lv_2 ol').eq(i)).hide(0);
		});
    });
	
	var pic=document.getElementById('pic');
	var menu=document.getElementById('menu_click');
	var bg=document.getElementById('btn');
	
	
	pic.onclick=function()
	{
		if(menu.style.display=='block')
		{
			menu.style.display='none';
			
		}
		else
		{
			menu.style.display='block';
		};
		
		if(bg.style.backgroundColor=='')
		{
			bg.style.backgroundColor='#131313';
		
		}
		else
		{
			bg.style.backgroundColor='';
		}
	
	};

	
	
});

$(function(){

	
		

	var b=0;
	
	
	$('.news_center .content .right>div .bottom .next').click(function(){
		b++;
		if(b>7){b=0};
		
		$('.news_center .content .right>.current .bottom .sub>div').eq(b).stop().show(200);
 		$('.news_center .content .right>.current .bottom .sub>div').not($('.news_center .content .right>.current .bottom .sub>div').eq(b)).stop().hide(200);
	});
	
	
	$('.news_center .content .right>div .bottom .prev').click(function(){
		b--;
		if(b<0){b=7};
		
		$('.news_center .content .right>.current .bottom .sub>div').eq(b).stop().show(200);
		$('.news_center .content .right>.current .bottom .sub>div').not($('.news_center .content .right>.current .bottom .sub>div').eq(b)).stop().hide(200);
	});
	
	
	$('.news_center .content .right>div .bottom .sub>div .text h1').eq(0).mouseover(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(0).addClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(0).addClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(0).addClass('line');
	});
	$(this).mouseout(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(0).removeClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(0).removeClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(0).removeClass('line');
	});
	
	$('.news_center .content .right>div .bottom .sub>div .text h1').eq(1).mouseover(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(1).addClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(1).addClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(1).addClass('line');
	});
	$(this).mouseout(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(1).removeClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(1).removeClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(1).removeClass('line');
	});
	
	$('.news_center .content .right>div .bottom .sub>div .text h1').eq(2).mouseover(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(2).addClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(2).addClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(2).addClass('line');
	});
	$(this).mouseout(function(){
		$('.news_center .content .right>div .bottom .sub>div .text h1').eq(2).removeClass('active');
		$('.news_center .content .right>div .bottom .sub>div .text time').eq(2).removeClass('show');
		$('.news_center .content .right>div .bottom .sub>div .text span').eq(2).removeClass('line');
	});




});














