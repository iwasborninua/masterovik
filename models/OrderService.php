<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_service".
 *
 * @property int $id
 * @property int $order_id
 * @property int $service_id
 */
class OrderService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'service_id'], 'integer'],
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
            'service_id' => 'Service ID',
        ];
    }
}
