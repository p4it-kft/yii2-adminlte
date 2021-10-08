<?php

namespace p4it\adminlte\assets;

use yii\web\AssetBundle;

class ToastrAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/toastr';

    public $css = [
        'toastr.min.css'
    ];

    public $js = [
        'toastr.min.js',
    ];
}