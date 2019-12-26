<?php

return [
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\MobileUser',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],

        'sms' => [
            "class"  => 'alexeevdv\sms\ru\Client',
            "api_id" => "3D03DB92-80ED-7C3D-AE4A-E792076687C2",
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.beget.com',
                'username' => 'support@masterovik.net',
                'password' => '741236Raziel',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
    ],
    'language' => 'ru-RU'
];