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
class Mdl_Magic_Baidu extends Model {

   // protected  $url = 'http://api.map.baidu.com/routematrix/v2/riding?output=json&ak=xi4AzWcvZ2FczMZlk4bvo0atmoqNX12h';

    //$parmas  lng lat lng lat
    public function juli($lng,$lat,$lng_1,$lat_1){
       // http://api.map.baidu.com/routematrix/v2/driving?output=json&ak=xi4AzWcvZ2FczMZlk4bvo0atmoqNX12h&origins=40.45,116.34&destinations=40.34,116.45
        if(!$lng||!$lat||!$lng_1||!$lat_1){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;

            $parmas = array(
                'origin'=>sprintf("%1\$.6f",$lng).','.sprintf("%1\$.6f",$lat),
                'destination'=>sprintf("%1\$.6f",$lng_1).','.sprintf("%1\$.6f",$lat_1)
            );
            $url = "http://restapi.amap.com/v4/direction/bicycling?key={$server_key}".'&origin='.$parmas['origin'].'&destination='.$parmas['destination'];
            if($res =  K::M('net/http')->get($url,array())){
                if($json = json_decode($res,true)){
                    if($json['errcode']==0&&$json['data']['paths'][0]){
                        return $json['data']['paths'][0]['distance'];
                    }
                    return false;
                }
                return false;
            }
            return false;
        }
    }

    public function juliinfo($lng,$lat,$lng_1,$lat_1){
        if(!$lng||!$lat||!$lng_1||!$lat_1){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;

            $parmas = array(
                'origin'=>sprintf("%1\$.6f",$lng).','.sprintf("%1\$.6f",$lat),
                'destination'=>sprintf("%1\$.6f",$lng_1).','.sprintf("%1\$.6f",$lat_1)
            );
            $url = "http://restapi.amap.com/v4/direction/bicycling?key={$server_key}".'&origin='.$parmas['origin'].'&destination='.$parmas['destination'];
            if($res =  K::M('net/http')->get($url,array())){
                if($json = json_decode($res,true)){
                    if($json['errcode']==0&&$json['data']['paths'][0]){
                        return array(
                            'distance'=>$json['data']['paths'][0]['distance'],
                            'path'=>$json['data']['paths'][0]['steps']
                        );
                    }
                    return false;
                }
                return false;
            }
            return false;
        }

    }

    public function decode_by_location($lng,$lat){
        if(!$lng||!$lat){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;
            $lng = sprintf("%1\$.6f",$lng);
            $lat = sprintf("%1\$.6f",$lat);

          // 'http://restapi.amap.com/v3/geocode/regeo?key='+window.CFG.JH_MAP_KEY+"&radius=100"+"&location="+lng+','+lat+"&output=json";
            $url = 'http://restapi.amap.com/v3/geocode/regeo?key='.$server_key."&radius=100&location=".$lng.','.$lat.'&output=json';
            if($res = K::M('net/http')->get($url)){
                if($data = json_decode($res,true)){
                    if($data['infocode']==10000){
                        $province = $data['regeocode']['addressComponent']['province'];
                        $city = $data['regeocode']['addressComponent']['city'];
                        $addr = $data['regeocode']['formatted_address'];
                        $addr = str_replace($province,"",$addr);
                        $addr =  str_replace($city,"",$addr);
                        $return_data = array(
                            'addr'=>$addr,
                            'country'=>$data['regeocode']['addressComponent']['country'],
                            'city'=>$data['regeocode']['addressComponent']['city'],
                            'district'=>$data['regeocode']['addressComponent']['district'],
                            'township'=>$data['regeocode']['addressComponent']['township'],
                            'lng'=>$lng,
                            'lat'=>$lat
                        );
                        return $return_data;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function geocode_by_addr($addr=null, $city=null)
    {
        if(!$addr){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;
            $url = "http://restapi.amap.com/v3/geocode/geo?address=".$addr."&output=json&key=".$server_key;
            if($city){
                $url .= "&city=".$city;
            }
            if($res = K::M('net/http')->get($url)){
                if($data = json_decode($res,true)){
                    if($data['infocode']==10000 && $data['geocodes']){
                        $return_data = array();
                        foreach ($data['geocodes'] as $k => $v) {
                            $lnglat = explode(',', $v['location']);
                            $v['lng'] = $lnglat[0];
                            $v['lat'] = $lnglat[1];
                            $return_data[] = $v;
                        }                        
                        return $return_data;
                    }
                }
            }
            return false;
        }       
    }


}