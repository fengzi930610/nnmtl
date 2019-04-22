<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Waimai extends Mdl_Table
{
    protected $_table = 'waimai';
    protected $_pk = 'shop_id';
    protected $_cols = 'shop_id,city_id,area_id,business_id,cate_id,cate_ids,title,contact,banner,logo,declare,addr,views,orders,comments,praise_num,score,score_fuwu,score_kouwei,first_amount,min_amount,freight,pei_amount,pei_time,pei_distance,pei_type,yy_status,yy_stime,yy_ltime,yy_xiuxi,is_new,online_pay,youhui,info,delcare,pmid,verify_name,audit,closed,clientip,tmpl_type,dateline,last_time,phone,freight_stage,is_daofu,is_ziti,lat,lng,orderby,yuyue_day,yy_peitime,area_polygon,waimai_bl,hot,hd_first_ltime,hd_coupon_ltime,hd_mf_ltime,hd_mj_ltime,group_id,print_type,config,is_separate,ps_time,refund_order,yy_weeks,pstime_type,jiesuan_type,hd_discount_ltime,is_ztsp,zt_bl,zero_ziti,deliver,warn_sku,auto_print,freight_calc_type';
    protected $_orderby = array('orderby'=>'ASC', 'praise_num'=>'DESC', 'orders'=>'DESC');
    protected static $cate_list = null;


    /*public function detail($shop_id, $closed=false)
    {
        if(!$shop_id = (int)$shop_id){
            return false;
        }
        $where ="s.shop_id=ext.shop_id AND s.shop_id=".$shop_id;
        if(empty($closed)){
            //$where .= " AND s.closed='0'";
        }
        $sql = "SELECT s.*, ext.* FROM " .$this->table('shop')." s, ".$this->table($this->_table)." ext WHERE $where";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);

        }
        return $row;
    }*/

    public function get_redirect($shop_id){
        $redirect = 1;
        if(!$shop_id = (int)$shop_id){
            $redirect = 1;
        }elseif(!$detail = $this->detail($shop_id)){
            $redirect = 1;
        }elseif($detail['audit'] == 0){
            $redirect = 1;
        }else if($detail['verify_name']==1){
           return 0;
        }elseif(!$verify = K::M('waimai/verify')->detail($shop_id)){
            $redirect = 1;
        }elseif($verify['verify'] != 1){
            $redirect = 1;
        }elseif(!$account = K::M('shop/account')->detail($shop_id)){
            $redirect = 1;
        }else{
            $redirect = 2;
        }
        return $redirect;
    }
    
    public function get_huodong($shop_ids){
        $wordColor = $this->getHdWordColor();
        $huodong = array();
        if($shop_ids){
            //首单优惠
            $firsts = K::M('waimai/huodongfirst')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($firsts as $k=>$v){
                if($v['config']){
                    $huodong[$v['shop_id']][0] = array('title'=>"首单立减".$v['config']['first_amount']."元",'word'=>$wordColor['first']['word'],'color'=>$wordColor['first']['color']);
                }
            }
            //满减优惠
            $manjian = K::M('waimai/huodongmj')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($manjian as $k=>$v){
                $title = '';
                foreach($v['config'] as $k1=>$v1){
                    if($v1['order_amount']&&$v1['coupon_amount']){
                        if($k1==0){
                            $title = '满'.$v1['order_amount'].'减'.$v1['coupon_amount'];
                        }else{
                            $title .= ',满'.$v1['order_amount'].'减'.$v1['coupon_amount'];
                        }
                        $huodong[$v['shop_id']][1] = array('title'=>$title,'word'=>$wordColor['manjian']['word'],'color'=>$wordColor['manjian']['color']);
                    }
                }
            }
            $manfan = K::M('waimai/huodongmf')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($manfan as $k=>$v){
                $title2 = '';
                foreach($v['config'] as $k1=>$v1){
                    if($v1['paid_amount']&&$v1['coupon_amount']){
                        if($k1==0){
                            $title2 = '实付满'.$v1['paid_amount'].'元返'.$v1['coupon_amount'].'元优惠券';
                        }else{
                            $title2 .= ',实付满'.$v1['paid_amount'].'元返'.$v1['coupon_amount'].'元优惠券';
                        }
                        $huodong[$v['shop_id']][2] = array('title'=>$title2,'word'=>$wordColor['manfan']['word'],'color'=>$wordColor['manfan']['color']);
                    }
                }
            }
            $coupons = K::M('waimai/huodongcoupon')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($coupons as $k=>$v){
                $num = 0;
                foreach($v['config'] as $k1=>$v1){
                    $num += $v1['coupon_amount'];
                }
                if($num>0){
                    $huodong[$v['shop_id']][3] = array('title'=>'进店领'.$num.'元优惠券','word'=>$wordColor['coupon']['word'],'color'=>$wordColor['coupon']['color']);
                }
            }

            $discounts = K::M('waimai/huodongdiscount')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            $now_week = $now_week = date('w',__TIME);
            foreach($discounts as $k=>$v){
                if(!in_array($now_week,$v['period_weeks']) || strtotime($v['period_times']['stime']) >= __TIME || strtotime($v['period_times']['ltime']) <= __TIME){
                    continue;
                }else{
                    /*if($v['discount_type']){
                        $title = '减价优惠';
                    }else{
                        $title = '打折优惠';
                    }*/
                    $title = $v['title'];
                    $huodong[$v['shop_id']][4] = array('title'=>$title,'word'=>$wordColor['discount']['word'],'color'=>$wordColor['discount']['color']);
                }
            }

            $huangous = K::M('waimai/huodonghuangou')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            $now_week = $now_week = date('w',__TIME);
            foreach($huangous as $k=>$v){
                if(!in_array($now_week,$v['period_weeks']) || strtotime($v['period_times']['stime']) >= __TIME || strtotime($v['period_times']['ltime']) <= __TIME){
                    continue;
                }else{
                    $title = $v['title'];
                    $huodong[$v['shop_id']][5] = array('title'=>$title,'word'=>$wordColor['huangou']['word'],'color'=>$wordColor['huangou']['color']);
                }
            }

        }
        foreach($huodong as $k=>$v){
            $huodong[$k] = array_values($v);
        }
        return $huodong;
    }

    protected function _format_row($row)
    {
        //处理freight_stage取最小值
        if($row['freight_stage'] = unserialize($row['freight_stage'])){
            foreach($row['freight_stage'] as $fk => $fv){
                $new_arr[$fv['fm']] = $fv['fm'];
            }
            ksort($new_arr);
            $row['freight_price'] = array_shift($new_arr);
            if(!$row['freight_price']){
                $row['freight_price'] = 0;
            }
            //处理freight_stage取最小值结束            
        }else{
            $row['freight_stage'] = array();
            $row['freight_price'] = 0;
        }
        if(!in_array($row['tmpl_type'], array('waimai', 'market'))){
            $row['tmpl_type'] = 'waimai';
        }
        $row['lat'] = bcdiv($row['lat'], 1000000,6);
        $row['lng'] = bcdiv($row['lng'], 1000000,6);


        //商家配送区域反序列化
        $row['area_polygon'] = !empty($row['area_polygon']) ? unserialize($row['area_polygon']) : array();
        $row['yy_peitime'] = !empty($row['yy_peitime']) ? unserialize($row['yy_peitime']) : array(); // 新版配送时间
        $row['hot'] = !empty($row['hot']) ? unserialize($row['hot']) : array();
        $row['ps_time'] = !empty($row['ps_time'])?unserialize($row['ps_time']):array();
        $row['yysj_status'] = 0;

        $yysj_times = -1;//4.1离打烊还剩多长时间
        $yysj_start_time = $yysj_end_time = 0; //4.1营业时间所在时间段
        if ($row['yy_peitime']) {
            $now_time = __TIME;
            foreach ($row['yy_peitime'] as $k => $v) {
                $start_time = strtotime(date('Y-m-d ',$now_time).$v['stime']);
                $end_time = strtotime(date('Y-m-d ',$now_time).$v['ltime']);

                //2017/11/22 v3.6
                $v['stime_time'] = $start_time - strtotime(date('Y-m-d ',$now_time));
                $v['ltime_time'] = $end_time - strtotime(date('Y-m-d ',$now_time));
                if(stristr($v['ltime'],'次日')){
                    $ltime = str_replace('次日', '', $v['ltime']);
                    $end_time = strtotime(date('Y-m-d ',$now_time).$ltime) + 86400;
                    $v['ltime_time'] = $end_time - strtotime(date('Y-m-d ',$now_time)) + 86400;

                    $start_time1 = strtotime(date('Y-m-d ',$now_time).'00:00');
                    $end_time1 = strtotime(date('Y-m-d ',$now_time).$ltime);
                    $start_time2 = $start_time;
                    $end_time2 = strtotime(date('Y-m-d ',$now_time).'23:59');
                    /*if(($now_time >= $start_time1 && $now_time <= $end_time1) || ($now_time >= $start_time2 && $now_time <= $end_time2)){
                        $row['yysj_status'] = 1;//营业中
                    }*/
                    if($now_time >= $start_time1 && $now_time <= $end_time1){
                        $row['yysj_status'] = 1;//营业中
                        $yysj_times = ceil(($end_time1 - $now_time)/60);
                        $yysj_start_time = $start_time1;
                        $yysj_start_time = $end_time1;
                    }else if($now_time >= $start_time2 && $now_time <= $end_time2){
                        $row['yysj_status'] = 1;//营业中
                        $yysj_times = ceil(($end_time2 - $now_time)/60);
                        $yysj_start_time = $start_time2;
                        $yysj_start_time = $end_time2;
                    }
                }else{
                    if ($start_time && $end_time) {
                        if($now_time >= $start_time && $now_time <= $end_time){
                            $row['yysj_status'] = 1;//营业中
                            $yysj_times = ceil(($end_time - $now_time)/60);
                            $yysj_start_time = $start_time;
                            $yysj_start_time = $end_time;
                        }
                    }
                }
                $row['yy_peitime'][$k] = $v;

                /*if ($start_time && $end_time) {
                    if($now_time >= $start_time && $now_time <= $end_time){
                        $row['yysj_status'] = 1;//营业中
                        //break;
                    }
                }*/
            }
        }

        //2017/11/22 v3.6
        if ($row['ps_time']) {
            $now_time = __TIME;
            foreach ($row['ps_time'] as $k => $v) {
                $start_time = strtotime(date('Y-m-d ',$now_time).$v['stime']);
                $end_time = strtotime(date('Y-m-d ',$now_time).$v['ltime']);

                $v['stime_time'] = $start_time - strtotime(date('Y-m-d ',$now_time));
                $v['ltime_time'] = $end_time - strtotime(date('Y-m-d ',$now_time));
                if(stristr($v['ltime'],'次日')){
                    $ltime = str_replace('次日', '', $v['ltime']);
                    $end_time = strtotime(date('Y-m-d ',$now_time).$ltime) + 86400;
                    $v['ltime_time'] = $end_time - strtotime(date('Y-m-d ',$now_time)) + 86400;
                }
                $row['ps_time'][$k] = $v;
            }
        }

        $row['remove'] = 0;
        if(!K::M('waimai/config')->getsiteopen()&&$row['pei_type']==1){
            $row['is_daofu'] = 0;
            $row['remove'] = 1;
        }
        $row['is_root'] = 0;
        //新增一键关店功能
        $site_config = K::M('system/config')->get('waimaihuodongconfig');
        if($site_config['closesite']==1){
          if($row['pei_type']==1){
              $row['yy_status'] = 0;
              $row['is_root'] = 1;
          }
        }else if($site_config['closesite']==2){
            if($row['pei_type']==0){
                $row['yy_status'] = 0;
                $row['is_root']=1;
            }

        }else if($site_config['closesite']==3){
            $row['yy_status'] = 0;
            $row['is_root']=1;
        }
        $row['config'] = $row['config']?unserialize($row['config']):array();

        if(!$row['logo']){
            $row['logo'] = 'default/shop_logo.png';
        }
        if(!$row['banner']){
            $row['banner'] = 'default/shop_logo.png';
        }
        if($row['pei_type']==1&&$row['group_id']){
            $cache = K::M('pei/group')->get_cache($row['group_id']);
            if($cache['badweather']['config']&&$cache['badweather']['is_used']==1){
                $row['is_separate'] = 0;
            }
        }

        //v3.6 营业时间按周选择
        $row['yy_weeks'] = $row['yy_weeks']||$row['yy_weeks']==0 ? explode(',',$row['yy_weeks']):array(1, 2, 3, 4, 5, 6, 0);
        $now_week = date('w',__TIME);
        if(!in_array($now_week,$row['yy_weeks'])){
            $row['yysj_status'] = 0;
        }
        if($row['is_ziti']==1&&$row['zero_ziti']==1){
            $row['can_zero_ziti'] = 1;
        }else{
            $row['can_zero_ziti'] = 0;
        }

        //4.1显示打烊标签问题
        $yyst_label = '';
        if ($row['yysj_status'] == 1 && $row['yy_status'] == 1) {// 取序列化配置的营业时间
            $row['yyst'] = 1;
            if($yysj_times == 0){
                $yyst_label = '即将打烊';
            }else if($yysj_times < 30 && $yysj_times > 0){
                $yyst_label = sprintf('%s分钟后打烊', $yysj_times);
            }
        }elseif($row['yysj_status'] != 1 || $row['yy_status'] != 1){
            $row['yyst'] = 0;
        }

        //4.1显示可预定时间（正常营业状态下，当前时间未到可配送时间）
        $yuyue_label = '';
        $yuyue_time = '';
        $min_yuyue_time = 0;
        if($row['yyst'] == 1 && $row['yuyue_day'] > 0 && !$row['pstime_type'] && $row['ps_time']){
            $now_time = __TIME;
            foreach ($row['ps_time'] as $k => $v) {
                $start_time = strtotime(date('Y-m-d ',$now_time).$v['stime']);
                if($now_time < $start_time){ 
                    $diff_time = $start_time - $now_time;                
                    if(!$min_yuyue_time){
                        $min_yuyue_time = $diff_time;
                        $yuyue_time = $v['stime'];
                    }else{
                        if($min_yuyue_time > $diff_time){
                            $min_yuyue_time = $diff_time;
                            $yuyue_time = $v['stime'];
                        }
                    }                    
                }
            }
            if($min_yuyue_time > 0 && $yuyue_time){
                $yuyue_label = sprintf('可预订%s后配送', $yuyue_time);
            }
        }

        $row['tips_label'] = '';
        if($yuyue_label){
            $row['tips_label'] = $yuyue_label;
        }else if($yyst_label){
            $row['tips_label'] = $yyst_label;
        }
        
        //$row['ziti_config'] = $row['ziti_config']?unserialize($row['ziti_config']):array();

        return $row;
    }

    public function format_data($row){

        if(self::$cate_list==null){
            self::$cate_list = K::M('waimai/cate')->fetch_all();
        }
        $city_ids = $shop_ids = array();
        $city_list =K::M('data/city')->items_by_ids($city_ids);
        
        $row['city_name'] = $city_list[$row['city_id']]['city_name'];
        if($cate = self::$cate_list[$row['cate_id']]){
            $row['cate_title'] = $row['cate_name'] = $cate['title'];
        }else{
            $row['cate_title'] = $row['cate_name'] = '';
        }
        $row['logo'] = $row['logo']?$row['logo']:'default/shop_logo.png';
        if($row['yuyue_day']==1){
            $row['str_yuyue_day'] = '当天';
        }else if($row['yuyue_day']==2){
            $row['str_yuyue_day'] = '明天';
        }else{
            $row['str_yuyue_day'] = $row['yuyue_day'].'天';
        }

        //首单-----------------------------------------------------------------------------------
        $filter_first = array('shop_id'=>$row['shop_id'], 'closed'=>0, 'audit'=>1, 'stime'=>'<:'.__TIME, 'ltime'=>'>:'.__TIME);
        $first = K::M('waimai/huodongfirst')->find($filter_first);
        if(!$first){
            $first_youhui['config'] =  array();
        }

        $first['title'] = $first['config'] ? "首单立减".$first['config']['first_amount']."元" : "";
        $first_title = $first['title'] ? $first['title'] : "";
        $row['first'] =$first['config'];
        $row['first']['type'] =  $first['type'];
        $row['first_title'] = $first_title;

        //满返-----------------------------------------------------------------------------------
        $manfan_filter = array('shop_id'=>$row['shop_id'], 'closed'=>0, 'audit'=>1, 'stime'=>'<:'.__TIME, 'ltime'=>'>:'.__TIME);
        $manfan = K::M('waimai/huodongmf')->find($manfan_filter);
        foreach($manfan['config'] as $k=>$v){
            if($v['paid_amount']&&$v['coupon_amount']){
                if($k==0){
                    $manfan['title'] = '实付满'.$v['paid_amount'].'元返'.$v['coupon_amount'].'元优惠券';
                }else{
                    $manfan['title'] .= ',实付满'.$v['paid_amount'].'元返'.$v['coupon_amount'].'元优惠券';
                }
            }
        }
        $manfan_title = $manfan['title'] ? $manfan['title'] : "";
        $row['manfan'] = $manfan;
        
        //满减-----------------------------------------------------------------------------------
        $manjian_filter = array('shop_id'=>$row['shop_id'], 'closed'=>0, 'audit'=>1, 'stime'=>'<:'.__TIME, 'ltime'=>'>:'.__TIME);
        $manjian = K::M('waimai/huodongmj')->find($manjian_filter);
        foreach($manjian['config'] as $k=>$v){
            if($v['order_amount']&&$v['coupon_amount']){
                if($k==0){
                    $manjian['title'] = '满'.$v['order_amount'].'减'.$v['coupon_amount'];
                }else{
                    $manjian['title'] .= ',满'.$v['order_amount'].'减'.$v['coupon_amount'];
                }
            }
        }
        $manjian_title = $manjian['title'] ? $manjian['title'] : "";
        $row['manjian'] = $manjian;
        
        //送券-----------------------------------------------------------------------------------        
        $coupon_filter = array('shop_id'=>$row['shop_id'], 'closed'=>0, 'audit'=>1, 'stime'=>'<:'.__TIME, 'ltime'=>'>:'.__TIME);
        $coupon = K::M('waimai/huodongcoupon')->find($coupon_filter);
        $num = 0;
        foreach($coupon['config'] as $k=>$v){
            $num += $v['coupon_amount'];
        }
        if($num > 0){
            $row['coupon'] = '进店领'.$num.'元优惠券';
            $coupon_title = '进店领'.$num.'元优惠券';
        }else{
            $row['coupon'] = "";
            $coupon_title = "";
        }

        //折扣商品
        $discount = K::M('waimai/huodongdiscount')->get_discount($row['shop_id']);      
        $row['discount'] = $discount;

        return $row;
    }
    
    public function create($data)
	{
		if(!$data = $this->_check($data)){
			return false;
		}
		$data['dateline'] = __CFG::TIME;
        if($this->db->insert($this->_table, $data)){
            return true;
        }else{
            return false;
        }
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
        }
        return parent::where($filter, $pre, $ANDOR);
    }
   
    protected function _check($row, $shop_id=null)
    {
        if(isset($row['passwd']) && !preg_match('/^[0-9a-f]{32}$/i', $row['passwd'])){
            if($shop_id && $row['passwd'] == '******'){
                unset($row['passwd']);
            }else{
                $row['passwd'] = md5($row['passwd']);
            }
        } 
        if(isset($row['mobile'])){
            $mobile = $row['mobile'];
            if($a = K::M('shop/shop')->shop($mobile, 'mobile')){
                if(empty($shop_id) || ($a['shop_id'] != $shop_id)){
                    $this->msgbox->add(L('手机号码已经存在'), 511);
                }
            }
        }
        if(isset($row['lat'])){
            $row['lat'] = round(bcmul($row['lat'], 1000000));
        } 
        if(isset($row['lng'])){
            $row['lng'] = round(bcmul($row['lng'], 1000000));
        }
        return parent::_check($row, $shop_id);
    }

    public function update_pei_distance($shop_id,$arr_fkm)
    {
        $arr_fkm =(int) max($arr_fkm);
        $pei_distance = round($arr_fkm)>0?round($arr_fkm):10;
        $update = array(
            'pei_distance' => $pei_distance
        );
        $is_update = K::M('waimai/waimai')->update($shop_id,$update);
    }



    // 商家配送设置提交字段验证（新版）
    public function check_field_shipping_fee($data=array())
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $k => $v) {
                if (!is_array($v) || empty($v)) {
                    return false;
                }else{
                    foreach ($v as $kk => $vv) {
                        if (!in_array($kk, array('min_price', 'shipping_fee', 'pei_amount','set_radius'))) {
                            return false;
                        }else{
                            $vv = round($vv, 2);
                            if ($vv < 0) {
                                return false;
                            }else{
                                $data[$k][$kk] = $vv;
                            }
                        }
                    }
                    
                }
            }
            return $data;
        }
        return false;
    }

    /**
     * 根据点坐标所在区域模板获取起送价、配送费
     * @param   array $area_polygon  区域模板数组
     * @param   float $lat           纬度
     * @param   float $lng           经度
     * @return  Array                包含起送价和配送费的一维数组，如果坐标存在多模板中，优先返回起送价价格最优的；
     *                               如果起送价相同，返回配送价最优的
     *                               如果点坐标不在模板中，返回false
     */
    public function get_shipping_fee($area_polygon, $lat, $lng)
    {
        $area_ids = array();
        if (!empty($area_polygon['polygon_point']) && !empty($area_polygon['area_price'])) {
            foreach ($area_polygon['polygon_point'] as $k => $v) {
                if (K::M('helper/round')->in_or_out_polygon($v, (float)$lat, (float)$lng)) {// 判断点坐标是否在一个几何多边形之内
                    $area_ids[$k] = $k;
                }
            }
            if (!empty($area_ids)) {
                $area_price = $_area_price = array();
                $equal = false;
                foreach ($area_ids as $k => $v) {
                    if ($_area_price && $area_polygon['area_price'][$k]['min_price'] !== $_area_price['min_price']) {// 前面保证了数据类型，这里全等判断
                        $equal = true;
                        break;
                    }elseif (empty($_area_price)) {
                        $_area_price['min_price'] = $area_polygon['area_price'][$k]['min_price'];
                    }
                }
                $type = $equal ? 'min_price' : 'shipping_fee';
                $contrary = $type === 'min_price' ? 'shipping_fee' : 'min_price'; // 取反
                
                foreach ($area_ids as $k => $v) {
                    if ($area_price && $area_polygon['area_price'][$k][$type] < $area_price[$type]) {
                        $area_price[$type] = $area_polygon['area_price'][$k][$type];
                        $area_price[$contrary] = $area_polygon['area_price'][$k][$contrary];
                    }elseif (empty($area_price)) {
                        $area_price[$type] = $area_polygon['area_price'][$k][$type];
                        $area_price[$contrary] = $area_polygon['area_price'][$k][$contrary];
                    }
                }
                return $area_price;
            }
        }

        return false;
    }

    /**
     * 根据用户收货地址坐标筛选配送模板
     * @param   array $addr_list     用户收货地址数组
     * @param   array $area_polygon  区域模板数组
     * @return  Array OR Boolean     数据错误返回false， 不在范围返回array()
     */
    public function get_shipping_fee_by_addrlist($addr_list, $area_polygon)
    {
        $area_price = $data = array();
        if (!empty($area_polygon['polygon_point']) && !empty($area_polygon['area_price'])) {
            foreach ($addr_list as $k => $v) {
                if ((float)$v['lat'] && (float)$v['lng']) {
                    if ($area_price = $this->get_shipping_fee($area_polygon, $v['lat'], $v['lng'])) {
                        $v['min_price'] = $area_price['min_price'];
                        $v['shipping_fee'] = $area_price['shipping_fee'];
                        $data[$k] = $v;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 递归函数，根据用户取收货地址在商家配送范围的地址
     * @param   array $uid           用户id
     * @param   array $area_polygon  区域模板数组
     * @return  Array                返回当前用户地址中在当前商家区域模板内的收货地址
     */
    public function get_shipping_fee_by_uid($uid, $area_polygon, &$data=array(), $i=0)
    {
        $i++;
        $addr_list = array();
        if($addr_list = K::M('member/addr')->items(array('uid'=>$uid), null, $i, 50)){// 直到分页取不到
            if ($items = $this->get_shipping_fee_by_addrlist($addr_list, $area_polygon)) {
                foreach ($items as $k => $v) {
                    $data[$k] = $v;
                }
            }
        }
        return $data;
    }
       
    //外卖商户后台推送消息
    public function send($shop_id, $title, $content, $type='order', $order_id=0, $sound='newMsg.mp3', $money='0', $name='')
    {
        if(in_array($type, array('newWaimaiOrder', 'newOrder'))){
            $type = 'order';
            $sound = 'newOrder.mp3';
        }else{
            $sound = 'newMsg.mp3';
        }
        K::M('jpush/device')->send_shop($shop_id, $title, $content, array('type'=>$type, 'order_id'=>(int)$order_id, 'sound'=>$sound, 'money'=>$money, 'name'=>$name));
        $a = array('shop_id'=>$shop_id, 'title'=>$title, 'content'=>$content);
        //0:所有消息 1:订单消息, 2:评价消息,3:投诉消息,4:系统消息 
        switch ($type) {
            case 'order': case 1:
                $a['order_id'] = (int)$order_id;
                $a['type'] = 1; break;
            case 'comment': case 2:
                $a['type'] = 2; break;
            case 'complaint': case 3:
                $a['type'] = 3; break;
            case 4:
                $a['type'] = 4; break; 
            default:
                $a['type'] = 0; break;
        }
        return K::M('shop/msg')->create($a);
    }

    public function check_pei_time($shop_id,$time){
        if(!$shop_id){
            return false;
        }else if($time== 0 ){
          return true;
        } else if(!$detail = $this->detail($shop_id)){
            return false;
        }else{
            //$pei_config_time = $detail['ps_time']?$detail['ps_time']:$detail['yy_peitime'];
            
            if($detail['pstime_type']){
                $pei_config_time = $detail['yy_peitime']; 
            }else{
                $pei_config_time = $detail['ps_time']?$detail['ps_time']:$detail['yy_peitime'];
            }
            $now_time =strtotime(date('Y-m-d ').date('H:i',$time));
            $now_week = date('w',$time);
            if ($pei_config_time && in_array($now_week, $detail['yy_weeks'])) {                
                foreach ($pei_config_time as $k => $v) {
                    $start_time = strtotime(date('Y-m-d ',$now_time).$v['stime']);
                    $end_time = strtotime(date('Y-m-d ',$now_time).$v['ltime']);
                    if(stristr($v['ltime'],'次日')){  //v3.6
                        $ltime = str_replace('次日', '', $v['ltime']);
                        $start_time1 = strtotime(date('Y-m-d ',$now_time).'00:00');
                        $end_time1 = strtotime(date('Y-m-d ',$now_time).$ltime);
                        $start_time2 = $start_time;
                        $end_time2 = strtotime(date('Y-m-d ',$now_time).'23:59');
                        if(($now_time >= $start_time1 && $now_time <= $end_time1) || ($now_time >= $start_time2 && $now_time <= $end_time2)){
                            return true;
                        }
                    }else{
                        if($start_time && $end_time) {
                            if($now_time >= $start_time && $now_time <= $end_time){
                                return true;
                            }
                        }
                    }                    
                }
                return false;
            }
            return false;
        }
       // get_day_time('00:00','23:59',$detail['yy_peitime']);
    }
    
    public function update_huodong_ltime($shop_id, $type)
    {
        if(!$shop_id = (int)$shop_id){
            return false;
        }elseif($type == 'manfan'){
            $type = 'mf';
        }elseif($type == 'manjian'){
            $type = 'mj';
        }elseif(!in_array($type, array('first', 'coupon', 'mj', 'mf', 'discount'))){
            return false;
        }
        if($row = K::M("waimai/huodong{$type}")->find(array('shop_id'=>$shop_id, 'audit'=>1, 'closed'=>0, 'ltime'=>'>:'.__TIME))){
            $_ltime = array("hd_{$type}_ltime"=>$row['ltime']);
        }else{
            $_ltime = array("hd_{$type}_ltime"=>0);
        }
        return $this->update($shop_id, $_ltime, true);
    }

    //设置外卖零元起送 免配送费
    public function after_set_min_zero($shop_id,$area_price,$true_price=null){
        if(!$shop_id){
            return false;
        }else if(!$detail = $this->detail($shop_id)){
            return false;
        }else{
            $arr1 = $arr2 = array();
            foreach($area_price as $k=>$v){
                $arr1[] = $v['min_price'];
                $arr2[] = $v['shipping_fee'];
            }
            sort($arr1);
            sort($arr2);
            $min_price = $arr1[0]?$arr1[0]:0;
            $shipping_fee = $arr2[0]?$arr2[0]:0;
            if($true_price){
                $shipping_fee =  $true_price['shipping_fee'];
            }
            return $this->update($shop_id,array('min_amount'=>$min_price,'freight'=>$shipping_fee));
        }
    }

    public function get_refuse_config(){
        return array(
            '卖家缺货',
            '打烊了',
            '没办法配送',
            '来不及做',
            '其他'
        );
    }

    //后台修改设置配送费时候修改最低配送费
    public function edit_shop_freight($freight = 0,$group_id=null){
        $where = $this->where(array('pei_type'=>array(1,2),'is_separate'=>0));
        if($group_id>0){
            $where = $this->where(array('pei_type'=>array(1,2),'is_separate'=>0,'group_id'=>$group_id));
        }
        $sql = "UPDATE ".$this->table($this->_table).' SET freight = '.$freight.' WHERE '.$where;
        return $this->db->Execute($sql);
    }

    //后台修改 同步更新商家当前配送站的最低配送费
    public function edit_shop_min_amount($group_id,$amount){
        if(!$group_id){
            return false;
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            return false;
        }else if((float)$amount<0){
            return false;
        }else{
            $where = $this->where(array('group_id'=>$group_id,'is_separate'=>0));
            $sql = "UPDATE ".$this->table($this->_table).' SET min_amount = '.$amount.' WHERE '.$where;
            return $this->db->Execute($sql);
        }
    }

    public function shipping_fee_by_type($cfg,$juli){

        $juli = ceil($juli/1000);
        $_freight = array();
        $fright_config = $cfg;
        $_max_freight = array('fkm'=>0, 'fm'=>0,'sm'=>0);
        foreach ($fright_config as $kk=>$vv){
            if($vv['fkm']==0){
                unset($fright_config[$kk]);
            }
        }
        foreach($fright_config as $k=>$v){
            if($juli <= $v['fkm']){
                if($_freight && $_freight['fkm'] > $v['fkm']){
                    $_freight = $v;
                }else if(empty($_freight)){
                    $_freight = $v;
                }
            }
            if($v['fkm'] > $_max_freight['fkm']){
                $_max_freight = $v;
            }
        }
        $p_amount = $_freight['fkm'] ? $_freight['fm'] : $_max_freight['fm'];
        return $p_amount;
    }

    public function formatTime($startTime, $lastTime, $space=15)
    {   
        //08:00 ~ 20:00
        $_result = array();
        $label = '';
        if(!($stime = (int)$startTime) && !isset($startTime)){
            $stime = 28800;
        }
        if(!$ltime = (int)$lastTime){
            $ltime = 72000;
        }
        $len = ($ltime - $stime)/($space*60);
        for($i = 0; $i <= $len; $i++){
            $a = $stime + $i*$space*60;
            $b = strtotime(date('Y-m-d')) + $a;
            $c = date('H:i',$b);
            switch ((int)($a/86400)) {
                case 0:
                    $label = '';
                    break;
                case 1:
                    $label = '次日';
                    break;
                case 2:
                    $label = '后日';
                    break;
                default:
                    $label = date('Y-m-d',$b);
                    break;
            }
            array_push($_result, array('time'=>$a, 'strtime'=>$label.' '.$c));          
        }
        return $_result;
    }

    public function items_join_shop($filter,$order_by = array(),$page=1,$limit = 50,&$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('shop')." w ON o.shop_id = w.shop_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            } 
            $sql =  "SELECT  o.*, w.`mobile` as 'mobile', w.`money` as 'money' FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('shop')." w ON o.shop_id = w.shop_id WHERE {$where} {$order_by} {$limit}";
           // print_r($sql);exit;
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row['shop_id']] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

    public function update_money($shop_id, $money, $intro, $admin='')
    {
        if(($shop_id = (int)$shop_id) && ($money = (float)$money)){
            $shop = $this->detail($shop_id);
            if($money > 0){
                $sql = "UPDATE ".$this->table($this->_table)." SET `deliver`=`deliver`+{$money}  WHERE shop_id='$shop_id'";
            }else if(($shop['deliver']+$money)>=0){
                $sql = "UPDATE ".$this->table($this->_table)." SET `deliver`=`deliver`+{$money} WHERE shop_id='$shop_id'";
            }else{
                return false;
            }
            if($this->db->Execute($sql)){
                $money_format =$shop['deliver']+$money;
                return K::M('shop/log')->create(array('shop_id'=>$shop_id, 'money'=>$money, 'intro'=>$intro, 'admin'=>$admin,'balance'=>$money_format));
            }
        }
        return false;
    }
   
    public function get_min_data($area_polygon, $min=true)
    {
        $data = array('min_price'=>0, 'shipping_fee'=>0);
        if (!empty($area_polygon['polygon_point']) && !empty($area_polygon['area_price'])) {
            
            $min_price = $freight = array();

            foreach ($area_polygon['area_price'] as $k => $v) {
                $min_price[] = $v['min_price'];
                $freight[] = $v['shipping_fee'];
            }

            if($min_price){
                if($min){
                    sort($min_price);
                }else{
                    arsort($min_price);
                }                
                $data['min_price'] = current($min_price);
            }

            if($freight){
                if($min){
                    sort($freight);
                }else{
                    arsort($freight);
                }                
                $data['shipping_fee'] = current($freight);
            }            
        }
        return $data;
    }

    public function get_min_data_by_group($group_id, $min=true)
    {
        $data = array('min_price'=>0, 'shipping_fee'=>0);
        if($group = K::M('pei/group')->detail($group_id)){
            $data['min_price'] = $group['min_amount'];
            $fm = array();
            foreach ($group['baseconfig'] as $k => $v) {
                $fm[] = $v['fm'];
            }
            if($fm){
                if($min){
                    sort($fm);
                }else{
                    arsort($fm);
                }
                $data['shipping_fee'] = current($fm);
            }
        }
        return $data;
    }

    //联表查询 jh_waimai o, jh_product w
    public function items_join_product($filter,$order_by=array(),$page=1, $limit=50, &$count=0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT count(DISTINCT o.`shop_id`) as count FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai_product')." w ON o.shop_id = w.shop_id WHERE {$where}";
        if($count = $this->db->GetOne($count_sql)){
            $sql =  "SELECT  o.* FROM ".$this->table($this->_table)." o INNER JOIN ".$this->table('waimai_product')." w ON o.shop_id = w.shop_id WHERE {$where} GROUP BY o.`shop_id` {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

    public function new_get_huodong($shop_ids){
        $huodong = array();
        $wordColor = $this->getHdWordColor();
        foreach ($wordColor as $k => $v) {
            $wordColor[$k]['color'] = 'FB564D';
        }
        if($shop_ids){
            //首单优惠
            $firsts = K::M('waimai/huodongfirst')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($firsts as $k=>$v){
                if($v['config']){
                    $title = sprintf("首单减%s", $v['config']['first_amount']);
                    $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['first']['word'], 'color'=>$wordColor['first']['color']);
                }
            }
            //满减优惠
            $manjian = K::M('waimai/huodongmj')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($manjian as $k=>$v){
                foreach($v['config'] as $k1=>$v1){
                    if($v1['order_amount'] && $v1['coupon_amount']){
                        $title = sprintf('%s减%s', $v1['order_amount'], $v1['coupon_amount']);
                        $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['manjian']['word'], 'color'=>$wordColor['manjian']['color']);
                    }
                }
            }
            //满返券
            $manfan = K::M('waimai/huodongmf')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($manfan as $k=>$v){
                foreach($v['config'] as $k1=>$v1){
                    if($v1['paid_amount']&&$v1['coupon_amount']){
                        $title = sprintf('返%s元券', $v1['coupon_amount']);
                        $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['manfan']['word'], 'color'=>$wordColor['manfan']['color']);
                    }
                }
            }
            //优惠券
            $coupons = K::M('waimai/huodongcoupon')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            foreach($coupons as $k=>$v){
                $num = 0;
                foreach($v['config'] as $k1=>$v1){
                    $num += $v1['coupon_amount'];
                }
                if($num>0){
                    $title = sprintf('领%s元券', $num);
                    $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['coupon']['word'], 'color'=>$wordColor['coupon']['color']);
                }
            }
            //折扣活动
            $discounts = K::M('waimai/huodongdiscount')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            $now_week = $now_week = date('w',__TIME);
            foreach($discounts as $k=>$v){
                if(!in_array($now_week,$v['period_weeks']) || strtotime($v['period_times']['stime']) >= __TIME || strtotime($v['period_times']['ltime']) <= __TIME){
                    continue;
                }else{
                    $title = $v['title'];
                    $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['discount']['word'], 'color'=>$wordColor['discount']['word']);
                }
            }
            //超值换购
            $huangous = K::M('waimai/huodonghuangou')->items(array('shop_id'=>$shop_ids,'audit'=>1,'closed'=>0,'stime'=>'<=:'.__TIME,'ltime'=>'>=:'.__TIME));
            $now_week = $now_week = date('w',__TIME);
            foreach($huangous as $k=>$v){
                if(!in_array($now_week,$v['period_weeks']) || strtotime($v['period_times']['stime']) >= __TIME || strtotime($v['period_times']['ltime']) <= __TIME){
                    continue;
                }else{
                    $title = $v['title'];
                    $huodong[$v['shop_id']][] = array('title'=>$title, 'word'=>$wordColor['huangou']['word'], 'color'=>$wordColor['huangou']['word']);
                }
            }
        }
        return $huodong;
    }

    public function getHdWordColor()
    {
        return array(
            'first'=>array('word'=>'首', 'color'=>'FF9000'),
            'manjian'=>array('word'=>'减', 'color'=>'FF4D5B'),
            'manfan'=>array('word'=>'返', 'color'=>'57C157'),
            'coupon'=>array('word'=>'券', 'color'=>'51ADFE'),
            'hongbao'=>array('word'=>'红', 'color'=>'FF4D5B'),
            'discount'=>array('word'=>'折', 'color'=>'C268D4'),
            'huangou'=>array('word'=>'换', 'color'=>'F27D23'),
            'peicard'=>array('word'=>'配', 'color'=>'19C5B1'),
            'card'=>array('word'=>'卡', 'color'=>'1D92F5'),
            );
    }

    //2019-01-26 新增 获取系统配置的虚拟商家ID列表，
    //    实际上是把/system/config.php下的__CFG::$CUSTOM_MANAGE_SHOP_IDS返回，如果没有设置，则返回空数组
    public function get_custom_mgr_shop_ids()
    {
        $ids = [];
        if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_IDS))
        {
            if(count(__CFG::$CUSTOM_MANAGE_SHOP_IDS) > 0)
            {
                foreach(__CFG::$CUSTOM_MANAGE_SHOP_IDS as $pid)
                {
                    $pid = (int)$pid;
                    if($pid > 0 && !isset($ids[$pid]))
                        $ids[$pid] = $pid;
                }
                $ids = array_merge($ids);
            }
        }
        return $ids;
    }

    
    //2019-01-26 新增 获取系统配置的虚拟商家分类ID列表，
    //    实际上是把/system/config.php下的__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS返回，如果没有设置，则返回空数组
    public function get_custom_mgr_shop_cate_ids()
    {
        $ids = [];
        if(property_exists("__CFG","CUSTOM_MANAGE_SHOP_CATEGORY_IDS") && is_array(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS))
        {
            if(count(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS) > 0)
            {
                foreach(__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS as $pid)
                {
                    $pid = (int)$pid;
                    if($pid > 0 && !isset($ids[$pid]))
                        $ids[$pid] = $pid;
                }
                $ids = array_merge($ids);
            }
        }
        return $ids;
    }

    //2019-01-26 新增 获取虚拟商家ID列表
    //    会使用/system/config.php下的__CFG::$CUSTOM_MANAGE_SHOP_CATEGORY_IDS和/system/config.php下的__CFG::$CUSTOM_MANAGE_SHOP_IDS共同作用，从数据表jh_waimai中获得相应的商家ID列表
    public function get_custom_mgr_shop_real_ids()
    {
        $arrWhere = [];
        $ids = $this->get_custom_mgr_shop_ids();
        if(count($ids) === 1)
            $arrWhere[] = "`shop_id`={$ids[0]}";
        else if(count($ids) > 0)
            $arrWhere[] = "`shop_id` IN(".implode(',', $ids).")";
        $ids = $this->get_custom_mgr_shop_cate_ids();
        if(count($ids) === 1)
            $arrWhere[] = "`cate_id`={$ids[0]}";
        else if(count($ids) > 0)
            $arrWhere[] = "`cate_id` IN(".implode(',', $ids).")";
        $ids = [];
        if(count($arrWhere) > 0)
        {
            $whereSql = implode(" OR ", $arrWhere);
            $rows = $this->db->select($this->_table,'shop_id',$whereSql)->fetch_all();
            if($rows)
            {
                foreach($rows as &$row)
                {
                    $ids[] = (int)$row['shop_id'];
                    unset($row);
                }
            }
        }
        return $ids;
    }

    //2019-01-26 新增 判断指定ID是不是虚拟商家
    private static $_isCustomMgrShopIdCacheForCates = [];   //由分类查到属于属于虚拟商家的ID列表，用于多次查询时不必每次都进行数据库操作，加快速度
    private static $_notCustomMgrShopIdCacheForCates = [];  //同上，但属于不是虚拟商家的ID缓存列表
    public function is_custom_mgr_shop($shop_id)
    {
        $shop_id = (int)$shop_id;
        if($shop_id <= 0)
            return false;
        $ids = $this->get_custom_mgr_shop_ids();
        if((count($ids) > 0 && in_array($shop_id, $ids)) || isset(self::$_isCustomMgrShopIdCacheForCates[$shop_id]))
            return true;
        if(isset(self::$_notCustomMgrShopIdCacheForCates[$shop_id]))
            return false;
        $ids = $this->get_custom_mgr_shop_cate_ids();
        if(count($ids) === 0)
            return false;
        $exSQl = "";
        if(count($ids) === 1)
            $exSQl = "`cate_id`={$ids[0]}";
        else
            $exSQl = "`cate_id` IN(".implode(",", $ids).")";
        if((int)$this->count("`shop_id`={$shop_id} AND {$exSQl}") > 0)
        {
            self::$_isCustomMgrShopIdCacheForCates[$shop_id] = $shop_id;
            return true;
        }
        else
            self::$_notCustomMgrShopIdCacheForCates[$shop_id] = $shop_id;
        return false;
    }
}
