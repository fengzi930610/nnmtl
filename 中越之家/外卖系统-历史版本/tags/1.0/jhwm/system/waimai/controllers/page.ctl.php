<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Page extends Ctl
{
    public $_call = 'index';

    public function index($page)
    {
        if($page == 'getapp'){
            $this->getapp();
        }elseif(!$detail = K::M('article/article')->detail_by_page($page)){
            $this->error(404);
        }elseif($detail['linkurl']){
            header("Location:{$detail['linkurl']}");
            exit;     
        }else{
            $this->pagedata['detail'] = $detail;
            $this->tmpl = 'page/page.html';
        }
    }

    public function getapp()
    {
        if(IN_APP_CLIENT){
            header("Location:".$this->mklink('index'));
        }else{
            $cfg = $this->system->config->get('app_download');
            $pager['apk_client_scheme'] = str_replace('.', '', $cfg['apk_client_packname']);
            $pager['ios_client_scheme'] = str_replace('.', '', $cfg['ios_client_packname']);
            $pager['apk_client_download'] = $cfg['apk_client_download'];
            $pager['ios_client_download'] = $cfg['ios_client_download'];
            $pager['yyb_client_url'] = $cfg['yyb_client_url'];
            $this->pagedata['pager'] = $pager;
            $this->tmpl = 'page/getapp.html';
        }
    }
}