<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material_tags".
 *
 * @property int $id
 * @property string $name
 * @property bool $published
 */
class MaterialTags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {

        return 'material_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['published'], 'boolean'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название тега',
            'published' => 'Опубликован',
        ];
    }
}
