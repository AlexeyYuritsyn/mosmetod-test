<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200723_084259_change_url_of_widget_gallery
 */
class m200723_084259_change_url_of_widget_gallery extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = \app\models\WidgetGallery::find()->all();

        if(!empty($model))
        {
            foreach ($model as &$value)
            {
                $image = $value['image'];

                $image = '/files'.$image;

                $value['image'] = $image;

                if($value->save())
                {
                    echo 'URL картинки widget_gallery поменялся. id widget_gallery = '.$value['id'].chr(10).chr(13);
                }
                else
                {
                    throw new HttpException(500 ,'Ошибка при изменении URL в видежете widget_gallery. id widget_gallery = '.$value['id']);
                }
            }
        }
    }
}
