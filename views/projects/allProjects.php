<?php
use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Проекты</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/projects/add-projects'],'get');?>
                <?= Html::submitButton( 'Добавить проект', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">

        <?php echo Html::beginForm(['/projects/all-projects','outdated'=>Yii::$app->getRequest()->get('outdated')],'get');?>
        <div class="filter-wrapper">
            <div class="filter-block school-filter-block">
                <span class="filter-header-text">Название проекта</span>
                <?=Html::textInput('title',Yii::$app->getRequest()->get('title'),['class'  => 'form-control']);?>
            </div>
            <div class="filter-block">
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                <?= Html::a('Сброс', ['/projects/all-projects','outdated'=>Yii::$app->getRequest()->get('outdated')], ['class' => 'btn btn-primary']) ?>
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-projects}',
                    'visibleButtons' => [
                        'update-projects' =>  true,
                    ],
                    'buttons' => [
                        'update-projects' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/projects/update-projects', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
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





