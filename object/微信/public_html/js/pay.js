$(function(){
	radiu = $(".pay ul li img:last-child");
	radiu.click(function(){
		for(var i=0;i<radiu.length;i++){
			radiu.eq(i).attr("src","images/pay_radius.png")
		}
		$(this).attr("src","images/pay_radius_c.png")
	})
	li = $(".pay ul li");
	li.click(function(){
		index = $(this).index();
		for(var i=0;i<li.length;i++){
			radiu.eq(i).attr("src","images/pay_radius.png")
		}
		radiu.eq(index).attr("src","images/pay_radius_c.png")
	})
})
function paycon(){
		ss = confirm("是否支付");
		console.log(ss)
		if(ss){
			window.location.href = "pay_success.html";
		}else{
			window.location.href = "pay_failure.html";
			
		}

	}