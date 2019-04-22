<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: 56dxw.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */

Import::I('sms');
class Mdl_Sms_56dxw implements Sms_Interface
{   
    protected $gateway = 'http://jiekou.56dxw.com/sms/HttpInterface.aspx';
    protected $_cfg = array();

    public $lastmsg = '';
	public $lastcode = 1;

    public function __construct($system)
    {
    	$this->_cfg = $system->config->get('sms');
    }
    
    public function send($mobile, $content)
    {
    	$params = array('comid'=>$this->_cfg['comid'], 'smsnumber'=>$this->_cfg['smsnumber']);
    	$params['username'] = $this->_cfg['uname'];
    	$params['userpwd'] = $this->_cfg['passwd'];
    	$params['sendtime'] = '';
    	$params['handtel'] = $mobile;
    	$params['sendcontent'] = iconv('UTF-8', 'GB2312//IGNORE', $content);
        $http = K::M('net/http');
    	if($ret = $http->http($this->gateway, $params, 'GET')){
    		if($ret == 1){
    			return true;
    		}else{
                switch($ret){
				   case -1:$error=L('手机号码不正确');break;
				   case -2:$error=L('除时间外，所有参数不能为空');break;
				   case -3:$error=L('用户名密码不正确');break;
				   case -4:$error=L('平台不存在');break;
				   case -5:$error=L('客户短信数量为0');break;
				   case -6:$error=L('客户账户余额小于要发送的条数');break;
				   case -8:$error=L('非法短信内容');break;
				   case -9:$error=L('未知系统故障');break;
				   case -10:$error=L('网络性错误');break;
				   case -21:$error = L('未添加短信签名');break;
				   default:$error=L('未知的错误');
				}
				$this->lastcode = $ret;
				$this->lastmsg = $error;
    		}
    	}else{
    		$this->lastmsg = L('未知的错误');
    	}
    	return false;
    }

    //查询短信余额
    public function query()
    {
        $url = 'http://jiekou.56dxw.com/sms/HttpInterfaceR.aspx';
        $params = array('comid'=>$this->_cfg['comid'], 'action'=>'moneyc');
        $params['username'] = $this->_cfg['uname'];
        $params['userpwd'] = $this->_cfg['passwd'];
        if($ret = K::M('net/http')->http($url, $params, 'GET')){
            if($ret == -1){
                $this->lastmsg = '账号密码错误';
            }else{
                $a = explode('#', $ret);
                return $a[0];
            }
        }else{
            $this->lastmsg = L('未知的错误');
        }
        return false;
    }
}