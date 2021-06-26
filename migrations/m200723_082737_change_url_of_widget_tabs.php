<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200723_082737_change_url_of_widget_tabs
 */
class m200723_082737_change_url_of_widget_tabs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = \app\models\WidgetTabs::find()->all();

        if(!empty($model))
        {
            foreach ($model as &$value)
            {
                $content = $value['content'];

                $content = str_replace('"files/','"/files/', $content);

                $value['content'] = $content;

                if($value->save())
                {
                    echo 'URL в контенте widget_tabs поменялся. id widget_tabs = '.$value['id'].chr(10).chr(13);
                }
                else
                {
                    throw new HttpException(500 ,'Ошибка при изменении URL в видежете widget_tabs. id widget_tabs = '.$value['id']);
                }
            }
        }
    }

}
