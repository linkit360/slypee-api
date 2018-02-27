<?php

use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'app\modules\admin\Bootstrap',
        'app\components\Bootstrap',
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'xFlgpxfkgitjfjgdslsuxciSfkzAAfkgohZ',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'customer' => [
            'identityClass' => 'app\models\Customers',
            'class' => 'app\components\CustomerComponent',
        ],
        'user' => [
            'identityClass' => 'app\models\SlypeeUser',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            //'suffix' => '/',
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT
            ],
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/main',
                    'pluralize' => false,
                    'except' => ['create', 'view', 'update', 'delete']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/slider',
                    'pluralize' => false,
                    'only' => ['index']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/category',
                    'pluralize' => false,
                    'only' => ['index', 'menu', 'info'],
                    'extraPatterns' => [
                        'GET menu' => 'menu',
                        'GET info/<slug:[\w-]+>' => 'info'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/content',
                    'pluralize' => false,
                    'only' => ['index', 'view', 'category', 'subscribe', 'unsubscribe', 'top', 'search', 'customer', 'subscribe-success', 'subscribe-error', 'unsubscribe-success', 'unsubscribe-error'],
                    'extraPatterns' => [
                        'GET subscribe/<id:\d+>' => 'subscribe',
                        'GET unsubscribe/<id:\d+>' => 'unsubscribe',
                        'GET category/<id:\d+>' => 'category',
                        'GET top' => 'top',
                        'GET search' => 'search',
                        'GET customer' => 'customer',
                        'GET subscribe-success' => 'subscribe-success',
                        'GET subscribe-error' => 'subscribe-error',
                        'GET unsubscribe-success' => 'unsubscribe-success',
                        'GET unsubscribe-error' => 'unsubscribe-error',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/customer',
                    'pluralize' => false,
                    'only' => ['index', 'sigin', 'logout', 'recovery', 'recovery-confirm'],
                    'extraPatterns' => [
                        'POST sigin' => 'sigin',
                        'GET logout' => 'logout',
                        'POST recovery' => 'recovery',
                        'POST recovery-confirm' => 'recovery-confirm',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
