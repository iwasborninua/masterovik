<?php

namespace app\modules\v1\controllers;


use app\models\Order;
use app\models\OrderCansel;
use app\models\OrderChangeConditions;
use app\models\Report;
use app\models\Token;
use app\traits\FormatterTrait;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use Yii;

/**
 * Default controller for the `v1` module
 */

class OrderController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\Order';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();

        return $behaviors;
    }
    // Активные заказы
    public function actionGetActiveOrders() {
        $access_token = Yii::$app->request->post('access_token');
        $city = Yii::$app->request->post('city_id');
        $price = Yii::$app->request->post('price');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if($user->role_id == 1) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(['status_id' => 1])
                ->andWhere(['customer_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        if($user->role_id == 2) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(['status_id' => 1])
                ->andWhere(['executor_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        return $this->customResponse(['response' => $orders]);
    }

    // Завершенные заказы
    public function actionGetCompletedOrders () {
        $access_token = Yii::$app->request->post('access_token');
        $status_id = Yii::$app->request->post('status_id');
        $city = Yii::$app->request->post('city_id');
        $price = Yii::$app->request->post('price');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if($user->role_id == 1) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(['status_id' => 2])
                ->andWhere(['customer_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        if($user->role_id == 2) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(['status_id' => 2])
                ->andWhere(['executor_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        return $this->customResponse(['response' => $orders]);
    }

    // Отмененные заказы
    public function actionGetCanceledOrders () {
        $access_token = Yii::$app->request->post('access_token');
        $status_id = Yii::$app->request->post('status_id');
        $city = Yii::$app->request->post('city_id');
        $price = Yii::$app->request->post('price');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if($user->role_id == 1) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(
                    [
                        'OR',
                        ['status_id' => 4],
                        ['status_id' => 5]
                    ]
                )
                ->andWhere(['customer_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        if($user->role_id == 2) {
            $orders = Order::find()
                ->with(['services', 'customer'])
                ->where(
                    [
                        'OR',
                        ['status_id' => 4],
                        ['status_id' => 5]
                    ]
                )
                ->andWhere(['executor_id' => $user->id])
                ->andFilterWhere(['>=', 'price', $price[0]])
                ->andFilterWhere(['<=', 'price', $price[1]])
                ->andFilterWhere(['city_id' => $city])
                ->offset($offset)
                ->limit($limit)
                ->asArray()
                ->all();
        }

        return $this->customResponse(['response' => $orders]);
    }

    // Завершить заказ
    public function actionAcceptOrder () {
        $access_token = Yii::$app->request->post('access_token');
        $order_id = Yii::$app->request->post('order_id');

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

        if ($order->customer_id != $user->id )
            return $this->customResponse('', 'Вы не можете закрыть заказ, создателем которого не являетесь', 404);

        if($order->status_id == 2) {
            return $this->customResponse('', "Объявление уже имеет статус 'выполненно'", 404);
        }

        $order->status_id = 2;
        if ($order->save())
            return $this->customResponse(['response' => 'Статус объявления успешно обновлен'], 200);

    }

    public function actionCompletionOrder() {
        $access_token = Yii::$app->request->post('access_token');
        return 'Я так понял, мы тут просто хуйнем пуш уведомление. Мне нужен токен и id объявлениея';
    }

    public function actionSendReport() {
        $access_token = Yii::$app->request->post('access_token');
        $order_id = Yii::$app->request->post('order_id');
        $title = Yii::$app->request->post('title');
        $description = Yii::$app->request->post('description');
        $order_id = Yii::$app->request->post('order_id');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $token->user;

        if(
            Report::find()
                ->where([
                    'AND',
                    ['user_id' => $user->id],
                    ['order_id' => $order_id]
                ])->exists()
        ) return $this->customResponse('', 'Ваша жалоба находится на рассмотрении', 404);

        $report = new Report();
        $report->order_id = $order_id;

        $report->title = $title;
        $report->description = $description;
        $report->user_id = $user->id;
        $report->create_at = time();
        $report->save();

        return $this->customResponse(['response' => 'Ваша жалоба на рассмотрении'], '',200);
    }

    public function actionChangeOrderConditions() {
        $access_token = Yii::$app->request->post('access_token');
        $order_id = Yii::$app->request->post('order_id');

        return 'запрос переделывается';

    }
}
