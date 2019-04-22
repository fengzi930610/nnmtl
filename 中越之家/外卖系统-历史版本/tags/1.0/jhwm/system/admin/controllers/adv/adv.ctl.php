<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: adv.ctl.php 6080 2014-08-13 15:20:01Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Adv_Adv extends Ctl
{
    
    public function index($page=1)
    {
    	$filter = $pager = array();
    	$pager['page'] = max(intval($page), 1);
    	$pager['limit'] = $limit = 50;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['adv_id']){
                $filter['adv_id'] = $SO['adv_id'];
            }else{
                if($SO['title']){$filter['title'] = "LIKE:%".$SO['title']."%";}
                if($SO['key']){$filter['key'] = "LIKE:%".$SO['key']."%";}
                if(is_array($SO['dateline'])){
                    if($SO['dateline'][0] && $SO['dateline'][1]){
                        $a = strtotime($SO['dateline'][0]); 
                        $b = strtotime($SO['dateline'][1]);
                        $filter['dateline'] = $a."~".$b;
                    }
                }
                if(is_numeric($SO['audit'])){
                    $filter['audit'] = $SO['audit'] ? 1 : 0;
                }
            }
        }
        $filter['closed'] = '0';
        if($items = K::M('adv/adv')->items($filter, null, $page, $limit, $count)){
        	$pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
            $this->pagedata['theme_list'] = K::M('system/theme')->fetch_all();
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['from_list'] = K::M('adv/adv')->from_list();
        $this->tmpl = 'admin:adv/adv/items.html';
    }
    public function so()
    {
        $this->tmpl = 'admin:adv/adv/so.html';
    }
    public function detail($adv_id=null, $page=1)
    {
        if(!$adv_id = intval($adv_id)){
            $this->msgbox->add('未指定广告位的ID', 211);
        }else if(!$detail = K::M('adv/adv')->adv($adv_id)){
            $this->msgbox->add('你要管理的广告位不存在', 212);
        }else{
            $pager = array();
            $pager['page'] = $page = max((int)$page, 1);
            $pager['limit'] = $limit = 30;
            $pager['count'] = $count = 0;        	
            if($items = K::M('adv/item')->items(array('adv_id'=>$adv_id, 'closed'=>0), null, $page, $limit, $count)){
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($adv_id, '{page}')));
                $this->pagedata['items'] = $items;
            }
            $this->pagedata['pager'] = $pager;
            $this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:adv/adv/detail.html';
        }
    }
    public function config($adv_id=null)
    {	
		if($data = $this->checksubmit('data')){
			K::M('adv/adv')->update($data['adv_id'], array('tmpl'=>$data['tmpl']));
			$this->msgbox->add("修改广告模板成功");
		}else{
			if(!($adv_id = intval($adv_id)) && !($adv_id = $this->GP('adv_id'))){
				$this->msgbox->add('未指定广告位的ID', 211);
			}else if(!$detail = K::M('adv/adv')->adv($adv_id)){
				$this->msgbox->add('你要管理的广告位不存在', 212);
			}else{
				$this->pagedata['detail'] = $detail;
				$this->pagedata['tmpl'] = stripslashes($detail['tmpl']);
				$this->tmpl = 'admin:adv/adv/config.html';
			}
		}
		
    }
    public function code($adv_id=null)
    {
        if(!($adv_id = intval($adv_id)) && !($adv_id = $this->GP('adv_id'))){
            $this->msgbox->add('未指定广告位的ID', 211);
        }else if(!$detail = K::M('adv/adv')->adv($adv_id)){
            $this->msgbox->add('你要管理的广告位不存在', 212);
        }else if($data = $this->checksubmit('data')){
            K::M('adv/adv')->update($adv_id, $data['tmpl']);
            $this->msgbox->add("修改广告模板成功");
        }else{
            $this->pagedata['detail'] = $detail;
            $code['widget'] = '<{adv id="'.$adv_id.'" name="'.$detail['title'].'" city_id=$request.city_id}>';
            $code['js'] = '<script src="'.$site['url'].'/index.php?market-adv-'.$adv_id.'"></script>';
            $code['widget'] = K::M('content/html')->encode($code['widget']);
            $code['js'] = K::M('content/html')->encode($code['js']);
            $this->pagedata['code'] = $code;
			
            $this->tmpl = 'admin:adv/adv/code.html';
        }
    }
    public function create()
    {
        if($data = $this->checksubmit('data')){
            if($adv_id = K::M('adv/adv')->create($data)){
                $this->msgbox->add('修改内容成功');
                $this->msgbox->set_data('forward', '?adv/adv-index.html');
            }
        }else{
            $this->pagedata['themes'] = K::M('system/theme')->options();
            $this->pagedata['from_list'] = K::M('adv/adv')->from_list();
            $this->tmpl = 'admin:adv/adv/create.html';
        }
    }
    public function edit($adv_id=null)
    {
        if(!($adv_id = intval($adv_id)) && !($adv_id = intval($this->GP('adv_id')))){
            $this->msgbox->add('未指要修改广告的ID', 211);
        }else if(!$detail = K::M('adv/adv')->adv($adv_id)){
            $this->msgbox->add('你要修改的广告位不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else if(K::M('adv/adv')->update($adv_id, $data)){
                $this->msgbox->add('修改内容成功');
                $this->msgbox->set_data('forward', '?adv/adv-index.html');
            }
        }else{
            $this->pagedata['themes'] = K::M('system/theme')->options();
            $this->pagedata['from_list'] = K::M('adv/adv')->from_list();
        	$this->pagedata['detail'] = $detail;
        	$this->tmpl = 'admin:adv/adv/edit.html';
        }
    }
    public function delete($pk=null)
    {
        if(!empty($pk)){
            if(K::M('adv/adv')->delete($pk)){
                $this->msgbox->add('删除成功');
            }
        }else if($pks = $this->GP('adv_id')){
            if(K::M('adv/adv')->delete($pks)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }



    //异步上传文件
    public function uploadimg()
    {
        $thumbs = array('photo'=>'800');
        $attach = $_FILES['paotui'];
        if($data = K::M('magic/upload')->upload($attach, 'adv', null)){
            $this->msgbox->set_data('data', array('photo'=>$data['photo']));
        }else{
            $this->msgbox->add('上传图片失败', 501);
        }
        $this->msgbox->json();
    }


}