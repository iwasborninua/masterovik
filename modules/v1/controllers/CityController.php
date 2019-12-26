<?php

namespace app\modules\v1\controllers;

use app\models\Token;
use app\traits\FormatterTrait;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Controller;
use app\models\MobileUser;
use Yii;

/**
 * Default controller for the `v1` module
 */
class CityController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\City';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'response',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

}
