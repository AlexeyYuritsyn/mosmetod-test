<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget_youtube".
 *
 * @property int $id
 * @property string|null $youtube_url
 * @property int|null $auto_save_materials_id
 */
class AutoSaveWidgetYoutube extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget_youtube';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['youtube_url'], 'string'],
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
            'youtube_url' => 'Youtube Url',
            'auto_save_materials_id' => 'Auto Save Materials ID',
        ];
    }
}
