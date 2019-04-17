<?php
namespace app\admin\model;
use think\Model;
class ProUpdate extends Model{
	protected $table = 'tp_product';
	protected $autoWriteTimestamp = true;
//	protected $createTime = 'ctime';
	protected $updateTime = 'utime';
}
?>