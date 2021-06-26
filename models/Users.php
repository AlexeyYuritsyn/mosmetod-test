<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $second_name
 * @property string $first_name
 * @property string $third_name
 * @property int $role
 * @property bool $in_archive
 * @property string $register_date
 * @property string $lastvisit_date
 */
class Users extends \yii\db\ActiveRecord
{
    public $fio;
    public $new_password;
    public $repeat_password;

//    public $accessToken;
    public $position_and_direction_in_users;

//    public $path_image_input;

    const ROLE_ADMIN = '1';
    const ROLE_MODERATOR = '2';
//    const ROLE_CORRECTOR = '3';
    const ROLE_SENIOR_METHODIST = '3';
    const ROLE_METHODIST = '4';
    const ROLE_USER = '5';

    public static $roles = [
        self::ROLE_ADMIN =>'Администратор',
        self::ROLE_MODERATOR =>'Модератор',
//        self::ROLE_CORRECTOR =>'Корректор',
        self::ROLE_SENIOR_METHODIST =>'Старший методист',
        self::ROLE_METHODIST =>'Методист',
        self::ROLE_USER =>'Пользователь',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['email', 'password', 'role'], 'required'],
            [['role'], 'integer'],//, 'id'
            [['email', 'password', 'new_password_email', 'second_name', 'first_name', 'third_name', 'guid'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 15],
//            [['path_image_input'], 'file', 'maxFiles' => 1],
            [['description','image','basic_information'], 'string'],
            [['new_password','repeat_password'],'custom_function_validation_chang_password','on'=>'change_password_user'],
            [['new_password','repeat_password'],'custom_function_validation_chang_new_password','on'=>'change_new_password_user'],
            [['new_password','repeat_password'],'required','on'=>'change_new_password_user'],
            ['email', 'match', 'pattern' => '/(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,6})$/i', 'message' => 'Вы записали не корректный email'],
            [['email'], 'unique'],
            [['in_archive','not_send_email'], 'boolean'],
            [['register_date', 'last_visit_date'], 'safe'],

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
            'password' => 'Пароль',
            'role' => 'Основаня роль',
            'in_archive' => 'Заблокирован',//'Заблокировать пользователя',
            'fio' => 'ФИО',
            'second_name' => 'Фамилия',
            'first_name' => 'Имя',
            'third_name' => 'Отчество',
            'new_password' => 'Новый пароль',
            'repeat_password' => 'Повторите пароль',
            'register_date' => 'Дата регистрации',
            'last_visit_date' => 'Дата входа',
            'description' => 'Описание пользователя',
            'ip' => 'Последнее IP',
//            'path_image_input' => 'Фото пользователя',
            'image' => 'Фото пользователя',
            'guid' => 'Идентификатор пользователя',
            'position_and_direction_in_users' => 'Должность или направление',
            'accessToken' => 'Токен сессии',
            'not_send_email' => 'Не отправлять Email',
            'basic_information' => 'Основная информация'
        ];
    }

    function custom_function_validation_chang_password($attribute)
    {
        if(($attribute == 'repeat_password' || $attribute == 'new_password') && $this->new_password != $this->repeat_password)
        {
            $this->addError($attribute, 'Пароли не совпадают');
        }

    }

    function custom_function_validation_chang_new_password($attribute)
    {
        if($attribute == 'repeat_password' && mb_strlen($this->repeat_password,'UTF-8') < 6)
        {
            $this->addError($attribute, 'Пароль должен быть не меньше 6 символов');
        }

        if($attribute == 'new_password' && mb_strlen($this->new_password,'UTF-8') < 6)
        {
            $this->addError($attribute, 'Пароль должен быть не меньше 6 символов');
        }

        if(($attribute == 'repeat_password' || $attribute == 'new_password') && $this->new_password != $this->repeat_password)
        {
            $this->addError($attribute, 'Пароли не совпадают');
        }
    }
}
