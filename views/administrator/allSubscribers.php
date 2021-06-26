<?php
use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Подписчики</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/administrator/add-subscribers'],'get');?>
                <?= Html::submitButton( 'Добавить подписчика', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">
        <?php echo Html::beginForm(['/administrator/all-subscribers'],'get');?>
        <div class="filter-wrapper">
            <div class="filter-block school-filter-block">
                <span class="filter-header-text">Email</span>
                <?=Html::textInput('email',Yii::$app->getRequest()->get('email'),['class'  => 'form-control']);?>
            </div>
            <div class="filter-block">
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                <?= Html::a('Сброс', ['/administrator/all-subscribers'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php echo Html::endForm();?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#'],],
                [
                    'attribute' => 'email',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('email')];
                    }
                ],
                [
                    'attribute' => 'status',
                    'format'    => 'html',
                    'value'     => function($model){
                        return \app\models\Subscribers::$statuses[$model['status']];
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('status')];
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
                    'attribute' => 'time_send',
                    'format'    => 'html',
                    'value'     => function($model){
                        return $model['time_send'] != ''? date('d.m.Y H:i',strtotime($model['time_send'])):'Даты нет';
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('time_send')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-subscribers}',
                    'visibleButtons' => [
                        'update-subscribers' =>  true,
                    ],
                    'buttons' => [
                        'update-subscribers' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/administrator/update-subscribers', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
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




