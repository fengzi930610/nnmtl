$(function(){
	//本地存储
	var regList =[];
		var regLocal = localStorage.getItem('regList'),
			regLocalJson = JSON.parse(regLocal)  //把获取到的字符串数据转换成数组对象
			if(regLocalJson != null){ //判断本地存储localStorage是否存在proList的值
				for(var i = 0;i<regLocalJson.length ;i++){
					regList.push(regLocalJson[i]) //把数组对象push进数组里面
				}
			}
	
	
	//选框
	$(".treaty label").click(function(){
		if($(this).find("input").is(":checked")){
			$(this).find("input").removeAttr("checked")
			$(this).removeClass("on")
		}else{
			$(this).find("input").prop("checked","checked")
			$(this).addClass("on")
		}
	})
	
	
	//用户名
		$("#username").focus(function(){
			document.getElementById("name").style.display="none"
			document.getElementById("cxname").style.display="none";
		})
		$("#username").blur(function(){
			var UserName = /^([\u4e00-\u9fa5]+|([a-z]+\s?)+)$/;
			var userval = $("#username").val();
			var regL = JSON.parse(localStorage.regList)
			for (var i = 0; i < regList.length; i++) {
				if(regList[i].username == userval){
					document.getElementById("cxname").style.display="block";
					document.getElementById("name").style.display="none";
					return false;
				}else{
					document.getElementById("cxname").style.display="none";
					
				}	
			}
			if(!UserName.test(userval)){
	    	   document.getElementById("name").style.display="block";
	    	   document.getElementById("cxname").style.display="none";
	
	    	}else{
	    		document.getElementById("name").style.display="none";
	    	}
			
		})

		//密码
		$("#password").focus(function(){
			document.getElementById("mima").style.display="none"
		})
		$("#password").blur(function(){
			var pasval = $("#password").val();
			var mimags = /^[a-zA-Z0-9]{6,16}$/;
			if(!mimags.test(pasval)){
				document.getElementById("mima").style.display="block"
			}else{
				document.getElementById("mima").style.display="none"
			}
		})
		
		
		
		//确认密码
		$("#qrpassword").focus(function(){
			document.getElementById("qrmima").style.display="none"
			document.getElementById("mimaer").style.display="none"
		})
		$("#qrpassword").blur(function(){
			var mimaval = $("#password").val()
			var qrmimaval = $("#qrpassword").val()
			
			if(mimaval==qrmimaval){
				document.getElementById("mimaer").style.display="none"
			}else{
				document.getElementById("mimaer").style.display="block"
				document.getElementById("qrmima").style.display="none"
			}
			if(qrmimaval==""){
				document.getElementById("qrmima").style.display="block"
				document.getElementById("mimaer").style.display="none"
			}else{
				document.getElementById("qrmima").style.display="none"
			}
		})
		
		//手机
		$("#shouji").focus(function(){
			document.getElementById("sj").style.display="none"
			document.getElementById("zqsj").style.display="none"
		})
		$("#shouji").blur(function(){
			var sjgs = /^[1][3458][0-9]{9}$/;
			var shoujival=$("#shouji").val()
			
			if(shoujival==""){
				document.getElementById("sj").style.display="block"
			}else{
					document.getElementById("sj").style.display="none"
				if(!sjgs.test(shoujival)){
					document.getElementById("zqsj").style.display="block"
				}else{
					document.getElementById("zqsj").style.display="none"
				}
			}
			
			
		})
		
		//验证码
//		$("#verification").focus(function(){
//			document.getElementById("yzm").style.display="none"
//		})
//		$("#verification").blur(function(){
//			var yzmval=$("#verification").val()
//			if(yzmval==""){
//				document.getElementById("yzm").style.display="block"
//			}else{
//				document.getElementById("yzm").style.display="none"
//			}
//		})
		
		//邮箱
		
		$("#E-mail").focus(function(){
			document.getElementById("post").style.display="none"
		})
		$("#E-mail").blur(function(){
			var Email = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
			var postval = $("#E-mail").val();
			
			
			if(!Email.test(postval)){
				document.getElementById("post").style.display="block"
			}else{
				document.getElementById("post").style.display="none"
			}
		
		})
		
		//立即注册
		$("#register").click(function(){
			var userval = $("#username").val();
			var mimaval = $("#password").val()
			var qrmimaval = $("#qrpassword").val()
			var shoujival=$("#shouji").val()
			var yzmval=$("#verification").val()
			var postval = $("#E-mail").val()
			var UserName = /^([\u4e00-\u9fa5]+|([a-z]+\s?)+)$/;
			var Password = /^[a-zA-Z0-9]{6,16}$/;
			var Email = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
			var sjgs = /^[1][3458][0-9]{9}$/;
			
			
				if(userval==""||mimaval==""||qrmimaval==""||shoujival==""||yzmval==""||postval==""){
					alert("亲，请填好您的信息！")
				}else if($("#treaty").prop("checked")==false){
					alert("亲，请勾选协议！")
				}
				
				else if(!mimaval==qrmimaval){
					return false;
				}
				else if(!Password.test(mimaval)){
					$("#cxname").css("display","none")
					$("#name").css("display","none")
					$("#mima").css("display","block")
					$("#qrmima").css("display","none")
					$("#sj").css("display","none")
					$("#zqsj").css("display","none")
					$("#post").css("display","none")
					return false;
				}
				else if(mimaval!==qrmimaval){
				    $("#cxname").css("display","none")
					$("#name").css("display","none")
					$("#mima").css("display","none")
					$("#qrmima").css("display","block")
					$("#sj").css("display","none")
					$("#zqsj").css("display","none")
					$("#post").css("display","none")
					return false;
				}
				else if(!Email.test(postval)){
					$("#cxname").css("display","none")
					$("#name").css("display","none")
					$("#mima").css("display","none")
					$("#qrmima").css("display","none")
					$("#sj").css("display","none")
					$("#zqsj").css("display","none")
					$("#post").css("display","block")
					return false;
				}
				else if(!sjgs.test(shoujival)){
					$("#cxname").css("display","none")
					$("#name").css("display","none")
					$("#mima").css("display","none")
					$("#qrmima").css("display","none")
					$("#sj").css("display","block")
					$("#zqsj").css("display","none")
					$("#post").css("display","none")
					return false;
				}else{
	        	alert("注册成功")
	        	window.setTimeout("window.location='login.html'");
	        }
	       regLocalDate(userval,mimaval)
			
		})
		function regLocalDate(username,password){
		var register={
			username:username,
			password:password
		}
		regList.push(register);
		localStorage.setItem("regList",JSON.stringify(regList));	
    }
})






















