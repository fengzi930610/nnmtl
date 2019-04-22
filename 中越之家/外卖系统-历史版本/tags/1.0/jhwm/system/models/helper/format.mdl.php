<?php

/**

 * Copy Right Anhuike.com

 * $Id format.mdl.php shzhrui<anhuike@gmail.com>

 */

class Mdl_Helper_Format

{



    /**

     * 格式化输出字节数 {GB,MB,KB,Bytes}

     * @param   $size   字节数单位bytes

     * @return string

     */

    static public function size($size)

    {

        if($size >= 1073741824) {

            $size = round($size / 1073741824 * 100) / 100 . ' GB';

        } elseif($size >= 1048576) {

            $size = round($size / 1048576 * 100) / 100 . ' MB';

        } elseif($size >= 1024) {

            $size = round($size / 1024 * 100) / 100 . ' KB';

        } else {

            $size = $size . ' Bytes';

        }

        return $size;

    }

    static public function price($price)

    {



    }

    static public function juli($juli)

    {

        //return ($juli < 1000) ? (round($juli, 2).'m') : round(intval($juli/1000).'.'.($juli%1000), 2).'km';

        if($juli < 100){

            return '<100m';

        }else{

            return($juli < 1000) ?(round($juli, 0).'m') : round(($juli/1000), 1).'km';//2017/04/06

        }



    }

    static public function time($time, $format='')

    {



        if($format){

            return date($format,$time);

        }

        $s = date('Y-m-d H:i:s',$time);

        $sdaytime = K::$system->sdaytime;

        $stime = __CFG::TIME - $time;

        if($time >= $sdaytime){ //当天

            if($stime > 3600) {

                return '<span title="'.$s.'">'.intval($stime / 3600).'小时前</span>';

            } elseif($stime > 1800) {

                return '<span title="'.$s.'">半小时前</span>';

            } elseif($stime > 60) {

                return '<span title="'.$s.'">'.intval($stime / 60).'分钟前</span>';

            } elseif($stime > 0) {

                return '<span title="'.$s.'">'.$stime.'秒前</span>';

            } elseif($stime == 0) {

                return '<span title="'.$s.'">刚刚</span>';

            } else {

                return '<span title="'.$s.'">'.$s.'</span>';

            }

        }else if(($days = intval($stime / 86400)) >= 0 && $days < 7){

            if($days == 0) {

                return '<span title="'.$s.'">昨天&nbsp;'.date("H:i", $time).'</span>';

            } elseif($days == 1) {

                return '<span title="'.$s.'">前天&nbsp;'.date("H:i", $time).'</span>';

            } else {

                return '<span title="'.$s.'">'.($days + 1).'天前</span>';

            }

        }else if(empty($time)){

            return '<span title="'.$s.'">0</span>';

        }else{

            return '<span title="'.$s.'">'.$s.'</span>';

        }

    }

    static public function microtime($time=0)

    {

        $time = $time ? $time : explode(' ',microtime());

        return date('Y-m-d H:i:s',$time[0]).",{$time[1]}毫秒";

    }

    public function overturn($data,$type= true){
        $return_data = array();
        if($type){
            $key = array_keys($data);
            foreach ($data as $k1=>$v1){
                foreach ($v1 as $k2=>$v2){
                    $return_data[$k2] = array();
                    foreach ($key as $k3=>$v3){
                        $return_data[$k2][$v3] =$data[$v3][$k2];
                    }
                }
            }
            return $return_data;
        }else{

        }

    }

    //v3.6 次日
    public function morrowTime($startTime, $lastTime, $space=15)
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
        $m = ($ltime - $stime)%($space*60);
        for($i = 0; $i <= $len; $i++){
            $a = $stime + $i*$space*60;
            $b = strtotime(date('Y-m-d')) + $a;
            $c = date('H:i',$b);
            switch ((int)($a/86400)) {
                case 0:
                    $label = '';
                    break;
                case 1:
                    $label = '次日 ';
                    break;
                case 2:
                    $label = '后日 ';
                    break;
                default:
                    $label = date('Y-m-d',$b);
                    break;
            }
            array_push($_result, array('time'=>$a, 'strtime'=>$label.$c));
            if($i == (int)$len && $m > 60){
                $a = $stime + $i*$space*60 + $m;
                $b = strtotime(date('Y-m-d')) + $a;
                $c = date('H:i',$b);
                array_push($_result, array('time'=>$a, 'strtime'=>$label.$c));
            }          
        }
        return $_result;
    }

    public function checkTime($time)
    {
        if($time){
            if((strpos($time,'次日') === false) && (!preg_match('/^\d{1,2}\:(\d{2}|\d{2}\:\d{2})$/i', $time))){
                return false;
            }else if(!preg_match('/^\d{1,2}\:(\d{2}|\d{2}\:\d{2})$/i', trim(str_replace('次日','',$time)))){
                return false;
            }else{
                return trim($time);
            }
        }
        return false;
    }

    public function checkTimes($stime, $ltime)
    {
        if($stime && !$stime = $this->checkTime($stime)){
            return false;
        }else if($ltime && !$ltime = $this->checkTime($ltime)){
            return false;
        }else if(($stime && $ltime) && (strpos($ltime,'次日') === false) && (strtotime($stime) >= strtotime($ltime))){
            return false;
        }else{
            return true;
        }
    }

    /*public function formatTime($row)
    {
        $now_time = strtotime(date('Y-m-d '));
        $stime_time = strtotime($row['stime']) - $now_time;        
        if(stristr($row['ltime'],'次日')){
            $ltime = str_replace('次日', '', $row['ltime']);
            $ltime_time = strtotime($ltime) - $now_time + 86400;
        }else{
            $ltime_time = strtotime($row['ltime']) - $now_time; 
        }
        $row['stime_time'] = max($stime_time, 0);
        $row['ltime_time'] = max($ltime_time, 0);
        return $row;
    }*/

    public function format_morrowTime($strtime)
    {
        $now_time = strtotime(date('Y-m-d '));
        $time = 0;
        if($strtime){
            if(stristr($strtime,'次日')){
                $a = str_replace('次日', '', $strtime);
                $time = strtotime($a) - $now_time + 86400;
            }else{
                $time = strtotime($strtime) - $now_time; 
            }
        }        
        $time = max($time, 0);
        return $time;
    }


    public function get_bl($y_amounts,$yy_amounts){
        if($y_amounts==0 && $yy_amounts>0){
            $radio_amounts = sprintf('%.2f', -100);
        }else if($y_amounts>0 && $yy_amounts==0){
            $radio_amounts = sprintf('%.2f', 100);
        }else if($y_amounts==0 && $yy_amounts==0){
            $radio_amounts = sprintf('%.2f', 0);
        }else{
            $radio_amounts  =   (float)bcdiv(bcsub($y_amounts,$yy_amounts, 4), $yy_amounts, 4) * 100;
           // $radio_amounts = sprintf('%.2f', (($y_amounts-$yy_amounts)/$yy_amounts)*100);
        }
        return $radio_amounts;
    }

    public function format_Time($time=0)
    {
        $label = '';
        if(is_numeric($time) && $time>0){
            if($time < 60){
                $label = $time.'秒';
            }else if($time < 60*60){
                $m = intval($time/60);
                $s = $time%60;
                $label = $m.'分'.$s.'秒';
            }else if($time < 60*60*24){
                $h = intval($time/(60*60));
                $m = intval(($time%(60*60))/60);
                $s = intval(($time%(60*60))%60);
                $label = $h.'时'.$m.'分'.$s.'秒';
            }else{
                $d = intval($time/(60*60*24));
                $h = intval(($time%(60*60*24))/(60*60));
                $m = intval((($time%(60*60*24))%(60*60))/60);
                $s = intval((($time%(60*60*24))%(60*60))%60);
                $label = $d.'日'.$h.'时'.$m.'分'.$s.'秒';
            }
        }
        return $label;
    }






}