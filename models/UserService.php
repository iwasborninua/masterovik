<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_service".
 *
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 */
class UserService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'service_id'], 'integer'],
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
            'service_id' => 'Service ID',
        ];
    }
}
