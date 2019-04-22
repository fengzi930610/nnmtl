<?php
/**
 * Copy	Right Anhuike.com
 * $Id dir.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_IO_Dir
{
	public static function create($dir, $mode=0777, $makeindex=false)
	{
        $dir = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR);       
        return self::mkdir($dir, $mode, $makeindex);
        //在php设置为base_dir的时候会出错改用self::mkdir递归来创建
		if(!file_exists($dir)){
            if(!$arr = explode(DIRECTORY_SEPARATOR, $dir)){
                return false;
            }
            $path = '';
            foreach ($arr as $k=>$v) {
                $path .= $v . DIRECTORY_SEPARATOR;
                if ($k > 0 && !file_exists($path)) {
                    mkdir($path);
                }
            }
        }
        return true;        
	}
    public static function mkdir($dir, $mode = 0777, $makeindex = true){
        if(!is_dir($dir)) {
            if(preg_match('/\.(asp|php|aspx|jsp|cgi)/i', $dir)){
                return false;
            }else if(preg_match('/;/i', $dir)){
                return false;
            }            
            self::mkdir(dirname($dir), $mode, $makeindex);
            @mkdir($dir, $mode);
            if(!empty($makeindex)) {
                @touch($dir.'/index.html'); @chmod($dir.'/index.html', 0777);
            }
        }
        return true;
    }    
	public static function copy($source, $target, $over=false)
	{
        $source = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $source), DIRECTORY_SEPARATOR);
        $target = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $target), DIRECTORY_SEPARATOR);
        if (!is_dir($source)){
            return false;
        }
        if (!file_exists($target)){
            self::create($target);
        }
        if(!$handler = opendir($source)){
            return false;
        }
        while(false !== ($file = readdir($handler))){
            if ($file == '.' || $file == '..') {
                continue;
            }else if (is_dir($source.$file)) {
                self::copy($source.$file, $target.$file, $over);
            } else {
                 K::M('io/file')->copy($source.$file, $target.$file, $over);               
            }
        }
        return closedir($handler);        
	}
	public function move($source, $target, $over=false)
	{
        $source = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $source), DIRECTORY_SEPARATOR);
        $target = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $target), DIRECTORY_SEPARATOR);
        if (!is_dir($source)) {
            return false;
        }
        if (!file_exists($target)) {
            self::create($target);
        }
        if(!$handler = opendir($source)){
            return false;
        }
        while(false !== ($file = readdir($handler))){
            if($file == '.' || $file == '..'){
                continue;
            }
            if(is_dir($source.$file)){
                self::move($source.$file, $target.$file, $over);
            }else{
                K::M('io/file')->move($source.$file, $target.$file, $over);
            }
        }
        closedir($handler);
        return rmdir($source);
	}
	//注：危险指令慎用
	public static function remove($dir)
	{
        $dir = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR);
        if (!is_dir($dir)) {
            return false;
        }
        if(!$handler = opendir($dir)){
            return false;
        }
        while(false !== ($file = readdir($handler))){
            if($file == '.' || $file == '..') {
                continue;
            }
            if(is_dir($dir.$file)){
                self::remove($dir . $file);
            }else{
                K::M('io/file')->remove($dir.'/'.$file);
            }
        }
        closedir($handler);
        return rmdir($dir);
	}
}