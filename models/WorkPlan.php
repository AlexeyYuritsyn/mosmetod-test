<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_plan".
 *
 * @property int $id
 * @property int|null $work_plan_period_id
 * @property int|null $user_groups_id
 * @property string|null $event_time
 * @property string|null $event_name
 * @property string|null $district
 * @property string|null $location
 * @property string|null $responsible
 * @property string|null $note
 */
class WorkPlan extends \yii\db\ActiveRecord
{
    public $event_date;
    public $event_note;

    public $start_date;
    public $end_date;

    public $month;
    public $year;

    public $user_groups_name;

    public $fio;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_event','for_whom','user_groups_id', 'event_name', 'location', 'responsible', 'users_id'], 'required'],
            [['work_plan_period_id'], 'default', 'value' => null],
            [['work_plan_period_id','user_groups_id'], 'integer'],
            [['in_archive','not_included_main_report'], 'boolean'],
            [['event_name', 'district', 'location', 'responsible', 'description'], 'string'],
            [['type_event','for_whom', 'event_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_plan_period_id' => 'Work Plan Period ID',
            'type_event' => 'Тип мероприятия',
            'for_whom' => 'Для кого',
            'user_groups_id' => 'Предмет/направление',
            'user_groups_name' => 'Предмет/направление',
            'event_time' => 'Время',
            'event_name' => 'Название события',
            'district' => 'Округ',
            'location' => 'Место проведения',
            'responsible' => 'Ответственный',
            'description' => 'Описание',
            'in_archive' => 'Удалить мероприятие',
            'month' => 'Месяц плана работ',
            'year' => 'Год плана работ',
            'fio' => 'ФИО автора',
            'not_included_main_report' => 'Не входит в основной отчет',
        ];
    }
}
