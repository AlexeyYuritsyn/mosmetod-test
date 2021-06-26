<?php

use yii\helpers\Url;

?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3>Редактирование категорий</h3>
    </div>
    <div class="box-body">
            <div class="box-body">
                <div class="categories-edit-main">
                    <div class="categories-edit-wrap">
                        <div class="tree-block">
                            <div>Поиск категории</div>
                            <input type="text" id="edit-search" value="" class="categories-input">
                            <?php if(Yii::$app->getRequest()->get('in_archive') == '0'):?>
                                <div class="buttons-block">
                                    <button class="create-button btn btn-primary">+</button>
                                    <button class="refresh-button btn btn-primary">Обновить</button>
                                    <button class="btn btn-primary" id="move-up-button">Вверх</button>
                                    <button class="btn btn-primary" id="move-down-button">Вниз</button>
                                    <button class="btn btn-primary" id="root-node">Корневая категория</button>
                                </div>
                            <?php endif;?>
                                <div class="scrollable-block">
                                    <div id="edit-data" class="demo"></div>
                                </div>
                        </div>
                        <div class="edit-block">
                            <div class="edit-form">
                                <?php if(Yii::$app->getRequest()->get('in_archive') == '0'):?>
                                    <button class="save-button btn btn-primary">Сохранить</button>
                                <?php else:?>
                                    <button class="restore-button btn btn-primary">Восстановить</button>
                                <?php endif;?>
                                    <div>Название</div>
                                        <input type="text" id="edit-name" value="" class="edit-name">
                                    <div>Порядок</div>
                                        <input type="text" id="edit-order" value="" class="edit-order">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label for="edit-exclude-from-search">
                                                <input type="checkbox" id="edit-exclude-from-search">
                                                Исключить категорию из поиска сайта
                                            </label>
                                        </div>
                                    </div>
                                    <div>ID</div>
                                        <input type="text" id="edit-id" value="" class="edit-id" readonly="readonly">
                                    <div>ID родительской категории</div>
                                        <input type="text" id="edit-parent" value="" class="edit-parent" readonly="readonly">

                                <?php if(Yii::$app->getRequest()->get('in_archive') == '0'):?>
                                    <button class="delete-button btn btn-primary">В архив</button>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>
</div>
<?php

$url_save = Url::to(['/tree/save-data-tree']);
$url_delete = Url::to(['/tree/delete-data-tree']);
$url_refresh = Url::to(['/tree/refresh-data-tree']);
$url_restore = Url::to(['/tree/restore-data-tree']);
$url_get = Url::to(['/tree/get-data-tree']);


$script = <<< JS

      $('#edit-data')
        .jstree({
            'core' : {
                'data' : $json,
                'multiple' : false,
                "check_callback" : function () {
                    if ($('#move-up-button').attr('disabled') === 'disabled') {
                        return false;
                    } else {
                        return true;
                    }
                }
            },
            "plugins" : [ "search", "dnd" ]
        })
        .on("changed.jstree", function (e, data) {
			if(data.selected.length) {
			    
			    let selectedNode = data.instance.get_node(data.selected);
			    $('#edit-name').val(selectedNode.text);
			    $('#edit-id').val(selectedNode.id);
			    $('#edit-parent').val(data.instance.get_parent(data.selected));
			    
			    var str = selectedNode.id;
                if((str.indexOf('j1_') + 1) == 0) 
                {
    			    $.ajax({
                        type: "POST",
                        url: "$url_get",
                        data: {
                            edit_id: $("#edit-id").val(),
                        },
                      success: function(msg){
                            let data = JSON.parse(msg);
                            // ref.refresh();
                            //
                            $("#edit-order").val(data.order_categories);
                            $("#edit-exclude-from-search").prop('checked',data.exclude_from_search);
                            
                            // .val(data.exclude_from_search);
                            // $("#edit-id").val('');
                            // $("#edit-parent").val('');
                      },
                      error:function(msg) {
                        alert(msg.responseText);
                      }  
                    });
                }
// console.log((str.indexOf('j1_') + 1));
			    

			    
			    // console.log(selectedNode.id);
			}
			
			/*$('button').each( function () {
			    var current = $(this);
			    if (current.attr('disabled') === 'disabled') {
			        current.addClass('disabled');
			    } else {
			        current.removeClass('disabled');
			    }
			});*/
        })
        .on("move_node.jstree", function (e, data) {            
            $('#edit-parent').val(data.parent);
            $('#edit-name').val(data.node.text);
            $('#edit-id').val(data.node.id);
            
            let ref = $("#edit-data").jstree();
            
            ref.deselect_all();
            ref.select_node(data.node.id);
            
            $(".create-button").attr('disabled', 'disabled');
            $("#root-node").attr('disabled', 'disabled');
        })
        .on("rename_node.jstree", function (e, data) {
            /*$.ajax({
                type: "POST",
                url: "",
                data: {
                    id: data.node.id,
                    name: data.node.text
                }
            });*/
        })
        .on("delete_node.jstree", function (e, data) {
            /*$.ajax({
                type: "POST",
                url: "",
                data: {
                    id: data.node.id
                }
            });*/
        })
        .on("create_node.jstree", function (e, data) {
            $(".create-button").attr('disabled', 'disabled');
            $("#move-up-button").attr('disabled', 'disabled');
            $("#move-down-button").attr('disabled', 'disabled');
            $("#root-node").attr('disabled', 'disabled');
            
            let ref = $("#edit-data").jstree();
            
            ref.deselect_node(data.parent);
            ref.select_node(data.node.id);
            /*$.ajax({
                type: "POST",
                url: "",
                data: {
                    id: data.node.id,
                    name: data.node.text,
                    parentId: data.node.parent,
                    position: data.position
                }
            });*/
        });
        
        $('#edit-parent').on("change", function () {
            let newParent = $('#edit-parent').val();
            let ref = $("#edit-data").jstree();
            let sel = ref.get_selected();
            ref.move_node (sel, newParent);
        });
        
        // $('.delete-button').on("click", function () {
        //     let ref = $("#edit-data").jstree();
        //     let sel = ref.get_selected();
        //     ref.delete_node(sel);
        // });
        
        $('#move-up-button').on("click", function () {
            let ref = $("#edit-data").jstree();
            let sel = ref.get_selected();
            let newParent = $(".jstree-clicked").parents('li')[2];
            if (!newParent) {
                newParent = '#'
            }
            ref.move_node(sel, newParent);
        });
        
        $('#move-down-button').on("click", function () {
            let ref = $("#edit-data").jstree();
            let sel = ref.get_selected();
            //let newParent = $(".jstree-clicked").next('ul').children('li')[0].getAttribute('id');
            let newParent = $(".jstree-clicked").parent('li').next('li')[0];
            ref.move_node(sel, newParent);
        });
        
        $('.create-button').on("click", function () {
            
            let ref = $("#edit-data").jstree();
            let parent = $("#edit-data").jstree('get_selected');
            let newNode = {
                text: 'New node'
            };
            ref.create_node(parent, newNode);
            ref.open_node(parent);
        });
        
        $('.restore-button').on("click", function () {
            let ref = $("#edit-data").jstree(true);
            
            $.ajax({
                type: "POST",
                url: "$url_restore",
                data: {
                    edit_name: $("#edit-name").val(),
                    edit_id: $("#edit-id").val(),
                    edit_parent: $("#edit-parent").val(),
                },
              success: function(msg){
                    ref.settings.core.data = JSON.parse(msg);
                    ref.refresh();
                    
                    $("#edit-name").val('');
                    $("#edit-id").val('');
                    $("#edit-parent").val('');
              },
              error:function(msg) {
                alert(msg.responseText);
              }  
            });
        });
        
        $('.save-button').on("click", function () {
            let ref = $("#edit-data").jstree(true);
            // let exclude_from_search = $("#edit-exclude-from-search");
            //
            // console.log(exclude_from_search);
            $.ajax({
                type: "POST",
                url: "$url_save",
                data: {
                    edit_name: $("#edit-name").val(),
                    edit_id: $("#edit-id").val(),
                    edit_parent: $("#edit-parent").val(),
                    edit_order: $("#edit-order").val(),
                    exclude_from_search: $("#edit-exclude-from-search").prop('checked'),
                },
              success: function(msg){
                    ref.settings.core.data = JSON.parse(msg);
                    ref.refresh();
                    $(".create-button").attr('disabled', false);
                    $("#move-up-button").attr('disabled', false);
                    $("#move-down-button").attr('disabled', false);
                    $("#root-node").attr('disabled', false);
                    
                    $("#edit-name").val('');
                    $("#edit-id").val('');
                    $("#edit-parent").val('');
                    $("#edit-order").val('');
                    $("#edit-exclude-from-search").val('');
                    
              },
              error:function(msg) {
                alert(msg.responseText);
              }  
            });
            
            
            // let parent = $("#edit-data").jstree('get_selected');
            // let newNode = {
            //     text: 'New node'
            // };
            // ref.create_node(parent, newNode);
        });
        
        $('.refresh-button').on("click", function () {
            let ref = $("#edit-data").jstree(true);
            
            $.ajax({
                type: "POST",
                url: "$url_refresh",
              success: function(msg){
                    ref.settings.core.data = JSON.parse(msg);
                    ref.refresh();
                    $(".create-button").attr('disabled', false);
                    $("#move-up-button").attr('disabled', false);
                    $("#move-down-button").attr('disabled', false);
                    $("#root-node").attr('disabled', false);
                    
                    $("#edit-name").val('');
                    $("#edit-id").val('');
                    $("#edit-parent").val('');
                    
                    
              },
              error:function(msg) {
                alert(msg.responseText);
              }  
            });
            
            
            // let parent = $("#edit-data").jstree('get_selected');
            // let newNode = {
            //     text: 'New node'
            // };
            // ref.create_node(parent, newNode);
        });
        
        $('.delete-button').on("click", function () {
            let ref = $("#edit-data").jstree(true);
            
            $.ajax({
                type: "POST",
                url: "$url_delete",
                data: {
                    edit_name: $("#edit-name").val(),
                    edit_id: $("#edit-id").val(),
                    edit_parent: $("#edit-parent").val(),
                },
              success: function(msg){
                    ref.settings.core.data = JSON.parse(msg);
                    ref.refresh();
                    
                    $(".create-button").attr('disabled', false);
                    $("#move-up-button").attr('disabled', false);
                    $("#move-down-button").attr('disabled', false);
                    $("#root-node").attr('disabled', false);

                    $("#edit-name").val('');
                    $("#edit-id").val('');
                    $("#edit-parent").val('');
                    
                    
              },
              error:function(msg) {
                alert(msg.responseText);
              }  
            });
            
            
            // let parent = $("#edit-data").jstree('get_selected');
            // let newNode = {
            //     text: 'New node'
            // };
            // ref.create_node(parent, newNode);
        });
        
        $('#root-node').on("click", function () {
            let ref = $("#edit-data").jstree();
            let root = '#';
            let newNode = {
                text: 'New root node'
            };
            ref.create_node(root, newNode);
            // ref.refresh();
        });
        
        
        
        // $('#edit-name').on("change", function () {
        //     let newName = $('#edit-name').val();
        //     let ref = $("#edit-data").jstree();
        //     let sel = ref.get_selected();
        //     ref.rename_node (sel, newName);
        // });
        
      let tos = false;
      $('#edit-search').keyup(function () {
        if(tos) { clearTimeout(tos); }
        tos = setTimeout(function () {
          let vs = $('#edit-search').val();
          $('#edit-data').jstree(true).search(vs);
        }, 250);
      });
      
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>