<?php

use yii\db\Migration;

/**
 * Class m200921_092938_create_table_webinars
 */
class m200921_092938_create_table_webinars extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('webinars', [
            'id' => $this->primaryKey(),
            'user_groups_id' => $this->integer(11),
            'title' => $this->text(),
            'description' => $this->string(255),
            'youtube_url' => $this->text(),
            'time_created' => $this->dateTime(),
            'in_archive' => $this->boolean()->defaultValue(false),
            'users_id' => $this->integer(11)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('webinars');
    }

}
