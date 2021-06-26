<?php

use yii\db\Migration;

/**
 * Class m200518_115648_create_uploads_file_material_log
 */
class m200518_115648_create_uploads_file_material_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('uploads_file_material_log', [
            'id' => $this->primaryKey(),
            'guid_material' => $this->string(255),
            'url_file_material' => $this->text(),
            'user_id' => $this->integer(11),
            'date_uploads' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('uploads_file_material_log');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200518_115648_create_uploads_file_material_log cannot be reverted.\n";

        return false;
    }
    */
}
