<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "service_offered".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $reason
 */
class ServiceOffered extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_offered';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 100],
            [['description', 'reason'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'reason' => 'Reason',
        ];
    }
}
