<?php
use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Группы пользователей</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/user/add-user-groups'],'get');?>
                <?= Html::submitButton( 'Добавить группу', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">
        <?php echo Html::beginForm(['/user/all-user-groups'],'get');?>
            <div class="filter-wrapper">
                <div class="filter-block school-filter-block">
                    <span class="filter-header-text">Название группы пользователей</span>
                    <?=Html::textInput('name',Yii::$app->getRequest()->get('name'),['class'  => 'form-control']);?>
                </div>
                <div class="filter-block">
                    <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                    <?= Html::a('Сброс', ['/user/add-user-groups'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        <?php echo Html::endForm();?>


        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#']],
                [
                    'attribute' => 'name',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('name')];
                    }
                ],
                [
                    'attribute' => 'in_archive',
                    'format'    => 'html',
                    'value' => function ($data) {
                        return  $data['in_archive'] == true?'Да':'Нет';
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('in_archive')];
                    }
                ],
                [
                    'attribute' => 'role_users_in_user_groups_users_id',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('role_users_in_user_groups_users_id')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-user-groups}',
                    'visibleButtons' => [
                        'update-user-groups' =>  true,
                    ],
                    'buttons' => [
                        'update-user-groups' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/user/update-user-groups', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
                        }
                    ],
                ],
            ],
            'options' => [
                'style'=>'margin-top: 20px;'
            ],
            'pager' => [
                'firstPageLabel' => '««',
                'lastPageLabel'  => '»»'
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





