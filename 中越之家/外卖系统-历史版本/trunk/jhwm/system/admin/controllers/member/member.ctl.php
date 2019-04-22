<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: member.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Member_Member extends Ctl
{
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 0;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if($SO['from']){$filter['from'] = $SO['from'];}
            if($SO['nickname']){$filter['nickname'] = "LIKE:%".$SO['nickname']."%";}
            if($SO['mail']){$filter['mail'] = "LIKE:%".$SO['mail']."%";}
            if($SO['mobile']){$filter['mobile'] = "LIKE:%".$SO['mobile']."%";}
            if($SO['realname']){$filter['realname'] = "LIKE:%".$SO['realname']."%";}
            if($SO['regip']){$filter['regip'] = "LIKE:%".$SO['regip']."%";}
            if($SO['closed']){
                $filter['closed'] = $SO['closed'];
            }else{
                //$filter['closed'] = array(0, 1, 2);
                $filter['closed'] = 0;
            }
            if(is_array($SO['lastlogin'])){if($SO['lastlogin'][0] && $SO['lastlogin'][1]){$a = strtotime($SO['lastlogin'][0]); $b = strtotime($SO['lastlogin'][1]);$filter['lastlogin'] = $a."~".$b;}}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1]);$filter['dateline'] = $a."~".$b;}}
            
            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array('nickname'=>"LIKE:%".$SO['keywords']."%", 'mobile'=>"LIKE:%".$SO['keywords']."%");
            }
        }else{
             $filter['closed'] = 0;
        }
        $member_money = K::M('member/member')->sum(array('closed'=>0),'money');
        $this->pagedata['total'] = $member_money;
        if($items = K::M('member/member')->items($filter, array('uid'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/member/items.html';
    }
    public function so($target=null, $multi=null)
    {
        if($target){
            $pager['multi'] = $multi == 'Y' ? 'Y' : 'N';
            $pager['target'] = $target;
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/member/so.html';
    }
    public function dialog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['from'] = 'all';
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['uid']){$filter['uid'] = $SO['uid'];}
            if($SO['nickname']){$filter['nickname'] = "LIKE:%".$SO['nickname']."%";}
            if($SO['mobile']){$filter['mobile'] = "LIKE:%".$SO['mobile']."%";}
            if($SO['regip']){$filter['regip'] = "LIKE:%".$SO['regip']."%";}
            if(is_array($SO['lastlogin'])){if($SO['lastlogin'][0] && $SO['lastlogin'][1]){$a = strtotime($SO['lastlogin'][0]); $b = strtotime($SO['lastlogin'][1]);$filter['lastlogin'] = $a."~".$b;}}
            if(is_array($SO['dateline'])){if($SO['dateline'][0] && $SO['dateline'][1]){$a = strtotime($SO['dateline'][0]); $b = strtotime($SO['dateline'][1]);$filter['dateline'] = $a."~".$b;}}
        }
        $filter['closed'] = 0;
        if($items = K::M('member/member')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO, 'multi'=>$multi));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/member/dialog.html';
    }
    public function ucard($uid=null)
    {
        if(!$uid = (int)$uid){
            $this->msgbox->add('未指定要查看的ID', 211);
        }else if(!$member = K::M('member/member')->detail($uid)){
            $this->msgbox->add('要查看的用户不存在', 212);
        }else{
            $this->pagedata['detail'] = $member;
            $this->tmpl = 'admin:member/member/ucard.html';
        }
    }
    public function manager($uid)
    {
        if(K::M('member/auth')->manager($uid)) {
            $cfg = $this->system->config->get('site');
            $link = $cfg['siteurl'].'/index.php?ucenter/member-index.html';
            header("Location: {$link}");
            exit();
        }
    }
    public function ulock($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = '2';
        if($items = K::M('member/member')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/member/items.html';
    }
    public function recycle($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 1;

        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array('nickname'=>"LIKE:%".$SO['keywords']."%", 'mobile'=>"LIKE:%".$SO['keywords']."%");
            }
        }
        if($items = K::M('member/member')->items($filter, array('uid'=>'desc'), $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));;
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:member/member/recycle.html';
    }
    public function regain($uid=null)
    {
        if($uid = intval($uid)){
            if(K::M('member/member')->regain($uid)){
                $this->msgbox->add('恢复会员帐号成功');
            }
        }else if($uids = $this->GP('uid')){
            if(K::M('member/member')->regain($uids)){
                $this->msgbox->add('批量恢复会员帐号成功');
            }
        }else{
            $this->msgbox->add('未指定要恢复会员', 401);
        }
    }
    public function money($uid=null)
    {
        if(!($uid = (int)$uid) && !($uid = (int)$this->GP('uid'))){
            $this->msgbox->add('未指定要修改的用户ID', 211);
        }else if(!$detail = K::M('member/member')->detail($uid)){
            $this->msgbox->add('指定的用户不存在或已经删除', 212);
        }else if($this->checksubmit('data')){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 213);
            }else if(!$money = $data['money']){
                $this->msgbox->add('非法的充值金额', 214);
            }else if(!$intro= trim($data['intro'])){
                $this->msgbox->add('没有填写充值说明', 215);
            }else if($money<0 && (abs($money)>$detail['money'])){
                $this->msgbox->add('减去的余额不能大于现有余额', 216);
            }else if(K::M('member/money')->update($uid, $money, $intro)){
                $bills_data  = array();
                $bills_data['amount'] = $money;
                if(K::M('chongzhi/bills')->create($bills_data)){
                    $filter = array();
                    $filter['bills_sn'] = date('Ymd');
                    $bills = K::M('chongzhi/bills')->find($filter);
                    $log_data = array();
                    $log_data['uid'] = $uid;
                    $log_data['amount'] = $money;
                    $log_data['bills_id'] = $bills['bills_id'];
                    $log_data['reason'] = $intro;
                    $log_data['dateline'] = __TIME;
                    K::M('chongzhi/billslog')->create($log_data);
                }

                $this->msgbox->add('充值余额成功');
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:member/member/money.html';
        }
    }
    public function detail($pk)
    {
        $this->pagedata['detail'] = K::M('member/member')->detail($pk);
        $this->pagedata['addrs'] = K::M('member/addr')->items(array('uid'=>$this->pagedata['detail']['uid']));
        $this->tmpl = 'admin:member/member/detail.html';
    }
    public function create()
    {
        $this->msgbox->add('禁用的功能', 202)->response();exit;
        if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                $data['passwd'] = md5($data['passwd']);
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'face')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if($uid = K::M('member/member')->create($data)){
                    $this->msgbox->add('修改内容成功');
                    $this->msgbox->set_data('forward', '?member/member-index.html');
                }
            }
        }else{
            $this->tmpl = 'admin:member/member/create.html';
        }
    }
    public function edit($uid=null)
    {
        //print_r($uid);exit;
        if(!($uid = (int)$uid) && !($uid = (int)$this->GP('uid'))){
            $this->msgbox->add('未指定要修改的用户ID', 211);
        }else if(!$detail = K::M('member/member')->detail($uid)){
            $this->msgbox->add('指定的用户不存在或已经删除', 212);
        }else if($this->checksubmit()){
            if(!$data = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 201);
            }else{
                if($data['passwd'] == '******'){
                    unset($data['passwd']);
                }else{
                    $passwd = trim($data['passwd']);
                    if(K::M('member/account')->update_passwd($uid, $passwd)){
                        $data['passwd'] = md5($passwd);
                    }
                }
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'face')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if(K::M('member/member')->update($uid, $data)){
                    $this->msgbox->add('修改内容成功');
                }
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:member/member/edit.html';
        }
    }
    public function face()
    {
        if(!$uid = $this->GP('itemId')){
            $this->msgbox->add('未指定要修改的用户！',201);
        }else if(!$member = K::M('member/member')->detail($uid)){
            $this->msgbox->add('用户不存在，请刷新重式！',202);
        }else if(!$data = file_get_contents("php://input")){
            $this->msgbox->add('图片数据上传失败',203);
        }else if(K::M('member/face')->update_face($uid, null, $data)){
            $this->msgbox->add('更新会员头像成功');
        }
        $this->msgbox->json();
    }
    public function delete($uid=null, $force=false)
    {
        if($uid = intval($uid)){
            if(K::M('member/member')->delete($uid, $force)){
                $this->msgbox->add('删除成功');
            }
        }else if($uids = $this->GP('uid')){
            $force = $this->GP('force') ? $this->GP('force') : $force;
            if(K::M('member/member')->delete($uids, $force)){
                $this->msgbox->add('批量删除成功');
            }
        }else{
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }
}
