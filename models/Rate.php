<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rate".
 *
 * @property int $id
 * @property int $order_id ссылка на обьявление
 * @property int $user_id
 * @property int $rate 1-5 звезд
 * @property string $review отзыв заказчика
 * @property int $create_at
 */
class Rate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'rate', 'create_at'], 'integer'],
            [['review'], 'string'],
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
            'user_id' => 'User ID',
            'rate' => 'Rate',
            'review' => 'Review',
            'create_at' => 'Create At',
        ];
    }

    public function getOrder() {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getCustomer() {
        return $this->hasOne(MobileUser::className(), ['id' => 'customer_id'])
            ->viaTable('order', ['id' => 'order_id']);
    }
}
