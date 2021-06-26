<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget_youtube".
 *
 * @property int $id
 * @property string|null $youtube_url
 * @property int|null $materials_id
 */
class WidgetYoutube extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget_youtube';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['youtube_url'], 'string'],
            [['materials_id'], 'default', 'value' => null],
            [['materials_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'youtube_url' => 'Youtube Url',
            'materials_id' => 'Materials ID',
        ];
    }
}
