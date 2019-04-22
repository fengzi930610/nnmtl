<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Weixin_Weixin extends Mdl_Table
{   
  
    protected $_table = 'weixin';
    protected $_pk = 'shop_id';
    protected $_cols = 'shop_id,wx_appid,wx_appsecret,access_token,refresh_token,token_expire_in,nick_name,verify_type,wx_type,wx_name,wx_ghid,head_img,qrcode_url,dateline';
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, false);
    }
    
    
    public function update_weixin($shop_id, $data)
    {
        $shop_id = (int)$shop_id;
        if(!$detail = $this->detail($shop_id)){
            //创建
            $data['shop_id'] = $shop_id;
            if($this->create($data)){
                return true;
            }else{
                return false;
            }
        }else{       
            //更新
            if($this->update($shop_id,$data)){
                 return true;
            }else{
                return false;
            }
        }
    }
   //通过商家shop_id获取绑定的access_token
    public function get_access_token($shop_id)
    {
        static $__access_token = null;
        $shop_id = (int)$shop_id;
        if($token = $__access_token[$shop_id]){
            return $token;
        }else if(!$detail = $this->detail($shop_id)){
            return false;
        }else{
            if($detail['token_expire_in'] < __TIME){ // 过期
                //获取最新的token                
                 $component = K::M('weixin/wechat')->component_client();
                 if($ret = $component->refresh_authorizer_token($detail['wx_appid'], $detail['refresh_token'])){          
                    $a = array('access_token'=>$ret['authorizer_access_token'], 'refresh_token'=>$ret['authorizer_refresh_token'],'token_expire_in'=>__TIME+$ret['expires_in']);
                    $this->update($shop_id, $a);
                    $__access_token[$shop_id] =  $a['access_token'];
                    return $__access_token[$shop_id];
                 }
            }else{
                $__access_token[$shop_id] =  $detail['access_token'];
                return $__access_token[$shop_id];
            }
        }
    }

    public function get_msgType()
    {
        return array(
            1 => '文本',
            2 => '图片',
            3 => '语音',
            4 => '视频',
            5 => '小视频',
            6 => '位置',
            7 => '链接',
        );   
    }
    
    public function detail_by_sid($shop_id)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE shop_id='$shop_id'";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }

    public function detail_by_appid($appid)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE wx_appid='$appid'";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;
    }
    public function detail_by_ghid($ghid)
    {
        $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE wx_ghid='$ghid'";
        if($row = $this->db->GetRow($sql)){
            $row = $this->_format_row($row);
        }
        return $row;        
    }
}