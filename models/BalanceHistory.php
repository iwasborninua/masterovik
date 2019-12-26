<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "balance_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $card_id
 * @property int $created_at
 * @property int $sum
 * @property int $balance
 * @property string $operation
 */
class BalanceHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balance_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'card_id', 'created_at', 'sum', 'balance'], 'integer'],
            [['operation'], 'string', 'max' => 100],
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
            'card_id' => 'Card ID',
            'created_at' => 'Created At',
            'sum' => 'Sum',
            'balance' => 'Balance',
            'operation' => 'Operation',
        ];
    }

}
