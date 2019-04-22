<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/24
 * Time: 15:07
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
//启动页广告位
class Ctl_Adv_Start extends Ctl {

    protected  $limit = 3;

    public function index(){
        if($adv = K::M('adv/adv')->adv_by_name('V3启动页广告')){
            $cate_adv = K::M('adv/item')->items_by_adv($adv['adv_id']);
            $this->pagedata['dav'] = $cate_adv;
            $this->pagedata['adv_id'] = $adv['adv_id'];
        }
        $this->tmpl = "admin:adv/start/index.html";

    }


    public function save(){
        if($data = $this->checksubmit('data')){
            if(!$data['adv_id']){
                $this->msgbox->add('未指定需要修改的广告位',201)->response();
            }else{
                $adv_id = $data['adv_id'];
                $city_id = $data['city_id'];
                unset($data['adv_id']);
                unset($data['city_id']);
                $re_data = K::M('helper/format')->overturn($data);
                if(count($re_data)>$this->limit){
                    $this->msgbox->add('广告位最多添加'.$this->limit.'条',207)->response();
                }
                foreach ($re_data as $k=>$v){
                    if(!$v['thumb']){
                        $this->msgbox->add('请上传图片',202)->response();
                    }else if(!$v['title']){
                        $this->msgbox->add('请填写标题',203)->response();
                    }/*else if(!K::M('verify/check')->url($v['link'])){
                        $this->msgbox->add('请填写正确的链接',204)->response();
                    }*/else{
                        $re_data[$k]['adv_id'] = $adv_id;
                        $re_data[$k]['city_id'] = $city_id;
                        continue;
                    }
                }
                if($adv = K::M('adv/adv')->adv_by_name('V3启动页广告')){
                    $cate_adv = K::M('adv/item')->items_by_adv($adv['adv_id']);
                    $items_ids = array();
                    foreach ($cate_adv as $k=>$v){
                        $items_ids[$v['item_id']] = $v['item_id'];
                    }
                    foreach ($re_data as $kk=>$vv){
                        $vv['orderby'] = $kk;
                        $vv['audit'] = 1;
                        if(!K::M('adv/item')->create($vv)){
                            $this->msgbox->add('添加失败',206)->response();
                        }
                    }

                    K::M('adv/item')->delete($items_ids,true);
                    $this->cache->flush();
                    $this->msgbox->add('操作成功');
                }

            }

        }else{
            $this->msgbox->add('非法数据请求',201);
        }

    }


}