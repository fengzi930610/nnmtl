$(function(){
	//本地存储
	
	
	//******************************************************************/
	//图片选项卡
	var proPic = document.getElementById('pro-pic')
	var li = proPic.getElementsByTagName("li")
	for(var i = 0;i<li.length;i++){
		
		li[i].onclick=function(){
			
			var src = this.getElementsByTagName('img')[0].getAttribute('src')
			document.getElementById('glass').setAttribute('src',src)
			var child = this.parentNode.children
			for(var x = 0;x<child.length;x++){
				child[x].className = " "
				console.log(child[x])
			}
			this.className = "active"
		}
	}
	
	
	
	
	//轮播
	var bool=true;
		li_width = $(".left-bot ul li").outerWidth(true),
		len = $(".left-bot ul li").length,
		speed = 3000;
	 var time;
	 $(".left-bot ul").width(li_width*len)
	time = setInterval(_,speed)
	$('.left-bot').mouseover(function(){
		clearInterval(time)
	})
	$('.left-bot').mouseout(function(){
		time = setInterval(_,speed)
	})
	//prev
	$(".prev").click(function(){
		if(bool){
			bool=false;
			$('.left-bot ul').find('li').eq(0).before($('.left-bot ul').find('li').eq(len-1))
			$(".left-bot ul").css("margin-left",-li_width+"px")
			$(".left-bot ul").animate({marginLeft:-0+"px"},700,function(){
				bool=true
			})
			
		}else{
			return
		}
	})
	//next
	$(".next").click(function(){
		_()
//							
	})
	function _(){
		if(bool){
			bool=false;
			$(".left-bot ul").animate({marginLeft:-li_width+"px"},700,function(){
				$('.left-bot ul').find('li').eq(len-1).after($('.left-bot ul').find('li').eq(0).css({"righ":-li_width+"px"}))
				$(".left-bot ul").css("margin-left","0")
			})
			bool=true
		}else{
			return
		}
	}
	//身高选择
	$(".stature ul li").click(function(){
		$(this).addClass("on")
		$(this).siblings("li").removeClass("on")
	})
	//按钮选择
	$(".right-bot a").click(function(){
		$(this).addClass("default")
		$(this).siblings("a").removeClass("default")
	})
	
	//选项卡
	var top_li = document.querySelectorAll('.box-top ul li'),
		bot_ul = document.querySelectorAll('.box-bottom ul'),
		last = top_li[0];
		for( var i = 0;i<top_li.length;i++){
			
			top_li[i].index = i;
			top_li[i].onclick=function(){
				$(this).addClass('present')
				$(this).siblings('li').removeClass('present')
				bot_ul[this.index].style.display='block'
				bot_ul[last.index].style.display='none'
				last=this
			}
		}
		
	//.box-bottom .last li 单选
	$(".box-bottom .last li label").click(function(){
		$(this).find("input").prop("checked","checked")
		$(this).addClass("on")
		$(this).siblings("label").removeClass("on")
		$(this).siblings("label").find("input").removeAttr("checked")
	})
})



$(function(){
	var src;
	$(".conter .top .left .magni_fier").html("<img />");
	$(".conter .top .left .left-top li").mouseover(function(){
		src = $(this).find("img").attr("src");
		$(".conter .top .left .magni_fier img").attr("src",src).css({"position":"absolute"})
	})
	$(".conter .top .left .left-top").mouseover(function(){
		$(".conter .top .left .magni_fier").stop(true,true).fadeIn(300)
		$(".small_box").stop(true,true).fadeIn(300)
	})
	$(".conter .top .left .left-top").mouseout(function(){
		$(".conter .top .left .magni_fier").stop(true,true).fadeOut(300)
		$(".small_box").stop(true,true).fadeOut(300)
	})
	var big_boxLeft = $(".conter .top .left .left-top li").offset().left;
	var big_boxTop = $(".conter .top .left .left-top li").offset().top;
	var small_boxWidth = $(".small_box").innerWidth();
	var small_boxHeight = $(".small_box").innerHeight();
	$(".conter .top .left .left-top").mousemove(function(e){
		var x = e.pageX;
		var y = e.pageY;
		var fierLeft = x-big_boxLeft-small_boxWidth/2;
		var fierTop = y-big_boxTop-small_boxHeight/2;
		if(fierLeft<0){
			fierLeft = 0;
		}else if(fierLeft>($(".conter .top .left .left-top").innerWidth()-$(".small_box").innerWidth())){
			fierLeft = $(".conter .top .left .left-top").innerWidth()-$(".small_box").innerWidth();
		}
		if(fierTop<0){
			fierTop = 0;
		}else if(fierTop>($(".conter .top .left .left-top").innerHeight()-$(".small_box").innerHeight())){
			fierTop = $(".conter .top .left .left-top").innerHeight()-$(".small_box").innerHeight();
		}
		$(".small_box").css({"left":fierLeft+"px","top":fierTop+"px"});
		$(".conter .top .left .magni_fier img").css({"left":-fierLeft+"px","top":-fierTop+"px"})
		
	})
	
	//color 选择
	$(".color a").click(function(){
		$(this).addClass("color-active")
		$(this).siblings("a").removeClass("color-active")
	})
	
	//加减
	
	//加
	$(".prd_addNum").click(function(){
	var num = parseInt($(this).parents().siblings(".prd_num").val());
	++num;
	if(num>99){
		num=99;
	}
	$(this).parents().siblings(".prd_num").val(num);
	})
	//减
	$(".prd_subNum").click(function(){
	var num = parseInt($(this).parent().siblings(".prd_num").val());
	--num;
	if(num<1){
		num=1;
	}
	$(this).parent().siblings(".prd_num").val(num);
	})

	var input_val ;
	$(".prd_num").focus(function(){
		input_val = $(this).val();
	})
	$(".prd_num").change(function(){
		var numA = $(this).val();
		if(isNaN(numA)){
			numA=input_val;
		}else if(numA>99){
			numA=input_val;
		}else if(numA<1){
			numA=input_val;
		}
		numA = Math.round(numA);
		$(this).val(numA);

	})
})