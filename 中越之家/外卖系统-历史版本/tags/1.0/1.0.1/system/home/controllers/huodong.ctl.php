<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/14
 * Time: 15:06
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Huodong extends Ctl {

    //活动详情
    public function detail($act_id){
        if(!$act_id){
            $this->msgbox->add('活动不存在',210);
        }else if(!$detail = K::M('activity/activity')->detail($act_id)){
            $this->msgbox->add('活动不存在',211);
        }else if($detail['stime']>time()){
            $this->msgbox->add('活动还没有开始',212);
        }else if($detail['ltime']<time()){
            $this->msgbox->add('活动已结束',213);
        }else {
            $this->pagedata['huodong_id'] =$act_id;
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            $filter=array();
            $filter['active_id'] = $act_id;
            if($detail['cate_id']==1){
                $filter['type'] = 4;
            }else if($detail['cate_id']==2){
                $filter['type'] = 2;
            }
            $data_list = K::M('activity/items')->items($filter,null,1,50,$count);
            $this->pagedata['count_shop'] = $count;
            $this->pagedata['huodong'] =$detail;
            $this->pagedata['shop_list'] = $data_list;
            $this->tmpl = 'huodong/detail.html';
        }
    }

    public function actlist($type=1){
        $filtering=$filterend = array();
        //进行中
        $filtering['cate_id'] =$type;
        $filtering['stime'] = '<:'.time();
        $filtering['ltime'] = '>:'.time();

        //已结束
       /* $filterend['cate_id'] =$type;
        $filterend['ltime'] = '<:'.time();*/
        $huodong_ing = K::M('activity/activity')->items($filtering,array('active_id'=>'desc'),1,50,$counting);
        $huodong_end = K::M('activity/activity')->items($filterend,array('active_id'=>'desc'),1,50,$countend);
        $this->pagedata['huodong_ing'] =$huodong_ing;
      /*  $this->pagedata['huodong_end'] =$huodong_end;*/
        $this->tmpl = 'huodong/actlist.html';


    }
    //加载
    public function loadgood($page=1){
        $page = max(1,$page);
        $huodong_id = $this->GP('huodong_id');
        if(!$huodong_id){
            $this->msgbox->add('活动不存在',214);
        }else if(!$detail=K::M('activity/activity')->detail($huodong_id)){
            $this->msgbox->add('活动不存在',215);
        }else{
            /*1 团购 2微店
             * 1:微店商品2：微店商家3：团商品4：团商家*/
            $filter=array();
            $filter['active_id'] = $huodong_id;
            if($detail['cate_id']==1){
                $filter['type'] = 3;
            }else if($detail['cate_id']==2){
                $filter['type'] = 1;
            }
            $items_format= $items = array();
            $ids = array();
            if($product_list = K::M('activity/items')->items($filter,null,$page,10,$count)){
                foreach ($product_list as $v){
                    $ids[]=$v['can_id'];
                }
            }

            $config = $this->system->config->get('site');
            if($filter['type']==3){
                //tuan
               $items=K::M('tuan/tuan')->items_by_ids($ids);
               $url = $config['siteurl'];

            }else if($filter['type']==1){
                //product
                $items=K::M('weidian/product')->items_by_ids($ids);
                $url = $config['mallurl'];
            }

            foreach ($items as $k=>$v){
               if($filter['type']==3){
                   //tuan
                   $items[$k]['url'] = $this->mklink('tuan/product/detail',array($v['tuan_id']),array(),$url);
                   $items[$k]['format_price'] = $v['market_price'];
                   $items[$k]['cp'] =$v['price'];

               }else if($filter['type']==1){
                    //product
                   $items[$k]['url'] = $this->mklink('product/detail',array($v['product_id']),array(),$url);
                   $items[$k]['format_price'] = $v['price'];
                   $items[$k]['cp'] =$v['wei_price'];
               }
            }

            if($count <= 20){
                $loadst = 0;
            }else{
                $loadst = 1;
            }
            $this->pagedata['items'] =$items;
            $this->tmpl = 'huodong/load.html';
            $html = $this->output(true);
            /*var_dump($items);
            print_r($html);exit;*/
            $this->msgbox->set_data('loadst', $loadst);
            $this->msgbox->set_data('html',$html);
            $this->msgbox->json();

        }
    }




}