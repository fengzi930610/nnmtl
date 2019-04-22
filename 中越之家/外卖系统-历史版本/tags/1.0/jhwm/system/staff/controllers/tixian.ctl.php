<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/17
 * Time: 10:52
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('staff');
class Ctl_Tixian extends Ctl_Staff {

    public function index(){
        $account = K::M('staff/account')->find(array('staff_id'=>$this->staff_id));
        if($account){
            $account['number'] = substr_replace($account['account'],'**** **** ****',0,strlen($account['account_number'])-4);
        }
        $ke = 0;
        $this->pagedata['account'] = $account;
        $this->pagedata['staff'] = $this->staff;
        $this->tmpl = 'tixian/index.html';
    }

    public function create(){
        $sday = strtotime(date('Y-m-d'));
        if($data = $this->checksubmit('data')){
            if(($money = (float)$data['money']) <1){
                $this->msgbox->add('提现金额不能少于1元', 211);
            }else if($money > $this->staff['money']){
                $this->msgbox->add(L('提现金额不正确'), 212);
            }else if(!$account = K::M('staff/account')->detail_by_staff_id($this->staff_id)){
                $this->msgbox->add(L('没有设置提现帐号'), 214);
            }else if(false && K::M('staff/tixian')->count(array('staff_id'=>$this->staff_id, 'dateline'=>'>:'.$sday))){
                $this->msgbox->add(L('一天只能提现一次'), 215);
            }else if($info  = K::M('staff/staff')->tixian($this->staff_id, $money, $account)){
                $arr = array('type'=>2,'can_id'=>$info['tixian_id']);
                K::M('staff/log')->update($info['log_id'],array('extend'=>serialize($arr)));
               // $this->msgbox->set_data('data', array('money'=>($this->staff['money']-$money), 'tixian_id'=>$tixian_id));
                $this->msgbox->add('提现成功');

            }else {
                $this->msgbox->add('提现失败',216);
            }
        }
    }

    public function account(){
        $account = K::M('staff/account')->find(array('staff_id'=>$this->staff_id));
        if($account){
            $account['number'] = substr_replace($account['account'],'**** **** ****',0,strlen($account['account_number'])-4);
        }
        $this->pagedata['account'] = $account;
        $this->tmpl = 'tixian/account.html';

    }


}