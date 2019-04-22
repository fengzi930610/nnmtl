<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Apply extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        $filter = array('closed'=>0, 'verify_name'=>array(0, 2), 'last_time'=>">:0"); //verify_name仅作审核外卖商家筛选，不参与接口和其他逻辑判断，审核通过设为1
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['phone']){
               // $filter['phone'] = "LIKE:%".$SO['phone']."%";
                $filter[':SQL'] = " ( o.phone LIKE '%".$SO['phone']."%' OR w.mobile LIKE '%".$SO['phone']."%')";

            }
            if(isset($SO['verify_name']) && $SO['verify_name'] >= 0){
                $filter['verify_name'] = $SO['verify_name'];
            }

            //4.0模糊查询
            if ($SO['keywords']) {
                $filter[':SQL'] = " (o.title LIKE '%".$SO['keywords']."%' OR o.contact LIKE '%".$SO['keywords']."%' OR o.phone LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }

        //echo '<pre>';print_r($SO);die;

        $cates = K::M('waimai/cate')->fetch_all();
        if($items = K::M('waimai/waimai')->items_join_shop($filter, array('verify_name'=>'asc','last_time'=>'asc','shop_id'=>'desc'), $page, $limit, $count)){
            $shop_ids = array();
            foreach($items as $k=>$v){
                 $items[$k]['cate_name'] = $this->get_format_cate($v['cate_id'],$cates);
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach ($items as $k1=>$v1){
                $items[$k1]['shop'] = $shop_list[$v1['shop_id']];
            }

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/apply/items.html';
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
    
    
    public function so()
    {
        $this->tmpl = 'admin:waimai/apply/so.html';
    }
    
    public function detail($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else if($waimai['verify_name'] == 1){
            $this->msgbox->add('该商家已通过审核', 213);
        }else{
            $cates = K::M('waimai/cate')->fetch_all();
            $verify = K::M('waimai/verify')->detail($shop_id);
            $account = K::M('shop/account')->detail($shop_id);
            $waimai['cate_name'] = $this->get_format_cate($waimai['cate_id'],$cates);
            $env = K::M('waimai/env')->items(array('shop_id'=>$shop_id),array('photo_id'=>'asc'));
            $this->pagedata['env'] = $env;
            $this->pagedata['verify'] = $verify;
            $this->pagedata['account'] = $account;
            $this->pagedata['waimai'] = $waimai;

                $filter = array();
                $filter['city_id'] = $waimai['city_id'];
                $filter['closed'] = 0;
                $group_list = K::M('pei/group')->items($filter);
                $this->pagedata['group_list'] = $group_list;



            $this->tmpl = 'admin:waimai/apply/detail.html';
        }
    }
    
    public function audit($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要审核的商家ID', 211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('您要审核的商家不存在或已经删除', 212);
        }elseif(!$status = (int)$this->GP('status')){
            $this->msgbox->add('请选择审核结果', 213);
        }elseif((!$refuse = htmlspecialchars($this->GP('refuse')))&&$status==2){
            $this->msgbox->add('拒绝请填写理由', 214);
        }else if(!$this->GP('group_id') && $status==1){
            $this->msgbox->add('请选择配送站',215);
        } else{
            $data_group_id = $this->GP('group_id')?$this->GP('group_id'):0;
            if(!$verify = K::M('waimai/verify')->detail($shop_id)){
                K::M('waimai/verify')->create(array('shop_id'=>$shop_id));
            }
            if($status == 1){
                $up_data = array('verify_name'=>1,'audit'=>1,'group_id'=>$data_group_id);
                if($data_group_id){
                    $mindata = K::M('waimai/waimai')->get_min_data_by_group($data_group_id);
                    $up_data['min_amount'] = $mindata['min_price'];
                    $up_data['freight'] = $mindata['shipping_fee'];
                }

                K::M('waimai/verify')->update($shop_id, array('verify'=>1));
                K::M('waimai/waimai')->update($shop_id, $up_data);
                $this->msgbox->add('审核通过');
                $this->msgbox->set_data('forward', '?waimai/apply-index.html');
            }else{
                K::M('waimai/verify')->update($shop_id,array('verify'=>2,'refuse'=>$refuse));
                K::M('waimai/waimai')->update($shop_id,array('verify_name'=>2));
                $this->msgbox->add('已成功拒绝');
            }
        }
    }
    
    public function delete($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/waimai')->delete($shop_id,true)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('waimai/waimai')->delete($ids,true)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
    
}