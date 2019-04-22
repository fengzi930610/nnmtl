<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15
 * Time: 9:56
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Waimai_Print extends Ctl {

    public function index($page=1){
        $page = max((int)$page,1);
        $limit = 50;
        $filter = array();
        if($SO = $this->GP("SO")){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if($SO['online']||$SO['online']==0){
                $filter['online'] = $SO['online'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.title LIKE '%".$SO['keywords']."%')";
            }
        }
        $this->pagedata['print_type'] = PRINT_TYPE;

        if($items = K::M('shop/print')->items_join_shop($filter, array('plat_id'=>"DESC"), $page, $limit, $count)){
            /*$shop_ids = array();
            foreach ($items as $k=>$v){
                $shop_ids[$v['shop_id']] =$v['shop_id'];
            }
            $waimai_list = K::M('waimai/waimai')->items_by_ids($shop_ids);*/
            foreach ($items as $kk=>$vv){
                //$items[$kk]['waimai'] = $waimai_list[$vv['shop_id']];
                switch ($vv['online']){
                    case 0:
                        $items[$kk]['label'] = "离线";
                        break;
                    case 1:
                        $items[$kk]['label'] = "在线";
                        break;
                    case 2:
                        $items[$kk]['label'] = "缺纸";
                        break;
                    default :
                        $items[$kk]['label'] = "离线";
                        break;
                }
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:waimai/print/index.html";

    }


    public function so(){
        $this->tmpl = "admin:waimai/print/so.html";
    }


    public function delete($plat_id){
        if($data = $this->checksubmit('plat_id')){
            foreach ($data as $k=>$v){
                if(!$plat = K::M('shop/print')->detail($v)){
                    $this->msgbox->add('打印机不存在',204)->response();
                }else if(!K::M('shop/print')->delete($v)){
                    $this->msgbox->add('删除失败',205);
                }

            }
            $this->msgbox->add('删除成功');

        }else{
            if(!$plat_id){
                $this->msgbox->add('打印机不存在',201);
            }else if(!$plat = K::M('shop/print')->detail($plat_id)){
                $this->msgbox->add('打印机不存在',202);
            }else if(!K::M('shop/print')->delete($plat_id)){
                $this->msgbox->add('删除失败',203);
            }else{
                $this->msgbox->add('删除成功');
            }

        }


    }

    public function edit($plat_id){
        if($data = $this->checksubmit('data')){

            if(!$plat_id = $data['plat_id']){
                $this->msgbox->add('打印机不存在',203);
            }else if(!$print = K::M('shop/print')->detail($plat_id)){
                $this->msgbox->add('打印机不存在',204);
            }else if(!$shop_id = $data['shop_id']){
                $this->msgbox->add('请选择商户',205);
            }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('选择的商户不存在或已被关闭',206);
            }else if(!$title = $data['title']){
                $this->msgbox->add('打印机名称不能为空',207);
            }else if(!$machine_code =$data['machine_code']){
                $this->msgbox->add('终端号不能为空',208);
            }else if(!$mkey = $data['mkey']){
                $this->msgbox->add('终端秘钥不能为空',209);
            }else{

                if(!$print = K::M('printer/common')->load()){
                    $this->msgbox->add('加载打印机模型错误',210);
                }else if(!$print->addAndOpenPrint($data['machine_code'],$data['mkey'])){
                    $this->msgbox->add('同步打印机失败',205)->response();
                }else if(K::M('shop/print')->update($plat_id,$data)){
                    $this->msgbox->add('更新成功');
                }else{
                    $this->msgbox->add("更新失败",210);

                }
                $this->msgbox->set_data('forward',$this->mklink('waimai/print:index',array(),array(),'admin'));
/*
                if(!K::M('printer/ylyun')->addAndOpenPrint($data['machine_code'],$data['mkey'])){
                    $this->msgbox->add('同步打印机失败',205)->response();
                }
                if(K::M('shop/print')->update($plat_id,$data)){
                    $this->msgbox->add('更新成功');
                }else{
                    $this->msgbox->add("更新失败",210);

                }
                $this->msgbox->set_data('forward',$this->mklink('waimai/print:index',array(),array(),'admin'));*/
            }

        }else{
            if(!$plat_id){
                $this->msgbox->add('打印机不存在',201);
            }else if(!$print = K::M('shop/print')->detail($plat_id)){
                $this->msgbox->add('打印机不存在',202);
            }else{
                $waimai = K::M('waimai/waimai')->detail($print['shop_id']);
                $this->pagedata['print'] =$print;
                $this->pagedata['waimai'] = $waimai;
                $this->tmpl = "admin:waimai/print/edit.html";
            }
        }

    }


    public function create(){
        if($data = $this->checksubmit('data')){
           if(!$shop_id = $data['shop_id']){
                $this->msgbox->add('请选择商户',205);
            }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
                $this->msgbox->add('选择的商户不存在或已被关闭',206);
            }else if(!$title = $data['title']){
                $this->msgbox->add('打印机名称不能为空',207);
            }else if(!$machine_code =$data['machine_code']){
                $this->msgbox->add('终端号不能为空',208);
            }else if(!$mkey = $data['mkey']){
                $this->msgbox->add('终端秘钥不能为空',209);
            }else{
               $print_cfg = $this->system->config->get('print');
               $data['from']=$print_cfg['from'];
               $data['partner'] = $print_cfg['partner'];
               $data['apikey'] = $print_cfg['apikey'];
               $data['num'] = 1;
               if(!$print = K::M('printer/common')->load()){
                   $this->msgbox->add('加载打印机模型错误',210);
               }else if(!$print->addAndOpenPrint($data['machine_code'],$data['mkey'])){
                   $this->msgbox->add('同步打印机失败',205)->response();
               }else if(K::M('shop/print')->create($data)){
                   $this->msgbox->add('创建成功');
               }else{
                   $this->msgbox->add("创建失败",210);
               }
               /*if(!K::M('printer/ylyun')->addAndOpenPrint($data['machine_code'],$data['mkey'])){
                   $this->msgbox->add('同步打印机失败',205)->response();
               }
                if(K::M('shop/print')->create($data)){
                    $this->msgbox->add('创建成功');
                }else{
                    $this->msgbox->add("创建失败",210);

                }*/
                $this->msgbox->set_data('forward',$this->mklink('waimai/print:index',array(),array(),'admin'));
            }

        }else{
            $this->tmpl = "admin:waimai/print/create.html";
        }
    }

    public function cancelall($plat_id){
        if(!$plat_id){
            $this->msgbox->add('未指定打印机',201);
        }else if(!$plat = K::M('shop/print')->detail($plat_id)){
            $this->msgbox->add('指定的打印机不存在',202);
        }else if(!$print = K::M('printer/common')->load()){
            $this->msgbox->add('打印机模型不存在',203);
        }else if(!$print->cancelAll($plat['machine_code'])){
           $this->msgbox->add('清除失败',204);
        }else{
            $this->msgbox->add('清除成功');
        }

    }



}