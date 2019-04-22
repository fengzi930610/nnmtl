<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: image.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Verify_Image
{
    
	function output($S='0000',$W=80,$H=30)
	{
		$L = strlen($S);
		$char_X = 6;
		$char_Y = 0;
		$char_S = $W/$L;
		$font = __CFG::DIR.'data/font/verify.ttf';
		$font_size = 12;
		$font_color = "";
		$im = imagecreatetruecolor($W, $H);
		sscanf("#FFFFFF", "#%2x%2x%2x", $R, $G, $B);
		$backColor = imagecolorallocate($im, $R, $G, $B);
		imagefill($im,0,0,$backColor);
		for($i=0;$i<$L;$i++){
			$font_color = $this->rand_color($im);
			imagettftext($this->image,$font_size,mt_rand(-20,20),$i*$font_size+($W/$L)-floor($font_size/2),floor($H/2+$font_size/2),$font_color,$font,$S[$i]);
		}
		$line_X1 = $line_Y1 = $line_X2 = $line_Y2 = 0;
		for($i=0;$i<mt_rand(0,64);$i++){
			$line_X1 = mt_rand(0,$W);
			$line_Y1 = mt_rand(0,$H);
			$line_X2 = mt_rand(0,$W);
			$line_Y2 = mt_rand(0,$H);
			$line_X2 = $line_X1 + mt_rand(1,8);
			$line_Y2 = $line_Y1 + mt_rand(1,8);
			$color_Line = imagecolorallocate($im,mt_rand(0,230),mt_rand(0,230),mt_rand(0,230));
			imageline($im,$line_X1,$line_Y1,$line_X2,$line_Y2,$color_Line);
		}
		ob_clean();
		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
		@header("Content-type: image/png");
		imagepng($im);
		imagedestroy($im);
	}
	public function rand_color($im)
	{
		return imagecolorallocate($im, mt_rand(0,230), mt_rand(0,230), mt_rand(0,230));
	}
}