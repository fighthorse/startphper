<?php

use Guzzle\Http\Client; 
use Illuminate\Database\Capsule\Manager as Capsule;

header("Content-type:text/html;charset=utf-8");

// Autoload 自动载入

require '../vendor/autoload.php';

// 应用目录为当前目录
define('APP_PATH', __DIR__.'/');

// 开启调试模式
define('APP_DEBUG', true);

// 网站根URL
define('APP_URL', 'http://www.xinchao.com');


// Eloquent ORM

$capsule = new Capsule;

$capsule->addConnection(require '../config/database.php');

$capsule->bootEloquent();

// 路由配置

require '../config/routes.php';