<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: config.php 10736 2015-06-10 12:39:11Z maoge $
 * config.php  备份
 */


if(!defined('__DEBUG')){
    define('__DEBUG', false);
}
define('__TIME',    time());
define('__CHARSET', 'UTF-8');
define('__CFG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('__IMG_DIR', dirname(__CFG_DIR).DIRECTORY_SEPARATOR.'attachs'.DIRECTORY_SEPARATOR);
define('__TPL_DIR', dirname(__CFG_DIR).DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR);
define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
define('__DEBUG_LEVEL', 'system'); // false, data, system
define('__SYSTEM_LOGS', false); 
ini_set("display_errors", __DEBUG);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING & ~E_DEPRECATED);
date_default_timezone_set('Asia/Shanghai');
header("Content-type: text/html; charset=UTF-8");
class __CFG
{
    CONST DEBUG     = __DEBUG;
    CONST CHARSET   = __CHARSET;
    CONST DIR       = __CORE_DIR;
    CONST GPC       = MAGIC_QUOTES_GPC;
    CONST TIME      = __TIME;
    CONST TMPL_DIR  = __TPL_DIR;

    CONST RES_URL = '/static';
    CONST MYSQL = 'mysql://root:123456@localhost:3306/waimaipro/jh_/UTF8';

    CONST SECRET_KEY = 'f1b519de47ed93f3ded465da2df35c67';
    CONST Authorize = '0a3ec87e37d64a133492169a7c22e262';

    //Cookie
    CONST C_PREFIX  = 'KT-';
    CONST C_PATH    = '/';
    CONST C_DOMAIN  = 'waimai.local';
    CONST C_EXPIRE  = 2592000;
    CONST C_SECURE  = false; //https
    CONST C_HTTPONLY= true;  //httponly

    //session
    CONST S_TYPE    = 'database';
    CONST S_STAGE   = '';
    CONST S_NAME    = 'KTSSID';
    CONST S_EXPIRE  = 3600;

    //cache
    CONST CACHE_TYPE = 'file';
    CONST CACHE_PREFIX = 'jhcmswm';
    CONST MEMCACHE = '127.0.0.1:11211';

    //attach
    //CONST ATTACH_TYPE = 'aliyun';

    //


    //拥有的模块的模块--红包
    static $MODULE = array(
        'all'=>array('title'=>'通用','used'=>true),
        'waimai'=>array('title'=>'外卖','used'=>true),
        'paotui'=>array('title'=>'跑腿','used'=>true),

    );
    //红包配置

    //阿里云voice 相关配置
    CONST ALIVOICE_ID = false;//没有直接配置为false即可
    CONST ALIVOICE_Secret= "";
    CONST ALIVOICE_MOBILE = "";
    CONST ALIVOICE_TIME = 5;

    static $APPS = array(
        'www' => array('title'=>'默认', 'url'=>'http://waimai.local', 'rewrite'=>true),
        'home' => array('title'=>'默认', 'url'=>'http://waimai.local', 'rewrite'=>true),
        'waimai' => array('title'=>'默认', 'url'=>'http://waimai.local/waimai', 'rewrite'=>true),
        'admin' => array('title'=>'后台', 'url'=>'http://waimai.local/admin', 'rewrite'=>false),
        'wmbiz' => array('title'=>'商户', 'url'=>'http://waimai.local/wmbiz', 'rewrite'=>false),
        'staff'=>array('title'=>'骑手','url'=>'http://waimai.local/staff','rewrite'=>true),
        'jifen'=>array('title'=>'骑手','url'=>'http://waimai.local/jifen','rewrite'=>true),
        'pei'=>array('title'=>'配送团队','url'=>'http://waimai.local/pei','rewrite'=>true),
        'ditui'=>array('title'=>'地推','url'=>'http://waimai.local/ditui','rewrite'=>true),
        'paotui'=>array('title'=>'跑腿','url'=>'http://waimai.local/paotui','rewrite'=>true),
        'dispatch'=>array('title'=>'调度站','url'=>'http://waimai.local/dispatch','rewrite'=>true),
        'qiang'=>array('title'=>'抢购','url'=>'http://waimai.local/qiang','rewrite'=>true),
    );
}

//打印机系统参数 需要设置的参数
define('YLY_ID',"");//易连云打印机的应用ID
define("YLY_SECRET","");
define("PRINT_TYPE",'ylyun'); // ylyun : 易连云 xprint：喜乐打印机 ''：暂时还没有的  等待新加 外卖3.7 及以后的版本 必须填写 ！！！
//打印机系统参数

define('HAVE_WX_APP',false);//是否有微信小程序
define('HAVE_PAOTUI',false);//是否有跑腿
define("HAVE_PEI",false);//是否有配送站指派模式 用于配送站的派单逻辑处理 和定时脚本任务
define('HAVE_TONGCHENG',false);//配送站是否支持同城商城配送服务
define('HAVE_QIANG',false);//是否有抢购
define('HAVE_JIFEN',false);//是否有积分
define('HAVE_DITUI',false);//是否有地推
define('HAVE_PWXAPP',false);//是否有跑腿小程序

//阿里云相关配置
define('OSS_ACCESS_ID', '');
define('OSS_ACCESS_KEY', '');
define('OSS_ENDPOINT', '');
define('OSS_ISCNAME', true);
define('OSS_PREFIX', '/');
define('OSS_BUCKET', '');
//阿里云相关配置结束


//同城配送相关配置
define('API_KEY',"28e10523c6b6a3f9138c887c77217661");
define("API_URL","http://mall.jhcms.cn/api.php");
//同城配送相关配置结束


//地图相关配置
define("MAP_KEY","AIzaSyBs_cng6qHCdg7cYRyTVzplJQ5_e6WdluQ");//地图秘钥 --使用谷歌地图所使用的key
define("MAP_SERVER_KEY","AIzaSyBs_cng6qHCdg7cYRyTVzplJQ5_e6WdluQ");//谷歌地图 --服务端接口所使用的key
define("MAP_DEFAULT_CENTER","105.834888, 21.020805");    //谷歌地图默认中心坐标
//地图相关配置结束


//配送站相关配置
define('PEI_VRESIPN','dispatch');
define('IS_SWOOLE',0);//是否使用swoole 长连接
define('SWOOLE_PORT',0);//长连接端口
define('SWOOLE_IP',0);//服务器IP
define("AUTO_UPDATE",0);//开启地图自动刷新
//配送站相关配置结束



//饿了相关配置
define("ELE_APP_KEY","");//饿了吗 app_key --必填
define("ELE_APP_SECRET","");//饿了吗 app_secret  --必填
define('ELE_SANDBOX',true);//是否沙箱环境  需要配合
define("ELE_SCOPE","");//授权范围 无需修改
define('CALL_BACK_URL',"http://wmfz.jhcms.cn/wmbiz/oauth/ele/setAccessToken");//回调url
//饿了吗相关配置结束

//美团相关配置
define('MEITUAN_APP_ID','');
define('MWITUAN_SIGN_KEY','');
//美团设置结束

define('PUSH_TYPE', ''); //alipush阿里推送  jpush极光推送
define('iOSApnsEnv', ''); //阿里云推送ios环境（DEV:开发，PRODUCT:生产环境）