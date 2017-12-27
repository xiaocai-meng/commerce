<?php
//注释掉以下两行被部署到生产环境的时候
defined('YII_DEBUG') or define('YII_DEBUG', true);
//开启开发模式才可以访问gii模块
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');


$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
