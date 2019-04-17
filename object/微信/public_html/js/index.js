$(function(){
//	当浏览器窗口宽度发生变化的时候,刷新页面
	var winW = $(window).width();
      $(winW).resize(function(){
        location.reload();
      })


	var winW = $(window).width();
	liLen = $("#middle .banner .bd ul li").length;
	$("#middle .banner .bd ul").css("width",liLen*winW+"px");
	$("#middle .banner .bd ul li").width(winW);
	
	var index = 0;
	var bool = true;
	for(var i=0;i<liLen;i++){
		$(".bd ul li").eq(i).css("left",i*winW+"px")
	}
	
	
	$(".hd ul li").click(function(){
		clearlunbo();
		if(bool){
			bool = false;
			var onindex = $(".hd ul li.on").index();
			index = $(this).index();
			$(this).addClass("on").siblings().removeClass("on");
			
			if(index>onindex){
				for(var i=0;i<(index - onindex);i++){
					$(".bd ul li").eq(i).animate({"left":-winW+"px"},function(){
						$(this).css("left",winW+"px").appendTo(".bd ul");
						})
					}
					$(".bd ul li").eq(index-onindex).animate({"left":0},function(){bool=true;})
				for(var i=index-onindex+1;i<liLen;i++){
					$(".bd ul li").eq(i).animate({"left":(i-(index-onindex))*winW+"px"})
					}
				}
			}
			if(index==onindex){
				bool=true;
			}
			if(index<onindex){
				$(".bd ul li").eq(0).animate({"left":winW+"px"})
				for(var i=liLen-1;i>liLen-1-(onindex-index);i--){
					$(".bd ul li").eq(i).css("left",(i-liLen)*winW+"px").animate({"left":(i-liLen+(onindex-index))*winW+"px"},function(){
						$(this).prependTo(".bd ul");
						bool=true;	
						})
				}
					
			}
			
		
	})
	$(".banner").swipe({
		swipeLeft: function(){
			next();
			clearlunbo();
		},
		swipeRight: function(){
			prev();
			clearlunbo();
		},
	})
	$(".banner").hover(
		function(){clearInterval(lunbo)},
		function(){lunbo=setInterval(next,3000)}
	)
//	切换下一张
	function next(){
		if(bool){
			bool=false;
			index=$(".hd li.on").index();
			index++;
			if(index==liLen){
				index=0;	
			}
			$(".hd li").eq(index).addClass("on").siblings().removeClass("on");
			for(var i=1;i<liLen;i++){
				$(".bd ul li").eq(i).animate({"left":(i-1)*winW+"px"})
			}	
			
			$(".bd ul li").eq(0).animate({"left":-winW+"px"},function(){
				$(this).appendTo(".bd ul").css("left",(liLen-1)*winW+"px");	
				bool=true;
			})

		}
	}
	//	切换下一张
	
	//	切换上一张

	function prev(){

		if(bool){
			bool=false;
			index=$(".hd li.on").index();
			index--;
		
		if(index<0){
			index=liLen-1;
			}
			
			$(".hd li").eq(index).addClass("on").siblings().removeClass("on");
			for(var i=0;i<liLen-1;i++){
				$(".bd ul li").eq(i).animate({"left":(i+1)*winW+"px"})	
			}	
			$(".bd ul li").eq(-1).css("left",-winW+"px").animate({"left":0},function(){
				$(this).prependTo(".bd ul")	
				bool=true;
			})	
		}
	}
//	切换上一张
	
	
		/*自动轮播*/
	lunbo=setInterval(next,3000)
	
//	定义一个清除轮播的函数
	function clearlunbo(){
		clearInterval(lunbo);
		setTimeout(function(){
			lunbo=setInterval(next,3000)
		},1)
	}
})
