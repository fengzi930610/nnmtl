<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z wanglei $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Position extends Ctl
{

    public function index()
    {
        if($this->uid){
            $addr_list = K::M('member/addr')->items(array('uid'=>$this->uid));
            foreach($addr_list as $key => &$value)
            {
                $addr_list[$key]['streetAddr'] = explode(" - ", $value['addr']);
                if(count($addr_list[$key]['streetAddr']) > 1)
                {
                    $addr_list[$key]['streetAddr'] = explode(",",$addr_list[$key]['streetAddr'][1]);
                    $addr_list[$key]['streetAddr'] = trim($addr_list[$key]['streetAddr'][0]);
                }
                else
                    $addr_list[$key]['streetAddr'] = $value['addr'];
                unset($value);
            }
            $this->pagedata['addr_list']= $addr_list;
        }
        $this->tmpl = 'position.html';
    }

    public function iplocation(){
        if($data = K::M('net/http')->callapi('tools/ip/geo',array('ip'=>__IP))){

            $this->msgbox->set_data('data',$data);
        }else{
            $this->msgbox->add('定位失败',201);

        }

    }


}