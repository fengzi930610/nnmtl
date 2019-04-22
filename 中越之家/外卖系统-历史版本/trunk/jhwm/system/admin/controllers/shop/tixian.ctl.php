<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Tixian extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('shop/tixian')->items($filter, array('tixian_id'=>'DESC', 'status'=>'ASC'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $shop_ids = array();
            foreach($items as $k=>$v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            if($shop_ids){
                $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            }
        }
    
        $this->pagedata['shop_list'] = $shop_list;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/tixian/items.html';
    }
    public function doaudit($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(K::M('shop/tixian')->update($tixian_id, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('通过审核成功');
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('shop/tixian')->update($ids, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/tixian/so.html';
    }
    public function reason($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = (int)$this->GP('tixian_id'))){
            $this->msgbox->add('未指要操作的体现ID', 211);
        }else if(!$detail = K::M('shop/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现不存在或已经删除', 212);
        }else if(!empty($detail['status'])){
            $this->msgbox->add('提现申请状态不可退回', 213);
        }
        if($this->checksubmit()){
            if($reason_content = $this->checksubmit('reason')){
                if(K::M('shop/tixian')->update($tixian_id, array('status'=>2, 'reason'=>$reason_content, 'updatetime'=>__TIME))){
                    K::M('shop/shop')->update_money($detail['shop_id'], $detail['money'], $reason_content.',退回到商户余额');
                    $this->msgbox->add('退回提现申请成功');
                }
        } else {
                $this->msgbox->add('退回理由不可为空',220);

        }
             } else {
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/tixian/reason.html';
            }

    }  
}