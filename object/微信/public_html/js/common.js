$(document).ready(function(){
//	定义rem
	var winW = $(window).width();
	var constant = winW/7.5;
	$('body,html').css({"font-size":constant});
	$(window).resize(function(){
		var winW = $(window).width();
		var constant = winW/7.5;
		$('body,html').css({"font-size":constant});
	});
	
//	导航栏下拉
		var bool=true;
		
		$(".nav_btn").click(function(){
			if(bool){
				$(".nav_list").slideDown(500,function () {
					bool=false;
				});
				
			}else{
				$(".nav_list").slideUp(500,function(){
						bool=true;
				});
			
				}
		})
		
//	弹出搜索页
$("#seainput").focus(function(){
		window.location = "search.html";
	})
$(".search img").click(function(){
		window.location = "search.html";
	})
//	弹出搜索页 end

//返回上一页

$("a.prev").click(function(){
	window.history.go(-1); 
	
})
//返回上一页 end
})