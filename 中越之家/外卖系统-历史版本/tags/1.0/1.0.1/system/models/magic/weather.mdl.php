<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 10:16
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Magic_Weather extends Model {

    protected $url = 'https://www.baidu.com/home/other/data/weatherInfo?city={city}';

    public function weather($city){
        $_cache_key = 'weather_'.MD5($city);
        if(!$data = K::M('cache/cache')->get($_cache_key)){
            $data = array('type'=>1, 'temperature'=>'25°', 'title'=>'晴', 'indicator'=>'50 良', 'icon'=>'');
            $city = str_replace('市', '', $city);  
            $url = str_replace('{city}', $city, $this->url);
            if($res =  K::M('net/http')->get($url,array())){
                if($json = json_decode($res,true)){
                    if($json['errNo'] == 0 && $today = $json['data']['weather']['content']['today']){
                        $data['type'] = 1;
                        $data['title'] = $this->getPollution($today['pm25']);
                        $data['temperature'] = str_replace('℃', '°', $today['temp']);
                        $data['indicator'] = $today['pm25'];
                        //$data['icon'] = $today['img'][0];
                        $data['icon'] = 'https'.substr($today['img'][0], 4);
                    }
                }
            }
            K::M('cache/cache')->set($_cache_key, $data, 7200);
        }        
        return $data;
    }

    public function getPollution($pm25=0)
    {
        $pm25 = (int)$pm25 ? (int)$pm25 : 0;
        $label = '优';
        if($pm25 < 50){
            $label = '优';
        }else if($pm25 > 50 && $pm25 <= 100){
            $label = '良';
        }else if($pm25 > 100 && $pm25 <= 150){
            $label = '轻度';
        }else if($pm25 > 150 && $pm25 <= 200){
            $label = '中度';
        }else if($pm25 > 200 && $pm25 <= 300){
            $label = '重度';
        }else if($pm25 > 300 ){
            $label = '严重';
        }
        return $label;
    }
}