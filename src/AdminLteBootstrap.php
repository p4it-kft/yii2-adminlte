<?php

namespace p4it\adminlte;

use kartik\select2\ThemeBootstrap4Asset;
use kartik\select2\ThemeBootstrapAsset;
use kartik\select2\ThemeDefaultAsset;
use kartik\select2\ThemeKrajeeBs4Asset;
use p4it\adminlte\assets\Select2Bootstrap4ThemeAsset;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;

class AdminLteBootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        \Yii::$container->set('kartik\widgets\Select2',
            [
                'theme' => 'bootstrap4',
                'defaultPluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '...',
                ],
                'on init' => fn() => Select2Bootstrap4ThemeAsset::register(\Yii::$app->view)
            ]);
        \Yii::$container->set('kartik\select2\Select2',
            [
                'theme' => 'bootstrap4',
                'defaultPluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '...',
                ],
                'on init' => fn() => Select2Bootstrap4ThemeAsset::register(\Yii::$app->view)
            ]);
        \Yii::$container->set('pappco\yii2\widgets\select2\Select2',
            [
                'theme' => 'bootstrap4',
                'defaultPluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '...',
                ],
                'on init' => fn() => Select2Bootstrap4ThemeAsset::register(\Yii::$app->view)
            ]);
    }

}