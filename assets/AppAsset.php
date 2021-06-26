<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jquery.datetimepicker.min.css',
        'css/jstree/style.min.css',
    ];
    public $js = [
//        'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js',

//        'https://cdn.tiny.cloud/1/invalid-origin/tinymce/5.2.0-75/tinymce.min.js',
//        'js/tinymce/tinymce.js',

        'js/datetimepicker/jquery.datetimepicker.min.js',
        'js/datetimepicker/jquery.datetimepicker.full.js',

        'js/inputmask/inputmask.js',
        'js/inputmask/inputmask.min.js',

        'js/jstree/jstree.js',
        'js/jstree/jstree.checkbox.js',
        'js/jstree/jstree.search.js',
        'js/jstree/jstree.dnd.js',

        'js/dynamicForm/dynamic-form.js',

//        'js/tinymce/plugins/quickbars/plugin.js',
//        'js/tinymce/plugins/toc/plugin.js',
//        'js/tinymce/myplugins/example/plugin.js',
//        'js/tinymce/myplugins/responsivefilemanager/plugin.min.js',

//        'form_editor/js/form-builder.min.js',
//        'form_editor/js/form-render.min.js',
//        'http://formbuilder.online/assets/js/form-builder.min.js',
//        'http://formbuilder.online/assets/js/form-render.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
