<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_tags".
 *
 * @property int $id
 * @property string $name
 * @property int $published
 */
class Otk4mK2Tags extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['published'], 'integer'],
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
            'name' => 'Name',
            'published' => 'Published',
        ];
    }
}
