<?php

//随机字符串函数封装
function randstr($num=5){
	
	$string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
	$str ='';
	$strlen = strlen($string);
	
	for($i=1;$i<=$num;$i++){
		
		$str.= substr($string,mt_rand(0,$strlen-1), 1);
	}
	return $str;
}


//分页模块封装
function page($table,$page,$num){	
	$total = selectAll($table,'*',1);
	$totalpage=ceil(count($total)/$num);//总页数
	//print_r($totalpage);die;
	//分页模块代码
	$href = 'index.php?m='.MODULE.'&c='.CON.'&a='.ACTION.'&page=';
	
		$pageNum = '<a href="'.$href.'1">首页</a>';
		$prev = ($page-1)<=0?1:($page-1);
		$pageNum .='<a href="'.$href.$prev.'">上一页</a>';
		
		$starpage = $page-2;
		$endpage = $page+2;
		if($starpage<1){
			$starpage=1;
			$endpage=$starpage+4;
		}
		if($endpage>$totalpage){
			$starpage=$totalpage-4;
			$endpage=$totalpage;
		}
		if($endpage<5){
			$starpage=1;
			$endpage=$totalpage;
		}
		
		for($i=$starpage;$i<=$endpage;$i++){
			if($page==$i){
				$pageNum .='<a href="'.$href.$i.'" style=" display:inline-block;width: 25px; border:1px solid red;margin-right: 5px;" class="current" >'.$i.'</a>';
			}else{
				$pageNum .='<a href="'.$href.$i.'" style=" display:inline-block;width: 25px; border:1px solid rgba(0,0,0,0);margin-right: 5px; " >'.$i.'</a>';
			}
			
			
		}
	
		$next = ($page+1)>=$totalpage?$totalpage:($page+1);
		$pageNum .='<a href="'.$href.$next.'">下一页</a>';
		$pageNum .='<a href="'.$href.$totalpage.'">尾页</a>';
		return $pageNum;
}
//分页模块封装结束
//图片上传封装
function upload($imgName){
	$path = '';
	//图片
	$error = $_FILES[$imgName]['error'];
	if($error==0){
		//获取上传文件的后缀；即文件类型
		$name = $_FILES[$imgName]['name'];
		$int = strrpos($name, '.');
		$type = substr($name, $int);//文件类型
		//判定文件类型必须为图片类型
		$typeArr = array('.jpg','.png','.jpeg','.gif');
		
		if(!in_array($type, $typeArr)){
			echo "<script>alert('上传文件类型不符合要求');history.go(-1);</script>";die;
		}
		
		$filename =date('YmdHis').randstr(4);//上传文件后的名称
		
		//设置文件路径
//		$fname = $_GET['c'];
		$filedir = 'upload/';
		if(!file_exists($filedir)){
			$oldumask=umask(0);
			//判断路径是否存在，目录不存在则自动创建目录；
			mkdir($filedir,'0777',true);//0777为权限设置；创建$filedir对应的目录,true为多层创建时自动创建对应的
			umask($oldumask);
		}
		$path = $filedir.$filename.$type;
		
		move_uploaded_file($_FILES[$imgName]['tmp_name'], $path);
	}else{
		switch($error){
			case 1:
				echo "<script>alert('文件超过php.ini允许的大小');history.go(-1);</script>";die;
				break;
			case 2:
			echo "<script>alert('超过表单允许的大小');history.go(-1);</script>";die;
				break;
			case 3:
				echo "<script>alert('图片只有部分被上传');history.go(-1);</script>";die;
				break;
			case 4:
//					echo "<script>alert('请选择图片');history.go(-1);</script>";die;
				break;
			case 6:
				echo "<script>alert('找不到临时目录');history.go(-1);</script>";die;
				break;
			case 7:
				echo "<script>alert('写文件到硬盘出错');history.go(-1);</script>";die;
				break;
			case 8:
				echo "<script>alert('File upload stopped by extension');history.go(-1);</script>";die;
				break;
			default:
				echo "<script>alert('未知错误');history.go(-1);</script>";die;
				break;
			
		}
	}
	return $path;
}

//缩略图
function thumb($path,$new_width=100){
	if(!empty($path)){
			
		$int = strrpos($path, '.');
		$type = substr($path, $int+1);//文件类型
		$type = ($type=='jpg')?'jpeg':$type;
		$fun = 'imagecreatefrom'.$type;//定义一个变量当做函数
		$imgstr = $fun($path);//获取该图片的内容
		$old_width = imagesx($imgstr);//获取原图的宽
		$old_height = imagesy($imgstr);//获取原图的高
		
//		$new_width = 100;//定义新图像的宽
		$new_height = $new_width*$old_height/$old_width;//等比例计算新图的高
		$newimg = imagecreatetruecolor($new_width, $new_height);//设置新图像的宽高
		imagecopyresized($newimg, $imgstr, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
//			dirname($path)为获取源图路径，
//			basename($path)为获取源图名称，
		$newpath = dirname($path).'/thumb_'.basename($path);//为新图拼接一个thumb_前缀，区别源图
		$imagefun = 'image'.$type;
		$imagefun($newimg,$newpath);
	}
}
//水印图
function water($path,$text,$imagestr='1',$waterImage='logo2.png',$x=0,$y=16){
	if(!empty($path)){
	
		$int = strrpos($path, '.');//字符串截取到文件类型后缀
		$type = substr($path, $int+1);//文件类型
		$type = ($type=='jpg')?'jpeg':$type;
		$fun = 'imagecreatefrom'.$type;//定义一个变量当做函数
		$imgstr = $fun($path);//获取该图片的内容
		
//		$imagestr = 2;//1.图片水印，2为文字水印
		
		if($imagestr==1){
//					$waterImage = 'logo2.png';
			$waterint = strrpos($waterImage, '.');
			$watertype = substr($waterImage, $waterint+1);//文件类型
			$watertype = ($watertype=='jpg')?'jpeg':$watertype;
			$waterfun = 'imagecreatefrom'.$watertype;//定义一个变量当做函数
			$waterimgstr = $waterfun($waterImage);//获取该图片的内容
			$waterImageX=imagesx($waterimgstr);
			$waterImageY=imagesy($waterimgstr);//获取需要水印的图片的宽高
			imagecopymerge($imgstr, $waterimgstr, 20, 20, 0, 0, $waterImageX, $waterImageY, 100);//合并图片
			//生成图像-路径
			$newpath = dirname($path).'/'.'/water_'.basename($path);
			//生成图像
			$imagefun = 'image'.$type;
			$imagefun($imgstr,$newpath);
		}else{
//				$wei = 1;
//					if($wei = 1){
//						$x = 0;
//						$y = $size;
//					}
			$textColor = imagecolorallocate($imgstr, 0, 0, 0);
			imagettftext($imgstr, 16, 0, $x, $y, $textColor, 'public/font/AdobeGothicStd-Bold.otf', $text);
			$newpath = dirname($_POST['img']).'/water_'.basename($_POST['img']);
			//生成图像
			$imagefun = 'image'.$type;
			$imagefun($imgstr,$newpath);
		}
	}
}

//新闻与产品分页
function nppage($table,$page,$num,$type,$total){
	$totalpage=ceil(count($total)/$num);//总页数
	//print_r($totalpage);die;
	//分页模块代码
	$href = 'index.php?m='.MODULE.'&c='.CON.'&a='.ACTION.'&page=';
	
		$pageNum = '<a href="'.$href.'1&type='.$type.'">首页</a>';
		$prev = ($page-1)<=0?1:($page-1);
		$pageNum .='<a href="'.$href.$prev.'&type='.$type.'">上一页</a>';
		
		$starpage = $page-2;
		$endpage = $page+2;
		if($starpage<1){
			$starpage=1;
			$endpage=$starpage+4;
		}
		if($endpage>$totalpage){
			$starpage=$totalpage-4;
			$endpage=$totalpage;
		}
		if($endpage<5){
			$starpage=1;
			$endpage=$totalpage;
		}
		
		for($i=$starpage;$i<=$endpage;$i++){
			if($page==$i){
				$pageNum .='<a href="'.$href.$i.'&type='.$type.'" style=" display:inline-block;width: 25px; border:1px solid red;margin-right: 5px;" class="current" >'.$i.'</a>';
			}else{
				$pageNum .='<a href="'.$href.$i.'&type='.$type.'" style=" display:inline-block;width: 25px; border:1px solid rgba(0,0,0,0);margin-right: 5px; " >'.$i.'</a>';
			}
			
			
		}
	
		$next = ($page+1)>=$totalpage?$totalpage:($page+1);
		$pageNum .='<a href="'.$href.$next.'&type='.$type.'">下一页</a>';
		$pageNum .='<a href="'.$href.$totalpage.'&type='.$type.'">尾页</a>';
		return $pageNum;
}


?>