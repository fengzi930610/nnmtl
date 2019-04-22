<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: config.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
class Mdl_System_Config extends Mdl_Table
{
    protected $_table = 'system_config';
    protected $_pk = 'k';
    protected $_cols = 'k,v,dateline';
    protected $_pre_cache_key = 'system_config_';
    public static $_CFG = array();
    public function __construct($system)
    {
        parent::__construct($system);
        K::$system->_CFG = &self::$_CFG;
    }
    public function add($k, $title='')
    {
        if($this->get($k)){
            $this->msgbox->add('标识已经在，不能重复', 451);
            return false;
        }
        return $this->db->insert($this->_table, array('k'=>$k, 'title'=>$title, 'dateline'=>__CFG::TIME), false,true);
    }
    public function get($k)
    {
         if(!isset(self::$_CFG[$k])){
            if($data = $this->cache->get($this->_pre_cache_key.$k)){
                self::$_CFG[$k] = $data;
            }else{
                $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE k='$k'";
                if($row = $this->db->GetRow($sql)){
                    self::$_CFG[$k] = unserialize(stripslashes($row['v']));
                        if('attach' == $k){
                            self::$_CFG[$k] = $this->attach(self::$_CFG[$k]);
                        }else if('hongbao' == $k){//v3.6
                            self::$_CFG[$k] = $this->hongbao(self::$_CFG[$k]);
                        }else if('tjhongbao' == $k){//v3.6
                            self::$_CFG[$k] = $this->tjhongbao(self::$_CFG[$k]);
                        }else if('invite' == $k){//v3.6
                            self::$_CFG[$k] = $this->invite(self::$_CFG[$k]);
                        }else if('moneypack' == $k){//v3.6
                            self::$_CFG[$k] = $this->moneypack(self::$_CFG[$k]);
                        }else if('ditui' == $k){//v3.6
                            self::$_CFG[$k] = $this->ditui(self::$_CFG[$k]);
                        }
                }else{
                  self::$_CFG[$k] = null;
                }
                $this->cache->set($this->_pre_cache_key.$row['k'], self::$_CFG[$row['k']]);
            }
            if('site' == $k){
                self::$_CFG[$k] = $this->site(self::$_CFG[$k]);
            }
        }
        return self::$_CFG[$k];
    }
    public function set($k, $v,$flag=true)
    {   
        if($flag){
            if(!$v = $this->_check($v, $k)){
                return false;
            }
        }
        $v = K::M('content/filter')->stripslashes($v, true);
        $data = addslashes(serialize($v));
        if($this->db->update($this->_table, array('v'=>$data, 'dateline'=>__CFG::TIME), "k='$k'")){
           self::$_CFG[$k] = $v;
           $this->cache->delete($this->_pre_cache_key.$k);
           return true;
        }
        return false;
    }
    public function load($keys=null)
    {
        if(is_string($keys)){
            $keys = explode(',', $keys);
        }else if(!is_array($keys)){
            return self::$_CFG;
        }
        $ks = array();
        foreach($keys as $k){
            if(!isset(self::$_CFG[$k])){
                if($data = $this->cache->get($this->_pre_cache_key.$k)){
                    self::$_CFG[$k] = $data;
                    if('site' == $k){
                        self::$_CFG[$k] = $this->site(self::$_CFG[$k]);
                    }
                }else{
                    $ks[] = $k;
                }
            }
        }
        if(!empty($ks)){
            $ks = "'".implode("','", $ks)."'";
            $sql = "SELECT * FROM ".$this->table($this->_table)." WHERE k IN($ks)";
            if($rs = $this->db->Execute($sql)){
                while($row = $rs->fetch()){
                    self::$_CFG[$row['k']] = unserialize(stripslashes($row['v']));
                    if('attach' == $row['k']){
                        self::$_CFG[$row['k']] = $this->attach(self::$_CFG[$row['k']]);
                    }else if('site'==$row['k']){
                        self::$_CFG[$row['k']] = $this->site(self::$_CFG[$row['k']]);
                    }
                    $this->cache->set($this->_pre_cache_key.$row['k'], self::$_CFG[$row['k']]);
                }
            }
        }
        return self::$_CFG;
    }

    protected function attach($cfg)
    {
        $cfg['attachdir'] = dirname(__CORE_DIR).DIRECTORY_SEPARATOR.'attachs'.DIRECTORY_SEPARATOR;
        if(!preg_match("/(http|https).+/i", $cfg['url'])){
            $site = $this->get('site');
            $cfg['attachurl'] = trim($site['siteurl'], '/').'/'.trim($cfg['url'], '/');
        }else{
            $cfg['attachurl'] = $cfg['url'];
        }
        return $cfg;
    }

    protected function site($cfg)
    {
        if(!defined('UC_OPEN')){
            define('UC_OPEN', ($cfg['ucenter'] ? true : false));
        }
        $cfg['city_domain'] = $cfg['domain'];
        return $cfg;
    }

    protected function _check($data, $pk=null)
    {
        $file = __CFG::DIR.'schemas'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR."{$pk}.php";
        if(file_exists($file)){
            if($schemas = include($file)){
                $check = K::M('verify/check');
                foreach((array)$schemas as $k=>$v){
                    if(!$v['empty']){
                        if(!isset($data[$k]) || $data['k'] === ''){
                            $this->msgbox->add($v['label'].'不能为空', 451);
                            return false;
                        }
                    }
                    if(isset($data[$k])){
                        switch(strtolower($v['type'])){
                            case 'number':
                                $data[$k] = (int) $data[$k]; break;
                            case 'boolean':
                                $data[$k] = $data[$k] ? 1 : 0; break;
                            case 'mail':
                                if(!$check->mail($data[$k])){
                                    $this->msgbox->add($v['label'].'必须为Email格式', 452);
                                    return false;
                                }
                                break;
                            case 'phone': case 'mobile';
                                if(!$check->phone($data[$k]) && !$check->mobile($data[$k])){
                                    $this->msgbox->add($v['label'].'必须为电话/手机号格式',453);
                                    return false;
                                }
                                break;
                            case 'text': case 'textarea': case 'editor';
                                if(!$v['html'] && is_string($data[$k])){
                                    $data[$k] = K::M('content/html')->encode($data[$k]);
                                }
                                break;
                        }
                    }
                }
            }
        }
        return $data;
    }
    public function ucenter()
    {
        if(!defined('UC_API')){
            if(file_exists(__CFG::DIR.'uc_config.php')){
                include_once(__CFG::DIR.'uc_config.php');
            }
        }
    }

    //v3.6
    protected function hongbao($cfg)
    {
        foreach($cfg['hongbao'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $cfg['hongbao'][$k] = $v;
        }
        return $cfg;
    }

    protected function invite($cfg)
    {        
        foreach($cfg['inviter_hongbao_cfg'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $cfg['inviter_hongbao_cfg'][$k] = $v;
        }
        foreach($cfg['invitee_hongbao_cfg'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $cfg['invitee_hongbao_cfg'][$k] = $v;
        }
        return $cfg;
    }

    protected function ditui($cfg)
    {
        
        foreach($cfg['hongbao'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $cfg['hongbao'][$k] = $v;
        }
        return $cfg;
    }

    protected function moneypack($cfg)
    {
        foreach ($cfg as $k => $v) {
            foreach ($v['hongbao'] as $kk => $vv) {
                $vv['stime_time'] = K::M('helper/format')->format_morrowTime($vv['stime']);
                $vv['ltime_time'] = K::M('helper/format')->format_morrowTime($vv['ltime']);
                $v['hongbao'][$kk] = $vv;
            }
            $cfg[$k] = $v;
        }
        return $cfg;
    }

    protected function tjhongbao($cfg)
    {
        foreach($cfg['hongbao'] as $k=>$v){
            $v['stime_time'] = K::M('helper/format')->format_morrowTime($v['stime']);
            $v['ltime_time'] = K::M('helper/format')->format_morrowTime($v['ltime']);
            $cfg['hongbao'][$k] = $v;
        }
        return $cfg;
    }

}
