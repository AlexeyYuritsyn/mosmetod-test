<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_groups_rights_material_categories".
 *
 * @property int $id
 * @property int $user_groups_id
 * @property int $category_id
 */
class UserGroupsRightsMaterialCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_groups_rights_material_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_groups_id', 'category_id'], 'required'],
            [['user_groups_id', 'category_id'], 'default', 'value' => null],
            [['user_groups_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_groups_id' => 'User Groups ID',
            'category_id' => 'Category ID',
        ];
    }
}
