<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Data_Tongji extends Mdl_Table
{
    protected $_from = array('waimai','mall');

    // 获取图表数据（暂时只支持整月每天 或 指定1~31天内 统计）
    public function get_chart_totime($time)
    {
        $data = array();
        if ($date = $this->get_date_filter($time)) {
            $common_data = $this->get_common_data($time);
            if ($days_count = count($common_data['chart_days'])) {
                // 初始化
                $day_init = array();
                for ($i=0; $i < $days_count; $i++) {
                    $day = date('Ymd', strtotime("{$common_data['begin_date']} +{$i} day"));
                    $day_init[$day] = array();
                }
                $count_list = $this->yytj_count_list(array('bills_sn'=>$date)); // 获取统计求和 (不判断，循环中初始化)
                foreach ($day_init as $k => $v) {
                    // 曲线图数据
                    $data['curve_yyamount'][] = $count_list[$k] ? $count_list[$k]['yyamount'] : 0;
                    $data['curve_pingtai_amount'][] = $count_list[$k] ? $count_list[$k]['pingtai_amount'] : 0;
                }
                // 获取当前查询的时间段营业数据 （展示板数据）
                /*foreach ($this->get_amount($date) as $k => $v) {
                    $data["{$k}_amount"] = $v['amount'];
                    $data['show_yyamount'] += $v['amount'];
                    $data['show_pingtai_amount'] += $v['fee'];
                }*/
            }
            $data['items'] = array_values($this->get_amount($date));
            $data['yyamount_count'] = $this->get_yyamount_count($data['items']);
            $data['categories'] = $this->get_from();
            $data['intro'] = $common_data['intro'];
            $data['chart_days'] = $common_data['chart_days']; // X轴 时间线
        }
        return $data;
    }

    // 获取图表订单量数据（暂时只支持整月每天 或 指定1~31天内 统计）
    public function get_chart_order_totime($time)
    {
        $data = array();
        if (($date = $this->get_date_filter($time)) && ($common_data = $this->get_common_data($time))) {
            if ($days_count = count($common_data['chart_days'])) {
                // 初始化
                $day_init = array();
                for ($i=0; $i < $days_count; $i++) {
                    $day = date('Ymd', strtotime("{$common_data['begin_date']} +{$i} day"));
                    $day_init[$day] = array();
                }
                // 查询条件
                $map['day'] = $date;
                $map['order_status'] = 8; // 已完成
                $map[':SQL'] = "(`from` IN('".(implode("','", $this->_from)."'")."))";
                $limit = count($this->_from) * 31; // 所有的可能性为订单类型数 * 月最大天数

                $count_list = $this->order_count_list($map, 1, $limit); // 获取订单量统计 (不判断，循环中初始化)
                foreach ($day_init as $k => $v) {
                    // 曲线图数据
                    foreach ($this->_from as $vv) {
                        $data["curve_{$vv}_yorder"][] = (int)$count_list[$k][$vv] ? (int)$count_list[$k][$vv] : 0; //  这里赋值的值必须是整型，否则前台不展示数据
                    }
                }
                // 获取当前查询的时间段营业数据 （展示板数据）
                foreach ($this->get_yorder($date) as $k => $v) {
                    $data["{$k}_yorder"] = $v;
                }
                $data['intro'] = $common_data['intro'];
                $data['chart_days'] = $common_data['chart_days']; // X轴 时间线
            }
        }
        return $data;
    }

    // 获取日期筛选的条件
    private function get_date_filter($time=7)
    {
        $date = "";
        if ($time = (int) $time) {
            if ($date_list = $this->format_date_list()) {// 获取第一笔订单至今 经历的月份数组
                // 判断查询类型（一周、指定天数 还是 一整个月）
                if ($time <= 31) {
                    $day_count = $time - 1;
                    $begin_date = date('Ymd', __TIME-$time*86400);
                    $date = $begin_date.'~'.date('Ymd', strtotime("{$begin_date} +{$day_count} day"));
                }elseif(!empty($date_list[$time])){
                    $begin_date = date('Ymd', $date_list[$time]);
                    $date = $begin_date.'~'.date('Ymd', strtotime("{$begin_date} +1 month -1 day"));
                }elseif (date('Ymd') == $time || date('Ymd', __TIME-86400) == $time) { // 今日 || 昨日
                    $date = $time;
                }
            }
        }
        return $date;
    }

    // 展示数据
    private function get_common_data($time=7)
    {
        $data = array('begin_date'=>"", "intro"=>"", 'chart_days'=>array());
        if ($time = (int) $time) {
            if ($date_list = $this->format_date_list()) {// 获取第一笔订单至今 经历的月份数组
                // 判断查询类型（一周、指定天数 还是 一整个月）
                if ($time <= 31) {
                    $data['begin_date'] = date('Ymd', __TIME-$time*86400);
                    $data['intro'] = $time == 7 ? "本周营收统计" : "近".$time."天营收统计";
                    $data['chart_days'] = K::M('helper/date')->get_days($time);
                }elseif(!empty($date_list[$time])){
                    $data['begin_date'] = date('Ymd', $date_list[$time]);
                    $data['intro'] = date('Y-m', $time)." 月营收统计";
                    $data['chart_days'] = K::M('helper/date')->get_days($date_list[$time]);
                }elseif (date('Ymd') == $time) { // 今日
                    $data['intro'] = "今日营收统计";
                }elseif (date('Ymd', __TIME-86400) == $time) { // 昨日
                    $data['intro'] = "昨日营收统计";
                }
            }
        }
        return $data;
    }

    protected function format_date_list()
    {
        $date_list = array();
        if ($items = $this->get_date_list()) {// 获取第一笔订单至今 经历的月份数组
            foreach ($items as $k => $v) {
                $date_list[$v] = $v;
            }
        }
        return $date_list;
    }

    // 营业统计求和公共处理方法
    protected function yytj_count_list($filter)
    {
        $data = $items = array();
        foreach ($this->_from as $v) {
            if ($count_list[$v] = $this->count_by_day($filter, $v)) { // 根据天统计对账单表
                foreach ($count_list[$v] as $kk => $vv) {
                    if ($items[$kk] == $kk) {
                        $items[$kk][$v] = $vv;
                    }elseif (empty($items[$kk][$v])) {
                        $items[$kk][$v] = $vv;
                    }
                }
            }
        }
        if (!empty($items)) {
            foreach ($items as $k => $v) {
                foreach ($v as $kk => $vv) {
                    $data[$k]['yyamount'] += bcadd($vv['day_shop_amount'], $vv['day_fee'], 2);
                    $data[$k]['pingtai_amount'] += $vv['day_fee'];
                }
            }
        }
        return $data;
    }

    /**
     * 订单量统计求和处理方法
     * param $filter   查询条件
     */
    protected function order_count_list($filter)
    {
        $data = array();
        if ($items = K::M('order/order')->order_count_by_day($filter)) { // 根据天 和 订单类型统计订单量
            foreach ($items as $v) { 
                if ($data[$v['day']] == $v['day']) {
                    $data[$v['day']][$v['from']] = $v['day_orders'];
                }elseif (empty($data[$v['day']][$v['from']])) {
                    $data[$v['day']][$v['from']] = $v['day_orders'];
                }
            }
        }
        return $data;
    }

    /**
     * 根据天统计对账单表
     * param $from   要查询的对账单表 (waimai、tuan、weidian、maidan......)
     * param $limit  条数 (一个月的天数)
     */
    public function count_by_day($filter=null,$from='waimai',$page=1,$limit=31)
    {
        $where = $this->where($filter);
        $limit = $this->limit($page, $limit);
        $sql = "SELECT `bills_sn`, COUNT(1) as day_bills, SUM(`amount`) as day_shop_amount, SUM(`fee`) as day_fee FROM ".$this->table("{$from}_bills")." WHERE {$where} GROUP BY `bills_sn` ORDER BY bills_sn ASC $limit";
        $items = array();
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row['bills_sn']] = $row;
            }
        }
        return $items;
    }

    /**
     * 根据日期条件获取营业数据
     * param date(Ymd)  $date   查询日期（Ymd格式）
     */
    public function get_amount($date)
    {
        $data = array();
        foreach ($this->_from as $v) {
            $data["{$v}"]['shop_amount'] = (float) K::M("{$v}/bills")->sum(array("bills_sn"=>$date), 'amount');
            $data["{$v}"]['fee'] = (float) K::M("{$v}/bills")->sum(array("bills_sn"=>$date), 'fee');
            $data["{$v}"]['name'] = $this->get_from_title($v);
            $data["{$v}"]['y'] = (float) bcadd($data["{$v}"]['shop_amount'], $data["{$v}"]['fee'], 2); //当前分类下的营业收入
        }
        return $data;
    }

    /**
     * 根据日期条件获取有效的订单
     * param Date(Ymd)  $day   查询日期 (Ymd 格式)
     */
    public function get_yorder($day)
    {
        $data = array();
        foreach ($this->_from as $v) {
            $data["{$v}"] = K::M('order/order')->count(array('from'=>"{$v}", 'day'=>$day, 'order_status'=>8));
        }
        return $data;
    }

    // 第一笔订单至今 经历的月份数组
    public function get_date_list()
    {
        $first_order = current(K::M('order/order')->items(array('order_status'=>8, ':SQL'=>"(`from` IN('waimai','tuan','weidian','maidan','mall'))"), array('order_id'=>'ASC')));// 第一笔订单
        $begin_date = $first_order['lasttime'] ? $first_order['lasttime'] : 1483200000;
        return K::M('helper/date')->get_date_list($begin_date, __TIME);
    }

    protected function get_from_title($from)
    {
        switch ($from) {
            case 'waimai':
                $title = "外卖";
                break;
            case 'tuan':
                $title = "团购";
                break;
            case 'weidian':
                $title = "商城";
                break;
            case 'maidan':
                $title = "买单";
                break;
            case 'mall':
                $title ='积分商城';
                break;
            case 'qiang':
                $title ='抢购';
                break;
            default:
                $title = "";
                break;
        }
        return $title;
    }

    protected function get_from()
    {
        $items = array();
        foreach ($this->_from as $v) {
            $items[] = $this->get_from_title($v);
        }
        return $items;
    }

    protected function get_yyamount_count($items)
    {
        $count = array();
        foreach ($items as $k => $v) {
            $count['yyamount'] += $v['y']; // 总营业收入
            $count['fee'] += $v['fee'];    // 平台收入
        }
        return $count;
    }
}