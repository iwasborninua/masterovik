<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "offer".
 *
 * @property int $id
 * @property int $announcement_id id обьявления
 * @property int $executor_id
 * @property int $price
 * @property int $term
 */
class Offer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['announcement_id', 'executor_id', 'price', 'term'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'announcement_id' => 'Announcement ID',
            'executor_id' => 'Executor ID',
            'price' => 'Price',
            'term' => 'Term',
        ];
    }
}
