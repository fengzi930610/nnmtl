<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: session.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_System_Session extends Model 
{
    public $SSID = null;
    public $_SESSION = array();
    protected $_ttl = 1800;
    protected $activated = false;
    protected $_table = 'session';
    public function __construct(&$system)
    {
        parent::__construct($system);
        $this->_ttl = __CFG::S_EXPIRE;      
        $site = K::$system->config->get('site');
        if(!__CFG::C_DOMAIN && $site['multi_city'] && $site['city_domain']){
            $domain = '.'.trim($site['city_domain'], '.');
        }else{
            $domain = __CFG::C_DOMAIN;
        }
        @ini_set('session.cookie_domain', $domain);
        @ini_set('session.cookie_lifetime', __TIME+__CFG::C_EXPIRE);
    }
    public function start()
    {
        if(!$this->activated){
            session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
            session_start();
            $this->SSID = session_id();
            $this->_SESSION = &$_SESSION;
            if(!defined('PHPSESSID')){
                define('PHPSESSID', $this->SSID);
            }
            // $this->_SESSION['SSID'] = &$this->SSID;
        }
        return $this;
    }   
    public function open()
    {
        return true;
    }
    public function close()
    {
        return $this->gc($this->_ttl);
    }
    public function read($ssid)
    {
        $sql = "SELECT * FROM ".$this->db->table($this->_table)." WHERE SSID='$ssid'";
        $row = $this->db->GetRow($sql);
        return $row ? $row['data'] : '';
    }
    public function write($ssid, $data)
    {
        
        $uid = $city = 0;
        if(!defined('IN_ADMIN')){
            $uid = (int)K::$system->uid;
            $city = (int)K::$system->request['city_id'];
        }
        $ip = __IP;
        if(strlen($data) > 1024) $data = '';
        $sessiondata = array(
                            'ssid'=>$ssid,
                            'uid'=>$uid,
                            'city_id'=>$city,
                            'ip'=>$ip,                          
                            'data'=>$data,
                            'lastupdate'=>__CFG::TIME
                        );
        return $this->db->insert($this->_table, $sessiondata, false, true);
    }
    public function destroy($ssid)
    {
        $sql = "DELETE FROM ".$this->db->table($this->_table)." WHERE `ssid`='$ssid'";
        return $this->db->query($sql);
    }
    public function gc($ttl)
    {
        $expiretime = __CFG::TIME - $this->_ttl;
        $sql = "DELETE FROM ".$this->db->table($this->_table)." WHERE `lastupdate`<$expiretime";
        return $this->db->query($sql);
    }
    public function set($key,$val=null)
    {  
        if(is_array($key) && $val === null){
            $this->_SESSION = array_merge($this->_SESSION,$key);
        }else{
            $this->_SESSION[$key] = $val;
        }
    }
    public function get($key)
    {
        return $this->_SESSION[$key];
    }
    public function delete($k)
    {
        unset($this->_SESSION[$k]);
    }
    public function clean()
    {
        $this->_SESSION = array();
        $this->isclean = true;
    }
    public function fetch_all()
    {
        return $this->_SESSION;
    }
}