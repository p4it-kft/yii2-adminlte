<?php

namespace p4it\adminlte\assets;

use yii\web\AssetBundle;

class SweetAlertAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/sweetalert2';

    public $css = [
        'sweetalert2.min.css'
    ];

    public $js = [
        'sweetalert2.all.min.js',
    ];

    public $depends = [
       BaseAsset::class
    ];
}