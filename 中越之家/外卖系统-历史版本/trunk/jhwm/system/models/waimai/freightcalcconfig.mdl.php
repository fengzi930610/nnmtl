<?php
/**
 * Copy Right zyzjgzh.cn
 * Writed by Vast
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_Freightcalcconfig extends Mdl_Table
{
    protected $_table = 'freight_calc_config';
    protected $_pk = 'id';
    protected $_cols = 'id,shop_id,type,distance,fee';
    protected $_orderby = array('distance'=>'ASC');

    public function save($shop_id,$type,$data)
    {
        //2019-01-24数据表已废弃直接使用文件保存配置数据，而且数据格式也不再依赖于数据库，也不再有固定的格式
        return $this->save_in_file($shop_id,$type,$data);
        //==========================================

        //=====2019-01-24 以下为未修改时的逻辑，依托于数据表
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        if($shop_id < 0 || !is_array($data))
            return false;
        $optData = [];
        foreach($data as &$value)
        {
            if(!isset($value['distance']) || (!is_numeric($value['distance']) && !is_string($value['distance'])) ||
                !isset($value['fee']) || (!is_numeric($value['fee']) && !is_string($value['fee']))
            ) return false;
            $item = [
                'distance' => (int)$value['distance'],
                'fee' => (float)$value['fee']
            ];
            if($item['distance'] <= 0 || $item['fee'] < 0)
                return false;
            $optData[$item['distance']] = &$item;
            unset($item);
            unset($value);
        }
        ksort($optData);
        $optData = array_merge($optData);
        $maxOptOfs = count($optData)-1;
        //选出原有记录
        $_count = 0;
        $oldRows = $this->items(['shop_id'=>$shop_id,'type'=>$type],"distance ASC",1,9999999,$_count);
        $oldRows = array_merge($oldRows);
        $upts = $adds = $dels = [];
        $ofs = 0;
        foreach($oldRows as &$row)
        {
            if($ofs > $maxOptOfs)
                break;
            if((int)$row['distance'] !== $optData[$ofs]['distance'] || (float)$row['fee'] !== $optData[$ofs]['fee'])
            {
                $upts[] = [
                    'id'=>(int)$row['id'],
                    'distance' => $optData[$ofs]['distance'],
                    'fee' => $optData[$ofs]['fee']
                ];
            }
            $ofs++;
        }
        if($ofs <= $maxOptOfs)
        {
            //原数据比新数据数量少，所以要添加新行
            for($i = $ofs;$i<=$maxOptOfs;++$i)
                $adds[] = &$optData[$i];
        }
        else if($ofs > $maxOptOfs)
        {
            //原数据比新数据要多，所以要删除
            for($i = $ofs;$i<count($oldRows);++$i)
            {
                $id = (int)$oldRows[$i]['id'];
                $dels[$id] = $id;
            }
        }
        $hasErr = false;
        $this->db->Execute("START TRANSACTION");
        if(!$hasErr && count($upts) > 0)
        {
            foreach($upts as &$item)
            {
                if(false === $this->update($item['id'],['distance'=>$item['distance'],'fee'=>$item['fee']],true))
                {
                    $hasErr = true;
                    break;
                }
                unset($item);
            }
        }
        if(!$hasErr && count($adds) > 0)
        {
            foreach($adds as &$item)
            {
                if(false === $this->create(['shop_id'=>$shop_id,'type'=>$type,'distance'=>$item['distance'],'fee'=>$item['fee']],true))
                {
                    $hasErr = true;
                    break;
                }
                unset($item);
            }
        }
        if(!$hasErr && count($dels) > 0 && false === $this->delete(array_merge($dels)))
            $hasErr = true;

        if($hasErr)
            $this->db->Execute("ROLLBACK");
        else
            $this->db->Execute("COMMIT");

        return !$hasErr;
    }

    public function save_by_km($shop_id,$type,$data)
    {
        if(!is_array($data))
            return false;
        foreach($data as $key => &$value)
        {
            if(!isset($value['distance']) || (!is_numeric($value['distance']) && !is_string($value['distance'])))
                return false;
            $val = (int)((float)$value['distance']*1000);
            if($val <= 0)
                return false;
            $data[$key]['distance'] = $val;
            unset($value);
        }
        return $this->save($shop_id,$type,$data);
    }

    public function save_in_file($shop_id,$type,$data)
    {
        $shop_id = (int)$shop_id;
        $type = (int)$type;

        if($shop_id < 0 || ($shop_id>0 && $type<0) || !is_array($data) || !$this->check_data_format($type,$data))
            return false;

        $key = "ShopFreightCalcConfig_{$shop_id}_{$type}";
        K::M('system/data')->set($key,$data);
        return true;
    }

    //返回值:
    //  false表示执行错误或没有符合的数据（可以当作超出范围来理解）
    //  数值就是表示对应的值（包括0）
    public function get_fee($shop_id,$type,$distance)
    {
        //2019-01-24 根据类型，分别选用不同计算公式
        $selType = abs((int)$type);
        if($selType === 1)
            return $this->calcType1($shop_id,$type,$distance);
        else if($selType === 2)
            return $this->calcType1($shop_id,$type,$distance);//$this->calcType2($shop_id,$type,$distance);2019-01-25 同城送也使用相同的计算公式，只是参数不同
        return false;
        //2019-01-24 原来的逻辑，通过查询数据表，废弃
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        $distance = (float)$distance;
        if($shop_id<0 || $type===0 || $distance<0)
            return false;
        $row = $this->find(['shop_id' => $shop_id,'type' => $type, 'distance' => ">:{$distance}"],"distance ASC");
        if(!$row)
            return false;
        return (float)$row['fee'];
    }

    //2019-01-24 新增 外卖运费计算
    protected function calcType1($shop_id,$type,$distance)
    {
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        $distance = (float)$distance;
        if($shop_id<0 || $distance<0)
            return false;
        $cfg = $this->get_data($shop_id,$type);
        if(!is_array($cfg))
            return false;
        $fee = (float)$cfg['start_fee'];
        $curDist = (float)$cfg['start_distance'];
        if($curDist < $distance)
        {
            do
            {
                $fee += (float)$cfg['fee_base'];
                $curDist += (float)$cfg['distance_base'];
                if($curDist >= $distance)
                    break;
            }while(true);
        }
        return $fee;
    }

    //2019-01-24 新增 同城送运费计算
    protected function calcType2($shop_id,$type,$distance)
    {
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        $distance = (float)$distance;
        if($shop_id<0 || $distance<0)
            return false;
        $cfg = $this->get_data($shop_id,$type);
        if(!is_array($cfg))
            return false;
        $fee = 0;
        $dist = 0;
        $distStep = $distance<(float)$cfg['distance_range']?(float)$cfg['in_distance_base']:(float)$cfg['out_distance_base'];
        if($distance > 0 && $distStep > 0)
        {
            while($dist <= $distance)
            {
                $fee += $distance<(float)$cfg['distance_range']?(float)$cfg['in_fee_base']:(float)$cfg['out_fee_base'];
                $dist += $distStep;
            }
        }
        return $fee;
    }

    //2019-01-24 已废弃
    public function get_data_list($shop_id,$type)
    {
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        if($shop_id < 0)
            return false;
        $_count = 0;
        return $this->items(['shop_id'=>$shop_id,'type'=>$type],"distance ASC",1,9999999,$_count);
    }

    //2019-01-24 新增，获取店铺配置数据
    public function get_data($shop_id,$type)
    {
        $shop_id = (int)$shop_id;
        $type = (int)$type;
        if($shop_id < 0 || ($shop_id>0 && $type<0))
            return NULL;

        $key = "ShopFreightCalcConfig_{$shop_id}_{$type}";
        return K::M('system/data')->get($key);
    }

    protected function _format_row($row)
    {
        return $row;
    }

    //2019-01-24 新增，判断数据格式，会根据type来判断
    public function check_data_format($type,&$data)
    {
        $type = abs((int)$type);
        switch($type)
        {
            case 1:
                if(!is_array($data) || 
                    !isset($data['start_distance']) || (float)$data['start_distance'] < 0 ||
                    !isset($data['start_fee']) || (float)$data['start_fee'] < 0 ||
                    !isset($data['distance_base']) || (float)$data['distance_base'] < 0 ||
                    !isset($data['fee_base']) || (float)$data['fee_base'] < 0
                ) return false;
                $optArr = [
                    'start_distance' => (float)$data['start_distance'],
                    'start_fee' => (float)$data['start_fee'],
                    'distance_base' => (float)$data['distance_base'],
                    'fee_base' => (float)$data['fee_base']
                ];
                $data = $optArr;
                unset($optArr);
                break;
            case 2:
                //2019-01-25 使用与外卖相同的参数及模式
                if(!is_array($data) || 
                    !isset($data['start_distance']) || (float)$data['start_distance'] < 0 ||
                    !isset($data['start_fee']) || (float)$data['start_fee'] < 0 ||
                    !isset($data['distance_base']) || (float)$data['distance_base'] < 0 ||
                    !isset($data['fee_base']) || (float)$data['fee_base'] < 0
                ) return false;
                $optArr = [
                    'start_distance' => (float)$data['start_distance'],
                    'start_fee' => (float)$data['start_fee'],
                    'distance_base' => (float)$data['distance_base'],
                    'fee_base' => (float)$data['fee_base']
                ];
                //---以下暂不使用
                // if(!is_array($data) || 
                //     !isset($data['distance_range']) || (float)$data['distance_range'] < 0 ||
                //     !isset($data['in_distance_base']) || (float)$data['in_distance_base'] < 0 ||
                //     !isset($data['in_fee_base']) || (float)$data['in_fee_base'] < 0 ||
                //     !isset($data['out_distance_base']) || (float)$data['out_distance_base'] < 0 ||
                //     !isset($data['out_fee_base']) || (float)$data['out_fee_base'] < 0
                // ) return false;
                // $optArr = [
                //     'distance_range' => (float)$data['distance_range'],
                //     'in_distance_base' => (float)$data['in_distance_base'],
                //     'in_fee_base' => (float)$data['in_fee_base'],
                //     'out_distance_base' => (float)$data['out_distance_base'],
                //     'out_fee_base' => (float)$data['out_fee_base']
                // ];
                //===================================
                $data = $optArr;
                unset($optArr);
                break;
            default:
                return false;
        }
        return true;
    }

    //2019-01-27 新增 配送附加费的缓存
    protected static $_addoneCfgCache = null;
    //2019-01-24 新增 获取运费附加费
    public function get_addone()
    {
        if(self::$_addoneCfgCache === null)
            self::$_addoneCfgCache = $this->get_addone_cfg();
        $cfg = &self::$_addoneCfgCache;
        if($cfg['is_open'])
            return $cfg['value'];
        return 0.00;

        // $key = "ShopFreightCalcConfig_Addone";
        // return (float)K::M('system/data')->get($key);
    }

    //2019-01-27 新增 因为不再直接使用数值，所以增加一个获取配置数据的接口
    public function get_addone_cfg()
    {
        $cfg = K::M('system/data')->get("ShopFreightCalcConfig_Addone");
        if(!is_array($cfg) || empty($cfg))
        {
            $cfg = [
                'is_open' => false,
                'value' => 0.00
            ];
        }
        else
        {
            $cfg['is_open'] = $cfg['is_open']?true: false;
            $cfg['value'] = (float)$cfg['value'];
        }
        return $cfg;
    }

    //2019-01-24 新增 设置运费附加费
    public function set_addone($data)
    {
        if(!is_array($data))
            return false;
        $cfg = $this->get_addone_cfg();
        if(isset($data['is_open']))
        {
            if(is_string($data['is_open']))
            {
                $data['is_open'] = trim($data['is_open']);
                if(strtolower($data['is_open']) === "true")
                    $cfg['is_open'] = true;
                else if(strtolower($data['is_open']) === "false")
                    $cfg['is_open'] = false;
                else
                    $cfg['is_open'] = (int)$data['is_open']?true:false;
            }
            else
                $cfg['is_open'] = (int)$data['is_open']?true:false;
            self::$_addoneCfgCache['is_open'] = $cfg['is_open'];
        }
        if(isset($data['value']))
        {
            $data['value'] = (float)$data['value'];
            if($data['value'] < 0)
                return false;
            $cfg['value'] = $data['value'];
            self::$_addoneCfgCache['value'] = $cfg['value'];
        }
        K::M('system/data')->set("ShopFreightCalcConfig_Addone",$cfg);
        return true;

        // $value = (float)$value;
        // K::M('system/data')->set($key,$value);
    }

    protected $_goodsAddoneKey = "ShopFreightCalcConfig_GoodsAddone";   //2019-01-26 新增 商品附加费的数据键名
    protected static $_goodsAddoneCfgCache = null;  //2019-01-26 新增 商品附加费配置数据缓存，主要用于在调用get_goods_addone()时无需每次从文件读取值，加快速度
    //2019-01-26 新增 获取商品附加费，得到的新是一个小数值，如果需要整个配置数据，请使用get_goods_addone_cfg()接口
    //                如果未打开，得到的数值也是0
    public function get_goods_addone()
    {
        if(self::$_goodsAddoneCfgCache === null)
            self::$_goodsAddoneCfgCache = $this->get_goods_addone_cfg();
        $cfg = &self::$_goodsAddoneCfgCache;
        if($cfg['is_open'])
            return $cfg['value'];
        return 0.00;
    }

    //2019-01-26 新增 获取商品附加费配置参数
    public function get_goods_addone_cfg()
    {
        $cfg = K::M('system/data')->get($this->_goodsAddoneKey);
        if(!$cfg)
        {
            $cfg = [
                'is_open' => false,
                'value' => 0.00
            ];
        }
        else
        {
            $cfg['is_open'] = $cfg['is_open']?true: false;
            $cfg['value'] = (float)$cfg['value'];
        }
        return $cfg;
    }

    //2019-01-26 新增 设置商品附加费
    //    注意：由于附加费还有开关（以后可能还增加其他）参数，不仅仅是数值，所以使用的数组进行保存的方式
    public function set_goods_addone($data)
    {
        if(!is_array($data))
            return false;
        $cfg = $this->get_goods_addone_cfg();
        if(isset($data['is_open']))
        {
            if(is_string($data['is_open']))
            {
                $data['is_open'] = trim($data['is_open']);
                if(strtolower($data['is_open']) === "true")
                    $cfg['is_open'] = true;
                else if(strtolower($data['is_open']) === "false")
                    $cfg['is_open'] = false;
                else
                    $cfg['is_open'] = (int)$data['is_open']?true:false;
            }
            else
                $cfg['is_open'] = (int)$data['is_open']?true:false;
            self::$_goodsAddoneCfgCache['is_open'] = $cfg['is_open'];
        }
        if(isset($data['value']))
        {
            $data['value'] = (float)$data['value'];
            if($data['value'] < 0)
                return false;
            $cfg['value'] = $data['value'];
            self::$_goodsAddoneCfgCache['value'] = $cfg['value'];
        }
        K::M('system/data')->set($this->_goodsAddoneKey,$cfg);
        return true;
    }

    //2019-02-22 新增 根据配送距离获取配送时间，单位秒
    //  参数：
    //      $distance 距离，单位 米
    public function get_freight_time($distance)
    {
        $cfg = [
            3000 => 1800,
            4000 => 2400,
            5999 => 3000,
            6000 => 3600
        ];
        $distance = (int)$distance;
        ksort($cfg,SORT_NUMERIC);
        $time = 0;
        foreach($cfg as $key => $value)
        {
            $time = $value;
            if($key >= $distance)
                break;
        }
        return (int)$time;
    }
}
