<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ucenter_Collect extends Ctl_Ucenter
{

    /* 收藏列表
     * @param $type,收藏类型,1:店铺,2:人员
     */

    public function items($type)
    {
        $this->check_login();
        if(!$type){
            $this->msgbox->add('参数不正确', 200);
        }
        else if(!in_array($type, array(1, 2))){
            $this->msgbox->add('参数不正确', 210);
        }
        else{
            $this->pagedata['type'] = $type;
            if($type == 1){
                $this->tmpl = "ucenter/collect/shop.html";
            }
            else{
                $this->tmpl = "ucenter/collect/staff.html";
            }
        }
    }

    public function loaditems($page, $type)
    {

        $this->check_login();
        $type = $this->GP('type');
        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
        $pager['page'] = $page = max((int) $page, 1);
        $filter['uid'] = $this->uid;
        $filter['type'] = $type;
        $filter['status'] = 1;

        $items = K::M("member/collect")->items($filter, $orderby, $page, $limit, $count);

            foreach($items as $k => $v){
                $ids[] = $v['can_id'];
            }
            $lng = $this->request['UxLocation']['lng'];
            $lat = $this->request['UxLocation']['lat'];

            $count_num = count($items);
            if($count_num <= $limit){
                $loadst = 0;
            }
            else{
                $loadst = 1;
            }
            
            if($type == 1){
                if(!$shop_items = K::M('shop/shop')->items(array('shop_id' => $ids))){
                    $shop_items = array();
                }
                foreach($shop_items as $k => $item){
                    $shop_items[$k]['juli'] = K::M('helper/round')->juli($item['lng'], $item['lat'], $lng, $lat);
                    $shop_items[$k]['juli_label'] = K::M('helper/format')->juli($shop_items[$k]['juli']);
                }
                $this->msgbox->set_data('loadst', $loadst);
                $this->pagedata['pager'] = $pager;
                $this->pagedata['items'] = $shop_items;
                $this->tmpl = "ucenter/collect/shop_loaditems.html";
            }
            else{
                if(!$staff_items = K::M('staff/staff')->items(array('staff_id' => $ids))){
                    $staff_items = array();
                }
                $this->msgbox->set_data('loadst', $loadst);
                $this->pagedata['pager'] = $pager;
                $this->pagedata['items'] = $staff_items;
                $this->tmpl = "ucenter/collect/staff_loaditems.html";
            }
            $html = $this->output(true);
            $this->msgbox->set_data('html', $html);
            $this->msgbox->json();

    }

    // 收藏
    public function collect($status, $type, $can_id)
    {
        $this->check_login();
        $data = array();
        $type = (int) $type;
        $status = (int) $status;
        $can_id = (int) $can_id;
        $detail = K::M('member/collect')->find(array('uid' => $this->uid, 'can_id' => $can_id, 'type' => $type));
        if($detail){
            if(K::M('member/collect')->update($detail['collect_id'], array('status' => $status, 'dateline' => __TIME))){
                if($status == 0){
                    $this->msgbox->add('取消收藏成功');
                }
                else{
                    $this->msgbox->add('收藏成功');
                }
            }
        }
        else{
            if($collect_id = K::M('member/collect')->create(array('uid' => $this->uid, 'type' => $type, 'can_id' => $can_id, 'status' => 1, 'dateline' => __TIME))){
                $this->msgbox->add('恭喜您，收藏成功');
            }
        }
    }

}
