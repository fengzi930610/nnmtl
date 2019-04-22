<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27
 * Time: 10:40
 */
class Mdl_Helper_Imglink extends Model {


    public function get_img_link($parmas){
        static $cfg = null;
        if($cfg===null){
            $cfg = $config_attach = K::M('system/config')->get('attach');
        }
        if(!is_array($parmas)){
            return false;
        }else{
            if($parmas['thumb']){
                $img_src = $cfg['attachurl'].'/'.$parmas['photo'].'_thumb.jpg';
            }else{
                $img_src =  $cfg['attachurl'].'/'.$parmas['photo'];
            }
            return $img_src;
        }

    }


}