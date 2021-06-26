<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_groups".
 *
 * @property int $id
 * @property string $name
 * @property string $permissions
 */
class UserGroups extends \yii\db\ActiveRecord
{
    public $role_senior_methodist = [];
    public $role_methodist = [];
    public $delegation_rights = [];

    public $role_users_in_user_groups_users_id;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['in_archive','admin_group'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название группы',
            'in_archive' => 'Группа удалена',
            'role_users_in_user_groups_users_id' => 'Человек в группе',
        ];
    }
}
