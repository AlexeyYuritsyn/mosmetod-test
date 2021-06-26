<?php

use yii\db\Migration;

/**
 * Class m200423_184750_create_widget_logo
 */
class m200423_184750_create_widget_logo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('widget_logo', [
            'id' => $this->primaryKey(),
            'materials_id' => $this->integer(11),
            'image' => $this->text(),
            'url' => $this->text(),
            'type' => $this->integer(11),
            'created' => $this->dateTime(),
            'modified' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('widget_logo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200423_184750_create_widget_logo cannot be reverted.\n";

        return false;
    }
    */
}
