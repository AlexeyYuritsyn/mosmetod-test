<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_users".
 *
 * @property int $id
 * @property int $userID
 * @property string $userName
 * @property string $gender
 * @property string $description
 * @property string $image
 * @property string $url
 * @property int $group
 * @property string $plugins
 * @property string $ip
 * @property string $hostname
 * @property string $notes
 */
class Otk4mK2Users extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userID', 'description', 'plugins', 'ip', 'hostname', 'notes'], 'required'],
            [['userID', 'group'], 'integer'],
            [['gender', 'description', 'plugins', 'notes'], 'string'],
            [['userName', 'image', 'url', 'hostname'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userID' => 'User ID',
            'userName' => 'User Name',
            'gender' => 'Gender',
            'description' => 'Description',
            'image' => 'Image',
            'url' => 'Url',
            'group' => 'Group',
            'plugins' => 'Plugins',
            'ip' => 'Ip',
            'hostname' => 'Hostname',
            'notes' => 'Notes',
        ];
    }
}
