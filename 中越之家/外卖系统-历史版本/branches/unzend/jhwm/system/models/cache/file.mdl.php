<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: file.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::M('cache/mfile');
/**
 * 文件缓存类 基于secache修改
 */
class Mdl_Cache_File extends Mdl_Cache_Mfile{}