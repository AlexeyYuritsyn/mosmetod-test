<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_categories".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property int $parent
 * @property int $extraFieldsGroup
 * @property int $published
 * @property int $access
 * @property int $ordering
 * @property string $image
 * @property string $params
 * @property int $trash
 * @property string $plugins
 * @property string $language
 */
class Otk4mK2Categories extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'description', 'extraFieldsGroup', 'image', 'params', 'plugins', 'language'], 'required'],
            [['description', 'params', 'plugins'], 'string'],
            [['parent', 'extraFieldsGroup', 'published', 'access', 'ordering', 'trash'], 'integer'],
            [['name', 'alias', 'image'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'description' => 'Description',
            'parent' => 'Parent',
            'extraFieldsGroup' => 'Extra Fields Group',
            'published' => 'Published',
            'access' => 'Access',
            'ordering' => 'Ordering',
            'image' => 'Image',
            'params' => 'Params',
            'trash' => 'Trash',
            'plugins' => 'Plugins',
            'language' => 'Language',
        ];
    }
}
