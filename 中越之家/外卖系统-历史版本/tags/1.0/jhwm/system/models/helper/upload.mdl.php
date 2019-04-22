<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: upload.mdl.php 9343 2015-03-24 07:07:00Z youyi $
 */
/**
 * 上传类只支持图片格式
 *
 * 601:上传失败
 * 602:不支持的文件扩展名
 * 603:不支持的文件类型
 * 604:上传的文件太大
 * 605:
 */

Import::M('magic/upload');
class Mdl_Helper_Upload extends Mdl_Magic_Upload{};