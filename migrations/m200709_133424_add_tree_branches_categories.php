<?php

use yii\db\Migration;
//use \app\models\RoleUsersInUserGroups;
use \app\models\UserGroupsRightsMaterialCategories;
use \app\models\MaterialCategories;
use yii\web\HttpException;

/**
 * Class m200709_133424_add_tree_branches_categories
 */
class m200709_133424_add_tree_branches_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user_groups = \app\models\UserGroups::find()->all();

        $category_id_tree_total = [];
        foreach ($user_groups as $value)
        {
            $category_id_tree = [];

            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->select('category_id')->where(['user_groups_id'=>$value['id']])->all();

            if(!empty($user_groups_rights_material_categories))
            {
                foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
                {
                    $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
                }
            }



            $category_id_tree_total[$value['id']] = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);

        }

        if(!empty($category_id_tree_total))
        {
            var_dump($category_id_tree_total);
            UserGroupsRightsMaterialCategories::deleteAll();
            foreach($category_id_tree_total as $category_id_tree_total_key=>$category_id_tree_total_val)
            {
                if(!empty($category_id_tree_total_val))
                {
                    foreach($category_id_tree_total_val as $val)
                    {
                        var_dump($val);
                        $new_user_groups_rights_material_categories = new UserGroupsRightsMaterialCategories();

                        $new_user_groups_rights_material_categories['user_groups_id'] = $category_id_tree_total_key;
                        $new_user_groups_rights_material_categories['category_id'] = $val;

                        if($new_user_groups_rights_material_categories->save())
                        {
                            echo 'Пересоздано правило группы пользователей '.$category_id_tree_total_key.chr(10).chr(13);
                        }
                        else
                        {
                            var_dump($new_user_groups_rights_material_categories->getErrors());

                            throw new HttpException(500 ,'Ошибка при обновлении правил группы пользователей. id группы = '.$category_id_tree_total_key);
                        }
                    }
                }
//                $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
            }
        }

//        var_dump($category_id_tree_total);
////            die;
//        throw new HttpException(500 ,'Сессия закончилась. Выполните повторно вход.');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_133424_add_tree_branches_categories cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_133424_add_tree_branches_categories cannot be reverted.\n";

        return false;
    }
    */
}
