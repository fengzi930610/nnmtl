<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Jpush_Log extends Mdl_Table
{   
    protected $_table = 'jpush_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,from,platform,tag,device_id,alias,register_id,content,status,clientip,dateline,push_time,link_title,link_url';
    protected function _format_row($row)
    {
    	static $_from_list = array('member'=>'客户端', 'staff'=>'服务端', 'shop'=>'商户端','waimai'=>'外卖商户端','mall'=>'商城商户端');
    	$row['from_title'] = $_from_list[$row['from']];
    	return $row;
    }
}