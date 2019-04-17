$(function(){
	liL = $(".page ul li");
	pro_list = $(".pro_list");
	liL.click(function(){
		index = $(this).index();
		for (var i=0;i<liL.length;i++) {
			liL.eq(i).removeClass("on");
		}
		liL.eq(index).addClass("on");
		for (var i=0;i<pro_list.length;i++) {
			pro_list.eq(i).removeClass("active");
		}
		pro_list.eq(index).addClass("active");
	})
//	a = $("#middle .active .delete");
//	ul = $("#middle .active ul");
//	li = $("#middle .active ul li");
//	a.click(function(){
//		index = $(this).index();
//		console.log(index)
//		for (var i=0;i<a.length;i++) {
//			
//		}
//	})
	a = $("#middle .active .delete");
	for (var i=0;i<a.length;i++) {
			a[i].onclick = function(){
				$(this).parent().remove();
			}
		}
})
//	删除商品	
	

