<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_widgetkit_widget".
 *
 * @property int $id
 * @property string $type
 * @property string $style
 * @property string $name
 * @property string $content
 * @property string $created
 * @property string $modified
 */
class Otk4mWidgetkitWidget extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_widgetkit_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'style', 'name', 'content', 'created', 'modified'], 'required'],
            [['content'], 'string'],
            [['created', 'modified'], 'safe'],
            [['type', 'style', 'name'], 'string', 'max' => 255],
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
            'style' => 'Style',
            'name' => 'Name',
            'content' => 'Content',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
