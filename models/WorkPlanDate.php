<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_plan_date".
 *
 * @property int $id
 * @property int|null $work_plan_id
 * @property string|null $start_date
 * @property string|null $end_date
 */
class WorkPlanDate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_plan_date';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_date', 'end_date'], 'required'],
            [['work_plan_id'], 'default', 'value' => null],
            [['work_plan_id'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
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
            'start_date' => 'Дата начала',
            'end_date' => 'Дата конца',
        ];
    }
}
