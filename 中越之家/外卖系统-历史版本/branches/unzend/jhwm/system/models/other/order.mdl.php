<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Other_Order extends Mdl_Table
{
    protected $_table = 'other_order';
    protected $_pk    = 'order_id';
    protected $_cols  = 'order_id,shop_id,type,price,product,lng,lat,addr,contact,mobile,ext_shop_id,ext_order_id,extend,dateline,p_order_id';

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
        return parent::_check($data, $order_id);
    }

    protected function _format_row($row)
    {
        $row['product'] = $row['product']?unserialize($row['product']):array();
        $row['extend'] = $row['extend']?unserialize($row['extend']):array();


        return $row;
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
            $row = $this->_format_row($row);            

        }        
        return $row;
    }

    //连表查询  jh_order  jh_other_order
    public function items_join_by($filter=null, $orderby=array(), $page=1, $limit=50, &$count=0)
    {
        $where  = K::M('order/order')->where($filter,'o.');
        $orderby = K::M('order/order')->order($orderby,null,'o.');
        $limit = K::M('order/order')->limit($page,$limit);
        $sql = "SELECT COUNT(1) FROM ".$this->table('order')." o INNER JOIN ".$this->table($this->_table)." w ON o.order_id = w.order_id WHERE {$where}";       
        $items = array();
        if($count = (int) $this->db->GetOne($sql)){
            $sql = "SELECT o.*,w.* FROM ".$this->table('order')." o INNER JOIN ".$this->table($this->_table)." w ON o.order_id = w.order_id WHERE {$where} {$orderby} $limit";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
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

    //取消订单
    public function cancel($order_id=0, $shop_id=0, $from='shop'){
        if(!$order_id){
            $this->msgbox->add('未指定订单',201);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if(!in_array($order['order_status'],array(0,1,2,3,4))){
            $this->msgbox->add('当前订单不可取消',204);
        }else if(!(K::M('waimai/accesstoken')->get_access_token($shop_id)) && $other_order['type'] != 'own'){
            $this->msgbox->add('获取授权失败',205);
        }else if($order['shop_id']!=$shop_id){
            $this->msgbox->add('该订单不属于您的商家',206);
        }else{
            $access_token = K::M('waimai/accesstoken')->get_access_token($shop_id);
            if($other_order['type']=='ele'){
                if(K::M('ele/ele')->cancel_order_lite($other_order['ext_order_id'],$access_token['access_token'])){
                    K::M('ele/order')->recievecancel($other_order['ext_order_id']);
                    $this->msgbox->add('取消订单成功');
                }else{
                    $this->msgbox->add('取消订单失败',207);
                }
            }else if($other_order['type']=='meituan'){
                if(K::M('meituan/meituan')->cancel_order_lite($other_order['ext_order_id'],$access_token['meituan_token'])){
                    K::M('meituan/order')->recievecancel($other_order['ext_order_id']);
                    $this->msgbox->add('取消订单成功');
                }else{
                    $this->msgbox->add('取消订单失败',207);
                }
            }else{
                if(K::M('order/order')->update($order_id,array('order_status'=>-1))){                    
                    if($p_order = K::M('order/order')->detail($other_order['p_order_id'])){
                        K::M('order/order')->update($p_order['order_id'],array('order_status'=>-1));
                        K::M('waimai/waimai')->update_money($shop_id,$p_order['pei_amount'],'取消三方单(自发单)，配送费退回配送费余额￥'.$p_order['pei_amount']);
                    }
                    K::M('order/log')->create(array('from'=>$from, 'log'=>'三方单(自发单),取消订单', 'order_id'=>$order['order_id']));
                    K::M('order/order')->update($order_id,array('order_status'=>-1));
                    $this->msgbox->add('取消订单成功');
                }else{
                    $this->msgbox->add('取消订单失败',207);
                }
                
                //自己送暂时不需要
            }
        }
    }

    //接单
    public function jiedan($order_id=0, $shop_id=0, $from='shop'){
        if(!$order_id){
          $this->msgbox->add('未指定订单',201);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
          $this->msgbox->add('订单不存在',202);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if($order['order_status']!=0){
            $this->msgbox->add('当前订单不可接单',204);
        }else if(!$access_token = K::M('waimai/accesstoken')->get_access_token($shop_id)){
           $this->msgbox->add('获取授权失败',205);
        }else if($order['shop_id']!=$shop_id){
           $this->msgbox->add('该订单不属于您的商家',206);
        }else {
           if($other_order['type']=='ele'){
               if(K::M('ele/ele')->confirm_order_lite($other_order['ext_order_id'],$access_token['access_token'])){
                   K::M('ele/order')->recievejiedan($other_order['ext_order_id']);
                  $this->msgbox->add('接单成功');
               }else{
                  $this->msgbox->add('接单失败',207);
               }
           }else if($other_order['type']=='meituan'){
               if(K::M('meituan/meituan')->confirm_order_lite($other_order['ext_order_id'],$access_token['meituan_token'])){
                   K::M('meituan/order')->recievejiedan($other_order['ext_order_id']);
                   $this->msgbox->add('接单成功');
               }else{
                   $this->msgbox->add('接单失败',207);
               }
           }else{
               //自己送暂时不需要
               $this->msgbox->add('接单失败',207);
           }
        }
    }

    //发起配送
    public function setpei($order_id=0, $price=0, $shop_id=0, $from='shop'){
        if(!$order_id){
            $this->msgbox->add('未指定需要发单的订单',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['online_pay']==0){
            $this->msgbox->add('货到付款订单不支持罚单',210);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('外卖商家不存在',204);
        }else if(!$group = K::M('pei/group')->detail($waimai['group_id'])){
            $this->msgbox->add('商家绑定的配送站不存在或者已关闭',205);
        }else if($order['order_status']!=2){
            $this->msgbox->add('该订单不可发起配送',224);
        }else if($other_order['p_order_id']){
            $this->msgbox->add('该订单已发单',207);
        }else if($waimai['deliver']<(float)($price+$group['min_pei'])){
            $this->msgbox->add('配送费余额不足',208);
        }else{
            if(K::M('waimai/waimai')->update_money($shop_id,-($price+$group['min_pei']),'订单('.$order_id.')发单,扣除配送费余额￥'.($price+$group['min_pei']))){
                $order_data['amount'] = ($price+$group['min_pei']);
                $order_data['city_id'] = $group['city_id'];
                $order_data['staff_id'] = 0;
                $order_data['uid'] = 0;
                $order_data['shop_id'] = $order['shop_id'];
                $order_data['from'] = 'paotui';
                $order_data['order_status'] = 0;
                $order_data['online_pay'] = 1;
                $order_data['pay_status'] = 0;
                $order_data['total_price'] = ($price+$group['min_pei']);
                $order_data['hongbao_id'] = 0;
                $order_data['hongbao'] = 0;
                $order_data['o_lng'] = $waimai['lng'];
                $order_data['o_lat'] = $waimai['lat'];
                $order_data['lng'] = $other_order['lng'];
                $order_data['lat'] = $other_order['lat'];
                $order_data['contact'] = $other_order['contact'];
                $order_data['mobile'] = $other_order['mobile'];
                $order_data['addr'] = $other_order['addr'];
                $order_data['house'] = "";
                $order_data['day'] = date('Ymd');
                $order_data['clientip'] = __IP;
                $order_data['pei_type'] = 1;
                $order_data['intro'] = $order['intro']?$order['intro']:"";
                $order_data['order_from'] = defined('IN_WEIXIN') ? 'weixin' : 'wap';
                $order_data['pei_time'] = $order['pei_time'];
                $order_data['pei_amount'] = ($price+$group['min_pei']);
                $order_data['dateline'] = __TIME;
                $order_data['group_id'] = $waimai['group_id'];
                if($p_order_id = K::M('order/order')->create($order_data)){
                    $paotui_order = array();
                    $paotui_order['order_id'] = $p_order_id;
                    $paotui_order['from'] = 'song';
                    $paotui_order['o_lng'] = $order_data['o_lng'] ;
                    $paotui_order['o_lat'] = $order_data['o_lat'] ;
                    $paotui_order['o_addr'] =$waimai['addr'];
                    $paotui_order['lng'] = $order_data['lng'];
                    $paotui_order['lat'] = $order_data['lat'];
                    $paotui_order['addr'] = $other_order['addr'];
                    $paotui_order['amount'] = $group['min_pei'];
                    $paotui_order['tip'] = $price;
                    $product = array();
                    foreach($other_order['product'] as $k=>$v){
                        $product[] = $v['product_name'];
                    }
                    $paotui_order['product'] = serialize($product);
                    $paotui_order['dateline'] = __TIME;
                    $paotui_order['contact'] = $other_order['contact'];
                    $paotui_order['mobile'] = $other_order['mobile'];
                    $paotui_order['o_contact'] = $waimai['contact'];
                    $paotui_order['o_mobile'] = $waimai['phone'];
                    $paotui_order['price'] = $price+$group['min_pei'];
                    $paotui_order['weight'] = 1;
                    $paotui_order['type'] = 0;
                    if (K::M('paotui/order')->create($paotui_order)) {                        
                        switch ($from) {
                            case 'shop':
                                $log = '三方单,商家申请配送';
                                break;
                            case 'admin':
                                $log = '三方单,平台申请配送';
                                break;
                            default:
                                $log = '';
                                break;
                        }
                        K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order_id));
                        K::M('order/log')->create(array('order_id'=>$p_order_id,'from'=>'member','log'=>'订单已提交','status'=>1));
                        K::M('paotui/order')->set_payed(array('order_id'=>$p_order_id,'amount'=>$price+$group['min_pei']),array('code'=>'money'));
                        K::M('other/order')->update($order_id,array('p_order_id'=>$p_order_id));
                        K::M('order/order')->update($order_id,array('pei_type'=>1));
                        K::M('order/time')->update($p_order_id,array('shop_jiedan_time'=>__TIME));
                        $this->msgbox->add('申请配送成功');
                    } else {
                        K::M('order/order')->delete($p_order_id,true);
                        K::M('other/order')->delete($p_order_id,true);
                        $this->msgbox->add('申请配送失败', 223)->response();
                    }
                    K::M('paotui/order')->set_order_day_num($order_id);
                }
            }else{
                $this->msgbox->add('发单失败',209);
            }
        }

    }

    //取消配送
    public function cancelpei($order_id=0, $shop_id=0, $from='shop'){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if($other_order['p_order_id']==0){
           $this->msgbox->add('该订单还未发起配送，不可取消配送',204);
        }else if(!$p_order = K::M('order/order')->detail($other_order['p_order_id'])){
            $this->msgbox->add('发单的订单不存在',204);
        }else if($p_order['staff_id']>0){
            $this->msgbox->add('配送员已接单，不可取消配送',205);
        }else if($order['order_status']!=2){
            $this->msgbox->add('当前订单不可取消配送');
        }else if($order['shop_id']!=$shop_id){
            $this->msgbox->add('该订单不属于您的店铺',206);
        }else{
            if(K::M('order/order')->update($order_id,array('pei_type'=>0))&&K::M('other/order')->update($order_id,array('p_order_id'=>0))){
                if(K::M('waimai/waimai')->update_money($shop_id,$p_order['pei_amount'],'取消配送，配送费退回配送费余额￥'.$p_order['pei_amount'])){
                    K::M('order/order')->update($other_order['p_order_id'],array('order_status'=>-1));
                    switch ($from) {
                        case 'shop':
                            $log = '三方单,商家取消配送';
                            break;
                        case 'admin':
                            $log = '三方单,平台取消配送';
                            break;
                        default:
                            $log = '';
                            break;
                    }
                    K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order_id));
                    $this->msgbox->add('取消配送成功');
                }else{
                    $this->msgbox->add('取消配送失败',208);
                }
            }else{
                $this->msgbox->add('取消配送失败',207);
            }
        }
    }

    //增加小费
    public function addtip($order_id=0, $shop_id=0, $tip=0, $from='shop')
    {
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$shop_id){
            $this->msgbox->add('未指定商家',202);
        }else if((float)$tip<=0){
            $this->msgbox->add('小费设置错误',203);
        }else if(!$order =K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',204);
        }else if(!$other_order =K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',205);
        }else if(!$p_order = K::M('order/order')->detail($other_order['p_order_id'])){
            $this->msgbox->add('订单还未发起配送',206);
        }else if(!$paotui_order = K::M('paotui/order')->detail($other_order['p_order_id'])){
            $this->msgbox->add('订单还未发起配送',207);
        }else if($paotui_order['staff_id'] > 0){
            $this->msgbox->add('骑手已经接单，无需追加小费',208);
        }else if(!$waimai = K::M('waimai/waimai')->detail($shop_id)){
            $this->msgbox->add('商家不存在',209);
        }else if($waimai['deliver']<$tip){
            $this->msgbox->add('商户配送费余额不足',210);
        }else{
            if(K::M('waimai/waimai')->update_money($shop_id,-($tip),'订单('.$order_id.')增加小费,扣除配送费余额￥'.($tip))){
                if(K::M('order/order')->update($other_order['p_order_id'],array('amount'=>$p_order['amount']+$tip,'pei_amount'=>$p_order['pei_amount']+$tip,'total_price'=>$p_order['total_price']+$tip))&&K::M('paotui/order')->update($other_order['p_order_id'],array('tip'=>$paotui_order['tip']+$tip))){
                    switch ($from) {
                        case 'shop':
                            $log = '三方单,商家追加小费￥'.$tip;
                            break;
                        case 'admin':
                            $log = '三方单,平台追加小费￥'.$tip;
                            break;
                        default:
                            $log = '';
                            break;
                    }
                    K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order_id));
                    $this->msgbox->add('追加小费成功');
                }else{
                    $this->msgbox->add('追加小费失败',211);
                }
            }else{
                $this->msgbox->add('追加小费失败',209);
            }
        }
    }

    //同意退款
    public function agree_order_lite($order_id,$shop_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if($order['shop_id']!=$shop_id){
            $this->msgbox->add('该订单不属于您的店铺',204);
        }else if($order['refund_status']!=1){
            $this->msgbox->add('该订单不能同意退款',205);
        }else if($order['order_status']==-1){
            $this->msgbox->add('订单已取消',206);
        }else if($order['order_status']==8){
            $this->msgbox->add('订单已完成',207);
        }else if(!(K::M('waimai/accesstoken')->get_access_token($shop_id))&&$other_order['type']!='own'){
            $this->msgbox->add('获取授权失败',205);
        }else{
            $access_token =   K::M('waimai/accesstoken')->get_access_token($shop_id);
            if($other_order['type']=='ele'){
                if(K::M('ele/ele')->agree_refund_lite($other_order['ext_order_id'],$access_token['access_token'])){
                    K::M('ele/order')->recieveagree($other_order['ext_order_id']);
                    $this->msgbox->add('同意退款成功');
                }else{
                    $this->msgbox->add('同意退款失败',207);
                }
            }else if($other_order['type']=='meituan'){
                if(K::M('meituan/meituan')->agree_refund_lite($other_order['ext_order_id'],$access_token['meituan_token'])){
                    K::M('meituan/order')->recieveagree($other_order['ext_order_id']);
                    $this->msgbox->add('同意退款成功');
                }else{
                    $this->msgbox->add('同意退款失败',207);
                }
            }else{
                //自己送暂时不需要
            }
        }
    }

    //拒绝退款
    public function disagree_refund_lite($order_id,$shop_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',203);
        }else if($order['shop_id']!=$shop_id){
            $this->msgbox->add('该订单不属于您的店铺',204);
        }else if($order['refund_status']!=1){
            $this->msgbox->add('该订单不能同意退款',205);
        }else if($order['order_status']==-1){
            $this->msgbox->add('订单已取消',206);
        }else if($order['order_status']==8){
            $this->msgbox->add('订单已完成',207);
        }else if(!(K::M('waimai/accesstoken')->get_access_token($shop_id))&&$other_order['type']!='own'){
            $this->msgbox->add('获取授权失败',205);
        }else{
            $access_token =   K::M('waimai/accesstoken')->get_access_token($shop_id);
            if($other_order['type']=='ele'){
                if(K::M('ele/ele')->disagree_refund_lite($other_order['ext_order_id'],$access_token['access_token'],'订单已配送！')){
                    K::M('ele/order')->recieverefuse($other_order['ext_order_id']);
                    $this->msgbox->add('拒绝退款成功');
                }else{
                    $this->msgbox->add('拒绝退款失败',207);
                }
            }else if($other_order['type']=='meituan'){
                if(K::M('meituan/meituan')->disagree_refund_lite($other_order['ext_order_id'],$access_token['meituan_token'])){
                    K::M('meituan/order')->recieverefuse($other_order['ext_order_id']);
                    $this->msgbox->add('拒绝退款成功');
                }else{
                    $this->msgbox->add('拒绝退款失败',207);
                }
            }else{
                //自己送暂时不需要
            }
        }
    }

    public  function confirm($order_id,$order = null,$from='member'){
        if(!$order_id){
            return  false;
        }else if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            return false;
        }else if($order['order_status']==-1||$order['order_status']==8){
            return false;
        }else{
            if($other_order['p_order_id']){
              if($p_order = K::M('order/order')->detail($other_order['p_order_id'])){
                  if($p_order['order_status']!=8&&$p_order['order_status']!=-1){
                      K::M('paotui/order')->confirm($other_order['p_order_id'],null,'member');
                  }
              }
            }
            $log = "";
            if($other_order['type']=='ele'){
                $log = "三方单(饿了么),用户确认完成";
            }else if($other_order['type']=='meituan'){
                $log = "三方单(美团),用户确认完成";
            }else if($other_order['type']=='own'){
                $log = "三方单(自发单),用户确认完成";
            }
            K::M('order/log')->create(array('from'=>'member', 'log'=>$log, 'order_id'=>$order['order_id']));
            K::M('order/time')->update($order_id,array('order_compltet_time'=>__TIME));
            return K::M('order/order')->update($order_id,array('order_status'=>8));
        }
    }

    public function setconfirm($order_id=0, $shop_id=0, $from='shop')
    {
        if(!$order_id){
            $this->msgbox->add('未指定需要处理的订单',201);
        }else if(!$order = K::M('other/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['from'] != 'other'){
            $this->msgbox->add('订单不是三方订单',203);
        }else if($order['shop_id'] != $shop_id){
            $this->msgbox->add('该订单不属于您的商铺',204);
        }else if($order['order_status'] != 2 || $order['pei_type'] != 0 || $order['staff_id'] || $order['p_order_id']){
            $this->msgbox->add('订单当前状态不支持此操作',205);
        }else if($order['type'] == 'ele' && $order['dateline']+15*60 > __TIME){
            $this->msgbox->add('饿了么订单请在15分钟后进行此操作',206);
        }else if(!($access_token = K::M('waimai/accesstoken')->get_access_token($order['shop_id'])) && $order['type'] != 'own'){
            $this->msgbox->add('获取授权失败',207);
        }else{
            $log = "";
            $updata = array();
            switch ($from) {
                case 'shop':
                    $from_label = '商家';
                    break;
                case 'admin':
                    $from_label = '平台';
                    break;
                default:
                    $from_label = '';
                    break;
            }
            if($order['type'] == 'ele'){
                $log = "三方单(饿了么),".$from_label."确认送达";
                $updata['order_status'] = 4;
            }else if($order['type'] == 'meituan'){
                $log = "三方单(美团),".$from_label."确认送达";
                $updata['order_status'] = 4;
            }else if($order['type'] == 'own'){
                $log = "三方单(自发单),".$from_label."确认送达";
                $updata['order_status'] = 8;
            }

            if(K::M('order/order')->update($order_id, $updata)){
                if($order['type'] == 'ele'){
                    K::M('ele/ele')->received_order($order['ext_order_id'],$access_token['access_token']);
                }else if($order['type'] == 'meituan'){
                    K::M('meituan/meituan')->received_order($order['ext_order_id'],$access_token['meituan_token']);
                }else if($order['type'] == 'own'){
                    K::M('order/time')->update($order_id,array('order_compltet_time'=>__TIME));
                }
                K::M('order/log')->create(array('from'=>$from, 'log'=>$log, 'order_id'=>$order['order_id']));
                $this->msgbox->add('订单确认送达成功');
            }else{
                $this->msgbox->add('订单确认送达失败',210);
            }
        }
    }

    public function received($order_id=0)
    {
        if(!$order_id = (int)$order_id){
            return false;
        }else if(!$order = K::M('order/order')->detail($order_id)){
            return false;
        }else if(!$other_order = K::M('other/order')->detail($order_id)){
            return false;
        }else if(in_array($order['order_status'], array(-1, 8))){
            return false;
        }else if(!($access_token = K::M('waimai/accesstoken')->get_access_token($other_order['shop_id'])) && $other_order['type'] != 'own'){
            return false;
        }else{
            if($other_order['type']=='ele'){
                if(K::M('ele/ele')->received_order($other_order['ext_order_id'],$access_token['access_token'])){
                    K::M('ele/order')->recievecomplete($other_order['ext_order_id']);
                    return true;
                }else{
                    return false;
                }
            }else if($other_order['type']=='meituan'){
                if(K::M('meituan/meituan')->received_order($other_order['ext_order_id'],$access_token['meituan_token'])){
                    //确认送达
                    K::M('meituan/order')->recievecomplete($other_order['ext_order_id']);
                    return true;
                }else{
                    return false;
                }
            }else{
                if($this->confirm($order_id)){
                    return true;
                }else{
                    return false;
                }
                //自己送暂时不需要
            }
        }
    }

    public function set_order_day_num($order_id,$shop_id){
        if(!$order_id){
            return false;
        }else{
            $filter = array();
            $filter['dateline'] = strtotime(date('Y-m-d')).'~'.(strtotime(date('Y-m-d'))+86399);
            $filter['type'] = 'own';
            $filter['shop_id'] = $shop_id;
            $filter['order_id'] = '<:'.$order_id;
            $count = K::M('other/order')->count($filter);
            return K::M('order/order')->update($order_id,array('day_num'=>$count+1));
        }
    }

    public function format_data($row)
    {
        if($row['pei_time']>0){
            $row['pei_time_label'] = "预计".date('Y-m-d H:i',$row['pei_time'])."送达";
        }else{
            $row['pei_time_label'] = "尽快送达";
        }

        $tyeps = $this->getType();
        $row['type_label'] = $tyeps[$row['type']] ? $tyeps[$row['type']] : '';

        $msg = '';
        $show_btn = array(
            'cancel'     => 0,   //取消订单
            'jiedan'     => 0,   //接单
            'setpei'     => 0,   //申请配送
            'cancelpei'  => 0,   //取消配送
            'setconfirm' => 0,   //确认送达（商家自己送）
            'addtip'     => 0,   //追加小费
            'agree'      => 0,   //同意退款
            'refuse'     => 0    //拒绝退款
            );

        if($row['from'] == 'other'){            
            if($row['order_status'] == -1){
                $msg = '已取消';
            }else if($row['order_status'] == 0){
                $msg = '待接单';
                $show_btn['cancel']=1;
                $show_btn['jiedan'] = 1;
               // $show_btn = array('cancel'=>1, 'jiedan'=>1);
            }else if($row['order_status'] == 2){
                $show_btn['cancel'] = 1;
                if($row['staff_id'] && $row['p_order_id'] && $row['pei_type'] == 1){
                    $msg = '骑手已接单';                    
                }else if(!$row['staff_id'] && $row['p_order_id'] && $row['pei_type'] == 1){
                    $msg = '等待骑手接单';
                    $show_btn['cancelpei'] = 1;
                    $show_btn['addtip'] = 1;                    
                }else{
                    if($row['online_pay']==1){
                        $msg = '待发单';
                        $show_btn['setpei'] = 1;
                        $show_btn['setconfirm'] = 1;
                    }else{
                        $msg = '待配送';
                        $show_btn['setconfirm'] = 1;
                    }

                } 

                if($row['refund_status'] == 1){
                    $msg = '用户申请退款';
                    $show_btn['agree'] = 1;
                    $show_btn['refuse'] = 1;
                    $show_btn['cancel'] = 1;
                }else if($row['refund_status'] == -1){
                    $msg .= '(商户拒绝退款)';
                    $show_btn = array('cancel'=>1);
                }                              
            }else if($row['order_status'] == 3){
                $msg = '骑手送货中';
                $show_btn = array('cancel'=>1);
                if($row['refund_status'] == 1){
                    $msg = '用户申请退款';
                    $show_btn['agree'] = 1;
                    $show_btn['refuse'] = 1;
                }else if($row['refund_status'] == -1){
                    $msg .= '(商户拒绝退款)';

                }
            }else if($row['order_status'] == 4){
                $show_btn = array('cancel'=>1);
                if($row['pei_type'] == 1){
                    $msg = '骑手已送达';
                }else{
                    $msg = '商户已送达';
                }
                if($row['refund_status'] == 1){
                    $msg = '用户申请退款';
                    $show_btn['agree'] = 1;
                    $show_btn['refuse'] = 1;
                }else if($row['refund_status'] == -1){
                    $msg .= '(商户拒绝退款)';
                    $show_btn = array('cancel'=>1);
                }
            }else if($row['order_status'] == 8){
                $msg = '已完成';
            }            
        }
        $row['msg'] = $msg;
        $row['show_btn'] =$show_btn;
        $row['order_status_label'] = $msg;
        return $row;
    }

    public function getType()
    {
        return array(
            'ele'=>'三方单(饿了么)',
            'meituan'=>'三方单(美团)',
            'own'=>'三方单(自发)'
            );
    }

    //连表查询  jh_order  jh_other_order  jh_waimai
    public function items_join_by_shop($filter=null, $orderby=array(), $page=1, $limit=50, &$count=0)
    {
        $where  = K::M('order/order')->where($filter,'o.');
        $orderby = K::M('order/order')->order($orderby,null,'o.');
        $limit = K::M('order/order')->limit($page,$limit);
        $sql = "SELECT COUNT(1) FROM ".$this->table('order')." o INNER JOIN ".$this->table($this->_table)." w ON o.order_id = w.order_id LEFT JOIN ".$this->table('waimai')." ext on o.shop_id = ext.shop_id WHERE {$where}";       
        $items = array();
        if($count = (int) $this->db->GetOne($sql)){
            $sql = "SELECT o.*, w.*, ext.`title` as 'shop_title', ext.`phone` as 'shop_phone' FROM ".$this->table('order')." o INNER JOIN ".$this->table($this->_table)." w ON o.order_id = w.order_id LEFT JOIN ".$this->table('waimai')." ext on o.shop_id = ext.shop_id WHERE {$where} {$orderby} $limit";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
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

}