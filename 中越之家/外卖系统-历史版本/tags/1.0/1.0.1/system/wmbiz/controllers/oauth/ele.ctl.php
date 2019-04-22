<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/22
 * Time: 17:21
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Oauth_Ele extends Ctl {

    //获取用户AccessToken
    public function setAccessToken(){
        $code = $_GET["code"];
        $error = $_GET["error"];
        if(($error!=null)||($code==null)){
            $this->msgbox->add('用户取消授权',201);
        }else{
            try {
                $token = K::M('ele/ele')->get_token_by_code($code);
                $access_token = $token->access_token;
                $expires_in = $token->expires_in;
                $refresh_token = $token->refresh_token;
                $data = array(
                    'access_token'=>$access_token,
                    'expires_in'=>$expires_in+__TIME,
                    'refresh_token'=>$refresh_token
                );
                try{
                    $oauth = K::M('ele/ele')->get_user($access_token);
                    $data['ext_shop_id'] = $oauth[0]->id;
                    $data['ext_title'] = $oauth[0]->name;
                    $data['type'] = 'ele';
                    //在更新之前 先把之前绑定的清空
                    $filter = array();
                    $filter['ext_shop_id'] =  $data['ext_shop_id'];
                    if($items = K::M('waimai/accesstoken')->items($filter)){
                        foreach($items as $k=>$v){
                            K::M('waimai/accesstoken')->update($v['shop_id'],array('ext_shop_id'=>0,'access_token'=>'',"expires_in"=>0,'refresh_token'=>""));
                        }
                    }
                    K::M('waimai/accesstoken')->update_accesstoken($this->shop_id,$data);
                    if(K::M('waimai/accesstoken')->get_access_token($this->shop_id)){
                        $this->msgbox->add('获取授权成功');
                    }else{
                        $this->msgbox->add('获取授权失败',203);
                    }
                }catch (Exception $e) {
                    $this->msgbox->add($e->getMessage(),202);
                }

            } catch (Exception $e) {
                $this->msgbox->add($e->getMessage(),202);
            }


        }
        $this->msgbox->set_data('forward',K::M('helper/link')->mklink('oauth/index',array(),array(),'wmbiz'));

    }





}