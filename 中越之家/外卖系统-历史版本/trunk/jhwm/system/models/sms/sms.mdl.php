<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id$
 */

class Mdl_Sms_Sms extends Model
{   
    
    protected $sms = null;
    protected   $_sitetitle = null;
    protected   $_sitephone = null;
    protected   $_cityname = null;
    protected   $_dateline = null;
    protected   $_adminmobile = null;

    protected $_tmpl = array(
            'sms_login'=>'【{site_title}】您的短信验证码是{code}，该验证码3分钟有效',
            'sms_verify'=>'【{site_title}】您的短信验证码是{code}，该验证码3分钟有效',
            'sms_have_count'=>'【{site_title}】您的短信余额仅剩{number}条，请及时充值',
            'sms_test'=>'【{site_title}】这是一条后台短信设置发送的短信,剩余短信{number}条',
        );
    
    public function __construct(&$system)
    {
        parent::__construct($system);
        $this->sms = K::M('sms/56dxw');
        $this->_config = $system->config->get('sms');
        $this->_cfg = K::$system->config->get('site');
        $this->_sitetitle = $this->_config['title'] ? $this->_config['title'] : $this->_cfg['title'];
        $this->_sitephone = $this->_cfg['phone'];
        $this->_cityname = $system->request['city']['city_name'];
        if(K::M('verify/check')->vietnamMobile($system->request['city']['mobile'])){
            $this->_cfg['mobile'] = $system->request['city']['mobile'];
        }
        $this->_dateline = date('Y-m-d H:i:s',__TIME);
        
    }
    
    //通知管理员的短信接口
    public function admin($key, $data=array())
    {
        if(empty($this->_config['mobile'])){
            $this->_config = K::$system->config->get('sms');
        }
        //一般接口都支持 ,分割的多个手机号 如果不支持的请在做逻辑处理！
        $mobiles = explode(',',$this->_config['mobile']);
        foreach($mobiles  as $mobile){
            if($mobile = K::M('verify/check')->vietnamMobile($mobile)){
                $this->send($mobile, $key, $data, true);
            }
        }
        return  true;
    }

    protected function _send($mobile, $content)
    {
        if(!(defined("__DEBUG") && __DEBUG) && !$this->sms->send($mobile, $content)){  //---remark--- 如果开启DEBUG,则不真的发送
            //$msg = $this->sms->lastmsg;
            //$this->msgbox->add($msg, 450);
            if(__CFG::DEBUG){
                $this->msgbox->add('短信接口错误:['.$this->sms->lastcode.':'.$this->lastmsg.']', 450);
            }else{
                $this->msgbox->add('发送短信失败['.$this->sms->lastcode.']', 450);
            }
            K::M('sms/log')->create(array('mobile'=>$mobile, 'content'=>$content, 'sms'=>'56dx', 'status'=>0));
            return false;
        }
        K::M('sms/log')->create(array('mobile'=>$mobile, 'content'=>$content, 'sms'=>'56dx', 'status'=>1));
        return true;        
    }
    
    public function send($mobile, $tmpl, $data=array(), $checked=false)
    {   
        if(!$content = $this->_parse($tmpl, $data)){
            return false;
        }
        if(!$checked && !defined('IN_ADMIN')){
            if(!$this->check_sms(__IP, $title)){
                $this->msgbox->add($title, 451);
                return false;
            }
        }
        $status = $this->_send($mobile, $content);
        $this->check_have_count();
        return $status;
    }

    public function check_sms($clientip=null, &$title='')
    {
        //临时注释掉限制
       // return true;
        $clientip = $clientip ? $clientip : __IP;
        //增加测试ip,不受限制
        $ip_test_array = array('60.166.198.21');
        if(in_array($clientip, $ip_test_array)){
            return true;
        }
        $access = K::$system->config->get('access');
        if($mobile_time = (int)$access['mobile_time']){
            if((__TIME - $mobile_time*60) < K::M('sms/log')->lasttime_by_ip($clientip)){
                $title = '两次短信间隔不能少于'.$mobile_time.'分钟';
                return false;
            }
        }
        if($mobile_count = (int)$access['mobile_count']){
            $time = __TIME - 86400;
            if($mobile_count <= K::M('sms/log')->count("clientip='{$clientip}' AND dateline>$time")){
                $title = '同一IP24小时只能发送'.$mobile_count.'短信';
                return false;
            }
        }
        return true;
    }

 
    protected function _parse($tmpl, $data=array())
    {
        if(preg_match('/^[\w\-\:]+$/i', $tmpl)){
            $ident = $tmpl;
            if(strpos($ident, 'sms_') === false){
                $ident = 'sms_'.$tmpl;
            }
            if($a = $this->_tmpl[$ident]){
                $tmpl = $a;
            }
        }
        $a = $b = array();
        foreach((array)$data as $k=>$v){
            $a[] = '{'.$k.'}';
            $b[] = $v;
        }
        $a[] = '{site_title}'; $a[] = '{site_phone}'; $a[] = '{city_name}'; $a[] = '{dateline}';$a[]='{site_name}';
        $b[] = $this->_sitetitle; $b[] = $this->_sitephone; $b[] = $this->_cityname; $b[] = $this->_dateline; $b[] = $this->_sitetitle;
        $content = str_replace($a, $b, $tmpl);
        return $content;
    }

    public function query()
    {
        return $this->sms->query();
    }

    public function check_have_count($force=false)
    {
        if($force || !($have_sms_count = K::M('cache/cache')->get('have_sms_count'))){
            $have_sms_count = $this->query();
            if($have_sms_count < 100){
                $expire_time = 600;
            }elseif($have_sms_count < 500){
                $expire_time = 1800;
            }elseif($have_sms_count < 1000){
                $expire_time = 3600;
            }else{
                $expire_time = 10800;
            }

            if($this->_config['remind_open']){
                if($have_sms_count < 1000 && $have_sms_count > 0){
                    $have_sms_count = $have_sms_count-1;
                    if($content = $this->_parse('sms_have_count', array('number'=>$have_sms_count))){
                        $mobiles = explode(',',$this->_config['mobile']);
                        foreach($mobiles  as $mobile){
                            if($mobile = K::M('verify/check')->vietnamMobile($mobile)){
                                $this->_send($mobile, $content);
                            }
                        }
                    }
                }
            }
            
            K::M('cache/cache')->set('have_sms_count', $have_sms_count, $expire_time);
        }
    }
}
