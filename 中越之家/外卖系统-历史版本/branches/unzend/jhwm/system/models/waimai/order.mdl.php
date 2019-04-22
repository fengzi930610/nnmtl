<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Order extends Mdl_Table
{   
  
    protected $_table = 'waimai_order';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,product_number,product_price,package_price,freight,spend_number,spend_status,shop_amount,roof_amount,first_shop,first_roof';
    public function get_order_status(){
        return array(
            '-1' => '已取消',
            '0' => '未处理',
            '1' => '已接单',
            '3' => '配送开始',
            '4' => '配送完成',
            '8' => '订单完成',
        );
    }
    public function  get_payments(){
        return array(
            'wxpay' => '微信支付',
            'alipay' => '支付宝支付',
            'money' => '余额支付',
        );
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
            $row = K::M('order/order')->order_format_row($row);
            
        }        
        return $row;
    }
    public function detail_by_number($number, $closed=false)
    {
        if(!preg_match('/^(\d+)$/i', $number)){
            return false;
        }
        if(empty($closed)){
            $where .= " AND o.closed='0'";
        }
        $where ="o.order_id=ext.order_id AND ext.spend_number=".$number;
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
        }else if($order['order_status'] < 0 || $order['order_status'] == 8){
            return false;
        }else if($order['pay_status']){
            $amount = $order['amount'] + $order['money'];
        }else{
            $amount = $order['money'];
        }
        return $amount;
    }
    
    /*public function return_sku($order_id, $order=null)
    {
        if(empty($order) && !($order = $this->detail($order_id))){
            return false;
        }
        if($produt_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_id))){
            foreach($produt_list as $v){
                if($v['spec_id']){
                    $a = array('sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                    K::M('waimai/productspec')->update($v['spec_id'],  $a, true);
                }
                $b = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                K::M('waimai/product')->update($v['product_id'], $b, true);

                if($v['huodong_id']){
                    $c = array('sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                    K::M('waimai/discountproduct')->update($v['product_id'], $c, true);
                }
            }
        }
        return true;
    }*/
    public function return_sku($order_id, $order=null)
    {
        if(empty($order) && !($order = $this->detail($order_id))){
            return false;
        }
        if($product_list = K::M('waimai/orderproduct')->items(array('order_id'=>$order_id))){
            $pids = $sids = array();
            foreach ($product_list as $k => $v) {
                if($v['product_id']){
                    $pids[$v['product_id']] = $v['product_id'];
                }
                if($v['spec_id']){
                    $sids[$v['spec_id']] = $v['spec_id'];
                }                
            }
            $products = K::M('waimai/product')->items_by_ids($pids);
            $specs = K::M('waimai/productspec')->items_by_ids($sids);
            foreach ($product_list as $k => $v) {
                $up_prod = $up_spec = $up_disc = $up_hg = array();
                if($prod = $products[$v['product_id']]){
                    if($v['huodong_id']){
                        //$up_disc = array('sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                        $up_disc = array('sale_count'=>'`sale_count`-'.$v['product_number']);
                        $up_prod = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number']);
                        if($v['spec_id'] && $sp = $specs[$v['spec_id']]){                        
                            $up_spec = array('sale_count'=>'`sale_count`-'.$v['product_number']);                
                        }                       
                    }else{
                        $up_prod = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                        if($v['spec_id'] && $sp = $specs[$v['spec_id']]){
                            $up_spec = array('sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                            if($sp['sale_type'] == 0){
                                $up_spec = array('sale_count'=>'`sale_count`-'.$v['product_number']);
                            }                                  
                        }else if($prod['sale_type'] == 0){
                            $up_prod = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number']);
                        }
                    }

                    if($v['huangou_id']){
                        $up_prod = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                        if($prod['sale_type'] == 0){
                            $up_prod = array('sales'=>'`sales`-'.$v['product_number'], 'sale_count'=>'`sale_count`-'.$v['product_number']);
                        }
                        $up_hg = array('sale_count'=>'`sale_count`-'.$v['product_number'], 'sale_sku'=>'`sale_sku`+'.$v['product_number']);
                    }
                }
                
                if($up_prod){
                    K::M('waimai/product')->update($v['product_id'], $up_prod, true);
                }
                if($up_spec){
                    K::M('waimai/productspec')->update($v['spec_id'],  $up_spec, true);
                }
                if($up_disc){
                    K::M('waimai/discountproduct')->update($v['product_id'], $up_disc, true);
                }
                if($up_hg){
                    K::M('waimai/huangouproduct')->update($v['product_id'], $up_hg, true);
                }                
            }
        }
        //K::M("system/logs")->log("sqllog",$this->db->SQLLOG());
        return true;
    }
    
    // 自提订单创建消费码 15位
    public function create_number($order_id)
    {    
        do{
            $no = '1'.date('ymd',__TIME) . rand(10000000, 99999999);
            $number = $this->db->GetRow("SELECT spend_number FROM ".$this->table($this->_table)." WHERE spend_number='{$no}'");
        } while ($number);
        if(isset($no)) {
            $this->update($order_id,array('spend_number'=>$no, 'spend_status'=>0));
        }
    }
    
    
    /*public function get_days($day)
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
    }*/

    public function get_days($day, $weeks)
    { //送达日期,v3.6 新增营业时间按周选择
        $now_date = strtotime(date('Y-m-d',__TIME));
        $ch_week = array(0=>'日',1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六');
        $day_dates = array();
        for($i=0;$i<$day;$i++){
            $week = date('w',$now_date+$i*86400);
            if(in_array($week, $weeks)){
                if($i==0){
                    $day_date = array('date'=>date('Y-m-d',$now_date),'day'=>'今天(周'.$ch_week[$week].')');
                }elseif($i==1){
                    $day_date = array('date'=>date('Y-m-d',$now_date+86400),'day'=>'明天(周'.$ch_week[$week].')');
                }elseif($i==2){
                    $day_date = array('date'=>date('Y-m-d',$now_date+$i*86400),'day'=> '后天(周'.$ch_week[$week].')');
                }else{
                    $day_date = array('date'=>date('Y-m-d',$now_date+$i*86400),'day'=>date('m-d',$now_date+$i*86400).'(周'.$ch_week[$week].')');
                }
                array_push($day_dates, $day_date);
            }            
        }
        return $day_dates?$day_dates:array(array('date'=>date('Y-m-d',$now_date),'day'=>'今天(周'.$ch_week[date('w',$now_date)].')'));
    }    


    public function get_time()
    {//送达具体时间
        $return_array = array();
        $start_quarter = 0;
        $start = date('H',__TIME+3600);
        $q = date('i',__TIME+3600);
        if($q <15){
            $start_quarter =0;
        }else if($q <30 &&$q>=15){
            $start_quarter=1;
        }else if($q <45 &&$q>=30){
            $start_quarter=2;
        }else{
            $start_quarter=3;
        }
        $return_array['start'] = $start;
        $return_array['start_quarter'] = $start_quarter;
        return $return_array;
    }
    
    public function get_day_time($yy_stime,$yy_ltime,$yy_pritime){
        $return_time = array();
        //当日配送时间
        $res = $this->get_time();
        $set_time['start'] = $res['start'];
        $set_time['start_quarter'] = $res['start_quarter'];
        $stime = $res['start'].":".$res['start_quarter']*15;
        $rr = explode(':',$yy_ltime);
        $set_time['end'] = $rr[0];
        $set_time['end_quarter'] = $rr[1]/15;
        $ltime = $res['start'].":".$res['start_quarter']*15;

        if($stime > $yy_ltime){
           $set_time = array();
        }
        //print_r($set_time);die;
        $result = $_result = array();
        if($set_time){
            for($i=$set_time['start'];$i<=$set_time['end'];$i++){
                if($i==$set_time['start']&&$set_time['start_quarter']>=0){
                    for($k=$set_time['start_quarter'];$k<=3;$k++){
                        if($i<10){
                            $s = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($result, $s.":".$j);
                    }
                }elseif($i<$set_time['end']){
                    for($k=0;$k<=3;$k++){
                        if($i<10){
                            $s = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($result, $s.":".$j);
                    }
                }elseif($i==$set_time['end']&&$set_time['end_quarter']>=0){
                    for($k=0;$k<=$set_time['end_quarter'];$k++){
                        if($i<10){
                            $s = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($result, $s.":".$j);
                    }
                }
            }
        }
        //非当日配送时间
        $ss = explode(':',$yy_stime);
        $nomal_time['start'] = $ss[0];
        $nomal_time['start_quarter'] = $ss[1]/15;
        $nomal_time['end'] = $rr[0];
        $nomal_time['end_quarter'] = $rr[1]/15;
        if($nomal_time){
            for($i=$nomal_time['start'];$i<=$nomal_time['end'];$i++){
                if($i==$nomal_time['start']&&$nomal_time['start_quarter']>=0){
                    for($k=$nomal_time['start_quarter'];$k<=3;$k++){
                        if($i<10){
                            $s = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($_result, $s.":".$j);
                    }
                }elseif($i<$nomal_time['end']){
                    for($k=0;$k<=3;$k++){
                        if($i<10){
                            $s = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($_result, $s.":".$j);
                    }
                }elseif($i==$nomal_time['end']&&$nomal_time['end_quarter']>=0){
                    for($k=0;$k<=$nomal_time['end_quarter'];$k++){
                        if($i<10){
                            $i = $i;
                        }else{
                           $s = $i; 
                        }
                        if($k==0){
                           $j = 15*$k."0"; 
                        }else{
                           $j = 15*$k;  
                        }
                        if($s<10 && $s>0){$s = '0'.$s;}
                        array_push($_result, $i.":".$j);
                    }
                }
            } 
        }
       // K::M('system/logs')->log('time',array('set_time'=>  array_values($result),'nomal_time'=>array_values($_result)));

        //先设置原始配置的数据
        
        $_peiTime = array(); //v3.6 2017/11/23将配送时间段存入新数组(含次日拆分成两个时间段)
        $i = 0;
        foreach ($yy_pritime as $k=>$v){
            //$yy_pritime[$k]['stime'] = strtotime(date('Y-m-d').' '.$v['stime']);
            //$yy_pritime[$k]['ltime'] = strtotime(date('Y-m-d').' '.$v['ltime']);

            //v3.6 2017/11/23
            if(stristr($v['ltime'],'次日')){
                $lt = str_replace('次日', '', $v['ltime']);
                $_peiTime[$i]['stime'] = strtotime(date('Y-m-d ',__TIME));
                $_peiTime[$i]['ltime'] = strtotime(date('Y-m-d ',__TIME).$lt);
                ++$i;
                $_peiTime[$i]['stime'] = strtotime(date('Y-m-d ',__TIME).$v['stime']);
                $_peiTime[$i]['ltime'] = strtotime(date('Y-m-d ',__TIME).'23:59');
            }else{
                $_peiTime[$i]['stime'] = strtotime(date('Y-m-d').' '.$v['stime']);
                $_peiTime[$i]['ltime'] = strtotime(date('Y-m-d').' '.$v['ltime']);
            }
            $i++;
        }
        //echo '<pre>';
        //print_r();exit;

        foreach ($_result as $k=>$v){
            $unset = 0;
            $time = strtotime(date('Y-m-d').' '.$v);
            foreach ($_peiTime as $vv){
                if($time>=$vv['stime']&&$time<=$vv['ltime']){
                    $unset++;
                }                
            }

            if($unset==0){
                unset($_result[$k]);
            }

        }


        foreach ($result as $k=>$v){
            $unset = 0;
            $time = strtotime(date('Y-m-d').' '.$v);
            foreach ($_peiTime as $vv){
                if($time>=$vv['stime']&&$time<=$vv['ltime']){
                    $unset++;
                }
            }
            if($unset==0){
                unset($result[$k]);
            }

        }



        return array('set_time'=>  array_values($result),'nomal_time'=>array_values($_result));
    }

    

    //  备注
    public function get_note()
    {
        return array(
            1=>array(
                1=>'不要辣',
                2=>'少点辣',
                3=>'多点辣',
            ),
            2=>'不要香菜',
            3=>'不要洋葱',
            4=>'多点醋',
            5=>'多点葱',
            6=>array(
                1=>'去冰',
                2=>'少冰',
                3=>'多冰',
            ),
        );
    }
    
    
    public function set_payed($log, $trade=array())
    {         
        $order_id = $log['order_id'];
        if(!$order = K::M('order/order')->detail($order_id)){
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

            if($order['card_id'] && ($card = K::M('peicard/card')->detail($order['card_id']))){
                $peicard_member_data = array(
                    'card_id'=>$order['card_id'],
                    'uid'=>$order['uid'],
                    'title'=>$card['title'],
                    'ltime'=>__TIME + ($card['days'])*86400,
                    'limits'=>$card['limits'],
                    'reduce'=>$card['reduce'],
                    'dateline'=>__TIME
                );
                if($cid = K::M('peicard/member')->create($peicard_member_data)){
                    $peicard_log = array(
                        'uid'=>$order['uid'],
                        'cid'=>$cid,
                        'order_id'=>$order_id,
                        'money'=>$card['reduce'],
                        'day'=>date('Ymd', __TIME),
                        'dateline'=>__TIME
                    );
                    K::M('order/order')->update($order_id, array('peicard_id'=>$cid));
                    K::M('peicard/log')->create($peicard_log);

                    $tongji = array(
                        'amount'=>$card['amount'],
                        'from'=>'peicard',
                        'uid'=>$order['uid'],
                        'year'=>date('Y', __TIME),
                        'mouth'=>date('Ym', __TIME),
                        'day'=>date('Ymd', __TIME),
                        'hour'=>date('H', __TIME),
                        'dateline'=>__TIME
                        );
                    K::M('site/tongji')->create($tongji);
                }
            }

            if($order['from'] == 'waimai') {
                if($order['pei_type'] == 3) { // 自提订单 创建消费码
                    K::M('waimai/order')->create_number($order_id);
                    $addr = '客户自提';
                }else{
                    $addr = $order['addr'].$order['house'];
                }
                $title = sprintf("您有新的外卖订单(单号：%s)", $order_id);
                $content = sprintf("您有新的外卖订单(单号：%s)，客户%s(电话：%s)配送地址:%s", $order_id, $order['contact'], $order['mobile'], $addr);
                K::M('shop/shop')->send($order['shop_id'], $title, $content, array('type'=>'newOrder','order_id'=>$order_id));
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

                /*if($order['order_from'] != 'wxapp'){
                    K::M('order/order')->send_member('订单支付成功', sprintf("您在[%s]下的订单(%s)，支付成功", $waimai['title'], $order_id), $order);
                }*/

                if($order['online_pay']==1){
                    K::M('pei/group')->set_order_expect_time($order_id,$order);

                    //4.1续-记录用户在当前商户的下单次数
                    K::M('order/order')->set_member_orders($order_id, $order['uid'], $order['shop_id']);
                }
            }
            $waimai_shop_id = $order['shop_id'];
            if($waimai){
                if($waimai['print_type']==1){
                    if($print_list = K::M('shop/print')->items(array('shop_id'=>$waimai_shop_id),array('plat_id'=>'desc'),1,10,$count)){
                        foreach($print_list as $k=>$v){
                            $num = max(1, $v['num']);
                            K::M('order/order')->yunprint($order_id, $num, $v['plat_id']);
                        }
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
        if(!$waimai_order = $this->detail($order_id)){
            return false;
        }else if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        } else if(in_array($waimai_order['order_status'], array(1,2,3,4,5))){

            if($this->db->update('order', array('order_status'=>8), "order_id='{$order_id}'", true)){
                K::M('order/time')->update($order_id,array('order_compltet_time'=>__TIME));
                // 基于 mysql_affected_rows 返回影响的记录行数防止并发  add by zhuhongwei 2017-3-9
                K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);
                if($from == 'member'&&$waimai_order['pei_type'] == 3){ //自提状态
                    K::M('waimai/order')->update($order_id, array('spend_status'=>1), true);
                }

                $tongji = array(
                    'amount'=>$order['amount']+$order['money'],
                    'order_id'=>$order['order_id'],
                    'from'=>'waimai',
                    'shop_id'=>$order['shop_id'],
                    'uid'=>$order['uid'],
                    'staff_id'=>$order['staff_id'],
                    'pei_amount'=>$order['pei_amount'],
                    'year'=>date('Y',$order['dateline']),
                    'mouth'=>date('Ym',$order['dateline']),
                    'day'=>date('Ymd',$order['dateline']),
                    'hour'=>date('H',$order['dateline']),
                    'dateline'=>$order['dateline'],
                    'city_id'=>$order['city_id'],
                    'site_fee'=>0
                    );
                $staff_amount = $shop_amount = 0;
                if($order['online_pay']){
                    $log = $staff_log = '订单完成结算(ID:'.$order_id.')';
                    if($order['from'] == 'waimai'){
                        //配送员送
                        if($order['pei_type']){
                            if($order['pei_type'] == 2){//代购订单，全部结算给配送员
                                $staff_amount = $order['amount'] + $order['hongbao'] + $order['money'];
                                $staff_log = '订单代购完成结算(ID:'.$order_id.')';
                            }else{
                                $staff_amount = $order['pei_amount'];
                                $shop_amount = $order['amount'] + $order['hongbao'] + $order['money']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$staff_amount+$order['peicard_youhui'];
                                $staff_log = '订单配送完成(ID:'.$order_id.'),资金已入对账单';
                            } 
                        }else{
                            //商家自己送
                            $shop_amount = $order['amount'] + $order['hongbao'] + $order['money'] + $waimai_order['roof_amount'] + $waimai_order['first_roof'];
                        }
                    }
                    if($staff_amount && $order['staff_id']){
                        //修改配送员配送费获取 2018 01 15 叶超 begin
                        $true_staff_amount = K::M('staff/config')->get_peiamount($order,$order['staff_id'],$staff_amount);
                        //修改配送员配送费获取 2018 01 15 叶超 end
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
                        //统计数据 -- 配送员部分
                        $tongji['staff_fee'] =$staff_amount- $percent_and_money['end_money'];
                        $tongji['staff_amount'] = $percent_and_money['end_money'];
                        $tongji['platform_staff'] = ($percent_and_money['end_money']-$staff_amount)>0? ($percent_and_money['end_money']-$staff_amount):0;
                        $tongji['site_fee']=$tongji['site_fee']+($true_staff_amount-$percent_and_money['end_money']);

                        K::M('staff/log')->log($order['staff_id'],$true_staff_amount,$staff_log);
                        //这里生成骑手补贴相关 外卖3.8
                        if($staff_amount<$true_staff_amount){
                            $staff = K::M('staff/staff')->detail($order['staff_id']);
                            $subsidy_staff = array(
                                'order_id'=>$order_id,
                                'staff_id'=>$order['staff_id'],
                                'pei_amount'=>$staff_amount,
                                'staff_amount'=>$true_staff_amount,
                                'diff_amount'=>$true_staff_amount-$staff_amount,
                                'from'=>'waimai',
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
                        //这里生成骑手补贴相关 外卖3.8
                        //K::M('staff/staff')->update_money($order['staff_id'], $staff_amount, $log);
                    }
                    if($shop_amount){
                        $config = K::M('waimai/waimai')->detail($order['shop_id']);

                        //2017-12-25 外卖3.7 新增自提单单独结算比例 begin
                        if($order['pei_type']==3&&$config['is_ztsp']==1){
                            $config['waimai_bl'] = $config['zt_bl'];
                        }

                        if($config['jiesuan_type']==1){
                            $fee =  number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $config['waimai_bl'])/100, 2, '.', '');
                            if($fee<=0){
                                $fee = 0;
                            }
                        }else if($config['jiesuan_type']==2){
                            $fee =  number_format((($order['total_price']-$waimai_order['freight']) * $config['waimai_bl'])/100, 2, '.', '');
                            if($fee<=0){
                                $fee = 0;
                            }
                        }else{
                            $fee =  number_format(($shop_amount * $config['waimai_bl'])/100, 2, '.', '');
                            if($shop_amount<=0){
                                $fee = 0;
                            }
                        }
                        //修改商家结算比例 --20171202 叶超
                        $bill_data = array();
                        $bill_data['shop_id']=$waimai_order['shop_id'];
                        $bill_data['status'] = 0;
                        $bill_data['amount']=$shop_amount-$fee;
                        $bill_data['fee'] =$fee;
                        $bill_data['shop_amount'] = $waimai_order['shop_amount']+$waimai_order['first_shop']+$order['coupon'];
                        $bill_data['roof_amount'] = $waimai_order['roof_amount']+$waimai_order['first_roof']+$order['hongbao']+$order['peicard_youhui'];
                        $bill_data['user_amount'] = $order['amount']+$order['money'];
                        if($order['pei_type']==1||$order['pei_type']==2){
                            $bill_data['freight'] = $order['pei_amount'];
                        }else{
                            $bill_data['freight'] = 0;
                        }
                        K::M('waimai/bills')->create($bill_data);
                        $filter = array(
                            'bills_sn'=>date('Ymd'),
                            'shop_id'=>$waimai_order['shop_id']
                        );
                        $bills = K::M('waimai/bills')->find($filter);
                        $data = array('bills_id'=>$bills['bills_id'],
                            'bills_sn'=>date('Ymd'),
                            'shop_id'=>$waimai_order['shop_id'],
                            'bills_number'=>$waimai_order['order_id'],
                            'status'=>0,
                            'online_pay'=>$waimai_order['online_pay'],
                            'amount'=>$shop_amount-$fee,
                            'fee'=>$fee,
                            'shop_amount'=>$waimai_order['shop_amount']+$waimai_order['first_shop']+$order['coupon'],
                            'roof_amount'=>$waimai_order['roof_amount']+$waimai_order['first_roof']+$order['hongbao']+$order['peicard_youhui'],
                            'freight'=> $bill_data['freight'],
                            'user_amount'=>$order['amount']+$order['money'],
                            'bl'=>$config['waimai_bl']
                        );
                        K::M('waimai/billslog')->create($data);
                        //修改对账单 --end

                         //数据统计商家部分
                        $tongji['shop_amount'] = $data['amount'];
                        $tongji['shop_fee'] = $fee;
                          //数据统计商家部分
                        $tongji['site_fee'] =  $tongji['site_fee']+$fee;

                        if(($order['hongbao']>0)||($order['order_youhui'])>0||($order['first_youhui']>0)||($order['coupon']>0)||($order['discount_youhui']>0)||($order['huangou_youhui']>0)||($order['peicard_youhui']>0)){
                            $subsidy_waimai = array();
                            $subsidy_waimai['order_id'] = $order['order_id'];
                            $subsidy_waimai['shop_id'] = $order['shop_id'];
                            $subsidy_waimai['platform_first'] = $waimai_order['first_roof'];
                            $subsidy_waimai['platform_mj'] = $waimai_order['roof_amount'];
                            $subsidy_waimai['platform_hongbao'] = $order['hongbao'];
                            $subsidy_waimai['shop_first'] = $waimai_order['first_shop'];
                            $subsidy_waimai['shop_mj'] = $waimai_order['shop_amount']-$order['discount_youhui'];
                            $subsidy_waimai['shop_coupon'] = $order['coupon'];
                            $subsidy_waimai['year'] = date('Y',$order['dateline']);
                            $subsidy_waimai['mouth'] = date('Ym',$order['dateline']);
                            $subsidy_waimai['day'] = date('Ymd',$order['dateline']);
                            $subsidy_waimai['hour'] = date('H',$order['dateline']);
                            $subsidy_waimai['group_id'] = $config['group_id'];
                            $subsidy_waimai['city_id'] = $config['city_id'];
                            $subsidy_waimai['bl'] = $config['waimai_bl'];
                            $subsidy_waimai['shop_discount'] = $order['discount_youhui'];
                            $subsidy_waimai['uid'] = $order['uid'];
                            $subsidy_waimai['dateline'] = $order['dateline'];

                            //4.0新增商家换购和配送会员卡优惠
                            $subsidy_waimai['shop_huangou'] = $order['huangou_youhui'];
                            $subsidy_waimai['platform_peicard'] = $order['peicard_youhui'];

                            K::M('subsidy/waimai')->create($subsidy_waimai);

                            //统计  优惠相关
                            $tongji['platform_first'] = $subsidy_waimai['platform_first'];
                            $tongji['platform_mj'] = $subsidy_waimai['platform_mj'];
                            $tongji['platform_hongbao'] = $subsidy_waimai['platform_hongbao'];
                            $tongji['shop_first'] = $subsidy_waimai['shop_first'];
                            $tongji['shop_mj'] = $subsidy_waimai['shop_mj'];
                            $tongji['shop_coupon'] = $subsidy_waimai['shop_coupon'];
                            $tongji['shop_discount'] = $subsidy_waimai['shop_discount'];

                            //4.0新增商家换购和配送会员卡优惠
                            $tongji['shop_huangou'] = $subsidy_waimai['shop_huangou'];
                            $tongji['platform_peicard'] = $subsidy_waimai['platform_peicard'];

                            //统计 优惠相关
                        }
                        //商户补贴统计处理 end 2018 01 29
                    }
                    //首单奖励发放
                    //订单满反--beging
                    K::M('waimai/huodongmf')->coupon_mf($order);
                    //订单满反--end
                }else if($order['pei_type']==1&&$order['online_pay']==0){
                    //新增加的 订单为商家配送时候 用户货到付款
                    $log = $staff_log = '订单完成结算(ID:'.$order_id.')';
                    $staff_amount = $order['pei_amount'];
                    if($staff_amount){

                        $true_staff_amount = K::M('staff/config')->get_peiamount($order,$order['staff_id'],$staff_amount);

                        $percent_and_money = K::M('staff/staff')->get_percent_and_money($order['staff_id'], $true_staff_amount); // 获取当前提现比例下，配送员最终可提现金额以及比例（每天入账金额，提现不再读取比例）
                        $bill_data = array(
                            'staff_id' => $order['staff_id'],
                            'freight_amount' => $true_staff_amount,
                            'amount' => $percent_and_money['end_money'],
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
                                'dateline'=>__TIME,

                            );
                            K::M('staff/billslog')->create($bill_log);
                        }

                        //统计 配送员部分
                        $tongji['staff_fee'] =$staff_amount- $percent_and_money['end_money'];
                        $tongji['staff_amount'] = $percent_and_money['end_money'];
                        $tongji['platform_staff'] = ($percent_and_money['end_money']-$staff_amount)>0? ($percent_and_money['end_money']-$staff_amount):0;
                        //统计 配送员部分
                        $tongji['site_fee']=$tongji['site_fee']+($true_staff_amount-$percent_and_money['end_money']);
                        $tongji['amount'] = $order['total_price']-$order['first_youhui']-$order['coupon']-$order['order_youhui']-$order['hongbao']-$order['discount_youhui']-$order['huangou_youhui']-$order['peicard_youhui'];

                        //这里生成骑手补贴相关 外卖3.8
                        if($staff_amount<$true_staff_amount){
                            $staff = K::M('staff/staff')->detail($order['staff_id']);
                            $subsidy_staff = array(
                                'order_id'=>$order_id,
                                'staff_id'=>$order['staff_id'],
                                'pei_amount'=>$staff_amount,
                                'staff_amount'=>$true_staff_amount,
                                'diff_amount'=>$true_staff_amount-$staff_amount,
                                'from'=>'waimai',
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
                        //这里生成骑手补贴相关 外卖3.8
                    }
                    $shop_detail = K::M('waimai/waimai')->detail($order['shop_id']);
                    $shop_amount = $order['total_price']-$order['coupon']-$order['order_youhui']-$staff_amount-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui']-$order['huangou_youhui'];

                    if($shop_detail['jiesuan_type']==1){
                        $fee =  number_format((($order['total_price']-$waimai_order['freight']-$waimai_order['package_price']) * $shop_detail['waimai_bl'])/100, 2, '.', '');
                        if($fee<=0){
                            $fee = 0;
                        }
                    }else if($shop_detail['jiesuan_type']==2){
                        $fee =  number_format((($order['total_price']-$waimai_order['freight']) * $shop_detail['waimai_bl'])/100, 2, '.', '');
                        if($fee<=0){
                            $fee = 0;
                        }
                    }else{
                        $fee =  number_format(($shop_amount * $shop_detail['waimai_bl'])/100, 2, '.', '');
                        if($shop_amount<=0){
                            $fee = 0;
                        }
                    }

                    $bill_data = array();
                    $bill_data['shop_id']=$waimai_order['shop_id'];
                    $bill_data['status'] = 0;
                    $bill_data['amount']=$shop_amount-$fee;
                    $bill_data['fee'] =$fee;
                    $bill_data['shop_amount'] = $waimai_order['shop_amount']+$waimai_order['first_shop']+$order['coupon'];
                    $bill_data['roof_amount'] = $waimai_order['roof_amount']+$waimai_order['first_roof']+$order['hongbao']+$order['peicard_youhui'];
                    $bill_data['user_amount'] = $order['total_price']-$order['first_youhui']-$order['coupon']-$order['order_youhui']-$order['hongbao']-$order['discount_youhui']-$order['huangou_youhui']-$order['peicard_youhui'];
                    $bill_data['freight'] = $staff_amount;
                    K::M('waimai/bills')->create($bill_data);

                    //数据统计商家部分
                    $tongji['shop_amount'] = $bill_data['amount'];
                    $tongji['shop_fee'] = $fee;
                    $tongji['site_fee'] =  $tongji['site_fee']+$fee;
                    //数据统计商家部分

                    $filter = array(
                        'bills_sn'=>date('Ymd'),
                        'shop_id'=>$waimai_order['shop_id']
                    );
                    $bills = K::M('waimai/bills')->find($filter);
                    $data = array('bills_id'=>$bills['bills_id'],
                        'bills_sn'=>date('Ymd'),
                        'shop_id'=>$waimai_order['shop_id'],
                        'bills_number'=>$waimai_order['order_id'],
                        'status'=>0,
                        'online_pay'=>$waimai_order['online_pay'],
                        'amount'=>$shop_amount-$fee,
                        'fee'=>$fee,
                        'shop_amount'=>$waimai_order['shop_amount']+$waimai_order['first_shop']+$order['coupon'],
                        'roof_amount'=>$waimai_order['roof_amount']+$waimai_order['first_roof']+$order['hongbao']+$order['peicard_youhui'],
                        'freight'=> $bill_data['freight'],
                        'user_amount'=>$bill_data['user_amount'],
                        'bl'=>$shop_detail['waimai_bl']
                    );
                    K::M('waimai/billslog')->create($data);
                    $order['fee'] = $fee;

                    $order['shop_amount'] = $order['total_price']-$order['first_youhui']-$order['coupon']-$order['order_youhui']-$order['hongbao'] - $order['discount_youhui'] - $order['huangou_youhui'] - $order['peicard_youhui'];
                    K::M('cash/bills')->create_bills($order);
                    K::M('waimai/huodongmf')->coupon_mf($order);
                    //商家补贴统计
                    if(($order['hongbao']>0)||($order['order_youhui'])>0||($order['first_youhui']>0)||($order['coupon']>0)||($order['discount_youhui']>0)){
                        $subsidy_waimai = array();
                        $subsidy_waimai['order_id'] = $order['order_id'];
                        $subsidy_waimai['shop_id'] = $order['shop_id'];
                        $subsidy_waimai['platform_first'] = $waimai_order['first_roof'];
                        $subsidy_waimai['platform_mj'] = $waimai_order['roof_amount'];
                        $subsidy_waimai['platform_hongbao'] = $order['hongbao'];
                        $subsidy_waimai['shop_first'] = $waimai_order['first_shop'];
                        $subsidy_waimai['shop_mj'] = $waimai_order['shop_amount']-$order['discount_youhui']-$order['huangou_youhui'];
                        $subsidy_waimai['shop_coupon'] = $order['coupon'];
                        $subsidy_waimai['year'] = date('Y',$order['dateline']);
                        $subsidy_waimai['mouth'] = date('Ym',$order['dateline']);
                        $subsidy_waimai['day'] = date('Ymd',$order['dateline']);
                        $subsidy_waimai['hour'] = date('H',$order['dateline']);
                        $subsidy_waimai['group_id'] = $shop_detail['group_id'];
                        $subsidy_waimai['city_id'] = $shop_detail['city_id'];
                        $subsidy_waimai['bl'] = $shop_detail['waimai_bl'];
                        $subsidy_waimai['shop_discount'] = $order['discount_youhui'];
                        $subsidy_waimai['uid'] = $order['uid'];
                        $subsidy_waimai['dateline'] = $order['dateline'];

                        //4.0新增商家换购和配送会员卡优惠
                        $subsidy_waimai['shop_huangou'] = $order['huangou_youhui'];
                        $subsidy_waimai['platform_peicard'] = $order['peicard_youhui'];

                        K::M('subsidy/waimai')->create($subsidy_waimai);

                        //统计  优惠相关
                        $tongji['platform_first'] = $subsidy_waimai['platform_first'];
                        $tongji['platform_mj'] = $subsidy_waimai['platform_mj'];
                        $tongji['platform_hongbao'] = $subsidy_waimai['platform_hongbao'];
                        $tongji['shop_first'] = $subsidy_waimai['shop_first'];
                        $tongji['shop_mj'] = $subsidy_waimai['shop_mj'];
                        $tongji['shop_coupon'] = $subsidy_waimai['shop_coupon'];
                        $tongji['shop_discount'] = $subsidy_waimai['shop_discount'];

                        //4.0新增商家换购和配送会员卡优惠
                        $tongji['shop_huangou'] = $subsidy_waimai['shop_huangou'];
                        $tongji['platform_peicard'] = $subsidy_waimai['platform_peicard'];

                        //统计 优惠相关
                    }
                    //商家补贴统计
                }else{
                    //其他情况处理
                    $tongji['staff_fee'] =0;
                    $tongji['staff_amount'] = 0;
                    $tongji['platform_staff'] = 0;
                    //数据统计商家部分
                    //$tongji['shop_amount'] = $order['amount']+$order['money'];
                    $tongji['shop_fee'] = 0;
                    //数据统计商家部分
                    $tongji['amount'] =  $tongji['shop_amount']  = $order['total_price']-$order['coupon']-$order['order_youhui']-$order['first_youhui']+$waimai_order['roof_amount']+$waimai_order['first_roof']-$order['discount_youhui'];

                    $tongji['platform_first'] = $waimai_order['first_roof'];
                    $tongji['platform_mj'] = $waimai_order['roof_amount'];
                    $tongji['platform_hongbao'] = $order['hongbao'];
                    $tongji['shop_first'] = $waimai_order['first_shop'];
                    $tongji['shop_mj'] =  $waimai_order['shop_amount']-$order['discount_youhui'];
                    $tongji['shop_coupon'] = $order['coupon'];
                    $tongji['shop_discount'] = $order['discount_youhui'];

                    //4.0新增商家换购和配送会员卡优惠
                    $tongji['shop_huangou'] = $order['huangou_youhui'];
                    $tongji['platform_peicard'] = $order['peicard_youhui'];

                    $tongji['site_fee'] = 0;
                }

                //生成统计数据
                K::M('site/tongji')->create($tongji);
                //生成统计数据

                //3.8新增超时规则的处理 begin
                $order_time =  K::M('order/time')->detail($order_id);
                if($order['staff_id'] && !$order_time['staff_compltet_time']&&$order['expect_time']<__TIME){
                    $data_time_out = array(
                        'order_id'=>$order['order_id'],
                        'staff_id'=>$order['staff_id'],
                        'jd_time'=>$order['jd_time'],
                        'complete_time'=>__TIME,
                        'timeout'=>$order['expect_time'],
                        'dateline'=>__TIME
                    );
                    K::M('staff/timeoutorder')->create($data_time_out);
                }

                if($order['first_order']){
                    if($m = K::M('member/member')->detail($order['uid'])){
                        if(preg_match('/(B|S|D|M)(\d+)/i', $m['pmid'], $a)){
                            if($a[1] == 'M'){ //会员邀请
                                K::M('member/invite')->update($order['uid'],array('status'=>2));//邀请结束
                                $inviteCfg = K::$system->config->get('invite');
                                if ($inviteCfg['is_inviter_hongbao'] == 1) { // 邀请人是否奖励红包
                                    if($pm = K::M('member/member')->detail((int)$a[2])){
                                        if($moneys = K::M('member/invite')->send_hongbao_by_cfg($inviteCfg['inviter_hongbao_cfg'], $pm['uid'], 5)){
                                            K::M('member/invite')->update($order['uid'], array('money'=>$moneys));
                                        }
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
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                K::M('order/log')->create(array('order_id'=>$order_id,'from'=>$from,'log'=>$log,'status'=>8));
                if($order['order_from'] != 'wxapp' || $from != 'member'){
                    K::M('order/order')->send_member('订单已完成', sprintf("您在[%s]下的订单(%s)，{$log}", $waimai['title'], $order_id), $order);
                }
                //记录配送员距离等信息 2017-12-25 外卖3.7新增 begin
                K::M('staff/distance')->create($order);
                //记录配送员距离等信息 2017-12-25 外卖3.7新增 end
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
        if(!$order && !($order = K::M('order/order')->detail($order_id))){
            return false;
        }elseif($order['order_status'] < 0){
            $this->msgbox->add('订单已取消过，不能再取消', 449);
            return false;
        }elseif(in_array($order['order_status'], array(0, 1, 2, 3, 4, 5))){ 
            //-1:已取消，0：未处理，1：已接单，2：已配货，3：配送开始，4：配送完成，8：订单完成
            // 基于 mysql_affected_rows 返回影响的记录行数防止并发  add by zhuhongwei 2017-3-9
            if($this->db->update('order', array('order_status'=>-1), "order_id='{$order_id}'", true)){
                K::M('order/order')->update($order_id, array('lasttime'=>__TIME), true);// 更新时间
                if($order['online_pay']){
                    $refund_money = $refund_amount = 0;
                    $refund_money = $order['money'];
                    //退回已付款金额到余额,这时数据库的order_status已经为-1
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
                                $refund_log = $trade['refund_log'];
                            }
                        }else {
                            $refund_money += $order['amount'];
                            $refund_log = "资金退回余额";
                        }
                    }
                    if($refund_money > 0){
                        K::M('member/member')->update_money($order['uid'], $refund_money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                    }
                    // if($money = $this->get_return_amount($order_id, $order)){
                    //     K::M('member/member')->update_money($order['uid'], $money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                    // }
                }

                //退还红包
                if($order['hongbao_id']){
                    K::M('hongbao/hongbao')->update($order['hongbao_id'], array('order_id'=>0, 'used_time'=>0, 'used_ip'=>''));
                }
                //退回优惠券
                if($order['from'] == 'waimai' && $order['coupon_id']){
                    K::M('waimai/coupon')->refund_coupon($order['coupon_id']);
                }
                //退回商品库存
                if($order['from'] == 'waimai'){
                    $this->return_sku($order_id, $order);
                }
                //更新商户订单数量
                if($order['shop_id']){
                    if($order['from'] == 'waimai'){
                        K::M('waimai/waimai')->update_count($order['shop_id'], 'orders', -1);
                    }else {
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

                //4.0删除会员卡记录
                if($order['peicard_id']){
                    K::M('peicard/log')->delete_by_orderId($order['order_id']);
                }
                
                if($from == 'admin'){
                    $log = '管理员取消订单(订单号:'.$order['order_id'].')'.$extend;
                    K::M('member/member')->send($order['uid'], '订单被取消', $log, array('tag_and'=>'login_on'));
                    if($order['staff_id']){
                        K::M('staff/staff')->send($order['staff_id'], '配送订单被取消', $log, array('tag_and'=>'login_on'));
                    }
                    if($order['pay_status'] || !$order['online_pay']){
                        K::M('shop/shop')->send($order['shop_id'], '订单被取消', $log, array('tag_and'=>'login_on'));
                    }
                }else if($from == 'shop'){
                    $log = '商家取消订单(订单号:'.$order['order_id'].')'.$extend;
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
                    K::M('member/member')->send($order['uid'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    if($order['staff_id']){
                        K::M('staff/staff')->send($order['staff_id'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    }
                    if($order['pay_status'] || !$order['online_pay']){
                        K::M('shop/shop')->send($order['shop_id'], '订单被系统取消', $log, array('tag_and'=>'login_on'));
                    }                    
                }else{
                    $log = '用户取消订单(订单号:'.$order['order_id'].')'.$extend;
                }

                K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                if($refund_amount > 0){
                    K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id']));
                }
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

                if($order['order_from'] != 'wxapp' || $from != 'member'){
                    K::M('order/order')->send_member('订单已取消', sprintf("您在[%s]下的订单(%s)，{$log}", $waimai['title'], $order_id), $order);
                }
                
                if($from=='system'||$from=='shop'){
                    K::M('waimai/waimai')->update_count($order['shop_id'],'refund_order',1);
                }

                if($order['first_order']){ //邀请好友
                    K::M('member/invite')->update($order['uid'], array('status'=>0));
                }

                return true;
            }
        }
        return false;
    }

    public function format_data($row)
    {
        if($row['from']=='waimai'){
            $show_btn = array();
            //endtime 倒计时  pay 去支付 comment 评论 again 再来一单 canel 取消 cui 催单 confirm 确认送达 payback 申请退款 see 查看评价 admin 申请客服
            //waiting 等待处理结果
            if($row['order_status']==8){
                if($row['refund_status']==3&&$row['comment_status']==0){
                    //平台拒绝拒绝退款--没有评论 再来一单 评论
                    $msg = '平台拒绝退款';
                    $show_btn = array(
                        'again'=>1,'comment'=>1
                    );
                }else if($row['refund_status']==3&&$row['comment_status']==1){
                    //平台拒绝退款--以评论
                    $msg = '平台拒绝退款';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==0&&$row['comment_status']==0){
                    //正常订单 没有评价的
                    $msg = '等待评价';
                    $show_btn = array(
                        'again'=>1,'comment'=>1
                    );
                }else if($row['refund_status']==0&&$row['comment_status']==1){
                    //正常订单 已经评价
                    $msg = '已评价';
                    $show_btn = array(
                        'again'=>1,'see'=>1
                    );
                }else{
                    $msg = '已完成';
                    if($row['comment_status'] == 0){
                        $show_btn = array('again'=>1, 'comment'=>1);
                    }else{
                        $show_btn = array('again'=>1,'see'=>1);
                    }
                }
            }else if($row['order_status']==4){
                if($row['refund_status']==1&&$row['pei_type']==1){
                    //申请退单 平台送 骑手已送达已送达
                    $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==1&&$row['pei_type']==0&&$row['online_pay']==1){
                    //申请退单 商家自己送 商家已送达 在线支付
                    $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==1){
                    //正常流程 平台送 在线支付  配送员已送达
                    $msg = '骑手已送达';
                    $show_btn = array(
                        'confirm'=>1,
                        'again'=>1,
                        'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==0){
                    //正常流程 平台送 在线支付  配送员已送达
                    $msg = '骑手已送达';
                    $show_btn = array(
                        'confirm'=>1,
                        'again'=>1,
                    );
                }
                else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==1){
                    //正常流程 商家自己送 已送达的 在线支付
                    $msg = '商家已送达';
                    $show_btn = array(
                        'confirm'=>1,
                        'again'=>1,
                        'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==0){
                    //正常流程 商家自己送 已送达的 货到付款
                    $msg = '商家已送达';
                    $show_btn = array(
                        'confirm'=>1,
                        'again'=>1,
                    );
                } else if($row['refund_status']==3){
                    //商家拒绝以后 申请客服介入的
                    $msg = '客服介入中';
                    $show_btn = array(
                        'waiting'=>1,'again'=>1, 'confirm'=>1,
                    );
                }else if($row['refund_status']==-1){
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,'confirm'=>1
                    );
                }
            }else if($row['order_status']==3){
                if($row['refund_status']==1&&$row['pei_type']==1){
                    //骑手开始配送 申请退款
                    $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==1&&$row['pei_type']==0){
                    //商家开始送 申请退款的
                    $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==1){
                    //正常流程 骑手送货中
                    $msg = '骑手送货中';
                    /*$show_btn = array(
                        'cui'=>1,'confirm'=>1,'payback'=>1
                    );*/
                    $show_btn = array('confirm'=>1,'payback'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==0){
                    //正常流程 骑手送货中
                    $msg = '骑手送货中';
                    /*$show_btn = array(
                        'cui'=>1,'confirm'=>1
                    );*/
                    $show_btn = array('confirm'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }
                }
                else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==1){
                    //正常流程下 商家送货中 在线支付
                    $msg = '商家送货中';
                    $show_btn = array(
                        'cui'=>1,'confirm'=>1,'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==0){
                    //正常流程 商家送 货到付款
                    $msg = '商家送货中';
                    $show_btn = array(
                        'cui'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==3){
                    $msg = '客服介入中';
                    $show_btn = array(
                        'waiting'=>1,'again'=>1, 'confirm'=>1,
                    );
                }else if($row['refund_status']==-1&&$row['pei_type']==1){
                    //骑手送货中 已拒绝
                    $msg = '骑手已接单';
                    /*$show_btn = array(
                        'cui'=>1,'admin'=>1,'confirm'=>1
                    );*/
                    $show_btn = array('admin'=>1,'confirm'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }
                }
                else if($row['refund_status']==-1&&$row['pei_type']==0&&$row['online_pay']==1){
                    //商家送货 已拒绝 在线支付
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                       'admin'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==-1&&$row['pei_type']==0&&$row['online_pay']==0){
                    //商家送货 已拒绝 货到付款
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,'confirm'=>1
                    );
                }
            }else if($row['order_status']==2){
                if($row['refund_status']==1){
                     $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                    //修改流程
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['staff_id']&&$row['online_pay']==1){
                    //正常订单 骑手已接单
                    $msg = '骑手已接单';
                    /*$show_btn = array(
                        'cui'=>1,'confirm'=>1,'payback'=>1
                    );*/
                    $show_btn = array('confirm'=>1,'payback'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['staff_id']&&$row['online_pay']==0){
                    //正常订单 骑手已接单
                    $msg = '骑手已接单';
                    /*$show_btn = array(
                        'cui'=>1,'confirm'=>1
                    );*/
                    $show_btn = array('confirm'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }

                }else if($row['refund_status']==0&&$row['pei_type']==1&&!$row['staff_id']&&$row['online_pay']==1){
                    //正常订单 骑手未接单
                    $msg = '等待骑手接单';
                    $show_btn = array(
                        'payback'=>1,'again'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&!$row['staff_id']&&$row['online_pay']==0){
                    //正常订单 骑手未接单
                    $msg = '等待骑手接单';
                    $show_btn = array(
                      'again'=>1
                    );
                }
                else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==1){
                    //正常订单 商家送   在线支付
                    $msg = '商户已接单';
                    $show_btn = array(
                       'cui'=>1,'payback'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==0){
                    //正常订单 商家送   货到付款
                    $msg = '商户已接单';
                    $show_btn = array(
                        'cui'=>1,'again'=>1,'confirm'=>1
                    );

                }else if($row['refund_status']==0&&$row['pei_type']==3&&$row['online_pay']==1){
                    //自提单 在线支付
                    $msg = '等待自提';
                    $show_btn = array(
                        'confirm'=>1,'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==3&&$row['online_pay']==0){
                    $msg = '等待自提';
                    $show_btn = array(
                        'confirm'=>1,'again'=>1
                    );
                }else if($row['refund_status']==-1&&$row['pei_type']==1&&$row['online_pay']==1&&!$row['staff_id']){
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,
                    );
                }else if($row['refund_status']==-1&&$row['pei_type']==1&&$row['staff_id']){
                    $msg = '骑手已接单';
                    /*$show_btn = array(
                        'cui'=>1,'admin'=>1
                    );*/
                    $show_btn = array('admin'=>1);
                    if($row['expect_time'] && $row['expect_time'] <= __TIME){
                        $show_btn['cui'] = 1;
                    }
                }else if($row['refund_status']==-1&&$row['pei_type']==0&&$row['online_pay']==1){
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,'confirm'=>1
                    );
                } else if($row['refund_status']==-1&&$row['pei_type']==3&&$row['online_pay']==1){
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==3){
                    $msg = '平台介入中';
                    $show_btn = array(
                        'waiting'=>1,'again'=>1
                    );
                }
            }else if($row['order_status']==1){
                if($row['refund_status']==1){
                    $msg = '退款处理中';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==1){
                    //商家自己送 在线支付
                    $msg = '商家已接单';
                    $show_btn = array(
                        'payback'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==0&&$row['online_pay']==0){
                    //商家自己送 货到付款
                    $msg = '商家已接单';
                    $show_btn = array(
                        'confirm'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==1){
                    //平台送 在线支付
                    $msg = '商家已接单';
                    $show_btn = array(
                       'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==1&&$row['online_pay']==0){
                    //平台送 在线支付
                    $msg = '商家已接单';
                    $show_btn = array(
                        'again'=>1
                    );
                }
                else if($row['refund_status']==0&&$row['pei_type']==3&&$row['online_pay']==1){
                    //自提 在线支付
                    $msg = '商家已接单';
                    $show_btn = array(
                        'confirm'=>1,'payback'=>1
                    );
                }else if($row['refund_status']==0&&$row['pei_type']==3&&$row['online_pay']==0){
                    //自提 货到付款
                    $msg = '商家已接单';
                    $show_btn = array(
                        'confirm'=>1,
                    );
                }else if($row['refund_status']==-1){
                    $msg = '商家拒绝退款';
                    $show_btn = array(
                        'admin'=>1,'confirm'=>1
                    );
                }else if($row['refund_status']==3){
                    $msg = '平台介入中';
                    $show_btn = array(
                        'waiting'=>1,'again'=>1
                    );
                }
            }else if($row['order_status']==0){
                if($row['online_pay']==1&&$row['pay_status']==0){
                    $msg = '未付款';
                    $show_btn = array(
                        'endtime'=>1,'canel'=>1,'pay'=>1
                    );
                }else if($row['online_pay']==0){
                    $msg = '等待商户接单';
                    $show_btn = array(
                        'canel'=>1,'again'=>1
                    );
                }else if($row['online_pay']==1&&$row['pay_status']==1){
                    $msg = '等待商户接单';
                    $show_btn = array(
                        'canel'=>1,'again'=>1
                    );
                }
            }else if($row['order_status']==-1){
                $msg = '已取消';
                $show_btn = array(
                    'again'=>1
                );
            }else if($row['order_status']==-2){
                 if($row['refund_status']==3){
                    $msg = '平台同意退款';
                    $show_btn = array(
                        'again'=>1
                    );
                }else if($row['refund_status']==2){
                    $msg = '商家同意退款';
                    $show_btn = array(
                        'again'=>1
                    );
                }
            }

            if($row['is_baskets']){
                unset($show_btn['again']);
            }

            $row['msg'] = $msg;
            $row['show_btn'] =$show_btn;

            if($row['order_status'] == -2){
                $label = '已退款';
                $warning = '订单已退款';
            }else if($row['order_status'] == -1){
                $label = '已取消';
                $warning = '订单已取消';
            }else if(empty($row['order_status']) && empty($row['pay_status'])){
                $label = '待支付';
                $warning = '订单等待支付';
            }else if($row['order_status'] == 5){
                $label = '待消费';
                $warning = '等待消费';
            }else if($row['order_status'] == 8){
                $label = '已完成';
                $warning = '订单已完成';
            }else{
                $label = '已完成';
                $warning = '订单已完成';
            }
        }

        if($row['from'] == 'waimai'){
            if($row['pei_type'] == 3) {
                if($row['order_status'] == -1) {
                    $label = '已取消';
                    $warning = '订单已取消';
                }else if(empty($row['order_status']) && empty($row['pay_status']) && $row['online_pay'] == 1){
                    $label = '待支付';
                    $warning = '订单等待支付';
                }else if(empty($row['order_status']) && $row['pay_status'] == 1 && $row['online_pay'] == 1){
                    $label = '待接单';
                    $warning = '订单逾期1小时未接单自动取消';
                }else if(empty($row['order_status']) && $row['pay_status'] == 0 && $row['online_pay'] == 0){
                    $label = '待接单';
                    $warning = '订单逾期1小时未接单自动取消';
                }else if($row['order_status'] == 1 || $row['order_status'] == 2){
                    $label = '等待自提';
                    $warning = '等待用户自提';
                }else if($row['order_status'] == 8){
                    $label = '已完成';
                    $warning = '订单已完成';
                }else{
                    $label = '已完成';
                    $warning = '订单已完成';
                }
            } else{
                if($row['order_status'] == -2){
                    $label = '已退款';
                    $warning = '订单已退款';
                }elseif($row['order_status'] == -1){
                    $label = '已取消';
                    $warning = '订单已取消';
                }else if(empty($row['order_status']) && empty($row['pay_status']) && $row['online_pay'] == 1){
                    $label = '待支付';
                    $warning = '订单等待支付';
                }else if(empty($row['order_status']) && $row['pay_status'] == 1 && $row['online_pay'] == 1){
                    $label = '待接单';
                    $warning = '订单逾期1小时未接单自动取消';
                }else if(empty($row['order_status']) && $row['pay_status'] == 0 && $row['online_pay'] == 0){
                    $label = '待接单';
                    $warning = '订单逾期1小时未接单自动取消';
                }else if($row['order_status'] == 1 || $row['order_status'] == 2){
                    $label = '等待配送';
                    $warning = '配货完成等待配送';
                }else if($row['order_status'] == 3){
                    $label = '正在配送';
                    $warning = '订单正在配送中';
                }else if($row['order_status'] == 4){
                    $label = '配送完成';
                    $warning = '订单配送完成';
                }else if($row['order_status'] == 8){
                    $label = '已完成';
                    $warning = '订单已完成';
                }else{
                    $label = '已完成';
                    $warning = '订单已完成';
                }
            }
        }

        /*if($row['shop_id']) {
            if($shop = K::M('waimai/waimai')->detail($row['shop_id'])) {
                $row['shop_title'] = $shop['title'];
                $row['shop_logo'] = $shop['logo'];
            }
        }*/
        $row['shop_title'] = $row['shop_title'] ? $row['shop_title'] : "外卖店铺";
        $row['shop_logo'] = $row['shop_logo'] ? $row['shop_logo'] : "default/shop_logo.png";
        //循环
        $row['msg'] = $msg;
        $row['show_btn'] =$show_btn;
        $row['order_status_label'] = $label;
        $row['order_status_warning'] = $warning;
        return $row;
    }
        
    public function get_label($row){

        if($row['pei_type'] == 3 || ($row['pei_type'] == 4 && !empty($row['zhuohao_id']))) {
            if ($row['pei_type'] == 4) {
                $zhuohao = array();
                if ($row['zhuohao_id'] == 2147483647) {
                    $zhuohao['title'] = '通用';
                }else{
                    if ($zhuohao = K::M('yuyue/zhuohao')->detail($row['zhuohao_id'])) {
                        if ($zhuohao_cate = K::M('yuyue/zhuohaocate')->detail($zhuohao['cate_id'])) {
                            $zhuohao['cate_title'] = $zhuohao_cate['title'];
                        }
                    }
                }
                $row['zhuohao'] = $zhuohao;
            }
            if($row['order_status'] == -1) {
                $label = '已取消';
                $warning = '订单已取消';
            }else if(empty($row['order_status']) && empty($row['pay_status']) && $row['online_pay'] == 1){
                $label = '待支付';
                $warning = '订单等待支付';
            }else if(empty($row['order_status']) && $row['pay_status'] == 1 && $row['online_pay'] == 1){
                $label = '待接单';
                $warning = '订单逾期1小时未接单自动取消';
            }else if(empty($row['order_status']) && $row['pay_status'] == 0 && $row['online_pay'] == 0){
                $label = '待接单';
                $warning = '订单逾期1小时未接单自动取消';
            }else if($row['order_status'] == 1 || $row['order_status'] == 2){
                if ($row['pei_type'] == 4) {
                    $label = '等待取餐';
                    $warning = '等待用户取餐';
                }elseif ($row['pei_type'] == 3) {
                    $label = '等待取餐';
                    $warning = '等待用户取餐';
                }
            }else if($row['order_status'] == 8){
                $label = '已完成';
                $warning = '订单已完成';
            }else{
                $label = '已完成';
                $warning = '订单已完成';
            }
        } else{
            //0:自己送，1:跑腿送,
            if($row['order_status'] == -1){
                $label = '已取消';
                $warning = '订单已取消';
            }else if(empty($row['order_status']) && empty($row['pay_status']) && $row['online_pay'] == 1){
                $label = '待支付';
                $warning = '订单等待支付';
            }else if(empty($row['order_status']) && $row['pay_status'] == 1 && $row['online_pay'] == 1){
                $label = '待接单';
                $warning = '订单逾期1小时未接单自动取消';
            }else if(empty($row['order_status']) && $row['pay_status'] == 0 && $row['online_pay'] == 0){
                $label = '待接单';
                $warning = '订单逾期1小时未接单自动取消';
            }else if($row['order_status']==1){
                $label = '商家已接单';
                $warning = '商家已接单';
            }else if($row['order_status']==3&&$row['pei_type']==0){
                $label = '商家配送中';
                $warning = '商家配送中';
            }else if($row['order_status']==4&&$row['pei_type']==0){
                $label = '商家已送达';
                $warning = '商家已完成配送';
            }else if($row['order_status']==2&&$row['pei_type'] ==1&&!$row['staff_id']){
                $label = '等待配送员接单';
                $warning = '等待配送员接单';
            }else if($row['order_status']==2&&$row['pei_type'] ==1&&$row['staff_id']){
                $label = '配送员已接单';
                $warning = '配送员已接单';
            }else if($row['order_status']==3&&$row['pei_type']==1&&$row['staff_id']){
                $label = '配送员送货中';
                $warning = '配送员送货中';
            }else if($row['order_status']==4&&$row['pei_type']==1&&$row['staff_id']){
                $label = '配送员已送达';
                $warning = '配送员已送达';
            }/*elseif($row['order_status']==2&&$row['pei_type']==1&&$row['staff_id']){
                $label = '配送员已接单';
                $warning = '配送员已送达';
            }*/else if($row['order_status']==2&&$row['pei_type']==0){
                $label = '等待商家配送';
                $warning = '等待商家配送';
            } else if($row['order_status']==-2){
                $label = '同意退款';
                $warning = '同意退款';
            }else if($row['order_status']!=8&&$row['refund_status']==-1&&$row['order_status']!=-1&&$row['order_status']!=-2){
                $label = '拒绝退款';
                $warning = '拒绝退款';
            }else if($row['order_status']==8){
                $label = '已完成';
                $warning = '已完成';
            }
            /*else if($row['order_status'] == 1 || $row['order_status'] == 2){
                $label = '等待配送';
                $warning = '配货完成等待配送';
            }else if($row['order_status'] == 3){
                $label = '正在配送';
                $warning = '订单正在配送中';
            }else if($row['order_status'] == 4){
                $label = '配送完成';
                $warning = '订单配送完成';
            }else if($row['order_status'] == 8){
                $label = '已完成';
                $warning = '订单已完成';
            }*/else{
                $label = '已完成';
                $warning = '订单已完成';
            }
        }
        $row['order_status_label'] = $label;
        $row['order_status_warning'] = $warning;
        return $row;       
    }
    
    //商家自动接单
    public function auto_jiedan($shop_id){
        if(!$shop_id){
            return false;
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            return false;
        }else if(!$waimai['auto_jiedan']){
            return false;
        }else {
           return $this->db->update('order', array('order_status'=>1), "shop_id = ".$shop_id." AND dateline > ".strtotime(date('Y-m-d')).' AND ((online_pay="1" AND pay_status="1") OR (online_pay="0")) AND order_status="0"');

        }

    }

    public function update($val, $data, $checked=false)
    {

        if(isset($val) && !empty($data) && is_array($data)) {
            $this->_checkpk();
            if(!$checked && !($data = $this->_check_schema($data, $val))){
                return false;
            }

            if($ret = $this->db->update($this->_table, $data, self::field($this->_pk, $val))){
                $this->clear_cache($val);

            }
            return $ret;
        }
        return false;
    }
    
   
    // 格式化输出外卖订单优惠详细数据
    public function _format_order_detail($item)
    {
        $data = array();
        $package_price = $item['package_price'] > 0 ? $item['package_price'] : 0; // 餐盒费
        $data[] = array('title'=>'餐盒费', 'val'=>"¥".$package_price);

        $freight = $item['freight'] > 0 ? $item['freight'] : 0; // 用户支付配送费
        $data[] = array('title'=>'用户支付配送费', 'val'=>"¥".$freight);

        if ($item['staff_id'] && $item['pei_type']==1) {
            $pei_amount = $item['pei_amount'] > 0 ? $item['pei_amount'] : 0; // 骑手配送费
            $data[] = array('title'=>'骑手配送费', 'val'=>"¥".$pei_amount);
        }

        $fee = $item['fee'] > 0 ? $item['fee'] : 0; // 平台服务费
        $data[] = array('title'=>'平台服务费', 'val'=>"-¥".$fee['fee']);

        // 商家活动款
        $shop_amount = $item['waimai_order']['shop_amount'] ? $item['waimai_order']['shop_amount'] : 0;
        $first_shop = $item['waimai_order']['first_shop'] ? $item['waimai_order']['first_shop'] : 0;
        $data[] = array('title'=>'商家活动款', 'val'=>"-¥".($shop_amount + $first_shop));

        $total_price = $item['total_price'] > 0 ? $item['total_price'] : 0; // 小计
        $data[] = array('title'=>'小计', 'val'=>"¥".$total_price);
            
        if ($item['first_youhui'] > 0) { // 首单优惠
            $data[] = array('title'=>'首单优惠', 'val'=>"-¥".$item['first_youhui']);
        }
        if ($item['order_youhui'] > 0) { // 满减优惠
            $data[] = array('title'=>'满减优惠', 'val'=>"-¥".$item['order_youhui']);
        }
        if ($item['hongbao'] > 0) { // 红包抵扣
            $data[] = array('title'=>'红包抵扣', 'val'=>"-¥".$item['hongbao']);
        }
        if ($item['money'] > 0) { // 余额抵扣
            $data[] = array('title'=>'余额抵扣', 'val'=>"-¥".$item['money']);
        }
        if ($item['coupon'] > 0) { // 优惠券
            $data[] = array('title'=>'优惠券', 'val'=>"-¥".$item['coupon']);
        }
        if ($item['discount_youhui'] > 0) { // 折扣
            $data[] = array('title'=>'折扣', 'val'=>"-¥".$item['discount_youhui']);
        }

        $amount = $item['amount']+$item['money'] > 0 ? $item['amount']+$item['money'] : 0; // 总计收入
        $data[] = array('title'=>'总计收入', 'val'=>"¥".$amount);
        return $data;
    }


    /**
     * @function  退款（全退处理，包括运费）
     * @params  $order
     * @params  $reply string  说明
     * @params  $from  string  由哪个角色退款的[shop, admin]
     */
    public function refund($order=array(), $reply='', $from='shop')
    {
        if (!is_array($order) || empty($order)) {
            return false;
        }else{
            if ($from == 'shop') {
                if (K::M('order/order')->update($order['order_id'], array('refund_status' => 2, 'order_status' => -2, 'lasttime'=>__TIME))) {// 商家处理退款，更新退款状态已完成、订单状态 -2
                    if ($money = $this->get_return_amount($order['order_id'], $order)) {// 获取订单需要退回的金额
                        $refund_money = $refund_amount = 0;
                        $refund_money = $order['money'];
                        //退回已付款金额到余额,这时数据库的order_status已经为-1
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
                                    //$refund_amount = 0;
                                    $refund_log = $trade['refund_log'];
                                }
                            }else {
                                $refund_money += $order['amount'];
                                $refund_log = "资金退回余额";
                            }
                        }
                        if($refund_money > 0){
                            K::M('member/member')->update_money($order['uid'], $refund_money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                            K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id']));
                        }
                        K::M('waimai/order/refund')->update($order['order_id'], array('reply' => $reply, 'reply_time' => time(), 'refund_price'=>$money, 'from'=>$from, 'status'=>1));// schemas 验证了 reply_time 字段，这里不用__TIME
                    }

                    if($order['hongbao_id']){ //退还红包
                        K::M('hongbao/hongbao')->update($order['hongbao_id'], array('order_id'=>0, 'used_time'=>0, 'used_ip'=>''));
                    }
                    //退回商品库存
                    if(in_array($order['from'], array('waimai'))){
                        K::M("{$order['from']}/order")->return_sku($order['order_id'], $order);
                    }


                    //更新商户订单数量
                    if($order['shop_id']){
                        if($order['from'] == 'waimai'){
                            K::M('waimai/waimai')->update_count($order['shop_id'], 'orders', -1);
                            if($order['coupon_id']){
                                K::M('waimai/coupon')->refund_coupon($order['coupon_id']);
                            }
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

                    //4.0删除会员卡记录
                    if($order['peicard_id']){
                        K::M('peicard/log')->delete_by_orderId($order['order_id']);
                    }

                    $log = '商家同意订单(订单号:'.$order['order_id'].')退款';
                    K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                    K::M('waimai/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id'], 'type'=>8));
                    $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                    K::M('order/order')->send_member('同意退款', sprintf("您在[%s]下的订单(%s)，商家同意退款", $waimai['title'], $order['order_id']), $order);

                    if($order['first_order']){ //邀请好友
                        K::M('member/invite')->update($order['uid'], array('status'=>0));
                    }

                    return true;
                }
            }elseif ($from == 'admin'||$from=='system') {
                if (K::M('order/order')->update($order['order_id'], array('refund_status' => 3, 'order_status' => -2, 'lasttime'=>__TIME))) {// 平台处理退款（最终）、订单状态 -2
                    if ($money = $this->get_return_amount($order['order_id'], $order)) {// 获取订单需要退回的金额
                        $refund_money = $refund_amount = 0;
                        $refund_money = $order['money'];
                        //退回已付款金额到余额,这时数据库的order_status已经为-1
                        if($order['pay_status'] && ($order['amount'] > 0)){
                            $refund_amount = $order['amount'];
                            //原路退回支付金额
                            if(in_array($order['pay_code'], array('wxpay', 'alipay'))){
                                $order['refund_amount'] = $order['amount'];
                                $order['refund_reason'] = '订单(ID:'.$order['order_id'].')取消退款';
                                if(!$trade = K::M('trade/payment')->refund($order['pay_code'], $order, $msg)){
                                    //原路退还失败时退回的余额
                                    $refund_money += $order['amount'];
                                }else{
                                    //$refund_amount = 0;
                                    $refund_log = $trade['refund_log'];
                                }
                            }else if($order['pay_code']!='cash'){
                                $refund_money += $order['amount'];
                            }
                        }
                        if($refund_money > 0){
                            K::M('member/member')->update_money($order['uid'], $refund_money, '订单(ID:'.$order['order_id'].')取消退回到余额');
                            K::M('order/log')->create(array('status'=>-1, 'from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id']));
                        }
                        K::M('waimai/order/refund')->update($order['order_id'], array('reply' => $reply, 'reply_time' => time(), 'refund_price'=>$money, 'from'=>$from, 'status'=>1));
                    }
                    if($order['hongbao_id']){ //退还红包
                        K::M('hongbao/hongbao')->update($order['hongbao_id'], array('order_id'=>0, 'used_time'=>0, 'used_ip'=>''));
                    }
                    //退回商品库存
                    if(in_array($order['from'], array('waimai'))){
                        K::M("{$order['from']}/order")->return_sku($order['order_id'], $order);
                        if($order['coupon_id']){
                            K::M('waimai/coupon')->refund_coupon($order['coupon_id']);
                        }
                    }
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

                    //4.0删除会员卡记录
                    if($order['peicard_id']){
                        K::M('peicard/log')->delete_by_orderId($order['order_id']);
                    }
                    
                    if($from=='admin'){
                        $log = '管理员同意订单(订单号:'.$order['order_id'].')退款';
                    }else if($from=='system'){
                        $log = '超时未处理系统自动同意订单(订单号:'.$order['order_id'].')退款';
                    }
                    K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                    K::M('waimai/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id'], 'type'=>8));
                    $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                    K::M('order/order')->send_member('同意退款', sprintf("您在[%s]下的订单(%s)，平台同意退款", $waimai['title'], $order['order_id']), $order);
                    
                    if($order['first_order']){ //邀请好友
                        K::M('member/invite')->update($order['uid'], array('status'=>0));
                    }
                    
                    return true;
                }
            }

            return false;
        }
    }

    public function refund_refused($order=array(), $reply='', $from='shop')
    {
        if (!is_array($order) || empty($order)) {
            return false;
        }elseif (empty($reply)) {
            return false;
        }else{
            $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
            if ($from == 'shop') {
                if (K::M('order/order')->update($order['order_id'], array('refund_status' => -1))) {// 商家拒绝退款，更新退款状态失败（-1）
                    K::M('waimai/order/refund')->update($order['order_id'], array('reply' => $reply, 'reply_time' => time(), 'from'=>$from, 'status'=>2));
                    $refund_log = '商家拒绝退款订单(订单号:'.$order['order_id'].')';
                    K::M('order/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'],'intro'=>$reply));
                    K::M('waimai/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'], 'type'=>9));

                    K::M('order/order')->send_member('拒绝退款', sprintf("您在[%s]下的订单(%s)，商家拒绝退款:({$reply})", $waimai['title'], $order['order_id']), $order);
                    return true;
                }
            }elseif ($from == 'admin') {
                //修改订单流程 waimai3.7 2017-12-25 begin
                $go = K::M('waimai/config')->getPokemanGo();
                if($go){
                    if(K::M('order/order')->update($order['order_id'], array('refund_status' => 0))){
                        //删除订单退款记录
                        K::M('waimai/order/refund')->delete($order['order_id']);
                        $refund_log = '管理员拒绝退款订单(订单号:'.$order['order_id'].')';
                        K::M('order/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'],'intro'=>$reply));
                        K::M('waimai/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'], 'type'=>9));
                        K::M('order/order')->send_member('拒绝退款', sprintf("您在[%s]下的订单(%s)，平台拒绝退款:({{$reply}})", $waimai['title'], $order['order_id']), $order);
                        return true;
                    }
                    return false;

                }else{
                    if (K::M('order/order')->update($order['order_id'], array('refund_status' => 3))) {// 平台拒绝（最终）, 更新确认订单完成
                        // 管理员拒绝退款订单
                        K::M('waimai/order/refund')->update($order['order_id'], array('reply' => $reply, 'reply_time' => time(), 'from'=>$from, 'status'=>3));
                        $refund_log = '管理员拒绝退款订单(订单号:'.$order['order_id'].')';
                        K::M('order/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'],'intro'=>$reply));
                        K::M('waimai/log')->create(array('from'=>$from, 'log'=>$refund_log, 'order_id'=>$order['order_id'], 'type'=>9));

                        // 管理员确认订单完成
                        K::M('order/order')->confirm($order['order_id'],$order,'admin');
                        $confirm_log = '管理员确认完成订单(订单号:'.$order['order_id'].')';
                        K::M('waimai/log')->create(array('from'=>$from, 'log'=>$confirm_log, 'order_id'=>$order['order_id'], 'type'=>6));
                        $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                        K::M('order/order')->send_member('拒绝退款', sprintf("您在[%s]下的订单(%s)，平台拒绝退款:({{$reply}})", $waimai['title'], $order['order_id']), $order);
                        return true;
                    }
                    return false;
                }
                //修改订单流程 waimai3.7 2017-12-25 end
            }
            return false;
        }
    }

    public function optimal_amount($discount,$products)
    {
        $total_prices = 0;
        $quota = $discount['quota'] ? (int)$discount['quota'] : 0;
        natsort($products,array($this,'price_difference'));
        $discnums = 0;
        foreach ($products as $k => $v) {
            $num = (int)$v['num'];
            $price = (float)$v['price'];
            $oldprice = (float)$v['oldprice'];
            $prices = $price*$num;
            $oldprices = $oldprice*$num;
            if($discount['products'][$v['product_id']]){
                $discnums += $num;
                if($quota >0 && $discnums > $quota){
                    if($discnums-$quota > $num){
                        $prices = $oldprice*$num;
                    }else{
                        $prices = $price*($quota-$discnums+$num)+$oldprice*($discnums-$quota);
                    }                    
                }
            }
            $v['prices'] = $prices;
            $v['oldprices'] = $oldprices;
            $products[$k] = $v;
            $total_prices += $prices;
        }
        //return $total_prices;
        return $products;
    }

    //差价排序辅助uasort()
    protected function price_difference($a, $b)
    {
        if ($a['diffprice'] == $b['diffprice']) {
            return 0;
        }else{
            return ($a['diffprice'] < $b['diffprice']) ? 1 : -1;
        }        
    }

    public function autopei($order_id)
    {
        if(!$order_id){
           return false;
        }else if(!$order= $this->detail($order_id)){
            return false;
        }else if($order['order_status'] != 1){
            return false;
        }else if($order['from'] != 'waimai'){
            return false;
        }else if($order['pei_time'] <= __TIME){
            return false;
        }else{
            $this->db->begin();
            K::M('order/order')->update($order_id,array('order_status'=>2));
            if($this->db->tranform_errno>0){
                $this->db->rollback();
                return false;
            }else{
                $this->db->commit();
                return true;
            }
        }
    }

    public function orders_group_by($filter, $groupby='uid')
    {
        $where = K::M('order/order')->where($filter);
        $sql = "SELECT COUNT(1) as orders,shop_id,uid,staff_id,group_id FROM ".$this->table('order')."   WHERE {$where} GROUP BY {$groupby}";
        $items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row[$groupby]] =$row;
            }
        }
        return $items;
    }


}