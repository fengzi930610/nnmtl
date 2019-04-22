<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Hongbao_Hongbao extends Mdl_Table
{   
  
    protected $_table = 'hongbao';
    protected $_pk = 'hongbao_id';
    protected $_cols = 'hongbao_id,from,title,min_amount,amount,type,uid,hongbao_sn,stime,ltime,order_id,used_ip,used_time,clientip,dateline,limit_stime,limit_ltime,cate_id';
    protected $cd_key = 'hsoewocnsdl2479sdfew_12whf';
    
    public function getType()
    {
        /*return array(
            1=>'普通红包',
            2=>'彩头红包',
            3=>'天降红包',
            4=>'超级红包',
            5=>'新人红包',
        );*/
        return array(
            0=>'普通红包',
            1=>'充值红包',
            2=>'兑换红包',
            3=>'天降红包',
            4=>'分享红包',
            5=>'邀请新人红包',
            6=>'被邀请新人注册红包',
            7=>'被邀请新人注册红包(地推)',
        );
    }
    
    public function create_normal($data,$checked=false){
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['type'] = 4;
        $data['limit_stime'] = trim($data['limit_stime']);
        $data['limit_ltime'] = trim($data['limit_ltime']);
        if($data['uid']){
            K::M('jpush/device')->jpush('发红包啦',"恭喜您获得{$data['amount']}元红包！", array('alias'=>(string)$data['uid'], 'from'=>'member', 'tag_and'=>array('login_on')),null);
        }
        return $this->db->insert($this->_table, $data, true);
    }

    public function create($data, $checked=false)
    {
        $num = $data['num'];
        $ltime = $data['ltime'];
        if(isset($data['ltime'])){
            if(is_numeric($data['ltime'])){
                $ltime = strtotime(date("Y-m-d", $data['ltime'])." 23:59:59");
            }else if(preg_match('/^(\d{4}-\d{1,2}-\d{1,2})/', $row['ltime'])){
                $ltime = $row['ltime'].' 23:59:59';
            }
        }
        for ($i = 1; $i <= $num; $i++) {
            $datas[$i]['title'] = $data['title'];
            $datas[$i]['min_amount'] = $data['min_amount'];
            $datas[$i]['amount'] = $data['amount'];
            $datas[$i]['type'] = $data['type'];
            $datas[$i]['stime'] = $data['stime'];
            $datas[$i]['uid'] = $data['uid'];
            $datas[$i]['ltime'] = $ltime;
            $datas[$i]['dateline'] = __CFG::TIME;
            $datas[$i]['clientip'] = __IP;
            $datas[$i]['from'] = $data['from'];
            $datas[$i]['limit_stime'] = trim($data['limit_stime']);
            $datas[$i]['limit_ltime'] = trim($data['limit_ltime']);
        }
            
        foreach ($datas as $key => $v) {
            $hongbao_id = $this->db->insert($this->_table, $v, true);
           if(empty($v['uid'])){
                $keys = md5($this->cd_key.$hongbao_id);
                $keys = substr($keys,10,10);
                $this->batch($hongbao_id,array('hongbao_sn'=>$keys));
            }else{
               K::M('jpush/device')->jpush('发红包啦',"恭喜您获得{$data['amount']}元红包！", array('alias'=>(string)$v['uid'], 'from'=>'member', 'tag_and'=>array('login_on')),null);



           }
        }
        return $hongbao_id;
    }
    
    
    public function add($data, $checked=false)
    {
        $uid = intval($data['uid']);        
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['uid'] = $uid;
        $data['dateline'] = __CFG::TIME;
        $data['clientip'] = __IP;
        $data['limit_stime'] = trim($data['limit_stime']);
        $data['limit_ltime'] = trim($data['limit_ltime']);
        if($hongbao_id = $this->db->insert($this->_table, $data, true)){
            if(empty($uid)){
                $sn = substr(md5($this->cd_key.$hongbao_id),10,10);
                $this->update($hongbao_id,array('hongbao_sn'=>$sn), true);
            }
        }
        if($uid){
        K::M('jpush/device')->jpush('发红包啦',"恭喜您获得{$data['amount']}元红包！", array('alias'=>(string)$uid, 'from'=>'member', 'tag_and'=>array('login_on')),null);
        }

        return $hongbao_id;
    }
    
    public function send($uid, $data)
    {
        $uid = (int)$uid;
        if(!$data = $this->_check_schema($data)){
            return false;
        }
        $data['uid'] = $uid;     
        $data['dateline'] = __TIME;
        $data['clientip'] = __IP;
        $data['limit_stime'] = trim($data['limit_stime']);
        $data['limit_ltime'] = trim($data['limit_ltime']);
        if($hongbao_id = $this->db->insert($this->_table, $data, true)){
            if(empty($uid)){
                $sn = substr(md5($this->cd_key.$hongbao_id),10,10);
                $this->update($hongbao_id,array('hongbao_sn'=>$sn), true);
            }else{
                //消息推送
                K::M('jpush/device')->jpush('发红包啦',"恭喜您获得{$data['amount']}元红包！", array('alias'=>(string)$uid, 'from'=>'member', 'tag_and'=>array('login_on')),null);
            }
        }
        return $hongbao_id;        
    }
    
    
    public function get_hongbao($uid, $amount, $from)
    {
        $filter = array();
        if($from=='waimai'){
            $filter = array(
                'waimai','all'
            );
        }else if($from == 'paotui'){
            $filter = array(
                'paotui','all'
            );
        }
        $hongbao = $this->find(array('uid'=>$uid, 'from'=>$filter, 'min_amount'=>'<=:'.$amount,'ltime'=>'>=:'.__TIME,'order_id'=>0),array('amount'=>'desc'));
        if($count = $this->count(array('uid'=>$uid,'min_amount'=>'<=:'.$amount,'ltime'=>'>=:'.__TIME,'order_id'=>0))){
            $hongbao['count'] = $count;
        }
        /*if(trim($hongbao['limit_stime'])&&trim($hongbao['limit_ltime'])){
            if((strtotime($hongbao['limit_stime'])>__TIME)||(strtotime($hongbao['limit_ltime'])<__TIME)){
                return array();
            }
        }else if(!trim($hongbao['limit_stime'])&&trim($hongbao['limit_ltime'])){
            if(strtotime($hongbao['limit_ltime'])<__TIME){
                return array();
            }
        }else if(trim($hongbao['limit_stime'])&&!trim($hongbao['limit_ltime'])){
            if((strtotime($hongbao['limit_stime'])>__TIME)){
                return array();
            }
        }*/
        if(!$this->check_hongbao($hongbao, $from)){  //v3.6 次日
            return array();
        }

        return $hongbao;
    }
    
    
    public function get_hongbaos($uid, $amount, $from)
    {
        $filter = array();
        if($from=='waimai'){
            $filter = array(
                'waimai','all'
            );
        }else if($from == 'paotui'){
            $filter = array(
                'paotui','all'
            );
        }
        if($hongbaos = $this->items(array('uid'=>$uid, 'from'=>$filter, 'min_amount'=>'<=:'.$amount,'ltime'=>'>=:'.__TIME,'order_id'=>0),array('amount'=>'desc'))){
            foreach($hongbaos as $k=>$v){
                /*if((trim($v['limit_stime'])&&trim($v['limit_ltime']))){
                    if((strtotime($v['limit_stime'])>__TIME)||(strtotime($v['limit_ltime'])<__TIME)){
                        unset($hongbaos[$k]);
                    }
                }else if((trim($v['limit_stime']))&&!(trim($v['limit_ltime']))){
                    if((strtotime($v['limit_stime'])>__TIME)){
                        unset($hongbaos[$k]);
                    }
                }else if(!(trim($v['limit_stime']))&&(trim($v['limit_ltime']))){
                    if(strtotime($v['limit_ltime'])<__TIME){
                        unset($hongbaos[$k]);
                    }
                }*/
                if(!$this->check_hongbao($v, $from)){ //v3.6 次日
                    unset($hongbaos[$k]);
                }
            }
            return array_values($hongbaos);
        }
    }
    
    protected function _check($row, $hongbao_id=null)
    {
        if(isset($row['ltime'])){
            if(is_numeric($row['ltime'])){
                $row['ltime'] = strtotime(date("Y-m-d", $row['ltime'])." 23:59:59");
            }else if(preg_match('/^(\d{4}-\d{1,2}-\d{1,2})/', $row['ltime'])){
                $row['ltime'] = $row['ltime'].' 23:59:59';
            }
        }
        return parent::_check($row, $hongbao_id);
    }

    /*public function check_hongbao($hongbao,$from){
        if($hongbao['from']!='all'&&$hongbao['from']!=$from){
            return false;
        }else if(trim($hongbao['limit_stime'])&&trim($hongbao['limit_ltime'])){
            if(strtotime($hongbao['limit_stime'])<__TIME&&strtotime($hongbao['limit_ltime'])>__TIME){
                return true;
            }
            return false;
        }else if(!trim($hongbao['limit_stime'])&&trim($hongbao['limit_ltime'])){
            if(strtotime($hongbao['limit_ltime'])>__TIME){
                return true;
            }
            return false;
        }else if(trim($hongbao['limit_stime'])&&!trim($hongbao['limit_ltime'])){
            if(strtotime($hongbao['limit_stime'])<__TIME){
                return true;
            }
            return false;
        }else if(!trim($hongbao['limit_stime'])&&!trim($hongbao['limit_ltime'])){
            return true;
        }
        return false;
    }*/

    public function check_hongbao($hongbao,$from)
    {   
        //v3.6 次日
        $limit_stime = $hongbao['limit_stime'] ? trim($hongbao['limit_stime']) : '00:00';
        $limit_ltime = $hongbao['limit_ltime'] ? trim($hongbao['limit_ltime']) : '24:00';

        if($hongbao['from'] != 'all' && $hongbao['from'] != $from){
            return false;
        }else{
            $start_time = strtotime($limit_stime);
            if(stristr($limit_ltime,'次日')){
                $ltime = str_replace('次日', '', $limit_ltime);
                $start_time1 = strtotime('00:00');
                $end_time1 = strtotime($ltime);
                $start_time2 = strtotime($limit_stime);
                $end_time2 = strtotime('23:59');
                if((__TIME >= $start_time1 && __TIME <= $end_time1) || (__TIME >= $start_time2 && __TIME <= $end_time2)){
                    return true;
                }
            }else{
                $end_time = strtotime($limit_ltime);              
                if(__TIME >= $start_time && __TIME <= $end_time){
                    return true;
                }
            }               
        }
        return false;
    }

    protected function _format_row($row)
    {
        $row['limit_stime_time'] = K::M('helper/format')->format_morrowTime($row['limit_stime']);
        $row['limit_ltime_time'] = K::M('helper/format')->format_morrowTime($row['limit_ltime']);
        return $row;
    }

    public function items_join_member($filter, $order_by=array(), $page=1, $limit = 50, &$count = 0){
        $where = $this->where($filter,'o.');
        $order_by = $this->order($order_by,'o.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql =  "SELECT  o.*, w.`mobile` as 'mobile', w.`nickname` as 'nickname'  FROM ".$this->table($this->_table)." o LEFT JOIN ".$this->table('member')." w ON o.uid = w.uid WHERE {$where} {$order_by} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[$row[$this->_pk]] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }
}
