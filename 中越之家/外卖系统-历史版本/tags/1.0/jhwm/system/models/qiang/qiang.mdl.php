<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Qiang_Qiang extends Mdl_Table
{
    protected $_table = 'qiang';
    protected $_pk = 'qiang_id';
    protected $_cols = 'qiang_id,city_id,area_id,business_id,photo,shop_id,title,price,discount_price,type,freight,sales,is_onsale,qiang_ltime,use_ltime,info,rules,notes,orderby,closed,clientip,dateline,sku,count_sku,is_tui,pei_type,is_yuyue,is_limit,limit_sku,bl';

    //连表处理 过滤外卖删除商家
    public function items($filter=array(),$order=array(),$page=1,$limit=50,&$count=0){
        $where = $this->where($filter,'q.');
        $order = $this->order($order,'DESC','q.');
        $limit = $this->limit($page,$limit);
        $items = array();
        $count_sql = "SELECT COUNT(1) as count FROM ".$this->table($this->_table)." q INNER JOIN ".$this->table('waimai')." w ON q.shop_id = w.shop_id WHERE {$where}";
        if($res_count = $this->db->Execute($count_sql)){
            if($count_row  = $res_count->fetch()){
                $count = $count_row['count'];
            }
            $sql = "SELECT q.* FROM ".$this->table($this->_table)." q INNER JOIN ".$this->table('waimai')." w ON q.shop_id = w.shop_id AND w.closed=0 WHERE {$where} {$order} {$limit}";
            if($res = $this->db->Execute($sql)){
                while($row = $res->fetch()){
                    $items[] = $this->_format_row($row);
                }
            }
        }
        return $items;
    }

    public function getType()
    {
        return array(
            'goods'=>'实物',
            'ticket'=>'电子券',
        );
    }

    public function get_notes_label($limit_sku='指定')
    {
        return array(
            'is_tui' => array(
                        '0'=>array('title'=>'不支持退款','note'=>'不支持退款申请','photo'=>'not_tui'),
                        '1'=>array('title'=>'支持退款','note'=>'支持退款申请','photo'=>'tui')
                        ),
            'pei_type' => array(
                        '0'=>array('title'=>'物流配送','note'=>'商品由商家发货配送','photo'=>'wuliu'),
                        '1'=>array('title'=>'到店消费','note'=>'到商户门店进行消费','photo'=>'daodian'),
                        '2'=>array(
                            '0'=>array('title'=>'物流配送','note'=>'商品由商家发货配送','photo'=>'wuliu'),
                            '1'=>array('title'=>'到店消费','note'=>'到商户门店进行消费','photo'=>'daodian'),
                        ),
                        ),
            'is_yuyue' => array(
                        '0'=>array('title'=>'免预约','note'=>'无需预约，直接到店进行消费','photo'=>'mian'),
                        '1'=>array('title'=>'提前预约','note'=>'消费前，请提前联系商家预约','photo'=>'ti')
                        ),
            'is_limit' => array(
                        '0'=>array('title'=>'不限购','note'=>'每笔订单不限制购买份数','photo'=>'not_xian'),
                        '1'=>array('title'=>'限购'.$limit_sku.'份','note'=>'每笔订单限制购买'.$limit_sku.'份','photo'=>'xian')
                        ),
        );
    }

    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        $data['dateline'] = $data['dateline'] ? $data['dateline'] : __TIME;
        $data['clientip'] = $data['clientip'] ? $data['clientip'] : __IP;
        if($qiang_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }        
        return $qiang_id;        
    }

    protected function _format_row($row)
    {
        $ziti_status = 0;//默认不支持到店消费
        if((int)$row['pei_type'] == 0){  //物流配送
            $ziti_status = 0;
        }else if((int)$row['pei_type'] == 1){ //自提 
            $ziti_status = 1;
        }else if((int)$row['pei_type'] == 2){
            if($row['use_ltime'] < __TIME){  //全部支持的情况下 到店消费已过期 只支持物流
                $ziti_status = 0;
            }else{
                $ziti_status = 2;
            }
        }
        $row['ziti_status'] = $ziti_status; 

        $row['sale_bili'] = $row['discount'] = 0;
        if((int)$row['count_sku'] > 0 && (int)$row['sales'] > 0){
            $row['sale_bili'] = number_format((int)$row['sales']/(int)$row['count_sku'],2,'.','');
        }

        if((float)$row['price'] > 0 && (float)$row['discount_price'] > 0){
            $row['discount'] = number_format((float)$row['discount_price']/(float)$row['price'],2,'.','');
        }

        if($type = $this->getType()){
            $row['product_type_name'] = $type[$row['type']];
        }

        $row['notes_label'] = array();
        if($notes_label = $this->get_notes_label($row['limit_sku'])){
            foreach ($row as $k => $v) {
                if($notes_label[$k]){
                    if($k == 'pei_type' && $v == 2){
                        $row['notes_label'][] = $notes_label[$k][$v][0];
                        $row['notes_label'][] = $notes_label[$k][$v][1];
                    }else{
                        $row['notes_label'][] = $notes_label[$k][$v];
                    }
                }
            }
        }

        if($row['is_onsale'] == 0){
            $row['onsale_status'] = '<b class="blue">上架</b>';
        }else{
            $row['onsale_status'] = '<b class="red">下架</b>';
        }

        $row['use_ltime_label'] = '----';
        if($row['use_ltime']){
            $row['use_ltime_label'] = date('Y-m-d',$row['use_ltime']);
        }
        $row['photo'] = $row['photo'] ? $row['photo'] : 'default/product.png';
        return $row;
    }

}
