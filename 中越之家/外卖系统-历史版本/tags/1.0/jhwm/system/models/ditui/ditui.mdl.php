<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_Ditui_Ditui extends Mdl_Table
{   
  
    protected $_table = 'ditui';
    protected $_pk = 'ditui_id';
    protected $_cols = 'ditui_id,city_id,mobile,passwd,money,pmid,reg_count,total_money,order_count,name,id_number,id_photo,account_type,account_name,account_number,audit,clientip,dateline,closed';
    protected $_pre_cache_key = 'ditui-list';

    public function update_money($ditui_id, $money, $tm=true)
    {
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }else if(!$money = (float)$money){
            $this->msgbox->add('变更的余额值非法',411);
        }else{
            if($money > 0 && $tm){
                $sql = "UPDATE ".$this->table($this->_table)." SET `money`=`money`+{$money},`total_money`=`total_money`+{$money} WHERE ditui_id='$ditui_id'";
            }else{
                $sql = "UPDATE ".$this->table($this->_table)." SET `money`=`money`+{$money} WHERE ditui_id='$ditui_id'";
            }
            if($this->db->Execute($sql)){
                return true;
            }
        }
        
    }

    public function ditui($u, $l='ditui_id')
    {
        $l = strtolower($l);
        switch ($l) {
            case 'ditui_id':
                $field = 'ditui_id';
                break;
            case 'mobile':
                $field = 'mobile';
                break;
            default:
                return false;
        }
        $sql = "SELECT * FROM " . $this->table($this->_table) . " WHERE " . $this->field($field, $u);
        if ($row = $this->db->GetRow($sql)) {
            $row = $this->_format_row($row);
        }
        return $row;
    }

    protected function _format_row($row)
    {
        /*if($city = K::M('data/city')->city($row['city_id'])){
            $row['city_name'] = $city['city_name'];
        }*/
        if(empty($row['face'])){
            $row['face'] = 'face/default.png';
        }
        $row['pid'] = sprintf("D%05d", $row['ditui_id']);
        return $row;
    }

    public function update_regcount($ditui_id)
    {
        $sql = "UPDATE ".$this->table($this->_table)." SET `reg_count`=`reg_count`+1 WHERE ditui_id='$ditui_id'";
        if($this->db->Execute($sql)){
            return true;
        }
    }

    public function update_ordercount($ditui_id)
    {
        $sql = "UPDATE ".$this->table($this->_table)." SET `order_count`=`order_count`+1 WHERE ditui_id='$ditui_id'";
        if($this->db->Execute($sql)){
            return true;
        }
    }

    public function check_mobile($mobile)
    {
        if(!K::M('verify/check')->mobile($mobile)){
            $this->msgbox->add('手机号码格式不正确', 511);
            return false;
        }else if($member = $this->ditui($mobile,'mobile')){
            $this->msgbox->add('此手机号已被占用', 512);
            return false;
        }
        return $mobile;
    }

    //passwd 为明文的密码,非MD5后的。
    public function update_passwd($uid, $passwd)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$passwd = $this->check_passwd($passwd)){
            return false;
        }else if(!$member = $this->detail($uid)){
            return false;
        }
        return $this->update($uid, array('passwd'=>md5($passwd)), true);
    }

    public function check_passwd($passwd)
    {
        if(!preg_match('/^[\x21-\x7E]{6,32}$/', $passwd)){
            $this->msgbox->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~32字符', 401);
            return false;
        }
        return $passwd;
    }

    public function myhaibao($ditui_id)
    {
        $attach = K::$system->config->get('attach');
        $config = K::$system->config->get('ditui');
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }elseif(!$ditui = $this->detail($ditui_id)){
            return false;
        }else{
            $pmid = $ditui['pid'];
            $photo = 'ditui/'.substr($pmid, -3).'/'.$pmid.'.png';
            $file = $attach['attachdir'].$photo;
            $haibao_photo = $attach['attachdir'].$config['haibao_photo'];
            $qr_top = (int)$config['haibao_qrtop'];
            $qr_left = (int)$config['haibao_qrleft'];
            $qr_width = (int)$config['haibao_qrwidth'];
            if(!file_exists($haibao_photo)){
                return false;
            }elseif($qr_width < 50 || $qr_left < 0 || $qr_top < 0){
                //二维码大小不能小于50;
                return false;
            }else{
                if(!$invite_url = K::M('helper/link')->mklink('invite:invite', array($ditui_id), array(), 'ditui')){
                    return false;
                }
                K::M('io/dir')->create(dirname($file));
                Import::L('qrcode/phpqrcode.php');
                if(!$im = imagecreatefrompng($haibao_photo)){
                    return false;
                }
                QRcode::png($invite_url,$file.'_qr.png', QR_ECLEVEL_M, 10, 1);
                if(!$qr_im = imagecreatefrompng($file.'_qr.png')){
                    return false;
                }
                imagecopyresized($im, $qr_im, $qr_left, $qr_top, 0, 0, $qr_width, $qr_width, 350, 350);
                ob_clean();
                header("Content-type: image/png");
                imagepng($im);
                imagedestroy($im);
                imagedestroy($qr_im);
                exit();
            }
        }
    }

    public function gethaibao($ditui_id, $force=false)
    {
        $attach = K::$system->config->get('attach');
        //$attach['attachdir']
        $config = K::$system->config->get('ditui');
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }elseif(!$ditui = $this->detail($ditui_id)){
            return false;
        }else{
            $pmid = $ditui['pid'];
            $photo = 'ditui/'.substr($pmid, -3).'/'.$pmid.'.png';
            $file = $attach['attachdir'].$photo;
            $haibao_photo = $attach['attachdir'].$config['haibao_photo'];
            $qr_top = (int)$config['haibao_qrtop'];
            $qr_left = (int)$config['haibao_qrleft'];
            $qr_width = (int)$config['haibao_qrwidth'];
            if(!$force && file_exists($file)){
                return $attach['attachurl'].'/'.$photo;
            }elseif(!file_exists($haibao_photo)){
                echo $haibao_photo;
                return false;
            }elseif($qr_width < 50 || $qr_left < 0 || $qr_top < 0){
                //二维码大小不能小于50;
                return false;
            }else{
                if(!$invite_url = K::M('helper/link')->mklink('invite:invite', array($ditui_id), array(), 'ditui')){
                    return false;
                }
                K::M('io/dir')->create(dirname($file));
                Import::L('qrcode/phpqrcode.php');
                if(!$im = imagecreatefrompng($haibao_photo)){
                    return false;
                }
                QRcode::png($invite_url,$file.'_qr.png', QR_ECLEVEL_M, 10, 1);
                if(!$qr_im = imagecreatefrompng($file.'_qr.png')){
                    return false;
                }
                imagecopyresized($im, $qr_im, $qr_left, $qr_top, 0, 0, $qr_width, $qr_width, 350, 350);
                imagepng($im, $file);
                imagedestroy($im);
                imagedestroy($qr_im);
                return $attach['attachurl'].'/'.$photo;
            }
        }
    }

    /*public function fetch_all_rank($filter=array(), $orderby=null)
    {
        if($this->_pre_cache_key === null){
            trigger_error('Table '.$this->_table.' has not cache_key defined');
        }else if(isset(self::$_CACHE_TABLES[$this->_pre_cache_key])){
            return self::$_CACHE_TABLES[$this->_pre_cache_key];
        }else if(($items = $this->cache->get($this->_pre_cache_key)) === false){
            $order = $this->order($orderby);
            $where = $this->where($filter);
            $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE $where ".$order;
            if($rs = $this->db->Execute($sql)){
                $k = 0;
                while($row = $rs->fetch()){
                    $row = $this->_format_row($row);
                    $k += 1;
                    $items[$k] = $row;
                }
            }
            self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
            $this->cache->set($this->_pre_cache_key, $items, 60);//1分钟缓存
        }
        return $items;
    }*/

    public function fetch_all_rank($filter=array(), $orderby=null)
    {
        $where = $this->where($filter);
        $orderby = $this->order($orderby);
        $sql = "SELECT *, @count:=@count+1 AS rank FROM ". $this->table($this->_table).", (SELECT @count:=0) c WHERE $where $orderby";
        if($rs = $this->db->query($sql)){
            $k = 0;
            while($row = $rs->fetch()){
                $row = $this->_format_row($row);
                $k += 1;
                $items[$k] = $row;
            }
        }
        return $items;

    }

    public function get_rank_num($ditui_id=null,$filter=array(), $orderby=null)
    {
        if(!$ditui_id = (int)$ditui_id){
            return false;
        }else{
            $rank_num = 0;
            if($ranks = $this->fetch_all_rank($filter, $orderby)){
                foreach ($ranks as $k => $v) {
                    if($ditui_id == $v['ditui_id']){
                        $rank_num = $k;
                        //$rank_num = $v['rank']; 
                        break;
                    }
                }
            }
            return $rank_num;
        }
    }

}
