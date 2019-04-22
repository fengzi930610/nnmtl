<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Tongji_Tongji extends Ctl
{
    public function index()
    {
        //$sdate = strtotime("2017-6-1");
        $sdate = strtotime(date('Y-m-01', strtotime('-11 month')));
        $mouth_arr = K::M('helper/date')->get_date_list($sdate, __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        foreach ($mouth_arr as $k=>$v){
            $mouth_arr[$k] = date('Y-m',$v);
        }
        $this->pagedata['bills_month'] = $mouth_arr;
        $this->pagedata['tongji'] = $xx= K::M('site/tongji')->get_three_day_data();

        $data = array();
        
        $m_dateline = strtotime(date('Y-m-1')).'~'.__TIME;//本月时间       
        $l_m_dateline = strtotime(date("Y-m-01",strtotime(date('Y-m-d')))."-1 month")."~".(strtotime(date('Y-m-1'))-1);//上月时间

        //新增用户数据
        $t_member = K::M('member/member')->count(array('dateline'=>$m_dateline));//本月新用户
        $l_member =  K::M('member/member')->count(array('dateline'=>$l_m_dateline));//上月新用户
        $data['member'] = $t_member;
        $data['member_bl'] = K::M('helper/format')->get_bl($t_member,$l_member);
        $data['member_bl'] =  $data['member_bl']>0?'+'.$data['member_bl']: $data['member_bl'];

        //外卖数据-----------------------------------------------------------------------------------
        $t_mouth_w = K::M('site/tongji')->sum_by_filter(array('dateline'=>$m_dateline,'from'=>'waimai')); //本月营业统计
        $l_mouth_w = K::M('site/tongji')->sum_by_filter(array('dateline'=>$l_m_dateline,'from'=>'waimai'));//上月营业统计
        $data['amount_w'] = $t_mouth_w['amount'];
        $data['amount_w_bl'] =  K::M('helper/format')->get_bl($t_mouth_w['amount'],$l_mouth_w['amount']);
        $data['amount_w_bl'] =  $data['amount_w_bl']>0 ? '+'.$data['amount_w_bl'] : $data['amount_w_bl'];

        $data['y_order_w'] = $t_mouth_w['orders'];//有效配送数（单）
        $data['y_order_w_bl'] = K::M('helper/format')->get_bl($t_mouth_w['orders'],$l_mouth_w['order_s']);
        $data['y_order_w_bl'] = $data['y_order_w_bl']>0?"+".$data['y_order_w_bl']:$data['y_order_w_bl'];

        $t_order_w = K::M('order/order')->count(array('dateline'=>$m_dateline,'from'=>array('waimai')));//本月订单
        $l_order_w = K::M('order/order')->count(array('dateline'=>$l_m_dateline,'from'=>array('waimai')));//上月订单
        $data['order_w'] = $t_order_w;
        $data['order_w_bl'] =  K::M('helper/format')->get_bl($t_order_w,$l_order_w);
        $data['order_w_bl'] =  $data['order_w_bl'] > 0 ? '+'.$data['order_w_bl'] : $data['order_w_bl'];

        //跑腿数据-----------------------------------------------------------------------------------
        if(defined('HAVE_PAOTUI') && HAVE_PAOTUI){
            $t_mouth_p = K::M('site/tongji')->sum_by_filter(array('dateline'=>$m_dateline,'from'=>'paotui'));
            $l_mouth_p = K::M('site/tongji')->sum_by_filter(array('dateline'=>$l_m_dateline,'from'=>'paotui'));
            $data['amount_p'] = $t_mouth_p['amount'];
            $data['amount_p_bl'] =  K::M('helper/format')->get_bl($t_mouth_p['amount'],$l_mouth_p['amount']);
            $data['amount_p_bl'] =  $data['amount_p_bl']>0?"+".$data['amount_p_bl']:$data['amount_p_bl'];

            $data['y_order_p'] = $t_mouth_p['orders'];
            $data['y_order_p_bl'] =  K::M('helper/format')->get_bl($t_mouth_p['orders'],$l_order_p['order_s']);
            $data['y_order_p_bl'] =  $data['y_order_p_bl']>0? $data['y_order_p_bl']: $data['y_order_p_bl'];

            $t_order_p = K::M('order/order')->count(array('dateline'=>$m_dateline,'from'=>array('paotui')));
            $l_order_p = K::M('order/order')->count(array('dateline'=>$l_m_dateline,'from'=>array('paotui')));
            $data['order_p'] = $t_order_p;
            $data['order_p_bl'] =  K::M('helper/format')->get_bl($t_order_p,$l_order_p);
            $data['order_p_bl'] =  $data['order_p_bl']>0?"+".$data['order_p_bl']:$data['order_p_bl'];
        }
        
        //抢购数据-----------------------------------------------------------------------------------
        if(defined('HAVE_QIANG') && HAVE_QIANG){
            $t_mouth_q = K::M('site/tongji')->sum_by_filter(array('dateline'=>$m_dateline,'from'=>'qiang'));
            $l_mouth_q = K::M('site/tongji')->sum_by_filter(array('dateline'=>$l_m_dateline,'from'=>'qiang'));
            $data['amount_q'] = $t_mouth_q['amount'];
            $data['amount_q_bl'] =  K::M('helper/format')->get_bl($t_mouth_q['amount'],$l_mouth_q['amount']);
            $data['amount_q_bl'] =  $data['amount_q_bl']>0?"+".$data['amount_q_bl']:$data['amount_q_bl'];

            $data['y_order_q'] = $t_mouth_q['orders'];
            $data['y_order_q_bl'] =  K::M('helper/format')->get_bl($t_mouth_q['orders'],$l_order_q['order_s']);
            $data['y_order_q_bl'] =  $data['y_order_q_bl']>0? $data['y_order_q_bl']: $data['y_order_q_bl'];

            $t_order_q = K::M('order/order')->count(array('dateline'=>$m_dateline,'from'=>array('qiang')));
            $l_order_q = K::M('order/order')->count(array('dateline'=>$l_m_dateline,'from'=>array('qiang')));
            $data['order_q'] = $t_order_q;
            $data['order_q_bl'] =  K::M('helper/format')->get_bl($t_order_q,$l_order_q);
            $data['order_q_bl'] =  $data['order_q_bl']>0?"+".$data['order_q_bl']:$data['order_q_bl'];
        }
        
        //配送会员卡数据-----------------------------------------------------------------------------------
        $t_mouth_peicard = K::M('site/tongji')->sum_by_filter(array('dateline'=>$m_dateline,'from'=>'peicard'));
        $l_mouth_peicard = K::M('site/tongji')->sum_by_filter(array('dateline'=>$l_m_dateline,'from'=>'peicard'));
        $data['amount_peicard'] = $t_mouth_peicard['amount'];
        $data['amount_peicard_bl'] =  K::M('helper/format')->get_bl($t_mouth_peicard['amount'],$l_mouth_peicard['amount']);
        $data['amount_peicard_bl'] =  $data['amount_peicard_bl'] > 0 ? '+'.$data['amount_peicard_bl'] : $data['amount_peicard_bl'];

        $data['order_peicard'] = $t_mouth_peicard['orders'];
        $data['order_peicard_bl'] =  K::M('helper/format')->get_bl($t_mouth_peicard['orders'], $l_mouth_peicard['orders']);
        $data['order_peicard_bl'] =  $data['order_peicard_bl'] > 0 ? '+'.$data['order_peicard_bl'] : $data['order_peicard_bl'];

        $this->pagedata['data'] =$data;
        $this->tmpl = 'admin:tongji/tongji/index.html';
    }

    public function get_chart($time)
    {
        if ($time == 0) {
            $time = date('Ymd', __TIME-86400); // 昨天
        }elseif ($time == 1) {
            $time = date('Ymd');// 今天
        }
        $this->msgbox->set_data('data', K::M('data/tongji')->get_chart_totime($time));
        $this->msgbox->json();
    }

    public function get_chart_order($time)
    {
        $this->msgbox->set_data('data', K::M('data/tongji')->get_chart_order_totime($time));
        $this->msgbox->json();
    }

    public function get_data(){
        if($data = $this->checksubmit('data')){

            $step = 'h';
            if($data['time']==1){
                //昨日
                $step_first = strtotime(date('Y-m-d')."-1 day");
                $step_last = (strtotime(date('Y-m-d'))-1);
            }else if($data['time']==2){
                //今天
                $step_first = strtotime(date('Y-m-d'));
                $step_last = (strtotime(date('Y-m-d'))+86399);;
            }else if($data['time']==3){
                //本周
                $w_dateline = K::M('helper/date')->get_date_filter('l');
                $arr = explode("~",$w_dateline['t']);
                $step_first = $arr[0];
                $step_last = $arr[1];
                if($data['type']=='d'){
                    $step = 'd';
                }else if($data['type']=='h'){
                    $step = 'h';
                }else{
                    $step = 'd';
                }

            }else{
                //指定月份
                $step_first = strtotime(date('Y-m-01',strtotime($data['time'])));  //获取本月第一天时间戳
                $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
                if($data['type']=='d'){
                    $step = 'd';
                }else if($data['type']=='h'){
                    $step = 'h';
                }else{
                    $step = 'd';
                }
            }
            $data = array();
            $filter = array();
            $filter['dateline'] = $step_first.'~'.$step_last;
            $data['high_data_h'] = K::M('site/tongji')->get_data_by_type($filter,$step_first,$step_last,$step);
            $data['data_waimai'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'waimai'));
            $data['data_paotui'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'paotui'));
            $data['data_qiang'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last,'from'=>'qiang'));
            $data['data_all'] = K::M('site/tongji')->sum_by_filter(array('dateline'=>$step_first.'~'.$step_last));
            $this->msgbox->set_data('data',$data);
        }

    }

}