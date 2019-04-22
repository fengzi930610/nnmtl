<?php
/**
 * Copy	Right Anhuike.com
 * $Id crypt.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Secure_Crypt
{
	
	public function decode($hex, $key='')
	{	
		$param = $this->hexstr($hex);
		return $this->crypt($param, 'DECODE', $key);
	}
	public function encode($param, $key='')
	{	
		$param =  $this->crypt($param, 'ENCODE', $key);
		return $this->strhex($param);
	}
	public function arrhex($param)
	{
		foreach((array)$param as $k=>$v){
			$_K = $_V = '';
			$k = strval($k);
			$v = strval($v);
			$_K = $this->strhex($k);
			$_V = $this->strhex($v);
			$hex .= strtoupper($_K).'O'.strtoupper($_V).'I';
		}
		return trim($hex,'I');
	}
	public function hexarr($hex)
	{
		$param = ''; 
		foreach(explode('I',$hex) as $h){
			list($_K,$_V) = explode('O',$h);
			$k = $this->hexstr($_K);
			$v = $this->hexstr($_V);
			$param[$k] = $v;
		}
		return	$param;
	}
	public function strhex($string)
	{
		$hex = "";  
		for ($i=0; $i<strlen($string); $i++)  
			$hex .= dechex(ord($string[$i]));  
		$hex = strtoupper($hex);  
		return $hex;	
	}
	public function hexstr($hex)
	{
		$string = "";  
		for($i=0; $i<strlen($hex)-1; $i+=2)  
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));  
		return $string;  		
	}
	public function strbin($string)
	{
	
	}
	public function binstr()
	{
		
	}
	public function crypt($string, $operation='ENCODE', $key = '')
	{
		$key = md5($key ? $key : PRI_KEY);
		$key_length = strlen($key);
		$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
		$string_length = strlen($string);
		$rndkey = $box = array();
		$result = '';
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'DECODE') {
			if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
				return substr($result, 8);
			} else {
				return '';
			}
		} else {
			return str_replace('=', '', base64_encode($result));
		}
	}
}