<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: log.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Payment_Log extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['from']){$filter['from'] = $SO['from'];}
            if($SO['type']){$filter['type'] = $SO['type'];}
            if($SO['payment']){$filter['payment'] = "LIKE:%".$SO['payment']."%";}
            if($SO['trade_no']){$filter['trade_no'] = "LIKE:%".$SO['trade_no']."%";}
            if(is_numeric($SO['payed'])){$filter['payed'] = $SO['payed'];}
            if(is_array($SO['payedtime'])){if($SO['payedtime'][0] && $SO['payedtime'][1]){$a = strtotime($SO['payedtime'][0]); $b = strtotime($SO['payedtime'][1])+86400;$filter['payedtime'] = $a."~".$b;}}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('payment/log')->items($filter, array('log_id'=>'desc'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:payment/logs/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:payment/logs/so.html';
    }
    public function create()
    {
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($log_id = K::M('payment/log')->create($data)){
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', '?payment/log-index.html');
                }
            } 
        }else{
           $this->tmpl = 'admin:payment/logs/create.html';
        }
    }
    public function edit($log_id=null)
    {
        if(!($log_id = (int)$log_id) && !($log_id = $this->GP('log_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('payment/log')->detail($log_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if(K::M('payment/log')->update($log_id, $data)){
                    $this->msgbox->add('修改内容成功');
                }  
            } 
        }else{
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:payment/logs/edit.html';
        }
    }


    public function highchat(){

        $sdate = strtotime("2017-6-1");
        $mouth_arr = K::M('helper/date')->get_date_list($sdate, __TIME);// 获取2个时间之间的月份数组（不论前后顺序）
        foreach ($mouth_arr as $k=>$v){
            $mouth_arr[$k] = date('Y-m',$v);
        }
        $this->pagedata['bills_month'] = $mouth_arr;
        $this->tmpl  = "admin:payment/logs/highchat.html";

    }

    public function get_payment_log_data(){
        if($data = $this->checksubmit('data')){
            //指定月份
            $step_first = strtotime(date('Y-m-01',strtotime($data['mouth'])));  //获取本月第一天时间戳
            $step_last = strtotime(date('Y-m-d', (strtotime(date('Y-m-01',$step_first)." +1 month")-1)))+86399;
            $step = $data['step'];
            $filter = array();
            $filter['dateline'] = $step_first.'~'.$step_last;
            $filter['payed'] = 1;
            $data = K::M('payment/log')->group_by_data($filter,$step_first,$step_last,$step);
            $this->msgbox->set_data('data',$data);

        }else{
            $this->msgbox->add('非法数据请求',201);
        }

    }


}