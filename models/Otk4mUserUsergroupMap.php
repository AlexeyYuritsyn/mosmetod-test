<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_user_usergroup_map".
 *
 * @property int $user_id Foreign Key to #__users.id
 * @property int $group_id Foreign Key to #__usergroups.id
 */
class Otk4mUserUsergroupMap extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_user_usergroup_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'group_id'], 'required'],
            [['user_id', 'group_id'], 'integer'],
            [['user_id', 'group_id'], 'unique', 'targetAttribute' => ['user_id', 'group_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'group_id' => 'Group ID',
        ];
    }
}
