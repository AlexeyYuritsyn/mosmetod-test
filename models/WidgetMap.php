<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget_map".
 *
 * @property int $id
 * @property int|null $widget_id
 * @property string|null $title
 * @property string|null $lat
 * @property string|null $lng
 */
class WidgetMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['widget_id','materials_id', 'name'], 'default', 'value' => null],
            [['widget_id','materials_id'], 'integer'],
            [['title', 'lat', 'lng', 'name'], 'string'],
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
            'widget_id' => 'Widget ID',
            'title' => 'Title',
            'lat' => 'Lat',
            'lng' => 'Lng',
        ];
    }
}
