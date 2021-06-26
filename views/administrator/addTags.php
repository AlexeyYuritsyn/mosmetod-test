<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Тег</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'material_tags'
    ]); ?>
    <div class="box-body">

        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'published')->checkbox() ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>