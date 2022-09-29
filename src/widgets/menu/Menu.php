<?php
namespace p4it\adminlte\widgets\menu;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * Class Menu
 * @package hail812\adminlte\widgets
 *
 * For example,
 *
 * ```php
 * Menu::widget([
 *      'items' => [
 *          [
 *              'label' => 'Starter Pages',
 *              'icon' => 'tachometer-alt',
 *              'badge' => '<span class="right badge badge-info">2</span>',
 *              'items' => [
 *                  ['label' => 'Active Page', 'url' => ['site/index'], 'iconStyle' => 'far'],
 *                  ['label' => 'Inactive Page', 'iconStyle' => 'far'],
 *              ]
 *          ],
 *          ['label' => 'Simple Link', 'icon' => 'th', 'badge' => '<span class="right badge badge-danger">New</span>'],
 *          ['label' => 'Yii2 PROVIDED', 'header' => true],
 *          ['label' => 'Gii',  'icon' => 'file-code', 'url' => ['/gii'], 'target' => '_blank'],
 *          ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'target' => '_blank'],
 *          ['label' => 'Important', 'iconStyle' => 'far', 'iconClassAdded' => 'text-danger'],
 *          ['label' => 'Warning', 'iconClass' => 'nav-icon far fa-circle text-warning'],
 *      ]
 * ])
 * ```
 *
 * @var array menu item
 * - label: string, the menu item label.
 * - header: boolean, not nav-item but nav-header when it is true.
 * - url: string or array, it will be processed by [[Url::to]].
 * - items: array, the sub-menu items.
 * - icon: string, the icon name. @see https://fontawesome.com/
 * - iconStyle: string, the icon style, such as fas(Solid), far(Regular), fal(Light), fad(Duotone), fab(Brands).
 * - iconClass: string, the whole icon class.
 * - iconClassAdded: string, the additional class.
 * - badge: string, html.
 * - target: string.
 */
class Menu extends \yii\widgets\Menu {

    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a class="nav-link {active}" href="{url}" {target}>{icon} {label}</a>';

    /**
     * @inheritdoc
     */
    public $labelTemplate = '<p>{label} {treeFlag} {badge}</p>';

    public string $treeTemplate = "\n<ul class='nav nav-treeview'>\n{items}\n</ul>\n";

    public string $iconDefault = 'circle';

    public string $iconStyleDefault = 'fas';

    public bool $guessActivePattern = true;
    
    public const EVENT_PREPARE_ITEM = 'prepareItem';

    /**
     * @inheritdoc
     */
    public $itemOptions = ['class' => 'nav-item'];

    /**
     * @inheritdoc
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'nav nav-pills nav-sidebar flex-column',
        'data-widget' => 'treeview',
        'role' => 'menu',
        'data-accordion' => 'false'
    ];

    public function run()
    {
        $items = $this->items;

        $keys = array_keys($items);
        foreach ($keys as $key) {
            $this->prepareItems($items, $key);
        }

        $item = $this->setItemVisiblityBasedOnSubItems($items);

        $this->items = array_values($items);

        parent::run(); // TODO: Change the autogenerated stub
    }


    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));

            if (isset($item['items'])) {
                Html::addCssClass($options, 'has-treeview');
            }

            if (isset($item['header']) && $item['header']) {
                Html::removeCssClass($options, 'nav-item');
                Html::addCssClass($options, 'nav-header');
            }

            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            Html::addCssClass($options, $class);

            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $treeTemplate = ArrayHelper::getValue($item, 'treeTemplate', $this->treeTemplate);
                $menu .= strtr($treeTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
                if ($item['active']) {
                    $options['class'] .= ' menu-open';
                }
            }

            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    protected function renderItem($item)
    {
        if(isset($item['header']) && $item['header']) {
            return $item['label'];
        }

        if (isset($item['iconClass'])) {
            $iconClass = $item['iconClass'];
        } else {
            $iconStyle = $item['iconStyle'] ?? $this->iconStyleDefault;
            $icon = $item['icon'] ?? $this->iconDefault;
            $iconClassArr = ['nav-icon', $iconStyle, 'fa-'.$icon];
            isset($item['iconClassAdded']) && $iconClassArr[] = $item['iconClassAdded'];
            $iconClass = implode(' ', $iconClassArr);
        }
        $iconHtml = '<i class="'.$iconClass.'"></i>';

        $treeFlag = '';
        if (isset($item['items'])) {
            $treeFlag = '<i class="right fas fa-angle-left"></i>';
        }

        $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
        return strtr($template, [
            '{label}' => strtr($this->labelTemplate, [
                '{label}' => $item['label'],
                '{badge}' => $item['badge'] ?? '',
                '{treeFlag}' => $treeFlag
            ]),
            '{url}' => isset($item['url']) ? Url::to($item['url']) : '#',
            '{icon}' => $iconHtml,
            '{active}' => $item['active'] ? $this->activeCssClass : '',
            '{target}' => isset($item['target']) ? 'target="'.$item['target'].'"' : ''
        ]);
    }

    /**
     * @param array $items
     * @param $key
     */
    protected function prepareItems(array &$items, $key): void
    {
        $items[$key] = $this->prepareItem($items[$key]);

        $this->fixSubItems($items[$key]);
        $this->fixFaIcons($items[$key]);
        
        if (isset($items[$key]['items'])) {
            $subKeys = array_keys($items[$key]['items']);
            foreach ($subKeys as $subKey) {
                $this->prepareItems($items[$key]['items'], $subKey);
            }
        }
    }
    
    protected function prepareItem($item) {
        $event = new PrepareItemEvent(['item' => $item]);

        $this->trigger(self::EVENT_PREPARE_ITEM, $event);
        
        return $event->item;
    }

    /**
     * @param $item
     */
    protected function fixFaIcons(&$item) {
        if(isset($item['icon'])) {
            $item['icon'] = str_replace(['fa-', 'fas-', 'far-', 'fal-', 'fad-', 'fab-'],['', '', '', '', '', ''],$item['icon']);
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

    /**
     * @param array $items
     * @return array|mixed
     */
    protected function setItemVisiblityBasedOnSubItems(array $items)
    {
        foreach ($items as &$item) {
            if (isset($item['visible'])) {
                continue;
            }

            if (isset($item['url'])) {
                continue;
            }

            if (!isset($item['items'])) {
                continue;
            }

            $visible = $this->hasVisibleItems($item['items']);

            $item['visible'] = $visible;
        }
        return $item;
    }

    protected function isItemActive($item) {
        if ($activePattern = ArrayHelper::getValue($item, 'activePattern', $this->guessActivePattern($item))) {
            return StringHelper::matchWildcard($activePattern, \Yii::$app->controller->getRoute() ?: \Yii::$app->request->get('r'));
        }

        return parent::isItemActive($item);
    }

    /**
     * only for 2 levels
     *
     * @param $items
     * @return bool
     */
    protected function hasVisibleItems($items): bool
    {
        foreach ($items as $item) {
            if (!isset($item['items'])) {
                continue;
            }

            if((bool)array_filter(ArrayHelper::getColumn($item['items'], 'visible'), fn($value) => $value === true || $value === null)) {
                return true;
            }
        }

        $itemsVisiblity = ArrayHelper::getColumn($items, 'visible');
        $visible = (bool)array_filter($itemsVisiblity, fn($value) => $value === true || $value === null);

        return $visible;
    }

    protected function guessActivePattern(array $item)
    {
        if($this->guessActivePattern === false) {
            return null;
        }

        $url = $item['url'][0]??null;
        if($url === null) {
            return null;
        }

        $parts = StringHelper::explode($url,'/','/', true);
        array_pop($parts);
        return implode('/',$parts).'*';
    }
}
