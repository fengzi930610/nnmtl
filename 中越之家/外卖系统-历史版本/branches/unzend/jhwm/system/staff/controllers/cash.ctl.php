<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 13:50
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Cash extends Ctl_Staff{

    public function index(){
        $this->tmpl = "cash/index.html";

    }

    public function loaditems($page = 1){
        $page = max((int)$page,1);
        $filter = array();
        $filter['staff_id'] = $this->staff_id;
        if($items = K::M('cash/bills')->items($filter,array('bills_id'=>'DESC'),$page,20,$count)){

        }else{
            $items = array();
        }
        if($count <= 20){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'cash/loadlog.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html',$html);
        $this->msgbox->json();



    }



}