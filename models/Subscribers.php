<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscribers".
 *
 * @property int $id
 * @property int $email
 * @property string $time_created
 * @property int $status
 * @property string $time_send
 */
class Subscribers extends \yii\db\ActiveRecord
{

    public $school;
    public $is_deleted;

    const CONFIRMATION_NOT_SENT = '0';
    const CONFIRMATION_SENT = '1';
    const CONFIRMATION = '2';

    public static $statuses = [
        self::CONFIRMATION_NOT_SENT =>'Подтверждение не отправлено',
        self::CONFIRMATION_SENT =>'Подтверждение отправлено',
        self::CONFIRMATION =>'Подтверждено'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'time_created'], 'required'],
            ['email', 'unique'],
            [['status'], 'default', 'value' => 0],
            [['status'], 'integer'],
            [['send_notification_immediately'], 'boolean'],
            [['time_created', 'time_send'], 'safe'],
            [['email'], 'string', 'max' => 255],
            ['email', 'match', 'pattern' => '/(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,6})$/i', 'message' => 'Вы записали не корректный email'],

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
            'time_created' => 'Дата создания email',
            'status' => 'Статус',
            'time_send' => 'Дата отправки подтверждения',
            'school' => 'Название школы',
            'is_deleted' => 'Удалить',
            'send_notification_immediately' => 'Отправить уведомления сразу после публикации',

        ];
    }
}
