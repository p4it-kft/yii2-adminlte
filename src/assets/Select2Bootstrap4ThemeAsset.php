<?php

namespace p4it\adminlte\assets;

use yii\web\AssetBundle;

class Select2Bootstrap4ThemeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/select2-bootstrap4-theme';

    public $css = [
        'select2-bootstrap4.min.css'
    ];

    public $depends = [
        'kartik\select2\Select2KrajeeAsset'
    ];
}