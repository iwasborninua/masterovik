<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "offer_ban".
 *
 * @property int $id
 * @property int $executor_id
 * @property int $announcement_id
 * @property int $expired
 */
class OfferBan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer_ban';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['executor_id', 'announcement_id', 'expired'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'executor_id' => 'Executor ID',
            'announcement_id' => 'Announcement ID',
            'expired' => 'Expired',
        ];
    }
}
