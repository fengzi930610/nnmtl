<?php
/**
 * Copy	Right Anhuike.com
 * $Id string.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Content_String
{
	
	public static function &addslashes($string, $force = 0)
	{
		if (!MAGIC_QUOTES_GPC || $force) {
			if (is_array($string)) {
				foreach($string as $key => $val) {
					$string[$key] = &self::addslashes($val, $force);
				}
			} else {
				$string = addslashes(trim($string));
			}
		}
		return $string;
	}
	public static function sub($str, $start=0, $len=10, $suffix="…")
	{
		if(function_exists("mb_substr")){
			$slice =  mb_substr($str, $start, $len, __CFG::CHARSET);
		}else if(function_exists('iconv_substr')) {
			$slice = iconv_substr($str, $start, $len, __CFG::CHARSET);
		}else{
			$re['UTF-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['GB2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['GBK']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['BIG5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			$TBA_CHAR_SET = strtoupper(__CFG::CHARSET);
			preg_match_all($re[$TBA_CHAR_SET], $str, $match);
			$slice = join("",array_slice($match[0], $start, $len));
		}
		if($slice != $str){
			$slice = $slice.$suffix;
		}
		return $slice;
	}
	public static function Len($str)
	{		
		if(function_exists('mb_strlen')){
			$count = mb_strlen($str, __CFG::CHARSET);
		}else if(function_exists('iconv_strlen')){
			$count = iconv_strlen($str, __CFG::CHARSET);
		}else if(strtolower(__CFG::CHARSET) == 'utf-8'){
			$i = 0;
			$count = 0;
			$len = strlen($str);
			while ($i < $len) {
				$chr = ord($str[$i]);
				$count++;
				$i++;
				if ($i >= $len)
					break;
				if ($chr &0x80) {
					$chr <<= 1;
					while ($chr &0x80) {
						$i++;
						$chr <<= 1;
					}
				}
			}
		} else {
			$count = strlen($str);
		}
		return $count;
	}
	public static function strpos($string, $arr, $returnvalue = false)
	{
		if(empty($string)) return false;
		foreach((array)$arr as $v) {
			if(strpos($string, $v) !== false) {
				$return = $returnvalue ? $v : true;
				return $return;
			}
		}
		return false;
	}
	
	public static function Random($len, $type='', $extchars='')
	{
		switch($type){
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz';
				break;
			case 4:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break;				
			default :
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
		}
		$chars .= $extchars;
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		$chars = str_shuffle($chars);
		$charslen = strlen($chars);
		$start = mt_rand(0,$charslen-$len);
		$hash = substr($chars,$start,$len);
		return $hash;
	}
	/** 
	 * 类js unescape函数，解码经过escape编码过的数据 
	 * @param $str 
	 */ 
	public function unescape($str) 
	{ 
	    $ret = ''; 
	    $len = strlen($str); 
	    for ($i = 0; $i < $len; $i ++) 
	    { 
	        if ($str[$i] == '%' && $str[$i + 1] == 'u') 
	        { 
	            $val = hexdec(substr($str, $i + 2, 4)); 
	            if ($val < 0x7f) 
	                $ret .= chr($val); 
	            else  
	                if ($val < 0x800) 
	                    $ret .= chr(0xc0 | ($val >> 6)) . 
	                     chr(0x80 | ($val & 0x3f)); 
	                else 
	                    $ret .= chr(0xe0 | ($val >> 12)) . 
	                     chr(0x80 | (($val >> 6) & 0x3f)) . 
	                     chr(0x80 | ($val & 0x3f)); 
	            $i += 5; 
	        } else  
	            if ($str[$i] == '%') 
	            { 
	                $ret .= urldecode(substr($str, $i, 3)); 
	                $i += 2; 
	            } else 
	                $ret .= $str[$i]; 
	    } 
	    return $ret; 
	}
	/** 
	 * js escape php 实现 
	 * @param $string           the sting want to be escaped 
	 * @param $in_encoding       
	 * @param $out_encoding      
	 */ 
	public function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2')
	{ 
	    $return = ''; 
	    if (function_exists('mb_get_info')) { 
	        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
	            $str = mb_substr ( $string, $x, 1, $in_encoding ); 
	            if (strlen ( $str ) > 1) { // 多字节字符 
	                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
	            } else { 
	                $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
	            } 
	        } 
	    } 
	    return $return; 
	} 	
}