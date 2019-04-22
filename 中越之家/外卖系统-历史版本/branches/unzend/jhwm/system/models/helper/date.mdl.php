<?php
/**
 * Copy	Right Anhuike.com
 * $Id date.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Helper_Date
{
	/**
     * 计算2个时间之间的月份数（不论前后顺序） {int}
     * @param   $st_time   开始时间（亦可截止时间）
     * @param   $end_time  截止时间（亦可开始时间）
     * @return int   
     */
	public function get_month_count($st_time=0, $end_time=0)
	{
		$part = 0;
		$max_time = 2145888000; // 时间戳即将溢出值,防止后面溢出死循环 2038-01-01 00:00:00 (UTC)
		if (($st_time = (int) $st_time) && ($end_time = (int) $end_time) && $st_time < $max_time && $end_time < $max_time) {
			if ($st_time > $end_time) {
				$_st_time = $st_time;
				$st_time = $end_time;
				$end_time = $_st_time;
			}
			$st_time = date('Y-m', $st_time);// 不加 d 从 1号计算， 此处要与 max_time 计算一致
			$dateUnit = 'month';
		    while(true){
		    	if ($nextDate = date('Y-m-d', strtotime("$st_time + $part $dateUnit"))) {//得到下一个具体的日期
		    		$next_time = strtotime("$nextDate");
		    		if($next_time > $end_time){// 前面保证了 next_time 值不会溢出
			            break;
			        }
			        $part++;
		    	}
		    }
		}
	    return $part;
	}

	/**
     * 获取2个时间之间的月份数组（不论前后顺序） {int}
     * @param   $st_time   开始时间（亦可截止时间）
     * @param   $end_time  截止时间（亦可开始时间）
     * @return  Array      array value is timestamp
     */
	public function get_date_list($st_time=0, $end_time=0)
	{
		$date_list = array();
        if ($month_count = $this->get_month_count($st_time, $end_time)) {
        	$firstday = date("Y-m-01");
        	$date_list = array(0 => $firstday);
	        for ($i=1; $i < $month_count; $i++) {
	            $firstday = date("Y-m-d",strtotime("$firstday -1 month"));
	            $date_list[$i] = $firstday;
	        }
	       	foreach ($date_list as $k => $v) {
	       		$date_list[$k] = strtotime($v);
	       	}
        }
        return $date_list;
	}

	//获取天数
	public function get_day_list($mouth){
		$day = array();
		if($mouth){
			$start_time = date('Y-m-01', strtotime($mouth));  //获取本月第一天时间戳
			$end_time = date('Y-m-d', strtotime("$start_time +1 month"));
			$date = range(0,31);
		    foreach ($date as $v){
				if((strtotime($start_time)+($v*86400))<(strtotime($end_time)-1)){
					$day[] = date('Ymd',(strtotime($start_time)+($v*86400)));
				}

			}
		}
		return $day;
	}

	/*
	 * 获取当前月天数 或 指定1 ~ 31天 日期数组 (图表X轴) 
	 * @param date $unix
	 * return Array
	 */
	public function get_days($unix){
		$day_list = array();
		if ($unix = (int) $unix) {
			if ($unix <= 31) {
				if ($begin_time = strtotime(date('Y-m-d')."-{$unix} day")) {// 起始日
					for ($i=0; $i < $unix; $i++) {
				    	$day_list[] = date("m-d", $i*86400+$begin_time);
				    }
				}
			}else{
				$month = date('m', $unix);
			    $year = date('Y', $unix);
			    $nextMonth = (($month+1)>12) ? 1 : ($month+1);
			    $year = ($nextMonth>12) ? ($year+1) : $year;
			    $days = date('d',mktime(0,0,0,$nextMonth,0,$year));
			    for ($i=1; $i <= $days; $i++) {
			    	$day_list[] = date("{$month}-{$i}");
			    }
			}
		}
        return $day_list;
	}

	public function get_diff_time_array($stime,$ltime){
	   $date = array();
	   if(($ltime-$stime)<86400){
	       //一天范围内
          for ($i=57600;$i<=144000;$i=$i+3600){
              $date[date("H:i",$i)] =  date("H",$i);
          }
           $date['step'] = "hour";
           $date['data'] = $date;
	   }

    }




    public function get_daysone($unix, $format=null, $contain=true){
        $format = !isset($format) || empty($format) ? 'Ymd' : $format;
        $day_list = array();
        if ($unix === 'l') { // 本周第一天至本周最后一天
            $begin = $this->week_first_day('this week');
            for ($i=0; $i < 7; $i++) {
                $t = strtotime("{$begin} +{$i} day");
                $day_list[date('Ymd', $t)] = date($format, $t);
            }
            return $day_list;
        }elseif ($unix === 'm') {
            // list($y, $m) = explode("-", date('Y-m'));
            // $days = cal_days_in_month(CAL_GREGORIAN, $m, $y); 不可用
            $days = (int)date("t",strtotime(date('Y-m'))); // cal_days_in_month 替代方法
            $begin = date('Ym01');
            for ($i=0; $i < $days; $i++) {
                $t = strtotime("{$begin} +{$i} day");
                $day_list[date('Ymd', $t)] = date($format, $t);
            }
            return $day_list;
        }

        $date_list = $this->get_date_list(1357248000, __TIME); // 2013/1/4 ~ now
        if ($unix = abs((int) $unix)) {
            if ($unix <= 31 && $unix > 1) {
                $begin = $contain ? date('Ymd', __TIME-($unix-1)*86400) : date('Ymd', __TIME-($unix)*86400);;
                for ($i=0; $i < $unix; $i++) {
                    $t = strtotime("{$begin} +{$i} day");
                    $day_list[date('Ymd', $t)] = date($format, $t);
                }
            }elseif (date('Ymd') == $unix) {                      // 今日

            }elseif (date('Ymd', strtotime("-1 day")) == $unix) { // 昨日

            }elseif ($date_list[$unix]) {
                $month = date('m', $unix);
                $year = date('Y', $unix);
                $nextMonth = (($month+1)>12) ? 1 : ($month+1);
                $year = ($nextMonth>12) ? ($year+1) : $year;
                $days = date('d',mktime(0,0,0,$nextMonth,0,$year));
                for ($i=1; $i <= $days; $i++) {
                    $t = strtotime(date("{$year}-{$month}-{$i}"));
                    $day_list[date('Ymd', $t)] = date($format, $t);
                }
            }
        }
        return $day_list;
    }

	public function mouth($stime,$ltime){
		$time1 = $stime; // 自动为00:00:00 时分秒 两个时间之间的年和月份
		$time2 = $ltime;
		$monarr = array();
		while( ($time1 = strtotime('+1 month', $time1)) <= $time2){
			$monarr[] = date('Y-m-1',$time1);

		}
		$monarr[] = date('Y-m-1'); // 当前月;
		return  array_reverse($monarr);

	}

	//BD-09(百度) 坐标转换成  GCJ-02(火星，高德) 坐标
	//@param bd_lon 百度经度
	//@param bd_lat 百度纬度
	function bd_decrypt($bd_lon,$bd_lat)
	{
        $data['gg_lon'] = $bd_lon;
        $data['gg_lat'] = $bd_lat;
		return $data;
	}

	//GCJ-02(火星，高德) 坐标转换成 BD-09(百度) 坐标
	//@param bd_lon 百度经度
	//@param bd_lat 百度纬度
	function bd_encrypt($gg_lon,$gg_lat)
	{

        $data['bd_lon'] = $gg_lon;
        $data['bd_lat'] = $gg_lat;
		return $data;
	}

    public function get_date_filter($time, $contain=true)
    {
        $date = array();
        $date_obj = new DateTime();
        $td = date('Ymd');
        $yd = date('Ymd', strtotime("-1 day"));
        $date_list = K::M('helper/date')->get_date_list(1357248000, __TIME); // 2013/1/4 ~ now
        if ($time === "l") { // 指本周，本周第一天到最后一天
            $begin_date = K::M('helper/date')->week_first_day('this week');
            $end_date = date('Ymd', strtotime("{$begin_date} +1 week -1 day"));
            $date = array('k'=>'week', 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
            return $date;
        }elseif (!isset($time) || $time === 'm') { // 本月
            $end_date = $contain && date('Ym01') != $td ? $td : $yd;
            return array('k'=>'month', 'v'=>date('Ym01').'~'.$end_date, 't'=>strtotime(date('Ym01')).'~'.(strtotime($end_date)+86399));
        }elseif ($time === "q") { // 指本季度（近三个月）
            $begin_date = $contain ? $date_obj->modify('-3 month')->format('Ymd') : $date_obj->modify('-3 month -1 day')->format('Ymd'); // 90天
            $end_date = $contain ? $td : $yd;
            return array('k'=>'quarter', 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
        }elseif ($time === "M") { // 近一月
            $begin_date = $contain ? $date_obj->modify('-1 month')->format('Ymd') : $date_obj->modify('-1 month -1 day')->format('Ymd');
            $end_date = $contain ? $td : $yd;
            return array('k'=>'Month', 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
        }
        if (isset($time) && $time = abs((int) $time)) {
            // 判断查询类型（今日、昨日、指定天数、一整个月、一年）
            if ($time <= 31 && $time > 1) {                       // 指定天数
                $begin_date = $contain ? date('Ymd', __TIME-($time-1)*86400) : date('Ymd', __TIME-($time)*86400);
                $end_date = $contain ? $td : $yd;
                $date = array('k'=>"{$time}", 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
            }elseif (date('Ymd') == $time) {                      // 今日
                $date = array('k'=>'today', 'v'=>$time, 't'=>(strtotime($time)+86399));
            }elseif (date('Ymd', strtotime("-1 day")) == $time) { // 昨日
                $date = array('k'=>'yesterday', 'v'=>$time, 't'=>(strtotime($time)+86399));
            }elseif(!empty($date_list[$time])){                   // 指定月
                $begin_date = date('Ymd', $date_list[$time]);
                $end_date = date('Ymd', strtotime("{$begin_date} +1 month -1 day"));
                $date = array('k'=>'month', 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
            }elseif($time == 365){                                // 一年
                $begin_date = $contain ? date('Ymd', strtotime(date('Ymd')."-1 year")) : date('Ymd', strtotime(date('Ymd')."-1 year -1 day"));
                $end_date = $contain ? $td : $yd;
                $date = array('k'=>'year', 'v'=>$begin_date.'~'.$end_date, 't'=>strtotime($begin_date).'~'.(strtotime($end_date)+86399));
            }
        }
        return $date;
    }

    public function week_first_day($string, $format='Ymd')
    {
        $date = new DateTime();
        if ($date->format('N') == 7) {
            $matches = array();
            $pattern = '/this week|next week|previous week|last week/i';
            if (preg_match($pattern, $string, $matches)) {
                $string = str_replace($matches[0], '-7 days '.$matches[0], $string);
            }
        }
        return $date->modify($string)->format($format);
    }


    public function get_arr_by_type($st_time=0, $end_time=0,$type = 'd'){
        $arr = array();
        switch ($type) {
            case 'd':
                $st_time = $st_time+86399;
                $days = ceil(($end_time - $st_time) / 86400)+1;
                $begin = date('Ymd', $end_time-($days-1)*86400);
                for ($i=0; $i < $days; $i++) {
                    $t = strtotime("{$begin} +{$i} day");
                    $v = date('m/d', $t);
                    $k =(int)date('Ymd', $t);
                    $arr[$k] = $v;
                }
                break;
            case "h";
                for ($i=57600;$i<=144000;$i=$i+3600){
                    $arr[(int)date("H",$i)] =  date("H:i",$i);
                }
                break;

        }
        return $arr;

    }



    public function date_group($st_time=0, $end_time=0,&$group_rule){
        $arr = array();
        if (($st_time = abs((int) $st_time)) && ($end_time = abs((int) $end_time))) {
            $group_rule = $this->date_group_rule($st_time, $end_time);
            switch ($group_rule) {
                case 'y':
                    $arr[date('Y',$st_time)] = date('Y',$st_time);
                    while( ($st_time = strtotime('+1 year', strtotime(date('Y-m', $st_time)))-1) < $end_time){
                        $arr[date('Y',$st_time)] = date('Y',$st_time);
                    }
                    break;
                case 'm':
                    $st_time = strtotime(date('Y-m', $st_time)); // strtotime 操作月份需要先转为月首（比如8/31 + 1month = 10/1）
                    $arr[date('Ym',$st_time)] = date('Ym',$st_time);
                    while( ($st_time = strtotime('+1 month', $st_time)) <= $end_time){
                        $arr[date('Ym',$st_time)] = date('Y-m',$st_time);
                    }
                    break;
                case "h";
                    for ($i=57600;$i<=144000;$i=$i+3600){
                        $arr[date("H",$i)] =  date("H:i",$i);
                    }
                   break;
                default:
                    $days = ceil(($end_time - $st_time) / 86400)+1;
                    $begin = date('Ymd', $end_time-($days-1)*86400);
                    for ($i=0; $i < $days; $i++) {
                        $t = strtotime("{$begin} +{$i} day");
                        $v = date('m-d', $t);
                        $k = date('Ymd', $t);
                        $arr[$k] = $v;
                    }
                    $group_rule = "d";
                    break;
            }
        }
        return $arr;
    }



    public function date_group_rule($st_time=0, $end_time=0)
    {
        $str = "";
        if (($st_time = abs((int) $st_time)) && ($end_time = abs((int) $end_time))) {
            if ($st_time > $end_time) {
                $_st_time = $st_time;
                $st_time = $end_time;
                $end_time = $_st_time;
            }
            if (strtotime('+1 year', strtotime(date('Y-m', $st_time)))-1 < $end_time) { // 超出一年按年分组
                $str = "y";
            }elseif (ceil(($end_time - $st_time) / 86400) > 31) { // 超出31天按月分组
                $str = "m";
            }else if(($end_time-$st_time)<86400){
                $str = 'h';
            }
        }
        return $str;
    }










}