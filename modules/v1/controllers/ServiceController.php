<?php

namespace app\modules\v1\controllers;

use app\models\Service;
use app\models\Token;
use app\models\UserService;
use app\traits\FormatterTrait;
use phpDocumentor\Reflection\Types\Object_;
use yii\db\ForeignKeyConstraint;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\Controller;
use app\models\MobileUser;
use Yii;

/**
 * Default controller for the `v1` module
 */
class ServiceController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\Service';

//    public $serializer = [
//        'class' => 'yii\rest\Serializer',
//        'collectionEnvelope' => 'response',
//    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();

        return $behaviors;
    }

    public function actionGetServices() {
        $result = Service::find()->all();

        foreach ($result as $data){
            if ($data->logo)
                $data->logo = 'http://masterovik.net/uploads/' . $data->logo;
            else
                $data->logo = 'http://masterovik.net/uploads/default.jpg';
        }

        $services_array = (array)$result;

        return $this->customResponse(['response' => $services_array], '', 200);

    }

    public function actionGetUserServices() {
        $access_token = Yii::$app->request->post('access_token');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;
        $services = $user->services;



        if ($services != null)
        {
            foreach ($services as $service) {
                if ($service->logo)
                    $service->logo = 'http://masterovik.net/uploads/' . $service->logo;
                else
                    $service->logo = 'http://masterovik.net/uploads/default.jpg';
            }

        }

        return $this->customResponse(['response' => $services], '', 200);
    }
}
