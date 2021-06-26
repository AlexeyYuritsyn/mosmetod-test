<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_user_groups".
 *
 * @property int $id
 * @property string $name
 * @property string $permissions
 */
class Otk4mK2UserGroups extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_user_groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'permissions'], 'required'],
            [['permissions'], 'string'],
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
            'permissions' => 'Permissions',
        ];
    }
}
