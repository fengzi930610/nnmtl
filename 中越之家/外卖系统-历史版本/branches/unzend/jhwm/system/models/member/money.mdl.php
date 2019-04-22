<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: gold.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
Import::M('member/member');
class Mdl_Member_Money extends Mdl_Member_Member
{

    public function update($uids, $money, $intro='')
    {
        if(!$money = (float)$money){
            $this->msgbox->add('更变的余额值非法'.$money, 411);
        }else if(empty($intro)){
            $this->msgbox->add('未填写充值说明', 412);
        }else{
            if($uids = K::M('verify/check')->ids($uids)){

                foreach(explode(',', $uids) as $uid){
                    $member = $this->detail($uid);
                    if($money > 0){
                        $sql = "UPDATE ".$this->table($this->_table)." SET `money`=`money`+{$money},`total_money`=`total_money`+{$money} WHERE uid='$uid'";
                    }else if(($member['money']+$money)>=0){
                        $sql = "UPDATE ".$this->table($this->_table)." SET `money`=`money`+{$money} WHERE uid='$uid'";
                    }else{
                        return false;
                    }
                    if($this->db->Execute($sql)){


                        K::M('member/log')->log($uid, 'money', $money, $intro,$member['money']+$money);
                    }
                }
                return true;
            }
        }
        return false;
    }
    public function package()
    {
        // 充值金额读取管理员后台配置 by 夏玉峰 2016-11-24 14:30:33
        $money_pack = array();
        if($cfg = K::$system->config->get('moneypack')) {
            foreach($cfg as $k=>$v) {
                $money_pack[$v['money']] = $v['give'];
            }
        }
        return $money_pack;
    }
}
