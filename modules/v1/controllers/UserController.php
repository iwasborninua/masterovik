<?php

namespace app\modules\v1\controllers;

use app\models\FavoriteExecutors;
use app\models\Order;
use app\models\PhoneValidate;
use app\models\Rate;
use app\models\Statistic;
use app\models\Token;
use app\models\UserService;
use app\traits\FormatterTrait;
use yii\db\ActiveQuery;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Controller;
use app\models\MobileUser;
use Yii;
use alexeevdv\sms\ru\Sms;

/**
 * Default controller for the `v1` module
 */
class UserController extends ActiveController
{
    use FormatterTrait;

    public $modelClass = 'app\models\MobileUser';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['except'] = ['create-user', 'login', 'refresh-token', 'change-role', 'get-all-users', 'delete'];
        return $behaviors;
    }

    // отладочное

    public function actionGetAllUsers() {
        return MobileUser::find()->all();
    }


    public function actionCreateUser() {
        $model = new MobileUser();
        $email = Yii::$app->request->post('email');

        if($email != null && MobileUser::find()->where(['email' => $email])->exists())
            return $this->customResponse('', 'A user with this email adress is already exists', 404);

        if ($model->load(Yii::$app->request->post(), '') && $model->save(true) && $model->setToken($model->id)) {
            $result = Token::find()
                ->where(['user_id' => $model->id])
                ->one();

            // Вышел быдлокод, но не я же меняю требования во время разработки, так что...
            $model->invite = uniqid();
            $model->status = 0;
            $model->created_at = time();
            $model->save();

            $statistic = new Statistic();
            $statistic->user_id = $model->id;
            $statistic->save();

            $tokens['access_token'] = $result->access_token;
            $tokens['refresh_token'] = $result->refresh_token;

            return $this->customResponse($tokens, '', 200);
        } else {
            return $this->customResponse('', 'User not created, check input values', 404);
        }
    }

    public function actionLogin()
    {
        $phone_input = Yii::$app->request->post('phone');
        $sms_input = Yii::$app->request->post('sms');
        $sms_code = mt_rand(1111, 9999);
        $data = [];


        $user = null;
        $validate_phone = null;

        if ($phone_input != null && $sms_input == null) {

            if (MobileUser::find()->where(['phone' => $phone_input])->exists()) {
                $user = MobileUser::find()
                    ->where(['phone' => $phone_input])
                    ->one();

                $user->sms = $sms_code;
                $user->save();

                Yii::$app->sms->send(new Sms([
                    'to' => $user->phone,
                    'text' => "Code: {$user->sms}",
                ]));

                return $this->customResponse(['message' => 'sms sent to registered user'], '', 200);

            } else {
                PhoneValidate::deleteAll(['phone' => $phone_input]);

                $validate_phone = new PhoneValidate();
                $validate_phone->phone = $phone_input;
                $validate_phone->sms = $sms_code;

                $validate_phone->save(false);

                Yii::$app->sms->send(new Sms([
                    'to' => $validate_phone->phone,
                    'text' => $validate_phone->sms,
                ]));

                return $this->customResponse(['message' => 'sms sent to unregistered user'], '', 404);
            }
        }

        if ($phone_input != null && $sms_input != null) {

            if (MobileUser::find()->where(['phone' => $phone_input])->exists()) {
                $user = MobileUser::find()
                    ->with(['tokens'])
                    ->where(['phone' => $phone_input])
                    ->one();

                if ($sms_input != $user->sms) {
                    return $this->customResponse(['status_code' => '700'], 'Invalid sms code', 404);
                } else {
                    $data['access_token'] = $user->tokens[0]['access_token'];
                    $data['refresh_token'] = $user->tokens[0]['refresh_token'];
                    $data['role_id'] = $user->role_id;
                    $user->sms = null;
                    $user->save();
                    return $this->customResponse($data, '', 200);
                }
            } else {
                $validate_phone = PhoneValidate::find()
                    ->where(['phone' => $phone_input])
                    ->one();

                if ($validate_phone->sms != $sms_input) {
                    return $this->customResponse(['status_code' => '700'], 'Invalid sms code', 404);
                } else {
//                    MobileUser::find()->where(['phone' => $phone_input])->exists() ? $registred = true: $registred = false;
                    return $this->customResponse(['message'=> 'user has verified his number',
                        'isExist' => MobileUser::find()->where(['phone' => $phone_input])->exists()], '', 200);
                }
            }
        }
    }

    public function actionRefreshToken() {
        $refresh_token = Yii::$app->request->post('refresh_token');
        $result = Token::find()
            ->where(['refresh_token' => $refresh_token])
            ->one();

        if(!$result)
            return $this->customResponse('', 'refresh_token not found', 404);

        $result->access_token = Yii::$app->security->generateRandomString();
        $result->expires = mktime(0, 0, 0, date("m") + 1);
        $result->updated_at = mktime();
        $result->save();

        return $this->customResponse(['access_token' => $result->access_token], '', 200);
    }

    public function actionGetUserData() {
        $access_token = Yii::$app->request->post('access_token');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with('user')
            ->one();

        return $result->user;
    }

    public function actionChangeUserPhone() {
        $access_token = Yii::$app->request->post('access_token');
        $phone_input = Yii::$app->request->post('phone');
        $sms_input = Yii::$app->request->post('sms');
        $sms_code = mt_rand(1111, 9999);

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if (MobileUser::find()->where(['phone' => $phone_input])->one())
            return $this->customResponse('', 'Phone already reserved by another user', 404);

        if (!$result)
            return $this->customResponse('','User not found', 404);

        if ($phone_input !== null && $sms_input === null) {
                Yii::$app->sms->send(new Sms([
                    'to' => $user->phone,
                    'text' => $sms_code,
                ]));
                $user->sms = $sms_code;
                $user->save();
                return $this->customResponse(['response' => 'An SMS verification code has been sent to your number'], '', 200);

        }

        if ($phone_input !== null && $sms_input !== null) {
            if ($sms_input != $user->sms)
                return $this->customResponse('', 'sms not mach', 404);

            $user->phone = $phone_input;
            if ($user->save())
                return $this->customResponse(['message' => 'Phone number was changed'], '', 200);
            else
                return $this->customResponse('', 'Failed to change number', 404);
        }

        return $this->customResponse('', 'Что то сука пошло не так', 404);
    }

    public function actionChangeUserEmail () {
        $access_token = Yii::$app->request->post('access_token');
        $email = Yii::$app->request->post('email');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        if (!$result)
            return $this->customResponse('', 'User not fond', 404);

        if (MobileUser::find()->where(['email' => $email])->one())
            return $this->customResponse('', 'Email already reserved by another user', 404);

        if ($email !== null) {
            $user = $result->user;
            $user->email = $email;
            if ($user->save())
                return $this->customResponse(['message' => 'Email was changed'], '', 200);
            else
                return $this->customResponse('', 'Email was not saved', 404);
        }
    }

    public function actionChangeRole() {
        $access_token = Yii::$app->request->post('access_token');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        if(!$result)
            return $this->customResponse('', 'User not fond', 404);

        $user =  $result->user;

        $user->role_id == 1 ? $user->role_id = 2: $user->role_id = 1;
        $user->save();

        return $this->customResponse(['role_id' => $user->role_id], '', 200);
    }

    public function actionEditUserData() {
        $access_token = Yii::$app->request->post('access_token');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        if (!$result)
            return $this->customResponse('', 'User not found', 404);

        $user = $result->user;
        if ($user->load(Yii::$app->request->post(), '') && $user->save())
            return $this->customResponse(['message' => 'Data success updated'], '' , 200);
    }

    public function actionAddService() {
        $access_token = Yii::$app->request->post('access_token');
        $services = Yii::$app->request->post('services');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if ($services == null)
            return $this->customResponse('', 'Empty service array', 404);

        if (count($services) > 5 || count($result->user->services) + count($services) > 5)
            return $this->customResponse('', 'Превышенно ограничение по количетву услуг', 404);

        foreach ($services as $service) {
            $temp = new UserService();
            $temp->user_id = $user->id;
            $temp->service_id = $service;
            $temp->save();
        }

        return $this->customResponse(['response' => ''], '', 200);
    }

    public function actionRemoveService() {
        $access_token = Yii::$app->request->post('access_token');
        $services = Yii::$app->request->post('services');

        if ($services == null)
            return $this->customResponse('', 'Empty service array', 404);

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        UserService::deleteAll(
            [
                'AND',
                ['user_id' => $user->id],
                ['service_id' => $services],
            ]
        );

        return $this->customResponse('','', 200);
    }

    public function actionAddFavoriteExecutor() {
        $access_token = Yii::$app->request->post('access_token');
        $executor_id = Yii::$app->request->post('executor_id');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if (
            FavoriteExecutors::find()
                ->where(['executor_id' => $executor_id])
                ->exists()
            )
            return $this->customResponse('', 'User is already exist');

        $favorite = new FavoriteExecutors();
        $favorite->customer_id = $user->id;
        $favorite->executor_id = $executor_id;
        $favorite->save();

        return $this->customResponse('','', 200);
    }

    public function actionRemoveFavoriteExecutor() {
        $access_token = Yii::$app->request->post('access_token');
        $executor_id = Yii::$app->request->post('executor_id');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        FavoriteExecutors::deleteAll([
            'AND',
            ['customer_id' => $user->id],
            ['executor_id' => $executor_id]
        ]);

        return $this->customResponse('','', 200);
    }

    public function actionGetFavoriteExecutors() {
        $access_token = Yii::$app->request->post('access_token');
        $customer_id = Yii::$app->request->post('customer_id');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        $favorite = FavoriteExecutors::find()
            ->select('executor_id')
            ->where(['customer_id' => $user->id])
            ->all();

        return $this->customResponse(['response' => $favorite], '',200);
    }


    public function actionGetBestExecutors() {
        $access_token = Yii::$app->request->post('access_token');
        $city_id = Yii::$app->request->post('city_id');
        $services_id = Yii::$app->request->post('services_id');
        $rate = Yii::$app->request->post('rate');
        $favorite = Yii::$app->request->post('favorite');

        $data = [];

        $token = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $token->user;

        if(null != $favorite) {
            $favorite = FavoriteExecutors::find()
                ->select('executor_id')
                ->where(['customer_id' => $user->id])
                ->asArray()
                ->all();
        }

        $results = (new \yii\db\Query())
            ->select(['mobile_user.id', 'mobile_user.first_name', 'mobile_user.last_name', 'mobile_user.city_id',
                new \yii\db\Expression('AVG(rate) as avg_rate'), 'statistic.completed_orders'])
            ->from('rate')
            ->leftJoin('mobile_user', 'mobile_user.id = rate.user_id')
            ->leftJoin('statistic', 'statistic.user_id = mobile_user.id')
            ->groupBy('mobile_user.id')
            ->orderBy('rate DESC')
            ->andFilterWhere(['city_id' => $city_id])
            ->andFilterWhere(['services_id' => $services_id])
            ->andFilterWhere(['mobile_user.id' => $favorite])
            ->filterHaving(['>=', 'avg_rate', $rate])
            ->all();

        foreach ($results as $result) {
            $result['completed_yours_orders'] = Order::find()
                ->where([
                    'AND',
                    ['customer_id' => $user->id],
                    ['executor_id' => $result['id']]
                ])
                ->andWhere(['status_id' => 4])
                ->count();

            array_push($data, $result);
        }

        return $this->customResponse(['response' => $data], '', 200);
    }

    public function actionGetExecutorProfile() {
        $executor_id = Yii::$app->request->post('executor_id');

        $results = (new \yii\db\Query())
            ->select(['mobile_user.id', 'mobile_user.first_name', 'mobile_user.last_name', 'mobile_user.city_id', 'mobile_user.phone', 'mobile_user.email', 'mobile_user.created_at',
                new \yii\db\Expression('AVG(rate) as avg_rate'), 'statistic.completed_orders', 'statistic.canseled_orders', 'statistic.reports'])
            ->from('rate')
            ->where(['mobile_user.id' => $executor_id])
            ->leftJoin('mobile_user', 'mobile_user.id = rate.user_id')
            ->leftJoin('statistic', 'statistic.user_id = mobile_user.id')
            ->groupBy('mobile_user.id')
            ->orderBy('rate DESC')
            ->all();

        return $this->customResponse(['response' => $results], '', 200);
    }

    public function actionSendPersonalOffer() {
        return "Персональный пуш на объявление конкретному исполнителю?";
    }

    public function actionDeleteUser() {
        $access_token = Yii::$app->request->post('access_token');

        $result = Token::find()
            ->where(['access_token' => $access_token])
            ->with(['user'])
            ->one();

        $user = $result->user;

        if(!$user) {
            return $this->customResponse('', 'User not found', 404);
        } else {
            MobileUser::deleteAll(['id' => $user->id]);
            return $this->customResponse(['message' => 'User was deleted', '', 200]);
        }
    }
}
