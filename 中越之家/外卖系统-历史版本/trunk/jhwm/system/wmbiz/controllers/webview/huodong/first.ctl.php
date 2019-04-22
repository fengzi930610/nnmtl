<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 17:25
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Huodong_First extends Ctl {

    
    public function detail($huodong_id){
        if(!$huodong_id){
            $this->msgbox->add('非法数据请求',217);
        }else if(!$huodong = K::M('waimai/huodongfirst')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已撤销',218);
        }else{
            $this->pagedata['detail'] = $huodong;
            $this->pagedata['config'] = $huodong['config'];
            $this->tmpl = 'webview/huodong/first/detail.html';
        }
    }

    public function close($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在',211);
        }elseif(!$detail = K::M('waimai/huodongfirst')->detail($huodong_id,true)){
            $this->msgbox->add('该活动不存在',212);
        }elseif($detail['closed'] ==1){
            $this->msgbox->add('该活动已撤销',213);
        }else{
            if(K::M('waimai/huodongfirst')->update($huodong_id,array('closed'=>1))){
                $this->msgbox->add('活动撤销成功');
                $this->msgbox->set_data('forward',$this->mklink('webview/huodong/index'));
            }else{
                $this->msgbox->add('活动撤销失败');
            }

        }
    }
    public function history($page=1)
    {//历史记录
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 100;
        $filter['shop_id'] = $this->shop_id;
        if($huodong_id = (int)$this->GP('huodong_id')){
            $filter['huodong_id'] = "<>:".$huodong_id;
        }
        if($items = K::M('waimai/huodongfirst')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'webview/huodong/first/history.html';
    }

}