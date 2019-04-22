<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: admin.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Admin_Admin extends Ctl
{
	
	public function index($page=1)
	{
		$pager['page'] = $page = max(intval($page), 1);
		$pager['limit'] = $limit = 50;
		$pager['count'] = $count = 0;
		if($items = K::M('admin/view')->items(null, null, $page, $limit, $count)){
			$pager['count'] = $count;
			$pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
			$this->pagedata['items'] = $items;
		}
		$this->pagedata['role_list'] = K::M('admin/role')->fetch_all();
		$this->pagedata['pager'] = $pager;
		$this->tmpl = 'admin:admin/admin/index.html';
	}

	public function create()
	{
		if($this->admin->role['role'] == 'editor'){
			$this->msgbox->add('您没有权限创建或修改管理员',201);
		}else{ 
			$this->pagedata['role_list'] = K::M('admin/role')->fetch_all();
			$this->tmpl = 'admin:admin/admin/detail.html';
		}
	}

	public function edit($ID)
	{
		if($this->admin->role['role'] == 'editor'){
			$this->msgbox->add('您没有权限创建或修改管理员',201);
		}else if(!$ID = intval($ID)){
			$this->msgbox->add('没有指定要修改的管理员',202);
		}else if(!$detail = K::M('admin/view')->admin($ID)){
			$this->msgbox->add('你要修改的管理员不存在或已经删除',201);
		}else{
			$this->pagedata['detail'] = $detail;
			$this->pagedata['role_list'] = K::M('admin/role')->fetch_all();
			$this->tmpl = 'admin:admin/admin/detail.html';
		}
	}

	public function save()
	{
		if($this->admin->admin['role'] == 'editor'){
			$this->msgbox->add('您没有权限创建或修改管理员',201);
		}else if(!$data = $this->GP('data')){
			$this->msgbox->add('非法的数据提交',202);
		}else if($ID = $this->GP('admin_id')){
			if(empty($data['passwd'])){
				unset($data['passwd']);
			}
			if(K::M('admin/handler')->update($ID, $data)){
				$this->msgbox->add('修改管理员成功');
			}
		}else if(K::M('admin/handler')->create($data)){
			$this->msgbox->add('添加管理员成功');
		}
	}

    public function delete($admin_id)
    {
        if(!empty($admin_id)){
            if(K::M('admin/handler')->delete($admin_id, true)){
                $this->msgbox->add('删除管理员成功');
            }
        }else if($pks = $this->GP('admin_id')){
            if(K::M('admin/handler')->delete($pks, true)){
                $this->msgbox->add('批量删除管理员成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的管理员ID', 401);
        }
    }
}