<?php

use yii\db\Migration;

/**
 * Class m200804_081922_create_table_auto_save_materials
 */
class m200804_081922_create_table_auto_save_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('auto_save_materials', [
            'id' => $this->primaryKey(),
            'materials_id' => $this->integer(11),
            'title' => $this->text(),
            'alias' => $this->text(),
            'material_categories_id' => $this->integer(11),
            'status' => $this->integer(11),
            'published_date' => $this->integer(11),
            'content' => $this->text(),
            'description' => $this->string(255),
            'created' => $this->dateTime(),
            'created_by' => $this->integer(11),
            'checked_out' => $this->integer(11),
            'checked_out_time' => $this->dateTime(),
            'modified' => $this->dateTime(),
            'modified_by' => $this->integer(11),
            'publish_up' => $this->dateTime(),
            'publish_down' => $this->dateTime(),
            'hits' => $this->boolean()->defaultValue(false),
            'urgency_withdrawal' => $this->integer(11),
            'guid' => $this->string(255),
            'users_id' => $this->integer(11),
            'save_date' => $this->dateTime(),
            'comment' => $this->text(),
            'date_unpinning' => $this->dateTime(),
        ]);

        $this->createTable('auto_save_widget', [
            'id' => $this->primaryKey(),
            'type' => $this->string(255),
            'name' => $this->text(),
//            'materials_id' => $this->integer(11),
            'auto_save_materials_id' => $this->integer(11),
            'created' => $this->dateTime(),
            'modified' => $this->dateTime()
        ]);

        $this->createTable('auto_save_widget_map', [
            'id' => $this->primaryKey(),
//            'auto_save_widget_id' => $this->integer(11),
            'title' => $this->text(),
            'lat' => $this->text(),
            'lng' => $this->text(),
            'name' => $this->text(),
            'auto_save_materials_id' => $this->integer(11),
        ]);

        $this->createTable('auto_save_widget_tabs', [
            'id' => $this->primaryKey(),
//            'auto_save_widget_id' => $this->integer(11),
            'title' => $this->text(),
            'content' => $this->text(),
            'auto_save_materials_id' => $this->integer(11)
        ]);

        $this->createTable('auto_save_widget_gallery', [
            'id' => $this->primaryKey(),
//            'auto_save_widget_id' => $this->integer(11),
            'image' => $this->text(),
            'order_id' => $this->integer(11),
            'auto_save_materials_id' => $this->integer(11)
        ]);

        $this->createTable('auto_save_widget_accordion', [
            'id' => $this->primaryKey(),
//            'auto_save_widget_id' => $this->integer(11),
            'title' => $this->text(),
            'content' => $this->text(),
            'auto_save_materials_id' => $this->integer(11)
        ]);

        $this->createTable('auto_save_widget_youtube', [
            'id' => $this->primaryKey(),
            'youtube_url' => $this->text(),
            'auto_save_materials_id' => $this->integer(11)
        ]);

        $this->createTable('auto_save_widget_logo', [
            'id' => $this->primaryKey(),
            'image' => $this->text(),
            'url' => $this->text(),
            'type' => $this->integer(11),
            'auto_save_materials_id' => $this->integer(11),
            'created' => $this->dateTime(),
            'modified' => $this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('auto_save_materials');
        $this->dropTable('auto_save_widget');
        $this->dropTable('auto_save_widget_map');
        $this->dropTable('auto_save_widget_tabs');
        $this->dropTable('auto_save_widget_gallery');
        $this->dropTable('auto_save_widget_accordion');
        $this->dropTable('auto_save_widget_youtube');
        $this->dropTable('auto_save_widget_logo');
    }

}
