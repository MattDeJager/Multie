<?php

namespace matthewdejager\craftmultie\helpers;

class VueAdminTableHelper
{

    public static function getActionArray(string $label, string $action, string $param, array $values, string $status = ""): array
    {
        return [
            'label' => \Craft::t('app', $label),
            'action' => $action,
            'param' => $param,
            'value' => json_encode($values),
            'status' => $status,
        ];
    }

}