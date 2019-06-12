<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $city
 *
 * @property MobileUser[] $mobileUsers
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'City',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobileUsers()
    {
        return $this->hasMany(MobileUser::className(), ['city_id' => 'id']);
    }
}
