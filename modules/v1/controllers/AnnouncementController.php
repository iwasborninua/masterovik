<?php

namespace app\modules\v1\controllers;

use app\models\Announcement;
use app\models\AnnouncementService;
use app\models\BalanceHistory;
use app\models\City;
use app\models\Offer;
use app\models\OfferBan;
use app\models\OfferHistory;
use app\models\Order;
use app\models\OrderService;
use app\models\Service;
use app\models\ServiceOffered;
use app\models\Statistic;
use app\models\Token;
use app\models\UserService;
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
class AnnouncementController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\Announcement';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();

        return $behaviors;
    }

    public function actionCreateAnnouncement() {

        $post = Yii::$app->request->post();

        $result = Token::find()
            ->where(['access_token' => $post['access_token']])
            ->with('user')
            ->one();

        $user = $result->user;

        if (count($post['service_id']) > 3)
            return $this->customResponse('', 'Превышен лимит на сервисы для объявления.', 404);

        if ($post['service_id'] == null)
            return $this->customResponse('', 'Выберите для объявления хотя бы один тип услуги.', 404);

        $announcement = new Announcement();
        $announcement->city_id = $post['city_id'];
        $announcement->user_id = $user->id;
        $announcement->status_id = 1;
        $announcement->term = $post['term'];
        $announcement->price = $post['price'];
        $announcement->place_of_work = $post['place_for_work'];
        $announcement->description = $post['description'];
        $announcement->description_full = $post['description_full'];
        $announcement->created_at = time();

        if ($announcement->save()) {

            $statistic = Statistic::find()
                ->where(['user_id' => $user->id])
                ->one();

            $statistic->created_announcements = $statistic->created_announcements + 1;
            $statistic->save();

            foreach ($post['service_id'] as $service) {
                $model = new AnnouncementService();
                $model->service_id = $service;
                $model->announcement_id = $announcement->id;
                $model->save();
            }
        } else {
            return $this->customResponse('', 'Данные не сохранены', 404);
        }
        return $this->customResponse('','',200);
    }

    public function actionGetCatalogAnnouncements() {

        $data = [];
        $city = Yii::$app->request->post('city_id');
        $services = Yii::$app->request->post('services_id');
        $price = Yii::$app->request->post('price');
        $term = Yii::$app->request->post('term');
        $date = Yii::$app->request->post('date');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');

        $results = Announcement::find()
            ->select('announcement.*')
            ->innerJoin('announcement_service', ['announcement_service.announcement_id' => new \yii\db\Expression('announcement.id')])
            ->innerJoin('service', ['service.id' => new \yii\db\Expression('announcement_service.service_id')])
            ->andFilterWhere(['service_id' => $services])
            ->andFilterWhere(['service_id' => $services])
            ->andFilterWhere(['>=', 'price', $price[0]])
            ->andFilterWhere(['<=', 'price', $price[1]])
            ->andFilterWhere(['>=', 'term', $term[0]])
            ->andFilterWhere(['<=', 'term', $term[1]])
            ->andFilterWhere(['>=', 'created_at', $date[0]])
            ->andFilterWhere(['<=', 'created_at', $date[1]])
            ->andFilterWhere(['city_id' => $city])
            ->asArray()
            ->orderBy('id DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();

        foreach ($results as $result){
            $temp = Array();

            $temp['id'] = $result['id'];
            $temp['announcement_title'] = $result['category'];
            $temp['name'] = $result['user']['first_name'] . " " .$result['user']['last_name'];
            $temp['phone'] = $result['user']['phone'];
            $temp['email'] = $result['user']['email'];
            $temp['create_at'] = Yii::$app->formatter->asDate($result['created_at'], 'php:d F Y');
            $temp['date_end'] = Yii::$app->formatter->asDate(strtotime("+{$result['term']} days", $result['created_at']) , 'php:d F Y');
            $temp['price'] = $result['price'];
            $temp['place_of_work'] = $result['place_of_work'];
            $temp['description'] = $result['description'];
            $temp['description_full'] = $result['description_full'];
            $temp['city_id'] = $result['city_id'];

            array_push($data, $temp);
        }

        return $this->customResponse(['response' => $data], '', 200);
    }


    public function actionGetMyAnnouncements() {
        $user_service_id = [];
        $data = [];

        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');

        $token = Token::find()
            ->where(['access_token' => Yii::$app->request->post('access_token')])
            ->with(['user'])
            ->one();

        $user = MobileUser::find($token->user->id)
            ->one();

        foreach ($user->services as $service)
            $user_service_id[] = $service->id;

        $results = Announcement::find()
            ->joinWith('category')
            ->where(['service_id' => $user_service_id])
            ->asArray()
            ->with(['user', 'category', 'offer'])
            ->offset($offset)
            ->limit($limit)
            ->all();


        foreach ($results as $result){
            $temp = Array();

            $temp['id'] = $result['id'];
            $temp['announcement_title'] = $result['category'];
            $temp['name'] = $result['user']['first_name'] . " " .$result['user']['last_name'];
            $temp['phone'] = $result['user']['phone'];
            $temp['email'] = $result['user']['email'];
            $temp['create_at'] = Yii::$app->formatter->asDate($result['created_at'], 'php:d F Y');
            $temp['date_end'] = Yii::$app->formatter->asDate(strtotime("+{$result['term']} days", $result['created_at']) , 'php:d F Y');
            $temp['price'] = $result['price'];
            $temp['place_of_work'] = $result['place_of_work'];
            $temp['description'] = $result['description'];
            $temp['description_full'] = $result['description_full'];
            $temp['city_id'] = $result['city_id'];
            $temp['offer'] = $result['offer'];

            array_push($data, $temp);
        }

        return $this->customResponse(['response' => $data], '', 200);
    }

    public function actionEditAnnouncement() {
        $id = Yii::$app->request->post('id');
        $term = Yii::$app->request->post('term');
        $price = Yii::$app->request->post('price');

        $announcement = Announcement::find()
            ->where(['id' => $id])
            ->one();

        $announcement->load(Yii::$app->request->post(), '');

        return $this->customResponse(['message' => 'Объявление успешно отредактированно'], '', 200);
    }

    public function actionSendOffer() {
        $access_token = Yii::$app->request->post('access_token');
        $announcement_id = Yii::$app->request->post('announcement_id');
        $term = Yii::$app->request->post('term');
        $price = Yii::$app->request->post('price');

        $response = 'Ваше условия отправленны заказчику';

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        $offer = Offer::find()
            ->where([
                'AND',
                ['announcement_id' => $announcement_id],
                ['executor_id' => $user->id],
            ])->one();

        $user = $result->user;

        if (
            OfferBan::find()
            ->where([
                'AND',
                ['executor_id' => $user->id],
                ['announcement_id' => $announcement_id],
            ])->exists()
        ) {
            return $this->customResponse('', 'Вы не можете откликатся на это объявление',404);
        }

        // Если исполнитель впервые отправляет оффер на объявление
//        if ($user->role_id == 2 && $offer == null) {
        if($offer == null) {
            $offer = new Offer();
            $response = 'Оффер успешно отправлен';
        }

        if ($offer->term == $term && $offer->price == $price)
            return $this->customResponse('', 'Вы уже внесли данные условия', 404);

        if ($term == null && $price == null)
            return $this->customResponse('', 'Вы внесли пустые данные', 404);

        $offer->announcement_id = $announcement_id;
        $offer->term = $term == null ? : $offer->term = $term;
        $offer->price = $price == null ? : $offer->price = $price;
        $offer->executor_id = $user->id;

        if ($offer->save()) {
            $offer_history = new OfferHistory();
            $offer_history->term = $offer->term;
            $offer_history->price = $offer->price;
            $offer_history->announcement_id  = $offer->announcement_id;
            $offer_history->create_at = time();
            $offer_history->isExecutor = $user->role_id == "2" ? "true" : "false";
            $offer_history->save();
            return $this->customResponse(['response' => $response], '',200);
        }
    }

    public function actionGetListOffers () {
        $announcement_id = Yii::$app->request->post('announcement_id');

        $offers = Offer::find()
            ->where(['announcement_id' => $announcement_id])
            ->all();

        return $this->customResponse(['response' => $offers], '', 200);
    }

    public function actionCounterOffer () {
        $access_token = Yii::$app->request->post('access_token');
        $offer_id = Yii::$app->request->post('offer_id');
        $term = Yii::$app->request->post('term');
        $price = Yii::$app->request->post('price');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        $offer = Offer::find()
            ->where(['id' => $offer_id])
            ->one();

        $offer->term = $term;
        $offer->price = $price;

        if ($offer->save()) {
            $offer_history = new OfferHistory();
            $offer_history->term = $offer->term;
            $offer_history->price = $offer->price;
            $offer_history->announcement_id  = $offer->announcement_id;
            $offer_history->create_at = time();
            $offer_history->isExecutor = $user->role_id == "2" ? "true" : "false";
            $offer_history->save();
            return $this->customResponse(['response' => 'Встречное предложение успешно отправлено'], '',200);
        }
    }

    public function actionRejectOffer()
    {
        $access_token = Yii::$app->request->post('access_token');
        $offer_id = Yii::$app->request->post('offer_id');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        $offer = Offer::find()
            ->where(['id' => $offer_id])
            ->one();


        if($user->role_id == 1) {
            $ban = new OfferBan();
            $ban->announcement_id = $offer->announcement_id;
            $ban->executor_id = $offer->executor_id;
            $ban->expired = time() + strtotime("+ 2 days");
            $ban->save();
        }

        return $this->customResponse(['response' => 'Вы отклонили предложение'], '', 200);
    }

    public function actionAcceptOffer() {
        $access_token = Yii::$app->request->post('access_token');
        $offer_id = Yii::$app->request->post('offer_id');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        $offer = Offer::find()
            ->where(['id' => $offer_id])
            ->one();

        $announcement = Announcement::find()
            ->where(['id' => $offer->announcement_id])
            ->one();

        if(!$user)
            return $this->customResponse('', 'User not found', 404);
        if(!$announcement)
            return $this->customResponse('', 'Announcement not found', 404);
        if(!$offer)
            return $this->customResponse('', 'Offer not found', 404);

        $comission = ($announcement->price / 100) * 10 < 500 ? $comission = 500 : $comission = ($announcement->price / 100) * 10;

        $executor = MobileUser::find()
            ->where(['id' => $offer->executor_id])
            ->one();

        if ($executor->balance < $comission)
            return $this->customResponse('', 'Недостаточно средств на вашем балансе', 404);

        $executor->balance = $executor->balance - $comission;
        $executor->save();

        $balance_history = new BalanceHistory();
        $balance_history->user_id = $executor->id;
        $balance_history->card_id = null;
        $balance_history->created_at = time();
        $balance_history->sum = $comission;
        $balance_history->balance = $executor->balance;
        $balance_history->operation = 'Списание с внутреннего баланса';
        $balance_history->save();

        $order = new Order();
        $order->customer_id = $user->id;
        $order->executor_id = $offer->executor_id;
        $order->status_id = 1;
        $order->start_date = time();
        $order->end_date = time() + strtotime("+{$offer->term} days");
        $order->price = $announcement->price;
        $order->comission = $comission;
        $order->city_id = $announcement->city_id;
        $order->place_of_work = $announcement->place_of_work;
        $order->description = $announcement->description;
        $order->created_at = time();
        $order->save();

        $announcement_service = AnnouncementService::find()
            ->where(['announcement_id' => $offer->announcement_id])
            ->all();

        foreach ($announcement_service as $item) {
            $order_service = new OrderService();
            $order_service->order_id = $order->id;
            $order_service->service_id = $item->service_id;
            $order_service->save();
        }

        $announcement_id = $offer->announcement_id;

        Announcement::deleteAll(['id' => $announcement_id]);

        AnnouncementService::deleteAll(['id' => $announcement_id]);

        Offer::deleteAll(['announcement_id' => $announcement_id]);

        return $this->customResponse(['response' => 'Оффер успешно принят, объявление прешло в статус заказа']);
    }
}
