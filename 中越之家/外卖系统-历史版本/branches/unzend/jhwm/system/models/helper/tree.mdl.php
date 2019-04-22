<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author  shzhrui<anhuike@gmail.com>
 * $Id: tree.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_Helper_Tree
{
    
/**
	* 生成树型结构所需要的2维数组
	* @var array
	*/
	public $arr = array();
	public $cid = 'cat_id';
	public $pid = 'parent_id';
	/**
	* 生成树型结构所需修饰符号，可以换成图片
	* @var array
	*/
	public $icon = array('│ ','├─ ','└─ ');
	public $nbsp = "&nbsp;";
	/**
	* @access private
	*/
	public $ret = '';
	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      1 => array('cat_id'=>'1','parent_id'=>0,'title'=>'一级栏目一'),
	*      2 => array('cat_id'=>'2','parent_id'=>0,'title'=>'一级栏目二'),
	*      3 => array('cat_id'=>'3','parent_id'=>1,'title'=>'二级栏目一'),
	*      4 => array('cat_id'=>'4','parent_id'=>1,'title'=>'二级栏目二'),
	*      5 => array('cat_id'=>'5','parent_id'=>2,'title'=>'二级栏目三'),
	*      6 => array('cat_id'=>'6','parent_id'=>3,'title'=>'三级栏目一'),
	*      7 => array('cat_id'=>'7','parent_id'=>3,'title'=>'三级栏目二')
	*      )
	*/
	public function init($arr=array(), $cid='cat_id', $pid='parent_id'){
       $this->arr = $arr;
       $this->cid = $cid;
       $this->pid = $pid;
	   $this->ret = '';
	   return is_array($arr);
	}
    /**
	* 得到父级数组
	* @param int
	* @return array
	*/
	public function parent($cid)
	{
		$parent = array();
		if(!isset($this->arr[$cid])) return false;
		$pid = $this->arr[$cid][$this->pid];
		$pid = $this->arr[$pid][$this->pid];
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a[$this->pid] == $pid) $parent[$id] = $a;
			}
		}
		return $parent;
	}
    /**
	* 得到子级数组
	* @param int
	* @return array
	*/
	public function child($pid)
	{
		$a = $child = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a[$this->pid] == $pid) $child[$id] = $a;
			}
		}
		return $child ? $child : false;
	}
    /**
	* 得到当前位置数组
	* @param int
	* @return array
	*/
	public function pos($id, &$newarr)
	{
		$a = array();
		if(!isset($this->arr[$id])) return false;
        $newarr[] = $this->arr[$id];
		$pid = $this->arr[$id][$this->pid];
		if(isset($this->arr[$pid])){
		    $this->pos($pid,$newarr);
		}
		if(is_array($newarr)){
			krsort($newarr);
			foreach($newarr as $v){
				$a[$v[$this->cid]] = $v;
			}
		}
		return $a;
	}
    /**
	* 得到树型结构
	* @param int ID，表示获得这个ID下的所有子级
	* @param string 生成树型结构的基本代码，例如："<option value=\$cat_id \$selected>\$spacer\$title</option>"
	* @param int 被选中的ID，比如在做树型下拉框的时候需要用到
	* @return string
	*/
	public function tree($pid, $str, $sid = 0, $adds = '', $str_group = '')
	{
		$number=1;
		$child = $this->child($pid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $id=>$value){
				$j=$k='';
				if($number==$total){
					$j .= $this->icon[2];
				}else{
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				if(is_array($sid)){
					$selected = in_array($id, $sid) ? 'selected' : '';
				}else{
					$selected = $id==$sid ? 'selected' : '';
				}
				$a = array('{$spacer}', '{$selected}');
				$b = array($spacer, $selected);
				foreach($value as $kk=>$vv){
					$a[] = '{$'.$kk.'}';
					$b[] = $vv;
				}
				if($parent_id == 0 && $str_group){
					$nstr = str_replace($a, $b, $str_group);
				}else{
					$nstr = str_replace($a, $b, $str);
				}
				$this->ret .= $nstr;
				$nbsp = $this->nbsp;
				$this->tree($id, $str, $sid, $adds.$k.$nbsp, $str_group);
				$number++;
			}
		}
		return $this->ret;
	}
    /**
	* 同上一方法类似,但允许多选
	*/
	public function tree_multi($pid, $str, $sid = 0, $adds = '')
	{
		$number=1;
		$child = $this->get_child($pid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $id=>$a){
				$j=$k='';
				if($number==$total){
					$j .= $this->icon[2];
				}else{
					$j .= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				$selected = $this->have($sid, $id) ? 'selected' : '';
				@extract($a);
				eval("\$nstr = \"$str\";");
				$this->ret .= $nstr;
				$this->tree_multi($id, $str, $sid, $adds.$k.'&nbsp;');
				$number++;
			}
		}
		return $this->ret;
	}
	/**
	 * 同上一类方法，jquery treeview 风格，可伸缩样式（需要treeview插件支持）
	 * @param $myid 表示获得这个ID下的所有子级
	 * @param $effected_id 需要生成treeview目录数的id
	 * @param $str 末级样式
	 * @param $str2 目录级别样式
	 * @param $showlevel 直接显示层级数，其余为异步显示，0为全部限制
	 * @param $style 目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam'
	 * @param $currentlevel 计算当前层级，递归使用 适用改函数时不需要用该参数
	 * @param $recursion 递归使用 外部调用时为FALSE
	 */
    function treeview($myid, $effected_id='example', $str="<span class='file'>\$title</span>", $str2="<span class='folder'>\$title</span>" ,$showlevel = 0 ,$style='filetree ' , $currentlevel = 1,$recursion=FALSE) {
        $child = $this->get_child($myid);
        if(!defined('EFFECTED_INIT')){
           $effected = ' id="'.$effected_id.'"';
           define('EFFECTED_INIT', 1);
        } else {
           $effected = '';
        }
		$placeholder = 	'<ul><li><span class="placeholder"></span></li></ul>';
        if(!$recursion) $this->str .='<ul'.$effected.'  class="'.$style.'">';
        foreach($child as $id=>$a) {
        	@extract($a);
			if($showlevel > 0 && $showlevel == $currentlevel && $this->get_child($id)) $folder = 'hasChildren'; //如设置显示层级模式@2011.07.01
        	$floder_status = isset($folder) ? ' class="'.$folder.'"' : '';
            $this->str .= $recursion ? '<ul><li'.$floder_status.' id=\''.$id.'\'>' : '<li'.$floder_status.' id=\''.$id.'\'>';
            $recursion = FALSE;
            if($this->get_child($id)){
            	eval("\$nstr = \"$str2\";");
            	$this->str .= $nstr;
                if($showlevel == 0 || ($showlevel > 0 && $showlevel > $currentlevel)) {
					$this->treeview($id, $effected_id, $str, $str2, $showlevel, $style, $currentlevel+1, TRUE);
				} elseif($showlevel > 0 && $showlevel == $currentlevel) {
					$this->str .= $placeholder;
				}
            } else {
                eval("\$nstr = \"$str\";");
                $this->str .= $nstr;
            }
            $this->str .=$recursion ? '</li></ul>': '</li>';
        }
        if(!$recursion)  $this->str .='</ul>';
        return $this->str;
    }
	
	private function have($list, $item)
	{
		return(strpos(',,'.$list.',',','.$item.','));
	}
}