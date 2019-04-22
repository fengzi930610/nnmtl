<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: file.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_IO_File
{
	static public function create($file,$over=false)
	{
        if(file_exists($file) && !$over){
            return false;
        }else if(file_exists($file) && $over){
            self::remove($file);
        }
		K::M('io/dir')->create(dirname($file));
        touch($file);
        return true;		
	}
	static public function copy($source,$target,$over=false)
	{
        if (!file_exists($source)) {
            return false;
        }
        if (file_exists($target) && $over==false) {
            return false;
        } elseif (file_exists($target) && $over==true) {
            self::remove($target);
        }
		K::M('io/dir')->create(dirname($target));
        copy($source, $target);
        return true;		
	}
	static public function move($source,$target,$over=false)
	{
        if(!file_exists($source)){
            return false;
        }
        if(file_exists($target) && $over = false){
            return false;
        }else if(file_exists($target) && $over = true) {
            self::remove($target);
        }
		K::M('io/dir')->create(dirname($target));
        @rename($source, $target);
        return true;		
	}
	static public function remove($file)
	{
        if (file_exists($file)) {
            @unlink($file);
            return true;
        } else {
            return false;
        }		
	}
	static function extension($file)
	{
		return trim(substr(strrchr($file, '.'), 1, 10));
	}
}