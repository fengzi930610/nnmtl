$(function(){
	//清空表单
	function clearForm(){
		$(".user input").val("");
		$(".password input").val("");
		$(".upass input").val("");
		$(".email input").val("");
		$(".code input").val("");
	}
	

	//	获取验证码

	$("#getcode").click(function(){
		var i = 60;
		var dao = setInterval(function(){
			i-=1;
			$("#getcode").val(i+"秒后重新获取");
			if(i == 0){
				clearInterval(dao);
				$("#getcode").val("重新获取");
			}
		},1000);
		
		
		
	})
//	获取验证码 end

	var userinp = $(".user input");
	var passinp = $(".password input");
	var upassinp = $(".upass input");
	var emailinp = $(".email input");
	var codeinp = $(".code input");
	
	
	var user = $(".user input").val();
	var pass = $(".password input").val();
	var upass = $(".upass input").val();
	var email = $(".email input").val();
	var code = $(".code input").val();
	
	
	userinp.change(function(){
		var reg_user = /^[a-zA-Z_]\w{2,15}$/; //8到16个字符，首字母不要有数字
		var user =userinp.val();
		if(user.trim() === "" || !reg_user.test(user)){
			$("#middle form div.userTip").css("display","block");return;
		}else{
			$("#middle form div.userTip").css("display","none");
		}
	})
	passinp.change(function(){
		var reg_pass = /^[\w!@#$%^&*,.<>]{6,18}$/; //6到20个字符
		var pass =passinp.val();
		if(pass.trim() === "" || !reg_pass.test(pass)){
			$("#middle form div.passTip").css("display","block");return;
		}else{
			$("#middle form div.passTip").css("display","none");
		}
	})
	upassinp.change(function(){
		var upass =upassinp.val();
		var pass =passinp.val();
		if(upass.trim() === ""){
			$("#middle form div.upassTip2").css("display","block");return;
		}else{
			$("#middle form div.upassTip2").css("display","none");
		}
		if(pass != upass){
			$("#middle form div.upassTip1").css("display","block");return;
		}else{
			$("#middle form div.upassTip1").css("display","none");
		}
	})
	emailinp.change(function(){
		var reg_email = /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.(com|cn|net))+$/g;//邮箱
		var email =emailinp.val();
		if(email.trim() === "" || !reg_email.test(email)){
			$("#middle form div.emailTip").css("display","block");return;
		}else{
			$("#middle form div.emailTip").css("display","none");
		}
	})
	codeinp.change(function(){
		var reg_code = /^[a-zA-Z]{4}$/;	//验证码
		var code =codeinp.val();
		if(code.trim() === "" || !reg_code.test(code)){
			$("#middle form div.codeTip").css("display","block");return;
		}else{
			$("#middle form div.codeTip").css("display","none");
		}
	})
	
	
	
	
	
	
	
	$("#btn_reg").click(function(event){
		//处理默认事件
		event.preventDefault();
		//获取控件的内容
		var user = $(".user input").val();
		var pass = $(".password input").val();
		var upass = $(".upass input").val();
		var email = $(".email input").val();
		var code = $(".code input").val();
		
		var reg_user = /^[a-zA-Z_]\w{2,15}$/; //8到16个字符，首字母不要有数字
		//var reg_user = /^(?!\d)\w\w{7,15}$/;
		var reg_pass = /^[\w!@#$%^&*,.<>]{6,18}$/; //6到20个字符
		var reg_email = /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.(com|cn|net))+$/g;//邮箱
		var reg_code = /^[a-zA-Z\d]{4}$/;	//验证码
		//
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
		if(upass.trim() === ""){
			$("#middle form div.upassTip1").css("display","block");return;
		}else{
			$("#middle form div.upassTip1").css("display","none");
		}
		if(email.trim() === "" || !reg_email.test(email)){
			$("#middle form div.emailTip").css("display","block");return;
		}else{
			$("#middle form div.emailTip").css("display","none");
		}
		
		if(code.trim() === "" || !reg_code.test(code)){
			$("#middle form div.codeTip").css("display","block");return;
		}else{
			$("#middle form div.codeTip").css("display","none");
		}
		
		
		//判断输入的两次密码是否一致
		if(pass != upass){
			$("#middle form div.upassTip2").css("display","block");return;
		}else{
			$("#middle form div.upassTip2").css("display","none");
		}
		
		//设置点击之后的内容，同时限制多次点击
		$("#btn_reg").val("注册中...").prop("disabled",true);
		//进行注册接口的调用
		$.ajax({
			type:"post",
			url:"admin/reg.php",
			async:true,
			data:{
				//数据获取
				"user":user,
				"pass":pass,
				"email":email,
			},
			success:function(data){
				console.log(data);
				$("#btn_reg").val("注册").prop("disabled",false);
				//将后端返回的字符串数据转成json
				var json = JSON.parse(data);
				//通过判断json数据的不同，进行不同的操作
				if(json.type === "error"){
					switch(json.code){
						case "1":
							alert("该用户已经被注册");
							break;
						case "2":
							alert("邮箱已经被使用");
							break;
						case "3":
							alert("手机已经被使用");
							break;
						default:
							alert("发生未知错误，code：" + json.code);
					}
					
				}else{
					alert("注册成功");
					clearForm();
					location.href = "login.html";
				}
			},
			error:function(){
				alert("网络错误");
				$("#btn_reg").val("注册").prop("disabled",false);
			}
		});
		
		
		
	});
})
