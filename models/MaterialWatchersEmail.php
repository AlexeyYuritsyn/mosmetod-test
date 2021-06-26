<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material_watchers_email".
 *
 * @property int $id
 * @property string|null $email
 */
class MaterialWatchersEmail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_watchers_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'string', 'max' => 255],
            [['in_archive'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'in_archive' => 'In Archive',
        ];
    }
}
