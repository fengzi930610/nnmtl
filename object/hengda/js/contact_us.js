// JavaScript Document

$(function(){
	
	$('.contact_us .content .left .sidebar li').eq(0).addClass('current');

	$('.contact_us .content .left .sidebar li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.contact_us .content .left .sidebar li').eq(i).addClass('current');
			$('.contact_us .content .left .sidebar li').not($('.contact_us .content .left .sidebar li').eq(i)).removeClass('current');
			$('.contact_us .content .right>div').eq(i).show(0);
			$('.contact_us .content .right>div').not($('.contact_us .content .right>div').eq(i)).hide(0);
		});
    });
	
	$('.max .foot .ct_us li').eq(1).mouseover(function(){
	
		$('.max .foot #chatpic').show(0);
	
	});
	$('.max .foot .ct_us li').eq(1).mouseout(function(){
	
		$('.max .foot #chatpic').hide(0);
	
	});
	
	
	$('.contact_us .title #menu_click ul li:first-child').addClass('current');
	$('.contact_us .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.contact_us .title #menu_click ul li').eq(i).addClass('current');
			$('.contact_us .title #menu_click ul li').not($('.contact_us .title #menu_click ul li').eq(i)).removeClass('current');
			$('.contact_us .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.contact_us .title #menu_click .lv_2 ol').not($('.contact_us .title #menu_click .lv_2 ol').eq(i)).hide(0);
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

	$('.foot .ct_us li').eq(1).mouseover(function(){
	
		$('.foot #chatpic').show(0);
	
	});
	$('.foot .ct_us li').eq(1).mouseout(function(){
	
		$('.foot #chatpic').hide(0);
	
	});
	
	
});