<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/26
 * Time: 15:59
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Waimai_complaintPhoto extends Mdl_Table{
    protected $_table = 'waimai_complaint_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,complaint_id,photo,dateline';

}