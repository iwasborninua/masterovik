<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "token".
 *
 * @property int $id
 * @property int $user_id
 * @property string $access_token
 * @property string $refresh_token
 * @property int $expires
 * @property int $created_at
 *
 * @property MobileUser $user
 */
class Token extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'expires', 'created_at'], 'integer'],
            [['access_token', 'refresh_token'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => MobileUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'expires' => 'Expires',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(MobileUser::className(), ['id' => 'user_id']);
    }
}
