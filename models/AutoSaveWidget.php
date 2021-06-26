<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $name
 * @property int|null $auto_save_materials_id
 * @property string|null $created
 * @property string|null $modified
 */
class AutoSaveWidget extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['auto_save_materials_id'], 'default', 'value' => null],
            [['auto_save_materials_id'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'auto_save_materials_id' => 'Auto Save Materials ID',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
