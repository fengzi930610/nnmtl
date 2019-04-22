<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author @shzhrui<Anhuike@gmail.com>
 * $Id: page.ctl.php 2034 2013-12-07 03:08:33Z $
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::C('article/article');
class Ctl_Article_Pchelp extends Ctl_Article_Article
{
    protected $article_from = 'pchelp';
}