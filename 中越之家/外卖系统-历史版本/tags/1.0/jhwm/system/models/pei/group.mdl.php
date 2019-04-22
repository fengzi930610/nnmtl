<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/22
 * Time: 15:23
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Pei_Group extends Mdl_Table {

    protected $_table = 'pei_group';
    protected $_pk = 'group_id';
    protected $_cols = 'group_id,province_id,city_id,group_name,mobile,passwd,addr,dateline,overtime,contact,face,closed,lng,lat,polygon_point,min_amount,min_pei,voice,assign,limit_order,is_used,baseconfig,timeconfig,badweather,timeout_config,timeout_time,efence,min_pei,autopei_config';
    protected $_orderby = array('group_id'=>'DESC');
    protected $_pre_cache_key = 'pei-group-list';

    public function member($u, $l = 'group_id')
    {
        $l = strtolower($l);
        switch ($l) {
            case 'group_id':
                $field = 'group_id';
                break;
            case 'mobile':
                $field = 'mobile';
                break;

            default:
                return false;
        }
        $where = $this->where(array($field=>$u));
        $sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE `closed`=0 AND " . $where;
        if ($row = $this->db->GetRow($sql)) {
            $row = $this->_format_row($row);
            return $row;
        }else{
            return false;
        }
    }

    public function update_passwd($uid,$pwd){
        if(!$uid||!$pwd){
            return false;
        }else if(!$group = $this->detail($uid)){
            return false;
        }else{
            return $this->update($uid,md5($pwd));
        }


    }

  /*  public function detail($pk, $closed=false)
    {
        if(!$pk = (int)$pk){
            return false;
        }
        $this->_checkpk();
        $where = self::field($this->_pk, $pk);
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where";
        if($detail = $this->db->GetRow($sql)){
            $detail = $this->_format_row($detail);
        }
        return $detail;
    }*/

    protected function _check($data)
    {
        if(isset($data['lat'])){
            $data['lat'] = round(bcmul($data['lat'], 1000000));
        }
        if(isset($data['lng'])){
            $data['lng'] = round(bcmul($data['lng'], 1000000));
        }
        return parent::_check($data);
    }

    protected function _format_row($row)
    {

        if($row['lat']){
            $row['lat'] = bcdiv($row['lat'], 1000000,6);
        }
        if($row['lng']){
            $row['lng'] = bcdiv($row['lng'], 1000000,6);
        }
        $row['polygon_point'] =  $row['polygon_point']?unserialize($row['polygon_point']):array();
        $row['baseconfig'] = $row['baseconfig']?unserialize($row['baseconfig']):array();
        $row['timeconfig'] = $row['timeconfig']?unserialize($row['timeconfig']):array();
        $row['badweather'] = $row['badweather']?unserialize($row['badweather']):array();
        $row['timeout_config'] = $row['timeout_config']?unserialize($row['timeout_config']):array();
        $row['autopei_config'] = $row['autopei_config']?unserialize($row['autopei_config']):array();
        if(!defined("HAVE_PEI")){
            $row['assign']   = 0;
        }else {
            if(!HAVE_PEI){
                $row['assign'] = 0;
            }
        }

        if(!$row['face']){
            $row['face'] = 'default/face.png';
        }
        $row['efence'] =  $row['efence']?unserialize($row['efence']):array();
        return $row;
    }
     public function get_group_by_lnglat($lng,$lat)
    {
        static $group = null;
        if(!$lng || !$lat){
            return false;
        }else{
            if($group===null){
                $groups = $this->fetch_all();
            }
            foreach($groups as $k=>$v){
                if(!empty($v['polygon_point'])&&empty($v['closed'])){
                    if(K::M('helper/round')->in_or_out_polygon($v['polygon_point'],$lat,$lng)){
                        return $groups[$k];
                    }
                }
            }
            return false;            
        }
    }

    public function get_default_addr($uid){
        $addr_list = K::M('member/addr')->items(array('uid'=>$uid),null,1,10,$count);
        foreach($addr_list as $k=>$v){
            $location = K::M('helper/date')->bd_decrypt($v['lng'],$v['lat']);
            if($group = $this->get_group_by_lnglat($location['gg_lon'],$location['gg_lat'])){
                $addr_list[$k]['group_id'] = $group['group_id'];
                 return $addr_list[$k];
            }
        }
        return false;
    }

    public function set_cache($group_id,$is_check=false){

        if($group = $this->detail($group_id,$is_check)){
            if($result = K::M('cache/cache')->get('pei_group_'.$group_id) === false){
               return  K::M('cache/cache')->set('pei_group_'.$group_id,$group);
            }else{
                return  K::M('cache/cache')->set('pei_group_'.$group_id,$group);
            }
        }else{
            return false;
        }

    }

    public function get_cache($group_id,$is_check=false){
        if(($result = K::M('cache/cache')->get('pei_group_'.$group_id)) !== false){
           return $result;
        }else if($group = $this->detail($group_id,$is_check)){
             K::M('cache/cache')->set('pei_group_'.$group_id,$group);
            return $group;
        }else{
            return false;
        }
    }

    //平台配送  设置预计送达时间
    public function set_order_expect_time($order_id,$data){
        if($data['pei_type']==0 || $data['pei_type']==3){
            return false;
        }else if(!$data['group_id']){
            return false;
        }else{
            if($data['pei_time']>0){
                K::M('order/order')->update($order_id,array('expect_time'=>$data['pei_time']));
            }else{
              /*  if($data['lat']&&(abs($data['lat'])>0)){
                    $data['lat'] = bcdiv($data['lat'], 1000000,6);
                }
                if($data['lng']&&(abs($data['lng'])>0)){
                    $data['lng'] = bcdiv($data['lng'], 1000000,6);
                }
                if($data['o_lat']&&(abs($data['o_lat'])>0)){
                    $data['o_lat'] = bcdiv($data['o_lat'], 1000000,6);
                }
                if($data['o_lng']&&(abs($data['o_lng'])>0)){
                    $data['o_lng'] = bcdiv($data['o_lng'], 1000000,6);
                }
*/
                if(!$juli= K::M('magic/baidu')->juli($data['o_lng'], $data['o_lat'], $data['lng'], $data['lat'])){
                    $juli = K::M('helper/round')->juli($data['o_lng'], $data['o_lat'], $data['lng'], $data['lat']);
                }
                $juli = ceil($juli/1000);
                $config = $this->get_cache($data['group_id']);
                $_max_freight = array('fkm'=>0, 'time'=>40);
                $_freight = array();
                foreach($config['timeout_config'] as $k=>$v){
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
                $p_time = $_freight['fkm'] ? $_freight['time'] : $_max_freight['time'];
                K::M('order/order')->update($order_id,array('expect_time'=>(__TIME+($p_time*60))));
            }


        }
    }

}