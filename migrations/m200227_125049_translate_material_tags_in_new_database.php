<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200227_125049_translate_material_tags_in_new_database
 */
class m200227_125049_translate_material_tags_in_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('material_tags', [
            'id' => $this->integer(11),
            'name' => $this->string(255),
            'published' => $this->boolean()->defaultValue(false)
        ]);

        $otk4m_k2_tags = \app\models\Otk4mK2Tags::find()->all();

        $material_tags_id_seq = 1;

        foreach($otk4m_k2_tags as $value)
        {
            $model =  new \app\models\MaterialTags();

            $model['id'] = $value['id'];
            $model['name'] = $value['name'];
            $model['published'] = $value['published'];

            if($model->save())
            {
                echo 'Тег создан '.$value['name'].chr(10).chr(13);
                if($value['id'] > $material_tags_id_seq)
                {
                    $material_tags_id_seq = $value['id'];
                }
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении тега. id тега = '.$model['id']);
            }

        }

        $this->execute('ALTER TABLE material_tags ADD PRIMARY KEY (id)');
        $this->execute('create sequence material_tags_id_seq start '.($material_tags_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE material_tags ALTER COLUMN id SET DEFAULT nextval('material_tags_id_seq'::regclass)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('material_tags');
        $this->execute('drop sequence material_tags_id_seq');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_125049_translate_material_tags_in_new_database cannot be reverted.\n";

        return false;
    }
    */
}
