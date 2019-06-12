<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "mobile_user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property int $city_id
 * @property string $company_name
 * @property string $company_description
 * @property string $invite
 * @property int $invited_by id пользователя который пригласил
 * @property int $balance
 * @property int $role_id
 * @property int $statistics_id
 * @property string $email
 *
 * @property City $city
 * @property Role $role
 * @property Token[] $tokens
 */
class MobileUser extends \yii\db\ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function tableName()
    {
        return 'mobile_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['city_id', 'invited_by', 'balance', 'role_id', 'statistics_id'], 'integer'],
            [['first_name', 'last_name', 'company_name'], 'string', 'max' => 25],
            [['phone'], 'string', 'max' => 11],
            [['company_description'], 'string', 'max' => 95],
            [['invite'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 30],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'city_id' => 'City ID',
            'company_name' => 'Company Name',
            'company_description' => 'Company Description',
            'invite' => 'Invite',
            'invited_by' => 'Invited By',
            'balance' => 'Balance',
            'role_id' => 'Role ID',
            'statistics_id' => 'Statistics ID',
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    public function setToken($id) {
        $model = new Token();
        $model->user_id = $id;
        $model->access_token = Yii::$app->security->generateRandomString();
        $model->refresh_token = Yii::$app->security->generateRandomString();
        $model->expires = mktime(0, 0, 0, date("m") + 1);
        $model->created_at = mktime();
        return $model->save();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $model = Token::find()
            ->where(['access_token' => $token])
            ->with(['user'])
            ->one();

        if ($model === null) {
            throw new \yii\web\UnauthorizedHttpException('Wrong authorization token');
        }

        if ($model->expires < mktime()) {
            return Yii::$app->response->statusCode = "426";
        }

        return $model->user;
    }
}
