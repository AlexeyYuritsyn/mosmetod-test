<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200421_114817_modify_widget_tabs
 */
class m200421_114817_modify_widget_tabs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('widget_tabs','created',$this->dateTime());
        $this->addColumn('widget_tabs','modified',$this->dateTime());

        $widget = \app\models\Widget::find()->where(['type'=>'tab'])->all();

        foreach ($widget as $val)
        {
            $model = \app\models\WidgetTabs::find()->where(['widget_id'=>$val['id']])->all();

            foreach ($model as &$model_val)
            {
                $model_val['materials_id'] = $val['materials_id'];
//                $model_val['name'] = $val['name'];
                $model_val['created'] = $val['created'];
                $model_val['modified'] = $val['modified'];

                if($model_val->save())
                {
                    echo 'Виджет карты создан. id виджета таб = '.$model_val['id'].chr(10).chr(13);
                }
                else
                {
                    throw new HttpException(500 ,'Ошибка при сохранении виджета карты. id виджета = '.$model_val['id']);
                }
            }
        }

        \app\models\Widget::deleteAll(['type'=>'tab']);
//        $this->dropColumn('widget_tabs','widget_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_114817_modify_widget_tabs cannot be reverted.\n";

        return false;
    }
    */
}
