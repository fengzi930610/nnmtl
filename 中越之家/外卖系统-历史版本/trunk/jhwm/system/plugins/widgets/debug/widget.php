<?php
/**
 * Copy Right IJH.CC
 * $Id widget.php shzhrui$
 */

class Widget_Debug extends Model
{

    public function index(&$params)
    {   
        $params['tpl'] = 'default.html';
        $params['__DEBUGINFO__'] = K::M('helper/debug')->output(__DEBUG_LEVEL, true);
        return $params;
    }
}