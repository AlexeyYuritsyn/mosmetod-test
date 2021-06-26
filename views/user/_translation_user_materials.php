<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

//use app\models\Users;
use phpnt\bootstrapSelect\BootstrapSelectAsset;

BootstrapSelectAsset::register($this);

?>
<div class="site-login" style="margin-top: 20px; min-height: 400px;">

    <?php $form = ActiveForm::begin([
        'id' => 'translation_user_materials',
    ]); ?>
    <div class="filter-wrapper">
        <div class="filter-block" style="width: 800px;">
            <span class="filter-header-text">Выберите пользователя для передачи всех материалов другому пользователю</span>
            <?=Html::dropDownList('Users[created_by]', null, $users_array, [
                'class'  => 'form-control selectpicker',
                'data' => [
                    'live-search' => 'true',
                    'size' => 10,
                    'title' => 'Ничего не выбрано',
                ]
            ]);?>
        </div>
        <div class="filter-block">
            <?= Html::hiddenInput('Users[scenario]','translation_user_materials')?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
