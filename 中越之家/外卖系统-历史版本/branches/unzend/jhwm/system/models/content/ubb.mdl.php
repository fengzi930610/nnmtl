<?php
/**
 * Copy	Right Anhuike.com
 * $Id ubb.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Content_Ubb extends Model
{
	
	//return html
	public function parse($content)
	{
		$content = str_replace(array(
				'[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]',
				'[i=s]', '[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
				'[list=A]', '[*]', '[/list]', '[indent]', '[/indent]', '[/float]','[quote]','[/quote]'
			), array(
				'</font>', '</font>', '</font>', '</p>', '<strong>', '</strong>', '<i class="pstatus">', '<i>',
				'</i>', '<u>', '</u>', '<ul>', '<ul type="1">', '<ul type="a">',
				'<ul type="A">', '<li>', '</ul>', '<blockquote>', '</blockquote>', '</span>','<div class="quote">','</div>'
			), preg_replace(array(
				"/\[color=([#\w]+?)\]/i",
				"/\[size=(\d+?)\]/i",
				"/\[size=(\d+(\.\d+)?(px|pt|in|cm|mm|pc|em|ex|%)+?)\]/i",
				"/\[font=([^\[\<]+?)\]/i",
				"/\[align=(left|center|right)\]/i",
				"/\[float=(left|right)\]/i",
				"/\[img\](.+?)(jpg|gif|png|jpeg|bmp)\[\/img\]/i",
				"/\[url\](.+?)\[\/url\]/is",
				"/\[url=([^\]]+)\](.+?)\[\/url\]/is"
			), array(
				"<font color=\"\\1\">",
				"<font size=\"\\1\">",
				"<font style=\"font-size: \\1\">",
				"<font face=\"\\1 \">",
				"<p align=\"\\1\">",
				"<span style=\"float: \\1;\">",
				"<img src=\"\\1\\2\">",
				"<a href=\"\\1 \">\\1</a>",
				"<a href=\"\\1 \">\\2</a>"
			), $content));
		return nl2br($content);	
	}
	
	//return ubb
	public function unparse($content)
	{
		$content = preg_replace("/\<a[^>]+href=\"([^\"]+)\"[^>]*\>(.*?)<\/a\>/i","[url=$1]$2[/url]",$content);
		$content = preg_replace("/\<font(.*?)color=[\"|']?#([^ >]+)[\"|']?(.*?)\>(.*?)<\/font>/i","<font$1$3>[color=$2]$4[/color]</font>",$content);
		$content = preg_replace("/\<font(.*?)face=[\"|']?([^ >]+)[\"|']?(.*?)\>(.*?)<\/font>/i","<font$1$3>[font=$2]$4[/font]</font>",$content);
		$content = preg_replace("/\<font(.*?)size=[\"|']?([^ >]+)[\"|']?(.*?)\>(.*?)<\/font>/i","[size=$2]$4[/size]",$content);
		$content = preg_replace("/\<img[^>]+src=[\"\']([^\"]+)[\"\'][^>]*\>/is","[img]$1[/img]",$content);
		$content = preg_replace("/\<img[^>]+src=\\\\[\"\']([^\"]+)\\\\[\"\'][^>]*\>/is","[img]$1[/img]",$content);
		$content = preg_replace("/\<div[^>]+align=\"([^\"]+)\"[^>]*\>(.*?)<\/div\>/is","[align=$1]$2[/align]",$content);
		$content = preg_replace("/\<div[^>]*\>(.*?)\<\/div\>/is","$1\n",$content);
		$content = preg_replace("/\<p(.*?)align=\"([^\"]+)\"(.*?)\>(.*?)<\/(\s*)p\>/is","[align=$2]$4[/align]",$content);
		$content = preg_replace("/\<p[^>]*\>(.*?)\<\/p\>/is","$1\n",$content);
		$content = preg_replace("/\<u(\s*?)\>(.*?)\<\/(\s*?)u\>/is","[u]$2[/u]",$content);
		$content = preg_replace("/\<em(\s*?)\>(.*?)\<\/(\s*?)em\>/is","[i]$2[/i]",$content);
		$content = preg_replace("/\<strong(\s*?)\>(.*?)\<\/(\s*?)strong\>/i","[b]$2[/b]",$content);
		$content = preg_replace("/\<b(\s*?)\>(.+?)\<\/(\s*?)b\>/is","[b]$2[/b]",$content);
		$content = preg_replace("/\<i(\s*?)\>(.+?)\<\/(\s*?)i\>/is","[b]$2[/b]",$content);
		$content = preg_replace("/<br\s*[\/]*>/i","\r\n",$content);
		$content = str_replace("&nbsp;"," ",$content);
		$content = preg_replace("/<[^>]*?>/i","",$content);
		//$content = preg_replace("/([^\r]\n)/i","\r\n",$content);
		return $content;	
	}
}