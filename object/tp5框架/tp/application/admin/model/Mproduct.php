<?php
namespace app\admin\model;
use think\Model;
class Mproduct extends Model{
	protected $table = 'tp_product';
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	protected $updateTime = 'utime';
}
?>