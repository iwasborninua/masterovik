<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_conditions".
 *
 * @property int $id
 * @property int $term
 * @property int $price
 * @property int $order_id
 * @property int $created_at
 * @property int $who_send
 */
class OrderConditions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_conditions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['term', 'price', 'order_id', 'created_at', 'who_send'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'term' => 'Term',
            'price' => 'Price',
            'order_id' => 'Order ID',
            'created_at' => 'Created At',
            'who_send' => 'Who Send',
        ];
    }
}
