<?php
namespace pappco\yii2\adminlte\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;

class ActionArea extends Widget{
    /**
     * @var array the HTML attributes for the action area container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'title-action'];

    public $containerTag = 'div';

    /**
     * @var array of string and/or button configuration arrays to appear in the action area. If this property is empty,
     * the widget will not render anything. Each array element represents a single button
     * with the following structure, or a string:
     *
     * ~~~
     * [
     *     'label' => 'label of the button',  // required
     *     'href' => 'href of the link',      // optional, will be processed by Url::to(), default: #
     *     'template' => 'own template of the item', // optional, if not set $this->itemTemplate will be used
     *     'buttonClass' => 'btn-primary', // optional, default: btn-primary
     *     'addClass' => 'css class of button', // optional, a css class to added to button
     *     'id' => 'id attribute of button', // optional, if not set an incremental generated id will be used
     *     'icon' => 'class of the icon to be used', // optional, the html of the icon with the given class will be generated into the button
     *     'confirm' => 'display confirm popup', // optional, text to be written in the confirmation window
     *     'method' => 'get|post', // optional, default: get
     *     'attr' => 'any string', // optional
     * ]
     * ~~~
     */
    public $buttons = [];

    public $itemTemplate = '<a href="{href}" class="btn {buttonClass} {buttonSize} {addClass}" id="{id}"{attr}>{icon}{label}</a> ';
    public $itemWidgetTemplate = '{widget}';

    public function init(){
        parent::init();

        $this->renderButtons();
    }


    private function renderButtons(){
        $buttons = [];
        $i = 0;

        foreach($this->buttons as $btn){
            $i++;
            if(!is_array($btn)){
                $buttons[] = $btn;
            } elseif(($class = \Illuminate\Support\Arr::get($btn, 'class')) && class_exists($class)) {
                /** @var Widget $object */
                $object = Yii::createObject($btn);
                ob_start();
                ob_implicit_flush(false);
                $result = $object->run();
                $widget = ob_get_clean();
                $replace = [
                    '{widget}' => $result??$widget,
                ];

                $buttons[] = strtr($this->itemWidgetTemplate, $replace);
            } elseif (isset($btn["label"])){
                //FIXME:: maybe we should remove this!!!!
                $tmp = $btn;
                $tmp["href"] = ( empty($btn["href"]) ) ? '#' : Url::to($btn["href"]);
                $tmp["template"] = ( empty($btn["template"]) ) ? $this->itemTemplate : $btn["template"];
                $tmp["buttonClass"] = ( empty($btn["buttonClass"]) ) ? 'btn-primary' : $btn["buttonClass"];
                $tmp["addClass"] = ( empty($btn["addClass"]) ) ? '' : $btn["addClass"];
                $tmp["id"] = ( empty($btn["id"]) ) ? ('actionButton'.$i) : $btn["id"];
                if(!empty($btn["icon"])){
                    $tmp["icon"] = '<i class="fa '.$btn["icon"].'"></i> ';
                }
                else{
                    $tmp["icon"] = '';
                }

                $tmp["attr"] = '';
                $tmp["attr"] .= ( empty($btn["confirm"]) ) ? '' : (' data-confirm="'.$btn["confirm"].'"');
                $tmp["attr"] .= ( !empty($btn["method"]) && $btn["method"] == 'post' ) ? (' data-method="'.$btn["method"].'"') : '';
                if(!empty($btn["attr"]))
                    $tmp["attr"] .= $btn["attr"];

                $params = \Yii::$app->params;
                $buttonSize = 'md';
                if (isset($params['style']['headerButtons']['size'])) {
                    $buttonSize = $params['style']['headerButtons']['size'];
                }

                $tmp['buttonSize'] = 'btn-' . $buttonSize;

                $buttons[] = $tmp;
            }
        }

        $this->buttons = $buttons;
    }

    public function run(){
        $html = '';
        foreach($this->buttons as $btn){
            $html .= $this->renderItem($btn).' ';
        }
        
        if(!$this->containerTag) {
            return $html;
        }

        return Html::tag($this->containerTag, $html, $this->options);
    }

    private function renderItem($item){
        if(!is_array($item))
            return $item;

        $replace = [
            '{href}' => $item['href'],
            '{buttonClass}' => $item['buttonClass'],
            '{buttonSize}' => $item['buttonSize'],
            '{addClass}' => $item['addClass'],
            '{id}' => $item['id'],
            '{icon}' => $item['icon'],
            '{label}' => $item['label'],
            '{attr}' => $item['attr'],
        ];

        return strtr($item["template"], $replace);
    }
}
