<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Order extends Mdl_Table
{   
  
    protected $_table = 'order';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,city_id,shop_id,staff_id,uid,from,order_status,online_pay,pay_status,trade_no,total_price,hongbao_id,hongbao,order_youhui,first_youhui,money,amount,o_lng,o_lat,contact,mobile,addr,house,lng,lat,day,intro,order_from,clientip,dateline,cui_time,comment_status,jd_time,pay_code,pay_time,pei_time,closed,lasttime,pei_amount,pei_type,coupon_id,coupon,express_name,express,refund_status,change_price,day_num,first_order,first_shop_order,group_id,tmp_staff_id,tmp_ltime,print_id,wx_openid,jifen_status,jifen_cfg,discount_youhui,formid,prepayid,expect_time,card_id,card_amount,peicard_id,peicard_youhui,huangou_youhui,is_baskets,member_orders,is_calc_freight_mod,remark,remark_mgr,addr_photo,shop_confirm_time';
    protected $_orderby = array('order_id' => 'DESC');
    public $view_params = array(
        'order_status' => array(
            'default' => 0,
            'select'  => array(
                '-1' => '已取消',
                '0'  => '未处理',
                '1'  => '已接单',
                '2'  => '配货中',
                '3'  => '配货开始',
                '4'  => '配送完成',
                '8'  => '订单完成',
            )
        )
    );
    public function create($data, $checked = false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        $data['jd_time'] = 0;
        $data['cui_time'] = 0;
        $data['pay_time'] = 0;
        $data['lasttime'] = 0;
        $data['day'] = date('Ymd', $data['dateline']);
        if($id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
            if($data['from']=='waimai'){
                $waimai = K::M('waimai/waimai')->detail($data['shop_id']);
                $data['order_id'] = $id;
               /* if($data['online_pay']==1){
                    $this->send_member('订单创建成功', sprintf("您在[%s]下的订单(%s)，下单成功,请支付!", $waimai['title'], $id), $data);
                }else{
                    $this->send_member('订单创建成功', sprintf("您在[%s]下的订单(%s)，下单成功", $waimai['title'], $id), $data);
                }*/

                if($data['online_pay']==0){
                    K::M('pei/group')->set_order_expect_time($id,$data);
                }
            }
            K::M('order/time')->create(array('order_id'=>$id, 'create_time'=>__TIME, 'pay_time'=>0, 'shop_jiedan_time'=>0, 'staff_jiedan_time'=>0, 'staff_start_time'=>0, 'staff_compltet_time'=>0, 'order_compltet_time'=>0));
        }        
        return $id;        
    }
    
    public function set_payed($log, $trade=array())
    {       
        if($log['order_id']){
            $order_id = $log['order_id'];
            if(!$order = $this->detail($order_id)){
                return false;
            }
            K::M('order/time')->update($order_id,array('pay_time'=>__TIME));
            return K::M("{$order['from']}/order")->set_payed($log, $trade);
        }elseif($log['order_ids']){
            $order_ids = explode(",",$log['order_ids']);
            $_order = $this->detail($order_ids[0]);
            $i = 0;
            foreach($order_ids as $order_id){
                if(!$order = $this->detail($order_id)){
                    $i++;
                }
            }
            if($i>0){
                return false;
            }else{
                return K::M("{$_order['from']}/order")->set_payed($log, $trade);
            }
        }
    }
    //确认订单 ，结算订单
    public function confirm($order_id=null, $order=null, $from='member')
    {
        $order_id = (int)$order_id;
        if(!$order && !($order = $this->detail($order_id))){
            return false;
        }
        return K::M("{$order['from']}/order")->confirm($order_id, null, $from);
    }
        
    public function  get_payments()
    {
        return array(
            'wxpay' => '微信支付',
            'alipay' => '支付宝支付',
            'money' => '余额支付',
        );
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
        if(!$order && !($order = $this->detail($order_id))){
            return false;
        }
        return K::M("{$order['from']}/order")->cancel($order_id, null, $from);
    }

    //订单变动给用户消息 $type create, payment, jiedan, qiang, startwork, fineshed, confrim, 
    public function send_member($title, $content, $order, $type='qiang')
    {
        if($order['order_from'] == 'weixin' && $order['wx_openid']){
            //服务号模板消息
            $wxtmpl = K::M('system/config')->get('wxtmpl');
            $config = K::M('system/config')->get('site');
            $wechat = K::M('system/config')->get('wechat');
            if(!$wxtmpl['wxmp']['order']){
                $token =   K::M('weixin/wechat')->admin_wechat_client()->getAccessToken();
                $res = 0;  //判断行业设置 2017/11/17 by yufan
                if($json_get_industry = K::M('net/http')->get('https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token='.$token)){
                    if($data_get_industry = json_decode($json_get_industry,true)){
                        if($data_get_industry['primary_industry'] && $data_get_industry['secondary_industry']){
                            $res = 1;
                        }else{
                            if($json_set_industry = K::M('net/http')->https_request('https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$token,array("industry_id1"=>"1","industry_id2"=>"4"),'json')){
                                $data_set_industry = json_decode($json_set_industry,true);
                            }
                        }
                    }
                }

                if($res){
                    $preg = 0;
                    if($json = K::M('net/http')->get('https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$token)){
                        if($data = json_decode($json,true)){
                            foreach($data['template_list'] as $v){
                                  if($v['title']=='订单状态更新'){
                                      $wxtmpl['wxmp']['order'] = $v['template_id'];
                                      $preg = 1;
                                      break;
                                  }
                            }
                            K::M('system/config')->set('wxtmpl',$wxtmpl);
                        }
                    }
                    if(!$preg){
                        if($jsons = K::M('net/http')->https_request('https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$token,array('template_id_short'=>'TM00017'),'json')){
                            if($datas = json_decode($jsons,true)){
                                if($datas['errcode']==0){
                                    $wxtmpl['wxmp']['order'] = $datas['template_id'];
                                }
                            }
                            K::M('system/config')->set('wxtmpl',$wxtmpl);
                        }
                    }
                }
            }
            $a = array('title'=>$title, 'items' => array('OrderSn' => $order['order_id'], 'OrderStatus' => $title), 'remark' =>$content);
            
            $b = array();   //跳转小程序所需参数
            if($wechat['wxtmpl'] && $wechat['wxapp_appid']){
                $b = array('appid'=>$wechat['wxapp_appid'],'pagepath'=>'pages/orderDetail/detail?orderid='.$order['order_id']);
            }

            $member = K::M('member/member')->detail($order['uid']);
            $url = K::M('helper/link')->mklink('waimai/ucenter/order:detail', array('args'=>$order['order_id']), array(), 'www');
            $url = trim(str_replace("?","",$url));
            K::M('weixin/wechat')->admin_wechat_client()->sendTempMsg($member['wx_openid'], $wxtmpl['wxmp']['order'], $url, $a, $b);
        }/*elseif($order['order_from'] == 'wxapp' && $order['wx_openid']){
            //小程序模板消息
            $wxtmpl = K::M('system/config')->get('wxtmpl');
            $config = K::M('system/config')->get('site');
            $wechat = K::M('system/config')->get('wechat');
            if(!$wxtmpl['wxapp']['order']){
                $token =   K::M('weixin/wechat')->admin_wechat_client()->getAccessToken();
                $preg = 0;
                if($json = K::M('net/http')->get('https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$token)){
                    if($data = json_decode($json,true)){
                        foreach($data['template_list'] as $v){
                            if($v['title']=='订单状态更新'){
                                $wxtmpl['wxapp']['order'] = $v['template_id'];
                                $preg = 1;
                                break;
                            }
                        }
                        K::M('system/config')->set('wxtmpl',$wxtmpl);
                    }
                }
                if(!$preg){
                    if($jsons = K::M('net/http')->https_request('https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$token,array('template_id_short'=>'TM00017'),'json')){
                        if($datas = json_decode($jsons,true)){
                            if($datas['errcode']==0){
                                $wxtmpl['wxapp']['order'] = $datas['template_id'];
                            }
                        }
                        K::M('system/config')->set('wxtmpl',$wxtmpl);
                    }
                }
            }
            $a = array('title'=>$title, 'items' => array('OrderSn' => $order['order_id'], 'OrderStatus' => $title), 'remark' =>$content);
            $member = K::M('member/member')->detail($order['uid']);
            $url = K::M('helper/link')->mklink('ucenter/order:detail', array('args'=>$order['order_id']), array(), 'www');
            K::M('weixin/wechat')->admin_wxapp_client()->sendTempMsg($member['wx_openid'], $wxtmpl['wxmp']['order'], $url, $a);
        }*/elseif($order['order_from'] == 'wxapp' && $order['wx_openid']){
            //小程序模板消息
            $wxtmpl = K::M('system/config')->get('wxtmpl');
            if(!$wxtmpl['wxapp']['order']){
                if($tmpl_id = K::M('weixin/wxapp')->admin_wxapp_client()->getTmplid('order')){
                    $wxtmpl['wxapp']['order'] = $tmpl_id;
                    K::M('system/config')->set('wxtmpl',$wxtmpl);
                }                
            }
            
            $openid = $order['wx_openid'];
            $tmpl_id = $wxtmpl['wxapp']['order'];
            $page = 'pages/orderDetail/detail?orderid='.$order['order_id'];
            $params = array('keyword1'=>$title, 'keyword2' =>$order['order_id'], 'keyword3' =>$content);
            //$form_id = '';
            if($form_id = $order['formid']){
                if($res = K::M('weixin/wxapp')->admin_wxapp_client()->sendTempMsg($openid, $tmpl_id, $page, $form_id, $params)){
                    if($res['errcode'] > 0 && $order['prepayid']){
                        K::M('weixin/wxapp')->admin_wxapp_client()->sendTempMsg($openid, $tmpl_id, $page, $order['prepayid'], $params);
                    }
                }
            }else if($form_id = $order['prepayid']){
                K::M('weixin/wxapp')->admin_wxapp_client()->sendTempMsg($openid, $tmpl_id, $page, $form_id, $params);
            }
            //K::M('weixin/wxapp')->admin_wxapp_client()->sendTempMsg($openid, $tmpl_id, $page, $form_id, $params);
        }
        return K::M('member/member')->send((string)$order['uid'], $title, $content, array('type'=>'order', 'order_id'=>$order['order_id']));
    }
    //向配送员发消息
    public function send_staff($title, $content, $order, $type='confirm')
    {
        if(!$staff_id = $order['staff_id']){
            return false;
        }
        return K::M('staff/staff')->send($staff_id, $title, $content, array('type'=>'order', 'order_id'=>$order['order_id']));
    }
    
    //像商户发消息
    public function send_shop($title, $content, $order, $type='confirm')
    {
        if(!$shop_id = $order['shop_id']){
            return false;
        }
        return K::M('shop/shop')->send($shop_id, $title, $content, array('type'=>'order', 'order_id'=>$order['order_id']));
    }

    public function customs_by_shop($filter, $page=1, $limit=50, &$count=0)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $items = array();
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table)." WHERE $where GROUP BY `uid`";
        if($count = $this->db->GetOne($sql)){
            $sql = "SELECT uid, SUM(`amount`+`money`) as total_amount, COUNT(1) total_order FROM ".$this->table($this->_table)." WHERE $where GROUP BY `uid` ORDER BY `uid` $limit";
            if($rs = $this->db->Execute($sql)){
                $count = $this->db->GetOne("SELECT FOUND_ROWS()");
                while($row = $rs->fetch()){
                    $items[$row['uid']] = $row;
                }
            }
        }
        return $items;    
    }
    /**
     * 根据天统计订单
     * param $filter 订单条件
     * param $limit 开始 
     */
    public function count_by_day($filter=null, $page=1,$limit=30)
    {
        /*if($day = (int)$day){
            return array();
        }*/
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `day`, COUNT(1) as day_order, SUM(`amount`) as day_amount, SUM(`money`) as day_money, SUM(`pei_amount`) as day_pei_money, SUM(`hongbao`) as day_hongbao
                 FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `day` ORDER BY day ASC $limit";      
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['day']] = $row;
            }
        }
        $filter['from'] = 'waimai';
        $filter['staff_id'] = 0;
        $where2 = $this->where($filter);
        $sql = "SELECT `day`,  SUM(`pei_amount`) as day_pei_money 
                 FROM ".$this->table($this->_table)." WHERE {$where2} GROUP BY `day` ORDER BY day ASC $limit";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['day']]['day_pei_money'] -= $row['day_pei_money'];
            }
        }
        return $items;
    }

    public function orderfrom($filter) 
    {
        $where = $this->where($filter);
        $sql = "SELECT order_from, COUNT(1) as nums FROM {$this->table($this->_table)} WHERE {$where} GROUP BY order_from ORDER BY order_from";   
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }
        
    public function ordersale($shop_id,$time){
        $sql = "SELECT sum(a.product_number) as num,(a.product_name) as name,(b.dateline) as time,a.order_id,a.product_id FROM ".$this->table('waimai_order_product')." as a left join ".$this->table($this->_table)." as b on a.order_id = b.order_id where b.from = 'waimai' AND b.order_status = 8 AND b.shop_id = ".$shop_id." AND b.dateline  BETWEEN " . $time . " AND " . time() . " group by a.product_id order by num desc limit 5";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;  
    }

    public function order_format_row($row)
    {
        return $this->_format_row($row);
    }

    public function get_time()
    {
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

    public function count_by_shopid($filter=null, $page=1,$limit=30)
    {
        /*if($day = (int)$day){
            return array();
        }*/
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `shop_id`,`online_pay`, COUNT(1) as day_order, SUM(`amount`) as day_amount, SUM(`money`) as day_money, SUM(`hongbao`) as day_hongbao, SUM(`pei_amount`) as day_pei_money  FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `shop_id` ORDER BY shop_id ASC $limit";      
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
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

    public function where($filter=null, $pre='', $ANDOR='AND')
    {
        if(is_array($filter)){
            if(isset($filter['lat'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['lat'], $m)){
                    $filter['lat'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['lat'] = bcmul($filter['lat'], 1000000);
                }                
            }
            if(isset($filter['lng'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['lng'], $m)){
                    $filter['lng'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['lng'] = bcmul($filter['lng'], 1000000);
                }                
            }
            if(isset($filter['o_lat'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['o_lat'], $m)){
                    $filter['o_lat'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['o_lat'] = bcmul($filter['o_lat'], 1000000);
                }
            }
            if(isset($filter['o_lng'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['o_lng'], $m)){
                    $filter['o_lng'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['o_lng'] = bcmul($filter['o_lng'], 1000000);
                }
            }                       
        }
        return parent::where($filter, $pre, $ANDOR);
    }
    /**
     * 云打印接口
     * param $order_id int  订单号
     * param $num     int  需要打印的份数
     */
    public function yunprint($order_id, $num=null,$plait_id)
    {
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('订单不存在',210);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',211);
        }else if(!$shop = K::M('shop/shop')->detail($order['shop_id'])) {
            $this->msgbox->add('商家不存在',212);
        }else if(!in_array($order['from'],array('waimai','other'))) {
            $this->msgbox->add('目前只支持外卖订单打印',213);
        }else if(!$plait_id){
            $this->msgbox->add('没找到打印机',218);
        } else if(!$printer = K::M('shop/print')->detail($plait_id)){
            $this->msgbox->add('未设置或启用打印机',213);
        }else {
            if(!$num = (int)$num){
                $num = $printer['num'];
            }
            $site = K::M('system/config')->get('site');
            $js_price = $order['amount'] + $order['money'];
    
            switch ($order['from']) {
                case 'waimai':
                    $products = K::M('waimai/orderproduct')->items(array('order_id'=>$order['order_id']));
                     foreach ($products as $k => $v) {
                        $shuxin = "";
                        foreach ($v['specification'] as $vvt) {
                            $shuxin .= "+" . $vvt['val'];
                        }
                        $v['shuxin'] = $shuxin;
                       $products[$k]['product_name'] =$v['product_name'].$v['shuxin'];
                     }
                    break;
                case 'other':
                    $other_order = K::M('other/order')->detail($order['order_id']);
                    $products = $other_order['product'];
                    if($other_order['type']=='meituan'){
                        $site['title'] .="[美团]";
                    }else if($other_order['type']=='ele') {
                        $site['title'] .="[饿了吗]";
                    }else{
                        $site['title'] .="[自发单]";
                    }
                    $js_price = $order['total_price'];
                    break;
                default:
                    $products = array();
                    break;
            }
            

            $payments = $this->get_payments();
            if($order['online_pay'] == 1){
                $pay = '线上支付';
            }
            else{
                $pay = '线下支付';
            }

            if($order['pay_status'] == 1){
                $pay_status = '[已付]';
            }
            else{
                $pay_status = '[未付]';
            }
            //$youhui = $first_yh + $order_yh + $hongbao_yh;
            if($order['pei_time']==0){
                $si = "";
                if($order['pei_type'] == 3){
                    $pei_time_label = '立即自提';
                }else{
                    $pei_time_label = '尽快送达';
                }
            }else{
                $si = "[预订单]";
                $pei_time_label = date('Y-m-d H:i', $order['pei_time']);
            }

            $waimai = K::M('waimai/waimai')->detail($order['shop_id']);

            if(PRINT_TYPE=='ylyun'){
                $content = '';
                if($waimai['auto_print']){
                    $content .= "<MC>4,00010,1</MC><MN>".$num."</MN>\n";
                }else{
                    $content .= "<MN>".$num."</MN>\n";
                }
                
                $content.="<FW2><FH2><FB>{$site['title']}#".$order['day_num'].$si."</FB></FH2></FW2>\n";
                $content .= "<center>"."{$shop['title']}"."({$shop['city_name']})"."</center>\n";
                $content .="[订单编号]: ".date('m-d',$order['dateline']).'#'.$order['day_num'].$si."\n";
                $content .= "[下单时间]: ".date('Y-m-d H:i:s', $order['dateline'])."\n";
                $content .= "[送达时间]: ".$pei_time_label."\n";
                if($order['intro']){
                    $content .= "<FS><FB>用户留言：".$order['intro']."</FB></FS>\n";
                }
                if($order['pei_type']==1&&$order['online_pay']==0){
                    $content .= "<FS><FB>备注：该订单需要配送员代收￥".($order['total_price']-$order['coupon']-$order['hongbao']-$order['order_youhui']-$order['first_youhui'])."</FB></FS>\n";
                }
                $content .= "<FH2><FB>- - - - - - - - - - - - - - - -</FB></FH2>\n";
                foreach($products as $k=>$v) {
                    $content .= "<FW><FH2>".$v['product_name']."\t\t".'x'.$v['product_number']."\t  ".$v['amount']."</FH2></FW>\n";
                }
                $content .= "<FH2><FB>- - - - - - - - - - - - - - - -</FB></FH2>\n";
                if($order['first_youhui'] > 0) {
                    $content .= "首单优惠：\t\t\t\t  -".$order['first_youhui']."\n";
                }
                if($order['order_youhui'] > 0) {
                    $content .= "满减优惠：\t\t\t\t  -".$order['order_youhui']."\n";
                }
                if($order['discount_youhui'] > 0){
                    $content .= "折扣优惠：\t\t\t\t  -".$order['discount_youhui']."\n";
                }
                if($order['hongbao'] > 0) {
                    $content .= "红包抵扣：\t\t\t\t  -".$order['hongbao']."\n";
                }
                if($order['coupon'] > 0) {
                    $content .= "优惠券：\t\t\t\t  -".$order['coupon']."\n";
                }
                $content .= "<FH2><FB>- - - - - - - - - - - - - - - -</FB></FH2>\n";
                $content .= "<FW2><FH2><FB>总计￥".$js_price.$pay_status."</FB></FH2></FW2>\n";
                //$content .= "<FW2><FH2><FB>".$order['addr'].$order['house']."</FB></FH2></FW2>\n";
                if($order['pei_type'] == 3){
                    $content .= "<FW2><FH2><FB>自提单</FB></FH2></FW2>\n";
                }else{
                    $content .= "<FW2><FH2>".$order['addr'].$order['house']."</FH2></FW2>\n";
                }
                $content .= "<FW2><FH2><FB>联系电话：".$order['mobile']."</FB></FH2></FW2>\n";
                $content .= "<FW2><FH2><FB>联系人:".$order['contact']."</FB></FH2></FW2>\n";
                
            }else if(PRINT_TYPE=='xprint'){
                $content = array();
                $content['dingdanID'] = "dingdanID=".$order['order_id'];
                $content['dayinjisn'] = 'dayinjisn='.$printer['machine_code'];
                $content['pages'] = 'pages='.$num;
                $str = "";
                $str.='<1B40><1B40><1B40><1D2111>'.$site['title'].'#'.$order['day_num'].$si.'<0D0A><1D2100><0D0A>';
                $str.='<0D0A>'.$shop['title'].'('.$shop['city_name'].')';
                $str.="<0D0A>[订单编号]: ".date('m-d',$order['dateline']).'#'.$order['day_num'].$si;
                $str.="<0D0A>[订单ID]: ".$order['order_id'];
                $str.="<0D0A>[下单时间]: ".date('Y-m-d H:i:s', $order['dateline']);
                $str.="<0D0A>[送达时间]: ".$pei_time_label;

                if($order['intro']){
                    $str .= "<0D0A>用户留言：".$order['intro'];
                }
                if($order['pei_type']==1&&$order['online_pay']==0){
                    $str .= "<0D0A><1D2111><1B6101>备注：该订单需要配送员代收￥".($order['total_price']-$order['coupon']-$order['hongbao']-$order['order_youhui']-$order['first_youhui'])."<1B6100><1D2100>";
                }
                
                $str.="<0D0A>- - - - - - - - - - - - - - - -";
                $str.="<0D0A>订单商品<0D0A>";
                foreach($products as $k=>$v) {
                    $str .= "<0D0A><1D2101>".$v['product_name'].'  x'.$v['product_number']."  ".$v['amount']."<1D2101>";
                }
                $str.='<0D0A>- - - - - - - - - - - - - - - -';
                if($order['first_youhui'] > 0) {
                    $str .= "<0D0A>首单优惠：-￥ ".$order['first_youhui'];
                }
                if($order['order_youhui'] > 0) {
                    $str .= "<0D0A>满减优惠：-￥ ".$order['order_youhui'];
                }
                if($order['discount_youhui'] > 0) {
                    $str .= "<0D0A>折扣优惠：-￥ ".$order['discount_youhui'];
                }
                if($order['hongbao'] > 0) {
                    $str .= "<0D0A>红包抵扣：-￥ ".$order['hongbao'];
                }
                if($order['coupon'] > 0) {
                    $str .= "<0D0A>优惠券：-￥ ".$order['coupon'];
                }
                $str .= "<0D0A>- - - - - - - - - - - - - - - -";
                $str .= "<0D0A><1D2111><1B6101>总计￥".$js_price.$pay_status."<1B6100><1D2100>";
                //$str .= "<0D0A><1D2111><1B6101>".$order['addr'].$order['house']."<1B6100><1D2100>";
                
                if($order['pei_type'] == 3){
                    $str .= "<0D0A><1D2111><1B6101>自提单<1B6100><1D2100>";
                }else{
                    $str .= "<0D0A><1D2111><1B6101>".$order['addr'].$order['house']."<1B6100><1D2100>";
                }

                $str .= "<0D0A><1D2111><1B6101>联系电话:".$order['mobile'].'<1B6100><1D2100>';
                $str .= "<0D0A><1D2111><1B6101>联系人:".$order['contact'].'';
                
                $str.="<0D0A><0D0A><0D0A><0D0A>";
                $content['dingdan'] ="dingdan=".$str;
                $content['replyURL'] ="replyURL=".K::M('helper/link')->mklink('order/xprintorder',"",array(),'waimai');
            }else{
                //todo 后期支持更多的打印机使用
            }

            if($num > 0 && isset($content)) {
                if(!$printss = K::M('printer/common')->load()){
                    $this->msgbox->add('加载打印机模型错误',311);
                }else if($data = $printss->send_print($printer['machine_code'],$content,$order_id)){
                    if(PRINT_TYPE=='ylyun'){
                        if($data['error']==0){
                            K::M('order/order')->update($order_id,array('print_id'=>$data['body']['id']));
                            $this->msgbox->add('数据提交成功');
                        }else if($data['error']==8){
                            $this->msgbox->add('打印机信息错误,参数有误',203);
                        }else if($data['error']==9){
                            $this->msgbox->add('连接打印机失败,参数有误',204);
                        }else if($data['error']==10){
                            $this->msgbox->add('权限不足',205);
                        }else if($data['error']==12){
                            $this->msgbox->add('缺少必要参数',206);
                        }else if($data['error']==13){
                            $this->msgbox->add('打印失败,参数有误',207);
                        }else if($data['error']==33){
                            $this->msgbox->add('Uuid不合法',208);
                        }
                    }else if(PRINT_TYPE=='xprint'){
                        $this->msgbox->add('数据提交成功');
                    }else{
                        return true;
                    }

                }else{
                    $this->msgbox->add('打印失败',209);
                }
            }
        }
        return false;
    }
    
    public function print_test($pal_id,$text){
        if(!$printer = K::M('shop/print')->detail($pal_id)){
            $this->msgbox->add('打印机不存在',218);
        }else if(!$text){
            $this->msgbox->add('请输入需要打印的文字',219);
        }else{
            if(PRINT_TYPE=='xprint'){
                $content = array();
                $content['dingdanID'] = "dingdanID=1";
                $content['dayinjisn'] = "dayinjisn=".$printer['machine_code'];
                $content['pages'] = 'pages=1';
                $content['dingdan'] ="dingdan=<1B40><1B40><1B40><1D2111>".$text."<0D0A><1D2100><0D0A>";
                $content['replyURL'] ="replyURL=".K::M('helper/link')->mklink('order/xprintorder',"",array(),'waimai');
            }else if(PRINT_TYPE=='ylyun'){
                $content = '';
                $content .= "<MN>1</MN>\n";
                $content .= "<center>"."打印测试"."</center>\n";
                $content .= "<FH2><FB>- - - - - - - - - - - - - - - -</FB></FH2>\n";
                $content .= "<FW2><FH2><FB>".$text."</FB></FH2></FW2>\n";
            }else {
                //todo 后期支持更多的打印机
            }

            if(isset($content)) {
                if(!$printerss = K::M('printer/common')->load()){
                    $this->msgbox->add('加载模型错误',201);
                    return false;
                }else if(!$state = $printerss->send_print($printer['machine_code'],$content,0)){
                    $this->msgbox->add('提交数据失败', 210);
                    return false;
                }else if(PRINT_TYPE=='ylyun'){
                    if($state) {
                        $rlt = json_decode($state,true);
                        if($rlt->state == 2){
                            $this->msgbox->add('提交时间超时', 210);
                            return false;
                        }
                        else if($rlt->state == 3){
                            $this->msgbox->add('参数有误', 211);
                            return false;
                        }
                        else if($rlt->state == 4){
                            $this->msgbox->add('sign加密验证失败', 212);
                            return false;
                        }
                        else{
                            $this->msgbox->add('数据提交成功');
                            return true;
                        }
                    }
                    return false;
                }else if(PRINT_TYPE=='xprint'){
                    if($state){
                        $this->msgbox->add('提交数据成功');
                        return true;
                    }else{
                        $this->msgbox->add('提交数据失败',213);
                        return false;
                    }
                }else{
                    return false;
                }
                //$state = K::M('printer/ylyun')->send_print($printer['partner'],$printer['apikey'],$printer['machine_code'],$printer['mkey'],$content);
            }
            return false;
        }        
    }
        
    //本地打印
    public function localprint($order_id){
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('订单不存在',210);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在',211);
        }else if(!$shop = K::M('shop/shop')->detail($order['shop_id'])) {
            $this->msgbox->add('商家不存在',212);
        }else if($order['from'] != 'waimai') {
            $this->msgbox->add('目前只支持外卖订单打印',213);
        }else{
            $waimai_shop = K::M('waimai/waimai')->detail($order['shop_id']);
            $products = K::M('waimai/orderproduct')->items(array('order_id'=>$order['order_id']));
            $js_price = $order['amount'] + $order['money'];
            $payments = $this->get_payments();
            if($order['online_pay'] == 1){
                $pay = '线上支付';
            }
            else{
                $pay = '线下支付';
            }
            if($order['pay_status'] == 1){
                $pay_status = '(已付)';
            }
            else{
                $pay_status = '(未付)';
            }
            $data = array();
            $data[] = array(array('title'=>$waimai_shop['title'],'size'=>15));
            $data[] = array(array('title'=>'','size'=>8));
            $data[] = array(array('title'=>'- - - '.$pay.'- - - ','size'=>12));
            $data[] = array(array('title'=>'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -','size'=>9));
            $data[] = array(array('title'=>'','size'=>8));
            $data[] = array(array('title'=>'下单时间:&&&','size'=>9),array('title'=>date('m-d H:i',$order['dateline']),'size'=>9));
            $data[] = array(array('title'=>'','size'=>8));
            if($order['intro']){
                $data[] = array(array('title'=>'备注:&&&','size'=>12));
                $data[] = array(array('title'=>'','size'=>8));
            }
            $data[] = array(array('title'=>'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -','size'=>9));
            foreach ($products as $k=>$v){
                $data[] = array(array('title'=>$v['product_name'].'&&&&','size'=>9),array('title'=>'x'.$v['product_number'].'&&&','size'=>9),array('title'=>$v['amount'],'size'=>9));
            }
            $data[] = array(array('title'=>'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -','size'=>9));
            $data[] = array(array('title'=>'','size'=>4));
            $data[] = array(array('title'=>'订单信息：&&&','size'=>15));
            $data[] = array(array('title'=>'','size'=>4));
            if($order['first_youhui'] > 0) {
                $data[] = array(array('title'=>"首单优惠:".$order['first_youhui'],'size'=>9));
            }
            if($order['order_youhui'] > 0) {
                $data[] = array(array('title'=>"满减优惠:".$order['order_youhui'],'size'=>9));
            }
            if($order['hongbao'] > 0) {
                $data[] = array(array('title'=>"红包抵扣:".$order['hongbao'],'size'=>9));
            }
            if($order['coupon'] > 0) {
                $data[] = array(array('title'=>"优惠券:".$order['coupon'],'size'=>9));
            }
            $data[] = array(array('title'=>'','size'=>4));
            $data[] = array(array('title'=>'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -','size'=>9));
            $data[] = array(array('title'=>'','size'=>8));
            $data[] = array(array('title'=>'总计:'.($order['amount']+$order['money']).$pay_status,'size'=>15));

            $addr = $this->mbStrSplit($order['addr'],12);
            foreach ($addr as $key1 => $value1) {
                $data[] = array(array('title'=>$value1,'size'=>10));
            }
            $data[] = array(array('title'=>'&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&','size'=>9));
            $house = $this->mbStrSplit($order['house'],13);
            foreach ($house as $key => $value) {
                $data[] = array(array('title'=>$value,'size'=>9));
            }
           // $data[] = array(array('title'=>$order['addr'].$order['house'],'size'=>15));
            $data[] = array(array('title'=>$order['mobile'],'size'=>15));
            $data[] = array(array('title'=>$order['contact'],'size'=>15));
            if($order['pei_type']==1&&$order['online_pay']==0){
                $data[]  = array(array('title'=>"备注：该订单需要配送员代收".($order['total_price']-$order['coupon']-$order['hongbao']-$order['order_youhui']-$order['first_youhui'])."元",'size'=>10));
            }
            return $data;
        }
        return array();
    }

    function mbStrSplit ($string, $len=1) {
        $start = 0;
        $strlen = K::M('content/string')->Len($string);
        while ($strlen) {
            $array[] = K::M('content/string')->sub($string,$start,$len,"");
            $string = K::M('content/string')->sub($string, $len, $strlen,"");
            $strlen = K::M('content/string')->Len($string);
        }
        return $array;
    }

    public function shop_members_count($shop_id)
    {
        $sql = "SELECT COUNT(distinct uid) as nums FROM {$this->table($this->_table)} WHERE `shop_id`={$shop_id} AND `order_status` IN (4,8)";   
        if ($row = $this->db->GetRow($sql)) {
            $row = $this->_format_row($row);
        }
        return $row['nums'];
    }

    /**
     * @function  退款（全退处理，包括运费）
     * @params  $order
     * @params  $reply string  说明
     * @params  $from  string  由哪个角色退款的[shop, admin]
     */
    public function refund($order=array(), $reply='', $from='shop')
    {
        return K::M("waimai/order")->refund($order,$reply,$from);
    }

    /**
     * @function  拒绝退款
     * @params  $order
     * @params  $reply string  说明
     * @params  $from  string  由哪个角色拒绝的[shop, admin]
     */
    public function refund_refused($order=array(), $reply='', $from='shop')
    {
        return K::M("waimai/order")->refund_refused($order,$reply,$from);
    }

    protected function _format_row($row)
    {
        if(in_array($row['from'],array('waimai','paotui'))){
            if($row['pei_time'] > 0) {
                $row['pei_time_label'] = date("Y-m-d H:i", $row['pei_time']);
            }else{
                $row['pei_time_label'] = L('尽快送达');
            }
        }
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
        //积分配置冗余  2017/11/10 by yufan
        if($row['jifen_cfg']){
            $row['jifen_cfg'] = unserialize(stripslashes($row['jifen_cfg']));
        }
        return $row;
    }

    public function format_row($row)
    {
      return  $this->_format_row($row);
    }

    public function items_join_cuilog($filter, $orderby=null, $p=1, $l=10, &$count=0)
    {
        $where = '1';
        $ext_sql = '';
        if(is_array($filter)){
            $ext_sql = " RIGHT JOIN ".$this->table('waimai_order')." w ON o.`order_id`=w.`order_id` RIGHT JOIN ".$this->table('order_cuilog')." ext ON o.`order_id`=ext.`order_id` AND o.`cui_time`=ext.`dateline`";
        }
        $where .= " AND ". $this->where($filter, 'o.');
        $orderby = $this->order($orderby, null, 'o.');
        $limit = $this->limit($p, $l);
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table) . " o " . $ext_sql . " WHERE $where";
        $items = array();
        if($count = (int) $this->db->GetOne($sql)){
            $sql = "SELECT ext.*,o.*,w.* FROM ". $this->table($this->_table)." o $ext_sql WHERE $where $orderby $limit";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $row = K::M('order/order')->_format_row($row);
                    if($row[$this->_pk]){
                        $items[$row[$this->_pk]] = $row;
                    }else{
                        $items[] = $row;
                    }
                }
            }
        }
        return $items;
    }

    // 外卖营收统计
    public function items_join_order_product($filter, $orderby=null, $p=1, $l=10, &$count=0)
    {
        $where = '1';
        $ext_sql = $group = '';
        if(is_array($filter)){
            $ext_sql = " RIGHT JOIN ".$this->table('waimai_order_product')." wop ON o.`order_id`=wop.`order_id`";
            $group = " GROUP BY wop.`product_id`";
        }
        $where .= " AND ". $this->where($filter, 'o.');
        $orderby = $this->order($orderby);
        $limit = $this->limit($p, $l);
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table) . " o " . $ext_sql . " WHERE $where $group";
        $items = $_items = array();
        //if($_rs = $this->db->GetOne($sql)){ // 框架不支持子查询
        if($_rs = $this->db->Execute($sql)){
            while($_row = $_rs->fetch()){
                $_items[] = $_row;
            }
            $count = $_items ? count($_items) : 0;
            $sql = "SELECT SUM(wop.`amount`) as total_product_amount, SUM(wop.`product_number`) as total_product_number, wop.`product_id`, wop.`product_name` FROM ". $this->table($this->_table)." o $ext_sql WHERE $where GROUP BY wop.`product_id` $orderby $limit";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $row['amount_ratio'] = $row['number_ratio'] = '0%';
                    $items[$row['product_id']] = $row;
                }
            }
            // 查询总销量及总销售额
            if (!empty($items)) {
                $sql = "SELECT SUM(wop.`amount`) as total_amount, SUM(wop.`product_number`) as total_number FROM ". $this->table($this->_table)." o $ext_sql WHERE $where";
                if ($rs_ex = $this->db->Execute($sql)) {
                    if ($row = $rs_ex->fetch()) {
                        $total_amount = (float)bcdiv((float)$row['total_amount'], 100, 4); // 总额最低只可能0.01  保留4位
                        $total_number = (float)bcdiv((int)$row['total_number'], 100, 2);// 总销量最低只可能1 保留2位
                        foreach ($items as $k => $v) {
                            if ($total_amount > 0) {// 除数不能为0
                                $items[$k]['amount_ratio'] = bcdiv($v['total_product_amount'], $total_amount, 2)."%";
                            }
                            if ($total_number > 0) {// 除数不能为0
                                $items[$k]['number_ratio'] = bcdiv($v['total_product_number'], $total_number, 2)."%";
                            }
                        }
                    }
                }
            }
        }
        return $items;
    }

    /**
     * 根据天统计订单量表
     * param $from   要查询的订单类型 (waimai、tuan、weidian、maidan......)
     * param $limit  条数 (订单类型个数 * 一个月最大天数)
     */
    public function order_count_by_day($filter=null,$page=1,$limit=279)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `day`, COUNT(1) as day_orders, `from` FROM ".$this->table("order")." WHERE {$where} GROUP BY `day`,`from` ORDER BY day ASC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[] = $row;
            }
        }
        return $items;
    }

    public function items_group_by_ids($filter=null,$page=1,$limit=50)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT COUNT(1) as orders, `uid`,`shop_id` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `shop_id`,`uid` $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['uid'].'_'.$row['shop_id']] = $row['orders'];
            }
        }
        return $items;
    }

    public function set_order_day_num($order_id,$shop_id){
        if(!$order_id){
            return false;
        }/*else if(!$order = $this->detail($order_id)){
            return false;
        }*//*else if($order['from']!='waimai'){
            return false;
        }*/else{
                $filter = array();
                $filter['day'] = date('Ymd', __TIME);
                $filter['from'] = 'waimai';
                $filter['shop_id'] = $shop_id;
                $filter['order_id'] = '<:'.$order_id;
                $count = $this->othercount($filter);
                return $this->update($order_id,array('day_num'=>$count+1));
        }
    }

    public function othercount($filter){
        $where = $this->where($filter);
        $sql = "SELECT COUNT(*) as count FROM ".$this->table($this->_table)." IGNORE INDEX (pri)  WHERE {$where}";
        $count = 0;
        if($row = $this->db->GetOne($sql)){
            //$count =  $row['count'];
            $count =  $row;
        }
        return $count;
    }

    // 根据shop_id分组每个商家待接订单数
    public function neworders_by_shopid($filter,$page=1, $limit=30)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `shop_id`, COUNT(1) as neworders FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY `shop_id` ORDER BY shop_id ASC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
    }

    public function items_group_by_staff($filter,$page=1,$limit=200){
        $where = $this->where($filter);
        $limit = $this->limit($page,$limit);
        $sql = "SELECT COUNT(1) as orders ,`staff_id` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `staff_id` $limit";
        $items  = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['staff_id']] = $row;
            }
            return $items;
        }
    }

    //统计配送员每天的订单
    public function items_group_by_staff_day($filter,$page=1,$limit=200){
        $where = $this->where($filter);
        $limit = $this->limit($page,$limit);
        $sql = "SELECT COUNT(1) as orders ,`staff_id`,`day` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `day`, `staff_id` $limit";
       // K::M('system/logs')->log('staff_day',$sql);
        $items  = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['staff_id'].$row['day']] = $row;
            }
            return $items;
        }
    }

    public function assessment_group_by_staff_order($filter){
        $where = $this->where($filter);
        $sql = "SELECT COUNT(1) as orders ,`staff_id` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY  `staff_id` ";
        $items  = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['staff_id']] = $row;
            }
            return $items;
        }
    }

    //外卖3.7新增方法  处理配送员限制接单相关功能 2017 12 19
    public function check_staff_order($staff=null,$group=null,&$msg=''){
        $count = 0;
        if(!$staff){
            $msg = "未指定配送员";
            return false;
        }else if(!$group){
            $msg = "未指定配送站";
            return false;
        }else if($staff['is_used']==0&&$group['is_used']==0){
            return true;
        }else{
            if($staff['is_used']==1){
                $count = $staff['limit_order'];
            }else if($staff['is_used']==0&&$group['is_used']==1){
                $count = $group['limit_order'];
            }
            $filter = array();
            $filter['staff_id'] = $staff['staff_id'];
            $filter['order_status'] = array(1,2,3);
            $ing_count = K::M('order/order')->count($filter);
            if($ing_count>=$count){
                $msg = "还有".$ing_count.'个订单需要处理，请完成后再抢单';
                return false;
            }else{
                return true;
            }
        }
    }

    //获取指定用户的订单数量等
    public function get_user_items_by_shop_id($filter,$page,$limit,&$count,$order_by = array("used_money"=>"DESC")){
        $where = $this->where($filter);
        $limit = $this->limit($page,$limit);
        $orderby = $this->order($order_by);
        $sql = "SELECT COUNT(order_id) as orders,sum(amount+money) as used_money,uid FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY uid {$orderby}".$limit;
        $sql1 = "SELECT COUNT(order_id) as orders,sum(amount+money) as used_money,uid FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY uid {$orderby}";
        $count_sql = "SELECT COUNT(1) FROM ({$sql1}) aa";
        if($res = $this->db->query($count_sql)){
            $row = $res->fetch_array(MYSQLI_NUM);
            $count = $row[0];
        }else{
            $count = 0;
        }
        $items = array();
        if($re = $this->db->Execute($sql)){
            while($rwo = $re->fetch()){
                $items[$rwo['uid']] = $rwo;
            }
        }
        return $items;
    }

    //获取用户最后下单时间
    public function member_last_order_time($filter){
        $where = $this->where($filter);
        $sql = " SELECT * FROM (SELECT uid,dateline as last_order_time FROM ".$this->table($this->_table)." WHERE {$where} ORDER BY last_order_time DESC) asa GROUP BY uid";
        /*SELECT * FROM (SELECT * FROM posts ORDER BY dateline DESC) GROUP BY  tid ORDER BY dateline DESC LIMIT 10*/
        $items = array();
        if($re = $this->db->query($sql)){
            while($rwo = $re->fetch()){
                $items[$rwo['uid']] = $rwo;
            }
        }
        return $items;
    }

    public function group_sum($filter, $orderby=null, $p=1, $l=50, &$count=0){
        $where = $this->where($filter);
        $sum = "";
        if(is_array($filter[':SUM']) && !empty($filter[':SUM'])){
            foreach ($filter[':SUM'] as $v) {
                if($this->field_exists("{$v}")){
                    $sum .= " SUM(`{$v}`) AS {$v},";
                }
            }
        }
        $limit = $this->limit($p, $l);
        $orderby = $this->order($orderby);
        $items = array();
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table)." WHERE $where GROUP BY `day`";
        if($count = $this->db->GetOne($sql)){
            $count = $this->db->Execute($sql) ? $this->db->GetOne("SELECT FOUND_ROWS()") : 0; // 获取记录数，不能带limit参数，否则返回值为limit的值
            $sql = "SELECT `day`, {$sum} COUNT(1) as day_orders FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `day` $orderby $limit";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $items[$row['day']] = $row;
                }
            }
        }
        return $items;
    }

    //统计商家订单
    public function group_sum_by_shop_id($filter = array())
    {
        $where = $this->where($filter);
        $items = $arr = array();
        $sql = "SELECT count(1) as orders,`shop_id` FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY shop_id ";
        if ($res = $this->db->Execute($sql)) {
            while ($row = $res->fetch()) {
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
    }
	 //统计用户未完成订单数量
	public function group_sum_by_uid($filter = array())
	{
	    $where = $this->where($filter);
	    $items = $arr = array();
	    $sql = "SELECT count(*) as total_num FROM " . $this->table($this->_table) . " WHERE {$where} and order_status in(0,1,2,3,4)";
		// $res = $this->db->Execute($sql)
	    if ($res = $this->db->Execute($sql)) {
	        while ($row = $res->fetch()) {
	            $items["data"] = $row;
	        }
	    }
	    return $items;
	}

    public function group_sum_by_shopid_table($filter=array(),$join_table=null)
    {
        $where = $this->where($filter);
        $sql = "SELECT count(1) as orders,`shop_id` FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY shop_id ";

        if($join_table){
            $where = $this->where($filter,'o.');
            $sql = "SELECT count(1) as orders,o.`shop_id` FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table($join_table)." w ON o.order_id = w.order_id WHERE {$where} GROUP BY o.shop_id";
        }
        $items = array();        
        if ($res = $this->db->Execute($sql)) {
            while ($row = $res->fetch()) {
                $items[$row['shop_id']] = $row;
            }
        }
        return $items;
    }



    public function group_sum_by_staffid_table($filter=array(),$join_table=null)
    {
        $where = $this->where($filter);
        $sql = "SELECT count(1) as orders,`staff_id` FROM " . $this->table($this->_table) . " WHERE {$where} GROUP BY staff_id ";

        if($join_table){
            $where = $this->where($filter,'o.');
            $sql = "SELECT count(1) as orders,o.`staff_id` FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table($join_table)." w ON o.order_id = w.order_id WHERE {$where} GROUP BY o.staff_id";
        }

        $items = array();
        if ($res = $this->db->Execute($sql)) {
            while ($row = $res->fetch()) {
                $items[$row['staff_id']] = $row;
            }
        }
        return $items;
    }



    public function get_staff_reward($filter){
        $where = $this->where($filter);
        $sql = "SELECT COUNT(1) as orders,SUM(amount+money) as amount,`staff_id` FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY staff_id";
        $items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['staff_id']] = $row;
            }
        }
        return $items;
    }

    public function group_by_type($filter,$stime,$ltime,$step){
        $where = $this->where($filter);
        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,$step);
        $arr = $items = array();
        switch ($step){
            /*case "y":
                $group_by = "year";
                break;
            case "m":
                $group_by = "mouth";
                break;*/
            case "d":
                $group_by = "days";
                break;
            case "h":
                $group_by = "hours";
                break;
            default:
                $group_by = "days";
                break;
        }
        $sql = "SELECT count(1) as orders ,FROM_UNIXTIME(dateline,'%Y%m%d') as days,FROM_UNIXTIME(dateline,'%H') as hours  FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY `{$group_by}`  ";
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[(int)$row[$group_by]] = $row;
            }
        }
        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['order'][] = $items[$k]['orders']? (float)$items[$k]['orders']:0;

        }
        return $arr;
    }

    //根据配送站获取商家订单统计
    public function items_count_by_group($filter){
        $where = $this->where($filter);
        $sql = "SELECT COUNT(1) as orders,shop_id FROM ".$this->table($this->_table)."   WHERE {$where} GROUP BY shop_id";
        $items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['shop_id']] =$row;
            }
        }
        return $items;
    }

    //连表查询  jh_order  jh_staff
    public function items_join_by_staff($filter,$orderby=array(),$page=1,$limit=50,&$count=0){
        $where  = $this->where($filter,'o.');
        $orderby = $this->order($orderby,'o.');
        $limit = $this->limit($page,$limit);
        $count_sql = "SELECT COUNT(o.order_id) as count  FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')."  w WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($row_count = $res_count->fetch()){
                $count = $row_count['count'];
            }
        }
        $sql = "SELECT o.*,w.name as staff_name,w.mobile as staff_mobile FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id = w.staff_id WHERE {$where} {$orderby} $limit";
        $items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $row = $this->_format_row($row);
                $items[$row['order_id']] = $row;
            }
        }
        return $items;
    }

    public function group_by_type_group($filter,$stime,$ltime,$step){
        $where = $this->where($filter,'o.');
        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,$step);
        $arr = $items = array();
        switch ($step){
            /*case "y":
                $group_by = "year";
                break;
            case "m":
                $group_by = "mouth";
                break;*/
            case "d":
                $group_by = "days";
                break;
            case "h":
                $group_by = "hours";
                break;
            default:
                $group_by = "days";
                break;
        }
        $sql = "SELECT count(1) as orders ,FROM_UNIXTIME(o.dateline,'%Y%m%d') as days,FROM_UNIXTIME(o.dateline,'%H') as hours  FROM ".$this->table($this->_table)." o  INNER JOIN ".$this->table("staff_timeoutorder")." w  ON o.order_id = w.order_id WHERE {$where} GROUP BY `{$group_by}`  ";
        //K::M('system/logs')->log('SQL',$sql);
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[(int)$row[$group_by]] = $row;
            }
        }
        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['order'][] = $items[$k]['orders']? (float)$items[$k]['orders']:0;
        }
        return $arr;
    }

    //联表查询 jh_order o, jh_staff s, jh_staff_timeoutorder t
    public function items_by_staff_timeoutorder($filter,$orderby=array(),$page=1,$limit=50,&$count=0)
    {
        $where  = $this->where($filter,'o.');
        $orderby = $this->order($orderby,null,'o.');
        $limit = $this->limit($page,$limit);

        $sql = "SELECT COUNT(DISTINCT o.`order_id`) FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." s ON s.staff_id=o.staff_id LEFT JOIN ".$this->table('staff_timeoutorder')." t ON t.order_id=o.order_id WHERE {$where}";

        $items = array();
        if($count = $this->db->GetOne($sql)){
            $sql = "SELECT o.*,s.name as staff_name,s.mobile as staff_mobile,t.time_id as time_id,t.jd_time as jd_time,t.complete_time as complete_time,t.timeout as timeout_time,t.dateline as timeout_dateline  FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." s ON s.staff_id=o.staff_id LEFT JOIN ".$this->table('staff_timeoutorder')." t ON t.order_id=o.order_id WHERE {$where} {$orderby} {$limit}";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    if($row[$this->_pk]){
                        $items[$row[$this->_pk]] = $row;
                    }else{
                        $items[] = $row;
                    }
                }
            }
        }
        return $items;
    }

    public function count_join_timeout($filter){
        $count = 0;
        $where = $this->where($filter,'o.');
        $sql = "SELECT count(1) as orders ,FROM_UNIXTIME(w.dateline,'%Y%m%d') as day,FROM_UNIXTIME(w.dateline,'%H') as hour  FROM ".$this->table($this->_table)." o  INNER JOIN ".$this->table("staff_timeoutorder")." w  ON o.order_id = w.order_id WHERE {$where}  ";
        if($rs = $this->db->Execute($sql)){
            if($row = $rs->fetch()){
                $count = $row['orders'];
            }
        }
        return $count;
    }

    public function order_join_refund($filter,$order_by,$page,$limit = 50,$count = 0){
        $where  = $this->where($filter,'o.');
        $orderby = $this->order($order_by,null,'o.');
        $limit = $this->limit($page,$limit);
        $sql = "SELECT COUNT(1) FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai_order_refund')." w  ON o.order_id = w.order_id WHERE {$where} ";
        $items = array();
        if($count = $this->db->GetOne($sql)){
            $res_sql = "SELECT o.* FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai_order_refund')." w On o.order_id = w.order_id WHERE {$where} {$orderby} {$limit}";
            if($rs = $this->db->Execute($res_sql)){
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    if($row[$this->_pk]){
                        $items[$row[$this->_pk]] = $row;
                    }else{
                        $items[] = $row;
                    }
                }
            }
        }
        return $items;
    }

    //联表查询 jh_order o, jh_member w
    public function items_join_member($filter,$order_by = array(),$page=1,$limit = 50,&$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where}";
        if($count = $this->db->GetOne($count_sql)){
            $sql =  "SELECT  o.*, w.`mobile` as 'member_mobile', w.`nickname` as 'member_nickname' FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

    //联表查询 jh_order o, jh_member w, jh_shop ext
    public function items_join_member_shop($filter,$order_by = array(),$page=1,$limit = 50,&$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid LEFT JOIN ".$this->table('shop')." ext ON o.shop_id=ext.shop_id WHERE {$where}";
        if($count = $this->db->GetOne($count_sql)){
            $sql =  "SELECT  o.*, w.`mobile` as 'member_mobile', w.`nickname` as 'member_nickname', ext.`title` as 'shop_title', ext.`mobile` as 'shop_mobile' FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid LEFT JOIN ".$this->table('shop')." ext ON o.shop_id=ext.shop_id WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $pk = $row[$this->_pk];
                    $items[$pk] = $this->_format_row($row);
                    $items[$pk]['member_nickname'] = rawurldecode($row['member_nickname']);
                }
            }
        }
        return $items;
    }

    public function get_youhui($order, $huodong_title)
    {
        //订单优惠
        $youhui = array();
        $huodong_title = $huodong_title ? $huodong_title : '限时折扣';
        $wordColor = K::M('waimai/waimai')->getHdWordColor();
        if($order['first_youhui']>0){
            $youhui[0] = array('title'=>'首单立减','word'=>$wordColor['first']['word'],'color'=>$wordColor['first']['color'],'amount'=>$order['first_youhui']);
        }
        if($order['order_youhui']>0){
            $youhui[1] = array('title'=>'满减活动','word'=>$wordColor['manjian']['word'],'color'=>$wordColor['first']['color'],'amount'=>$order['order_youhui']);
        }
        if($order['discount_youhui']>0){
            $youhui[1] = array('title'=>$huodong_title,'word'=>$wordColor['discount']['word'],'color'=>$wordColor['discount']['color'],'amount'=>$order['discount_youhui']);
        }
        if($order['coupon']>0){
            $youhui[2] = array('title'=>'商家优惠券','word'=>$wordColor['coupon']['word'],'color'=>$wordColor['coupon']['color'],'amount'=>$order['coupon']);
        }
        if($order['hongbao']>0){
            $youhui[3] = array('title'=>'红包抵扣','word'=>$wordColor['hongbao']['word'],'color'=>$wordColor['hongbao']['color'],'amount'=>$order['hongbao']);
        }

        //4.0配送会员卡优惠，换购优惠
        if($order['huangou_youhui']>0){
            $youhui[4] = array('title'=>'超值换购','word'=>$wordColor['huangou']['word'],'color'=>$wordColor['huangou']['color'],'amount'=>$order['huangou_youhui']);
        }
        if($order['peicard_youhui'] > 0){
            $youhui[5] = array('title'=>'配送会员卡','word'=>$wordColor['peicard']['word'],'color'=>$wordColor['peicard']['color'],'amount'=>$order['peicard_youhui']);
        }

        if($order['card_amount'] > 0){
            $youhui[6] = array('title'=>'购买会员卡','word'=>$wordColor['card']['word'],'color'=>$wordColor['card']['color'],'amount'=>$order['card_amount']);
        }
        return $youhui;
    }

    public function hideContact($row)
    {
        if(in_array($row['order_status'], array(-2, -1, 8)) && (__TIME - $row['lasttime']) > 172800){
            if($row['mobile']){
                $row['mobile'] = substr($row['mobile'], 0, 3).'****'.substr($row['mobile'], -4);
            }
            if($row['contact']){
                $row['contact'] = mb_substr($row['contact'], 0, 1, 'utf-8').str_repeat('*', mb_strlen($row['contact'], 'utf-8') - 1);
            }
            if($row['o_mobile']){
                $row['o_mobile'] = substr($row['o_mobile'], 0, 3).'****'.substr($row['o_mobile'], -4);
            }
            if($row['o_contact']){
                $row['o_contact'] = mb_substr($row['o_contact'], 0, 1, 'utf-8').str_repeat('*', mb_strlen($row['o_contact'], 'utf-8') - 1);
            }
        }
        return $row;
    }

    public function set_member_orders($order_id, $uid, $shop_id){
        if(!$order_id || !$uid || !$shop_id){
            return false;
        }else{
            $filter = array();
            $filter['from'] = 'waimai';
            $filter['uid'] = $uid;
            $filter['shop_id'] = $shop_id;
            //$filter['order_id'] = '<:'.$order_id;
            $filter[':OR'] = array('pay_status' => 1, 'online_pay' => 0); // 已付款 || 货到付款(含自提)
            $count = $this->othercount($filter);
            $count = max(1, $count);
            return $this->update($order_id, array('member_orders'=>$count));
        }
    }

    //2019-02-22 新增 预计配送时间的缓存接口
    public function cacheFreightTime($order_id,$timeVal)
    {
        $order_id = (int)$order_id;
        $timeVal = (int)$timeVal;
        if($order_id <= 0 || $timeVal <= 0)
            return false;
        return K::M('cache/cache')->set("OrderFreightTimeCache".$order_id,$timeVal);
    }

    public function getCacheFreightTime($order_id)
    {
        $order_id = (int)$order_id;
        if($order_id <= 0)
            return 0;
        return (int)K::M('cache/cache')->get("OrderFreightTimeCache".$order_id);
    }

    public function delFreightTimeCache($order_id)
    {
        $order_id = (int)$order_id;
        if($order_id > 0)
            K::M('cache/cache')->delete("OrderFreightTimeCache".$order_id);
    }
    //========================================

    //2019-03-06 添加 部分退款功能===========================
    //退款,一次只能退一个订单！
    //参数
    //  $order_id   订单ID
    //  $amount     订单金额
    //  $remark     退款备注
    //  $from       退款操作来源
    //  $type       退款路径类型，0原路退回，退常不使用此参数
    public function refund_ex($order_id,$amount,$remark="",$from="admin",$type=0)
    {
        $order_id = (int)$order_id;
        if($order_id <= 0)
            return false;
        $order = $this->detail($order_id);
        if(!$order || (int)$order['order_status']<0 || (int)$order['order_status']===8)
            return false;
        $amount = ((int)((float)$amount*100))/100;
        if($amount < 0.01 || $amount>$this->get_real_refund_amount($order_id))
            return false;
        $remark = trim($remark);
        if($remark === "")
            return false;
        $from = trim($from);
        if(!in_array($from, ['admin','shop','system']))
            return false;
        $type = (int)$type;

        //2019-03-16 添加 事务支持
        $hasErr = false;
        $this->db->begin();
        //======================

        try
        {
            $refundAmount = $balanceAmount = 0;
            if($type === 0)
                $refundAmount = $amount;
            else
                $balanceAmount = $amount;
            $refundPath = $refundPathLog = "";
            if($refundAmount > 0 && in_array($order['pay_code'], ['wxpay', 'alipay']))
            {
                //先尝试原路退回
                $refundTrade = K::M('trade/payment')->refund_part($order, $refundAmount, $remark);
                if(!$refundTrade)
                {
                    $balanceAmount = $refundAmount;
                    $refundPathLog = "原路退回失败，退回余额";
                }
                else
                    $refundPath = "原路退回";
            }
            else
                $balanceAmount = $amount;

            if($balanceAmount > 0)
            {
                //退回余额
                $refundPath = "退回余额";
                if(!K::M('member/member')->update_money($order['uid'], $balanceAmount, '订单(ID:'.$order['order_id'].')部分退款到余额'))
                    throw new \Exception("Error", 1);
            }

            $partRefundData = [
                'order_id' => $order_id,
                'create_time' => time(),
                'amount' => $amount*100,
                'remark' => $remark,
                'type' => $type,
                'from' => $from
            ];
            if(!K::M('order/refund')->create($partRefundData,true))
                throw new \Exception("Error", 1);
            self::$_amountCache[$order_id]['refund'] += $amount;
            if(self::$_amountCache[$order_id]['refund'] > self::$_amountCache[$order_id]['origin'])
                self::$_amountCache[$order_id]['refund'] = self::$_amountCache[$order_id]['origin'];
            $optUserName = "";
            if($from === "shop")
                $optUserName = "商家";
            else if($from === "admin")
                $optUserName = "管理员";
            else
                $optUserName = "系统";
            $log = $optUserName.'执行订单(订单号:'.date('YmdHis',(int)$order['dateline']).$order['order_id'].')部分退款';
            if(!K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id'])))
                throw new \Exception("Error", 1);
            if($refundPathLog !== "")
            {
                if(!K::M('order/log')->create(array('status'=>8, 'from'=>$from, 'log'=>$refundPathLog, 'order_id'=>$order['order_id'])))
                    throw new \Exception("Error", 1);
            }
            if(!K::M('waimai/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id'], 'type'=>8)))
                throw new \Exception("Error", 1);
            $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
            $this->send_member('订单部分退款', sprintf("您在[%s]下的订单(%s)，{$optUserName}已经执行部分退款，款项已经{$refundPath}", $waimai?$waimai['title']:"-", date('YmdHis',(int)$order['dateline']).$order['order_id']), $order);
        }catch(\Exception $e){ $hasErr = true; }

        if($hasErr)
        {
            $this->db->rollback();
            return false;
        }
        $this->db->commit();
        return true;
    }

    //订单金额数据缓存
    static private $_amountCache = [];
    //查询订单真实可退款金额，值是“订单原用户付款金额” 减去 “已退款的总金额”
    public function get_real_refund_amount($order_id)
    {
        $order_id = (int)$order_id;
        $this->_cacheRefundAmount($order_id);
        return self::$_amountCache[$order_id]['origin'] - self::$_amountCache[$order_id]['refund'];
    }

    //查询已退总金额
    public function get_refunded_amount($order_id)
    {
        $order_id = (int)$order_id;
        $this->_cacheRefundAmount($order_id);
        return self::$_amountCache[$order_id]['refund'];
    }

    //查询已退总金额
    public function get_part_refunded_amount($order_id)
    {
        $order_id = (int)$order_id;
        $this->_cacheRefundAmount($order_id);
        return self::$_amountCache[$order_id]['part_refund'];
    }

    private function _cacheRefundAmount($order_id)
    {
        $order_id = (int)$order_id;
        if(isset(self::$_amountCache[$order_id]))
            return;
        self::$_amountCache[$order_id] = [
            'origin' => 0,
            'refund' => 0,
            'part_refund' => 0
        ];
        $order = $this->detail($order_id);
        if($order)
        {
            self::$_amountCache[$order_id]['origin'] = ((int)$order['pay_status']!==0)?((float)$order['amount']+(float)$order['money']):(float)$order['money'];
            $list = K::M('order/refund')->items(['order_id'=>$order_id],NULL,1,99999999);
            if($list)
            {
                foreach($list as &$item)
                {
                    self::$_amountCache[$order_id]['part_refund'] += (int)$item['amount'];
                    unset($item);
                }
                self::$_amountCache[$order_id]['part_refund'] /= 100;
            }
            if((int)$order['refund_status']===2 || (int)$order['refund_status']===3)
                self::$_amountCache[$order_id]['refund'] = self::$_amountCache[$order_id]['origin'];
            else
                self::$_amountCache[$order_id]['refund'] = self::$_amountCache[$order_id]['part_refund'];
        }
    }
    //=========================================
}