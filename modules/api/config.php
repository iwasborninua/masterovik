<?php

return [
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\MobileUser',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
    ],
];