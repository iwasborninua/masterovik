<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Masterovik',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'layout' => 'main'
        ],

        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/modules/admin/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                ],
            ],
        ],

        'sms' => [
            "class"  => 'alexeevdv\sms\ru\Client',
            "api_id" => "3D03DB92-80ED-7C3D-AE4A-E792076687C2",
        ],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'M83Zqk1e-4ZKxFrzWhOEO8zIYYn7UQ-7',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/user',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/city',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/service-offered',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/service',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/announcement',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/credit-cards',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/faq',
                    'pluralize' => false,
                ],

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/v1/rule',
                    'pluralize' => false,
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
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '178.151.69.133', '178.150.235.2', '*'],
    ];
}

return $config;