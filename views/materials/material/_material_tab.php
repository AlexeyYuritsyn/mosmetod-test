<?php

use dosamigos\tinymce\TinyMce;

?>
<?= $form->field($model, 'name_widget_tabs')->label('Название виджета') ?>
<hr>
<div class="form-group" id="dynamic_form_tab">
    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10">
                    <label class="control-label">Название вкладки</label>
                    <input type="text" name="title_tab" id="title_tab" class="form-control">
                    <input type="hidden" name="id_tab" id="id_tab" class="form-control">
                    <input type="hidden" name="created_tab" id="created_tab" class="form-control">
                    <input type="hidden" name="modified_tab" id="modified_tab" class="form-control">
                </div>
                <div class="button-group">
                    <a href="javascript:void(0)" class="btn btn-primary" id="plus_tab">Добавить</a>
                    <a href="javascript:void(0)" class="btn btn-danger" id="minus_tab">Удалить</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10">
                    <label class="control-label">Контент</label>
                    <textarea class="form-control content-widget" name="content_tab" id="content_tab"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$array_widget_tab = [];
foreach($widget_tab as $i => $val)
{
    $array_widget_tab[]=['id_tab'=>$val['id'],'title_tab'=>$val['title'],'content_tab'=>$val['content'],'created_tab'=>$val['created'],'modified_tab'=>$val['modified']];
}

$json_array = json_encode($array_widget_tab);


$script = <<< JS

$(document).ready(function() {
        	var dynamic_form_tab =  $("#dynamic_form_tab").dynamicForm("#dynamic_form_tab","#plus_tab", "#minus_tab", {
		        limit:10,
		        formPrefix : "WidgetTabs",
		        normalizeFullForm : false,
		    });

        	dynamic_form_tab.inject($json_array);

		    $("#dynamic_form_tab #minus_tab").on('click', function(){
		    	var initDynamicId = $(this).closest('#dynamic_form_tab').parent().find("[id^='dynamic_form_tab']").length;
		    	if (initDynamicId === 2) {
		    		$(this).closest('#dynamic_form_tab').next().find('#minus_tab').hide();
		    	}
		    	$(this).closest('#dynamic_form_tab').remove();
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



// tinyMCE.init({
//     selector: 'textarea',
//     language:"ru",
//     // theme : "simple"
//  });
// tinymce.init({
//       selector: 'textarea',
//       menubar: true,
//        plugins: 'print preview powerpaste casechange importcss searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter charmap mentions quickbars emoticons',
//        toolbar: 'a11ycheck addcomment showcomments casechange code formatpainter table print preview powerpaste casechange importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter charmap mentions quickbars emoticons',
//       language:"ru",
//       content_css: [
//     '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
//     '//www.tinymce.com/css/codepen.min.css'],
//       // plugins: 'print preview powerpaste casechange importcss searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter charmap mentions quickbars emoticons',
//       // toolbar: 'a11ycheck addcomment showcomments casechange code formatpainter table print preview powerpaste casechange importcss searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter charmap mentions quickbars emoticons',
//       // toolbar_mode: 'floating',
//     });

// tinyMCE.init({tinydrive mediaembed  checklist permanentpen pageembed tinycomments linkchecker advtable
//         // General options 
//         mode : "textareas",
//         elements: "content_editor",
//         theme : "silver",
//         language:"ru",
//         plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,precode,uploads_image",
//    
//         // Theme options
//         theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
//         theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,preCode,anchor,image,uploads_image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//         theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//         theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
//         theme_advanced_toolbar_location : "top",
//         theme_advanced_toolbar_align : "left",
//         theme_advanced_statusbar_location : "bottom",
//         theme_advanced_resizing : true,
//
//         // Skin options
//         skin : "o2k7",
//         skin_variant : "silver",
//
//         // Drop lists for link/image/media/template dialogs
//         template_external_list_url : "js/template_list.js",
//         external_link_list_url : "js/link_list.js",
//         external_image_list_url : "js/image_list.js",
//         media_external_list_url : "js/media_list.js",
//
//         // Replace values for the template plugin
//         template_replace_values : {  username : "Some User",  staffid : "991234"  }
//     });

 //      tinyMCE.init({
 //    mode : "textareas",
 //    theme : "silver"
 // });

// tinyMCE.init({
//   mode: 'textareas',
//   theme:'silver',
//   plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable',
//   tinydrive_token_provider: 'URL_TO_YOUR_TOKEN_PROVIDER',
//   tinydrive_dropbox_app_key: 'YOUR_DROPBOX_APP_KEY',
//   tinydrive_google_drive_key: 'YOUR_GOOGLE_DRIVE_KEY',
//   tinydrive_google_drive_client_id: 'YOUR_GOOGLE_DRIVE_CLIENT_ID',
//   mobile: {
//     plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker textpattern noneditable help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable'
//   },
//   menu: {
//     tc: {
//       title: 'TinyComments',
//       items: 'addcomment showcomments deleteallconversations'
//     }
//   },
//   menubar: 'file edit view insert format tools table tc help',
//   toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
//   autosave_ask_before_unload: true,
//   autosave_interval: "30s",
//   autosave_prefix: "{path}{query}-{id}-",
//   autosave_restore_when_empty: false,
//   autosave_retention: "2m",
//   image_advtab: true,
//   content_css: '//www.tiny.cloud/css/codepen.min.css',
//   link_list: [
//     { title: 'My page 1', value: 'http://www.tinymce.com' },
//     { title: 'My page 2', value: 'http://www.moxiecode.com' }
//   ],
//   image_list: [
//     { title: 'My page 1', value: 'http://www.tinymce.com' },
//     { title: 'My page 2', value: 'http://www.moxiecode.com' }
//   ],
//   image_class_list: [
//     { title: 'None', value: '' },
//     { title: 'Some class', value: 'class-name' }
//   ],
//   importcss_append: true,
//   height: 400,
//   templates: [
//         { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
//     { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
//     { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
//   ],
//   template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
//   template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
//   height: 600,
//   image_caption: true,
//   quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
//   noneditable_noneditable_class: "mceNonEditable",
//   toolbar_mode: 'sliding',
//   spellchecker_dialog: true,
//   spellchecker_whitelist: ['Ephox', 'Moxiecode'],
//   tinycomments_mode: 'embedded',
//   content_style: ".mymention{ color: gray; }",
//   contextmenu: "link image imagetools table configurepermanentpen",
//   a11y_advanced_options: true,
//   /* 
//   The following settings require more configuration than shown here.
//   For information on configuring the mentions plugin, see:
//   https://www.tiny.cloud/docs/plugins/mentions/.
//   */
//   mentions_selector: '.mymention',
//   // mentions_fetch: mentions_fetch,
//   // mentions_menu_hover: mentions_menu_hover,
//   // mentions_menu_complete: mentions_menu_complete,
//   // mentions_select: mentions_select,
//  });

JS;

$this->registerJs($script, yii\web\View::POS_END);
?>
