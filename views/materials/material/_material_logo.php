<?php
use yii\helpers\Url;
use kartik\file\FileInput;


//$url = Url::to(['/methodist/file-sorted','id'=>Yii::$app->getRequest()->get('id')]) ;

$widget_logo_remove[]['url'] = Url::to(['/materials/file-delete-logo-material','id'=>$widget_logo['id']]);
?>
<!--
<?//= $form->field($widget_logo, 'id')->hiddenInput()->label(false); ?>
<?//= $form->field($widget_logo, 'created')->hiddenInput()->label(false); ?>
<?//= $form->field($widget_logo, 'modified')->hiddenInput()->label(false); ?>
-->
<?= $form->field($widget_logo, 'type')->dropDownList(\app\models\WidgetLogo::$types); ?>
<?= $form->field($widget_logo, 'url')->textInput(['placeholder'=>'https://example.ru/']); ?>
<?=$form->field($widget_logo, "path_widget_logo")->widget(FileInput::classname(), [
    'options'=>[
        'multiple'=>false,
        'accept' => 'image/png,image/jpg,image/jpeg'
    ],
    'pluginOptions'=>[
        'previewFileType' => 'image',
        'allowedFileExtensions' => ['png','jfif','pjpeg','jpeg','pjp','jpg'],
        'showUpload' => false,
        'showPreview' => true,
        'initialPreview'=> $widget_logo['image'] != ''? Url::to($widget_logo['image'],true):[],
//        'uploadUrl' => Url::to(['/methodist/file-delete-material','id'=>Yii::$app->getRequest()->get('id')]),
        'initialPreviewConfig' => $widget_logo_remove,
        'initialPreviewAsData'=>true,
        'overwriteInitial'=>false,
//        'maxFileCount' => 1
        'maxFileSize'=>100
    ],
//    'pluginEvents' => [
//        'filesorted' => '
//
//        function(event, params) {
//
//        $.ajax({
//                 url: "'.$url.'",
//                 type: "post",
//                 data: {oldIndex:params.oldIndex, newIndex:params.newIndex},
//                }).done(function( msg ) {
//                                   // alert( "Data Saved: " + msg );
//                });
//        }',
//    ],
]);?>

