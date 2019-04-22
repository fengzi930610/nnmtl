<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: setting.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Mdl_System_Setting extends Mdl_Table
{
    protected $_table = 'system_setting';
    protected $_pk = 'k';
    protected $_cols = 'k,v,dateline';
    protected static $_CFG = null;
    public function __construct(&$system)
    {
        parent::__construct($system);
        if((self::$_CFG === null) && (self::$_CFG = $this->cache->get('system/setting')) === false){
            self::$_CFG = array();
            if($rs = $this->db->Execute('SELECT * FROM '.$this->table($this->_table))){
                while($row = $rs->fetch()){
                    self::$_CFG[$k] = unserialize($v);
                }
            }
            $this->cache->set('system/setting', self::$_CFG);
        }
    }
    
    public function get($k)
    {
        $k = str_replace('/','.', $k);
        $kk = explode('.', $k);
        if(isset($kk[3])){
            return self::$_CFG[$kk[0]][$kk[1]][$kk[2]][$kk[3]];
        }else if(isset($kk[2])){
            return self::$_CFG[$kk[0]][$kk[1]][$kk[2]];
        }else if(isset($kk[1])){
            return self::$_CFG[$kk[0]];
        }
        return self::$_CFG[$k];
    }
    public function set($k, $v)
    {   
        $time = __CFG::TIME;
        $v = addslashes(serialize($v));
        if($this->db->Execute("REPLACE INTO ".$this->table($this->_table)."(k,v,dateline) VALUES('$k','$v','$time')")){
            if($this->db->affected_rows){
                $this->flush();
            }
            return true;
        }
        return false;         
    }
    public function flush()
    {
        return $this->cache->delete('system/setting');
    }
}