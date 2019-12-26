<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "offer_history".
 *
 * @property int $id
 * @property int $term
 * @property int $price
 * @property int $create_at
 * @property string $isExecutor
 */
class OfferHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['term', 'price', 'create_at'], 'integer'],
            [['isExecutor'], 'string', 'max' => 10],
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
            'create_at' => 'Create At',
            'isExecutor' => 'Is Executor',
        ];
    }
}
