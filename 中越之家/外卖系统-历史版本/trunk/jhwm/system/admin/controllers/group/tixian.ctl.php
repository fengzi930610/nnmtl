<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Group_Tixian extends Ctl
{
    protected $_status = array(0=>'待处理',1=>'通过',2=>'拒绝');
    public function index($page=1,$status=0)
    {

        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        if($status==0){
            $filter['status'] = 0;
        }else if($status==1){
            $filter['status'] = 1;

        }else if($status==2){
            $filter['status'] = 2;
        }else if($status==3){
            $filter['status'] = 4;
        }
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['tixian_id']){$filter['tixian_id'] = $SO['tixian_id'];}
            if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
            if($SO['status']){$filter['status'] = $SO['status'];}
            if(is_array($SO['updatetime'])){if($SO['updatetime'][0] && $SO['updatetime'][1]){$a = strtotime($SO['updatetime'][0]); $b = strtotime($SO['updatetime'][1])+86400;$filter['updatetime'] = $a."~".$b;}}

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%')";
            }
        }
        $orderby = array('tixian_id'=>'desc');
        $staff_ids = array();

        if($items = K::M('staff/tixian')->items_join_staff($filter, $orderby, $page, $limit, $count)){
            /*foreach($items as $k=>$v){
                $staff_ids[] = $v['staff_id'];
            }
            $staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach($items as $k=>$v){
                $items[$k]['staff'] = $staff_list[$v['staff_id']];
            }*/

            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}',$status)), array('SO'=>$SO));
        }

        $sum = K::M('staff/staff')->sum(array(),'money');
        $sum1 = K::M('staff/tixian')->sum(array('status'=>0),'money');
        $this->pagedata['items']  = $items;
        $this->pagedata['pager']  = $pager;
        $this->pagedata['status'] = $this->_status;
        $this->pagedata['st'] = $status;
        $this->pagedata['total'] = $sum+$sum1;
        $this->tmpl = 'admin:group/tixian/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:group/tixian/so.html';
    }
    public function detail($tixian_id = null)
    {
        if(!$tixian_id = (int)$tixian_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:group/tixian/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($tixian_id = K::M('staff/tixian')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/tixian-index.html');
            }
        }else{
            $this->tmpl = 'admin:group/tixian/create.html';
        }
    }
    public function edit($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = $this->GP('tixian_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(K::M('staff/tixian')->update($tixian_id, $data)){
                $this->msgbox->add('修改内容成功');
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:group/tixian/edit.html';
        }
    }
    public function doaudit($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(K::M('staff/tixian')->update($tixian_id, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('通过审核成功');
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('staff/tixian')->update($ids, array('status'=>1,'updatetime'=>__TIME))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function reason($tixian_id=null)
    {
        if(!($tixian_id = (int)$tixian_id) && !($tixian_id = (int)$this->GP('tixian_id'))){
            $this->msgbox->add('未指要操作的体现ID', 211);
        }else if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现不存在或已经删除', 212);
        }else if(!empty($detail['status'])){
            $this->msgbox->add('提现申请状态不可退回', 213);
        }else if($reason_content = $this->checksubmit('reason')){
            if(K::M('staff/tixian')->update($tixian_id, array('status'=>2, 'reason'=>$reason_content, 'updatetime'=>__TIME))){
                $log_id =  K::M('staff/staff')->update_money($detail['staff_id'], $detail['money'], $reason_content.',退回到帐户余额');
                K::M('staff/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                $this->msgbox->add('退回提现申请成功');
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:group/tixian/reason.html';
        }
    }
    public function delete($tixian_id=null)
    {
        if($tixian_id = (int)$tixian_id){
            if(!$detail = K::M('staff/tixian')->detail($tixian_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/tixian')->delete($tixian_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('staff/tixian')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function zhuanzhang($tixian_id){
        if($tixian_id = (int)$tixian_id){
            if(K::M('staff/tixian')->update($tixian_id, array('status'=>4,'updatetime'=>__TIME))){
                $this->msgbox->add('打款成功');
            }
        }else if($ids = $this->GP('tixian_id')){
            if(K::M('staff/tixian')->update($ids, array('status'=>4,'updatetime'=>__TIME))){
                $this->msgbox->add('批量打款成功');
            }
        }else{
            $this->msgbox->add('未指定要打款的内容', 401);
        }
    }

    /**
     * [支付宝一键转账]
     * @param  [type] $tixian_id [description]
     * @return [type]            [description]
     */
    public function transfer($tixian_id=null)
    {
        if(!$tixian_id = (int)$tixian_id){
            $this->msgbox->add('提现记录不正确', 401);
        }elseif(!$tixian = K::M('staff/tixian')->detail($tixian_id)){
            $this->msgbox->add('提现记录不正确', 401);
        }elseif($tixian['status'] != 1){
            $this->msgbox->add('提现记录不正确', 401);
        }else{
            $staff = K::M('staff/staff')->detail($tixian['staff_id']);
            //仅支持支付宝转帐
            $site = K::$system->config->get('site');
            $data = array(
                    'trade_no' => $tixian['trade_no'],
                    'payee_account' => $tixian['payee_account'],
                    'amount' => $tixian['end_money'],
                    'title'  => $staff['name'].'('.$tixian['payee_account'].')提现转账',
                    'body'  => $staff['name'].'('.$tixian['payee_account'].')提现转账【'.$site['title'].'】'
                );

            if(!$payObj = K::M('trade/payment')->loadPayment('alipay')){
                $this->msgbox->add('生成转账申请失败', 401);
            }elseif($trade = $payObj->transfer($data, $msg)){
                $a = array('pay_status'=>1, 'pay_result'=>$msg, 'pay_time'=>__TIME, 'status'=>4);
                if(K::M('staff/tixian')->update($tixian_id, $a)){
                    $this->msgbox->add(' 转账成功');
                }
            }else{
                K::M('staff/tixian')->update($tixian_id, array('status'=>2,'updatetime'=>__TIME,'reason'=>$msg));
                if(K::M('staff/tixian')->update($tixian_id, array('status'=>2, 'reason'=>$msg, 'updatetime'=>__TIME))){
                    $log_id = K::M('staff/staff')->update_money($tixian['staff_id'], $tixian['money'], "提现退回({$msg})");
                    K::M('staff/log')->update($log_id,array('extend'=>serialize(array('type'=>2,'can_id'=>$tixian_id))));
                }                
                $this->msgbox->add('转账失败('.$msg.')', 421);
            }
        }  
    }

    
}
