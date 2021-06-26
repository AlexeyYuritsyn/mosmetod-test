<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200421_081655_modify_widget_map
 */
class m200421_081655_modify_widget_map extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('widget_map','created',$this->dateTime());
        $this->addColumn('widget_map','modified',$this->dateTime());

        $widget = \app\models\Widget::find()->where(['type'=>'map'])->all();

        foreach ($widget as $val)
        {

            $model = \app\models\WidgetMap::find()->where(['widget_id'=>$val['id']])->all();

            foreach ($model as &$model_val)
            {
                $model_val['materials_id'] = $val['materials_id'];
//                $model_val['name'] = $val['name'];
                $model_val['created'] = $val['created'];
                $model_val['modified'] = $val['modified'];

                if($model_val->save())
                {
                    echo 'Виджет карты создан. id виджета карты = '.$model_val['id'].chr(10).chr(13);
                }
                else
                {
                    throw new HttpException(500 ,'Ошибка при сохранении виджета карты. id виджета = '.$model_val['id']);
                }
            }
        }

        \app\models\Widget::deleteAll(['type'=>'map']);
//        $this->dropColumn('widget_map','widget_id');
    }

    /**
     * {@inheritdoc}
     */
//    public function safeDown()
//    {
//        echo "m200421_081655_modify_widget_map cannot be reverted.\n";
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
        echo "m200421_081655_modify_widget_map cannot be reverted.\n";

        return false;
    }
    */
}
