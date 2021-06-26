<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $content
 * @property string $created
 * @property string $modified
 */
class Widget extends \yii\db\ActiveRecord
{
    public $path_widget_gallery;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['name'], 'string'],
            [['path_widget_gallery'], 'file'],
            [['materials_id'], 'integer'],
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
            'content' => 'Content',
            'created' => 'Created',
            'modified' => 'Modified',
            'path_widget_gallery' => false,
        ];
    }
}
