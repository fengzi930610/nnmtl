<?php
namespace app\wechat\controller;
use app\wechat\controller\Commom;

class Index extends Commom{
	
	//微信接口入口函数
	public function index(){
		//获取access_token
		$appid = 'wxfaa1ef66c31ccd79';
		$secret = '8a150f4e6014d1d742d1adc4c02ee020';
		$api = 'https://api.weixin.qq.com/cgi-bin/token';
		$arr_param = [
			"appid"=>$appid;
			"secret"=>$secret;
			'grant_type'=>"client_credential";
		];
		$returnarr = $this->getRequst($api,$arr_param);
		
		
		$this->getattr();
//		echo '123456';die;
		switch ($this->msgType) {
			case 'text':
				$this->textMsg();
				break;
			case 'image':
				$this->imageMsg();
				break;
			case 'event':
				$this->eventMsg();
				break;
		}
		
		
	}
	//关注事件
	public function eventMsg(){
		$array = [];
		$array['openId'] = $this->fromUsername;
		if($this->event == 'subscribe'){
			//关注
			$array['ctime'] = $this->ctime;
			db('event')->insert($array);
			
			//回复信息
			$time = time();
		    $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";  
		//		$array=['php','java'];
			
			$contentStr = "欢迎关注岛国动漫在线"."\n";
			$contentStr .= "官方微信号12222222222"."\n";
			$contentStr .= "<a href='http://www.baidu.com'>点我，有惊喜</a>"."\n";
			$msgType = "text";
			$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, $msgType, $contentStr);
			echo $resultStr;
		}else{
			//取消关注
			db('event')->where('openId',$array['openId'])->delete();
		}
	}
	
	//文字信息回复
	public function textMsg(){
		//get post data, May be due to the different environments
      	//extract post data
		
        $time = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";  
		$array=['php','java'];
		if(!empty( $this->keyword ))
        {
      		$msgType = "text";
			if(in_array($this->keyword, $array)){
				$contentStr = "欢迎咨询";
			}else if($this->keyword == 520){
				$contentStr = "me too";
			}else{
				$contentStr = "Welcome to wechat world!";
			}
        	
        	$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, $msgType, $contentStr);
        	echo $resultStr;
        }else{
        	echo "Input something...";
        }

        
    }
	
	//图片信息回复
	public function imageMsg(){
		//get post data, May be due to the different environments
      	//extract post data
		
        $time = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";  
//		$array=['php','java'];
		
  		$contentStr = "很漂亮啊！！！！！";
  		$msgType = "text";
    	$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, $msgType, $contentStr);
    	echo $resultStr;
        

        
    }
	
		
}

?>