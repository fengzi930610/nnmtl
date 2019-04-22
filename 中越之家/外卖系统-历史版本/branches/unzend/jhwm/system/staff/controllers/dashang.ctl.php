<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 11:03
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Dashang extends Ctl {

    public function index(){
        $this->pagedata['staff_id'] = $this->staff_id;
        $this->tmpl = "dashang/index.html";
    }

    public function load_reward($page){
        $page = max((int)$page,1);
        $limit = 20;
        if($items = K::M('order/order')->items(array('staff_id'=>$this->staff_id,'from'=>'reward','order_status'=>8,'pay_status'=>1),array('order_id'=>'DESC'),$page,$limit,$count)){
            $uids = array();
            foreach ($items as $k=>$v){
                $uids[$v['uid']] = $v['uid'];
            }
            $member_list = K::M('member/member')->items_by_ids($uids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $member_list[$vv['uid']];
            }
        }
        if($count <= $limit){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $items;
        $this->tmpl = 'dashang/load_reward.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();

    }


}