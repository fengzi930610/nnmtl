<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2016-01-14 01:25:14Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Adv extends Ctl
{
    public function index($page=1)
    {
    	$filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 20;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 0;
        $orderby = array('orderby'=>'ASC','adv_id'=>'DESC');

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($title = $SO['title']){
                $filter['title'] = "LIKE:%".$title."%";
            }

            if($SO['stime'] && $SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime'] && $SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
        }

        if($items = K::M('waimai/adv')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('shop/adv/index', array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'shop/adv/index.html';
    }

    public function history($page=1)
    {
    	$filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 20;
        $filter['shop_id'] = $this->shop_id;
        $filter['closed'] = 1;
        $orderby = array('orderby'=>'ASC','adv_id'=>'DESC');

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($title = $SO['title']){
                $filter['title'] = "LIKE:%".$title."%";
            }

            if($SO['stime'] && $SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+86399);
            }
            if(!$SO['stime'] && $SO['ltime']){
                $filter['dateline'] = "<:".(strtotime($SO['ltime'])+86399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline'] = ">:".strtotime($SO['stime']);
            }
        }

        if($items = K::M('waimai/adv')->items($filter, $orderby, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink('shop/adv/history', array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'shop/adv/history.html';
    }

    public function create()
    {
        if($data = $this->checksubmit('data')){
        	if(!$data = $this->check_fields($data, 'title,link,stime,ltime,orderby')){
        		$this->msgbox->add('非法数据提交', 210);
        	}else if(!$data['title']){
                $this->msgbox->add('标题不能为空', 211);
            }/*else if(!$data['link']){
                $this->msgbox->add("链接不能为空",212);
            }*/else if(!$data['stime'] || !$data['ltime']){
               	$this->msgbox->add('时间设置有误',213);
           	}else if((strtotime($data['ltime'])+86399 <= (strtotime($data['stime'])))){
               	$this->msgbox->add('结束时间不能早于开始时间',214);
           	}else if((strtotime($data['ltime'])+86399) < __TIME){
               	$this->msgbox->add('结束时间不能早于当前时间', 215);
           	}else{
           		//$thumbs = array('photo'=>'800', 'thumb'=>'300X300');
	        	$thumbs = array();
	            if($_FILES['data']){
	                foreach($_FILES['data']['name']['photo'] as $k=>$v){
	                    $attachs[$k] = array(
	                        'name' => $v,
	                        'type' => $_FILES['data']['type']['photo'][$k],
	                        'tmp_name' => $_FILES['data']['tmp_name']['photo'][$k],
	                        'error' => $_FILES['data']['error']['photo'][$k],
	                        'size' => $_FILES['data']['size']['photo'][$k]
	                    );
	                }
	                $upload = K::M('magic/upload');
	                foreach($attachs as $k=>$attach){
	                    if($attach['error'] == UPLOAD_ERR_OK){
	                        if($a = $upload->upload($attach, 'image', null, $thumbs)){
	                            if($k == 0){
	                                $data['photo'] = $a['photo'];
	                            }else{
	                                $photos[$k] = $a['photo'];
	                            }
	                        }
	                    }
	                }
	            }
                if(!$data['photo']){
                    $this->msgbox->add('请上传图片', 216)->response();
                }

	            $data['shop_id'] = $this->shop_id;
	            $data['stime'] = strtotime($data['stime']);
           		$data['ltime'] = strtotime($data['ltime']) + 86399;
           		$data['dateline'] = __TIME;

                if($adv_id = K::M('waimai/adv')->create($data)){                                
	                $this->msgbox->add('添加成功');
	                if(!$type = (int)$this->GP('type')){
		                $type = 1;
		            }
	                if($type == 1){
	                    $this->msgbox->set_data('forward', $this->mklink('shop/adv/index'));
	                }else{
	                    $this->msgbox->set_data('forward', $this->mklink('shop/adv/create'));
	                }
	            }else{
	            	$this->msgbox->add('添加失败', 217);
	            }
            }           
        }else{
           $this->tmpl = 'shop/adv/create.html';
        }  
    }

    public function edit($adv_id = null)
    {
        if(!($adv_id = (int)$adv_id) && !($adv_id = $this->GP('adv_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('waimai/adv')->detail($adv_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($detail['shop_id'] != $this->shop_id){
            $this->msgbox->add('非法操作', 213);
        }else if($data = $this->checksubmit('data')){
        	if(!$data = $this->check_fields($data, 'title,link,stime,ltime,orderby')){
        		$this->msgbox->add('非法数据提交', 210);
        	}else if(!$data['title']){
                $this->msgbox->add('标题不能为空', 211);
            }/*else if(!$data['link']){
                $this->msgbox->add("链接不能为空",212);
            }*/else if(!$data['stime'] || !$data['ltime']){
               	$this->msgbox->add('时间设置有误',213);
           	}else if((strtotime($data['ltime'])+86399 <= (strtotime($data['stime'])))){
               	$this->msgbox->add('结束时间不能早于开始时间',214);
           	}else if((strtotime($data['ltime'])+86399) < __TIME){
               	$this->msgbox->add('结束时间不能早于当前时间', 215);
           	}else{
            	if($_FILES['data']){
	            	//$thumbs = array('photo'=>'800', 'thumb'=>'300X300');
	        		$thumbs = array();
	                foreach($_FILES['data']['name']['photo'] as $k=>$v){
	                    $attachs[$k] = array(
	                        'name' => $v,
	                        'type' => $_FILES['data']['type']['photo'][$k],
	                        'tmp_name' => $_FILES['data']['tmp_name']['photo'][$k],
	                        'error' => $_FILES['data']['error']['photo'][$k],
	                        'size' => $_FILES['data']['size']['photo'][$k]
	                    );
	                }
	                $upload = K::M('magic/upload');
	                foreach($attachs as $k=>$attach){
	                    if($attach['error'] == UPLOAD_ERR_OK){
	                        if($a = $upload->upload($attach, 'wmproduct')){
	                            if($k == 0){
	                                $data['photo'] = $a['photo'];
	                            }else{
	                                $photos[$k] = $a['photo'];
	                            }
	                        }
	                    }
	                }
	            }
            
                $data['stime'] = strtotime($data['stime']);
           		$data['ltime'] = strtotime($data['ltime']) + 86399;

                if($adv_id = K::M('waimai/adv')->update($adv_id, $data)){                                
	                $this->msgbox->add('修改成功');
	                $this->msgbox->set_data('forward', $this->mklink('shop/adv/index'));	                
	            }else{
	            	$this->msgbox->add('修改失败', 217);
	            }
            } 
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'shop/adv/edit.html';
        }       
    }

    public function delete($adv_id=null, $force=false)
    {
        if($adv_id = (int)$adv_id){
            if(!$detail = K::M('waimai/adv')->detail($adv_id, true)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else if($detail['shop_id'] != $this->shop_id){
                $this->msgbox->add('非法操作', 212);
            }else{
                if(K::M('waimai/adv')->delete($adv_id, $force)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('adv_id')){
        	if($force = $this->GP('force')){
        		$force = true;
        	}else{
        		$force = false;
        	}

            if(K::M('waimai/adv')->delete($ids, $force)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 213);
        }
    }
}