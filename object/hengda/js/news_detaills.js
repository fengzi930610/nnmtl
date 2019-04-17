// JavaScript Document
window.onload=function ()
{
	$('.foot .ct_us>li').eq(1).mouseover(function(){
	
		$('.foot>#chatpic').show(0);
	
	});
	$('.foot .ct_us>li').eq(1).mouseout(function(){
	
		$('.foot>#chatpic').hide(0);
	
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
	
	$('.news_detaills .title #menu_click ul li:first-child').addClass('current');
	
	$('.news_detaills .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.news_detaills .title #menu_click ul li').eq(i).addClass('current');
			$('.news_detaills .title #menu_click ul li').not($('.news_detaills .title #menu_click ul li').eq(i)).removeClass('current');
			$('.news_detaills .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.news_detaills .title #menu_click .lv_2 ol').not($('.news_detaills .title #menu_click .lv_2 ol').eq(i)).hide(0);			
		});
    });

	
	
	
	$('.news_detaills .content .left .sidebar li:first-child').addClass('current');
	$('.news_detaills .content .right>div:first-child').addClass('current');
	
	$('.news_detaills .content .left .sidebar li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.news_detaills .content .left .sidebar li').eq(i).addClass('current');
			$('.news_detaills .content .left .sidebar li').not($('.news_detaills .content .left .sidebar li').eq(i)).removeClass('current');
			$('.news_detaills .content .right>div').eq(i).show(100);
			$('.news_detaills .content .right>div').not($('.news_detaills .content .right>div').eq(i)).hide(100);
			
			$('.news_detaills .content .right>div').eq(i).addClass('current');
			$('.news_detaills .content .right>div').not($('.news_detaills .content .right>div').eq(i)).removeClass('current');
		});
    });
	
	
	var b=0;
	
	
	$('.news_detaills .content .right>div .bottom .next').click(function(){
		b++;
		if(b>2){b=0};
		
		$('.news_detaills .content .right>.current .bottom .sub>div').eq(b).stop().show(200);
 		$('.news_detaills .content .right>.current .bottom .sub>div').not($('.news_detaills .content .right>.current .bottom .sub>div').eq(b)).stop().hide(200);
	});
	
	
	$('.news_detaills .content .right>div .bottom .prev').click(function(){
		b--;
		if(b<0){b=2};
		
		$('.news_detaills .content .right>.current .bottom .sub>div').eq(b).stop().show(200);
		$('.news_detaills .content .right>.current .bottom .sub>div').not($('.news_detaills .content .right>.current .bottom .sub>div').eq(b)).stop().hide(200);
	});
	
	
	
	
};