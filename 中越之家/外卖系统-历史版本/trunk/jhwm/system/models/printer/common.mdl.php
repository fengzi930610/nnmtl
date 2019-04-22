<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 15:12
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Printer_Common extends Model {

    public function load(){


        $code = PRINT_TYPE;
        $file = __CFG::DIR."models/printer/{$code}.mdl.php";
        if(!file_exists($file)){
            return false;
        }else{
            require_once($file);
            $class_name = "Mdl_Printer_{$code}";
            return new $class_name();
        }

    }

}
