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
class ServiceOfferedController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\ServiceOffered';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className(); 

        return $behaviors;
    }

}
