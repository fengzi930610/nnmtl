// JavaScript Document
$(function(){
	
	
	$('.max .page li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.max .page li').eq(i).addClass('current');
			$('.max .page li').not($('.max .page li').eq(i)).removeClass('current');
			$('.max .content>div').eq(i).show(0);
			$('.max .content>div').not($('.max .content>div').eq(i)).hide(0);
		});
    });


	var list_li_w=$('.index .banner>ul li').width();
	var list_li_len=$('.index .banner>ul li').length;
	var list_ul_w=list_li_w*list_li_len;
	
	$('.index .banner>ul').css({'width':list_ul_w});
	$('.index .banner').append('<ol></ol>');
	
	var p=0;
	for(p=0;p<list_li_len;p++){
		$('.index .banner>ol').append('<li></li>');
	}
	
	$('.index .banner>ol li:first-child').addClass('current');
	
	var k=0;
	
	$('.index .banner>ol li').each(function(){
		var i=$(this).index();
        $(this).click(function(){
			$('.index .banner>ol li').eq(i).addClass('current');
			$('.index .banner>ol li').not($('.index .banner>ol li').eq(i)).removeClass('current');
			$('.index .banner>ul li').eq(i).show(0);
			$('.index .banner>ul li').not($('.index .banner>ul li').eq(i)).hide(0);
			k=i;
		});
    });
	
	var set=setInterval(function(){
		k++;
		if(k>2){k=0};
		
		$('.index .banner ol li').eq(k).addClass('current');
		$('.index .banner ol li').not($('.index .banner ol li').eq(k)).removeClass('current');
		$('.index .banner ul li').eq(k).stop().show(0);
		$('.index .banner ul li').not($('.index .banner ul li').eq(k)).stop().hide(0);	
		
	},1000);
	
	
	$('.index .banner ul li').each(function(){
        $(this).mouseover(function(){
			clearInterval(set);
		});
    });
	
	
	$('.index .banner ul li').each(function(){
        $(this).mouseout(function(){
			set=setInterval(function(){
				k++;
				if(k>2){k=0};
				$('.index .banner ol li').eq(k).addClass('current');
				$('.index .banner ol li').not($('.index .banner ol li').eq(k)).removeClass('current');
				$('.index .banner ul li').eq(k).stop().show(0);
				$('.index .banner ul li').not($('.index .banner ul li').eq(k)).stop().hide(0);	
			},3000);
		});
    });
	
	$('.max .center .page li:first-child').addClass('current');
	
	
	$('.max .content .news .container .bottom>div .text>h1').eq(0).mouseover(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(0).addClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(0).addClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(0).addClass('line');
	});
	$(this).mouseout(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(0).removeClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(0).removeClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(0).removeClass('line');
	});
	
	$('.max .content .news .container .bottom>div .text>h1').eq(1).mouseover(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(1).addClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(1).addClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(1).addClass('line');
	});
	$(this).mouseout(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(1).removeClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(1).removeClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(1).removeClass('line');
	});
	
	$('.max .content .news .container .bottom>div .text>h1').eq(2).mouseover(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(2).addClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(2).addClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(2).addClass('line');
	});
	$(this).mouseout(function(){
		$('.max .content .news .container .bottom>div .text>h1').eq(2).removeClass('active');
		$('.max .content .news .container .bottom>div .text>time').eq(2).removeClass('show');
		$('.max .content .news .container .bottom>div .text>span').eq(2).removeClass('line');
	});
   
	
	
});




$(function(){
	
	$('.max .content .about .container .guide>span:first-child').addClass('current');
	
	$('.max .content .about .container .guide>span').each(function()
	{	
		var i=$(this).index();
        $(this).click(function()
		{
			$('.max .content .about .container .guide>span').eq(i).addClass('current');
			$('.max .content .about .container .guide>span').not($('.max .content .about .container .guide>span').eq(i)).removeClass('current');
			$('.max .content .about .text>div').eq(i).show(200);
			$('.max .content .about .text>div').not($('.max .content .about .text>div').eq(i)).hide(200);
		});
    });
	
	
	
	
});

$(document).ready(function() {
		
	$('.max>.title>span img').click( function () {
			
			
		$('.max>.title>div').slideToggle(0);
		
		
	});
});



window.onload=function()
{
	
	
	
	
	
	
	var list_li_len=$('.max .content .news .container .top .menu li').length;
	
	var k=0;
	
	
	$('.max .content .news .container .top .menu li').each(function(){
		
		var i=$(this).index();
		
        $(this).click(function(){
			$('.max .content .news .container .top .menu li').eq(i).addClass('current');
		
			$('.max .content .news .container .top .menu li').not($('.max .content .news .container .top .menu li').eq(i)).removeClass('current');
			$('.max .content .news .container .bottom>div').eq(i).show(0);
			$('.max .content .news .container .bottom>div').not($('.max .content .news .container .bottom>div').eq(i)).hide(0);
			k=i;
		});
    });
	
	$('.max .content .news .container .top ul li.next').click(function(){
		k++;
		if(k>list_li_len-1){k=0};
		$('.max .content .news .container .top .menu li').eq(k).addClass('current');
		$('.max .content .news .container .top .menu li').not($('.max .content .news .container .top .menu li').eq(k)).removeClass('current');
		$('.max .content .news .container .bottom>div').eq(k).stop().show(0);
		$('.max .content .news .container .bottom>div').not($('.max .content .news .container .bottom>div').eq(k)).stop().hide(0);
	});
	
	
	$('.max .content .news .container .top ul li.prev').click(function(){
		k--;
		if(k<0){k=list_li_len-1};
		$('.max .content .news .container .top .menu li').eq(k).addClass('current');
		$('.max .content .news .container .top .menu li').not($('.max .content .news .container .top .menu li').eq(k)).removeClass('current');
		$('.max .content .news .container .bottom>div').eq(k).stop().show(200);
		$('.max .content .news .container .bottom>div').not($('.max .content .news .container .bottom>div').eq(k)).stop().hide(200);
	});
	
	
	
	
	
	var list_li_len=$('.max .content .product .container .top .menu li').length;
	
	var k=0;
	
	
	$('.max .content .product .container .top .menu li').each(function(){
		
		var i=$(this).index();
		
        $(this).click(function(){
			$('.max .content .product .container .top .menu li').eq(i).addClass('current');
		
			$('.max .content .product .container .top .menu li').not($('.max .content .product .container .top .menu li').eq(i)).removeClass('current');
			$('.max .content .product .container .bottom>div').eq(i).show(0);
			$('.max .content .product .container .bottom>div').not($('.max .content .product .container .bottom>div').eq(i)).hide(0);
			k=i;
		});
    });
	
	$('.max .content .product .container .top ul li.next').click(function(){
		k++;
		if(k>list_li_len-1){k=0};
		$('.max .content .product .container .top .menu li').eq(k).addClass('current');
		$('.max .content .product .container .top .menu li').not($('.max .content .product .container .top .menu li').eq(k)).removeClass('current');
		$('.max .content .product .container .bottom>div').eq(k).stop().show(0);
		$('.max .content .product .container .bottom>div').not($('.max .content .product .container .bottom>div').eq(k)).stop().hide(0);
	});
	
	
	$('.max .content .product .container .top ul li.prev').click(function(){
		k--;
		if(k<0){k=list_li_len-1};
		$('.max .content .product .container .top .menu li').eq(k).addClass('current');
		$('.max .content .product .container .top .menu li').not($('.max .content .product .container .top .menu li').eq(k)).removeClass('current');
		$('.max .content .product .container .bottom>div').eq(k).stop().show(0);
		$('.max .content .product .container .bottom>div').not($('.max .content .product .container .bottom>div').eq(k)).stop().hide(0);
	});
	
	
	
	
	$('.max .title>div ul li:first-child').addClass('current');
	$('.max .title>div ul li').each(function(){
		var i=$(this).index();
        $(this).mouseover(function(){
			$('.max .title>div ul li').eq(i).addClass('current');
			$('.max .title>div ul li').not($('.max .title>div ul li').eq(i)).removeClass('current');
			$('.max .title>div div ol').eq(i).show(0);
			$('.max .title>div div ol').not($('.max .title>div div ol').eq(i)).hide(0);
		});
    });
	
	
	
	$('.max .foot .ct_us li').eq(1).mouseover(function(){
	
		$('.max .foot #chatpic').show(0);
	
	});
	$('.max .foot .ct_us li').eq(1).mouseout(function(){
	
		$('.max .foot #chatpic').hide(0);
	
	});
	
	
	
};