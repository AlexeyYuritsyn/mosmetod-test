<?php

use yii\db\Migration;

/**
 * Class m200727_123140_create_table_position_and_direction_in_users
 */
class m200727_123140_create_table_position_and_direction_in_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('position_and_direction_in_users',[
            'id' => $this->primaryKey(),
            'users_id' => $this->integer(11),
            'position_and_direction_id' => $this->integer(11)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('position_and_direction_in_users');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200727_123140_create_table_position_and_direction_in_users cannot be reverted.\n";

        return false;
    }
    */
}
