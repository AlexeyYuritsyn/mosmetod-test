<?php

use yii\db\Migration;

/**
 * Class m200924_124852_create_table_send_email_material_published
 */
class m200924_124852_create_table_send_email_material_published extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('send_email_material_published', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11),
            'materials_id' => $this->integer(11),
            'time_created' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('send_email_material_published');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_124852_create_table_send_email_material_published cannot be reverted.\n";

        return false;
    }
    */
}
