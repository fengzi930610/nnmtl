<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: theme.ctl.php 10831 2015-06-13 10:07:02Z maoge $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_System_Theme extends Ctl
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $pager['page'] = max(intval($page), 1);
        $pager['limit'] = $limit = 50;
        if($items = K::M('system/theme')->items($filter, null, $page, $limit, $count)){
            $pager['count'] = $count;
            $themes = array();
            foreach($items as $k=>$v){
                $themes[$v['theme']] = $v;
            }
        }
        if($items = K::M('system/theme')->load_themes()){
            foreach($items as $k=>$v){
                if(empty($themes[$k])){
                    $themes[$k] = $v;
                }else{
                    $themes[$k] = array_merge($v, $themes[$k]);
                }
            }
        }
        $this->pagedata['themes'] = $themes;
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'admin:system/theme/items.html';
    }
    public function install($ident)
    {
        if(K::M('system/theme')->theme($ident)){
            $this->msgbox->add('模板已经安装过了', 211);
        }else if(K::M('system/theme')->install($ident)){
            $this->msgbox->add('安装模板成功');
        }
    }
    public function uninstall($theme_id)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未定义操作', 211);
        }else if(!$theme = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或未安装', 222);
        }else if($theme['theme'] == 'default'){
            $this->msgbox->add('系统模板不能删除', 223);
        }else if($theme['default']){
            $this->msgbox->add('不能删除当前使用的模板', 224);
        }else if(K::M('system/theme')->delete($theme_id)){
            $this->msgbox->add('卸载模板成功');
        }
    }
    public function config($theme_id=null)
    {
        $this->msgbox->add('功能开发中，敬请期待');
        $this->msgbox->response();
        exit;
    }
    public function setdefault($theme_id)
    {
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未定义操作', 211);
        }else if(!$theme = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或未安装', 222);
        }else if(K::M('system/theme')->set_default($theme_id)){
            $this->msgbox->add('设置默认模板成功');
        }
    }
    public function detail($theme_id, $path='')
    {
        $this->config();
        exit;
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未指定要管理的模板', 211);
        }else if(!$detail = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或已经删除', 212);
        }else{
            $tmp = K::M('system/themebak')->dir_decode($path);
            $tmpldir = $detail['theme'].'/'.$tmp;
            $tmpls = K::M('system/theme')->load_tmpls($tmpldir);
            $this->pagedata['tmpls'] = $tmpls;
            $this->pagedata['theme_id'] = $theme_id;
            $this->pagedata['detail'] = $detail;
            $this->pagedata['tmpldir'] = $tmpldir;
            $this->pagedata['theme'] = $detail['theme'].'/';
            $this->pagedata['nowtmpl'] = $detail['theme'].':'.$tmp;
            $this->pagedata['uptheme'] = K::M('system/themebak')->dir_encode(substr(K::M('system/themebak')->dir_decode($path),0,strrpos(K::M('system/themebak')->dir_decode($path),'/')));;
            $this->tmpl = 'admin:system/theme/detail.html';
        }
    }
    public function tmpledit($theme_id, $tmpl)
    {
        $this->config();
        exit;
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未指定要管理的模板', 211);
        }else if(!$detail = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或已经删除', 212);
        }else{
            $tmp = K::M('system/themebak')->dir_decode($tmpl);
            $tmpfile = $detail['theme'].'/'.$tmp;
            $content = stripslashes(file_get_contents(__TPL_DIR.$tmpfile));
            $this->pagedata['theme'] = $detail['theme'];
            $this->pagedata['theme_id'] = $theme_id;
            $a = array('content'=>$content);
            $this->pagedata['content'] = json_encode($a);
            $this->pagedata['detail'] = $detail;
            $this->pagedata['nowtmpl'] = $detail['theme'].':'.$tmp;
            $this->pagedata['uptheme'] = K::M('system/themebak')->dir_encode(substr($tmp,0,strrpos($tmp,'/')));            
        }
        $this->tmpl = 'admin:system/theme/tmpledit.html';
    }
    public function tmplsave()
    {
        $this->config();
        exit;       
        $theme_id = $this->GP('theme_id');
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未指定要管理的模板', 211);
        }else if(!$detail = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或已经删除', 212);
        }else{
            $tmpl = $this->GP('tmpl');
            $content = stripslashes($this->GP('content'));
            if(K::M('system/theme')->tmplsave($this->admin->admin_id.':'.$this->admin->admin_name, $content, $tmpl)){
                $this->msgbox->add('保存修改的模板成功');
                $url = 'index.php?system/theme-detail-'.$theme_id;
                $tmp = K::M('system/themebak')->dir_encode($tmpl);
                $this->msgbox->set_data('forward', $url.$tmp);
            }            
        } 
        
    }
    public function tmplbak($theme_id, $tmpl)
    {
        $this->config();
        exit;
        if(!$theme_id = (int)$theme_id){
            $this->msgbox->add('未指定要管理的模板', 211);
        }else if(!$detail = K::M('system/theme')->detail($theme_id)){
            $this->msgbox->add('模板不存在或已经删除', 212);
        }else{
            $tmplast = $detail['theme'].':'.K::M('system/themebak')->dir_decode($tmpl);
            $this->pagedata['tmpl_bak'] = K::M('system/theme')->bak_tmpls($tmplast);
        }
        $this->tmpl = 'admin:system/theme/tmplbak.html';
    }
    public function tmplrestore($bak_id)
    {
        $this->config();
        exit;
        if(!$bak_id = (int)$bak_id){
            $this->msgbox->add('未指定所需的备份', 211);
        }else if(!$detail = K::M('system/themebak')->detail($bak_id)){
            $this->msgbox->add('备份不存在或已经删除', 212);
        }else{
            $tmpl_bak = K::M('system/theme')->restore_bak($bak_id);
            $this->msgbox->add('还原成功');
        }
    }
    public function tmpldelbak($bak_id)
    {
        $this->config();
        exit;
        if(!$bak_id = (int)$bak_id){
            $this->msgbox->add('未指定所需的备份', 211);
        }else if(!$detail = K::M('system/themebak')->detail($bak_id)){
            $this->msgbox->add('备份不存在或已经删除', 212);
        }else{
            $tmpl_bak = K::M('system/themebak')->delete($bak_id);
            $this->msgbox->add('删除成功');
        }
    }
}