<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Huodong_Discount extends Ctl
{

    public function detail($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$detail = K::M('waimai/huodongdiscount')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已被删除！',212);
        }else{
            $pros = K::M('waimai/product')->items_by_ids($detail['products']);
            $disc_pros = K::M('waimai/discountproduct')->items(array('huodong_id'=>$huodong_id,'product_id'=>$detail['products']),array('product_id'=>'desc'));
            $products = array();
            if($pros && $disc_pros){               
                foreach ($disc_pros as $k => $v) {
                    if($p = $pros[$v['product_id']]){                       
                        if($detail['discount_type']){
                            $disc_price = $p['price'] - $v['discount_value'];
                            $disc_label = '减价：￥'.$v['discount_value'];
                        }else{
                            $disc_price = $p['price']*$v['discount_value'];
                            $v['discount_value'] = sprintf("%.1f",$v['discount_value']*10);
                            $disc_label = '打折：'.$v['discount_value'].'折';
                        }
                        $val = array_merge($p,$v);
                        $val['disc_price'] = $disc_price;
                        $val['disc_label'] = $disc_label;
                        $products[$v['product_id']] = $val;
                    }
                }
            }
            $this->pagedata['products'] = $products;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['huodong_id'] = $huodong_id;
            $this->tmpl = 'webview/huodong/discount/detail.html';
        }     
    }

    public function history($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 10;
        $filter['shop_id'] = $this->shop_id;
        
        if($items = K::M('waimai/huodongdiscount')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'webview/huodong/discount/history.html';
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }elseif(!$detail = K::M('waimai/huodongdiscount')->detail($huodong_id)){
            $this->msgbox->add('撤销的活动不存在或已撤销！',212);
        }else{
            if(K::M('waimai/huodongdiscount')->update($huodong_id,array('closed'=>1))){
                if(!K::M('waimai/waimai')->update_huodong_ltime($this->shop_id,'discount')){
                    K::M('waimai/huodongdiscount')->update($huodong_id,array('closed'=>0));
                    $this->msgbox->add('撤销失败!!',214);
                }else{
                    $this->msgbox->add('撤销成功!');
                    //$this->msgbox->set_data('forward',$this->mklink('webview/huodong/index'));
                }
            }else{
                $this->msgbox->add('撤销失败!',215);
            }
        }
    }

    
}