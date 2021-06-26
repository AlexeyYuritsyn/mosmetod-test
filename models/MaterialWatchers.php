<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material_watchers".
 *
 * @property int $id
 * @property int|null $material_id
 * @property int|null $material_watchers_email_id
 */
class MaterialWatchers extends \yii\db\ActiveRecord
{
    public $material_watchers_email;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_watchers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_id', 'material_watchers_email_id'], 'default', 'value' => null],
            [['material_id', 'material_watchers_email_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_id' => 'Material ID',
            'material_watchers_email_id' => 'Users ID',
        ];
    }
}
