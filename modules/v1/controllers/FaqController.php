<?php

namespace app\modules\v1\controllers;

use app\models\Token;
use app\traits\FormatterTrait;
use Yii;
use yii\rest\ActiveController;

/**
 * Default controller for the `v1` module
 */
class FaqController extends ActiveController
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

    public function actionSendMail()
    {
        $access_token = Yii::$app->request->post("access_token");
        $theme = Yii::$app->request->post("theme");
        $text = Yii::$app->request->post("text");

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        Yii::$app->mailer->compose()
            ->setFrom('support@masterovik.net')
            ->setTo('mihail.macnamara@gmail.com')
            ->setSubject($theme == null ? "{$user->email}": "$theme\n{$user->email}")
            ->setTextBody($text)
            ->send();

        return $this->customResponse(['message' => 'Сообщение отправленно'],'', 200);
    }
}
