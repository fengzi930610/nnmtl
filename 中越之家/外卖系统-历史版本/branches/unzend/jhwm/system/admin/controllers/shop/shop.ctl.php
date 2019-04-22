<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Shop extends Ctl
{
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['cate_id']){$filter['cate_id'] = $SO['cate_id'];}
            if($SO['mobile']){$filter['mobile'] = $SO['mobile'];}
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if($SO['addr']){$filter['addr'] = "LIKE:%".$SO['addr']."%";}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        $filter['closed'] = 0;
        if($items = K::M('shop/shop')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        foreach($items as $k=>$v) {
            $items[$k]['orders'] = K::M('order/order')->count(array('shop_id'=>$v['shop_id']));
        } 
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/shop/items.html';
    }
    public function so($target=null, $multi=null)
    {
        if($target){
            $pager['multi'] = $multi == 'Y' ? 'Y' : 'N';
            $pager['target'] = $target;
        }


        $this->pagedata['pager'] = $pager; 
        $this->tmpl = 'admin:shop/shop/so.html';
    }

    public function dialog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['cate_id']){$filter['cate_id'] = $SO['cate_id'];}
            if($SO['mobile']){
               /* $filter['mobile'] = "LIKE:%".$SO['mobile']."%";*/
                $items_shop = K::M('shop/shop')->items(array('mobile'=> "LIKE:%".$SO['mobile']."%"),array('shop_id'=>'DESC'),1,50,$count_shop);
                $filter_shop_ids = array();
                foreach ($items_shop as $kk=>$vv){
                    $filter_shop_ids[$vv['shop_id']] = $vv['shop_id'];
                }
                $filter['shop_id'] = array_values($filter_shop_ids);
            }
            if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
            if($SO['addr']){$filter['addr'] = "LIKE:%".$SO['addr']."%";}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        //$filter['verify_name'] = 1;

        $filter['audit'] = 1;
        $filter['closed'] = 0;
        if($items = K::M('waimai/waimai')->items($filter, null, $page, $limit, $count)){
            $city_ids = $shop_ids = array();
            foreach ($items as $k=>$v){
                $city_ids[$v['city_id']] = $v['city_id'];
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            $city_list = K::M('data/city')->items_by_ids($city_ids);
            $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach ($items as $k1=>$v1){
                $items[$k1]['city_name'] = $city_list[$v1['city_id']]['city_name'];
                $items[$k1]['mobile'] = $shop_list[$v1['shop_id']]['mobile'];
            }

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO, 'multi'=>$multi));
        }
       /* echo '<pre>';
        print_r($items);exit;*/
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/shop/dialog.html';   
    }
    public function detail($shop_id = null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $waimai = K::M('waimai/waimai')->detail($shop_id);
            if($detail['have_waimai']==1 && $waimai && $waimai['closed']==0 && $waimai['audit']==1){
                 $this->pagedata['waimai'] = $waimai;
            }
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/shop/detail.html';
        }
    }
    public function product($shop_id=null)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('未指定隶属商铺', 211);
        }else if(!$shop = K::M('shop/shop')->detail($shop_id, true)){
            $this->msgbox->add('指定的商铺不存在或删除', 212);
        }else{
            $filter = array('shop_id'=>$shop_id, 'closed'=>0);
            if($items = K::M('product/product')->items($filter, null, $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($shop_id, '{page}')));
                $this->pagedata['items'] = $items;
            }
            $this->pagedata['shop'] = $shop;
            $this->tmpl = 'admin:shop/shop/product/index.html';
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
            $data['passwd'] = md5($data['passwd']);
            if($shop_id = K::M('shop/shop')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?shop/shop-index.html');
            } 
        }else{
            $this->pagedata['citys'] = K::M('data/city')->items();
            $this->pagedata['areas'] = K::M('data/area')->items(array('city_id'=>$detail['city_id']));
            $this->pagedata['busis'] = K::M('data/business')->items(array('area_id'=>$detail['area_id']));
            $this->tmpl = 'admin:shop/shop/create.html';
        }
    }
    public function edit($shop_id=null)
    {
        if(!($shop_id = (int)$shop_id) && !($shop_id = $this->GP('shop_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('shop/shop')->detail($shop_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if($data['tixian_percent']<0 || $data['tixian_percent']>100) {
                $this->msgbox->add('提现比例请填写0~100之间的数字', 213);
            }else {
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
                if(isset($data['passwd'])){
                    if($data['passwd'] == '******'){
                        unset($data['passwd']);
                    }
                }
                if(K::M('shop/shop')->update($shop_id, $data)){
                    if($waimai = K::M('waimai/waimai')->detail($shop_id)) {
                        K::M('waimai/waimai')->update($shop_id,array('city_id'=>$data['city_id'], 'lat'=>$data['lat'], 'lng'=>$data['lng']));    
                    }
                    $this->msgbox->add('修改内容成功');
                }  
            }            
        }else{
        	$this->pagedata['detail'] = $detail;
            $this->pagedata['citys'] = K::M('data/city')->items();
            $this->pagedata['areas'] = K::M('data/area')->items(array('city_id'=>$detail['city_id']));
            $this->pagedata['busis'] = K::M('data/business')->items(array('area_id'=>$detail['area_id']));
        	$this->tmpl = 'admin:shop/shop/edit.html';
        }
    }
    public function audit($shop_id=null)
    {
        if($shop_id = (int)$shop_id){
            if(K::M('shop/shop')->batch($shop_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('shop_id')){
            if(K::M('shop/shop')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($shop_id=null, $force=false)
    {
        if($shop_id = (int)$shop_id){
            if(!$detail = K::M('shop/shop')->detail($shop_id, $force)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                K::M('shop/shop')->delete($shop_id, $force);
                K::M('waimai/waimai')->delete($shop_id, $force);
                $this->msgbox->add('删除内容成功');
                // if(K::M('shop/shop')->batch($shop_id,array('closed'=>1))){
                //     K::M('waimai/waimai')->batch($shop_id,array('closed'=>1));
                //     $this->msgbox->add('删除内容成功');
                // }
            }
        }else if($ids = $this->GP('shop_id')){
            K::M('shop/shop')->delete($ids, $force);
            K::M('waimai/waimai')->delete($ids, $force);
            $this->msgbox->add('批量删除内容成功');
            // if(K::M('shop/shop')->batch($ids, array('closed'=>1))){
            //     K::M('waimai/waimai')->batch($ids, array('closed'=>1));
            //     $this->msgbox->add('批量删除内容成功');
            // }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }  

    public function recycle($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 1;
        if($items = K::M('shop/shop')->items($filter, array('shop_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:shop/shop/recycle.html';
    }
    public function regain($shop_id=null)
    {
        if($shop_id = intval($shop_id)){
            if(K::M('shop/shop')->regain($shop_id)){
                $this->msgbox->add('恢复服务人员帐号成功');
            }
        }else if($shop_ids = $this->GP('shop_id')){
            if(K::M('shop/shop')->regain($shop_ids)){
                $this->msgbox->add('批量恢复服务人员帐号成功');
            }
        }else{
            $this->msgbox->add('未指定要恢复服务人员', 401);
        }
    }
    public function manage($shop_id)
    {
        K::M('shop/auth')->manager($shop_id);
        header("Location:".'../merchant/index.php');
    }

    public function waimai($shop_id) 
    {
        K::M('shop/auth')->manager($shop_id);
        header("Location:".'../merchant/?waimai/');
    }
}