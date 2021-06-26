<?php

use yii\db\Migration;
use \app\models\Notifications;


/**
 * Class m200709_102803_create_table_notifications
 */
class m200709_102803_create_table_notifications extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('notifications',[
                'id' => $this->primaryKey(),
                'subject' => $this->string(255),
                'body' => $this->text(),
                'type' => $this->integer(11)
            ]);

        $this->insert('notifications',[
            'subject' => 'Материал отправлен на подтверждение',
            'body' => 'Материал «<%link%>» отправлен на подтверждение Старшему методисту.',
            'type' => Notifications::MATERIAL_SENT_FOR_CONFIRMATION
        ]);

        $this->insert('notifications',[
            'subject' => 'Новый материал ожидает подтверждения',
            'body' => 'Вам необходимо выполнить проверку и подтверждение нового материала «<%link%>».',
            'type' => Notifications::NEW_MATERIAL_AWAITING_CONFIRMATION
        ]);

        $this->insert('notifications',[
            'subject' => 'Материал подтвержден',
            'body' => 'Материал «<%link%>» подтвержден Старшим методистом.',
            'type' => Notifications::MATERIAL_CONFIRMED_SENIOR_METHODIST
        ]);

        $this->insert('notifications',[
            'subject' => 'Материал подтвержден',
            'body' => 'Материал «<%link%>» подтвержден.',
            'type' => Notifications::MATERIAL_CONFIRMED
        ]);

        $this->insert('notifications',[
            'subject' => 'Новый материал ожидает публикации',
            'body' => 'Вам необходимо выполнить проверку и публикацию нового материала «<%link%>».',
            'type' => Notifications::NEW_MATERIAL_AWAITING_PUBLICATION
        ]);

        $this->insert('notifications',[
            'subject' => 'Материал опубликован',
            'body' => 'Материал «<%link%>» опубликован на сайте.',
            'type' => Notifications::MATERIAL_PUBLISHED
        ]);

        $this->insert('notifications',[
            'subject' => 'Материал отправлен на доработку',
            'body' => 'Вам необходимо выполнить доработку материала «<%link%>». Подробности указаны в личном кабинете.',
            'type' => Notifications::MATERIAL_SENT_FOR_REVISION
        ]);

        $this->insert('notifications',[
            'subject' => 'Материал перенесён в архив',
            'body' => 'Материал «<%link%>» перенесен в архив.',
            'type' => Notifications::MATERIAL_MOVED_TO_ARCHIVE
        ]);


        $this->insert('notifications',[
            'subject' => 'Восстановления пароля',
            'body' => '<%link%>',
            'type' => Notifications::RECORD_ACCOUNT_RECOVERY
        ]);

        $this->insert('notifications',[
            'subject' => 'В план работ были внесены изменения',
            'body' => '<p>Пользователь <%user%> внес изменения в план работ на <%month%> <%year%> по направлению «<%direction%>»</p><table style="border-collapse: collapse; width: 100%; background-color: #ECF0F1;" border="1"><tbody><tr><td style="width: 50%;">Удаленные мероприятия</td><td style="width: 50%;"><%remote_events%></td></tr><tr><td style="width: 50%;">Добавленные мероприятия</td><td style="width: 50%;"><%added_events%></td></tr><tr><td style="width: 50%;">Отредактированные мероприятия</td><td style="width: 50%;"><%edited_events%></td></tr></tbody></table>',
            'type' => Notifications::WORK_PLAN_HAS_CHANGED
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('notifications');
    }

}
