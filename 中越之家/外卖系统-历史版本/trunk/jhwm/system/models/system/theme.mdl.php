<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: theme.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Theme extends Mdl_Table
{   
  
    protected $_table = 'themes';
    protected $_pk = 'theme_id';
    protected $_cols = 'theme_id,theme,title,thumb,config,default,dateline';
    protected $_orderby = array('theme_id'=>'ASC');
    protected $_pre_cache_key = 'system-themes-list';
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        if($theme_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $theme_id;
    }
    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        if($rs = $this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return $rs;
    }
    public function theme($key=null, $theme_id=0)
    {
        if($items = $this->fetch_all()){
            if(empty($key) && ($theme_id = (int)$theme_id)){
                return $items[$theme_id];
            }
            foreach($items as $k=>$v){
                if($v['theme'] == $key){
                    return $v;
                }
            }
        }
        return false;       
    }
    public function install($ident)
    {
        $xml = __CFG::TMPL_DIR.$ident.DIRECTORY_SEPARATOR.'theme.xml';
        if(!$data = $this->parse_xml($xml)){
            $this->msgbox->add('模板不存在或模板配置文件不正确', 231);
        }else if($theme = $this->theme($ident)){
            $this->msgbox->add('该模板已经安装', 232);
        }else{
            $a = array('theme'=>$ident);
            $a['title'] = $data['title'];
            $a['thumb'] = $data['thumb'];
            if($theme_id = $this->create($a)){
    			$config = __CFG::TMPL_DIR.$ident.DIRECTORY_SEPARATOR.'config.xml';
                if($data = $this->parse_xml($config)){
                    if(!empty($data['adv'])){
                        if(isset($data['adv']['attrs'])){
                            $items = array($data['adv']);
                        }else{
                            $items = $data['adv'];
                        }
                        $oAdv = K::M('adv/adv');
                        $advs = array();
                        foreach($items as $v){
                            if($v['name'] && $v['from']){
                                if(!$oAdv->adv_by_name($v['name'])){
                                    $a = array('theme'=>$ident, 'title'=>(string)$v['name'], 'from'=>(string)$v['from']);
                                    if($v['width'] && $v['height']){
                                        $a['config'] = array('width'=>(int)$v['width'], 'height'=>(int)$v['height']);
                                    }
                                    $advs[] = $a;
                                }                                
                            }
                        }
                        if($advs){
                            foreach($advs as $v){
                                $oAdv->create($v);
                            }
                        }
                    }
                    if(!empty($data['block'])){
                        if(isset($data['block']['attrs'])){
                            $items = array($data['adv']);
                        }else{
                            $items = $data['block'];
                        }
                        $oBlock = K::M('block/block');
                        $blocks = array();
                        foreach($items as $v){
                            if($v['name'] && $v['from']){
                                if(!$oBlock->block_by_name($v['name'])){
                                    $a = array('theme'=>$ident, 'title'=>(string)$v['name'], 'from'=>(string)$v['from']);
                                    $a['type'] = $v['type'] == 'hot' ? 'hot' : 'new';
                                    $blocks[] = $a;
                                }
                            }
                        }
                        if($blocks){
                            foreach($blocks as $v){
                                $oBlock->create($v);
                            }                            
                        }
                    }                    
                }                
            }
            return $theme_id;
        }
        return false;
    }
    public function default_theme()
    {
        if($this->default_theme){
            return $this->default_theme;
        }else if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                if($v['default']){
                    return $v;
                }
            }
            return $this->theme('default');
        }
        return false;
    }
    public function set_default($theme_id)
    {
        $theme_id = (int)$theme_id;
        $this->db->update($this->_table, array('default'=>0), 1);
        return $this->update($theme_id, array('default'=>1), true);
    }
    public function load_themes()
    {
        $themes = array();
        $d = dir(__CFG::TMPL_DIR);
        while (false !== ($entry = @$d->read())) {
            $path = $d->path.DIRECTORY_SEPARATOR.$entry;
            $xml = $path.DIRECTORY_SEPARATOR.'theme.xml';
            if(is_dir($path)){
                if($data = $this->parse_xml($xml)){
                    $themes[$entry] = $data;
                }
            }
        }
        $d->close();
        return $themes;
    }
    public function parse_xml($xml)
    {
        $result = array();
        if(!file_exists($xml)){
            return false;
        }else if($data = simplexml_load_file($xml)){
            $data = (array)$data;
            foreach($data as $k=>$v){
                if(isset($v['@attributes'])){
                    $v['attrs'] = $v['@attributes'];
                }else if($v instanceof SimpleXMLElement){
                    if($v = (array)$v){
                        foreach($v as $kk=>$vv){
                            if($vv instanceof SimpleXMLElement){
                                $vv = (array)$vv;
                            }
                            $v[(string)$kk] = $vv;
                        }
                    }
                } 
                $result[(string)$k] = $v;
            }
        }
        return $result;
    }
    protected function _format_row($row)
    {
        if($row['config']){
            $config = unserialize($row['config']);
            $row['config'] = $config ? $config : array();
        }
        return $row;
    }
    public function options()
    {
        $options = array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                $options[$v['theme_id']] = $v['title']."({$v['theme']})";
            }
        }
        return $options;        
    }
    public function load_tmpls($path)
    {
        $dir = __TPL_DIR.trim($path, '/').'/';
        if(!is_dir($dir)){
            return false;
        }
        $p = dir($dir);
        $tmpls = array();
        while (false !== ($name = $p->read())) {
            $row = array();
            if($name == '.' || $name == '..'){
                continue;
            }else if(is_dir($dir.$name)){
                $row['type'] =  'dir';
                $row['time'] = filectime($dir.$name);
            }else if(preg_match("/\.(html|css|js)$/", $name)){
                $row['type'] =  'file';
                $row['size'] = filesize($dir.$name);
                $row['time'] = filemtime($dir.$name);
            }  
            $row['basename'] = $name;
			
            $tmpls[] = $row;
        }
		$tmpls = $this->up_sort($tmpls);
		
		foreach($tmpls as $k => $v){
			if($v['type'] != 'dir' && $v['size']<='0'){
				unset($tmpls[$k]);
			}else{
				if(substr($path,strpos($path,'/')+1) == ''){
					$tmpls[$k]['url'] = base64_encode($v['basename']);
				}else{
					$tmpls[$k]['url'] = base64_encode(substr($path,strpos($path,'/')+1).'/'.$v['basename']);
				}
				
			}
			
		}
        return $tmpls;
    }  
	public function up_sort($tmpls)
    {
		$temp = array();
		$temp1 = array();
		$count = count($tmpls);
		for($i=1;$i<$count;$i++){
			for($j=$count-1;$j>=$i;$j--){
				if($tmpls[$j]["time"]<$tmpls[$j-1]["time"]){
					$temp = $tmpls[$j-1];
					$tmpls[$j-1] = $tmpls[$j];
					$tmpls[$j] = $temp;
				}
				if(strlen($tmpls[$j]["type"])<strlen($tmpls[$j-1]["type"])){
					$temp1 = $tmpls[$j-1];
					$tmpls[$j-1] = $tmpls[$j];
					$tmpls[$j] = $temp1;
				}
			}
		}
		return $tmpls;
		
	}
    public function tmplsave($admin, $content, $tmpl)
    {
        $file = __TPL_DIR . str_replace(':', '/', $tmpl);
	   
        if(!file_exists($file)){
            $this->msgbox->add('模板文件不存在', 211);
            return false;
        }else if(!is_writable($file)){
            $this->msgbox->add('模板没有权限写入', 212);
            return false;
        }else if(!$bak = file_get_contents($file)){
            $this->msgbox->add('文件读取失败', 213);
            return false;
        }else if(md5($bak) == md5($content)){
            $this->msgbox->add('文件相同，未被修改', 214);
            return false;
        }else{
            $a = array('admin'=>$admin, 'content'=>addslashes($bak), 'tmpl'=>$tmpl,'dateline'=>time());
            if($bak_id = K::M('system/themebak')->create($a)){
                @file_put_contents($file, $content);
            }
            return true;
        }
    }

	public function bak_tmpls($tmpl)
	{
		$filter = array('tmpl'=>$tmpl);
		$orderby = array('bak_id'=>'desc');
		$detail = K::M('system/themebak')->items($filter, $orderby);
		return $detail;
		
	}

	public  function restore_bak($bak_id)
    {
		$detail = K::M('system/themebak')->detail($bak_id);
		$file = __TPL_DIR . str_replace(':', '/', $detail['tmpl']);
		if(!file_exists($file)){
            $this->msgbox->add('模板文件不存在', 211);
            return false;
        }else if(!is_writable($file)){
            $this->msgbox->add('模板没有权限写入', 212);
            return false;
        }else if(!$bak = file_get_contents($file)){
            $this->msgbox->add('文件读取失败', 213);
            return false;
        }else if(md5($bak) == md5(stripslashes($detail['content']))){
            $this->msgbox->add('文件相同，未被修改', 214);
            return false;
        }else{
            @file_put_contents($file, stripslashes($detail['content']));
            return true;
        }
	
	}
	
}