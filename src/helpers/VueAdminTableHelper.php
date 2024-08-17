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

}