<?php

namespace p4it\adminlte\widgets\grid;

use Closure;
use kartik\base\Config;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\base\BaseObject;
use yii\base\Model;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Filter Renderer egy autómata filter generátor, amit gridalapján lehet használni, de attól függetlenül
 */
class AutoFilterRender extends BaseObject
{
    /**
     * @var GridView the grid view object that owns this column.
     */
    public $grid;
    /**
     * @var string the attribute name associated with this column. When neither [[content]] nor [[value]]
     * is specified, the value of the specified attribute will be retrieved from each data model and displayed.
     *
     * Also, if [[label]] is not specified, the label associated with the attribute will be displayed.
     */
    public $attribute;
    /**
     * @var string|array|null|false the HTML code representing a filter input (e.g. a text field, a dropdown list)
     * that is used for this data column. This property is effective only when [[GridView::filterModel]] is set.
     *
     * - If this property is not set, a text field will be generated as the filter input with attributes defined
     *   with [[filterInputOptions]]. See [[\yii\helpers\BaseHtml::activeInput]] for details on how an active
     *   input tag is generated.
     * - If this property is an array, a dropdown list will be generated that uses this property value as
     *   the list options.
     * - If you don't want a filter for this data column, set this value to be false.
     */
    public $filter;
    /**
     * @var array the HTML attributes for the filter input fields. This property is used in combination with
     * the [[filter]] property. When [[filter]] is not set or is an array, this property will be used to
     * render the HTML attributes for the generated filter input fields.
     *
     * Empty `id` in the default value ensures that id would not be obtained from the model attribute thus
     * providing better performance.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $filterInputOptions = [];

    /**
     * @var string the filter input type for each filter input. You can use one of the `GridView::FILTER_` constants or
     * pass any widget classname (extending the Yii Input Widget).
     */
    public $filterType;

    /**
     * @var array the options/settings for the filter widget. Will be used only if you set `filterType` to a widget
     * classname that exists.
     */
    public $filterWidgetOptions = [];
    /**
     * @var array the HTML attributes for the filter cell tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $filterOptions = [];

    /**
     * @var string|array|Closure in which format should the value of each data model be displayed as (e.g. `"raw"`, `"text"`, `"html"`,
     * `['date', 'php:Y-m-d']`). Supported formats are determined by the [[GridView::formatter|formatter]] used by
     * the [[GridView]]. Default format is "text" which will format the value as an HTML-encoded plain text when
     * [[\yii\i18n\Formatter]] is used as the [[GridView::$formatter|formatter]] of the GridView.
     * @see \yii\i18n\Formatter::format()
     */
    public $format = 'text';

    public $label;

    public static function createFromColumn(Column $column)
    {
        $config = [
            'filterOptions' => $column->filterOptions,
            'grid' => $column->grid,
        ];

        if ($column instanceof DataColumn) {
            $config['filterWidgetOptions'] = $column->filterWidgetOptions;
            $config['filterType'] = $column->filterType;
            $config['label'] = $column->label;
        }

        if ($column instanceof \yii\grid\DataColumn) {
            $config['filter'] = $column->filter;
            $config['format'] = $column->format;
            
            if($column->filterAttribute) {
                $config['filterInputOptions'] = ArrayHelper::merge(['data-test' => 'filter-input-'.Html::getInputId($column->grid->filterModel, $column->filterAttribute)],$column->filterInputOptions);
            } else {
                $config['filterInputOptions'] = $column->filterInputOptions;
            }
            
            $config['attribute'] = $column->filterAttribute??$column->attribute;
        }

        return new static($config);
    }

    public function init()
    {
        if (!isset($this->filterInputOptions['placeholder']) && !isset($this->filterWidgetOptions['options']['placeholder'])) {
            $this->filterWidgetOptions['options']['placeholder'] = $this->label ?? $this->grid->filterModel->getAttributeLabel($this->attribute);
            $this->filterInputOptions['placeholder'] = $this->label ?? $this->grid->filterModel->getAttributeLabel($this->attribute);
        }

        parent::init(); // TODO: Change the autogenerated stub
    }

    public function render()
    {
        return $this->kartikRender();
    }

    public function kartikRender()
    {
        $content = $this->yiiRender();
        $chkType = !empty($this->filterType) && $this->filterType !== GridView::FILTER_CHECKBOX &&
            $this->filterType !== GridView::FILTER_RADIO && !class_exists($this->filterType);
        if ($this->filter === false || empty($this->filterType) || $content === $this->grid->emptyCell || $chkType) {
            return $content;
        }
        $widgetClass = $this->filterType;
        $options = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'options' => $this->filterInputOptions,
        ];

        if (is_array($this->filter)) {
            if (Config::isInputWidget($this->filterType) && $this->grid->pjax) {
                $options['pjaxContainerId'] = $this->grid->pjaxSettings['options']['id'];
            }
            if ($this->filterType === GridView::FILTER_SELECT2 || $this->filterType === GridView::FILTER_TYPEAHEAD) {
                $options['data'] = $this->filter;
            }
            if ($this->filterType === GridView::FILTER_RADIO) {
                return Html::activeRadioList(
                    $this->grid->filterModel,
                    $this->attribute,
                    $this->filter,
                    $this->filterInputOptions
                );
            }
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return Html::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        $options = array_replace_recursive($this->filterWidgetOptions, $options);

        /** @var \kartik\base\Widget $widgetClass */
        return $widgetClass::widget($options);
    }

    public function yiiRender()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }

            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            }

            if ($this->format === 'boolean') {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, [
                        1 => $this->grid->formatter->booleanFormat[1],
                        0 => $this->grid->formatter->booleanFormat[0],
                    ], $options) . $error;
            }

            return Html::activeTextInput($model, $this->attribute, $this->filterInputOptions) . $error;
        }

        return $this->emptyRender();
    }

    public function emptyRender()
    {
        return $this->grid->emptyCell;
    }
}
