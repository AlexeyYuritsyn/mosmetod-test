<?php
use yii\grid\GridView;
use yii\helpers\Html;

use yii\helpers\Url;
use \yii\bootstrap\Modal;
use \app\models\Users;
use phpnt\datepicker\BootstrapDatepicker;
use phpnt\bootstrapSelect\BootstrapSelectAsset;


BootstrapSelectAsset::register($this);

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title ">Материалы</h3>
        <div class="box-tools pull-right">
            <?php echo Html::beginForm(['/materials/add-material'],'get');?>

            <?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>

                <?= Html::Button( 'Снять с публикации', ['class' => 'btn btn-primary remove-published-material']) ?>
                <?= Html::Button( 'Перенести', ['class' => 'btn btn-primary move-material-another-group']) ?>


            <?php endif;?>
                <?= Html::submitButton( 'Добавить материал', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::endForm();?>
        </div>
    </div>
    <div class="box-body" style="min-height: 700px;">
        <?php echo Html::beginForm(['/materials/all-materials'],'get');//,'status'=>Yii::$app->getRequest()->get('status')?>

        <div class="filter-wrapper">
            <div class="filter-block" style="width: 300px;">
                <span class="filter-header-text">Заголовок</span>
                <?=Html::textInput('title',Yii::$app->getRequest()->get('title'),['class'  => 'form-control']);?>
            </div>
            <div class="filter-block date-filter-block">
                <span class="filter-header-text">Период даты публикации</span>
                <?= BootstrapDatepicker::widget([
                    'name'  => 'fromdate',
                    'name_2'  => 'todate',
                    'options'  => ['autocomplete' => 'off','class' => 'form-control'],
                    'type'                  => BootstrapDatepicker::TYPE_RANGE,     // тип виджета TYPE_TEXT, TYPE_COMPONENT, TYPE_EMBEDDED, TYPE_RANGE (по умолчанию TYPE_TEXT)
                    'attribute_2'           => 'date2', // только для типа TYPE_RANGE
                    'autoclose'             => false,   // закрывать при выборе
                    'assumeNearbyYear'      => false,   // изменять двухзначный год на четырехзначный (например "17" изменит на "2017")
                    'calendarWeeks'         => false,   // отображать календарную неделю
                    'clearBtn'              => false,   // отображать кнопку очистить
                    'container'             => 'body',  // контейнер для всплывающего окна
                    'datesDisabled'         => [],      // отключить даты (например ['12.04.2017', '30.04.2017'])
                    'daysOfWeekDisabled'    => [],      // отключить дни недели от 0 до 6 (например ['0', '6'])
                    'daysOfWeekHighlighted' => [],      // выделить дни недели от 0 до 6 (например ['0', '6'])
                    'defaultViewDate'       => [],      // дата по умолчанию (например ['day' => '25', 'month' => '04', 'year' => '2017'])
                    'disableTouchKeyboard'  => false,   // Если правда, на мобильных устройствах не будет отображаться клавиатура
                    'enableOnReadonly'      => true,
                    'endDate'               => false,   // последняя дата, которую можно выбрать; Все последующие даты будут отключены (например '17.04.2017')
                    'forceParse'            => true,    // когда недопустимая дата остается в поле ввода, виджет принудительно проанализирует ее значение
                    // и установит значение ввода на новую, действительную дату, соответствующую данному формату.
                    'format' => 'dd.mm.yyyy',           // формат даты
                    'immediateUpdates'      => false,   // Если true, выбор года или месяца в datepicker будет немедленно обновлять значение ввода
                    // В противном случае, только выбор дня месяца будет немедленно обновлять значение ввода
                    'keepEmptyValues'       => false,   // работает только в range. Если true, выбранное значение не распространяется на другие
                    'keyboardNavigation'    => true,    // перемещать дату клавиатурой
                    'language'              => 'ru',    // выбор языка
                    'maxViewMode'           => 4,
                    'minViewMode'           => 0,
                    'multidate'             => false,   // выбор нескольких дат (например для двух дат, будет значение 2)
                    'multidateSeparator'    => ',',     // разделитель нескольких дат
                    'orientation'           => 'auto',  // расположение “left”, “right”, “top”, “bottom”, “auto”
                    'showOnFocus'           => true,    // открывает при нажатии на input
                    'startDate'             => false,   // самая ранняя дата, которую можно выбрать. Все более ранние даты будут отключены
                    'startView'             => 0,
                    'templates'             => [
                        'leftArrow' => '&larr;',
                        'rightArrow' => '&rarr;'
                    ],
                    'showWeekDays'          => true,    // показывать дни недели
                    'title'                 => '',      // заголовок
                    'todayBtn'              => false,   // отображать кнопку сегодня
                    'todayHighlight'        => false,   // выделять сегодня
                    'toggleActive'          => false,
                    'updateViewDate'        => true,
                    'weekStart'             => 1,       // начало недели (значения от 0 до 6)
                    'zIndexOffset'          => 10,

                ]);

                ?>
            </div>

            <div class="categories-search-wrap filter-block" style="width: 230px;">
                <span class="filter-header-text">Категории</span>
                <div id="categories-block-filter" class="categories-block" style="margin-bottom: 0px;"></div>
                <div id="categories-drop-block-filter" class="categories-drop-block" style="width: 550px;">
                    <input type="text" id="search-categories-filter" value="" class="categories-input">
                    <div id="categories-filter" class="categories-data"></div>
                </div>
            </div>

            <?php if(Yii::$app->user->identity->role != \app\models\Users::ROLE_METHODIST || $delegation_rights_in_user_groups != false):?>
                <div class="filter-block" style="width: 230px;">
                    <span class="filter-header-text">Автор</span>
                    <?=Html::dropDownList('created_by', Yii::$app->getRequest()->get('created_by'), $users_array, [
                        'class'  => 'form-control selectpicker',
                        'data' => [
                            'live-search' => 'true',
                            'size' => 10,
                            'title' => 'Ничего не выбрано',
                        ]
                    ]);?>
                </div>
            <?php endif;?>

            <div class="filter-block" style="width: 200px;">
                <span class="filter-header-text">Статус</span>
                <?=Html::dropDownList('status', Yii::$app->getRequest()->get('status'),$status_array, [
                    'class'  => 'form-control selectpicker',
                    'data' => [
                        'live-search' => 'true',
                        'size' => 10,
                        'title' => 'Ничего не выбрано',
                    ]
                ]);?>
            </div>

            <div class="filter-block">
                <span class="filter-header-text"><?=Html::checkbox('not_in_archive',Yii::$app->getRequest()->get('not_in_archive'));?> Не архив</span>
            </div>

            <div class="filter-block pull-right">
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary button-filter']) ?>
                <?= Html::a('Сброс', ['/materials/all-materials','not_in_archive' => true], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php echo Html::endForm();?>



        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn',
                    'contentOptions' => ['aria-label' => ''],
//                    'checkboxOptions' => function ($model, $key, $index, $column) {
//                        return ['checked' => ($model['id'] == 35170 || $model['id'] == 35169 || $model['id'] == 35165)?true:false];
//                    }
                ],
                [
                    'attribute' => 'title',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('title')];
                    },
                    'value'     => function($model){
                        return $model['urgency_withdrawal'] == \app\models\Materials::HIGH_SPEED?'<div style="color: red">'.$model['title'].'</div>':$model['title'];
                    },
                ],
                [
                    'attribute' => 'status',
                    'format'    => 'html',
                    'value'     => function($model){
                        return \app\models\Materials::$status[$model['status']];
                    },
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('status')];
                    }
                ],
                [
                    'attribute' => 'categories_name',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('categories_name')];
                    }
                ],
                [
                    'attribute' => 'fio_created',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('fio_created')];
                    }
                ],
                [
                    'attribute' => 'fio_modified',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('fio_modified')];
                    }
                ],
                [
                    'attribute' => 'published_date',
                    'value'     => function($model){
                        return date('d.m.Y H:i:s',$model['published_date']);
                    }
                ],
                [
                    'attribute' => 'hits',
                    'format'    => 'boolean',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('hits')];
                    }
                ],
                [
                    'attribute' => 'id',
                    'format'    => 'html',
                    'contentOptions' => function ($model) {
                        return ['aria-label' => $model->getAttributeLabel('id')];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update-material}',
                    'visibleButtons' => [
                        'update-material' =>  true,
                    ],
                    'buttons' => [
                        'update-material' => function ($url,$model,$key) {
                            return Html::a('Редактировать', ['/materials/update-material', 'id'=>$model['id']], ['class' => 'btn btn-success btn-xs']);
                        }
                    ],
                ],
            ],

        ]); ?>
    </div>
</div>

<?php if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN):?>
    <?php
    Modal::begin([
        'header' => '<h5>Категории к материалу</h5>',
        'id'=> 'modal-form-move-material-another-group',
//        'clientOptions' => ['show' => true],
    ]);
    ?>

    <div class="categories-search-wrap">
        <h5><b>Выберите категорию для переноса материалов</b></h5>
        <div id="categories-block" class="categories-block"></div>
        <div id="categories-drop-block-move-material" class="categories-drop-block">
            <input type="text" id="search-categories-move-material" value="" class="categories-input">
            <div id="categories-move-material-another-group" class="categories-data"></div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::Button('Перенести материалы', ['class' => 'btn btn-primary modal-button-move-material-another-group']) ?>
    </div>
    <?php Modal::end(); ?>


    <?php
    Modal::begin([
        'header' => '<h5>Перенос материалов</h5>',
        'id'=> 'modal-form-loading',
//        'clientOptions' => ['show' => true],
    ]);
    ?>
    <table>
        <tr>
            <td>
                <?= Html::img('/images/mosmetod/preloader.gif') ?>
            </td>
            <td>
                Подождите, пока материалы обрабатываются.
            </td>
        </tr>
    </table>


    <?php Modal::end(); ?>
<?php endif;?>

<?php

if(isset($_GET['fromdate']))
{
    $FROMDATE = $_GET['fromdate'];
}
else
{
    $FROMDATE = '';
}
if(isset($_GET['todate']))
{
    $TODATE = $_GET['todate'];
}
else
{
    $TODATE = '';
}

$change_material_category = Url::to(['/materials/change-material-category']);
$remove_published_material = Url::to(['/materials/remove-published-material']);
$get_json_change_material_category = Url::to(['/materials/get-json-change-material-category']);
$get_json_filter_material_category = Url::to(['/materials/get-json-filter-material-category','category'=>Yii::$app->getRequest()->get('category')]);


$script1 = <<< JS
    
   $(document).ready(function() 
    {
                         
        $('.remove-published-material').on('click',function() {
            
            
            let material_checked = $('input[name="selection[]"]:checked');
            
            if(material_checked.length > 0)
            {
                $('#modal-form-loading').modal('show');
                
                var processSchema = function() {
                        let promises = [];
                        var def = new $.Deferred();
                        let i = 1;
                        for (let material_checked_item of material_checked) {
                            
                            $.ajax({
                                type: "POST",
                                url: "$remove_published_material",
                                data: {
                                    material_id: $(material_checked_item).val()
                                },
                              success: function(data){
                                i += 1;
                                if(i > material_checked.length)
                                {
                                    def.resolve(1);
                                }
                                
                              },
                              error:function(msg) {
                                    i += 1;
                                alert(msg.responseText);
                                
                                if(i > material_checked.length)
                                {
                                    def.resolve(1);
                                }
                              }  
                            });
                            
                            promises.push(def);  
                            
                          }
                          
                          return $.when.apply(undefined, promises).promise();
                    };
                
                    let move_material_another_group =  processSchema();
                    
                    move_material_another_group.done(function() {
                        location.reload();
                    });
            }
            else 
            {
              alert('Вы должны выбрать хотя бы один материал');  
            }
            
        });
        
        
        $('.move-material-another-group').on('click',function() {
            
            if($('input[name="selection[]"]:checked').length > 0)
            {
                $('#modal-form-move-material-another-group').modal('show');
            }
            else
            {
                alert('Отметьте хотя бы один материал');
            }
            
          // console.log($('input[name="selection[]"]:checked'));
        });
                 
        $('.modal-button-move-material-another-group').on('click',function() {
            
            
            let material_checked = $('input[name="selection[]"]:checked');
            let categories_item = $('.categories-item-move-material-another-group');
            
            if(material_checked.length > 0 && categories_item.val() != undefined)
            {
                  
                $('#modal-form-move-material-another-group').modal('hide');
                $('#modal-form-loading').modal('show');
                
                var processSchema = function() {
                        let promises = [];
                        var def = new $.Deferred();
                        let i = 1;
                        for (let material_checked_item of material_checked) {
                            
                            $.ajax({
                                type: "POST",
                                url: "$change_material_category",
                                data: {
                                    material_id: $(material_checked_item).val(),
                                    category_id: categories_item.val(),
                                },
                              success: function(data){
                                i += 1;
                                if(i > material_checked.length)
                                {
                                    def.resolve(1);
                                }
                                
                              },
                              error:function(msg) {
                                    i += 1;
                                alert(msg.responseText);
                                
                                if(i > material_checked.length)
                                {
                                    def.resolve(1);
                                }
                              }  
                            });
                            
                            promises.push(def);  
                             // 
                          }
                          
                          // return promises;
                          // console.log($.when.apply(undefined, promises).promise());
                          return $.when.apply(undefined, promises).promise();
                    };
                
                    let move_material_another_group =  processSchema();
                    
                    move_material_another_group.done(function() {
                        // $('#modal-form-loading').modal('hide');
                        location.reload();
                        // console.log('Привет МИР!!!');
                    });
            }
            else 
            {
              alert('Вы должны выбрать хотя бы один материал и выбрать категорию');  
            }
            
        });

        $.ajax({
                type: "POST",
                url: "$get_json_filter_material_category",
                success: function(data){
                    $('#categories-filter')
                    .jstree({
                        'core' : {
                            'multiple': false,
                            'data' : data
                        },
                        'checkbox' : {            
                            'deselect_all': true,
                            'three_state' : false, 
                        },
                        "plugins" : [ "search"]
                    });
                  },
                error:function(msg) {
                  alert(msg.responseText);
                  }  
            });
        
     
        
        
        $('#categories-filter').on('changed.jstree', function (e, data) {
          $('#categories-block-filter').empty();
          var i, j, r = [];
          
          for(i = 0, j = data.selected.length; i < j; i++) {
              r.push(data.instance.get_node(data.selected[i]));
          }
          
          for (let item of r) {
              $('#categories-block-filter').append('<input type="button" value="'+ item.text +'" class="categories-item ">');
              $('#categories-block-filter').append('<input type="hidden" name="category" value="'+ item.id +'" class="categories-item-filter">');
          }
          
          // $('.categories-drop-block').toggleClass('opened');
      });
              
        
        $('body').on('click','#categories-block-filter', function (){
            $('#categories-drop-block-filter').toggleClass('opened');
          });
          
          $('body').on('click','#categories-filter .jstree-clicked', function (){
                $('#categories-drop-block-filter').toggleClass('opened');
          });
        
     
          $.ajax({
                type: "POST",
                url: "$get_json_change_material_category",
                success: function(data){
                    $('#categories-move-material-another-group')
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
          
          
        
        
        $('#categories-move-material-another-group').on('changed.jstree', function (e, data) {
          $('#categories-block').empty();
          var i, j, r = [];
          
          for(i = 0, j = data.selected.length; i < j; i++) {
              r.push(data.instance.get_node(data.selected[i]));
          }
          
          for (let item of r) {
              $('#categories-block').append('<input type="button" value="'+ item.text +'" class="categories-item ">');
              $('#categories-block').append('<input type="hidden" value="'+ item.id +'" class="categories-item-move-material-another-group">');
          }
          
          // $('.categories-drop-block').toggleClass('opened');
      });
      
      var to = false;
      $('#search-categories-move-material').keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
          var v = $('#search-categories-move-material').val();
          $('#categories-move-material-another-group').jstree(true).search(v);
        }, 250);
      }); 
        
      var to_filter = false;
      $('#search-categories-filter').keyup(function () {
        if(to_filter) { clearTimeout(to_filter); }
        to_filter = setTimeout(function () {
          var v = $('#search-categories-filter').val();
          $('#categories-filter').jstree(true).search(v);
        }, 250);
      }); 
      
      $('body').on('click','#categories-block', function (){
            $('#categories-drop-block-move-material').toggleClass('opened');
      });
      
      $('body').on('click','#categories-move-material-another-group .jstree-clicked', function (){
            $('#categories-drop-block-move-material').toggleClass('opened');
      });
      
        
        if('$TODATE' != '')
        {
         $('input[name="todate"]').val('$TODATE');   
        }
        if('$FROMDATE' != '')
        {
            $('input[name="fromdate"]').val('$FROMDATE');   
        }

        $('html').keydown(function(e){
          if (e.keyCode == 13) {
            $('.button-filter').onclick();
          }
        });
    }); 

JS;

$this->registerJs($script1, yii\web\View::POS_END);

?>
