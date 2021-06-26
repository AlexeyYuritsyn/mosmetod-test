<?php

use dosamigos\tinymce\TinyMce;

?>

<?= $form->field($model, 'name_widget_accordion')->label('Название виджета') ?>
<hr>

<div class="form-group" id="dynamic_form_accordion">
    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10">
                    <label class="control-label">Название раскрывающегося списка</label>
                    <input type="text" name="title_accordion" id="title_accordion" class="form-control">
                    <input type="hidden" name="id_accordion" id="id_accordion" class="form-control">
                    <input type="hidden" name="created_accordion" id="created_accordion" class="form-control">
                    <input type="hidden" name="modified_accordion" id="modified_accordion" class="form-control">
                </div>
                <div class="button-group">
                    <a href="javascript:void(0)" class="btn btn-primary" id="plus_accordion">Добавить</a>
                    <a href="javascript:void(0)" class="btn btn-danger" id="minus_accordion">Удалить</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10">
                    <label class="control-label">Контент</label>
                    <textarea class="form-control content-widget" name="content_accordion" id="content_accordion"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$array_widget_accordion = [];
foreach($widget_accordion as $i => $val)
{
    $array_widget_accordion[]=['id_accordion'=>$val['id'],'title_accordion'=>$val['title'],'content_accordion'=>$val['content'],'created_accordion'=>$val['created'],'modified_accordion'=>$val['modified']];
}

$json_array = json_encode($array_widget_accordion);


$script = <<< JS

$(document).ready(function() {
        	var dynamic_form_accordion =  $("#dynamic_form_accordion").dynamicForm("#dynamic_form_accordion","#plus_accordion", "#minus_accordion", {
		        limit:10,
		        formPrefix : "WidgetAccordion",
		        normalizeFullForm : false,
		        
		    });

        	dynamic_form_accordion.inject($json_array);

		    $("#dynamic_form_accordion #minus_accordion").on('click', function(){
		    	var initDynamicId = $(this).closest('#dynamic_form_accordion').parent().find("[id^='dynamic_form_accordion']").length;
		    	if (initDynamicId === 2) {
		    		$(this).closest('#dynamic_form_accordion').next().find('#minus_accordion').hide();
		    	}
		    	$(this).closest('#dynamic_form_accordion').remove();
		    });

		    // $('form').on('submit', function(event){
	        // 	var values = {};
			// 	$.each($('form').serializeArray(), function(i, field) {
			// 	    values[field.name] = field.value;
			// 	});
			// 	console.log(values)
        	// 	event.preventDefault();
        	// })
        });


JS;

$this->registerJs($script, yii\web\View::POS_END);
?>
