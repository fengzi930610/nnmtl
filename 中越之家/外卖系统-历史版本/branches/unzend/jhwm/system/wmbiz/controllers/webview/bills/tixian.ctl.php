<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/14
 * Time: 14:47
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Webview_Bills_Tixian extends Ctl {

    public function index(){
        
        //提现配置
        $cfg_tixian = $this->system->config->get('tixian');
        //v3.6 判断商户提现还是平台提现
        if(!$cfg_tixian['shop']){
            $this->msgbox->add('平台未开启商户提现功能！',211)->response();
        }
        //外卖店铺
        $waimai = $this->waimai_shop;
        //商家
        $shop = K::M('shop/shop')->detail($this->shop_id);
        $tixian = K::M('shop/tixian')->find(array('shop_id'=>$this->shop_id),array('tixian_id'=>'DESC'));
        $account = K::M('shop/account')->detail($this->shop_id);
        $ltime = $tixian['dateline'] +($cfg_tixian['day']*86400);

        if($data = $this->checksubmit('data')){

            if(!$money = (float)$data['money']){
               $this->msgbox->add('输入的金额不正确',201);
            }else if($money>$shop['money']){
                $this->msgbox->add('输入的金额不正确',202);
            }else if(empty($account['account_number'])){
                $this->msgbox->add('提现帐号不正确', 214);
            }else if($tixian['tixian_id']&&($tixian['status']==0)){
                $this->msgbox->add('有正在提现中的提现任务',203);
            }else if($money<$cfg_tixian['limit']){
                $this->msgbox->add('提现金额金额最低'.$cfg_tixian['limit'].'元',205);
            }else if($ltime>__TIME){
                $this->msgbox->add('距离上次提现不足'.$cfg_tixian['day'].'天',206);
            }else if(!K::M('cache/cache')->islock('shop_tixian'.$waimai['shop_id'],3)){
                $this->msgbox->add('处理中..',207)->response();
            } else if($log_id = K::M('shop/shop')->update_money($this->shop_id,-$money,'余额提现，扣款')){
                if($tixian_id = K::M('shop/tixian')->create(array('shop_id'=>$this->shop_id,'money'=>$money,'intro'=>'申请提现'.$money,'account_info'=>'开户行：'.$account['account_type'].'，账户：'.$account['account_number'].',开户人：'.$account['account_name'],'end_money'=>$money,'payee_account' => $account['account_number']))){
                    K::M('cache/cache')->unlock('shop_tixian'.$waimai['shop_id']);
                    K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                    $this->msgbox->add('提现成功');
                }
            }else{
                $this->msgbox->add('提现失败',216);
            }
        }else{
            $this->pagedata['waimai_detail'] = $waimai;
            //商家结算信息
            //最后一次提现记录
            $ke_tx = 0;
            /*  [day] => 3
      [limit] => 100*/

            $ltime = $tixian['dateline'] +($cfg_tixian['day']*86400);
            if($tixian['tixian_id']&&($tixian['status']==0)){
                $extend = false;
            }else{
                $extend = true;
            }

            if($shop['money']>$cfg_tixian['limit']&&$ltime<=__TIME&&$extend){
                $ke_tx = 1;
            }

            if($account){
               $account['number'] = substr_replace($account['account_number'],'**** **** ****',0,strlen($account['account_number'])-4);
            }
            $this->pagedata['account'] = $account;
            $this->pagedata['shop'] = $shop;
            $this->pagedata['ke_tx'] = $ke_tx;
            $this->tmpl = 'webview/bills/tixian/index.html';
        }
    }

    public function account(){
        $account  =  K::M('shop/account')->detail($this->shop_id);
        if($account){
            $account['number'] = substr_replace($account['account_number'],'**** **** ****',0,strlen($account['account_number'])-4);
        }

        $cfg_tixian = $this->system->config->get('tixian');
        $this->pagedata['account'] = $account;
        $this->pagedata['cfg'] = $cfg_tixian;
        $cfg_site = $this->system->config->get('site');
        $this->pagedata['site'] = $cfg_site;
        $this->tmpl = 'webview/bills/tixian/account.html';
    }




}