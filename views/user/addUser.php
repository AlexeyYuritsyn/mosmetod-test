<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Users;
use dosamigos\tinymce\TinyMce;

use yii\helpers\Url;
use budyaga\cropper\Widget;

use kartik\select2\Select2;

?>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Создать пользователя</h3>
        </div>
        <?php $form = ActiveForm::begin([
            'id' => 'basic_information_user',
//            'layout' => 'horizontal',
//            'fieldConfig' => [
//                'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
//                'labelOptions' => ['class' => 'col-lg-2 control-label'],
//            ],
        ]); ?>
        <div class="box-body">

            <?= $form->field($model, 'email')->textInput(['autofocus' => true,'autocomplete'=>'off']) ?>
            <?= $form->field($model, 'password')->passwordInput(['autocomplete'=>'off']) ?>
            <?= $form->field($model, 'second_name')->textInput(['autocomplete'=>'off']) ?>
            <?= $form->field($model, 'first_name')->textInput(['autocomplete'=>'off']) ?>
            <?= $form->field($model, 'third_name')->textInput(['autocomplete'=>'off']) ?>
            <?= $form->field($model, 'guid')->hiddenInput()->label(false) ?>

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

            <?php elseif(Yii::$app->user->identity->role == Users::ROLE_MODERATOR):?>
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



            <?= $form->field($model, 'position_and_direction_in_users')->widget(Select2::classname(), [
                'data' => $position_and_direction_array,
                'toggleAllSettings' => [
                    'selectLabel' => '',
                    'unselectLabel' => '',
                ],
                'options' => ['class'=>'list_tag','placeholder' => 'Выберите тег ...', 'multiple' => true],
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

            <?= $form->field($model, 'in_archive')->checkbox()->label('Заблокировать пользователя') ?>

        </div>

        <div class="box-footer">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php

$script = <<< JS

    $(document).ready(function() {

    // $("#users-email").inputmask("email");
    
   }); 
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>