<?php
/**
 * Copy Right Anhuike.com
 * $Id text.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Content_Text extends Model
{
    static public function substr($str, $s=0, $l=50, $suffix='…')
    {
        if(function_exists("mb_substr")){
            $slice =  mb_substr($str, $s, $l, __CFG::CHARSET);
        }else if(function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $s, $l, __CFG::CHARSET);
        }
        if($slice != $str){
            $slice = $slice.$suffix;
        }
        return $slice;
    }
    
    static public function strlen($str)
    {
        if(function_exists('mb_strlen')){
            $count = mb_strlen($str, __CFG::CHARSET);
        }else if(function_exists('iconv_strlen')){
            $count = iconv_strlen($str, __CFG::CHARSET);
        }
        return $count;      
    }
    static public function addslashes($str, $force=false)
    {
        if (!__CFG::GPC || $force) {
            if (is_array($str)) {
                foreach($str as $key => $val) {
                    $str[$key] = &self::addslashes($val, $force);
                }
            } else {
                $str = addslashes(trim($str));
            }
        }
        return $str;    
    }
}