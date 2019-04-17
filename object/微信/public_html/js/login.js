$(function(){
//	记住密码
	$("#middle form p img").click(function(){
		if($("#middle form p input").val() == 0){
			$("#middle form p input").val(1);
			$("#middle form p img").attr("src","images/login_checked.png");
		}else{
			$("#middle form p input").val(0);
			$("#middle form p img").attr("src","images/login_check.png");
		}
		
	})
	$("#middle form p span").click(function(){
		if($("#middle form p input").val() == 0){
			$("#middle form p input").val(1);
			$("#middle form p img").attr("src","images/login_checked.png");
		}else{
			$("#middle form p input").val(0);
			$("#middle form p img").attr("src","images/login_check.png");
		}
	})
//	记住密码
	
	
	
	var reg_user = /^[a-zA-Z_]\w{2,15}$/;//3到16个字符，首字母不要有数字
	var reg_pass = /^[\w!@#$%^&*,.<>]{6,18}$/;; //6到18个字符
	
	var userinp = $("#user");
	var passinp = $("#password");
	
	userinp.change(function(){
		var user = $("#user").val();
		if(user.trim() === "" || !reg_user.test(user)){
			$("#middle form div.userTip").css("display","block");return;
		}else{
			$("#middle form div.userTip").css("display","none");
		}
	})
	
	passinp.change(function(){
		var pass = $("#password").val();
		if(pass.trim() === "" || !reg_pass.test(pass)){
			$("#middle form div.passTip").css("display","block");return;
		}else{
			$("#middle form div.passTip").css("display","none");
		}
	})
	
	$("#btn_login").click(function(event){
		//清除默认事件
		event.preventDefault();
		var user = $("#user").val();
		var pass = $("#password").val();
		
		var reg_user = /^[a-zA-Z_]\w{2,15}$/;//3到16个字符，首字母不要有数字
		var reg_pass = /^[\w!@#$%^&*,.<>]{6,18}$/;; //6到18个字符
		
		if(user.trim() === "" || !reg_user.test(user)){
			$("#middle form div.userTip").css("display","block");return;
		}else{
			$("#middle form div.userTip").css("display","none");
		}
		if(pass.trim() === "" || !reg_pass.test(pass)){
			$("#middle form div.passTip").css("display","block");return;
		}else{
			$("#middle form div.passTip").css("display","none");
		}
		
		//限制按钮
		$("#btn_login").val("登录中...").prop("disabled",true);
		//ajax提交登录请求
		$.ajax({
			type:"post",
			url:"admin/login.php",
			async:true,
			data:{
				"user":user,
				"pass":hex_md5(pass)   //提交的密码经过加密
			},
			success:function(msg){
				//恢复按钮
				$("#btn_login").val("登录").prop("disabled",false);
				//json数据转换
				console.log(msg)
				jsan = JSON.parse(msg);
				if(jsan.type == "success"){
					//document.cookie = "user="+user;
					localStorage.setItem("user",user);//跨页面验证
					setcookie(user,pass);
					//登录成功操作
					$("#user").val("");
					$("#password").val("");
					
					location.href = "index.html";
				}else{
					//判断错误情况
					switch(jsan.code){
						case "1":
							alert("该用户不存在，请检查再输入");
							break;
						case "2":
							$(".log_inp:eq(1)").val("");
							alert("密码错误！");
							break;
						
						default:
							alert("发生未知错误");
					}
				}
			},
			error:function(){
				$("#btn_login").val("登录").prop("disabled",false);
				alert("服务器连接失败，请检查网络");
			}
		});
	});
	
	function setcookie(user,pass){
		var isLogin = $("#ckbox").val();
		if($("#ckbox").val() == 1){
			var nowDate = new Date();
			//设置cookie过期时间
			nowDate.setTime(nowDate.getTime()+1000*60*60*12);
			document.cookie = "user="+user+";expires="+nowDate.toUTCString()+";path=/";
			document.cookie = "pass="+pass+";expires="+nowDate.toUTCString()+";path=/";
			document.cookie = "isLogin="+isLogin+";expires="+nowDate.toUTCString()+";path=/";
		}else{
			document.cookie = "user=";
			document.cookie = "pass=";
			document.cookie = "isLogin="+0;
		}
	}
	//获取cookie
	
	
});
function getCookie(){
		var cookies = document.cookie;
		
		if (!cookies) {
			return;
		}
		//做一个判断，cookie的长度大于0
		if(cookies.length >0){
			//通过split()方法，将cookie分割成多个数据
			var cookieAll = cookies.split(";");
			for (var i=0;i<cookieAll.length;i++) {
				
				if(cookieAll[i].indexOf("user")>-1){
					user = cookieAll[i].split("=");
				}
				if(cookieAll[i].indexOf("pass")>-1){
					pass = cookieAll[i].split("=");
				}
				if(cookieAll[i].indexOf("isLogin")>-1){
					isLogin = cookieAll[i].split("=");
				}
			}
		}
		if(isLogin){
			if (isLogin) {
				if(isLogin[1] > 0){
				
				//把值赋值给对应的输入框
					document.getElementById("user").value = user[1];
					document.getElementById("password").value = pass[1];
					document.querySelector("#middle form p input").value = isLogin[1];
					document.querySelector("#middle form p img").setAttribute("src","images/login_checked.png");
					
				}
			}
		}
		
		
	}