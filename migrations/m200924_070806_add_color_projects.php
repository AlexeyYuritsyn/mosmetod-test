<?php

use yii\db\Migration;

/**
 * Class m200924_070806_add_color_projects
 */
class m200924_070806_add_color_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('color_projects',[
            'start' => '#BE002E',
            'end' => '#FF003D',
            'name' => 'Американский розовый'
        ]);
        $this->insert('color_projects',[
            'start' => '#DA2B55',
            'end' => '#FF5B83',
            'name' => 'Розовый Крайола'
        ]);
        $this->insert('color_projects',[
            'start' => '#972BDA',
            'end' => '#FF5BD1',
            'name' => 'Сине-лиловый'
        ]);

        $this->insert('color_projects',[
            'start' => '#C78F00',
            'end' => '#FFDB5B',
            'name' => 'Нарциссово-желтый'
        ]);
        $this->insert('color_projects',[
            'start' => '#0063BE',
            'end' => '#FF00A8',
            'name' => 'Модная фуксия'
        ]);
        $this->insert('color_projects',[
            'start' => '#DA2B94',
            'end' => '#5BFFD8',
            'name' => 'Амарантовый светло-вишневый'
        ]);
        $this->insert('color_projects',[
            'start' => '#01AACF',
            'end' => '#BAFF4A',
            'name' => 'Зелено-желтый'
        ]);
        $this->insert('color_projects',[
            'start' => '#4900C1',
            'end' => '#06FDC2',
            'name' => 'Умеренный весенний зеленый'
        ]);

        $this->insert('color_projects',[
            'start' => '#6BBE00',
            'end' => '#9EFF00',
            'name' => 'Весенний бутон'
        ]);
        $this->insert('color_projects',[
            'start' => '#D8637F',
            'end' => '#FFA7BC',
            'name' => 'Сакура'
        ]);
        $this->insert('color_projects',[
            'start' => '#A9007A',
            'end' => '#F30EB3',
            'name' => 'Баклажановый'
        ]);
    }

    /**
     * {@inheritdoc}
     */

    /*
    public function safeDown()
    {
        echo "m200924_070806_add_color_projects cannot be reverted.\n";

        return false;
    }


    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200924_070806_add_color_projects cannot be reverted.\n";

        return false;
    }
    */
}
