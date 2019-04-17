$(function(){
//		//选框
//		$(".remember label").click(function(){
//			if($(this).find("input").is(":checked")){
//				$(this).find("input").removeAttr("checked")
//				$(this).removeClass("on")
//			}else{
//				$(this).find("input").prop("checked","checked")
//				$(this).addClass("on")
//			}
//		})
		
		//用户名
		$("#username").focus(function(){
			document.getElementById("name").style.display="none"
		})
		
		$("#username").blur(function(){
			var userval = $("#username").val();
			if(userval==""){
				document.getElementById("name").style.display="block";
			}else{
				document.getElementById("name").style.display="none"
			}
		})
		
		//密码
		$("#password").focus(function(){
			document.getElementById("mima").style.display="none"
		})
		$("#password").blur(function(){
			var pasval = $("#password").val();
			if(pasval==""){
				document.getElementById("mima").style.display="block"
			}else{
				document.getElementById("mima").style.display="none"
			}
		})
		
		/******************************************************/
		//登录
			$("#login").click(function(){
				var xmval = $("#username").val();
				var mmval = $("#password").val();
				if(xmval==""||mmval==""){
					alert("亲，请填好信息！")
				}else{
					window.setTimeout("window.location='user.html'");			
							var isAutoLogin = document .getElementById("isAutoLogin").checked;
							var userNamev = document .getElementById("username").value
							var Passwordv = document .getElementById("password").value
							setStorage(userNamev,Passwordv,isAutoLogin);	
				}	
			})
		

})
		//本地存储
		var  setStorage = function(userName,Password,IsAutoLogin){
				if(IsAutoLogin){
					var userLoginInfor ={
						userName:userName,
						Password:Password,
						isAutoLogin:IsAutoLogin
					};
			
					localStorage.setItem("userLoginInfor",JSON.stringify(userLoginInfor));
				}
			}
		
		var loadStorage = function(){
			var obj = localStorage.getItem("userLoginInfor");
			var userInfor = JSON.parse(obj);
			if(userInfor!=null){
				
				document.getElementById("username").value = userInfor.userName;
				document.getElementById("password").value = userInfor.Password
				document.getElementById("isAutoLogin").checked = userInfor.isAutoLogin
			}
			
		}
		