<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "duplication_materials".
 *
 * @property int $id
 * @property string|null $materials_guid
 * @property int|null $time_open_material
 * @property int|null $users_id
 */
class DuplicationMaterials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'duplication_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_open_material', 'users_id'], 'default', 'value' => null],
            [['time_open_material', 'users_id'], 'integer'],
            [['materials_guid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'materials_guid' => 'Materials Guid',
            'time_open_material' => 'Time Open Material',
            'users_id' => 'Users ID',
        ];
    }
}
