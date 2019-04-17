$(function(){
//	当浏览器窗口宽度发生变化的时候,刷新页面
	var winW = $(window).width();
      $(winW).resize(function(){
        location.reload();
      })

//轮播
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
		},
		swipeRight: function(){
			prev();
		},
	})
	
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
	
	
//轮播 end
	
//	选项卡

	var pageli = $(".page ul li");
	var product =  $(".product");
	pageli.each(function(i){
		$(this).click(function(){
			index = $(this).index();
			pageli.eq(index).addClass("on").siblings().removeClass("on");
			product.eq(index).addClass("active").siblings().removeClass("active")
		})
	})
//	选项卡 end
	

})
	function setproduct(){
		var imgSrc = [
			"images/bluetooth_headset_01.png","images/bluetooth_headset_02.png",
			"images/bluetooth_headset_03.png","images/bluetoorh_headset_04.png",
			"images/bluetooth_speaker_01.png","images/bluetooth_speaker_02.png",
			"images/bluetooth_speaker_03.png","images/bluetooth_speaker_04.png",
			"images/hot_explosion_01.png","images/hot_explosion_02.png",
			"images/hot_explosion_03.png","images/hot_explosion_04.png"
		]
		var nameArr = [
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机",
			"爱度A3可翻转头戴式蓝牙耳机"
		]
		var id = parseInt(Math.random()*12);
		var img = imgSrc[id];
		var num = 1
		var price = $("#middle .price span:first-child").text().slice(1);
		var product = {
					imgSrc:img,
					name:nameArr[id],
					num:num,
					price:price,
					id:id,
					totalPrice:(price*num).toFixed(2),
					
				};
			addproduct(product);
			
	}
	function goorderss(){
		
		user = localStorage.getItem("user");
		if(!user){
			
			alert("请先登录");
			window.location.href = "login.html";
			return;
		}
		window.location.href="comfirm_order.html"
	}