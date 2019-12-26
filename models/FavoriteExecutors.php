<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "favorite_executors".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $executor_id
 */
class FavoriteExecutors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorite_executors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'executor_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'executor_id' => 'Executor ID',
        ];
    }
}
