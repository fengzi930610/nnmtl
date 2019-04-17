// JavaScript Document
$(function(){
	
	$('.product_center .content .left .sidebar li:first-child').addClass('current');
	
	$('.product_center .content .left .sidebar li').each(function()
	{
		var i=$(this).index();
		
        $(this).click(function(){
			$('.product_center .content .left .sidebar li').eq(i).addClass('current');
			$('.product_center .content .left .sidebar li').not($('.product_center .content .left .sidebar li').eq(i)).removeClass('current');
			$('.product_center .content .right>div').eq(i).show(200);
			$('.product_center .content .right>div').not($('.product_center .content .right>div').eq(i)).hide(200);
			
			$('.product_center .content .right>div').eq(i).addClass('current');
			$('.product_center .content .right>div').not($('.product_center .content .right>div').eq(i)).removeClass('current');

		});
		
    });
	
	$('.max .foot .ct_us li').eq(1).mouseover(function(){
	
		$('.max .foot #chatpic').show(0);
	
	});
	$('.max .foot .ct_us li').eq(1).mouseout(function(){
	
		$('.max .foot #chatpic').hide(0);
	
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
	
	$('.product_center .title #menu_click ul li:first-child').addClass('current');
	$('.product_center .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.product_center .title #menu_click ul li').eq(i).addClass('current');
			$('.product_center .title #menu_click ul li').not($('.product_center .title #menu_click ul li').eq(i)).removeClass('current');
			$('.product_center .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.product_center .title #menu_click .lv_2 ol').not($('.product_center .title #menu_click .lv_2 ol').eq(i)).hide(0);
		});
    });
	
	
	
	var b=0;
	
	
	$('.product_center .content .right>div .bottom .next').click(function(){
		b++;
		if(b>3){b=0};
		
		$('.product_center .content .right>.current .bottom .text ul').eq(b).stop().show(100);
 		$('.product_center .content .right>.current .bottom .text ul').not($('.product_center .content .right>.current .bottom .text ul').eq(b)).stop().hide(100);
	});
	
	
	$('.product_center .content .right>div .bottom .prev').click(function(){
		b--;
		if(b<0){b=3};
		
		$('.product_center .content .right>.current .bottom .text ul').eq(b).stop().show(100);
		$('.product_center .content .right>.current .bottom .text ul').not($('.product_center .content .right>.current .bottom .text ul').eq(b)).stop().hide(100);
	});

	
});