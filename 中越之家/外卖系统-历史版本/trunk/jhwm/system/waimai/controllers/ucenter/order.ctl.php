<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/24
 * Time: 13:39
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Ucenter_Order extends Ctl_Ucenter { 
    //个人订单列表
    public function index(){
        $this->tmpl = 'order/index.html';
    }

    public function loadorder($page=1){
        $page=max((int)$page,1);
        $filter = array('uid'=>$this->uid, 'closed'=>0, 'from'=>'waimai');
        $config = K::M('system/config')->get('automatic');
        $pay_ltime = $config['unpay_cancel_time']?$config['unpay_cancel_time']:15;

        //1 全部  2 代付款  3待接单  4骑手接货 5 未评价 已评价       
        if($order= $this->_order_items($filter,array('order_id'=>'desc'),$page,10,$count)){
            $items = $order;
        }else{
            $items = array();
        }
        $count_num = $items['count'];
        if($count_num <= 10){
            $loadst = 0;
        }else{
            $loadst = 1;
        }
        $order_list  = array();
        foreach ($items as $v){
            //订单过期时间
            $v['flash_time'] = $v['dateline']+60*$pay_ltime;
            $v['dateline']   = date('Y-m-d H:i:s',$v['dateline']);
            $v['products'] =K::M('waimai/orderproduct')->items(array('order_id'=>$v['order_id']));
            $order_list[] =$v;
        }
        $this->msgbox->set_data('loadst', $loadst);
        $this->pagedata['items'] = $order_list;
        $this->tmpl = "order/loaditems.html";
        $html = $this->output(true);
        $this->msgbox->set_data('html', $html);
        $this->msgbox->json();
    }
	
	//商户订单未完成状态数量
	public function uid_order_total(){
		$this->check_login();
		$filter = array('uid'=>$this->uid);
		$items=K::M('order/order')->group_sum_by_uid($filter);
		$this->msgbox->set_data('data', $items);
	}
	

    //外卖订单详情
    public function detail($order_id)
    {
        if(!$order_format = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('错误的订单',211);
        }else if($order_format['uid'] != $this->MEMBER['uid']){
            $this->msgbox->add('非法操作',211);
        }else{
            $order = K::M('waimai/order')->format_data($order_format);
            if($order['staff_id']>0){
                $order['staff'] = K::M('staff/staff')->detail($order['staff_id']);
            }
            //商家信息
            $order['shop'] = K::M('shop/shop')->detail($order['shop_id']);
            $order['detail'] = K::M('waimai/order')->detail($order['order_id']);
            //订单商品
            $p = K::M('waimai/orderproduct')->items(array('order_id'=>$order['order_id']));
            $huodong_title = '';
            if($p){
                foreach($p as $kk=>$vv){
                    $p[$kk]['shuxin'] = '';
                    if($vv['specification']){
                        $p[$kk]['shuxin'].="[";
                        foreach($vv['specification'] as $vvv){
                            $p[$kk]['shuxin'].="+".$vvv['val'];
                        }
                        $p[$kk]['shuxin'].=']';
                    }
                    if(!$huodong_title){
                        $huodong_title = $vv['huodong_title'];
                    }
                }
                $order['products'] = array_values($p);
                //4.0订单分篮处理
                $order['products'] = K::M('waimai/orderproduct')->get_basketProducts($order['products']);
            }
        
            $order['discount_title'] = $huodong_title;
            //外卖详情
            $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
            $order['waimai_title'] = $waimai['title'];
            $order['waimai_logo'] = $waimai['logo'];
            $order['waimai_addr'] = $waimai['addr'];
            //$order['end_time']  = $order['dateline']+60*60;
            //默认不显示地图
            $order['show_map'] =0;
            //地图 距离
            $shop_lng = $order['shop']['lng'];
            $shop_lat = $order['shop']['lat'];
            $staff_lng =$order['staff']['lng'];
            $staff_lat =$order['staff']['lat'];

            if($order['staff_id']!=0&&$order['order_status']==2/*&&($order['refund_status']==0||$order['refund_status']==-1)*/){
                $order['show_map'] =1;
                $round = K::M('helper/round')->juli($shop_lng,$shop_lat,$staff_lng,$staff_lat);
                $distancce = K::M('helper/format')->juli($round);
                $this->pagedata['msg'] ='<span style="color:display:#000000;">配送员距离商家还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';
                
            } else if($order['staff_id']!=0&&$order['order_status']==3/*&&($order['refund_status']==0||$order['refund_status']==-1)*/){
                $order['show_map'] =1;
                $round = K::M('helper/round')->juli($order['lng'],$order['lat'],$staff_lng,$staff_lat);
                $distancce = K::M('helper/format')->juli($round);
                $this->pagedata['msg'] ='<span style="color:display:#000000;">配送员距离你还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';
            }
            $pay_log = K::M('payment/log')->items(array('order_id'=>$order['order_id'],'payed'=>1));
            switch ($pay_log['payment']){
                case 'alipay':
                    $order['payment_type'] = '支付宝';
                    break;
                case 'wxpay':
                    $order['payment_type'] = '微信';
                    break;
                case 'money':
                    $order['payment_type'] = '现金';
                    break;
                default:
                    $order['payment_type'] = '现金';
                    
            }
            if($order['staff_id']>0){
                $staff = K::M('staff/staff')->detail($order['staff_id']);
            }else{
                $staff = array();
            }
            $this->pagedata['staff'] = $staff;
            $config = K::M('system/config')->get('automatic');
            $pay_ltime = $config['unpay_cancel_time']?$config['unpay_cancel_time']:15;
            //倒计时
            $order['end_time'] = $order['dateline']+$pay_ltime*60;
            //获取订单投诉情况
            $filter_staff = $filter_shop = array();
            $filter_shop['order_id']=$filter_staff['order_id'] =$order['order_id'];
            $filter_staff['staff_id'] = '>:1';
            $filter_shop['shop_id'] = '>:1';
            $count_staff = K::M('waimai/complaint')->count($filter_staff);
            $count_shop = K::M('waimai/complaint')->count($filter_shop);
            $order['count_staff'] =$count_staff;
            $order['count_shop']  =$count_shop;
            
            /* -1:已取消，0：未处理，1：已接单（跑腿订单已接单），2:配货中  3：配送开始（跑腿服务中），4：配送完成（跑腿服务完成），8：订单完成*/
            /* exit;*/
            $cfg = K::$system->config->get('hongbao');
            $cfg['desc'] = str_replace("\r\n",'',$cfg['desc']);
            if((($order['online_pay']==1&&$order['pay_status']==1)||$order['online_pay']==0)&&in_array($order['order_status'],array(1,2,3,0))&&$order['pei_type']!=3){
                if($order['pei_type']==1){
                    $expect_msg = "预计送达时间".date('H:i',$order['expect_time'])."";
                }else{
                    $expect_msg = "商家自主配送。";
                }
              $expect_time = 1;
            }else{
                $expect_time = 0;
                $expect_msg = "";
            }
            $this->pagedata['expect_time'] = $expect_time;
            $this->pagedata['expect_msg'] = $expect_msg;

            //订单优惠
            $youhui = K::M('order/order')->get_youhui($order, $huodong_title);
            $order['youhui'] = array_values($youhui);
            $order['youhui_amount'] = $order['first_youhui']+$order['coupon']+$order['order_youhui']+$order['hongbao']+$order['discount_youhui']+$order['huangou_youhui']+$order['peicard_youhui'];

            $this->pagedata['cfg'] = $cfg;
            $this->pagedata['order'] = $order;

            //2019-02-22 新增 订单已支付且未完成，且有缓存的预计配送时间，则可以显示这个送达时间，送达时间是下单时间+预计配送时间
            $freightDoneTime = 0;
            $cacheFreightTime = K::M('order/order')->getCacheFreightTime($order['order_id']);
            if(in_array((int)$order['order_status'],[1,2,3,0]) && $cacheFreightTime>0)
            {
                $freightDoneTime = (int)$order['dateline'] + $cacheFreightTime;
                if(date("Y-m-d") === date("Y-m-d",$freightDoneTime) && time()<$freightDoneTime)
                    $freightDoneTime = date("H:i",$freightDoneTime);
                else
                    $freightDoneTime = date("Y-m-d H:i",$freightDoneTime); 
            }
            $this->pagedata['freight_done_time'] = $freightDoneTime;
            //===============================================

            //2019-03-13 添加 获取部分退款记录
            $refundInfoList = K::M('order/refund')->items(['order_id'=>$order_id],"create_time ASC",1,999999);
            if(!$refundInfoList)
                $refundInfoList = [];
            $this->pagedata['part_refund_list'] = $refundInfoList;
            //=================================

            //echo '<pre>';print_r($order);die;
            $this->tmpl = 'order/orderdetail.html';
        }
    }

    //订单投诉
    public function complaint($order_id,$target='staff'){
        if(!$order_id =(int)$order_id){
            $this->msgbox->add('订单不能为空!',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',212);
        }else if($order['order_status'] < 1){
            $this->msgbox->add('该订单暂时不可投诉!',213);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作!',214);
        }else if($target=='staff'&&K::M('waimai/complaint')->count(array('uid'=>$this->uid,'order_id'=>$order_id,'staff_id'=>$order['staff_id']))){
            $this->msgbox->add('该订单已经投诉过了配送员了!',330);
        } else if($target=='shop'&&$check = K::M('waimai/complaint')->find(array('uid'=>$this->uid,'order_id'=>$order_id,'shop_id'=>$order['shop_id']))){
            $this->msgbox->add('该订单已经投诉过了商家了!',331);
        }else{
            $tousu_arr = $this->noticearr();
            $page_array = array();

            if($target == 'staff'&&$order['staff_id']>0){
                $page_array['notice'] =$tousu_arr['staff'];
                $page_array['target'] = 'staff';
                
            }else {
                $page_array['notice'] =$tousu_arr['shop'];
                $page_array['target'] = 'shop';
            }
            $this->pagedata['list'] =$page_array;

            $this->pagedata['order_id'] = $order_id;
            $this->tmpl = 'order/complaint.html';
        }
        $this->msgbox->set_data('forward',$this->mklink('ucenter/order',array(),array(),'waimai'));
    }

    //保存订单投诉
    public function complaint_handle(){
        $data = $this->GP('data');
        $order_id = (int)$data['order_id'];
        if(!$title=$data['title']){
            $this->msgbox->add('投诉内容不能为空!',252);
        }else if(!$order_id){
            $this->msgbox->add('订单不能为空!',245);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在!',246);
        }else if($order['order_status'] < 1){
            $this->msgbox->add('该订单暂时不可投诉!',247);
        }else if($order['uid'] != $this->uid){
            $this->msgbox->add('非法操作!',248);
        }else if($data['target'] == ''){
            $this->msgbox->add('没有指定投诉对象!',249);
        } else if($data['target'] == 'staff'&&$order['staff_id']==0){
            $this->msgbox->add('快递员不存在！',308);
        } else if($data['target'] == 'staff'&&$count_shop=K::M('waimai/complaint')->count(array('order_id'=>$order['order_id'],'staff_id'=>$order['staff_id']))){
            $this->msgbox->add('该订单已经投诉过了配送员了!',310);
         }else if($data['target'] == 'shop'&&$count_shop =K::M('waimai/complaint')->count(array('order_id'=>$order['order_id'],'shop_id'=>$order['shop_id']))){
            $this->msgbox->add('该订单已经投诉过了商家了!',311);
        }else{
            if(isset($data['file'])){
                $photo = 1;
            }else{
                $photo =0;
            }
           $data_complaint = array(
                'order_id'=>$order_id,
                'uid'=>$this->uid,
                'content'=>$data['content'],
                'title'=>$title,
                'have_photo'=>$photo
            );
            if($data['target']=='shop'){
                $data_complaint['shop_id'] = $order['shop_id'];
            } else if($data['target']=='staff'){
                $data_complaint['staff_id'] = $order['staff_id'];
            }
            if(!$add = K::M('waimai/complaint')->create($data_complaint)){
                $this->msgbox->add('投诉失败!',312);
            }else{
                $upload = K::M('magic/upload');
                //上传图片数据
                if(isset($data['file'])){
                    foreach ($data['file'] as $v){
                        //检测是否被修改图片
                        if($upload->count(array('photo'=>$v))){
                            K::M('waimai/complaintphoto')->create(array('complaint_id'=>$add,'photo'=>$v));
                        }                       
                    }
                }
                if($data['target']=='shop'){
                    if(K::M('shop/msg')->create(array('shop_id'=>$order['shop_id'],'title'=>'订单被投诉','content'=>sprintf('订单(%s)被用户投诉',$order['order_id']).'投诉原因:'.$title.'。'.$data['content'],'is_read'=>0,'type'=>3,'order_id'=>$order_id))){
                        $this->msgbox->add('投诉商家成功!');
                    }else{
                        $this->msgbox->add('投诉商家失败!',253);
                    }
                }else{
                    $data2 = array(
                        'staff_id'  => $order['staff_id'],
                        'title'    => '用户投诉订单',
                        'content'  => '用户('.$order['contact'].')于'.date('Y-m-d H:i').'投诉订单(ID:'.$order_id.')'.'原因:'.$title.'。'.$data['content'],
                        'is_read'  => 0,
                    );
                    K::M('staff/msg')->create($data2);

                    $this->msgbox->add('投诉配送员成功!');
                }
            }
        }
    }

    //订单评价
    public function comment($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',240);
        }else if(!$order_detail = K::M('waimai/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',241);
        }else if($order_detail['uid'] != $this->MEMBER['uid']){
            $this->msgbox->add('非法提交数据',242);
        }else if($order_detail['order_status']!=8){
            $this->msgbox->add('订单未完成不能评价',243);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',267);
        }else if($order['comment_status']==1){
            $this->msgbox->add('订单已经评价',268);
        }else{
            $order_detail['products'] = K::M('waimai/orderproduct')->items(array('order_id'=>$order_detail['order_id']));
            $waimai_shop = K::M('waimai/waimai')->detail($order_detail['shop_id']);

            //$order_detail['jifen']    =  $this->system->config->get('jifen');
            $order_detail['jifen_total'] = 0;
            $jifen = $order_detail['jifen_cfg'];
            if($order_detail['online_pay'] == 1 && $order_detail['jifen_status'] == 0 && $jifen && $jifen['jifen_module']){
                $order_detail['jifen_total'] = (int)(($order_detail['amount']+$order_detail['money']) * $jifen['jifen_ratio']);
            }

            $order_detail['shop_logo'] = $waimai_shop['logo'];
            $order_detail['shop_title'] = $waimai_shop['title'];
            //$minute = (strtotime(date('H:i',$order_detail['lasttime'])) - strtotime(date('H:i',$order_detail['dateline'])))/60;
            $minute = ceil(($order_detail['lasttime'] - $order_detail['dateline'])/60);
            $order_detail['minute'] = $minute;
            $this->pagedata['order'] = $order_detail;
            
            $dateline = $order_detail['dateline'];
            $i=1;
            $time = array();
            while($i<13){
                $times= $i*10;
                $time[] =$times.'分钟送达'.'('.date('H:i',$dateline+10*60).')';
                $dateline=$dateline+10*60;
                $i++;
            }
            //格式化配送时间
            $this->pagedata['time_format'] = json_encode($time);
            $this->tmpl = 'order/comment.html';
        }
        $this->msgbox->set_data('forward',$this->mklink('waimai/ucenter/order:detail'),array($order_id));
    }

    //外卖订单评论保存//外卖商家和普通商家评论通用
    public function comment_handle()
    {
        if($this->checksubmit()) {
            $datas = $this->checksubmit('data');
            $datas['uid'] = $this->uid;
            if (!$this->uid) {
                $this->msgbox->add('您还没有登录!', 254)->response();
            }else if (!(int)$datas['order_id']) {
                $this->msgbox->add('错误的订单!', 255)->response();
            } else if (!$order = K::M('order/order')->detail($datas['order_id'])) {
                $this->msgbox->add('错误的订单!', 256)->response();
            } else if ($order['comment_status'] == 1) {
                $this->msgbox->add('你已经评价过了!', 257)->response();
            } else if ($order['pei_type'] != 3&&((!$datas['score_peisong'] = (int)$datas['score_peisong']) || $datas['score_peisong'] < 1 || $datas['score_peisong'] > 5)) {
                $this->msgbox->add('请正确选择配送评分!', 258)->response();
            } else if ((!$datas['score'] = (int)$datas['score']) || $datas['score'] < 1 || $datas['score'] > 5) {
                $this->msgbox->add('请正确选择总评分!', 259)->response();
            } else if ($order['pei_type'] != 3 && empty($datas['pei_time'])) {
                $this->msgbox->add('没有选择配送速度!', 260)->response();
            } else if ($order['uid']!=$this->uid) { //订单评价可以不填内容
                $this->msgbox->add('该订单不属于您!', 261)->response();
            }else if(!$waimai_format = K::M('waimai/waimai')->detail($order['shop_id'])){
                $this->msgbox->add('评价的外卖店铺不存在');
            }else if($datas['pei_time']<0&&$order['pei_type']!=3){
                $this->msgbox->add('非法的时间提交',262);
            }else{
                $datas['shop_id'] = $order['shop_id'];
                if (isset($datas['file'])) {
                    $datas['have_photo'] = 1;
                }
                //更新商品评价
                $product = K::M('waimai/orderproduct')->items(array('order_id' => $datas['order_id']));
                $extend = array(
                    'intro'=>$order['intro']
                );
                foreach ($product as $v) {
                    if ($datas[$v['product_id']] == 1) {
                        K::M('waimai/product')->update_count($v['product_id'], 'good', 1);
                        $arr = array(
                           'product_id'=>$v['product_id'],
                            'product_name'=>$v['product_name'],
                            'pingjia'=>1,
                        );
                        $extend[]= $arr;
                    }else if($datas[$v['product_id']] == 2) {
                        K::M('waimai/product')->update_count($v['product_id'], 'bad', 1);
                        $arr = array(
                            'product_id'=>$v['product_id'],
                            'product_name'=>$v['product_name'],
                            'pingjia'=>2
                        );
                        $extend[]= $arr;
                    }else{
                        $arr = array(
                            'product_id'=>$v['product_id'],
                            'product_name'=>$v['product_name'],
                            'pingjia'=>0
                        );
                        $extend[]= $arr;
                    }
                }
                $datas['extend'] = serialize($extend);
                $km = 'waimai/comment';
                $hp = 'waimai/commentphoto';
                $datas['clientip'] = __IP;
                $datas['dateline'] = __TIME;
                $comment_data = array();
                $comment_data['order_id'] = $datas['order_id'];
                $comment_data['score'] = $datas['score'];
                $comment_data['shop_id'] = $datas['shop_id'];
                $comment_data['uid'] = $datas['uid'];
                if($order['pei_type'] == 3){
                    $comment_data['score_peisong'] = $waimai_format['score_peisong']/$waimai_format['comments'];
                }else{
                    $comment_data['score_peisong'] = $datas['score_peisong'];
                }
                $score_avg = round(($datas['score'] +  $comment_data['score_peisong'])/2, 2);// 求平均数
                $datas['score_avg'] = $score_avg;

                $comment_data['score_avg'] = $datas['score_avg'];
                $comment_data['content'] = $datas['contents'];
                if($order['pei_type'] == 3){
                    $comment_data['pei_time'] = $waimai_format['pei_time'];
                }else{
                    $comment_data['pei_time'] = $datas['pei_time'];
                }
                $comment_data['have_photo'] = $datas['have_photo'];
                $comment_data['extend'] = $datas['extend'];
                $comment_data['dateline']= __TIME;

                $comment_data['is_anonymous'] = $datas['is_anonymous'] ? $datas['is_anonymous'] : 0;//4.1匿名评价

                if ($comment_id = K::M('waimai/comment')->create($comment_data)) {
                    if (isset($datas['file'])) {
                        foreach ($datas['file'] as $v) {
                            $photo_data = array(
                                'comment_id' => $comment_id,
                                'photo' => $v
                            );
                           K::M($hp)->create($photo_data);
                        }
                    }
                    K::M('order/order')->update($datas['order_id'], array('comment_status' => 1));
                    /*$jifen = $this->system->config->get('jifen');
                    if($order['online_pay']==1){
                        $jifen_total = (int)(($order['amount']+$order['money']) * $jifen['jifen_ratio']);
                       K::M('member/member')->update_jifen($this->uid, $jifen_total, '订单' . $datas['order_id'] . '评价完成，获得积分');
                    }*/
                    //评价积分获得 2017/11/10 by yufan
                    if($order['online_pay'] == 1 && $order['jifen_cfg']['jifen_type'] == 1){
                        K::M('jifen/jifen')->update_jifen($datas['order_id'],$order);
                    }

                    $pei_times = intval((($waimai_format['pei_time'] * $waimai_format['comments'])+$datas['pei_time']) / ($waimai_format['comments']+1));
                    if($order['pei_type'] == 3){
                        if ($datas['score'] > 3) {
                            $update_data = array('comments' => '`comments`+1', 'praise_num' => '`praise_num`+1', 'score' => '`score`+' . $datas['score'],'score_peisong' => '`score_peisong`+' . $comment_data['score_peisong']);
                        } else {
                            $update_data = array('comments' => '`comments`+1', 'score' => '`score`+' . $datas['score'],'score_peisong' => '`score_peisong`+' . $comment_data['score_peisong']);
                        }
                    }else{
                        if ($datas['score'] > 3) {
                            $update_data = array('comments' => '`comments`+1', 'praise_num' => '`praise_num`+1', 'score' => '`score`+' . $datas['score'], 'score_peisong' => '`score_peisong`+' . $datas['score_peisong'], 'pei_time' => $pei_times);
                        } else {
                            $update_data = array('comments' => '`comments`+1', 'score' => '`score`+' . $datas['score'], 'score_peisong' => '`score_peisong`+' . $datas['score_peisong'], 'pei_time' => $pei_times);
                        }
                    }
                    if($order['pei_type']==1&&$order['staff_id']){
                        $staff_data = array();
                        $staff_data['order_id'] = $order['order_id'];
                        $staff_data['staff_id'] = $order['staff_id'];
                        $staff_data['uid'] = $order['uid'];
                        $staff_data['score'] = $datas['score_peisong'];
                        $staff_data['content'] = '';
                        $staff_data['have_photo'] = 0;
                        $staff_data['pei_time'] = $datas['pei_time'];
                        $staff_data['is_anonymous'] = $datas['is_anonymous'] ? $datas['is_anonymous'] : 0;//4.1匿名评价
                        $update_data_staff = array('score' => '`score`+' . $datas['score_peisong'], 'comments' => '`comments`+1', 'pei_time' => '`pei_time`+' .$datas['pei_time']);
                        K::M('staff/staff')->update($order['staff_id'], $update_data_staff, true);
                        K::M('staff/comment')->create($staff_data);
                    }

                    if(K::M('waimai/waimai')->update($order['shop_id'], $update_data, true)){
                        $content = '用户(' . $order['contact'] . ')已评价订单(ID:' . $order['order_id'] . ')';
                        K::M('shop/shop')->send($order['shop_id'], '订单评价', $content, array('type'=>'comment'));
                        $this->msgbox->add('评价成功!');
                    }else{
                        $this->msgbox->add('评价失败!', 263);
                    }
                }else{
                    $this->msgbox->add('添加评论失败',264);
                }
            }
        }else{
            $this->msgbox->add('评价失败!', 262);
        }
    }

    //获取外卖订单load
    protected function _order_items($filter, $orderby, $page=1, $limit=10, &$count=0)
    {
        $items = array();
        if($order_list = K::M('order/order')->items($filter, $orderby, $page, $limit, $count)){
            $order_ids = $shop_ids = $staff_ids = $waimai_shop_ids = array();
            foreach ($order_list as $k=>$v){
                $arr = K::M('waimai/order')->format_data($v);
                $order_list[$k]=$arr;
            }
            foreach($order_list as $k=>$v){
                if($v['shop_id']){
                    $shop_ids[$v['shop_id']] = $v['shop_id'];
                }
                $staff_ids[$v['staff_id']] = $v['staff_id'];
                $order_ids[$v['order_id']] = $v['order_id'];
                $waimai_shop_ids[$v['shop_id']] = $v['shop_id'];
                $waimai_order_ids[$v['order_id']] = $v['order_id'];
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
        }
        return $order_list;
    }

    // 取消订单
    public function chargeback($order_id)
    {
        if(!$order_id = (int)$order_id) {
            $this->msgbox->add('错误的订单ID',271);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add("不存在的订单",272);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('你没有权限操作',273);
        }else if($order['order_status'] < 0){
            $this->msgbox->add('订单已经取消，无需重复取消',274);
        }else if($order['order_status'] != 0){
            $this->msgbox->add('当前订单是不可取消的状态',275);
        }else{
            if(K::M('waimai/order')->cancel($order_id, $order, 'member')) {
               // K::M('shop/shop')->send($order['shop_id'], '订单已取消', '用户('.$order['contact'].')已取消订单(ID:'.$order_id.')',array('type'=>'cancelOrder','order_id'=>$order_id));
                $waimai_order_log=array(
                  'order_id'=>$order_id,
                    'from'=>'member',
                    'log'=>$this->MEMBER['nickname'].'(ID:'.$this->uid.')'.'取消订单'.'(ID:'.$order_id.')',
                    'type'=>-1
                );
                //========== 2019-01-18 取消订单，要把到货服务数据删除 =========
                K::M('order/srv/arrival')->delete($order_id);
                //===========================================================
              if(K::M('waimai/log')->create($waimai_order_log)){
                  $this->msgbox->add('取消订单成功');
              }else{
                  $this->msgbox->add('取消订单失败',277);
              }
            }else {
                $this->msgbox->add('取消订单失败',276);
            }
        }
    }

    // 催单
    public function remind($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->msgbox->add('错误的订单ID',277);
        }else if(!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add("订单不存在",278);
        }else if($order['uid'] != $this->uid) {
            $this->msgbox->add('你没有权限操作',279);
        }else if(!$worder = K::M('waimai/order')->detail($order_id)) {
            $this->msgbox->add("订单不存在",280);
        }else if($order['pei_type'] == 0 && (__TIME - $order['jd_time']) < 1800){
            $this->msgbox->add('请在30分钟后催单',281);
        }else if($order['pei_type'] == 1 && $order['expect_time'] && __TIME < $order['expect_time']){
            $this->msgbox->add('未超过预计送达时间，不可催单',282);
        }else if($order['cui_time']){
            $this->msgbox->add('您已经催过单了',289);
        }else {
            if(K::M('order/order')->update($order_id, array('cui_time'=>__TIME))) {
                K::M('shop/shop')->send($order['shop_id'], '用户正在催单', '用户('.$order['contact'].')正在催促订单(ID:'.$order_id.')',array('type'=>'cuiOrder','order_id'=>$order_id,'from'=>'waimai'));
                if($staff_id = $order['staff_id']) {
                    K::M('staff/staff')->send($staff_id, '用户正在催单', '用户('.$order['contact'].')于'.date('Y-m-d H:i').'催单(ID:'.$order_id.')'.'手机号'.'('.$order['mobile'].')',array('type'=>'cuiOrder','order_id'=>$order_id));
                }
                $cui_log_data = array();
                $cui_log_data['uid'] =$order['uid'];
                $cui_log_data['shop_id'] = $order['shop_id'];
                $cui_log_data['staff_id'] = $order['staff_id'];
                $cui_log_data['order_id'] = $order['order_id'];
                K::M('order/cuilog')->create($cui_log_data);
                $this->msgbox->add('催单成功');
            }else {
                $this->msgbox->add('催单失败',282);
            }
        }
    }

    //申请退款
    public function payback(){
        if(!$order_id = (int)$this->GP('order_id')){
            $this->msgbox->add('错误的订单ID',283);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('没有找到该订单',284);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('请勿越权操作',285);
        }else if(!in_array($order['order_status'], array(1,2,3,4))){
            $this->msgbox->add('当前订单状态无法退款',286);
        }else if($order['refund_status'] != 0){
            $this->msgbox->add('该订单退款处理中',288);
        }else if(strlen($this->GP('reason')) == 0&&!$this->GP('reason')){
            $this->msgbox->add('请输入退款理由！',289);
        } else{
            //订单日志
            $reason = $this->GP('reason');
            $order_log = array(
                'order_id'=>$order['order_id'],
                'status'=>0,
                'from'=>'member',
                'log'=>'用户申请订单(ID:'.$order['order_id'].')退款',
                'intro'=>$reason
            );
            //外卖订单日志
            $waimai_log = array(
                'order_id'=>$order['order_id'],
                'from'=>'member',
                'log'=>'用户申请订单(ID:'.$order['order_id'].')退款',
                'type'=>7
            );
            if($order['staff_id']>0){
                K::M('staff/staff')->send($order['staff_id'], '用户已经申请退单', '用户('.$order['contact'].')已经申请订单(ID:'.$order_id.')'.'退款',array('type'=>'tuiOrder','order_id'=>$order_id));
            }
            //保存记录

            if($order['refund_status']!=1){
                if(K::M('order/order')->update($order['order_id'],array('refund_status'=>1))){
                    $refund_log = array(
                        'order_id' => $order['order_id'],
                        'from' => 'member',
                        'uid' => $this->uid,
                        'shop_id' => $order['shop_id'],
                        'reflect' => $this->GP('reason'),
                        'refund_price' => $order['amount'],
                    );
                    if(K::M('waimai/order/refund')->create($refund_log)){
                        $this->msgbox->add('退款申请成功');
                        K::M('order/log')->create($order_log);
                        K::M('waimai/log')->create($waimai_log);
                        K::M('shop/shop')->send($order['shop_id'], '用户申请退款','用户('.$order['contact'].')申请订单(ID:'.$order_id.')'."退款",array('type'=>'tuiOrder','order_id'=>$order_id));
                        $waimai = K::M('waimai/waimai')->detail($order['shop_id']);
                        K::M('order/order')->send_member('申请退款', sprintf("您在[%s]下的订单(%s)，用户申请退款", $waimai['title'], $order_id), $order);
                    }else{
                        $this->msgbox->add('退款申请失败',289);
                        K::M('order/order')->update($order['order_id'],array('refund_status'=>0));
                    }                    
                }
            } else{
                $this->msgbox->add('退款申请失败',288);
            }
        }
    }

    //确认收货
    public function confirm($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',289);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',290);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('您没有权限操作',291);
        }else if($order['order_status']==8){
            $this->msgbox->add('订单已完成',292);
        }else if($order['order_status']==-1){
            $this->msgbox->add('订单已取消',293);
        }/*else if($order['refund_status']!=0){
            $this->msgbox->add('该订单退款处理中',294);
        }*/else{
            //确认结单
            if($xx=K::M('waimai/order')->confirm($order_id,null,'member')){
                K::M('order/order')->update($order_id,array('refund_status'=>0));
                K::M('waimai/order/refund')->delete($order_id);
                //外卖订单日志
                $waimai_log = array(
                    'order_id'=>$order['order_id'],
                    'from'=>'member',
                    'log'=>$order['contact'].'(ID:'.$this->uid.')'.'结算订单(ID:'.$order['order_id'].')',
                    'type'=>6

                );
                if(K::M('waimai/log')->create($waimai_log)){
                    $this->msgbox->add('确认订单成功');
                }else{
                    $this->msgbox->add('确认订单失败',296);
                }
            }else{
                $this->msgbox->add('确认订单失败',295);
            }
        }
    }

    //申请客服介入
    public function kefu($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',296);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',297);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('您没有权限操作',298);
        }else if($order['order_status']==-1||$order['order_status']==8){
            $this->msgbox->add('当前订单状态无法申请客服',299);
        }else if($order['refund_status']==1||$order['refund_status']==3){
            $this->msgbox->add('订单正在退款处理中',300);
        }else{
            $order_log = array(
                'order_id'=>$order['order_id'],
                'status'=>0,
                'from'=>'member',
                'log'=>'用户(ID:'.$this->uid.')'.'申请客服协助退款'
            );
            //外卖订单日志
            $waimai_log = array(
                'order_id'=>$order['order_id'],
                'from'=>'member',
                'log'=>'用户申请客服协助订单(ID:'.$order['order_id'].')退款',
                'type'=>0
            );
            //商家消息
            $shop_msg = array(
                'shop_id'=>$order['shop_id'],
                'title'=>'用户申请平台协助退款',
                'content'=>'用户('.$order['contact'].')申请客服协助订单(ID:'.$order_id.')'."退款",
                'is_read'=>0,
                'type'=>1,
                'order_id'=>$order_id
            );
            //保存记录
            if(K::M('order/log')->create($order_log)&&K::M('waimai/log')->create($waimai_log)&&K::M('shop/msg')->create($shop_msg)){
                if(K::M('order/order')->update($order_id,array('refund_status'=>3))){
                    K::M('waimai/order/refund')->update($order_id,array('status'=>3));
                    $this->msgbox->add('申请成功，请耐心等待客服处理');
                }else{
                    $this->msgbox->add('申请客服失败,请稍后再试',301);
                }
            }else{
                $this->msgbox->add('申请客服失败，请稍后再试',302);
            }
        }
    }

    //查看订单状态
    public function status($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',305);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',306);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('您没有权限操作',307);
        }else {
            $filter = array();
            $filter['order_id'] =$order_id;
            $log = K::M('order/log')->items($filter,array('log_id'=>'asc'));
            $log_data = array();
            foreach ($log as $v){
                $v['show_time'] = date('Y-m-d H:i',$v['dateline']);
                $log_data[] =$v;
            }
            $this->pagedata['log'] =$log_data;
            $this->tmpl = 'order/status.html';
        }
        $this->msgbox->set_data('forward',$this->mklink('waimai/ucenter/order:detail',array($order_id)));
    }

    //返回投诉的配置--可以配置快速投诉理由
    public function noticearr(){
        $array= array(
            'staff'=>array(
                '送餐速度慢',
                '服务态度差',
                '餐盒破损',
                '电话打不通',
                '任性',
                '其他',
            ),
            'shop'=>array(
                '菜不好吃',
                '价格不合适',
                '任性',
                '其他',
            ),
        );
        return $array;
    }

    public function again($order_id){
        if(!$order_id){
            $this->msgbox->add('订单号不存在',330);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单号不存在',331);
        }else if(!$waimai_order_product = K::M('waimai/orderproduct')->items(array('order_id'=>$order_id))){
            $this->msgbox->add('订单商品不存在',332);
        }else{
            $shop_id = $order['shop_id'];
            $array=array();
            $ids =array();
            $end_format = array();
            $spec_id = array();
            foreach ($waimai_order_product as $k=>$items){
                if($items['huangou_id']){
                    unset($waimai_order_product[$k]);
                }else{
                    $ids[$items['product_id']] =$items['product_id'];
                    if($items['spec_id']!=0){
                        $spec_id[$items['spec_id']] = $items['spec_id'];
                    }
                }               
            }
            $product_list = K::M('waimai/product')->items_by_ids($ids);
            $spec = K::M('waimai/productspec')->items_by_ids($spec_id);
            $count_error = 0;
            foreach ($waimai_order_product as $k=>$v){
                $error = 0;
                $count_shuxin_1 = count($v['specification']);
                $count_shuxin_2 = count($product_list[$v['product_id']]['specification']);
                if($count_shuxin_2!=$count_shuxin_1){
                    $error = 999;
                    $count_error++;
                }else{
                    $key_array = array();
                    foreach($product_list[$v['product_id']]['specification'] as $kkk4=>$vvv4){
                        $key_array[] =  $vvv4['key'];
                    }
                    foreach($product_list[$v['product_id']]['specification'] as $kkk2=>$vvv3){
                        foreach($v['specification'] as $kk3=>$vv3){
                            if(!in_array($vv3['key'],$key_array)){
                                $error = 999;
                                $count_error++;
                            }
                            if($vvv3['key']==$vv3['key']){
                                if(!in_array($vv3['val'],$vvv3['val'])){
                                    $error = 999;
                                    $count_error++;
                                }
                            }
                        }
                    }
                }
                //有规格时返回的再来一单数据
                if($v['spec_id']!=0&&$error==0){
                    foreach ($spec as $kk=>$vv){
                        if($v['spec_id']==$vv['spec_id']&&$product_list[$v['product_id']]['is_spec']&&$product_list[$v['product_id']]){
                            $array[] = array(
                                'product_id'=>$v['product_id'],
                                'title'=>$product_list[$v['product_id']]['title'],
                                'spec_name'=>$vv['spec_name'],
                                'price'=>$vv['price'],
                                'spec_photo'=>$vv['spec_photo']?$vv['spec_photo']:$product_list[$v['product_id']]['photo'],
                                'package'=>$vv['package_price'],
                                'sale_type'=>$product_list[$vv['product_id']]['sale_type'],
                                'sale_sku'=>$vv['sale_sku'],
                                'sku_id'=>$vv['product_id'].'-'.$vv['spec_id'],
                                'num'=>$v['product_number'],
                                'specification'=>$v['specification'],
                                'pcate_id'=>implode(',',$product_list[$v['product_id']]['cate_ids'])
                            );
                        }
                    }
                }else if($error==0){
                 //无规格是返回的再来一单数据
                    foreach ($product_list as $kk=>$vv){
                        if($v['product_id']==$vv['product_id']&&!$vv['is_spec']){
                            $array[] = array(
                                'product_id'=>$v['product_id'],
                                'title'=>$vv['title'],
                                'spec_name'=>'',
                                'price'=>$vv['price'],
                                'photo'=>$vv['photo'],
                                'package'=>$vv['package_price'],
                                'sale_type'=>$vv['sale_type'],
                                'sale_sku'=>$vv['sale_sku'],
                                'sku_id'=>$v['product_id'].'-'.'0',
                                'num'=>$v['product_number'],
                                'specification'=>$v['specification'],
                                'pcate_id'=>implode(',',$product_list[$v['product_id']]['cate_ids'])
                            );
                        }
                    }
                }
            }

            $discount = K::M('waimai/huodongdiscount')->get_discount($shop_id);//折扣
            $array = K::M('waimai/huodongdiscount')->get_newProducts($discount, $array);
            $end_format['discount'] = $discount;

            foreach($array as $kk=>$vv){
                $array[$kk]['pcate_id'] = $product_list[$vv['product_id']]['cate_id'];
            }

            $end_format['order']=$array;
            $end_format['shop_id'] = $shop_id;
            $shop = K::M('waimai/waimai')->detail($shop_id);
            $end_format['title'] = $shop['title'];

            if($count_error>0){
                $this->msgbox->add('购物车商品发生变更，请注意',999);
            }
            $this->msgbox->set_data('data',$end_format);
            $this->msgbox->json();
        }
    }

    //异步上传文件
    public function uploadimg()
    {
        if(!$attach = $_FILES['file']){
            $this->msgbox->add('上传图片失败', 501);
        }elseif($attach['error'] != UPLOAD_ERR_OK){
            $this->msgbox->add('上传图片失败', 502);
        }elseif($data = K::M('magic/upload')->upload($attach, 'comment', $fname, array('photo'=>'720', 'thumb'=>'200X200'))){
            $this->msgbox->set_data('file', $data);
        }
        $this->msgbox->json();
    }

    public function order_map($order_id){
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('越权操作',203);
        }else {
            $staff = K::M('staff/staff')->detail($order['staff_id']);
            $this->pagedata['staff'] = $staff;
            $this->pagedata['order'] = $order;
            if($order['order_status']==2/*&&($order['refund_status']==0||$order['refund_status']==-1)*/){
                $round = K::M('helper/round')->juli($order['o_lng'],$order['o_lat'],$staff['lng'],$staff['lat']);
                $distancce = K::M('helper/format')->juli($round);
                $this->pagedata['msg'] ='<span style="color:display:#000000;">配送员距离商家还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';

            } else if($order['order_status']==3){
                $order['show_map'] =1;
                $round = K::M('helper/round')->juli($order['lng'],$order['lat'],$staff['lng'],$staff['lat']);
                $distancce = K::M('helper/format')->juli($round);
                $this->pagedata['msg'] ='<span style="color:display:#000000;">配送员距离你还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';
            }
            $this->tmpl ="order/order_map.html";
        }
    }

    public function order_dispatch_status($order_id)
    {
        if(!$order_id){
            $this->msgbox->add('订单不存在',201);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在',202);
        }else if($order['uid']!=$this->uid){
            $this->msgbox->add('越权操作',203);
        }else {
            $data = [
                'msg' => "",
                'staff_lng' => 0,
                'staff_lat' => 0
            ];
            $bSend = true;
            $staff = K::M('staff/staff')->detail($order['staff_id']);
            $data['staff_lng'] = (float)$staff['lng'];
            $data['staff_lat'] = (float)$staff['lat'];
            if($order['order_status']==2){
                $round = K::M('helper/round')->juli($order['o_lng'],$order['o_lat'],$staff['lng'],$staff['lat']);
                $distancce = K::M('helper/format')->juli($round);
                $data['msg'] ='<span style="color:display:#000000;">配送员距离商家还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';

            }else if($order['order_status']==3){
                $order['show_map'] =1;
                $round = K::M('helper/round')->juli($order['lng'],$order['lat'],$staff['lng'],$staff['lat']);
                $distancce = K::M('helper/format')->juli($round);
                $data['msg'] ='<span style="color:display:#000000;">配送员距离你还有</span>'.'<span style="color:#A64540;">'.$distancce.'</span>';
            }
            else
                $bSend = false;
            if($bSend)
                $this->msgbox->set_data('data',$data);
            else
                $this->msgbox->add("无配送数据",204);
        }
        $this->msgbox->json();
    }

}