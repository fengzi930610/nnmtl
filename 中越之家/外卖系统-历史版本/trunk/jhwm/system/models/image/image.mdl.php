<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: image.mdl.php 2034 2013-12-07 03:08:33Z $
 */
Import::M('image/gd');
class Mdl_Image_Image extends Model
{   
	protected static $_oimg = null; 
    
    public function __construct(&$system)
    {
    	parent::__construct($system);
    	self::$_oimg = new Mdl_Image_Image();
    	self::$_oimg->params = $system->config->get('attach');
    }
    public function thumb()
    {}
}