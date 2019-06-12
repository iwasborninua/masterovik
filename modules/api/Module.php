<?php

namespace app\modules\api;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        \Yii::configure(\Yii::$app, require __DIR__ . '/config.php');

        $this->modules = [
            'v1' => [
                // вот здеся первая версия нашего апиария
                'class' => 'app\modules\v1\Module',
            ],
        ];
    }
}
