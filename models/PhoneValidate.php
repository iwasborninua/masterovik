<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "phone_validate".
 *
 * @property int $id
 * @property string $phone
 * @property string $sms
 */
class PhoneValidate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'phone_validate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'string', 'max' => 15],
            [['sms'], 'string', 'max' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'sms' => 'Sms',
        ];
    }
}
