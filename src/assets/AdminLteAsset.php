<?php

namespace p4it\adminlte\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AdminLteAsset extends AssetBundle
{
    public $sourcePath = __DIR__.'/adminLte';

    public $css = [
        'css/fix.css'
    ];

    public $js = [
        'js/pjax-timeout.js',
    ];

    public $depends = [
       SweetAlertAsset::class,
       ToastrAsset::class,
       BaseAsset::class
    ];
}