<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
Class QiniuAutoLoad
{
	static public function loader($class)
	{
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		$file = __DIR__ . DIRECTORY_SEPARATOR . $path . '.php';
		if (file_exists($file)) {
			require_once $file;
		}
	}
}
spl_autoload_register('QiniuAutoLoad::loader');
require_once  __DIR__ . '/Qiniu/functions.php';