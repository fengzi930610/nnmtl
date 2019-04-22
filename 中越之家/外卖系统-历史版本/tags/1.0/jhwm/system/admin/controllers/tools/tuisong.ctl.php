<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: developer.ctl.php 2034 2013-12-07 03:08:33Z $
 */
class Ctl_Tools_Tuisong extends Ctl
{
    /*推送页*/
    public function index()
    {
        if($data = $this->GP('data')){
            $content  = $data['content'];
            $range    = intval($data['range']);
            require_once "../system/libs/JPush/JPush.php";
            // $app_key  = "8a4542ab83c557d88d211a74";
            // $secret   = "ee329de7acae2c3505b4d05d";
            $cfg = $this->system->config->get('apppush');
            $app_key = $cfg['appkey'];
            $secret  = $cfg['secret'];
            $client   =  new JPush($app_key, $secret);
            $pushLoad = $client->push();
            $pushLoad->setPlatform('ios', 'android');
            if($range === 1){
                $pushLoad->addAllAudience();
            }else if($range === 2){
                $pushLoad->addTag($data['tags']);
            }else if($range === 3){
                $register = $data['register'];
                $pushLoad->addRegistrationId($register);
            }
            $pushLoad->addAndroidNotification($content, 'Anroid 标题', 1);
            $pushLoad->addIosNotification($content, 'default', '+1', true, 'iOS category');
            $respone = $pushLoad->send();
            $respone = (array)$respone;
            if(isset($respone['data'])){
                exit(json_encode(array('status'=>200)));
            }else{
                exit(json_encode(array('status'=>0)));
            }
        }else{
            $items = K::M('tuisong/group')->items(null, array('order'=>'asc'));
            $this->pagedata['items'] = $items;
            $this->tmpl = "admin:tools/tuisong/index.html";
        }
    }
    /*分组列表*/
    public function tags()
    {
        $items = K::M('tuisong/group')->items(null, array('order'=>'asc'));
        $this->pagedata['items'] = $items;
        $this->tmpl = "admin:tools/tuisong/tags/tags.html";
    }
    /*添加推送标签*/
    public function addtag()
    {
        if($data = $this->GP('data')){
            $data['dateline'] = time();
            $con = K::M('tuisong/group')->items(array('name'=>$data['name']));
            if(!empty($con)){
                $this->msgbox->add('英文标示已存在,请重换英文标示',200);
            }else if(K::M('tuisong/group')->create($data)){
                $this->msgbox->add('添加分组成功');
                $this->msgbox->set_data('forward', '?tools/tuisong-tags.html');
            }
        }else{
            $this->tmpl = "admin:tools/tuisong/tags/addtag.html";
        }
    }
    /*添加推送标签*/
    public function edittag($tui_id = null)
    {
        if(!$tui_id){
            $this->msgbox->add('参数不正确',200);
        }else if(!$detail=K::M('tuisong/group')->detail($tui_id)){
            $this->msgbox->add('非法操作',201);
        }else if($data = $this->GP('data')){
            $data['dateline'] = time();
            $con = K::M('tuisong/group')->items(array('name'=>$data['name']));
            if(!empty($con)){
                $this->msgbox->add('英文标示已存在,请重换英文标示',202);
            }else if(K::M('tuisong/group')->update($tui_id, $data)){
                $this->msgbox->add('编辑分组成功');
                $this->msgbox->set_data('forward', '?tools/tuisong-tags.html');
            }
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = "admin:tools/tuisong/tags/edittag.html";
        }
    }
    /*删除推送标签*/
    public function removetag($tui_id = null)
    {
        if(!$tui_id){
            $this->msgbox->add('参数不正确', 200);
        }else if(!$detail=K::M('tuisong/group')->detail($tui_id)){
            $this->msgbox->add('非法操作', 201);
        }else if($detail['number']>0){
            $this->msgbox->add('抱歉,该分组已被使用,无法删除',202);
        }else if(K::M('tuisong/group')->delete($tui_id)){
            $this->msgbox->add('删除推送分组成功');
        }else{
            $this->msgbox->add('删除失败,未知错误', 203);
        }
    }
    /* 设置标签 */
    public function settag($uid = null)
    {
        if(empty($uid)){
            $this->msgbox->add('参数不正确', 200);
        }else if($data = $this->GP('data')){
            $data = implode(',', $data);
            $model = K::M('tuisong/tuisong');
            if($model->detail($uid)){
                $model->update($uid, array('tags'=>$data));
            }else{
                $data = array(
                    'uid'         => $uid,
                    'register_id' => '',
                    'tags'        => $data,
                    'dateline'    => time()
                );
                $model->create($data);
            }
            exit(json_encode(array('status'=>200)));
        }else{
            $items = K::M('tuisong/group')->items();
            $this->pagedata['items'] = $items;
            $this->pagedata['uid'] = $uid;
            $this->tmpl = "admin:tools/tuisong/tags/settag.html";
        }
    }
    /*推送-会员列表*/
    public function member($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
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
        }else{
            $filter['closed'] = array(0, 1, 2);
        }
        if($items = K::M('member/member')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO));;
        }
        $member_ids = array();
        foreach($items as $v) {
            array_push($member_ids, $v['uid']);
        }
        $member_ids = implode(',', array_unique($member_ids));
        $tuisong = K::M('tuisong/tuisong')->tuisong_by_uids($member_ids);
        foreach($items as $k=>$v) {
            foreach($tuisong as $ts){
                if($ts['uid'] == $v['uid']){
                    $items[$k]['register_id'] = $ts['register_id'];
                    $items[$k]['tags'] = $ts['tags'];
                    continue;
                }else if(!isset($items[$k]['register_id'])){
                    $items[$k]['register_id'] = '';
                    $items[$k]['tags'] = '';
                }
            }
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:tools/tuisong/member/member.html";
    }
    /*独立推送*/
    public function mtui($register_id = null)
    {
        $this->pagedata['register_id']=$register_id;
        $this->tmpl = "admin:tools/tuisong/member/tuisong.html";
    }
}
