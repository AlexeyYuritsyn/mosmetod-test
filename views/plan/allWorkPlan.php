<?php
use yii\grid\GridView;
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
//use kartik\file\FileInput;
//use \app\models\WorkPlanPeriod;

//use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">План работы</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/plan/add-work-plan'],'get');?>
                <?= Html::submitButton( 'Добавить план работ', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#']],
                [
                    'attribute' => 'month',
                    'format'    => 'html',
                    'value'     => function($model){
                        return \app\models\WorkPlanPeriod::$month[$model['month']];
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('month')];
                    }
                ],
                [
                    'attribute' => 'year',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('year')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-work-plan}',
                    'visibleButtons' => [
                        'update-work-plan' =>  true,
                    ],
                    'buttons' => [
                        'update-work-plan' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/plan/update-work-plan', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
                        }
                    ],
                ],
            ],

        ]); ?>
    </div>
</div>






