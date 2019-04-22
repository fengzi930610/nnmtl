<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if (!defined('__CORE_DIR')) {
    exit("Access Denied");
}

class Ctl_Qrcode extends Ctl
{
    
    public function index()
    {
       Import::L('qrcode/phpqrcode.php');
        $data = $this->GP('data');
        if(!$size = $this->GP('size')){
            $size = 6;
        }
        echo QRcode::png($data, false, QR_ECLEVEL_M, $size, 2);
        exit;
    }

  
}
