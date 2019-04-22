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
class Mdl_Storage_Local
{

    function upload(&$attach, $dir, &$fname="")
    {

        K::M('io/dir')->create($dir, 0777, true);
        if(empty($fname)){
            $fname = date('Ymd_').strtoupper(md5(microtime().$attach['tmp_name'].PRI_KEY.rand())).".{$attach['extension']}";
        }
        $file = $dir.$fname;
        if(move_uploaded_file($attach['tmp_name'],$file)){
            return $this->check_safe($file);
        }else if(K::M('io/file')->move($attach['tmp_name'], $file)){
            return $this->check_safe($file);
        }else{
            K::M('helper/error')->add("上传失败",605);
            return false;
        }
    }
}