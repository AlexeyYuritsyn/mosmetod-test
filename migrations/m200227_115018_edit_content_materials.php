<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200227_115018_edit_content_materials
 */
class m200227_115018_edit_content_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $materials = \app\models\Materials::find()->where("content LIKE '%[widgetkit%'")->all();

        foreach ($materials as $model)
        {
            $text_strip_tags = strip_tags($model['content']);

            preg_match_all('/\[widgetkit id=\d+\]/', $text_strip_tags, $wordlist_array, PREG_OFFSET_CAPTURE);

            if(!empty($wordlist_array[0]))
            {
                foreach($wordlist_array[0] as $wordlist_value)
                {
                    $widgetkit_id = trim($wordlist_value[0]);

                    preg_match("/\d+/",$widgetkit_id,$wordlist);

                    $widget = \app\models\Widget::find()->where(['id'=>$wordlist[0]])->one();

                    if(!is_null($widget))
                    {
                        $widget['materials_id'] = $model['id'];

                        if($widget->save())
                        {
                        $model['content'] = preg_replace('/\[widgetkit id=\d+\]/','',$model['content']);

                            if($model->save(false))
                            {
                                echo 'Контент материала был отредактирован и сохранен. id материала = '.$model['id'].chr(10).chr(13);
                            }
                            else
                            {
                                var_dump($model->getErrors());

                                throw new HttpException(500 ,'Ошибка при сохранении материала после редактирования виджета. id материала = '.$model['id']);
                            }
                        }
                        else
                        {
                            var_dump($model->getErrors());

                            throw new HttpException(500 ,'Ошибка при сохранении виджета. id виджета = '.$widget['id']);
                        }
                    }
                }
            }
        }
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_115018_edit_content_materials cannot be reverted.\n";

        return false;
    }
    */
}
