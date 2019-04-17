<?php
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 配置目录
define('CONF_PATH', __DIR__ . '/config/');
// 限定只能访问api模块，也可以继续限定某个控制器，某个方法
define('BIND_MODEL','api');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
?>