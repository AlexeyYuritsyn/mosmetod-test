<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use kartik\file\FileInput;

//use yii\helpers\Url;
use phpnt\bootstrapSelect\BootstrapSelectAsset;

BootstrapSelectAsset::register($this);

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Вебинар</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'webinars'
    ]); ?>
    <div class="box-body" style="min-height: 700px;">

        <?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'description')->textarea();?>


        <?= $form->field($model, 'youtube_url')->textInput(['placeholder'=>'https://youtu.be/']); ?>

        <?= $form->field($model, 'user_groups_id')->dropDownList($user_groups_array, [
            'class'  => 'form-control selectpicker',
            'data' => [
                'live-search' => 'true',
                'size' => 7,
                'title' => 'Ничего не выбрано',
            ],
        ]); ?>


        <?= $form->field($model, 'time_created')->textInput(['autocomplete'=>'off']) ?>

        <?= $form->field($model, 'in_archive')->checkbox() ?>
    </div>

    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php

$script = <<< JS


jQuery('#webinars-time_created').datetimepicker({lang: 'fr', format:'d.m.Y H:i', dayOfWeekStart: 1});
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>