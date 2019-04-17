// JavaScript Document
$(document).ready(function(){
	/*var big_wid=parseInt($('#big').width());
    $('#pic').css({'width':big_wid})
	var big_li_len=$('#big>ul>li').length;
	var big_li_wid=$('#big>ul>li').width();
	$('#big>ul').css({'width':big_li_len*big_li_wid+'px'});
	var small_li_len=$('#small>ol>li').length;
	var small_li_wid=$('#small>ol>li').width();
	var small_li_width=small_li_wid+20;
	$('#small>ol').css({'width':small_li_len*small_li_width+'px'});
	$('#big>ul>li a img').css({'width':'100%','height':'100%'});
	$('#small>ol>li a img').css({'width':'100%','height':'100%'});*/
	var smalllist=$('#small>ol>li');
	var biglist=$('#big>ul>li');
	$('#small>ol>li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			smalllist.eq(i).addClass('current');
			smalllist.not(smalllist.eq(i)).removeClass('current');
			biglist.eq(i).fadeIn();
			biglist.not(biglist.eq(i)).fadeOut();
		});
    });
	
});


$(function(){
	
	$('.product_details .content .left .sidebar li:first-child').addClass('current');
	$('.product_details .content .left .sidebar li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.product_details .content .left .sidebar li').eq(i).addClass('current');
			$('.product_details .content .left .sidebar li').not($('.product_details .content .left .sidebar li').eq(i)).removeClass('current');
			$('.product_details .content .right>div').eq(i).show(200);
			$('.product_details .content .right>div').not($('.product_details .content .right>div').eq(i)).hide(200);
		});
    });	
	
	
	
	var pic=document.getElementById('img');
	var menu=document.getElementById('menu_click');
	var bg=document.getElementById('btn');
	pic.onclick=function(){
	
		if(menu.style.display=='block'){
		
			menu.style.display='none';
			
		}else{
		
			menu.style.display='block';
		};
		
		if(bg.style.backgroundColor==''){
		
			bg.style.backgroundColor='#131313';
		
		}else{
		
			bg.style.backgroundColor='';
		}
	
	};
	$('.product_details .title #menu_click ul li:first-child').addClass('current');
	$('.product_details .title #menu_click ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.product_details .title #menu_click ul li').eq(i).addClass('current');
			$('.product_details .title #menu_click ul li').not($('.product_details .title #menu_click ul li').eq(i)).removeClass('current');
			$('.product_details .title #menu_click .lv_2 ol').eq(i).show(0);
			$('.product_details .title #menu_click .lv_2 ol').not($('.product_details .title #menu_click .lv_2 ol').eq(i)).hide(0);
		});
    });

	$('.foot .ct_us li').eq(1).mouseover(function(){
	
		$('.foot #chatpic').show(0);
	
	});
	$('.foot .ct_us li').eq(1).mouseout(function(){
	
		$('.foot #chatpic').hide(0);
	
	});
	
	
});