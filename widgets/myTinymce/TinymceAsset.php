<?php

namespace app\widgets\myTinymce;


use yii\web\AssetBundle;

class TinymceAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/myTinymce/assets';
    public $js = [
        'tinymce/tinymce.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}