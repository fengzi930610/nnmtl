<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/11
 * Time: 18:01
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Group_Staff extends Ctl
{
    protected $_status = array(0 => '离线', 1 => '在线');
    protected $_audit = array(0 => '待审核', 1 => '通过审核', 2 => '审核失败');
    protected $_verify = array(0 => '待审核', 1 => '通过认证', 2 => '认证被拒绝');
    protected $_from = array('weixiu' => '维修', 'paotui' => '跑腿', 'house' => '家政');

    public function weiaudit($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 50;
        $filter['from'] = 'paotui';
        $filter['audit'] = array(0, 2);
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            }
            if ($SO['city_id']) {
                $filter['city_id'] = $SO['city_id'];
            }
            if ($SO['from']) {
                $filter['from'] = $SO['from'];
            }
            if ($SO['name']) {

                $filter['name'] = 'LIKE:%' . $SO['name'] . '%';
            }
            if ($SO['mobile']) {
                $filter['mobile'] = 'LIKE:%' . $SO['mobile'] . '%';;
            }
            if ($SO['score']) {
                $filter['score'] = $SO['score'];
            }
            if ($SO['verify_name']) {
                $filter['verify_name'] = $SO['verify_name'];
            }
            if ($SO['status']) {
                $filter['status'] = $SO['status'];
            }
            if ($SO['audit']) {
                $filter['audit'] = $SO['audit'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'name'=>"LIKE:%".$SO['keywords']."%",
                    'mobile'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }
        $filter['closed'] = 0;
        $level_ids =$group_ids =  array();
        if ($items = K::M('staff/staff')->items($filter, array('staff_id' => 'DESC'), $page, $limit, $count)) {
            foreach ($items as $k=>$v){
                $level_ids[$v['level_id']] = $v['level_id'];
                $group_ids[$v['group_id']] = $v['group_id'];
            }
            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            $level_list = K::M('staff/level')->items_by_ids($level_ids);
            foreach ($items as $kk => $vv) {
                $items[$kk]['group'] = $group_list[$vv['group_id']];
                $items[$kk]['level'] = $level_list[$vv['level_id']]?$level_list[$vv['level_id']]:array();
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['audit'] = $this->_audit;
        $this->pagedata['status'] = $this->_status;
        $this->pagedata['verify'] = $this->_verify;
        $this->pagedata['from'] = $this->_from;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['type'] = 1;
        $this->tmpl = 'admin:group/staff/items.html';
    }

    public function index($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 50;
        $filter['from'] = 'paotui';
        $filter['audit'] = 1;
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            }
            if ($SO['city_id']) {
                $filter['city_id'] = $SO['city_id'];
            }
            if ($SO['from']) {
                $filter['from'] = $SO['from'];
            }
            if ($SO['name']) {
                $filter['name'] = 'LIKE:%' . $SO['name'] . '%';
            }
            if ($SO['mobile']) {
                $filter['mobile'] = 'LIKE:%' . $SO['mobile'] . '%';;
            }
            if ($SO['score']) {
                $filter['score'] = $SO['score'];
            }
            if ($SO['verify_name']) {
                $filter['verify_name'] = $SO['verify_name'];
            }
            if ($SO['status']) {
                $filter['status'] = $SO['status'];
            }
            if ($SO['audit']) {
                $filter['audit'] = $SO['audit'];
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'name'=>"LIKE:%".$SO['keywords']."%",
                    'mobile'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }
        $staff_money = K::M('staff/staff')->sum(array('closed'=>0),'money');
        $filter['closed'] = 0;
        $group_ids = array();
        if ($items = K::M('staff/staff')->items($filter, array('staff_id' => 'DESC'), $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
            $level_ids = array();
            foreach ($items as $k => $v) {
                $group_ids[] = $v['group_id'];
                $level_ids[$v['level_id']] = $v['level_id'];
            }

            $group_list = K::M('pei/group')->items_by_ids($group_ids);
            $level_list = K::M('staff/level')->items_by_ids($level_ids);
            foreach ($items as $kk => $vv) {
                $items[$kk]['group'] = $group_list[$vv['group_id']];
                $items[$kk]['level'] = $level_list[$vv['level_id']]?$level_list[$vv['level_id']]:array();
            }
        }
        $this->pagedata['total'] = $staff_money;

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['audit'] = $this->_audit;
        $this->pagedata['status'] = $this->_status;
        $this->pagedata['verify'] = $this->_verify;
        $this->pagedata['from'] = $this->_from;
        $this->pagedata['pager'] = $pager;
        $this->pagedata['type'] = 2;
        $this->tmpl = 'admin:group/staff/items.html';
    }

    public function so()
    {
        $this->tmpl = 'admin:group/staff/so.html';
    }
    public function so2()
    {
        $this->tmpl = 'admin:staff/index/so.html';
        //$this->tmpl = 'admin:group/staff/so2.html';
    }

    public function wso()
    {
        $this->tmpl = 'admin:group/staff/wso.html';
    }



    public function dialog($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            } else {
                if ($SO['from']) {
                    $filter['from'] = $SO['from'];
                }
                if ($SO['mobile']) {
                    $filter['mobile'] = "LIKE:%" . $SO['mobile'] . "%";
                }
                if ($SO['name']) {
                    $filter['name'] = "LIKE:%" . $SO['name'] . "%";
                }
                if (is_array($SO['dateline'])) {
                    if ($SO['dateline'][0] && $SO['dateline'][1]) {
                        $a = strtotime($SO['dateline'][0]);
                        $b = strtotime($SO['dateline'][1]) + 86400;
                        $filter['dateline'] = $a . "~" . $b;
                    }
                }
            }
        }
        $filter['closed'] = 0;
        if ($items = K::M('staff/staff')->items($filter, null, $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO, 'multi' => $multi));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/staff/dialog.html';
    }

    public function detail($staff_id = null)
    {
        if (!$staff_id = (int)$staff_id) {
            $this->msgbox->add('未指定要查看内容的ID', 211);
        } else if (!$detail = K::M('staff/staff')->detail($staff_id)) {
            $this->msgbox->add('您要查看的内容不存在或已经删除', 212);
        } else {
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'admin:group/staff/detail.html';
        }
    }

    public function create()
    {
        if ($data = $this->checksubmit('data')) {
            if (!$data['city_id']) {
                $this->msgbox->add('请选择城市', 201);
            } else if (!$data['from']) {
                $this->msgbox->add('请选择人员类型', 202);
            } else if (!$data['name']) {
                $this->msgbox->add('请填写人员姓名', 203);
            } else if (!$data['mobile']) {
                $this->msgbox->add('请填写手机号码', 204);
            } else if (!$data['passwd']) {
                $this->msgbox->add('请输入密码', 205);
            } else if (($data['tixian_percent'] > 100) || ($data['tixian_percent'] < 0)) {
                $this->msgbox->add('提现比例错误', 206);
            } else if (!$mobile = K::M('verify/check')->vietnamMobile($data['mobile'])) {
                $this->msgbox->add('手机号码错误', 207);
            } else if (K::M('staff/staff')->staff($data['mobile'], 'mobile')) {
                $this->msgbox->add('手机号码已存在', 208);
            } else if (!$data['group_id']) {
                $this->msgbox->add('请选择配送团队', 209);
            }else if((int)$data['is_used']==1&&((int)$data['limit_order']<=0)){
                $this->msgbox->add('限制接单数量设置错误',213);
            }else if(!$level_id = $data['level_id']){
                $this->msgbox->add('请选择骑手等级',214);
            }else if(!$level = K::M('staff/level')->detail($level_id)){
                $this->msgbox->add('所选择的骑手等级不存在',215);
            }else {
                $data['passwd'] = md5($data['passwd']);

                if((int)$data['is_used']==0){
                    $data['limit_order'] = 0;
                }

                $verify = $this->checksubmit('verify');
                if(!$id_name = $verify['id_name']){
                    $this->msgbox->add('真实姓名不能为空', 211)->response();
                }else if(!($id_number = $verify['id_number']) || !($id_number = K::M('verify/check')->id_number($id_number))){
                    $this->msgbox->add('身份证号码有误', 212)->response();
                }else if(!$id_photo = $verify['id_photo']){
                    $this->msgbox->add('请上传身份证图片', 212)->response();
                }else{
                    $id_photo = K::M('magic/upload')->geturl($id_photo);
                    $verify_data = array('id_name'=>$id, 'id_number'=>$id_number, 'id_photo'=>$id_photo, 'verify'=>1, 'verify_time'=>__TIME);
                    $data['audit'] = 1;
                    $data['verify_name'] = 1;
                }

                if ($staff_id = K::M('staff/staff')->create($data)) {
                    $verify_data['staff_id'] = $staff_id;
                    K::M('staff/verify')->create($verify_data);
                    $this->msgbox->add('添加内容成功');
                    $this->msgbox->set_data('forward', '?group/staff-index.html');
                } else {
                    $this->msgbox->add('添加失败', 210);
                }
            }
           /* echo  '<pre>';
            print_r($this->system->db->SQLLOG());exit;*/

        } else {

            $this->pagedata['level'] = K::M('staff/level')->fetch_all();
            $group_list = K::M('pei/group')->fetch_all();
            foreach ($group_list as $k=>$v){
                $group_list[$k] = $this->filter_fields('group_id,city_id,province_id,group_name',$v);
            }
            $this->pagedata['items'] = json_encode($group_list);
            $this->tmpl = 'admin:group/staff/create.html';
        }
    }

    public function edit($staff_id = null)
    {
        if (!($staff_id = (int)$staff_id) && !($staff_id = $this->GP('staff_id'))) {
            $this->msgbox->add('未指定要修改的内容ID', 211);
        } else if (!$detail = K::M('staff/staff')->detail($staff_id)) {
            $this->msgbox->add('您要修改的内容不存在或已经删除', 212);
        } else if ($data = $this->checksubmit('data')) {

            $update_data = array();
            if (!$data['city_id']) {
                $this->msgbox->add('请选择城市', 201);
            } else if (!$data['name']) {
                $this->msgbox->add('请填写人员姓名', 203);
            } else if (!$data['mobile']) {
                $this->msgbox->add('请填写手机号码', 204);
            } else if (!$data['passwd']) {
                $this->msgbox->add('请输入密码', 205);
            } else if (($data['tixian_percent'] > 100) || ($data['tixian_percent'] < 0)) {
                $this->msgbox->add('提现比例错误', 206);
            } else if (!$mobile = K::M('verify/check')->vietnamMobile($data['mobile'])) {
                $this->msgbox->add('手机号码错误', 207);
            } else if ((K::M('staff/staff')->staff($data['mobile'], 'mobile')) && ($mobile != $detail['mobile'])) {
                $this->msgbox->add('手机号码已存在', 208);
            }else if((int)$data['is_used']==1&&((int)$data['limit_order']<=0)){
                $this->msgbox->add('限制接单数量设置错误',213);
            }else if(!$level_id = $data['level_id']){
                $this->msgbox->add('请选择骑手等级',214);
            }else if(!$level = K::M('staff/level')->detail($level_id)){
                $this->msgbox->add('所选择的骑手等级不存在',215);
            }else if(!$account =$this->checksubmit('account')) {
                $this->msgbox->add('请填写开户行等信息',219);
            }else if((!$account['title'])||(!$account['name'])||(!$account['account'])){
                $this->msgbox->add('请填写开户行信息',220);
            }
            else {
                if((int)$data['is_used']==0){
                    $data['limit_order'] = 0;
                }
                if ($detail['from'] == 'paotui' && !$data['group_id']&&$data['audit']==1) {
                    $this->msgbox->add('请选择配送站')->response();
                }
                if($data['audit']==2){
                    $data['group_id']=0;
                }


                if (K::M('staff/staff')->update($staff_id, $data)) {

                    $addTags = $removeTags = array();
                    if ($data['status'] != $detail['status']) {
                        $addTags[] = 'work_status_' . $data['status'];
                        $removeTags[] = 'work_status_' . $detail['status'];
                    }
                    if ($data['group_id'] != $detail['group_id']) {
                        $addTags[] = 'group_' . $data['group_id'];
                        $removeTags[] = 'group_' . $detail['group_id'];
                    }
                    if ($data['city_id'] != $detail['city_id']) {
                        $addTags[] = 'city_' . $data['city_id'];
                        $removeTags[] = 'city_' . $detail['city_id'];
                    }
                    if ($addTags || $removeTags) {
                        K::M('jpush/device')->update_tags($staff_id, $addTags, $removeTags, 'staff');
                    }
                    if(!$account_id = $account['account_id']){
                        unset($account['account_id']);
                        $account['staff_id'] = $staff_id;
                        $account['type'] = 'nongye';
                        K::M('staff/account')->create($account);
                    }else{
                        unset($account['account_id']);
                     K::M('staff/account')->update($account_id,$account);
                    }


                    $this->msgbox->add('修改内容成功');
                }
            }
        } else {
            $this->pagedata['level'] = K::M('staff/level')->fetch_all();
            $group_list = K::M('pei/group')->fetch_all();
            foreach ($group_list as $k=>$v){
                $group_list[$k] = $this->filter_fields('group_id,city_id,province_id,group_name',$v);
            }
            $this->pagedata['group'] = $group_list;
            $this->pagedata['items'] = json_encode($group_list);
            $this->pagedata['detail'] = $detail;
            $this->pagedata['account'] = K::M('staff/account')->find(array('staff_id'=>$staff_id));
            $this->tmpl = 'admin:group/staff/edit.html';
        }
    }

    public function audit()
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['audit'] = 0;
        if ($items = K::M('staff/staff')->items($filter, null, $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/staff/audit.html';
    }

    public function doaudit($staff_id = null)
    {
        if ($staff_id = (int)$staff_id) {
            if (K::M('staff/staff')->batch($staff_id, array('audit' => 1))) {
                $this->msgbox->add('审核内容成功');
            }
        } else if ($ids = $this->GP('staff_id')) {
            if (K::M('staff/staff')->batch($ids, array('audit' => 1))) {
                $this->msgbox->add('批量审核内容成功');
            }
        } else {
            $this->msgbox->add('未指定要审核的内容', 401);
        }
    }

    public function delete($staff_id = null, $force = false)
    {
        if ($staff_id = (int)$staff_id) {
            if (!$detail = K::M('staff/staff')->detail($staff_id, $force)) {
                $this->msgbox->add('你要删除的内容不存在或已经删除', 211);
            } else {
                if (K::M('staff/staff')->delete($staff_id, $force)) {
                    K::M('jpush/device')->update_tags($staff_id, null, array('work_status_1','login_on','group_'.$detail['group_id']), 'staff');
                    $this->msgbox->add('删除内容成功');
                }
            }
        } else if ($ids = $this->GP('staff_id')) {
            if (K::M('staff/staff')->delete($ids, $force)) {
                $this->msgbox->add('批量删除内容成功');
            }
        } else {
            $this->msgbox->add('未指定要删除的内容ID', 401);
        }
    }

    public function recycle($page = 1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $filter['from'] = 'paotui';
        $pager['limit'] = $limit = 50;
        $filter['closed'] = 1;

        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'name'=>"LIKE:%".$SO['keywords']."%",
                    'mobile'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }

        if ($items = K::M('staff/staff')->items($filter, array('staff_id' => 'DESC'), $page, $limit, $count)) {
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/staff/recycle.html';
    }

    public function regain($staff_id = null)
    {
        if ($staff_id = intval($staff_id)) {
            if (K::M('staff/staff')->regain($staff_id)) {
                $this->msgbox->add('恢复服务人员帐号成功');
            }
        } else if ($staff_ids = $this->GP('staff_id')) {
            if (K::M('member/member')->regain($staff_ids)) {
                $this->msgbox->add('批量恢复服务人员帐号成功');
            }
        } else {
            $this->msgbox->add('未指定要恢复服务人员', 401);
        }
    }

    public function paiorder($order_id, $page = 1)
    {
        $filter = $pager = array();
        if (!($order_id = (int)$order_id) && !($order_id = (int)$this->GP('order_id'))) {
            $this->msgbox->set_data('未指定要派单的单号', 211);
        } else if (!$order = K::M('order/order')->detail($order_id)) {
            $this->msgbox->add('订单不存在或已经删除', 211);
        } else if (!in_array($order['from'], array('waimai', 'paotui', 'weixiu', 'house'))) {
            $this->msgbox->add('该订单不支持派单', 212);
        } else if ($order['staff_id'] > 0) {
            $this->msgbox->add('已经有人接单了，您可以选取消再派单', 212);
        } else if (!$order['pay_status']) {
            $this->msgbox->add('未支付订单不可派单', 213);
        } else if ($order['from'] == 'waimai' && !in_array($order['pei_type'], array(1, 2))) {
            $this->msgbox->add('该订单为商家自送，不可派单', 214);
        } else if (!in_array($order['order_status'], array(0, 1, 2, 3, 4))) {
            $this->msgbox->add('该订单状态不可派单', 215);
        } else if ($order['from'] == 'waimai' && ($order['order_status'] == 0 && (int)$order['pei_type'] !== 2)) {
            $this->msgbox->add('该订单状态不可派单', 215);
        } else {
            $pager['page'] = max(intval($page), 1);
            $pager['limit'] = $limit = 10;
            $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
            if ($SO = $this->GP('SO')) {
                $pager['SO'] = $SO;
                if ($SO['name']) {
                    $filter['name'] = "LIKE:%" . $SO['name'] . "%";
                }
                if ($SO['mobile']) {
                    $filter['mobile'] = "LIKE:%" . $SO['mobile'] . "%";
                }
                if (is_array($SO['lastlogin'])) {
                    if ($SO['lastlogin'][0] && $SO['lastlogin'][1]) {
                        $a = strtotime($SO['lastlogin'][0]);
                        $b = strtotime($SO['lastlogin'][1]) + 86400;
                        $filter['lastlogin'] = $a . "~" . $b;
                    }
                }
                if (is_array($SO['dateline'])) {
                    if ($SO['dateline'][0] && $SO['dateline'][1]) {
                        $a = strtotime($SO['dateline'][0]);
                        $b = strtotime($SO['dateline'][1]) + 86400;
                        $filter['dateline'] = $a . "~" . $b;
                    }
                }
            } else {
                if (!defined('__DEV_MODEL') || !constant('__DEV_MODEL')) { //开发环境忽略坐标
                    //使用此函数计算得到结果后，带入sql查询。
                    $squares = K::M('helper/round')->returnSquarePoint($order['o_lng'], $order['o_lat'], 5); //5KM以内的配送员
                    $filter['lat'] = $squares['left-bottom']['lat'] . '~' . $squares['right-top']['lat'];
                    $filter['lng'] = $squares['left-bottom']['lng'] . '~' . $squares['right-top']['lng'];
                }
            }
            if ($order['from'] == 'waimai') {
                $filter['from'] = 'paotui';
            } else {
                $filter['from'] = $order['from'];
            }
            if ($items = K::M('staff/staff')->items($filter, array('status' => 'DESC'), $page, $limit, $count)) {
                foreach ($items as $k => $v) {
                    $v['order_juli'] = K::M('helper/round')->getdistance($v['lng'], $v['lat'], $order['lng'], $order['lat']);  //距离
                    $items[$k] = $v;
                }
                $pager['count'] = $count;
                $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array($order['order_id'], '{page}')), array('SO' => $SO, 'multi' => $multi));
            }
            $this->pagedata['items'] = $items;
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'admin:group/staff/paiorder.html';
        }
    }

    public function complaint($page=1){
        $filter = $pager = array();
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 50;
        $filter['shop_id'] = 0;
        if($SO=$this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['staff_id']){
                $filter['staff_id'] = $SO['staff_id'];
            }
            if($SO['uid']){
                $filter['uid'] = $SO['uid'];
            }
            if($SO['stime']&&$SO['ltime']){
                $filter['dateline'] = strtotime($SO['stime']).'~'.(strtotime($SO['ltime'])+68399);
            }
            if(!$SO['stime']&&$SO['ltime']){
                $filter['dateline']  = "<:".(strtotime($SO['ltime'])+68399);
            }
            if($SO['stime']&&!$SO['ltime']){
                $filter['dateline']  = ">:".strtotime($SO['stime']);
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }
        }

        if($items = K::M('waimai/complaint')->items_join_staff($filter,array('complaint_id'=>'DESC'), $page, $limit, $count)){
            $uids = $staff_ids = array();
            foreach ($items as $k=>$v){
                $uids[$v['uid']] =$v['uid'];
                //$staff_ids[$v['staff_id']] =$v['staff_id'];
            }
            $member_list = K::M('member/member')->items_by_ids($uids);
            //$staff_list = K::M('staff/staff')->items_by_ids($staff_ids);
            foreach ($items as $kk=>$vv){
                $items[$kk]['member'] = $member_list[$vv['uid']]['nickname'].'('.$member_list[$vv['uid']]['mobile'].')';
                //$items[$kk]['staff'] = $staff_list[$vv['staff_id']]['name'].'('.$staff_list[$vv['staff_id']]['mobile'].')';
                $items[$kk]['staff'] = $vv['staff_name'].'('.$vv['staff_mobile'].')';
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink("waimai/complaint/index", array('{page}')), array('SO'=>$SO));
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:group/staff/complaint.html";
    }

    public function complaint_detail($complaint_id){
        if($data = $this->checksubmit('data')){
            if(!$complaint_id = $data['complaint_id']){
                $this->msgbox->add('投诉不存在',203);
            }else if(!$data['reply']){
                $this->msgbox->add('请填写回复内容',204);
            }else{
                if(K::M('waimai/complaint')->update($complaint_id,array('reply'=>$data['reply'],'reply_time'=>__TIME))){
                    $this->msgbox->add('回复成功');
                }else{
                    $this->msgbox->add('回复失败',205);
                }
            }
        }else{
            if(!$complaint_id){
                $this->msgbox->add('投诉不存在',201);
            }else if(!$complaint = K::M('waimai/complaint')->detail($complaint_id)){
                $this->msgbox->add('投诉不存在',202);
            }else{
                $member = K::M('member/member')->detail($complaint['uid']);
                $staff = K::M('staff/staff')->detail($complaint['staff_id']);
                $photo = K::M('waimai/complaintphoto')->items(array('complaint_id'=>$complaint_id));
                $this->pagedata['member'] = $member;
                $this->pagedata['staff'] = $staff;
                $this->pagedata['photo'] = $photo;
                $this->pagedata['complaint']=$complaint;
                $this->tmpl = "admin:group/staff/complaintdetail.html";
            }
        }

    }

    public function complaint_so(){
        $this->tmpl = "admin:group/staff/complaint_so.html";
    }



    public function  dialog_so(){
        $this->tmpl = 'admin:group/staff/diaso.html';
    }

    //配送员考核
    public function assessment($page=1){
        $page = max((int)$page,1);
    }

    //骑手弃单记录
    public function cancellog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['from'] = 'staff';
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            }
            if ($SO['order_id']) {
                $filter['order_id'] = $SO['order_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]);
                    $filter['dateline'] = $a."~".$b;
                }
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }            
        }
        $staff_ids =$group_ids =  array();
        if($items = K::M('order/cancellog')->items_join_staff($filter, array('dateline' => 'DESC'), $page, $limit, $count)) {
            foreach ($items as $k=>$v){
                $staff_ids[$v['staff_id']] = $v['staff_id'];
                $group_ids[$v['group_id']] = $v['group_id'];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }
        //$this->pagedata['staffs'] = K::M('staff/staff')->items_by_ids($staff_ids);
        $this->pagedata['groups'] = K::M('pei/group')->items_by_ids($group_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/staff/cancellog.html';
    }

    public function  cancellog_so(){
        $this->tmpl = 'admin:group/staff/cancellog_so.html';
    }

    //骑手强制送达记录
    public function forcedlog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        $filter['from'] = 'staff';
        if ($SO = $this->GP('SO')) {
            $pager['SO'] = $SO;
            if ($SO['staff_id']) {
                $filter['staff_id'] = $SO['staff_id'];
            }
            if ($SO['order_id']) {
                $filter['order_id'] = $SO['order_id'];
            }
            if(is_array($SO['dateline'])){
                if($SO['dateline'][0] && $SO['dateline'][1]){
                    $a = strtotime($SO['dateline'][0]);
                    $b = strtotime($SO['dateline'][1]);
                    $filter['dateline'] = $a."~".$b;
                }
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':SQL'] = " (w.name LIKE '%".$SO['keywords']."%' OR w.mobile LIKE '%".$SO['keywords']."%')";
            }            
        }
        $staff_ids =$group_ids =  array();
        if($items = K::M('staff/forcedlog')->items_join_staff($filter, array('dateline' => 'DESC'), $page, $limit, $count)) {
            foreach ($items as $k=>$v){
                $staff_ids[$v['staff_id']] = $v['staff_id'];
                $group_ids[$v['group_id']] = $v['group_id'];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO' => $SO));
        }

        $this->pagedata['groups'] = K::M('pei/group')->items_by_ids($group_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/staff/forcedlog.html';
    }

    public function  forcedlog_so(){
        $this->tmpl = 'admin:group/staff/forcedlog_so.html';
    }


}
