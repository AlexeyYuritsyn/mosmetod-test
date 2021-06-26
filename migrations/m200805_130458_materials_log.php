<?php

use yii\db\Migration;
use app\models\Materials;
use app\models\AutoSaveMaterials;
use app\models\WidgetMap;
use app\models\AutoSaveWidgetMap;
use app\models\AutoSaveWidgetGallery;
use app\models\WidgetGallery;
use app\models\WidgetAccordion;
use app\models\AutoSaveWidgetAccordion;
use app\models\AutoSaveWidgetTabs;
use app\models\WidgetTabs;
use app\models\WidgetLogo;
use app\models\AutoSaveWidgetLogo;
use app\models\AutoSaveWidgetYoutube;
use app\models\WidgetYoutube;
use app\models\Widget;
use app\models\AutoSaveWidget;
use yii\db\Expression;
use yii\web\HttpException;

/**
 * Class m200805_130458_materials_log
 */
class m200805_130458_materials_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = Materials::find()->all();

        foreach($model as $val)
        {
            $auto_save_model = new AutoSaveMaterials();

            $auto_save_model->setAttributes($val->getAttributes());
            $auto_save_model['materials_id'] = $val['id'];
            $auto_save_model['save_date'] = new Expression('NOW()');
            $auto_save_model['users_id'] = $val['created_by'];
            $auto_save_model['comment'] = null;

            if($auto_save_model->save())
            {
                $widget_map = WidgetMap::find()->where(['materials_id'=>$val['id']])->all();

                if(!empty($widget_map))
                {
                    foreach($widget_map as $widget_map_val)
                    {
                        $auto_save_widget_map =  new AutoSaveWidgetMap();
                        $auto_save_widget_map->setAttributes($widget_map_val->getAttributes());
                        $auto_save_widget_map['auto_save_materials_id'] = $auto_save_model['id'];
                        if(!$auto_save_widget_map->save())
                        {
                            throw new HttpException(500 ,var_export($auto_save_widget_map->getErrors(),true));
                        }
                    }
                }

                $widget_gallery = WidgetGallery::find()->where(['materials_id'=>$val['id']])->all();

                if(!empty($widget_gallery))
                {
                    foreach($widget_gallery as $widget_gallery_val)
                    {
                        $auto_save_widget_gallery =  new AutoSaveWidgetGallery();
                        $auto_save_widget_gallery->setAttributes($widget_gallery_val->getAttributes());
                        $auto_save_widget_gallery['auto_save_materials_id'] = $auto_save_model['id'];
                        if(!$auto_save_widget_gallery->save())
                        {
                            throw new HttpException(500 ,var_export($auto_save_widget_gallery->getErrors(),true));
                        }
                    }
                }

                $widget_accordion = WidgetAccordion::find()->where(['materials_id'=>$val['id']])->all();

                if(!empty($widget_accordion))
                {
                    foreach($widget_accordion as $widget_accordion_val)
                    {
                        $auto_save_widget_accordion =  new AutoSaveWidgetAccordion();
                        $auto_save_widget_accordion->setAttributes($widget_accordion_val->getAttributes());
                        $auto_save_widget_accordion['auto_save_materials_id'] = $auto_save_model['id'];
                        if(!$auto_save_widget_accordion->save())
                        {
                            throw new HttpException(500 ,var_export($auto_save_widget_accordion->getErrors(),true));
                        }
                    }
                }

                $widget_tab = WidgetTabs::find()->where(['materials_id'=>$val['id']])->all();

                if(!empty($widget_tab))
                {
                    foreach($widget_tab as $widget_tab_val)
                    {
                        $auto_save_widget_tabs =  new AutoSaveWidgetTabs();
                        $auto_save_widget_tabs->setAttributes($widget_tab_val->getAttributes());
                        $auto_save_widget_tabs['auto_save_materials_id'] = $auto_save_model['id'];
                        if(!$auto_save_widget_tabs->save())
                        {
                            throw new HttpException(500 ,var_export($auto_save_widget_tabs->getErrors(),true));
                        }
                    }
                }

                $widget_logo = WidgetLogo::find()->where(['materials_id' => $val['id']])->one();

                if(!is_null($widget_logo))
                {
                    $auto_save_widget_logo = new AutoSaveWidgetLogo();
                    $auto_save_widget_logo->setAttributes($widget_logo->getAttributes());
                    $auto_save_widget_logo['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_logo->save())
                    {
                        throw new HttpException(500 ,var_export($auto_save_widget_logo->getErrors(),true));
                    }
                }

                $widget_youtube = WidgetYoutube::find()->select('youtube_url')->where(['materials_id' => $val['id']])->one();

                if(!is_null($widget_youtube))
                {
                    $auto_save_widget_youtube = new AutoSaveWidgetYoutube();
                    $auto_save_widget_youtube->setAttributes($widget_youtube->getAttributes());
                    $auto_save_widget_youtube['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_youtube->save())
                    {
                        throw new HttpException(500 ,var_export($auto_save_widget_youtube->getErrors(),true));
                    }
                }

                $widget = Widget::find()->where(['materials_id'=>$val['id']])->all();

                if(!empty($widget))
                {
                    foreach($widget as $widget_val)
                    {
                        $auto_save_widget =  new AutoSaveWidget();
                        $auto_save_widget->setAttributes($widget_val->getAttributes());
                        $auto_save_widget['auto_save_materials_id'] = $auto_save_model['id'];
                        if(!$auto_save_widget->save())
                        {
                            throw new HttpException(500 ,var_export($auto_save_widget->getErrors(),true));
                        }
                    }
                }

                echo 'Логирование материала сохранено. id материала ='.$val['id'].chr(10).chr(13);
            }
            else
            {
                throw new HttpException(500 ,var_export($auto_save_model->getErrors(),true));
            }
        }
    }
}
