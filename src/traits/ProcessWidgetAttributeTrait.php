<?php
namespace p4it\adminlte\traits;

use yii\base\InvalidConfigException;
use yii\base\Widget;

trait  ProcessWidgetAttributeTrait {

    public static function autoProcessableAttributes() {
        return [];
    }

    protected function autoProcessAttributes(array $config): array
    {
        $processedConfig = $config;

        foreach (self::autoProcessableAttributes() as $autoProcessAttribute) {
            if(!array_key_exists($autoProcessAttribute, $config)) {
                continue;
            }

            $value = $config[$autoProcessAttribute];

            if($value === null) {
                continue;
            }

            if($this->attributeTypeIsArray($autoProcessAttribute)) {
                if(!is_array($value)) {
                    throw new InvalidConfigException('Attribute type is array');
                }

                foreach ($value as $item) {
                    $processedConfig[$autoProcessAttribute] = $this->autoProcessAttribute($item);
                }
            } else {
                $processedConfig[$autoProcessAttribute] = $this->autoProcessAttribute($value);
            }
        }

        return $processedConfig;
    }

    private function attributeTypeIsArray($attribute) {
        $rp = new \ReflectionProperty($this, $attribute);

        return $rp->getType() && $rp->getType()->getName() === 'array';
    }

    private function autoProcessAttribute($widget) {
        if(is_string($widget)) {
            return (string)$widget;
        }

        if($widget instanceof Widget) {
            ob_start();
            ob_implicit_flush(false);
            $html = $widget->run();
            $obHtml = ob_get_clean();
            if(!$html && $obHtml !== false) {
                $html = $obHtml;
            }
            return $html;
        }

        if(is_array($widget) && isset($widget['class']) && $widget['class'] instanceof Widget) {
            return $widget['class']::widget($widget);
        }

        if(is_array($widget)) {
            throw new InvalidConfigException('Widget cannot be auto processed');
        }

        return (string)$widget;
    }
}