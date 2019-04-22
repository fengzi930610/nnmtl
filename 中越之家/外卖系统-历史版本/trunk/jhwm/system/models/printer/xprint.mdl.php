<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/3
 * Time: 15:29
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Printer_Xprint extends Model {

    protected $url = "http://115.28.15.113:60002";
    public function __construct()
    {

    }

    public function addAndOpenPrint($machineCode,$msign){
        return true;
    }

    public function send_print($machine_caode,$content,$order_id){
        $res =$this->postData($this->url,implode("&",$content));
        if($res=='OK'){
            return true;
        }else{
            return false;
        }
    }

    public function cancelAll($machine_code){
       K::$system->msgbox->add('该打印机暂不支持清空打印队列',308);
        return false;

    }


    //喜乐打印机专用
    public function postData($url, $data)
    {
        $ch = curl_init();
        $timeout = 300;
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转 （很重要）
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, "http://127.0.0.1/");   //构造来路
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        //ob_start();
        $handles = curl_exec($ch);  //获取返回结果
        //$result = ob_get_contents() ;
        //ob_end_clean();
        //close connection
        curl_close($ch);
        //return $result;
        return $handles;
    }




}