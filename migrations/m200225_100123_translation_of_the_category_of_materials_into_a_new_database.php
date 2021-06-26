<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200225_100123_translation_of_the_category_of_materials_into_a_new_database
 */
class m200225_100123_translation_of_the_category_of_materials_into_a_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('material_categories', [
            'id' => $this->integer(11),
            'title' => $this->text(),
            'alias' => $this->text(),
            'description' => $this->text(),
            'parent' => $this->integer(11),
            'published' => $this->boolean()->defaultValue(false),
            'access' => $this->integer(11),
            'in_archive' => $this->boolean()->defaultValue(false),
            'order_categories' => $this->integer(11)->defaultValue(1),
            'exclude_from_search' =>  $this->boolean()->defaultValue(false)
        ]);

        $otk4m_k2_categories = \app\models\Otk4mK2Categories::find()->all();

        $material_categories_id_seq = 1;
        foreach($otk4m_k2_categories as $value)
        {
            $model =  new \app\models\MaterialCategories();

            $model['id'] = $value['id'];
            $model['title'] = $value['name'];
            $model['alias'] = $value['alias'];
            $model['description'] = $value['description'];
            $model['parent'] = $value['parent'];
            $model['published'] = $value['published'];
            $model['access'] = $value['access'];
            $model['in_archive'] = $value['trash'];


            if($model->save(false))
            {
                echo 'Категория создана '.$value['name'].chr(10).chr(13);
                if($value['id'] >  $material_categories_id_seq)
                {
                    $material_categories_id_seq = $value['id'];
                }
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении категории. id категории = '.$model['id']);
            }

        }

        $this->execute('ALTER TABLE material_categories ADD PRIMARY KEY (id)');
        $this->execute('create sequence material_categories_id_seq start '.($material_categories_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE material_categories ALTER COLUMN id SET DEFAULT nextval('material_categories_id_seq'::regclass)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('material_categories');
        $this->execute('drop sequence material_categories_id_seq');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_100123_translation_of_the_category_of_materials_into_a_new_database cannot be reverted.\n";

        return false;
    }
    */
}
