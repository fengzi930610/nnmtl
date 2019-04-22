<?php
/**
 * Copy	Right Anhuike.com
 * $Id page.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Helper_Page
{
	
	public $page_limit = 5;

	public function page($num, $perpage, $curpage=1, $prefix='', $params=array(), $ext='.html')
	{
		$page = $this->page_limit;
		if(defined('IN_MOBILE')){
			$page = 3;
		}		
		$multipage = '';
		$maxpage = '';
		$num = is_numeric($num) ? $num : 0;
		if(empty($curpage)){
			$curpage = $_GET['page'] ?  $_GET['page'] :  1;
		}
		$request = K::$system->request;
		if(empty($prefix) && !empty($_SERVER['REQUEST_URI'])){
			$prefix = $request['uri'];
			$prefix = str_replace(".html","", $prefix);
			$prefix = preg_replace("/\-page\-[\d]+/i","-page-{page}",$prefix);
		}
		$prefix .= trim($prefix, '-').'-';
		if($MINI = K::$system->request['MINI']){
			$params['MINI'] = $MINI;
		}
		if(!empty($params) && is_array($params)){
			$params = http_build_query($params);
			if(strpos($prefix, '?') === false){
				$params = '?'.$params;
			}else{
				$params = '&'.$params;
			}
		}else{
			$params =  '';
		}
		//$realpages = 1;
		if($num > $perpage) {
			$offset = 2;
			$pages = @ceil($num / $perpage);
			if($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if($to - $from < $page) {
						$to = $page;
					}
				} elseif($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			if(!defined('IN_MOBILE')){
			$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$prefix.'1'.$ext.$params.'" class="first">1 ...</a>' : '').
				($curpage > 1 ? '<a href="'.$prefix.($curpage - 1).$ext.$params.'" title="上一页" class="prev">上一页</a>' : '');
			}
			for($i = $from; $i <= $to; $i++) {
				$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
					'<a href="'.$prefix.$i.$ext.$params.'">'.$i.'</a>';
			}
			$multipage .= ($curpage < $pages ? '<a href="'.$prefix.($curpage + 1).$ext.$params.'" title="下一页" class="next">下一页</a>' : '').
				($to < $pages ? '<a href="'.$prefix.$pages.$ext.$params.'" class="last">... '.$pages.'</a>' : '');
			if(defined('IN_ADMIN') || defined('IN_FENZHAN')){
				$goto = '<label><input type="text" size="3" title="输入页码，按回车快速跳转" value="%s" onkeydown="if(event.keyCode==13) {window.location=\'%s\'+this.value+\'%s%s\';event.stopPropagation();event.preventDefault();}"><span title="共 %s 页"> / %s 页</span></label>';
				$goto = sprintf($goto, $curpage, $prefix, $ext, $params, $pages, $pages);
				$multipage = $multipage ? ('<label><em>&nbsp;'.$num.'条记录&nbsp;</em></label>'.$multipage.$goto) : '';
			}else if(defined('IN_MOBILE')){
				$multipage = $multipage ? ('<em>&nbsp;'.$curpage.'/'.$pages.'&nbsp;</em>'.$multipage):'';
			}else{
				$multipage = $multipage ? ('<em>&nbsp;'.$num.'条记录&nbsp;</em>'.$multipage):'';
			}
		}
		return $multipage;		
	}
	public function format_link($link, $page)
	{
		if(strpos($link, '{page}') !== false){
			return str_replace('{page}', $page, $link);
		}else if(strpos($link, '%d') !== false){
			return sprintf($link, $page);
		}else if(strpos($link, '%s') !== false){
			return sprintf($link, $page);
		}
		return $link.'&page='.$page; 
	}
	public function mkpage($count, $limit, $curpage=1, $link=null, $params=array())
	{
		$page = $this->page_limit;
		if(defined('IN_MOBILE')){
			$page = 4;
		}
		if(empty($curpage)){
			$curpage = $_GET['page'] ?  $_GET['page'] :  1;
		}
		$request = K::$system->request;
		if(empty($link) && ($link = $request['uri'])){
			$link = K::M('helper/link')->mklink();
			if(preg_match('/\.html/', $link)){
				$link = preg_replace("/\-page\-[\d]+/i","-page-{page}",$link);
			}else{
				$link = '?ctl='.$request['ctl'].'&act='.$request['act'].'&page={page}';
			}
			//$prefix = str_replace(".html","", $link);
			//$prefix = preg_replace("/\-page\-[\d]+/i","-page-{page}",$prefix);
		}
		$request = K::$system->request;
		if($MINI = $request['MINI']){
			$params['MINI'] = $MINI;
		}
		if(!empty($params) && is_array($params)){
			if($params = http_build_query($params)){
				if(strpos($link, '?') === false){
					$params = '?'.$params;
				}else{
					$params = '&'.$params;
				}
			}else{
				$params =  '';
			}
		}else{
			$params =  '';
		}
		$link .= $params;	
		if($count > $limit) {
			$offset = 2;
			$pages = @ceil($count / $limit);
			if($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if($to - $from < $page) {
						$to = $page;
					}
				} elseif($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			if(true || !defined('IN_MOBILE')){
			$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="'.$this->format_link($link, 1).'" class="first">1 ...</a>' : '').
				($curpage > 1 ? '<a href="'.$this->format_link($link, ($curpage - 1)).'" title="上一页" class="prev">上一页</a>' : '');
			}
			for($i = $from; $i <= $to; $i++) {
				$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
					'<a href="'.$this->format_link($link, $i).'">'.$i.'</a>';
			}
			if(true || !defined('IN_MOBILE')){
			$multipage .= ($curpage < $pages ? '<a href="'.$this->format_link($link, ($curpage + 1)).'" title="下一页" class="next">下一页</a>' : '').
				($to < $pages ? '<a href="'.$this->format_link($link, $pages).'" class="last">... '.$pages.'</a>' : '');
			}
			if(defined('IN_ADMIN') || defined('IN_FENZHAN')){
				$golink = str_replace(array('%s', '%d'), array('{page}', '{page}'), $link);
				$goto = '<label><input type="text" size="3" title="输入页码，按回车快速跳转" value="%s" onkeydown="if(event.keyCode==13) {window.location=\'%s\'.replace(\'%s\',this.value);event.stopPropagation();event.preventDefault();}"><span title="共 %s 页"> / %s 页</span></label>';
				$goto = sprintf($goto, $curpage, $golink, '{page}', $pages, $pages);
				$multipage = $multipage ? ('<label><em>&nbsp;'.$count.'条记录&nbsp;</em></label>'.$multipage.$goto) : '';
			}else if(defined('IN_MOBILE')){
				$multipage = $multipage ? ('<em>&nbsp;'.$curpage.'/'.$pages.'&nbsp;</em>'.$multipage):'';
			}else{
				$multipage = $multipage ? ('<em>&nbsp;'.$count.'条记录&nbsp;</em>'.$multipage):'';
			}
		}
		return $multipage;			
	}	
	public function multipage($num, $perpage, $curpage=1, $prefix='', $params=array())
	{
		if(empty($prefix) && !empty($_SERVER['REQUEST_URI'])){
			$prefix = $_SERVER['REQUEST_URI'];
			$prefix = preg_replace("/&page\=[\d]+/i","",$prefix);
			$prefix .= '&page=';
		}
		return preg_replace("/-page-(\d+)/i", "&page=$1", $this->page($num,$perpage, $curpage, $prefix,''));
	}
}