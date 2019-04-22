<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1
 * Time: 10:30
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Webview_Hezuo extends Ctl {
    public function index(){
        $pei_type =  $this->waimai_shop['pei_type'];
        if($pei_type==1){
            $start = $end = 0;
         //   $freight =  $this->system->config->get('fright');外卖配置改为模型获取
            $freight = K::M('waimai/config')->getfright();
            $freight_config = array();
            foreach($freight as $k=>$v){
               $tmp = array();
               $tmp['juli'] = $start.'-'.$v['fkm'].'千米';
               $tmp['money'] = $v['fm'];
               $start = $v['fkm'];
                $freight_config[] = $tmp;

            }
            $this->pagedata['freight']  = $freight_config;
            $this->pagedata['group'] =K::M('pei/group')->detail($this->waimai_shop['group_id']);
           $this->tmpl = 'webview/hezuo/yes.html';
        }else{
           $this->tmpl = 'webview/hezuo/no.html';
        }
    }
}