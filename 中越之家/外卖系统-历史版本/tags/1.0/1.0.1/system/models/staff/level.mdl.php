<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/13
 * Time: 10:33
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Level extends Mdl_Table {

    protected $_table = 'staff_level';
    protected $_pk = 'level_id';
    protected $_cols = 'level_id,title,config_waimai,config_paotui,dateline,stime,ltime,config_waimai_time,config_paotui_time';
    protected $_orderby = array('level_id'=>'DESC');
    protected $_pre_cache_key = 'staff-level-list';


    protected function _format_row($row)
    {

        if($row['config_waimai']){
           $row['config_waimai'] = unserialize($row['config_waimai']);
        }
        if($row['config_paotui']){
            $row['config_paotui']  = unserialize($row['config_paotui']);
        }
        if($row['config_waimai_time']){
            $row['config_waimai_time'] = unserialize($row['config_waimai_time']);
        }
        if($row['config_paotui_time']){
            $row['config_paotui_time'] = unserialize($row['config_paotui_time']);
        }
        if($row['stime']){
            $start_time = strtotime(date('Y-m-d ',__TIME).$row['stime']);
            $row['stime_time'] = $start_time-strtotime(date('Y-m-d ',__TIME));
        }
        if($row['ltime']){
            $end_time = strtotime(date('Y-m-d ',__TIME).$row['ltime']);
            $row['ltime_time'] = $end_time-strtotime(date('Y-m-d ',__TIME));
            if(stristr($row['ltime'],'次日')){
                $ltime = str_replace('次日', '', $row['ltime']);
                $end_time = strtotime(date('Y-m-d ',__TIME).$ltime) + 86400;
                $row['ltime_time'] = $end_time - strtotime(date('Y-m-d ',__TIME)) + 86400;
            }
        }

        return $row;
    }

}