<?php

namespace app\modules\v1\controllers;

use app\models\DisputFeedback;
use app\models\Order;
use app\models\Rate;
use app\models\Statistic;
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
class RateController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\Rate';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'response',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $behaviors;
    }

    public function actionWriteFeedback() {
        $access_token = Yii::$app->request->post('access_token');
        $order_id = Yii::$app->request->post('order_id');
        $review = Yii::$app->request->post('review');
        $rate = Yii::$app->request->post('rate');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $token->user;

        $order = Order::find()
            ->where(['id' => $order_id])
            ->one();

        if(!$order)
            return $this->customResponse('', 'Заказ не найден', 404);

        if(Rate::find()->where(['order_id' => $order_id])->exists())
            return $this->customResponse('', 'Вы уже оставили отзыв', 404);

        $feedback = new Rate();
        $feedback->order_id = $order->id;
        $feedback->user_id = $order->executor_id;
        $feedback->rate = $rate;
        $feedback->review  = $review;
        $feedback->create_at  = time();
        $feedback->save();

        return $this->customResponse(['response' => 'Отзыв отсавлен'], '', 200);
    }

    public function actionAverage() {
        return Rate::find()
            ->average('rate');
    }

    public function actionMyRate() {

        $access_token = Yii::$app->request->post('access_token');
        $city_id = Yii::$app->request->post('city_id');
        $date = Yii::$app->request->post('date');
        $rate_sort = Yii::$app->request->post('rate_sort');
        $rate_filter = Yii::$app->request->post('rate_filter');
        $newest = Yii::$app->request->post('newest');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $token->user;

        $rate = Rate::find()
            ->with(['order' => function(\yii\db\ActiveQuery $query) {
                $query->andWhere(['city_id' => Yii::$app->request->post('city_id')]);
            },
                'customer',
                ])
            ->where(['user_id' => $user->id])
            ->andFilterWhere(['>=', 'create_at', $date[0]])
            ->andFilterWhere(['<=', 'create_at', $date[1]])
            ->andFilterWhere(['rate' => $rate_filter])
            ->orderBy([
                'id' => $newest,
                'rate' => $rate_sort
            ])
            ->asArray()
            ->all();

        $rate = array_filter($rate, function ($item) {
            if ($item['order'] != null )
                return $item;
        });

        if(!$rate)
            $average = 0;
        else
            $average = array_sum(array_column($rate, 'rate')) / count($rate);


        return $this->customResponse(['response' => $rate, 'average' => $average]);
    }

    public function actionMyReviews() {

        $access_token = Yii::$app->request->post('access_token');
        $city_id = Yii::$app->request->post('city_id');
        $date = Yii::$app->request->post('date');
        $rate_sort = Yii::$app->request->post('rate_sort');
        $rate_filter = Yii::$app->request->post('rate_filter');
        $newest = Yii::$app->request->post('newest');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $token->user;

        $rate = Rate::find()
            ->with(['order', 'customer'])
            ->where(['user_id' => $user->id])
            ->andFilterWhere(['>=', 'create_at', $date[0]])
            ->andFilterWhere(['<=', 'create_at', $date[1]])
            ->andFilterWhere(['rate' => $rate_filter])
            ->orderBy([
                'id' => $newest,
                'rate' => $rate_sort
            ])
            ->asArray()
            ->all();

        return $this->customResponse(['response' => $rate]);
    }

    public function actionGetReview() {
        $rate_id = Yii::$app->request->post('rate_id');

        $rate = Rate::find()
            ->with(['customer' => function (\yii\db\ActiveQuery $query) {
                $query->select(['first_name', 'last_name']);
            }])
            ->where(['id' => $rate_id])
            ->asArray()
            ->one();

        return $this->customResponse(['response' => $rate], '', 200);
    }

    public function actionDisputeFeedback () {
        $rate_id = Yii::$app->request->post('rate_id');
        $text = Yii::$app->request->post('text');

        if (DisputFeedback::find()->where(['rate_id' => $rate_id])->exists()) {
            return $this->customResponse('','Ваша жалоба еще рассматривается', 404);
        }

        $disput = new DisputFeedback();
        $disput->rate_id = $rate_id;
        $disput->text = $text;
        $disput->save();

        return $this->customResponse(['response' => 'Ваша жалобы принята к рассмотрению'], '', 200);
    }

    public function actionGetStatistic() {
        $user_id = Yii::$app->request->post('user_id');

        $statistic = Statistic::find()
            ->where(['user_id' => $user_id])
            ->one();

        if (!$statistic)
            return $this->customResponse('', 'Для данного пользователя таблица статистики не найденна', 404);

        return $this->customResponse(['response' => $statistic], '' ,200);
    }

}
