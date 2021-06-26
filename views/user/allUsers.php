<?php
use yii\grid\GridView;
use yii\helpers\Html;

use phpnt\bootstrapSelect\BootstrapSelectAsset;
BootstrapSelectAsset::register($this);

//use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Пользователи</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/user/add-user'],'get');?>
                <?= Html::submitButton( 'Добавить пользователя', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body">
        <?php echo Html::beginForm(['/user/all-users'],'get');?>

        <div class="filter-wrapper">

            <div class="filter-block school-filter-block">
                <span class="filter-header-text">ФИО пользователя или Email</span>
                <?=Html::dropDownList('user_id', Yii::$app->getRequest()->get('user_id'), $users_array, [
                    'class'  => 'form-control selectpicker',
                    'data' => [
                        'live-search' => 'true',
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]);?>
            </div>


            <div class="filter-block school-filter-block">
                <span class="filter-header-text">Основная роль</span>
                <?=Html::dropDownList('role', Yii::$app->getRequest()->get('role'), $role_array, [
                    'class'  => 'form-control selectpicker',
                    'data' => [
                        'live-search' => 'true',
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]);?>
            </div>

            <div class="filter-block school-filter-block">
                <span class="filter-header-text"><?=Html::checkbox('not_user',Yii::$app->getRequest()->get('not_user'));?> Не пользователь</span>
            </div>

            <div class="filter-block">
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                <?= Html::a('Сброс', ['/user/all-users','not_user' => true], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php echo Html::endForm();?>


        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                    'contentOptions' => ['aria-label' => '#']],
                [
                    'attribute' => 'fio',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('fio')];
                    }
                ],
                [
                    'attribute' => 'email',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('email')];
                    }
                ],
                [
                    'attribute' => 'role',
                    'format'    => 'html',
                    'value' => function ($data) {
                        return  \app\models\Users::$roles[$data['role']];
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('role')];
                    }
                ],
                [
                    'attribute' => 'last_visit_date',
                    'format'    => 'html',
                    'value' => function ($data) {
                        return  date("d.m.Y H:i:s", strtotime($data['last_visit_date']));
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('last_visit_date')];
                    }
                ],
                [
                    'attribute' => 'register_date',
                    'format'    => 'html',
                    'value' => function ($data) {
                        return  date("d.m.Y H:i:s",strtotime($data['register_date']));
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('register_date')];
                    }
                ],
                [
                    'attribute' => 'in_archive',
                    'format'    => 'boolean',
//                    'value' => function ($data) {
//                        return  date("d.m.Y H:i:s",strtotime($data['in_archive']));
//                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('in_archive')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-user}',
                    'visibleButtons' => [
                        'update-user' =>  true,
                    ],
                    'buttons' => [
                        'update-user' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/user/update-user', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
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





