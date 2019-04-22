<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 10:04
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Bills extends Ctl {

    public function index($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array('audit'=>1,'closed'=>0,'verify_name'=>1);
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['phone']){
                $filter['phone'] = "LIKE:%".$SO['phone']."%";
            }
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }

            //4.0模糊查询
            if ($SO['keywords']) {
                $filter[':SQL'] = " (o.title LIKE '%".$SO['keywords']."%' OR o.contact LIKE '%".$SO['keywords']."%' OR o.phone LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        $cates = K::M('waimai/cate')->fetch_all();
        $group_ids = array();
        $shop_ids = array();
        if($items = K::M('waimai/waimai')->items_join_shop($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
            foreach($items as $k=>$v){
                $items[$k]['cate_name'] = $this->get_format_cate($v['cate_id'],$cates);
                $group_ids[] = $v['group_id'];
                $shop_ids[] = $v['shop_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            //$shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach($items as $kk=>$vv){
                $items[$kk]['group'] = $group_list[$vv['group_id']];
                //$items[$kk]['shop_info'] = $shop_list[$vv['shop_id']];
            }
            $join_sum = K::M('waimai/bills')->items_join_by_shop_id(array('shop_id'=>$shop_ids));
            foreach($items as $k=>$v){
                $items[$k]['bills'] = $join_sum[$v['shop_id']] ?$join_sum[$v['shop_id']] :array('amount'=>0,'fee'=>0);
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/bills/index.html';




    }

    public function get_format_cate($acte_id,$arr){
        $name = '';
        if($arr[$acte_id]['parent_id']==0){
            $name = $arr[$acte_id]['title'];
        }else{
            $act_id = $arr[$acte_id]['parent_id'];
            if($arr[$act_id]){
                $name=$arr[$act_id]['title'].'-'.$arr[$acte_id]['title'];
                $level_3 = $arr[$act_id]['parent_id'];
            }
            if($arr[$level_3]){
                $name = $arr[$level_3]['title'].'-'.$arr[$act_id]['title'].'-'.$arr[$acte_id]['title'];
            }

        }
        return $name;
    }

    public function so(){
        $this->tmpl = 'admin:waimai/bills/so.html';

    }


}
