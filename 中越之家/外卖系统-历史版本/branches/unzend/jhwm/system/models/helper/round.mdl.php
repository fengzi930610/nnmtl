<?php

/**

 * Copy Right IJH.CC

 * Each engineer has a duty to keep the code elegant

 * Author @shzhrui<Anhuike@gmail.com>

 * $Id$

 */

class Mdl_Helper_Round extends Model

{



    /**

    *计算某个经纬度的周围某段距离的正方形的四个点

    *

    *@param lng float 经度

    *@param lat float 纬度

    *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米

    *@return array 正方形的四个点的经纬度坐标

    */

    public function returnSquarePoint($lng, $lat, $distance = 10)

    {

        define(EARTH_RADIUS, 6371);//地球半径，平均半径为6371km

        $dlng =  2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));

        $dlng = rad2deg($dlng);

        $dlat = $distance/EARTH_RADIUS;

        $dlat = rad2deg($dlat);

        return array(

            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),

            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),

            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),

            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)

        );

    }

    //计算经纬度距离

    public function getdistance($lng1, $lat1, $lng2, $lat2)

    {  

        return $this->juli_label($lng1, $lat1, $lng2, $lat2);

    }

    //计算经纬度距离 返回米数

    public function getdistances($lng1, $lat1, $lng2, $lat2)

    {  

        return $this->juli($lng1, $lat1, $lng2, $lat2);

    }

    //计算经纬度距离 返回米数

    public function juli($lng1, $lat1, $lng2, $lat2)

    {  //计算经纬度距离

        //将角度转为狐度

        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度

        $radLat2=deg2rad($lat2);

        $radLng1=deg2rad($lng1);

        $radLng2=deg2rad($lng2);

        $a=$radLat1-$radLat2;

        $b=$radLng1-$radLng2;

        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;

        return round($s,2);

    }

    //计算经纬度距离

    public function juli_label($lng1, $lat1, $lng2, $lat2)

    {  

        $s = $this->juli($lng1, $lat1, $lng2, $lat2);

        return K::M('helper/format')->juli($s);

    }





    /**

     * 判断点坐标是否在一个几何多边形之内

     * @param   array  $poly_list  包含经纬度(lat,lng)的一维数组

     * @param   float  $x          纬度(lat)

     * @param   float  $y          经度(lng)

     * @return  boolean

     *

     * 如果点在多边形内部，返回true， 否则返回 false;

     * 如果恰好点与多边形边缘重合，函数会返回 true 或 false;

     */

    public function in_or_out_polygon($poly_list, $x, $y)

    {

        $polyX = $polyY = array();

        $oddNodes = false;

        foreach ($poly_list as $k => $v) {

            $polyX[] = $v['lat'];

            $polyY[] = $v['lng'];

        }

        $x = (float) $x;

        $y = (float) $y;

        $polySides = count($polyX);

        $j = $polySides - 1;

        for ($i=0; $i < $polySides; $i++) { 

            if(($polyY[$i] < $y && $polyY[$j] >= $y || $polyY[$j] < $y && $polyY[$i] >= $y) && ($polyX[$i] <= $x || $polyX[$j] <= $x)){

                if($polyX[$i] + ($y - $polyY[$i]) / ($polyY[$j] - $polyY[$i]) * ($polyX[$j] - $polyX[$i]) < $x) {

                    $oddNodes = !$oddNodes;

                }

            }

            $j = $i;

        }

        return $oddNodes;

    }

}

