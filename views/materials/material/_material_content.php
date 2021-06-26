<?php
//use yii\grid\GridView;
//use yii\helpers\Html;
use yii\helpers\Url;
//use dosamigos\tinymce\TinyMce;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use app\widgets\myTinymce\myTinymce;
use \app\models\Users;
//use yii\helpers\StringHelper;
//use app\models\MuseumSchedule;
//$this->title = 'Музеи'; addaddress
//var_dump(app\widgets\myTinymce\myTinymce::className());
//die;

?>
<!--    <div id="cms-page_content_ifr"></div>-->
<?=$form->field($model, 'content')->widget(myTinymce::className(), [
    //TinyMCE options
    'pluginOptions' => [
        'plugins' => [ //help styleselect spellchecker pagebreak
            "responsivefilemanager advlist autolink lists table contextmenu imagetools textcolor autosave colorpicker autoresize codesample directionality emoticons importcss legacyoutput noneditable  save tabfocus textpattern wordcount quickbars ", //toc
            "paste link image charmap print preview hr anchor searchreplace visualblocks visualchars fullscreen insertdatetime media nonbreaking fullpage ".((Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)?'code':''),
        ],
        'toolbar1' => "responsivefilemanager undo redo | formatselect fontselect fontsizeselect | bullist numlist | outdent indent | forecolor",
        'toolbar2' => "contextmenu cut copy paste removeformat selectall | justifyleft justifycenter justifyright justifyfull justifynone insertunorderedlist insertorderedlist bold italic underline strikethrough superscript subscript | alignleft aligncenter alignright alignjustify  | link unlink image | nonbreaking | backcolor",
//        'toolbar3' => " |  forecolor hilitecolor fontname fontsize |  mceblockquote formatblock mcecleanup mceremovenode mceselectnodedepth mceselectnode mceinsertcontent mceinsertrawhtml mcetoggleformat mcesetcontent indent outdent mcerepaint inserthorizontalrule mcetogglevisualaid mcereplacecontent mceinsertlink  delete mcenewdocument insertlinebreak undo redo mcelink mceanchor",
//        'toolbar3' => "help styleselect spellchecker pagebreak",

//        'toolbar1' => "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
//        'toolbar2' => "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor ",
        'setup'=>new JsExpression("function(ed) {
        ed.on('init', function() 
        {
//            this.getDoc().body.style.fontSize = '12px';
            this.getDoc().body.style.fontFamily = 'Arial';
        });
                    }"),
        'image_advtab' => true,
        'max_height' => 600,
        'width' => 820,
        'filemanager_title' => "Файловый менеджер",
        'language' => ArrayHelper::getValue(explode('-', Yii::$app->language), '0', Yii::$app->language),
        'image_title' => true,
        'display_other_forms' => true,
        'paste_data_images' => true,
        'paste_as_text' => true,
        'relative_urls' => false,
        'images_upload_url'=>Url::to(['/materials/images-upload-url','guid'=>$model['guid']],true),


        //        'templates' => [ (нужно добавить плагин template)
//            [ 'title'=>'Test template 1', 'content'=>'Test 1' ],
//            [ 'title'=>'Test template 2', 'content'=>'Test 2' ]
//        ],

        // here we add custom filepicker only to Image dialog
        'file_picker_types'=>'image',
        // and here's our custom image picker
        'file_picker_callback'=> new JsExpression("function(callback, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/jpeg,image/png,image/jpg');
                        //If this is not included, the onchange function will not
                        //be called the first time a file is chosen
                        //(at least in Chrome 58)
                        var foo = document.getElementById('cms-page_content_ifr');
                        foo.appendChild(input);

                        input.onchange = function() {
                            //alert('File Input Changed');
                            //console.log( this.files[0] );

                            var file = this.files[0];

                            var reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function () {
                                // Note: Now we need to register the blob in TinyMCEs image blob
                                // registry. In the next release this part hopefully won't be
                                // necessary, as we are looking to handle it internally.

                                //Remove the first period and any thing after it
                                var rm_ext_regex = /(\.[^.]+)+/;
                                var fname = file.name;
                                fname = fname.replace( rm_ext_regex, '');

                                //Make sure filename is benign
                                var fname_regex = /^([A-Za-z0-9])+([-_])*([A-Za-z0-9-_]*)$/;
                                if( fname_regex.test( fname ) ) {
                                    var id = fname + '-' + (new Date()).getTime(); //'blobid' + (new Date()).getTime();
                                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                                    var blobInfo = blobCache.create(id, file, reader.result);
                                    blobCache.add(blobInfo);

                                    // call the callback and populate the Title field with the file name
                                    callback(blobInfo.blobUri(), { title: file.name });
                                }
                                else {
                                    alert( 'Invalid file name' );
                                }
                            };
                            //To get get rid of file picker input
                            this.parentNode.removeChild(this);
                        };

                        input.click();
                    }"),
    ],
    //Widget Options
    'fileManagerOptions' => [
        //Upload Manager Configuration
        'configPath' => [
            //path from base_url to base of upload folder with start and final /
            'upload_dir' => '/uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/',
            //relative path from filemanager folder to upload folder with final /
            'current_path' => '../../../uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/',
            //relative path from filemanager folder to thumbs folder with final / (DO NOT put inside upload folder)
            'thumbs_base_path' => '../../../uploads/files_for_material/'.Yii::$app->user->identity->guid.'/thumbs/',
            'material_guid' => $model['guid'],
        ]
    ]
])?>

<!--
<?//= $form->field($model, 'content')->widget(TinyMce::className(), [
//    'options' => ['rows' => 50],
//    'language' => 'ru',
//    'clientOptions' => [
//        //'inline' => true,
//        //$content_css needs to be defined as "" or some css rules/files
////                    'content_css' => $content_css,
//        'plugins' => [ //help styleselect spellchecker
//            "responsivefilemanager advlist autolink lists table contextmenu imagetools textcolor autosave colorpicker autoresize bbcode codesample directionality emoticons importcss legacyoutput noneditable quickbars save tabfocus textpattern toc wordcount",
//            "paste link image charmap print preview hr anchor pagebreak searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking  template fullpage",
//        ],
//        'toolbar1' => "responsivefilemanager undo redo | fontselect fontsizeselect forecolor backcolor | bold italic",
//        'toolbar2' => "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
//        'toolbar3' => "contextmenu cut copy paste unlink justifyleft justifycenter justifyright justifyfull justifynone insertunorderedlist insertorderedlist bold italic underline strikethrough superscript subscript forecolor hilitecolor fontname fontsize removeformat mceblockquote formatblock mcecleanup mceremovenode mceselectnodedepth mceselectnode mceinsertcontent mceinsertrawhtml mcetoggleformat mcesetcontent indent outdent mcerepaint inserthorizontalrule mcetogglevisualaid mcereplacecontent mceinsertlink selectall delete mcenewdocument insertlinebreak undo redo mcelink mceanchor",
//        'image_advtab' => true,
//        'templates' => [
//            [ 'title'=>'Test template 1', 'content'=>'Test 1' ],
//            [ 'title'=>'Test template 2', 'content'=>'Test 2' ]
//        ],
////                    'visualblocks_default_state'=>true,
//        'image_title' => true,
//        'paste_data_images' => true,
//        'images_upload_url'=>Url::to('/methodist/test',true),
//        // here we add custom filepicker only to Image dialog
//        'file_picker_types'=>'image',
//        // and here's our custom image picker
//        'file_picker_callback'=> new JsExpression("function(callback, value, meta) {
//                        var input = document.createElement('input');
//                        input.setAttribute('type', 'file');
//                        input.setAttribute('accept', 'image/*');
//
//                        //If this is not included, the onchange function will not
//                        //be called the first time a file is chosen
//                        //(at least in Chrome 58)
//                        var foo = document.getElementById('cms-page_content_ifr');
//                        foo.appendChild(input);
//
//                        input.onchange = function() {
//                            //alert('File Input Changed');
//                            //console.log( this.files[0] );
//
//                            var file = this.files[0];
//
//                            var reader = new FileReader();
//                            reader.readAsDataURL(file);
//                            reader.onload = function () {
//                                // Note: Now we need to register the blob in TinyMCEs image blob
//                                // registry. In the next release this part hopefully won't be
//                                // necessary, as we are looking to handle it internally.
//
//                                //Remove the first period and any thing after it
//                                var rm_ext_regex = /(\.[^.]+)+/;
//                                var fname = file.name;
//                                fname = fname.replace( rm_ext_regex, '');
//
//                                //Make sure filename is benign
//                                var fname_regex = /^([A-Za-z0-9])+([-_])*([A-Za-z0-9-_]*)$/;
//                                if( fname_regex.test( fname ) ) {
//                                    var id = fname + '-' + (new Date()).getTime(); //'blobid' + (new Date()).getTime();
//                                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
//                                    var blobInfo = blobCache.create(id, file, reader.result);
//                                    blobCache.add(blobInfo);
//
//                                    // call the callback and populate the Title field with the file name
//                                    callback(blobInfo.blobUri(), { title: file.name });
//                                }
//                                else {
//                                    alert( 'Invalid file name' );
//                                }
//                            };
//                            //To get get rid of file picker input
//                            this.parentNode.removeChild(this);
//                        };
//
//                        input.click();
//                    }")
//    ]
////                'clientOptions' => [
////                    'plugins' => [
////                        "advlist autolink lists link charmap print preview anchor",
////                        "searchreplace visualblocks code fullscreen",
////                        "insertdatetime media table contextmenu paste"
////                    ],
////                    'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
////                ]
//]);?>
-->
<?php


$script = <<< JS


// function test_test_test(url) {
//         console.log(url);
// }
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