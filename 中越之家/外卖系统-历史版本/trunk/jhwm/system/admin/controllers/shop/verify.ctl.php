<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Verify extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['id_name']){$filter['id_name'] = "LIKE:%".$SO['id_name']."%";}
            if($SO['id_number']){$filter['id_number'] = $SO['id_number'];}
            if($SO['yz_number']){$filter['yz_number'] = $SO['yz_number'];}
            if($SO['cy_number']){$filter['cy_number'] = $SO['cy_number'];}
            if(is_array($SO['verify_time'])){if($SO['verify_time'][0] && $SO['verify_time'][1]){$a = strtotime($SO['verify_time'][0]); $b = strtotime($SO['verify_time'][1])+86400;$filter['verify_time'] = $a."~".$b;}}
        }
        if($items = K::M('shop/verify')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $shop_ids = array();
            foreach($items as $v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            if($shop_ids){
                $this->pagedata['shop_list'] = K::M('shop/shop')->items_by_ids($shop_ids);
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/verify/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:shop/verify/so.html';
    }
    public function detail($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('shop/verify')->detail($shop_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['shop'] = K::M('shop/shop')->detail($shop_id);
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/verify/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            
            if($shop_id = K::M('shop/verify')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/verify-index.html');
            } 
        }else{
           $this->tmpl = 'admin:shop/verify/create.html';
        }
    }
    public function edit($shop_id=null)
    {
        if(!($shop_id = (int)$shop_id) && !($shop_id = $this->GP('shop_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('shop/verify')->detail($shop_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if($_FILES['data']){
                foreach($_FILES['data'] as $k=>$v){
                    foreach($v as $kk=>$vv){
                        $attachs[$kk][$k] = $vv;
                    }
                }
                $upload = K::M('magic/upload');
                foreach($attachs as $k=>$attach){
                    if($attach['error'] == UPLOAD_ERR_OK){
                        if($a = $upload->upload($attach, 'shop')){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['verify_time'] = __TIME;
            if(K::M('shop/verify')->update($shop_id, $data)){
                if(isset($data['verify'])){
                    if($data['verify']){
                        $a = array('verify_name'=>1, 'audit'=>1);
                    }else{
                        $a = array('verify_name'=>0);
                    }
                    K::M('shop/shop')->update($shop_id, $a);
                }
                // $shop_verify = K::M('shop/verify')->detail($shop_id);
                // if($shop_verify['verify_dianzhu']==1 && $shop_verify['verify_yyzz']==1 && $shop_verify['verify_cy']==1) {
                //     K::M('shop/verify')->update($shop_id, array('refuse'=>'','verify'=>1));
                // }
                $this->msgbox->add('修改内容成功');
            }  
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:shop/verify/edit.html';
        }
    }
    public function doaudit($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(K::M('shop/verify')->batch($shop_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('shop/verify')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('shop/verify')->detail($shop_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('shop/verify')->delete($shop_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('shop/verify')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  
}