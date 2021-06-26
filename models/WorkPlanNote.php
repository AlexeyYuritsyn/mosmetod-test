<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_plan_note".
 *
 * @property int $id
 * @property int|null $work_plan_id
 * @property string|null $note_name
 * @property string|null $note_url
 */
class WorkPlanNote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_plan_note';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['work_plan_id'], 'default', 'value' => null],
            [['work_plan_id'], 'integer'],
            [['note_url'], 'string'],
            [['note_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_plan_id' => 'Work Plan ID',
            'note_name' => 'Название ссылки',
            'note_url' => 'URL ссылки',
        ];
    }
}
