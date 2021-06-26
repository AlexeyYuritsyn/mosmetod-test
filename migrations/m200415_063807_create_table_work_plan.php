<?php

use yii\db\Migration;

/**
 * Class m200415_063807_create_table_work_plan
 */
class m200415_063807_create_table_work_plan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('work_plan_period', [
            'id' => $this->primaryKey(),
            'month' => $this->integer(11),
            'year' => $this->integer(11),
//            'in_archive' => $this->boolean()->defaultValue(false),
        ]);

        $this->createTable('work_plan', [
            'id' => $this->primaryKey(),
            'work_plan_period_id' => $this->integer(11),
            'type_event' => $this->string(255),
            'for_whom' => $this->string(255),
            'user_groups_id' => $this->integer(11),
            'event_time' => $this->string(255),
            'event_name' => $this->text(),
            'district' => $this->text(),
            'location' => $this->text(),
            'responsible' => $this->text(),
            'description' => $this->text(),
            'users_id' => $this->integer(11),
            'in_archive' => $this->boolean()->defaultValue(false),
            'not_included_main_report' => $this->boolean()->defaultValue(false),
        ]);

        $this->createTable('work_plan_date', [
            'id' => $this->primaryKey(),
            'work_plan_id' => $this->integer(11),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
        ]);

        $this->createTable('work_plan_note', [
            'id' => $this->primaryKey(),
            'work_plan_id' => $this->integer(11),
            'note_name' => $this->string(255),
            'note_url' => $this->text(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('work_plan_period');
        $this->dropTable('work_plan');
        $this->dropTable('work_plan_date');
        $this->dropTable('work_plan_note');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_063807_create_table_work_plan cannot be reverted.\n";

        return false;
    }
    */
}
