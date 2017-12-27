<?php
//yii.php入口文件的配置文件 管理yii的脚本
Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');


$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');



$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    //'aliases' => [
    //    '@redisMail/mailQueue' => '@app/vendor/redisMail/mailQueue/src'
    //],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile' => '@app/runtime/logs/kafka.log',
                    //使用Yii::info('111111', 'myinfo');
                    'categories' => ['testkafka'],
                    //只保留日志中的$_SERVER 如果不要这些信息则[]
                    //'logVars' => ['_SERVER']
                    'logVars' => [],
                    //当缓存中存满10000条时候在写入log
                    //'exportInterval' => 100000,
                ],
            ],
        ],
        'db' => $db,
        //RBAC权限控制配置
        'authManager' => [
            //使用数据库来存储 如果使用文件存储使用PhpManager类
            'class' => 'yii\rbac\DbManager',
            //auth_item (role permission type(1,2))
            //auth_item_child (role->permission)
            //auth_assignment (user->role)
            //auth_rule (rule) 额外的规则
            //给auth_item配置名称 用法跟model里面的用法一样 加前缀
            'itemTable' => '{{%auth_item}}',
            'itemChildTable' => '{{%auth_item_child}}',
            'assignmentTable' => '{{%auth_assignment}}',
            'ruleTable' => '{{%auth_rule}}',
        ],
        'mailer' => [
            //'class' => 'yii\swiftmailer\Mailer',
            'class' => 'redisMail\mailQueue\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => '1026251951@qq.com',
                //注意不是密码,是授权码
                'password' => 'drcnqvuionsybbcj',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        //redis模块配置
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            //redis默认开启了0-15 16个库
            'database' => 0,
        ],

        //自定义kafka模块
        'kafka' => [
            'class' => 'app\models\Kafka',
            'broker_list' => 'localhost:9092',
            'topic' => 'test'
        ],
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
