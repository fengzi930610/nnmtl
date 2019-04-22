<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: item.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Block_Item extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['block_id']){$filter['block_id'] = $SO['block_id'];}
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
        }
        if($items = K::M('block/item')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $this->pagedata['block_list'] = K::M('block/block')->fetch_all();
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:block/item/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:block/item/so.html';
    }
    public function create($from=null, $itemId=null)
    {
        if(!$from && !($from = $this->GP('from'))){
            $this->msgbox->add('未指定推荐位类型', 211);
        }else if(!($itemId = (int)$itemId) && !($itemId = $this->GP('itemId'))){
            $this->msgbox->add('未指定要推荐的内容', 212);            
        }else if(!$mdl = K::M('block/block')->load_mdl($from)){
            $this->msgbox->add('不支持的数据类型', 213);            
        }else if(!$item = $mdl->detail($itemId)){
            $this->msgbox->add('推送的内容不存在或已经删除', 214);
        }else if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 215);
            }else{
                $data['itemId'] = $itemId;
                //$data['data'] = $item;
                if($attachs = $_FILES['data']){
                    if($photos = $this->__upload($attachs)){
                        $data = $photos + $data;
                    }
                }        
                if($city_ids = $data['city_ids']){
                    unset($data['city_ids']);
                    $res = true;
                    foreach($city_ids as $city_id){
                        $data['city_id'] = $city_id;
                        if(!K::M('block/item')->create($data)){
                            $res = false;
                            break;
                        }
                    }
                    if($res){
                        K::M('block/item')->flush($data['block_id']);
                        $this->msgbox->add('推荐内容成功');
                        if(strpos($this->request['uri'], 'block/item-plush') !== false){
                            $this->msgbox->set_data('forward', '?block/item-index.html');
                        }
                    }                 
                }
            } 
        }else{
            $pager['itemId'] = $itemId;
            $pager['from'] = $from;
            $this->pagedata['item'] = K::M('block/block')->format_item($item, $from);
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:block/item/create.html';
        }
    }
    public function edit($item_id=null)
    {
        if(!($item_id = (int)$item_id) && !($item_id = (int)$this->GP('item_id'))){
            $this->msgbox->add('未指宁修改的内容ID', 211);
        }else if(!$detail = K::M('block/item')->detail($item_id)){
            $this->msgbox->add('你要修改的内容不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 213);
            }else if(!$block = K::M('block/block')->detail($detail['block_id'])){
                $this->msgbox->add('您要修改的推荐位不存在或已经删除', 214);
            }else{
                /*
                if($mdl = K::M('block/block')->load_mdl($block['from'])){
                    if($items = $mdl->items_by_ids($detail['itemId'])){
                        $data['data'] = $items[$detail['itemId']];
                    }
                }
                */
               if($attachs = $_FILES['data']){
                    if($photos = $this->__upload($attachs)){
                        $data = $photos + $data;
                    }
                }
                if(K::M('block/item')->update($item_id, $data)){
                    K::M('block/item')->flush($block['block_id']);
                    $this->msgbox->add('修改推荐内容成功');
                }
            } 
        }else{
            $this->pagedata['block'] = K::M('block/block')->detail($detail['block_id']);
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:block/item/edit.html';
        }
    }
    public function update()
    {
        /*
        if(!$block_id = (int)$this->GP('block_id')){
            $this->msgbox->add('未指定对应的推荐位ID', 211);
        }else 
        */
        if($orderby = $this->GP('orderby')){
            $obj = K::M('block/item');
            foreach((array)$orderby as $item_id=>$order){
                $item_id = (int)$item_id;
                $order = (int)$order;
                $obj->update($item_id, array('orderby'=>$order));
            }
            $obj->flush($block_id);
            $this->msgbox->add('更新数据成功');
        }        
    }
    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('block/item')->delete($pk)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('item_id')){
            if(K::M('block/item')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    public function push($from=null, $itemId=null)
    {
        $this->create($from, $itemId);
    }
    public function batch($from=null, $itemIds=null)
    {
        if(!$from && !($from = $this->GP('from'))){
            $this->msgbox->add('未指定推荐位类型', 211);
        }else if(!($itemIds = $itemIds) && !($itemIds = $this->GP('itemIds'))){
            $this->msgbox->add('未指定要推荐的内容', 212);            
        }else if(!$mdl = K::M('block/block')->load_mdl($from)){
            $this->msgbox->add('不支持的数据类型', 213);
        }else if(!$items = $mdl->items_by_ids($itemIds)){
            $this->msgbox->add('推送的内容不存在或已经删除', 214);
        }else if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 215);
            }else if(!$city_ids = $data['city_ids']){
                $this->msgbox->add('未指定要推荐到的城市', 216);
            }else{
                $total = count($items);
                $count = 0;
                $success = false;
                $oBlock = K::M('block/block');
                foreach($items as $item){
                    $item = $oBlock->format_item($item, $from);
                    $data['itemId'] = $item['itemId'];
                    $data['title'] = $item['title'];
                    $data['link'] = $item['link'];
                    $data['thumb'] = $item['thumb'];
                    $data['orderby'] = 50;
                    foreach($city_ids as $city_id){
                        $data['city_id'] = $city_id;
                        if($item_id = K::M('block/item')->create($data)){
                            $success = true;
                            $count ++;
                        }
                    }
                }
                if($success){
                    $this->msgbox->add("批量推荐,共:{$total},成功:{$count}");
                }else{
                    $this->msgbox->add("批量推荐内容失败", 411);
                }
            } 
        }else{
            $oBlock = K::M('block/block');
            foreach($items as $k=>$v){
                $items[$k] = $oBlock->format_item($v, $from);
            }            
            $pager['itemIds'] = $itemIds;
            $pager['from'] = $from;
            $pager['total'] = count($items);
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:block/item/batch.html';
        }        
    }
    protected function __upload($data, $item=array())
    {
        $attachs = $photos = array();
        if($data){
            foreach($data as $k=>$v){
                foreach($v as $kk=>$vv){
                    $attachs[$kk][$k] = $vv;
                }
            }
            $upload = K::M('magic/upload');
            foreach($attachs as $k=>$attach){
                if($attach['error'] == UPLOAD_ERR_OK){
                    if($a = $upload->upload($attach, 'adv', $item[$k])){
                        $photos[$k] = $a['photo'];
                    }
                }
            }
        }
        return $photos;
    }
    protected function __upload2($item=null)
    {
        $photos = array();
        if($_FILES['data']){
            foreach($_FILES['data'] as $k=>$v){
                foreach($v as $kk=>$vv){
                    $attachs[$kk][$k] = $vv;
                }
            }
            $upload = K::M('magic/upload');
            foreach($attachs as $k=>$attach){
                if($attach['error'] == UPLOAD_ERR_OK){
                    //if($a = $upload->upload($attach, 'block', $item[$k])){
                    if($a = $upload->upload($attach, 'block')){
                        $photos[$k] = $a['photo'];
                    }
                }
            }
        }
        return $photos;      
    }
}