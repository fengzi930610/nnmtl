<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 13:46
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Finance_Staffbills extends Ctl {

    public function index($page = 1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        $filter['from'] = 'paotui';
        $filter['audit'] = 1;
        $filter['closed'] = 0;
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            }

            //4.0模糊查询
            if ($SO['keywords']) {
                $filter[':OR'] = array('name'=>'LIKE:%'.$SO['keywords'].'%', 'mobile'=>'LIKE:%'.$SO['keywords'].'%');
            }
        }

        if($items = K::M('staff/staff')->items($filter, array('staff_id' => 'DESC'), $page, $limit, $count)) {
            $staff_ids = array();
            foreach ($items as $k => $v) {
                $group_ids[] = $v['group_id'];
                $staff_ids[$v['staff_id']] = $v['staff_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            $items_join = K::M('staff/bills')->items_join_by_staff_id(array('staff_id'=>$staff_ids));
            $compltet_order = K::M('order/order')->assessment_group_by_staff_order(array('staff_id'=>array_values($staff_ids),'order_status'=>array(4,8)));
            foreach ($items as $kk => $vv) {
                $items[$kk]['group'] = $group_list[$vv['group_id']];
                $items[$kk]['bills'] = $items_join[$vv['staff_id']]?$items_join[$vv['staff_id']]:array(
                    'amount'=>0,
                    'fee'=>0
                );
                $items[$kk]['compltet'] = $compltet_order[$vv['staff_id']]['orders']?$compltet_order[$vv['staff_id']]['orders']:0;
            }

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/staffbills:index', array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pagers'] = $pager;
        $this->tmpl = "admin:finance/staffbills/index.html";
    }

    public function so(){
        $this->tmpl = "admin:finance/staffbills/so.html";

    }

    public function wso($staff_id){
        $this->pagedata['staff_id'] = $staff_id;
        $this->tmpl = "admin:finance/staffbills/wso.html";
    }

}