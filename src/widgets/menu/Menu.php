<?php
namespace pappco\yii2\adminlte\widgets;

use Illuminate\Support\Arr;
use pappco\yii2\adminlte\widgets\menuState\assets\MenuStateAsset;
use pappco\yii2\adminlte\widgets\menuState\MenuStateEnum;
use pappco\yii2\adminlte\widgets\menuState\MenuStateHandlerTrait;
use pappco\yii2\helpers\Url;
use pappco\yii2\helpers\Html;
use Yii;

/**
 * Class Menu
 * Theme menu widget.
 */
class Menu extends \dmstr\widgets\Menu {

    /**
     * 'menuStateRoute' => ['layout-state/save-menu-state'],
     *
     * @var
     */
    public $menuStateRoute;

    public function init() {
        parent::init();

        if ($this->menuStateRoute) {
            $this->initMenuState();
            MenuStateAsset::register($this->getView());
        }
    }

    protected function initMenuState() {
        $stateHandler = new class() {
            use MenuStateHandlerTrait;
        };

        $state = $stateHandler->getMenuState();

        $this->options['data-stateExpanded'] = MenuStateEnum::STATE_EXPANDED;
        $this->options['data-stateCollapsed'] = MenuStateEnum::STATE_COLLAPSED;
        $this->options['data-url'] = Url::to(\pappco\yii2\helpers\ArrayHelper::merge($this->menuStateRoute, ['state' => '']));

        $this->options['data-state'] = $state === false || $state == MenuStateEnum::STATE_EXPANDED ?
            MenuStateEnum::STATE_EXPANDED :
            MenuStateEnum::STATE_COLLAPSED;
    }

    /**
     * @param array $items
     * @param bool $active
     * @return array
     * @throws \Throwable
     */
    protected function normalizeItems($items, &$active) {
        $keys = array_keys($items);

        foreach ($keys as $key) {
            $this->processItems($items, $active, $key);
        }

        return array_values($items);
    }

    /**
     * @param array $items
     * @param $active
     * @param $key
     */
    protected function processItems(array &$items, &$active, $key) {
        $this->replaceAtInItems($items[$key]);
        $this->fixSubItems($items[$key]);
        $this->fixFaIcons($items[$key]);

        $hasActiveChild = false;
        if (isset($items[$key]['items'])) {
            $subKeys = array_keys($items[$key]['items']);
            foreach ($subKeys as $subKey) {
                $this->processItems($items[$key]['items'], $hasActiveChild, $subKey);
            }

            if (!$items[$key]['items']) {
                unset($items[$key]);
                return;
            }
        }

        if (!$this->isAllowed($items[$key])) {
            unset($items[$key]);
            return;
        };

        $encodeLabel = isset($item['encode']) ? $items[$key]['encode'] : $this->encodeLabels;
        $items[$key]['label'] = $encodeLabel ? Html::encode($items[$key]['label']) : $items[$key]['label'];
        if (($this->activateParents && $hasActiveChild) ||($this->activateItems && $this->isItemActive($items[$key]))) {
            $active = $items[$key]['active'] = true;
        } else {
            $items[$key]['active'] = false;
        }
    }

    protected function isItemActive($item) {
        $defaultActivePattern = $item['url'][0]??null;

        if($defaultActivePattern) {
            $defaultActivePattern = ltrim($defaultActivePattern, '/').'*';
        }

        if ($activePattern = Arr::get($item, 'activePattern', $defaultActivePattern)) {
            $currentRoute = explode('/', \Yii::$app->controller->getRoute());
            $activePattern = str_replace(['*', '/'] , ['.*', '\/'], $activePattern);

            $active =  preg_match("/^($activePattern)$/", implode('/', $currentRoute));
            $active =  preg_match("/^($activePattern)$/", \Yii::$app->request->queryParams['r']??null) || $active;

            return $active;
        }

        return parent::isItemActive($item);
    }

    /**
     * @param $item
     * @return mixed
     */
    protected function isAllowed($item) {
        $url = Arr::get($item, 'url');
        
        if (
            $url === null ||
            $url == '#' ||
            (is_array($url) && Arr::first($url) == '#')
        ) return true;
        
        return Url::to($url) ? true : false;
    }

    /**
     * @param $item
     */
    protected function replaceAtInItems(&$item) {
        if (is_string($item) && starts_with($item, '@')) {
            $key = str_replace_first('@', '', $item);

            if ($module = $this->getModule($key)) {
                $moduleClass = is_array($module) ? Arr::get($module, 'class') : $module;
                // todo instance check
                $item = $moduleClass::getMenuData();
            }
        }
    }

    private function getModule($moduleId) {
        $module = null;
        $parentModule = Yii::$app;
        $modules = Yii::$app->modules;
        $keyChain = explode('.', $moduleId);

        foreach ($keyChain as $key) {
            if (!array_key_exists($key, $modules)) {
                if (YII_DEBUG) throw new \Exception();

                return null;
            }

            $module = $modules[$key];

            if (is_array($module)) {
                $moduleClass = Arr::get($module, 'class');
                $module = $parentModule->getModule($moduleClass::ID);
            }

            if (is_string($module)) {
                $moduleClass = $module;
                $module = $parentModule->getModule($moduleClass::ID);
            }

            $parentModule = $module;
            $modules = $module->modules;
        }

        return $module;
    }

    /**
     * @param $item
     */
    protected function fixFaIcons(&$item) {
        if(isset($item['icon'])) {
            $item['icon'] = str_replace('fa-','',$item['icon']);
        }
    }

    /**
     * @param $item
     */
    protected function fixSubItems(&$item) {
        if(isset($item['subItems'])) {
            $item['items'] = $item['subItems'];
            unset($item['subItems']);
        }
    }
}
