<?php

namespace p4it\adminlte\widgets\toastr;

use yii\bootstrap4\Widget;

class Toastr extends Widget
{
    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();

        foreach ($flashes as $type => $data) {
            $data = (array)$data;
            foreach ($data as $message) {
                $this->view->registerJs(<<<JS
toastr['$type']('$message');
JS
                );
            }
            $session->removeFlash($type);
        }
    }
}
