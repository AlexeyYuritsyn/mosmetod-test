<?php

use yii\db\Migration;
use yii\web\HttpException;

/**
 * Class m200227_090507_transferring_materials_to_new_database
 */
class m200227_090507_transferring_materials_to_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('materials', [
            'id' => $this->integer(11),
            'title' => $this->text(),//$this->string(255),
            'alias' => $this->text(),
            'material_categories_id' => $this->integer(11),
            'status' => $this->integer(11),
            'published_date' => $this->integer(11),
            'content' => $this->text(),
            'description' => $this->string(255),
            'created' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'checked_out' => $this->integer(11),
            'checked_out_time' => $this->dateTime(),
            'modified' => $this->dateTime(),
            'modified_by' => $this->integer(11),
            'publish_up' => $this->dateTime(),
            'publish_down' => $this->dateTime(),
//            'in_archive' => $this->boolean()->defaultValue(false),
            'hits' => $this->boolean()->defaultValue(false),
            'urgency_withdrawal' => $this->integer(11),
            'guid' => $this->string(255),
            'date_unpinning' => $this->dateTime()
        ]);

        $otk4m_k2_items = \app\models\Otk4mK2Items::find()->all();

        $materials_id_seq = 1;
        foreach ($otk4m_k2_items as $value)
        {
            $model = new \app\models\Materials();

            $model['id'] = $value['id'];
            $model['title'] = $value['title'];
            $model['alias'] = $value['alias'];

            $model['material_categories_id'] = $value['catid'];

            if($value['published'] == true)
            {
                $model['status'] = \app\models\Materials::PUBLISHED;
            }
            else
            {
                $model['status'] = \app\models\Materials::DRAFT;
            }

            $introtext_fulltext = $value['introtext'].$value['fulltext'];

            $introtext_fulltext = str_replace('"files/','"/files/', $introtext_fulltext);

            $model['content'] = $introtext_fulltext;

            if($model['content'] == '')
            {
                $model['content'] = '&nbsp;';
            }
            else
            {
                $content = strip_tags($model['content']);

                $content = str_replace(array("\r\n", "\n", "\r"), '', $content);
                $content = str_replace(array("&nbsp;"), ' ', $content);

                $content = substr($content, 0, 252);

                $content =  explode(" ", $content);
                array_pop($content);
                array_push($content, '...');

                $content = implode(" ", $content);

                $model['description'] = $content;
            }

            $created = null;

            if($value['created'] != '0000-00-00 00:00:00')
            {
                $created = $value['created'];
            }
            $model['created'] = $created;

            $model['created_by'] = $value['created_by'];
            $model['checked_out'] = $value['checked_out'];

            $checked_out_time = null;

            if($value['checked_out_time'] != '0000-00-00 00:00:00')
            {
                $checked_out_time = $value['checked_out_time'];
            }
            $model['checked_out_time'] = $checked_out_time;

            $modified = null;

            if($value['modified'] != '0000-00-00 00:00:00')
            {
                $modified = $value['modified'];
            }
            $model['modified'] = $modified;

            $model['modified_by'] = $value['modified_by'];


            $publish_up = null;

            if($value['publish_up'] != '0000-00-00 00:00:00')
            {
                $publish_up = $value['publish_up'];
            }
            $model['publish_up'] = $publish_up;
            $model['published_date'] = strtotime($publish_up);


            $publish_down = null;

            if($value['publish_down'] != '0000-00-00 00:00:00')
            {
                $publish_down = $value['publish_down'];
            }
            $model['publish_down'] = $publish_down;


            if($value['trash'] == true)
            {
                $model['status'] = \app\models\Materials::ARCHIVE;
            }


//            $model['hits'] = 5;
            $model['urgency_withdrawal'] = 1;

            $model['guid'] = md5(rand(1,2147483647).' '.rand(1,2147483647).' '.time());


            if($model->save(false))
            {
                echo 'Материал создан. id материала = '.$value['id'].chr(10).chr(13);
                if($model['id'] >  $materials_id_seq)
                {
                    $materials_id_seq = $model['id'];
                }
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении материала. id материала = '.$model['id']);
            }

        }
        $this->execute('ALTER TABLE materials ADD PRIMARY KEY (id)');
        $this->execute('create sequence materials_id_seq start '.($materials_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE materials ALTER COLUMN id SET DEFAULT nextval('materials_id_seq'::regclass)");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('materials');
        $this->execute('drop sequence materials_id_seq');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_090507_transferring_materials_to_new_database cannot be reverted.\n";

        return false;
    }
    */
}
