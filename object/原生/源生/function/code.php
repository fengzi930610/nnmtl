<?php


//session_start();
//验证码函数
function code()
{
//	return 44141;
	ob_start();
	//获取画板
	$paper = imagecreatetruecolor(93, 31);
	//画笔
	$pen = imagecolorallocate($paper, 240 , 248 , 255); 
	//画一个有颜色的矩形替代画板的黑色
	imagefilledrectangle($paper, 0	, 0, 93, 31, $pen);
	//随机字符
	$str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
	$arr = str_split($str);//将字符串转成单字符数组
	$vastr = '';
	//写入文字
	for($i=1;$i<=4;$i++)
	{
		$j = $i*15+rand(0,10);//文字x位置
		$rand = mt_rand(20, 25);//文字y位置
		$angel = mt_rand(-45, 45);//文字角度
		$char = $arr[mt_rand(0, 61)];//取字符
		
		$vastr = $vastr.$char;
		$pen2 = imagecolorallocate($paper, mt_rand(0, 255) ,  mt_rand(0, 255) ,  mt_rand(0, 255));//随机画笔 
		imagefttext($paper, 16, $angel, $j	, $rand, $pen2, 'public/font/ARIAL.TTF', $char);
	}
	$_SESSION['code'] = $vastr;
	//点干扰
	for($i=0;$i<100;$i++)
	{
		$pen3 = imagecolorallocate($paper, mt_rand(0, 255) ,  mt_rand(0, 255) ,  mt_rand(0, 255)); 
		imagesetpixel($paper, mt_rand(0, 200), mt_rand(0, 50), $pen3);	
	}
	//线干扰
	$pen4 = imagecolorallocate($paper, 0,0,0); 
	imagearc($paper, 100, 45, 1000, 50, 0, 360, $pen4);
	ob_flush();
	//生成图片
	imagepng($paper);
	//将图片显示为html格式
	header("Content-type: image/png;charset=utf8");	
}

?>