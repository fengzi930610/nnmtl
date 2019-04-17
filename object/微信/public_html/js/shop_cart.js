$(function(){
//	选项卡
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
	
//	选项卡 end



		totalall();

	
})



//添加商品到购物车操作
function loadCar(){
	var carData = JSON.parse(getCar());
	var ul = document.getElementById("cart_product");
	if(carData == ""){
		ul.innerHTML = '';
	}
	if(carData){
		var li = "";
		for(var i=0;i<carData.length;i++){
					li += '<li data-id="'+carData[i].id+'">'+
						'<h4><span class="time">2017.5.24</span><span onclick=delect(this)>删除</span></h4>'+
						'<div class="img">'+
			 				'<a href="pro_details.html"><img src="'+carData[i].imgSrc+'"/></a>'+
			 			'</div>'+
			 			'<div class="text">'+
			 				'<p><a href="pro_details.html">'+carData[i].name+'</a></p>'+
			 				'<div class="pri_num">'+
			 					'<span class="price">&yen;'+carData[i].price+'</span>'+
			 					'<p class="num">数量：'+
			 						'<a href="javascript:void(0)" class="minus" onclick="minusnum(this)">-</a>'+
			 						'<input type="text" onchange="numChange(this)" value = '+carData[i].num+'></input>'+
			 						'<a href="javascript:void(0)" class="add" onclick="addnum(this)">+</a>'+
			 					'</p>'+
			 				'</div>'+
			 				'<div class="check">'+
			 					'<input type="hidden"  value="1" />'+
			 					'<img src="images/shop_cart-checked.png" alt="images/shop_cart-checked.png" onclick="checks(this)"/>'+
			 				'</div>'+
			 			'</div>'+
			 		'</li>';
			 	ul.innerHTML= li;

		}
	}
}

//单选框
	function checks(obj){
		var value = obj.previousSibling.value;
		if(value == 0){
			obj.setAttribute("src","images/shop_cart-checked.png");
			obj.previousSibling.value = 1;
			
		}else{
			obj.setAttribute("src","images/shop_cart-check.png");
			obj.previousSibling.value = 0;
			
		}
		var inputHi = document.querySelectorAll("#cart_product li .check input");
		var inputLen = document.querySelectorAll("#cart_product li .check input").length;
		var allinput = document.querySelector("#bottom .checkall input");
		
		for (var i =0;i<inputLen;i++) {
			if (inputHi[i].value == 0) {
				allinput.value = 0;
				allinput.nextElementSibling.setAttribute("src","images/shop_cart-check.png");
				break;
			}else if(inputHi[i].value == 1){
				allinput.value = 1;
				allinput.nextElementSibling.setAttribute("src","images/shop_cart-checked.png");
			}
			
		}
		totalall();
	}
//单选框 end

//	全选按钮
	function allcheck(obj){
		var value = obj.parentNode.firstElementChild.value;
		var totalall = 0;
		var checkimg = obj.parentNode.firstElementChild.nextElementSibling;
		length = document.getElementById("cart_product").children.length;
		if(value == 0){
				checkimg.setAttribute("src","images/shop_cart-checked.png");
				obj.parentNode.firstElementChild.value = 1;
				
			}else if(value == 1){
				checkimg.setAttribute("src","images/shop_cart-check.png");
				obj.parentNode.firstElementChild.value = 0;
			}
			var pic = document.querySelectorAll("#cart_product .check");
			if(obj.parentNode.firstElementChild.value == 1){
				for(var i=0;i<pic.length;i++){
					pic[i].firstElementChild.value = 1;
					pic[i].firstElementChild.nextSibling.setAttribute("src","images/shop_cart-checked.png");
				}
			}else if(obj.parentNode.firstElementChild.value == 0){
				for(var i=0;i<pic.length;i++){
					pic[i].firstElementChild.value = 0;
					pic[i].firstElementChild.nextSibling.setAttribute("src","images/shop_cart-check.png");
					document.querySelector(".total p span:first-child").innerText = "￥"+0;
				}
			}
			
	}
//	全选按钮 end
//总计
	function totalall(){
		var inputHidden = document.querySelectorAll("#cart_product li .check input");
		var totalall = 0;
		
		for(var i=0;i<inputHidden.length;i++){
			
			if(inputHidden[i].value==1){
				var id = inputHidden[i].parentElement.parentElement.parentElement.getAttribute("data-id");
				var totalPrice = price*num;
				var price = inputHidden[i].parentElement.previousSibling.firstElementChild.innerText.slice(1);
				var num = inputHidden[i].parentElement.previousSibling.lastElementChild.firstElementChild.nextElementSibling.value;
				var kuaidi = parseInt(document.querySelector(".total p:last-child").innerText.slice(7));
				totalall += parseInt(price*num);
			}
			
			
			if(totalall == 0){
				kuaidi = 0;
			}
			document.querySelector(".gotal a span").innerText = totalall + kuaidi;
			
		}
		changeCarNum(id,num,totalPrice);
		document.querySelector(".total p span").innerText =totalall.toFixed(2);
	}

//总计 end

//改变本地数据的数量
	function changeCarNum(id,num,totalPrice){
		var carData = JSON.parse(getCar());
		for(var i=0;i<carData.length;i++){
			if(carData[i].id == id){
				carData[i].num = num;
				carData[i].totalPrice = totalPrice;
				break;
			}
		}
		addCar(carData);
	}


//加操作
	function addnum(obj){
		var id = obj.parentElement.parentElement.parentElement.parentElement.getAttribute("data-id");
		var num = Math.floor(obj.previousSibling.value);
		var price = parseInt(obj.parentElement.parentElement.firstElementChild.innerText.slice(1));
		
		num+=1;
		obj.previousSibling.value = num;
		totalall();
		var totalPrice = price*num;
		changeCarNum(id,num,totalPrice);
	}

//加操作 end
//数量改变操作
	function numChange(obj){
		var num = obj.value;
		var id = obj.parentElement.parentElement.parentElement.parentElement.getAttribute("data-id");
		var price = parseInt(obj.parentElement.parentElement.firstElementChild.innerText.slice(1));
		if(isNaN(num)||num == ""||num<1){
			num = 1;
		}
		num = parseInt(num);
		obj.value = num;
		totalPrice = (price*num).toFixed(2);
		changeCarNum(id,num,totalPrice);
		totalall();
	}
//数量改变操作 end
//减操作
	function minusnum(obj){
		var num = obj.nextSibling.value;
		var price = parseInt(obj.parentElement.parentElement.firstElementChild.innerText.slice(1));
		var id = obj.parentElement.parentElement.parentElement.parentElement.getAttribute("data-id");
		
//		num = Math.floor(obj.nextviousSibling.innerText);
		num-=1;
		if (num < 1) {
			num = 1;
		}
		obj.nextSibling.value = num;
		totalall();
		var totalPrice = num*price;
		changeCarNum(id,num,totalPrice);
	}
//减操作 end
function changeCarNum(id,num,totalPrice){
		var carData = JSON.parse(getCar());
		if (carData) {
			for(var i=0;i<carData.length;i++){
				if(carData[i].id == id){
					carData[i].num = num;
					carData[i].totalPrice = totalPrice;
					break;
				}
			}
			addCar(carData);
		}
		
	}
	

//	定义key名称
	var keyName = "shop_car";
//	将商品添加到购物车
	function addproduct(product){
		//先获取本地数据
		var productData = getCar();
		//如果本地里面没有任何商品
		if(!productData){
			//创建一个json数据，将商品数据添加到这个json数据里面
			var proData = [
				product
			]
			addCar(proData);
		}else{
			//本地已经有数据（商品）
			//将数据转换成json格式的数据
			var carData =JSON.parse(productData);
			var bool = true;
			
			for(i=0;i<carData.length;i++){
				//通过id判断是否有相同商品，如果有相同的商品，直接加数量和小计
				if(carData[i].id == product.id){
					carData[i].num = parseInt(carData[i].num) + parseInt(product.num);
					carData[i].totalPrice = (parseFloat(carData[i].totalPrice) + parseFloat(product.totalPrice)).toFixed(2);
					bool = false;
					break;
				}
			}
			if (bool){
				carData.push(product);
			}
			addCar(carData);
		}
		
	}
	//通过指定的key获取商品数据
	function getCar(){
		return localStorage.getItem(keyName);
	}
	//通过指定的key添加商品到本地
	function addCar(productData){
		localStorage.setItem(keyName,JSON.stringify(productData));
	}
	//	删除操作
	function delect(obj){
		id = obj.parentElement.parentElement.getAttribute("data-id");
		var carData = JSON.parse(getCar());
		var arrData = [];
		for(var i=0;i<carData.length;i++){
			if(carData[i].id == id){
				continue;
			}else{
				arrData.push(carData[i]);
			}
		}
		addCar(arrData);
		loadCar();
		totalall();
//		location.reload();
		
	}
	//通过指定的id删除对应的商品
	function delProduct(id){
		var carData = JSON.parse(getCar());
		var arrData = [];
		for(var i=0;i<carData.length;i++){
			if(carData[i].id == id){
				continue;
			}else{
				arrData.push(carData[i]);
			}
		}
		addCar(arrData);
//		location.reload();
		pro_num();
	}
	
	function clearCar(){
		localStorage.removeItem(keyName);
	}
	
	
	function clearProAll(){
		clearCar();
		var ul = document.getElementById("pro_list");
		if(ul){
			var li = ul.getElementsByName("pro_");
			var length = tr.length;
			for(i=0;i<length;i++){
				ul.remove(i);
			}
		}
		
	}
	function goorder(){


		user = localStorage.getItem("user");
		if(!user){
			
			alert("请先登录");
			window.location.href = "login.html";
			return;
		}

		var inputHidden = document.querySelectorAll("#cart_product li .check input");
	

		if(inputHidden.length == 0){
			alert("请先添加商品");
			return;
		}
		var index = 0;
		for(var i=0;i<inputHidden.length;i++){
			
			if(inputHidden[i].value==1){
				window.location= "comfirm_order.html";
			}else{
				 index += 1; 
			}
		}
		if(index == inputHidden.length){
				alert("请先勾选商品");
			}
	}
	
