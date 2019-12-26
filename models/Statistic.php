<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statistic".
 *
 * @property int $id
 * @property int $user_id
 * @property int $canseled_orders
 * @property int $completed_orders
 * @property int $created_announcements
 * @property int $reports
 */
class Statistic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statistic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'canseled_orders', 'completed_orders', 'created_announcements', 'reports'], 'integer'],
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
            'canseled_orders' => 'Canseled Orders',
            'completed_orders' => 'Completed Orders',
            'created_announcements' => 'Created Announcements',
            'reports' => 'Reports',
        ];
    }
}
