<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Mall_Order extends Mdl_Table
{   
  
    protected $_table = 'mall_order';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,product_id,product_jifen,product_price,product_number,freight';


    public function detail($order_id, $closed=false) 
    {
        if(!$order_id = (int)$order_id){
            return false;
        }
        if(empty($closed)){
            $where .= " AND o.closed='0'";
        }
        $where ="o.order_id=ext.order_id AND o.order_id=".$order_id;
        $sql = "SELECT o.*, ext.* FROM " .$this->table('order')." o, ".$this->table($this->_table)." ext WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = K::M('order/order')->order_format_row($row);   
        }
        return $row;
    }

    public function get_payment_amount($order_id, &$level=0)
    {
        if(!$order = $this->detail($order_id)){
            return false;
        }else if($order['pay_status']){
            return false;
        }else if($order['order_status']<0 || $order['order_status']==8){
            return false;
        }else{
            $level = 0;
            $amount = $order['amount'];
        }
        return $amount;
    }

    //返回订单需要退回的金额
    public function get_return_amount($order_id, $order=null)
    {
        $amount = 0;
        if($order === null && !($order = $this->detail($order_id))){
            return false;
        }else if($order['order_status'] !=0){
            return false;
        }else if($order['pay_status'] == 1){
            $amount = $order['product_price'] + $order['freight'];
        }
        return $amount;
    }

    public function return_sku($order_id, $order=null)
    {
        if(empty($order) && !($order = $this->detail($order_id))){
            return false;
        }
        if($produt_list = K::M('mall/order/product')->items(array('order_id'=>$order_id))){
            foreach($produt_list as $v){
                $data = array('sales'=>'`sales`-'.$v['product_number'], 'sku'=>'`sku`+'.$v['product_number']);
                K::M('mall/product')->update($v['product_id'], $data, true);
            }
        }
        return true;
    }

    public function format_data($row){

            if($row['order_status'] == -1){
                $label = '已取消';
            }else if(empty($row['order_status']) && empty($row['pay_status']) && $row['online_pay'] == 1) {
                $label = '待支付';
            }else if(empty($row['order_status']) && $row['pay_status'] == 1 && $row['online_pay'] == 1){
                $label = '待发货';
            }else if($row['order_status']==1){
                $label = '待收货';
            }else if($row['order_status']==8){
                $label = '已完成';
            }else{
                $label = '已完成';
            }
        $row['order_status_label'] = $label;
        return $row;
    }

    public function set_payed($log, $trade=array())
    {
        $order_id = $log['order_id'];
        if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        }else if($res = $this->db->update('order', array('pay_status' => 1), "order_id='{$order_id}'", true)){
            $a = array('online_pay'=>1, 'pay_time'=>__TIME,'lasttime'=>__TIME,'pay_code'=>$trade['code']);
            //如果下单时选择了服务人员更新订单order_status为1
            if(in_array($order['from'], array('house', 'weixiu', 'paotui')) && $order['order_status']==0 && $order['staff_id'] > 0){
                $a['order_status'] = 1;
            }
            K::M('order/order')->update($order_id, $a, true);
            if($trade['code'] == 'money') {
                $logmsg = '订单余额支付成功';
            }else {
                $logmsg = '订单支付成功';
            }
            K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'payment','log'=>$logmsg,'status'=>$order['order_status']));

        }
        return $res;
    }

    public function cancel($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = K::M('order/order')->detail($order_id))){
            return false;
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消过，不能再取消', 449);
            return false;
        }else if(in_array($order['order_status'], array(0, 1, 2, 3, 4, 5))){ ////-1:已取消，0：未处理，1：已接单，2：已配货，3：配送开始，4：配送完成，8：订单完成

            if($this->db->update('order', array('order_status'=>-1,'lasttime'=>__TIME), "order_id='{$order_id}'", true)){ //防止并发多退钱

                if($order['hongbao_id']){ //退还红包
                    K::M('hongbao/hongbao')->update($order['hongbao_id'], array('order_id'=>0, 'used_time'=>0, 'used_ip'=>''));
                }
                 if($order['from'] == 'mall'){

                    //退回积分和余额
                    $mall_order = K::M('mall/order')->detail($order_id);
                    $money = $mall_order['product_price'];
                    $MEMBER = K::M('member/member')->detail($order['uid']);
                    $mall_up = array(
                        'jifen'=>$MEMBER['jifen']+$mall_order['product_jifen']
                    );

                    K::M('member/member')->update_jifen($MEMBER['uid'], $mall_order['product_jifen'], '商城订单(ID:' . $order['order_id'] . ')取消返还积分', $from);
                    //K::M('member/log')->log($MEMBER['uid'], 'jifen', $mall_order['product_jifen'], '取消商城订单，返回积分');

                    if($order['pay_status'] == 1){
                        $mall_up['money']=$MEMBER['money']+$money+$mall_order['freight'];
                        //K::M('member/log')->log($MEMBER['uid'], 'money', ($money+$mall_order['freight']), '取消商城订单，返回余额');
                        K::M('member/member')->update_money($MEMBER['uid'],$money+$mall_order['freight'],'商城订单(ID:' . $order['order_id'] . ')取消返还余额');
                    }else if($order['money']){
                        K::M('member/member')->update_money($MEMBER['uid'],$order['money'],'商城订单(ID:' . $order['order_id'] . ')取消返还余额');
                    }
                    
                    //K::M('member/member')->update($MEMBER['uid'],$mall_up);

                }

                //取消订单 返回库存
                $this->return_sku($order_id);

                //更新商户订单数量
                if($order['shop_id']){
                    if($order['from'] == 'waimai'){
                        K::M('waimai/waimai')->update_count($order['shop_id'], 'orders', -1);
                    }else{
                        K::M('shop/shop')->update_count($order['shop_id'], 'orders', -1);
                    }
                }
                //更新服务人员订单数量
                if($order['staff_id']){
                    K::M('staff/staff')->update_count($order['staff_id'], 'orders', -1);
                }
                //更新会员订单数量
                if($order['uid']){
                    K::M('member/member')->update_count($order['uid'], 'orders', -1);
                }

                if($from == 'admin'){
                    $log = '管理员取消订单(订单号:'.$order['order_id'].')';
                }else if($from == 'shop'){
                    $log = '商家取消订单(订单号:'.$order['order_id'].')';
                }else if($from == 'staff'){
                    if(in_array($order['from'], array('waimai', 'paotui'))) {
                        $log = '骑手取消订单(订单号:'.$order['order_id'].')';
                    }else{
                        $log = '师傅取消订单(订单号:'.$order['order_id'].')';
                    }
                }else if($from == 'system'){
                    $log = '超时未支付系统取消了订单(订单号:'.$order['order_id'].')';
                }else{
                    $log = '用户取消订单(订单号:'.$order['order_id'].')';
                }
                K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                return true;
            }else{
                echo 2;die;
            }
        }
        return false;
    }

    //确认订单 ，结算订单
    public function confirm($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = K::M('order/order')->detail($order_id))){
            return false;
        }else if(!$order = K::M("{$order['from']}/order")->detail($order_id)){
            return false;
        }else if(in_array($order['order_status'], array(1,2,3,4,5))){ //-1:已取消，0：未处理，1：已接单，2：已配货，3：开始工作，4：完成工作，5：待完成/补差价，8：订单完成
            $order_id = $order['order_id'];
            if($this->db->update('order', array('order_status'=>8,'lasttime'=>__TIME), "order_id='{$order_id}'", true)){
                $staff_amount = $shop_amount = 0;
                if($order['online_pay']){
                    $log = $staff_log = '订单完成结算(ID:'.$order_id.')';
                    $jifen_order = K::M('mall/order')->detail($order_id);
                    $bills_data = array();
                    $bills_data['fee'] = $order['amount'];
                    $bills_data['jifen'] = $jifen_order['product_jifen'];
                    if($sql = K::M('mall/bills')->create($bills_data)){
                        $filter_bill = array();
                        $filter_bill['bills_sn'] = date('Ymd');
                        $bill = K::M('mall/bills')->find($filter_bill);
                        $log_data = array();
                        $log_data['bills_id'] = $bill['bills_id'];
                        $log_data['fee'] = $order['amount'];
                        $log_data['jifen'] = $jifen_order['product_jifen'];
                        $log_data['uid'] = $order['uid'];
                        $log_data['bills_sn'] = date('Ymd');
                        $log_data['dateline'] = __TIME;
                        $log_data['order_id'] = $order_id;
                        K::M('mall/billslog')->create($log_data);
                    }

                    // 首单奖励发放
                    if(K::M('order/order')->count(array('uid'=>$order['uid'], 'order_status'=>8))===1){
                        if($m = K::M('member/member')->detail($order['uid'])){
                            if(preg_match('/(B|S|D|M)(\d+)/i', $m['pmid'], $a)){
                                if($a[1] == 'M'){ //会员邀请
                                    $inviteCfg = K::$system->config->get('invite');
                                    if(($invite_order_money = (float)$inviteCfg['invite_order_money'])>0){
                                        if($pm = K::M('member/member')->detail((int)$a[2])){
                                            K::M('member/member')->update_money($pm['uid'], $invite_order_money, sprintf(L('邀请用户(%s)首单奖励:￥%s'), $m['nickname'], $invite_order_money));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($from == 'admin'){
                    $log = '管理员确认订单完成';
                }else if($from == 'system'){
                    $log = '超过3小时系统自动确认订单完成';
                }else if($from == 'shop'){
                    $log = '商家确认订单完成';
                }else{
                    $log = '用户确认订单完成';
                }
                K::M('order/log')->create(array('order_id'=>$order_id,'from'=>$from,'log'=>$log,'status'=>8));
                return true;
            }
        }
        return false;
    }
    
}
