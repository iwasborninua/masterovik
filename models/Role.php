<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property int $id
 * @property string $role
 *
 * @property MobileUser[] $mobileUsers
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobileUsers()
    {
        return $this->hasMany(MobileUser::className(), ['role_id' => 'id']);
    }
}
