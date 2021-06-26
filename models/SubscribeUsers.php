<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscribe_users".
 *
 * @property int $id
 * @property string $email
 * @property int $time_created
 * @property int $status
 * @property int $time_send
 */
class SubscribeUsers extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribe_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'time_created', 'status'], 'required'],
            [['time_created', 'status', 'time_send'], 'integer'],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'time_created' => 'Time Created',
            'status' => 'Status',
            'time_send' => 'Time Send',
        ];
    }
}
