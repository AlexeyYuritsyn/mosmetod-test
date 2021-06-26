<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material_tags_in_materials".
 *
 * @property int $id
 * @property int $material_tags_id
 * @property int $materials_id
 */
class MaterialTagsInMaterials extends \yii\db\ActiveRecord
{
    public $material_tags_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_tags_in_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_tags_id', 'materials_id'], 'required'],
            [['material_tags_id', 'materials_id'], 'default', 'value' => null],
            [['material_tags_id', 'materials_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_tags_id' => 'Material Tags ID',
            'materials_id' => 'Materials ID',
        ];
    }
}
