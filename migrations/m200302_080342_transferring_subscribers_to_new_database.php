<?php

use yii\db\Migration;

/**
 * Class m200302_080342_transferring_subscribers_to_new_database
 */
class m200302_080342_transferring_subscribers_to_new_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('subscribers', [
            'id' => $this->integer(11),
            'email' => $this->string(255),
            'time_created' => $this->dateTime(),
            'status' =>  $this->integer(11),
            'time_send' => $this->dateTime(),
            'send_notification_immediately'=> $this->boolean()->defaultValue(false)
        ]);

        $subscribe_users = \app\models\SubscribeUsers::find()->all();

        $subscribers_id_seq = 1;
        foreach($subscribe_users as $value)
        {
            $model =  new \app\models\Subscribers();

            $model['id'] = $value['id'];
            $model['email'] = $value['email'];

            $checked_time_created = null;

            if($value['time_created'] != '')
            {
                $checked_time_created = date("Y-m-d H:i:s",$value['time_created']);
            }

            $model['time_created'] = $checked_time_created;
            $model['status'] = $value['status'];

            $checked_time_send = null;

            if($value['time_send'] != '')
            {
                $checked_time_send = date("Y-m-d H:i:s",$value['time_send']);
            }

            $model['time_send'] = $checked_time_send;


            if($model->save(false))
            {
                echo 'Подписчик создан '.$value['email'].chr(10).chr(13);

                if($value['id'] >  $subscribers_id_seq)
                {
                    $subscribers_id_seq = $value['id'];
                }
            }
            else
            {
                var_dump($model->getErrors());

                throw new HttpException(500 ,'Ошибка при сохранении подписчика. id категории = '.$model['id']);
            }

        }

        $this->execute('ALTER TABLE subscribers ADD PRIMARY KEY (id)');
        $this->execute('create sequence subscribers_id_seq start '.($subscribers_id_seq + 1).' increment 1 NO MAXVALUE CACHE 1');
        $this->execute("ALTER TABLE subscribers ALTER COLUMN id SET DEFAULT nextval('subscribers_id_seq'::regclass)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('subscribers');
        $this->execute('drop sequence subscribers_id_seq');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200302_080342_transferring_subscribers_to_new_database cannot be reverted.\n";

        return false;
    }
    */
}
