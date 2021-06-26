<?php

use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;

use kartik\select2\Select2;
use \app\models\Users;
use \yii\bootstrap\Modal;

use \app\models\Materials;

$status = [];


if($model->isNewRecord)
{
    $model['status'] = Materials::DRAFT;
}

?>
<?php $form = ActiveForm::begin(['id' => 'material-form', 'enableClientValidation' => false]); ?>
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Материалы</h3>
        <div class="box-tools pull-right">



            <?= Html::a( 'К списку материалов',\yii\helpers\Url::to(['/materials/come-back']), ['class' => 'btn btn-primary']) ?>


            <?php if($model->isNewRecord):?>
                <?= Html::Button( 'Предварительный просмотр', ['class' => 'btn btn-primary preview-materials','title'=>'Отображение сохраненных дынных']) ?>
            <?php else:?>
                <?= Html::a( 'Предварительный просмотр',
                    \yii\helpers\Url::to(['/show-preliminary-material/'.$model['id'].'/'.(2).'/'.Yii::$app->session->get('access_token')],true),
                    ['class' => 'btn btn-primary','title'=>'Отображение сохраненных дынных','target'=>'_blank']) ?>
            <?php endif;?>

            <?php if($model['status'] != Materials::ARCHIVE):?>
                <?php if(Yii::$app->user->identity->role == Users::ROLE_METHODIST && ($model['status'] != Materials::DRAFT)):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-draft-materials']) ?>
                <?php elseif (Yii::$app->user->identity->role == Users::ROLE_METHODIST && $model['status'] == Materials::DRAFT):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

                <?php if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && ($model['status'] != Materials::DRAFT)):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-sent-for-confirmation-materials']) ?>
                <?php elseif (Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && $model['status'] == Materials::DRAFT):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

                <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

            <?php endif;?>

        </div>
    </div>
    <div class="box-body">

        <div class="form-group field-materials-status">
            <label class="control-label" for="materials-status">Статус: <font style="color: red"><?=\app\models\Materials::$status[$model['status']]?></font></label>

        </div>

        <div class="form-group field-materials-button">

            <?php if($model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Перенести в архив', ['class' => 'btn btn-primary in-archive-materials']) ?>
            <?php elseif ($model['status'] == Materials::ARCHIVE && (Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)):?>
                <?= Html::Button( 'Вернуть из архива', ['class' => 'btn btn-primary from-archive-materials']) ?>
            <?php endif;?>

            <?php if(Yii::$app->user->identity->role == Users::ROLE_METHODIST && $model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Отправить на публикацию', ['class' => 'btn btn-primary send-publisher-materials']) ?>
                <?= Html::Button( 'Подтвердить', ['class' => 'btn btn-primary confirm-materials','style'=>'display:none']) ?>
                <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials','style'=>'display:none']) ?>
            <?php elseif(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && $model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Подтвердить', ['class' => 'btn btn-primary confirm-materials']) ?>
                <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials']) ?>
            <?php endif;?>

            <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
                <?= Html::Button( 'Опубликовать', ['class' => 'btn btn-primary publish-materials']) ?>
                <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials']) ?>
            <?php endif;?>
        </div>

        <?= $form->field($model, 'new_status')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>

        <?= Html::hiddenInput( null,null, ['class' => 'no_duplicates']) ?>

        <div class="categories-search-wrap">
            <h5><b>Категории</b></h5>
            <div id="categories-block" class="categories-block"></div>
            <div class="categories-drop-block">
                <input type="text" id="plugins4_q" value="" class="categories-input">
                <div id="data" class="categories-data"></div>
            </div>
        </div>

        <?= $form->field($model, 'tag')->widget(Select2::classname(), [
//            'data' => $tags,
            'toggleAllSettings' => [
                'selectLabel' => '',
                'unselectLabel' => '',
            ],
            'options' => ['class'=>'list_tag','placeholder' => 'Выберите тег ...', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [','],//, ' '
                'minimumInputLength' => 3,
                'maximumInputLength' => 30,
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/materials/get-material-tag']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
            ],
        ]);

        ?>

        <?= $form->field($model, 'guid')->hiddenInput()->label(false); ?>

        <?php if (Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
        <table style="width: 100%">
            <tr>
                <td style="width: 20%">
                    <?= $form->field($model, 'hits')->checkbox(); ?>
                </td>
                <td>
                    <?= $form->field($model, 'date_unpinning')->textInput(['autocomplete'=>'off']) ?>
                </td>
            </tr>
        </table>

            <?= $form->field($model, 'published_date')->textInput(['autocomplete'=>'off']) ?>
        <?php else:?>
            <?= $form->field($model, 'published_date')->hiddenInput()->label(false) ?>
        <?php endif;?>


        <?php if($model->isNewRecord){
            $model['urgency_withdrawal'] = 1;
        } ?>

        <?= $form->field($model, 'urgency_withdrawal')->dropDownList(\app\models\Materials::$urgency_withdrawal, [
            'class'  => 'form-control selectpicker',
            'data' => [
//                'style' => 'btn-primary',
//                'live-search' => 'true',            'pluginOptions' => [
//                'minimumInputLength' => 3,
//            ],
                'size' => 7,
                'title' => 'Ничего не выбрано',
            ],
        ])->label('Приоритет публикации материала <font color="red" title="Высокая  —   такая публикация должна быть опубликована в течении текущего часа

Средняя —  такая публикация должна быть опубликована после выполнения материалов с более высоким приоритетом

Низкая — такая публикация может подождать некоторое время">&#128712;</font>'); ?>



        <?= $form->field($model, 'material_watchers')->widget(Select2::classname(), [
//            'data' => $material_watchers_email_array,
            'toggleAllSettings' => [
                'selectLabel' => '',
                'unselectLabel' => '',
            ],
            'options' => ['placeholder' => 'name@example.com', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'minimumInputLength' => 3,
                'maximumInputLength' => 255,
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/materials/get-material-watchers']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
            ],
//            'pluginEvents'=>[
//                "select2:select" => "function(e) { console.log($(this).val()); console.log($(e)); }",
//            ]
        ]);
        ?>

<!--        <?//= $form->field($model, 'in_archive')->checkbox()  filled-tab?>-->

        <?php

        echo Tabs::widget([
            'options' =>[],
            'items' => [
                [
                    'label' => 'Текст',
                    'linkOptions' => ['class'=>'gkTabs-1 '],
                    'content' => $this->render('material/_material_content',['form'=>$form,'model'=>$model]),
//                    'active' => !isset($no_validate)
                ],
                [
                    'label' => 'Описание',
                    'linkOptions' => ['class'=>'gkTabs-2 '.($model['description'] != ''?'filled-tab':'')],
                    'content' => $this->render('material/_materials_description',['form'=>$form,'model'=>$model]),
//                    'active' => !isset($no_validate)
                ],
                [
                    'label' => 'Логотип',
                    'linkOptions' => ['class'=>'gkTabs-3 '.($widget_logo['image'] != ''?'filled-tab':'')],
                    'content' => $this->render('material/_material_logo',['form'=>$form,'model'=>$model,'widget_logo' => $widget_logo]),
//                    'active' => true
                ],
                [
                    'label' => 'Галерея',
                    'linkOptions' => ['class'=>'gkTabs-4 '.((!empty($widget_gallery) || $model['name_widget_gallery'] != '')?'filled-tab':'')],
                    'content' => $this->render('material/_material_gallery',['form'=>$form,'model'=>$model,
                        'widget_gallery' => $widget_gallery,'widget_gallery_image' => $widget_gallery_image,'widget_gallery_remove' => $widget_gallery_remove,]),
//                    'active' => true
                ],
                [
                    'label' => 'Ссылка на youtube',
                    'linkOptions' => ['class'=>'gkTabs-5 '.(($model['youtube_url'] != '' || $model['name_widget_youtube'] != '')?'filled-tab':'')],
                    'content' => $this->render('material/_material_youtube_url',['form'=>$form,'model'=>$model]),
//                    'active' => true
                ],
                [
                    'label' => 'Карты',
                    'linkOptions' => ['class'=>'gkTabs-6 '.(($widget_map[0]['materials_id'] != '' || $model['name_widget_map'] != '')?'filled-tab':'')],
                    'content' => $this->render('material/_material_map',['form'=>$form,'model'=>$model,'widget_map' => $widget_map]),
//                    'active' => true
                ],
                [
                    'label' => 'Вкладки',
                    'linkOptions' => ['class'=>'gkTabs-7 '.(($widget_tab[0]->attributes['materials_id'] != '' || $model['name_widget_tabs'] != '')?'filled-tab':'')],
                    'content' => $this->render('material/_material_tab',['form'=>$form,'model'=>$model,'widget_tab' => $widget_tab]),
//                    'active' => true
                ],
                [
                    'label' => 'Раскрывающиеся списки',
                    'linkOptions' => ['class'=>'gkTabs-8 '.(($widget_accordion[0]->attributes['materials_id'] != '' || $model['name_widget_accordion'] != '')?'filled-tab':'')],
                    'content' => $this->render('material/_material_accordion',['form'=>$form,'model'=>$model,'widget_accordion' => $widget_accordion]),
//                    'active' => true
                ],
                [
                    'label' => 'История публикации',
                    'linkOptions' => ['class'=>'gkTabs-9'],
                    'content' => $this->render('material/_materials_log',['form'=>$form,'model'=>$model]),
//                    'active' => true
                ],
            ],
        ]);
        ?>
        <div class="box-footer clearfix">

<!--            <?//= Html::Button( 'Автосохранение', ['class' => 'btn btn-primary auto-save-materials']) ?>-->
            <?php if($model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Перенести в архив', ['class' => 'btn btn-primary in-archive-materials']) ?>
            <?php elseif ($model['status'] == Materials::ARCHIVE && (Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)):?>
                <?= Html::Button( 'Вернуть из архива', ['class' => 'btn btn-primary from-archive-materials']) ?>
            <?php endif;?>

            <?php if(Yii::$app->user->identity->role == Users::ROLE_METHODIST && $model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Отправить на публикацию', ['class' => 'btn btn-primary send-publisher-materials']) ?>
                <?= Html::Button( 'Подтвердить', ['class' => 'btn btn-primary confirm-materials','style'=>'display:none']) ?>
                <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials','style'=>'display:none']) ?>
            <?php elseif(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && $model['status'] != Materials::ARCHIVE):?>
                <?= Html::Button( 'Подтвердить', ['class' => 'btn btn-primary confirm-materials']) ?>
                <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials']) ?>
            <?php endif;?>

            <?php if($model->isNewRecord):?>
                <?= Html::Button( 'Предварительный просмотр', ['class' => 'btn btn-primary preview-materials','title'=>'Отображение сохраненных дынных']) ?>
            <?php else:?>
                <?= Html::a( 'Предварительный просмотр',
                    \yii\helpers\Url::to(['/show-preliminary-material/'.$model['id'].'/'.(2).'/'.Yii::$app->session->get('access_token')],true),
                    ['class' => 'btn btn-primary','title'=>'Отображение сохраненных дынных','target'=>'_blank']) ?>
            <?php endif;?>

            <?php if($model['status'] != Materials::ARCHIVE):?>
                <?php if(Yii::$app->user->identity->role == Users::ROLE_METHODIST && ($model['status'] != Materials::DRAFT)):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-draft-materials']) ?>
                <?php elseif (Yii::$app->user->identity->role == Users::ROLE_METHODIST && $model['status'] == Materials::DRAFT):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

                <?php if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && ($model['status'] != Materials::DRAFT)):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-sent-for-confirmation-materials']) ?>
                <?php elseif (Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST && $model['status'] == Materials::DRAFT):?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

                <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
                    <?= Html::Button( 'Опубликовать', ['class' => 'btn btn-primary publish-materials']) ?>
                    <?= Html::Button( 'Отправить на доработку', ['class' => 'btn btn-primary send-revision-materials']) ?>
                    <?= Html::Button( 'Сохранить', ['class' => 'btn btn-primary save-materials']) ?>
                <?php endif;?>

            <?php endif;?>
        </div>
    </div>
</div>


<?php
    Modal::begin([
    'header' => '<h2>Комментарий к материалу</h2>',
    'id'=> 'comment-material-form',
//    'clientOptions' => ['show' => true],
]);
?>
<?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>

    <?= $form->field($model, 'created_by')->widget(Select2::classname(), [
//        'data' => $users_array,
        'initValueText' => $init_value_text,
        'options' => ['placeholder' => 'Выберите пользователя ...', 'multiple' => false],
        'pluginOptions' => [
//            'minimumInputLength' => 3,
            'ajax'=>[
                'url'=>\yii\helpers\Url::to(['/materials/get-users']),
                'dataType'=>'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
//                'delay'=>250,
//                'cache'=>true,
//                'data' => new JsExpression('function(params) {return {q:params.term}; }')
            ]
//            'tags' => true,
//            'tokenSeparators' => [',', ' '],
//            'maximumInputLength' => 15
        ],
    ])->label('Создатель материала');

    ?>
<?php endif;?>



<?= $form->field($model, 'comment')->textarea(['rows'=>20,'style'=>'resize: none;','class'=>'form-control materials-comment'])->label(false) ?>

    <div class="form-group">
        <?= Html::Button('Отправить комментарий', ['class' => 'btn btn-primary comment-material-submit']) ?>
    </div>
<?php Modal::end(); ?>
<?php ActiveForm::end(); ?>
<?php

$alert = '';
if(!empty($model->getErrors()))
{
    foreach ($model->getErrors() as $error)
    {
        $alert .= current($error).'\r\n';
    }
}



//$guid = $model['guid'];

$DRAFT = Materials::DRAFT;
$SENT_FOR_CONFIRMATION = Materials::SENT_FOR_CONFIRMATION;
$CONFIRMED = Materials::CONFIRMED;
$PUBLISHED = Materials::PUBLISHED;
$SENT_FOR_DEVELOPMENT = Materials::SENT_FOR_DEVELOPMENT;
$ARCHIVE = Materials::ARCHIVE;

$role = Yii::$app->user->identity->role;

$ROLE_ADMIN = Users::ROLE_ADMIN;
$ROLE_MODERATOR = Users::ROLE_MODERATOR;
$ROLE_SENIOR_METHODIST = Users::ROLE_SENIOR_METHODIST;
$ROLE_METHODIST = Users::ROLE_METHODIST;

$isNewRecord = $model->isNewRecord?'1':'0';

$old_status = $model['status'];

$resolve =Yii::$app->request->resolve();
if(empty($resolve[1]))
{
    $resolve['auto_save'] = true;
}
else
{
    $resolve['id'] = $resolve[1]['id'];
    $resolve['auto_save'] = true;
    unset($resolve[1]);
//    var_dump($resolve);
}

$url = \yii\helpers\Url::to($resolve);

$url_duplication_material = \yii\helpers\Url::to(['/materials/duplication-material','guid'=>$model['guid']]);
$get_json_material_category = \yii\helpers\Url::to(['/materials/get-json-material-category','category'=>$model['material_categories_id']]);
$get_delegation_rights_in_user_groups = \yii\helpers\Url::to(['/materials/get-delegation-rights-in-user-groups']);

$check_methodist = (Yii::$app->user->identity->role == Users::ROLE_METHODIST?1:0);

$script = <<< JS

    $(document).ready(function() {
        // console.log(location.hostname);
    // $.ajax({
    //     type: "POST",
    //     url: "http://mosmetod-new.local.gmc/methodist/log-uploads-file-material",
    //     data: {
    //         // url: u,
    //         // material_guid: $('#material_guid').val(),
    //     },
    //     success: function(msg){
    //         // ref.settings.core.data = JSON.parse(msg);
    //         // ref.refresh();
    //         // $(".create-button").attr('disabled', false);
    //         // $("#move-up-button").attr('disabled', false);
    //         // $("#move-down-button").attr('disabled', false);
    //         // $("#root-node").attr('disabled', false);
    //         //
    //         // $("#edit-name").val('');
    //         // $("#edit-id").val('');
    //         // $("#edit-parent").val('');
    //
    //
    //     },
    //     error:function(msg) {
    //         // alert(msg.responseText);
    //     }
    // });        
        
        
        
        $('.preview-materials').on('click',function() {
            alert('Перед просмотром вы должны сохранить материал');            
        });  
        
        $('.send-revision-materials').on('click',function() {
            $('#materials-new_status').val('$SENT_FOR_DEVELOPMENT');
            $('#comment-material-form').modal('show');            
        });  
        
        $('.in-archive-materials').on('click',function() {
            $('#materials-new_status').val('$ARCHIVE');
            $('#comment-material-form').modal('show');            
        });    
        
        $('.send-publisher-materials').on('click',function() {
            $('#materials-new_status').val('$SENT_FOR_CONFIRMATION');
            form_validation();
        });    
        
        $('.save-materials').on('click',function() {
            
            if('$isNewRecord' == '1')
            {
                $('#materials-new_status').val('$DRAFT');
            }
            else
            {
                $('#materials-new_status').val('$old_status');
            }
            
            form_validation();
     
        });     
        
        $('.save-draft-materials').on('click',function() {
            
            $('#materials-new_status').val('$DRAFT');
            
            form_validation();
     
        });      
        
        $('.save-sent-for-confirmation-materials').on('click',function() {
            
            $('#materials-new_status').val('$SENT_FOR_CONFIRMATION');
            
            form_validation();
     
        });    
        
        $('.confirm-materials').on('click',function() {
            
            $('#materials-new_status').val('$CONFIRMED');
            
            form_validation();
     
        });    
        
        $('.publish-materials').on('click',function() {
            
            $('#materials-new_status').val('$PUBLISHED');
            
            form_validation();
     
        });     
        
        $('.from-archive-materials').on('click',function() {
            
            $('#materials-new_status').val('$DRAFT');
            
            
            form_validation();
        });  
        

        $('.comment-material-submit').on('click',function() {
           
            // console.log($('.materials-comment'));
            // console.log($('.materials-comment').val());
           
            if($('.materials-comment').val() == '')
            {
               alert('Вы должны оставить комментарий');
            }
            else
            {
               form_validation();
            }
        });
        
        // function auto_save_materials()
        // {
        //   
        // }
        
        let auto_save_materials = setInterval(function() 
            {
                if('$isNewRecord' == '1')
                    {
                        $('#materials-new_status').val('$DRAFT');
                    }
                    else
                    {
                        $('#materials-new_status').val('$old_status');
                    }
                  
                  tinyMCE.triggerSave();
                  var peopleData=$('#material-form').serialize();
                    $.ajax({
                        type: "POST",
                        url: '$url',
                        data: peopleData,
                        success: function(result){console.log(result);},
                        error: function(err){console.log(err);}
                    });
            }, 300000);
        
        function form_validation()
        {
            let error = '';
            
            if($('#materials-title').val() == '')
            {
                error += 'Вы должны заполнить "Заголовок"\\r\\n'
            }
            
            if($('.categories-item').length == 0)
            {
                error += 'Вы должны заполнить "Категории"\\r\\n'
            }
             
            if($('.list_tag').val().length == 0)
            {
                error += 'Вы должны заполнить "Теги"\\r\\n'
            }
            
            if(error != '')
            {
                alert(error);
            }
            else
            {
                clearInterval(auto_save_materials);
                $('#material-form').yiiActiveForm('validate', true); 
            }
        }
                
        
        setInterval(function() 
            {
             $.ajax({
                type: "GET",
                url: '$url_duplication_material',
                // success: function(result){console.log(result);},
                error: function(err){  alert('Проверка на редактирование двух пользователей одного материала перестало работать .\\n\\r'+err.responseText); console.log(err);}
            });
                    
            }, 25000);
        
        
        $.ajax({
                type: "POST",
                url: "$get_json_material_category",
                success: function(data){
                    $('#data')
                    .jstree({
                        'core' : {
                            'multiple': false,
                            'data' : data
                        },
                        'checkbox' : {            
                            'deselect_all': true,
                            'three_state' : false, 
                        },
                        // "checkbox" : {
                        //   "keep_selected_style" : false
                        // },
                        "plugins" : [ "checkbox", "search"]
                    });
                  },
                error:function(msg) {
                  alert(msg.responseText);
                  }  
            });
        
            
        
        
        $('#data').on('changed.jstree', function (e, data) {
          $('#categories-block').empty();
          var i, j, r = [];
          
          for(i = 0, j = data.selected.length; i < j; i++) {
              r.push(data.instance.get_node(data.selected[i]));
          }
          
          for (let item of r) {
              
              if('$check_methodist' == '1')
              {
                  $.ajax({
                        type: "GET",
                        url: "$get_delegation_rights_in_user_groups",
                        data:{
                            category:item.id
                        },
                        success: function(data){
                            if(data == '1')
                            {
                                $('.send-publisher-materials').css("display", "none");
                                $('.confirm-materials, .send-revision-materials').css("display", "");
                            }
                            else
                            {
                                $('.send-publisher-materials').css("display", "");
                                $('.confirm-materials, .send-revision-materials').css("display", "none");
                            }
                           console.log(data);
                          },
                        error:function(msg) {
                          alert(msg.responseText);
                          }
                    });
              }
              
              
              
              $('#categories-block').append('<input type="button" value="'+ item.text +'" class="categories-item">');
              $('#categories-block').append('<input name="Materials[material_categories_id]" type="hidden" value="'+ item.id +'">');
          }
          
          // $('.categories-drop-block').toggleClass('opened');
      });
      
      var to = false;
      $('#plugins4_q').keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
          var v = $('#plugins4_q').val();
          $('#data').jstree(true).search(v);
        }, 250);
      });    
      
      $(document).mouseup(function (e){
          let block = $('#categories-block');
          let drop = $('.categories-drop-block');
          if (!block.is(e.target)
          && block.has(e.target).closest('div', 'input').length === 0
          && !drop.is(e.target) 
          && drop.has(e.target).closest('div', 'input').length === 0) {
              drop.removeClass('opened');
          }
      });
      
      $('body').on('click','#categories-block', function (){
            $('.categories-drop-block').toggleClass('opened');
      });
      
      $('body').on('click','.jstree-clicked', function (){
            $('.categories-drop-block').toggleClass('opened');
      });
      
      
      if('$alert' != '')
      {
         alert('$alert');
      }
      
      
      // $('.auto-save-materials').on('click',function() {
      //    
      //    
      //    
      //   // console.log($('#material-form').serialize());
      //   // console.log(tinyMCE.get('text').getBody().innerHTML);
      // });


    if('$role' == '$ROLE_METHODIST' && ('$old_status' != '$DRAFT' && '$old_status' != '$SENT_FOR_DEVELOPMENT'))
    {
        clearInterval(auto_save_materials);
    }

        
    if('$role' == '$ROLE_SENIOR_METHODIST' && ('$old_status' != '$DRAFT' && '$old_status' != '$SENT_FOR_DEVELOPMENT' && '$old_status' != '$SENT_FOR_CONFIRMATION'))
    {
        clearInterval(auto_save_materials);
    }
    
    if(('$role' == '$ROLE_MODERATOR' || '$role' == '$ROLE_ADMIN') && ('$old_status' != '$DRAFT' && '$old_status' != '$SENT_FOR_DEVELOPMENT' && '$old_status' != '$ARCHIVE' && '$old_status' != '$CONFIRMED'))
    {
        clearInterval(auto_save_materials);
    }

    if('$old_status' == '$PUBLISHED')
    {
        clearInterval(auto_save_materials);
    }
    

// $('body').on('click','.confirm-changes-file',function(){ window.location.href = $("#refresh").attr("href") + "&" + (new Date).getTime()});
    
    jQuery('#materials-published_date, #materials-date_unpinning').datetimepicker({lang: 'fr', format:'d.m.Y H:i', dayOfWeekStart: 1});
   });
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>