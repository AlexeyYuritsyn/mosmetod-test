<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\file\FileInput;
use dosamigos\tinymce\TinyMce;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Проект</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'projects'
    ]); ?>
    <div class="box-body">

        <?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>
<!---->
<!--        <?//=$form->field($model, "background_input")->widget(FileInput::classname(), [
//            'options'=>[
//                'multiple'=>false,
//                'accept' => 'image/png,image/jpg,image/jpeg'
//            ],
//            'pluginOptions'=>[
//                'previewFileType' => 'any',
////                    'uploadUrl' => $model->isNewRecord == false?Url::to(['/administrator/file-upload-presentations','id'=>$model['id']]):'',
////                    'showUpload' => $model->isNewRecord == false?true:false,
//                'showUpload' => false,
//                'showPreview' => true,
//                'initialPreview'=>$model['background'] != ''?[Url::to($model['background'],true)]:[],
////                'initialPreviewConfig' => $gallery_array_remove,
////                    [
////                        ['url' => Url::to(['/administrator/file-remove-presentations','id'=>$model['id']])],
////                    ],
//                'initialPreviewAsData'=>true,
//                'overwriteInitial'=>false,
//                'maxFileSize'=>500
////                'maxFileCount' => 3
//            ]
//        ]);?>-->

        <?= $form->field($model, 'description')->textarea();?>

        <?=$form->field($model, "logo_input")->widget(FileInput::classname(), [
            'options'=>[
                'multiple'=>false,
                'accept' => 'image/png,image/jpg,image/jpeg'
            ],
            'pluginOptions'=>[
                'previewFileType' => 'any',
                'showUpload' => false,
                'showPreview' => true,
                'initialPreview'=>$model['logo'] != ''?[Url::to($model['logo'],true)]:[],
                'initialPreviewConfig' =>
                    [
                        ['url' => Url::to(['/projects/file-logo-remove-projects','id'=>$model['id']])],
                    ],
                'initialPreviewAsData'=>true,
                'overwriteInitial'=>false,
//                'maxFileCount' => 3
            ]
        ]);?>

        <?= $form->field($model, 'url')->textInput(['placeholder'=>'https://example.ru/']); ?>

        <?= $form->field($model, 'color_projects_id')->dropDownList($color_projects, [
            'data' => [
                'size' => 7,
                'title' => 'Ничего не выбрано',
            ],
        ]); ?>

<!--        <?//= $form->field($model, 'color_projects_id')->input('color',['class'=>"input_class"]) ?>-->


        <?= $form->field($model, 'time_create')->textInput(['autocomplete'=>'off']) ?>

        <?= $form->field($model, 'display_on_home_page')->checkbox() ?>
        <?= $form->field($model, 'outdated')->checkbox() ?>
    </div>

    <div class="box-footer">
        <div class="pull-right">
            <?= Html::submitButton('Удалить', ['class' => 'btn btn-primary','name'=>'delete-project']) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php


$url = \Yii::$app->urlManager->baseUrl . '/images/flags/';

$script = <<< JS

jQuery('#projects-time_create').datetimepicker({lang: 'fr', format:'d.m.Y H:i', dayOfWeekStart: 1});
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>