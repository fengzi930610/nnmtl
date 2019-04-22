<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 15:46
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
//积分广告位

class Ctl_Adv_Waimai extends Ctl {

    //广告位限制
    //banner 轮播图限制
    //分类 限制
    //推荐 限制
    protected  $limit = array(
        'banner'=>3,
        'cate'=>20,
        'tuijian'=>4
    );


    public function index(){
        if($adv_banner = K::M('adv/adv')->adv_by_name('V3外卖首页轮播')){
            $adv_banner_items = K::M('adv/item')->items_by_adv($adv_banner['adv_id']);
            $this->pagedata['banner'] = array_values($adv_banner_items);
            $this->pagedata['banner_id'] = $adv_banner['adv_id'];
        }
        if($adv_cate = K::M('adv/adv')->adv_by_name('v3外卖首页分类')){
            $adv_cate_items = K::M('adv/item')->items_by_adv($adv_cate['adv_id']);
            $this->pagedata['cate'] = array_values($adv_cate_items);
            $this->pagedata['cate_id'] = $adv_cate['adv_id'];
        }
        if($adv_tuijian = K::M('adv/adv')->adv_by_name('V3外卖首页推荐')){
            $adv_tuijian_items = K::M('adv/item')->items_by_adv($adv_tuijian['adv_id']);
            for ($i=count($adv_tuijian_items); $i < 4; $i++) { 
                array_push($adv_tuijian_items, array('title'=>'', 'link'=>'', 'wxapp_link'=>'', 'thumb'=>''));
            }
            $this->pagedata['tuijian'] = array_values($adv_tuijian_items);
            $this->pagedata['tuijian_id'] = $adv_tuijian['adv_id'];
        }
        $this->pagedata['linktypes'] = $this->getTypes();
        $this->pagedata['wxcfg'] = $wxcfg = $this->system->config->get('wechat');
        $this->tmpl = "admin:adv/waimai/index.html";
    }

    public function save(){
        if($data = $this->checksubmit()){
            $arr = array(
                'banner'=>'V3外卖首页轮播','cate'=>"v3外卖首页分类",'tuijian'=>"V3外卖首页推荐"
            );

            foreach ($arr as $kkk=>$vvv){
                if($banner_data = $this->checksubmit($kkk)){
                    $adv_id = $banner_data['adv_id'];
                    $city_id = 0;
                    unset($banner_data['adv_id']);
                    unset($banner_data['city_id']);
                    $re_data = K::M('helper/format')->overturn($banner_data);
                    /*if(count($re_data)>$this->limit['banner']){
                        $this->msgbox->add($vvv.'广告'.$this->limit[$kkk].'条',207)->response();
                    }*/
                    $adv_data = array();
                    foreach ($re_data as $k1=>$v1){
                        if(!$v1['thumb']){
                            //$this->msgbox->add('请上传图片',202)->response();
                            continue;
                        }else if(!$v1['title']){
                            //$this->msgbox->add('请填写标题',203)->response();
                            continue;
                        }/*else if(!K::M('verify/check')->url($v1['link'])){
                            //$this->msgbox->add('请填写正确的链接',204)->response();
                            continue;
                        }*/else{
                            $re_data[$k1]['adv_id'] = $adv_id;
                            $re_data[$k1]['city_id'] = $city_id;
                            $adv_data[] = $re_data[$k1];
                            continue;
                        }
                    }


                    $cate_adv = K::M('adv/item')->items_by_adv($adv_id);
                    $items_ids = array();
                    foreach ($cate_adv as $k=>$v){
                        $items_ids[$v['item_id']] = $v['item_id'];
                    }

                    foreach ($adv_data as $kk=>$vv){
                        $vv['orderby'] = $kk;
                        $vv['audit'] = 1;
                        if(!K::M('adv/item')->create($vv)){
                            $this->msgbox->add('添加失败',206)->response();
                        }
                    }

                    K::M('adv/item')->delete($items_ids,true);


                }
            }


            $this->cache->flush();
            $this->msgbox->add('操作成功');


        }else{
            $this->msgbox->add('非法提交数据',201);
        }

    }

    public function getTypes()
    {
        return array(
            '_shop'=>'商家',
            '_cate'=>'分类',
            '_huodong'=>'活动',
            '_module'=>'模块'
            );
    }

    public function getModules()
    {
        return array(
            array(
                'id'=>1,
                'title'=>'跑腿',
                'advlink'=>K::M('helper/link')->mklink('',array(),array(),'paotui'),
                'advwxlink'=>'',
                ),
            array(
                'id'=>2,
                'title'=>'积分',
                'advlink'=>K::M('helper/link')->mklink(null,array(),array(),'jifen'),
                'advwxlink'=>'',
                ),
            );
    }

    public function dialog($type="_shop", $page)
    {

        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        switch ($type) {
            case '_shop':
                $id = 'shop_id';
                $filter['closed'] = 0;
                $filter['verify_name'] = 1;
                $model = K::M('waimai/waimai');
                $ctl = 'shop/detail';
                $wxctl = '../shoptail/shoptail?id=';
                break;
            case '_cate':
                $id = 'cate_id';
                $model = K::M('waimai/cate');
                $filter['parent_id'] = 0;
                $ctl = 'shoplist/index';
                $wxctl = '../shoplist/shoplist?cateid=';
                break;
            case '_huodong':
                $id = 'huodong_id';
                $filter['stime'] = '<=:'.__TIME;
                $filter['ltime'] = '>=:'.__TIME;
                $model = K::M('waimai/huodong');
                $ctl = 'huodong/detail';
                $wxctl = '../huodong/huodong?huodong_id=';
                break;
            case '_module':
                $items = $this->getModules();
            default:
                break;
        }

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['id']){
                $filter[$id] = $SO['id'];
            }
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
        }

        if($model){
            if($items = $model->items($filter, null, $page, $limit, $count)){
                foreach($items as $k=>$v){
                    $advlink = K::M('helper/link')->mklink($ctl,array($v[$id]),array(),'waimai');
                    $v['advlink'] = trim(str_replace("?","",$advlink));
                    $v['advwxlink'] = $wxctl.$v[$id];
                    $v['id'] = $v[$id];
                    $items[$k] = $v;
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($type,'{page}')), array('SO'=>$SO, 'multi'=>$multi));
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['types'] = $this->getTypes();
        $this->pagedata['type'] = $type;
        $this->tmpl = 'admin:adv/waimai/dialog.html';
    }

    public function dialogso($target=null, $multi=null, $type='_shop')
    {
        if($target){
            $pager['multi'] = $multi == 'Y' ? 'Y' : 'N';
            $pager['target'] = $target;
        }
        $this->pagedata['pager'] = $pager;
        $this->pagedata['type'] = $type;
        $this->tmpl = 'admin:adv/waimai/dialogso.html';
    }






}