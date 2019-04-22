<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Recycle extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        $filter = array('closed'=>1);
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['phone']){
                $filter['phone'] = $SO['phone'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'title'=>"LIKE:%".$SO['keywords']."%",
                    'contact'=>"LIKE:%".$SO['keywords']."%",
                    'phone'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }
        $cates = K::M('waimai/cate')->fetch_all();
        if($items = K::M('waimai/waimai')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
            foreach($items as $k=>$v){
                 $items[$k]['cate_name'] = $this->get_format_cate($v['cate_id'],$cates);
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/recycle/items.html';
    }
    
    
    public function get_format_cate($acte_id,$arr)
    {
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
        $this->tmpl = 'admin:waimai/recycle/so.html';
    }
    
    public function recovery($shop_id=null)
    {//恢复
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('waimai/waimai')->detail($shop_id,true)){
                $this->msgbox->add('你要恢复的商户不存在或已经删除', 211);
            }else{
                if(K::M('waimai/waimai')->batch($shop_id, array('closed'=>0))){
                    $this->msgbox->add('恢复商家成功');
                }
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('waimai/waimai')->batch($ids, array('closed'=>0))){
                $this->msgbox->add('批量恢复商家成功');
            }
        }else{
            $this->msgbox->add('未指定要恢复的商家ID', 401);
        }
    }  
     
    
    public function delete($shop_id=null)
    { //彻底删除
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('waimai/waimai')->detail($shop_id,true)){
                $this->msgbox->add('你要操作的商户不存在或已经删除', 211);
            }else{
                if(K::M('waimai/waimai')->delete($shop_id,true)){
                    K::M('shop/shop')->delete($shop_id,true);
                    $this->msgbox->add('删除商户成功');
                }
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('waimai/waimai')->delete($ids,true)){
                K::M('shop/shop')->delete($ids,true);
                $this->msgbox->add('批量删除商户成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的商户', 401);
        }
    }
    
    
    
}