<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 18:36
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_File extends Ctl{
    public function uploadimg(){
        if(!$a=K::M('magic/upload')->upload($_FILES['file'])){
            $a = array();
        }
        $this->msgbox->set_data('file',$a);
        $this->msgbox->json();
    }
}