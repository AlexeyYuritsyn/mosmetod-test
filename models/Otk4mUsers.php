<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_users".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $usertype
 * @property int $block
 * @property int $sendEmail
 * @property string $registerDate
 * @property string $lastvisitDate
 * @property string $activation
 * @property string $params
 * @property string $lastResetTime Date of last password reset
 * @property int $resetCount Count of password resets since lastResetTime
 */
class Otk4mUsers extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['block', 'sendEmail', 'resetCount'], 'integer'],
            [['registerDate', 'lastvisitDate', 'lastResetTime'], 'safe'],
            [['params'], 'required'],
            [['params'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 150],
            [['email', 'password', 'activation'], 'string', 'max' => 100],
            [['usertype'], 'string', 'max' => 25],
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
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'usertype' => 'Usertype',
            'block' => 'Block',
            'sendEmail' => 'Send Email',
            'registerDate' => 'Register Date',
            'lastvisitDate' => 'Lastvisit Date',
            'activation' => 'Activation',
            'params' => 'Params',
            'lastResetTime' => 'Last Reset Time',
            'resetCount' => 'Reset Count',
        ];
    }
}
