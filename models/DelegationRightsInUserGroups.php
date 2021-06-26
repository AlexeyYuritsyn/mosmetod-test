<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delegation_rights_in_user_groups".
 *
 * @property int $id
 * @property int|null $users_id
 * @property int|null $user_groups_id
 */
class DelegationRightsInUserGroups extends \yii\db\ActiveRecord
{
    public $user_groups_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delegation_rights_in_user_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
        ];
    }
}
