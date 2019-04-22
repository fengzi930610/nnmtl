<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
class Ctl_Shop_Option extends Ctl
{
    public function index()
    {
        $this->tmpl = 'shop/option/index.html';
    }
}