<?php

namespace pappco\yii2\adminlte\widgets\grid;

use pappco\yii2\components\twig\TwigBuilder;
use pappco\yii2\components\Widget;
use pappco\yii2\grid\widgets\GridView;
use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: papppeter
 * Date: 12/05/2019
 * Time: 12:59
 */
class GridViewBox extends GridView
{
    const FILTER_POS_BOX_HEADER = 'box-header';

    public $filterPosition = self::FILTER_POS_BOX_HEADER;
    public $bordered = false;
    public $striped = false;
    public $condensed = true;

    /*
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     * - `{export}`: the grid export button menu. See [[renderExport()]].
     * - `{toolbar}`: the grid panel toolbar. See [[renderToolbar()]].
     * - `{toolbarContainer}`: the toolbar container. See [[renderToolbarContainer()]].
     *
     * In addition to the above tokens, refer the [[panelTemplate]] property for other tokens supported as
     *
     * */
    public $layout = <<<TWIG
      {% if filters or actionButtons %} 
      <div class="row mb-10">
        <div class="col-sm-10">
          {% if filters %}
            {{ filters|raw }}
          {% endif %}
        </div>
        <div class="col-sm-2 text-right">
        {% if actionButtons %}
            {{ actionButtons|raw }}
        {% endif %}
        </div> 
      </div>
      {% endif %}      
          
      <div class="box">
        {% if boxTitle or boxTools %} 
        <div class="box-header {{ boxHeaderCss }}">
          {% if boxTitle %} 
          <h3 class="box-title">{{ boxTitle|raw }}</h3>
          {% endif %}
          {% if boxTools %}
          <div class="box-tools pull-right">
            {{ boxTools|raw }}
          </div><!-- /.box-tools -->
          {% endif %}
        </div><!-- /.box-header -->
        {% endif %}
        <div class="box-body no-padding">
          {items}
        </div><!-- /.box-body -->
        <div class="box-footer">
          {summary} <div class="pull-right">{pager}</div>
        </div><!-- box-footer -->
      </div><!-- /.box -->
TWIG;

    public $filterTemplate = <<<TWIG
        <div class="form-inline">
          {% for filter in filters %}
          <div class="form-group">
            {{ filter|raw }} 
          </div>
          {% endfor %}
          <button type="submit" class="btn btn-default hidden-sm hidden-xs"><i class="fa fa-search"></i></button>
        </div>
TWIG;

    public $boxToolsTemplate = <<<TWIG
          {% for tool in tools %}
            {{ tool|raw }} 
          {% endfor %}
TWIG;

    public $actionButtonsTemplate = <<<TWIG
          {% for actionButton in actionButtons %}
            {{ actionButton|raw }} 
          {% endfor %}
TWIG;


    public $boxTitle;
    public $boxTools = [];
    public $actionButtons = [];
    public $boxHeaderCss = 'with-border';

    public function renderActionButtons() {
        if(!$this->actionButtons) {
            return '';
        }

        $this->actionButtons = array_map(function ($button){
            if(isset($button['class'])) {
                $class = $button['class'];
                /** @var Widget $class */
                $button = $class::widget($button);
            }

            return $button;
        }, $this->actionButtons);

        $twig = $this->getTwig($this->actionButtonsTemplate);

        return $twig->render('template', [
            'actionButtons' => $this->actionButtons,
        ]);
    }

    public function renderBoxTools() {
        if(!$this->boxTools) {
            return '';
        }

        $twig = $this->getTwig($this->boxToolsTemplate);

        return $twig->render('template', [
            'tools' => $this->boxTools,
        ]);
    }

    public function renderFilters()
    {
        if($this->filterModel === null) {
            return '';
        }

        if($this->filterPosition !== self::FILTER_POS_BOX_HEADER) {
            return parent::renderFilters();
        }

        $filters = [];
        foreach ($this->columns as $column) {
            /* @var $column DataColumn */
            $filter = AutoFilterRender::createFromColumn($column)->render();
            if($filter === $this->emptyCell) {
                continue;
            }
            $filters[] = $filter;
        }

        $twig = $this->getTwig($this->filterTemplate);

        return Html::tag('div',$twig->render('template', [
            'filters' => $filters,
        ]), $this->filterRowOptions);
    }

    public function init()
    {
        Html::addCssClass($this->pager['options']['class'], 'pagination no-margin');
        Html::addCssClass($this->tableOptions, 'no-margin');
        //Html::addCssClass($this->filterRowOptions, 'form-group col-md-2');

        return parent::init(); // TODO: Change the autogenerated stub
    }

    protected function initLayout()
    {
        $twig = $this->getTwig($this->layout);

        $this->layout = $twig->render('template', [
            'boxHeaderCss' => $this->boxHeaderCss,
            'boxTitle' => $this->boxTitle,
            'boxTools' => $this->renderBoxTools(),
            'actionButtons' => $this->renderActionButtons(),
            'filters' => $this->renderFilters()
        ]);

        parent::initLayout(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $template
     * @return \Twig\Environment
     */
    protected function getTwig($template) {
        $twig = TwigBuilder::create()
            ->setArrayLoader(['template' => $template])
            ->build()
        ;

        return $twig;
    }



}