<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\tinymce\TinyMce;
//use kartik\file\FileInput;
use yii\helpers\Url;
use budyaga\cropper\Widget;
use kartik\select2\Select2;

use app\models\Users;


?>
<div class="site-login" style="margin-top: 20px; ">

    <?php $form = ActiveForm::begin([
        'id' => 'basic_information_user',
        'options' => ['enctype'=>'multipart/form-data']
    ]); ?>
        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'second_name')->textInput() ?>
        <?= $form->field($model, 'first_name')->textInput() ?>
        <?= $form->field($model, 'third_name')->textInput() ?>

    <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>

        <?php if(Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
            <?= $form->field($model, 'role')->dropDownList(Users::$roles, [
                'class'  => 'form-control selectpicker',
                'data' => [
//                'style' => 'btn-primary',
//                'live-search' => 'true',
                    'size' => 7,
                    'title' => 'Ничего не выбрано',
                ],
            ])->label('Роль'); ?>

        <?php else:?>
            <?php if($model['role'] == Users::ROLE_ADMIN):?>
                <div class="form-group field-users-role">
                    <label class="control-label" for="users-role">Роль:</label><br>
                    <?=Users::$roles[Users::ROLE_ADMIN]?>
                </div>
            <?php else:?>
                <?= $form->field($model, 'role')->dropDownList([
                    Users::ROLE_MODERATOR =>'Модератор',
                    Users::ROLE_SENIOR_METHODIST =>'Старший методист',
                    Users::ROLE_METHODIST =>'Методист',
                    Users::ROLE_USER =>'Пользователь',], [
                    'class'  => 'form-control selectpicker',
                    'data' => [
//                'style' => 'btn-primary',
//                'live-search' => 'true',
                        'size' => 7,
                        'title' => 'Ничего не выбрано',
                    ],
                ])->label('Роль'); ?>
            <?php endif;?>
        <?php endif;?>


        <?= $form->field($model, 'position_and_direction_in_users')->widget(Select2::classname(), [
            'data' => $position_and_direction_array,
            'toggleAllSettings' => [
                'selectLabel' => '',
                'unselectLabel' => '',
            ],
            'options' => ['class'=>'list_tag','placeholder' => 'Выберите должность или направление ...', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => ['.'],
    //            'minimumInputLength' => 3,
                'maximumInputLength' =>254
            ],
        ]);
        ?>

        <?= $form->field($model, 'description')->widget(TinyMce::className(), [
            'options' => ['rows' => 10],
            'language' => 'ru',
            'clientOptions' => [
                'plugins' => [
                    "advlist autolink lists link charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
            ]
        ]);?>

        <?= $form->field($model, 'basic_information')->widget(TinyMce::className(), [
            'options' => ['rows' => 25],
            'language' => 'ru',
            'clientOptions' => [
                'plugins' => [
                    "advlist autolink lists link charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
            ]
        ]);?>

    <?php endif;?>

    <div class="form-group field-users-register_date">
        <label class="control-label" for="users-register_date">Дата регистрации</label>
        <div><?=$model->isNewRecord || $model['register_date'] == ''?'Нет даты':date("d.m.Y H:i",strtotime($model['register_date']))?></div>
    </div>
    <div class="form-group field-users-last_visit_date">
        <label class="control-label" for="users-last_visit_date">Дата входа</label>
        <div><?=$model->isNewRecord || $model['last_visit_date'] == ''?'Нет даты':date("d.m.Y H:i",strtotime($model['last_visit_date']))?></div>
    </div>



    <?= $form->field($model, 'image')->widget(Widget::className(), [
        'uploadUrl'     => Url::toRoute('/user/uploadPhoto'),
        'width'         => 220,
        'height'        => 220,
        'pluginOptions' => [
            'maxWidth'              => 220,
            'maxHeight'             => 220,
            'imageSmoothingQuality' => "medium",
            'cropBoxResizable'      => true,
            'zoomOnWheel'           => true,
        ],
        'extensions'    => 'jpeg, jpg, png',

    ]); ?>


    <div class="form-group field-users-ip">
        <label class="control-label" for="users-ip">Последний IP адрес</label>
        <div><?=$model->isNewRecord || $model['ip'] == ''?'Еще не входил':$model['ip']?></div>
    </div>

    <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
        <?= $form->field($model, 'not_send_email')->checkbox() ?>
        <?= $form->field($model, 'in_archive')->checkbox()->label('Заблокировать пользователя') ?>
    <?php endif;?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>


    <?php ActiveForm::end(); ?>

    <?php

    $script = <<< JS

    $(document).ready(function() {
        // jQuery('#users-register_date,#users-last_visit_date').datetimepicker({lang: 'fr', format:'d.m.Y H:i', dayOfWeekStart: 1});
    // $("#users-email").inputmask("email");
   
    
   }); 
JS;

            $this->registerJs($script, yii\web\View::POS_END);
    ?>
</div>
