<?php

use yii\db\Migration;

/**
 * Class m200513_112929_color_projects
 */
class m200513_112929_color_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('color_projects', [
            'id' => $this->primaryKey(),
            'start' => $this->string(7),
            'end' => $this->string(7),
            'name' => $this->string(255)
        ]);

        $this->insert('color_projects',[
            'start' => '#1ABC9C',
            'end' => '#1AB2BC',
            'name' => 'Бирюза'
        ]);

        $this->insert('color_projects',[
            'start' => '#00B08E',
            'end' => '#00777D',
            'name' => 'Зеленое Море'
        ]);

        $this->insert('color_projects',[
            'start' => '#E1B400',
            'end' => '#C36A00',
            'name' => 'Апельсин'
        ]);

        $this->insert('color_projects',[
            'start' => '#DA5353',
            'end' => '#F99500',
            'name' => 'Персик'
        ]);

        $this->insert('color_projects',[
            'start' => '#3CBF01',
            'end' => '#029226',
            'name' => 'Свежий лист'
        ]);

        $this->insert('color_projects',[
            'start' => '#2ECC71',
            'end' => '#059B26',
            'name' => 'Изумруд'
        ]);

        $this->insert('color_projects',[
            'start' => '#27AE60',
            'end' => '#007047',
            'name' => 'Нефрит'
        ]);

        $this->insert('color_projects',[
            'start' => '#E67E22',
            'end' => '#BB4F00',
            'name' => 'Морковь'
        ]);

        $this->insert('color_projects',[
            'start' => '#D1641C',
            'end' => '#973D01',
            'name' => 'Тыква'
        ]);

        $this->insert('color_projects',[
            'start' => '#DEC672',
            'end' => '#9F7434',
            'name' => 'Кремовый'
        ]);

        $this->insert('color_projects',[
            'start' => '#05C9D9',
            'end' => '#015BDA',
            'name' => 'Стратосфера'
        ]);

        $this->insert('color_projects',[
            'start' => '#3498DB',
            'end' => '#3445DB',
            'name' => 'Небо'
        ]);

        $this->insert('color_projects',[
            'start' => '#335AE4',
            'end' => '#06039E',
            'name' => 'Кобальт'
        ]);

        $this->insert('color_projects',[
            'start' => '#F35646',
            'end' => '#9A182F',
            'name' => 'Кардинал'
        ]);

        $this->insert('color_projects',[
            'start' => '#D43E2E',
            'end' => '#730015',
            'name' => 'Гранат'
        ]);

        $this->insert('color_projects',[
            'start' => '#ACACAC',
            'end' => '#8B0019',
            'name' => 'Вишневый'
        ]);

        $this->insert('color_projects',[
            'start' => '#ACACAC',
            'end' => '#8B0019',
            'name' => 'Сапфир'
        ]);

        $this->insert('color_projects',[
            'start' => '#6001DA',
            'end' => '#4A05D9',
            'name' => 'Фиолетовый'
        ]);

        $this->insert('color_projects',[
            'start' => '#9B59B6',
            'end' => '#540076',
            'name' => 'Аметист'
        ]);

        $this->insert('color_projects',[
            'start' => '#8D4CE0',
            'end' => '#29006C',
            'name' => 'Эфир'
        ]);

        $this->insert('color_projects',[
            'start' => '#C9CBCC',
            'end' => '#4B5153',
            'name' => 'Сталь'
        ]);

        $this->insert('color_projects',[
            'start' => '#8FBC9C',
            'end' => '#5B302D',
            'name' => 'Хамелеон'
        ]);

        $this->insert('color_projects',[
            'start' => '#3C05D9',
            'end' => '#360057',
            'name' => 'Космос'
        ]);

        $this->insert('color_projects',[
            'start' => '#486786',
            'end' => '#002040',
            'name' => 'Глубинный'
        ]);

        $this->insert('color_projects',[
            'start' => '#2C3E50',
            'end' => '#030F1C',
            'name' => 'Гранит'
        ]);

        $this->insert('color_projects',[
            'start' => '#82949C',
            'end' => '#392D2D',
            'name' => 'Свинец'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('color_projects');
    }
}
