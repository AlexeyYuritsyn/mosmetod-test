<?php

use yii\db\Migration;

/**
 * Class m200811_142115_create_duplication_material
 */
class m200811_142115_create_duplication_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('duplication_materials', [
            'id' => $this->primaryKey(),
            'materials_guid' => $this->string(255),
            'time_open_material' => $this->integer(11),
            'users_id' => $this->integer(11)
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('duplication_materials');
    }

}
