<?php
/**
 * Copy	Right Anhuike.com
 * $Id html.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Content_Html
{
	
	public function filter($content)
	{
        //屏蔽的标签       
        $content = preg_replace("/<[\/]{0,1}(iframe|script|style|form|link|meta|ifr|fra|input|textarea|button)[^>]*>/is", '', $content);
        //屏蔽on事件
        $content = preg_replace("/<([^>]+)(href|src|background|url|dynsrc|expression|codebase)\s*[=:(]([ \"']{0,1}(javascript|vbscript):[^>]+[ \"']{0,1})([^>]+)>/is","<\\1\\5>",$content);
        //屏蔽 href=javascript:
        $content = preg_replace("/<([^>]+?)(on[a-z]+?=[^>]+?)>/ies","\$this->__filter_onevent('<\\1\\2>')",$content);
        //去掉样式表class
        //$content = preg_replace("/<([^>]+?)class\s*=([\"']{0,1}[^>]+?[\"']{0,1})(.*)>/is","<\\1\\3>",$content);       
        //div转换成p
		$content = preg_replace("/<([\/]{0,1})div(\s+[^>]*)?>/is", "<\\1p>", $content);
		return $content;
	}
    public function __filter_onevent($content)
    {
        $content = preg_replace("/\\\\/",'',$content);
        $content = preg_replace("/on[a-z]+=\".+?\"/is",'',$content);
        $content = preg_replace('/on[a-z]+=\'.+?\'/is','',$content);
        $content = preg_replace('/on[a-z]+=[^\s]+/','',$content);
        return $content;
    }
    
    /**
     * 过滤掉html标记,得到纯文本
     * $content 待处理里内容
     * $space   是否过滤掉多余的空白{\n,\r,空格等}
     */
	public function text($content, $space=false)
	{
		$content = preg_replace("/<style .*?<\/style>/is", "", $content);
		$content = preg_replace("/<iframe.*?<\/iframe>/is", "", $content);
		$content = preg_replace("/<script .*?<\/script>/is", "", $content);
		$content = preg_replace("/<meta(.*?)>/is", "", $content);
		$content = preg_replace("/<br \s*\/?\/>/i", "\n", $content);
		$content = preg_replace("/<\/?p>/i", "\n", $content);
		$content = preg_replace("/<\/?td>/i", "\n", $content);
		$content = preg_replace("/<\/?div>/i", "\n", $content);
		$content = preg_replace("/<\/?blockquote>/i", "\n", $content);
		$content = preg_replace("/<\/?li>/i", "\n", $content);
		$content = preg_replace("/\&nbsp\;/i", " ", $content);
		$content = html_entity_decode($content);
		$content = strip_tags($content);
		$content = preg_replace("/\&\#.*?\;/i", "", $content);
        if($space){
            $content = preg_replace('/(\s+)/s',' ',$content);
        }
		return $content;		
	}
	public function encode($content)
	{
		if($content){
            //$content = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $content);
			$content = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $content);
			if(strpos($content, '&amp;#') !== false) {
				$content = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $content);
			}
		}
		return $content;
	}
	public function decode($content)
	{
		if($content){
            //$content = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $content);
			$content = str_replace(array('&amp;', '&lt;', '&gt;'), array('&', '<', '>'), $content);
		}
		return $content;
	}
}