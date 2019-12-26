<?php

namespace app\modules\v1\controllers;

use app\models\Announcement;
use app\models\AnnouncementService;
use app\models\BalanceHistory;
use app\models\CreditCards;
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
class CreditCardsController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\CreditCards';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();

        return $behaviors;
    }

    public function actionAddCard() {
        $access_token = Yii::$app->request->post('access_token');
        $card = Yii::$app->request->post('card');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        if ( CreditCards::find()->where(['card' => $card])->one())
            return $this->customResponse('', 'Card with this number already exists', 404);

        $user = $token->user;

        $credit_card = new CreditCards();
        $credit_card->user_id = $user->id;
        $credit_card->card = $card;
        $credit_card->save();

        return $this->customResponse(['response' => $credit_card], '', 200);
    }

    public function actionViewCards() {
        $access_token = Yii::$app->request->post('access_token');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        $user = $token->user;

        $cards = CreditCards::find()
            ->where(['user_id' => $user->id])
            ->asArray()
            ->all();

        return $this->customResponse(['response' => $cards], '', 200);
    }

    public function actionDeleteCard() {
        $access_token = Yii::$app->request->post('access_token');
        $card_id = Yii::$app->request->post('card_id');

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        if ($token != null && CreditCards::find()->where(['id' => $card_id])->one())
            if (CreditCards::deleteAll(['id' => $card_id]))
                return $this->customResponse(['response' => 'Card deleted'], '', 200);


        return $this->customResponse('', 'Карта была удаленна или ее не существует', 404);
    }

    public function actionEditCard() {
        $card_id = Yii::$app->request->post('card_id');
        $card = Yii::$app->request->post('card');

        $result = CreditCards::find()
            ->where(['id' => $card_id])
            ->one();

        if(!$result)
            return $this->customResponse('', 'Card not found', 404);

        $result->card = $card;
        if ($result->save())
            return $this->customResponse(['response' => 'Card number has been changed'], '', 200);
    }

    public function actionAddMoney() {
        $access_token = Yii::$app->request->post('access_token');
        $card_id = Yii::$app->request->post('card_id');
        $sum = Yii::$app->request->post('sum');

        if(!is_int($sum))
            return $this->customResponse('', 'No integer entered', 404);


        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        $user = $token->user;

        $user->balance += $sum;

        $balance_history = new BalanceHistory();
        $balance_history->user_id = $user->id;
        $balance_history->card_id = $card_id;
        $balance_history->created_at = time();
        $balance_history->sum = $sum;
        $balance_history->balance = $user->balance;
        $balance_history->operation = 'Пополнение баланса';

        // При попролнении баланса от 1000 активировать учетную запись пользователя
        $balance_sum = BalanceHistory::find()
            ->where(['user_id' => $user->id])
            ->sum('sum');

        if($user->status == 0 && $balance_sum >= 1000) {
            $user->status = 1;
            $user->save();
            return $this->customResponse(['response' => 'Баланс успешно пополнен'], '', 200);
        }


        if ($balance_history->save() && $user->save()){
            return $this->customResponse(['response' => 'Баланс успешно пополнен'], '', 200);
        }

    }

    public function actionSubtractMoney() {
        $access_token = Yii::$app->request->post('access_token');
        $sum = Yii::$app->request->post('sum');

        if(!is_int($sum))
            return $this->customResponse('', 'No integer entered', 404);

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        $user = $token->user;

        if ($user->balance < $sum) {
            return $this->customResponse('', 'Not enough money for this operation', 404);
        }

        $user->balance += $sum;

        $balance_history = new BalanceHistory();
        $balance_history->user_id = $user->id;
        $balance_history->card_id = null;
        $balance_history->created_at = time();
        $balance_history->sum = $sum;
        $balance_history->balance = $user->balance;
        $balance_history->operation = 'Списание с внутреннего баланса';
        if ($balance_history->save() && $user->save()){
            $this->customResponse(['response' => 'Баланс успешно списан'], '', 200);
        }
    }

    public function actionGetPaymentHistory() {
        $access_token = Yii::$app->request->post('access_token');
        $new = Yii::$app->request->post('new');
        $operation = Yii::$app->request->post('operation');
        $card_id = Yii::$app->request->post('card_id');


        $data = [];

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        $user = $token->user;

        $balance_history = BalanceHistory::find()
            ->where(['user_id' => $user->id])
            ->andWhere($operation == null ? []: ['operation' => $operation])
            ->andWhere($card_id == null ? []: ['card_id' => $card_id])
            ->orderBy($new == true ? ['id' => SORT_DESC]:['id' => SORT_ASC])
            ->all();

        foreach ($balance_history as $item) {
            $item['created_at'] = Yii::$app->formatter->asDate($item['created_at'], 'php:d F Y');
        }

        return $this->customResponse(['response' => $balance_history], '', 200);
    }

    public function actionGetPartners() {
        $access_token = Yii::$app->request->post('access_token');
        $data = [];

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        $user = $token->user;

        $partners = MobileUser::find()
            ->where(['invited_by' => $user->invite])
            ->all();

        foreach ($partners as $partner) {
            $temp = [];
            $temp['name'] = $partner->first_name . " " . $partner->last_name;
            $temp['activate'] = $partner->status == 0 ?
                'Не активировал учетную запись': 'Активировал учетную запись';
            $temp['date'] = Yii::$app->formatter->asDate($partner->created_at, 'php:d F Y');

            array_push($data, $temp);
        }

        return $this->customResponse([
            'response' => $data,
            'referal_code' => $user->invite
        ], '' , 200);
    }
}
