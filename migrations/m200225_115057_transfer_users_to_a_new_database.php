<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200221_114355_transfer_users_to_a_new_database
 */
class m200225_115057_transfer_users_to_a_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->integer(11),
            'email' => $this->string(255),
            'password' => $this->string(255),
            'second_name' => $this->string(255),
            'first_name' => $this->string(255),
            'third_name' => $this->string(255),
            'role' => $this->integer(11),
            'in_archive' => $this->boolean()->defaultValue(false),
            'register_date' => $this->dateTime(),
            'last_visit_date' => $this->dateTime(),
            'description' => $this->text(),
            'image' => $this->text(),
            'ip' => $this->string(15),
            'guid' => $this->string(255),
            'new_password_email' => $this->string(255),
            'not_send_email' => $this->boolean()->defaultValue(false),
            'basic_information' => $this->text()
        ]);

        $this->createTable('role_users_in_user_groups', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer(11),
            'user_groups_id' => $this->integer(11),
//            'role' => $this->integer(11)
        ]);

        $otk4m_users = \app\models\Otk4mUsers::find()->all();

        $users_id_seq = 1;
        foreach($otk4m_users as $value)
        {
            $otk4m_k2_users = \app\models\Otk4mK2Users::find()->where(['userID'=>$value['id']])->one();

            if(!is_null($otk4m_k2_users))
            {
                $model =  new \app\models\Users();
//            $string = trim($value['name']);
                $fio = preg_split("/ /",trim($value['name']));

                $model['id'] = $value['id'];
                $model['email'] = $value['email'];
                $model['password'] = '0';
                $model['second_name'] = isset($fio[0])?$fio[0]:null;
                $model['first_name'] = isset($fio[1])?$fio[1]:null;
                $model['third_name'] = isset($fio[2])?$fio[2]:null;

                if(preg_match("/@mosmetod.ru/i", $value['email']))
                {
                    $model['role'] = \app\models\Users::ROLE_METHODIST;

                    $otk4m_user_usergroup_map = \app\models\Otk4mUserUsergroupMap::find()->where(['user_id'=>$value['id']])->all();
                    $usergroup_map_array = [];

                    if(!empty($otk4m_user_usergroup_map))
                    {
                        foreach($otk4m_user_usergroup_map as $map_value)
                        {
                            $usergroup_map_array[] = $map_value['group_id'];
                        }
                    }

                    if(in_array('7',$usergroup_map_array))
                    {
                        $model['role'] = \app\models\Users::ROLE_MODERATOR;
                    }

                    if(in_array('8',$usergroup_map_array))
                    {
                        $model['role'] = \app\models\Users::ROLE_ADMIN;
                    }
                }
                else
                {
                    $model['role'] = \app\models\Users::ROLE_USER;
                }

                $model['in_archive'] = $value['block'];
                $model['description'] = isset($otk4m_k2_users['description'])?$otk4m_k2_users['description']:'';
//            $model['image'] = isset($otk4m_k2_users['image'])?$otk4m_k2_users['image']:null;
                $model['image'] = null;
                $model['ip'] = isset($otk4m_k2_users['ip'])?$otk4m_k2_users['ip']:'';
                $model['guid'] = md5(rand(1,2147483647).' '.rand(1,2147483647).' '.time());


                $registerDate = null;

                if($value['registerDate'] != '0000-00-00 00:00:00')
                {
                    $registerDate = $value['registerDate'];
                }
                $model['register_date'] = $registerDate;


                $lastvisitDate = null;

                if($value['lastvisitDate'] != '0000-00-00 00:00:00')
                {
                    $lastvisitDate = $value['lastvisitDate'];
                }

                $model['last_visit_date'] = $lastvisitDate;

                if($model->save(false))
                {
                    $user_groups = \app\models\UserGroups::find()->where(['id'=>$otk4m_k2_users['group']])->count();

                    if($model['role'] == \app\models\Users::ROLE_METHODIST && $user_groups > 0)
                    {
                        $role_users_in_user_groups = new \app\models\RoleUsersInUserGroups();

                        $role_users_in_user_groups['users_id'] = $model['id'];
                        $role_users_in_user_groups['user_groups_id'] = $otk4m_k2_users['group'];
//                    $role_users_in_user_groups['role'] = \app\models\RoleUsersInUserGroups::ROLE_USERS_GROUP_METHODIST;

                        if($role_users_in_user_groups->save())
                        {
                            echo 'Роль на пользователя в группе пользователей была создана у пользователя '.$value['name'].chr(10).chr(13);
                        }
                        else
                        {
                            var_dump($model->getErrors());
                            throw new HttpException(500 ,'Ошибка при сохранении роли пользователя в группе пользоватлей. id пользователя = '.$model['id']);
                        }
                    }

                    echo 'Пользователь создан '.$value['name'].chr(10).chr(13);
                    if($value['id'] >  $users_id_seq)
                    {
                        $users_id_seq = $value['id'];
                    }
                }
                else
                {
                    var_dump($model->getErrors());

                    throw new HttpException(500 ,'Ошибка при сохранении пользователя. id пользователя = '.$model['id']);
                }
            }
        }

        $this->execute('ALTER TABLE users ADD PRIMARY KEY (id)');
        $this->execute('create sequence users_id_seq start '.($users_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass)");

        $model =  new \app\models\Users();

        $model['email'] = 'i25585@yandex.ru';
        $model['password'] = password_hash('49oSnb',PASSWORD_DEFAULT);
        $model['second_name'] = 'Перемышленников';
        $model['first_name'] = 'Владимир';
        $model['third_name'] = 'Сергеевич';
        $model['role'] = \app\models\Users::ROLE_ADMIN;
        $model['in_archive'] = false;
        $model['register_date'] = new \yii\db\Expression('NOW()');
        $model['last_visit_date'] = null;
        $model['guid'] = md5(rand(1,2147483647).' '.rand(1,2147483647).' '.time());
        $model->save(false);



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
        $this->execute('drop sequence users_id_seq');
        $this->dropTable('role_users_in_user_groups');
    }
}
