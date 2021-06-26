<?php

use yii\db\Migration;

/**
 * Class m200521_130659_create_delegation_rights_in_user_groups
 */
class m200521_130659_create_delegation_rights_in_user_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('delegation_rights_in_user_groups', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer(11),
            'user_groups_id' => $this->integer(11)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('delegation_rights_in_user_groups');
    }

}
