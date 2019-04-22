<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Jpush_Device extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            //4.0
            if($SO['from']){
                $filter['from'] = $SO['from'];
                if($SO['from'] == 'member' && $SO['uid']){
                    $filter['uid'] = $SO['uid'];
                }
                if($SO['from'] == 'staff' && $SO['staff_id']){
                    $filter['staff_id'] = $SO['staff_id'];
                }
                if($SO['from'] == 'shop' && $SO['shop_id']){
                    $filter['shop_id'] = $SO['shop_id'];
                }
            }
            if($SO['platform']){
                $filter['platform'] = $SO['platform'];
            }
            if($SO['register_id']){
                $filter['register_id'] = 'LIKE:%'.$SO['register_id'].'%';
            }

            //模糊查询
            if($SO['keywords']){
                $filter['register_id'] = 'LIKE:%'.$SO['keywords'].'%';
            }            
        }
        if($items = K::M('jpush/device')->items($filter, array('device_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $uids = $staff_ids = $shop_ids = array();
            foreach($items as $v){
                if($uid = $v['uid']){
                    $uids[$uid] = $uid;
                }else if($staff_id = $v['staff_id']){
                    $staff_ids[$staff_id] = $staff_id;
                }else if($shop_id = $v['shop_id']){
                    $shop_ids[$shop_id] = $shop_id;
                }
            }
            if($uids){
                $this->pagedata['member_list'] = K::M('member/member')->items_by_ids($uids);
            }
            if($staff_ids){
                $this->pagedata['staff_list'] = K::M('staff/staff')->items_by_ids($staff_ids);
            }
            if($shop_ids){
                $this->pagedata['shop_list'] = K::M('shop/shop')->items_by_ids($shop_ids);
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:jpush/device/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:jpush/device/so.html';
    }

    public function push($device_id=null)
    {
        if($data = $this->checksubmit('data')){
            if(!$content = $data['content']){
                $this->msgbox->add('推送内容不能为空', 211);
                $this->msgbox->respone();
            }else if(!$title = $data['title']){
                $this->msgbox->add('推送标题不能为空', 211);
                $this->msgbox->respone();
            }
            $link_url = $data['link_url'] ? $data['link_url'] : "";
            $link_title = $data['link_title'] ? $data['link_title'] : "";
            $schedule = null;
            if($data['push_time'] &&  strtotime($data['push_time']) > __TIME){
                $schedule = strtotime($data['push_time']);
            }
            if($data['alias']){
                unset($data['register_id'], $data['device_id']);
            }
            if(K::M('jpush/device')->jpush($title, $content, $data, array('link_title'=>$link_title, 'link_url'=>$link_url), $schedule)){
                $this->msgbox->add('推送消息成功');
            }
            /*if(K::M('jpush/device')->jpush($title, $content, $data)){
                $this->msgbox->add('推送消息成功');
            }*/
        }else{
            $device_id = (int)$device_id;
            if($device = K::M('jpush/device')->detail($device_id)){
                $this->pagedata['device'] = $device;
                if($uid = $device['uid']){
                    $this->pagedata['member'] = K::M('member/member')->detail($uid);
                }else if($staff_id = $device['staff_id']){
                    $this->pagedata['staff'] = K::M('staff/staff')->detail($staff_id);
                }else if($shop_id = $device['shop_id']){
                    $this->pagedata['shop'] = K::M('shop/shop')->detail($shop_id);
                }
                try{
                    $info = K::M('jpush/device')->client($device['from'])->device()->getDevices($device['register_id']);
                }catch(Exception $e){
                    $info = K::M('jpush/device')->client($device['from'], 'ltd')->device()->getDevices($device['register_id']);
                }
            }else{
                $this->pagedata['tag_list'] = K::M('jpush/tag')->fetch_all();
            }
            $this->tmpl = "admin:jpush/device/push.html";
        }
    }

    public function edit($device_id=null)
    {
        if(!($device_id = (int)$device_id) && !($device_id = $this->GP('device_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('jpush/device')->detail($device_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($tags = $this->checksubmit('tags')){
            $tag_ids = implode(',', $tags);
            if(K::M('jpush/device')->update($device_id, array('tag_ids'=>$tag_ids))){
                $tag_list = K::M('jpush/tag')->fetch_all();
                if($client = K::M('jpush/device')->client($detail['from'])){
                    $add_tags = $add_tag_ids = $remove_tags = $remove_tag_ids = array();
                    $old_tag_ids = explode(',', $detail['tag_ids']);
                    $add_tag_ids = array_diff($tags, $old_tag_ids);
                    $remove_tag_ids = array_diff($old_tag_ids, $tags);
                    foreach($tag_list as $k=>$v){
                        if(in_array($k, $add_tag_ids)){
                            $add_tags[] = $v['tag'];
                        }else if(in_array($k, $remove_tag_ids)){
                            $remove_tags[] = $v['tag'];
                        }
                    }
                    $client->device()->updateDevice($detail['register_id'], $detail['alias'], null, $add_tags, $remove_tags);
                }
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            if($uid = $detail['uid']){
                $this->pagedata['member'] = K::M('member/member')->detail($uid);
            }else if($staff_id = $detail['staff_id']){
                $this->pagedata['staff'] = K::M('staff/staff')->detail($staff_id);
            }else if($shop_id = $detail['shop_id']){
                $this->pagedata['shop'] = K::M('shop/shop')->detail($shop_id);
            }
            $this->pagedata['detail'] = $detail;
            $this->pagedata['tag_list'] = K::M('jpush/tag')->fetch_all();
            if($detail['register_id']){
                $this->pagedata['device_info'] = K::M('jpush/device')->device_info($detail['register_id'], $detail['from']);
            }
            $this->tmpl = 'admin:jpush/device/edit.html';
        }
    }

    public function delete($device_id=null)
    {
        if($device_id = (int)$device_id){
            if(!$detail = K::M('jpush/device')->detail($device_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('jpush/device')->delete($device_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('device_id')){
            if(K::M('jpush/device')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}