<?php
use yii\helpers\Url;
use kartik\file\FileInput;


$url = Url::to(['/materials/file-sorted','id'=>Yii::$app->getRequest()->get('id')]) ;
?>

<?= $form->field($model, 'name_widget_gallery')->label('Название виджета') ?>
    <hr>


<?=$form->field($model, "path_widget_gallery")->widget(FileInput::classname(), [
    'options'=>[
        'multiple'=>true,
        'accept' => 'image/png,image/jpg,image/jpeg'
    ],
    'pluginOptions'=>[
        'previewFileType' => 'image',
        'allowedFileExtensions' => ['png','jfif','pjpeg','jpeg','pjp','jpg'],
        'showUpload' => false,
        'showPreview' => true,
        'initialPreview'=> $widget_gallery_image,
//        'uploadUrl' => '#',
            'initialPreviewConfig' => $widget_gallery_remove,
        'initialPreviewAsData'=>true,
        'overwriteInitial'=>false,
        'maxFileSize'=>550
//        'maxFileCount' => 1
    ],
    'pluginEvents' => [
        'filesorted' => '

        function(event, params) { 
       
        $.ajax({
                 url: "'.$url.'",
                 type: "post",
                 data: {oldIndex:params.oldIndex, newIndex:params.newIndex},
                }).done(function( msg ) {
                                   // alert( "Data Saved: " + msg );
                });
        }',
    ],
]);?>