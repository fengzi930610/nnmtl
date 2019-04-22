<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: xheditor.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Ctl_Magic_Xheditor extends Ctl
{
    
    public function index()
    {
        
    }
    public function upload()
    {
		//HTML5上传
    	if(isset($_SERVER['HTTP_CONTENT_DISPOSITION']) && preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'], $info)){
			$attach['name'] = $info[2];
			$attach['data'] = file_get_contents("php://input");
			$data['html5'] = true;
		}else if(!$attach = $_FILES['filedata']){
    		echo '{"err":, "msg":"上传文件失败"}';
    	}else if(UPLOAD_ERR_OK != $attach['error']){
    		echo '{"err":"221", "msg":"上传文件失败"}';
    	}
        if($data = K::M('magic/upload')->xheditor($attach)){
    		$cfg = $this->system->config->get('attach');
            echo '{"err":"","msg":{"url":"!'.$cfg['attachurl'].'/'.$data['photo'].'?PID'.$data['photo_id'].'","localname":"'.$attach['name'].'","id":"'.$data['photo_id'].'"}}';
    	}
        exit();
    }
}