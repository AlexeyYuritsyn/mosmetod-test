<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "role_users_in_user_groups".
 *
 * @property int $id
 * @property int $users_id
 * @property int $user_groups_id
 * @property int $role
 */
class RoleUsersInUserGroups extends \yii\db\ActiveRecord
{

    public $user_groups_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_users_in_user_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['users_id', 'user_groups_id'], 'required'],
            [['users_id', 'user_groups_id'], 'default', 'value' => null],
            [['users_id', 'user_groups_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'users_id' => 'Users ID',
            'user_groups_id' => 'User Groups ID',
            'role' => 'Role',
        ];
    }
}
