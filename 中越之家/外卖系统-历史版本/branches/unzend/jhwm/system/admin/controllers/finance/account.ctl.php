<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Finance_Account extends Ctl
{

    public function index($page = 1,$st=0)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['status'] = array(0, 1, 2, 4, 5);
        if($st = (int)$st){
            $this->pagedata['st'] = $st;
            if($st == 1){
                $filter['status'] = array(1, 4);
            }elseif($st == 2){
                $filter['status'] = 0;
            }elseif($st == 3){
                $filter['status'] = 2;
            }elseif($st == 4){
                $filter['pay_status'] = 1;
                $filter['status'] = 4;
            }elseif($st == 5){
                $filter = array('status'=>5, 'pay_status'=>0);
            }
        }
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.title LIKE '%".$SO['keywords']."%' OR w.contact LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('shop/tixian')->items_join_shop($filter, array('tixian_id' => 'asc'), $page, $limit, $count)){
            /*$shop_ids = array();
            foreach($items as $k => $v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);*/
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $tixian = K::M('shop/tixian')->sum(array('status' => 0), 'money');
        $total = K::M('shop/shop')->sum(array('audit' => 1, 'closed' => 0), 'money');
        $total_tixian = $tixian + $total;
        $this->pagedata['total'] = $total_tixian;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:finance/account/items.html';
    }
    
    public function wait($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['status'] = 1;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.title LIKE '%".$SO['keywords']."%' OR w.contact LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('shop/tixian')->items_join_shop($filter, array('tixian_id' => 'asc'), $page, $limit, $count)){
            $shop_ids = $tixian_ids = array();
            foreach($items as $k => $v){
                $tixian_ids[] = $v['tixian_id'];
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            //$this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $tixian = K::M('shop/tixian')->sum(array('status' => 1), 'money');
        $tx_ids = "";
        foreach($tixian_ids as $k=>$v){
            if($k==0){
                $tx_ids .= $v;
            }else{
                $tx_ids .= "_".$v;
            }
        }
        $this->pagedata['tx_ids'] = $tx_ids;
        $this->pagedata['total'] = $tixian;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:finance/account/wait.html';
    }
    
    public function logs($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['status'] = array(4, 5);
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]) + 86400;
                    $filter['dateline'] = $a . "~" . $b;
                }
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.title LIKE '%".$SO['keywords']."%' OR w.contact LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        if($items = K::M('shop/tixian')->items_join_shop($filter, array('tixian_id' => 'asc'), $page, $limit, $count)){
            /*$shop_ids = array();
            foreach($items as $k => $v){
                $shop_ids[$v['shop_id']] = $v['shop_id'];
            }
            $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);*/
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['total'] = $tixian;
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:finance/account/logs.html';
    }

    public function so($type=null,$st=null)
    {
        if($type = (int)$type){
            $this->pagedata['type'] = $type;
        }
        if($st = (int)$st){
            $this->pagedata['st'] = $st;
        }
        $this->tmpl = 'admin:finance/account/so.html';
    }

    public function agree($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(!$detail = K::M('shop/tixian')->detail($tixian_id)){
                $this->msgbox->add('提现不存在或已经删除', 212);
            }elseif($detail['status'] == 2){
                $this->msgbox->add('该提现申请已拒绝', 213);
            }else{
                if(K::M('shop/tixian')->batch($tixian_id, array('status'=>1,'updatetime'=>__TIME))){
                    $this->msgbox->add('通过审核成功');
                }
            }
        }else if($ids = $this->GP('tixian_id')){
            $tixians = K::M('shop/tixian')->items(array('tixian_id'=>$ids));
            $tixian_ids = array();
            foreach($tixians as $k=>$v){
                if($v['status'] == 0){
                    $tixian_ids[] = $v['tixian_id'];
                }
            }
            if(K::M('shop/tixian')->batch($tixian_ids, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    
    
    public function refund($tixian_id = null)
    {
        if(!($tixian_id = (int) $tixian_id) && !($tixian_id = (int) $this->GP('tixian_id'))){
            $this->msgbox->add('未指要操作的体现ID', 211);
        }else if(!$detail = K::M('shop/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现不存在或已经删除', 212);
        }else if(!empty($detail['status'])){
            $this->msgbox->add('提现申请状态不可拒绝', 213);
        }else{
            if($this->checksubmit()){
                if($reason_content = $this->checksubmit('reason')){
                    if(K::M('shop/tixian')->update($tixian_id, array('status' => 2, 'reason' => $reason_content, 'updatetime' => __TIME))&&$log_id = K::M('shop/shop')->update_money($detail['shop_id'], $detail['money'], $reason_content . ',退回到商户余额')){
                        K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                        $this->msgbox->add('拒绝提现申请成功');
                    }
                }
                else{
                    $this->msgbox->add('拒绝理由不可为空', 220);
                }
            }
            else{
                $this->pagedata['detail'] = $detail;
                $this->tmpl = 'admin:finance/account/refund.html';
            }
        }
    }
    
    public function loan($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(K::M('shop/tixian')->batch($tixian_id, array('status'=>4,'updatetime'=>__TIME))){
                $this->msgbox->add('开始转账成功');
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('shop/tixian')->batch($ids, array('status'=>4,'updatetime'=>__TIME))){
                $this->msgbox->add('批量开始转账成功');
            }
        }else{
            $this->msgbox->add('未指定要开始转账的内容', 401);
        }
    }
    
    public function export($tx_ids)
    {    
        if(!$tx_ids = htmlspecialchars($tx_ids)){
            $this->msgbox->add('没有需要导出的数据', 201);
        }else{
            $tx_id = explode("_", $tx_ids);
            $filter = array('status'=>1,'tixian_id'=>$tx_id);
            if($items = K::M('shop/tixian')->items($filter, array('tixian_id'=>'asc'), 1, 1000, $count)){
                $shop_ids = array();
                foreach($items as $k=>$v){
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
                $shops = K::M('shop/shop')->items_by_ids($shop_ids);
                $a = array('提现编号','提现商家','提现账户','提现金额','审核时间','提现时间');
                $b = array();
                foreach($items as $v){
                    $b[] = array(
                        'tixian_id'=> $v['tixian_id'],
                        'shop'  => $shops[$v['shop_id']]['title'],
                        'account_info'=> $v['account_info'],
                        'money'   => $v['money'],
                        'updatetime' => date('Y-m-d H:i:s', $v['updatetime']),
                        'dateline' => date('Y-m-d H:i:s', $v['dateline']),
                    );                    
                }
                K::M('dataio/xls')->export($a, $b, '商户对账单');
            }else{
                $this->msgbox->add('没有需要导出的数据', 211);
            }
        }
    }

    public function money($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter = array('audit'=>1,'closed'=>0,'verify_name'=>'1');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = "LIKE:%".$SO['title']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['phone']){
                $filter['phone'] = "LIKE:%".$SO['phone']."%";
            }
            if($SO['shop_id']){
                $filter['shop_id'] = $SO['shop_id'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (o.title LIKE '%".$SO['keywords']."%' OR o.contact LIKE '%".$SO['keywords']."%' OR o.phone LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }
        
        $shop_ids = $shops = $accounts = array();
        if($items = K::M('waimai/waimai')->items_join_shop($filter, array('shop_id'=>'desc'), $page, $limit, $count)){
            /*foreach($items as $k=>$v){
                $shop_ids[] = $v['shop_id'];
            }
            $shops = K::M('shop/shop')->items_by_ids($shop_ids);*/
            $accounts = K::M('shop/account')->items_by_ids($shop_ids);

            /*foreach ($items as $k => $v) {
                if(!$shops[$v['shop_id']] ){
                    unset($items[$k]);
                }else{
                    $items[$k]['shop_info'] = $shops[$v['shop_id']];
                    $items[$k]['account_info'] = $accounts[$v['shop_id']];
                }
            }*/ 
            foreach ($items as $k => $v) {                
                $items[$k]['shop_info'] = array('mobile'=>$v['mobile']);
                $items[$k]['account_info'] = $accounts[$v['shop_id']];
            }        

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:finance/account/money.html";
    }

    public function tixian($shop_id)
    {
        if(!$shop_id = (int)$shop_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商户不存在或已被删除！',212);
        }else if(!$account = K::M('shop/account')->detail($shop_id)){
            $this->msgbox->add('商户未设置提现账户！',213);
        }else if($data = $this->checksubmit('data')){
            if(!$money = (float)$data['money']){
                $this->msgbox->add('提现金额不能为空！',214);
            }else if($money <= 0){
                $this->msgbox->add('提现金额有误！',215);
            }else if($log_id = K::M('shop/shop')->update_money($shop_id,-$money,'余额提现，扣款',$this->admin->admin_name.'('.$this->admin->admin_id.')')){
                $txdata = array(
                    'shop_id'=>$shop_id,
                    'money'=>$money,
                    'intro'=>'平台提现'.$money,
                    'account_info'=>'开户行：'.$account['account_type'].'，账户：'.$account['account_number'].',开户人：'.$account['account_name'],
                    'status'=>1,
                    'updatetime'=>__TIME,
                    'end_money'=>$money,
                    'payee_account' => $account['account_number']
                    );           
                if($tixian_id = K::M('shop/tixian')->create($txdata)){
                    K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                    $this->msgbox->add('提现成功！');
                }              
            }else{
                $this->msgbox->add('提现失败！',216);
            }
        }else{
            $this->pagedata['waimai'] = $waimai;
            $this->pagedata['account'] = $account;
            $this->tmpl = 'admin:finance/account/tixian.html';
        }
    }

    public function tixians()
    {
        if(!$shop_ids = $this->GP('shop_id')){
            $this->msgbox->add('参数有误！',211);
        }else{
            $shops = K::M('shop/shop')->items_by_ids($shop_ids);
            $accounts = K::M('shop/account')->items_by_ids($shop_ids);
            $admin = $this->admin->admin_name.'('.$this->admin->admin_id.')';
            foreach ($shops as $k => $v) {
                if(($account = $accounts[$v['shop_id']]) && ($v['money'] > 0)){
                    if($log_id = K::M('shop/shop')->update_money($v['shop_id'],-$v['money'],'余额提现，扣款',$admin)){
                        $txdata = array(
                            'shop_id'=>$v['shop_id'],
                            'money'=>$v['money'],
                            'intro'=>'平台提现'.$v['money'],
                            'account_info'=>'开户行：'.$account['account_type'].'，账户：'.$account['account_number'].',开户人：'.$account['account_name'],
                            'status'=>1,
                            'updatetime'=>__TIME,
                            'end_money'=>$v['money'],
                            'payee_account' => $account['account_number']
                            );           
                        if($tixian_id = K::M('shop/tixian')->create($txdata)){
                            K::M('shop/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                        }else{
                            $this->msgbox->add($v['title'].'提现失败！',213)->response();
                        }
                    }else{
                        $this->msgbox->add($v['title'].'提现失败！',212)->response();
                    }
                }
            }
            $this->msgbox->add('批量提现成功！');            
        }
    }

    public function shopso()
    {
        $this->tmpl = 'admin:finance/account/shopso.html';
    }
        
     /**
     * [支付宝一键转账]
     * @param  [type] $tixian_id [提现ID]
     * @return [type]            [description]
     */
     public function transfer($tixian_id=null)
    {
        if(!($tixian_id = (int) $tixian_id) && !($tixian_id = (int) $this->GP('tixian_id'))){
            $this->msgbox->add('提现记录不正确', 401);
        }elseif(!$tixian = K::M('shop/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现记录不正确', 401);
        }elseif($tixian['status'] != 1){
            $this->msgbox->add('提现记录不正确', 401);
        }else{
            $shop = K::M('shop/shop')->detail($tixian['shop_id']);
            //仅支持支付宝转帐 
            $site = K::$system->config->get('site');
            $data = array(
                    'trade_no' => $tixian['trade_no'],
                    'payee_account' => $tixian['payee_account'],
                    'amount' => $tixian['end_money'],
                    'title'  => $shop['title'].'('.$tixian['payee_account'].')提现转账',
                    'body'  => $shop['title'].'('.$tixian['payee_account'].')提现转账【'.$site['title'].'】'
                );
            if(!$payObj = K::M('trade/payment')->loadPayment('alipay')){
                $this->msgbox->add('生成转账申请失败', 401);
            }elseif($trade = $payObj->transfer($data, $msg)){
                $a = array('pay_status'=>1, 'pay_result'=>$msg, 'pay_time'=>__TIME, 'status'=>4);
                if(K::M('shop/tixian')->update($tixian_id, $a)){
                    $this->msgbox->add(' 转账成功');
                }
            }else{
                if(K::M('shop/tixian')->update($tixian_id, array('status'=>5, 'reason'=>$msg, 'updatetime'=>__TIME))){
                    K::M('shop/shop')->update_money($tixian['shop_id'], $tixian['money'], "提现退回({$msg})");
                }
                $this->msgbox->add('转账失败('.$msg.')', 421);
            }
        }  
    }
    /**
     * [提现退回]
     * @param  [type] $tixian_id [description]
     * @return [type]            [description]
     */
     public function reason($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = (int)$this->GP('tixian_id'))){
            $this->msgbox->add('未指要操作的体现ID', 211);
        }else if(!$detail = K::M('shop/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现不存在或已经删除', 212);
        }else if(!empty($detail['status'])){
            $this->msgbox->add('提现申请状态不可退回', 213);
        }
        if($this->checksubmit()){
            if($reason_content = $this->checksubmit('reason')){
                if(K::M('shop/tixian')->update($tixian_id, array('status'=>2, 'reason'=>$reason_content, 'updatetime'=>__TIME))){
                    K::M('shop/shop')->update_money($detail['shop_id'], $detail['money'], "提现退回({$reason_content})");
                    $this->msgbox->add('退回提现申请成功');
                }
            } else {
                $this->msgbox->add('退回理由不可为空',220);
            }
        } else {
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:shop/tixian/reason.html';
        }
    } 

}
