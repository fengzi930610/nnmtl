<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: censor.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Content_Censor extends Mdl_Table
{
	public $succeed = true;
	public $code = 300;		//{300:成功,301:含屏蔽词,302:敏感词}
	public $message = '';	//{返回以","分隔的词}
	
	public $_censor = null;
	
	public function __construct(&$system)
	{
		parent::__construct($system);
		$this->_censor = K::M('data/censor')->fetch_all();
	}
	//敏感词
	public function censor($content)
	{
		if(!empty($this->_censor['censor'])){
			foreach((array)$this->_censor['censor'] as $censor){
				//用了提效率用preg_match 替换 preg_match_all
				if(preg_match($censor,$content,$match)){
					$this->succeed = false;
					$this->code = 302;
					$this->message = $match[0];
					return false;
				}
			}
		}
		$this->succeed = true;
		$this->code = 300;
		return $this->succeed;		
	}
	
	//屏蔽词
	public function shield($content)
	{
		if(!empty($this->_censor['shilde'])){
			foreach((array)$this->_censor['shilde'] as $shilde){
				//用了提效率用preg_match 替换 preg_match_all
				if(preg_match($shilde,$content,$match)){
					$this->succeed = false;
					$this->code = 301;
					$this->message = $match[0];
					return false;
				}
			}
		}
		$this->succeed = true;
		$this->code = 300;
		return $this->succeed;	
	}
	//过滤词
	public function filter($content)
	{
		if(!empty($this->_censor['filter'])){
			//注意这里可能存在多次替换
			foreach((array)$this->_censor['filter'] as $filter){
				$content = preg_replace($filter['find'],$filter['replace'],$content);
			}
		}
		return $content;
	}
}