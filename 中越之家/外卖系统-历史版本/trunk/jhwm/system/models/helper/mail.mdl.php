<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: mail.mdl.php 10111 2015-05-06 15:59:27Z maoge $
 */
Import::L('phpmailer/class.phpmailer.php');
class Mdl_Helper_Mail extends PHPMailer
{
    
    protected $from_mail = 'anhuike@qq.com';
    protected $from_name = '合肥江湖信息科技';
    
    protected  $_datetime = null;
    protected  $_adminmail = null;
    public function __construct(&$system)
    {
        parent::__construct(true);
        $this->CharSet = 'UTF-8';
        $cfg = $system->config->get('mail');
        $site = $system->config->get('site');
        if(strtolower($cfg['mode']) == 'smtp'){
            if(empty($cfg['smtp']['host']) || empty($cfg['smtp']['uname']) || empty($cfg['smtp']['passwd'])){
                exit('smtp config error');
            }
            $this->IsSMTP();
            $this->Host       = $cfg['smtp']['host'];
            $this->Port       = $cfg['smtp']['port'] ? $cfg['smtp']['port'] : 25;
            $this->SMTPAuth   = true;
            $this->Username   = $cfg['smtp']['uname'];
            $this->Password   = $cfg['smtp']['passwd'];
            $this->SMTPDebug  = false;
        }else{
            $this->IsMail();
        }
        $this->from_mail = $cfg['sender'];
        $this->from_name = $site['title'];
        $this->_adminmail = $cfg['email']; 
        $this->_datetime = date('Y-m-d H:i:s',__TIME);
        
        $this->SetFrom($this->from_mail, $this->from_name); 
    }
    
    public function MsgHTML($body, $basedir='')
    {
        $body = "<body>{$body}<p>本邮件由系统自动发出，请勿直接回复</p></body>";
        parent::MsgHTML($body, $basedir);
    }
    
    public function sendmail($to, $title, $body=null)
    {
        if(preg_match('/[\w\-\:]/i', $title)){
            $ident = str_replace(':', '_', $title);
            if(strpos($ident, 'mail_') === false){
                $ident = 'mail_'.$ident;
            }
            if($tmpl = K::M('system/systmpl')->detail_by_key($ident)){
                if(empty($tmpl['is_open'])){
                    return false;
                }
                $params = (array)$body;
                $title = $body = '';
                $this->_tmpl = $tmpl;
                $city = K::$system->request['city'];
                $site = K::$system->config->get('site');            
                $params['site_title'] = $site['title'];
                $params['site_url'] = $site['url'];
                $params['site_phone'] = $site['phone'];
                $params['city_name'] = $city['city_name'];
                $params['dateline'] = $this->_datetime;
                $a = $b = array();
                foreach($params as $k=>$v){
                    $a[] = '{'.$k.'}';
                    $b[] = $v;
                }
                $title = str_replace($a, $b, $tmpl['tmpl']);
                $body = str_replace($a, $b, $tmpl['tmpl1']);
            }
        }
        $body = (string)$body;
        $check = K::M('verify/check');
        if(is_array($to)){
            $this->AddAddress($to[0], $to[1]);
        }else if($check->mail($to)){
            $this->AddAddress($to);
        }else{
            $this->errmsg = '错误的收件人地址';
            return false;
        }      
        $this->Subject = $title;
        $this->AltBody = $this->AltBody ? $this->AltBody : K::M('content/html')->text($body);
        $this->MsgHTML($body);
        return $this->send();
    }
    public function clear()
    {
        $this->ClearAddresses();
        $this->ClearAttachments();
    }
    public function send()
    {
        try{
            parent::Send();
            $this->clear();
            return true;
        }catch(phpmailerException $e){
            $this->errmsg = $e->errorMessage();
            return false;
        }catch(Exception $e){
            $this->errmsg = $e->errorMessage();
            return false;
        }
        return false;
    }
    //通过模板文件发送邮件
    public function sendtmpl($ident, $data=array())
    {
        //
    }

    //通过配置发送邮件
    public function sendsystmpl($mail, $ident, $params=array())
    {
        return $this->sendmail($mail, $ident, $params);
    }
    public function sendadmin($ident, $data=array())
    {
        return $this->sendmail($this->_adminmail, $ident, $data);
    }
}