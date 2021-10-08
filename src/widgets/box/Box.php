<?php
namespace pappco\yii2\adminlte\widgets\box;

use Illuminate\Support\Arr;
use pappco\yii2\adminlte\widgets\box\enums\BoxClassEnum;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class Ibox
 * @package pappco\yii2\inspinia


 * Widget example
<?= \pappco\yii2\adminlte\widgets\box\Box::widget([
'title' => 'widget test',
'content' => 'This is the content',
'buttons' => [
[
'icon' => 'fa fa-folder',
'text' => 'folder',
'tools' => [
Html::a('option 1','#'),
Html::a('option 2','#'),
Html::a('option 3','#'),
]
]
]
]) ?>



 * Begin-end example
<?php \pappco\yii2\adminlte\widgets\box\Box::begin([
'title' => 'Apple',
'heading' => ['title' => 'You have a meeting today!', 'content'=>'Meeting is at 6:00am. Check your schedule to see details.'],
'enableCollapse' => true,
'enableExpand' => false,
'isCollapsed' => false,
'close' => true,
'buttons' => [
[
'icon' => 'fa fa-bars',
'text' => 'click',
'options' => [
'onclick' => new \yii\web\JsExpression('alert("clicked");'),
]
],
[
'icon' => 'fa fa-folder',
'text' => 'open',
'tools' => [
Html::a('nothing','#'),
]
]
],
'tools' => [
Html::a('Go to index',['index']),
Html::a('nothing 2','#'),
]
]); ?>

<h2>Content</h2>
<p>This is the content</p>

<?php \pappco\yii2\adminlte\widgets\box\Box::end() ?>
 */
class Box extends \pappco\yii2\components\Widget {

    public $title;
    public $heading;
    public $footer;
    public $enableCollapse = false;
    public $enableExpand = false;
    public $isCollapsed = false;
    public $close = false;
    public $tools;
    public $buttons;
    public $format = 'encoded'; // raw
    public $content;
    public $stateControllerClass; //FIXME: do it
    public $containerClass;
    public $boxBodyOptions;

    public function init() {
        parent::init();

        if($this->content === null) {
            ob_start();
            ob_implicit_flush(false);
        }
    }

    public function run() {
        if($this->content === null) {
            $this->content = ob_get_clean();
        }
        $this->renderIbox();
    }

    protected function renderIbox() {
        Html::addCssClass($containerClass,$this->containerClass ?? BoxClassEnum::BOX);
        Html::addCssClass($containerClass,$this->isCollapsed ? 'collapsed-box': '');

        echo Html::beginTag('div',['class' => $containerClass, "id" => $this->id]);

        if($this->buttons || $this->enableCollapse || $this->enableExpand || $this->tools || $this->close || $this->title) {
            echo Html::beginTag('div',['class'=>"box-header with-border"]);
        }

        /* Title */
        if($this->title) {
            /* Title content*/
            echo Html::tag('h3', $this->format == 'raw' ? $this->title : Html::encode($this->title), ['class' => 'box-title']);

        }

        if($this->buttons || $this->enableCollapse || $this->enableExpand || $this->tools || $this->close) {

            if(!$this->title) {
                echo Html::tag('h3', '&nbsp;', ['class' => 'box-title']);
            }

            echo Html::beginTag('div', ['class' => 'box-tools pull-right']);

            /*Buttons*/
            if ($this->buttons) {
                $this->renderButtons();
            }

            /*Collapse*/
            if ($this->enableCollapse) {

                $collapsed = $this->isCollapsed ? 'fa-plus' : 'fa-minus';

                echo <<<HTML
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa {$collapsed}"></i></button>
HTML;
            }

            /*Tools*/
            if ($this->tools) {
                $tools =  Html::ul(
                    $this->tools,
                    [
                        'class' => "dropdown-menu",
                        'role' => 'menu',
                        'encode' => false
                    ]
                );

                echo <<<HTML
                <div class="btn-group open">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <i class="fa fa-wrench"></i></button>
                  $tools
                </div>
HTML;
            }

            /*Close*/
            if ($this->close) {
                echo <<<HTML
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
HTML;
            }

            echo Html::endTag('div');
        }
        if($this->buttons || $this->enableCollapse || $this->enableExpand || $this->tools || $this->close || $this->title) {
            echo Html::endTag('div');
        }

        /*Content*/
        Html::addCssClass($this->boxBodyOptions, 'box-body');
        echo Html::tag('div', $this->content, ['class' => $this->boxBodyOptions['class']]);

        /*Footer*/
        $footer = array_filter(Arr::wrap($this->footer));

        foreach ($footer as $item) {
            echo Html::tag('div', $item, ['class' => 'box-footer']);
        }

        echo Html::endTag('div');
    }

    protected function renderButtons() {
        $btnNumber = 1;
        $buttons = [];
        foreach($this->buttons as $button){

            if(!is_array($button)) {
                echo $button;
                continue;
            }

            if(!isset($button['options']['class']))
                $button['options']['class'] = 'btn btn-box-tool';

            if(isset($button['class'])) {
                /** @var Widget $object */
                //$object = create_object($button);
                $class = $button['class'];
                $buttons[] = $class::widget($button);
                //$buttons[] = Widget::widget($button);
                //$object->run();
                continue;
                //FIXME: if we have class than call as an object.
            }

            $options = [];
            if(isset($button['options']))
                $options = $button['options'];

            $text = "";
            if(isset($button['text']))
                $text = $button['text'];

            $tools = false;
            if(isset($button['tools'])) {
                $tools = true;
                $options = ArrayHelper::merge(
                    [
                        'id' => $this->options['id'] . '-btn-' . $btnNumber,
                        'class' => 'dropdown-toggle',
                        'data-toggle' => 'dropdown',
                        'aria-expanded' => 'false'
                    ],
                    $options
                );
            }

            if(isset($button['icon'])) {
                $content = Html::tag('a','<i class="'.$button['icon'].'"></i> '.$text,$options).' ';
            } else {
                $content = Html::tag('a',$text,$options).' ';
            }

            if($tools) {
                $dropdown = Html::ul(
                    $button['tools'],
                    [
                        'class' => "dropdown-menu",
                        'encode' => false,
                        "aria-labelledby" => $this->options['id'] . '-btn-' . $btnNumber
                    ]
                );
                $buttons[] = Html::tag('span',$content.$dropdown,['style'=>'position:relative;']);
            } else {
                $buttons[] = $content;
            }

            $btnNumber++;
        }

        echo implode(' ', $buttons);
    }
}