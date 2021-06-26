<?php
use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Теги</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/administrator/add-tag'],'get');?>
                <?= Html::submitButton( 'Добавить тег', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">
        <?php echo Html::beginForm(['/administrator/all-tags'],'get');?>
            <div class="filter-wrapper">
                <div class="filter-block school-filter-block">
                    <span class="filter-header-text">Название тега</span>
                    <?=Html::textInput('name',Yii::$app->getRequest()->get('name'),['class'  => 'form-control']);?>
                </div>
                <div class="filter-block">
                    <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                    <?= Html::a('Сброс', ['/administrator/all-tags'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php echo Html::endForm();?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#'],],
                [
                    'attribute' => 'name',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('name')];
                    }
                ],
                [
                    'attribute' => 'published',
                    'format'    => 'boolean',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('published')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-tag}',
                    'visibleButtons' => [
                        'update-tag' =>  true,
                    ],
                    'buttons' => [
                        'update-tag' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/administrator/update-tag', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
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





