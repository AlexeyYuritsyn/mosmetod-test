<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $email;
    public $password;
    public $second_name;
    public $first_name;
    public $third_name;
    public $role;
    public $in_archive;
    public $register_date;
    public $last_visit_date;

    public $description;
    public $image;
    public $ip;
    public $guid;
    public $new_password_email;
    public $not_send_email;
    public $basic_information;


    public $authKey;
    public $accessToken;

    private static $users = [
//        '100' => [
//            'id' => '100',
//            'username' => 'admin',
//            'password' => 'admin',
//            'authKey' => 'test100key',
//            'accessToken' => '100-token',
//        ],
//        '101' => [
//            'id' => '101',
//            'username' => 'demo',
//            'password' => 'demo',
//            'authKey' => 'test101key',
//            'accessToken' => '101-token',
//        ],
    ];


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = Users::find()->where(['id'=>$id])->one();
        return isset($user) ? new static($user->getAttributes()) : null;
//        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }
    /**
     * {@inheritdoc}
     */
    public static function findByEmail($email)
    {
        $user = Users::find()->where(['lower(email)'=>mb_strtolower($email),'in_archive'=>false])->one();

        if($user)
        {
            return new static($user->getAttributes());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
//    public static function findByUsername($username)
//    {
//        foreach (self::$users as $user) {
//            if (strcasecmp($user['username'], $username) === 0) {
//                return new static($user);
//            }
//        }
//
//        return null;
//    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return  password_verify($password, $this->password);  //  $this->password === $password;
    }
}
