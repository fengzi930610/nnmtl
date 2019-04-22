<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Staff_Msg extends Ctl
{
    protected $_status = array(0=>'未读',1=>'已读');
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
      if($SO = $this->GP('SO')){
          $pager['SO'] = $SO;
          if($SO['msg_id']){$filter['msg_id'] = $SO['msg_id'];}
          if($SO['staff_id']){$filter['staff_id'] = $SO['staff_id'];}
          if($SO['is_read']){$filter['is_read'] = $SO['is_read'];}
          if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1])+86400;$filter['dateline'] = $a."~".$b;}}
        }
        if($items = K::M('staff/msg')->items($filter, array('msg_id'=>'desc'), $page, $limit, $count)){
          $staff_ids = array();
          foreach($items as $k=>$v){
             $staff_ids[$v['staff_id']] = $v['staff_id'];
          }
          $staff = K::M('staff/staff')->items_by_ids($staff_ids);
          $staffs = array();
          foreach($staff as $k=>$v){
            $staffs[$v['staff_id']] = $v;
          }
        	$pager['count'] = $count;
        	$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['status']   = $this->_status;
        $this->pagedata['staffs']   = $staffs;
        $this->pagedata['items']    = $items;
        $this->pagedata['pager']    = $pager;
        $this->tmpl = 'admin:staff/msg/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:staff/msg/so.html';
    }
    
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($msg_id = K::M('staff/msg')->create($data)){
                if($this->GP('jpush_app')){
                    K::M('jpush/device')->send_staff($data['staff_id'], $data['title'], $data['content'], array('type'=>'message', 'msg_id'=>$msg_id));
                }
                $this->msgbox->add('添加内容成功');
                $this->msgbox->set_data('forward', '?staff/msg-index.html');
            } 
        }else{
           $this->tmpl = 'admin:staff/msg/create.html';
        }
    }
    
    public function delete($msg_id=null)
    {
        if($msg_id = (int)$msg_id){
            if(!$detail = K::M('staff/msg')->detail($msg_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('staff/msg')->delete($msg_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('msg_id')){
            if(K::M('staff/msg')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
