<?php

use yii\db\Migration;

/**
 * Class m200311_075951_transferring_subscribers_category_to_new_database
 */
class m200311_075951_transferring_subscribers_category_to_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('subscribers_category', [
            'id' => $this->integer(11),
            'user_id' => $this->integer(11),
//            'school' => $this->string(255),
            'category_id' =>  $this->integer(11),
            'time_created' => $this->dateTime()
        ]);
        $this->createTable('subscribers_send_log', [
            'id' => $this->primaryKey(),
            'time_send' => $this->dateTime(),
            'user_id' => $this->integer(11),
            'result_send' =>  $this->boolean()->defaultValue(false)
        ]);

        $subscribe_category = \app\models\SubscribeCategory::find()->all();

        $subscribe_category_id_seq = 1;
        foreach($subscribe_category as $value)
        {
            $model =  new \app\models\SubscribersCategory();

            $model['id'] = $value['id'];
            $model['user_id'] = $value['user_id'];
//            $model['school'] = $value['school'];
            $model['category_id'] = $value['category_id'];

            $checked_time_created = null;

            if($value['time_created'] != '')
            {
                $checked_time_created = date("Y-m-d H:i:s",$value['time_created']);
            }

            $model['time_created'] = $checked_time_created;


            if($model->save(false))
            {
                echo 'Связь категории и подписчика создана '.$value['id'].chr(10).chr(13);

                if($value['id'] >  $subscribe_category_id_seq)
                {
                    $subscribe_category_id_seq = $value['id'];
                }
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении связь категории и подписчика. id категории = '.$model['id']);
            }

        }

        $this->execute('ALTER TABLE subscribers_category ADD PRIMARY KEY (id)');
        $this->execute('create sequence subscribe_category_id_seq start '.($subscribe_category_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE subscribers_category ALTER COLUMN id SET DEFAULT nextval('subscribe_category_id_seq'::regclass)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('subscribers_category');
        $this->execute('drop sequence subscribers_category_id_seq');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200311_075951_transferring_subscribers_category_to_new_database cannot be reverted.\n";

        return false;
    }
    */
}
