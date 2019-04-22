<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: upload.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Magic_Upload extends Mdl_Table
{
	protected $_table = 'upload_photo';
	protected $_pk = 'photo_id';
	protected $_cols = 'photo_id,from,hash,photo,size,name,dateline,cate_id';
    protected $_orderby = array('photo_id'=>'ASC');
    public function upload_by_data($data, $from='data', $cate_id=0)
    {
        $ym = date('Ym', __CFG::TIME);
        $fname = date('Ymd_').strtoupper(md5(microtime().PRI_KEY.uniqid())).".png";
        $photo = "photo/{$ym}/{$fname}";
        $cfg = K::$system->config->get('attach');
        $dir = $cfg['attachdir'].'photo'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $file = $dir.$fname;
		$thumbs = array('800'=>$file, '200X200'=>$file.'_thumb.jpg');
        if(K::M('storage/upload')->upload_by_data($data, $dir, $fname, $thumbs)){
            $hash = md5($photo);
            $a = array('photo'=>$photo,'name'=>$fname, 'hash'=>$hash);
            $a['size'] = strlen($data);
               // file_size($file);
            $a['from'] = $from;
            $a['dateline'] = __CFG::TIME;
            $a['photo_id'] = $this->db->insert($this->_table, $a, true);
            $a['file'] = $file;
            $a['cate_id'] = (int)$cate_id;
            return $a;
        }
        return false;
    }
	public function upload($attach, $from='image', $source=null, $size=array(), $cate_id=0)
	{
        $ym = date('Ym', __CFG::TIME);
        $cfg = K::$system->config->get('attach');
        $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
        $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        $photo = "photo/{$ym}/{$fname}";
        $dir = $cfg['attachdir'].'photo'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $file = $dir.$fname;
        if($source && is_string($source) && strlen($source) > 32){
            if(!preg_match('/^(http|temp|cache)/', $source)){
                $hash = md5($source);
                if($item = $this->item_by_hash($hash)){
                    $fname = basename($item['photo']);
                }
            }
        }
        $thumbs = array();
        if($size && is_array($size)){
            foreach((array)$size as $k=>$v){
                if($k == 'photo'){
                    $thumbs[$v] = $file;
                }elseif($k == 'thumb'){
                    $thumbs[$v] = $file.'_thumb.jpg';
                }else{
                    $thumbs[$v] = $file."_{$v}.jpg";
                }
            }
        }elseif(in_array($from, array('wmproduct', 'jifenproduct', 'mallproduct', 'tuanproduct', 'product'))){
            $thumbs = array('750'=>$file, '200X200'=>$file.'_thumb.jpg');
        }elseif($from == 'comment'){
            $thumbs = array('800'=>$file, '300X300'=>$file.'_thumb.jpg');
        }elseif($from == 'editor'){
            $size = array();
            $size['photo'] = $cfg['editor']['photo'] ? $cfg['editor']['photo'] : '720';
            $size['thumb'] = $cfg['editor']['thumb'] ? $cfg['editor']['thumb'] : '200';
            $thumbs = array($size['photo']=>$file, $size['thumb']=>$file.'_thumb.jpg');
        }elseif($from == 'face'){
            $thumbs = array('200X200'=>$file);
        }elseif($from == 'image'){
            $thumbs = array('800'=>$file);
        }
        if(!K::M('storage/upload')->upload($attach, $dir, $fname, $thumbs)){
            return false;
        }
        $hash = md5($photo);
        $a = array('size'=>$attach['size'], 'photo'=>$photo,'name'=>$attach['name'], 'hash'=>$hash, 'cate_id'=>(int)$cate_id);
        if($item){
            $a = array_merge($item, $a);
            $this->db->update($this->_table, $a, "photo_id='{$item[photo_id]}'");
        }else{
            $a['from'] = $from;
            $a['dateline'] = __CFG::TIME;
            $a['photo_id'] = $this->db->insert($this->_table, $a, true);
        }
        $a['file'] = $file;
        return $a;
    }
    public function upload_all($attachs, $from='editor', $source=null)
    {
        $file_arr = array();
        foreach ($attachs as $attach){
            if($a = $this->upload($attach, $from)){
                $file_arr[] = $a;
            }
        }
        return $file_arr ? $file_arr : false;
    }
    public function zip($attach, $source=null)
    {
        $ym = date('Ym', __CFG::TIME);
        $cfg = K::$system->config->get('attach');
        $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
        $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        $photo = "file/{$ym}/{$fname}";
        $dir = $cfg['attachdir'].'file'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $file = $dir.$fname;
        if($source && is_string($source) && strlen($source) > 32){
            if(!preg_match('/^(http|temp|cache)/', $source)){
                $hash = md5($source);
                if($item = $this->item_by_hash($hash)){
                    $fname = basename($item['photo']);
                }
            }
        }
        if(!$file = K::M('storage/upload')->zip($attach, $dir, $fname)){
            return false;
        }
        $hash = md5($photo);
        $a = array('size'=>$attach['size'], 'photo'=>$photo,'name'=>$attach['name'], 'hash'=>$hash);
        if($item){
            $a = array_merge($item, $a);
            $this->db->update($this->_table, $a, "photo_id='{$item[photo_id]}'");
        }else{
            $a['from'] = 'zip';
            $a['dateline'] = __CFG::TIME;
            $a['photo_id'] = $this->db->insert($this->_table, $a, true);
        }
        $a['file'] = $file;
        return $a;
    }
    public function file($attach, $source=null)
    {
        $ym = date('Ym', __CFG::TIME);
        $cfg = K::$system->config->get('attach');
        $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
        $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        $photo = "file/{$ym}/{$fname}";
        $dir = $cfg['attachdir'].'file'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $file = $dir.$fname;
        if($source && is_string($source) && strlen($source) > 32){
            if(!preg_match('/^(http|temp|cache)/', $source)){
                $hash = md5($source);
                if($item = $this->item_by_hash($hash)){
                    $fname = basename($item['photo']);
                }
            }
        }
        if(!$file = K::M('storage/upload')->file($attach, $dir, $fname)){
            return false;
        }
        $hash = md5($photo);
        $a = array('size'=>$attach['size'], 'photo'=>$photo,'name'=>$attach['name'], 'hash'=>$hash);
        if($item){
            $a = array_merge($item, $a);
            $this->db->update($this->_table, $a, "photo_id='{$item[photo_id]}'");
        }else{
            $a['from'] = 'file';
            $a['dateline'] = __CFG::TIME;
            $a['photo_id'] = $this->db->insert($this->_table, $a, true);
        }
        $a['file'] = $file;
        return $a;
    }
    public function xheditor($attach)
    {
        $ym = date('Ym', __CFG::TIME);
        $cfg = K::$system->config->get('attach');
        $dir = $cfg['attachdir'].'photo'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        $ext = $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
        $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        $file = $dir.$fname;
        $is_yunstorage = K::M('storage/upload')->get_yun_storage();
        K::M('storage/upload')->set_yun_storage(false);
        if($attach['html5']){
            if(strlen($attach['data'])>2097152){
                $this->msgbox->add('上传的文件不能超过2M', 721);
                return false;
            }elseif(!K::M('storage/upload')->upload_by_data($attach['data'], $dir, $fname)){
                return false;
            }
        }else if(!K::M('storage/upload')->upload($attach, $dir, $fname)){
            return false;
        }
        K::M('storage/upload')->set_yun_storage($is_yunstorage);
        if($file){
            $photo = "photo/{$ym}/{$fname}";
            $hash = md5($photo);
            $a = array('size'=>$attach['size'], 'photo'=>$photo,'name'=>$attach['name'], 'hash'=>$hash);
            $a['from'] = $from;
            $a['dateline'] = __CFG::TIME;
            $a['photo_id'] = $this->db->insert($this->_table, $a, true);
            $a['file'] = $file;
            $size['photo'] = $cfg['editor']['photo'] ? $cfg['editor']['photo'] : '720';
            $size['thumb'] = $cfg['editor']['thumb'] ? $cfg['editor']['thumb'] : '200';
            $thumbs = array($size['photo']=>$file, $size['thumb']=>$file.'_thumb.jpg');
            K::M('image/gd')->thumbs($file, $thumbs, false);
            if($cfg['editor']['watermark']){
                $site = K::$system->config->get('site');
                $uname = $attach['uname'] ? $attach['uname'] : $site['title'];
                K::M('image/gd')->watermark($file, $uname);
            }
            /*if($is_yunstorage){
                if($yun_uploader = K::M('storage/upload')->get_yun_uploader()){
                    if(K::M('storage/aliyun')->upload($file, $photo, true)){
                        K::M('helper/msgbox')->add('云存储失败', 601);
                        return false;
                    }
                    K::M('storage/aliyun')->upload($file.'_thumb.jpg', $photo.'_thumb.jpg', true);
                }
            }*/
            if($is_yunstorage){
                if($yun_uploader = K::M('storage/upload')->get_yun_uploader()){
                    if($yun_uploader->upload($file, $photo, true)){//2017-9-30 15:44:08 云储存成功后 存储图片缩率图
                        $yun_uploader->upload($file.'_thumb.jpg', $photo.'_thumb.jpg', true);
                    }else{
                        K::M('helper/msgbox')->add('云存储失败', 601);//云储存失败后报错
                        return false;
                    }
                }
            }
            return $a;
        }
        return false;
    }
	public function item_by_hash($hash)
	{
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE ".$this->field('hash', $hash);
        return $this->db->GetRow($sql);
	}
    public function geturl($photo, $is_thumb=false)
    {
        static $cfg = null;
        if($cfg === null){
            $cfg = K::M('system/config')->get('attach');
        }
        if(!preg_match('/(http|https)\:\/\//i', $photo)){
            $url = $cfg['attachurl'].'/'.$photo;
        }else{
            $url = $photo;
        }

        if(preg_match('/^_(\d+)x(\d+).jpg$/', $is_thumb, $matches) && (defined('__CFG::ATTACH_TYPE') && in_array(__CFG::ATTACH_TYPE, array('aliyun')))){
            return $url.$is_thumb;
        }else{
            return $is_thumb ? ($url.'_thumb.jpg') : $url;
        }

        //return $is_thumb ? ($url.'_thumb.jpg') : $url;
    }
}