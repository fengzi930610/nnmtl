<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Qiang_Order extends Mdl_Table
{   
  
    protected $_table = 'qiang_order';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,qiang_id,qiang_title,qiang_price,qiang_discount_price,qiang_number,qiang_freight,notes,info,rules,type,photo,use_ltime,is_limit,ticket_status,is_ticket,number,use_time,bl';
    public function create($data, $checked=false)
    {
        if ($this->db->insert($this->_table, $data, true)) {
            $this->flush();
        }
        return true;
    }

    public function detail($order_id, $closed=false)
    {
        if(!$order_id = (int)$order_id){
            return false;
        }
        $where ="o.order_id=ext.order_id AND o.order_id=".$order_id;
        if(empty($closed)){
            $where .= " AND o.closed='0'";
        }
        $sql = "SELECT o.*, ext.* FROM " .$this->table('order')." o, ".$this->table($this->_table)." ext WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_data($row);
        }        
        return $row;
    }


    // 创建消费码 15位
    public function create_number($order_id)
    {    
        do{
            $no = '3'.date('ymd',__TIME) . rand(10000000, 99999999);
            $number = $this->db->GetRow("SELECT number FROM ".$this->table($this->_table)." WHERE number='{$no}'");
        } while ($number);
        if(isset($no)) {
            $this->update($order_id,array('number'=>$no, 'ticket_status'=>1));
        }
    }

    // 查询
    public function detail_by_number($number, $closed=false)
    {
        if(!preg_match('/^(\d+)$/i', $number)){
            return false;
        }
        if(empty($closed)){
            $where .= " AND o.closed='0'";
        }
        $where ="o.order_id=ext.order_id AND ext.number=".$number;
        $sql = "SELECT o.*, ext.* FROM " .$this->table('order')." o, ".$this->table($this->_table)." ext WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_data($row);
            
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
            $amount = $order['amount'] + $order['money'];
        }
        return $amount;
    }

    public function set_payed($log, $trade=array())
    {         
        $order_id = $log['order_id'];
        if(!$order = $this->detail($order_id)){
            return false;
        }elseif($order['order_status'] < 0){
            //订单取消后收到支付结果通知
            return false;
        }else if($res =  K::M('order/order')->update($order_id, array('pay_status' => 1),true)){
            $a = array('online_pay'=>1, 'pay_time'=>__TIME,'lasttime'=>__TIME,'pay_code'=>$trade['code'], 'trade_no'=>$trade['trade_no']);
            K::M('order/order')->update($order_id, $a, true);
            if($trade['code'] == 'money') {
                $logmsg = '订单余额支付成功';
            }else {
                $logmsg = '订单支付成功';
            }
            K::M('order/log')->create(array('order_id'=>$order_id,'from'=>'payment','log'=>$logmsg,'status'=>$order['order_status']));

            if($order['pei_type'] == 3) { // 自提订单 创建消费码
                if(K::M('order/order')->update($order_id,array('order_status'=>5,'lasttime'=>__TIME),true)){ //直接更新抢购订单状态为待消费
                    $this->create_number($order_id);
                }
                $addr = '客户自提';
            }else{
                $addr = $order['addr'].$order['house'];
            }
            $title = sprintf("您有新的抢购订单(单号：%s)", $order_id);
            $content = sprintf("您有新的抢购订单(单号：%s)，客户%s(电话：%s)配送地址:%s", $order_id, $order['contact'], $order['mobile'], $addr);
            K::M('shop/shop')->send($order['shop_id'], $title, $content, array('type'=>'newOrder','order_id'=>$order_id));
            $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

            if($order['order_from'] != 'wxapp'){
                K::M('order/order')->send_member('订单支付成功', sprintf("您在[%s]下的订单(%s)，支付成功", $waimai['title'], $order_id), $order);
            }
        }
        return $res;
    }

    //确认订单 ，结算订单
    public function confirm($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order = $this->detail($order_id)){
            return false;
        }else if(in_array($order['order_status'], array(5,6))){
            if($this->db->update('order', array('order_status'=>8), "order_id='{$order_id}'", true)){
                K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);
                
                if($order['pei_type'] == 3 && $order['pay_status']){ //到店消费
                    $this->update($order_id, array('use_time'=>__TIME), true);
                }

                $tongji = array(
                    'amount'=>$order['amount']+$order['money'],
                    'order_id'=>$order['order_id'],
                    'from'=>'qiang',
                    'shop_id'=>$order['shop_id'],
                    'uid'=>$order['uid'],
                    'staff_id'=>$order['staff_id'],
                    'pei_amount'=>$order['pei_amount'],
                    'year'=>date('Y',__TIME),
                    'mouth'=>date('Ym',__TIME),
                    'day'=>date('Ymd',__TIME),
                    'hour'=>date('H',__TIME),
                    'dateline'=>__TIME,
                    'city_id'=>$order['city_id'],
                    'site_fee'=>0
                    );
                $shop_amount = $fee = 0;
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

                $log = '订单完成结算(ID:'.$order_id.')';
                $shop_amount = $order['amount'] + $order['money'];
                if($shop_amount){
                    $fee =  number_format(($shop_amount * $order['bl'])/100, 2, '.', '');

                    //修改商家结算比例 --20171202 叶超
                    $bill_data = array();
                    $bill_data['shop_id'] = $order['shop_id'];
                    $bill_data['status'] = 0;
                    $bill_data['amount'] = $shop_amount-$fee;
                    $bill_data['fee'] = $fee;
                    $bill_data['user_amount'] = $order['amount'];
                    $bill_data['freight'] = $order['qiang_freight'];

                    K::M('qiang/bills')->create($bill_data);
                    $filter = array(
                        'bills_sn'=>date('Ymd'),
                        'shop_id' => $order['shop_id']
                    );
                    $bills = K::M('qiang/bills')->find($filter);
                    $data = array('bills_id'=>$bills['bills_id'],
                        'bills_sn'=>date('Ymd'),
                        'shop_id'=>$order['shop_id'],
                        'bills_number'=>$order['order_id'],
                        'status'=>0,
                        'amount'=>$shop_amount-$fee,
                        'fee'=>$fee,
                        'freight'=> $bill_data['freight'],
                        'user_amount'=>$order['amount'],
                        'bl'=>(float)$order['bl'],
                        'type' => $order['type'],
                        'count' => $order['qiang_number'],
                    );
                    K::M('qiang/billslog')->create($data);
                    //修改对账单 --end

                     //数据统计商家部分
                    $tongji['shop_amount'] = $data['amount'];
                    $tongji['shop_fee'] = $fee;
                      //数据统计商家部分
                    $tongji['site_fee'] =  $tongji['site_fee']+$fee;

                }

                //生成统计数据
                K::M('site/tongji')->create($tongji);
                //生成统计数据


                if($order['first_order']){
                    if($m = K::M('member/member')->detail($order['uid'])){
                        if(preg_match('/(B|S|D|M)(\d+)/i', $m['pmid'], $a)){
                            if($a[1] == 'M'){ //会员邀请
                                K::M('member/invite')->update($order['uid'],array('status'=>1));
                                $inviteCfg = K::$system->config->get('invite');
                                if ($inviteCfg['is_inviter_hongbao'] == 1) { // 邀请人是否奖励红包
                                    if($pm = K::M('member/member')->detail((int)$a[2])){
                                        K::M('member/invite')->send_hongbao_by_cfg($inviteCfg['inviter_hongbao_cfg'], $pm['uid'], 5);//邀请人是否奖励红包
                                    }
                                }
                            }
                            if($a[1] == 'D'){ //推广员推荐
                                $dituiCfg = K::$system->config->get('ditui');
                                if ($dituiCfg['order_amount']) { // 推荐用户首单，奖励推荐人
                                    if($dm = K::M('ditui/ditui')->detail((int)$a[2])){
                                        if(K::M('ditui/ditui')->update_money($dm['ditui_id'], $dituiCfg['order_amount'])){
                                            K::M('ditui/ditui')->update_ordercount($dm['ditui_id']);
                                            if($dtm = K::M('ditui/member')->find(array('ditui_id'=>$dm['ditui_id'],'uid'=>$order['uid']))){
                                                $dm_data = array();
                                                $dm_data['first_amount'] = $dituiCfg['order_amount'];
                                                $dm_data['first_order_id'] = $order_id;
                                                $dm_data['first_order_amount'] = (float)($order['amount'] + $order['money']);
                                                $dm_data['first_order_time'] = $order['dateline'];
                                                K::M('ditui/member')->update($dtm['mid'],$dm_data);
                                            }

                                            $intro = L('推荐用户首单奖励金额');
                                            K::M('ditui/log')->log($dm['ditui_id'],$order['uid'],$dituiCfg['order_amount'],$intro,'invite');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //确认订单积分获得  2017/11/10  by yufan
                if($order['online_pay'] == 1 && $order['jifen_cfg']['jifen_type'] == 2){
                    K::M('jifen/jifen')->update_jifen($order_id,$order);
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
                if($order['order_from'] != 'wxapp' || $from != 'member'){
                    K::M('order/order')->send_member('订单已完成', sprintf("您在[%s]下的订单(%s)，{$log}", $waimai['title'], $order_id), $order);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @function  取消/退单 退回余额+在线支付金额到余额，退回红包
     * @params  $order_id
     * @params  $order
     * @params  $from  string  由哪个角色取消的[member, staff, shop, admin]
     */
    public function cancel($order_id=null, $order=null, $from='member', $extend = '')
    {
        $order_id = (int)$order_id;
        if(!$order = $this->detail($order_id)){
            return false;
        }elseif($order['order_status'] < 0){
            return false;
        }elseif(in_array($order['order_status'], array(0, 5))){ 
            if($this->db->update('order', array('order_status'=>-1), "order_id='{$order_id}'", true)){
                if($order['online_pay'] && $order['pay_status']){  //取消退款
                    K::M('order/order')->update($order_id, array('order_status'=>-2,'lasttime'=>__TIME), true);// 更新时间
                }else{
                    K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);// 更新时间
                }

                if($order['online_pay'] && $order['pay_status']){
                    $refund_money = $refund_amount = 0;
                    $refund_money = $order['money'];
                    if($order['pay_status'] && ($order['amount'] > 0)){
                        $refund_amount = $order['amount'];
                        //原路退回支付金额
                        if(in_array($order['pay_code'], array('wxpay', 'alipay'))){
                            $order['refund_amount'] = $order['amount'];
                            $order['refund_reason'] = '订单(ID:'.$order['order_id'].')取消退款';
                            if(!$trade = K::M('trade/payment')->refund($order['pay_code'], $order, $msg)){
                                //原路退还失败时退回的余额
                                $refund_money += $order['amount'];
                                $refund_log = "原路返回失败,资金退回余额";
                            }else{
                                $refund_log = $trade['refund_log'] ? $trade['refund_log'] : '资金退回';
                            }
                        }else {
                            $refund_money += $order['amount'];
                            $refund_log = '资金退回';
                        }
                    }
                    if($refund_money > 0){
                        K::M('member/member')->update_money($order['uid'], $refund_money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                    }
                }

                //退回商品库存
                $this->return_sku($order_id, $order);
                //更新商户订单数量
                K::M('shop/shop')->update_count($order['shop_id'], 'orders', -1);
                //更新会员订单数量
                K::M('member/member')->update_count($order['uid'], 'orders', -1);
                
                if($from == 'admin'){
                    $log = '管理员取消订单(订单号:'.$order['order_id'].')'.$extend;
                    $refund_log = '管理员取消订单,'.$refund_log;
                    K::M('member/member')->send($order['uid'], '订单被取消', $log, array('tag_and'=>'login_on'));
                    if($order['staff_id']){
                        K::M('staff/staff')->send($order['staff_id'], '配送订单被取消', $log, array('tag_and'=>'login_on'));
                    }
                    if($order['pay_status'] || !$order['online_pay']){
                        K::M('shop/shop')->send($order['shop_id'], '订单被取消', $log, array('tag_and'=>'login_on'));
                    }
                }else if($from == 'shop'){
                    $log = '商家取消订单(订单号:'.$order['order_id'].')'.$extend;
                    $refund_log = '商家取消订单,'.$refund_log;
                    K::M('member/member')->send($order['uid'], '订单被取消', $log, array('tag_and'=>'login_on'));
                    if($order['staff_id']){
                        K::M('staff/staff')->send($order['staff_id'], '配送订单被取消', $log, array('tag_and'=>'login_on'));
                    }   
                }else if($from == 'staff'){
                    if(in_array($order['from'], array('waimai', 'paotui'))) {
                        $log = '骑手取消订单(订单号:'.$order['order_id'].')'.$extend;
                    }else{
                        $log = '师傅取消订单(订单号:'.$order['order_id'].')'.$extend;
                    }
                    K::M('member/member')->send($order['uid'], '骑手取消配送订单', $log, array('tag_and'=>'login_on'));
                    K::M('shop/shop')->send($order['shop_id'], '骑手取消配送订单', $log, array('tag_and'=>'login_on'));
                }else if($from == 'system'){
                    $log = '系统取消了订单(订单号:'.$order['order_id'].')'.$extend;
                    $refund_log = '系统取消了订单,'.$refund_log;
                    K::M('member/member')->send($order['uid'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    if($order['staff_id']){
                        K::M('staff/staff')->send($order['staff_id'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    }
                    if($order['pay_status'] || !$order['online_pay']){
                        K::M('shop/shop')->send($order['shop_id'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    }                    
                }else{
                    $log = '用户取消订单(订单号:'.$order['order_id'].')'.$extend;
                    $refund_log = '用户取消订单,'.$refund_log;
                }

                K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                if($refund_amount > 0){
                    K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id']));
                }
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

                if($order['order_from'] != 'wxapp' || $from != 'member'){
                    K::M('order/order')->send_member('订单已取消', sprintf("您在[%s]下的订单(%s)，{$log}", $waimai['title'], $order_id), $order);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @function  过期处理 
     * @params  $order_id
     * @params  $order
     * @params  $from  string  由哪个角色取消的[member, staff, shop, admin]
     */
    public function expired($order_id=null, $order=null, $from='system', $extend = '')
    {
        $order_id = (int)$order_id;
        if(!$order = $this->detail($order_id)){
            return false;
        }elseif($order['order_status'] < 0){
            return false;
        }elseif(in_array($order['order_status'], array(0, 5))){ 
            if($this->db->update('order', array('order_status'=>-3), "order_id='{$order_id}'", true)){
                K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);// 更新时间

                if($from == 'system'){
                    $log = '系统取消了订单(订单号:'.$order['order_id'].')'.$extend;
                    K::M('member/member')->send($order['uid'], '抢购订单已过期', $log, array('tag_and'=>'login_on'));
                    if($order['pay_status'] || !$order['online_pay']){
                        K::M('shop/shop')->send($order['shop_id'], '抢购订单已过期', $log, array('tag_and'=>'login_on'));
                    }
                } 
                K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                if($order['order_from'] != 'wxapp' || $from != 'member'){
                    K::M('order/order')->send_member('抢购订单已过期', sprintf("您在[%s]下的订单(%s)，{$log}", $waimai['title'], $order_id), $order);
                }
                return true;
            }
        }
        return false;
    }
    
    public function return_sku($order_id, $order=null)
    {
        if(empty($order) && !($order = $this->detail($order_id))){
            return false;
        }
        $data = array('sales'=>'`sales`-'.$order['qiang_number'],'sku'=>'`sku`+'.$order['qiang_number']);
        return K::M('qiang/qiang')->update($order['qiang_id'], $data, true);
    }

    public function _format_data($row)
    {
        /*pay 去支付 comment 评论 canel 取消 confirm 确认送达 payback 申请退款 see 查看评价 wuliu 查看物流 ticket 查看券码 expired 已过期 delivery 发货*/
        /*order_status -3 已过期 -2 已退款 -1 已取消 0 待支付 待发货 5 待消费 6 待收货 8 已完成 */
        $show_btn = array();
        $msg = $status_label = '';
        $ziti_label = '物流发货';
        $notes = $row['notes'] ? unserialize($row['notes']) : array();
        if($row['from'] == 'qiang'){
            if($row['order_status'] == -3){
                $status_label = '已过期';
                $msg = '已过期';
                $show_btn = array(
                    'expired'=>1,
                );
            }else if($row['order_status'] == -2){
                $status_label = '已退款';
                $msg = '已退款';
                $show_btn = array(
                    'detail' =>1
                );
            }else if($row['order_status'] == -1){
                $status_label = '已取消';
                $msg = '已取消';
                $show_btn = array(
                    'detail' =>1
                );
            }else if($row['order_status'] == 0){
                if($row['pay_status'] == 0){
                    $status_label = '未付款';
                    $msg = '未付款';
                    $show_btn = array(
                        'canel'=>1,'pay'=>1,
                    );
                }else if($row['pay_status'] == 1){
                    $status_label = '待发货';
                    if($notes['is_tui'] == 1){ //支持退款
                        $msg = '待发货';
                        $show_btn = array(
                            'payback'=>1,
                            'delivery'=>1,
                        );
                    }else{
                        $msg = '待发货';
                        $show_btn = array(
                            'delivery'=>1,
                        );
                    }
                }
            }else if($row['order_status'] == 5){
                $status_label = '待消费';
                if($row['ticket_status'] == 1 && !empty($row['use_ltime']) && $row['use_ltime'] < __TIME){
                    $status_label = '已过期';
                    $msg = '已过期';
                    $show_btn = array(
                        'expired'=>1,
                    );
                }else if($notes['is_tui'] == 1){ //支持退款
                    $msg = '待消费';
                    $show_btn = array(
                        'ticket'=>1,'payback'=>1,
                    );
                }else{
                    $msg = '待消费';
                    $show_btn = array(
                        'ticket'=>1,
                    );
                }
            }else if($row['order_status'] == 6){
                $status_label = '待收货';
                $msg = '待收货';
                $show_btn = array(
                    'confirm'=>1,'wuliu'=>1
                );
            }else if($row['order_status'] == 8){
                $status_label = '已完成';
                if($row['comment_status'] == 1){
                    $msg = '已评价';
                    $show_btn = array(
                        'see'=>1,
                    );
                }else{
                    $msg = '等待评价';
                    $show_btn = array(
                        'comment'=>1,
                    );
                }
                if($row['pei_type'] == 0 && !empty($row['express'])){
                    $show_btn['wuliu'] = 1;
                }
            }
        }

        if($row['pei_type'] == 3){
            $ziti_label = '到店消费';
        }

        $row['ziti_label'] = $ziti_label;
        $row['status_label'] = $status_label;
        $row['msg'] = $msg;
        $row['show_btn'] = $show_btn;

        return $row;
    }
}