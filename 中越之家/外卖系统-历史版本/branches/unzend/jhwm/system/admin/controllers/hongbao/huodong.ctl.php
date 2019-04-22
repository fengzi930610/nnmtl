<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Hongbao_Huodong extends Ctl
{
    public function InitializeApp()
    {
        parent::InitializeApp();
        $types = K::M('hongbao/hongbao')->getType();
        $this->pagedata['types'] = $types;
    }

    public function index()
    {
        $filter = array('closed'=>0);
        $detail = K::M('hongbao/huodong')->find($filter);
        $this->pagedata['detail'] = $detail;
        $this->tmpl = 'admin:hongbao/huodong/index.html';
    }

    public function check_data($data)
    {
        if($data){
            $stime = $data['stime'] = strtotime($data['stime']);
            $ltime = $data['ltime'] = strtotime($data['ltime']) + 86399;
            $times['stime'] = $data['times']['stime'] ? $data['times']['stime'] : '00:00';
            $times['ltime'] = $data['times']['ltime'] ? $data['times']['ltime'] : '23:59';
            if(!$title = $data['title']){
                $this->msgbox->add('活动标题不能为空！',212)->response();
            }else if(!$intro = $data['intro']){
                $this->msgbox->add('活动副标题不能为空！',213)->response();
            }else if($ltime < __TIME){
                $this->msgbox->add('结束时间不能早于当前时间', 214)->response();
            }else if($ltime < $stime){
                $this->msgbox->add('结束时间不能早于开始时间', 215)->response();
            }else if(strtotime($times['stime']) > strtotime($times['ltime'])){
                $this->msgbox->add('时间区间设置有误！', 216)->response();
            }else if(!$weeks = $data['weeks']){
                $this->msgbox->add('请选择活动周期！',217)->response();
            }else if(!array_intersect($weeks,array(0,1,2,3,4,5,6))){
                $this->msgbox->add('活动周期设置有误！',218)->response();
            }else if(!$limit = (int)$data['limit']){
                $this->msgbox->add('限领数量不能为空！',219)->response();
            }else if(!$config = $data['config']){
                $this->msgbox->add('红包设置不能为空！',220)->response();
            }else{
                $_hongbao_data = array();
                foreach($config as $k=>$v){                    
                    $a = (float)$v['min_amount'];
                    $b = (float)$v['amount'];
                    $c = (int)$v['day'];
                    if(!K::M('helper/format')->checkTimes(trim($v['stime']), trim($v['ltime']))){
                        $this->msgbox->add('红包使用时间不正确',201)->response();
                    }
                    if($a > 0 && $b > 0 && $a >= $b && $c >0 && $c <= 7){
                        $_hongbao_data[] = $v;
                    }else{
                        $this->msgbox->add('红包信息设置有误！',221)->response();
                    }
                }

                $new_data = array(
                    'title'=>$title,
                    'intro'=>$intro,
                    'stime'=>$stime,
                    'ltime'=>$ltime,
                    'times'=>serialize($times),
                    'weeks'=>implode(',',$weeks),
                    'limit'=>abs($limit),
                    'status'=>$data['status'] ? $data['status'] : 0,
                    'config'=>serialize($_hongbao_data),
                    'background_color'=>$data['background_color']
                    );
                return $new_data;
            }
        }
        return false;
    }

    public function create()
    {
        if($curr_hd = K::M("hongbao/huodong")->find(array('closed'=>0))){
            $this->msgbox->add('有活动未结束！', 211);
        }else if($data = $this->checksubmit('data')){
            if(!($new_data = $this->check_data($data)) || !is_array($new_data)){
                $this->msgbox->add('数据有误！',212);
            }else{
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'tjhongbao')){
                                $new_data[$k] = $a['photo'];
                            }
                        }
                    }
                }

                $new_data['dateline'] = __TIME;
                $new_data['clientip'] = __IP;
                if($huodong_id = K::M("hongbao/huodong")->create($new_data)){
                    $this->msgbox->add('创建活动成功！');
                    $this->msgbox->set_data('forward','?hongbao/huodong-index.html');
                }else{
                    $this->msgbox->add('创建活动失败！',222);
                }
            }              
        }else{
            $this->tmpl = 'admin:hongbao/huodong/create.html';
        }        
    }

    public function edit($huodong_id=null)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$huodong = K::M('hongbao/huodong')->detail($huodong_id)){
            $this->msgbox->add('活动不存在或已删除！',212);
        }else if($huodong['closed']){
            $this->msgbox->add('活动已撤销，不可修改！',213);
        }else if($data = $this->checksubmit('data')){
            if(!($new_data = $this->check_data($data)) || !is_array($new_data)){
                $this->msgbox->add('数据有误！',212);
            }else{
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'tjhongbao')){
                                $new_data[$k] = $a['photo'];
                            }
                        }
                    }
                }

                if($huodong_id = K::M("hongbao/huodong")->update($huodong_id, $new_data)){
                    $this->msgbox->add('活动修改成功！');
                    $this->msgbox->set_data('forward','?hongbao/huodong-index.html');
                }else{
                    $this->msgbox->add('活动修改失败！',222);
                }
            }
        }else{
            $this->pagedata['detail'] = $huodong;
            $this->tmpl = 'admin:hongbao/huodong/edit.html';
        }
    }

    public function detail($huodong_id=null)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！',211);
        }else if(!$huodong = K::M('hongbao/huodong')->detail($huodong_id,true)){
            $this->msgbox->add('活动不存在或已删除！',212);
        }else{
            $this->pagedata['detail'] = $huodong;
            $this->tmpl = 'admin:hongbao/huodong/detail.html';
        }
    }

    public function history($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 1;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['title']){
                $filter['title'] = 'LIKE:%'.$SO['title'].'%';
            }
            if($SO['dateline'][0] && $SO['dateline'][1]){
                $a = strtotime($SO['dateline'][0]);
                $b = strtotime($SO['dateline'][1])+86400;
                $filter['dateline'] = $a."~".$b;
            }
        }
        
       if($items = K::M('hongbao/huodong')->items($filter, array('huodong_id'=>'desc'), $page, $limit, $count)){
            foreach($items as $k=>$v){
                $items[$k]['type'] = 'first';
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:hongbao/huodong/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:hongbao/huodong/so.html';
    }

    public function delete($huodong_id)
    {
        if(!$huodong_id = (int)$huodong_id){
            $this->msgbox->add('参数有误！', 211);
        }else if(!$huodong = K::M('hongbao/huodong')->detail($huodong_id)){
            $this->msgbox->add('活动不存在或已删除！',212);
        }elseif(K::M("hongbao/huodong")->delete($huodong_id)){
            $this->msgbox->add('活动撤销成功');
        }else{
            $this->msgbox->add('活动撤销失败', 213);
        }
    }
    
}