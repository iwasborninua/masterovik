<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "disput_feedback".
 *
 * @property int $id
 * @property int $rate_id
 * @property string $text
 */
class DisputFeedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'disput_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rate_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rate_id' => 'Rate ID',
            'text' => 'Text',
        ];
    }

    public function getRate() {
        return $this->hasOne(Rate::className(), ['id' => 'rate_id']);
    }
}
