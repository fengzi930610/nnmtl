<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 14:48
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Verify extends Ctl{

    public function index(){
        $link =K::M('helper/link')->mklink('webview/index',null,null,'wmbiz');
        header('location:'.$link);exit;
        if($data = $this->checksubmit('data')){
            if(!$data['id_name']){
                $this->msgbox->add('法人姓名不能为空',211);
            }else if(!$id_name = K::M('verify/check')->id_number($data['id_number'])){
                $this->msgbox->add('身份证号码不能为空',212);
            }else{
                if(K::M('waimai/verify')->detail($this->shop_id)){
                    if(K::M('waimai/verify')->update($this->shop_id,$data)){
                        $this->msgbox->add('更新成功');
                    }else {
                        $this->msgbox->add('更新成功失败,请稍后再试',217);
                    }
                }else{
                    $data['shop_id'] = $this->shop_id;
                    if(K::M('waimai/verify')->create($data)){
                        $this->msgbox->add('提交成功');
                    }else{
                        $this->msgbox->add('提交失败，请稍后再试',218);
                    }
                }
                $this->msgbox->set_data('forward',$this->mklink('webview/verify:edit_pei'));
            }
            
        }else{
            $this->pagedata['verify']  = K::M('waimai/verify')->detail($this->shop_id);
            $this->tmpl='webview/verify/index.html';
        }
    }
    
    public function edit_pei(){
        $link =K::M('helper/link')->mklink('webview/index',null,null,'wmbiz');
        header('location:'.$link);exit;
        $waimai = K::M('waimai/waimai')->detail($this->shop_id);
        if(!$waimai){
            $this->msgbox->add('请先完善店铺信息')->response();
        }
        $this->pagedata['waimai'] =$waimai;
        if($data = $this->checksubmit('data')){
            $datas = array('pei_type'=>$data['pei_type']);
            if($data['pei_type'] == 1){
                $datas['online_pay'] = 1;
                $datas['is_dao'] = 0;
            }
           if(K::M('waimai/waimai')->update($this->shop_id,$datas,false)){
               $this->msgbox->add('提交成功');
           }else{
               $this->msgbox->add('提交失败，请稍后再试',219);
           }
        }else{
            $this->tmpl = 'webview/verify/pei.html';
        }
        $this->msgbox->set_data('forward',$this->mklink('webview/account:index'));
        
    }





}