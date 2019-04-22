<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 15:28
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Activity_Items extends Ctl {


    public function dialog($act_id,$from,$page)
    {
        /*1:微店商品2：微店商家3：团商品4：团商家*/
        $item_activity = K::M('activity/items')->items(array('active_id'=>$act_id,'type'=>$from));
        $can_id=array();
        foreach ($item_activity as $v){
            $can_id[] = $v['can_id'];
        }
        $yy = implode("','",$can_id );
        $pager['page'] = max(intval($page), 1);
        $filter = array();
        $pager['limit'] = $limit = 10;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
        }
        $format = array();
        /*1:微店商品2：微店商家3：团商品4：团商家*/
        if($from==1){

            $filter[':SQL']= ' product_id NOT IN (\''.$yy.'\''.')';
            $filter['is_onsale'] = 1;
            $filter['closed'] = 0;
            if($items = K::M('weidian/product')->items($filter, array('product_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($act_id,$from,'{page}')), array('SO'=>$SO));;
            }
            $format=$items;
        }else if($from==2){
            $filter[':SQL']= ' shop_id NOT IN (\''.$yy.'\''.')';
            $filter['audit']=1;
            $filter['closed']=0;
            $filter['have_weidian'] = 1;

            if($items = K::M('weidian/weidian')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($act_id,$from,'{page}')), array('SO'=>$SO));;
            }
            foreach ($items as $v){
                $v['photo'] = $v['logo'];
                $format[]=$v;
            }

        }else if($from==3){
            $filter[':SQL']= ' tuan_id NOT IN (\''.$yy.'\''.')';
            $filter['is_onsale'] = 1;
            $filter['closed'] = 0;
            if($items = K::M('tuan/tuan')->items($filter, array('tuan_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($act_id,$from,'{page}')), array('SO'=>$SO));;
            }
            $format=$items;

        }else if($from==4){
            $filter[':SQL']= ' shop_id NOT IN (\''.$yy.'\''.')';
            $filter['audit']=1;
            $filter['closed']=0;
            $filter['have_tuan'] = 1;
            if($items = K::M('shop/shop')->items($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($act_id,$from,'{page}')), array('SO'=>$SO));;
            }
            foreach ($items as $v){
                $v['photo'] = $v['logo'];
                $format[]=$v;
            }
        }
        $this->pagedata['active_id'] =$act_id;
        $this->pagedata['from'] =$from;
        $this->pagedata['items'] =$format;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:activity/items/diglog.html';

    }



    //微店商家
    public function weidianshop($active_id,$page=1){
        $page = max($page,1);
        if(!$active_id){
            $this->msgbox->add('未指定活动',201);
        }else if(!$detail = K::M('activity/activity')->detail($active_id)){
            $this->msgbox->add('活动不存在',202);
        }else{
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            $filter = array();
            $filter['active_id'] =$active_id;
            $filter['type'] =2;
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 20;
            if($item = K::M('activity/items')->items($filter,null,$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($active_id,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['from'] = '2';
            $this->pagedata['detail'] =$detail;
            $this->pagedata['items'] = $item;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:activity/items/data_list.html';
        }
        

    }

    //团购商家
    public function tuanshop($active_id,$page=1){
        $page = max($page,1);
        if(!$active_id){
            $this->msgbox->add('未指定活动',201);
        }else if(!$detail = K::M('activity/activity')->detail($active_id)){
            $this->msgbox->add('活动不存在',202);
        }else{
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            $filter = array();
            $filter['active_id'] =$active_id;
            $filter['type'] =4;
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 20;
            if($item = K::M('activity/items')->items($filter,null,$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($active_id,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['from'] = '4';
            $this->pagedata['detail'] =$detail;
            $this->pagedata['items'] = $item;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:activity/items/data_list.html';
        }

    }

    //团购商品
    public function tuangood($active_id,$page=1){
        $page = max($page,1);
        if(!$active_id){
            $this->msgbox->add('未指定活动',201);
        }else if(!$detail = K::M('activity/activity')->detail($active_id)){
            $this->msgbox->add('活动不存在',202);
        }else{
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            $filter = array();
            $filter['active_id'] =$active_id;
            $filter['type'] =3;
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 20;
            if($item = K::M('activity/items')->items($filter,null,$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($active_id,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['from'] = '3';
            $this->pagedata['detail'] =$detail;
            $this->pagedata['items'] = $item;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:activity/items/data_list.html';
        }

    }

    //微店商品
    public function weidiangood($active_id,$page=1){
        $page = max($page,1);
        if(!$active_id){
            $this->msgbox->add('未指定活动',201);
        }else if(!$detail = K::M('activity/activity')->detail($active_id)){
            $this->msgbox->add('活动不存在',202);
        }else{
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            $filter = array();
            $filter['active_id'] =$active_id;
            $filter['type'] =1;
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 20;
            if($item = K::M('activity/items')->items($filter,null,$page,$limit,$count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($active_id,'{page}')), array('SO'=>$SO));
            }
            $this->pagedata['from'] = '1';
            $this->pagedata['detail'] =$detail;
            $this->pagedata['items'] = $item;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:activity/items/data_list.html';
        }


    }
     //删除
    public function delete($cate_id){
        if(!$cate_id){
            $this->msgbox->add('没有选择需要删除的内容',216);
        }else if(!$detail=K::M('activity/items')->detail($cate_id)){
            $this->msgbox->add('没有选择需要删除的内容',217);
        }else{
            if($del=K::M('activity/items')->delete($cate_id)){
                $this->msgbox->add('删除成功');
            }else{
                $this->msgbox->add('删除失败',218);
            }
        }

    }
    //搜索
    public function so($active_id,$from){
        $this->pagedata['active_id'] =$active_id;
        $this->pagedata['from'] =$from;
        $this->tmpl = 'admin:activity/items/so.html';
    }
   //创建
    public function create($active_id,$from,$can_id){
        if(!$active_id){
            $this->msgbox->add('未指定活动',219);
        }else if(!$from){
            $this->msgbox->add('未指定来源',220);
        }else if(!$can_id){
            $this->msgbox->add('未指定需要添加内容',221);
        }else{
            /*1:微店商品2：微店商家3：团商品4：团商家*/
            if($from==1){
                if(!$weidian = K::M('weidian/product')->detail($can_id)){
                 $this->msgbox->add('微店商品不存在',223);
                }else{
                    $data = array();
                    $data['active_id'] =$active_id;
                    $data['can_id'] = $weidian['product_id'];
                    $data['type'] = 1;
                    $data['order_by'] = 50;
                    $data['title'] = $weidian['title'];
                    $data['photo'] = $weidian['photo'];
                }
                if(K::M('activity/items')->create($data)){
                   $this->msgbox->add('添加微店商品成功');
                }

            }else if($from==2){
                if(!$weidian = K::M('shop/shop')->detail($can_id)){
                    $this->msgbox->add('微店商家不存在',224);
                }else{
                    $data = array();
                    $data['active_id'] =$active_id;
                    $data['can_id'] = $weidian['shop_id'];
                    $data['type'] = 2;
                    $data['order_by'] = 50;
                    $data['title'] = $weidian['title'];
                    $data['photo'] = $weidian['logo'];
                }
                if(K::M('activity/items')->create($data)){
                    $this->msgbox->add('添加微店商家成功');
                }

            }else if($from==3){
                if(!$weidian = K::M('tuan/tuan')->detail($can_id)){
                    $this->msgbox->add('团购商品不存在',225);
                }else{
                    $data = array();
                    $data['active_id'] =$active_id;
                    $data['can_id'] = $weidian['tuan_id'];
                    $data['type'] = 3;
                    $data['order_by'] = 50;
                    $data['title'] = $weidian['title'];
                    $data['photo'] = $weidian['photo'];
                }
                if(K::M('activity/items')->create($data)){
                    $this->msgbox->add('添加团购商品成功');
                }

            }elseif($from==4){
                if(!$weidian = K::M('shop/shop')->detail($can_id)){
                    $this->msgbox->add('团购商家不存在',226);
                }else{
                    $data = array();
                    $data['active_id'] =$active_id;
                    $data['can_id'] = $weidian['shop_id'];
                    $data['type'] = 4;
                    $data['order_by'] = 50;
                    $data['title'] = $weidian['title'];
                    $data['photo'] = $weidian['logo'];
                }
                if(K::M('activity/items')->create($data)){
                    $this->msgbox->add('添加团购商品成功');
                }

            }else{
                $this->msgbox->add('添加失败',222);
            }

        }

    }

    
    
}