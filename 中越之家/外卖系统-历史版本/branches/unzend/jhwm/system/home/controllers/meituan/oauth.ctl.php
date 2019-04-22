<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/4/3
 * Time: 11:08
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Meituan_Oauth extends ctl {


    public function setaccesstoken(){
        $data = $_REQUEST;
        if($data){
            $insert_data = array();
            $insert_data['shop_id'] =$data['ePoiId'];
            $insert_data['meituan_token'] = $data['appAuthToken'];
           /*  $shop_info = K::M('meituan/meituan')->get_user($data['appAuthToken'],$data['ePoiId']);
            if($shop_data = json_decode($shop_info,true)){
                $insert_data['ext_shop_id'] = 0;
                $insert_data['ext_title'] = $shop_data['name'];
            }*/
            K::M('waimai/accesstoken')->update_accesstoken($insert_data['shop_id'],$insert_data);

        }

        echo json_encode(array('data'=>"success"));exit;
    }


    public function unbind(){
        $data = $_REQUEST;
        if($data){
         $shop_id = $data['ePoiId'];
         K::M('waimai/accesstoken')->update_accesstoken($shop_id,array('meituan_token'=>""));
        }
        echo json_encode(array('data'=>"success"));exit;

    }


}