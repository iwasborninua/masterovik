<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report".
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property int $create_at
 */
class Report extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id', 'create_at'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 200],
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
            'title' => 'Title',
            'description' => 'Description',
            'create_at' => 'Create At',
        ];
    }
    public function GetUser() {
        return $this->hasOne(MobileUser::className(), ['id' => 'user_id']);
    }

    public function getOrder() {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
