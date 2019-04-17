// JavaScript Document

		$(document).ready(function(){
			var winW = $(window).width();
			var liLen = $(".index_banner .bd li").length;
			$(".index_banner .bd").css({"width":winW*liLen+"px"});
			$(".index_banner .bd li").width(winW+"px");
			var a = 0;
			$(".index_banner .hd li").eq(0).addClass("on");

			$(".index_banner .next").click(function(){
				a = $(".index_banner .hd li.on").index();
				a +=1;
				if(a>liLen-1){
					a = 0;
				}
				$(".index_banner .bd").animate({"left":-winW*a+"px"},700);
				$(".index_banner .hd li").removeClass("on").eq(a).addClass("on");
			})

			$(".index_banner .prev").click(function(){
				a = $(".index_banner .hd li.on").index();
				a -=1;
				if(a<0){
					a = liLen-1;
				}
				$(".index_banner .bd").animate({"left":-winW*a+"px"},700);
				$(".index_banner .hd li").removeClass("on").eq(a).addClass("on");
			})

			$(".index_banner .hd li").click(function(){
				a = $(this).index();
				$(".index_banner .bd").animate({"left":-winW*a+"px"},700);
				$(".index_banner .hd li").removeClass("on").eq(a).addClass("on");
			})

			$(".index_banner").mouseover(function(){
				clearInterval(lun);
			})
			$(".index_banner").mouseout(function(){
				lun = setInterval(show,3000);
			})
			function show(){
				a = a + 1;
				if(a>liLen-1){
					a = 0;
				}
				$(".index_banner .bd").animate({"left":-winW*a+"px"},700);
				$(".index_banner .hd li").removeClass("on").eq(a).addClass("on");
			}
			var lun = setInterval(show,3000);
		})
