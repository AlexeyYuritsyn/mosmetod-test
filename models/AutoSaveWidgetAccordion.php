<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget_accordion".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property int|null $auto_save_materials_id
 */
class AutoSaveWidgetAccordion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget_accordion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'string'],
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
            'content' => 'Content',
            'auto_save_materials_id' => 'Auto Save Materials ID',
        ];
    }
}
