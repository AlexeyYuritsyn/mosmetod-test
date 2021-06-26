<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200421_131139_modify_widget_accordion
 */
class m200421_131139_modify_widget_accordion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('widget_accordion','created',$this->dateTime());
        $this->addColumn('widget_accordion','modified',$this->dateTime());

        $widget = \app\models\Widget::find()->where(['type'=>'accordion'])->all();

        foreach ($widget as $val)
        {
            $model = \app\models\WidgetAccordion::find()->where(['widget_id'=>$val['id']])->all();

            foreach ($model as &$model_val)
            {
                $model_val['materials_id'] = $val['materials_id'];
//                $model_val['name'] = $val['name'];
                $model_val['created'] = $val['created'];
                $model_val['modified'] = $val['modified'];

                if($model_val->save())
                {
                    echo 'Виджет карты создан. id виджета галлерея = '.$model_val['id'].chr(10).chr(13);
                }
                else
                {
                    throw new HttpException(500 ,'Ошибка при сохранении виджета карты. id виджета = '.$model_val['id']);
                }
            }
        }

        \app\models\Widget::deleteAll(['type'=>'accordion']);
//        $this->dropColumn('widget_accordion','widget_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_131139_modify_widget_accordion cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_131139_modify_widget_accordion cannot be reverted.\n";

        return false;
    }
    */
}
