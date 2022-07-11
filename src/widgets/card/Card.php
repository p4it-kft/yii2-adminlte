<?php
namespace p4it\adminlte\widgets\card;

use p4it\adminlte\widgets\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Card extends Widget {

    public ?string $title = null;

    public ?string $header = null;

    public array $tools = [];

    public ?string $body = null;

    public ?string $footer = null;

    public array $footers = [];

    public array $headerOptions = [];

    public array $bodyOptions = [];

    public bool $isCollapsible = false;
    public bool $isCollapsed = false;
    public bool $isMaximizable = false;
    public bool $isMaximized = false;
    public bool $isRemovable = false;

    public string $layoutView = '@vendor/p4it-kft/yii2-adminlte/src/widgets/card/views/layout';

    public function init() {
        parent::init();

        if(!isset($this->options['class'])) {
            $this->options['class'] = CardCssEnum::CARD;
        }
        Html::addCssClass($this->headerOptions, 'card-header');
        Html::addCssClass($this->bodyOptions, 'card-body');

        if($this->isCollapsed) {
            Html::addCssClass($this->options, 'collapsed-card');
            $this->isCollapsible = true;
        }

        if($this->isMaximized) {
            Html::addCssClass($this->options, 'maximized-card');
            $this->isMaximizable = true;
        }

        if($this->isCollapsible) {
            $this->tools[] = <<<HTML
      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
HTML;
        }

        if($this->isMaximizable) {
            $this->tools[] = <<<HTML
      <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
HTML;
        }

        if($this->isRemovable) {
            $this->tools[] = <<<HTML
      <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
HTML;
        }

        if(!isset($this->body)) {
            ob_start();
            ob_implicit_flush(false);
        }
    }

    public function run() {
        if(!isset($this->body)) {
            $this->body = ob_get_clean();
        }

        $footers = [];
        if($this->footer) {
            $footers[] = $this->footer;
        }
        if($this->footers) {
            $footers = ArrayHelper::merge($footers, $this->footers);
        }

        return $this->render($this->layoutView,[
            'containerAttributes' => Html::renderTagAttributes($this->options),
            'headerContainerAttributes' => Html::renderTagAttributes($this->headerOptions),
            'bodyContainerAttributes' => Html::renderTagAttributes($this->bodyOptions),
            'title' => $this->title,
            'tools' => $this->renderTools(),
            'body' => $this->body,
            'footer' => $this->footer,
            'footers' => $footers,
            'header' => $this->header,
        ]);
    }

    public function renderTools() {
        if(!$this->tools) {
            return [];
        }

        $actionButtons = array_map(static function ($button){
            if(isset($button['class']) && is_subclass_of($button['class'], \yii\base\Widget::class)) {
                Html::addCssClass($button['options'], 'btn-xs');
                $button = $button['class']::widget($button);
            }

            return $button;
        }, $this->tools);

        return $actionButtons;
    }
}