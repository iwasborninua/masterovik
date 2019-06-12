<?php

namespace app\modules\v1\controllers;

use app\models\Token;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Controller;
use app\models\MobileUser;
use Yii;

/**
 * Default controller for the `v1` module
 */
class UserController extends ActiveController
{
    public $modelClass = 'app\models\MobileUser';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['except'] = ['create-user', 'login', 'refresh-token', 'test-test'];
        return $behaviors;
    }


    public function actionCreateUser() {
        $model = new MobileUser();
        if ($model->load(Yii::$app->request->post(), '') && $model->save(false) && $model->setToken($model->id)) {
            return Yii::$app->response->statusCode = '200';
        } else {
            return Yii::$app->response->statusCode = '400';
        }
    }

    public function actionLogin() {
        $phone_input = Yii::$app->request->post('phone');
        $sms_input = Yii::$app->request->post('sms');
        $sms = '0000';

        if ($sms_input != '0000')
            return "Invalid sms code";


        $user = MobileUser::find()
            ->where(['phone' => $phone_input])
            ->with(['tokens'])
            ->asArray()
            ->one();

        if (!$user) {
            return "User not found";
        }

        $tokens['access_token'] = $user['tokens'][0]['access_token'];
        $tokens['refresh_token'] = $user['tokens'][0]['refresh_token'];
        return $tokens;
    }

    public function actionRefreshToken() {
        $refresh_token = Yii::$app->request->post('refresh_token');

        $result = Token::find()
            ->where(['refresh_token' => $refresh_token])
            ->one();

        if(!$result)
            return "token ot found";

        $result->access_token = Yii::$app->security->generateRandomString();
        $result->expires = mktime(0, 0, 0, date("m") + 1);
        $result->save();
        return $result->access_token;
    }



    public function actionTest() {
        echo mktime() . "</br>";
        echo date("Y-m-d", mktime(0, 0, 0, date("m") + 1)) . "</br>" ;die;
    }
}
