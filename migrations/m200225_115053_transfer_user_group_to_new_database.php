<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200225_115053_transfer_user_group_to_new_database
 */
class m200225_115053_transfer_user_group_to_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_groups', [
            'id' => $this->integer(11),
            'name' => $this->string(255),
//            'permissions' => $this->text(),
            'in_archive' => $this->boolean()->defaultValue(false),
            'admin_group' => $this->boolean()->defaultValue(false),

        ]);

        $this->createTable('user_groups_rights_material_categories', [
            'id' => $this->primaryKey(),
            'user_groups_id' => $this->integer(11),
            'category_id' => $this->integer(11),
        ]);


        $otk4m_k2_user_groups = \app\models\Otk4mK2UserGroups::find()->all();

        $user_groups_id_seq = 1;
        foreach($otk4m_k2_user_groups as $value)
        {
            $otk4m_k2_users = \app\models\Otk4mK2Users::find()->where(['group'=>$value['id']])->count();

            if($otk4m_k2_users > 0)
            {
                $model =  new \app\models\UserGroups();

                $model['id'] = $value['id'];
                $model['name'] = $value['name'];


                if($model->save(false))
                {
                    $permissions = json_decode($value['permissions'],true);



                    if(isset($permissions['categories']) && is_array($permissions['categories']))
                    {
                        foreach ($permissions['categories'] as $categories)
                        {
                            $user_groups_rights_material_categories = new \app\models\UserGroupsRightsMaterialCategories();
                            $user_groups_rights_material_categories['user_groups_id'] = $model['id'];
                            $user_groups_rights_material_categories['category_id'] = $categories;

                            if($user_groups_rights_material_categories->save())
                            {
                                echo 'Создано правило группы пользователей '.$value['name'].chr(10).chr(13);
                            }
                            else
                            {
                                var_dump($user_groups_rights_material_categories->getErrors());

                                throw new HttpException(500 ,'Ошибка при сохранении правил группы пользователей. id группы = '.$user_groups_rights_material_categories['id']);
                            }
                        }
                    }


                    echo 'Категория создана '.$value['name'].chr(10).chr(13);
                    if($value['id'] >  $user_groups_id_seq)
                    {
                        $user_groups_id_seq = $value['id'];
                    }
                }
                else
                {
                    var_dump($model->getErrors());

                    throw new HttpException(500 ,'Ошибка при сохранении группы пользователе. id группы пользователе = '.$model['id']);
                }
            }
        }

        $this->execute('ALTER TABLE user_groups ADD PRIMARY KEY (id)');
        $this->execute('create sequence user_groups_id_seq start '.($user_groups_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE user_groups ALTER COLUMN id SET DEFAULT nextval('user_groups_id_seq'::regclass)");

//
//        $model =  new \app\models\UserGroups();
//        $model['name'] = 'Группа администраторов и модероторов сайта';
//        $model['admin_group'] = true;
//
//        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_groups');
        $this->execute('drop sequence user_groups_id_seq');
        $this->dropTable('user_groups_rights_material_categories');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_115053_transfer_user_group_to_new_database cannot be reverted.\n";

        return false;
    }
    */
}
