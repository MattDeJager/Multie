<?php

namespace boost\multie\helpers;

use boost\multie\constants\TranslationMethods;
use Craft;

abstract class VueAdminTableHelper implements VueAdminTableHelperInterface
{
    protected static function createColumn(string $name, string $titleKey, string $translationCategory = 'app'): array
    {
        return [
            'name' => $name,
            'title' => Craft::t($translationCategory, $titleKey),
        ];
    }

    public static function getActionArray(string $label, string $action, string $param, $values, string $status = ""): array
    {
        return [
            'label' => \Craft::t('app', $label),
            'action' => $action,
            'param' => $param,
            'value' => json_encode($values),
            'status' => $status,
        ];
    }

    public static function getActionsArray(string $label, array $actions, string $icon = null): array
    {
        $actionArr = [
            'label' => \Craft::t('app', $label),
            'actions' => $actions,
        ];

        $icon && $actionArr['icon'] = $icon;

        return $actionArr;
    }

    public static function getTranslationMethodActions(string $action, $handle): array
    {
        $actions = [];

        foreach (TranslationMethods::getTranslationMethods() as $value => $label) {
            $actions[] = self::getActionArray($label, $action, 'fields', [['handle' => $handle, 'value' => $value]]);
        }

        return $actions;
    }

}