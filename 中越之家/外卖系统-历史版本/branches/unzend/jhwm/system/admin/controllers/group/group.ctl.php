<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 14:32
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Group_Group extends Ctl {

    public function items($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 500;
        $filter['closed']= 0 ;
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['group_name']){
                $filter['group_name'] = "LIKE:%".$SO['group_name']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['mobile']){
                $filter['mobile'] = "LIKE:%".$SO['mobile']."%";
            }

            //4.0模糊查询
            if($SO['keywords']){
                $filter[':OR'] = array(
                    'group_name'=>"LIKE:%".$SO['keywords']."%",
                    'contact'=>"LIKE:%".$SO['keywords']."%",
                    'mobile'=>"LIKE:%".$SO['keywords']."%"
                    );
            }
        }
        if($items = K::M('pei/group')->items($filter,array(),$pager['page'], $pager['limit'],$count)){
            $pro_ids = array();
            $city_ids = array();
            foreach($items as $k=>$v){
                $pro_ids[] = $v['province_id'];
                $city_ids[] = $v['city_id'];
            }
            $pro_list = K::M('data/province')->items_by_ids($pro_ids);
            $city_list = K::M('data/city')->items_by_ids($city_ids);
            foreach($items as $k1=>$v1){
                $items[$k1]['dizhi'] = $pro_list[$v1['province_id']]['province_name'].$city_list[$v1['city_id']]['city_name'].$v1['addr'];
                $items[$k1]['city_name'] = $city_list[$v1['city_id']]['city_name'];
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $pager['limit'], $page, $this->mklink('group/group:items', array('{page}')), array('SO'=>$SO));
        }else{
            $items = array();
        }

        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/items.html';
    }

    /*public function so(){
        $this->tmpl = "admin:group/so.html";

    }*/

    public function create(){
        if($data = $this->checksubmit('data')){

            if(!$data['mobile']){
                $this->msgbox->add('手机号码不能为空',201);
            }else if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add('手机号码不正确',202);
            }else if(!$data['passwd']){
                $this->msgbox->add('密码不能为空',203);
            }else if(!$data['group_name']){
                $this->msgbox->add('配送站名称不能为空',204);
            }else if(!$data['addr']){
                $this->msgbox->add('配送站地址不能为空',205);
            }else if(!$data['province_id']){
                $this->msgbox->add('请选择省份',206);
            }else if(!$data['city_id']){
                $this->msgbox->add('请选择城市',206);
            }else if(!$data['contact']){
                $this->msgbox->add('配送站联系人不能为空',207);
            }else if(!$data['overtime']){
                $this->msgbox->add('指定订单过期有效时间',208);
            }else if(K::M('pei/group')->member($data['mobile'],'mobile')){
               $this->msgbox->add('手机号码已存在',210);
            }else if((float)$data['min_amount']<0){
               $this->msgbox->add('起送价不合法',212);
            }else if((int)$data['is_used']==1&&((int)$data['limit_order']<=0)){
                $this->msgbox->add('限制接单数量设置错误',213);
            }else if((float)$data['min_pei']<0){
               $this->msgbox->add('基础配送费不合法',214);
            }else {
                if((int)$data['is_used']==0){
                    $data['limit_order'] = 0;
                }

                /*if($_FILES['file']){
                    if($a = K::M('magic/upload')->upload($_FILES['file'])){
                        $data['face'] = $a['photo'];
                    }
                }*/

                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'group')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }

                if(!$data['lng']||!$data['lat']){
                    $this->msgbox->add('请在地图上选择地址',211)->response();
                }
                $location = K::M('helper/date')->bd_decrypt($data['lng'],$data['lat']);
                $data['lng'] = $location['gg_lon'];
                $data['lat'] = $location['gg_lat'];
                $data['passwd'] = md5($data['passwd']);
                $data['dateline'] = __TIME;
                if(K::M('pei/group')->create($data)){
                    $this->msgbox->add('创建成功');
                    $this->msgbox->set_data('forward',$this->mklink('group/group:items'));
                } else{
                    $this->msgbox->add('创建失败',209);
                }
            }
        }else{
            $this->pagedata['pro'] = K::M('data/province')->fetch_all();
            $this->pagedata['city'] = json_encode(K::M('data/city')->fetch_all());
            $this->tmpl = 'admin:group/create.html';
        }
    }

    public function edit($group_id){

        if($data  = $this->checksubmit('data')){
            if(!($group_id = (int)$group_id) && !($group_id = $this->GP('group_id'))){
                $this->msgbox->add('未找到需要修改的配送站',201);
            }else if(!$group = K::M('pei/group')->detail($group_id)){
                $this->msgbox->add('未找到需要修改的配送站',202);
            }else if(!$data['mobile']){
                $this->msgbox->add('请填写手机号码',203);
            }else if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add('手机号码格式不正确',204);
            }else if(($mobile!=$group['mobile'])&&(K::M('pei/group')->member($data['mobile'],'mobile'))){
                $this->msgbox->add('手机号码不正确',205);
            }else if(!$data['group_name']){
                $this->msgbox->add('请填写配送团队名称',206);
            }else if(!$data['addr']){
                $this->msgbox->add('请填写地址',207);
            }else if(!(int)$data['province_id']){
                $this->msgbox->add('请选择省份',208);
            }else if(!(int)$data['city_id']){
                $this->msgbox->add('请选择城市',209);
            }else if(!$data['contact']){
                $this->msgbox->add('请填写配送站联系人',210);
            }else if((float)$data['min_amount']<0){
                $this->msgbox->add('起送价不合法',212);
            }else if((int)$data['is_used']==1&&((int)$data['limit_order']<=0)){
                $this->msgbox->add('限制接单数量设置错误',213);
            }else if((float)$data['min_pei']<0){
               $this->msgbox->add('基础配送费不合法',214);
            }else {

                if((int)$data['is_used']==0){
                    $data['limit_order'] = 0;
                }
                /*if($_FILES['file']){
                   if($a = K::M('magic/upload')->upload($_FILES['file'])){
                       $data['face'] = $a['photo'];
                   }
                }*/
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'group')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                
                if(!$data['lng']||!$data['lat']){
                    $this->msgbox->add('请在地图上选择地址',211)->response();
                }
                $location = K::M('helper/date')->bd_decrypt($data['lng'],$data['lat']);
                $data['lng'] =$location['gg_lon'];
                $data['lat'] = $location['gg_lat'];
                if($data['passwd']){
                    $data['passwd'] = md5($data['passwd']);
                }else{
                    unset( $data['passwd']);
                }

                $data['overtime'] =  $data['overtime']?$data['overtime']:5;
                if(K::M('pei/group')->update($group_id,$data)){
                    K::M('pei/group')->set_cache($group_id);
                    $this->msgbox->add('修改成功');
                    K::M("waimai/waimai")->edit_shop_min_amount($group_id,$data['min_amount']);
                   // $this->msgbox->set_data('forward',$this->mklink('group/group:items'));
                }else{
                    $this->msgbox->add('修改失败',211);
                }
                //K::M("system/logs")->log("sqllog",$this->system->db->SQLLOG());
            }

        }else{
            if(!$group_id){
                $this->msgbox->add('配送站不存在',201);
            }else if(!$group = K::M('pei/group')->detail($group_id)){
                $this->msgbox->add('配送站不存在',202);
            }else {
                $this->pagedata['pro']  = K::M('data/province')->fetch_all();
                $this->pagedata['city'] = json_encode(K::M('data/city')->fetch_all());
                $location = K::M('helper/date')->bd_encrypt($group['lng'],$group['lat']);
                $group['g_lng'] = $location['bd_lon'];
                $group['g_lat'] = $location['bd_lat'];
                $this->pagedata['group_id'] = $group_id;
                $this->pagedata['detail'] = $group;

                $this->tmpl = 'admin:group/edit.html';
            }
        }
    }

    public function manage($group_id){
        K::M('pei/auth')->manager($group_id);
        $link = $this->mklink('index',array(),array(),'dispatch');
        $link = str_replace("?","",$link);
        header('Location:'.$link);
        exit;
    }

    public function delete($group_id){
        if($group_id){
            if(!$group = K::M('pei/group')->detail($group_id)){
                $this->msgbox->add('未找到配送站',202);
            }else{
                if(K::M('pei/group')->delete($group_id)){
                   $this->msgbox->add('关闭成功');
                }else{
                    $this->msgbox->add('关闭失败',205);
                }
            }
        }else if($group_ids = $this->GP('group_id')){
            foreach ($group_ids as $v){
                if(!$group = K::M('pei/group')->detail($v)){
                    $this->msgbox->add('配送站不存在',206)->response();
                }
                if(K::M('pei/group')->delete($group_id)){
                    $this->msgbox->add('关闭成功');
                }else{
                    $this->msgbox->add('关闭失败',208);
                }
            }

        }else{
            $this->msgbox->add('未指定配送站',201);
        }

    }
     public function setarea($city_id = null)
    {
        $filter = $orderby = array();
        $filter['closed'] = 0;
        $filter['city_id'] = 1;
        $items = K::M('pei/group')->items($filter,$orderby,1,50,$count);
        $this->pagedata['items'] = $items;
        $this->pagedata['city_id'] = $city_id;
        $city_list = K::M('data/city')->fetch_all();
        $this->pagedata['json_city'] = K::M('utility/json')->encode($city_list);
        $this->tmpl = 'admin:group/setarea.html';
    }

    public function get_groups($city_id = null)
    {
        if(!$city_id = (int)$city_id){
            $this->msgbox->add('请选择城市！',211);
        }else if(!$city = K::M('data/city')->detail($city_id)){
            $this->msgbox->add('该城市不存在！',212);
        }else{
            $filter = $orderby = array();
            $filter['closed'] = 0;
            $filter['city_id'] = $city_id;
            $items = K::M('pei/group')->items($filter,$orderby,1,50,$count);
            foreach($items as $k=>$v){
                foreach($v['polygon_point'] as $kk=>$vv){
                    $location = K::M('helper/date')->bd_decrypt($vv['lng'],$vv['lat']);
                    $items[$k]['polygon_point'][$kk]['lat'] = $location['gg_lat'];
                    $items[$k]['polygon_point'][$kk]['lng'] = $location['gg_lon'];
                }
            }
            $this->msgbox->add('success');
            $this->msgbox->set_data('data',array('groups'=>array_values($items),'city'=>$city));
            $this->msgbox->json();
        }
    }

    public function save_polygon($group_id = null)
    {
        if (!$group_id = (int)$group_id) {
            $this->msgbox->add('请选择配送团队！',212);
        }else if (!$polygon_point = $this->GP('polygon_point')) {
            $this->msgbox->add('非法的数据提交！',213);
        }else{
            foreach($polygon_point as $k=>$v){
                $location = K::M('helper/date')->bd_encrypt($v['lng'],$v['lat']);
                $polygon_point[$k]['lat'] = $location['bd_lat'];
                $polygon_point[$k]['lng'] = $location['bd_lon'];
            }
            $polygon_point = serialize($polygon_point);
            if (K::M('pei/group')->update($group_id, array('polygon_point'=>$polygon_point))) {
                K::M('pei/group')->set_cache($group_id);
                $this->msgbox->add('保存成功！');
            }else{
                $this->msgbox->add('保存失败！',214);
            }
        }        
    }

    public function so($target=null, $multi=null)
    {
        if($target){
            $pager['multi'] = $multi == 'Y' ? 'Y' : 'N';
            $pager['target'] = $target;
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = "admin:group/so.html";
    }

    public function dialog($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 10;
        $pager['multi'] = $multi = ($this->GP('multi') == 'Y' ? 'Y' : 'N');
        if($SO = $this->GP('SO')){
            $pager['SO'] = $SO;
            if($SO['group_name']){
                $filter['group_name'] = "LIKE:%".$SO['group_name']."%";
            }
            if($SO['contact']){
                $filter['contact'] = "LIKE:%".$SO['contact']."%";
            }
            if($SO['mobile']){
                $filter['mobile'] = "LIKE:%".$SO['mobile']."%";
            }
        }
        //$filter['closed'] = 0;
        $city_ids = array();
        if($items = K::M('pei/group')->items($filter, null, $page, $limit, $count)){
            foreach ($items as $k => $v) {
                if($v['city_id']){
                    $city_ids[] = $v['city_id'];
                }
            }
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')), array('SO'=>$SO, 'multi'=>$multi));
        }
        $this->pagedata['citys'] = K::M('data/city')->items_by_ids($city_ids);
        $this->pagedata['items'] = $items;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:group/dialog.html';
    }

    public function set_map($group_id){
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在或已删除',202);
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['point'] = $group['polygon_point']?json_encode($group['polygon_point']):json_encode(array());
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/set_map.html';
        }
    }

    public function baseconfig($group_id){
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在或已删除',202);
        }else if($this->checksubmit()){
            $data = $this->checksubmit('config');
            $data = K::M('helper/format')->overturn($data);
            $min_array = array();
            foreach($data as $k=>$v){
                if(!$v['fkm']){
                    unset($data[$k]);
                    continue;
                }
                $min_array[] =  $v['fm'];
            }
            if(!$data){
                $this->msgbox->add('请设置配送规则',204)->response();
            }
            sort($min_array);
            $min_amount = $min_array[0]?$min_array[0]:0;
            if($data){
                K::M('waimai/waimai')->edit_shop_freight($min_amount,$group_id);
            }
            if(K::M('pei/group')->update($group_id,array('baseconfig'=>$data?serialize($data):""))){
                K::M('pei/group')->set_cache($group_id);
                $this->msgbox->add('操作成功');
            }else{
                $this->msgbox->add('操作失败',203);
            }
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/baseconfig.html';
        }
    }


    public function timeconfig($group_id)
    {
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在或已删除',202);
        }else if($this->checksubmit()){
            $config = $this->checksubmit('config');
            $config = K::M('helper/format')->overturn($config);
            $time = $this->checksubmit('time');
            foreach($config as $k=>$v){
                if(!$v['fkm']){
                    unset($config[$k]);
                    continue;
                }
            }
            $data = $this->checksubmit('data');
            if(!$config){
                $this->msgbox->add('请设置配送规则',204)->response();
            }
            if(!(preg_match('/^\d{1,2}\:\d{2}$/i', $data['stime']))||!(preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime']))){
                $this->msgbox->add('开始时间或结束时间格式错误',203)->response();
            }
            $update_data = array();
            $update_data['time'] = array('stime'=>$data['stime'],'ltime'=>$data['ltime']);
            $update_data['config'] = $config;
            $update_data['is_used'] = $time['is_used']==1?1:0;
            if(K::M('pei/group')->update($group_id,array('timeconfig'=>serialize($update_data)))){
                K::M('pei/group')->set_cache($group_id);
                $this->msgbox->add('操作成功');
            }else{
                $this->msgbox->add('操作失败',203);
            }
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/timeconfig.html';
        }
    }

    public function badweather($group_id){
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在或已删除',202);
        }else if($this->checksubmit()){
            $data = $this->checksubmit('config');
            $time = $this->checksubmit('time');
            $data = K::M('helper/format')->overturn($data);
            foreach($data as $k=>$v){
                if(!$v['fkm']){
                    unset($data[$k]);
                    continue;
                }
            }
            if(!$data){
                $this->msgbox->add('请设置配送规则',204)->response();
            }
            $update_data = array();
            $update_data['is_used'] = $time['is_used']==1?1:0;
            $update_data['config'] = $data;
            //$data['is_used'] = $time['is_used']==1?1:0;
            if(K::M('pei/group')->update($group_id,array('badweather'=>serialize($update_data)))){
                K::M('pei/group')->set_cache($group_id);
                $this->msgbox->add('操作成功');
            }else{
                $this->msgbox->add('操作失败',203);
            }
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/badweather.html';
        }
    }

    //配送站超时规则
    public function timeoutconfig($group_id)
    {
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在',202);
        }else if($this->checksubmit()){
            $data = $this->checksubmit('config');
            $data = K::M('helper/format')->overturn($data);
            foreach($data as $k=>$v){
                if(!$v['fkm']||!$v['time']){
                    unset($data[$k]);
                    continue;
                }
            }
            if(!$data){
                $this->msgbox->add('请设置超时规则',204)->response();
            }

            $timeout_time = $this->checksubmit('timeout_time') ? $this->checksubmit('timeout_time') : 0;

            if(K::M('pei/group')->update($group_id,array('timeout_config'=>serialize($data),'timeout_time'=>$timeout_time))){
                K::M('pei/group')->set_cache($group_id);
                $this->msgbox->add('操作成功');
            }else{
                $this->msgbox->add('操作失败',203);
            }
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/timeoutconfig.html';
        }
    }


    public function autopai($group_id)
    {
        return false;
        if(!$group_id){
            $this->msgbox->add('未指定配送站',201);
        }else if(!$group = K::M('pei/group')->detail($group_id)){
            $this->msgbox->add('配送站不存在',202);
        }else if($data = $this->checksubmit('data')){
           if(!in_array($data['open_autopai'],array(0,1))){
               $this->msgbox->add('非法数据请求',203);
           }else if(!in_array($data['jiedan'],array(0,1))){
               $this->msgbox->add('非法数据请求',204);
           }else if(!(float)$data['pei_distance']){
               $this->msgbox->add('请填写有效的距离',205);
           }else if((int)$data['orders']<0){
             $this->msgbox->add('请填写正确的背数量',206);
           }else{
               $auto_pei_config = array();
               $auto_pei_config['open_autopai'] = $data['open_autopai'];
               $auto_pei_config['pei_distance'] = $data['pei_distance'];
               $auto_pei_config['orders'] = $data['orders'];
               $auto_pei_config['jiedan'] = $data['jiedan'];
               if(K::M('pei/group')->update($group_id,array('autopei_config'=>serialize($data)))){
                   K::M('pei/group')->set_cache($group_id);
                   $this->msgbox->add('操作成功');
               }else{
                   $this->msgbox->add('操作失败',203);
               }
           }
        }else{
            $this->pagedata['detail'] = $group;
            $this->pagedata['group_id'] = $group_id;
            $this->tmpl = 'admin:group/autopai.html';
        }
    }
}
