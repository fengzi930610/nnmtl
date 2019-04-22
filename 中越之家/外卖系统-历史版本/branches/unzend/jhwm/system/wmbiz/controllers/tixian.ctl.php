<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6
 * Time: 18:31
 */
class Ctl_Tixian extends Ctl {

    public function index($page = 1)
    {
        //0:待审核 1:已通过 2:以拒绝 3:打款完成 4：已完成
        $page = max((int)$page,1);
        $limit = 20;
      /*  [status] => 0
    [stime] => 2017-05-01
    [ltime] => 2017-05-11*/
        $filter = array();
        $filter['shop_id'] = $this->shop_id;
        if($so = $this->checksubmit('SO')){
            $this->pagedata['so'] = $so;
           if($so['status']==1){
               $filter['status'] = 0;
           }else if($so['status']==2){
               $filter['status'] = 1;
           }else if($so['status']==3){
               $filter['status'] = 2;
           }else if($so['status']==4){
               $filter['status'] = 3;
           }else if($so['status']==5){
               $filter['status'] = 4;
           }
            if($so['stime']&&!$so['ltime']){
                $filter['dateline'] = '>:'.strtotime($so['stime']);
            }
            if(!$so['stime']&&$so['ltime']){
                $filter['dateline'] = '<:'.strtotime($so['ltime']);
            }
            if($so['stime']&&$so['ltime']){
                $filter['dateline'] = strtotime($so['stime']).'~'.strtotime($so['ltime']);
            }
        }
        if($items = K::M('shop/tixian')->items($filter,array('tixian_id'=>'DESC'),$page,$limit,$count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('tixian/index', array('{page}')), array('SO' => $so));
        }else{
            $items = array();
        }
       /* echo '<pre>';
        print_r($items);exit;*/
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'tixian/index.html';
    }
    public function detail($tixian_id){
        if(!$tixian_id){
            $this->msgbox->add('未找到数据',210);
        }else if(!$tixian=K::M('shop/tixian')->detail($tixian_id)){
            $this->msgbox->add('未找到数据',211);
        }else if($tixian['shop_id']!=$this->shop_id){
            $this->msgbox->add('不可查看其他商铺的提现记录',212);
        }else{
            $this->pagedata['tixian'] = $tixian;
            $this->tmpl = 'tixian/detail.html';
        }

    }
}