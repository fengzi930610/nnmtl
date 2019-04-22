<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: cache.ctl.php 3170 2014-01-22 09:29:16Z youyi $
 */
class Ctl_Tools_Cache extends Ctl
{
    
    public function index()
    {
        
    }
    public function clean()
    {
        if($this->checksubmit()){
            if($this->GP('cache_data')){
                $this->cache->flush();
            }
            $output = K::M('system/frontend');
            if($this->GP('cache_tplcache')){
                $output->clearCompiledTemplate();
            }
            $output->setCompileDir(__CFG::DIR.'data/tpladmin');
            if($this->GP('cache_tpladmin')){
                $output->clearCompiledTemplate();
            }
            $this->msgbox->add('清空数据缓存成功');
            //$this->msgbox->set_data('forward', '?index-welcome.html');            
        }else{
            $this->tmpl = 'admin:tools/cache/index.html';
        }
    }
}