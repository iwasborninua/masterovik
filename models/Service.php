<?php

namespace app\models;

use Yii;
use yii\debug\models\Router;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name
 * @property string $logo
 */
class Service extends \yii\db\ActiveRecord
{
    public $image;

    public function upload()
    {
        if($this->validate()) {
            $image_name = $this->image->baseName . "_" . uniqid() . "." . $this->image->extension;
            if ($this->image->saveAs(Yii::getAlias('@webroot') . '/uploads/' . $image_name))
                return $image_name;
            else
                return 'Image not saved';
        }
    }

    public function getUsers()
    {
        return $this->hasMany(MobileUser::className(), ['id' => 'user_id'])->via('UserService');
    }



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 100],
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
            'logo' => 'Logo',
        ];
    }

    public function getAnnouncements () {
        return $this->hasMany(Announcement::className(), ['id' => 'announcement_id'])
            ->viaTable('announcement_service', ['service_id' => 'id']);
    }
}
