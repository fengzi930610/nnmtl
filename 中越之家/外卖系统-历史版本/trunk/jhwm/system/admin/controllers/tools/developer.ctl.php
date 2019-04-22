<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: developer.ctl.php 2034 2013-12-07 03:08:33Z $
 */
class Ctl_Tools_Developer extends Ctl
{
    
    public function index()
    {
        
    }
    public function module()
    {
    	if(!$this->checksubmit()){
            $this->pagedata['menu_tree'] = K::M('module/view')->tree();            
    		$this->tmpl = 'admin:developer/module/create.html';
    		$this->output();
    	}else if(!$module = $this->GP('module')){
            $this->msgbox->add('非法的数据提交', 201);
        }else if(!$schema = $this->GP('schema')){
            $this->msgbox->add('Schema数据不正确', 201);
        }else if(!$mdl = $this->GP('mdl')){
            $this->msgbox->add('MDL数据不正确', 201);
        }else if(!$ctl = $this->GP('ctl')){
            $this->msgbox->add('CTL数据不正确', 201);
        }else{
            $obj = K::M('developer/module');
            if(!$schema = $obj->create_schema($mdl['table'], $schema, $module['schema'])){
                $this->msgbox->add('Schema生成失败', 201)->show();
            }
            if($module['view']){
                $obj->create_tmpl($schema, $ctl, $mdl);
            }
            if($module['mdl']){
                $obj->create_mdl($mdl, $schema);
            }
            if($module['ctl']){
                if($ctl = $this->GP('ctl')){
                    $obj->create_ctl($ctl, $mdl, $schema);
                }
            }
            //$this->response("javascript:;");          
        }
    }
    public function schema($table=null)
    {
        if($table === null){
            $this->msgbox->add('未指定数据表', 401);
        }else if(!$fields = K::M('developer/module')->table_fields($table)){
            $this->msgbox->add('数据表不存在', 401);
        }else{
            $this->pagedata['schema'] = $fields;
            $this->tmpl = 'admin:developer/module/schema.html';
        }
    }
    public function config()
    {
    	if($this->checksubmit('config')){
            if(!$module = $this->GP('module')){
                $this->msgbox->add('非法的数据提交', 201);
            }else if(!$config = $this->GP('config')){
                $this->msgbox->add('非法的数据提交', 202);
            }else if(!$schema = $this->GP('data')){
                $this->msgbox->add('非法的数据提交', 203);
            }else{
                $obj = K::M('developer/config');
                if(!$schema = $obj->create_schema($config, $schema, $module['schema'])){
                    $this->msgbox->add('Schema数据不正确', 221);
                }else{
                    if($module['cfg']){
                        $obj->create_cfg($config, $schema);
                    }
                    if($module['view']){
                        $obj->create_tmpl($config, $schema);
                    }
                    $this->msgbox->add('创建配置成功');
                }
            }
        }else{
            $this->pagedata['menu_tree'] = K::M('module/view')->tree();
    		$this->tmpl = 'admin:developer/config/create.html';
    		$this->output();
    	}
    }
}