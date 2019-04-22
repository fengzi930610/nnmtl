<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Article_Photo extends Mdl_Table
{   
  
    protected $_table = 'article_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,article_id,title,photo,size,dateline';
    protected $_orderby = array('photo_id'=>'DESC');
    public function update_by_article($article_id, $pids, $force=false)
    {
        if(!$article_id = (int)$article_id){
            return false;
        }else if(!$pids = K::M('verify/check')->ids($pids)){
            return false;
        }
        $where = "photo_id IN ($pids)";
        if(empty($force)){
            $where .= " AND article_id=0";
        }
        if($count = $this->count($where)){
            $this->db->update($this->_table, array('article_id'=>$article_id), $where);
        }
        return $count;
    }
    public function upload($article_id=0, $attach)
    {
        $ym = date('Ym', __CFG::TIME);
        $cfg = K::$system->config->get('attach');
        $dir = $cfg['attachdir'].'photo'.DIRECTORY_SEPARATOR.$ym.DIRECTORY_SEPARATOR;
        if($attach['html5']){
            if(strlen($attach['data'])>2097152){
                $this->msgbox->add('上传的文件不能超过2M', 721);
                return false;
            }
            $ext = $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
            $file = $dir.$fname;
            file_put_contents($file, $attach['data']);
        }else if(!$file = K::M('storage/upload')->upload($attach, $dir, $fname)){
            return false;
        }
        if($file){
            $photo = "photo/{$ym}/{$fname}";
            $article_id = (int)$article_id;
            $a = array('article_id'=>$article_id,'size'=>$attach['size'], 'photo'=>$photo,'title'=>$attach['name']);
            $a['dateline'] = __CFG::TIME;
            if($a['photo_id'] = $this->db->insert($this->_table, $a, true)){
                if($article_id){
                    K::M('article/article')->update_count($article_id, 'photos', 1);
                }
            }
            $a['file'] = $file;
            $size['photo'] = $cfg['article']['photo'] ? $cfg['article']['photo'] : '720';
            $size['thumb'] = $cfg['article']['thumb'] ? $cfg['article']['thumb'] : '200';
            $thumbs = array($size['photo']=>$file, $size['thumb']=>$file.'_thumb.jpg');
            K::M('image/gd')->thumbs($file, $thumbs, false);
            if($cfg['editor']['watermark']){
                $site = K::$system->config->get('site');
                $uname = $attach['uname'] ? $attach['uname'] : $site['title'];
                K::M('image/gd')->watermark($file, $uname);
            }
            return $a;
        }
        return false;
    }
}