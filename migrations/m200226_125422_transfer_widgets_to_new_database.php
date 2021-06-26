<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200226_125422_transfer_widgets_to_new_database
 */
class m200226_125422_transfer_widgets_to_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('widget', [
            'id' => $this->integer(11),
            'type' => $this->string(255),
            'name' => $this->text(),
//            'content' => $this->text(),
            'materials_id' => $this->integer(11),
            'created' => $this->dateTime(),
            'modified' => $this->dateTime()
        ]);

        $this->createTable('widget_map', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->integer(11),
            'title' => $this->text(),
            'lat' => $this->text(),
            'lng' => $this->text(),
            'name' => $this->text(),
            'materials_id' => $this->integer(11),
        ]);

        $this->createTable('widget_tabs', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->integer(11),
            'title' => $this->text(),
            'content' => $this->text(),
            'materials_id' => $this->integer(11)
        ]);

        $this->createTable('widget_gallery', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->integer(11),
            'image' => $this->text(),
            'order_id' => $this->integer(11),
            'materials_id' => $this->integer(11)
        ]);

        $this->createTable('widget_accordion', [
            'id' => $this->primaryKey(),
            'widget_id' => $this->integer(11),
            'title' => $this->text(),
            'content' => $this->text(),
            'materials_id' => $this->integer(11)
        ]);

        $this->createTable('widget_youtube', [
            'id' => $this->primaryKey(),
            'youtube_url' => $this->text(),
            'materials_id' => $this->integer(11)
        ]);

        $otk4m_widgetkit_widget = \app\models\Otk4mWidgetkitWidget::find()->all();

        $widget_id_seq = 1;
        foreach ($otk4m_widgetkit_widget as $value)
        {
            if($value['type'] != '')
            {
                $model = new \app\models\Widget();

                $model['id'] = $value['id'];
                $model['type'] =  $value['type'] == 'slideshow'?'tab':$value['type'];
                $model['name'] = $value['name'];
//                $model['content'] = $value['content'];



//                $model['created'] = $value['created'];

                $createdDate = null;

                if($value['created'] != '0000-00-00 00:00:00')
                {
                    $createdDate = $value['created'];
                }
                $model['created'] = $createdDate;

                $modifiedDate = null;

                if($value['modified'] != '0000-00-00 00:00:00')
                {
                    $modifiedDate = $value['modified'];
                }
                $model['modified'] = $modifiedDate;

                if($model->save())
                {
                    $json_content = json_decode($value['content'],true);
                    if($json_content != false)
                    {
                        if($value['type'] == 'map')
                        {
                            if(!empty($json_content['items']))
                            {
                                foreach ($json_content['items'] as $json_content_val)
                                {
                                    $widget_map = new \app\models\WidgetMap();
                                    $widget_map['widget_id'] = $model['id'];
                                    $widget_map['title'] = $json_content_val['title'];
                                    $widget_map['lat'] = $json_content_val['lat'];
                                    $widget_map['lng'] = $json_content_val['lng'];
                                    $widget_map['name'] = $value['name'];
                                    if($widget_map->save())
                                    {
                                        echo 'Виджет карты создан. id виджета карты = '.$widget_map['id'].chr(10).chr(13);
                                    }
                                    else
                                    {
                                        throw new HttpException(500 ,'Ошибка при сохранении виджета карты. id виджета = '.$model['id']);
                                    }
                                }
                            }
//                            die;
                        }

                        if($value['type'] == 'gallery')
                        {
                            if(!empty($json_content['links']))
                            {
                                $i = 0;
                                foreach ($json_content['links'] as $json_content_key => $json_content_val)
                                {
                                    $widget_gallery = new \app\models\WidgetGallery();
                                    $widget_gallery['widget_id'] = $model['id'];
                                    $widget_gallery['image'] = $json_content_key;
                                    $widget_gallery['order_id'] = $i;

                                    if($widget_gallery->save())
                                    {
                                        echo 'Виджет галереи создан. id виджета галереи = '.$widget_gallery['id'].chr(10).chr(13);
                                    }
                                    else
                                    {
                                        throw new HttpException(500 ,'Ошибка при сохранении виджета карты. id виджета = '.$model['id']);
                                    }
                                    $i++;
                                }
                            }
                        }
                        if($value['type'] == 'slideshow')
                        {
                            if(!empty($json_content['items']))
                            {
                                foreach ($json_content['items'] as $json_content_val)
                                {
                                    $widget_tabs = new \app\models\WidgetTabs();
                                    $widget_tabs['widget_id'] = $model['id'];
                                    $widget_tabs['title'] = $json_content_val['title'];
                                    $widget_tabs['content'] = $json_content_val['content'];

                                    if($widget_tabs->save())
                                    {
                                        echo 'Виджет таба создан. id виджета таба = '.$widget_tabs['id'].chr(10).chr(13);
                                    }
                                    else
                                    {
                                        throw new HttpException(500 ,'Ошибка при сохранении виджета таба. id виджета = '.$model['id']);
                                    }
                                }
                            }
                        }
                        if($value['type'] == 'accordion')
                        {
                            if(!empty($json_content['items']))
                            {
                                foreach ($json_content['items'] as $json_content_val)
                                {
                                    $widget_accordion = new \app\models\WidgetAccordion();
                                    $widget_accordion['widget_id'] = $model['id'];
                                    $widget_accordion['title'] = $json_content_val['title'];
                                    $widget_accordion['content'] = $json_content_val['content'];

                                    if($widget_accordion->save())
                                    {
                                        echo 'Виджет аккардиона создан. id виджета аккардиона = '.$widget_accordion['id'].chr(10).chr(13);
                                    }
                                    else
                                    {
                                        throw new HttpException(500 ,'Ошибка при сохранении виджета аккардиона. id виджета = '.$model['id']);
                                    }
                                }
                            }
                        }
                    }


                    echo 'Виджет создан. id виджета = '.$value['id'].chr(10).chr(13);
                    if($model['id'] >  $widget_id_seq)
                    {
                        $widget_id_seq = $model['id'];
                    }
                }
                else
                {
                    var_dump('id виджета = '.$model['id']);
//                    var_dump($model->getErrors());

                    throw new HttpException(500 ,'Ошибка при сохранении виджета. id виджета = '.$model['id']);
                }
            }
//                $model['modified'] = $value['modified'];

        }

        $this->execute('ALTER TABLE widget ADD PRIMARY KEY (id)');
        $this->execute('create sequence widget_id_seq start '.($widget_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE widget ALTER COLUMN id SET DEFAULT nextval('widget_id_seq'::regclass)");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('widget');
        $this->execute('drop sequence widget_id_seq');
        $this->dropTable('widget_map');
        $this->dropTable('widget_tabs');
        $this->dropTable('widget_gallery');
        $this->dropTable('widget_accordion');
        $this->dropTable('widget_youtube');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_125422_transfer_widgets_to_new_database cannot be reverted.\n";

        return false;
    }
    */
}
