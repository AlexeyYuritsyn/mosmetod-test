<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use app\models\Users;


?>
<div class="site-login" style="margin-top: 20px;">

    <?php $form = ActiveForm::begin([
        'id' => 'change_password_user',
    ]); ?>

        <?= $form->field($model, 'new_password')->passwordInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'repeat_password')->passwordInput() ?>
        <?= Html::hiddenInput('Users[scenario]','change_password_user')?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>

    <?php ActiveForm::end(); ?>

</div>
