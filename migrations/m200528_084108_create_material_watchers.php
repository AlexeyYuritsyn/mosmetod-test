<?php

use yii\db\Migration;

/**
 * Class m200528_084108_create_material_watchers
 */
class m200528_084108_create_material_watchers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('material_watchers', [
            'id' => $this->primaryKey(),
            'material_id' => $this->integer(11),
            'material_watchers_email_id' => $this->integer(11)
        ]);

        $this->createTable('material_watchers_email', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255),
            'in_archive'=>$this->boolean()->defaultValue(false)
        ]);

        $users = \app\models\Users::find()->where(['in_archive'=>false])->all();

        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $this->insert('material_watchers_email',['email' => $user['email'], 'in_archive'=>false]);
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('material_watchers');
        $this->dropTable('material_watchers_email');
    }

}
