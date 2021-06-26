<?php

use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
//var_dump($widget_map);
//die;
?>

<?= $form->field($model, 'name_widget_map')->label('Название виджета') ?>
<hr>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper_widget_map', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item',//'.item', // required: css class
    'limit' => 50, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $widget_map[0],
    'formId' => 'material-form',
    'formFields' => [
        'title',
        'lat',
        'lng',
    ],
]); ?>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Адрес</th>
        <th>Название</th>
        <th>Широта</th>
        <th>Долгота</th>

        <th class="text-center">
            <button type="button" class="add-item btn btn-success btn-xs weekend-add-btn"><span class="glyphicon glyphicon-plus"></span></button>
        </th>
    </tr>
    </thead>
    <tbody class="container-items">
    <?php foreach ($widget_map as $i => $val): ?>

        <tr class="item">
            <td class="vcenter">
                <?php
                //   necessary for update action.
                echo Html::activeHiddenInput($val, "[{$i}]id");
                echo Html::activeHiddenInput($val, "[{$i}]created");
                echo Html::activeHiddenInput($val, "[{$i}]modified");
                ?>
                <?= $form->field($val, "[{$i}]name")->label(false);?>
            </td>
            <td class="vcenter">
                <?= $form->field($val, "[{$i}]title")->label(false);?>
            </td>
            <td class="vcenter">
                <?= $form->field($val, "[{$i}]lat")->textInput(['class'=>'form-control text-lat'])->label(false); ?>
            </td>
            <td class="vcenter">
                <?= $form->field($val, "[{$i}]lng")->textInput(['class'=>'form-control text-lng'])->label(false); ?>
            </td>
            <td class="text-center vcenter" style="width: 90px;">
                <button type="button" class="remove-item btn btn-danger btn-xs" ><span class="glyphicon glyphicon-minus"></span></button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php DynamicFormWidget::end(); ?>

<?php

$script = <<< JS

    $(document).ready(function() {
        $('body').on('click','.add-item',function() {
            let find_item = $('body').find('.item');
            
            if(find_item.length > 0)
            {
               $(find_item[find_item.length-1]).find('input').attr('value','');
               $(find_item[find_item.length-1]).find('input').val('');
            }
          
        });
        
    $(".text-lat,.text-lng").mask("99.99999");
   });
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>
