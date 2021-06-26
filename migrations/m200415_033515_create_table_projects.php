<?php

use yii\db\Migration;

/**
 * Class m200415_033515_create_table_progect
 */
class m200415_033515_create_table_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('projects', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'background' => $this->text(),
            'logo' => $this->text(),
            'description' => $this->text(),
            'time_create' => $this->dateTime(),
            'color_projects_id' => $this->integer(11),
            'url' => $this->text(),
            'in_archive' => $this->boolean()->defaultValue(false),
            'display_on_home_page' => $this->boolean()->defaultValue(false),
            'outdated' => $this->boolean()->defaultValue(false)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('projects');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_033515_create_table_progect cannot be reverted.\n";

        return false;
    }
    */
}
