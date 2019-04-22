<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Staff extends Ctl
{
    protected $_status   = array(0=>'离线',1=>'在线');
    protected $_audit    = array(0=>'待审核',1=>'通过审核',2=>'审核失败');
    protected $_verify   = array(0=>'待审核',1=>'通过认证',2=>'认证被拒绝');
    protected $_from     = array(weixiu=>'维修', 'paotui'=>'跑腿','house'=>'家政');
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
          $pager['SO'] = $SO;
          if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
          if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
          if($SO['from']){$filter['from'] = $SO['from'];}
          if($SO['name']){$filter['name'] = $SO['name'];}
          if($SO['mobile']){$filter['mobile'] = $SO['mobile'];}
          if($SO['score']){$filter['score'] = $SO['score'];}
          if($SO['verify_name']){$filter['verify_name'] = $SO['verify_name'];}
          if($SO['status']){$filter['status'] = $SO['status'];}
          if($SO['audit']){$filter['audit'] = $SO['audit'];}
        }
        $filter['closed'] = 0;
        if($items = K::M('staff/staff')->items($filter, array('staff_id'=>'DESC'), $page, $limit, $count)){
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['audit'] = $this->_audit;
        $this->pagedata['status'] = $this->_status;
        $this->pagedata['verify'] = $this->_verify;
        $this->pagedata['from'] = $this->_from;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/index/items.html';
    }


    public function index2($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
            if($SO['city_id']){$filter['city_id'] = $SO['city_id'];}
            if($SO['from']){$filter['from'] = $SO['from'];}
            if($SO['name']){$filter['name'] = $SO['name'];}
            if($SO['mobile']){$filter['mobile'] = $SO['mobile'];}
            if($SO['score']){$filter['score'] = $SO['score'];}
            if($SO['verify_name']){$filter['verify_name'] = $SO['verify_name'];}
            if($SO['status']){$filter['status'] = $SO['status'];}
            if($SO['audit']){$filter['audit'] = $SO['audit'];}
        }
        $filter['closed'] = 0;
        if($items = K::M('staff/staff')->items($filter, array('staff_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['audit'] = $this->_audit;
        $this->pagedata['status'] = $this->_status;
        $this->pagedata['verify'] = $this->_verify;
        $this->pagedata['from'] = $this->_from;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/index/dialog.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:staff/index/so.html';
    }
    public function dialog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }else{
                if($SO['from']){$filter['from'] = $SO['from'];}
                if($SO['mobile']){$filter['mobile'] = "LIKE:%".$SO['mobile']."%";}
                if($SO['name']){$filter['name'] = "LIKE:%".$SO['name']."%";}
                if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}                
            }
        }
        $filter['closed'] = 0;
        if($items = K::M('staff/staff')->items($filter, null, $page, $limit, $count)){
            //print_r($items);die;

            $city = K::M('data/city')->items();

            foreach ($items as $k => $v){
                  foreach ($city as $kk=>$vv){
                      if($v['city_id']== $vv['city_id']){
                          $items[$k]['city_name'] = $vv['city_name'];
                      }

                  }
            }



            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO, 'multi'=>$multi));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/index/dialog.html';   
    }
    
    public function detail($staff_id = null)
    {
        if(!$staff_id = (int)$staff_id){
            $this->msgbox->add('未指定要查看内容的ID', 211);
        }else if(!$detail = K::M('staff/staff')->detail($staff_id)){
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:staff/index/detail.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($staff_id = K::M('staff/staff')->create($data)){
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/staff-index.html');
            }
        }else{
           $this->tmpl = 'admin:staff/index/create.html';
        }
    }
    public function edit($staff_id=null)
    {
        if(!($staff_id = (int)$staff_id) && !($staff_id = $this->GP('staff_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('staff/staff')->detail($staff_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if($detail['from']=='paotui'&&!$data['group_id']){
                $this->msgbox->add('请选择配送站')->response();
            }
            if(K::M('staff/staff')->update($staff_id, $data)){
                $this->msgbox->add('修改内容成功');
            }

        }else{
            $this->pagedata['group'] = K::M('pei/group')->fetch_all();
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:staff/index/edit.html';
        }
    }
    public function audit()
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['audit'] = 0;
        if($items = K::M('staff/staff')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/index/audit.html';
    }
    public function doaudit($staff_id=null)
    {
        if($staff_id = (int)$staff_id){
            if(K::M('staff/staff')->batch($staff_id, array('audit'=>1))){
                $this->msgbox->add('审核内容成功');
            }
        }else if($ids = $this->GP('staff_id')){
            if(K::M('staff/staff')->batch($ids, array('audit'=>1))){
                $this->msgbox->add('批量审核内容成功');
            }
        }else{
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }
    public function delete($staff_id=null, $force=false)
    {
        if($staff_id = (int)$staff_id){
            if(!$detail = K::M('staff/staff')->detail($staff_id, $force)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/staff')->delete($staff_id, $force)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('staff_id')){
            if(K::M('staff/staff')->delete($ids, $force)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
    public function recycle($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 1;
        if($items = K::M('staff/staff')->items($filter, array('staff_id'=>'DESC'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:staff/index/recycle.html';
    }
    public function regain($staff_id=null)
    {
        if($staff_id = intval($staff_id)){
            if(K::M('staff/staff')->regain($staff_id)){
                $this->msgbox->add('恢复服务人员帐号成功');
            }
        }else if($staff_ids = $this->GP('staff_id')){
            if(K::M('member/member')->regain($staff_ids)){
                $this->msgbox->add('批量恢复服务人员帐号成功');
            }
        }else{
            $this->msgbox->add('未指定要恢复服务人员', 401);
        }
    }

public function paiorder($order_id, $page=1)
    {
        $filter = $pager = array();
        if(!($order_id=(int)$order_id) && !($order_id = (int)$this->GP('order_id'))){
            $this->msgbox->set_data('未指定要派单的单号',211);
        }else if(!$order = K::M('order/order')->detail($order_id)){
            $this->msgbox->add('订单不存在或已经删除', 211);
        }else if(!in_array($order['from'], array('waimai', 'paotui', 'weixiu', 'house'))){
            $this->msgbox->add('该订单不支持派单', 212);
        }else if($order['staff_id']>0){
            $this->msgbox->add('已经有人接单了，您可以选取消再派单',212);
        }else if(!$order['pay_status']){
            $this->msgbox->add('未支付订单不可派单', 213);
        }else if($order['from'] == 'waimai' && !in_array($order['pei_type'], array(1, 2))){
            $this->msgbox->add('该订单为商家自送，不可派单', 214);
        }else if(!in_array($order['order_status'], array(0,1,2,3,4))){
            $this->msgbox->add('该订单状态不可派单', 215);
        }else if($order['from'] == 'waimai' && ($order['order_status']==0 && (int)$order['pei_type']!==2)){
            $this->msgbox->add('该订单状态不可派单', 215);
        }else{
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 10;
            $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N'); 
            if($SO = $this->GP('SO')){
                $pager['SO'] = $SO;
                if($SO['name']){$filter['name'] = "LIKE:%".$SO['name']."%";}
                if($SO['mobile']){$filter['mobile'] = "LIKE:%".$SO['mobile']."%";}
                if(is_array($SO['lastlogin'])){if($SO['lastlogin'][0] && $SO['lastlogin'][1]){$a = strtotime($SO['lastlogin'][0]); $b = strtotime($SO['lastlogin'][1])+86400;$filter['lastlogin'] = $a."~".$b;}}
                if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
            }else{
                if(!defined('__DEV_MODEL') || !constant('__DEV_MODEL')){ //开发环境忽略坐标
                    //使用此函数计算得到结果后，带入sql查询。
                    $squares = K::M('helper/round')->returnSquarePoint($order['o_lng'], $order['o_lat'], 5); //5KM以内的配送员
                    $filter['lat'] = $squares['left-bottom']['lat'].'~'.$squares['right-top']['lat'];
                    $filter['lng'] = $squares['left-bottom']['lng'].'~'.$squares['right-top']['lng'];
                }
            }
            if($order['from'] == 'waimai'){
                $filter['from'] = 'paotui';
            }else{
                $filter['from'] = $order['from'];
            }

            if($order['group_id']){
                $filter['group_id'] = $order['group_id']; 
            }
            $filter['verify_name'] = 1;
            $filter['status'] = 1;
            
            if($items = K::M('staff/staff')->items($filter, array('status'=>'DESC'), $page, $limit, $count)){
                foreach($items as $k=>$v){
                    $v['order_juli'] = K::M('helper/round')->getdistance($v['lng'], $v['lat'], $order['lng'], $order['lat']);  //距离
                    $items[$k] = $v;
                }                
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($order['order_id'], '{page}')), array('SO'=>$SO, 'multi'=>$multi));
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:staff/index/paiorder.html';  
        }
    }
}
