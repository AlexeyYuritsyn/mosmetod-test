<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget_map".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $lat
 * @property string|null $lng
 * @property string|null $name
 * @property int|null $auto_save_materials_id
 */
class AutoSaveWidgetMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'lat', 'lng', 'name'], 'string'],
            [['auto_save_materials_id'], 'default', 'value' => null],
            [['auto_save_materials_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'name' => 'Name',
            'auto_save_materials_id' => 'Auto Save Materials ID',
        ];
    }
}
