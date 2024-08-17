<?php

namespace boost\multie\helpers;

class VueAdminTableHelper
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

    public static function getTranslationMethodActions(): array
    {
        return [
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Not translatable'), 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'none']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each site'), 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'site']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each site group'), 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'siteGroup']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Translate for each language'), 'multie/fields/update', 'fields', [['handle' => 'translationMethod', 'value' => 'language']]),
            VueAdminTableHelper::getActionArray(\Craft::t('app', 'Custom…', 'multie/fields/update'), 'fields', '', [['handle' => 'translationMethod', 'value' => 'custom']]),
        ];

    }
}