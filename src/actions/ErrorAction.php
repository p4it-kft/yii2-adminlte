<?php
namespace p4it\adminlte\actions;

class ErrorAction extends \yii\web\ErrorAction {

    /**
     * Builds array of parameters that will be passed to the view.
     * @return array
     * @since 2.0.11
     */
    protected function getViewRenderParams()
    {
        return [
            'name' => $this->getExceptionCode(),
            'message' => $this->getExceptionMessage(),
            'exception' => $this->exception,
        ];
    }
}