
$(function(){
				$('.input-list').click(function(){
					if(!$('.input-list dd').eq(5).find('input').val()==""){
						$('.input-list dd').eq(5).find('input').focus()
					}else{
						$('.input-list dd').eq(0).find('input').focus()
					}
					
				})
				var num ;
				$('.input-list dd input').keyup(function(e){
					if(e.keyCode >= 48 && e.keyCode <= 57||e.keyCode >= 96 && e.keyCode <= 105 || e.keyCode == 8){
						var index = $(this).parent().index()
						switch(e.keyCode ){
							case 48:
								num = 0;
								break;
							case 49:
								num = 1;
								break;
							case 50:
								num = 2
								break;
							case 51:
								num = 3
								break;
							case 52:
								num = 4
								break;
							case 53:
								num = 5
								break;
							case 54:
								num = 6
								break;
							case 55:
								num = 7
								break;
							case 56:
								num = 8
								break;
							case 57:
								num = 9
								break;
							case 96:
								num = 0;
								break;
							case 97:
								num = 1;
								break;
							case 98:
								num = 2
								break;
							case 99:
								num = 3
								break;
							case 100:
								num = 4
								break;
							case 101:
								num = 5
								break;
							case 102:
								num = 6
								break;
							case 103:
								num = 7
								break;
							case 104:
								num = 8
								break;
							case 105:
								num = 9
								break;
						}
						if(!$('.input-list dd').eq(index).find('input').val()==""){
							$('.input-list dd').eq(index).find('input').val(num)
							index++;
							$('.input-list dd').eq(index).find('input').focus()
						}
						
						//按下删除键      keyCode键码数
						if(e.keyCode == 8){
							index = $(this).parent().index()
							if(!$('.input-list dd').eq(index-1).find('input').val()==""){
								$('.input-list dd').eq(index-1).find('input').focus()
								if(index==0){
									$('.input-list dd').eq(0).find('input').val("")
									$('.input-list dd').eq(index).find('input').focus()
								}
							}
							
						}
					}else{
						$('.input-list dd').eq(0).find('input').val("")
						return ;	
					}
					
				})
				$("#qrpay").click(function(){
        	var pass1 = $(".input-list dd #password1").val();
        	var pass2 = $(".input-list dd #password2").val();
        	var pass3 = $(".input-list dd #password3").val();
        	var pass4 = $(".input-list dd #password4").val();
        	var pass5 = $(".input-list dd #password5").val();
        	var pass6 = $(".input-list dd #password6").val();
        	
        	
        		if(pass1==""||pass2==""||pass3==""||pass4==""||pass5==""||pass6==""){
        			
        			alert("请输入密码")
        		}else{
        			alert("请输入密码")
        		}
        		
        	})
			})