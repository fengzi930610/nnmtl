<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9
 * Time: 14:32
 */

class Ctl_Print_Print extends Ctl {

    public function set_shop_print(){
        if($data = $this->checksubmit('data')){
            if($data == 'print'){
                //TODO 后期新增打印机平台在处理
                /*if(in_array(PRINT_TYPE,array('xprint'))){
                    $this->msgbox->add('该类型打印机不支持打印机接单',888)->response();
                }*/
                $filter = array();
                $filter['shop_id'] = $this->shop_id;
                if($items = K::M('shop/print')->items($filter,array('plat_id'=>'desc'),1,50,$count)){
                    if(!$print = K::M('printer/common')->load()){
                        $this->msgbox->add('加载打印机模型错误',207)->response();
                    }
                    $auto_print = $this->checksubmit('auto_print');
                    $responseType = 'close';
                    $updata = array('print_type'=>1, 'auto_print'=>0);
                    if($auto_print && $auto_print == 1){
                        $responseType = 'open';
                        $updata['auto_print'] = 1;
                    }
                    
                    foreach($items as $k=>$v){
                        if(!$print->addAndOpenPrint($v['machine_code'],$v['mkey'])){
                            $this->msgbox->add('设置打印机失败',205)->response();
                        }else if(!$print->getOrder($v['machine_code'], $responseType)){
                            $this->msgbox->add('设置打印机接/拒单失败',205)->response();
                        }
                    }
                    if(K::M('shop/print')->set_print_status($this->shop_id,1)&&K::M('waimai/waimai')->update($this->shop_id,$updata)){
                        $this->msgbox->add('设置成功');
                    }else{
                        $this->msgbox->add('设置失败',201);
                    }
                }else{
                    $this->msgbox->add('当前店铺没有打印机',202);
                }
            }else{
                if(K::M('shop/print')->set_print_status($this->shop_id,0)&&K::M('waimai/waimai')->update($this->shop_id,array('print_type'=>0))){
                    $this->msgbox->add('设置成功');
                }else{
                    $this->msgbox->add('设置失败',203);
                }
            }
        }else{
            $this->msgbox->add('非法数据请求',204);
        }

    }

    public function set_config()
    {
        if(!$data = $this->checksubmit('data')){
            $this->msgbox->add('非法数据请求',211);
        }else if(!$plat_id = (int)$data['plat_id']){
            $this->msgbox->add('未指定打印机', 212);
        }else if(!$num = (int)$data['num']){
            $this->msgbox->add('未指定打印份数', 213);
        }else if(!$plat = K::M('shop/print')->detail($plat_id)){
            $this->msgbox->add('打印机不存在或已被删除', 214);
        }else{
            $up_data = array('num'=>$num);
            if(K::M('shop/print')->update($plat_id, $up_data)){
                $this->msgbox->add('设置成功');
            }else{
                $this->msgbox->add('设置失败', 215);
            }
        }
    }


}