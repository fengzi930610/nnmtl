
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 14:01
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Distance extends Mdl_Table {

    protected $_table = 'staff_distance';
    protected $_pk = 'distance_id';
    protected $_cols = 'distance_id,staff_id,distance,day,dateline';

     //记录配送员距离的相关功能  外卖3.7新增  2017-12-25

    public function create($order){
        if(!$order['staff_id']){
            return false;
        }else{
            $insert_data = array();
            $insert_data['dateline'] = $order['dateline'];
            $insert_data['staff_id'] = $order['staff_id'];
            if($order['o_lng']&&$order['o_lat']&&$order['lng']&&$order['lat']){
                if($info = K::M('magic/baidu')->juliinfo($order['o_lng'],$order['o_lat'],$order['lng'],$order['lat'])){
                    $distance = $info['distance'];
                    $path = $info['path'];
                }else{
                    $distance =$juli = K::M('helper/round')->juli($order['o_lng'], $order['o_lat'], $order['lng'], $order['lat']);
                    $path = array();
                }
            }else{
                $distance = 0;
                $path = array();
            }

            $insert_data['day'] = date('Ymd',$order['dateline']);
            $insert_data['distance'] = $distance;
            $format_sql = $this->_insert_sql($insert_data);
            $sql = 'INSERT INTO '.$this->table($this->_table).$format_sql.' ON DUPLICATE KEY UPDATE '.'distance=distance+'.$insert_data['distance'];
            $track_data = array();
            $track_data['order_id'] = $order['order_id'];
            $track_data['diatance'] = $distance;
            $track_data['day'] =  $insert_data['day'];
            $track_data['data'] = serialize($path);
            $track_data['dateline'] = $insert_data['dateline'];
            $track_data['staff_id'] = $order['staff_id'];
            K::M('order/track')->create($track_data);
            return  $this->db->Execute($sql);
        }

    }
    protected function _insert_sql($data)

    {
        ksort($data);
        return "(`".implode("`,`",array_keys($data))."`) VALUES('".implode("','",$data)."')";
    }


}