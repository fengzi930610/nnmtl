<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 11:07
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Activity_Activity extends Ctl {
     //活动列表
    public function index($page=1){
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;


        if($items = K::M('activity/activity')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $cate = K::M('activity/cate')->getCate();
        foreach ($items as $k=> $v){
            $items[$k]['type'] = $cate[$v['cate_id']];
        }
        
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl='admin:activity/items/index.html';
    }

    public function create(){
      $this->pagedata['cate']= K::M('activity/cate')->getCate();
      $this->tmpl='admin:activity/items/create.html';
    }
    
    public function create_handee(){
        if($data=$this->checksubmit('data')){
            if(!$data['title']){
                $this->msgbox->add('标题不能为空',210);
            }else if(!$data['cate_id']){
                $this->msgbox->add('分类不能为空',211);
            }else if(!$data['intro']){
                $this->msgbox->add('活动描述不能为空',212);
            }else if(!$data['stime']){
                $this->msgbox->add('开始时间不能为空',213);
            }else if(!$data['ltime']){
                $this->msgbox->add('结束时间不能为空',214);
            }else{
                $create_data = array();
                if($_FILES){
                    foreach ($_FILES as $k=> $v){
                        $a=K::M('magic/upload')->upload($v);
                        if($a){
                            $create_data[$k]=$a['photo'];
                        }
                    }
                }
                $create_data['title'] = $data['title'];
                $create_data['intro'] = $data['intro'];
                $create_data['stime'] = strtotime($data['stime']);
                $create_data['ltime'] = strtotime($data['ltime']);
                $create_data['cate_id'] = $data['cate_id'];
                if($create = K::M('activity/activity')->create($create_data)){
                    $this->msgbox->add('操作成功');
                }else{
                    $this->msgbox->add('添加失败',215);
                }
            }

        }

    }
    
    public function delete($cate_id){
        if(!$cate_id){
            $this->msgbox->add('没有选择需要删除的内容',216);
        }else if(!$detail=K::M('activity/activity')->detail($cate_id)){
            $this->msgbox->add('没有选择需要删除的内容',217);
        }else{
            if($del=K::M('activity/activity')->delete($cate_id)){
                $this->msgbox->add('删除成功');
            }else{
                $this->msgbox->add('删除失败',218);
            }
        }
        
    }

    public function edit($active_id){
        if($data=$this->checksubmit('data')){
            if(!$data['title']){
                $this->msgbox->add('标题不能为空',210);
            }else if(!$data['cate_id']){
                $this->msgbox->add('分类不能为空',211);
            }else if(!$data['intro']){
                $this->msgbox->add('活动描述不能为空',212);
            }else if(!$data['stime']){
                $this->msgbox->add('开始时间不能为空',213);
            }else if(!$data['ltime']){
                $this->msgbox->add('结束时间不能为空',214);
            }else if(!$data['active_id']){
                $this->msgbox->add('未指定需要修改的内容',219);
            } else{
                $create_data = array();
                if($_FILES){
                    foreach ($_FILES as $k=> $v){
                        $a=K::M('magic/upload')->upload($v);
                        if($a){
                            $create_data[$k]=$a['photo'];
                        }
                    }
                }
                $create_data['title'] = $data['title'];
                $create_data['intro'] = $data['intro'];
                $create_data['stime'] = strtotime($data['stime']);
                $create_data['ltime'] = strtotime($data['ltime']);
                $create_data['cate_id'] = $data['cate_id'];
                if($create = K::M('activity/activity')->update($data['active_id'],$create_data)){
                    $this->msgbox->add('修改成功');
                }else{
                    $this->msgbox->add('修改失败',215);
                }
            }

        }else{
            if(!$active_id){
                $this->msgbox->add('没有指定编辑内容',219);
            }else if(!$detail = K::M('activity/activity')->detail($active_id)){
                $this->msgbox->add('编辑的内容不存在',220);
            }else{

                $this->pagedata['cate']= K::M('activity/cate')->getCate();
                $this->pagedata['detail'] =$detail;
                $this->tmpl = 'admin:activity/items/edit.html';
            }
        }

    }
    
    public function delete_all(){
        if($shop_ids =$this->checksubmit('shop_id')){
            $lock = true;
            foreach ($shop_ids as $v){
                if(!$a=K::M('activity/activity')->delete($v)){
                    $lock=false;
                }
            }
            if($lock){
                $this->msgbox->add('批量删除成功');
            }else{
                $this->msgbox->add('批量删除失败',222);
            }
        }else{
            $this->msgbox->add('未指定需要删除的内容',221);
        }
    }

}