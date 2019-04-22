<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Waimai_Orderby extends Ctl
{
    
    public function index()
    {
        $this->tmpl = 'admin:waimai/orderby/items.html';
    }
    
    public function create()
    {        
        if($data = $this->checksubmit('data')){
            
        }else{
           $this->tmpl = 'admin:waimai/orderby/create.html';
        }        
    }

    public function edit($youhui_id=null)
    {
        if($data = $this->checksubmit('data')){            
             
        }else{
        	$this->tmpl = 'admin:waimai/orderby/edit.html';
        }
    }  
}