<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: data.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Data_Data extends Model 
{    
    
    public function ttl($k=null)
    {
        static $ttls = array('600'=>'10分钟','900'=>'15分钟','1800'=>'30分钟',
            '3600'=>'1小时', '21600'=>'6小时', '43200'=>'12小时','86400'=>'24小时','0'=>'永不过期','-1'=>'不缓存');
        if($k === null){
            return $ttls;
        }
        return $ttls[$k];
    }
	public function money_pack()
	{
		return array(
			'1' => '1',
			'500'  => '50',
			'2000' => '250',
			'5000' => '600',
		);
	
	}
    public function staff_from()
    {
        return array('xiche'=>'洗车', 'chongwu'=>'宠物', 'meirong'=>'美容', 'weixiu'=>'维修');
    }
    public function bank_list()
    {
        return array('支付宝', '工商银行', '建设银行', '农业银行', '招商银行', '广发银行', '交通银行');
    }
    public function chepai()
    {
        return array('京','津','渝','沪','冀','晋','辽','吉','黑','苏','浙','皖','闽','赣','鲁','豫','鄂','湘','粤','琼','川','黔','云','陕','甘','青','蒙','桂','宁','新','藏','港','澳','台');
    }
    public function checolor()
    {
        return array('黑色','白色','红色','蓝色','银灰色','深灰色','黄色','绿色','香槟色','巧克力色','橙色','粉色','紫色','其他');
    }
}