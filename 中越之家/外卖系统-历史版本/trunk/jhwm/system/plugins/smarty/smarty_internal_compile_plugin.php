<?php
/**
 * Copy	Right Anhuike.com
 * $Id smarty_internal_compile_plugin.php shzhrui<anhuike@gmail.com>
 */

class Smarty_Internal_Compile_Plugin extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('name');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('name');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles the calls of user defined tags defined by {plugin}
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */	
	public function compile($args, $compiler,$parameter)
    {
		$tpl_name = K::$system->ctl->pagedata['_PLUGIN_'];
		$output = '<?php echo $_smarty_tpl->getSubTemplate ("plugin:'.$tpl_name.'", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>';
		return $output;
	}

}