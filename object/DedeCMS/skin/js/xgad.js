$(document).ready(function(){
	
	/*add*/
	var btn=$('.p27LCol'),
			ne=0;
		var lens=$(".p27LContent li").length;	
		$(".P27BSNum em").text(lens);
		$(".p27_Xx").css({height:$(window).height()}); 
		$(".p27_Bg").css({height:$(window).height()}); 
		//默认小图片按钮 挂接事件
		$('.p27LCol').click(function(){
			ne=$(this).index();
			var n=ne,
				lef=0;

			/*替换文字*/
			var contents=$(".p27LContent li").eq(n).find("img").attr("alt");
			//document.title=contents;
			$(".P27Msg").text(contents);
			/*替换数字*/
			$(".P27BSNum i").text(n+1);
			//获取宽度参数
			var kuan=$(this).width()+10,
				zong=btn.length*kuan - $('.p27LContainer').width()-10;

			//修改小图片按钮样式
			btn.removeClass('p27LCAct');
			$(this).addClass('p27LCAct');

			//获取移动距离
			lef=(n-2)*(kuan);

			//最左 最右判断
			if( n <= 2 )lef=0;
			if( lef>=zong )lef=zong;

			//执行滚动
			if($('.p27LContent li').length<4){
				$('.p27LContent ul').stop(true).animate({ left:0 });
			}else{
				$('.p27LContent').stop(true).animate({ left:-lef });
			}
			//修改大图路径
			$('.p27BImg img').attr('src',$(this).find('img').attr('src') );
			/*居中*/
			var mbimgs=$('.p27BImg img');
			var jaw=$('.p27BImg').width();
			var jah=$('.p27BImg').height();
			var jbw=$(this).find(".ylwh").width()
			var jbh=$(this).find(".ylwh").height();
			if(jbw>jaw && jbh<jah){//宽多高少
				var xgd=jbh/(jbw/jaw);
				mbimgs.css({ width:'100%',height:xgd+"px"});
				var mts=jah-xgd;
				mbimgs.css({margin:0}).css({marginTop:mts/2});
			}else if(jbw<jaw && jbh>jah){//宽少高多
				var xgd=jbw/(jbh/jah);
				mbimgs.css({ height:'100%',width:xgd+"px"});
				var mts=jaw-xgd;
				mbimgs.css({margin:0}).css({marginLeft:mts/2});
			}else if(jbw>jaw && jbh>jah){//宽多高多
				if(jbh>jbw){
					var xzk = jbw/(jbh/jah);
					mbimgs.css({ height:'100%',width:xzk+"px"});
					var mts=jaw-xzk;
					mbimgs.css({margin:0}).css({marginLeft:mts/2});
				}else{
					var xzk = jbh/(jbw/jaw);
					mbimgs.css({ width:'100%',height:xzk+"px"});
					var mts=jah-xzk;
					mbimgs.css({margin:0}).css({marginTop:mts/2});
				}
			}else{
				mbimgs.css({ width:jbw+"px",height:jbh+"px"});
				var mtsh=jah-jbh;
				var kok=jaw-jbw;
				mbimgs.css({marginTop:(mtsh/2)+"px",marginLeft:(kok/2)+"px"});	
			}
			
		}).eq(0).click();
	
		//默认样式 挂接左右按钮事件
		$('.p27LLeft').click(function(){
			ne-=1;
			if( ne<0 )ne=0;
			btn.eq(ne).click();
		});

		$('.p27LRight').click(function(){
			ne+=1;
			if( ne>=btn.length )ne=btn.length-1;
			btn.eq(ne).click();
		});

		$('.s-right').click(function(){
			$('.p27LRight').click();
		});
		$('.s-left').click(function(){
			$('.p27LLeft').click();
		});
		$('.P27BSRight').click(function(){
			$('.p27LRight').click();
		});
		$('.P27BSLeft').click(function(){
			$('.p27LLeft').click();
		});
		

		//详细按钮 点击事件
		var xBtn=$('.p27_Xx_Items');
		xBtn.click(function(){
			
			zidong();

			ne=$(this).index();

			//获取 图片路径
			var dizhi=$(this).find('img').attr('src');
			
			//获取宽度参数 15是 margin-left + border 的值
			var kuai=$(this).width()+10,
				zong=xBtn.length*kuai - $('.p27_Xx_Container').width();

			//修改小图片按钮样式
			xBtn.removeClass('p27_Xx_Act');
			$(this).addClass('p27_Xx_Act');

			var num=parseInt(($('.p27_Xx_Container').width()/kuai));
			
			//获取移动距离
			lef=( ne-num/2 )*kuai;

			//最左 最右判断
			if( lef<0 )lef=0;

			//执行滚动
			$('.p27_Xx_Content').stop(true).animate({ left:-lef });
			
			//修改大图路径
			$('.p27_Xx_Image img').attr('src',dizhi);
			daxiao();

			//修改当前序列号信息
			$('.p27_Xx_Title span').html( '<i>'+(ne+1)+'</i>/'+btn.length );

		});

		//计算大图位置
		function daxiao(){
			var ww=$(window).width(),
				wh=$('.p27_Xx').height(),
				xx=$('.p27_Xx_Shop');
			
			var are=$('.p27_Xx_Image'),
				img=$('.p27_Xx_Image img');
					
			var	he=wh-xx.height(),
				jj=160;		//上下最小间距的和
			
			//还原 图片的默认尺寸，并隐藏
			img.css({ opacity:0,width:'auto',height:'auto' });
			
			//如果 图片的默认高度超出 窗口+间隙 的高度，则缩小高度
			if( are.height() > wh-jj )img.height( wh-jj );
			
			//获取图片 距离窗口上边的 间隙
			var tp=(wh-are.height())/2;

			//修改大图位置
			$('.p27_Xx_Image').css({ top:tp,left:(ww-img.width())/2 });

			//刷新大图描述 位置
			$('.p27_Xx_Msg').css({ top:tp+are.height()+10 });
			
			//刷新 大图框宽度 9是边框宽度
			are.width( $('.p27_Xx_Image img').width()+9 );
			
			//显示图片
			img.css({ opacity:1 });
		}
		
		//详细界面 按钮挂接
		$('.p27_Xx_ILeft').click(function(){
			ne-=1;
			if( ne<0 )ne=0;
			xBtn.eq(ne).click();
		});

		$('.p27_Xx_IRight').click(function(){
			ne+=1;
			if( ne>=btn.length )ne=btn.length-1;
			xBtn.eq(ne).click();
		});

		$('.p27_Xx_BtnLeft').click(function(){
			$('.p27_Xx_ILeft').click();
		});
		$('.p27_Xx_BtnRight').click(function(){
			$('.p27_Xx_IRight').click();
		});
		

		//激活显示出 按钮
		$('.p27_Xx_Shop').hover(function(){
			clearTimeout(m);
			$(this).stop(true).animate({ bottom:0 });
		},function(){
			yincang();
		});
		
		//延时执行 隐藏 底部按钮区域
		var m;
		function yincang(){
			clearTimeout(m);
			m=setTimeout(function(){
				$('.p27_Xx_Shop').stop(true).animate({ bottom:"-82px" });
			},1000);
		}

		//自动播放
		var z;
		$('.p27_Xx_Auto').click(function(){
			$('.p27_Xx_Stop').show();
			$(this).hide();
			zidong();
		});

		function bofang(){
			zidong()
			$('.p27_Xx_IRight').click();
		}
		function zidong(){
			clearTimeout(z);
			z=setTimeout(function(){bofang()},3000);
		}

		//停止自动播放
		$('.p27_Xx_Stop').click(function(){
			$('.p27_Xx_Auto').show();
			$(this).hide();
			clearTimeout(z);
		});
		
		//关闭 全屏查看
		$('.p27_Xx_Colse').click(function(){
			$('.p27_Xx').hide();
			$('body').css({ overflow:'auto' });
		});
		
		
		
						
	})