<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 9:23
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Hongbao extends Ctl
{

    public function index($order_id)
    {
        $this->ordershare($order_id);
    }

    public function ordershare($order_id)
    {
        if(!$order_id = (int)$order_id){
            $this->error(404);
        }elseif(!$order = K::M('waimai/order')->detail($order_id)){
            $this->error(404);
        }else{
            $wx_openid = $wx_unionid = '';
            if(defined('IN_WEIXIN')){
                $wx_openid = $this->check_wxopenid();
                if(defined('WX_UNIONID')){
                    $wx_unionid = WX_UNIONID;
                }
            }
            if($this->uid){
                $uid = $this->uid;
            }else{
                $uid = $this->cookie->get('TEMP_UID');
            }
            //print_r($uid);die;
            if($uid){
                $member = K::M('member/member')->detail($uid);
            }
            if(!$uid){
                if(defined('IN_WEIXIN')){
                    if(defined('WX_UNIONID') && WX_UNIONID){
                        $member = K::M('member/member')->member(WX_UNIONID, 'wx_unionid');
                    }
                    if(empty($member)){
                        $member = K::M('member/member')->member(WX_OPENID, 'wx_openid');
                    }
                    if($member){
                        $this->system->auth->manager($member['uid']);
                    }else{
                        if(!$client = K::M('weixin/wechat')->admin_wechat_client()){
                            exit('微信公众号未配置');
                        }
                        $wx_info = $client->getUserInfoById($wx_openid);
                        $this->pagedata['wx_info'] = $wx_info;
                    }
                }
            }
            $cfg = $this->system->config->get("hongbao");
            $myhb_log = $member_list = $items = array();
            $hb_xin_lingqu = 0;
            if($log_list = K::M('hongbao/orderlog')->items(array('order_id'=>$order_id), array('log_id'=>'DESC'), 1, 50, $count)){
                $uids = array();
                foreach($log_list as $k=>$v){
                    $uids[$v['uid']] = $v['uid'];
                    if($v['uid'] == $uid){
                        $myhb_log = $v;
                    }
                }
                $member_list = K::M('member/member')->items_by_ids($uids);
            }
            if($uid && empty($myhb_log)){
                //登录且未领取过创建红包记录
                $limit =  $cfg['limit'] > 0 ? $cfg['limit'] : 10;
                $hongbao_arr = array_values($cfg['hongbao']);
                $hb_count = count($hongbao_arr);
                $hb_last_count = $limit - $count;
                if($hb_last_count > 0 && $hb_count > 0){
                    $hb_item = $hongbao_arr[rand(0, $hb_count-1)];
                    $data = array(
                            'order_id' => $order_id,
                            'uid' => $member['uid'],
                            'status' => 1,
                            'min_amount' => $hb_item['min_amount'],
                            'amount' => $hb_item['amount'],
                            'day' => $hb_item['day'],
                            'face' => $member['face'],
                            'wx_openid' => $wx_openid,
                            'wx_unionid' => $wx_unionid,
                            'nickname' => $member['nickname'],
                            'dateline' => __TIME,
                            'clientip' => __IP 
                        );
                    $ltime = $hb_item['day']*86400 + strtotime(date('Y-m-d',__TIME)) + 86399;
                    $hongbao_data = array(
                        'title'=>'分享红包',
                        'min_amount' => $hb_item['min_amount'],
                        'amount' => $hb_item['amount'],
                        'uid' => $member['uid'],
                        'ltime'=>$ltime,
                        'dateline' => __TIME,
                        'clientip' => __IP,
                        'from'=>$hb_item['type'],
                        'limit_stime'=>$hb_item['stime'],
                        'limit_ltime'=>$hb_item['ltime'],
                        'type'=>4
                    );
                    if($log_id = K::M('hongbao/orderlog')->create($data, true)){
                        K::M('hongbao/hongbao')->send($member['uid'],$hongbao_data);

                        $myhb_log = K::M('hongbao/orderlog')->detail($log_id);
                        $log_list = array_merge(array($log_id=>$myhb_log), $log_list);
                        $hb_xin_lingqu = 1;
                        $member_list[$member['uid']] = $member;
                    }
                    //$this->cookie->delete('TEMP_UID');
                }
            }
            $this->pagedata['hb_last_count'] = $hb_last_count;
            $this->pagedata['hb_xin_lingqu'] = $hb_xin_lingqu;
            $this->pagedata['myhb_log'] = $myhb_log;
            $this->pagedata['log_list'] = $log_list;
            $this->pagedata['member'] = $member;
            $this->pagedata['member_list'] = $member_list;
            $this->pagedata['module_hongbao'] = __CFG::$MODULE;
            $this->tmpl = "hongbao/ordershare.html";
        }
    }



     public function get_hongbao(){
         if(!$uid = (int)$this->GP('uid')){
             $this->msgbox->add("请先登录!",211);
         }else{
             $data = array('uid'=>$uid);
             $data['min_amount'] = floatval($this->GP('min_amount'));
             $data['amount'] = floatval($this->GP('amount'));
             $day = (int)$this->GP('day');
             $data['ltime'] = strtotime(date("Y-m-d",__TIME)) + 86399 + $day*86400;
             $data['title'] = "分享红包";
             $data['dateline'] = __TIME;
             $data['clientip'] = __IP;
             $log_id = (int)$this->GP('log_id');
             $limit = 10; //默认10个红包
             if($hongbao_id = K::M('hongbao/hongbao')->create_normal($data,true)){
                 K::M('hongbao/orderlog')->update($log_id,array('status'=>1));
                 $this->msgbox->add("领取成功!");
             }else{
                 $this->msgbox->add("领取失败!",212);
             }
         }
     }

    public function bindmobile()
    {
        if(!$mobile = $this->GP('mobile')){
            $this->msgbox->add("手机号码不正确!",211);
        }elseif(!K::M('verify/check')->vietnamMobile($mobile)){
            $this->msgbox->add("手机号码不正确!",212);
        }else{
            if($member = K::M('member/member')->member($mobile,'mobile')){
                $this->cookie->set('TEMP_UID',$member['uid']);
              //  K::M('member/auth')->manager($member['uid']);
            }else{
                $wx_openid = $wx_unionid = '';
                if(defined('IN_WEIXIN')){
                    $wx_openid = $this->check_wxopenid();
                    if(defined('WX_UNIONID')){
                        $wx_unionid = WX_UNIONID;
                    }
                    if(!$client = K::M('weixin/wechat')->admin_wechat_client()){
                        exit('微信公众号未配置');
                    }
                    $wx_info = $client->getUserInfoById($wx_openid);
                }
                //未注册用户
                $passwd = md5(uniqid());
                $data = array(
                        'mobile' => $mobile,
                        'passwd' => $passwd,
                        'paypasswd'=>$passwd
                    );
                $data['wx_openid'] = $wx_openid;
                $data['wx_unionid'] = $wx_unionid;
                if($wx_info['nickname']){
                    $data['nickname'] = $wx_info['nickname'];
                }else{
                    $data['nickname'] = substr($mobile, 0,3).'****'.substr($mobile, -4);
                }
                if($uid = K::M('member/account')->create($data)){
                    if($wx_info['headimgurl']){
                        if($face = K::M('net/http')->get($wx_info['headimgurl'])){
                            K::M('member/face')->update_face($uid, '', $face);
                        }
                    }
                    $this->cookie->set('TEMP_UID',$uid);
                    //K::M('member/auth')->manager($uid);
                }else{
                    $this->msgbox->add('领取红包失败!', 321);
                }
            }
        }
    }

     
    public function bind(){
         if(!$mobile = $this->GP('mobile')){
             $this->msgbox->add("请输入手机号码!",211);
         }elseif(!K::M('verify/check')->vietnamMobile($mobile)){
             $this->msgbox->add("手机号码不正确!",212);
         }else{
             if($member = K::M('member/member')->member($mobile,'mobile')){
                 K::M('member/auth')->manager($member['uid']);
             }else{//未注册用户
                 K::M('member/member')->create(array('mobile'=>$mobile),true);
                 $member = K::M('member/member')->member($mobile,'mobile');
                 K::M('member/auth')->manager($member['uid']);
             }
             $this->msgbox->add("绑定成功!");
         }
    }
    
}