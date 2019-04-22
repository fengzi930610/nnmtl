<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: json.mdl.php 2034 2013-12-07 03:08:33Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Utility_Json
{
    public function encode($data)
    {
        if(defined('IN_APP')){
            $data = $this->convert_data($data);
        }
        return $json = json_encode($data);
        return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $json); 
    }
    public function decode($data, $l=true)
    {
        return json_decode($data, $l);
    }
    protected function convert_data($data)
    {
        foreach($data as $k=>$v){
            if(is_array($v)){
                $data[$k] = $this->convert_data($v);
            }else{
                $data[$k] = (string)$v;
            }
        }
        return $data;
    }
}