<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "announcement".
 *
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property int $term
 * @property int $price
 * @property int $status_id
 * @property int $created_at
 * @property string $place_of_work
 * @property string $description
 * @property string $description_full
 */
class Announcement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'city_id', 'term', 'price', 'status_id'], 'integer'],
            [['description_full'], 'string'],
            [['place_of_work', 'description'], 'string', 'max' => 250],
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
            'city_id' => 'City ID',
            'term' => 'Term',
            'price' => 'Price',
            'status_id' => 'Status ID',
            'place_of_work' => 'Place Of Work',
            'description' => 'Description',
            'description_full' => 'Description Full',
            'created_at' => 'Created',
        ];
    }

    public function getUser() {
        return $this->hasOne(MobileUser::className(), ['id' => 'user_id']);
    }

    public function getCategory() {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
            ->viaTable('announcement_service', ['announcement_id' => 'id']);
    }

    public function getOffer() {
        return $this->hasOne(Offer::className(), ['announcement_id' => 'id']);
    }
}
