<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uploads_file_material_log".
 *
 * @property int $id
 * @property string|null $guid_material
 * @property string|null $url_file_material
 * @property int|null $user_id
 * @property string|null $date_uploads
 */
class UploadsFileMaterialLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uploads_file_material_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['url_file_material'], 'string'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['date_uploads'], 'safe'],
            [['guid_material'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'guid_material' => 'Guid Material',
            'url_file_material' => 'Url File Material',
            'user_id' => 'User ID',
            'date_uploads' => 'Date Uploads',
        ];
    }
}
