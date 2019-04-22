<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: imagick.mdl.php 2034 2013-12-07 03:08:33Z $
 */
//不支持多帧图片,多帧只处理第一帧
class Mdl_Image_Imagick
{
	
	CONST _WATE_FONT	= WATE_FONT;
	CONST _WATE_FSIZE	= WATE_FSIZE;
	CONST _WATE_IMAGE	= WATE_IMAGE;
	CONST _LIB_DIR		= '/usr/local/bin/';
	
	private $_types = array('jpg','png','gif','jpeg','bmp');
	private $_wate_str = '';

	public function set_watermark($str)
	{
		$this->_wate_str = $str;
	}
	
	public function Image($file)
	{
		return $this->_check($file);
		
	}
	public function thumbnail($im,$file,$w=200, $h=1000)
	{
		$im->thumbnailImage($w, $h, true,false);
		$im->writeImage($file);
		return true;
	}
	public function thumb($source,$target,$w=200,$h=1000,$l=false)
	{
		$success = false;
        if($im = $this->_check($source)){
			$wh = $im->getImageGeometry();
			if(($wh['width']>$w || $wh['height']>$h)){
				__::M('io/dir')->create(dirname($target));
				if($im->getNumberImages()>1){
					$im->setImageIndex(1);
					$frame = $im->getImage();
					$success = $this->thumbnail($frame, $target, $w, $h);
					$frame->destroy();
				}else if($target == $source){
					$success = $this->thumbnail($im, $target, $w, $h);
				}else if($l){
                    $cmd = "convert -quality 80 -resize {$w}x{$h}^ -gravity center -extent {$w}x{$h} {$source} {$target}"; 
                    $success = $this->_exec($cmd);
				}else{
					$size = ($w/$wh['width'] > $h/$wh['height'] ? $h/$wh['height'] : $w/$wh['width'])*100;
					$cmd = "convert -quality 80 -resize {$size}% {$source} {$target}";
					$success = $this->_exec($cmd);
				}
				$im->destroy();
			}else{
				__::M('io/file')->copy($source,$target);
				$success = true;
			}
		}
		return $success;
	}
	public function thumbs($source,$thumbs=array())
	{   
        
		if(!$im = $this->_check($source)){
			return false;
		}
		$wh = $im->getImageGeometry();
		foreach((array)$thumbs as $k=>$v){
			$w = $h = 0;
			if(preg_match("/(\d+)X(\d+)/i",$k,$match)){
				$w = $match[1]; $h = $match[2];
			}else{
				$w = $k;
			}
			if($h > 0){
				if($wh['width']>$w && $wh['height']>$h){
					$cmd = "convert -quality 80 -resize {$w}x{$h}^ -gravity center -extent {$w}x{$h} {$source} {$v}";
				}else if($wh['width']>$w){
					$size = ($w / $wh['width'])*100;
					$cmd = "convert -quality 80 -resize {$size}%^ -gravity center -extent {$w}x{$h} {$source} {$v}";
				}else if($wh['height']>0){
					$size = ($h / $wh['height'])*100;
					$cmd = "convert -quality 80 -resize {$size}%^ -gravity center -extent {$w}x{$h} {$source} {$v}";
				}else{
					$cmd = "convert -quality 80 {$source} {$v}";
				}
			}else if($wh['width']>$w){
				$size = ($w / $wh['width'])*100;
				$cmd = "convert -quality 80 -resize {$size}% {$source} {$v}";				
			}else{
				$cmd = "convert -quality 80 {$source} {$v}";
			}
			__::M('io/dir')->create(dirname($v));
			$this->_exec($cmd);
		}
	}
	public function watermark($source,$target=null)
	{
		if(!$im = $this->_check($source)){
			return false;
		}
		$wh = $im->getImageGeometry();
		$_w = $wh['width'];
		$_h = $wh['height'];
		if($wh['width']<200 || $wh['height']<300){
			$im->destroy();
			return true;
		}else if($im->getNumberImages()>1){ //多帖图片不处理水印
			$im->destroy();
			return true;
		}else{
			$im->destroy();
			$target = $target ? $target : $source;
			$_len = intval(strlen($this->_wate_str)*6.4);
			$t['x'] = 5;
			$t['y'] = $wh['height'] - 10;
			$i = array('w'=>193,'h'=>55);//水印大小提高效率写常量193*55
			$i['x'] = $wh['width'] - $i['w'];
			$i['y'] = $wh['height'] - $i['h'];
			$watestr = str_replace(array("\n", "\r", "'"), array('', '', '\''), $this->_wate_str);
			/*
			$exec = "convert -quality 80 -fill white -font ".self::_WATE_FONT." -pointsize ".self::_WATE_FSIZE." -stroke black -strokewidth 1 -draw \" text {$t[x]},{$t[y]} '{$watestr}\"";
			*/
			$cmd = "convert -fill white -font ".__CFG::W_FONT." -pointsize ".__CFG::W_FSIZE." -draw \" text {$t[x]},{$t[y]} '{$watestr}\"";
			if($wh['width']>$_len+$i['w']+10){
				$cmd .= " -draw \"image SrcOver {$i[x]},{$i[y]} {$i[w]},{$i[h]} '".__CFG::W_IMAGE."'\"";
			}
			$cmd .= " {$source} {$target}";
			return $this->_exec($cmd);
		}
	}
	
	private function _check($file)
	{
		$im = new Imagick($file);
		$type = strtolower($im->getImageFormat());
		if(!in_array($type,$this->_types)){
			$this->error = 703;
			return false;
		}
		return $im;
	}

	private function _exec($exec)
	{
		K::M('system/logs')->Log('exec.log',$exec);
		exec(self::_LIB_DIR.$exec, $output, $return);
		if(!empty($return) || !empty($output)) {
			return false;
		}
		return true;
	}
}