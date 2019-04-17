$(function(){
	
	
	hdList = $(".hdList");
	hdW = $(".hd").width();
	hdL =  $(".hd").length;
	hdList.css("width",hdW*hdL+"px");
	ld = $(".ld ul li");
	var index = 0;
	var bool = true;
	for(var i=0;i<hdL;i++){
		$(".hd").eq(i).css("left",i*hdW+"px")
	}
	
	
	$(".ld ul li").click(function(){
		if(bool){
			bool = false;
			var onindex = $(".ld ul li.on").index();
			index = $(this).index();
			$(this).addClass("on").siblings().removeClass("on");
			
			if(index>onindex){
				for(var i=0;i<(index - onindex);i++){
					$(".hd").eq(i).animate({"left":-hdW+"px"},function(){
						$(this).css("left",hdW+"px").appendTo(".hdList");
						})
					}
					$(".hd").eq(index-onindex).animate({"left":0},function(){bool=true;})
				for(var i=index-onindex+1;i<hdL;i++){
					$(".hd").eq(i).animate({"left":(i-(index-onindex))*hdW+"px"})
					}
				}
			}
			if(index==onindex){
				bool=true;
			}
			if(index<onindex){
				$(".hd").eq(0).animate({"left":hdW+"px"})
				for(var i=hdL-1;i>hdL-1-(onindex-index);i--){
					$(".hd").eq(i).css("left",(i-hdL)*hdW+"px").animate({"left":(i-hdL+(onindex-index))*hdW+"px"},function(){
						$(this).prependTo(".hdList");
						bool=true;	
						})
				}
					
			}
			
		
	})
	
	
	//	切换下一张
	function next(){
		if(bool){
			bool=false;
			index=$(".ld ul li.on").index();
			index++;
			if(index==hdL){
				index=0;	
			}
			$(".ld ul li").eq(index).addClass("on").siblings().removeClass("on");
			for(var i=1;i<hdL;i++){
				$(".hd").eq(i).animate({"left":(i-1)*hdW+"px"})
			}	
			
			$(".hd").eq(0).animate({"left":-hdW+"px"},function(){
				$(this).appendTo(".hdList").css("left",(hdL-1)*hdW+"px");	
				bool=true;
			})

		}
	}
	//	切换下一张
	
	//	切换上一张

	function prev(){

		if(bool){
			bool=false;
			index=$(".ld ul li.on").index();
			index--;
		
		if(index<0){
			index=hdL-1;
			}
			
			$(".ld ul li").eq(index).addClass("on").siblings().removeClass("on");
			for(var i=0;i<hdL-1;i++){
				$(".hd").eq(i).animate({"left":(i+1)*hdW+"px"})	
			}	
			$(".hd").eq(-1).css("left",-hdW+"px").animate({"left":0},function(){
				$(this).prependTo(".hdList")	
				bool=true;
			})	
		}
	}

	$(".hot_word").swipe({
		swipeLeft: function(){
			next();
		},
		swipeRight: function(){
			prev();
		},
	})
	
	
})
window.onload = function()
{
    document.getElementById("search").focus();
}