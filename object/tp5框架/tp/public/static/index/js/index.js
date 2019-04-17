		$(function(){
				var li_height = $(".switchover ul li").outerHeight(true),
					len = $('.switchover ul li').length,
					bool = true,
					speed = 3000 ,
					sum =  li_height*len,
					time;
					$(".switchover ul").height(li_height*len)
				$('.switchover ul').height(sum)
				time = setInterval(_,speed)
				$('.switchover').mouseover(function(){
					clearInterval(time)
				})
				$('.switchover').mouseout(function(){
					time = setInterval(_,speed)
				})
				$('.next').click(function(){
					_()
				})
				$('.prev').click(function(){
					if(bool){
						bool = false;
						$('.switchover ul').find('li').eq(0).before($('.switchover ul').find('li').eq(len-1))
						$('.switchover ul').css("margin-top",-li_height+"px")
						$('.switchover ul').animate({marginTop:"0px"},1000,function(){
							bool = true
						})
					}else{
						return ;
					}
					
				})
				function _(){
					if(bool){
						bool = false
						$('.switchover ul').animate({marginTop:-li_height+"px"},1000,function(){
							$(this).find('li').eq(len-1).after($(this).find('li').eq(0))
							$(this).css("margin-top","0")
							bool = true;
						})
					}else{
						return ;
					}
				}
			})