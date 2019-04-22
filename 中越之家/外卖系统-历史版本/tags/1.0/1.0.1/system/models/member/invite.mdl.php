<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Member_Invite extends Mdl_Table
{   
  
    protected $_table = 'member_invite';
    protected $_pk = 'invite_uid';
    protected $_cols = 'invite_uid,uid,mobile,money,dateline,status';

    //status 0,待下单(注册完成/首单取消/同意退款) 1,待完成(创建首单) 2,已完成(首单完成)
    public function invite_count($uid)
    {
        if(!$uid = (int)$uid){
            return false;
        }
        $sql = "SELECT uid, COUNT(1) as invite_count, SUM(`money`) as invite_money FROM ".$this->table($this->_table)." WHERE uid='$uid'";
        $row = array();
        if(($res = $this->db->GetRow($sql)) && $res['uid']){
            $row = $res;
        }
        return $row;
    }
    public function send_invite_money($invite_uid, $money)
    {
        if(!$invite_uid = (int)$invite_uid){
            return false;
        }else if(!$invite = $this->detail($invite_uid)){
            return false;
        }else if($this->update($invite_uid, array('money'=>$money), true)){
            K::M('member/money')->update($invite['uid'], $money, '邀请用户(UID:'.$invite_uid.')');
            return true;
        }
        return false;
    }

    public function send_hongbao_by_cfg($cfg, $id, $type)
    {
        if (is_array($cfg) && !empty($cfg)) {
            $moneys = 0;
            foreach ($cfg as $k => $v) {
                $ltime = strtotime(date('Y-m-d',__TIME)) + $v['hongbao_amount_ltime'] * 86400 + 86399;
                $hongbao = array(
                    'min_amount'=>$v['hongbao_min_amount'],
                    'amount'=>$v['hongbao_amount'],
                    'ltime'=>$ltime,
                    'title'=>L('好友邀请新人红包'),
                    'type'=>$type,
                    'limit_stime'=>$v['stime'],
                    'limit_ltime'=>$v['ltime'],
                    'from'=>$v['type']
                );
                if(K::M('hongbao/hongbao')->send($id, $hongbao)){
                    $moneys += $hongbao['amount'];
                }
            }
            
            return $moneys;
        }
        return false;
    }

    public function items_rank($filter=array(), $orderby=null, $p=1, $l=50, &$count=0)
    {
        $orderby = array('invite_count'=>'DESC', 'invite_money'=>'DESC');
        $where = $this->where($filter);
        $orderby = $this->order($orderby);
        $limit = $this->limit($p, $l);
        $items = array();
        $groupby = 'uid';
        $sql = "SELECT COUNT(DISTINCT $groupby) FROM ".$this->table($this->_table)." WHERE $where";
        if($count = $this->db->GetOne($sql)){
            $sql = "SELECT *, COUNT(1) as invite_count, SUM(`money`) as invite_money FROM ".$this->table($this->_table)." WHERE $where GROUP BY $groupby $orderby $limit";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    if($row[$groupby]){
                        $items[$row[$groupby]] = $row;
                    }else{
                        $items[] = $row;
                    }
                }
            }
        }
        return $items;
    }
}