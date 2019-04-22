<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: adv.mdl.php 14903 2015-08-12 10:17:27Z xiaorui $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Upload_Cate extends Mdl_Table
{       
    protected $_table = 'upload_cate';
    protected $_pk = 'cate_id';
    protected $_cols = 'cate_id,title,orderby,dateline';
    protected $_pre_cache_key = 'upload-cate-list';

    public function items_group_by_cateid($filter=null, $orderby, $page=1, $limit=50, &$count=0)
    {
        $where = $this->where($filter);
        $orderby = $this->order($orderby);
        $limit = $this->limit($page, $limit);  
        $items = array();
        if($count = $this->count($where)){
            $sql = "SELECT COUNT(1) as count  FROM ".$this->table($this->_table)." as o LEFT JOIN ".$this->table('upload_photo')." as ext ON WHERE {$where} GROUP BY `shop_id` {$orderby} $limit";    
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    if($row[$this->_pk]){
                        $items[$row[$this->_pk]] = $row;
                    }else{
                        $items[] = $row;
                    }
                }
            }
        }
        return $items;
    }

    public function getcounts($filter=array())
    {
    	$where = K::M('magic/upload')->where($filter);
    	$sql = "SELECT `cate_id`, COUNT(1) as count  FROM ".$this->table('upload_photo')." WHERE {$where} GROUP BY `cate_id`";
    	$items = array();
        if($res = $this->db->Execute($sql)){
            while($row = $res->fetch()){
                $items[$row['cate_id']] = $row;
            }
        }
        return $items;    
    }

    public function setcate($filter=array(), $cate_id=0)
    {
        $where = K::M('magic/upload')->where($filter);
        return $this->db->update('upload_photo', array('cate_id'=>(int)$cate_id), $where);
    }
}