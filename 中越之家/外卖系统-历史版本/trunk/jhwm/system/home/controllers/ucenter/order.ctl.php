<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Ucenter_Order extends Ctl_Ucenter {

    public function index(){
        $this->tmpl = 'ucenter/order/index.html';
    }


    public function del($order_id){
        $this->check_login();
        if(!$order_id){
            $this->msgbox->add('订单ID错误!',211);
        }elseif(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('不存在的订单!',212);
        }elseif(!$up = K::M('order/order')->delete($order_id)){
            $this->msgbox->add('删除失败!',213);
        }else{
            $this->msgbox->add('删除成功!');
        }
    }

    
    public function items_all($type=0){
        $this->pagedata['order_type'] = $type;
        $this->tmpl = 'ucenter/order/items_all.html';
    }


    public function loaditems($page=1)
    {
        $type = $this->GP('order_type');//防止翻页错误,展示所有订单
        $this->check_login();

        $filter = $pager = $items = array();
        $pager['limit'] = $limit = 10;
//        $pager['page'] = $page = max((int)$params['page'], 1);
        $pager['page'] = $page = max((int)$page, 1);
        $filter['uid'] = $this->uid;
        $filter['closed'] = 0;
        $filter['from'] = "<>:weidian";
        //加入个人中心条件筛选
        if($type == 1){ //待付款
            //$filter['order_status'] = '>:0';
            $filter['order_status'] = 0;
            $filter['pay_status'] = 0;
            $filter['online_pay'] = 1;
            $filter['from'] = "<>:maidan";
            $orderby = array('lasttime'=>'DESC');
        }else if($type == 2){ //待评价
            $filter['order_status'] = 8;
            $filter['comment_status'] = 0;
            $orderby = array('lasttime'=>'DESC');
        }else if($type == 3){ //已退款
            $filter['order_status'] = '<:0';
            $orderby = array('lasttime'=>'DESC');
        }else{
            $orderby = array('order_id'=>'DESC');
        }

        if(!$items = $this->_order_items($filter, $orderby, $page, $limit, $count)){
            $items = array();
        }
        $count_num = $items['count'];
        //print_r($count_num);die;
        if($count_num <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        
        $this->tmpl = 'ucenter/order/loaditems.html';
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }

    public function items_maidan()
    {
        $this->tmpl = 'ucenter/order/maidan.html';
    }
    
    
    public function loadmaidan($page=1)
    {
        $type = 0;
        $page = max((int)$page, 1);
        $this->check_login();
        $filter = $pager = array();
        $pager['limit'] = $limit = 10;
        $filter['uid'] = $this->uid;
        $filter['closed'] = 0;
        //加入个人中心条件筛选
        if($type == 1){ //待付款
            $filter['order_status'] = 0;
            $filter['pay_status'] = 0;
        }elseif($type == 2){//待评价
            $filter['order_status'] = 8;
            $filter['comment_status'] = 0;
        }elseif($type == 3){//已退款
            $filter['order_status'] = -1;
        }
        $filter['from'] = 'maidan';
        
        if(!$items = K::M('order/order')->items($filter,array('order_id'=>'DESC'),$page,$limit,$count)){
            $items = array();
        }
        $order_ids = $shop_ids = array();
        foreach($items as $k => $v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
            $order_ids[$v['order_id']] = $v['order_id'];
        }
        
        $orders = K::M('maidan/order')->items_by_ids($order_ids);
        $this->pagedata['orders'] = $orders;
        $this->pagedata['shops'] = K::M('shop/shop')->items_by_ids($shop_ids);
        $count_num = K::M('order/order')->count($filter);
        if($count_num <= $limit){
            $loadst = 0; 
        }else{
            $loadst = 1; 
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['pager'] = $pager;
        $this->pagedata['items'] = $items;
        $this->tmpl = "ucenter/order/loadmaidan.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }



    public function items($from,$type)
    {
        $this->pagedata['from'] = $from;
        if(!$type){
            $type = 1;
        }
        $this->pagedata['type'] = $type;
        $this->tmpl = "ucenter/order/{$from}.html";
    }

    public function loaddata($page=1)
    {
        if(!$type = (int)$this->GP('type')){
            $type = 1;
        }
        $this->check_login();
        $from = $this->GP('from');
        if(!in_array($from, array('tuan','waimai','paotui','maidan','weixiu','house','other','mall'))){
            $this->msgbox->add('订单来源不正确!',211);
        }else{
            $filter = $pager = array();
            $pager['limit'] = $limit = 10;
            $filter = array('uid'=>$this->uid, 'closed'=>0, 'from'=>$from);
            $orderby = array('order_id'=>'DESC');
            if(2 == $type){
                $filter['order_status'] = array(-1, -2, 8);
            }else{
                $filter['order_status'] = array(0, 1, 3, 4, 5);

            }
            if(!$items = $this->_order_items($filter, $orderby, $page, $limit, $count)){
                $items = array();
            }
            //print_r($items);die;
            $count_num = $items['count'];
            if($count_num <= $limit){
                $loadst = 0; 
            }else{
                $loadst = 1; 
            }

            $this->msgbox->set_data('loadst', $loadst);
            $this->pagedata['pager'] = $pager;
            $this->pagedata['items'] = $items;
            $this->tmpl = "ucenter/order/loaditems.html";
            $html = $this->output(true);
            $this->msgbox->set_data('html', $html);
            $this->msgbox->json();
        }
    }

    

    public function detail($order_id)
    {
        $this->check_login();
        $lng = $this->request['UxLocation']['lng'];
        $lat = $this->request['UxLocation']['lat'];
        if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('错误的订单',211);
        }else if($order['uid'] != $this->MEMBER['uid']){
            $this->msgbox->add('非法操作',211);
        }else{
            if($photo = K::M('order/photo')->items(array('order_id'=>$order['order_id']))){
                $order['photo'] = array_values($photo);
            }
            if($voice = K::M('order/voice')->items(array('order_id'=>$order['order_id']))){
                $order['voice'] = array_values($voice);
            }
            if($order['staff_id']>0){
                $order['staff'] = K::M('staff/staff')->detail($order['staff_id']);
            }
            $order['shop'] = K::M('shop/shop')->detail($order['shop_id']);
            switch ($order['from']){
                case 'tuan':
                $order['detail'] = K::M('tuan/order')->detail($order['order_id']);
                if($t = K::M('tuan/tuan')->detail($order['detail']['tuan_id'])){
                    $order['photo'] = $t['photo'];
                }
                if($quan = K::M('tuan/ticket')->find(array('order_id'=>$order_id))) {
                    $order['quan'] = $quan;
                }

                if($lat && $lng){
                    $order['juli'] = intval(K::M('helper/round')->juli($order['shop']['lng'],$order['shop']['lat'], $order['lng'],$order['lat']));
                    $order['juli_label'] = K::M('helper/format')->juli($order['juli']);
                }
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/tuan_detail.html';
              break;
              case 'waimai':
                $order['detail'] = K::M('waimai/order')->detail($order['order_id']);
                $p = K::M('waimai/orderproduct')->items(array('order_id'=>$order['order_id']));
                if($p){
                    $order['products'] = array_values($p);
                }
                $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                $order['waimai_title'] = $waimai['title'];
                $order['waimai_logo'] = $waimai['logo'];
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/waimai_detail.html';
              break;
              case 'house':
                $order['detail'] = K::M('house/order')->detail($order['order_id']);
                $order['waimai'] = K::M('waimai/waimai')->detail($order['shop_id']);
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/house_detail.html';
              break;
              case 'weixiu':
                $order['detail'] = K::M('weixiu/order')->detail($order['order_id']);
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/weixiu_detail.html';
              break;
              case 'paotui':
                $order['detail'] = K::M('paotui/order')->detail($order['order_id']);
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/paotui_detail.html';
              break;
              case 'maidan':
                $order['detail'] = K::M('maidan/order')->find(array('order_id'=>$order['order_id']));
                $order['juli'] = K::M('helper/round')->juli($order['shop']['lng'], $order['shop']['lat'], $lng, $lat);
                $order['juli_label'] = K::M('helper/format')->juli($order['juli']);
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/maidan_detail.html';
              break;
                case 'mall':
                $order['detail'] = K::M('mall/order')->find(array('order_id'=>$order['order_id']));
                $mall_products = K::M('mall/order/product')->items(array('order_id'=>$order_id));  
                $product_ids = array();
                foreach($mall_products as $k=>$v){
                    $product_ids[$v['product_id']] = $v['product_id'];
                }
                $products = K::M('mall/product')->items_by_ids($product_ids);
                foreach($mall_products as $k=>$v){
                    foreach($products as $k1=>$v1){
                        if($v['product_id'] == $v1['product_id']){
                            $mall_products[$k]['products'] = $v1; 
                        }
                    }
                }
                $order['mall_products'] = $mall_products;
                $this->pagedata['order'] = $order;
                $this->tmpl = 'ucenter/order/mall_detail.html';
              break;
              case 'other':
              $this->msgbox->add('订单支付成功');
              $link = '//'.$_SERVER['HTTP_HOST'];
              header("location:{$link}");
            }
        }
    }

    public function log($order_id){
        $this->check_login();
        if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('错误的订单!',211);
        }else if($order['uid'] != $this->MEMBER['uid']){
            $this->msgbox->add('错误!',211);
        }else{
            $order['log'] = K::M('order/log')->items(array('order_id'=>$order['order_id']),array('log_id'=>'asc'));
            $this->pagedata['order'] = $order;
            $this->tmpl = 'ucenter/order/log.html';
        }
    }



    /*评价*/
    public function comment($order_id){

        $this->check_login();
        if(!$order_id){
            $this->msgbox->add('订单不存在!',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',212);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作!',213);
        }else{
            $peitime = K::M('shop/comment')->peitime();
            $this->pagedata['peitime'] = $peitime;
            $jifen = K::M('system/config')->get('jifen');
            $this->pagedata['jifen'] = intval($order['amount']*$jifen['jifen_ratio']);
            $this->pagedata['order'] = $order;

            if($order['from'] == 'waimai'){
                $this->tmpl = 'ucenter/order/comment_waimai.html';
            }else if($order['from'] == 'tuan'){
                $tuan = K::M('tuan/tuan')->detail($order['shop_id']);
                $this->pagedata['tuan'] = $tuan;

                $this->tmpl = 'ucenter/order/comment_shop.html';
            }else if($order['from'] == 'maidan'){
                $this->tmpl = 'ucenter/order/comment_shop.html';
            }else{
                $this->tmpl = 'ucenter/order/comment_staff.html';
            }
        }
    }

    public function comment_handle()  //外卖商家和普通商家评论通用
    {
        $this->check_login();
        if($this->checksubmit()){

            $datas = $this->GP('data');
            $file = $_FILES;

            $datas['uid'] = $this->uid;

            if(!$this->uid){
                $this->msgbox->add('您还没有登录!',101);
            }else if(!$datas['order_id']){
                $this->msgbox->add('错误的订单!',216);
            }else if(!$order = K::M('order/order')->detail($datas['order_id'])){
                $this->msgbox->add('错误的订单!',216);
            }else if($order['comment_status'] == 1){
                $this->msgbox->add('你已经评价过了!',216);
            }else if($order['from'] == 'waimai' &&(!$datas['score_fuwu'] || $datas['score_fuwu'] < 1 || $datas['score_fuwu'] > 5)){
                $this->msgbox->add('请正确选择服务评分!'.$datas['score_fuwu'],211);
            }else if($order['from'] == 'waimai' &&(!$datas['score_kouwei'] || $datas['score_kouwei'] < 1 || $datas['score_kouwei'] > 5)){
                $this->msgbox->add('请正确选择口味评分!',213);
            }else if(!$datas['score'] || $datas['score'] < 1 || $datas['score'] > 5){
                $this->msgbox->add('请正确选择总评分!',214);
            }else if($order['from'] == 'waimai' && $order['pei_type'] != 3 && empty($datas['pei_time'])){
                $this->msgbox->add('没有选择配送速度!',212);
            }else if(!$datas['content']){
                $this->msgbox->add('没有填写评价内容!',215);
            }else{
                $pei_times = 0;
                $datas['shop_id'] = $order['shop_id'];
                if($file){
                    $datas['have_photo'] = 1;
                }

                if($order['from'] == 'waimai'){
                    $km = 'waimai/comment';
                    $hp = 'waimai/commentphoto';
                }else{
                    $km = 'shop/comment';
                    $hp = 'shop/commentphoto';
                }
                $datas['clientip'] = __IP;
                $datas['dateline'] = __TIME;
                if($create = K::M($km)->create($datas)){

                    if($file){
                        //插入评价
                        foreach($file as $k => $v){
                            if($a = K::M('magic/upload')->upload($v,'photo')){
                                $photo_data = array(
                                    'comment_id' => $create,
                                    'photo' => $a['photo']
                                );
                                $create_photo = K::M($hp) -> create($photo_data);
                            }
                        }
                    }
                    $shop = K::M('shop/shop') -> detail($order['shop_id']);

                    K::M('shop/shop')->update($order['shop_id'], array('comments'=>$shop['comments']+1));
                    K::M('shop/shop')->update_count($order['shop_id'], 'score', $datas['score']);
                    K::M('order/order')->update($datas['order_id'],array('comment_status'=>1));
                    $jifen = $this->system->config->get('jifen');
                    $jifen_total = (int)($order['amount']*$jifen['jifen_ratio']);
                    K::M('member/member')->update_jifen($this->uid,$jifen_total,'订单'.$datas['order_id'].'评价完成，获得积分');
                    K::M('shop/msg')->create(array('shop_id'=>$order['shop_id'],'title'=>'订单已评价','content'=>'用户('.$order['contact'].')已评价订单(ID:'.$order['order_id'].')','is_read'=>0,'type'=>2,'order_id'=>$order['order_id']));
                    // 计算商家平均等待时间
                    $order_items = K::M('waimai/comment')->items(array('shop_id' => $order['shop_id']), array(), 0, 9999999, $count);
                    foreach($order_items as $key => $val){
                        $pei_times += $val['pei_time'];
                    }
                    $pei_times = intval($pei_times / $count);

                    if($datas['score']>3){
                        $update_data = array('comments'=>'`comments`+1','praise_num'=>'`praise_num`+1','score'=>'`score`+'.$datas['score'],'score_fuwu'=>'`score_fuwu`+'.$datas['score_fuwu'],'score_kouwei'=>'`score_kouwei`+'.$datas['score_kouwei'],'pei_time'=>$pei_times);
                    }else{
                       $update_data = array('comments'=>'`comments`+1','score'=>'`score`+'.$datas['score'],'score_fuwu'=>'`score_fuwu`+'.$datas['score_fuwu'],'score_kouwei'=>'`score_kouwei`+'.$datas['score_kouwei'],'pei_time'=>$pei_times);
                    }
                    if($order['from'] == 'waimai') {
                        K::M('waimai/waimai')->update($order['shop_id'],$update_data,true);
                    }
                    $this->msgbox->add('评价成功!');
                    $this->msgbox->set_data('forward',$this->mklink('ucenter/order/detail', array('args'=>$order['order_id'])));
                }else{
                    $this->msgbox->add('评价失败!',217);
                }
            }

        }

    }



    public function staff_comment_handle()  //服务人员评价
    {
        $this->check_login();
        if($this->checksubmit()){

            $datas = $this->GP('data');
            $file = $_FILES;

            $datas['uid'] = $this->uid;

            if(!$this->uid){
                $this->msgbox->add('您还没有登录!',101);
            }else if(!$datas['order_id']){
                $this->msgbox->add('错误的订单!',216);
            }else if(!$order = K::M('order/order')->detail($datas['order_id'])){
                $this->msgbox->add('错误的订单!',216);
            }else if($order['comment_status'] == 1){
                $this->msgbox->add('你已经评价过了!',216);
            }else if($order['staff_id'] == 0){
                $this->msgbox->add('错误的服务人员!',216);
            }else if(!$datas['score'] || $datas['score'] < 1 || $datas['score'] > 5){
                $this->msgbox->add('请正确选择总评分!',214);
            }else if(!$datas['content']){
                $this->msgbox->add('没有填写评价内容!',215);
            }else{

                $datas['staff_id'] = $order['staff_id'];
                if($datas['file']){
                    $datas['have_photo'] = 1;
                }


                if($create = K::M('staff/comment')->create($datas)){
                    if($file){
                        //插入评价
                        foreach($file as $k => $v){
                            if($a = K::M('magic/upload')->upload($v,'photo')){
                                $photo_data = array(
                                    'comment_id' => $create,
                                    'photo' => $a['photo']
                                );
                                $create_photo = K::M('staff/commentphoto') -> create($photo_data);
                            }
                        }
                    }
                    K::M('order/order')->update($datas['order_id'],array('comment_status'=>1));

                    $this->msgbox->add('评价成功!');
                }else{
                    $this->msgbox->add('评价失败!',217);
                }
            }

        }

    }




    public function complaint($order_id) //订单投诉
    {
        $this->check_login();
        $order_id = (int)$order_id;
        if(!$order_id){
            $this->msgbox->add('订单不能为空!',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',212);
        }else if($order['order_status'] < 1){
            $this->msgbox->add('该订单暂时不可投诉!',213);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作!',214);
        }else{
            $this->pagedata['order_id'] = $order_id;
            $this->tmpl = 'ucenter/order/complaint.html';
        }
    }

    public function complaint_handle($order_id){

        $this->check_login();
        $order_id = (int)$order_id;
        if(!$order_id){
            $this->msgbox->add('订单不能为空!',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',222);
        }else if($order['order_status'] < 1){
            $this->msgbox->add('该订单暂时不可投诉!',212);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作!',213);
        }else if(!$title = $this->GP('title')){
            $this->msgbox->add('没有选择投诉类型!',214);
        }else if(!$content = $this->GP('content')){
            $this->msgbox->add('没有填写投诉内容!',215);
        }else if($check = K::M('order/complaint')->find(array('uid'=>$this->uid,'order_id'=>$order_id))){
            $this->msgbox->add('该订单已经投诉过了!',216);
        }else{

            $data = array(
                'order_id'=>$order_id,
                'uid'=>$this->uid,
                'shop_id'=>$order['shop_id'],
                'staff_id'=>$order['staff_id'],
                'title'=>$title,
                'content'=>$content
            );
            if(!$add = K::M('order/complaint')->create($data)){
                $this->msgbox->add('投诉失败!',217);
            }else{
                K::M('shop/msg')->create(array('shop_id'=>$order['shop_id'],'title'=>'订单被投诉','content'=>sprintf('订单(%s)被用户投诉',$order['order_id']),'is_read'=>0,'type'=>3,'order_id'=>$order_id));
                $this->msgbox->add('投诉成功!');
            }
        }
    }




    // 申请退单
    public function chargeback($order_id)
    {
        $this->check_login();
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('错误的订单ID',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add("不存在的订单",212);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('你没有权限操作',213);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已经取消，无需重复取消',214);
        }else if($order['order_status'] != 0){
            $this->msgbox->add('当前订单是不可取消的状态',215);
        }else{
            
            if(K::M('order/order')->cancel($order_id, $order, 'member')) {
                $data = array(
                    'shop_id'=>$order['shop_id'],
                    'title'=>'订单已取消',
                    'content'=>'用户('.$order['contact'].')已取消订单(ID:'.$order_id.')',
                    'is_read'=>0,
                    'type'=>1,
                    'order_id'=>$order_id
                    );
                if($order['from'] == 'tuan' || $order['from'] == 'waimai'){
                    K::M('shop/msg')->create($data);
                }
                
                //还原库存和销量
                $op = K::M('mall/order/product')->items(array('order_id'=>$order_id));
                foreach($op as $k => $v){
                    K::M('mall/product')->update_count($v['product_id'],'sku',$v['product_number']);
                    K::M('mall/product')->update_count($v['product_id'],'sales',($v['product_number']*-1));
                }
                
                $this->msgbox->add('退单成功');
            }else {
                $this->msgbox->add('退单失败',216);
            }
        }
    }
    
    //商城订单收货
    public function mall_ok($order_id){
        $this->check_login();
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('错误的订单ID',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add("不存在的订单",212);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('你没有权限操作',213);
        }else if($order['order_status'] == 8){
            $this->msgbox->add('订单已完成过了',214);
        }else{
            $this->msgbox->add('收货成功！');
            K::M('order/order')->update($order_id,array('order_status'=>8));
            K::M('order/log')->create(array('order_id'=>$order['order_id'],'from'=>'member','log'=>'订单已收货完成','status'=>6));
        }
    }

    // 催单
    public function remind($order_id)
    {
        $this->check_login();
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('错误的订单ID',211);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add("订单不存在",212);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('你没有权限操作',213);
        }else if(!$worder = K::M('waimai/order')->detail($order_id)) {
            $this->msgbox->add("订单不存在",212);
        }else if((__TIME - $order['jd_time']) < 1800){
            $this->msgbox->add('请在30分钟后催单',216);
        }else {
            if(K::M('order/order')->update($order_id, array('cui_time'=>__TIME))) {
                $data = array(
                    'shop_id'=>$order['shop_id'],
                    'title'=>'用户正在催单',
                    'content'=>'用户('.$order['contact'].')正在催促订单(ID:'.$order_id.')',
                    'is_read'=>0,
                    'type'=>1,
                    'order_id'=>$order_id
                );
                K::M('shop/msg')->create($data);
                if($staff = $order['staff_id']) {
                    $data2 = array(
                        'staff_id'  => $staff,
                        'title'    => '用户正在催单',
                        'content'  => '用户('.$order['contact'].')正在催促订单(ID:'.$order_id.')',
                        'is_read'  => 0,
                    );
                    K::M('staff/msg')->create($data2);
                }
                $this->msgbox->add('催单成功');
            }else {
                $this->msgbox->add('催单失败',214);
            }
        }

    }

    /*通用订单支付页面*/
    public function pay($order_id)
    {
        K::M('system/session')->start();
        $this->check_login();
        $order_id = (int)$order_id;
        if(!$order_id){
            $this->msgbox->add('订单不存在!',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',212);
        }else if($order['pay_status']){
               $this->msgbox->add('该订单已支付!',213);
               if($order['from'] == 'weidian'){
                    if($detail = K::M('weidian/order')->detail($order_id)){
                        if($detail['type'] == 'default'){
                            $this->msgbox->set_data("forward", $this->mklink('weidian/product'));
                        }else{
                            $this->msgbox->set_data("forward", $this->mklink('weidian/ucenter/pintuan'));
                        }
                    }
                }
        }else{
            if($order['from'] == 'weidian'){
                $worder = K::M('weidian/order')->detail($order['order_id']);
                if($worder['type'] == 'pintuan'){
                    
                    $arr_p_order = K::M('weidian/pintuan/order')->find(array('order_id' => $order_id));
     
                        if(1 == $arr_p_order['is_money_pre']){
                            if(0 == $arr_p_order['money_paid']){
                                //1.预付款
                                $order['amount'] = $arr_p_order['money_need_pay'];
                            } 
                            else if($arr_p_order['money_need_pay'] == $arr_p_order['money_paid']){
                                //2.付尾款
                                $order['is_weikuan'] = 1;//拼图付尾款标记
                                $order['amount'] = abs($arr_p_order['product_price']*$arr_p_order['product_number'] - $arr_p_order['money_paid']);
                            }
                            else{
                                //3.付尾款 多次付款兼容
                                $order['is_weikuan'] = 1;//拼图付尾款标记
                                $order['amount'] = abs($arr_p_order['product_price']*$arr_p_order['product_number'] - $arr_p_order['money_paid']);
                            }
                        }
                        $arr_group = K::M('weidian/pintuan/group')->find(array('pintuan_group_id' => $arr_p_order['pintuan_group_id']));
                        $leftover_seconds = $arr_group['end_time'] - __TIME;
                        $order['link'] = $this->mklink('pintuan/tuan_detail', array($arr_p_order['pintuan_group_id']));
                    
                    $this->pagedata['pintuan'] = 1;
                }
            }
            if(defined('IN_WEIXIN')){
                $this->pagedata['weixin'] = 1;
            }
            $this->pagedata['order'] = $order;
            $payment_amount = $youhui_amount = $total_price = 0;
            $payment_amount = K::M("{$order['from']}/order")->get_payment_amount($order_id, $payment_level);
            $total_price = $payment_amount;
            if($payment_level){
                if($order['total_price'] > $payment_amount){
                    $total_price = $order['total_price'];
                    $youhui_amount = $total_price - $payment_amount;
                }
            }
            if($youhui_amount&&$payment_level==1){
                $str = "已支付";
            }else{
                $str = "优惠";
            }
            $pager = array('payment_amount'=>$payment_amount, 'youhui_amount'=>$youhui_amount, 'total_price'=>$total_price, 'payment_level'=>$payment_level,'payment_str'=>$str);
            $this->pagedata['pager'] = $pager;

            $this->pagedata['order'] = $order;
            $this->tmpl = 'ucenter/order/pay.html';
        }
    }

    protected function _order_items($filter, $orderby, $page=1, $limit=10, &$count=0)
    {
        $items = array();
        if($order_list = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $order_ids = $shop_ids = $staff_ids = $waimai_shop_ids = array();
            foreach($order_list as $k=>$v){
                if($v['shop_id']){
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
                $staff_ids[$v['staff_id']] = $v['staff_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
                if($v['from'] == 'tuan'){
                    $tuan_order_ids[$v['order_id']] = $v['order_id'];
                }else if($v['from'] == 'maidan'){
                    $maidan_order_ids[$v['order_id']] = $v['order_id'];
                }else if($v['from'] == 'waimai'){
                    $waimai_shop_ids[$v['shop_id']] = $v['shop_id'];
                    $waimai_order_ids[$v['order_id']] = $v['order_id'];
                }else if($v['from'] == 'paotui'){
                    $paotui_order_ids[$v['order_id']] = $v['order_id'];
                }else if($v['from'] == 'house'){
                    $house_order_ids[$v['order_id']] = $v['order_id'];
                }else if($v['from'] == 'weixiu'){
                    $weixiu_order_ids[$v['order_id']] = $v['order_id'];
                }elseif($v['from'] == 'mall'){
                    $mall_order_ids[$v['order_id']] = $v['order_id'];
                }
            }
            if($tuan_order_ids){
                if($tuan_order_list = K::M('tuan/order')->items_by_ids($tuan_order_ids)){
                    foreach($order_list as $k=>$v){
                        if($row = $tuan_order_list[$v['order_id']]){
                            $row['photo'] = $v['tuan_photo'];
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            if($maidan_order_ids){
                if($maidan_order_list = K::M('maidan/order')->items_by_ids($maidan_order_ids)){
                    foreach($order_list as $k=>$v){
                        if($row = $maidan_order_list[$v['order_id']]){
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            if($waimai_order_ids){
                if($waimai_order_list = K::M('waimai/order')->items_by_ids($waimai_order_ids)){
                    $waimai_list = K::M('waimai/waimai')->items_by_ids($waimai_shop_ids);
                    foreach($order_list as $k=>$v){
                        if($row = $waimai_order_list[$v['order_id']]){
                             if($a = $waimai_list[$v['shop_id']]){
                                $v['waimai_title'] = $a['title'];
                                $v['waimai_logo'] = $a['logo'];
                            }else{
                                $v['waimai_title'] = '';
                                $v['waimai_logo'] = 'default/shop.png';
                            }
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            if($paotui_order_ids){
                if($paotui_order_list = K::M('paotui/order')->items_by_ids($paotui_order_ids)){
                    foreach($order_list as $k=>$v){
                        if($row = $paotui_order_list[$v['order_id']]){
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            if($weixiu_order_ids){
                if($weixiu_order_list = K::M('weixiu/order')->items_by_ids($weixiu_order_ids)){
                    $weixiu_cate_list = K::M('weixiu/cate')->fetch_all();
                    foreach($order_list as $k=>$v){
                        if($row = $weixiu_order_list[$v['order_id']]){
                            $row['icon'] = $weixiu_cate_list[$row['cate_id']]['icon'];
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            if($house_order_ids){
                if($house_order_list = K::M('house/order')->items_by_ids($house_order_ids)){
                    $house_cate_list = K::M('house/cate')->fetch_all();
                    foreach($order_list as $k=>$v){
                        if($row = $house_order_list[$v['order_id']]){
                            $row['icon'] = $house_cate_list[$row['cate_id']]['icon'];
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            //积分商城订单
            if($mall_order_ids){
                $mall_order_products = K::M('mall/order/product')->items(array('order_id'=>$mall_order_ids));
                $product_ids = array();
                foreach($mall_order_products as $k=>$v){
                    $product_ids[$v['product_id']] = $v['product_id'];
                }
                $products = K::M('mall/product')->items_by_ids($product_ids);
                foreach($mall_order_products as $k=>$v){
                    foreach($products as $k1=>$v1){
                        if($v['product_id'] == $v1['product_id']){
                            $mall_order_products[$k]['product'] = $v1;
                        }
                    }
                }
                if($mall_order_list = K::M('mall/order')->items_by_ids($mall_order_ids)){
                    foreach($mall_order_list as $k=>$v){
                        foreach($mall_order_products as $k1=>$v1){
                            if($v['order_id'] == $v1['order_id']){
                                $mall_order_list[$k]['order_products'][] = $v1;
                            }
                        }
                    }
                    foreach($order_list as $k=>$v){
                        if($row = $mall_order_list[$v['order_id']]){
                            $v['order'] = $row;
                        }
                        $order_list[$k] = $v;
                    }
                }
            }
            //积分配置
            $jifen = $this->system->config->get('jifen');
            $shop_list = K::M('shop/shop')->items_by_ids($shop_ids);
            foreach($order_list as $k=>$v){
                $v['jifen'] = (int)($jifen['jifen_ratio']*$v['amount']);
                if($row = $shop_list[$v['shop_id']]){
                    $v['shop'] = $this->filter_fields('shop_id,contact,cate_id,mobile,phone,thumb,logo,title', $row);
                }else{
                    $v['shop'] = array('shop_id'=>$v['shop_id'], 'title'=>'');
                }
                $order_list[$k] = $v;
            }
        }
        $order_list['count'] = K::M('order/order')->count($filter);
        return $order_list;
    }
    
    
    public function common_handle_two(){
        $this->check_login();
        if($this->checksubmit()){
            $datas = $this->GP('data');
            $datas['uid'] = $this->uid;
            if(!$this->uid){
                $this->msgbox->add('您还没有登录!',101);
            }else if(!$datas['order_id']){
                $this->msgbox->add('错误的订单!',216);
            }else if(!$order = K::M('order/order')->detail($datas['order_id'])){
                $this->msgbox->add('错误的订单!',216);
            }else if($order['comment_status'] == 1){
                $this->msgbox->add('你已经评价过了!',216);
            }else if($order['from'] == 'waimai' &&(!$datas['score_fuwu'] || $datas['score_fuwu'] < 1 || $datas['score_fuwu'] > 5)){
                $this->msgbox->add('请正确选择服务评分!'.$datas['score_fuwu'],211);
            }else if($order['from'] == 'waimai' &&(!$datas['score_kouwei'] || $datas['score_kouwei'] < 1 || $datas['score_kouwei'] > 5)){
                $this->msgbox->add('请正确选择口味评分!',213);
            }else if(!$datas['score'] || $datas['score'] < 1 || $datas['score'] > 5){
                $this->msgbox->add('请正确选择总评分!',214);
            }else if($order['from'] == 'waimai' && $order['pei_type'] != 3 && empty($datas['pei_time'])){
                $this->msgbox->add('没有选择配送速度!',212);
            }/*else if(!$datas['content']){
                $this->msgbox->add('没有填写评价内容!',215);
            }*/else{
                $pei_times = 0;
                $datas['shop_id'] = $order['shop_id'];
                if($datas['file']){
                    $datas['have_photo'] = 1;
                }

                if($order['from'] == 'waimai'){
                    $km = 'waimai/comment';
                    $hp = 'waimai/commentphoto';
                }else{
                    $km = 'shop/comment';
                    $hp = 'shop/commentphoto';
                }
                $datas['clientip'] = __IP;
                $datas['dateline'] = __TIME;
                $file = $datas['file'];
                unset( $datas['file']);
                if($create = K::M($km)->create($datas)){

                    if($file){
                        //插入评价
                        foreach($file as $k => $v){
                            $photo_data = array(
                                'comment_id' => $create,
                                'photo' =>$v
                            );
                            $create_photo = K::M($hp) -> create($photo_data);

                        }
                    }
                    $shop = K::M('shop/shop') -> detail($order['shop_id']);
                    K::M('shop/shop')->update($order['shop_id'], array('comments'=>$shop['comments']+1));
                    K::M('shop/shop')->update_count($order['shop_id'], 'score', $datas['score']);
                    K::M('order/order')->update($datas['order_id'],array('comment_status'=>1));
                    $jifen = $this->system->config->get('jifen');
                    $jifen_total = (int)($order['amount']*$jifen['jifen_ratio']);
                    K::M('member/member')->update_jifen($this->uid,$jifen_total,'订单'.$datas['order_id'].'评价完成，获得积分');
                    K::M('shop/msg')->create(array('shop_id'=>$order['shop_id'],'title'=>'订单已评价','content'=>'用户('.$order['contact'].')已评价订单(ID:'.$order['order_id'].')','is_read'=>0,'type'=>2,'order_id'=>$order['order_id']));
                    // 计算商家平均等待时间
                    $order_items = K::M('waimai/comment')->items(array('shop_id' => $order['shop_id']), array(), 0, 9999999, $count);
                    foreach($order_items as $key => $val){
                        $pei_times += $val['pei_time'];
                    }
                    $pei_times = intval($pei_times / $count);

                    if($datas['score']>3){
                        $update_data = array('comments'=>'`comments`+1','praise_num'=>'`praise_num`+1','score'=>'`score`+'.$datas['score'],'score_fuwu'=>'`score_fuwu`+'.$datas['score_fuwu'],'score_kouwei'=>'`score_kouwei`+'.$datas['score_kouwei'],'pei_time'=>$pei_times);
                    }else{
                        $update_data = array('comments'=>'`comments`+1','score'=>'`score`+'.$datas['score'],'score_fuwu'=>'`score_fuwu`+'.$datas['score_fuwu'],'score_kouwei'=>'`score_kouwei`+'.$datas['score_kouwei'],'pei_time'=>$pei_times);
                    }
                    if($order['from'] == 'waimai') {
                        K::M('waimai/waimai')->update($order['shop_id'],$update_data,true);
                    }
                    $this->msgbox->add('评价成功!');
                    $this->msgbox->set_data('forward',$this->mklink('ucenter/order/detail', array('args'=>$order['order_id'])));
                }else{
                    $this->msgbox->add('评价失败!',217);
                }
            }

        }


    }

}
