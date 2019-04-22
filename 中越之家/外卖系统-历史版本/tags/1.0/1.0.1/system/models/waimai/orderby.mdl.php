<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: account.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Orderby extends Model
{  
    protected $orderby;

    public function __construct()
    {
        $wmcfg = K::M('system/config')->get('waimaihuodongconfig');
        $this->orderby = $wmcfg['shop_orderby'] ? $wmcfg['shop_orderby'] : 'juli';
    } 

    // 默认排序辅助uasort()
    public function default_order($a, $b)
    {
        switch ($this->orderby) {
            case 'bought':
                return $this->default_order_bought($a, $b);
                break;
            case 'viewed':
                return $this->default_order_viewed($a, $b);
                break;
            case 'juli':
                return $this->default_order_juli($a, $b);
                break;
            default:
                return $this->default_order_juli($a, $b);
                break;
        }

        /*if($a['yyst'] == $b['yyst']){
            if ($a['juli'] == $b['juli']) {
                if ($a['orderby'] == $b['orderby']) {
                    if($a['orders'] == $b['orders']){
                        return 0;
                    }else{
                        return ($a['orders'] > $b['orders']) ? -1 : 1;
                    }
                }else{
                    return ($a['orderby'] < $b['orderby']) ? -1 : 1;
                }
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }*/
    }

    public function default_order_juli($a, $b)
    {
        /*if($a['yyst'] == $b['yyst']){
            if ($a['juli'] == $b['juli']) {
                if ($a['orderby'] == $b['orderby']) {
                    if($a['orders'] == $b['orders']){
                        return 0;
                    }else{
                        return ($a['orders'] > $b['orders']) ? -1 : 1;
                    }
                }else{
                    return ($a['orderby'] < $b['orderby']) ? -1 : 1;
                }
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }*/

        if($a['yyst'] == $b['yyst']){
            if ($a['orderby'] == $b['orderby']) {
                if ($a['juli'] == $b['juli']) {
                    if($a['orders'] == $b['orders']){
                        return 0;
                    }else{
                        return ($a['orders'] > $b['orders']) ? -1 : 1;
                    }
                }else{
                    return ($a['juli'] < $b['juli']) ? -1 : 1;
                }
            }else{
                return ($a['orderby'] < $b['orderby']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }

    public function default_order_bought($a, $b)
    {
        /*if($a['yyst'] == $b['yyst']){
            if($a['bought'] == $b['bought']){
                if ($a['juli'] == $b['juli']) {
                    if ($a['orderby'] == $b['orderby']) {
                        if($a['orders'] == $b['orders']){
                            return 0;
                        }else{
                            return ($a['orders'] > $b['orders']) ? -1 : 1;
                        }
                    }else{
                        return ($a['orderby'] < $b['orderby']) ? -1 : 1;
                    }
                }else{
                    return ($a['juli'] < $b['juli']) ? -1 : 1;
                }
            }else{
                return ($a['bought'] > $b['bought']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }*/
        if($a['yyst'] == $b['yyst']){
            if ($a['orderby'] == $b['orderby']) {
                if($a['bought'] == $b['bought']){
                    if ($a['juli'] == $b['juli']) {                        
                        if($a['orders'] == $b['orders']){
                            return 0;
                        }else{
                            return ($a['orders'] > $b['orders']) ? -1 : 1;
                        }                        
                    }else{
                        return ($a['juli'] < $b['juli']) ? -1 : 1;
                    }
                }else{
                    return ($a['bought'] > $b['bought']) ? -1 : 1;
                }
            }else{
                return ($a['orderby'] < $b['orderby']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }

    public function default_order_viewed($a, $b)
    {
        /*if($a['yyst'] == $b['yyst']){
            if($a['viewed'] == $b['viewed']){
                if ($a['juli'] == $b['juli']) {
                    if ($a['orderby'] == $b['orderby']) {
                        if($a['orders'] == $b['orders']){
                            return 0;
                        }else{
                            return ($a['orders'] > $b['orders']) ? -1 : 1;
                        }
                    }else{
                        return ($a['orderby'] < $b['orderby']) ? -1 : 1;
                    }
                }else{
                    return ($a['juli'] < $b['juli']) ? -1 : 1;
                }
            }else{
                return ($a['viewed'] > $b['viewed']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }*/
        if($a['yyst'] == $b['yyst']){
            if($a['orderby'] == $b['orderby']) {
                if($a['viewed'] == $b['viewed']){
                    if ($a['juli'] == $b['juli']) {                        
                        if($a['orders'] == $b['orders']){
                            return 0;
                        }else{
                            return ($a['orders'] > $b['orders']) ? -1 : 1;
                        }                        
                    }else{
                        return ($a['juli'] < $b['juli']) ? -1 : 1;
                    }
                }else{
                    return ($a['viewed'] > $b['viewed']) ? -1 : 1;
                }
            }else{
                return ($a['orderby'] < $b['orderby']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    // 距离排序辅助uasort()
    public function juli_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['juli'] == $b['juli']) {
                return 0;
            }else{
                return ($a['juli'] < $b['juli']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    //评分排序
    public function score_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['avg_score'] == $b['avg_score']) {
                return 0;
            }else{
                return ($a['avg_score'] > $b['avg_score']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    //销量排序
    public function sales_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['orders'] == $b['orders']) {
                return 0;
            }else{
                return ($a['orders'] > $b['orders']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    // 起送价排序uasort()
    public function price_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['min_amount'] == $b['min_amount']) {
                return 0;
            }else{
                return ($a['min_amount'] < $b['min_amount']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
    // 送达排序uasort()
    public function ptime_order($a, $b)
    {
        if($a['yyst'] == $b['yyst']){
            if ($a['pei_time'] == $b['pei_time']) {
                return 0;
            }else{
                return ($a['pei_time'] < $b['pei_time']) ? -1 : 1;
            }
        }else{
            return ($a['yyst'] > $b['yyst']) ? -1 : 1;
        }
    }
    
}
