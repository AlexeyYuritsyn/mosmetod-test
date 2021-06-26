<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscribers_send_log".
 *
 * @property int $id
 * @property string|null $time_send
 * @property int|null $user_id
 * @property bool|null $result_send
 */
class SubscribersSendLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribers_send_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time_send'], 'safe'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['result_send'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_send' => 'Time Send',
            'user_id' => 'User ID',
            'result_send' => 'Result Send',
        ];
    }
}
