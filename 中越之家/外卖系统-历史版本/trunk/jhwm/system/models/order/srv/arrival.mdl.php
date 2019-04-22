<?php
/**
 * Copy Right zyzjgzh.cn
 * Writed by Vast
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Order_Srv_Arrival extends Mdl_Table
{   
  
    protected $_table = 'order_srv_arrival';
    protected $_pk = 'order_id';
    protected $_cols = 'order_id,admin_id,member_id,shop_id,user_name,user_tel,user_lng,user_lat,create_time,close_time,remark';
    protected $_orderby = array('order_id' => 'DESC');
   
    public function create($data)
    {
        if(!$data = $this->_check($data))
            return false;

        if($this->fetch($data['order_id']))
            return $data['order_id'];

        $order = K::M('order/order')->detail($data['order_id']);
        if(!$order)
            return false;
        $data['shop_id'] = (int)$order['shop_id'];
        $data['member_id'] = (int)$order['uid'];
        $data['user_name'] = $order['contact'];
        $data['user_tel'] = $order['mobile'];
        $data['user_lng'] = (int)($order['lng']*1000000);
        $data['user_lat'] = (int)($order['lat']*1000000);
        $data['create_time'] = time();
        $data['close_time'] = 0;
        if(!isset($data['remark']))
            $data['remark'] = "";
        else
            $data['remark'] = trim($data['remark']);

        $rtn = false;
        if($this->db->insert($this->_table, $data, true))
        {
            $this->flush();
            $rtn = $data['order_id'];
        }        
        return $data['order_id'];
    }
    
    
    //设置关闭状态
    public function setClose($order_ids,$bClose=true)
    {
        $oids = [];
        if(is_numeric($order_ids))
        {
            $order_ids = (int)$order_ids;
            if($order_ids <= 0)
                return false;
            $oids[$order_ids] = $order_ids;
        }
        else if(is_string($order_ids))
        {
            $order_ids = explode(",", trim($order_ids));
            foreach($order_ids as $idStr)
            {
                $idStr = trim($idStr);
                if($idStr !== "")
                {
                    $tid = (int)$idStr;
                    if($tid > 0 && !isset($oids[$tid]))
                        $oids[$tid] = $tid;
                }
            }
        }
        else if(is_array($order_ids))
        {
            foreach($order_ids as $tid)
            {
                $tid = (int)$tid;
                if($tid > 0 && !isset($oids[$tid]))
                    $oids[$tid] = $tid;
            }
        }
        else
            return false;

        $setWhere = $bClose?"close_time=0":"close_time>0";
        $setVal = $bClose?time():0;

        if(count($oids) === 0)
            return false;
        else if(count($oids) === 1)
        {
            $oids = array_merge($oids);
            $setWhere .= " AND order_id=".$oids[0];
        }
        else
            $setWhere .= " AND order_id IN(".implode(",", $oids).")";
        return $this->db->update($this->_table,['close_time'=>$setVal],$setWhere);
    }

    //绑定管理员
    public function bindAdmin($order_id,$admin_id)
    {
        $order_id = (int)$order_id;
        $admin_id = (int)$admin_id;

        if($order_id <= 0 || $admin_id < 0)
            return false;

        if($this->db->update($this->_table,['admin_id'=>$admin_id],"order_id={$order_id}"))
            return true;
        return false;
    }

    //设置备注
    public function setRemark($order_id,$remark)
    {
        $order_id = (int)$order_id;
        

        if($order_id <= 0)
            return false;

        $remark = trim($remark);

        if($this->db->update($this->_table,['remark'=>$remark],"order_id={$order_id}"))
            return true;
        return false;
    }

    protected function _check($data, $order_id=null)
    {
        if(isset($data['user_lat'])){
            $data['user_lat'] = round(bcmul($data['user_lat'], 1000000));
        }
        if(isset($data['user_lng'])){
            $data['user_lng'] = round(bcmul($data['user_lng'], 1000000));
        }
        if(!isset($data['order_id']) || (int)$data['order_id']<=0)
            return false;
        $data['order_id'] = (int)$data['order_id'];

        if(isset($data['admin_id']))
        {
            $data['admin_id'] = (int)$data['admin_id'];
            if($data['admin_id'] < 0)
                return false;
        }

        if(isset($data['member_id']))
        {
            $data['member_id'] = (int)$data['member_id'];
            if($data['member_id'] < 0)
                return false;
        }

        if(isset($data['shop_id']))
        {
            $data['shop_id'] = (int)$data['shop_id'];
            if($data['shop_id'] < 0)
                return false;
        }


        if(isset($data['user_name']))
            $data['user_name'] = trim($data['user_name']);

        if(isset($data['user_tel']))
            $data['user_tel'] = trim($data['user_tel']);
        
        if(isset($data['create_time']))
        {
            $data['create_time'] = (int)$data['create_time'];
            if($data['create_time'] < 0)
                $data['create_time'] = 0;
        }

        if(isset($data['close_time']))
        {
            $data['close_time'] = (int)$data['close_time'];
            if($data['close_time'] < 0)
                $data['close_time'] = 0;
        }

        if(isset($data['remark']))
            $data['remark'] = trim($data['remark']);
        
        return parent::_check($data, $order_id);
    }

    public function where($filter=null, $pre='', $ANDOR='AND')
    {
        if(is_array($filter)){
            if(isset($filter['user_lat'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['user_lat'], $m)){
                    $filter['user_lat'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['user_lat'] = bcmul($filter['user_lat'], 1000000);
                }                
            }
            if(isset($filter['user_lng'])){
                if(preg_match('/^([\-\d\.]+)~([\-\d\.]+)$/', $filter['user_lng'], $m)){
                    $filter['user_lng'] = bcmul($m[1], 1000000).'~' . bcmul($m[2], 1000000);
                }else{
                    $filter['user_lng'] = bcmul($filter['user_lng'], 1000000);
                }                
            }
        }
        return parent::where($filter, $pre, $ANDOR);
    }
    

    // function mbStrSplit ($string, $len=1) {
    //     $start = 0;
    //     $strlen = K::M('content/string')->Len($string);
    //     while ($strlen) {
    //         $array[] = K::M('content/string')->sub($string,$start,$len,"");
    //         $string = K::M('content/string')->sub($string, $len, $strlen,"");
    //         $strlen = K::M('content/string')->Len($string);
    //     }
    //     return $array;
    // }


    protected function _format_row($row)
    {
        if($row['user_lat']){
            $row['user_lat'] = bcdiv($row['user_lat'], 1000000,6);
        }
        if($row['user_lng']){
            $row['user_lng'] = bcdiv($row['user_lng'], 1000000,6);
        }
        return $row;
    }
}