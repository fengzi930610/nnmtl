<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: item.ctl.php 6080 2014-08-13 15:20:01Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Adv_Item extends Ctl
{
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['allcity'] != 'on' && $SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['adv_id']){$filter['adv_id'] = $SO['adv_id'];}
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
        }
        if($items = K::M('adv/item')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $this->pagedata['adv_list'] = K::M('adv/adv')->fetch_all();
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:adv/item/index.html';
    }
    
    public function create($adv_id=null)
    {
        if(!($adv_id = intval($adv_id)) && !($adv_id = intval($this->GP('adv_id')))){
            $this->msgbox->add('未指定对应的广告位ID', 211);
        }else if(!$adv = K::M('adv/adv')->adv($adv_id)){
             $this->msgbox->add('广告位不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){      
           if($attachs = $_FILES['data']){
                if($photos = $this->__upload($attachs)){
                    $data = $data + $photos;
                }
            }
            $data['adv_id'] = $adv_id;
            $data['city_id'] = 0;
            if($item_id = K::M('adv/item')->create($data)){
                $this->msgbox->add('添加广告成功');
                $this->msgbox->set_data('forward', '?adv/adv-detail-'.$adv_id.'.html');
            }
            // 不启用多城市分城市广告功能     
            // if(empty($data['city_ids'])){
            //     $this->msgbox->add('广告没有绑定到对应的城市', 214);
            // }else{
            //    if($attachs = $_FILES['data']){
            //         if($photos = $this->__upload($attachs)){
            //             $data = $data + $photos;
            //         }
            //     }
            //     $data['adv_id'] = $adv_id;
            //     if($city_ids = $data['city_ids']){
            //         unset($data['city_ids']);
            //         $res = true;
            //         foreach($city_ids as $city_id){
            //             $data['city_id'] = $city_id;
            //             if(!$item_id = K::M('adv/item')->create($data)){
            //                 $res = false;
            //                 break;
            //             }
            //         }
            //         if($res){
            //             $this->msgbox->add('添加广告成功');
            //             $this->msgbox->set_data('forward', '?adv/adv-detail-'.$adv_id.'.html');
            //         }                 
            //     }
            // } 
        }else{
            $this->pagedata['adv'] = $adv;
            $this->tmpl = 'admin:adv/item/create.html';
        }
    }
    public function so()
    {
        $this->tmpl = 'admin:adv/item/so.html';
    }
    public function edit($item_id=null)
    {
        if(!($item_id = intval($item_id)) && !($item_id = intval($this->GP('item_id')))){
            $this->msgbox->add('未指要修改广告的ID', 211);
        }else if(!$detail = K::M('adv/item')->item($item_id)){
            $this->msgbox->add('你要修改的广告不存在或已经删除', 212);
        }else if(!$adv = K::M('adv/adv')->adv($detail['adv_id'])){
            $this->msgbox->add('所在的广告位不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
               if($attachs = $_FILES['data']){
                    if($photos = $this->__upload($attachs, $detail)){
                        $data = $data + $photos;
                    }
                }
                if(K::M('adv/item')->update($item_id, $data)){
                    $this->msgbox->add('修改广告成功');
                    $this->msgbox->set_data('forward', '?adv/adv-detail-'.$detail['adv_id'].'.html');
                }
            } 
        }else{
            $this->pagedata['adv'] = $adv;
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:adv/item/edit.html';
        }
    }
    public function update()
    {
        if(!$adv_id = (int)$this->GP('adv_id')){
            $this->msgbox->add('未指定对应的广告位ID', 211);
        }else if($orderby = $this->GP('orderby')){
            $obj = K::M('adv/item');
            foreach((array)$orderby as $item_id=>$order){
                $item_id = (int)$item_id;
                $order = (int)$order;
                $obj->update($item_id, array('orderby'=>$order));
            }
            $this->msgbox->add('更新数据成功');
        }
    }
    public function doaudit($item_id=null)
    {
        if($item_id = (int)$item_id){
            if(K::M('adv/item')->batch($item_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('item_id')){
            if(K::M('adv/item')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('adv/item')->delete($pk, true)){
                $this->msgbox->add('删除广告成功');
            }
        }else if($pks = $this->GP('item_id')){
            if(K::M('adv/item')->delete($pks, true)){
                $this->msgbox->add('批量删除广告成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的广告ID', 401);
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
                    if($a = $upload->upload($attach, 'adv', null)){
                        $photos[$k] = $a['photo'];
                    }
                }
            }
        }
        return $photos;
    }
}