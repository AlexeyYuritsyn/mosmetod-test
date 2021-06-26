<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_tags_xref".
 *
 * @property int $id
 * @property int $tagID
 * @property int $itemID
 */
class Otk4mK2TagsXref extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_tags_xref';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tagID', 'itemID'], 'required'],
            [['tagID', 'itemID'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tagID' => 'Tag ID',
            'itemID' => 'Item ID',
        ];
    }
}
