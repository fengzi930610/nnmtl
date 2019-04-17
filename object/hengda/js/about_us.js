 //JavaScript Document
window.onload=function(){
	
	$('.about_us .content .left .sidebar li:first-child').addClass('current');
	$('.about_us .content .left .sidebar li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.about_us .content .left .sidebar li').eq(i).addClass('current');
			$('.about_us .content .left .sidebar li').not($('.about_us .content .left .sidebar li').eq(i)).removeClass('current');
			$('.about_us .content .right>div').eq(i).show(100);
			$('.about_us .content .right>div').not($('.content .right>div').eq(i)).hide(100);
			
			$('.about_us .content .right>div').eq(i).addClass('current');
			$('.about_us .content .right>div').not($('.about_us .content .right>div').eq(i)).removeClass('current');

		});
    });
	
	
	
	$('.about_us .title #menu_click ul li:first-child').addClass('current');
	$('.about_us .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.about_us .title #menu_click ul li').eq(i).addClass('current');
			$('.about_us .title #menu_click ul li').not($('.about_us .title #menu_click ul li').eq(i)).removeClass('current');
			$('.about_us .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.about_us .title #menu_click .lv_2 ol').not($('.about_us .title #menu_click .lv_2 ol').eq(i)).hide(0);
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
	
	
	
	$('.about_us .content .right>div:first-child').addClass('current');
	
	var b=0;
	
	
	$('.about_us .content .right>div .text .next').click(function(){
		b++;
		if(b>2){b=0};
		
		$('.about_us .content .right>div .text .container>div').eq(b).stop().show(100);
 		$('.about_us .content .right>div .text .container>div').not($('.about_us .content .right>div .text .container>div').eq(b)).stop().hide(100);
	});
	
	
	$('.about_us .content .right>div .text .prev').click(function(){
		b--;
		if(b<0){b=2};
		
		$('.about_us .content .right>div .text .container>div').eq(b).stop().show(100);
		$('.about_us .content .right>div .text .container>div').not($('.about_us .content .right>div .text .container>div').eq(b)).stop().hide(100);
	});
	
	$('.foot .ct_us li').eq(1).mouseover(function(){
	
		$('.foot #chatpic').show(0);
		$(this).style.backgroundColor="#181818";
	
	});
	$('.foot .ct_us li').eq(1).mouseout(function(){
	
		$('.foot #chatpic').hide(0);
	
	});
	
};

