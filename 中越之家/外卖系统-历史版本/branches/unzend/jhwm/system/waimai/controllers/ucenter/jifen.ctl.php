<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/5
 * Time: 17:52
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Jifen extends Ctl_Ucenter {
    public function index(){
        $this->tmpl = 'ucenter/jifen.html';
    }
    
    public function loadjifen($page=1){
        $page = max(1,$page);
        $filter = array();
        $filter['uid'] = $this->uid;
        $filter['type'] = 'jifen';
        if(!$item = K::M('member/log')->items($filter,array('log_id'=>'desc'),$page,20,$count)){
            $item = array();
        }

        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['item'] =$item;
        $this->tmpl='ucenter/loadjifen.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();
        
    }
    
}