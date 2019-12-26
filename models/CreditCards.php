<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "credit_cards".
 *
 * @property int $id
 * @property int $user_id
 * @property string $card
 */
class CreditCards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credit_cards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['card'], 'string', 'max' => 50],
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
            'card' => 'Card',
        ];
    }
}
