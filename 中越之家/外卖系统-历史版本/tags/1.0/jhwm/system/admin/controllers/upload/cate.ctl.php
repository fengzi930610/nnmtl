<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: role.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Upload_Cate extends Ctl
{
	
	public function index()
	{
		$this->tmpl = 'admin:upload/cate/index.html';
	}

	public function create()
	{
        if($data = $this->checksubmit('data')){
            if(!$data = $this->filter_fields('title,orderby', $data)){
                $this->msgbox->add('数据有误', 211);
            }else if(!$title = $data['title']){
                $this->msgbox->add('分组标题不能为空', 212);
            }else{
                $data['dateline'] = __TIME;
                if(K::M('upload/cate')->create($data)){
                    K::M('upload/cate')->flush();
                    $this->msgbox->add('创建成功');
                }else{
                    $this->msgbox->add('创建失败', 213);
                }
            } 
        }else{
            $this->tmpl = 'admin:upload/cate/create.html';
        }
	}

    public function edit($cate_id=0)
    {
        if(!($cate_id = (int)$cate_id) && !($cate_id = (int)$this->GP('cate_id'))){
            $this->msgbox->add('参数有误', 211);
        }else if(!$cate = K::M('upload/cate')->detail($cate_id)){
            $this->msgbox->add('分组不存在或已被删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(!$data = $this->filter_fields('title,orderby', $data)){
                $this->msgbox->add('数据有误', 211);
            }else if(!$title = $data['title']){
                $this->msgbox->add('分组标题不能为空', 213);
            }else if(K::M('upload/cate')->update($cate_id, $data)){
                K::M('upload/cate')->flush();
                $this->msgbox->add('修改成功');
            }else{
                $this->msgbox->add('修改失败', 214);
            }
        }else{
            $this->pagedata['detail'] = $cate;
            $this->tmpl = 'admin:upload/cate/edit.html';
        }
    }

	public function delete($cate_id=null)
	{
		if(!$cate_id = (int)$cate_id){
            $this->msgbox->add('参数有误', 211);
        }else if(!$cate = K::M('upload/cate')->detail($cate_id)){
            $this->msgbox->add('分组不存在或已被删除', 212);
        }else{
            if(K::M('upload/cate')->setcate(array('cate_id'=>$cate_id), 0)){
                if(K::M('upload/cate')->delete($cate_id)){
                    K::M('upload/cate')->flush();
                    $this->msgbox->add('删除成功');
                }else{
                    $this->msgbox->add('删除失败', 213);
                }
            }else{
                $this->msgbox->add('删除失败', 214);
            }
        }
	}
}