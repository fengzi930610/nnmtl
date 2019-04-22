<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/2/27
 * Time: 17:17
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Outlinelog extends Mdl_Table {
    protected $_table = 'staff_outlinelog';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,staff_id,city_id,group_id,stime,ltime,lng,lat,o_lng,o_lat,dateline';

    protected function _format_row($row){
        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }
        if($row['o_lat']){
            $row['o_lat'] = bcdiv($row['o_lat'], 1000000,6);
        }
        if($row['o_lng']){
            $row['o_lng'] = bcdiv($row['o_lng'], 1000000,6);
        }
        return $row;
    }

    protected function _check($data, $order_id=null)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
        if(isset($data['o_lat'])){
            $data['o_lat'] = round(bcmul($data['o_lat'], 1000000));
        }
        if(isset($data['o_lng'])){
            $data['o_lng'] = round(bcmul($data['o_lng'], 1000000));
        }
        return parent::_check($data, $order_id);
    }

    public function check_outline($staff,$data){
        if($staff['outline']==0){
            if(!$group = K::M('pei/group')->get_cache($staff['group_id'])){
                return false;
            }else if(!$group['efence']){
                return false;
            }else if(K::M('helper/round')->in_or_out_polygon($group['efence'],$data['lat'],$data['lng'])){
                return false;
            }else{
                $datas = array();
                $datas['staff_id'] = $staff['staff_id'];
                $datas['group_id'] = $staff['group_id'];
                $datas['city_id'] = $staff['city_id'];
                $datas['stime'] = __TIME;
                $datas['ltime'] = 0;
                $datas['lng'] = $data['lng'];
                $datas['lat'] = $data['lat'];
                $datas['o_lng'] = 0;
                $datas['o_lat'] = 0;
                $datas['dateline'] = __TIME;
                $this->create($datas);
                K::M('staff/staff')->update($staff['staff_id'],array('outline'=>1));
            }

        }else{
            if(!$group = K::M('pei/group')->get_cache($staff['group_id'])){
                return false;
            }else if(!K::M('helper/round')->in_or_out_polygon($group['efence'],$data['lat'],$data['lng'])){
                return false;
            }else if(!$group['efence']){
                K::M('staff/staff')->update($staff['staff_id'],array('outline'=>0));
                return false;
            }else{
                $filter = array();
                $filter['staff_id'] = $staff['staff_id'];
                $filter['ltime'] = 0;
                $filter['dateline'] = "<:".__TIME;
                $log = $this->find($filter);
                $this->update($log['log_id'],array('ltime'=>__TIME,'o_lng'=>$data['lng'],'o_lat'=>$data['lat']));
                K::M('staff/staff')->update($staff['staff_id'],array('outline'=>0));
            }
        }

    }


    public function items_join_staff($filter,$order_by = array(),$page=1,$limit =50,&$count){
        $where  = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $count_sql = " SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id=w.staff_id WHERE {$where}";
        if($res = $this->db->Execute($count_sql)){
            if($row = $res->fetch()){
                $count =  $row['count'];
            }
        }
        $items = array();
        $sql = " SELECT o.*,w.name,w.mobile FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id=w.staff_id WHERE {$where} {$order_by} {$limit}";
        if($res1 = $this->db->Execute($sql)){
            while($row1 = $res1->fetch()){
                $row1 = $this->_format_row($row1);
                $items[]  = $row1;
            }
        }
        return  $items;
    }

    public function group_by_data($filter,$stime,$ltime,$step){

        $data = K::M('helper/date')->get_arr_by_type($stime,$ltime,$step);
        $arr = $items = array();
        switch ($step){
            case "d":
                $group_by = "days";
                $format = "%Y%m%d";
                break;
            case "h":
                $group_by = "hours";
                $format = "%H";
                break;
            default:
                $group_by = "days";
                $format = "%Y%m%d";
                break;
        }
        
        $where  = $this->where($filter);
        $sql ="SELECT count(log_id) as count,FROM_UNIXTIME(dateline,'{$format}') as {$group_by} FROM ".$this->table($this->_table)." WHERE {$where} GROUP BY {$group_by}";
        
        //$where  = $this->where($filter,'o.');
        //$sql ="SELECT count(o.log_id) as count,FROM_UNIXTIME(o.dateline,'%Y%m%d') as {$group_by} FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id=w.staff_id WHERE {$where} GROUP BY {$group_by}";
        
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[(int)$row[$group_by]] = $row;
            }
        }

        foreach ($data as $k=>$v){
            $arr['x'][] = $v;
            $arr['count'][] = $items[$k]['count']? (int)$items[$k]['count']:0;
        }

        return $arr;
    }

    public function items_by_staff($filter=array(),$order_by = array(),$page=1,$limit =50,&$count){
        $where  = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);

        $count_sql = " SELECT count(DISTINCT o.staff_id) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id=w.staff_id WHERE {$where} ";
        if($res = $this->db->Execute($count_sql)){
            if($row = $res->fetch()){
                $count =  $row['count'];
            }
        }

        $items = array();
        $sql = " SELECT o.*,sum(if(o.ltime>0,o.ltime-o.stime, UNIX_TIMESTAMP(now())-o.stime)) as times,count(o.log_id) as count,w.name,w.mobile FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('staff')." w ON o.staff_id=w.staff_id WHERE {$where} GROUP BY o.staff_id {$order_by} {$limit}";
        if($res1 = $this->db->Execute($sql)){
            while($row1 = $res1->fetch()){
                $row1 = $this->_format_row($row1);
                $items[]  = $row1;
            }
        }
        return  $items;
    }




}