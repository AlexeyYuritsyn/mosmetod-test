<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_plan_period".
 *
 * @property int $id
 * @property int|null $month
 * @property int|null $year
 * @property bool|null $in_archive
 */
class WorkPlanPeriod extends \yii\db\ActiveRecord
{

    public $work_plan_input;
    public $user_groups_id;
    public $not_included_main_report;

    public static $month = [
        '1' =>'Январь',
        '2' =>'Февраль',
        '3' =>'Март',
        '4' =>'Апрель',
        '5' =>'Май',
        '6' =>'Июнь',
        '7' =>'Июль',
        '8' =>'Август',
        '9' =>'Сентябрь',
        '10' =>'Октябрь',
        '11' =>'Ноябрь',
        '12' =>'Декабрь'
    ];

    public static $year = [
        '2020' =>'2020',
        '2021' =>'2021',
        '2022' =>'2022',
        '2023' =>'2023',
        '2024' =>'2024',
        '2025' =>'2025',
        '2026' =>'2026',
        '2027' =>'2027',
        '2028' =>'2028',
        '2029' =>'2029',
        '2030' =>'2030',

    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_plan_period';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month', 'year'], 'default', 'value' => null],
            [['month', 'year'], 'integer'],
//            [['in_archive'], 'boolean'],
            [['month','year'],'custom_function_validation_month_and_year'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Месяц',
            'year' => 'Год',
            'user_groups_id' => 'Предмет/направление',
            'not_included_main_report' => 'Не входит в основной отчет',
//            'in_archive' => 'In Archive',
            'work_plan_input' => 'Excel файл с планом работы'
        ];
    }

    function custom_function_validation_month_and_year($attribute)
    {
        if(($attribute == 'month'))
        {
            $count = self::find()->where(['month'=>$this->month,'year'=>$this->year])->all();

            if(($this->isNewRecord && count($count) > 0) || (!$this->isNewRecord && count($count) >= 1 && $count[0]['id'] != $this->id))
            {
                $this->addError('month', 'План работ за '.$this->month.'.'.$this->year.' года уже создан');
            }

        }

    }
}
