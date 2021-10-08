<?php
namespace p4it\adminlte\widgets;

use p4it\adminlte\traits\ProcessWidgetAttributeTrait;

class Widget extends \yii\bootstrap4\Widget {

    use ProcessWidgetAttributeTrait;

    public function __construct($config = [])
    {
        $this->autoProcessAttributes($config);

        parent::__construct($config);
    }
}