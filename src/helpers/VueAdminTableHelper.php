<?php

namespace boost\multie\helpers;

abstract class VueAdminTableHelper implements VueAdminTableHelperInterface
{
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
        return [
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Not translatable'), $action, 'fields', [['handle' => $handle, 'value' => 'none']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each site'), $action, 'fields', [['handle' => $handle, 'value' => 'site']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each site group'), $action, 'fields', [['handle' => $handle, 'value' => 'siteGroup']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each language'), $action, 'fields', [['handle' => $handle, 'value' => 'language']]),
        ];
    }

}