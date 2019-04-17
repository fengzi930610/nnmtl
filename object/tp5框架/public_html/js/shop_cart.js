$(function(){
	
    //
    var obj = localStorage.getItem("proList");					
	var proList = JSON.parse(obj);
	
   var storage = window.localStorage;
	/*********************************************************************/
	//选项卡
	var title_li = document.querySelectorAll(".shop-title .left ul li");
	var shop_ul = document.querySelectorAll(".shop_box ul");
	var last = title_li[0];
	for(var i=0;i<title_li.length;i++){
		
			title_li[i].index = i;
			
		title_li[i].onclick=function(){
			$(this).addClass("on")
			$(this).siblings("li").removeClass("on")
			shop_ul[this.index].style.display="block"
			console.log(333)
			shop_ul[last.index].style.display="none"
			console.log(5555)
			last=this
		}
	}
	
	
	//购物车
	var cont=function(){
	var len1 = $(".shop_box tbody>tr>td input[type=checkbox]").length;
	var len2 = $(".shop_box tbody>tr>td input[type=checkbox]:checked").length;
	return len1-len2
	}
	var yixuan=function(){
		var len2 = $(".shop_box tbody>tr>td input[type=checkbox]:checked").length;
		$(".shop-bot ul li:nth-child(5) span i").text(len2)
	}
	var total = function(){
		var check = $(".shop_box tbody input[type=checkbox]:checked");
		
		var a = 0;
		for(var i=0;i<check.length;i++){
			a+=parseInt(check.eq(i).parents().siblings("td:nth-child(6)").children("span").text());
			
		}
		
		$(".shop-bot li:nth-child(6) span i").text(a.toFixed(2));
		
	}

	//头部全选
	$("thead>tr>th label").click(function(){
		if($(this).find("input").is(":checked")){
			$(".shop_box tbody>tr td input").removeAttr("checked")
			$(".shop_box tbody>tr td label").removeClass("on")
			$(".shop-bot label input").removeAttr("checked")
			$(".shop-bot label").removeClass("on")
			$("thead>tr>th label input").removeAttr("checked")
			$("thead>tr>th label").removeClass("on")
			total();
			yixuan();
		}else{
			$("thead>tr>th label input").prop("checked","checked");
			$("thead>tr>th label").addClass("on")
			$(".shop_box tbody>tr td input").prop("checked","checked");
			$(".shop_box tbody>tr td label").addClass("on")
			$(".shop-bot label input").prop("checked","checked");
			$(".shop-bot label").addClass("on")
			
			total();
			yixuan();
		}
		
	})

	//底部全选
	$(".shop-bot label").click(function(){
			
		if($(this).find("input").is(":checked")){
			$(".shop_box tbody>tr td label input").removeAttr("checked")
			$(".shop_box tbody>tr td label").removeClass("on")
			$(".shop-bot label input").removeAttr("checked")
			$(".shop-bot label").removeClass("on")
			$("thead>tr>th label input").removeAttr("checked")
			$("thead>tr>th label").removeClass("on")
			total();
			yixuan();
		}else{
			$("thead>tr>th label input").prop("checked","checked");
			$("thead>tr>th label").addClass("on")
			$(".shop_box tbody>tr td input").prop("checked","checked");
			$(".shop_box tbody>tr td label").addClass("on")
			$(".shop-bot label input").prop("checked","checked");
			$(".shop-bot label").addClass("on")
			
			
			total();
			yixuan();
		}
		
	})

	//单选
	$(".shop_box tbody>tr td label").click(function(){
	  		
		if($(this).find("input").is(":checked")){
			$(this).find("input").removeAttr("checked");
			$(this).removeClass("on")
			total();
		    yixuan();
		}else{
			$(this).find("input").prop("checked","checked");
			$(this).addClass("on")
			total();
		    yixuan();
		}
	})
	$(".shop_box tbody>tr td label").bind("click",function(){
			
		if(!cont()){
			$("thead>tr>th label input").prop("checked","checked");
			$("thead>tr>th label").addClass("on")
			$(".shop-bot label input").prop("checked","checked");
			$(".shop-bot label").addClass("on")
		}else{
			$(".shop-bot label input").removeAttr("checked")
			$(".shop-bot label").removeClass("on")
			$("thead>tr>th label input").removeAttr("checked")
			$("thead>tr>th label").removeClass("on")	
			
		}
		total();
		yixuan();
	})
	

	//加
	$(".prd_addNum").click(function(){
		var indexs = $(".prd_addNum").index(this)
		var num = parseInt($(this).siblings(".prd_num").val());
		++num;
		console.log(num)
		if(num>99){
			num=99;
		}
		
		proList[indexs].number = num;
				
		var tdV3 = parseInt($(this).parents("td").siblings(".univalence").children("span").text());
		
		$(this).parents("td").siblings("td:nth-child(6)").children("span").text(tdV3*num);
		
		$(this).siblings(".prd_num").val(num);
		total();
		localStorage.setItem("proList",JSON.stringify(proList))
	})
	
	//减
	$(".prd_subNum").click(function(){
		var indexs = $(".prd_subNum").index(this)
		var num = parseInt($(this).siblings(".prd_num").val());
		--num;
		if(num<1){
			num=1;
		}
		proList[indexs].number = num;
		var tdV3 = parseInt($(this).parents("td").siblings(".univalence").children("span").text());
		$(this).parents("td").siblings("td:nth-child(6)").children("span").text(tdV3*num);
		$(this).siblings(".prd_num").val(num);
		total();
		localStorage.setItem("proList",JSON.stringify(proList))
	})

	var input_val ;
	$(".prd_num").focus(function(){
		input_val = $(this).val();
	})
	$(".prd_num").change(function(){
		var indexs = $(".prd_num").index(this)
		var numA = $(this).val();
		if(isNaN(numA)){
			numA=input_val;
		}else if(numA>99){
			numA=input_val;
		}else if(numA<1){
			numA=input_val;
		}
		proList[indexs].number = numA;
		numA = Math.round(numA);
		$(this).val(numA);
		var tdV3 = parseInt($(this).parents("td").siblings(".univalence").children("span").text());
		
		$(this).parents("td").siblings("td:nth-child(6)").children("span").text(tdV3*numA);
		total();
		localStorage.setItem("proList",JSON.stringify(proList))
	})
	
	//删除
//	$(function(){
    	$(".shop_box  .remove").click(function(){
      		if(confirm("是否删除？")){
    		
    			var index = $(".remove").index(this)    			
//  			alert(index) 
    			proList.splice(index,1)
    			localStorage.setItem("proList",JSON.stringify(proList))
    			$(this).parents("tr").remove()
    			if(!cont()){
					$("thead>tr>th label input").prop("checked","checked");
					$("thead>tr>th label").addClass("on")
					$(".shop-bot label input").prop("checked","checked");
					$(".shop-bot label").addClass("on")
				}else{
					$(".shop-bot label input").removeAttr("checked")
					$(".shop-bot label").removeClass("on")
					$("thead>tr>th label input").removeAttr("checked")
					$("thead>tr>th label").removeClass("on")	
					
				}
	    		total();
	    		 yixuan();	
	    	}
    	})
//  })
    
    
    	$(" #allremove").click(function(){
    		if(confirm("是否删除？")){
    			
    			var obj = localStorage.getItem("proList");					
				var proList = JSON.parse(obj);
	    		var che=$(".shop_box tbody>tr>td input")
//	    		if(che.is(":checked")){	    			
//					$(".shop_box tbody>tr>td input:checked").parents("tr").remove();
//	    		}
	    		var arry = [];
	    		for(i=0;i<proList.length;i++){
	    			console.log(proList.length)
	    			if($(".inp .input").eq(i).prop("checked")){
	    				proList.splice(i,1)
	    				$(".inp .input").eq(i).parents("tr").remove();
	    				i=-1
	    			}
//	    			console.log(proList)
	    		}   	
	    		
	    		localStorage.setItem("proList",JSON.stringify(proList))
	    		
//	    		window.location.reload()
	            total();
	             yixuan();
	             
	       }

		})
			total();
			yixuan();
						
})
