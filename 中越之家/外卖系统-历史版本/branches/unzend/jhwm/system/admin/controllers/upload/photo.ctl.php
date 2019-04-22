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

class Ctl_Upload_Photo extends Ctl
{
	
	public function index($cate_id=0, $page=1)
	{
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
        }
        $filter['cate_id'] = (int)$cate_id;
        $filter['from'] = 'photoGallery';
        if($items = K::M('magic/upload')->items($filter, array('photo_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array((int)$cate_id, '{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['cates'] = $cates = K::M('upload/cate')->fetch_all();
        $this->pagedata['count'] = $count = K::M('upload/cate')->getcounts(array('from'=>'photoGallery'));
        $this->pagedata['cate_id'] = (int)$cate_id;
        $this->pagedata['cate'] = $cate = $cates[$cate_id] ? $cates[$cate_id] : array('title'=>'未分组', 'cate_id'=>0);
		$this->tmpl = 'admin:upload/photo/items.html';
	}

	public function setcate()
	{
        $cate_id = $this->checksubmit('cate_id') ? $this->checksubmit('cate_id') : 0;
        if($cate_id && !$cate = K::M('upload/cate')->detail($cate_id)){
            $this->msgbox->add('选择的分组不存在或已删除', 212);
        }else if($photo_id = $this->checksubmit('photo_id')){
            if(K::M('magic/upload')->update($photo_id, array('cate_id'=>$cate_id))){
                $this->msgbox->add('分组成功');
            }else{
                $this->msgbox->add('分组失败', 214);
            }
        }else if($ids = $this->checksubmit('photo_ids')){
            if(K::M('magic/upload')->batch($ids, array('cate_id'=>$cate_id))){
                $this->msgbox->add('分组成功');
            }else{
                $this->msgbox->add('分组失败', 215);
            }
        }else{
            $this->msgbox->add('参数有误', 213);
        }
	}

	public function delete($photo_id=null)
	{
		if($photo_id = (int)$photo_id){
            if(!$detail = K::M('magic/upload')->detail($photo_id)){
                $this->msgbox->add('图片不存在或已经删除', 211);
            }else if(K::M('magic/upload')->delete($photo_id)){
                $this->msgbox->add('删除成功');
            }else{
                $this->msgbox->add('删除失败', 212);
            }
        }else if($ids = $this->GP('photo_ids')){
            if(K::M('magic/upload')->delete($ids)){
                $this->msgbox->add('删除成功');
            }else{
                $this->msgbox->add('删除失败', 213);
            }
        }else{
            $this->msgbox->add('未指定要删除的图片', 214);
        }
	}

	public function photoGallery()
	{
		
	}

	//异步上传文件
    public function upload($cate_id=0)
    {
        if(!$from = $this->checksubmit('from')){
            $from = 'photoGallery';
        }
        $attach = $_FILES['photo'];
        if($data = K::M('magic/upload')->upload($attach, $from, null, array(), (int)$cate_id)){
            $this->msgbox->set_data('data', array('photo'=>$data['photo']));
        }else{
            $this->msgbox->add('上传图片失败', 501);
        }
        $this->msgbox->json();
    }

    public function upload_by_data($cate_id=0)
    {
        if(!$from = $this->checksubmit('from')){
            $from = 'theme';
        }
        if($attach = $this->checksubmit('data')){
            /*if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $attach, $result)){
                $ext = '.'.$result[2];
            }else{
                $ext = '.png';
            }*/
            $start=strpos($attach,',');
            $attach= substr($attach,$start+1);
            $attach = str_replace(' ', '+', $attach);
            $attach = base64_decode($attach);
            
            if($data = K::M('magic/upload')->upload_by_data($attach, $from, (int)$cate_id,1)){
                $this->msgbox->set_data('data', array('photo'=>$data['photo']));
            }else{
                $this->msgbox->add('上传图片失败', 501);
            }
        }else{
            $this->msgbox->add('请选择要上传的图片', 502);
        }
        $this->msgbox->json();
    }
}