<?php
namespace p4it\adminlte\widgets\menu;

use yii\base\Event;

class PrepareItemEvent extends Event {
    /** @var string|array */
    public $item;
}