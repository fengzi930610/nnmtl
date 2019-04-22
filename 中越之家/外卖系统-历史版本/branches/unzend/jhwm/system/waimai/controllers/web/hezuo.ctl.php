<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: index.ctl.php 14351 2015-07-22 01:25:14Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Web_Hezuo extends Ctl
{
    
    public function index()
    {
        $this->tmpl = 'web/page/hezuo.html';
    }
}