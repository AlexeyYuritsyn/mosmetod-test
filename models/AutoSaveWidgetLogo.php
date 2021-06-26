<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget_logo".
 *
 * @property int $id
 * @property string|null $image
 * @property int|null $type
 * @property int|null $auto_save_materials_id
 * @property string|null $created
 * @property string|null $modified
 */
class AutoSaveWidgetLogo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget_logo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image','url'], 'string'],
            [['type', 'auto_save_materials_id'], 'default', 'value' => null],
            [['type', 'auto_save_materials_id'], 'integer'],
            [['created', 'modified'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Image',
            'type' => 'Type',
            'auto_save_materials_id' => 'Auto Save Materials ID',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
