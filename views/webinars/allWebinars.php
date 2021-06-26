<?php
use yii\grid\GridView;
use yii\helpers\Html;

//use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

use phpnt\bootstrapSelect\BootstrapSelectAsset;

BootstrapSelectAsset::register($this);

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Вебинары</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/webinars/add-webinars'],'get');?>
                <?= Html::submitButton( 'Добавить вебинар', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body" style="min-height: 700px;">

        <?php echo Html::beginForm(['/webinars/all-webinars','in_archive'=>Yii::$app->getRequest()->get('in_archive')],'get');?>
        <div class="filter-wrapper">
            <div class="filter-block school-filter-block">
                <span class="filter-header-text">Название проекта</span>
                <?=Html::textInput('title',Yii::$app->getRequest()->get('title'),['class'  => 'form-control']);?>
            </div>
            <div class="filter-block" style="width: 230px;">
                <span class="filter-header-text">Категория</span>
                <?=Html::dropDownList('user_groups_id', Yii::$app->getRequest()->get('user_groups_id'), $user_groups_array, [
                    'class'  => 'form-control selectpicker',
                    'data' => [
                        'live-search' => 'true',
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]);?>
            </div>
            <div class="filter-block">
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                <?= Html::a('Сброс', ['/webinars/all-webinars','in_archive'=>Yii::$app->getRequest()->get('in_archive')], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php echo Html::endForm();?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#'],],
                [
                    'attribute' => 'title',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('title')];
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
                    'attribute' => 'time_created',
                    'format'    => 'html',
                    'value'     => function($model){
                        return date('d.m.Y H:i',strtotime($model['time_created']));
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('time_created')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-webinars}',
                    'visibleButtons' => [
                        'update-webinars' =>  true,
                    ],
                    'buttons' => [
                        'update-webinars' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/webinars/update-webinars', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
                        }
                    ],
                ],
            ],

        ]); ?>
    </div>
</div>
<?php

$script = <<< JS
$(document).ready(function() 
    {
        $('html').keydown(function(e){
          if (e.keyCode == 13) {
            $('.button-filter').onclick();
          }
        });

    });
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>






