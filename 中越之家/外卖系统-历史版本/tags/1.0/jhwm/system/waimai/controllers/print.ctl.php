<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15
 * Time: 11:01
 */
if(!defined("__CORE_DIR")){
    exit("Access");
}
Import::L('yilianyun/YLYSignAndUuidClient.php');
class Ctl_Print extends Ctl {

    public function setPrintStatus(){


        if($this->checksubmit()){

            $data = $_REQUEST;
            $machine_code = $data['machine_code'];
            $online = $data['online'];
            $push_time = $data['push_time'];
            $sign = $data['sign'];
            if(!$machine_code){
                $this->msgbox->add('打印机不存在',201);
            }else if(!$print = K::M('shop/print')->find(array('machine_code'=>$machine_code))){
                $this->msgbox->add('打印机不存在',202);
            }else if(!$local_sign = YLYSignAndUuidClient::GetSign($push_time)){
                $this->msgbox->add('签名错误',203);
            }else if($local_sign!=$sign){
                $this->msgbox->add('签名错误',204);
            }else {
                if(K::M('shop/print')->set_print_online_by_code($machine_code,$online)){
                    $this->msgbox->add('设置成功');
                }else{
                    $this->msgbox->add('设置失败');
                }
                $this->msgbox->json();


            }
        }else{
            print_r(json_encode(array('data'=>"OK")));exit;

        }






    }


}