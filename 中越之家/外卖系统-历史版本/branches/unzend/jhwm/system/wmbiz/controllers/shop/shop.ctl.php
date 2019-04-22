<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Shop extends Ctl
{
    public function index()
    {
        $this->pagedata['waimai'] =  $this->waimai_shop;
        $this->pagedata['env'] = K::M('waimai/env')->items(array('shop_id'=>$this->shop_id),array(),1,5,$count);
        $this->pagedata['catetitle'] = $this->get_cate_title($this->waimai_shop['cate_id']);
        $this->tmpl = 'shop/shop/index.html';
    }
    
    // 基本信息修改
    public function basic(){
         $env =  K::M('waimai/env')->items(array('shop_id'=>$this->shop_id),array(),1,5,$count);
        if($data = $this->checksubmit('data')){
            if (!$data = $this->filter_fields('title,cate_id,logo,phone,pay_type,addr,delcare,env,is_ziti,auto_jiedan,zero_ziti,warn_sku', $data)) {
                $this->msgbox->add('非法的数据提交',211);
            }/*elseif (!strlen($data['title'])) {
                $this->msgbox->add('没有填写店铺名称！',212);
            }*//*elseif (!$cate_ids = K::M('waimai/cate')->children_ids($data['cate_id'])) {
                $this->msgbox->add('该分类不存在或已被删除',213);
            }elseif (!in_array($data['cate_id'], explode(',', $cate_ids))) {// 越权
                $this->msgbox->add('该分类不存在或已被删除',214);
            }elseif (!strlen($data['logo'])) {
                $this->msgbox->add('没有上传店铺logo！',215);
            }*/elseif (!strlen($data['phone'])) {
                $this->msgbox->add('没有填写电话！',216);
            }elseif (!$data['phone'] = K::M('verify/check')->phone($data['phone'])) {
                $this->msgbox->add('电话格式不正确！',217);
            }elseif (!in_array($data['pay_type'] , array(1,2,3))) {// 1：全部支持 2：仅支持货到付款 3：仅支持在线支付
                $this->msgbox->add('支付类型不正确',218);
            }/*elseif (!strlen($data['addr'])) {
                $this->msgbox->add('没有填写门店地址',219);
            }*/else if((int)$data['warn_sku'] < 0){
                $this->msgbox->add('库存预警设置有误',219);
            }else{
                $data['warn_sku'] = (int)$data['warn_sku'];
                if($_FILES['data']){
                    foreach($_FILES['data'] as $k=>$v){
                        foreach($v as $kk=>$vv){
                            $attachs[$kk][$k] = $vv;
                        }
                    }
                    $upload = K::M('magic/upload');
                    foreach($attachs as $k=>$attach){
                        if($attach['error'] == UPLOAD_ERR_OK){
                            if($a = $upload->upload($attach, 'waimai')){
                                $data[$k] = $a['photo'];
                            }
                        }
                    }
                }
                if ($data['pay_type'] == 1) {// 全部支持
                    $data['online_pay'] = $data['is_daofu'] = 1;
                }elseif ($data['pay_type'] == 2) {// 仅支持货到付款
                    $data['online_pay'] = 0;
                    $data['is_daofu'] = 1;
                }elseif ($data['pay_type'] == 3) {// 仅支持在线支付
                    $data['online_pay'] = 1;
                    $data['is_daofu'] = 0;
                }
                if($data['is_ziti']==0){
                    $data['zero_ziti']=0;
                }else if($data['is_ziti']==1){
                    if(!$data['zero_ziti']){
                        $data['zero_ziti'] = 0;
                    }
                }
                foreach ($env as $v){
                    K::M('waimai/env')->delete($v['photo_id']);
                }
                if($data['env']){
                    foreach ($data['env'] as $v1){
                        $insert_data = array(
                            'shop_id'=>$this->shop_id,
                            'photo'=>$v1
                        );
                        K::M('waimai/env')->create($insert_data);
                    }
                }
                unset($data['pay_type']);
                if (K::M('waimai/waimai')->update($this->shop_id, $data)) {
                    K::M('shop/shop')->update($this->shop_id, array('addr'=>$data['addr']));
                    $this->msgbox->add('修改成功！');
                    $this->msgbox->set_data('forward', $this->mklink('shop/shop:index'));
                }else{
                    $this->msgbox->add('修改失败！',301);
                }
            }
        }else{            
            $this->pagedata['env'] = $env;
            $this->pagedata['count'] = count($env);
            $this->pagedata['waimai_cate'] = K::M('waimai/cate')->tree();
            $this->pagedata['waimai'] =  $this->waimai_shop;
            $this->pagedata['catetitle'] = $this->get_cate_title($this->waimai_shop['cate_id']);
            $this->tmpl = 'shop/shop/basic.html';
        }
    }

    // 营业时间修改
    public function other(){

        if($this->checksubmit()){
            $data = $this->checksubmit('data');
            $ps = $this->checksubmit('ps');
            if (!in_array($data['yuyue_day'], array(0,1,2,3,4,5,6,7))) {// 1：当天 2：明天  .... 最大到6天内
                $this->msgbox->add('非法的数据提交',217);
            }else{
                if(!$data['stime'] || !$data['ltime'] || !$data['yy_weeks']){
                    $this->msgbox->add('请填写营业时间',218);
                }else{
                    $yy_peitime = array();
                    foreach ($data['stime'] as $k=>$v){
                        //检测两个时间是否检查
                        if($data['stime'][$k]&&$data['ltime'][$k]){
                            if(!preg_match('/^\d{1,2}\:\d{2}$/i', $v)){
                                $this->msgbox->add('开始时间格式不对',219)->response();
                            }else if((strpos($data['ltime'][$k],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $data['ltime'][$k]))){
                                $this->msgbox->add('结束时间格式不对',220)->response();
                            }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$data['ltime'][$k])))){
                                $this->msgbox->add('结束时间格式不对',220)->response();
                            }else{
                                $yy_peitime[] = array(
                                    'stime'=>$data['stime'][$k],
                                    'ltime'=>$data['ltime'][$k]
                                );
                            }
                        }
                    }
                    $data['yy_peitime'] = serialize($yy_peitime);
                    $ps_peitime = array();
                    foreach ($ps['stime'] as $k=>$v){
                        //检测两个时间是否检查
                        if($ps['stime'][$k]&&$ps['ltime'][$k]){
                            /*if((!preg_match('/^\d{1,2}\:\d{2}$/i', $v))||(!preg_match('/^\d{1,2}\:\d{2}$/i', $ps['ltime'][$k]))){
                                $this->msgbox->add('开始时间或者结束时间格式不对',219)->response();
                            }*/
                            if(!preg_match('/^\d{1,2}\:\d{2}$/i', $v)){
                                $this->msgbox->add('开始时间格式不对',219)->response();
                            }else if((strpos($ps['ltime'][$k],'次日') === false) && (!preg_match('/^\d{1,2}\:\d{2}$/i', $ps['ltime'][$k]))){
                                $this->msgbox->add('结束时间格式不对',220)->response();
                            }else if(!preg_match('/^\d{1,2}\:\d{2}$/i', trim(str_replace('次日','',$ps['ltime'][$k])))){
                                $this->msgbox->add('结束时间格式不对',220)->response();
                            }else{
                                $ps_peitime[] = array(
                                    'stime'=>$ps['stime'][$k],
                                    'ltime'=>$ps['ltime'][$k]
                                );
                            }
                        }
                    }
                    $data['ps_time'] = serialize($ps_peitime);

                    //v3.6 营业时间按周选择
                    $data['yy_weeks'] = implode(',', $data['yy_weeks']);
                    
                    if(K::M('waimai/waimai')->update($this->shop_id, $data)){
                        $this->msgbox->add('修改成功！');
                        $this->msgbox->set_data('forward', $this->mklink('shop/shop:index'));
                    }else{
                        $this->msgbox->add('修改失败！',301);
                    }

                }


            }

        }else{
            /*$time_start = 631123200;
            $end_time = 631295999;
            $time_arr = array();
            while($time_start<$end_time){
                if($time_start< 631209600){
                    $time_arr[] = array(
                        'k'=>date("H:i",$time_start),
                        'next'=>0,
                        'v'=>date("H:i",$time_start)
                    );
                }else{
                    $time_arr[] = array(
                        'k'=>"下一天".date("H:i",$time_start),
                        'next'=>1,
                        'v'=>date("H:i",$time_start)
                    );
                }
                $time_start = $time_start+1800;
            }
           */

            $this->pagedata['waimai'] =  $this->waimai_shop;
            $this->pagedata['catetitle'] = $this->get_cate_title($this->waimai_shop['cate_id']);
            $this->tmpl = 'shop/shop/other.html';
        }
    }
    //异步上传文件
    public function uploadimg()
    {
        if(!$attach = $_FILES['file']){
            $this->msgbox->add('上传图片失败', 501);
        }elseif($attach['error'] != UPLOAD_ERR_OK){
            $this->msgbox->add('上传图片失败', 502);
        }elseif($data = K::M('magic/upload')->upload($attach, 'image', $fname, array('photo'=>'800', 'thumb'=>'200X200'))){
            $this->msgbox->set_data('file', $data);
        }
        $this->msgbox->json();        
    }

    public function close($mdzz){
        if(!$mdzz){
           $this->msgbox->add('非法操作',201);
        }else{
            if($mdzz==1){
                if(K::M('waimai/waimai')->update($this->shop_id,array('yy_status'=>1))){
                    $this->msgbox->add('操作成功');
                }else{
                    $this->msgbox->add('操作失败',202);
                }
            }else if($mdzz==2){
                if(K::M('waimai/waimai')->update($this->shop_id,array('yy_status'=>0))){
                    $this->msgbox->add('操作成功');
                }else{
                    $this->msgbox->add('操作失败',203);
                }
            }

        }

    }

    public function get_cate_title($cate_id){
        $cate = K::M('waimai/cate')->detail($cate_id);
        if($cate['parent_id']){
           $children = K::M('waimai/cate')->detail($cate['parent_id']);
            $cate_title = $cate['title'].'-'.$children['title'];
        }else{
            $cate_title =  $cate['title'];
        }
        return $cate_title;
    }

    function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '')
    {
        $status = $beginTime2 - $beginTime1;
        if ($status > 0)
        {
            $status2 = $beginTime2 - $endTime1;
            if ($status2 >= 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            $status2 = $endTime2 - $beginTime1;
            if ($status2 > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function getTime()
    {
        if($stime = (int)$this->GP('stime')){
            $res = K::M('helper/format')->morrowTime($stime+15*60, 115200, 15);
        }else if($ltime = (int)$this->GP('ltime')){
            $ltime = min($ltime, 86400);
            $res = K::M('helper/format')->morrowTime(0, $ltime-15*60, 15);
        }else{
            $res = K::M('helper/format')->morrowTime(28800, 72000-15*60, 15);
        }       
        $this->msgbox->add('SUCCESS');
        $this->msgbox->set_data('data',$res);
    }

    /*public function morrowTime($stime='08:00', $ltime='20:00', $space=15, $day=0)
    {
        $_result = array();
        $start = 0;
        $end = 60/$space;
        $label = '';
        if($day > 0){
            $label = '次日';
        }
        $stimeArr = explode(':',$stime);
        $ltimeArr = explode(':',$ltime);
        $Hour = array('start'=>(int)$stimeArr[0],'end'=>(int)$ltimeArr[0]);
        $Minute = array('start'=>(int)$stimeArr[1]/$space,'end'=>(int)$ltimeArr[1]/$space);
        if($Hour){
            for ($i = $Hour['start']; $i <= $Hour['end']; $i++) {
                
                if($i == $Hour['start']){
                    $start = $Minute['start'];                    
                }else if($i == $Hour['end']){
                    $end = $Minute['end'];
                }
                for($k = $start; $k < $end; $k++){
                    $H = $i;                                               
                    $M = $space*$k;
                    if($H < 10){
                        $H = '0'.$H;
                    }
                    if($M < 10){
                        $M = '0'.$M;
                    }                    
                    array_push($_result, $label.$H.":".$M);
                }
            }
        }
        if($day == 0){
            array_shift($_result);
        }
        return $_result;
    }*/

}