<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: upload.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
/**
 * 上传类只支持图片格式
 *
 * 601:上传失败
 * 602:不支持的文件扩展名
 * 603:不支持的文件类型
 * 604:上传的文件太大
 * 605:
 */
class Mdl_Storage_Upload
{
    public $message = '';
    public $code = '200';
    public $succeed = true;
    private $_allow_exts = array('gif','jpg', 'png','jpeg','bmp');
    private $_allow_zip_exts = array('zip', 'tar', 'rar');
    private $_allow_file_exts = array('doc','docx','txt','pdf', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx','zip',  'tar', 'rar','gif','jpg', 'png','jpeg','bmp','mp3','mp4');
    private $_allow_type = array('image/gif', 'image/jpeg','image/pjpeg', 'image/png', 'image/x-png', 'image/bmp','application/octet-stream','audio/mpeg','image/jpg');
    private $_check_allow_type = true;
    private $_allow_max_size = 2097152;
    protected $_uploader = null;
    protected $_yunstorage = false;
    protected $_remove_local = false; //如果用云存储是否删除本地文件

    public function __construct($system)
    {
        $cfg = $system->config->get('attach');
        if(is_numeric($cfg['allow_size'])){
            $this->_allow_max_size = $cfg['allow_size'] * 1024;
        }
//屏蔽后台设置附件上传类型,防止后台帐号泄露导致上传漏洞
//        if($cfg['allow_exts']){
//            if($_allow_exts = explode(',', $cfg['allow_exts'])){
//                $this->_allow_exts = $_allow_exts;
//            }
//        }
//        if($cfg['allow_exts_zip']){
//            if($_allow_zip_exts = explode(',', $cfg['allow_exts'])){
//                $this->_allow_zip_exts = $_allow_zip_exts;
//            }
//        }
//        if($cfg['allow_exts_file']){
//            if($_allow_file_exts = explode(',', $cfg['allow_exts'])){
//                $this->_allow_file_exts = $_allow_file_exts;
//            }
//        }
        if(defined('__CFG::ATTACH_TYPE') && in_array(__CFG::ATTACH_TYPE, array('aliyun', 'qiniu'))){
            $this->_uploader = K::M('storage/'.__CFG::ATTACH_TYPE);
            $this->_yunstorage = true;
        }
    }

    public function upload_by_data($data, $dir, &$fname='', $thumbs=array())
    {
        if(empty($fname)){
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".png";
        }
        $file = $dir.$fname;
        K::M('io/dir')->create($dir, 0777, true);
        $ret = $file;
        if(!file_put_contents($file, $data)){
            K::M('helper/msgbox')->add('保存图片数据失败',501);
            $ret = false;
        }
        if($ret){
            $file_list = array();
            if($thumbs){
                K::M('image/gd')->thumbs($file, (array)$thumbs, false);
                $file_list = array_values($thumbs);
            }
            if($this->_yunstorage){
                if(!in_array($file, $file_list)){
                    $file_list[] = $file;
                }
                foreach($file_list as $v){
                    try {
                        if(!$this->_uploader->upload($v, null, $this->_remove_local)){
                            K::M('helper/msgbox')->add("云存储失败", 605);
                            K::M('io/file')->remove($v);
                            $ret = false;
                        }
                    }catch(\OSS\Core\OssException $e){
                        K::M('helper/msgbox')->add($e->getErrorMessage(), 605);
                    }

                }
            }
        }
        return $ret;
    }

    public function upload(&$attach, $dir, &$fname="", $thumbs=array())
    {
        if(!$this->_check($attach)){
            return false;
        }
        if(empty($fname)){
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        }
        $file = $dir.$fname;
        K::M('io/dir')->create($dir, 0777, true);
        if(move_uploaded_file($attach['tmp_name'], $file)){
            $ret = $this->check_safe($file);
        }else if(K::M('io/file')->move($attach['tmp_name'], $file)){
            $ret = $this->check_safe($file);
        }else{
            K::M('helper/msgbox')->add("上传文件失败",605);
            $ret = false;
        }
        if($ret){
            $file_list = array();
            if($thumbs){
                K::M('image/gd')->thumbs($file, (array)$thumbs, false);
                $file_list = array_values($thumbs);
            }
            if($this->_yunstorage){
                if(!in_array($file, $file_list)){
                    $file_list[] = $file;
                }
                foreach($file_list as $v){
                    if(!$this->_uploader->upload($v, null, $this->_remove_local)){
                        K::M('helper/msgbox')->add("云存储失败", 605);
                        K::M('io/file')->remove($v);
                        $ret = false;
                    }
                }
            }
        }
        return $ret;
    }

    public function zip($attach, $dir, &$fname='')
    {
        $_allow_exts = $this->_allow_exts;
        $_check_allow_type = $this->_check_allow_type;
        $this->set_allow_exts($this->_allow_zip_exts);
        $this->_check_allow_type = false;
        if(!$this->_check($attach)){
            return false;
        }
        if(empty($fname)){
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        }
        $file = $dir.$fname;
        K::M('io/dir')->create($dir, 0777, true);
        if(move_uploaded_file($attach['tmp_name'], $file)){
            $ret = $file;
        }else if(K::M('io/file')->move($attach['tmp_name'], $file)){
            $ret = $file;
        }else{
            K::M('helper/error')->add("上传文件失败",605);
            $ret = false;
        }
        if($ret && $this->_yunstorage){
            if(!$ret = $this->_uploader->upload($file, null, $this->_remove_local)){
                K::M('io/file')->remove($file);
                K::M('helper/msgbox')->add("云存储失败", 605);
            }
        }
        $this->_allow_exts = $_allow_exts;
        $this->_check_allow_type = $_check_allow_type;
        return $ret;
    }

    public function file($attach, $dir, &$fname='')
    {
        $_allow_exts = $this->_allow_exts;
        $_check_allow_type = $this->_check_allow_type;
        $this->set_allow_exts($this->_allow_file_exts);
        $this->_check_allow_type = false;
        if(!$this->_check($attach)){
            return false;
        }
        if(empty($fname)){
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        }
        $file = $dir.$fname;
        K::M('io/dir')->create($dir, 0777, true);
        if(move_uploaded_file($attach['tmp_name'], $file)){
            $ret = $file;
        }else if(K::M('io/file')->move($attach['tmp_name'], $file)){
            $ret = $file;
        }else{
            K::M('helper/msgbox')->add("上传文件失败",605);
            $ret = false;
        }
        if($ret && $this->_yunstorage){
            if(!$ret = $this->_uploader->upload($file, null, $this->_remove_local)){
                K::M('io/file')->remove($file);
                K::M('helper/msgbox')->add("云存储失败", 605);
            }
        }
        $this->_allow_exts = $_allow_exts;
        $this->_check_allow_type = $_check_allow_type;
        return $ret;
    }

    public function set_max_size($size)
    {
        if(!is_numeric($size) || $size>2097152 || $size< 1){
            return false;
        }
        $this->_allow_max_size = $size;
    }
    
    public function set_allow_exts($ext)
    {
        $this->_allow_exts = $ext;
    }

    public function set_yun_storage($bool=true)
    {
        $this->_yunstorage = $bool;
    }

    public function get_yun_storage()
    {
        return $this->_yunstorage;
    }

    public function get_yun_uploader()
    {
        return $this->_uploader;
    }

    public function check_safe($file)
    {
        if($data = @file_get_contents($file)){
            if(preg_match("/\<(\?php|\<\? )/is", $data)){
                K::M('msgbox/msgbox')->add('不是安全的图片', 999);
                K::M('io/file')->remove($file);
                return false;
            }
        }
        return $file;
    }

    public function check_attach(&$attach, $type=null)
    {
        if($type == 'zip'){
            $this->set_allow_exts($this->_allow_zip_exts);
            $this->_check_allow_type = false;
        }elseif($type == 'file'){
            $this->set_allow_exts($this->_allow_file_exts);
            $this->_check_allow_type = false;
        }else{
            $this->set_allow_exts($this->_allow_exts);
            $this->_check_allow_type = true;
        }
        return $this->_check($attach);
    }

    private function _check(&$attach)
    {
        if($attach['error'] != UPLOAD_ERR_OK || empty($attach['size'])){
            K::M('magic/msgbox')->add("上传失败".$attach['error'], 601);
            return false;
        }
        $attach['extension'] = strtolower(K::M('io/file')->extension($attach['name']));
        $attach['type'] = strtolower($attach['type']);
        if(!in_array($attach['extension'], $this->_allow_exts)){
            K::M('helper/msgbox')->add("不支持的文件扩展名", 602);
        }else if($this->_check_allow_type && !in_array($attach['type'],$this->_allow_type)){
            K::M('helper/msgbox')->add("不支持的文件类型", 603);
        }else if($attach['size']>$this->_allow_max_size){
            K::M('helper/msgbox')->add("上传的文件太大", 604);
        }else{
            return true;
        }
        return false;
    }
}