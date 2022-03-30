<?php

namespace p4it\adminlte;

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
        \Yii::$container->set(\kartik\select2\Select2::class,
            [
                'theme' => 'bootstrap',
                'defaultPluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '...',
                ]
            ]);
        \Yii::$container->set(\pappco\yii2\widgets\select2\Select2::class,
            [
                'theme' => 'bootstrap',
                'defaultPluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '...',
                ],
            ]);
    }

}