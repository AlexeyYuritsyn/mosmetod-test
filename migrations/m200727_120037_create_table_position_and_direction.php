<?php

use yii\db\Migration;

/**
 * Class m200727_120037_create_table_position_and_direction
 */
class m200727_120037_create_table_position_and_direction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('position_and_direction',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'position' => $this->integer(11)
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Директор',
            'position' => 1
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Заместители директора',
            'position' => 2
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Адаптированные основные образовательные программы',
            'position' => 3
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Дошкольное образование',
            'position' => 4
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Начальная школа',
            'position' => 5
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Биология',
            'position' => 6
        ]);

        $this->insert('position_and_direction',[
            'name' => 'География',
            'position' => 7
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Естествознание',
            'position' => 8
        ]);

        $this->insert('position_and_direction',[
            'name' => 'ИЗО,МХК',
            'position' => 9
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Иностранные языки',
            'position' => 10
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Информатика, ИКТ',
            'position' => 11
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Информационно-библиотечное обеспечение',
            'position' => 12
        ]);

        $this->insert('position_and_direction',[
            'name' => 'История',
            'position' => 13
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Математика',
            'position' => 14
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Музыка',
            'position' => 15
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Обществознание',
            'position' => 16
        ]);

        $this->insert('position_and_direction',[
            'name' => 'ОДНКНР',
            'position' => 17
        ]);

        $this->insert('position_and_direction',[
            'name' => 'ОРКСЭ',
            'position' => 18
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Право',
            'position' => 19
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Русский язык и литература',
            'position' => 20
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Физика',
            'position' => 21
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Химия',
            'position' => 22
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Экология',
            'position' => 23
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Экономика',
            'position' => 24
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Проектный офис «Предпрофессиональное образование»',
            'position' => 25
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Отдел сопровождения проектов',
            'position' => 26
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Работа с молодыми учителями',
            'position' => 27
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Робототехника',
            'position' => 28
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Технические конкурсы',
            'position' => 29
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Творческие конкурсы',
            'position' => 30
        ]);

        $this->insert('position_and_direction',[
            'name' => 'ОБЖ',
            'position' => 31
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Технология',
            'position' => 32
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Физическая культура',
            'position' => 33
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Воспитательная работа',
            'position' => 34
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Профилактика негативных проявлений',
            'position' => 35
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Профилактика детского травматизма',
            'position' => 36
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Профориентация',
            'position' => 37
        ]);

        $this->insert('position_and_direction',[
            'name' => 'СПО',
            'position' => 38
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Пресс-служба',
            'position' => 39
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Учебно-методическое обеспечение',
            'position' => 40
        ]);

        $this->insert('position_and_direction',[
            'name' => 'Дополнительное профессиональное образование',
            'position' => 41
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('position_and_direction');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200727_120037_create_table_position_and_direction cannot be reverted.\n";

        return false;
    }
    */
}
