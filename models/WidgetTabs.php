<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget_tabs".
 *
 * @property int $id
 * @property int|null $widget_id
 * @property string|null $title
 * @property string|null $content
 */
class WidgetTabs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget_tabs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['widget_id'], 'default', 'value' => null],
            [['widget_id','materials_id'], 'integer'],
            [['title', 'content'], 'string'],
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
            'content' => 'Content',
        ];
    }
}
