<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $status_id
 * @property int $customer_id
 * @property int $executor_id
 * @property int $start_date
 * @property int $end_date
 * @property int $price
 * @property int $comission
 * @property int $city_id
 * @property string $place_of_work
 * @property string $description
 * @property int $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_id', 'customer_id', 'executor_id', 'start_date', 'end_date', 'price', 'comission', 'city_id', 'created_at'], 'integer'],
            [['place_of_work'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_id' => 'Status ID',
            'customer_id' => 'Customer ID',
            'executor_id' => 'Executor ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'price' => 'Price',
            'comission' => 'Comission',
            'city_id' => 'City ID',
            'place_of_work' => 'Place Of Work',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }

    public function getServices() {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
            ->viaTable('order_service', ['order_id' => 'id']);
    }

    public function getCustomer() {
        return $this->hasOne(MobileUser::className(), ['id' => 'customer_id']);
    }

    public function getExecutor() {
        return $this->hasOne(MobileUser::className(), ['id' => 'executor_id']);
    }

    public function getCondition() {
        return $this->hasOne(OrderConditions::className(), ['order_id' => 'id']);
    }
}
