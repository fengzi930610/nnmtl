<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Paotui_Order extends Mdl_Table
{
    protected $_table = 'paotui_order';
    protected $_pk    = 'order_id';
    protected $_cols  = 'order_id,from,o_lng,o_lat,lng,lat,o_addr,addr,amount,tip,product,dateline,contact,mobile,o_mobile,o_contact,yuji_price,price,weight,type,ext_shop_id,extend,ext_order_id';
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data);
    }

    protected function _check($data, $order_id=null)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
        if(isset($data['o_lat'])){
            $data['o_lat'] = round(bcmul($data['o_lat'], 1000000));
        }
        if(isset($data['o_lng'])){
            $data['o_lng'] = round(bcmul($data['o_lng'], 1000000));
        }
        return parent::_check($data, $order_id);
    }



    public function get_payment_amount($order_id, &$level=0)
    {
        if(!$order = $this->detail($order_id)){
            return false;
        }else if($order['pay_status']){
            return false;
        }else if($order['order_status']<0 || $order['order_status']==8){
            return false;
        }else if($order['order_status'] == 5){
            $level = 1;
            $amount = $order['jiesuan_amount'] - $order['danbao_amount'] - $order['hongbao'];
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
        }else if($order['order_status'] < 0 || $order['order_status'] == 8){
            return false;
        }else if($order['pay_status']){
            $amount = $order['amount'] + $order['money'];
        }else{
            $amount = $order['money'];
        }
        return $amount;
    }
    public function return_sku($order_id, $order=null)
    {
        return true;
    }
    protected function _format_row($row)
    {
        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }
        if($row['o_lat']){
            $row['o_lat'] = bcdiv($row['o_lat'], 1000000,6);
        }
        if($row['o_lng']){
            $row['o_lng'] = bcdiv($row['o_lng'], 1000000,6);
        }
        $row['product'] = $row['product']?unserialize($row['product']):array();

        switch ($row['from']) {
            case 'mai':
                $row['from_label'] = "帮我买";
                break;
            case 'song':
                $row['from_label'] = "帮我送";
                break;
            default:
                $row['from_label'] = "";
                break;
        }
        $row['extend'] =  $row['extend'] ?unserialize($row['extend']):array();

        return $row;


    }


    public function set_payed($log, $trade=array())
    {
        $order_id = $log['order_id'];
        if(!$order = K::M('order/order')->detail($order_id)){
            return false;

        }else if($res = K::M('order/order')->update($order_id,array('pay_status' => 1),true)){
            $a = array('online_pay'=>1, 'pay_time'=>__TIME,'lasttime'=>__TIME,'pay_code'=>$trade['code'], 'trade_no'=>$trade['trade_no']);
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
            K::M('order/time')->update($order_id,array('pay_time'=>__TIME));
            if(in_array($order['from'], array('weixiu', 'house', 'paotui'))){
                if($order['order_status']==5 && $order['staff_id'] /*&& $log['pay_level'] > 0*/){ //二次支付后直接订单结算
                    $this->confirm($order_id);
                    $title = sprintf("订单付款完成(订单：%s)",  $order_id);
                    $content = sprintf("客户%s(电话：%s)补付了订单尾款￥%s(订单：%s)",  $order['contact'], $order['mobile'], $log['amount'], $order_id);
                    K::M('staff/staff')->send($order['staff_id'], $title, $content, array('type'=>'order', 'order_id'=>$order_id));
                }else if($staff_id = (int)$order['staff_id']){
                    if($staff = K::M('staff/staff')->detail($staff_id)){
                        //更新师傅订单统计
                        K::M('staff/staff')->update_count($staff_id, 'orders', 1);
                        //通知师傅处理订单
                        $addr = $order['addr'].$order['house'];
                        $content = sprintf("客户%s(电话：%s)预约了您(订单号：%s),地址:%s", $order['contact'], $order['mobile'], $order_id, $addr);
                        K::M('staff/staff')->send($staff_id, '您有新的订单需要处理', $content, 'newOrder', $order_id);
                    }
                }
            }
        }
        return $res;
    }
    //确认订单 ，结算订单
    public function confirm($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = K::M('order/order')->detail($order_id))){
            return false;
        }else if(!$paotui_order = $this->detail($order_id)){
            return false;
        }else if(in_array($order['order_status'], array(1,2,3,4,5))){ //-1:已取消，0：未处理，1：已接单，2：已配货，3：开始工作，4：完成工作，5：待完成/补差价，8：订单完成
            $order_id = $order['order_id'];
            K::M('order/time')->update($order_id,array('order_compltet_time'=>__TIME));

            if($other_order = K::M('other/order')->find(array('p_order_id'=>$order_id))){
                K::M('order/time')->update($other_order['order_id'],array('order_compltet_time'=>__TIME));
            }

            if(K::M('order/order')->update($order_id, array('order_status'=>8), true)){
                $staff_amount = 0;
                if($order['online_pay']){
                    //初始化统计数据 begin
                    $tongji = array(
                        'amount'=>$order['amount']+$order['money'],
                        'order_id'=>$order['order_id'],
                        'from'=>'paotui',
                        'shop_id'=>$order['shop_id'],
                        'uid'=>$order['uid'],
                        'staff_id'=>$order['staff_id'],
                        'pei_amount'=>$order['pei_amount'],
                        'year'=>date('Y',$order['dateline']),
                        'mouth'=>date('Ym',$order['dateline']),
                        'day'=>date('Ymd',$order['dateline']),
                        'hour'=>date('H',$order['dateline']),
                        'dateline'=>$order['dateline'],
                        'city_id'=>$order['city_id']
                    );
                    //初始化统计数据 end
                    //其他情况处理
                    $log = $staff_log = '订单完成结算(ID:'.$order_id.')';
                    $staff_amount = $order['pei_amount'] ;
                    if($staff_amount > 0){
                        //K::M('staff/staff')->update_money($order['staff_id'], $staff_amount, $log);
                        $true_staff_amount = K::M('staff/config')->get_peiamount($order,$order['staff_id'],$staff_amount);
                        // -----配送人员对账单----- begin
                        $percent_and_money = K::M('staff/staff')->get_percent_and_money($order['staff_id'], $true_staff_amount); // 获取当前提现比例下，配送员最终可提现金额以及比例（每天入账金额，提现不再读取比例）

                        $staff_diff_amount = max(0,$true_staff_amount-$staff_amount); //平台补贴 2018/02/27 by yufan
                        
                        $bill_data = array(
                            'staff_id' => $order['staff_id'],
                            'freight_amount' => $true_staff_amount,
                            'amount' => $percent_and_money['end_money'],
                            'diff_amount'=> $staff_diff_amount,                      //平台补贴 2018/02/27 by yufan
                            'orders' => 1
                        );
                        if (K::M('staff/bills')->create($bill_data)) {
                            $bills = K::M('staff/bills')->find(array('bills_sn'=>date('Ymd'), 'staff_id'=>$order['staff_id']));
                            //  对账单日志
                            $bill_log = array(
                                'bills_id' => $bills['bills_id'],
                                'bills_sn' => date('Ymd'),
                                'staff_id' => $order['staff_id'],
                                'order_id' => $order_id,
                                'freight_amount' => $true_staff_amount,
                                'tixian_percent' => $percent_and_money['tixian_percent'],
                                'amount' => $percent_and_money['end_money'],
                                'diff_amount' => $staff_diff_amount,                 //平台补贴 2018/02/27 by yufan
                                'dateline'=>__TIME
                            );
                            K::M('staff/billslog')->create($bill_log);
                        }
                        // -----配送人员对账单----- end
                        //这里生成骑手补贴相关 外卖3.8
                        if($staff_amount<$true_staff_amount){
                            $staff = K::M('staff/staff')->detail($order['staff_id']);
                            $subsidy_staff = array(
                                'order_id'=>$order_id,
                                'staff_id'=>$order['staff_id'],
                                'pei_amount'=>$staff_amount,
                                'staff_amount'=>$true_staff_amount,
                                'diff_amount'=>$true_staff_amount-$staff_amount,
                                'from'=>'paotui',
                                'bl'=>$percent_and_money['tixian_percent'],
                                'year'=>date('Y',$order['dateline']),
                                'mouth'=>date('Ym',$order['dateline']),
                                'day'=>date('Ymd',$order['dateline']),
                                'hour'=>date('H',$order['dateline']),
                                'dateline'=>$order['dateline'],
                                'group_id'=>$order['group_id'],
                                'city_id'=>$staff['city_id']
                            );
                            K::M('subsidy/staff')->create($subsidy_staff);
                        }
                        $tongji['staff_fee'] =$staff_amount- $percent_and_money['end_money'];
                        $tongji['staff_amount'] = $percent_and_money['end_money'];
                        $tongji['platform_staff'] = ($percent_and_money['end_money']-$staff_amount)>0? ($percent_and_money['end_money']-$staff_amount):0;
                        //这里生成骑手补贴相关 外卖3.8
                        $tongji['site_fee']=$true_staff_amount-$percent_and_money['end_money'];
                    }
                    //数据统计商家部分
                    $tongji['shop_amount'] = 0;
                    $tongji['shop_fee'] = 0;
                    //数据统计商家部分
                    //统计  优惠相关
                    $tongji['platform_first'] =0;
                    $tongji['platform_mj'] = 0;
                    $tongji['platform_hongbao'] = $order['hongbao'];
                    $tongji['shop_first'] =0;
                    $tongji['shop_mj'] =  0;
                    $tongji['shop_coupon'] = 0;
                    $tongji['shop_discount'] = 0;
                    //统计 优惠相关

                    //生成统计数据
                    K::M('site/tongji')->create($tongji);
                    //生成统计数据

                    //商家补贴统计
                    if(($order['hongbao']>0)){
                        $group = K::M('pei/group')->set_cache($order['group_id']);
                        $subsidy_waimai = array();
                        $subsidy_waimai['order_id'] = $order['order_id'];
                        $subsidy_waimai['shop_id'] = 0;
                        $subsidy_waimai['platform_first'] =0;
                        $subsidy_waimai['platform_mj'] = 0;
                        $subsidy_waimai['platform_hongbao'] = $order['hongbao'];
                        $subsidy_waimai['shop_first'] = 0;
                        $subsidy_waimai['shop_mj'] = 0;
                        $subsidy_waimai['shop_coupon'] = 0;
                        $subsidy_waimai['year'] = date('Y',$order['dateline']);
                        $subsidy_waimai['mouth'] = date('Ym',$order['dateline']);
                        $subsidy_waimai['day'] = date('Ymd',$order['dateline']);
                        $subsidy_waimai['hour'] = date('H',$order['dateline']);
                        $subsidy_waimai['group_id'] = $order['group_id'];
                        $subsidy_waimai['city_id'] = $group['city_id'];
                        $subsidy_waimai['bl'] = 0;
                        $subsidy_waimai['shop_discount'] = 0;
                        $subsidy_waimai['uid'] = $order['uid'];
                        $subsidy_waimai['dateline'] = $order['dateline'];
                        K::M('subsidy/waimai')->create($subsidy_waimai);

                    }
                    //商家补贴统计
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

                 //同城配送对账单
                if($paotui_order['type']==1){
                    //Intracity
                    $data = array();
                   // bills_id,bills_sn,shop_id,amount,dateline,extend,group_id
                    $data['shop_id'] = $paotui_order['ext_shop_id'];
                    $data['amount'] = $order['amount'];
                    $data['extend'] = serialize($paotui_order['extend']);
                    $data['group_id'] = $order['group_id'];
                    if(K::M('intracity/bills')->create($data)){
                        $filter_inty_bill = array();
                        $filter_inty_bill['shop_id'] = $paotui_order['ext_shop_id'];
                        $filter_inty_bill['bills_sn'] = date('Ymd');
                        $inty_bills_log = K::M('intracity/bills')->find($filter_inty_bill);
                        $bills_log_data = array();
                       // log_id,bills_id,shop_id,order_id,amount,dateline,group_id,staff_id,extend
                        $bills_log_data['bills_id'] = $inty_bills_log['bills_id'];
                        $bills_log_data['shop_id'] = $paotui_order['ext_shop_id'];
                        $bills_log_data['order_id'] = $order['order_id'];
                        $bills_log_data['amount'] = $order['amount'];
                        $bills_log_data['dateline'] = __TIME;
                        $bills_log_data['group_id'] = $order['group_id'];
                        $bills_log_data['staff_id'] = $order['staff_id'];
                        $bills_log_data['extend'] = serialize($paotui_order['extend']);
                        K::M('intracity/billslog')->create($bills_log_data);
                    }

                }

                //确认订单得积分
                if($order['online_pay'] == 1 && $order['jifen_cfg']['jifen_type'] == 2){
                    K::M('jifen/jifen')->update_jifen($order_id);
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

                //记录配送员距离等信息 2017-12-25 外卖3.7新增 begin
                K::M('staff/distance')->create($order);
                //记录配送员距离等信息 2017-12-25 外卖3.7新增 end

                K::M('order/log')->create(array('order_id'=>$order_id,'from'=>$from,'log'=>$log,'status'=>8));
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
    public function cancel($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = K::M('order/order')->detail($order_id))){
            return false;
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已取消过，不能再取消', 449);
            return false;
        }else if(!$paotui_order = $this->detail($order_id)){
            return false;
        } else if(in_array($order['order_status'], array(0, 1, 2, 3, 4, 5))){ ////-1:已取消，0：未处理，1：已接单，2：已配货，3：配送开始，4：配送完成，8：订单完成
            if($from == 'member' && $order['from'] != 'tuan'){//用户可以在未接单时直接退单,团购订单则在使用前都可以退单
                if($order['staff_id'] > 0 && $order['pay_status'] == 1){
                    $this->msgbox->add('师傅已接单不可取消', 451);
                    return false;
                }else if($order['order_status'] > 0){
                    $this->msgbox->add('商家已接单不可取消', 451);
                }
            }

            if($this->db->update('order', array('order_status'=>-1), "order_id='{$order_id}'", true)){ // 基于 mysql_affected_rows 返回影响的记录行数防止并发  add by zhuhongwei 2017-3-9
            //if(K::M('order/order')->update($order_id,array('order_status'=>-1,'lasttime'=>__TIME),true)){ //防止并发多退钱  delete by zhuhongwei 2017-3-9
                K::M('order/order')->update($order_id,array('lasttime'=>__TIME), true); // 更新时间
              /*  /*if($order['online_pay'] && $order['from'] != 'mall'){
                    if($money = $this->get_return_amount($order_id, $order)){ //退回已付款金额到余额,这时数据库的order_status已经为-1
                        K::M('member/member')->update_money($order['uid'], $money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                    }
                }*/
                if($order['online_pay']){
                    $refund_money = $refund_amount = 0;
                    $refund_money = $order['money'];
                    //退回已付款金额到余额,这时数据库的order_status已经为-1
                    if($order['pay_status'] && ($order['amount'] > 0)&&$paotui_order['type']==0){
                        $refund_amount = $order['amount'];
                        //原路退回支付金额
                        if(in_array($order['pay_code'], array('wxpay', 'alipay'))){
                            $order['refund_amount'] = $order['amount'];
                            $order['refund_reason'] = '订单(ID:'.$order['order_id'].')取消退款';
                            if(!$trade = K::M('trade/payment')->refund($order['pay_code'], $order, $msg)){
                                //原路退还失败时退回的余额
                                $refund_money += $order['amount'];
                            }else{
                                $refund_log = $trade['refund_log'];
                            }
                        }else{
                            $refund_money += $order['amount'];
                        }
                    }
                    if($refund_money > 0&&$paotui_order['type']==0){
                        K::M('member/member')->update_money($order['uid'], $refund_money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                    }
                }
                if($order['hongbao_id']){ //退还红包
                    K::M('hongbao/hongbao')->update($order['hongbao_id'], array('order_id'=>0, 'used_time'=>0, 'used_ip'=>''));
                }

                //更新服务人员订单数量
                if($order['staff_id']){
                    K::M('staff/staff')->update_count($order['staff_id'], 'orders', -1);
                }
                //更新会员订单数量
                if($order['uid']){
                    K::M('member/member')->update_count($order['uid'], 'orders', -1);
                }
                if($paotui_order['type']==1){
                  K::M('net/http')->apirequest('mall/order/cancel',array('order_id'=>$paotui_order['ext_order_id'],'ext_order_id'=>$order_id));

                }

                if($from == 'admin'){
                    $log = '管理员取消订单(订单号:'.$order['order_id'].')';
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
                if($order['uid']){
                    $data['uid'] = $order['uid'];
                    $data['title'] = '跑腿订单ID(' . $order_id . ')已取消';
                    $data['content'] = '您在' . date('Y-m-d H:i:s') . '取消跑腿订单ID(' . $order_id . ')';
                    $data['type'] = 2;
                    $data['order_id'] = $order_id;
                    $data['is_read'] = 0;
                    K::M('member/message')->create($data);
                }
                return true;
            }else{
                echo 2;die;
            }
        }
        return false;
    }


    public function get_days($day)
    { //送达日期
        $now_date = strtotime(date('Y-m-d',__TIME));
        $ch_week = array(0=>'日',1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六');
        $day_dates = array(
            0=>array('date'=>date('Y-m-d',$now_date),'day'=>'今天(周'.$ch_week[date('w',$now_date)].')'),
        );
        for($i=1;$i<$day;$i++){
            if($i==1){
                $day_date = array('date'=>date('Y-m-d',$now_date+86400),'day'=>'明天(周'.$ch_week[date('w',$now_date+86400)].')');
            }elseif($i==2){
                $day_date = array('date'=>date('Y-m-d',$now_date+$i*86400),'day'=> '后天(周'.$ch_week[date('w',$now_date+$i*86400)].')');
            }else{
                $day_date = array('date'=>date('Y-m-d',$now_date+$i*86400),'day'=>date('m-d',$now_date+$i*86400).'(周'.$ch_week[date('w',$now_date+$i*86400)].')');
            }
            array_push($day_dates, $day_date);
        }
        return $day_dates;
    }


    public function get_time()
    {//送达具体时间
        $return_array = array();
        $start_quarter = 0;
        $start = date('H',__TIME+3600);
        $q = date('i',__TIME+3600);
        if($q <30){
            $start_quarter =0;
        }else if($q <60 &&$q>=30){
            $start_quarter=1;
        }
        $return_array['start'] = $start;
        $return_array['start_quarter'] = $start_quarter;
        return $return_array;
    }

    public function get_day_time($yy_stime,$yy_ltime){
        $return_time = array();
        //当日配送时间
        $res = $this->get_time();
        $set_time['start'] = $res['start'];
        $set_time['start_quarter'] = $res['start_quarter'];
        $stime = $res['start'].":".$res['start_quarter']*30;
        $rr = explode(':',$yy_ltime);
        $set_time['end'] = $rr[0];
        $set_time['end_quarter'] = $rr[1]/30;
        $ltime = $res['start'].":".$res['start_quarter']*30;

        if($stime > $yy_ltime){
           $set_time = array();
        }
        $result = $_result = array();
        if($set_time){
            for($i=$set_time['start'];$i<=$set_time['end'];$i++){
                if($i==$set_time['start']&&$set_time['start_quarter']>=0){
                    for($k=$set_time['start_quarter'];$k<=1;$k++){
                        if($i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($result, $s.":".$j);
                    }
                }elseif($i<$set_time['end']){
                    for($k=0;$k<=1;$k++){
                        if($i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($result, $s.":".$j);
                    }
                }elseif($i==$set_time['end']&&$set_time['end_quarter']>=0){
                    for($k=0;$k<=$set_time['end_quarter'];$k++){
                        if($i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($result, $s.":".$j);
                    }
                }
            }
        }
        //非当日配送时间
        $ss = explode(':',$yy_stime);
        $nomal_time['start'] = $ss[0];
        $nomal_time['start_quarter'] = $ss[1]/30;
        $nomal_time['end'] = $rr[0];
        $nomal_time['end_quarter'] = $rr[1]/30;
        if($nomal_time){
            for($i=$nomal_time['start'];$i<=$nomal_time['end'];$i++){
                if($i==$nomal_time['start']&&$nomal_time['start_quarter']>=0){
                    for($k=$nomal_time['start_quarter'];$k<=1;$k++){
                        if($i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($_result, $s.":".$j);
                    }
                }elseif($i<$nomal_time['end']){
                    for($k=0;$k<=1;$k++){
                        if($i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($_result, $s.":".$j);
                    }
                }elseif($i==$nomal_time['end']&&$nomal_time['end_quarter']>=0){
                    for($k=0;$k<=$nomal_time['end_quarter'];$k++){
                        if($$i >0 && $i < 10){
                            $s = "0".$i;
                        }else{
                           $s = $i;
                        }
                        if($k==0){
                           $j = 30*$k."0";
                        }else{
                           $j = 30*$k;
                        }
                        array_push($_result, $i.":".$j);
                    }
                }
            }
        }

        return array('set_time'=>  array_values($result),'nomal_time'=>array_values($_result));
    }



    public function get_addrs_by_uid($uid)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$member = K::M('member/member')->detail($uid)){
            return false;
        }else{
            $data = $filter = $orderby =array();
            $filter['uid'] = $this->uid;
            $orderby['addr_id'] = 'desc';
            $addrs = K::M('member/addr')->items($filter,$orderby,1,50,$count);
            foreach ($addrs as $k => $v) {
                if($v['lng'] && $v['lat']){
                    $lnglat = K::M('helper/date')->bd_decrypt($v['lng'],$v['lat']);
                    if($group = K::M('pei/group')->get_group_by_lnglat($lnglat['gg_lon'],$lnglat['gg_lat'])){
                        $data[] = $v;
                    }
                }
            }
            return $data;
        }
    }

    public function getone_addr_by_uid($uid)
    {
        $data = array();
        if($addrs = $this->get_addrs_by_uid($uid)){
            foreach ($addrs as $k => $v) {
                if($v['is_default'] == 1){
                    $data = $v;
                    break;
                }elseif(empty($data)){
                    $data = $v;
                }
            }
        }
        return $data;
    }

    public function get_msg($row){

        $btn = array(
            'pay'=>0,
            'cancel'=>0,
            'cui'=>0,
            'confirm'=>0,
            'comment'=>0,
            'again'=>0
        );
        $msg = "";
        if($row['order_status']==-1){
            $msg = "已取消";
            $btn['again'] = 1;
        }else if($row['order_status']==0&&$row['online_pay']==1&&$row['pay_status']==0){
            $msg = "待支付";
            $btn['pay'] = 1;
            $btn['cancel'] = 1;
        }else if($row['order_status']==0&&$row['online_pay']==1&&$row['pay_status']==1){
            $msg = "等待骑手接单";
            $btn['cancel'] = 1;
        }else if($row['order_status']==1&&$row['online_pay']==1&&$row['pay_status']==1&&$row['staff_id']>0){
            $msg = '取货中';
            $btn['cui'] = 1;
        }else if($row['order_status']==3&&$row['online_pay']==1&&$row['pay_status']==1&&$row['staff_id']>0){
            $msg = '送货中';
            $btn['cui'] = 1;
        }else if($row['order_status']==4&&$row['online_pay']==1&&$row['pay_status']==1&&$row['staff_id']>0){
            $msg = '已送达';
            $btn['confirm'] = 1;
        }else if($row['order_status']==8&&$row['online_pay']==1&&$row['pay_status']==1&&$row['comment_status']==0){
            $msg = '等待评价';
            $btn['comment'] = 1;
            $btn['again'] = 1;
        }else if($row['order_status']==8&&$row['online_pay']==1&&$row['pay_status']==1&&$row['comment_status']==1){
            $msg = '已评价';
            $btn['again'] = 1;
        }
        $row['msg']=$msg;
        $row['btn']=$btn;
        $row['order_status_label'] = $msg;
        return $row;



    }
    public function get_paotui_amount($parmas)
    {
        $juli_amount = $weight_amount = 0;
        static $pei_config = null;
        if ($pei_config === null) {
            $pei_config = K::M('system/config')->get('paotui');
        }

        if (!(int)$parmas['freight']&&$parmas['type']=='song') {
            return false;
        }
        if (!$parmas['type']) {
            return false;
        }
        if($parmas['lng']&&$parmas['lat']&&$parmas['o_lng']&&$parmas['o_lat']){
            if (!$juli = K::M('magic/baidu')->juli($parmas['lng'], $parmas['lat'], $parmas['o_lng'], $parmas['o_lat'])) {
                $juli = K::M('helper/round')->juli($parmas['lng'], $parmas['lat'], $parmas['o_lng'], $parmas['o_lat']);
            }
        }else{
            $juli = 0;
        }

        //如果有选择两个地址   则计算距离  没有则设置为 0
        $juli = ceil($juli / 1000);
        $_freight = array();
        $fright_config = $pei_config['mileage'];
        $_max_freight = array('fkm' => 0, 'fm' => 0,'wkg'=>0,'wm'=>0);
        foreach ($fright_config as $kk => $vv) {
            if ($vv['fkm'] == 0) {
                unset($fright_config[$kk]);
            }
        }
        foreach ($fright_config as $k => $v) {
            if ($juli <= $v['fkm']) {
                if ($_freight && $_freight['fkm'] > $v['fkm']) {
                    $_freight = $v;
                } else if (empty($_freight)) {
                    $_freight = $v;
                }
            }
            if ($v['fkm'] > $_max_freight['fkm']) {
                $_max_freight = $v;
            }
        }
        $juli_amount = $_freight['fkm'] ? $_freight['fm'] : $_max_freight['fm'];
        $weight_config = $_freight['fkm']?$_freight:$_max_freight;
        $now_time = __TIME;
        if($parmas['now_time']){
            $now_time = strtotime($parmas['now_time']);
        }
        $ratio = 0;
        foreach ($pei_config['time'] as $k => $v) {
            $stime = strtotime(date('Y-m-d ', $now_time) . $v['stime']);
            $ltime = strtotime(date('Y-m-d ', $now_time) . $v['ltime']);
            if ($stime && $ltime) {
                if ($now_time > $stime && $now_time < $ltime) {
                    $ratio = $v['ratio'] / 100;
                    break;
                }
            }
        }

        if($parmas['type']=='song'){
            if($parmas['freight']<=$weight_config['wkg']){
                $weight_amount = 0;
            }else{
                $_tmp_weight = $parmas['freight']-$weight_config['wkg'];
                $_tmp_weight = ceil($_tmp_weight);
                $weight_amount = $_tmp_weight*$weight_config['wm'];
            }

        }
        //return (float)(($juli_amount + $weight_amount) * ($ratio + 1));

        $pei_amount = (float)(($juli_amount + $weight_amount) * ($ratio + 1));
        K::M('system/logs')->log('parmas',array($parmas,$pei_amount));
        return sprintf('%.2f',$pei_amount);

    }


    public function get_priceinfo($parmas)
    {
        $juli_amount = $weight_amount = 0;
        static $pei_config = null;
        if ($pei_config === null) {
            $pei_config = K::M('system/config')->get('paotui');
        }

        if (!(int)$parmas['freight']&&$parmas['type']=='song') {
            return false;
        }
        if (!$parmas['type']) {
            return false;
        }
        if($parmas['lng']&&$parmas['lat']&&$parmas['o_lng']&&$parmas['o_lat']){
            if (!$juli = K::M('magic/baidu')->juli($parmas['lng'], $parmas['lat'], $parmas['o_lng'], $parmas['o_lat'])) {
                $juli = K::M('helper/round')->juli($parmas['lng'], $parmas['lat'], $parmas['o_lng'], $parmas['o_lat']);
            }
        }else{
            $juli = 0;
        }
        //如果有选择两个地址   则计算距离  没有则设置为 0
        $juli_m = $juli;//两者距离米为单位
        $juli = ceil($juli / 1000);
        $_freight = array();
        $fright_config = $pei_config['mileage'];
        $_max_freight = array('fkm' => 0, 'fm' => 0,'wkg'=>0,'wm'=>0);
        foreach ($fright_config as $kk => $vv) {
            if ($vv['fkm'] == 0) {
                unset($fright_config[$kk]);
            }
        }
        foreach ($fright_config as $k => $v) {
            if ($juli <= $v['fkm']) {
                if ($_freight && $_freight['fkm'] > $v['fkm']) {
                    $_freight = $v;
                } else if (empty($_freight)) {
                    $_freight = $v;
                }
            }

            if ($v['fkm'] > $_max_freight['fkm']) {
                $_max_freight = $v;
            }
            // 距离起步价
            if (0 <= $v['fkm']) {
                if ($_qibu && $_qibu['fkm'] > $v['fkm']) {
                    $_qibu = $v;
                } else if (empty($_qibu)) {
                    $_qibu = $v;
                }
            }
        }



        $juli_amount = $_freight['fkm'] ? $_freight['fm'] : $_max_freight['fm'];
        $weight_config = $_freight['fkm']?$_freight:$_max_freight;
        $now_time = __TIME;
        if($parmas['now_time']){
            $now_time = strtotime($parmas['now_time']);
        }
        $ratio = 0;
        foreach ($pei_config['time'] as $k => $v) {
            $stime = strtotime(date('Y-m-d ', $now_time) . $v['stime']);
            $ltime = strtotime(date('Y-m-d ', $now_time) . $v['ltime']);
            if ($stime && $ltime) {
                if ($now_time > $stime && $now_time < $ltime) {
                    $ratio = $v['ratio'] / 100;
                    break;
                }
            }
        }

        if($parmas['type']=='song'){
            if($parmas['freight']<=$weight_config['wkg']){
                $weight_amount = 0;
            }else{
                $_tmp_weight = $parmas['freight']-$weight_config['wkg'];
                $_tmp_weight = ceil($_tmp_weight);
                $weight_amount = $_tmp_weight*$weight_config['wm'];
            }

        }
        $addtime_price = (float)(($juli_amount + $weight_amount) * ($ratio));

        return array(
            'qibu_price'      => $_qibu['fm'],//起步价
            'addjuli_price'   => (float)($juli_amount-$_qibu['fm']),//起距加价
            'addweight_price' => $weight_amount,//重量加价
            'addtime_price'   => sprintf('%.2f',$addtime_price)
            );
    }

    public function set_order_day_num($order_id){
        K::M('system/logs')->log('paotui.order.othercount',$order_id);
        if(!$order_id){
            return false;
        }else if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        }else if($order['from']!='paotui'){
            return false;
        }else{
            $filter = array();
            $filter['day'] = date('Ymd',__TIME);
            $filter['from'] = 'paotui';
            $filter['group_id'] = $order['group_id'];
            $filter['order_id'] = '<:'.$order_id;
            $count = K::M('order/order')->othercount($filter);
            return K::M('order/order')->update($order_id,array('day_num'=>$count+1));
        }
    }




    public function count_join_uncomplete($parmas){
        if(!$parmas['shop_id']){
            return false;
        }else{

        }
        $where ="s.order_id=ext.order_id AND ext.ext_shop_id=".$parmas['shop_id'].' AND s.order_status IN (0,1,3,4)';

        if(empty($closed)){
            //$where .= " AND s.closed='0'";
        }
        $sql = "SELECT count(s.order_id) as count   FROM  " .$this->table('order')." s, ".$this->table('paotui_order')." ext WHERE $where";
        if($row = $this->db->GetRow($sql)){
            return $row['count'];
        }else{
            return 0;
        }

    }







}
