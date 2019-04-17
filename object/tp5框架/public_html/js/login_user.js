$(function(){
			var Name = JSON.parse(localStorage.getItem("userLoginInfor")).userName
			console.log(Name)
			if(Name!=null){
				$(".header-top .navigation ul li:nth-child(8) a").text(Name);
				$(".usename").text(Name);
				$(".header-top .navigation ul li:nth-child(7) a").text("安全退出");
				
				if($(".header-top .navigation ul li:nth-child(8) a").text()==Name){
					$(".header-top .navigation ul li:nth-child(8) a").click(function(){
						window.setTimeout("window.location='user.html'")
					})
				}
			}
			if($(".header-top .navigation ul li:nth-child(7) a").text()=="安全退出"){
				$(".header-top .navigation ul li:nth-child(7) a").click(function(){
					localStorage.setItem("userLoginInfor",null)
					window.setTimeout("window.location='login.html'")
				})
			}
			if($(".header-top .navigation ul li:nth-child(8) a").text()=="登录"){
				$(".header-top .navigation ul li:nth-child(8) a").click(function(){
					window.setTimeout("window.location='login.html'")
				})
			}
			if($(".header-top .navigation ul li:nth-child(7) a").text()=="注册"){
				$(".header-top .navigation ul li:nth-child(7) a").click(function(){
					window.setTimeout("window.location='register.html'")
				})
			}
})
