<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "send_email_material_published".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $materials_id
 * @property string|null $time_created
 */
class SendEmailMaterialPublished extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'send_email_material_published';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'materials_id'], 'default', 'value' => null],
            [['user_id', 'materials_id'], 'integer'],
            [['time_created'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Users ID',
            'materials_id' => 'Materials ID',
            'time_created' => 'Time Created',
        ];
    }
}
