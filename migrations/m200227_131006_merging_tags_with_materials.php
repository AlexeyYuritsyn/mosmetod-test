<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200227_131006_merging_tags_with_materials
 */
class m200227_131006_merging_tags_with_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('material_tags_in_materials', [
            'id' => $this->primaryKey(),
            'material_tags_id' => $this->integer(11),
            'materials_id' => $this->integer(11)
        ]);

        $otk4m_k2_tags_xref = \app\models\Otk4mK2TagsXref::find()->all();

        foreach($otk4m_k2_tags_xref as $value)
        {
            $model =  new \app\models\MaterialTagsInMaterials();

            $model['material_tags_id'] = $value['tagID'];
            $model['materials_id'] = $value['itemID'];

            if($model->save())
            {
                echo 'Связь тега и материала была создана. id старой связи = '.$value['id'].chr(10).chr(13);
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении связи тега и материала. id старой связи = '.$value['id']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
//    public function safeDown()
//    {
//        echo "m200227_131006_merging_tags_with_materials cannot be reverted.\n";
//
//        return false;
//    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_131006_merging_tags_with_materials cannot be reverted.\n";

        return false;
    }
    */
}
