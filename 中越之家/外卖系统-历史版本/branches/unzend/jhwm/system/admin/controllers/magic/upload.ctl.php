<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: upload.ctl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Ctl_Magic_Upload extends Ctl
{
    
    public function index()
    {
        
    }
    public function editor()
    {
    	if(!$attach = $_FILES['imgFile']){
    		$this->msgbox->add('上传文件失败', 211);
    	}else if(UPLOAD_ERR_OK != $attach['error']){
    		$this->msgbox->add('上传文件失败', 212);
    	}else if($data = K::M('magic/upload')->xheditor($attach)){
    		$cfg = $this->system->config->get('attach');
    		$this->msgbox->set_data('url', $cfg['attachurl'].'/'.$data['photo'].'?PID'.$data['photo_id']);
                $this->msgbox->set_data('photo', $data['photo']);
    	}
        $this->msgbox->json();
    }
}