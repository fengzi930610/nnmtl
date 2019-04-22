<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Finance_Account extends Ctl
{
    //我的账户
    public function index($page=1)
    {
        $filter = $pager = array();
        $filter['shop_id'] = $this->shop_id;
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 10;
        if($SO= $this->GP('SO')){ 
            if($SO['type']==2){
                $filter['money'] = '>:0';
            }else if($SO['type']==3) {
                $filter['money'] = '<:0';
            }

            if($SO['begin_time']){
                $filter['dateline'] ='>:'.strtotime($SO['begin_time']);
            }
            if($SO['end_time']){
                $filter['dateline'] = '<:'.(strtotime($SO['end_time'])+86399);
            }
            if($SO['begin_time']&&$SO['end_time']){
                $filter['dateline'] = strtotime($SO['begin_time'])."~".(strtotime($SO['end_time'])+86399);
            }
            $this->pagedata['type'] = $SO['type'];
            $this->pagedata['begin_time'] = $SO['begin_time'];
            $this->pagedata['end_time'] = $SO['end_time'];

        }

        if($items = K::M('shop/log')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('finance/account:index', array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['waimai_detail'] = $this->waimai_shop;

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'finance/account/index.html';
    }
    
    
    
    //账户信息
    public function info(){
        $account = K::M('shop/account')->detail($this->shop_id);
        $this->pagedata['account'] = $account;
        $this->tmpl = 'finance/account/info.html';
    }
    
    //充值
    public function recharge(){
        $this->tmpl = 'finance/account/recharge.html';
    }

    //提现
    public function cash(){
        if(!$account = K::M('shop/account')->detail($this->shop_id)){
            $this->msgbox->add('还未设置您的提现账户!',211)->response();
        }
        
        $cfg = $this->system->config->get('tixian');
        //v3.6 判断商户提现还是平台提现
        if(!$cfg['shop']){
            $this->msgbox->add('平台未开启商户提现功能！',212)->response();
        }
        $last = k::M('shop/tixian')->find(array('shop_id'=>$this->shop_id),array('tixian_id'=>'DESC'));
        $ltime = $last['dateline']+($cfg['day']*86400);

        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'money,intro')){
                $this->msgbox->add('非法的数据提交', 211);
            }else if($data['money'] <0){
                $this->msgbox->add('金额不正确', 212);
            }else if($data['money'] > $this->shop['money']){
                $this->msgbox->add('余额不足', 213);
            }else if(empty($account['account_number'])){
                $this->msgbox->add('提现帐号不正确', 214);
            }else if($data['money']<$cfg['limit']){
                $this->msgbox->add('最低提现金额'.$cfg['limit'],218);
            }else if($ltime>__TIME){
                $this->msgbox->add('距离上次提现不足'.$cfg['day'].'天',219);
            }else if(! K::M('cache/cache')->islock('shop_tixian'.$this->shop_id,10)){
                $this->msgbox->add('处理中请稍后',220)->response();
            }else if($log_id = K::M('shop/shop')->update_money($this->shop_id,-$data['money'],'余额提现，扣款')){
                if($tixian_id = K::M('shop/tixian')->create(array('shop_id'=>$this->shop_id,'money'=>$data['money'],'intro'=>$data['intro'],'account_info'=>'开户行：'.$account['account_type'].'，账户：'.$account['account_number'].',开户人：'.$account['account_name'],'end_money'=>$data['money'],'payee_account' => $account['account_number']))){
                    $this->msgbox->add('提现成功');
                    K::M('cache/cache')->unlock('shop_tixian'.$this->shop_id);
                    K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                  $this->msgbox->set_data('forward',$this->mklink('finance/account:index'));
                }else{
                    $this->msgbox->add('提现失败',217);
                }

             }else{
                 $this->msgbox->add('提现失败',216);
             }
        }else{
            $this->pagedata['account'] = $account;
            $this->tmpl = 'finance/account/cash.html';
        }
    }

    public function cash_log(){

    }


    public  function chongzhi(){
        if($parmas = $this->checksubmit('data')){
            if(!$deliver = (float)$parmas['deliver']){
                $this->msgbox->add('请填写充值金额',201);
            }else if(!$shop = K::M('shop/shop')->detail($this->shop_id)){
                $this->msgbox->add('商家不存在',202);
            }else if($shop['money']<$deliver){
                $this->msgbox->add('余额不足',203);
            }else {
                if(K::M('shop/shop')->update_money($this->shop_id,-$deliver,'充值配送费，扣除余额￥:'.$deliver)){
                    if(K::M('waimai/waimai')->update_money($this->shop_id,$deliver,'充值配送费￥:'.$deliver)){
                        $this->msgbox->add('操作成功');
                    }else{
                        $this->msgbox->add('操作失败',205);
                    }
                }else{
                    $this->msgbox->add('操作失败',204);
                }
            }
        }else{
            $this->msgbox->add('非法数据请求',206);
        }



    }

    

}
