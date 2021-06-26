<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Форма подписчиков</h3>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'subscribers'
    ]); ?>
    <div class="box-body"  style="min-height: 500px;">

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'time_created')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'time_send')->textInput(['autofocus' => true]) ?>
        <div class="categories-search-wrap">
            <h4>Подписан на категории</h4>
            <div id="categories-block" class="categories-block"></div>
            <div class="categories-drop-block">
                <input type="text" id="plugins4_q" value="" class="categories-input">
                <div id="data" class="categories-data"></div>
            </div>
        </div>

        <?= $form->field($model, 'status')->dropDownList(\app\models\Subscribers::$statuses) ?>
        <?= $form->field($model, 'is_deleted')->checkbox() ?>
        <?= $form->field($model, 'send_notification_immediately')->checkbox() ?>

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
//var_dump($model->getErrors('email')[0]);
//
//die;

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
   
              $('#categories-block').append('<input type="button" value="'+ item.text +'" class="categories-item input-button-parent-'+ item.parent +' input-button-id-'+ item.id +' " style="display: none;" >');
              $('#categories-block').append('<input name="Subscribers[category_id][]" type="hidden" value="'+ item.id +'">');       
              
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
        

jQuery('#subscribers-time_created,#subscribers-time_send').datetimepicker({lang: 'fr', format:'d.m.Y H:i', dayOfWeekStart: 1});
    $("#subscribers-email").inputmask("email");  
   });
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>