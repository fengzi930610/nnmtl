<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 18:44
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Account extends Ctl {

    public function index(){
        $link =K::M('helper/link')->mklink('webview/index',null,null,'wmbiz');
        header('location:'.$link);exit;
       $shop_account = K::M('shop/account')->detail($this->shop_id);
      if($data =$this->checksubmit('data')){
          if(!$data['account_type']){
              $this->msgbox->add('开户行不能为空',211);
          }else if(!$data['account_number']){
              $this->msgbox->add('账号或银行卡号不能为空',212);
          }else if(!$data['account_name']){
              $this->msgbox->add('开户姓名不能为空',213);
          }else{
              if($shop_account){
                  if(K::M('shop/account')->update($this->shop_id,$data)){
                      K::M('waimai/verify')->update($this->shop_id,array('verify'=>5));
                      K::M('waimai/waimai')->update($this->shop_id,array('last_time'=>__TIME,'verify_name'=>0));
                      $this->msgbox->add('更新资料成功');
                  }else{
                      $this->msgbox->add('更新资料失败，请稍后再试',215);
                  }
              }else{
                  $data['shop_id'] = $this->shop_id;
                  if(K::M('shop/account')->create($data,true)){
                      K::M('waimai/verify')->update($this->shop_id,array('verify'=>5));
                      K::M('waimai/waimai')->update($this->shop_id,array('last_time'=>__TIME,'verify_name'=>0));
                      $this->msgbox->add('更新资料成功');
                  }else{
                      $this->msgbox->add('更新资料失败，请稍后再试',216);
                  }

              }
              $this->msgbox->set_data('forward',$this->mklink('webview/index:index'));
          }
          
      }else{
          $this->pagedata['shop_account'] = $shop_account;
          $this->tmpl = 'webview/account/index.html';
      }
    }



}