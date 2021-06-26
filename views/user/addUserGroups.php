<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use kartik\select2\Select2;
//var_dump($model['role_methodist']);
//var_dump($users_array);
//die;

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Группа пользователей</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'user_groups'
    ]); ?>
    <div class="box-body">

        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
        <div class="categories-search-wrap">
            <h3>Поиск по категориям</h3>
            <div id="categories-block" class="categories-block"></div>
            <div class="categories-drop-block">
                <input type="text" id="plugins4_q" value="" class="categories-input">
                <div id="data" class="categories-data"></div>
            </div>
        </div>

        <?= $form->field($model, 'role_senior_methodist')->widget(Select2::classname(), [
            'data' => $users_array_senior_methodist,
            'options' => ['placeholder' => 'Выберите пользователя ...', 'multiple' => true],
        ])->label('Старший методист группы');

        ?>
        <?= $form->field($model, 'role_methodist')->widget(Select2::classname(), [
            'data' => $users_array_methodist,
            'options' => ['placeholder' => 'Выберите пользователя ...', 'multiple' => true],
        ])->label('Методист группы');

        ?>

        <?= $form->field($model, 'delegation_rights')->widget(Select2::classname(), [
            'data' => $users_array,
            'options' => ['placeholder' => 'Выберите пользователя ...', 'multiple' => true],
        ])->label('Делегирование прав');
        ?>

        <?= $form->field($model, 'in_archive')->checkbox() ?>

    </div>

    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>


</div>

<?php


//[
//                    {"text" : "Root node", "state" : { "opened" : true }, 'id':1,
//                        "children" : [
//                            {"text" : "Child node 1",'id':2, "state" : { "selected" : true },},
//                            {"text" : "Child node 2",'id':3},
//                            {"text" : "Child node 3",'id':4},
//                            {"text" : "Child node 4",'id':5},
//                            {"text" : "Child node 5",'id':6}
//                    ]}
//                ]


$script = <<< JS

    $(document).ready(function() {
        $('#data')
        .jstree({
            'core' : {
                'data' : $json
            },
            "checkbox" : {
              "keep_selected_style" : false
            },
            "plugins" : [ "checkbox", "search"]
        })
        
        .on('changed.jstree', function (e, data) {
          $('#categories-block').empty();
          var i, j, r = [];
          let item_id = [];
          
          for(i = 0, j = data.selected.length; i < j; i++) {
              r.push(data.instance.get_node(data.selected[i]));
          }
          
          for (let item of r) {
              
              if($.inArray(item.parent, data.selected) == (-1))
              {
                  item_id.push(item.id);
              }
              
              $('#categories-block').append('<input type="button" value="'+ item.text +'" class="categories-item input-button-parent-'+ item.parent +' input-button-id-'+ item.id +' " style="display: none;">');
              $('#categories-block').append('<input name="UserGroups[category_id][]" type="hidden" value="'+ item.id +'">');              
          }
          
          if(item_id.length > 0)
          {
              for (let item_val of item_id) 
              {
                    $('.input-button-parent-'+item_val).css("display", "");
                    $('.input-button-id-'+item_val).css("display", "");             
              }
          }
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
      
      $('#categories-block').on('click', function (){
            $('.categories-drop-block').toggleClass('opened');
      });
        


   });
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>