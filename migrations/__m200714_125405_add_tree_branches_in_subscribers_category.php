<?php

use yii\db\Migration;
use \app\models\MaterialCategories;
use \app\models\SubscribersCategory;
use yii\web\HttpException;

/**
 * Class m200714_125405_add_tree_branches_in_subscribers_category
 */
class m200714_125405_add_tree_branches_in_subscribers_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $subscribers = \app\models\Subscribers::find()->all();

        $category_id_tree_total = [];
        foreach ($subscribers as $value)
        {
            $category_id_tree = [];

            $subscribers_category = SubscribersCategory::find()->select('category_id')->where(['user_id'=>$value['id']])->all();

            if(!empty($subscribers_category))
            {
                foreach($subscribers_category as $subscribers_category_key=>$subscribers_category_val)
                {
                    $category_id_tree[] = $subscribers_category_val['category_id'];
                }
            }


            $category_id_tree_total[$value['id']] = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);
            echo 'Группа категорий подписчиков определена. id группы = '.$value['id'].chr(10).chr(13);
//            throw new HttpException(500 ,'Ошибка при обновлении правил группы категорий подписчиков. id группы = ');
        }

        if(!empty($category_id_tree_total))
        {
//            SubscribersCategory::deleteAll();
            foreach($category_id_tree_total as $category_id_tree_total_key=>$category_id_tree_total_val)
            {
                if(!empty($category_id_tree_total_val))
                {
                    foreach($category_id_tree_total_val as $val)
                    {
                        $new_subscribers_category = SubscribersCategory::find()
                            ->select('category_id')
                            ->where(['user_id'=>$category_id_tree_total_key,'category_id'=>$val])->one();

                        if(is_null($new_subscribers_category))
                        {
                            $new_subscribers_category = new SubscribersCategory();

                            $new_subscribers_category['user_id'] = $category_id_tree_total_key;
                            $new_subscribers_category['category_id'] = $val;
                            $new_subscribers_category['time_created'] = date('Y-m-d H:i:s',time());


                            if($new_subscribers_category->save())
                            {
                                echo 'Пересоздано правило группы категории подписчиков '.$new_subscribers_category['id'].chr(10).chr(13);
                            }
                            else
                            {
                                var_dump($new_subscribers_category->getErrors());

                                throw new HttpException(500 ,'Ошибка при обновлении правил группы категорий подписчиков. id группы = '.$new_subscribers_category['id']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        echo "m200714_125405_add_tree_branches_in_subscribers_category cannot be reverted.\n";
//
//        return false;
    }

}
