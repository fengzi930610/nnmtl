<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 14:45
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Track extends Mdl_Table {

    protected $_table = 'order_track';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,diatance,day,data,dateline,staff_id';

     protected function _format_row($row){
         $row['data'] =unserialize($row['data']);
         $data = array();
         foreach ($row['data'] as $k=>$v){
             $tmp = explode(";",$v['polyline']);
             foreach ($tmp as $k1=>$v1){
                 $tmp1 = explode(',',$v1);
                 $data[] = array(
                     $tmp1[0],
                     $tmp1[1]
                 );
             }
         }
         $row['ploy'] = $data;
         return $row;
     }


     //获取配送站的里程
     public function items_count_by_group($filter = array()){
         $where = $this->where($filter,'o.');
         $sql = "SELECT SUM(diatance) as diatance FROM ".$this->table($this->_table)." o  LEFT JOIN ".$this->table('order')." w ON o.order_id = w.order_id WHERE {$where}";
        // K::M('system/logs')->log('order_track',$sql);
         $diatance = 0;
         if($res = $this->db->Execute($sql)){
             if($row = $res->fetch()){
                 $diatance = $row['diatance'];
             }
         }
         return $diatance;

     }



}