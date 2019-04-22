<?php
/**
 * Created by PhpStorm.
 * User: T470P
 * Date: 2018/3/22
 * Time: 16:52
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Accesstoken extends Mdl_Table {

    protected $_table = 'waimai_accesstoken';
    protected $_pk = 'shop_id';
    protected $_cols = 'shop_id,access_token,expires_in,refresh_token,ext_shop_id,ext_title,meituan_token';


    public function update_accesstoken($shop_id, $data)
    {
        $shop_id = (int)$shop_id;
        $filter = array();
        $filter['shop_id'] = $shop_id;
        $detail = $this->detail($shop_id);
        if(!$detail){
            $data['shop_id'] = $shop_id;
            if($this->create($data)){
                return true;
            }else{
                return false;
            }
        }else{
            if($this->update($detail['shop_id'],$data)){
                return true;
            }else{
                return false;
            }
        }
    }



    public function get_access_token($shop_id)
    {
        static $__access_token = null;
        $shop_id = (int)$shop_id;
        if($token = $__access_token[$shop_id]){
            return $token;
        }else if(!$detail = $this->detail($shop_id)){
            return false;
        }else{
                if($detail['expires_in'] < __TIME&&$detail['access_token']){
                    try {
                        $token = K::M('ele/ele')->get_token_by_refresh_token($detail['refresh_token']);
                        $data = array(
                            'access_token'=>$token->access_token,
                            'expires_in'=>$token->expires_in+__TIME,
                            'refresh_token'=>$token->refresh_token
                        );
                        $this->update($shop_id, $data);
                        return $this->detail($shop_id);
                    } catch (Exception $e) {
                        return false;
                    }
                }else{
                  return $detail;
                }


        }
    }


















}