<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 11:07
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Activity extends Ctl {
    
     //活动列表
    public function index($page=1){
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('waimai/huodong')->items($filter, null, $page, $limit, $count)){
            foreach($items as $k=>$v){
                $items[$k]['adv_link'] = str_replace('?','',K::M('helper/link')->mklink('huodong/detail',array($v['huodong_id']),array(),'waimai'));
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        
        
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl='admin:waimai/activity/items.html';
    }
    
    public function so(){
        $this->tmpl = 'admin:waimai/activity/so.html';
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
                        if($a = $upload->upload($attach)){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['stime'] = strtotime($data['stime']);
            $data['ltime'] = strtotime($data['ltime']) + 86399;
            $data['dateline'] = __TIME;
            $data['clientip'] = __IP;
            if($huodong_id = K::M('waimai/huodong')->create($data)){
                $this->msgbox->add('添加内容成功');
            } 
        }else{
            $this->tmpl = 'admin:waimai/activity/create.html';
        }
        
    }
    
    public function edit($huodong_id=null)
    {
        if(!($huodong_id = (int)$huodong_id) && !($huodong_id = $this->GP('huodong_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/huodong')->detail($huodong_id)){
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
                        if($a = $upload->upload($attach)){
                            $data[$k] = $a['photo'];
                        }
                    }
                }
            }
            $data['stime'] = strtotime($data['stime']);
            $data['ltime'] = strtotime($data['ltime']) + 86399;
            if(K::M('waimai/huodong')->update($huodong_id, $data)){
                $this->msgbox->add('修改内容成功');
            }  
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:waimai/activity/edit.html';
        }
    }
    
    public function delete($huodong_id=null)
    {
        if($huodong_id = (int)$huodong_id){
            if(!$detail = K::M('waimai/huodong')->detail($huodong_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                $items = K::M('waimai/huodongitems')->items(array('huodong_id'=>$huodong_id));
                $item_ids = array();
                foreach($items as $k=>$v){
                    $item_ids[$v['item_id']] = $v['item_id'];
                }
                if(K::M('waimai/huodong')->delete($huodong_id)){
                    K::M('waimai/huodongitems')->delete($item_ids);
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('huodong_id')){
            $items = K::M('waimai/huodongitems')->items(array('huodong_id'=>$ids));
            $item_ids = array();
            foreach($items as $k=>$v){
                $item_ids[$v['item_id']] = $v['item_id'];
            }
            if(K::M('waimai/huodong')->delete($ids)){
                K::M('waimai/huodongitems')->delete($item_ids);
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    
    
    //活动商家
    public function shop($huodong_id,$page=1)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在或已删除', 211);
        }elseif(!$detail = K::M('waimai/huodong')->detail($huodong_id)){
            $this->msgbox->add('该活动不存在或已删除', 212);
        }else{
            $filter = $pager = array();
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 50;
            $filter['huodong_id'] = $huodong_id;
            $filter['type'] = 1;
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
                if($SO['shop_id']){$filter['can_id'] = $SO['shop_id'];}
            }
            if($items = K::M('waimai/huodongitems')->items($filter, array('item_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($huodong_id,'{page}')), array());
            }
            $this->pagedata['detail'] = $detail;
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:waimai/activity/shop.html';
        }
    }
    
     //活动商品
    public function product($huodong_id,$page=1)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('该活动不存在或已删除', 211);
        }elseif(!$detail = K::M('waimai/huodong')->detail($huodong_id)){
            $this->msgbox->add('该活动不存在或已删除', 212);
        }else{
            $filter = $pager = array();
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 50;
            $filter['huodong_id'] = $huodong_id;
            $filter['type'] = 2;
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
                if($SO['shop_id']){$filter['can_id'] = $SO['product_id'];}
            }
            if($items = K::M('waimai/huodongitems')->items($filter, array('item_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($huodong_id,'{page}')), array());
            }
            $this->pagedata['detail'] = $detail;
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:waimai/activity/product.html';
        }
    }
    
    
    public function dialog($huodong_id,$type,$page=1)
    {
        /*1：商家 2：商品*/
        $item_huodong = K::M('waimai/huodongitems')->items(array('huodong_id'=>$huodong_id,'type'=>$type));
        $can_id = array();
        foreach ($item_huodong as $v){
            $can_id[] = $v['can_id'];
        }
        
        $yy = implode("','",$can_id );
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        
        /*1：商家 2：商品*/
        if($type == 1){
            $filter = array('audit'=>1,'closed'=>0,'verify_name'=>1);
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                //if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
                if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            }
            $filter[':SQL']= ' shop_id NOT IN (\''.$yy.'\''.')';
            if($items = K::M('waimai/waimai')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
                foreach($items as $k=>$v){
                    $items[$k]['photo'] = $v['logo'];
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($huodong_id,$type,'{page}')), array('SO'=>$SO));
            }
        }else if($type == 2){
            $filter = array('closed'=>0,'is_onsale'=>1);
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                //if($SO['shop_id']){$filter['shop_id'] = $SO['shop_id'];}
                if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            }
            $filter[':SQL'] = 'product_id NOT IN (\''.$yy.'\''.')';
            if($items = K::M('waimai/product')->items($filter, array('product_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($huodong_id,$type,'{page}')), array('SO'=>$SO));
            }
        }
        //print_r($items);die;
        
        $this->pagedata['huodong_id'] =$huodong_id;
        $this->pagedata['type'] = $type;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:waimai/activity/dialog.html';

    }
    
    public function add($huodong_id,$type,$can_id){
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('活动不能为空',211);
        }else if(!$type = (int)$type){
            $this->msgbox->add('类型不能为空',212);
        }else if(is_numeric($can_id)&&!$can_id){
            $this->msgbox->add('项目ID不能为空',213);
        }else{
            /*1：商家 2：商品*/
            //print_r($can_id);die;
            
            if($type == 1){
                if($can_id = (int)$can_id){
                    if(!$waimai = K::M('waimai/waimai')->detail($can_id)){
                        $this->msgbox->add('外卖商家不存在',214);
                    }elseif($waimai['verify_name'] !=1){
                        $this->msgbox->add('外卖商家未通过审核',215);
                    }else{
                        $data = array('huodong_id'=>$huodong_id,'can_id'=>$can_id,'type'=>1,'orderby'=>50,'title'=>$waimai['title'],'photo'=>$waimai['logo'],'dateline'=>__TIME,'clientip'=>__IP);
                        if(K::M('waimai/huodongitems')->create($data)){
                           $this->msgbox->add('添加外卖商家成功');
                        }
                   }
                }elseif($ids = $this->GP('can_id')){
                    foreach($ids as $id){
                        if($waimai = K::M('waimai/waimai')->detail($id)){
                            if($waimai['verify_name'] == 1){
                                $data = array('huodong_id'=>$huodong_id,'can_id'=>$id,'type'=>1,'orderby'=>50,'title'=>$waimai['title'],'photo'=>$waimai['logo'],'dateline'=>__TIME,'clientip'=>__IP);
                                K::M('waimai/huodongitems')->create($data);
                            }
                        }
                    }
                    $this->msgbox->add('添加外卖商家成功');
                }
            }else if($type == 2){
                if($can_id = (int)$can_id){
                    if(!$product = K::M('waimai/product')->detail($can_id)){
                        $this->msgbox->add('外卖商品不存在',214);
                    }elseif($product['closed'] ==1||$product['is_onsale'] ==0){
                        $this->msgbox->add('商品未上架或已删除',215);
                    }else{
                        $data = array('huodong_id'=>$huodong_id,'can_id'=>$can_id,'type'=>2,'orderby'=>50,'title'=>$product['title'],'photo'=>$product['photo'],'dateline'=>__TIME,'clientip'=>__IP);
                        if(K::M('waimai/huodongitems')->create($data)){
                           $this->msgbox->add('添加外卖商品成功');
                        }
                   }
                }elseif($ids = $this->GP('can_id')){
                    foreach($ids as $id){
                        if($product = K::M('waimai/product')->detail($id)){
                            if($product['closed'] == 0&&$product['is_onsale']==1){
                                $data = array('huodong_id'=>$huodong_id,'can_id'=>$id,'type'=>2,'orderby'=>50,'title'=>$product['title'],'photo'=>$product['photo'],'dateline'=>__TIME,'clientip'=>__IP);
                                K::M('waimai/huodongitems')->create($data);
                            }
                        }
                    }
                    $this->msgbox->add('添加外卖商品成功');
                }
            }else{
                $this->msgbox->add('添加失败',222);
            }
        }
    }

    


    public function itemdel($item_id=null)
    {//删除商品或商家
        if($item_id = (int)$item_id){
            if(!$detail = K::M('waimai/huodongitems')->detail($item_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('waimai/huodongitems')->delete($item_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('item_id')){
            if(K::M('waimai/huodongitems')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  

    
    public function itemso($huodong_id,$type){
        $this->pagedata['huodong_id'] = $huodong_id;
        $this->pagedata['type'] = $type;
        $this->tmpl = 'admin:waimai/activity/itemso.html';
    }


}