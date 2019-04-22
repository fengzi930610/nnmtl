<?php
/**
 * Copy Right Anhuike.com
 * $Id check.mdl.php shzhrui<anhuike@gmail.com>
 */
class Mdl_Verify_Check
{
    
    public static function mail($mail)
    {
        if(strlen($mail)>6 && preg_match("/^[\w\-\.]+@[\w\-\.]+[\.\w]{2,}$/", $mail)){
            return $mail;
        }
        return false;
    }
    public static function phone($phone)
    {
        if(preg_match("/^((1[3-9]{1}[\d]{9})|(((400|800)-?(\d{3})-?(\d{4}))|^((\d{7,8})|(\d{4}|\d{3})-?(\d{7,8})|(\d{4}|\d{3})-(\d{3,7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$))$/", $phone)){
            return $phone;
        }
        return false;
        //return preg_match("/^(0?(([1-9]\d)|([3-9]\d{2}))-?)?\d{7,8}$/", $phone);
    }
    public static function mobile($mobile)
    {
        return self::vietnamMobile($mobile);
        if(preg_match("/^1[3-9]\d{9}$/", $mobile)){
            return $mobile;
        }
        return false;
    }
    public static function number($number)
    {
        return is_numeric($number);
    }
    public static function ids($ids)
    {
        if(is_array($ids)){
            $ids = implode(',', $ids);
        }
        if(preg_match("/^(\d+|(\d([\d,]+?)\d))$/",$ids)){
            return $ids;
        }
        return false;
    }
    public static function url($url)
    {
        if(!preg_match('/^http[s]?:\/\/(([0-9]{1,3}\.){3}[0-9]{1,3}|([0-9a-z_!~*\'()-]+\.)*([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.[a-z]{2,6})(:[0-9]{1,4})?((\/\?)|(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/',  
        $url)){
            return false;
        }
        return $url;   
    }
    public static function qq($qq)
    {
        if(preg_match('/^\d{5,10}(\,\d{5,10})*$/', $qq)){
            return $qq;
        }
        return false;
    }
    public static function len($len, $min=null, $max=null)
    {
        if($min !== null && $len < $min){
            return false;
        }else if($max !== null && $len > $max){
            return false;
        }
        return true;
    }
    public static function id_number($id_number)
    {
        if(preg_match('/^\d{17}(\d|x)$/i', $id_number)  || preg_match('/^\d{15}$/i', $id_number)){
            return $id_number;
        }
        return false;
    }

    public function check_specification($spection,$str){
        if(!$str){
            return true;
        }else{
            $shuxin_arr = array();
            $key_arr = array();
            $tmp_arr = array_filter(explode('-',$str));
            foreach($spection as $kk=>$vv){
                $key_arr[] =  $vv['key'];
            }
            foreach($tmp_arr as $k=>$v){
                $tmp_data = explode('_',$v);
                if(!in_array($tmp_data[0],$key_arr)){
                    return false;
                }
                foreach($spection as $k1=>$v1){
                    if($v1['key']==$tmp_data[0]){
                        if(!in_array($tmp_data[1],$v1['val'])){
                            return false;
                        }
                    }
                }
                $shuxin_arr[] = array(
                    'key'=>$tmp_data[0],
                    'val'=>$tmp_data[1]
                );

            }
        }

        return $shuxin_arr;

    }

    public function passwd($passwd)
    {
       if(!preg_match('/^[\x21-\x7E]{6,32}$/', $passwd)){
            //$this->msgbox->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~32字符', 401);
            return false;
        }        
        return $passwd;
    }

    //=====20181130 新增 检测越南手机号
    public static function vietnamMobile($mobile)
    {
        $mobile = trim($mobile);
        if(preg_match("/^\d{10,11}$/", $mobile)){
            return $mobile;
        }
        return false;
    }

}