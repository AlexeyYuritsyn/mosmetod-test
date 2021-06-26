<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \app\models\WorkPlanPeriod;
use wbraganca\dynamicform\DynamicFormWidget;


use phpnt\bootstrapSelect\BootstrapSelectAsset;

BootstrapSelectAsset::register($this);


$postWorkPlanPeriod = Yii::$app->getRequest()->post('WorkPlanPeriod');

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Мероприятие</h3>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'work-plan'
    ]); ?>
    <div class="box-body">

        <?= $form->field($work_plans, 'month')->dropDownList(WorkPlanPeriod::$month, [
            'class'  => 'form-control',
            'data' => [
                'size' => 10,
                'title' => 'Ничего не выбрано',
            ],
        ])->label('Месяц плана работ') ?>
        <?= $form->field($work_plans, 'year')->dropDownList(WorkPlanPeriod::$year, [
            'class'  => 'form-control',
            'data' => [
                'size' => 10,
                'title' => 'Ничего не выбрано',
            ],
        ])->label('Год плана работ') ?>


        <?= $form->field($work_plans, 'type_event')?>
        <?= $form->field($work_plans, 'for_whom')?>
        <?= $form->field($work_plans, 'user_groups_id')->dropDownList($user_groups_array, [
            'class'  => 'form-control selectpicker',
            'data' => [
                'live-search' => 'true',
                'size' => 10,
                'title' => 'Ничего не выбрано',
            ],
        ])?>

        <?= $form->field($work_plans, 'event_time')?>
        <?= $form->field($work_plans, 'event_name')?>
        <?= $form->field($work_plans, 'district')?>
        <?= $form->field($work_plans, 'location')?>
        <?= $form->field($work_plans, 'responsible')?>
        <?= $form->field($work_plans, 'description')?>
<!--        <?//= $form->field($work_plans, 'in_archive')->checkbox()?>-->
        <?= $form->field($work_plans, 'not_included_main_report')->checkbox()?>


<!--        <?//= Html::checkbox('WorkPlanDelete',$model['in_archive']); ?>--><!-- Удалить план работ-->

        <table style="width: 100%">
            <tr>
                <td class="vcenter">
                    <?php

                    $key = 0;
                    $i = 0;
                    $j = 0;
                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper_work_plan_date', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item',//'.item', // required: css class
                        'limit' => 50, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item', // css class
                        'deleteButton' => '.remove-item', // css class
                        'model' => $work_plan_date[0],
                        'formId' => 'work-plan',
                        'formFields' => [
                            'work_plan_id',
                            'start_date',
                            'end_date'
                        ],
                    ]); ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th class="text-center" style="width: 90px;">
                                <button type="button" class="add-item btn btn-success btn-xs weekend-add-btn"><span class="glyphicon glyphicon-plus"></span></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php

                        foreach ($work_plan_date as $key_work_plan_date => $value_work_plan_date):?>

                            <tr class="item">
                                <td class="vcenter admin-input-container">
                                    <?php
                                        echo $form->field($value_work_plan_date, "[$key_work_plan_date]start_date")->textInput(['class'=>'form-control work-plan-start-date','autocomplete'=>'off']);
                                        echo $form->field($value_work_plan_date, "[$key_work_plan_date]end_date")->textInput(['class'=>'form-control work-plan-end-date','autocomplete'=>'off']);
                                    ?>
                                </td>
                                <td class="text-center vcenter admin-input-container">
                                    <button type="button" class="remove-item btn btn-danger btn-xs" ><span class="glyphicon glyphicon-minus"></span></button>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end(); ?>
                </td>
                <td class="vcenter">
                    <?php


                    DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper_work_plan_note', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-items', // required: css class selector
                        'widgetItem' => '.item',//'.item', // required: css class
                        'limit' => 50, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-item-note', // css class
                        'deleteButton' => '.remove-item-note', // css class
                        'model' => $work_plan_note[0],
                        'formId' => 'work-plan',
                        'formFields' => [
                            'note_name',
                            'note_url'
                        ],
                    ]); ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Ссылка на мероприятие</th>
                            <th class="text-center" style="width: 90px;">
                                <button type="button" class="add-item-note btn btn-success btn-xs weekend-add-btn"><span class="glyphicon glyphicon-plus"></span></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <?php

                        foreach ($work_plan_note as $key_work_plan_note => $value_work_plan_note):?>

                            <tr class="item">
                                <td class="vcenter admin-input-container">
                                    <?php
                                        echo $form->field($value_work_plan_note, "[$key_work_plan_note]note_name")->textInput(['class'=>'form-control']);
                                        echo $form->field($value_work_plan_note, "[$key_work_plan_note]note_url")->textInput(['class'=>'form-control']);
                                    ?>
                                </td>
                                <td class="text-center vcenter admin-input-container">
                                    <button type="button" class="remove-item-note btn btn-danger btn-xs" ><span class="glyphicon glyphicon-minus"></span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php DynamicFormWidget::end(); ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="box-footer">
        <div class="pull-right">
            <?= Html::submitButton('Удалить', ['class' => 'btn btn-primary in-archive-event','name' => 'in-archive-event']) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php

$script = <<< JS

    $(document).ready(function() {
        
        setInterval(function() {
            jQuery('.work-plan-start-date, .work-plan-end-date').datetimepicker({timepicker:false,lang: 'fr', format:'d.m.Y', dayOfWeekStart: 1});
            // $('.work-plan-month').val($('.work-plan-period-month').val());
            // $('.work-plan-year').val($('.work-plan-period-year').val());
        }, 700); 
        
        // $('body').on('change','.work-plan-period-month',function() {
        //      $('.work-plan-month').val($(this).val());
        // }); 
        // $('body').on('change','.work-plan-period-year',function() {
        //   $('.work-plan-year').val($(this).val());
        // });
            
   }); 
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>

