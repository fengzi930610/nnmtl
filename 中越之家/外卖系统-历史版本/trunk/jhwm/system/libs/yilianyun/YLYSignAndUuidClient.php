<?php


require_once __CORE_DIR.'libs/yilianyun/YLYConfigClient.php';

class YLYSignAndUuidClient{


    public static function GetSign($timestamp)
    {
        return md5(
            YLYConfigClient::$YLYClientId.
            $timestamp.
            YLYConfigClient::$YLYClientSecret
        );
    }


    public static function Uuid4(){
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = '-';
        $uuidv4 =
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12);
        return $uuidv4;
    }


}