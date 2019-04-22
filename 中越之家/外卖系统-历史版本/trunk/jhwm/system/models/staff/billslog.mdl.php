<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Staff_Billslog extends Mdl_Table
{
    protected $_table = 'staff_bills_log';
    protected $_pk = 'log_id';
    protected $_cols = 'log_id,bills_id,bills_sn,staff_id,order_id,freight_amount,tixian_percent,amount,diff_amount,dateline';

    protected function _format_row($row)
    {
        $row['bills_sn'] = date('Y-m-d', $row['bills_sn']);
        $row['tixian_percent'] = $row['tixian_percent']."%";
        return $row;
    }

}
