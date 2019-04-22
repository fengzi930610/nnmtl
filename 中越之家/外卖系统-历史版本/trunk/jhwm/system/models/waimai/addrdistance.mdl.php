<?php
/**
 * Copy Right zyzjgzh.cn
 * Writed by Vast
 * 注意:此表有两个主键，所以不能使用Mdl_Table中的单主键特性的方法，如detail等
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Addrdistance extends Mdl_Table
{
    protected $_table = 'addr_shop_distance';
    protected $_pk = 'addr_id,shop_id';
    protected $_cols = 'addr_id,shop_id,distance,distance,addr_lng,addr_lat,shop_lng,shop_lat';
    protected $_orderby = array();

    //查询成功返回距离数值，失败返回false，包括向地图平台查询不到数值的情况
    public function get_distance($shop_id,$addr_id)
    {
        $shop_id = (int)$shop_id;
        $addr_id = (int)$addr_id;
        if($shop_id<=0 || $addr_id<=0)
            return false;
        $shop = K::M('waimai/waimai')->detail($shop_id);
        $addr = K::M('member/addr')->detail($addr_id);
        if(!$shop || !$addr)
            return false;
        $shop['lng'] = (int)((float)$shop['lng']*1000000);
        $shop['lat'] = (int)((float)$shop['lat']*1000000);
        $addr['lng'] = (int)((float)$addr['lng']*1000000);
        $addr['lat'] = (int)((float)$addr['lat']*1000000);
        $cache = $this->db->select($this->_table,"*","`shop_id`={$shop_id} AND `addr_id`={$addr_id}")->fetch_array();
        
        if(!$cache || (int)$cache['addr_lng'] !== $addr['lng'] || (int)$cache['addr_lat'] !== $addr['lat'] || (int)$cache['shop_lng'] !== $shop['lng'] || (int)$cache['shop_lat'] !== $shop['lat'])
        {
            $bAdd = $cache?false:true;
            $cache = [
                'shop_id' => $shop_id,
                'addr_id' => $addr_id,
                'distance' => 0,
                'addr_lng' => $addr['lng'],
                'addr_lat' => $addr['lat'],
                'shop_lng' => $shop['lng'],
                'shop_lat' => $shop['lat']
            ];
            $dist = K::M('magic/baidu')->juli($cache['addr_lng']/1000000,$cache['addr_lat']/1000000,$cache['shop_lng']/1000000,$cache['shop_lat']/1000000);
            if($dist === false)
                return false;
            $cache['distance'] = (int)$dist;

            if($cache['distance'] === 0 && $cache['addr_lng']!== $cache['shop_lng'] && $cache['addr_lat'] !== $cache['shop_lat'])
            {
                var_dump("query disitance error");
                return false;
            }

            if($bAdd)
            {
                if(!$this->db->insert($this->_table,$cache))
                    return false;
            }
            else
            {
                $uptData = $cache;
                unset($uptData['shop_id'],$uptData['addr_id']);
                if(!$this->db->update($this->_table,$uptData,"`shop_id`={$shop_id} AND `addr_id`={$addr_id}"))
                    return false;
            }
        }
        else
            $cache['distance'] = (int)$cache['distance'];
        return $cache['distance'];
    }
}
