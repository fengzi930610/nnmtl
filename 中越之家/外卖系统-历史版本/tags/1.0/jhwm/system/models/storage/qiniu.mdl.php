<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}
Import::L('storage/qiniu/autoload.php');
class Mdl_Storage_Qiniu
{

    static protected $QINIU_ACCESS_KEY = '';
    static protected $QINIU_SECRET_KEY = '';
    static protected $QINIU_BUCKET = 'jhcms01';
    static protected $QINIU_PREFIX = 'jhcms01/';

    public function __construct($system)
    {
        self::$QINIU_ACCESS_KEY = QINIU_ACCESS_KEY;
        self::$QINIU_SECRET_KEY = QINIU_SECRET_KEY;
        self::$QINIU_BUCKET     = QINIU_BUCKET;
        self::$QINIU_PREFIX     = QINIU_PREFIX;
    }

    public function client()
    {
        $qiniuClient = null;
        if($qiniuClient === null){
            $qiniuClient = new Qiniu\Storage\UploadManager();
        }
        return $qiniuClient;
    }

    public function token()
    {
        $token = null;
        if($token === null){
            $auth = new Qiniu\Auth(self::$QINIU_ACCESS_KEY, self::$QINIU_SECRET_KEY);
            $token = $auth->uploadToken(self::$QINIU_BUCKET);
        }
        return $token;
    }


    public function BucketName()
    {
        return self::$QINIU_BUCKET;
    }


    public function upload($file, $object=null, $remove=false)
    {
        // 先把本地的$file上传到指定$bucket, 命名为$object
        if($object === null){
            $cfg = K::$system->config->get('attach');
            $object = substr($file, strlen($cfg['attachdir']));
            $object = str_replace('\\', '/', $object);
        }
        $object = self::$QINIU_PREFIX.$object;
        try{
            list($ret, $err) = $this->client()->putFile($this->token(), $object, $file);
        }catch(Exception $e){
            return false;
        }
        if ($err !== null) {
            return false;
        } else {
            if($remove){
                K::M('io/file')->remove($file);
            }
            return true;
        }
    }

    public function download($object, $file)
    {

    }

}