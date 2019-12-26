<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_change_conditions".
 *
 * @property int $id
 * @property int $order_id
 * @property int $term
 * @property int $price
 * @property string $isExecutor
 * @property int $created_at
 * @property string $customer_consent
 * @property string $executor_consent
 */
class OrderChangeConditions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_change_conditions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'term', 'price', 'created_at'], 'integer'],
            [['isExecutor', 'customer_consent', 'executor_consent'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'term' => 'Term',
            'price' => 'Price',
            'isExecutor' => 'Is Executor',
            'created_at' => 'Created At',
            'customer_consent' => 'Customer Consent',
            'executor_consent' => 'Executor Consent',
        ];
    }
}
