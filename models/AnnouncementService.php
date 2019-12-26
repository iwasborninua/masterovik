<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "announcement_service".
 *
 * @property int $id
 * @property int $announcement_id
 * @property int $service_id
 */
class AnnouncementService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'announcement_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['announcement_id', 'service_id'], 'integer'],
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
            'service_id' => 'Service ID',
        ];
    }
}
