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

            $juliInfo = $this->juliinfo($lng,$lat,$lng_1,$lat_1);

            if(!$juliInfo)
                return false;
            return $juliInfo['distance'];

            


            // $parmas = array(
            //     'origin'=>sprintf("%1\$.6f",$lng).','.sprintf("%1\$.6f",$lat),
            //     'destination'=>sprintf("%1\$.6f",$lng_1).','.sprintf("%1\$.6f",$lat_1)
            // );
            // $url = "http://restapi.amap.com/v4/direction/bicycling?key={$server_key}".'&origin='.$parmas['origin'].'&destination='.$parmas['destination'];
            // if($res =  K::M('net/http')->get($url,array())){
            //     if($json = json_decode($res,true)){
            //         if($json['errcode']==0&&$json['data']['paths'][0]){
            //             return $json['data']['paths'][0]['distance'];
            //         }
            //         return false;
            //     }
            //     return false;
            // }
            // return false;
        }
    }

    public function juliinfo($lng,$lat,$lng_1,$lat_1){
        if(!$lng||!$lat||!$lng_1||!$lat_1){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;

            //注：谷歌地图骑车路线模式通常是无法获得路径数据的，所以这里只使用驾车模式！！！
            //    得到数据后，将驾车模式的steps转化为与高德地图的骑车返回数据（原数据模式）相兼容的数据，由于是转换的，所以有些数据可能就为空或没有实质意思，比如方向、行程预计耗时等。
            $url = "https://maps.googleapis.com/maps/api/directions/json?key={$server_key}&origin={$lat},{$lng}&destination={$lat_1},{$lng_1}&language=zh-CN&units=metric";
            
            $res = K::M('net/http')->get($url,[]);
            if(!$res)
                return false;
            $res = json_decode($res,true);
            if(!$res)
                return false;

            if(strtoupper($res['status']) !== "OK" 
                || !isset($res['routes']) 
                || !is_array($res['routes']) 
                || !isset($res['routes'][0])
                || !isset($res['routes'][0]['legs'])
                || !is_array($res['routes'][0]['legs'])
                || count($res['routes'][0]['legs']) === 0
            )
                return false;

            $info = [
                'distance' => (float)$res['routes'][0]['legs'][0]['distance']['value'],
                'path' => []
            ];

            foreach($res['routes'][0]['legs'][0]['steps'] as &$stepInfo)
            {
                $info['path'][] = [
                    'instruction' => $stepInfo['html_instructions'],
                    'road' => "",
                    'distance' => (float)$stepInfo['distance']['value'],
                    'orientation' => "",
                    'duration' => (float)$stepInfo['duration']['value'],
                    'polyline' => $stepInfo['polyline']['points'],  //注意！！！这个是encode polyline，因为也没有用到，所以暂时不研究如何解码，但如果使用，则需要解码后再使用！！！
                    'action' => "",
                    'assistant_action' => ""
                ];
                unset($stepInfo);
            }

            return $info;
            //$url = "https://maps.googleapis.com/maps/api/directions/json?key={$server_key}&origins={$lat},{$lng}&destinations={$lat_1},{$lng_1}&language=zh-CN&units=metric";

            // $parmas = array(
            //     'origin'=>sprintf("%1\$.6f",$lng).','.sprintf("%1\$.6f",$lat),
            //     'destination'=>sprintf("%1\$.6f",$lng_1).','.sprintf("%1\$.6f",$lat_1)
            // );
            // $url = "http://restapi.amap.com/v4/direction/bicycling?key={$server_key}".'&origin='.$parmas['origin'].'&destination='.$parmas['destination'];
            // if($res =  K::M('net/http')->get($url,array())){
            //     if($json = json_decode($res,true)){
            //         if($json['errcode']==0&&$json['data']['paths'][0]){
            //             return array(
            //                 'distance'=>$json['data']['paths'][0]['distance'],
            //                 'path'=>$json['data']['paths'][0]['steps']
            //             );
            //         }
            //         return false;
            //     }
            //     return false;
            // }
            // return false;
        }

    }

    public function decode_by_location($lng,$lat){
        if(!$lng||!$lat){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;
            $lng = sprintf("%1\$.6f",$lng);
            $lat = sprintf("%1\$.6f",$lat);

            $url = "https://maps.googleapis.com/maps/api/geocode/json?key={$server_key}&latlng={$lat},{$lng}&language=zh-CN&result_type=point_of_interest|premise|street_address";
            
            $res = K::M('net/http')->get($url);

            if(!$res)
                return false;
            $res = json_decode($res,true);
            if(!$res)
                return false;
            if(strtoupper($res['status']) !== "OK")
                return false;
            $info = [
                'addr' => "",   //地址，应该把国家、省、市的信息去掉，因为省市有数据库进行保存
                'country' => "",    //国家
                "province" => "",   //省
                'city' => "",   //城市
                'district' => "",   //坐标点所在区
                'township' => "",   //坐标点所在乡镇/街道（此街道为社区街道，不是道路信息）
                'lng' => 0,
                'lat' => 0,
            ];
            $addrArr = ["","",""];

            foreach($res['results'][0]['address_components'] as &$cmpInfo)
            {
                if(in_array("country",$cmpInfo['types']))
                    $info['country'] = $cmpInfo['long_name'];
                else if(in_array("administrative_area_level_1", $cmpInfo['types']))
                    $info['province'] = $cmpInfo['long_name'];
                else if(in_array("locality", $cmpInfo['types']) || in_array("administrative_area_level_2", $cmpInfo['types']))
                    $info['city'] = $cmpInfo['long_name'];
                else if(in_array("sublocality", $cmpInfo['types']) || in_array("sublocality_level_1", $cmpInfo['types']))
                    $info['district'] = $addrArr[0] = $cmpInfo['long_name'];
                else if(in_array("route", $cmpInfo['types']))
                    $info['township'] = $addrArr[1] = $cmpInfo['long_name'];
                else if(in_array("street_number", $cmpInfo['types']))
                {
                    $addrArr[2] = $cmpInfo['long_name'];
                    if(false ===strstr($addrArr[2],"号"))
                        $addrArr[2] .= "号";
                }
                else if(in_array("premise", $cmpInfo['types']))
                    $addrArr[2] =  $cmpInfo['long_name'];
                unset($cmpInfo);
            }
            $info['addr'] = implode("", $addrArr);
            $info['lng'] = (int)($res['results'][0]['geometry']['location']['lng']*1000000)/1000000;
            $info['lat'] = (int)($res['results'][0]['geometry']['location']['lat']*1000000)/1000000;
            return $info;


          // 'http://restapi.amap.com/v3/geocode/regeo?key='+window.CFG.JH_MAP_KEY+"&radius=100"+"&location="+lng+','+lat+"&output=json";
            // $url = 'http://restapi.amap.com/v3/geocode/regeo?key='.$server_key."&radius=100&location=".$lng.','.$lat.'&output=json';
            // if($res = K::M('net/http')->get($url)){
            //     if($data = json_decode($res,true)){
            //         if($data['infocode']==10000){
            //             $province = $data['regeocode']['addressComponent']['province'];
            //             $city = $data['regeocode']['addressComponent']['city'];
            //             $addr = $data['regeocode']['formatted_address'];
            //             $addr = str_replace($province,"",$addr);
            //             $addr =  str_replace($city,"",$addr);
            //             $return_data = array(
            //                 'addr'=>$addr,
            //                 'country'=>$data['regeocode']['addressComponent']['country'],
            //                 'city'=>$data['regeocode']['addressComponent']['city'],
            //                 'district'=>$data['regeocode']['addressComponent']['district'],
            //                 'township'=>$data['regeocode']['addressComponent']['township'], 
            //                 'lng'=>$lng,
            //                 'lat'=>$lat
            //             );
            //             return $return_data;
            //         }else{
            //             return false;
            //         }
            //     }else{
            //         return false;
            //     }
            // }else{
            //     return false;
            // }
        }
    }

    public function geocode_by_addr($addr=null, $city=null)
    {
        if(!$addr || !is_string($addr) || trim($addr)===""){
            return false;
        }else{
            $server_key = MAP_SERVER_KEY;

            $addr = str_replace("+", "%2b", $addr);
            $addr = preg_replace('/\s+/i', " ", $addr);
            $addr = str_replace(" ", "+", $addr);

            if($city !== null)
                $city = trim($city);
            
            $cityBounds = "";
            if($city)
            {
                //先查城市的范围数据
                $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?key={$server_key}&input={$city}&language=zh-CN&types=(cities)";
                $res = K::M('net/http')->get($url);
                
                if(!$res || !($res = json_decode($res,true)))
                    return false;
                if(strtoupper($res['status']) !== "OK")
                    return false;
                //只使用越南内的城市！
                $placeId = [];
                foreach($res['predictions'] as &$item)
                {
                    if(in_array("locality", $item['types']) && $item['terms'][count($item['terms'])-1]['value'] === "越南")
                    {
                        $placeId = $item['place_id'];
                        break;
                    }
                    unset($item);
                }
                if($placeId === "")
                    return false;

                //使用place_id查询各城市的范围信息（矩形区域）
                $url = "https://maps.googleapis.com/maps/api/geocode/json?key={$server_key}&place_id={$placeId}&language=zh-CN";
                $res = K::M('net/http')->get($url);
                if(!$res || !($res = json_decode($res,true)))
                    return false;
                if(strtoupper($res['status']) !== "OK")
                    return false;
                //rectangle:south,west|north,east
                $cityBounds = "rectangle:{$res['results'][0]['geometry']['bounds']['southwest']['lat']},{$res['results'][0]['geometry']['bounds']['southwest']['lng']}|{$res['results'][0]['geometry']['bounds']['northeast']['lat']},{$res['results'][0]['geometry']['bounds']['northeast']['lng']}";
            }

            //注意！！！虽然返回结果做了循环获取，但谷歌地图的place find一次仅会返回一个地址，所以函数return的数据永远都是一个单元素的数组 ！！！ 这只是为了兼容原代码逻辑而做的妥协！！！
            $url = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?key={$server_key}&input={$addr}&inputtype=textquery&language=zh-CN&fields=geometry,name";
            if($cityBounds !== "")
                $url .= "&locationbias={$cityBounds}";

            $res = K::M('net/http')->get($url);

            if(!$res)
                return false;
            $res = json_decode($res,true);
            if(!$res || strtoupper($res['status']) !== "OK")
                return false;
            $list = [];
            foreach($res['candidates'] as &$resInfo)
            {
                $list[] = [
                    'lng' => (int)($resInfo['geometry']['location']['lng']*1000000)/1000000,
                    'lat' => (int)($resInfo['geometry']['location']['lat']*1000000)/1000000,
                ];
                unset($resInfo);
            }
            return $list;

            // $url = "http://restapi.amap.com/v3/geocode/geo?address=".$addr."&output=json&key=".$server_key;
            // if($city){
            //     $url .= "&city=".$city;
            // }
            // if($res = K::M('net/http')->get($url)){
            //     if($data = json_decode($res,true)){
            //         if($data['infocode']==10000 && $data['geocodes']){
            //             $return_data = array();
            //             foreach ($data['geocodes'] as $k => $v) {
            //                 $lnglat = explode(',', $v['location']);
            //                 $v['lng'] = $lnglat[0];
            //                 $v['lat'] = $lnglat[1];
            //                 $return_data[] = $v;
            //             }                        
            //             return $return_data;
            //         }
            //     }
            // }
            // return false;
        }       
    }


}