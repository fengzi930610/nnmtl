<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Peicard_Card extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 0;
        if($SO = $this->GP('SO')){
            if($SO['title']){
                $filter['title'] = 'LIKE:%'.$SO['title'].'%';
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1])+86400;
                    $filter['dateline'] = $a."~".$b;
                }
            }
            if($SO['keywords']){
                $filter['title'] = 'LIKE:%'.$SO['keywords'].'%';
            }
        }
        
        if($items = K::M('peicard/card')->items($filter, array('orderby'=>'asc', 'card_id'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:peicard/card/items.html';
    }

    public function ckeckdata($data, $dateline=true)
    {
        if(!$data || !is_array($data)){
            $this->msgbox->add('参数有误', 211)->response();
        }else if(!$title = trim($data['title'])){
            $this->msgbox->add('标题不能为空', 212)->response();
        }else if(!($days = (int)$data['days']) || ($days <= 0)){
            $this->msgbox->add('有效期限设置有误', 213)->response();
        }else if(!($amount = (float)$data['amount']) || ($amount <= 0)){
            $this->msgbox->add('会员卡金额设置有误', 214)->response();
        }else if(!($limits = (int)$data['limits']) || ($limits <= 0)){
            $this->msgbox->add('单日限制次数设置有误', 215)->response();
        }else if(!($reduce = (float)$data['reduce']) || ($reduce <= 0)){
            $this->msgbox->add('每单减免金额设置有误', 216)->response();
        }else if(!($template = $data['template']) || !in_array($template, array(1, 2, 3, 4, 5))){
            $this->msgbox->add('会员卡模板设置有误', 217)->response();
        }else{
            $newdata = array(
                'title'=>$title,
                'days'=>$days,
                'amount'=>$amount,
                'limits'=>$limits,
                'reduce'=>$reduce,
                'template'=>$template,
                'photo'=>$data['photo']
                );
            if($dateline){
                $newdata['dateline'] = __TIME;
            }
            return $newdata;
        }
    }

    public function create()
    {
        if(($count = K::M('peicard/card')->count(array('closed'=>0))) >=5){
            $this->msgbox->add('会员卡最多生成5张', 210);
            $this->msgbox->set_data('forward', $this->mklink('peicard/card'));
        }else if($data = $this->checksubmit('data')){ 
            if(!$data = $this->ckeckdata($data)){
                $this->msgbox->add('数据有误', 211);
            }else if(K::M('peicard/card')->create($data)){
                $this->msgbox->add('创建内容成功');
            }else{
                $this->msgbox->add('创建内容失败', 212);
            }  
        }else{
            $this->tmpl = 'admin:peicard/card/create.html';
        }
    }

    public function edit($card_id=null)
    {
        if(!($card_id = (int)$card_id) && !($card_id = $this->GP('card_id'))){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('peicard/card')->detail($card_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else if($data = $this->checksubmit('data')){
            if(!$data = $this->ckeckdata($data, false)){
                $this->msgbox->add('数据有误', 213);
            }else if(K::M('peicard/card')->update($card_id, $data)){
                $this->msgbox->add('修改内容成功');
            }else{
                $this->msgbox->add('修改内容失败', 214);
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:peicard/card/edit.html';
        }
    }

    public function detail($card_id=null)
    {
        if(!($card_id = (int)$card_id)){
            $this->msgbox->add('未指定要修改的内容ID', 211);
        }else if(!$detail = K::M('peicard/card')->detail($card_id)){
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:peicard/card/detail.html';
        }
    }

    public function delete($card_id=null)
    {
        if($card_id = (int)$card_id){
            if(!$detail = K::M('peicard/card')->detail($card_id)){
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            }else{
                if(K::M('peicard/card')->delete($card_id)){
                    $this->msgbox->add('删除内容成功');
                }
            }
        }else if($ids = $this->GP('card_id')){
            if(K::M('peicard/card')->delete($ids)){
                $this->msgbox->add('批量删除内容成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function so()
    {
        $this->tmpl = 'admin:peicard/card/so.html';
    }  
}