<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\file\FileInput;
use \app\models\WorkPlanPeriod;

use phpnt\bootstrapSelect\BootstrapSelectAsset;

BootstrapSelectAsset::register($this);

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">
            <?php if(!$model->isNewRecord):?>
                План работы на <?= WorkPlanPeriod::$month[$model['month']] ?> <?= WorkPlanPeriod::$year[$model['year']] ?>
            <?php else:?>
                Новый план работы
            <?php endif;?>

            </h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/plan/add-event'],'get');?>
                <?= Html::a( 'Пример Excel',\yii\helpers\Url::to(['/template/example.xlsx']), ['class' => 'btn btn-primary']) ?>
                <?= Html::a( 'К списку плана работ',\yii\helpers\Url::to(['/plan/all-work-plan']), ['class' => 'btn btn-primary']) ?>
                <?= Html::submitButton( 'Добавить мероприятияе', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body" style="min-height: 750px;">
        <?php $form = ActiveForm::begin([
            'id' => 'upload-report-file'
        ]); ?>
        <div class="work-plan-grid">
            <div style="margin-right: 15px; width: 120px;">

                <?= $form->field($model, 'month')->dropDownList(WorkPlanPeriod::$month, [
                    'class'  => 'form-control work-plan-period-month selectpicker',
                    'disabled' => !$model->isNewRecord,
                    'data' => [
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ],
                ]) ?>

            </div>
            <div style="margin-right: 15px; width: 80px;">

                <?= $form->field($model, 'year')->dropDownList(WorkPlanPeriod::$year, [
                    'class'  => 'form-control work-plan-period-year selectpicker',
                    'disabled' => !$model->isNewRecord,
                    'data' => [
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]) ?>

            </div>
            <div style="margin-right: 15px;">

                <?= $form->field($model, 'user_groups_id')->dropDownList($user_groups_array, [
                    'class'  => 'form-control work-plan-user-groups-id selectpicker',
                    'value' => Yii::$app->getRequest()->get('user_groups_id')?Yii::$app->getRequest()->get('user_groups_id'):null,
                    'data' => [
                        'live-search' => 'true',
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]) ?>

            </div>
            <div style="margin-right: 15px;">
                <?=$form->field($model, "work_plan_input")->widget(FileInput::classname(), [
                    'options'=>[
                        'multiple'=>false,
                        'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ],
                    'pluginOptions'=>[
                        'previewFileType' => 'any',
                        'showUpload' => false,
                        'showPreview' => false,
                        'initialPreviewAsData'=>true,
                        'overwriteInitial'=>false,
                    ]
                ]);?>
            </div>
            <div style="margin-top: 25px;">
                <?= Html::submitButton('Загрузить файл', ['class' => 'btn btn-primary upload-report-file', 'name' => 'WorkPlanPeriod[upload-report-file]']) ?>
            </div>
            <?php if(!$model->isNewRecord):?>
                <div style="margin-top: 15px;">
                    <?= $form->field($model, 'not_included_main_report')->checkbox(['checked'=>Yii::$app->getRequest()->get('not_included_main_report') == '1'?'checked':null]) ?>
                </div>
                <div style="margin-top: 25px;">
                    <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter', 'name' => 'WorkPlanPeriod[filter-work-plan]']) ?>
                </div>
                <div style="margin-top: 25px;">
                    <?= Html::submitButton('Выгрузить в Excel', ['class' => 'btn btn-primary export-to-excel', 'name' => 'WorkPlanPeriod[export-to-excel]']) ?>
                </div>


            <?php endif;?>

        </div>
        <hr>
        <?php ActiveForm::end(); ?>

        <?php if(!$model->isNewRecord):?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn',
                        'contentOptions' => ['aria-label' => '#']],
                    [
                        'attribute' => 'type_event',
                        'format'    => 'html',
//                    'value'     => function($model){
//                        return \app\models\WorkPlanPeriod::$month[$model['type_event']];
//                    },
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('type_event')];
                        }
                    ],
                    [
                        'attribute' => 'for_whom',
                        'format'    => 'html',
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('for_whom')];
                        }
                    ],
                    [
                        'attribute' => 'user_groups_name',
                        'format'    => 'html',
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('user_groups_name')];
                        }
                    ],
                    [
                        'attribute' => 'event_name',
                        'format'    => 'html',
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('event_name')];
                        }
                    ],
                    [
                        'attribute' => 'responsible',
                        'format'    => 'html',
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('responsible')];
                        }
                    ],
                    [
                        'attribute' => 'fio',
                        'format'    => 'html',
                        'contentOptions' => function ($model) {
                            return ['aria-label' => $model->getAttributeLabel('fio')];
                        },
//                        'visible'=>false
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update-work-plan}',
                        'visibleButtons' => [
                            'update-work-plan' =>  true,
                        ],
                        'buttons' => [
                            'update-work-plan' => function ($url,$model,$key) {
                                return Html::a('Редактировать', ['/plan/update-event', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
                            }
                        ],
                    ],
                ],

            ]); ?>
        <?php endif;?>


    </div>
</div>


<?php

$script = <<< JS

    $(document).ready(function() {
        $('body').on('click','.export-to-excel, .upload-report-file',function() {
            let select_group_id = $('.work-plan-user-groups-id option:selected').val();
            if(select_group_id == '' || select_group_id == '0')
            {
                alert('Выберите направление');
                return false;
            }
        });
            
        $('html').keydown(function(e){
          if (e.keyCode == 13) {
            $('.button-filter').onclick();
          }
        });

   }); 
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>