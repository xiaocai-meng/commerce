<?php

$params = require(__DIR__ . '/params.php');
$adminmenu = require_once(__DIR__.'/adminmenu.php');

//定义第三方库命名空间
Yii::$classMap['AlipayPay'] = '@app/vendor/AliPay/AlipayPay.php';

$config = [
    //修改默认控制器  在访问localhost/index.php时候加载index控制器,默认加载site控制器
    'defaultRoute' => 'index',
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //'aliases' => [
    //    '@redisMail/mailQueue' => '@app/vendor/redisMail/mailQueue/src'
    //],
    'components' => [
        
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'root',
            //为了使 API 接收 JSON 格式的输入数据，配置 request 应用程序组件的 parsers 属性使用 yii\web\JsonParser 用于JSON输入
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],

        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            //如果不配置就用redis块的配置
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 2,
            ],
        ],

        //配置用户登录认证体系 框架的user认证组件
        'user' => [
            'identityClass' => 'app\models\User',
            //设置session __id别名防止前后台冲突
            'idParam' => '__user',
            //设置自动登录别名,防止前后台用户冲突
            'identityCookie' => ['name' => '__user_identity', 'httpOnly' => true],
            //启用自动登录
            'enableAutoLogin' => true,
            //启动session储存会话
            'enableSession' => true,
            'loginUrl' => ['member/auth']
        ],

        //自定义用户后台认证组件
        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\models\Admin',
            //设置session __id别名防止前后台冲突
            'idParam' => '__admin',
            //设置自动登录别名,防止前后台用户冲突
            'identityCookie' => ['name' => '__admin_identity', 'httpOnly' => true],
            //启用自动登录
            'enableAutoLogin' => true,
            //启动session储存会话
            'enableSession' => true,
            'loginUrl' => ['/admin/public/login']
        ],
        
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        //内置邮件发送类
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
            'db' => '1',
            'key' => 'mails',
        ],
        //sentry邮件发送给自己的成员
        'sentry' => [
            'class' => 'mito\sentry\Component',
            'dsn' => 'https://3e949bccc710499b982bb0e9c47c4e68:27d53e365522450abf96981134d7e6a2@sentry.io/250006', // private DSN
            'publicDsn' => 'https://3e949bccc710499b982bb0e9c47c4e68@sentry.io/250006',
            'environment' => 'staging', // if not set, the default is `production`
            'jsNotifier' => true, // to collect JS errors. Default value is `false`
            'jsOptions' => [ // raven-js config parameter
                //收集所有url的js错误信息
                'whitelistUrls' => [ // collect JS errors from these urls
                    //'http://staging.my-product.com',
                    //'https://my-product.com',
                ],
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace','info'],
                    'logFile' => '@app/runtime/logs/info.log',
                    //使用Yii::info('111111', 'myinfo');
                    'categories' => ['myinfo'],
                    //只保留日志中的$_SERVER 如果不要这些信息则[]
                    //'logVars' => ['_SERVER']
                    'logVars' => [],
                    
                ],
                //如果出现error,warning错误信息则给自己发邮件通知
//                [
//                    'class' => 'yii\log\EmailTarget',
//                    'mailer' => 'mailer',
//                    'levels' => ['error', 'warning'],
//                    'message' => [
//                        'from' => ['1026251951@qq.com'],
//                        'to' => ['xiaomengxiaoqiu@163.com','sss@qq.com'],
//                        'subject' => 'Shop log message',
//                    ],
//                ],
                //sentry log 配置
                [
                    'class' => 'mito\sentry\Target',
                    'levels' => ['error', 'warning'],
                    //404不收集
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                ],
            ],
        ],

        'db' => require(__DIR__ . '/db.php'),

        //redis模块配置
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            //redis默认开启了0-15 16个库
            'database' => 0,
        ],

        //加载和配置ES模块
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '127.0.0.1:9200'],
                // configure more hosts if you have a cluster
            ],
        ],

        //压缩资源包发布的时候使用 dev为开发环境 prod为生产环境
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV == 'dev' ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'js' => [
                        YII_ENV == 'dev' ? 'css/bootstrap.css' : 'css/bootstrap.min.css'
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV == 'dev' ? 'js/bootstrap.js' : 'js/bootstrap.min.js'
                    ]
                ],
            ],
        ],

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
            //array a list of role names that are assigned to every user automatically without calling [[assign()]]
            'defaultRoles' => ['default'],
        ],
        
        //使用redis来存储session 如果配置lvs则必须使用此方法
        'session' => [  
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 3,
            ],
            //redis的key的前缀
            'keyPrefix' => 'redis_session'
        ],

        //URL地址美化
        'urlManager' => [
            'enablePrettyUrl' => true, //是否开启美化效果
            'showScriptName' => false, //指定是否在URL 保留入口脚本index.php
            //'enableStrictParsing' => true, //是否开启严格解析路由
            'suffix' => '.html', //url后缀
            'rules' => [
                //在访问index,cart,order控制器时即路由为localhost/cart.html时 实际访问该控制器下的index方法即cart/index 简写路由
                '<controller:(index|cart|order)>' => '<controller>/index',
                'auth' => 'member/auth',
                //product-categoty-20 => product/index?cateid=20
                'product-category-<cateid:\d+>' => 'product/index',
                'product-detail-<productid:\d+>' => 'product/detail',
                'order-check-<orderid:\d+>' => 'order/check',
                [
                    'pattern' => 'admin',
                    'route' => '/admin/default/index',
                    'suffix' => '.html'
                ],
                //restful api
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/user'],

            ],
        ],
        
        //自定义kafka模块
        'kafka' => [
            'class' => 'app\models\Kafka',
            'broker_list' => 'localhost:9092',
            'topic' => 'test'
        ],

    ],

    'params' => array_merge($params, ['adminmenu' => $adminmenu]),
];

//print_r($config);
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1'],
    ];
    $config['bootstrap'][] = 'gii';
    //默认gii模块是开启的
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //配置gii模块的允许的IP地址,如果不配置是没有权限来操作gii模块的
        'allowedIPs' => ['127.0.0.1'],
    ];
    //通过gii modules生成的admin模块必须进行配置才可以访问
    //写在此处只有在YII_ENV_DEV为true即为开发模式时才能访问
//    $config['modules']['admin'] = [
//        'class' => 'app\modules\admin',
//    ];

//    $config['modules']['user'] = [
//        'class' => 'app\modules\user\admin',
//    ];
}

$config['modules']['admin'] = [
    'class' => 'app\modules\admin',
];
$config['modules']['api'] = [
    'class' => 'app\api\api',
];
return $config;
