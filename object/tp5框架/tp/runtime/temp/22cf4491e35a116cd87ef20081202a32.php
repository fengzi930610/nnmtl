<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:72:"D:\phpStudy\PHPTutorial\WWW\tp/application/index\view\cart\cartlist.html";i:1533881838;s:65:"D:\phpStudy\PHPTutorial\WWW\tp/application/index\view\layout.html";i:1533873593;}*/ ?>
<!DOCTYPE html>

<html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="__PUBLIC__css/commen.css" />
		
		<script type="text/javascript" src="__PUBLIC__js/jquery-2.1.1.js" ></script>
		<script type="text/javascript" src="__PUBLIC__js/jquery-1.11.2.min.js" ></script>
		<script type="text/javascript" src="__PUBLIC__js/jquery-1.11.0.js" ></script>		
		<script type="text/javascript" src="__PUBLIC__js/index.js" ></script>	
		<script type="text/javascript" src="__PUBLIC__js/login_user.js" ></script>	
<!--		<script type="text/javascript" src="__PUBLIC__js/menu.js" ></script>-->
	</head>
	<body>
		<!--头部-->
		<div class="header">
			<div class="header-top">
				<div class="navigation">
					<ul>
						<li><a href="#">网站导航</a></li>
						<li><a href="#">客户服务</a></li>
						<?php if(!empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('index/Index/user'); ?>">会员中心</a></li>
						<?php endif; if(empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('index/Index/login'); ?>" onclick="alert('请先登录')">会员中心</a></li>
						<?php endif; if(!empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('Cart/cartList'); ?>">购物车</a></li>
						<?php endif; if(empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('index/Index/login'); ?>" onclick="alert('请先登录')">购物车</a></li>
						<?php endif; ?>
						<li><a href="#">我的收藏</a></li>
						<li><a href="#">我的贺曼</a></li>
						<?php if(empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('index/Index/register'); ?>">注册</a></li>
						<li><a href="<?php echo url('index/Index/login'); ?>">登录</a></li>
						<?php endif; if(!empty(\think\Session::get('username'))): ?>
						<li><a href="<?php echo url('index/Index/logout'); ?>" onclick="return confirm('是否退出岛国后花园？')">退出</a></li>
						<li><a href="<?php echo url('index/Index/user'); ?>">用户：<?php echo \think\Session::get('username'); ?></a></li>
						
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<div class="header-conter">
				<a href="index.html" class="left"><img src="__PUBLIC__images/logo.jpg"></a>
				<div class="middle">
					<div class="search">
						<input type="text" class="btn" />
						<span>搜索</span>
					</div>
					<i>大家都在搜：<a href="pro_details.html">连衣裙</a>  <a href="pro_details.html">连体衣 </a> <a href="pro_details.html">儿童毯</a>  <a href="pro_details.html">帽子</a></i>
				</div>
				<a class="right"><img src="__PUBLIC__images/index01.jpg"></a>
			</div>
			<div class="header-bottom">
				<div class="menu" id="menu_index">
					<ul>
						<li><a href="<?php echo url('index/Index/index'); ?>" <?php if(!isset($nav)){echo 'class="on"';}; ?>  >首页</a></li>
						<?php if(is_array($navlist) || $navlist instanceof \think\Collection || $navlist instanceof \think\Paginator): $i = 0; $__LIST__ = $navlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<li><a href="<?php echo url('index/Index/shoplist',['nav'=>$v['Id']]); ?>" <?php if(isset($nav)){if($nav ==1 && $key==0){echo 'class="on"';}else if($nav ==2 && $key==1){echo 'class="on"';}else if($nav ==3 && $key==2){echo 'class="on"';}else if($nav ==4 && $key==3){echo 'class="on"';};}; ?>><?php echo $v['name']; ?></a></li>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="nav">
			<div class="sub-nav">
				<div class="pro-classify">
					<h2>全部产品分类</h2>
					<ul>
						<?php if(is_array($categorylist) || $categorylist instanceof \think\Collection || $categorylist instanceof \think\Paginator): $i = 0; $__LIST__ = $categorylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($v['fId'] == 0): ?>
						<li class='a<?php echo $key+1; ?>'>
							<a class="tx">
								<?php echo $v['name']; ?>>
							</a>
							<div class="pro">
								
								<dl>									
									<dd>
										<?php if(is_array($categorylist) || $categorylist instanceof \think\Collection || $categorylist instanceof \think\Paginator): $i = 0; $__LIST__ = $categorylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;if($vv['fId'] == $v['Id']): ?>
										<a><?php echo $vv['name']; ?></a>
										<?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</dd>
								</dl>
							</div>
						</li>
						<?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</ul>

				</div>

				<!--pro-classify-->

			</div>	
		</div>
		<!--nav-->
<link rel="stylesheet" type="text/css" href="__PUBLIC__css/commen.css" /><link rel="stylesheet" type="text/css" href="__PUBLIC__css/shop_cart.css" /><script type="text/javascript" src="__PUBLIC__js/jsAddress.js" ></script><!--头部 end-->
<div class="conter">
	<div class="middle">
		<div class="mid-contre">
			<h2>当前位置：<a href="index.html">首页</a>>我的购物车</h2>
			<div class="top">
				<ul>
					<li class="default">我的购物车</li>
					<li>确认订单</li>
					<li>提交订单</li>
				</ul>
			</div>
			<div class="shop_cart">
				<div class="shop-title">
					<div class="left">
						<ul>
							<li class="on">全部商品</li>
							<li>猜你喜欢</li>
						</ul>	
					</div>
					<div class="right">
						<label>配送至：</label>
						<select id="cmbProvince" name="cmbProvince" style="color: #000000;"></select>  						<select id="cmbCity" name="cmbCity" style="color: #000000;"></select>						<select id="cmbArea" name="cmbArea" style="color: #000000;"></select>
					</div>
				</div>
				<div class="shop_box">
					<ul>
						<li>
							<table>
								<thead>
									<tr>
										<th>
											<label>
												<input type="checkbox" class="all" />
												全选
											</label>
										</th>
										<th>商品</th>										<th>商品属性</th>										<th>单价（元）</th>										<th>数量</th>										<th>小计（元）</th>										<th>操作</th>									</tr>
								</thead>
								<tbody id="tbody">									<?php if(is_array($cartList) || $cartList instanceof \think\Collection || $cartList instanceof \think\Paginator): $i = 0; $__LIST__ = $cartList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
									<tr>
										<td class="shop_img">
											<label><input type="checkbox" value="<?php echo $list['Id']; ?>" class="ck" /></label>
											<a href="<?php echo url('Index/productDetail',['Id'=>$list['Id']]); ?>">												<img src="<?php echo substr($list['thumb'],1); ?>" />											</a>
										</td>
										<td class="shop_title"><span><a href="<?php echo url('Index/productDetail',['Id'=>$list['Id']]); ?>"><?php echo $list['name']; ?></a></span></td>										<td class="shop_color"><span id="color">【绿色】纯棉长袖</span></td>										<td class="univalence">											<span class="price"><?php echo $list['price']; ?></span>											<button id="Sales">特价产品</button>										</td>										<td class="shop_num">											<div class="num">												<button type="button" class="prd_subNum" id="addb" >-</button>												<input type="text" id="prd_numb" value="<?php echo $list['num']; ?>" class="prd_num">												<button type="button" class="prd_addNum" id="reduceb">+</button>									        </div>										</td>
										<td class="shop_price"><span class="total"><?php echo $list['price']*$list['num']; ?></span></td>
										<td class="shop_handle">
											<span class="delete" id="remove">删除</span>
											<span class="attention">添加关注</span>
										</td>
									</tr>									<?php endforeach; endif; else: echo "" ;endif; ?>
								</tbody>
							</table>	
						</li>
					</ul>					<ul style="display: none;">
						<li class="like"><a>猜你喜欢</a></li>
					</ul>
				</div>				<div class="shop-bot">
					<ul>
						<li>
							<label>
								<input type="checkbox" class="all" />
								全选
							</label>
						</li>
						<li class="delete">
							<span id="allremove">删除选中商品</span>
						</li>
						<li class="attention">
							<span>添加到关注</span>
						</li>
						<li class="eliminate">
							<span>清除下柜商品</span>
						</li>
						<li>
							<span class="allnum">已选择<i>0</i>件商品</span>
						</li>
						<li>
							<span class="allprice">总价：￥<i>0</i>元</span>							<br />
							<span>已节省：￥<a>0</a>元</span>
						</li>
						<li>
							<a href="JavaScript:void(0)"><button id="nextbtn">结算</button></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!--mid-contre-->
	</div>
	<!--middle-->
</div>
<script type="text/javascript">  addressInit('cmbProvince', 'cmbCity', 'cmbArea');</script><script>	//加	$(".prd_addNum").click(function(){		var num = parseInt($(this).siblings(".prd_num").val());		var price = $(this).parent().parent().parent().find("span.price").html();//		alert(price)		++num;		if(num>99){			num=99;		}		$(this).siblings(".prd_num").val(num);		var total = num*price		$(this).parent().parent().parent().find('.total').text(total)		allprice()	})	//减	$(".prd_subNum").click(function(){		var num = parseInt($(this).siblings(".prd_num").val());		var price = $(this).parent().parent().parent().find("span.price").html();		--num;		if(num<1){			num=1;		}		$(this).siblings(".prd_num").val(num);		var total = parseFloat(num*price)		$(this).parent().parent().parent().find('.total').text(total)		allprice()	})	var input_val ;	$(".prd_num").focus(function(){		input_val = $(this).val();	})	$(".prd_num").change(function(){		var numA = $(this).val();		if(isNaN(numA)){			numA=input_val;		}else if(numA<1){			numA=input_val;		}		numA = Math.round(numA);		$(this).val(numA);	})		$('.ck').click(function(){		allprice()	})		function allprice(){		var num =0		var total =0		$('.ck').each(function(){			var bool =$(this).is(":checked")			if(bool){				//数量				var cnum = $(this).parent().parent().parent().find('.prd_num').val()				num = parseFloat(num)+parseFloat(cnum)								//价格				var mintotal = $(this).parent().parent().parent().find('.total').text()//				alert(mintotal)				total= parseFloat(total)+parseFloat(mintotal)			}		})//		alert(num)//		alert(total)		$('.allnum i').text(num)		$('.allprice i').text(total)	}	//下一步  ，检测数据，更改购物车选中数据	$('#nextbtn').click(function(){//		alert(123456)		var data = []		$('.ck:checked').each(function(){			var every =[]			every.push($(this).val())			every.push($(this).parent().parent().parent().find('.prd_num').val())			data.push(every)		})//		alert(data)		if(data == ''){			alert('至少选择一个商品')			return false		}else{			$.post("<?php echo url('index/Cart/check'); ?>",{data:data},function(data){				if(data.info){					location="<?php echo url('index/Cart/check'); ?>"				}							})		}			})			//全选		$('.all').click(function(){		var bool = $(this).is(':checked')		$('.ck').prop('checked',bool)		allprice()	})</script>
		<!--footer-->
		<div class="footer">
			<div class="footer-top">

				<a class="code-left"><img src="__PUBLIC__images/code01.jpg"><span>官方微信</span></a>

				<a class="code-right"><img src="__PUBLIC__images/code02.jpg"><span>官方微博</span></a>
				<ul>

					<li>

						<i>7</i>

						<span>七天无理由退货</span>

					</li>

					<li>

						<i>优</i>

						<span>品质保证</span>

					</li>

					<li>

						<i>特</i>

						<span>特色服务体验</span>

					</li>

					<li>

						<i>？</i>

						<span>帮助中心</span>

					</li>

				</ul>
			</div>

			

			<!--footer-top-->

			<div class="footer-bottom">
				<p>
					<a href="about.html"><i>关于我们 </i></a>

					<a href="#"><i>购物袋 </i></a>

					<a href="#"><i>我的账户</i></a>

					<a href="#"><i>顾客查询</i></a>

					<a href="#"><i>产品优点</i></a>

					<a href="#"><i>零售网络</i></a>

					<a href="#"><i>联络我们</i></a>

					<a href="#">Hallmark Babies 官方网站 Officia</a>

					<a href="#">Website</a>
				</p>

				<p><a class="left">Copyright © 2009-2016 MYBABYKID.COM 版权所有 </a><a href="#" class="right">使用条款 | 隐私及安全条例</a></p>

				<p> © Hallmark Cards, Inc.</p>

				<div class="attention">

					<p>立即关注</p>

					<p>最新产品</p>

					<p>优惠信息发布</p>

				</div>
			</div>
		</div>

	

	</body>

</html>
<script>
	$(function(){
		$(document).ready(function(){

			var str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			var n = 4, s = "";

			for(var i = 0; i < n; i++){

			    var rand = Math.floor(Math.random() * str.length);

			    s += str.charAt(rand);

			}

			

		})

		$(".verification button").click(function(){

			var str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

			var n = 4, s = "";

			for(var i = 0; i < n; i++){

			    var rand = Math.floor(Math.random() * str.length);

			    s += str.charAt(rand);

			}

			$(".verification button").text(s);

		})

		

		$(".verification input").change(function(){

			var val = $(this).val()

			var p_text = $(".verification button").text();

			if(val.toLowerCase() == p_text.toLowerCase()){

				$(".verification b").text("√");

				$(".verification b").css("color","green");

				$(".verification b").css("display","block");

			}else{

				$(".verification b").text("×，请重新输入！");

				$(".verification b").css("color","red");

				$(".verification b").css("display","block");

				

											

				

			}

		})

		$(".verification input").blur(function(){

			var val = $(this).val()

			if(val == ""){

				$(".verification b").css("display","none");

			}

		})

		

		

		

	})

</script>
